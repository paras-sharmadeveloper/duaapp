<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\{
    PermissionController,
    RoleController,
    UserController,
    AuthController,
    HomeController,
    VenueController
}; 
use Illuminate\Support\Facades\Auth; 
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    if(Auth::check()){
        return redirect('home');
    }else{
        return redirect('login');
    } 
});
 
 
Route::get('/book/seat', [HomeController::class, 'index'])->name('book.show');
Route::post('/book/ajax', [HomeController::class, 'getAjax'])->name('booking.ajax');
Route::post('/book/submit', [HomeController::class, 'BookingSubmit'])->name('booking.submit');

Auth::routes();





Route::post('/post-login', [AuthController::class, 'Login'])->name('post-login');
Route::post('/post-signup', [AuthController::class, 'Signup'])->name('post-signup');
Route::get('/account/verify/{token}', [AuthController::class, 'verifyAccount'])->name('user.verify'); 
Route::post('/account/resend', [AuthController::class, 'ResendVerificationCode'])->name('user.resend'); 

Route::group(['middleware' => ['auth'],'prefix' => 'admin'], function() {
    Route::get('/home', [App\Http\Controllers\HomeController::class, 'home'])->name('home');
    Route::resource('roles', RoleController::class);
    Route::resource('users', UserController::class); 
    Route::resource('permissions',PermissionController::class); 
    Route::resource('venues',VenueController::class); 
}); 