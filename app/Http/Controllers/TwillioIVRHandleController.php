<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Twilio\TwiML\VoiceResponse;
class TwillioIVRHandleController extends Controller
{
    protected $statementUrl;
    protected $cityUrl;
    protected $numbersUrl;

    public function __construct()
    {
        $this->statementUrl = 'https://phoneivr.s3.ap-southeast-1.amazonaws.com/statements/';
        $this->cityUrl = 'https://phoneivr.s3.ap-southeast-1.amazonaws.com/city/';
        $this->numbersUrl = 'https://phoneivr.s3.ap-southeast-1.amazonaws.com/numbers/';
    }

    public function handleIncomingCall()
    {
        $response = new VoiceResponse();
        // STEP 1: Welcome Message
        $response->play($this->statementUrl.'statement_welcome_message.wav');

        // Prompt user to press any key to proceed
        $gather = $response->gather([
            'numDigits' => 1,
            'action' => route('ivr.bookMeeting'),
        ]);

        $gather->say('Press any key to book a meeting.');

        return $response;
    }

    public function handleBookMeeting()
    {
        $response = new VoiceResponse();
 

        // STEP 2: Book Meeting Prompt
        $response->play($this->statementUrl.'statement_bookmeeting.wav');
        $response->play($this->numbersUrl.'number_01.wav');
        $response->play($this->statementUrl.'statement_press.wav');

        $gather = $response->gather([
            'numDigits' => 1,
            'action' => route('ivr.selectCity'),
        ]);

        // Prompt user to select a city
       
        $gather->play($this->statementUrl.'statement_press.wav');
        $gather->play($this->numbersUrl.'number_01.wav');
        $gather->play($this->cityUrl.'city_lahore.wav');

        $gather->play($this->statementUrl.'statement_press.wav');
        $gather->play($this->numbersUrl.'number_02.wav');
        $gather->play($this->cityUrl.'city_islamabad.wav');

        $gather->play($this->statementUrl.'statement_press.wav');
        $gather->play($this->numbersUrl.'number_03.wav');
        $gather->play($this->cityUrl.'city_karachi.wav');
 
        // $gather->say('Press 1 for Lahore, 2 for Islamabad, 3 for Karachi.');

        return $response;
    }

    public function handleSelectCity()
    {
        $userInput = request('Digits');
        $response = new VoiceResponse();

        // Handle user selection based on input
        switch ($userInput) {
            case '1':
                $response->play($this->cityUrl.'city_lahore.wav');
                $response->play($this->statementUrl.'statement_kay_liye.wav');
                $response->play($this->numbersUrl.'number_01.wav');
                $response->play($this->statementUrl.'statement_press.wav');
                
                // Additional logic for Lahore
                break;
            case '2':
                $response->play('http://your-audio-server/city_islamabad.wav');
                $response->play('http://your-audio-server/statement_kay_liye.wav');
                $response->play('http://your-audio-server/number_02.wav');
                $response->play('http://your-audio-server/statement_press.wav');
                // Additional logic for Islamabad
                break;
            case '3':
                $response->play('http://your-audio-server/city_karachi.wav');
                $response->play('http://your-audio-server/statement_kay_liye.wav');
                $response->play('http://your-audio-server/number_03.wav');
                $response->play('http://your-audio-server/statement_press.wav');
                // Additional logic for Karachi
                break;
            default:
                $response->say('Invalid selection. Please try again.');
                $response->redirect(route('ivr.bookMeeting'));
                break;
        }

        return $response;
    }
}
