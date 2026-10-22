<?php

namespace App\Http\Controllers;

use App\Http\Requests\Product\StoreRequest;
use App\Http\Requests\Product\UpdateRequest;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Services\CloudinaryFileUploadService;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try{
            // $products = Product::latest()->get();

            // foreach($products as $product){
            //     $product->category_name=$product->category->name;
            // }

            // $products = Product::with('category')->get();
            $query = Product::query()->with('category');

            // if($request->has('search')){
            //     $query->where('name', 'like', '%' . $request['search'] . '%');
            // }
            $query->when($request->has('search'), function($q) use ($request){
                $searchKey = $request['search'];
                $q->where(function ($subquery) use ($searchKey) {
                    $subquery->where('name', 'like', '%' . $searchKey . '%')
                        ->orWhere('description', 'like', '%' . $searchKey . '%')
                        ->orWhere('price', 'like', '%' . $searchKey . '%')
                        ->orWhereHas('category',function ($categoryQuery) use($searchKey) {
                            $categoryQuery->where('name', 'like', '%' . $searchKey . '%');
                        });
                });
            });


            // $categories = $query->latest()->get();
            // pagination
            $perPage = $request->get('per_page',10);
            $products = $query->paginate($perPage);

            if(count($products) == 0){
                return response()->json([
                    "message"=>"Products not found"
                ],404);
            }
            return response()->json([
                "message"=>"Product retrieved successfully",
                "data"=>ProductResource::collection($products),
                "total"=>$products->total(),
                "per_page"=>$products->perPage(),
                "current_page"=>$products->currentPage(),
                "last_page"=>$products->lastPage() 
            ],200);
        }catch(Exception $e){
            return response()->json([
                "message"=>"Internal Server Error"
            ],500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreRequest $request)
    {
        try{
            $data = $request->validated();
            if ($request->hasFile('image_url')) {
                // storage/app/private/product
                // $path = $request->file('image_url')->store('products','public');
                // $data['image_url'] = $path;

                // upload file to cloudinary
                $file = $request->file('image_url');
                $path = CloudinaryFileUploadService::upload($file);
                $data['image_url'] = $path;
            }

            $product = Product::create($data);
            return response()->json([
                "message"=>"Product created successfully",
                "data"=>ProductResource::make($product)
            ],201);
        }catch(Exception $e){
            return response()->json([
                "message"=>$e->getMessage()
            ],500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try{
            $product = Product::find($id);
            if(!$product){
                return response()->json([
                    "message"=>"Product not found"
                ],404);
            }
            return response()->json([
                "message"=>"Product retrieved successfully",
                "data"=>ProductResource::make($product)
            ],200);
        }catch(Exception $e){
            return response()->json([
                "message"=>"Internal Server Error"
            ],500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateRequest $request, string $id)
    {
        try{
            $product = Product::find($id);
            if(!$product){
                return response()->json([
                    "message"=>"Product not found"
                ],404);
            }
            $data = $request->validated();
            if($request->hasFile('image_url')){
                // if ($product->image_url && Storage::disk('public')->exists($product->image_url)) {
                //     Storage::disk('public')->delete($product->image_url);
                // }
                    // $path = $request->file('image_url')->store('products','public');
                // $data['image_url'] = $path;
                $file = $request->file('image_url');
                CloudinaryFileUploadService::destroy($product->image_url);
                $path = CloudinaryFileUploadService::upload($file);
                $data['image_url'] = $path;
            }
            $product->update($data);
            return response()->json([
                "message"=>"Product updated successfully",
                "data"=>ProductResource::make($product)
            ],200);
        }catch(Exception $e){
            return response()->json([
                "message"=>"Internal Server Error"
            ],500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try{
            $product = Product::find($id);
            if(!$product){
                return response()->json([
                    "message"=>"Product not found"
                ],404);
            }
            if($product->image_url){
                CloudinaryFileUploadService::destroy($product->image_url);
            }
            $product->delete();
            return response()->json([
                "message"=>"Product deleted successfully"
            ],200);
        }catch(Exception $e){
            return response()->json([
                "message"=>$e->getMessage()
            ],500);
        }
    }
}
