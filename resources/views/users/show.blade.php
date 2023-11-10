@extends('layouts.app')


@section('content')



    <div class="col-lg-12">

        <div class="card">
            <div class="card-body">
                <h5 class="card-title">Show User</h5>
                <div class="row">
                    <div class="col-xs-3 col-sm-3 col-md-3 text-center">
                        @if (!empty($user->profile_pic) && Storage::disk('s3_general')->exists('images/' . $user->profile_pic))
                            <img src="{{ env('AWS_GENERAL_PATH') . 'images/' . $user->profile_pic }}" class="imgh"
                                alt="Flag Image" style="height: 100px; width: 100px;border-radius:50%;">
                        @else
                            <img src="https://t3.ftcdn.net/jpg/03/46/83/96/360_F_346839683_6nAPzbhpSkIpb8pmAwufkC7c5eD7wYws.jpg"
                                class="imgh" alt="Default Image" style="height: 100px; width: 100px;border-radius:50%;">
                        @endif
                    </div>  
                    <div class="col-xs-9 col-sm-9 col-md-9">
                        <div class="form-group">
                            <strong>Name:</strong>
                            {{ $user->name }}
                        </div>
                        <div class="form-group mt-2">
                            <strong>Email:</strong>
                            {{ $user->email }}
                        </div>
                        <div class="form-group mt-2">
                            <strong>Roles:</strong>
                            @if (!empty($user->getRoleNames()))
                                @foreach ($user->getRoleNames() as $v)
                                    <label class="badge bg-success">{{ $v }}</label>
                                @endforeach
                            @endif
                        </div>
                        
                    </div>
                </div>
                 

            </div>
        </div>
        <div class="row">
            <div class="col-lg-12 margin-tb text-end">

                <div class="pull-right">
                    <a class="btn btn-outline-primary" href="{{ route('users.index') }}"> Back</a>
                </div>
            </div>
        </div>

    </div>
@endsection
@section('page-script')
<script>document.title = 'User Show'; </script>
@endsection