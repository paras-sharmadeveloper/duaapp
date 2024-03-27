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
        <h5 class="card-title">Reasons List</h5>

        <a href="{{ route('reasons.create') }}" class="btn btn-primary mb-3">Add Reason</a>

        <table class="table-with-buttons table table-responsive cell-border dataTable no-footer ">
            <thead>
                <tr>
                    <th>Label</th>
                    <th>Reason English</th>
                    <th>Reason Urdu</th>
                    <th>Reason IVR</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($reasons as $reason)
                <tr>
                    <td>{{ $reason->label }}</td>
                    <td>{{ $reason->reason_english }}</td>
                    <td>{{ $reason->reason_urdu }}</td>
                    <td>
                        <audio controls>
                            <source src="{{ $reason->reason_ivr_path }}" type="audio/ogg">
                          </audio>
                    </td>
                    <td>
                        <a href="{{ route('reasons.edit', $reason->id) }}" class="btn btn-sm btn-primary">Edit</a>
                        <form action="{{ route('reasons.destroy', $reason->id) }}" method="POST" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this reason?')">Delete</button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>


</div>


@endsection
