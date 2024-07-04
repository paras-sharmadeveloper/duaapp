<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class QrCodeDoorUnlockApiController extends Controller
{
    //
    public function OpenDoor(Request $request)
    {
        // Validate incoming request data
        $validatedData = $request->validate([
            'Type' => 'required|integer', // Add more validation rules as needed
            'SCode' => 'required|string',
            'DeviceID' => 'required|string|size:12',
            'ReaderNo' => 'required|integer',
            'ActIndex' => 'required|integer',
            'SN' => 'required|string',
        ]);

        // Process the validated data (example: log it)
        Log::info('Received API request', $validatedData);

        // Perform any business logic here
        // you can respond  {"ResultCode":"1",”ActIndex”:”1”} to open
        // if not open  respond  {"ResultCode":"0",”ActIndex”:”1”}




        $response = [
            'ResultCode' => '1',  // 1 means success; 0 is failure
            'ActIndex' => '1',    // 1 allow to open relay 1
            'Time1' => '000A',    // set the relay1 delaytime (example value)
            'Time2' => '0005',    // set the relay2 delaytime (example value)
            'Audio' => '04',      // value 04 (enter); if not used, omit this field
            'Msg' => 'Success Paras'    // success or failure validation hint
        ];
        echo json_encode(['paras' =>1]); die;
        // Return the response as JSON
        return response()->json($response);

        // Return a response (optional)
        // return response()->json(['message' => 'Data Recived'], 200);
    }


    public function HeartBeat(Request $request)
    {
        // Validate incoming request data
        // $validatedData = $request->validate([
        //     'Type' => 'required|integer', // Add more validation rules as needed
        //     'SCode' => 'required|string',
        //     'DeviceID' => 'required|string|size:12',
        //     'ReaderNo' => 'required|integer',
        //     'ActIndex' => 'required|integer',
        //     'SN' => 'required|string',
        // ]);

        // Process the validated data (example: log it)
        Log::info('Received API request', $request->all());

        $response = [
            'ResultCode' => '1',               // 1 means success; 0 is failure
            'ActIndex' => '1',                 // If ActIndex field is present, it indicates relay to open
            'CorrectTime' => date('YmdHis'),   // Example of current datetime in correct format
            'Msg' => 'Success'                 // Success message, can be displayed on TFT screen
        ];

        // Return the response as JSON
        return response()->json($response);

    }


}


