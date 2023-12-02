<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\{VisitorBooking};

class TicketWebhook extends Controller
{
    public function FetchData(Request $request, $listId)
    { 

         $post = $request->all();
         if(empty($post)){
            return response()->json(['message' => 'Your Request must have some data'],406); 
         }
       // $post = $request->json()->all();
      
       // $email =  $post['email']; 
      //  $fname =  $post['first_name'];

        VisitorBooking::create([
            'name' => "webhookTest",
            'email' =>"email@gmail.com",
            'phone' => "7897897".$listId,
            'purpose' =>  json_encode($post),
            'visit_datetime' => date('y-m-d H:i:s'),
        ]);
        $accessToken = 'eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJhdWQiOiIxIiwianRpIjoiZTExZmE1NDBiYjg0YTNiNjNhZGRhODg4ZmNlOWQ5ZTI3Zjg1NzRhZWExODdiNmUzODk1NGViODU3ZThmYmU5MDA2OTAwNjBhYjQ0YTI4YmEiLCJpYXQiOjE2OTkzNTgyMDMuNzkyNjczLCJuYmYiOjE2OTkzNTgyMDMuNzkyNjc1LCJleHAiOjE3MzA5ODA2MDMuNzg5NTYsInN1YiI6Ijk5MzM1Iiwic2NvcGVzIjpbXX0.etAcxchp17C3RccNXFxOvzn-h0wD4hFGzUg6gh2B73opLYly4TBZAEvoegpEBAZ5fEATmDlCdHkgHBS1YW1C--HTiDjSIkuDAfasDKWb4Dm-q4cAgSKDKwm0loHWP1v2Rih2WsctTZsKA4WFyA_DlGvCdhippRZJMpyUjfrP3Ik0qiN3WqzuYna-nhwGEgEAW5q75fxHQi97vjjYQjojLpzjsjvCaWfQGxmusRA1Y8PqLLal5U0pFd2gp2FhQ8pn_Jybezfs_BRa7zqnvL4ZEFNqyWcW5D-7Tg38SwjNBtg_2rHYZA1zsyBNPWE9oryJvynd0zt5bhexS8EeIOne4yEkxaDbCWPF8BkjLKiyGXEeIaapxr7bZ3-c31ksm5m2f1QKgaCZQsUT_MaH0uRVJin28oomAEsgL9BZjX8Mqyh_-v5tvABEbLonOpRMV_-UC324R8prsCICchOeHNnJYOv5vAgiu4ku1Q_PgLSSCGKI0k0F7z-P-UhlIrQyy2vN7WViLnVwAoaVPRHusqEpPMHStc7KLh_FwC0Twnsu6u5wsoZBwgWjZVH4UYmC6bpfCh8gQrjypbrvfqGmRC-Rlrz5YBzeRz7KirFoBAI7cRaqBeh4Mn7dJ-nwdn6GzDOtDIPWLafpypfmi59LG3xHUGvDFV2Uu0Y437ZCMRJ-zPk';
        $curl = curl_init();

        curl_setopt_array($curl, [
            CURLOPT_URL => "https://api.sendlane.com/v2/lists/" . $listId . "/contacts",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => json_encode([
                'contacts' => [
                    [
                        'email' => $email,
                         'first_name' => $fname,
                        // 'last_name' => 'Doe',
                        // 'phone' => '+15555555555',
                        // 'tag_ids' => [ 1 ],
                        // 'custom_fields' => [
                        //     ['id' => 1,
                        //     'value' => '2023-02-02'
                        //     ]
                        // ],
                        'email_consent' => true,
                        // 'sms_consent' => [

                        // ]
                    ]
                ]
            ]),
            CURLOPT_HTTPHEADER => [
                "Authorization: Bearer " . $accessToken,
                "Content-Type: application/json"
            ],
        ]);

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        if ($err) {
            $data = ['error' =>  $err, 'message' => 'not send', 'status' => false];
            echo "cURL Error #:" . $err;
        } else {
            $data = ['error' =>  [], 'message' => $response, 'status' => true];
        }
        return response()->json($data); 
        
    }
}
