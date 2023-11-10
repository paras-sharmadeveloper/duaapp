@extends('layouts.app')
@section('content')
    <div class="row">
        <div class="col-lg-12 margin-tb">

            <div class="action-top float-end mb-3">
                <!-- <button type="button" class="btn btn-primary"><i class="bi bi-star me-1"></i> With Text</button> -->
                <a class="btn btn-outline-primary" href="{{ route('users.create') }}"> <i class="bi bi-plus me-1"></i> New
                    User</a>
            </div>
        </div>
    </div>


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
    <div class="card">
        <div class="card-body">
            <h5 class="card-title">Manage Users</h5>

            <table class="table-with-buttons table table-responsive cell-border" >
                <thead>
                <tr>
                    <th>No</th>
                    <th>Profile Pic </th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Roles</th>
                    <th width="280px">Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($data as $key => $user)
                    <tr>
                        <td>{{ ++$i }}</td>
                        <td><div class="col-xs-12 col-sm-12 col-md-12 text-center">
                            @if (!empty($user->profile_pic) && Storage::disk('s3_general')->exists('images/' . $user->profile_pic))
                                <img src="{{ env('AWS_GENERAL_PATH') . 'images/' . $user->profile_pic }}" class="imgh"
                                    alt="Flag Image" style="height: 100px; width: 100px;border-radius:50%">
                            @else
                                <img src="https://t3.ftcdn.net/jpg/03/46/83/96/360_F_346839683_6nAPzbhpSkIpb8pmAwufkC7c5eD7wYws.jpg"
                                    class="imgh" alt="Default Image" style="height: 100px; width: 100px;border-radius:50%">
                            @endif
                        </div>  </td>
                        <td>{{ $user->name }}</td>
                        <td>{{ $user->email }}</td>
                        <td>
                            @if (!empty($user->getRoleNames()))
                                @foreach ($user->getRoleNames() as $v)
                                    <span class="badge bg-success">{{ $v }}</span>
                                @endforeach
                            @endif
                        </td>
                        <td>
                            <a class="btn btn-info" href="{{ route('users.show', $user->id) }}">Show</a>
                            <a class="btn btn-primary" href="{{ route('users.edit', $user->id) }}">Edit</a>
                            {!! Form::open(['method' => 'DELETE', 'route' => ['users.destroy', $user->id], 'style' => 'display:inline']) !!}
                            {!! Form::submit('Delete', ['class' => 'btn btn-danger']) !!}
                            {!! Form::close() !!}
                        </td>
                    </tr>
                
                @endforeach
            </tbody>
            </table>
        </div>
    </div>




@endsection
@section('page-script')
<script>document.title = 'User List'; </script>
@endsection
