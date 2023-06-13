<?php

namespace App\Http\Controllers\API;

use App\Exceptions\InvalidPasswordException;
use Exception;
use App\Helpers\ResponseFormatter;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Laravel\Fortify\Rules\Password;

class UserController extends Controller
{
    public function login(Request $request): object
    {
        try {
            // Validate request
            $request->validate([
                'email' => ['required', 'email'],
                'password' => ['required'],
            ]);

            // Find user by email
            $user = User::where('email', $request->email)->firstOrFail();
            if (!Hash::check($request->password, $user->password)) {
                throw new InvalidPasswordException('Invalid password');
            }

            // Generate token
            $tokenResult = $user->createToken('authToken')->plainTextToken;

            // Return response
            return ResponseFormatter::success([
                'access_token' => $tokenResult,
                'token_type' => 'Bearer',
                'user' => $user
            ], 'Login success');
        } catch (Exception $e) {
            return ResponseFormatter::error('User login error', $e->getMessage());
        }
    }

    public function register(Request $request): object
    {
        try {
            // Validate request
            $request->validate([
                'name' => ['required', 'string', 'max:255'],
                'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
                'password' => ['required', 'string', new Password],
                'category_id' => ['nullable', 'exists:categories,id']
            ]);

            // Create user
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'category_id' => $request->category_id ?? null
            ]);

            // Generate token
            $tokenResult = $user->createToken('authToken')->plainTextToken;

            // Return response
            return ResponseFormatter::success([
                'access_token' => $tokenResult,
                'token_type' => 'Bearer',
                'user' => $user
            ], 'Register success');
        } catch (Exception $error) {
            // Return error response
            return ResponseFormatter::error('User register error', $error->getMessage());
        }
    }

    public function logout(Request $request): object
    {
        // Revoke Token
        $token = $request->user()->currentAccessToken()->delete();

        // Return response
        return ResponseFormatter::success($token, 'Logout success');
    }

    public function fetch(Request $request): object
    {
        // Get user
        $user = $request->user()->load('category');

        // Return responsep
        return ResponseFormatter::success($user, 'Fetch success');
    }
}
