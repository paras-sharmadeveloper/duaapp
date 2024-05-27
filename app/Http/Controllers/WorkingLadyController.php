<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\WorkingLady;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\File;
use Spatie\Browsershot\Browsershot;

class WorkingLadyController extends Controller
{
    //

    public function show(Request $request){
        // if (!isMobileDevice($request)) {
        //     return abort('403');
        // }
        return view('workingLady.working-lady');
    }

    public function view($id){
        $data = WorkingLady::findOrFail($id);

        return view('workingLady.view',compact('data'));
    }

    public function deleteImages($id){
        $data = WorkingLady::findOrFail($id);

        return view('workingLady.deleteImages',compact('data'));
    }


    public function list(){
        $registration  = WorkingLady::all();
        return view('workingLady.list',compact('registration'));
    }
    public function ApproveForm(Request $request,$id)
    {
        $request->validate([
            'formType' => 'required|string'
        ]);
        $formType = $request->input('formType');
        $workingLady = WorkingLady::findOrFail($id);

        $uuid = ( $workingLady->qr_id ) ? $workingLady->qr_id : Str::uuid();
        if ($formType == 'active') {
            $message = 'form approved';
            $workingLady->update(['is_active'  => $formType,'qr_id' => $uuid,'type' =>  $request->input('type')]);
        }else if($formType == 'inactive') {
            $message = 'form reject';
            $workingLady->update(['is_active'  => $formType , 'type' =>  $request->input('type')]);;
        }
        return  redirect()->back()->with('success', $message);


    }
    public function downloadQR(Request $request,$qr_id)
    {
        // Generate the QR code
        $fileName = $request->input('filename','qrcode');
         $qrCode = QrCode::size(200)->generate($qr_id);

        // Set response headers for download
        $headers = [
            'Content-Type' => 'image/svg',
            'Content-Disposition' => 'attachment; filename="'.$fileName.'.svg"'
        ];

        // Return the response with the QR code image
        return Response::make($qrCode, 200, $headers);
    }


    public function getWorkingLadyDetails(Request $request){

        $id = $request->input('id');
        $workingLady = WorkingLady::where(['qr_id' => $id])->get()->first();
        if(!empty($workingLady) && $workingLady['is_active'] == 'active'){
            return response()->json(['message' => 'ok','status' => true, 'data' => $workingLady  ], 200);
        }else{
            return response()->json(['message' => 'This Qr code is not Approved by Admin Or not active','status' => false  ], 200);

        }

    }



    public function store(Request $request)
    {
        $request->validate([
            'firstName' => 'required|string',
            'lastName' => 'required|string',
            'designation' => 'required|string',
            'employerName' => 'required|string',
            'placeOfWork' => 'required|string',
            // 'employeeId' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            // 'passportPhoto' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            'mobile' => 'required|numeric',
            'email' => 'required|email',
        ]);
        $employeeIdPath = $passportPhotoPath = '';

        // echo "<pre>"; print_r($request->all()); die;


        // Save employee ID image
    //    $employeeIdPath = $request->file('employeeId')->store('employee_ids');

        // Save passport photo
        // $passportPhotoPath = $request->file('passportPhoto')->store('passport_photos');

        // Create new employee record
        $employee = new WorkingLady();
        $employee->first_name = $request->firstName;
        $employee->last_name = $request->lastName;
        $employee->designation = $request->designation;
        $employee->employer_name = $request->employerName;
        $employee->place_of_work = $request->placeOfWork;


        $employee->mobile = $request->mobile;
        $employee->email = $request->email;
        $employee->why_consider_you_as_working_lady = $request->why_consider_you_as_working_lady;
        // if ($request->hasFile('employeeId')) {
        //     $employee_ids = $request->file('employeeId');
        //     $employeeIdPath = time() . 'employee_ids.' . $employee_ids->getClientOriginalExtension();
        //     Storage::disk('s3_general')->put('employee_ids/' . $employeeIdPath, file_get_contents($employee_ids));
        //     $employee->employee_id_image = $employeeIdPath;

        //     // $image->move(public_path('/flags'), $imageName);

        // }

        $working_lady_session = $request->input('working_lady_session');
            if($working_lady_session){
                $captured_user_image = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $working_lady_session));
                $filename = 'session_imageWorkingLady' . time() . '.jpg';
                $objectKey = $this->encryptFilename($filename);
                Storage::disk('s3')->put($objectKey, $captured_user_image);
                $employee->session_image = $objectKey;
            }
        // session_image

        // if ($request->hasFile('passportPhoto')) {
        //     $passport_photos = $request->file('passportPhoto');
        //     $passportPhotoPath = time() . 'passport_photos.' . $passport_photos->getClientOriginalExtension();
        //     Storage::disk('s3_general')->put('passport_photos/' . $passportPhotoPath, file_get_contents($passport_photos));
        //     $employee->passport_photo = $passportPhotoPath;
        //     // $image->move(public_path('/flags'), $imageName);

        // }

        $employee->save();

        return redirect()->back()->with('success', 'Your Request has been submitted successfully. You will get your QR ID Once Admin approve your form.');
    }

    protected function encryptFilename($filename)
    {
        $key = hash('sha256', date('Y-m-d') . $filename . now());
        //  $hashedPassword = Hash::make($filename.now());
        return $key;
    }
}
