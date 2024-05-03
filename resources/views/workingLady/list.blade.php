<!-- index.blade.php -->

@extends('layouts.app')

@section('content')

<div class="card">
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


    <div class="card-body">
        <h5 class="card-title">Working Lady List</h5>


        <table class="table-with-buttons table table-responsive cell-border">
            <thead>
                <tr>
                    <th>Info</th>
                    <th>Designation</th>
                    <th>Employer Name</th>
                    <th>Place of Work</th>
                    <th>Approved?</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($registration as $list)
                <tr>
                    <td>{{ $list->first_name }} {{ $list->last_name }}
                        <span>{{ $list->email }}</span>
                        <span>{{ $list->mobile }}</span>
                        <span>{{ $list->email }}</span>
                    </td>

                    <td>{{ $list->designation }}</td>
                    <td>{{ $list->employer_name }}</td>
                    <td>{{ $list->place_of_work }}</td>
                    <td>{{( $list->is_active =='active') ? 'Yes' : 'No' }}</td>

                    <td>

                        <a href="{{route('working.lady.view', $list->id) }}" class="btn btn-sm btn-primary">View</a>


                        {{-- <form action="{{ route('reasons.destroy', $list->id) }}" method="POST" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this reason?')">Delete</button>
                        </form> --}}
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>


</div>


@endsection
