<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Api\{
    AuthController,
    UserController,
    RoleController,
    VenueController,
    NewBookingController,
    QrCodeDoorUnlockApiController,
    VisitorBookingController,
    PermissionController
};

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::post('/twilio/status/callback', [NewBookingController::class, 'handleStatusUpdate'])->name('twillio.status.callback');
Route::post('/twilio/status/callback/wa', [NewBookingController::class, 'handleStatusUpdateWhatsApp'])->name('twillio.status.callback.whatsapp');


Route::post('/twilio/status/callback/notification', [NewBookingController::class, 'handleStatusUpdateNotification'])->name('twillio.status.callback.notification');
Route::post('/twilio/status/callback/temp', [NewBookingController::class, 'handleStatusUpdateVisitorTemp'])->name('twillio.status.callback.temp');

Route::any('/door/open', [QrCodeDoorUnlockApiController::class, 'OpenDoor']);
Route::any('/door/door_heart_beat', [QrCodeDoorUnlockApiController::class, 'HeartBeat']);

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Route::any('/send/lead/{listid}', [TicketWebhook::class, 'FetchData']);

Route::post('/booksubmit', [VisitorBookingController::class, 'WaitingPage'])->name('booking.submit');

//  Version 2 APi Codes

Route::post('/login', [AuthController::class, 'Login']);


Route::middleware('auth:sanctum')->group(function () {

    Route::post('/user/store', [UserController::class, 'store']);
    Route::post('/user/update/{id}', [UserController::class, 'update']);
    Route::get('/user/get/{id}', [UserController::class, 'show']);
    Route::get('/user/getall', [UserController::class, 'index']);
    Route::post('/user/delete/{id}', [UserController::class, 'destroy']);


    Route::post('/role/store', [RoleController::class, 'store']);
    Route::post('/role/update/{id}', [RoleController::class, 'update']);
    Route::get('/role/get/{id}', [RoleController::class, 'show']);
    Route::get('/role/getall', [RoleController::class, 'index']);
    Route::post('/role/delete/{id}', [RoleController::class, 'destroy']);


    Route::post('/permission/store', [PermissionController::class, 'store']);
    Route::post('/permission/update/{id}', [PermissionController::class, 'update']);
    Route::get('/permission/get/{id}', [PermissionController::class, 'show']);
    Route::get('/permission/getall', [PermissionController::class, 'index']);
    Route::post('/permission/delete/{id}', [PermissionController::class, 'destroy']);

    // venue Create Api started here
    Route::post('/venue/store', [VenueController::class, 'store']);
    Route::post('/venue/update/{id}', [AuthController::class, 'update']);
    Route::get('/venue/get/{id}', [AuthController::class, 'show']);
    Route::get('/venue/getall', [AuthController::class, 'index']);
    Route::post('/venue//delete', [AuthController::class, 'destroy']);
    // venue Create Api ends here
});
