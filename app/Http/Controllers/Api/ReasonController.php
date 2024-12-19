<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Models\Reason;
use Illuminate\Support\Facades\Storage;

class ReasonController extends Controller
{
    public function index()
    {
        $reasons = Reason::all();
        return view('reasons.index', compact('reasons'));
    }

    public function create()
    {
        return view('reasons.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'label' => 'required',
            'reason_english' => 'required',
            'reason_urdu' => 'required',
            'reason_ivr' => 'nullable|file',
        ]);

        $reason = new Reason();
        $reason->label = $request->label;
        $reason->reason_english = $request->reason_english;
        $reason->reason_urdu = $request->reason_urdu;

        $reason->type = $request->from;

        if ($request->hasFile('reason_ivr')) {
            $path = $request->file('reason_ivr')->store('reason_ivr', 's3_general');
            $reason->reason_ivr_path = env('AWS_GENERAL_PATH'). $path;
        }

        $reason->save();

        return redirect()->route('reasons.index')->with('success', 'Reason added successfully');
    }


    public function edit($id)
    {
        $reason = Reason::findOrFail($id);
        return view('reasons.create', compact('reason'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'label' => 'required',
            'reason_english' => 'required',
            'reason_urdu' => 'required',
            'reason_ivr' => 'nullable|file',
        ]);

        $reason = Reason::findOrFail($id);
        $reason->label = $request->label;
        $reason->reason_english = $request->reason_english;
        $reason->reason_urdu = $request->reason_urdu;
        $reason->type = $request->from;

        if ($request->hasFile('reason_ivr')) {
            // Delete old IVR file
            if ($reason->reason_ivr_path) {
                Storage::disk('s3')->delete($reason->reason_ivr_path);
            }
            $path = $request->file('reason_ivr')->store('reason_ivr', 's3');
            $reason->reason_ivr_path = env('AWS_GENERAL_PATH'). $path;
        }

        $reason->save();

        return redirect()->route('reasons.index')->with('success', 'Reason updated successfully');
    }

    public function destroy($id)
    {
        $reason = Reason::findOrFail($id);

        // Delete IVR file from storage if exists
        if ($reason->reason_ivr_path) {
            Storage::disk('s3')->delete($reason->reason_ivr_path);
        }

        $reason->delete();

        return redirect()->route('reasons.index')->with('success', 'Reason deleted successfully');
    }

}
