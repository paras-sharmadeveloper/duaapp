<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Twilio\Jwt\AccessToken;
use Twilio\Jwt\Grants\VideoGrant;
use Twilio\Rest\Client;
use App\Traits\OtpTrait;
use Illuminate\Support\Facades\Auth;
use App\Models\VideoConference;
// 
class VideoConferenceController extends Controller
{
    use OtpTrait; 
    public function createConference()
    {
         
        return view('conference.create');
    }
    
    public function createConferencePost(Request $request){

        $roomName = $request->input('roomname'); 
        $twilio = new Client(env('TWILIO_SID'), env('TWILIO_AUTH_TOKEN'));

        $room = $twilio->video->v1->rooms->create([
            'uniqueName' =>  $roomName,
            'type' => 'group',
        ]);

        VideoConference::create(['room_name' =>$roomName,'room_sid' => $roomName]);
        $message = "Hi ,\n Join Meeting here\n".route('join.conference.show',[$room->sid]); 
        $this->SendMessage('+91','8950990009',$message); 

        $userName = Auth::user()->name;   
        $roomName = $this->fetchRoomName($room->sid); 
        $accessToken = $this->generateAccessToken($roomName,$userName);
        // $room->sid
        //  $room->uniqueName $room->type 
        return redirect()->route('join.conference.show',[$room->sid])->with([
            'accessToken' => $accessToken,
            'roomName' => $roomName,
            'success' => 'You joined this Meeting',
            'enable' => false
        ]);  
    }   

    

    public function joinConference(Request $request,   $roomId)
    {
        return view('conference.join');  
    }

    public function joinConferencePost(Request $request,   $roomId)
    {
       
            $userName = $request->input('participantName');   
            $roomName = $this->fetchRoomName($roomId); 
            $accessToken = $this->generateAccessToken($roomName,$userName);
            return redirect()->back()->with([
                'accessToken' => $accessToken,
                'roomName' => $roomName,
                'success' => 'You joined this Meeting',
                'enable' => false
            ]);    
         
    }


    private function generateAccessToken($roomName,$identity)
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
        return  $videoConfernce->room_name;  
    }

    

    public function generate_token(Request $request)
    {
        $accountSid = env('TWILIO_SID');
        $apiKeySid = env('TWILIO_API_KEY_SID');
        $apiKeySecret = env('TWILIO_API_KEY_SECRET');
        $identity = uniqid();

        $roomName = "ParasTest9";

        // Create an Access Token
        $token = new AccessToken(
            $accountSid,
            $apiKeySid,
            $apiKeySecret,
            3600,
            $identity
        );

        // Grant access to Video
        $grant = new VideoGrant();
        $grant->setRoom($roomName);
        $token->addGrant($grant);
        return response()->json(['token' => $token->toJWT()]);
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
        $identity = $request->input('identity','ParasUSer1');
        $roomName = $request->input('roomName','room007');

        $accountSid = env('TWILIO_SID');
        $authToken =  env('TWILIO_AUTH_TOKEN');
        $apiKeySecret =  env('TWILIO_API_KEY_SID');

        $token = new AccessToken(
            $accountSid,
            $authToken, $apiKeySecret 
        );

        $token->setIdentity($identity);
        $videoGrant = new VideoGrant();
        $videoGrant->setRoom($roomName);
        $token->addGrant($videoGrant);
        $accessToken = $token->toJWT(); 
        return view('video-conference.video-conference', compact('accessToken','roomName'));
        return response()->json(['token' => $token->toJWT()]);
    }


     
}
