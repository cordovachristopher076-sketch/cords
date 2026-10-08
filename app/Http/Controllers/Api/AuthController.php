<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class AuthController extends Controller
{
    /**
     * Multi-Role User Registration
     * Handles account creation for Admins, Instructors, and Students.
     */
    public function register(Request $request)
    {
        // 1. Input Validation & Role Checking
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'student_id' => 'nullable|string|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'role' => ['required', Rule::in(['admin', 'instructor', 'student'])], // Validates assigned RBAC role
        ]);

        // 2. Create User Record with Password Protection (Bcrypt)
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'student_id' => $request->student_id,
            'password' => Hash::make($request->password), // Secure hashing via Bcrypt[cite: 1, 2]
            'role' => $request->role,
        ]);

        // 3. Issue Stateless Bearer Token[cite: 1, 2]
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'Account registered successfully.',
            'user' => $user,
            'access_token' => $token,
            'token_type' => 'Bearer',
        ], 201);
    }

    /**
     * User Login Endpoint
     * Verifies credentials and generates a new session token.
     */
    public function login(Request $request)
    {
        // 1. Validate Input Fields[cite: 1]
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        // 2. Locate User[cite: 1, 2]
        $user = User::where('email', $request->email)->first();

        // 3. Verify Bcrypt Password Hash[cite: 1, 2]
        if (! $user || ! Hash::check($request->password, $user->password)) {
            return response()->json([
                'message' => 'Invalid email or password credentials.',
            ], 401);
        }

        // 4. Create New Access Token[cite: 1, 2]
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'Login successful.',
            'user' => $user,
            'access_token' => $token,
            'token_type' => 'Bearer',
        ], 200);
    }

    /**
     * User Logout Endpoint
     * Revokes active API tokens to terminate the session.
     */
    public function logout(Request $request)
    {
        // Revoke the token that was used to authenticate the current request[cite: 1, 2]
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Session logged out successfully.',
        ], 200);
    }
}
