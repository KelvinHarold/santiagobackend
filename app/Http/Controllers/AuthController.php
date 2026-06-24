<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class AuthController extends Controller
{
    // Register Admin user
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'email' => 'required|string|email|unique:users',
            'password' => 'required|min:6|confirmed',
            'revenue_percentage' => 'nullable|numeric|min:0|max:100',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'Admin',
            'revenue_percentage' => $request->revenue_percentage ?? 100,
        ]);

        $adminRole = Role::firstOrCreate(['name' => 'Admin']);
        $user->assignRole($adminRole);

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'Admin User registered successfully',
            'user' => $user,
            'token' => $token
        ], 201);
    }

    // Login
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string'
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        // 🔹 Get roles & permissions
        $roles = $user->getRoleNames(); // collection
        $permissions = $user->getAllPermissions()->pluck('name'); // collection

        return response()->json([
            'message' => 'Login successful',
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'profile_picture' => $user->profile_picture,
                'roles' => $roles,
                'permissions' => $permissions
            ],
            'token' => $token
        ]);
    }

    // Logout
    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();
        return response()->json(['message' => 'Logged out successfully']);
    }
}
