<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;

class PermissionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $permissions = Permission::latest()->get();

        // Return a JSON response with permissions data
        return response()->json([
            'success' => true,
            'message' => 'Permissions fetched successfully.',
            'data' => $permissions,
        ], 200);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        // Since it's for creating permissions, returning an empty response as it's not necessary
        return response()->json([
            'success' => true,
            'message' => 'Ready to create a permission.',
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // Validate request
        $request->validate([
            'name' => 'required|string|unique:permissions,name',
        ]);

        // Create the new permission
        $permission = Permission::create(['name' => $request->name]);

        // Return a success response with the created permission
        return response()->json([
            'success' => true,
            'message' => 'Permission created successfully.',
            'data' => $permission,
        ], 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $permission = Permission::find($id);

        if (!$permission) {
            return response()->json([
                'success' => false,
                'message' => 'Permission not found.',
            ], 404);
        }

        // Return the permission details
        return response()->json([
            'success' => true,
            'message' => 'Permission fetched successfully.',
            'data' => $permission,
        ], 200);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  Permission  $permission
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $permission = Permission::find($id);
        // Return the permission details to be edited
        return response()->json([
            'success' => true,
            'message' => 'Permission ready for editing.',
            'data' => $permission,
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  Permission  $permission
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Permission $permission)
    {
        // Validate the request
        $request->validate([
            'name' => 'required|string|unique:permissions,name,' . $permission->id,
        ]);

        // Update the permission
        $permission->update(['name' => $request->name]);

        // Return a success response
        return response()->json([
            'success' => true,
            'message' => 'Permission updated successfully.',
            'data' => $permission,
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  Permission  $permission
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $permission = Permission::find($id);
        // Delete the permission
        $permission->delete();

        // Return a success response
        return response()->json([
            'success' => true,
            'message' => 'Permission deleted successfully.',
        ], 200);
    }
}
