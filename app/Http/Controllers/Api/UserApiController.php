<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class UserApiController extends Controller
{
    public function registration(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|max:20|regex:/^[A-Za-z\s]+$/u|unique:users,name',
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|regex:/^[A-Za-z\d\-_!@#$%^&*()]+$/|confirmed',
        ]);

        $user = User::create([
            'name'     => $request->input('name'),
            'email'    => $request->input('email'),
            'password' => Hash::make($request->input('password')),
        ]);

        return response()->json(['message' => 'User registered successfully', 'user' => $user], 201);
    }

    public function login(Request $request)
    {
        $request->validate([
            'name'     => 'required|string',
            'password' => 'required|string',
        ]);

        $credentials = $request->only('name', 'password');

        if (Auth::attempt($credentials)) {
            $user  = Auth::user();
            $token = $user->createToken('api-token')->plainTextToken;

            return response()->json(['message' => 'Login successful', 'user' => $user, 'token' => $token]);
        } else {
            throw ValidationException::withMessages(['login' => 'Invalid name or password.']);
        }
    }

    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();

        return response()->json(['message' => 'User logged out successfully'], 200);
    }
}
