<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\{
    PermissionController,
    RoleController,
    UserController,
    AuthController,
    HomeController,
    VenueController,
    VistorsController,
    VenueCountryController
};
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Artisan;

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
Route::get('/run/queue', function () {
    Artisan::call('migrate:refresh'); // Replace with the name of your custom command
    Artisan::call('db:seed',['--class' => 'DatabaseSeeder']);
    return 'Scheduled task triggered successfully.';
});

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

Route::get('/book/confirmation/{id}', [HomeController::class, 'bookingConfirmation'])->name('book.confirmation');
Route::get('/book/cancel/{id}', [HomeController::class, 'bookingConfirmation'])->name('book.cancel');
Route::get('/book/reschudule/{id}', [HomeController::class, 'bookingConfirmation'])->name('book.reschudule');
Route::post('/book/sent-otp', [HomeController::class, 'SendOtp'])->name('send-otp');
Route::post('/book/verify-otp', [HomeController::class, 'verify'])->name('verify-otp');

Auth::routes();

Route::post('/post-login', [AuthController::class, 'Login'])->name('post-login');
Route::post('/post-signup', [AuthController::class, 'Signup'])->name('post-signup');
Route::get('/account/verify/{token}', [AuthController::class, 'verifyAccount'])->name('user.verify');
Route::post('/account/resend', [AuthController::class, 'ResendVerificationCode'])->name('user.resend');
Route::get('/home', [App\Http\Controllers\HomeController::class, 'home'])->name('home');

Route::get('/sendEmail', [App\Http\Controllers\HomeController::class, 'sendEmail'])->name('sendEmail');
Route::post('/detect-liveness',  [HomeController::class,'detectLiveness']);
Route::group(['middleware' => ['auth'],'prefix' => 'admin'], function() {
    Route::get('/home', [App\Http\Controllers\HomeController::class, 'home'])->name('home');
    Route::resource('roles', RoleController::class);
    Route::resource('users', UserController::class);
    Route::resource('permissions',PermissionController::class);
    Route::resource('venues',VenueController::class);
    Route::resource('visitor',VistorsController::class);
    Route::resource('country',VenueCountryController::class);


});
