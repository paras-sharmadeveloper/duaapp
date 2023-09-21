@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Join a Conference</div>

                <div class="card-body">
                    <form method="POST" action="{{ route('join-conference', $roomId) }}">
                        @csrf

                        <div class="form-group">
                            <label for="participantName">Your Name</label>
                            <input type="text" class="form-control" id="participantName" name="participantName" required>
                        </div>

                        <button type="submit" class="btn btn-primary">Join Conference</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
