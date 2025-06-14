<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    // Login user
    public function login(Request $request)
    {
        if (Auth::check()) {
            return response()->json([
                'status' => false,
                'message' => 'Already logged in',
            ], 400);
        }

        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|string|min:6',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        $credentials = $request->only('email', 'password');

        try {
            if (!Auth::attempt($credentials)) {
                return response()->json([
                    'status' => false,
                    'message' => 'Invalid email or password',
                ], 401);
            }

            $user = User::where('email', $credentials['email'])->first();
            $token = $user->createToken('auth_token')->plainTextToken;

            return response()->json([
                'status' => true,
                'message' => 'Login successful',
                'data' => [
                    'token' => $token,
                    'user' => Auth::user()
                ]
            ], 200);
        } catch (Exception $e) {
            Log::error('Login error: ' . $e->getMessage());
            return response()->json([
                'status' => false,
                'message' => 'Server error',
            ], 500);
        }
    }

    // Register user
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $user = User::create([
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'username' => $request->username,
                'name' => $request->name
            ]);

            $token = $user->createToken('auth_token')->plainTextToken;

            return response()->json([
                'status' => true,
                'message' => 'Registration successful',
                'data' => [
                    'token' => $token,
                    'user' => $user,
                ]
            ], 201);
        } catch (Exception $e) {
            Log::error('Registration error: ' . $e->getMessage());
            return response()->json([
                'status' => false,
                'message' => 'Server error',
            ], 500);
        }
    }

    // Logout user
    public function logout(Request $request): JsonResponse
    {
        try {
            $request->user()->tokens()->delete();

            return response()->json([
                'status' => true,
                'message' => 'Logout successful',
            ], 200);
        } catch (Exception $e) {
            Log::error('Logout error: ' . $e->getMessage());

            return response()->json([
                'status' => false,
                'message' => 'Logout failed, please try again.',
            ], 500);
        }
    }
}
