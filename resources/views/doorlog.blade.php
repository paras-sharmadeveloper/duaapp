<!-- index.blade.php -->

@extends('layouts.app')

@section('content')

<div class="card">
 @include('alerts')


    <div class="card-body">
        <h5 class="card-title">Working Lady List</h5>


        <table class="table-with-buttons table table-responsive cell-border">
            <thead>
                <tr>
                    <th>Sr. No</th>
                    <th> Dua Ghar </th>
                    <th> phone </th>
                    <th> Token Number </th>
                    <th> Token URL </th>
                    <th>Sn</th>
                    <th>SCode </th>
                    <th>DeviceID</th>
                    <th>ReaderNo</th>
                    <th>ActIndex</th>
                    <th>CreatedAt</th>

                </tr>
            </thead>
            <tbody>
                @foreach($logs as $list)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td> {{ $list->visitor->venueSloting->venueAddress->address }} </td>
                    <td> {{ $list->visitor->phone }} </td>
                    <td> {{ $list->visitor->booking_number }} </td>
                    <td> {{ route('booking.status',$list->visitor->booking_uniqueid)  }} </td>


                    <td>{{ $list->SN }}</td>
                    <td>{{ $list->SCode }}</td>
                    <td>{{ $list->DeviceID }}</td>
                    <td>{{ $list->ReaderNo }}</td>
                    <td>{{ $list->ActIndex }}</td>
                    <td>{{ $list->created_at }}</td>




                </tr>
                @endforeach
            </tbody>
        </table>
    </div>


</div>


@endsection
