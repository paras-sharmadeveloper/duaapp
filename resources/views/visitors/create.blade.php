@extends('layouts.app')

@section('content')
    {{-- <div class="row">
        <div class="col-lg-12 margin-tb">

            <div class="action-top float-end mb-3">
                <a class="btn btn-outline-primary" href="{{ route('users.index') }}"> <i
                        class="bi bi-skip-backward-circle me-1"></i> Back</a>
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

    <div class="col-lg-12">

        <div class="card">
            <div class="card-body">

                <form action="{{ route('booking.store') }}" method="POST">
                    @csrf
                    @if (isset($booking))
                        @method('PUT')
                        <input type="hidden" name="id" value="{{ $booking->id }}">
                    @endif
                    <div class="form-group">
                        <label for="name">Name</label>
                        <input type="text" name="name" class="form-control"
                            value="{{ old('name', $booking->name ?? '') }}" required>
                    </div>
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" name="email" class="form-control"
                            value="{{ old('email', $booking->email ?? '') }}" required>
                    </div>
                    <div class="form-group">
                        <label for="phone">Phone</label>
                        <input type="text" name="phone" class="form-control"
                            value="{{ old('phone', $booking->phone ?? '') }}" required>
                    </div>
                    <div class="form-group">
                        <label for="purpose">Purpose of Visiting</label>
                        <textarea name="purpose" class="form-control" required>{{ old('purpose', $booking->purpose ?? '') }}</textarea>
                    </div>
                    <div class="form-group">
                        <label for="visit_datetime">Date and Time</label>
                        <input type="datetime-local" name="visit_datetime" class="form-control"
                            value="{{ old('visit_datetime', $booking->visit_datetime ?? '') }}" required>
                    </div>
                    <button type="submit" class="btn btn-primary mt-3">{{ isset($booking) ? 'Update' : 'Submit' }}</button>
                </form>
            </div>
        </div>
    </div>
@endsection
