<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\{
    PermissionController,
    RoleController,
    UserController,
    AuthController,
    HomeController,
    VenueController,
    VistorsController
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
Route::get('/book/confirmation/{id}', [HomeController::class, 'bookingConfirmation'])->name('book.confirmation');
 

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
    Route::resource('visitor',VistorsController::class); 
    Route::get('/venues/add-country', [VenueController::class, 'venueCountryShow'])->name('venues.add-country'); 
    Route::get('/venues/{id}/edit-country', [VenueController::class, 'venueCountryEdit'])->name('venues.edit-country'); 
    Route::post('/venues/{id}/edit-country', [VenueController::class, 'venueCountryUpdate'])->name('venues.update-country'); 
    
    Route::get('/venues/list-country', [VenueController::class, 'venueCountryList'])->name('venues.list-country'); 
    Route::post('/venues/add-country', [VenueController::class, 'venueCountryStore'])->name('venues.store-country');  
    Route::delete('/venues/delete/{id}', [VenueController::class, 'venueCountryDelete'])->name('venues.delete-country'); 
    
}); 