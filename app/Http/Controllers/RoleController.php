<?php 

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleController extends Controller
{
    // List all roles
    public function index()
    {
        return response()->json(Role::all());
    }

    // Create new role
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:roles'
        ]);

        $role = Role::create([
            'name' => $request->name
        ]);

        return response()->json(['message' => 'Role created', 'role' => $role]);
    }

    public function show(Role $role)
    {
        return response()->json($role);
    }

    // Update existing role
    public function update(Request $request, Role $role)
    {
        $request->validate([
            'name' => 'required|unique:roles,name,' . $role->id
        ]);

        $role->update([
            'name' => $request->name
        ]);

        return response()->json(['message' => 'Role updated', 'role' => $role]);
    }

    // Delete role
    public function destroy(Role $role)
    {
        $role->delete();
        return response()->json(['message' => 'Role deleted']);
    }

    /**
     * Assign (sync) permissions to a role.
     * Replaces all existing permissions with the provided list.
     */
    public function assignPermissions(Request $request, Role $role)
    {
        $request->validate([
            'permissions'   => 'required|array',
            'permissions.*' => 'exists:permissions,name',
        ]);

        $role->syncPermissions($request->permissions);

        return response()->json(['message' => 'Permissions assigned successfully']);
    }

    /**
     * Get the permissions currently assigned to a role.
     * Returns { role_name, permissions } so the frontend can display
     * the role name and pre-check the correct checkboxes.
     */
    public function getPermissions(Role $role)
    {
        return response()->json([
            'role_name'   => $role->name,
            'permissions' => $role->permissions->pluck('name'),
        ]);
    }
}
