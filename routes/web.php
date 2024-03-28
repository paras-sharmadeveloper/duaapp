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
    VenueCountryController,
    BookingController,
    SiteAdminController,
    VideoConferenceController,
    NotificationController,
    AgGridManagement,
    TwillioIVRHandleController,
    PrintController,
    ReasonController
};
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Artisan;
use App\Events\MyEvent;
use Illuminate\Support\Facades\Mail;
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



Route::post('/ivr/welcome', [TwillioIVRHandleController::class, 'handleIncomingCall'])
    ->withoutMiddleware(['web', 'verified'])
    ->name('ivr.welcome');

Route::post('/ivr/dua/option', [TwillioIVRHandleController::class, 'handleDuaOption'])
    ->withoutMiddleware(['web', 'verified'])
    ->name('ivr.dua.option');

Route::post('/ivr/welcome/inputs', [TwillioIVRHandleController::class, 'handleWelcomeInputs'])
    ->withoutMiddleware(['web', 'verified'])
    ->name('ivr.welcome.after-lang');




Route::post('/ivr/start/flow', [TwillioIVRHandleController::class, 'StartFlow'])
    ->withoutMiddleware(['web', 'verified'])
    ->name('ivr.start');

Route::post('/ivr/pickcity', [TwillioIVRHandleController::class, 'handleCity'])
    ->withoutMiddleware(['web', 'verified'])
    ->name('ivr.pickcity');

// Route::post('/ivr/dates', [TwillioIVRHandleController::class, 'handleDates'])
//     ->withoutMiddleware(['web', 'verified'])
//     ->name('ivr.dates');

// Route::post('/ivr/time', [TwillioIVRHandleController::class, 'handleSlots'])
//     ->withoutMiddleware(['web', 'verified'])
//     ->name('ivr.time');
Route::post('/ivr/makebooking', [TwillioIVRHandleController::class, 'MakeBooking'])
    ->withoutMiddleware(['web', 'verified'])
    ->name('ivr.makebooking');

Route::post('/ivr/handle-timeout', [TwillioIVRHandleController::class, 'handleTimeout'])
    ->withoutMiddleware(['web', 'verified'])
    ->name('ivr.handle.timeout');

    Route::get('/test-mail', function () {
        try {
            // Replace 'recipient@example.com' with the actual recipient email address
            $recipient = 'parassharmadeveloper@gmail.com';

            // Send a simple test email
            Mail::raw('This is a test email.', function ($message) use ($recipient) {
                $message->to($recipient)
                    ->subject('Test Email');
            });

            return 'Email sent successfully.';
        } catch (Exception $e) {
            return 'Failed to send email: ' . $e->getMessage();
        }
    });




Route::get('/run/queue', function () {
    Artisan::call('migrate:fresh'); // Replace with the name of your custom command
    Artisan::call('db:seed', ['--class' => 'AdminSeeder']);
    return 'Scheduled task triggered successfully.';
});


Route::get('/config/clear', function () {
    Artisan::call('config:cache');
    Artisan::call('config:clear');

    return 'Scheduled task triggered successfully.' . env('IP_API_KEY');
});

Route::get('/run/command', function () {
    $type = request()->type;
    if ($type == 'migrate') {
        Artisan::call('migrate');
    }
    if ($type == 'fresh') {
        Artisan::call('migrate:fresh'); // Replace with the name of your custom command
        Artisan::call('db:seed', ['--class' => 'AdminSeeder']);
        return 'Scheduled task triggered successfully.';
    }
    if ($type == 'refresh') {
        Artisan::call('migrate:refresh'); // Replace with the name of your custom command
        Artisan::call('db:seed', ['--class' => 'AdminSeeder']);
        return 'Scheduled task triggered successfully.';
    }


    return 'Scheduled task triggered successfully.';
});

Route::get('/welcome', function () {
    return view('welcome');
});

Route::get('/print', [PrintController::class, 'printReceipt'])->name("print");

Route::get('/print-this', function () {

    return view('frontend.print-token');

    $text = "thankyou for your visit at Dua Ghar\n\nYour Token Number #150\n\nVerified\nThank you\nTeam KahayFaqeer";

    // Open a connection to the printer
    $printer = fopen("php://printer", "w");

    // Check if the printer connection is open
    if ($printer) {
        // Send the text to the printer
        fwrite($printer, $text);

        // Close the printer connection
        fclose($printer);

        echo "Text sent to printer successfully!";
    } else {
        echo "Failed to open printer connection!";
    }


    // return view('welcome');
});


Route::get('/', function () {
    if (Auth::check()) {
        return redirect('home');
    } else {
        return redirect('login');
    }
});





Route::get('/video/{bookingId}/join-conference', [VideoConferenceController::class, 'joinConferenceFrontend'])->name('join.conference.frontend');
Route::get('/dua/{locale?}', [HomeController::class, 'index'])->name('book.show');
Route::post('/book/ajax', [HomeController::class, 'getAjax'])->name('booking.ajax');
Route::post('/book/get/users', [HomeController::class, 'getTheripistByIp'])->name('booking.get.users');

Route::post('/book/timezone/ajax', [HomeController::class, 'getTimzoneAjax'])->name('get-slots-timezone');

Route::post('/book/submit', [HomeController::class, 'BookingSubmit'])->name('booking.submit');

Route::get('/book/status/{id}', [BookingController::class, 'CustomerBookingStatus'])->name('booking.status');

Route::get('/book/confirm/spot', [BookingController::class, 'ConfirmBookingAvailabilityShow'])->name('booking.confirm-spot');
Route::post('/book/confirm/spot/post', [BookingController::class, 'ConfirmBookingAvailability'])->name('booking.confirm-spot.post');
Route::post('/book/confirm/spot/otp/post', [BookingController::class, 'ConfirmBookingAvailability'])->name('booking.confirm-spot.otp.post');

Route::get('/qr-code/{id}', [BookingController::class, 'generateQRCode'])->name('qr.code');

Route::get('/qr-scan/{id}', [BookingController::class, 'scanQRCode'])->name('qr.scan');

Route::post('/process-scan', [BookingController::class, 'processScan'])->name('process-scan');

Route::get('/scan-qr', [BookingController::class, 'showQrScan'])->name('qr.show.scan');




Route::get('/book/confirmation/{id}', [HomeController::class, 'bookingConfirmation'])->name('book.confirmation');

Route::any('/book/cancel/{id}', [BookingController::class, 'BookingCancle'])->name('book.cancle');
Route::any('/book/cancel/opt/{id}', [BookingController::class, 'BookingCancle'])->name('book.cancle.otp');
Route::any('/book/reschedule/{id}', [BookingController::class, 'BookingReschdule'])->name('book.reschdule');
Route::get('/generate-pdf/{id}', [BookingController::class, 'generatePDF'])->name('generate-pdf');

Route::post('/book/sent-otp', [HomeController::class, 'SendOtpUser'])->name('send-otp');
Route::post('/book/get-slots', [HomeController::class, 'getSlotsAjax'])->name('get-slots');




Route::post('/book/verify-otp', [HomeController::class, 'verify'])->name('verify-otp');
Route::post('/book/check-available/slot', [HomeController::class, 'CheckAvilableSolt'])->name('check-available');
Route::get('/booking/thankyou/{bookingId}', [HomeController::class, 'thankyouPage'])->name('thankyou-page');
Route::get('/screen/status/{id}', [SiteAdminController::class, 'WaitingQueueShow'])->name('waiting-queue');
Route::get('/status', [HomeController::class, 'StatusLcdScreen'])->name('status-screen');

Route::get('/get-states', [VenueCountryController::class, 'getStates'])->name('get-states');
Route::post('/add-city-state', [VenueCountryController::class, 'CityImagesUplaod'])->name('add-city-state');
Route::post('/remove-city-state', [VenueCountryController::class, 'CityImagesRemove'])->name('remove-city-state');

Auth::routes(['register' => false]);

Route::post('/post-login', [AuthController::class, 'Login'])->name('post-login');
Route::post('/post-signup', [AuthController::class, 'Signup'])->name('post-signup');
Route::get('/account/verify/{token}', [AuthController::class, 'verifyAccount'])->name('user.verify');
Route::post('/account/resend', [AuthController::class, 'ResendVerificationCode'])->name('user.resend');
Route::get('/home', [App\Http\Controllers\HomeController::class, 'home'])->name('home');
Route::post('/check-particepent/status', [VideoConferenceController::class, 'CheckParticpentStatus'])->name('checkparticepent-status');
Route::get('/sendEmail', [App\Http\Controllers\HomeController::class, 'sendEmail'])->name('sendEmail');
Route::post('/detect-liveness',  [HomeController::class, 'detectLiveness']);
Route::post('/ask-to-join/meeting', [VideoConferenceController::class, 'AskToJoin'])->name('asktojoin');
Route::post('/site/queue/{id}/vistor/update', [SiteAdminController::class, 'VisitorUpdate'])->name('siteadmin.queue.vistor.update');

Route::group(['middleware' => ['auth'], 'prefix' => 'admin'], function () {


    Route::get('/reasons', [ReasonController::class, 'index'])->name('reasons.index');
    Route::get('/reasons/create', [ReasonController::class, 'create'])->name('reasons.create');
    Route::post('/reasons', [ReasonController::class, 'store'])->name('reasons.store');
    Route::get('/reasons/{id}/edit', [ReasonController::class, 'edit'])->name('reasons.edit');
    // Update an existing reason
    Route::put('/reasons/{id}', [ReasonController::class, 'update'])->name('reasons.update');

    // Delete a reason
    Route::delete('/reasons/{id}', [ReasonController::class, 'destroy'])->name('reasons.destroy');




    // Route::get('/home', [App\Http\Controllers\HomeController::class, 'home'])->name('home');
    Route::resource('roles', RoleController::class);
    Route::resource('users', UserController::class);
    Route::resource('permissions', PermissionController::class);
    Route::resource('venues', VenueController::class);
    Route::resource('visitor', VistorsController::class);
    Route::resource('country', VenueCountryController::class);

    Route::post('/venue/pause/{id}', [VenueController::class, 'pauseResumeVenue'])->name('venues.pause');



    Route::post('/grid/fetch/booking', [AgGridManagement::class, 'getDataMessageLog'])->name('fetch.bookings');

    Route::get('/notifications', [NotificationController::class, 'index'])->name('notification.get');
    Route::post('/notifications/{id}/read', [NotificationController::class, 'markAsRead'])->name('notification.mark.read');
    Route::delete('/visitor/{id}/delete', [VistorsController::class, 'DeleteNow'])->name('visitor.delete');
    Route::get('/book/{venueId}/add', [HomeController::class, 'bookingAdmin'])->name('book.add');
    Route::any('delete-row', [HomeController::class, 'deleteRows'])->name('delete-row');
    // check-available
    Route::post('/update/status', [UserController::class, 'updateStatus'])->name('update.status');
    Route::get('/site/queue', [SiteAdminController::class, 'ShowQueue'])->name('siteadmin.queue.show');
    Route::get('/site/pending/verification', [SiteAdminController::class, 'ShowQueue'])->name('siteadmin.pending.show');
    Route::get('/site/queue/{id}/show', [SiteAdminController::class, 'ShowQueueList'])->name('siteadmin.queue.list');
    Route::get('/site/queue/{id}/pending', [SiteAdminController::class, 'ShowQueueList'])->name('siteadmin.pending.list');
    Route::get('/site/search/visitors', [SiteAdminController::class, 'searchVisitors'])->name('search.visitors');


    Route::get('/site/queue/list', [VideoConferenceController::class, 'fieldAdminRequest'])->name('siteadmin.queue.list.request');




    // Route::get('/video-conference', [VideoConferenceController::class, 'index']);
    // Route::any('/start-conference', [VideoConferenceController::class, 'startConference']);
    // Route::get('/create-conference', [VideoConferenceController::class, 'createConference'])->name('conference.create');
    // Route::post('/create-conference/submit', [VideoConferenceController::class, 'createConferencePost'])->name('create-conference');
    // Route::get('/join-conference/start', [VideoConferenceController::class, 'StartConferenceShow'])->name('join.conference.show');
    // Route::get('/join-conference/meeting/start', [VideoConferenceController::class, 'joinConference'])->name('join.conference');
    // Route::post('/join-conference/post/{roomId}', [VideoConferenceController::class, 'joinConferencePost'])->name('join.conference.post');
    Route::post('/visitor/request/list', [VideoConferenceController::class, 'VisitorRequests'])->name('visitor.list');
    Route::get('/design', [VideoConferenceController::class, 'design'])->name('design');
    Route::get('/booking/create', [VistorsController::class, 'create'])->name('booking.create');
    Route::post('/booking/store', [VistorsController::class, 'storeOrUpdate'])->name('booking.store');
    Route::post('/booking/update/{id}', [VistorsController::class, 'storeOrUpdate'])->name('booking.update');
    Route::get('/bookings/list', [VistorsController::class, 'list'])->name('booking.list');
    Route::get('/bookings/edit/{id}', [VistorsController::class, 'edit'])->name('booking.edit');
    Route::post('/bookings/delete/{id}', [VistorsController::class, 'destroy'])->name('booking.delete');
});
// RMb28cc2048ae67bf97983cab765febaa6
