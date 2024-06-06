@extends('layouts.app')
@section('content')

    <div class="row">
        <div class="col-lg-12 margin-tb">

            <div class="action-top float-end mb-3">
                <a class="btn btn-outline-primary" href="{{ route('users.index') }}"> <i
                        class="bi bi-skip-backward-circle me-1"></i> Back</a>
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
    <div class="col-lg-12">

        <div class="card">
            <div class="card-body">
                <h5 class="card-title">Edit User </h5>

                <form action="{{ route('users.update', $user->id) }}" method="POST" enctype="multipart/form-data" class="row g-3">
                    @method('PATCH')
                    @csrf

                <div class="col-md-6">
                    <div class="input-group">
                        <span class="input-group-text" id="inputGroupPrepend2">Name</span>
                        <input type="text" name="name" placeholder="Name" class="form-control" value="{{ ($user) ? $user->name : '' }}">


                    </div>
                </div>
                <div class="col-md-6">
                    <div class="input-group">
                        <span class="input-group-text" id="inputGroupPrepend2">Email</span>
                        <input type="text" name="email" placeholder="Email" class="form-control"  value="{{ ($user) ? $user->email : '' }}">

                    </div>
                </div>
                <div class="col-md-6">
                    <div class="input-group">
                        <span class="input-group-text" id="inputGroupPrepend2">Password</span>
                        <input type="password" name="password" placeholder="Password" class="form-control">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="input-group">
                        <span class="input-group-text" id="inputGroupPrepend2">C.Password</span>
                        <input type="password" name="confirm-password" placeholder="Confirm Password" class="form-control">

                    </div>
                </div>
                <div class="col-md-6">
                    <div class="input-group">
                        <span class="input-group-text" id="inputGroupPrepend2">Roles</span>
                        <select class="form-control" name="roles[]">
                            @foreach ($roles as $role)
                                <option @if ($userRole == $role) selected @endif value="{{ $role }}">
                                    {{ $role }}</option>
                            @endforeach
                        </select>

                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-12 d-flex justify-content-center">
                    <div class="profile-pic">
                        <label class="-label" for="file">
                            <span class="glyphicon glyphicon-camera"></span>
                            <span>Change Image</span>
                        </label>
                        <input id="file" type="file" name="profile_pic" onchange="loadFile(event)" />
                        @if (!empty($user->profile_pic) && Storage::disk('s3_general')->exists('images/' . $user->profile_pic))
                            <img id="output" src="{{ env('AWS_GENERAL_PATH') . 'images/' . $user->profile_pic }}"
                                alt="Profile Image">
                        @else
                            <img id="output"
                                src="https://t3.ftcdn.net/jpg/03/46/83/96/360_F_346839683_6nAPzbhpSkIpb8pmAwufkC7c5eD7wYws.jpg"
                                width="200" alt="Profile Image">
                        @endif

                    </div>
                </div>
                <span class="text-center text-warning mt-1" > Recommended  Size <strong class="text-info">300 X 300</strong> Pixel </span>
            </div>



            <div class="col-12 text-end p-4">
                <button class="btn btn-primary" type="submit">Update</button>
            </div>
        </div>
    </form>


    </div>
    </div>
    </div>
    <style>
        img.imgh {
            border-radius: 50%;
            position: absolute;
            top: 60%;
            right: 22%;
        }

        .tagify {
            width: 100%;
            max-width: 700px;
        }

        .profile-pic {
            color: transparent;
            transition: all 0.3s ease;
            display: flex;
            justify-content: center;
            align-items: center;
            position: relative;
            transition: all 0.3s ease;
        }

        .profile-pic input {
            display: none;
        }

        .profile-pic img {
            position: absolute;
            object-fit: cover;
            width: 200px !important;
            height: 200px !important;
            box-shadow: 0 0 10px 0 rgba(255, 255, 255, 0.35);
            border-radius: 100px;
            z-index: 0;
        }

        .profile-pic .-label {
            cursor: pointer;
            height: 200px;
            width: 200px;
        }

        .profile-pic:hover .-label {
            display: flex;
            justify-content: center;
            align-items: center;
            background-color: rgba(0, 0, 0, 0.8);
            z-index: 10000;
            color: #fafafa;
            transition: background-color 0.2s ease-in-out;
            border-radius: 100px;
            margin-bottom: 0;
        }

        .profile-pic span {
            display: inline-flex;
            padding: 0.2em;
            height: 2em;
        }
    </style>

@endsection



@section('page-script')
    <script>
        var loadFile = function(event) {
            var image = document.getElementById("output");
            image.src = URL.createObjectURL(event.target.files[0]);
        };
    </script>
    <script>
        document.title = 'Edit User';
    </script>
@endsection
