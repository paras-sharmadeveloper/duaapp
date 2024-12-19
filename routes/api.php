<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\{AuthController, NewBookingController, QrCodeDoorUnlockApiController, TicketWebhook,WhatsAppController,TwillioIVRHandleController,VisitorBookingController};
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





