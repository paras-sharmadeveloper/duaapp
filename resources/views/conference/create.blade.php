<!-- resources/views/conference/create.blade.php -->

@extends('layouts.app')

@section('content')
    <style>
        img.card-img-top {
            height: 200px;
            width: 200px;
            margin: auto;
        }
    </style>
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
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">Start a Conference</div>

                    <div class="card-body mt-4">

                        @php $counter = 0 @endphp
                        @foreach ($venues as $venue)
                        @if ($counter % 3 == 0)
                            <div class="row">
                        @endif

                        @php
                            $venueDate = \Carbon\Carbon::parse($venue->venue_date)->format('d-M-Y');
                            $startTimeFormattedMrg = \Carbon\Carbon::parse($venue->slot_starts_at_morning)->format('h:i A');
                            $endTimeFormattedMrg = \Carbon\Carbon::parse($venue->slot_ends_at_morning)->format('h:i A');
                            $startTimeFormattedEvg = ($venue->slot_starts_at_evening) ? \Carbon\Carbon::parse($venue->slot_starts_at_evening)->format('h:i A') : '';
                            $endTimeFormattedEvg = ($venue->slot_ends_at_evening)?\Carbon\Carbon::parse($venue->slot_ends_at_evening)->format('h:i A'):'';
                        @endphp
                        <div class="col-lg-4">
                            <form method="POST" action="{{ route('join.conference.post', [$venue->room_sid]) }}">
                                @csrf 
                                <div class="card"  >
                                    <img src="{{ asset('/assets/theme/img/vedio-call.png') }}" class="card-img-top"
                                        alt="...">
                                    <div class="card-body text-center">
                                        <h5 class="card-title">Online</h5>
                                        <p class="card-text">
                                            {{ \Carbon\Carbon::parse($venue->venue_date)->format('d-M-Y') }}</p><br>
                                            <h6 class="sub-title">Morning {{ $startTimeFormattedMrg }} - {{ $endTimeFormattedMrg }}</h6>
                                            @if($startTimeFormattedEvg)
                                            <h6 class="sub-title">Evening {{ $startTimeFormattedEvg }} - {{ $endTimeFormattedEvg }}</h6>
                                            @endif
                                        <button type="submit" class="btn btn-primary mt-3">Start Conference</button>

                                    </div>
                                </div>
                                <input type="hidden" class="form-control" id="participantName" name="participantName"
                                    value="{{ $userName }}">
                                <input type="hidden" class="form-control" id="roomName" name="roomName"
                                    value="{{ $venue->room_name }}">

                            </form>
                        </div>
                        @if ($counter % 3 == 2 || $loop->last)
                    </div>
                @endif
                        @php $counter++ @endphp
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
