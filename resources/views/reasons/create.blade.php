<!-- create.blade.php -->

@extends('layouts.app')

@section('content')

<div class="card">
    <div class="card-body">
        <h5 class="card-title">{{ isset($reason) ? 'Edit Reason' : 'Add Reason' }}</h5>

        <div class="row">
            <div class="col-md-8">

                <form action="{{ isset($reason) ? route('reasons.update', $reason->id) : route('reasons.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @if(isset($reason))
                        @method('PUT')
                    @endif
                    <div class="mb-3">
                        <label for="label" class="form-label">Label</label>
                        <input type="text" class="form-control" id="label" name="label" value="{{ isset($reason) ? $reason->label : '' }}" required>
                    </div>
                    <div class="mb-3">
                        <label for="reason_english" class="form-label">Reason English</label>
                        <input type="text" class="form-control" id="reason_english" name="reason_english" value="{{ isset($reason) ? $reason->reason_english : '' }}" required>
                    </div>
                    <div class="mb-3">
                        <label for="reason_urdu" class="form-label">Reason Urdu</label>
                        <input type="text" class="form-control" id="reason_urdu" name="reason_urdu" value="{{ isset($reason) ? $reason->reason_urdu : '' }}" required>
                    </div>
                    <div class="mb-3">
                        <label for="reason_ivr" class="form-label">Reason IVR</label>
                        @if(isset($reason) && $reason->reason_ivr_path)
                            <p>Current IVR: <a href="{{ $reason->reason_ivr_path }}">Download</a></p>
                        @endif
                        <input type="file" class="form-control" id="reason_ivr" name="reason_ivr">
                    </div>
                    <button type="submit" class="btn btn-primary">Submit</button>
                </form>
            </div>
        </div>
    </div>
</div>
<div class="container">

</div>
@endsection
