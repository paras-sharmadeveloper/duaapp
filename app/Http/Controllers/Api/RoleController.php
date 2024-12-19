<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\DB;

class RoleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $roles = Role::orderBy('id', 'DESC')->paginate(5);

        return response()->json([
            'message' => 'Roles fetched successfully',
            'success' => true,
            'data' => $roles
        ], 200);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $permissions = Permission::get();

        return response()->json([
            'message' => 'Permissions fetched for role creation',
            'success' => true,
            'data' => $permissions
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required|unique:roles,name',
            'permission' => 'required',
        ]);

        $role = Role::create(['name' => $request->input('name')]);
        $role->syncPermissions($request->input('permission'));

        return response()->json([
            'message' => 'Role created successfully',
            'success' => true,
            'data' => $role
        ], 201); // HTTP 201 Created
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $role = Role::find($id);
        if (!$role) {
            return response()->json([
                'message' => 'Role not found',
                'success' => false
            ], 404); // HTTP 404 Not Found
        }

        $rolePermissions = Permission::join('role_has_permissions', 'role_has_permissions.permission_id', '=', 'permissions.id')
            ->where('role_has_permissions.role_id', $id)
            ->get();

        return response()->json([
            'message' => 'Role and permissions fetched successfully',
            'success' => true,
            'data' => [
                'role' => $role,
                'permissions' => $rolePermissions
            ]
        ], 200);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $role = Role::find($id);
        if (!$role) {
            return response()->json([
                'message' => 'Role not found',
                'success' => false
            ], 404); // HTTP 404 Not Found
        }

        $permissions = Permission::get();
        $rolePermissions = DB::table('role_has_permissions')
            ->where('role_has_permissions.role_id', $id)
            ->pluck('role_has_permissions.permission_id', 'role_has_permissions.permission_id')
            ->all();

        return response()->json([
            'message' => 'Role data fetched for editing',
            'success' => true,
            'data' => [
                'role' => $role,
                'permissions' => $permissions,
                'rolePermissions' => $rolePermissions
            ]
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $this->validate($request, [
            'name' => 'required',
            'permission' => 'required',
        ]);

        $role = Role::find($id);
        if (!$role) {
            return response()->json([
                'message' => 'Role not found',
                'success' => false
            ], 404); // HTTP 404 Not Found
        }

        $role->name = $request->input('name');
        $role->save();

        $role->syncPermissions($request->input('permission'));

        return response()->json([
            'message' => 'Role updated successfully',
            'success' => true,
            'data' => $role
        ], 200); // HTTP 200 OK
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $role = Role::find($id);
        if (!$role) {
            return response()->json([
                'message' => 'Role not found',
                'success' => false
            ], 404); // HTTP 404 Not Found
        }

        $role->delete();

        return response()->json([
            'message' => 'Role deleted successfully',
            'success' => true
        ], 200); // HTTP 200 OK
    }
}
