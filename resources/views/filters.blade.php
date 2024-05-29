@extends('layouts.app')
@section('content')
    <div class="row">
        <div class="col-lg-12 margin-tb">

            <div class="action-top float-end mb-3">
                <!-- <button type="button" class="btn btn-primary"><i class="bi bi-star me-1"></i> With Text</button> -->
                <a class="btn btn-outline-primary" href="{{ route('venues.create') }}"> <i class="bi bi-plus me-1"></i> New
                    Venue</a>
            </div>
        </div>
    </div>
    @php
        $inactive = request()->get('inactive');
    @endphp

 @include('alerts')
        <div class="card">
            <div class="card-body">
                <form action="{{ route('admin.filter')}}" method="get">
                    <div class="row">
                        <div class="col-md-4">
                            <label> Filter Date </label>
                            <input class="form-control" type="date" name="date">
                        </div>
                        <div class="col-md-4">

                        </div>
                        <div class="col-md-4">

                        </div>
                    </div>
                    <button class="btn btn-secondary mt-4" type="submit">Filter </button>
                </form>
            </div>
        </div>

    <div class="card">
        <div class="card-body">
            <h5 class="card-title">Search Entries</h5>
            <table class="table-with-buttons table table-responsive cell-border">
                <thead>
                    <tr>
                        <th>Token Session Image </th>
                        <th>Working Lady Session Image </th>
                        <th>Checkin Time Stamp (PK Time Zone)</th>
                        <th>Token Print Count</th>
                        <th>1st Msg Sent Status</th>
                        <th>1st Msg Sent Date</th>
                        <th>1st Msg Sid </th>
                        <th>Status</th>

                        <th>Phone</th>
                        <th>Source</th>
                        <th>Token Url</th>
                        <th>Token</th>







                        <th style="width: 300px">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($visitors as  $visitor)

                    @php
                          $image = (!empty($visitor->recognized_code))  ? getImagefromS3($visitor->recognized_code) : '';
                          $workingLady = (!empty($visitor->working_lady_id)) ?  getWorkingLady($visitor->working_lady_id) : [];
                          $workingLadySession = (!empty($workingLady)) ? getImagefromS3($workingLady->session_image) : '';

                    @endphp
                    <tr>
                        <td>
                            @if($image)
                                <img src="data:image/jpeg;base64,{{ base64_encode($image) }}" alt="Preview Image"
                                    style="height: 150px; width:150px;border-radius:20%">
                                @else
                                <img src="https://kahayfaqeer-general-bucket.s3.amazonaws.com/na+(1).png" alt="Preview Image"
                                style="height: 150px; width:150px;border-radius:20%">
                            @endif
                        </td>
                        <td>
                            @if($workingLadySession)
                                <img src="data:image/jpeg;base64,{{ base64_encode($workingLadySession) }}" alt="Preview Image"
                                    style="height: 150px; width:150px;border-radius:20%">
                                @else
                                <img src="https://kahayfaqeer-general-bucket.s3.amazonaws.com/na+(1).png" alt="Preview Image"
                                style="height: 150px; width:150px;border-radius:20%">
                            @endif
                        </td>
                        <td>
                            {{ $visitor->confirmed_at }}
                        </td>
                        <td></td>
                        <td>{{ $visitor->msg_sent_status }}</td>
                        <td>{{ $visitor->msg_date }}</td>
                        <td>{{ $visitor->msg_sid }}</td>
                        <td>{{ $visitor->token_status }}</td>

                        <td>{{ $visitor->phone }}</td>
                        <td>{{ $visitor->source }}</td>
                        <td>{{ $visitor->booking_uniqueid  }}</td>
                        <td><a href="{{ route('booking.status' , [$visitor->booking_number])}}" > Token Status </a> </td>

                        <td>
                            @if($visitor->token_status  == 'invaild')
                                <form method="post" action="{{ route('admin.filter.status',[$visitor->id]) }}">
                                    @csrf
                                    <input type="hidden" name="status" value="vaild">
                                    <button type="submit" class="btn btn-success">Vaild</button>
                                </form>
                            @endif

                            @if($visitor->token_status  == 'vaild')
                                <form method="post" action="{{ route('admin.filter.status',[$visitor->id]) }}">
                                    @csrf
                                    <input type="hidden" name="status" value="invaild">
                                    <button type="submit" class="btn btn-danger">Invaild</button>
                                </form>
                            @endif

                        </td>
                    </tr>
                    @endforeach

                </tbody>

            </table>
        </div>
    </div>

@endsection
@section('page-script')
<script>


</script>
@endsection
