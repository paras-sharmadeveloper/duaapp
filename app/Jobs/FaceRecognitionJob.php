<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Aws\Rekognition\RekognitionClient;
use App\Models\{Vistors,JobStatus};
class FaceRecognitionJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public $selfieImage;
    public $rejoin;
    public $jobId;
    public function __construct($jobId , $selfieImage, $rejoin)
    {
        $this->selfieImage = $selfieImage;
        $this->rejoin = $rejoin;
        $this->jobId = $jobId;
        //
    }

    /**
     * Execute the job.
     */
    public function handle()
    {
        Log::info("Job dispatched ff");
        $rejoin = $this->rejoin;
        $selfieImage = $this->selfieImage;
        $filename = 'selfie_' . time() . '.jpg';
        $objectKey = $this->encryptFilename($filename);
        $userAll = Vistors::whereDate('created_at', date('Y-m-d'))->get(['recognized_code', 'id'])->toArray();
        $userArr = [];
        $count = 0;
        Storage::disk('s3')->put($objectKey, $selfieImage);
        if (!empty($userAll) &&  $rejoin > 0) {

            try {

                $awsDefaultRegion = (env('AWS_DEFAULT_REGION')) ? env('AWS_DEFAULT_REGION') : 'us-east-1';
                $awsAccessKeyId = (env('AWS_ACCESS_KEY_ID')) ? env('AWS_ACCESS_KEY_ID') : 'AKIAWTTVS7OFB7GJU4AF';
                $awsSecretAcessKey = (env('AWS_SECRET_ACCESS_KEY')) ? env('AWS_SECRET_ACCESS_KEY') : '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';


                $rekognition = new RekognitionClient([
                    'version' => 'latest',
                    'region' => $awsDefaultRegion,
                    'credentials' => [
                        'key' => $awsAccessKeyId,
                        'secret' => $awsSecretAcessKey,
                    ],
                ]);
                $targetImages = [];
                $bucket = 'kahayfaqeer-booking-bucket';
                foreach ($userAll as $user) {
                    if (!empty($user['recognized_code'])) {
                        $targetImages[] = [
                            'S3Object' => [
                                'Bucket' => $bucket,
                                'Name' => $user['recognized_code'],
                            ],
                        ];
                    }
                }
                $response = $rekognition->compareFaces([
                    'SimilarityThreshold' => 90,
                    'SourceImage' => [
                        'S3Object' => [
                            'Bucket' => $bucket,
                            'Name' => $objectKey,
                        ],
                    ],
                    'TargetImage' => [
                        'S3Object' => [
                            'Bucket' => $bucket,
                            'Name' =>  $targetImages[0]['S3Object']['Name'],
                        ],
                    ],
                    'TargetFaces' => $targetImages,
                ]);

                $faceMatches = (!empty($response)) ? $response['FaceMatches'] : [];
                foreach ($faceMatches as $match) {
                    if ($match['Similarity'] >= 80) {
                        $userArr[] = $user['id'];
                    }
                }

                $count = (!empty($userAll)) ? count($userAll)  : 0;



                Log::info("Job dispatched");

                if (empty($userArr)) {
                    JobStatus::where(['job_id', $this->jobId])->update(['user_inputs' => json_encode(['message' => 'Congratulation You are new user', 'status' => true, 'recognized_code' => $objectKey, 'count' => $count])]);
                } else {
                    JobStatus::where(['job_id', $this->jobId])->update(['recognized_code' => $objectKey, 'message' => 'Your token cannot be booked at this time. Please try again later.', 'message_ur' => 'آپ کا ٹوکن اس وقت بک نہیں کیا جا سکتا۔ براہ کرم کچھ دیر بعد کوشش کریں', 'status' => false, 'count' => $count]);

                    return ;
                }
            } catch (\Exception $e) {

                JobStatus::where(['job_id', $this->jobId])->update(['message' =>$e->getMessage(), 'status' => false, 'count' => $count]);

                Log::info("aws" . $e->getMessage());

                // return ['message' => 'We are encounter some error at application side please report this to admin. Or try after some time.',   'status' => false , 'recognized_code' => $objectKey];
                // return ['message' => $e->getMessage(), 'status' => false];
            }
        } else {
            JobStatus::where(['job_id', $this->jobId])->update(['message' => 'Congratulation You are new user', 'status' => true, 'recognized_code' => $objectKey]);


            Storage::disk('s3')->put($objectKey, $selfieImage);
            // return ['message' => 'Congratulation You are new user', 'status' => true, 'recognized_code' => $objectKey];
        }
    }

    protected function encryptFilename($filename)
    {
        $key = hash('sha256', date('Y-m-d') . $filename . now());
        //  $hashedPassword = Hash::make($filename.now());
        return $key;
    }
}
