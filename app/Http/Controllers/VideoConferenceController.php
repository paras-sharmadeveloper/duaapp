<?php

namespace App\Http\Controllers;

use App\Models\VenueAddress;
use Illuminate\Http\Request;
use Twilio\Jwt\AccessToken;
use Twilio\Jwt\Grants\VideoGrant;
use Twilio\Rest\Client;
use App\Traits\OtpTrait;
use Illuminate\Support\Facades\Auth;
use App\Models\{VideoConference, Vistors, Timezone, Ipinformation};
use Carbon\Carbon;


// 
class VideoConferenceController extends Controller
{
    use OtpTrait;


    public function design()
    {

        return view('conference.new');
    }
    public function createConference()
    {

        return view('conference.create');
    }

    public function createConferencePost(Request $request)
    {

        $roomName = $request->input('roomName');
        $twilio = new Client(env('TWILIO_SID'), env('TWILIO_AUTH_TOKEN'));

        $room = $twilio->video->v1->rooms->create([
            'uniqueName' =>  $roomName,
            'type' => 'peer-to-peer',
        ]);

        VideoConference::create(['room_name' => $roomName, 'room_sid' => $room->sid]);
        $message = "Hi ,\n Join Meeting here\n" . route('join.conference.show', [$room->sid]);
        $this->SendMessage('+91', '8950990009', $message);

        $userName = Auth::user()->name;


        $roomName = $this->fetchRoomName($room->sid);
        $accessToken = $this->generateAccessToken($roomName, $userName);
        // $room->sid
        //  $room->uniqueName $room->type

        return redirect()->route('join.conference.show', [
            'roomSid' => $room->sid,
            'accessToken' => $accessToken,
            'roomName' => $roomName,
            'success' => 'You joined this Meeting',
            'enable' => false
        ]);
    }
    public function joinConferenceFrontend(Request $request, $bookingId = '')
    {
        // echo $bookingId; die; 

        $vistor = Vistors::where(['booking_uniqueid' => $bookingId, 'meeting_start_at' => null])->get()->first();
        $venueAddress = [];
        $estimatedWaitTime = $aheadCount = $servedCount = $timePerSlot = 0;
        if (empty($vistor)) {
            abort(404);
        }




        if (!empty($vistor)) {
            $venueAddress =  VenueAddress::find($vistor->slot->venue_address_id);
            $userTimeZone = $vistor->user_timezone;
            $mytime = Carbon::now()->tz('America/New_York');
            if (!empty($userTimeZone)) {
                $mytime = Carbon::now()->tz($userTimeZone);
            } 
            $venueDateTme = $venueAddress->venue_date . ' ' . $vistor->slot->slot_time;
            $eventDate = '2023-11-03 21:38:00';
            // $meetingStartTime =  Carbon::parse($venueDateTme, $userTimeZone);
            $meetingStartTime =  Carbon::parse($eventDate, $userTimeZone);
            $timeRemaining = $meetingStartTime->diffInHours($mytime);
            // $timeRemaining = $mytime->diffInHours($meetingStartTime); 

            $isMeetingInProgress = $mytime->gte($meetingStartTime);
            $vistorName = $vistor->fname . ' ' . $vistor->lname;
            $aheadCount = Vistors::where(['meeting_type' => 'virtual'])->aheadOfVisitor();
            $servedCount = Vistors::where(['meeting_type' => 'virtual'])->alreadyServed();
            $timePerSlot =  $venueAddress->slot_duration; // Time duration for each slot (in minutes)
            $estimatedWaitTime = $aheadCount * $timePerSlot;
        }

        //  echo  $meetingStartTime ; die;  
        $roomName =  '';
        $accessToken = '';
        // $roomName =   $venueAddress->room_name;
        // $accessToken = $this->generateAccessToken($venueAddress->room_name, $vistorName);



        return view('frontend.onlinemeeting', compact(
            'venueAddress',
            'roomName',
            'accessToken',
            'aheadCount',
            'servedCount',
            'estimatedWaitTime',
            'timeRemaining',
            'vistor',
            'timePerSlot',
            'userTimeZone',
            'venueDateTme','mytime'
        ));
    }

    public function fieldAdminRequest()
    {
        $id = Auth::user()->id;
        $role = Auth::user()->roles->pluck('name')->first();
        if ($role == 'admin') {
            $venueAddress = VenueAddress::with('thripist')->get()->first();
        } else {
            $venueAddress = VenueAddress::where(['siteadmin_id' => $id])->with('thripist')->get()->first();
        }

        return view('site-admin.particpent', compact('id', 'venueAddress'));
    }


    public function StartConferenceShow(Request $request)
    {
        $userId = Auth::user()->id;
        $userName = Auth::user()->name;
        $venues = [];
        $role = Auth::user()->roles->pluck('name')->first();
        if ($role == 'admin') {
            $venues =  VenueAddress::where(['type' => 'virtual'])->get();
        } else {
            $venues =  VenueAddress::where(['therapist_id' => $userId, 'type' => 'virtual'])->get();
        }

        // echo "<pre>"; print_r($venues); die; 


        return view('conference.create', compact('venues', 'userId', 'userName'));
    }

    public function joinConference(Request $request)
    {
        $participants = Vistors::where(['meeting_type' => 'virtual', 'user_status' => 'in-queue'])->get();

        return view('conference.join', compact('participants'));
    }

    public function joinConferencePost(Request $request, $roomId)
    {
        $waitingQueue = Vistors::where(['meeting_type' => 'virtual', 'user_status' => 'in-queue'])->get();
        $userName = $request->input('participantName');
        $roomName = $request->input('roomName');
        $role = Auth::user()->roles->pluck('name')->first();
        if ($role == 'admin') {
            $venues =  VenueAddress::where(['room_sid' => $roomId])->get()->first();
        } else {
            $venues =  VenueAddress::where(['room_sid' => $roomId, 'therapist_id' => Auth::user()->id])->get()->first();
        }

        $accessToken = $this->generateAccessToken($roomName, $userName);

        return redirect()->route(
            'join.conference',
            [
                'accessToken' => $accessToken,
                'roomName' => $roomName,
                'roomId' => $roomId,
                'side_admin' => $venues->siteadmin_id,
                'waitingQueue' => $waitingQueue
            ]
        );

        // return redirect()->route('join.conference')->with([
        //     'accessToken' => $accessToken,
        //     'roomName' => $roomName,
        //     'success' => 'You joined this Meeting',
        //     'enable' => false,
        //     'roomId' => $roomId
        // ]);
    }


    public function AskToJoin(Request $request)
    {

        $id = $request->input('id');
        $type =  $request->input('action', 'in-queue');
        $visor = Vistors::find($id)->update(['user_status' =>  $type]);
        return response()->json(['message' => 'You request submitted successfully. Lets Wait for Host to approve your request', "status" => true], 200);
    }

    public function VisitorRequests(Request $request)
    {

        $id = $request->input('id');
        $vistors = Vistors::with('slot')->where(['meeting_type' => 'virtual', 'user_status' => 'in-queue'])->get();

        $dataArr = [];
        foreach ($vistors as $visitor) {
            $venueAdresId = $visitor->slot->venue_address_id;
            $venUAdress = VenueAddress::with('thripist')->where(['id' => $venueAdresId])->get()->first();

            $role = Auth::user()->roles->pluck('name')->first();
            if ($role == 'admin') {
                $dataArr[] = $visitor;
                $dataArr['user_info'] =  ($venUAdress) ? $venUAdress->thripist : "";
            } elseif ($role == 'therapist' && $venUAdress->therapist_id == Auth::user()->id) {
                $dataArr[] = $visitor;
                $dataArr['user_info'] =  ($venUAdress) ? $venUAdress->thripist : "";
            } else {
                if (!empty($venUAdress) && $venUAdress->siteadmin_id == Auth::user()->id) {
                    $dataArr[] = $visitor;
                    $dataArr['user_info'] =  ($venUAdress) ? $venUAdress->thripist : "";
                }
            }
        }
        return response()->json([
            'participants' => $dataArr,
            // "venUAdress" => $venUAdress , 
            //"authId" =>  Auth::user()->id,
            // 'siteadmin' => $venUAdress->siteadmin_id,
            "status" => (!empty($dataArr)) ? true : false
        ], 200);
    }


    public function CheckParticpentStatus(Request $request)
    {

        $id = $request->input('id');
        $vistor = Vistors::find($id);
        $roomDetails = [];
        $admitted = false;
        if (!empty($vistor) && $vistor->user_status == 'admitted') {
            $venueAddress =  VenueAddress::find($vistor->slot->venue_address_id);
            $roomDetails['room_name'] = $venueAddress->room_name;
            $vistorName = $vistor->fname . ' ' . $vistor->lname;
            $roomDetails['accessToken'] = $this->generateAccessToken($venueAddress->room_name, $vistorName);
            $admitted = true;
            return response()->json([
                'message' => 'You request submitted successfully. Please Wait While Host Approve Your Request',
                "status" => true,
                'visitor' => $vistor,
                'is_admit' => $admitted,
                'roomDetails' => $roomDetails
            ], 200);
        } else {
            return response()->json([
                'message' => 'You request submitted successfully. Please Wait While Host Approve Your Request',
                "status" => false,
                "is_admit" => $admitted,
                "user_status" => ($vistor) ?  $vistor->user_status : ''
            ], 200);
        }

        // $roomName =   ;
        // $accessToken = $this->generateAccessToken($venueAddress->room_name, $vistorName);

    }



    private function generateAccessToken($roomName, $identity)
    {
        // Twilio Account SID and Auth Token from your Twilio account 


        $token = new AccessToken(
            env('TWILIO_ACCOUNT_SID'),
            env('TWILIO_API_KEY_SID'),
            env('TWILIO_API_KEY_SECRET'),
            3600,
            $identity
        );

        $videoGrant = new VideoGrant();
        $videoGrant->setRoom($roomName);
        $token->addGrant($videoGrant);

        return $token->toJWT();
    }
    private function fetchRoomName($roomId)
    {
        $videoConfernce = VideoConference::where(['room_sid' => $roomId])->get()->first();
        // echo "<pre>"; print_r( $videoConfernce); die; 
        return  $videoConfernce->room_name;
    }


    public function index(Request $request)
    {
        $participantIdentity = $request->input('participant_identity');
        $accountSid = env('TWILIO_SID');
        $authToken =  env('TWILIO_AUTH_TOKEN');
        $apiKeySecret =  env('TWILIO_API_KEY_SID');
        // $rest = $this->getKey(); 

        $accessToken = new AccessToken($accountSid, $authToken, $apiKeySecret);

        // Create a Video Grant and add it to the token
        $videoGrant = new VideoGrant();
        $accessToken->addGrant($videoGrant);

        // Set the identity of the user (can be a unique identifier for each user)
        $identity = 'user123';
        $accessToken->setIdentity($identity);
        // Generate the token as a JWT (JSON Web Token)
        $token = $accessToken->toJWT();
        // echo  $token; die;  


        return view('video-conference.video-conference', ['token' => $token]);

        return view('video-conference.video-conference', compact('accessToken'));
    }

    public function startConference(Request $request)
    {
        $identity = $request->input('identity', 'ParasUSer1');
        $roomName = $request->input('roomName', 'room007');

        $accountSid = env('TWILIO_SID');
        $authToken =  env('TWILIO_AUTH_TOKEN');
        $apiKeySecret =  env('TWILIO_API_KEY_SID');

        $token = new AccessToken(
            $accountSid,
            $authToken,
            $apiKeySecret
        );

        $token->setIdentity($identity);
        $videoGrant = new VideoGrant();
        $videoGrant->setRoom($roomName);
        $token->addGrant($videoGrant);
        $accessToken = $token->toJWT();
        return view('video-conference.video-conference', compact('accessToken', 'roomName'));
        return response()->json(['token' => $token->toJWT()]);
    }
    public function getIpDetails($userIp)
    {
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://apiip.net/api/check?ip=' . $userIp . '&accessKey=' . env('IP_API_KEY'),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
        ));

        $response = curl_exec($curl);
        $result = json_decode($response, true);

        curl_close($curl);

        $data = [
            'user_ip' => $userIp,
            'countryName' => $result['countryName'],
            'regionName' => $result['regionName'],
            'city' => $result['city'],
            'postalCode' => $result['postalCode'],
            'complete_data' => $response
        ];

        Ipinformation::create($data);
        return $result;
    }
}
