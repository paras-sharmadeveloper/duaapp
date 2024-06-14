<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\{Venue, VenueSloting, VenueAddress, Vistors, Country,  Timezone, Reason, JobStatus, WorkingLady};
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use App\Jobs\{FaceRecognitionJob, WhatsAppConfirmation};
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;

class VisitorBookingController extends Controller
{
    public $venueCountry;
    public $venueAddress;

    public $venueAddressQuery;
    function __construct()
    {

        $this->venueCountry =  Venue::where(['iso' => 'PK'])->get()->first();
        $this->venueAddress = VenueAddress::where('venue_id', $this->venueCountry->id)
            ->whereDate('venue_date', date('Y-m-d'))->first();
        $this->venueAddressQuery = VenueAddress::whereDate('venue_date', date('Y-m-d'));
    }


    public function checkStatusForJob($jobId)
    {
        $jobStatus = JobStatus::where(['job_id' => $jobId, 'status' => 'completed'])->get()->first();
        if (!empty($jobStatus)) {
            $result = $jobStatus['result'];
            // $result = json_decode($jobStatus['result']);
            if ($result['status']) {
                $userInputs = json_decode($jobStatus['user_inputs'], true);
                // $userInputs = $jobStatus['user_inputs'];
                $inputs = $userInputs['inputs'];
                $uuid = Str::uuid()->toString();
                // Create a new Visitor record
                $visitor = new Vistors();
                $visitor->slot_id = $inputs['slotId'];
                $visitor->dua_type = $inputs['dua_type'];
                $visitor->working_lady_id = $inputs['working_lady_id'];
                $visitor->dua_type = $inputs['duaType'];
                $visitor->user_timezone = $inputs['timezone'];
                $visitor->lang = $inputs['lang'];
                $visitor->country_code = (strpos($inputs['country_code'], '+') === 0)  ? $inputs['country_code'] : '+' . $inputs['country_code'];
                // $visitor->country_code = (strpos($inputs['country_code'],'+')) ? $inputs['country_code'] : '+'.$inputs['country_code'];
                $visitor->phone = $inputs['mobile'];
                $visitor->is_whatsapp = 'yes';
                $visitor->booking_uniqueid = $uuid;
                $visitor->booking_number = $inputs['tokenId']; //
                $visitor->user_ip = (isset($inputs['user_ip'])) ? $inputs['user_ip'] : null;
                $visitor->recognized_code = (!empty($result)) ?  $result['recognized_code'] : null;
                $visitor->meeting_type = 'on-site';
                $visitor->source = 'Website';
                $workingLady = WorkingLady::where('qr_id', $inputs['QrCodeId'])->where('is_active', 'active')->count();


                if ($workingLady == 0 && !empty($inputs['working_lady_id'])) {
                    return response()->json([
                        'errors' => [
                            'status' => false,
                            'message' => 'This Qr is not valid or not active',
                            'message_ur' => 'یہ Qr درست نہیں ہے یا فعال نہیں ہے۔',
                        ]
                    ], 422);
                }

                $visitor->token_status = 'vaild';
                $visitor->save();
                $bookingId = $visitor->id;
                WhatsAppConfirmation::dispatch($bookingId)->onQueue('whatsapp-notification')->onConnection('database');

                return response()->json([
                    'message' => 'Booking submitted successfully',
                    "status" => true,
                    'bookingId' => $uuid,
                    'redirect_url' => route('booking.status', [$uuid])
                ], 200);
            } else {
                return response()->json([
                    'errors' => [
                        'status' => false,
                        'message' => 'You already book Token with us.Please try after few days',
                        'message_ur' => 'آپ پہلے سے ہی ہمارے ساتھ ٹوکن بک کر چکے ہیں۔ براہ کرم کچھ دنوں کے بعد کوشش کریں۔',
                    ]
                ], 455);
            }
        } else {

            return response()->json([
                'status' => false,
                'message' => 'Job not Completed',
            ], 422);
        }
    }

    public function getAjax(Request $request)
    {
        $type = $request->input('type');
        $id = $request->input('id');
        $today = Carbon::now();
        $NextDate = $today->addDay();
        $newDate = $NextDate->format('Y-m-d');

        if ($type == 'get_city' || $type == 'normal_person' || $type == 'working_lady') {
            $countryId = $this->venueCountry;
            if (empty($countryId)) {
                return response()->json([
                    'status' => false,
                    'message' => 'There is no Venue in the system. Please try after some time',
                    'message_ur' => 'سسٹم میں کوئی وینیو نہیں ہے۔ تھوڑی دیر بعد کوشش کریں۔',

                ]);
            } else {
                $venuesListArr =  $this->venueAddress;
            }


            if (empty($venuesListArr)) {
                return response()->json([
                    'status' => false,
                    'data' => []
                ]);
            }
            $dataArr = [];

            $cityName = $this->venueAddress->city;
            $dataArr['city'][$cityName] = [
                'name' => $cityName,
                'id' => $this->venueAddress->venue->id,
                'type' => $this->venueAddress->type,
                'venue_address_id' => $this->venueAddress->id,
            ];


            return response()->json([
                'status' => !(empty($dataArr)) ? true : false,
                'data' => $dataArr,
                'date' => $newDate
            ]);
        }
        if ($type == 'get_date') {

            $venuesListArr = $this->venueAddressQuery->where(['venue_id' => $request->input('id'), 'city' => $request->input('optional')])->orderBy('venue_date', 'ASC')->first();
            if (!empty($venuesListArr)) {
                return response()->json([
                    'status' =>  true,
                ]);
            } else {
                return response()->json([
                    'status' => false
                ]);
            }
        }

        if ($type == 'get_slot_book') {

            $currentTimezone = $request->input('timezone', 'America/New_York');
            $newDate = date('Y-m-d', strtotime(date('Y-m-d') . ' +1 day'));
            $today = getCurrentContryTimezone($request->input('id'));

            $venuesListArr = $this->venueAddressQuery->where(['venue_id' => $request->input('id'), 'city' => $request->input('optional')])->first();
            if (!empty($venuesListArr)) {

                $status = TokenBookingAllowed($venuesListArr->venue_date, $venuesListArr->venue_date_end,  $venuesListArr->timezone);
                if (!$status['allowed']) {
                    return response()->json([
                        'status' => false,
                        'message' => $status['message'],
                        'message_ur' => $status['message_ur'],

                    ]);
                }
                $phoneCode = session('phoneCode');
                $country = Country::where('phonecode', $phoneCode)->first();
                $venue_available_country =  json_decode($venuesListArr->venue_available_country);
                $userCountry = VenueAvilableInCountry($venue_available_country, $country->id);

                if (!$userCountry['allowed']) {
                    session()->forget('phoneCode');


                    return response()->json([
                        'status' => false,
                        'message' => $userCountry['message'],
                        'message_ur' => $userCountry['message_ur'],
                        'phoneCode' => $phoneCode

                    ]);
                }
            }

            if (!empty($venuesListArr) && $venuesListArr->status == 'inactive') {
                return response()->json([
                    'status' => false,
                    'message' => 'For some reason currently this venue not accepting bookings. Please try after some time. Thank You',
                    'message_ur' => 'کسی وجہ سے فی الحال یہ مقام بکنگ قبول نہیں کر رہا ہے۔ تھوڑی دیر بعد کوشش کریں۔ شکریہ',

                ]);
            }

            return response()->json([
                'city' => $request->input('optional'),
                'timezone' => $request->input('timezone'),
                'duaType' => $request->input('duaType'),
                'venueId' =>  $venuesListArr->id,
                'status' => true

            ]);
        }
    }

    public function WaitingPage(Request $request)
    {

        $vaildation = [];
        $validation = [
            'mobile' => 'required|string|digits:10|max:10',
            'country_code' => 'required'
        ];

        $messages = [];
        $validator = Validator::make($request->all(), $validation, $messages);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }



        $tokenStatus = $this->FinalBookingCheck($request);
        // return response()->json([
        //     'message' => $tokenStatus,
        //     "status" => false,
        // ], 422);

        $rejoin = 0;
        $userInputs = [];
        if ($tokenStatus['status']) {
            $slotId = $tokenStatus['slot_id'];
            $tokenId = $tokenStatus['tokenId'];
            $venueAddress  = $tokenStatus['venuesListArr'];
            $rejoin = $venueAddress->rejoin_venue_after;
            $userInputs['inputs'] = [];
            $userInputs['inputs']['slotId'] = $slotId ?? null;
            $userInputs['inputs']['tokenId'] = $tokenId ?? null;
            $userInputs['inputs']['booking_number'] = $tokenId ?? null; // Assuming booking_number should be tokenId
            $userInputs['inputs']['user_ip'] = $request->ip();

            // Exclude 'captured_user_image' from $request->except
            $userInputs['inputs'] += $request->except('captured_user_image');


            $captured_user_image = $request->input('captured_user_image');
            if (!empty($captured_user_image)) {

                $myImage = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $captured_user_image));
                $jobId = (string) Str::uuid();
                $filename = 'selfie_' . time() . '.jpg';
                $objectKey = $this->encryptFilename($filename);
                Storage::disk('s3')->put($objectKey, $myImage);


                try {
                    //code...
                    $uploadSuccess = Storage::disk('s3')->put($objectKey, $myImage);
                } catch (\Exception $e) {
                    // Log::error('Failed to upload file to S3.'.$e->getMessage());
                    return response()->json([
                        'errors' =>  ['message' => 'Unable to upload file '.$objectKey.'   '.$e->getMessage().' ']
                    ], 422);
                }


                if ($uploadSuccess) {
                    FaceRecognitionJob::dispatch($jobId, $rejoin, $objectKey)->onQueue('face-recognition')->onConnection('database')->delay(Carbon::now()->addSeconds(10));

                    JobStatus::create([
                        'job_id' => $jobId,
                        'status' => 'pending',
                        'user_inputs' => json_encode($userInputs),
                    ]);

                    Log::info('Job dispatched with ID: ' . $jobId);
                    return response()->json([
                        'message' => 'Moving to Waiting Page',
                        "status" => true,
                        'redirect_url' => route('booking.waiting', [$jobId]) . '?test='.$objectKey
                    ], 200);
                    // Proceed with further actions
                } else {
                    // Handle the case where file upload failed
                    // Log::error('Failed to upload file to S3.'.$myImage);
                    return response()->json([
                        'errors' =>  ['message' => 'Unable to upload file adasd '.$objectKey.' ','d' => $uploadSuccess ]
                    ], 422);
                }
            }
        } else {
            return response()->json([
                'errors' =>  $tokenStatus
            ], 422);
        }
    }

    private function sanitizeBase64($data)
    {
        // Remove any characters that are not part of the Base64 alphabet
        $data = preg_replace('/[^A-Za-z0-9+\/=]/', '', $data);

        // Ensure the string is UTF-8 encoded
        return mb_convert_encoding($data, 'UTF-8', 'UTF-8');
    }

    public function waitingPageShow($id)
    {
        return view('frontend.multistep.inc.waiting', compact('id'));
    }

    public function FinalBookingCheck($request)
    {

        $duaType = $request->input('duaType');
        $country_code = $request->input('country_code');
        // $today = getCurrentContryTimezone($request->input('venueId'));

        $venuesListArr = $this->venueAddressQuery->where('city',  $request->input('city'))->first();
        $rejoin = $venuesListArr->rejoin_venue_after;
        if ($duaType == 'dua' && !empty($venuesListArr->reject_dua_id)) {
            $reason  = Reason::find($venuesListArr->reject_dua_id);
            return [
                'status' => false,
                'message' => $reason->reason_english,
                'message_ur' => $reason->reason_urdu,
            ];
        }
        if ($duaType == 'dum' && !empty($venuesListArr->reject_dum_id)) {
            $reason  = Reason::find($venuesListArr->reject_dum_id);
            return [
                'status' => false,
                'message' => $reason->reason_english,
                'message_ur' => $reason->reason_urdu,
            ];
        }
        if (!empty($venuesListArr) && $venuesListArr->status == 'inactive') {
            return [
                'status' => false,
                'message' => 'For some reason currently this venue not accepting bookings. Please try after some time. Thank You',
                'message_ur' => 'کسی وجہ سے فی الحال یہ مقام بکنگ قبول نہیں کر رہا ہے۔ تھوڑی دیر بعد کوشش کریں۔ شکریہ',

            ];
        }

        $rejoinStatus = userAllowedRejoin($request->input('mobile'), $rejoin);
        if ($rejoinStatus['allowed'] == false) {

            return [
                'status' => false,
                'message' => $rejoinStatus['message'],
                'message_ur' => $rejoinStatus['message_ur']

            ];
        }



        if (!empty($venuesListArr)) {

            $status = TokenBookingAllowed($venuesListArr->venue_date, $venuesListArr->venue_date_end,  $venuesListArr->timezone);
            $phoneCode = (session('phoneCode')) ? session('phoneCode') : $country_code;
            $phoneCode = session('phoneCode');
            $country = Country::where('phonecode', $phoneCode)->first();
            $venue_available_country =  json_decode($venuesListArr->venue_available_country);
            $userCountry = VenueAvilableInCountry($venue_available_country, $country->id);

            if (!$userCountry['allowed']) {
                session()->forget('phoneCode');

                return [
                    'status' => false,
                    'message' => $userCountry['message'],
                    'message_ur' => $userCountry['message_ur'],
                    'phoneCode' => $phoneCode

                ];
            }

            $status = isAllowedTokenBooking($venuesListArr->venue_date, $venuesListArr->slot_appear_hours, $venuesListArr->timezone);

            if ($status['allowed']) {

                session()->forget('phoneCode');
                $tokenIs = VenueSloting::where('venue_address_id', $venuesListArr->id)
                    ->whereNotIn('id', Vistors::pluck('slot_id')->toArray())
                    ->where(['type' => $request->input('duaType')])
                    ->orderBy('id', 'ASC')
                    ->select(['venue_address_id', 'token_id', 'id'])->first();

                if (!empty($tokenIs)) {
                    return [
                        'status' =>  true,
                        'tokenId' => $tokenIs->token_id,
                        'slot_id' => $tokenIs->id,
                        'venuesListArr' => $venuesListArr,
                        'rejoin' => $rejoinStatus
                    ];
                } else {

                    return [
                        'status' =>  false,
                        'message' => 'All Tokens Dua / Dum Appointments have been issued for today. Kindly try again next week. For more information, you may send us a message using "Contact Us" pop up button below.',
                        'message_ur' => 'آج کے لیے تمام دعا/دم کے ٹوکن جاری کر دیے گئے ہیں۔ براہ مہربانی اگلے ہفتے دوبارہ کوشش کریں۔ مزید معلومات کے لیے، آپ نیچے "ہم سے رابطہ کریں" پاپ اپ بٹن کا استعمال کرتے ہوئے ہمیں ایک پیغام بھیج سکتے ہیں۔',
                    ];
                }
            } else {

                $message =  [
                    'status' => false,
                    'message' => $status['message'],
                    'message_ur' => $status['message_ur'],

                ];
            }
        } else {
            $message =  [
                'status' =>  false,
                'message' => 'There is no Dua / Dum token booking available for today. Please try again later.',
                'message_ur' => 'آج کے لیے کوئی دعا/دم ٹوکن بکنگ دستیاب نہیں ہے۔ براہ کرم کچھ دیر بعد کوشش کریں.',

            ];
        }
        return $message;
    }

    protected function encryptFilename($filename)
    {
        $key = hash('sha256', date('Y-m-d') . $filename . now());
        //  $hashedPassword = Hash::make($filename.now());
        return $key;
    }
}
