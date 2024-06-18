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
use App\Models\{Vistors, JobStatus};
use Aws\Exception\AwsException;
use GuzzleHttp\Promise;

class FaceRecognitionJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public $selfieImage;
    public $rejoin;
    public $jobId;
    public $objectKey;
    public function __construct($jobId, $rejoin, $objectKey)
    {
        // $this->selfieImage = $selfieImage;
        $this->rejoin = $rejoin;
        $this->jobId = $jobId;
        $this->objectKey = $objectKey;
        //
    }

    /**
     * Execute the job.
     */
    public function handle()
    {
        Log::info("Job dispatched ff");
        $rejoin = $this->rejoin;
        $objectKey = $this->objectKey;
        $userAll = Vistors::whereDate('created_at', date('Y-m-d'))->whereNotNull('recognized_code')->get(['recognized_code', 'id'])->toArray();
        // $userAll = Vistors::whereNotNull('recognized_code')->get(['recognized_code', 'id'])->toArray();
        Log::info("UserAll7" . json_encode($userAll));
        $userArr = [];
        $count = 0;
        $ab= 1;
        $b= 1;
        if (!empty($userAll) &&  $rejoin > 0 && $ab == $b) {

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

                $targetImages = [];

                foreach ($userAll as $user) {


                    $response = $rekognition->compareFaces([
                        'SimilarityThreshold' => 90,
                        'SourceImage' => [
                            'S3Object' => [
                                'Bucket' => $bucket,
                                'Name' => $objectKey,
                            ],
                        ],
                        'TargetImage' =>[
                            'S3Object' => [
                                'Bucket' => $bucket,
                                'Name' => $user['recognized_code']
                            ],
                        ],
                    ]);
                    $faceMatches = (!empty($response)) ? $response['FaceMatches'] : [];
                    foreach ($faceMatches as $match) {
                        if ($match['Similarity'] >= 80) {
                            $userArr[] = $user['id'];
                        }
                    }

                }

                $count = (!empty($userAll)) ? count($userAll)  : 0;

                if (empty($userArr)) {
                    JobStatus::where(['job_id' => $this->jobId])->update([
                        'result' => json_encode(
                            ['message' => 'Congratulation You are new user', 'status' => true, 'recognized_code' => $this->objectKey, 'count' => $count]
                        ),
                        'status' => 'completed'
                    ]);
                } else {
                    JobStatus::where(['job_id' => $this->jobId])->update([
                        'result' => json_encode(
                            ['recognized_code' => $this->objectKey, 'message' => 'Your token cannot be booked at this time. Please try again later.', 'message_ur' => 'آپ کا ٹوکن اس وقت بک نہیں کیا جا سکتا۔ براہ کرم کچھ دیر بعد کوشش کریں', 'status' => false, 'count' => $count]
                        ),
                        'status' => 'completed'
                    ]);
                }
            } catch (AwsException  $e) {

                Log::info("Error In Aws Side" . $e->getMessage());
                JobStatus::where(['job_id' => $this->jobId])->update([
                    'result' => json_encode(['message' => $e->getMessage(), 'status' => false, 'count' => $count]),
                    'status' => 'error'
                ]);
            }
        } else {
            JobStatus::where(['job_id' => $this->jobId])->update([
                'result' => json_encode(['message' => 'Congratulation You are new user', 'status' => true, 'recognized_code' => $this->objectKey]),
                'status' => 'completed'
            ]);
        }
    }

    protected function encryptFilename($filename)
    {
        $key = hash('sha256', date('Y-m-d') . $filename . now());
        //  $hashedPassword = Hash::make($filename.now());
        return $key;
    }
}
