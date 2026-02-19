<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use Illuminate\Cache\Repository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth ;

class AuthController extends Controller
{
    public function authenticate(LoginRequest $request){
        $data = $request->validated();

        if(Auth::attempt($data)){
            $token = $request->user()->createToken("app");

            return response()->json([
                "data"=> [
                    "token" => $token->plainTextToken
                ]
            ]);            
        }
        else{
            return response()->json([
                "data" => [
                    "message" => "Sikertelen belépés"
                ]
            ], 401);
        }
    }
}
