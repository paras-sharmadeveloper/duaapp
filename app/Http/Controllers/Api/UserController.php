<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Models\{User, VenueAddress};
use Spatie\Permission\Models\Role;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use App\Events\UserNotification;
use Illuminate\Support\Facades\Log;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $data = User::orderBy('id', 'DESC')->get();
        $siteAdmins = User::whereHas('roles', function ($query) {
            $query->where('name', 'site-admin');
        })->get();

        $dataArr = [
            'users' => $data,
            'siteadmin' =>$siteAdmins,
        ];

        return response()->json(['message' => 'Users fetched successfully', 'success' => true, 'data' => $dataArr ], 200);
    }

    public function create()
    {
        $roles = Role::pluck('name', 'name')->all();
        return response()->json(['message' => 'Roles fetched successfully', 'success' => true, 'data' => $roles], 200);
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|same:confirm-password',
            'roles' => 'required',
        ]);

        $input = $request->all();

        if ($request->hasFile('profile_pic')) {
            $image = $request->file('profile_pic');
            $imageName = time() . 'profile_pic.' . $image->getClientOriginalExtension();
            Storage::disk('s3_general')->put('images/' . $imageName, file_get_contents($image));
            $input['profile_pic'] = $imageName;
        }

        $input['password'] = Hash::make($input['password']);
        $user = User::create($input);
        $user->assignRole($request->input('roles'));

        return response()->json(['message' => 'User created successfully', 'success' => true, 'data' => $user], 201);
    }

    public function show(string $id)
    {
        $user = User::find($id);
        if (!$user) {
            return response()->json(['message' => 'User not found', 'success' => false], 404);
        }
        return response()->json(['message' => 'User fetched successfully', 'success' => true, 'data' => $user], 200);
    }

    public function edit(string $id)
    {
        $user = User::find($id);
        if (!$user) {
            return response()->json(['message' => 'User not found', 'success' => false], 404);
        }

        $roles = Role::pluck('name', 'name')->all();
        $userRole = $user->roles->pluck('name', 'name')->first();

        $data = [
            'user' => $user,
            'roles' => $roles,
            'userRole' => $userRole,
        ];

        return response()->json(['message' => 'Edit data fetched', 'success' => true, 'data' => $data], 200);
    }

    public function update(Request $request, string $id)
    {
        $this->validate($request, [
            'name' => 'required',
            'email' => 'required|email',
            'roles' => 'required',
        ]);

        $input = $request->all();

        if (!empty($input['password'])) {
            $input['password'] = Hash::make($input['password']);
        } else {
            $input = Arr::except($input, ['password']);
        }

        if ($request->hasFile('profile_pic')) {
            $image = $request->file('profile_pic');
            $imageName = time() . 'profile_pic.' . $image->getClientOriginalExtension();
            Storage::disk('s3_general')->put('images/' . $imageName, file_get_contents($image));
            $input['profile_pic'] = $imageName;
        }

        $user = User::find($id);
        if (!$user) {
            return response()->json(['message' => 'User not found', 'success' => false], 404);
        }

        $user->update($input);
        DB::table('model_has_roles')->where('model_id', $id)->delete();
        $user->assignRole($request->input('roles'));

        return response()->json(['message' => 'User updated successfully', 'success' => true, 'data' => $user], 200);
    }

    public function destroy(string $id)
    {
        $user = User::find($id);
        if (!$user) {
            return response()->json(['message' => 'User not found', 'success' => false], 404);
        }

        $user->delete();
        return response()->json(['message' => 'User deleted successfully', 'success' => true], 200);
    }
    public function updateStatus(Request $request)
    {
        $user = Auth::user();
        $status = $request->input('status');
        $site_admin_id = $request->input('site_admin_id');
        $user->status = $status;

        try {
            event(new UserNotification($status, $site_admin_id));
        } catch (\Exception $e) {
            Log::error("Issue in event: " . $e->getMessage());
            return response()->json(['message' => 'Error updating status', 'success' => false], 500);
        }

        $user->save();
        return response()->json(['message' => 'Status updated successfully', 'success' => true], 200);
    }
}
