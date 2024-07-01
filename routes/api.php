<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\{NewBookingController, QrCodeDoorUnlockApiController, TicketWebhook,WhatsAppController,TwillioIVRHandleController,VisitorBookingController};
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


Route::post('/door/open', [QrCodeDoorUnlockApiController::class, 'OpenDoor'])->name('open.door');
Route::post('/door/door_heart_beat', [QrCodeDoorUnlockApiController::class, 'HeartBeat'])->name('heart.beat');
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Route::any('/send/lead/{listid}', [TicketWebhook::class, 'FetchData']);

Route::post('/booksubmit', [VisitorBookingController::class, 'WaitingPage'])->name('booking.submit');


// Route::post('/handle-incoming-message', [WhatsAppController::class, 'handleWebhook']);
// Route::post('/handle-fallback', [WhatsAppController::class, 'handleFallback']);
// https://app.kahayfaqeer.org/api//twilio/status




