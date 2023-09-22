<!-- resources/views/conference/create.blade.php -->

@extends('layouts.app')

@section('content')
<div class="container">
    @if (count($errors) > 0)
  <div class="alert alert-danger alert-dismissible fade show" role="alert">
    <strong>Whoops!</strong> There were some problems with your input.<br><br>
    <ul>
       @foreach ($errors->all() as $error)
         <li>{{ $error }}</li>
       @endforeach
    </ul>
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
  </div>
@endif

@if ($message = Session::get('success'))
<div class="alert alert-success alert-dismissible fade show" role="alert">
    <i class="bi bi-check-circle me-1"></i>
    {{ $message }}
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
 
@endif
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

                        <button type="submit" class="btn btn-primary mt-3">Create Conference</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
