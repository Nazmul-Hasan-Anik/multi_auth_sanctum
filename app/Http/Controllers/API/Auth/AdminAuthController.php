<?php

namespace App\Http\Controllers\API\Auth;

use App\Models\Admin;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;

class AdminAuthController extends Controller
{
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'email' => 'required|email|unique:admins',
            'password' => 'required|min:6',
        ]);

        $admin = new Admin();
        $admin->name = $request->name;
        $admin->email = $request->email;
        $admin->password = Hash::make($request->password);
        $admin->save();

        return response()->json([
            'message' => 'Admin registered successfully!',
            'admin' => $admin,
        ], 201);
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|min:6',
        ]);

        $admin = Admin::where('email', $request->email)->first();

        if (!$admin || !Hash::check($request->password, $admin->password)) {
            return response()->json([
                'message' => 'Invalid credentials!',
            ], 401);
        }
        $admin->tokens->each(function ($token, $key) {
            $token->delete();
        });
        $token = $admin->createToken('api_token');

        return response()->json([
            'message' => 'Admin logged in successfully!',
            'token' => $token->plainTextToken,
        ], 200);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
    
        return response()->json([
            'message' => 'Admin logged out successfully!',
        ], 200);
    }

    public function info(Request $request)
    {
        return response()->json([
            'message' => 'This is admin section',
        ], 200);
    } 
}
