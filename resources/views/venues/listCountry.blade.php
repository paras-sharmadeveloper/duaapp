@extends('layouts.app')
@section('content')
    <div class="row">
        <div class="col-lg-12 margin-tb">

            <div class="action-top float-end mb-3">
                <!-- <button type="button" class="btn btn-primary"><i class="bi bi-star me-1"></i> With Text</button> -->
                <a class="btn btn-outline-primary" href="{{ route('country.create') }}"> <i class="bi bi-plus me-1"></i> New
                    Country</a>
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
            <h5 class="card-title">Manage Countries</h5>

            <table class="table-with-buttons table table-responsive cell-border">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Country</th>
                        <th>Flag</th>
                        <th width="280px">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @php $i=1;@endphp
                    @foreach ($venues as $venue)
                        <tr>
                            <td>{{ $i }}</td>
                            <td class="flag-country asd">
                                <div class="d-flex justify-content-between">
                                    <span> {{ $venue->country_name }} </span>
                                </div>
                            </td>

                            <td> <img src="{{ env('AWS_GENERAL_PATH') . 'flags/' . $venue->flag_path }}" alt="Flag Image">
                            </td>
                            <td>
                                <a href="{{ route('country.edit', $venue->id) }}" class="btn btn-primary">Edit</a>
                                <form action="{{ route('country.destroy', $venue->id) }}" method="POST"
                                    style="display: inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger"
                                        onclick="return confirm('Are you sure you want to delete this visitor?')">Delete</button>
                                </form>
                            </td>
                        </tr>
                        @php $i++;@endphp
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>




@endsection
@section('page-script')
<script>document.title = 'List Countries'; </script>
@endsection
