@extends('layouts.app')


@section('content')



    <div class="row">
        <div class="col-lg-12 margin-tb">

            <div class="action-top float-end mb-3">
                <a class="btn btn-outline-primary" href="{{ route('permissions.index') }}"> <i
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


    <div class="card">
        <div class="card-body">
            <h5 class="card-title">Edit Permission </h5>
            <form action="{{ route('permissions.update', $permission->id) }}" method="POST" class="row g-3">
                @method('PATCH')
                @csrf
            <div class="row">
                <div class="col-md-9">
                    <div class="form-group">
                        <strong>Name:</strong>
                        <input type="text" name="name" placeholder="Name" class="form-control" value="{{ $permission->name }}">

                        {{-- {!! Form::text('name', null, ['placeholder' => 'Name', 'class' => 'form-control']) !!} --}}
                    </div>
                </div>


                <div class="col-md-9">
                    <button type="submit" class="btn btn-primary mt-5 ">Submit</button>
                </div>
            </div>
            </form>
        </div>
    </div>
    </div>
@endsection
