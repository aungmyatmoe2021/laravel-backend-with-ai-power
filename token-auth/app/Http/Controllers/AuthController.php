<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserLoginRequest;
use App\Http\Requests\UserRegisterRequest;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function register(UserRegisterRequest $request){
        try{
            $user = User::create([
                "name" => $request->name,
                "email" => $request->email,
                "password" => Hash::make($request->password)
            ]);
            $token = $user->createToken('auth_token')->plainTextToken;
            return response()->json([
                "message" => "User registered successfully",
                "token" => $token
            ],201);
        }catch(Exception $e){
            return response()->json([
                "message"=>"Internal Server Error",
                "error"=>$e->getMessage()
            ],500);
        }
    }

    public function login(UserLoginRequest $request){
        try{
            $user = User::where("email", $request->email)->first();
            if(!$user){
                return response()->json([
                    "message" => "User not found"
                ],404);
            }

            if(!Hash::check($request->password,$user->password)){
                return response()->json([
                    "message" => "Password is incorrect"
                ],404);
            }
            $token = $user->createToken('auth_token')->plainTextToken;
            return response()->json([
                "message" => "User login successfully",
                "token" => $token
            ],201);
        }catch(Exception $e){
            return response()->json([
                "message"=>"Internal Server Error",
                "error"=>$e->getMessage()
            ],500);
        }
    }

    public function logout(){
        try{
            $user = Auth::user();
            $user->currentAccessToken()->delete();
            return response()->json([
                "message" => "User Logged out successfully"
            ],200);
        }catch(Exception $e){
            return response()->json([
                "message"=>"Internal Server Error",
                "error"=>$e->getMessage()
            ],500);
        }
    }

    public function me(){
        try{
            $user = Auth::user();

            return response()->json([
                "message" => "User found",
                "data" => $user
            ],200);
        }catch(Exception $e){
            return response()->json([
                "message"=>"Internal Server Error",
                "error"=>$e->getMessage()
            ],500);
        }
    }
}
