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

    public function show(){
        return view('workingLady.working-lady');
    }

    public function view($id){
        $data = WorkingLady::findOrFail($id);
        return view('workingLady.view',compact('data'));
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
        if ($formType == 'active') {
            $message = 'form approved';
            $workingLady->update(['is_active'  => $formType,'qr_id' => Str::uuid()]);;
        }else if($formType == 'inactive') {
            $message = 'form reject';
            $workingLady->update(['is_active'  => $formType]);;
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



    public function store(Request $request)
    {
        $request->validate([
            'firstName' => 'required|string',
            'lastName' => 'required|string',
            'designation' => 'required|string',
            'employerName' => 'required|string',
            'placeOfWork' => 'required|string',
            'employeeId' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            'passportPhoto' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            'mobile' => 'required|string|min:10|max:10',
            'email' => 'required|email',
        ]);
        $employeeIdPath = $passportPhotoPath = '';


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
        if ($request->hasFile('employeeId')) {
            $employee_ids = $request->file('employeeId');
            $employeeIdPath = time() . 'employee_ids.' . $employee_ids->getClientOriginalExtension();
            Storage::disk('s3_general')->put('employee_ids/' . $employeeIdPath, file_get_contents($employee_ids));
            $employee->employee_id_image = $employeeIdPath;

            // $image->move(public_path('/flags'), $imageName);

        }

        if ($request->hasFile('passportPhoto')) {
            $passport_photos = $request->file('passportPhoto');
            $passportPhotoPath = time() . 'passport_photos.' . $passport_photos->getClientOriginalExtension();
            Storage::disk('s3_general')->put('passport_photos/' . $passportPhotoPath, file_get_contents($passport_photos));
            $employee->passport_photo = $passportPhotoPath;
            // $image->move(public_path('/flags'), $imageName);

        }

        $employee->save();

        return redirect()->back()->with('success', 'Your Request has been submitted successfully. You will get your QR ID Once Admin approve your form.');
    }
}
