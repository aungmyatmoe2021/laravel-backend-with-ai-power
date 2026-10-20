<?php

namespace App\Http\Controllers;

use App\Http\Requests\PostStoreRequest;
use App\Http\Resources\PostResource;
use App\Models\Post;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try{
            $posts = Post::latest()->get();
            return response()->json([
                'message'=>'Post lists retrived successfully',
                // 'data'=>$posts,
                'data'=>PostResource::collection($posts)
            ]);
        }catch(Exception $e){
            return response()->json(['message'=>'Server Error'],500);
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // This is for Web application
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(PostStoreRequest $request)
    {
        try{
            // $validator = Validator::make($request->all(),[
            //     'title' => 'required|string|max:255',
            //     'content' => 'required|string'
            // ],
            // [
            //     'title.required' => 'You need to fill title',
            //     'content.required' => 'You need to fill content',
            //     'title.max' => 'Title is only accept 255 character',
            //     'title.string' => 'Title is only accept string',
            //     'content.string' => 'Content is only accept string',
            // ]);
            // if($validator->fails()){
            //     return response([
            //         'message'=>'Validation Error',
            //         'data'=>$validator->errors()
            //     ],422,[]);
            // }

            $post = Post::create([
                    'title' => $request->title,
                    'content' => $request->content
                ]);
            return response()->json([
                'message'=>'Post created successfully',
                'data'=>PostResource::make($post)
            ],201);
        }catch(Exception $e){
            return response()->json(['message'=>'Server Error'],500);
        }

    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try{
            $post = Post::find($id);
            if(!$post){
                return response(['message'=>'No Records Found'],404,[]);
            }
            return response()->json([
                'message'=>'Post retrieved successfully',
                'data'=>PostResource::make($post)
            ]);
        }catch(Exception $e){
            return response()->json(['message'=>'Server Error'],500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function showByTitle(Request $request)
    {
        try{
            $post = Post::where('title',$request->title)->first();
            if(!$post){
                return response(['message'=>'No Records Found'],404,[]);
            }
            return response()->json([
                'message'=>'Post retrieved successfully',
                'data'=>PostResource::make($post)
            ]);
        }catch(Exception $e){
            return response()->json(['message'=>'Server Error'],500);
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        // This is for Web application
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(PostStoreRequest $request, string $id)
    {
        try{
            // $validator = Validator::make($request->all(),[
            //     'title' => 'required|string|max:255',
            //     'content' => 'required|string'
            // ],
            // [
            //     'title.required' => 'You need to fill title',
            //     'content.required' => 'You need to fill content',
            //     'title.max' => 'Title is only accept 255 character',
            //     'title.string' => 'Title is only accept string',
            //     'content.string' => 'Content is only accept string',
            // ]);
            // if($validator->fails()){
            //     return response([
            //         'message'=>'Validation Error',
            //         'data'=>$validator->errors()
            //     ],422,[]);
            // }

            $post = Post::find($id);
            if(!$post){
                return response(['message'=>'No Records Found'],404,[]);
            }
            $post->title = $request->title;
            $post->content = $request->content;
            $post->save();
            return response()->json([
                'message'=>'Post retrieved successfully',
                'data'=>PostResource::make($post)
            ]);
        }catch(Exception $e){
            return response()->json(['message'=>'Server Error'],500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try{
            $post = Post::find($id);
            $post->delete();
            return response()->json(['message'=>'Post deleted successfully']);
        }catch(Exception $e){
            return response()->json(['message'=>'Server Error'],500);
        }
    }
}
