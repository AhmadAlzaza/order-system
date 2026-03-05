<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
class AuthController extends Controller
{
    public function register(Request $request)
    {
        $request->validate([
            'name'     => 'required|string',
            'email'    => 'required|email|unique:users',
            'password' => 'required|min:8',
        ]);
       $user= User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => $request->password
        ]);
        return response()->json([
            'message' => 'Register has been successfully',
            'user' => $user
        ]);

    }
    public function login(Request $request)
{
    $credentials = $request->only(['email', 'password']);

    if (!Auth::attempt($credentials)) {
        return response()->json([
            'message' => 'Invalid credentials'
        ], 401);
    }

    $token = $request->user()->createToken('token')->plainTextToken;

    return response()->json([
        'message' => 'Login successfully',
        'token' => $token
    ]);
}
public function logout(Request $request)
{
    $request->user()->currentAccessToken()->delete();
    return response()->json('logout successfully');
}

}
