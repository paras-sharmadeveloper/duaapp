@extends('layouts.app')

@section('content')
    <style>
        .tr-overlay {
            width: 100%;
            background-color: #333;
            overflow: hidden;
            margin: 30px 0px;
            text-align: center;
        }

        .wrapper {
            height: auto;
        }

        .tr-overlay .container {
            color: white;
        }

        .multiselect input[type="checkbox"] {
            /* display: none; */
        }

        .multiselect label {
            display: block;
            text-indent: -1.2em;
            padding: 0.2em 0 0.2em 1.2em;
        }

        .multiselect-on {
            color: #ffffff;
            background-color: #0E8EFF;
        }

        .multiselect-blurred {
            background: lightgray;
        }
    </style>

    <div class="row">
        <div class="col-lg-12 margin-tb">
            <div class="action-top float-end mb-3">
                <a class="btn btn-outline-primary" href="{{ route('venues.index') }}">
                    <i class="bi bi-skip-backward-circle me-1"></i> Back
                </a>
            </div>
        </div>
    </div>

    @if ($errors->any())
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

    <div class="container">
        <!-- Message Sending Form -->
        <div class="card mt-4">
            <div class="card-header">
                <h4>Logs</h4>
            </div>
            <div class="card-body">

                    <!-- Table to Display Recipients with Checkboxes -->
                    <div class="table-responsive mb-3">
                        <table id="recipientTable" class="table table-bordered">
                            <thead>
                                <tr>
                                    <th><input type="checkbox" id="selectAll"></th>
                                    <th>Date</th>
                                    <th>Phone Number</th>
                                    <th>WhatsApp Message</th>
                                    <th>Sid</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($recipients as $recipient)
                                    <tr>
                                        <td><input type="checkbox" class="recipient-checkbox" value="{{ $recipient->id }}"></td>
                                        <td>{{ $recipient->venue_date }}</td>
                                        <td>{{ $recipient->mobile }}</td>
                                        <td>{{ $recipient->whatsAppMessage }}</td>
                                        <td>{{ $recipient->msg_sid }}</td>
                                        <td>{{ $recipient->msg_sent_status }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
            </div>
        </div>
    </div>
@endsection

@section('page-script')
    <!-- jQuery and DataTable -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="https://cdn.datatables.net/1.12.1/js/jquery.dataTables.min.js"></script>


@endsection
