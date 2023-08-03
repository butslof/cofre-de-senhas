<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use App\Models\User;

class AuthController extends Controller
{
    public function login(Request $request){

        $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);

        if (!Auth::attempt($request->only('email', 'password'))) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $user = Auth::user();

        $token = $user->createToken('token-name')->plainTextToken;

        return response()->json(['token' => $token], 200);
    }

    public function register(Request $request){

        $request->validate(
            [
                'name' => 'required|string',
                'email' => 'required|string|email',
                'password' => 'required|string'
            ]
        );

        if(User::where('email', $request->email)->exists()){

            return response()->json(['message' => 'Email address is already taken.'], 400);

        }
        else{

            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => $request->password
            ]);

            return response()->json($user, 201);

        }

    }

    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();

        return response()->json(['message' => 'Logged out'], 200);
    }

}
