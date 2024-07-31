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
