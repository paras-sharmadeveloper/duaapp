<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Models\{Tenant,User};
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;
use Mail;
use Stancl\Tenancy\Facades\Tenancy;

class AuthController extends Controller
{
    use AuthenticatesUsers;


    public function Login(Request $request){

            $this->validate($request, [
                'email' => ['required', 'string', 'email', 'max:255'],
                'password' => ['required', 'string', 'min:6'],
            ]);

           $credentials = $request->only('email', 'password');

            if (Auth::attempt($credentials)) {
                $user = Auth::user();
                $token = $request->user()->createToken($request->email);
                return response()->json(['token' =>  $token->plainTextToken, 'status' => true, 'userInfo' => $user]);

            }
            return response()->json([
                'success' => false,
                'message' => 'Please check your Email and Password'
            ],401);
    }


}

