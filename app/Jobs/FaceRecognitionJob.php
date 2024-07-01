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
use App\Models\{Vistors, JobStatus, VenueSloting};
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
    public $venueSlotCount;
    public $duaType;
    public $venueId;
    public function __construct($jobId, $rejoin, $objectKey,$venueId,$duaType)
    {
        // $this->selfieImage = $selfieImage;
        $this->rejoin = $rejoin;
        $this->jobId = $jobId;
        $this->objectKey = $objectKey;
        // $this->venueSlotCount = $venueSlotCount;
        $this->duaType = $duaType;
        $this->venueId = $venueId;

        //
    }

    /**
     * Execute the job.
     */
    public function handle()
    {
        $rejoin = $this->rejoin;
        $objectKey = $this->objectKey;
        $query = Vistors::whereDate('created_at', date('Y-m-d'));

        $userAll = $query->whereNotNull('recognized_code')->get(['recognized_code', 'id'])->toArray();

        $DuaCount = $query->where(['dua_type' =>'dua'])->count();
        $DumCount = $query->where(['dua_type' =>'dum'])->count();
        $wlDuaCount = $query->where(['dua_type' =>'working_lady_dua'])->count();
        $wlDumCount = $query->where(['dua_type' =>'working_lady_dum'])->count();


        $duaCount =  VenueSloting::where(['venue_address_id' => $this->venueId,'type' => 'dua'])->count();
        $dumCount =  VenueSloting::where(['venue_address_id' => $this->venueId,'type' => 'dum'])->count();
        $wlduaCount = VenueSloting::where(['venue_address_id' => $this->venueId,'type' => 'working_lady_dua'])->count();
        $wldumCount = VenueSloting::where(['venue_address_id' => $this->venueId,'type' => 'working_lady_dum'])->count();;


        if( $DuaCount == $duaCount && $this->duaType == 'dua'){

            JobStatus::where(['job_id' => $this->jobId])->update([
                'result' => json_encode(
                    [
                    'message' => 'Your token cannot be booked at this time. Please try again or later.',
                    'message_ur' => 'آپ کا ٹوکن اس وقت بک نہیں کیا جا سکتا۔ براہ کرم دوبارہ یا بعد میں کوشش کریں۔',
                    'status' => false,
                    'token' => 'finished'
                    ]
                ),
                'status' => 'token_finished'
            ]);
            return false;


        }
        if( $DumCount == $dumCount && $this->duaType == 'dum'){

            JobStatus::where(['job_id' => $this->jobId])->update([
                'result' => json_encode(
                    [

                    'message' => 'Your token cannot be booked at this time. Please try again or later.',
                    'message_ur' => 'آپ کا ٹوکن اس وقت بک نہیں کیا جا سکتا۔ براہ کرم دوبارہ یا بعد میں کوشش کریں۔',
                    'status' => false,
                    'token' => 'finished'
                    ]
                ),
                'status' => 'token_finished'
            ]);
            return false;
        }

        if( $wlDuaCount == $wlduaCount && $this->duaType == 'working_lady_dua'){

            JobStatus::where(['job_id' => $this->jobId])->update([
                'result' => json_encode(
                    [

                    'message' => 'Your token cannot be booked at this time. Please try again or later.',
                    'message_ur' => 'آپ کا ٹوکن اس وقت بک نہیں کیا جا سکتا۔ براہ کرم دوبارہ یا بعد میں کوشش کریں۔',
                    'status' => false,
                    'token' => 'finished'
                    ]
                ),
                'status' => 'token_finished'
            ]);
            return false;

        }

        if( $wlDumCount == $wldumCount && $this->duaType == 'working_lady_dum'){

            JobStatus::where(['job_id' => $this->jobId])->update([
                'result' => json_encode(
                    [

                    'message' => 'Your token cannot be booked at this time. Please try again or later.',
                    'message_ur' => 'آپ کا ٹوکن اس وقت بک نہیں کیا جا سکتا۔ براہ کرم دوبارہ یا بعد میں کوشش کریں۔',
                    'status' => false,
                    'token' => 'finished'
                    ]
                ),
                'status' => 'token_finished'
            ]);
            return false;


        }



        $userArr = [];
        $count = 0;
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

                $targetImages = [];



                foreach ($userAll as $key => $user) {
                    try {
                        // Log::info("Index try".$key);

                        // Log::info('Bucket: ' . $bucket);
                        // Log::info('Source Image Key: ' . $objectKey);
                        // Log::info('User Recognized Codes: ' . json_encode(array_column($userAll, 'recognized_code')));

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
                                    'Name' => $user['recognized_code']
                                ],
                            ],
                        ]);
                        // Log::info('CompareFaces response: ' . json_encode($response));
                        $faceMatches = (!empty($response)) ? $response['FaceMatches'] : [];
                        foreach ($faceMatches as $match) {
                            if ($match['Similarity'] >= 80) {
                                $userArr[] = $user['id'];
                            }
                        }
                        //code...
                    } catch (\Exception $e) {
                        // Log::info("Index ex".$key);
                        // Log::info("This failed here".$this->jobId.$e->getMessage());
                        //throw $th;
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
                            ['recognized_code' => $this->objectKey,
                            'message' => 'Your token cannot be booked at this time. Please try again or later.',
                            'message_ur' => 'آپ کا ٹوکن اس وقت بک نہیں کیا جا سکتا۔ براہ کرم دوبارہ یا بعد میں کوشش کریں۔',

                            'status' => false, 'count' => $count]
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
