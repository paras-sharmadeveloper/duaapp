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

    @if ($err = Session::get('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="bi bi-check-circle me-1"></i>
        {{ $err }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif
    <div class="card">
        <div class="card-body">
            <h5 class="card-title">Manage Venues</h5>

            <div class="d-flex justify-content-between mb-2">
                <div class="a"><select class="form-control hide-inactive">
                    <option> Select Option </option>
                    <option data-href="{{ route('venues.index') }}?inactive=false"
                    @if($inactive == 'false')
                    selected
                    @endif

                    >Hide Inactive Entires</option>
                    <option data-href="{{ route('venues.index') }}?inactive=true"
                     @if($inactive == 'true')
                    selected
                    @endif >Show Inactive Entires</option>
                </select></div>
                <div class="b"><a href="{{ route('book.show') }}" class="btn btn-secondary mt-2" target="_blank">Booking Form</a></div>


            </div>

            <table class="table-with-buttons table table-responsive cell-border">
                <thead>
                    <tr>
                        <th>No</th>
                        <th> Status </th>
                        <th>Country Name</th>
                        <th>State/City</th>
                        {{-- <th style="width: 200px">Sahib-e-Dua Name</th> --}}
                        <th>Site Admin</th>
                        <th>Venue Address</th>
                        <th>Venue Detail</th>
                        <th>Issued Dua</th>
                        <th>Issued Dum</th>

                        <th>Issued WorkingDua</th>
                        <th>Issued WorkingDum</th>
                        <th>Type</th>
                        <th>Slot Generated</th>

                        <th style="width: 300px">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @php $i=1;@endphp
                    @foreach ($venuesAddress as $venueAdd)

                    @php
                        $dateTime = \Carbon\Carbon::parse($venueAdd->venue_date);
                        $dateToConvert = \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $dateTime);
                        $formattedDate  = $dateToConvert->format('d-M-Y h:i A');
                        $weekDay = $dateToConvert->format('l');
                        $today =  \Carbon\Carbon::parse(date('Y-m-d'))->format('Y-m-d');
                        $slotCreated = $venueAdd->venueSloting->count();

                        $totalBookings = [];
                        $totalTokens = [];


                        foreach($visitors as $visitor){


                            if($slotCreated > 0 && $visitor->slot->type !== null && $visitor->slot->type == 'dua' ){

                                $totalBookings[$visitor->slot->venue_address_id][$visitor->slot->type][] = $visitor->slot->id ;

                            }
                            if($slotCreated > 0 && $visitor->slot->type !== null && $visitor->slot->type == 'dum' ){
                                $totalBookings[$visitor->slot->venue_address_id][$visitor->slot->type][] = $visitor->slot->id ;



                            }
                            if($slotCreated > 0 && $visitor->slot->type !== null && $visitor->slot->type == 'working_lady_dua' ){

                                $totalBookings[$visitor->slot->venue_address_id][$visitor->slot->type][] = $visitor->slot->id ;

                            }
                            // if($slotCreated > 0 && $visitor->slot->type !== null && $visitor->slot->type == 'working_lady_dum' ){
                            //   $totalBookings[$visitor->slot->venue_address_id][$visitor->slot->type][] = $visitor->slot->id ;


                            // }

                        }

                        $hideClass  = 'd-none';

                        if($venueAdd->venue_date >= $today ||  $inactive == 'true' ){

                            $hideClass  = 'active';
                        }else{
                            $hideClass  = 'd-none';
                        }


                    @endphp

                        <tr class="{{ $hideClass }}">
                            <td>{{ $i }}</td>
                            <td>
                                @if($inactive =='false' || $venueAdd->venue_date >= $today)
                                <span class="badge bg-success">Active</span>
                                @elseif($inactive =='true')
                                <span class="badge bg-secondary">Inactive</span>
                                @endif

                            </td>
                            <td>{{ $venueAdd->venue->country_name }}  <img src="{{ env('AWS_GENERAL_PATH') . 'flags/' . $venueAdd->venue->flag_path }}"
                                    alt="Flag Image">
                            </td>
                            <td>  {{ $venueAdd->city }} </td>
                            {{-- <td>{{ $venueAdd->user->name }}</td> --}}
                            <td>{{ $venueAdd->siteadmin->name }}</td>
                            <td>{{  strlen($venueAdd->address) > 80 ? substr($venueAdd->address,0,80)."..." : $venueAdd->address }}</td>

                            <td>{{ $formattedDate }} ({{ $weekDay }})</td>
                            <td style="text-align: center">
                                {{  (isset($totalBookings[$venueAdd->id]['dua'])) ?count($totalBookings[$venueAdd->id]['dua']):0 }}
                                 / {{getTotalTokens($venueAdd->id , 'dua')}}
                            </td>
                            <td style="text-align: center">{{
                            (isset($totalBookings[$venueAdd->id]['dum']))?count($totalBookings[$venueAdd->id]['dum']):0 }}

                            / {{getTotalTokens($venueAdd->id , 'dum')}}  </td>


                            <td style="text-align: center">
                                {{  (isset($totalBookings[$venueAdd->id]['working_lady_dua'])) ?count($totalBookings[$venueAdd->id]['working_lady_dua']):0 }}
                                 / {{getTotalTokens($venueAdd->id , 'working_lady_dua') }}
                            </td>
                            <td style="text-align: center"> {{  (isset($totalBookings[$venueAdd->id]['working_lady_dum'])) ?count($totalBookings[$venueAdd->id]['working_lady_dum']):0 }}
                                / {{getTotalTokens($venueAdd->id , 'working_lady_dum') }}  </td>
                            <td><span class="badge bg-success">{{ ($venueAdd->type == 'on-site') ? 'Physical' : 'Online' }}</span></td>
                            <td><span class="badge bg-{{  ($slotCreated > 0) ? "success" : "warning" }}"> {{  ($slotCreated > 0) ? 'Generated': 'In-porcess'  }} </span> </td>
                            <td class="d-flex-my cdt justify-content-between">

                                @php
                                    $currentTime = \Carbon\Carbon::now()->tz($venueAdd->timezone ?? 'Asia/Karachi');
                                @endphp

                                <a href="{{ $currentTime->gte($venueAdd->venue_date) ? '#' : route('venues.edit', $venueAdd->id) }}"
                                    class="btn btn-primary{{ $currentTime->gte($venueAdd->venue_date) ? ' disabled' : '' }}">
                                     {{ ($currentTime->gte($venueAdd->venue_date)) ? 'Time up for Edit' : 'Edit' }}
                                </a>


                                {{-- <a href="{{ route('venues.edit', $venueAdd->id) }}" class="btn btn-primary">Edit</a> --}}

                                <form action="{{ route('venues.destroy', $venueAdd->id) }}" method="POST"
                                    style="display: inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger"
                                        onclick="return confirm('Are you sure you want to delete this visitor?')">Delete</button>
                                </form>
                                <a href="{{ route('book.add',[$venueAdd->id]) }}" class="btn btn-info">BookSlot</a>

                                <button  id="copyButton" class="btn btn-warning copyButton" data-href="{{ route('waiting-queue',[$venueAdd->id]) }}">CopyLink</button>

                                <form action="{{ route('venues.pause', $venueAdd->id) }}" method="post">
                                    @csrf
                                    <input type="hidden" name="status" value="{{$venueAdd->status}}">
                                    @if($venueAdd->status == 'active')
                                       <button  id="pauseBtn" class="btn btn-danger" >Click to Pause</button>
                                    @else
                                      <button  id="pauseBtn" class="btn btn-success" >Click to Resume</button>
                                    @endif

                                </form>
                            </td>
                        </tr>

                        @php $i++;@endphp
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <style>
        .d-flex-my {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
        }

        .justify-content-between {
            justify-content: space-between;
            width: 100%;
        }

       .cdt .btn {
            margin-bottom: 5px;
            width: 100%; /* Full width for buttons on small screens */
        }
        table.dataTable tbody th, table.dataTable tbody td{
            padding: 10px 3px !important;
        }

        @media only screen and (max-width: 768px) {
            /* Styles for tablets and larger screens */
            .cdt .btn {
                flex: 0 0 calc(100% - 10px); /* Adjust the percentage based on your needs for tablets */
                margin-right: 10px;
            }

            .cdt form {
                flex: 0 0 calc(100% - 10px); /* Adjust the percentage based on your needs for tablets */
                margin-right: 10px;
            }
        }
        .cdt  button,a {
                flex: 0 0 calc(50% - 5px); /* Adjust the percentage based on your needs for laptops */
            }
        .cdt form {
            flex: 0 0 calc(50% - 5px); /* Adjust the percentage based on your needs for tablets */
            /* margin-right: 10px; */
        }

        @media only screen and (max-width: 1024px) {
            /* Styles for laptops and larger screens */

        }
        .mybtns {
            float: right;
            margin: 10px 0;
        }
    </style>

@endsection
@section('page-script')
<script>
document.title = 'Venue List';
$(".hide-inactive").change(function(){
    var url = $(this).find(':selected').attr('data-href');
    if(url){
        location.href=url

    }
})

setInterval(() => {
    location.reload();
}, 10000 * 6);

</script>
@endsection
