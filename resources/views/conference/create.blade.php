<!-- resources/views/conference/create.blade.php -->

@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Create a Conference</div>

                <div class="card-body">
                    <form method="POST" action="{{ route('create-conference') }}">
                        @csrf

                        <div class="form-group">
                            <label for="roomName">Room Name</label>
                            <input type="text" class="form-control" id="roomName" name="roomName" required>
                        </div>

                        <button type="submit" class="btn btn-primary">Create Conference</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
