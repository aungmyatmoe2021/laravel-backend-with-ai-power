<?php

namespace App\Http\Controllers;

use App\Http\Requests\Note\StoreRequest;
use App\Http\Requests\Note\UpdateRequest;
use App\Http\Resources\NoteResource;
use App\Models\Note;
use Exception;
use Illuminate\Http\Request;

class NoteController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try{
            $query = Note::where('user_id',$request->user()->id)->latest();

            $query->when($request->has('search'),function ($q) use($request) {
                $searchKey = $request['search'];
                $q->where(function($subquery) use($searchKey){
                    $subquery->where('title','like','%'.$searchKey.'%')
                        ->orWhere('description','like','%'.$searchKey.'%');
                });
            });
            $perPage = $request->get('per_page',10);
            $notes = $query->paginate($perPage);
            if(count($notes) == 0){
                return response()->json([
                    "message"=>"Books not found"
                ],404);
            }
            return response()->json([
                "message"=>"Notes retrieved successfully",
                "data"=>NoteResource::collection($notes),
                "total"=>$notes->total(),
                "per_page"=>$notes->perPage(),
                "current_page"=>$notes->currentPage(),
                "last_page"=>$notes->lastPage()
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
            $note = Note::create($data);
            return response()->json([
                "message"=>"Note crated successfully",
                "data"=>NoteResource::make($note)
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
    public function show(Note $note)
    {
        try{
            if(!$note){
                return response()->json([
                    "message"=>"Note not found"
                ],404);
            }
            if($note->user_id !== request()->user()->id){
                return response()->json([
                    "message"=>"Unauthorized"
                ],403);
            }
            return response()->json([
                "message"=>"Note retrieved successfully",
                "data"=>NoteResource::make($note)
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
    public function update(UpdateRequest $request, Note $note)
    {
        try{
            if($note->user_id !== request()->user()->id){
                return response()->json([
                    "message"=>"Unauthorized"
                ],403);
            }
            if(!$note){
                return response()->json([
                    "message"=>"Note not found"
                ],404);
            }
            $data = $request->validated();
            $note->update($data);
            return response()->json([
                "message" => "Note updated successfully",
                "data" => NoteResource::make($note)
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
    public function destroy(Note $note)
    {
        try{
            if($note->user_id !== request()->user()->id){
                return response()->json([
                    "message"=>"Unauthorized"
                ],403);
            }
            if(!$note){
                return response()->json([
                    "message" => "Note not found"
                ],404);
            }
            $note->delete();
            return response()->json([
                "message"=>"Note deleted successfully"
            ],200);
        }catch(Exception $e){
            return response()->json([
                "message"=>"Internal Server Error"
            ],500);
        }
    }
}
