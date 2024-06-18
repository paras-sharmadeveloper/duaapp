@extends('layouts.guest')
@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <h1>Server Logs</h1>
        <a href="{{ route('admin.logs.clear')}}" class="btn btn-primary">Clear Log</a>
        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
            <pre>{{ $logs }}</pre>
        </div>
    </div>
</div>

@endsection
