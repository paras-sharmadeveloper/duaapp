<?php

namespace App\Http\Controllers;

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

    public function Signup(Request $request){


            $this->validate($request, [
                'name' => ['required', 'string', 'max:255'],
                'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
                'password' => ['required', 'string', 'min:8', 'confirmed'],
                'domain' => ['required', 'string', 'max:20', 'unique:domains,tenant_id'],
            ]);
             $token = Str::random(64);
             $tdomain = $request->input('domain').'.'. env('MASTER_DOMAIN');
             $userArr = [
                    'name' => $request->input('name'),
                    'email' => $request->input('email'),
                    'domain' => $tdomain,
                    'password' => Hash::make($request->input('password')),
                    'verify_token' =>  $token,
                ];
               $user = User::create($userArr);
               $tdomain = $request->input('domain').'.'. env('MASTER_DOMAIN');
               $tenant =  Tenant::create([
                    'id' => $request->input('domain'),
                ]);
               $tenant->domains()->create(['domain' => $tdomain ]);

               if ($user) {

                        // Switch to the tenant database connection
                        Tenancy::initialize($tenant);
                        // Create the user entry in the tenant database
                        $newuser = User::create($userArr);

                        $this->SendVerificationEmail($token,$request,$user->domain);
                        Artisan::call('app:tenant-seeder',['user' => $newuser]);
                        // Return a response or redirect to the desired page
                        return redirect('/login')->with('success', 'Registration successful. Please check your email for verification.');
                    } else {
                        // Failed to authenticate the user
                         return redirect('/login')->with('success', 'Registration failed');

                    }



            // if (Auth::attempt($credentials)) {
            //     $url = 'http://' . $tdomain.':'.$_SERVER['SERVER_PORT'].'/home?'.http_build_query($userArr);
            //     return Redirect::to($url);
            // }



    }

    public function Login(Request $request){

            $this->validate($request, [
                'email' => ['required', 'string', 'email', 'max:255'],
                'password' => ['required', 'string', 'min:6'],
            ]);

           $credentials = $request->only('email', 'password');

            if (Auth::attempt($credentials)) {

                    $tdomain = Auth()->user()->domain;
                    // if(env('MASTER_DOMAIN') === $tdomain ){
                    //     return redirect()->intended('home');
                    // }
                    return redirect()->route('home');
                      $url = 'http://' . $tdomain.':'.$_SERVER['SERVER_PORT'].'/home';
                     return Redirect::to($url);

            }
           return redirect()->back()->withInput(['email'=> $request->input('email')])->with('error', 'Invalid email or password.');

    }


     public function verifyAccount($token)
        {
            $user = User::where('verify_token', $token)->first();
            $domain = DB::table('domains')->where(['domain' => $user->domain])->first();
            $message = 'Sorry your email cannot be identified.';

            if(!is_null($user) ){

                if(!$user->email_verified_at) {
                    $user->email_verified_at = date('Y-m-d H:i:s');
                    $user->save();
                    $message = "Your e-mail is verified. You can now login.";
                } else {
                    $message = "Your e-mail is already verified. You can now login.";
                }
            }

            if ($domain) {
                    $tenantId = $domain->tenant_id;
                    $tenant = Tenant::find($tenantId);
                    Tenancy::initialize($tenant);
                    DB::table('users')->where(['email' => $user->email])->update(['email_verified_at' => date('Y-m-d H:i:s') ]);


            }

          return redirect()->route('login')->with('success', $message);
    }

    public function ResendVerificationCode(Request $request){
        $user = User::where('email', $request->input('email'))->first();
        $token = ($user->verify_token)  ? $user->verify_token : Str::random(64);
        $user->update(['verify_token' => $token ]);
        $this->SendVerificationEmail($token ,$request,$user->domain);
        return redirect('/login')->with('success', 'Please check your email for verification.');
    }

    public function SendVerificationEmail($token,$request,$domain){

        Mail::send('email.emailVerificationEmail', ['token' => $token,'domain' => $domain], function($message) use($request){
              $message->to($request->email);
              $message->subject('Email Verification Mail');
          });
    }


}

