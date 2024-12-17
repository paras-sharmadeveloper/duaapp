<!-- index.blade.php -->

@extends('layouts.app')

@section('content')

<div class="card">
 @include('alerts')


    <div class="card-body">
        <h5 class="card-title">Door Logs </h5>
        <table class="table-with-buttons table table-responsive cell-border">
            <thead>
                <tr>
                    <th>Door Access Timestamp</th>
                    <th> Dua Ghar </th>
                    <th> Dua Type </th>
                    <th> Token Number </th>
                    <th> Out of Sequence Access </th>
                    <th> Token URL </th>
                    <th> Actions </th>
                </tr>
            </thead>
            <tbody>
                @foreach($logs as $list)
                @php

                  $visitor =   getVisitor($list->SCode);

                @endphp
                <tr>
                    <td>{{$list->created_at->format('d-m-Y h:i:s A') }} </td>
                    <td> {{ ($visitor) ? $visitor->venueSloting->venueAddress->city : '' }} </td>
                    <td> {{ ($visitor) ? $visitor->venueSloting->type : ''}} </td>
                    <td> {{ ($visitor) ? $visitor->booking_number : ''}} </td>
                    <td> Yes </td>
                    <td><a href="{{ ($visitor) ? route('booking.status', $visitor->booking_uniqueid):"#" }}"
                        target="_blank">{{ ($visitor)  ? route('booking.status', $visitor->booking_uniqueid) : '' }} </a>
                    </td>
                    <td> <button> out of Sequence</button> </td>

                </tr>
                @endforeach
            </tbody>
        </table>
    </div>


</div>


@endsection
