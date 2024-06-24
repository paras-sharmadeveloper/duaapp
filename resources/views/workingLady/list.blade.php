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
                    <th>Full name </th>
                    <th>Email</th>
                    <th>Mobile </th>
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
                    <td>{{ $list->first_name }} {{ $list->last_name }} </td>
                    <td>{{ $list->email }}</td>
                    <td>{{ $list->mobile }}</td>
                    <td>{{ $list->designation }}</td>
                    <td>{{ $list->employer_name }}</td>
                    <td>{{ $list->place_of_work }}</td>
                    <td>{{( $list->is_active =='active') ? 'Yes' : 'No' }}</td>
                    <td class="d-flex justify-content-between">
                        <a href="{{route('working.lady.view', $list->id) }}" class="btn btn-sm btn-primary">View</a>

                        <form id="deleteForm" data-action="{{route('working.delete',[ $list->id ])}}" method="post">
                            @csrf
                            <button class="btn btn-danger" type="submit" onclick="return confirm('Are you sure you want to delete?')">Delete</button>
                        </form>


                    </td>


                </tr>
                @endforeach
            </tbody>
        </table>
    </div>


</div>


@endsection
