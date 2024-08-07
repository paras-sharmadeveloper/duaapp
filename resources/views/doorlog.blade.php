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
                    <th>TimeStamp</th>
                    <th> Dua Ghar </th>
                    <th> Date of Dua Day </th>
                    <th> Dua Type </th>
                    <th> Phone </th>
                    <th> Token Number </th>
                    <th> Token URL </th>
                    <th>Sn</th>
                    <th>SCode </th>
                    <th>DeviceID</th>
                    <th>ReaderNo</th>
                    <th>ActIndex</th>


                </tr>
            </thead>
            <tbody>
                @foreach($logs as $list)
                @php

                  $visitor =   getVisitor($list->SCode);

                @endphp
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $list->created_at }}</td>
                    <td> {{ ($visitor) ? $visitor->venueSloting->venueAddress->city : null }} </td>
                    <td> {{ ($visitor) ? $visitor->venueSloting->venueAddress->venue_date: null }} </td>
                    <td> {{ ($visitor) ? $visitor->venueSloting->type : null}} </td>
                    <td> {{ ($visitor) ? $visitor->phone : null }} </td>
                    <td> {{ ($visitor) ? $visitor->booking_number : null}} </td>
                    <td> <a href="{{  ($visitor) ? route('booking.status', $visitor->booking_uniqueid):"#" }}" target="_blank"> Token Url</a> </td>
                    <td>{{ $list->SN }}</td>
                    <td>{{ $list->SCode }}</td>
                    <td>{{ $list->DeviceID }}</td>
                    <td>{{ $list->ReaderNo }}</td>
                    <td>{{ $list->ActIndex }}</td>

                </tr>
                @endforeach
            </tbody>
        </table>
    </div>


</div>


@endsection
