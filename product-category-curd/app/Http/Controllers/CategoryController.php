<?php

namespace App\Http\Controllers;

use App\Http\Requests\Category\StoreRequest;
use App\Http\Requests\Category\UpdateRequest;
use App\Http\Resources\CategoryResource;
use App\Models\Category;
use Exception;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try{
            $query = Category::query();

            // if($request->has('search')){
            //     $query->where('name', 'like', '%' . $request['search'] . '%');
            // }
            $query->when($request->has('search'), function($q) use ($request){
                $searchKey = $request['search'];
                $q->where(function ($subquery) use ($searchKey) {
                    $subquery->where('name', 'like', '%' . $searchKey . '%')->orWhere('description', 'like', '%' . $searchKey . '%');
                });
            });


            // $categories = $query->latest()->get();
            // pagination
            $perPage = $request->get('per_page',10);
            $categories = $query->paginate($perPage);
            if(count($categories) == 0){
                return response()->json([
                    "message"=>"Categories not found",
                ],404);
            }
            return response()->json([
                "message"=>"Categories retrieved successfully",
                "data"=>CategoryResource::collection($categories),
                "total"=>$categories->total(),
                "per_page"=>$categories->perPage(),
                "current_page"=>$categories->currentPage(),
                "last_page"=>$categories->lastPage() 
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
            $cateogry = Category::create($request->validated());
            return response()->json([
                "message"=>"Category created successfully",
                "data"=>CategoryResource::make($cateogry)
            ],201);
        }catch(Exception $e){
            return response()->json([
                "message"=>"Internal Server Error"
            ],500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try{
            $category = Category::find($id);
            if(!$category){
                return response()->json([
                    "message"=>"Category not found"
                ],404);
            }
            return response()->json([
                "message"=>"Category retrieved successfully",
                "data"=>CategoryResource::make($category)
            ]);
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
            $category = Category::find($id);
            if(!$category){
                return response()->json([
                    "message"=>"Category not found"
                ]);
            }
            $category->update($request->validated());
            return response()->json([
                "message"=>"Category updated successfully",
                "data"=>CategoryResource::make($category)
            ]);
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
            $category = Category::find($id);
            if(!$category){
                return response()->json([
                    "message"=>"Category not found"
                ],404);
            }
            $category->delete();
            return response()->json([
                "message"=>"Category deleted successfully"
            ],200);
        }catch(Exception $e){
            return response()->json([
                "message"=>"Internal Server Error"
            ],500);
        }
    }
}
