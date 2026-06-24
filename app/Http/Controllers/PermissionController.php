<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;

class PermissionController extends Controller
{
    // List all permissions
    public function index()
    {
        return response()->json(Permission::all());
    }

    // Create new permission
    public function store(Request $request)
    {
        $request->validate(['name' => 'required|unique:permissions']);
        $permission = Permission::create(['name' => $request->name]);
        return response()->json(['message' => 'Permission created', 'permission' => $permission]);
    }

    // Show a single permission
    public function show(Permission $permission)
    {
        return response()->json($permission);
    }

    // Update a permission name
    public function update(Request $request, Permission $permission)
    {
        $request->validate([
            'name' => 'required|unique:permissions,name,' . $permission->id,
        ]);

        $permission->update(['name' => $request->name]);

        return response()->json(['message' => 'Permission updated', 'permission' => $permission]);
    }

    // Delete a permission
    public function destroy(Permission $permission)
    {
        $permission->delete();
        return response()->json(['message' => 'Permission deleted']);
    }
}
