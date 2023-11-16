@extends('layouts.app')
@section('content')
    <div class="row">
        <div class="col-lg-12 margin-tb">

            <div class="action-top float-end mb-3">
                <a class="btn btn-outline-primary" href="{{ route('roles.index') }}"> <i
                        class="bi bi-skip-backward-circle me-1"></i> Back</a>
            </div>

        </div>
    </div>

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
    <div class="card">
        <div class="card-body">
            <h5 class="card-title">Manage Queue</h5>
            @if (request()->route()->getName() == 'siteadmin.queue.show')
                <section class="services section-bg" id="services">
                    <div class="container">

                        @php $counter = 0 @endphp
                        <div class="row">
                            @foreach ($venueAddress as $venueAdd)
                                @if ($counter % 3 == 0)
                                    <div class="row">
                                @endif
                                @php
                                    $venueDate = \Carbon\Carbon::parse($venueAdd->venue_date)->format('d-M-y');
                                    $startTimeFormattedMrg = \Carbon\Carbon::parse($venueAdd->slot_starts_at_morning)->format('h:i A');
                                    $endTimeFormattedMrg = \Carbon\Carbon::parse($venueAdd->slot_ends_at_morning)->format('h:i A');
                                    $startTimeFormattedEvg = \Carbon\Carbon::parse($venueAdd->slot_starts_at_evening)->format('h:i A');
                                    $endTimeFormattedEvg = \Carbon\Carbon::parse($venueAdd->slot_ends_at_evening)->format('h:i A');
                                @endphp
                                <div class="col-lg-4">


                                    <div class="box">
                                        <div class="icon">
                                            @if (
                                                !empty($venueAdd->user->profile_pic) &&
                                                    Storage::disk('s3_general')->exists('images/' . $venueAdd->user->profile_pic))
                                                <img src="{{ env('AWS_GENERAL_PATH') . 'images/' . $venueAdd->user->profile_pic }}"
                                                    class="imgh" alt="Flag Image">
                                            @else
                                                <img src="https://t3.ftcdn.net/jpg/03/46/83/96/360_F_346839683_6nAPzbhpSkIpb8pmAwufkC7c5eD7wYws.jpg"
                                                    class="imgh" alt="Default Image">
                                            @endif
                                            {{-- <i class="fa fa-briefcase service-icon" style="color: #c59c35;"></i> --}}
                                        </div>
                                        <h4 class="title">{{ $venueAdd->user->name }}</h4>
                                        <h2 class="sub-title">{{ $venueDate }}</h2>
                                        <h4 class="sub-title">Morning {{ $startTimeFormattedMrg }} - {{ $endTimeFormattedMrg }}</h4>
                                        <h4 class="sub-title">Evening {{ $startTimeFormattedEvg }} - {{ $endTimeFormattedEvg }}</h4>
                                        <span class="sr"><strong>{{ $venueAdd->venue->country_name }}
                                                ({{ $venueAdd->state }})
                                            </strong></span>
                                        <p class="description text-center">{{ $venueAdd->city }}</p>
                                        <p class="description text-center">{{ $venueAdd->address }}</p>
                                        <a href="{{ route('siteadmin.queue.list', [$venueAdd->id]) }}"
                                            class="btn btn-outline-info text-center">Start</a>
                                    </div>


                                </div>
                                @if ($counter % 3 == 2 || $loop->last)
                        </div>
            @endif
            @php $counter++ @endphp
            @endforeach

        </div>
        </section>
        @endif


    </div>
    </div>
    </div>
    <style>
        .box img { max-height: 120px;}
        #services .box,
        .section-header h3 {
            position: relative;
            text-align: center
        }

        #services .box,
        section {
            overflow: hidden
        }

        a {
            color: #444
        }

        .container {
            max-width: 1320px
        }

        .section-header h3 {
            font-size: 36px;
            color: #413e66;
            font-weight: 700;
            font-family: Montserrat, sans-serif
        }

        .section-header p {
            text-align: center;
            margin: auto;
            font-size: 15px;
            padding-bottom: 60px;
            color: #535074;
            width: 50%
        }

        @media (max-width:767px) {
            .section-header p {
                width: 100%
            }
        }

        #services {
            padding: 60px 0 40px
        }

        #services .box {
            padding: 30px;
            border-radius: 10px;
            margin: 0 10px 40px;
            background: #fff;
            box-shadow: 0 10px 29px 0 rgba(68, 88, 144, .1);
            transition: .3s ease-in-out
        }

        #services .box:hover {
            transform: scale(1.1)
        }

        #services .icon {
            margin: 0 auto 15px;
            padding-top: 12px;
            display: inline-block;
            text-align: center;
            border-radius: 50%;
        }

        #services .icon .service-icon {
            font-size: 36px;
            line-height: 1
        }

        #services .title {
            font-weight: 700;
            margin-bottom: 15px;
            font-size: 18px
        }

        #services .title a {
            color: #111
        }

        #services .box:hover .title a {
            color: #c59c35
        }

        #services .box:hover .title a:hover {
            text-decoration: none
        }

        #services .description {
            font-size: 14px;
            line-height: 28px;
            margin-bottom: 0;
            text-align: left
        }
    </style>

@endsection
