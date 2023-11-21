@extends('layouts.app')
@section('content')
<style>
  
.table-wrapper {
    width: 100%;
    /* max-width: 500px; */
    overflow-x: auto;
  }

</style>
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
            <div class="action d-flex justify-content-between">
                <h5 class="card-title">Manage Queue</h5>
                <div class="row">
                    <div class="col-xl-12">
                        <input type="text" name="" id="search" class="form-control" placeholder="search">
                    </div>
                </div>
            </div>
            
            @if (request()->route()->getName() == 'siteadmin.queue.show')

            <div class="row">
 
                <div class="col-xl-12  users-list-header">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">

                                <div class="tokn">
                                    <span class="fw-bold rounded-circle text-center">Sr.No</span>
                                </div>
                                <div class="ms-3">
                                    <p class="fw-bold mb-1">Venue Info</p>
                                </div>
                                <span class="fw-bold">Venue Date</span>
                                <span class="fw-bold">Action</span>
                            </div>
                        </div>

                    </div>
                </div>


                @php $i=0; @endphp
                @foreach ($venueAddress as $venueAdd)
                @php
                    $venueDate = \Carbon\Carbon::parse($venueAdd->venue_date)->format('d-M-Y');
                    $startTimeFormattedMrg = \Carbon\Carbon::parse($venueAdd->slot_starts_at_morning)->format('h:i A');
                    $endTimeFormattedMrg = \Carbon\Carbon::parse($venueAdd->slot_ends_at_morning)->format('h:i A');
                    $startTimeFormattedEvg = ($venueAdd->slot_starts_at_evening) ? \Carbon\Carbon::parse($venueAdd->slot_starts_at_evening)->format('h:i A') : '';
                    $endTimeFormattedEvg = ($venueAdd->slot_ends_at_evening)?\Carbon\Carbon::parse($venueAdd->slot_ends_at_evening)->format('h:i A'):'';
                @endphp
                <div class="col-xl-12 mb-1 users-list">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">

                                <div class="token">
                                    <span class="rounded-circle text-center h6">{{ ++$i }}</span>
                                </div>
                                <div class="ms-3">
                                     
                                    <p class="fw-bold mb-1 h6"> 
                                        {{ $venueAdd->user->name }}
                                        <h6 class="sub-title">Morning {{ $startTimeFormattedMrg }} - {{ $endTimeFormattedMrg }}</h6>
                                        @if($startTimeFormattedEvg)
                                        <h6 class="sub-title">Evening {{ $startTimeFormattedEvg }} - {{ $endTimeFormattedEvg }}</h6>
                                        @endif
                                    </p>
                                    
                                        
                                   
                                </div>
                                <p class="text-muted mb-0 h6"> {{ $venueDate }}</p>
                                <a href="{{ route('siteadmin.queue.list', [$venueAdd->id]) }}"
                                    class="btn btn-info text-center">Start</a>
                                {{-- <span class="badge rounded-pill badge-success h2">Active</span> --}}
                                {{-- <span class="badge badge-warning rounded-pill d-inline h2">Awating..</span> --}}
                            </div>
                        </div>

                    </div>
                </div>
                @endforeach
                 
                 
             </div> 
 

        @endif


    </div>
    </div> 

@endsection

@section('page-script')
<script>
    $(document).ready(function() {
        // Add an input event listener to the search input
        $("#search").on("input", function() {
            // Get the search input value
            var searchText = $(this).val().toLowerCase();

            // Loop through each card
            $(".users-list .card").each(function() {
                // Get the text content of each card
                var cardText = $(this).text().toLowerCase();

                // Check if the card text contains the search text
                if (cardText.includes(searchText)) {
                    // Show the card if it matches
                    $(this).show();
                } else {
                    // Hide the card if it doesn't match
                    $(this).hide();
                }
            });
        });
    });
</script>
@endsection
