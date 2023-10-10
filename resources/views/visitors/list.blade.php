@extends('layouts.app')

@section('content')
    <div class="card">
        <div class="card-body">
            <h5 class="card-title">Visitor Bookings</h5>
            <table class="table-with-buttons table table-responsive cell-border">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Purpose</th>
                        <th>Visit Date and Time</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($bookings as $booking)
                        <tr>
                            <td>{{ $booking->name }}</td>
                            <td>{{ $booking->email }}</td>
                            <td>{{ $booking->phone }}</td>
                            <td>{{ $booking->purpose }}</td>
                            <td>{{ $booking->visit_datetime }}</td>
                            <td>
                                <a href="{{ route('booking.edit', ['id' => $booking->id]) }}" class="btn btn-primary">Edit</a>

                               
                                <form action="{{ route('booking.delete', ['id' => $booking->id]) }}" method="POST"
                                    style="display: inline;">
                                    @csrf
                                @method('delete')
                                    <button type="submit" class="btn btn-danger"
                                        onclick="return confirm('Are you sure you want to delete this booking?')">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    </div>
@endsection
