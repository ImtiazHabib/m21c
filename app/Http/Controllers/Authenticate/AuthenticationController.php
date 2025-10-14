<?php

namespace App\Http\Controllers\Authenticate;

use App\Http\Requests\LoginRequest;
use Throwable;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\RegisterRequest;

class AuthenticationController extends Controller
{
    public function register(RegisterRequest $request)
    {





        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => $request->password,
        ]);


        $token = auth()->login($user);


        return response()->json([
            'status' => 'success',
            'message' => 'User registered successfully',
            'data' => [
                'user' => $user,
                'access_token' => $token,
                'token_type' => 'bearer',
                'expires_in' => auth()->factory()->getTTL() * 60
            ]
        ]);


    }

    public function login(LoginRequest $request){

        $credentials = $request->only('email','password');

        if(!$token = auth()->attempt($credentials)){
            return response()->json([
            'status' => 'failed',
            'message' => 'User login failed',
        ]);
        }

         return response()->json([
            'status' => 'success',
            'message' => 'User login successfully',
            'data' => [
                'user' => auth()->user(),
                'access_token' => $token,
                'token_type' => 'bearer',
                'expires_in' => auth()->factory()->getTTL() * 60
            ]
        ]);
    }


}
