<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use App\Enums\UserRole;
use App\Enums\Gender;

class AuthController extends Controller
{
    /**
     * Register a new user with extended details
     */
    public function register(Request $request)
    {
        $data = $request->validate([
            'name'         => 'required|string|max:255',
            'email'        => 'required|string|email|unique:users',
            'password'     => 'required|string|min:6',
            'role'         => 'required|in:admin,manager,user',
            'phone'        => 'nullable|string|max:20',
            'avatar'       => 'nullable|string', // Store filename or URL
            'address'      => 'nullable|string',
            'designation'  => 'nullable|string|max:255',
            'dob'          => 'nullable|date',
            'gender'       => ['nullable'],
        ]);

        $user = User::create([
            'name'         => $data['name'],
            'email'        => $data['email'],
            'password'     => Hash::make($data['password']),
            'role'         => $data['role'],
            'phone'        => $data['phone'] ?? null,
            'avatar'       => $data['avatar'] ?? null,
            'address'      => $data['address'] ?? null,
            'designation'  => $data['designation'] ?? null,
            'dob'          => $data['dob'] ?? null,
            'gender'       => $data['gender'] ?? null,
        ]);

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'user'  => $user,
            'token' => $token,
        ]);
    }

    /**
     * Login and generate token
     */
    public function login(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email', $request->email)->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['The credentials are incorrect.'],
            ]);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'user'  => $user,
            'token' => $token,
        ]);
    }

    /**
     * Logout by deleting current token
     */
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Logged out successfully',
        ]);
    }

    /**
     * Get current authenticated user
     */
    public function me(Request $request)
    {
        return response()->json($request->user());
    }
}
