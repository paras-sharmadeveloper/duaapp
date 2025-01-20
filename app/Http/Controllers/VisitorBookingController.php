<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\{Venue, VenueSloting, VenueAddress, Vistors, Country,  Reason, JobStatus, VisitorTemp, WorkingLady};
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use App\Jobs\{FaceRecognitionJob, WhatsAppConfirmation, WhatsappforTempUsers};

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\QueryException;
use Aws\Rekognition\RekognitionClient;
class VisitorBookingController extends Controller
{
    public $venueCountry;
    public $venueAddress;

    public $venueAddressQuery;
    function __construct()
    {

        $this->venueCountry =  Venue::where(['iso' => 'PK'])->get()->first();
        if($this->venueCountry){
            $this->venueAddress = VenueAddress::where('venue_id', $this->venueCountry->id)->whereDate('venue_date', date('Y-m-d'))->first();

        }
        $this->venueAddressQuery = VenueAddress::whereDate('venue_date', date('Y-m-d'));
    }

    public function checkStatusForJob($jobId)
    {
        $jobStatus = JobStatus::where(['job_id' => $jobId])->get()->first();

        if (!empty($jobStatus) && $jobStatus['status'] == 'completed' ) {
            $result = $jobStatus['result'];
            // $result = json_decode($jobStatus['result']);
            if ($result['status']) {

                try {
                    $userInputs = json_decode($jobStatus['user_inputs'], true);
                    // $userInputs = $jobStatus['user_inputs'];
                    $inputs = $userInputs['inputs'];


                    $tokenIs = VenueSloting::where('venue_address_id',$inputs['venueId'])
                    ->whereNotIn('id', Vistors::pluck('slot_id')->toArray())
                    ->where(['type' => $inputs['dua_type']])
                    ->orderBy('id', 'ASC')
                    ->select(['venue_address_id', 'token_id', 'id'])->first();

                    if(empty($tokenIs)){
                        return response()->json([
                            'errors' => [
                                'status' => false,
                                'refresh' => true,
                                'message' => 'All Token is issued for the day. please try after some days.',
                                'message_ur' => 'تمام ٹوکن دن کے لیے جاری کیے جاتے ہیں۔ براہ کرم کچھ دنوں کے بعد کوشش کریں۔',
                            ]
                        ], 455);
                    }

                    $tokenId = $tokenIs->token_id;
                    $slotId = $tokenIs->id;

                    $existingVisitor = Vistors::where('phone', $inputs['mobile'])
                               ->whereDate('created_at', date('Y-m-d'))
                               ->first();

                if (!$existingVisitor) {

                    $uuid = Str::uuid()->toString();
                    // Create a new Visitor record
                    $visitor = new Vistors();
                    $visitor->slot_id = $slotId;
                    $visitor->dua_type = $inputs['dua_type'];
                    $visitor->working_lady_id = $inputs['working_lady_id'];
                    $visitor->dua_type = $inputs['duaType'];
                    $visitor->user_timezone = $inputs['timezone'];
                    $visitor->lang = $inputs['lang'];
                    $visitor->country_code = (strpos($inputs['country_code'], '+') === 0)  ? $inputs['country_code'] : '+' . $inputs['country_code'];
                    $visitor->phone = $inputs['mobile'];
                    $visitor->is_whatsapp = 'yes';
                    $visitor->booking_uniqueid = $uuid;
                    $visitor->booking_number = $tokenId; //
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
                    $jobStatus->update(['entry_created' => 'Yes']);
                    WhatsAppConfirmation::dispatch($bookingId)->onQueue('whatsapp-notification');

                    return response()->json([
                        'message' => 'Booking submitted successfully',
                        "status" => true,
                        'bookingId' => $uuid,
                        'redirect_url' => route('booking.status', [$uuid])
                    ], 200);
                }else{

                    $jobStatus->update(['entry_created' => 'Duplicate']);

                        return response()->json([
                            'errors' => [
                                'status' => false,
                                'refresh' => true,
                                'message' => trans('messages.slot_id'),
                                'message_ur' => 'یہ ٹوکن اس سیکنڈ میں کسی اور نے بک کروایا ہے۔ ٹوکن دوبارہ بک کرنے کے لیے براہ کرم اپنے براؤزر کو ریفریش کریں۔ ایک ہی وقت میں سینکڑوں دوسرے لوگ بھی ٹوکن بک کرنے کی کوشش کر رہے ہیں۔ دوسرا بک کرنے کے لیے براہ کرم اپنی اسکرین ریفریش کریں۔'
                            ]
                        ], 455);


                }
                } catch (QueryException $e) {

                    $errorCode = $e->errorInfo[1];

                    if ($errorCode === 1062) {

                        $jobStatus->update(['entry_created' => 'Duplicate']);

                        return response()->json([
                            'errors' => [
                                'status' => false,
                                'refresh' => true,
                                'message' => trans('messages.slot_id'),
                                'message_ur' => 'یہ ٹوکن اس سیکنڈ میں کسی اور نے بک کروایا ہے۔ ٹوکن دوبارہ بک کرنے کے لیے براہ کرم اپنے براؤزر کو ریفریش کریں۔ ایک ہی وقت میں سینکڑوں دوسرے لوگ بھی ٹوکن بک کرنے کی کوشش کر رہے ہیں۔ دوسرا بک کرنے کے لیے براہ کرم اپنی اسکرین ریفریش کریں۔'
                            ]
                        ], 455);

                    } else {
                        $jobStatus->update(['entry_created' => 'Pending']);
                        return response()->json([
                            'errors' => [
                                'status' => false,
                                'refresh' => false,
                                'message' => $e->getMessage(),
                            ]
                        ], 455);
                    }


                } catch (\Exception $e) {
                    // Log any other exceptions
                    $jobStatus->update(['entry_created' => 'Error']);
                    // Log::error('Exception: ' . $e->getMessage());

                    return response()->json([
                        'errors' => [
                            'status' => false,
                            'message' =>  $e->getMessage(),
                        ]
                    ], 455);

                }
            } else {
                $jobStatus->update(['entry_created' => 'Already_booked']);
                return response()->json([
                    'errors' => [
                        'status' => false,
                        'message' => 'You already book Token with us.Please try after few days',
                        'message_ur' => 'آپ پہلے سے ہی ہمارے ساتھ ٹوکن بک کر چکے ہیں۔ براہ کرم کچھ دنوں کے بعد کوشش کریں۔',
                    ]
                ], 455);
            }
        }else if($jobStatus['status'] == 'error'){
            $jobStatus->update(['entry_created' => 'Error']);
            return response()->json([
                'errors' => [
                    'status' => false,
                    'refresh' => true,
                    'message' => 'There Might be some issue at backend please try after some time.',
                    'message_ur' => 'بیک اینڈ پر کچھ مسئلہ ہو سکتا ہے براہ کرم کچھ دیر بعد کوشش کریں۔',
                ]
            ], 455);
        } else if($jobStatus['status'] == 'token_finished'){
            $userInputs = json_decode($jobStatus['user_inputs'], true);
            $inputs = $userInputs['inputs'];
            $countryCode = (strpos($inputs['country_code'], '+') === 0)  ? $inputs['country_code'] : '+' . $inputs['country_code'];
            $mobile = $inputs['mobile'];

            $completeNumber =   $countryCode . $mobile;

            $temp =  VisitorTemp::create(['user_inputs' => $jobStatus['user_inputs']]);
            $jobStatus->update(['entry_created' => 'Yes']);

            WhatsappforTempUsers::dispatch($temp->id,  $completeNumber)->onQueue('whatsapp-temp-users');
            return response()->json([
                'errors' => [
                    'status' => false,
                    'refresh' => true,
                    'message' => 'All Token is issued for the day. please try after some days.',
                    'message_ur' => 'تمام ٹوکن دن کے لیے جاری کیے جاتے ہیں۔ براہ کرم کچھ دنوں کے بعد کوشش کریں۔',
                ]
            ], 455);
        }


        else {
            $jobStatus->update(['entry_created' => 'Job_incomplete']);
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

                $city = $venuesListArr->city;
                $timezoneA = $venuesListArr->timezone;
                if($city === 'London'){
                    $timezoneA = 'Europe/London';
                }else{
                    $timezoneA = $venuesListArr->timezone;
                }

                $status = TokenBookingAllowed($venuesListArr->venue_date, $venuesListArr->venue_date_end,  $timezoneA);
                if (!$status['allowed']) {
                    return response()->json([
                        'status' => false,
                        'message' => $status['message'],
                        'message_ur' => $status['message_ur'],
                        'tz' => $status['tz'],
                        'mytime' => $status['mytime'],

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


        $rejoin = 0;
        $userInputs = [];
        if ($tokenStatus['status']) {
            $venueSlotsCount = [];
            // $slotId = $tokenStatus['slot_id'];
            // $tokenId = $tokenStatus['tokenId'];
            $venueAddress  = $tokenStatus['venuesListArr'];
            $rejoin = $venueAddress->rejoin_venue_after;
            $venueAddressId= $venueAddress->id;
            $userInputs['inputs'] = [];
            // $userInputs['inputs']['slotId'] = $slotId ?? null;
         //   $userInputs['inputs']['tokenId'] = $tokenId ?? null;
         //   $userInputs['inputs']['booking_number'] = $tokenId ?? null; // Assuming booking_number should be tokenId
            $userInputs['inputs']['user_ip'] = $request->ip();
            $userInputs['inputs']['venue_address_id'] = $tokenStatus['venue_address_id'];

            // Exclude 'captured_user_image' from $request->except
            $userInputs['inputs'] += $request->except('captured_user_image');

            $captured_user_image = $request->input('captured_user_image');
            if (!empty($captured_user_image)) {

                $myImage = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $captured_user_image));
                // $livefaces = $this->detectLiveness($myImage);

                // if(!$livefaces['status']){
                //     return response()->json([
                //         'errors' =>  [
                //             'status' => false,
                //             'message' => 'Your token cannot be booked at this time. Please refresh this window and try again',
                //             'message_ur' => 'آپ کا ٹوکن اس وقت بک نہیں کیا جا سکتا۔ براہ کرم دوبارہ کوشش کریں۔',
                //         ]
                //     ], 422);
                // }

                $jobId = (string) Str::uuid();
                $filename = 'selfie_' . time() . '.jpg';
                $objectKey = $this->encryptFilename($filename);
                Storage::disk('s3')->put($objectKey, $myImage);

                // Upload to local directory with today's date folder
                $localDirectory = 'sessionImages/' . date('d-m-Y');
                if (!Storage::disk('public_uploads')->exists($localDirectory)) {
                    Storage::disk('public_uploads')->makeDirectory($localDirectory);
                }
                Storage::disk('public_uploads')->put($localDirectory . '/' . $objectKey, $myImage);

                $mobile = $request->input('mobile');


                try {
                    //code...
                    $uploadSuccess = Storage::disk('s3')->put($objectKey, $myImage);
                } catch (\Exception $e) {
                    // Log::error('Failed to upload file to S3.'.$e->getMessage());
                    return response()->json([
                        'errors' =>  ['message' => 'Unable to upload file ' . $objectKey . '   ' . $e->getMessage() . ' ']
                    ], 422);
                }

                //mobile

                $existingRecord = JobStatus::where(['mobile' => $mobile])
                ->whereNotIn('status',['completed','token_finish'])
                ->whereDate('created_at', now()->toDateString())->count();


                if ($existingRecord == 0) {
                    // FaceRecognitionJob::dispatch($jobId, $rejoin, $objectKey)->onQueue('face-recognition')->onConnection('database');
                    FaceRecognitionJob::dispatch($jobId, $rejoin, $objectKey,$venueAddressId , $request->input('duaType'))->onQueue('face-recognition');

                    JobStatus::create([
                        'job_id' => $jobId,
                        'status' => 'pending',
                        'user_inputs' => json_encode($userInputs),
                        'mobile' => $mobile
                    ]);

                    Log::info('Job dispatched with ID: ' . $jobId);
                    return response()->json([
                        'message' => 'Moving to Waiting Page',
                        "status" => true,
                        'redirect_url' => route('booking.waiting', [$jobId]) . '?test=' . $objectKey
                    ], 200);
                    // Proceed with further actions
                } else {
                    return response()->json([
                        'errors' =>  ['message' => 'Your are already in the Queue, please wait for sometime we are processing tokens.','message_ur' => 'آپ پہلے سے ہی قطار میں ہیں، براہ کرم کچھ دیر انتظار کریں ہم ٹوکنز پر کارروائی کر رہے ہیں۔']
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
          //   $phoneCode = session('phoneCode');
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
                return [
                    'venue_address_id' => $venuesListArr->id,
                    'status' =>  true,
                    'venuesListArr' => $venuesListArr,
                    'rejoin' => $rejoinStatus
                ];


                // $tokenIs = VenueSloting::where('venue_address_id', $venuesListArr->id)
                //     ->whereNotIn('id', Vistors::pluck('slot_id')->toArray())
                //     ->where(['type' => $request->input('duaType')])
                //     ->orderBy('id', 'ASC')
                //     ->select(['venue_address_id', 'token_id', 'id'])->first();

                // if (!empty($tokenIs)) {

                // } else {

                //     return [
                //         'status' =>  false,
                //         'message' => 'All Tokens Dua / Dum Appointments have been issued for today. Kindly try again next week. For more information, you may send us a message using "Contact Us" pop up button below.',
                //         'message_ur' => 'آج کے لیے تمام دعا/دم کے ٹوکن جاری کر دیے گئے ہیں۔ براہ مہربانی اگلے ہفتے دوبارہ کوشش کریں۔ مزید معلومات کے لیے، آپ نیچے "ہم سے رابطہ کریں" پاپ اپ بٹن کا استعمال کرتے ہوئے ہمیں ایک پیغام بھیج سکتے ہیں۔',
                //     ];
                // }
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

    public function detectLiveness($selfieImage)
    {
        // $imageData = $request->input('image');
        // $selfieImage = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $imageData));


        try {
            $rekognition = new RekognitionClient([
                'version' => 'latest',
                'region' => env('AWS_DEFAULT_REGION'),
                'credentials' => [
                    'key' => env('AWS_ACCESS_KEY_ID'),
                    'secret' => env('AWS_SECRET_ACCESS_KEY'),
                ],
            ]);

            // Detect faces in the image
            $result = $rekognition->detectFaces([
                'Image' => [
                    'Bytes' => $selfieImage,
                ],
                'Attributes' => ['ALL'],
            ]);

            // Check for liveness by analyzing the result
            if (count($result['FaceDetails']) > 0) {
                // At least one face detected
                foreach ($result['FaceDetails'] as $face) {
                    if ($face['Quality']['Sharpness'] >= 80) {
                        // Face is sharp, indicating a live person
                        return [
                            'status' => true,
                            'message' => 'Human Face'
                        ];
                        // return response()->json(['message' => 'Liveness detected.', 'status' => true], 200);
                    }
                }
            }
            return [
                'status' => false,
                'message' => 'Not a real face it look like some object or not a real face'
            ];
        } catch (\Throwable $th) {
            //throw $th;
            return [
                'status' => true,
                'message' => 'Not a real face it look like some object or not a real face'
            ];
        }
        // No live faces detected
        // return response()->json(['message' => 'Liveness not detected.', 'status' => false], 400);
    }

    protected function encryptFilename($filename)
    {
        $key = hash('sha256',uniqid(). date('Y-m-d H:i:s') . $filename . now()->toDateTimeString().uniqid());
        return  Str::uuid()->toString().$key;
    }
}
