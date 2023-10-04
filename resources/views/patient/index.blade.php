@extends('layouts.app')
@section('content')
    {{-- <div class="row">
        <div class="col-lg-12 margin-tb">

            <div class="action-top float-end mb-3">
                <!-- <button type="button" class="btn btn-primary"><i class="bi bi-star me-1"></i> With Text</button> -->
                <a class="btn btn-outline-primary" href="{{ route('venues.create') }}"> <i class="bi bi-plus me-1"></i> New
                    User</a>
            </div>
        </div>
    </div> --}}


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
            <h5 class="card-title">Manage Vistors</h5>

            <table class="datatable table table-striped">
                <tr>
                    <th>BookingId</th>
                    <th>UserName</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>UserIp</th>
                    <th>WhatsApp</th>
                    <th>Booked Slot</th>
                    <th>Venue Address</th>
                    <th width="280px">Action</th>
                </tr>
                
                @if(!empty($vistors))
                @foreach ($vistors as $vistor)

                @php 
                   $venueAddress = \App\Models\VenueAddress::getAddress($vistor->slot->venue_address_id);
                   $venues =  \App\Models\Venue::getVenue($venueAddress->venue_id); 
                    
                @endphp 

                    <tr>
                        <td>{{ $vistor->booking_uniqueid }}</td>
                        
                        <td>{{ $vistor->fname . ' ' .  $vistor->lname }}</td>
                        <td>{{ $vistor->email }}</td>
                        <td>{{ $vistor->phone }}</td>
                        <td>{{ $vistor->user_ip }}</td>
                        <td>{{ $vistor->is_whatsapp }}</td>
                        <td>{{ $venueAddress->venue_date .' ' .$vistor->slot->slot_time }}</td>
                        <td> {{ $venueAddress->address  }} {{ '('. $venues->country_name .')' }} </td>
                        <td> 
                            <form action="{{ route('visitor.delete', $vistor->id) }}" method="DELETE"
                                style="display: inline;">
                                @csrf 
                                <button type="submit" class="btn btn-danger"
                                    onclick="return confirm('Are you sure you want to delete this venue?')">Delete</button>
                            </form>
                        </td>
                    </tr>
                @endforeach

                @endif
                 
            </table>
        </div>
    </div>
@endsection
 