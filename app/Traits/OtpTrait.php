<?php 

namespace App\Traits;
 
use Twilio\Rest\Client as TwilioClient;
use Illuminate\Support\Carbon;
trait OtpTrait
{
    public function VerifyOtp($otp)
    { 
 
        $storedOTP = session()->pull('otp'); 

        if ($otp == $storedOTP) {
            return ['message' => 'OTP Verfied successfully', 'status' => true];
        // OTP is valid, you can proceed with form submission or other actions
         
        } else {
            return ['message' => 'OTP Failed to  verified', 'status' => false];
        
        }
         
    }

    public function SendOtp($mobile,$country_code)
    { 
      $country = $country_code;
      $mobile = $mobile;
  
      $otp = rand(10000, 99999);
      $otp_expires_time = Carbon::now()->addSeconds(600);
  
      session()->put('otp', $otp, 'expiry_time', $otp_expires_time);
  
      // Store the OTP in the session for verification
  
      // Send the OTP via Twilio
      $twilio = new TwilioClient(
        config('services.twilio.sid'),
        config('services.twilio.token')
      );
      $usePhone = "+".$country.$mobile;
      if (strpos($country, '+') !== false) {
        $usePhone = $country.$mobile;
      }
  
      $twilio->messages->create(
        $usePhone, // User's phone number
        [
          'from' => config('services.twilio.phone'),
          'body' => "Your OTP is: $otp .\nOtp will be Expire in 10 minutes\Thanks,\Team Kahay Faqeer."
        ]
      );
      return ['message' => 'OTP Sent successfully', 'status' => true];
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
      $usePhone = "+".$country.$mobile;
      if (strpos($country, '+') !== false) {
        $usePhone = $country.$mobile;
      }
  
      $twilio->messages->create(
        $usePhone, // User's phone number
        [
          'from' => config('services.twilio.phone'),
          'body' => $message
        ]
      );
      return ['message' => 'Message Sent Successfully', 'status' => true];
    }

}

?>