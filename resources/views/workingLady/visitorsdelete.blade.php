@extends('layouts.app')

@section('content')
    <div class="card">
        <div class="card-body">
            <h5 class="card-title">Visitor Bookings</h5>
            <table class="table-with-buttons table table-responsive cell-border">
                <thead>
                    <tr>
                        <th>BookingNo</th>
                        <th>Phone</th>
                        <th>Code</th>
                        <th>Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($visitors as $booking)
                        <tr>
                            <td>{{ $booking->booking_number }}</td>
                            <td>{{ $booking->phone }}</td>
                            <td>{{ $booking->recognized_code }}</td>
                            <td>{{ $booking->created_at }}</td>


                            <td><form action="{{ route('delete.object', ['id' => $booking->recognized_code]) }}" method="POST"
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
