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
            <h5 class="card-title">Manage Venues</h5>

            <table class="table-with-buttons table table-responsive cell-border">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Country Name</th>
                        <th>State/City</th>
                        
                        <th>Sahib-e-Dua Name</th>
                        <th>Site Admin</th>
                        <th>Venue Address</th>
                        <th>Venue Detail</th>
                        <th>Type</th>
                        <th width="400px">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @php $i=1;@endphp
                    @foreach ($venuesAddress as $venueAdd)

                    @php
                        $dateToConvert = \Carbon\Carbon::createFromFormat('Y-m-d', $venueAdd->venue_date);
                        $formattedDate = $dateToConvert->format('d-M-Y');
                        $weekDay = $dateToConvert->format('l');
                    @endphp
                        <tr>
                            <td>{{ $i }}</td>
                            <td>{{ $venueAdd->venue->country_name }} 
                                <img src="{{ env('AWS_GENERAL_PATH') . 'flags/' . $venueAdd->venue->flag_path }}"
                                    alt="Flag Image">
                            </td>
                            <td>{{ $venueAdd->state }} / {{ $venueAdd->city }} </td>
                            <td>{{ $venueAdd->user->name }}</td>
                            <td>{{ $venueAdd->siteadmin->name }}</td>
                            <td>{{ $venueAdd->address }}</td>
                            <td>{{ $formattedDate }} ({{ $weekDay }})</td>
                            <td><span class="badge bg-success">{{ ($venueAdd->type == 'on-site') ? 'Physical' : 'Online' }}</span></td>
                            <td class="d-flex cdt justify-content-between"> 
                                <a href="{{ route('venues.edit', $venueAdd->id) }}" class="btn btn-primary">Edit</a>
                                <form action="{{ route('venues.destroy', $venueAdd->id) }}" method="POST"
                                    style="display: inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger"
                                        onclick="return confirm('Are you sure you want to delete this visitor?')">Delete</button>
                                </form>
                                <a href="{{ route('book.add',[$venueAdd->id]) }}" class="btn btn-info">Book Slot</a>
                                <button  id="copyButton" class="btn btn-warning copyButton" data-href="{{ route('waiting-queue',[$venueAdd->id]) }}">Copy Link</button>
                            </td>
                        </tr>
                        @php $i++;@endphp
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

<style>
    td.d-flex.cdt {
        padding: 14px; 
}
</style>


@endsection
@section('page-script')
<script>document.title = 'Venue List'; </script>
@endsection