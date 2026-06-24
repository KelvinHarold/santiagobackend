<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    // List all users
    public function index()
    {
        $users = User::with('roles')->get();
        return response()->json($users);
    }

    // Create new user
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'required|unique:users',
            'password' => 'required|min:6',
            'role' => 'required',
            'revenue_percentage' => 'required|numeric|min:0|max:100'
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'role' => $request->role,
            'revenue_percentage' => $request->revenue_percentage,
        ]);

        $role = Role::findByName($request->role);
        $user->assignRole($role);

        return response()->json(['message' => 'User created successfully', 'user' => $user]);
    }


    // Update user info
    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);
        $user->update($request->only(['name', 'email', 'revenue_percentage']));

        if ($request->has('role')) {
            $user->update(['role' => $request->role]);
            $user->syncRoles([$request->role]);
        }

        return response()->json(['message' => 'User updated', 'user' => $user]);
    }

    public function show($id)
    {
        $user = User::with('roles')->find($id);

        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        return response()->json($user);
    }


    // Delete user
    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $user->delete();

        return response()->json(['message' => 'User deleted']);
    }

    public function getRoles()
    {
        $roles = Role::select('name')->get();
        return response()->json($roles);
    }

    // Lightweight list for the revenue share preview on the daily report form
    // Only returns id, name, revenue_percentage — no sensitive data
    public function revenueMembers()
    {
        $users = User::where('revenue_percentage', '>', 0)
            ->select('id', 'name', 'revenue_percentage')
            ->get();
        return response()->json($users);
    }

    public function updateProfile(Request $request)
    {
        $user = $request->user();

        $request->validate([
            'password' => 'nullable|min:6|confirmed',
            'profile_picture' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($request->filled('password')) {
            $user->password = bcrypt($request->password);
        }

        if ($request->hasFile('profile_picture')) {
            if ($user->profile_picture && file_exists(public_path($user->profile_picture))) {
                @unlink(public_path($user->profile_picture));
            }

            $image = $request->file('profile_picture');
            $name = time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
            $destinationPath = public_path('/uploads/profiles');
            if (!file_exists($destinationPath)) {
                mkdir($destinationPath, 0777, true);
            }
            $image->move($destinationPath, $name);
            $user->profile_picture = '/uploads/profiles/' . $name;
        }

        $user->save();

        return response()->json([
            'message' => 'Profile updated successfully!',
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'profile_picture' => $user->profile_picture,
                'role' => $user->role,
                'roles' => $user->getRoleNames(),
            ]
        ]);
    }
}
