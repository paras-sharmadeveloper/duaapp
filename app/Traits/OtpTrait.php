<?php 

namespace App\Traits;
 
use Twilio\Rest\Client as TwilioClient;
use Illuminate\Support\Carbon;
use App\Mail\UserOtp;
use Illuminate\Support\Facades\Mail;
use SendGrid; 
trait OtpTrait
{
    public function VerifyOtp($otp)
    { 
 
        $storedOTP = session()->pull('otp'); 

        if ($otp == $storedOTP) {
            return ['message' => 'OTP Verfied successfully', 'status' => true];
        } else {
            return ['message' => 'OTP Failed to  verified', 'status' => false];
        }
         
    }
    

    public function SendOtp($userDetail,$isMobile=true,$isEmail = false)
    { 
      $country = $userDetail['country_code'];
      $mobile = $userDetail['mobile'];
      $email = $userDetail['email'];
      $otp = $this->generateOtp(); 
      $status = [];
      if($isEmail){
        $validatedData['subject'] = 'KahayFaqeer verification code. For your security, do not share this code.'; 
        $validatedData['otp'] = $otp; 
        // Send email
        Mail::to($email)->send(new UserOtp($validatedData));
        
        $status['email'] = true;
      }
      if($isMobile){
 
            $twilio = new TwilioClient(
              config('services.twilio.sid'),
              config('services.twilio.token')
            );
            $usePhone = "whatsapp:+".$country.$mobile;
            if (strpos($country, '+') !== false) {
              // $usePhone = $country.$mobile;
              $usePhone = 'whatsapp:'.$country.$mobile;
            }
            
              
            $message = "*$otp* is your verification code. For your security, do not share this code.";
            
            try {
              $twilio->messages->create(
                $usePhone, // User's phone number
                [
                  'from' => 'whatsapp:'.env('TWILIO_PHONE_WHATSAPP'),
                  'body' => $message
                ]
              );
              $status['mobile'] = true;
              // return ['message' => 'OTP Sent successfully', 'status' => true];
            } catch (\Exception $e) {
              //throw $th;
              return ['message' => 'Check You Mobile Number Again Or This Number Must be on WhatsApp', 'status' => false];
            }

      } 
      return ['message' => 'OTP Sent successfully', 'status' => true];
    }
    private function generateOtp(){
      $otp = rand(10000, 99999);
      $otp_expires_time = Carbon::now()->addSeconds(600);
      session()->put('otp', $otp, 'expiry_time', $otp_expires_time);
      return $otp; 
    }
    


public function SendMessage($country_code,$mobile,$message)
    { 
      $country = $country_code;
      $mobile = $mobile;
      // Send the OTP via Twilio
      $twilio = new TwilioClient(
        config('services.twilio.sid'),
        config('services.twilio.token')
      );
      $usePhone = "whatsapp:+".$country.$mobile;
      if (strpos($country, '+') !== false) {
        $usePhone = 'whatsapp:'.$country.$mobile;
      }

      try {
        $twilio->messages->create(
          $usePhone, // User's phone number
          [
            'from' => 'whatsapp:'.env('TWILIO_PHONE_WHATSAPP'),
            'body' => $message
          ]
        );
        return ['message' => 'Message Sent Successfully', 'status' => true];
      } catch (\Exception $e) {
        //throw $th;
        return ['message' => 'Check You Mobile Number Again Or This Number Must be on WhatsApp', 'status' => false];
      }
  
      
      
    }

    

}
