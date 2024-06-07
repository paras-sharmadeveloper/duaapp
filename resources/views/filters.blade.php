@extends('layouts.app')
@section('content')
    <div class="row">
        <div class="col-lg-12 margin-tb">

            <div class="action-top float-end mb-3">
                <!-- <button type="button" class="btn btn-primary"><i class="bi bi-star me-1"></i> With Text</button> -->
                <a class="btn btn-outline-primary" href="{{ route('venues.create') }}"> <i class="bi bi-plus me-1"></i> New
                    Venue</a>
            </div>
        </div>
    </div>
    @php
        $inactive = request()->get('inactive');
    @endphp
    <style>
        th,
        td {
            white-space: nowrap;
            /* Prevent text from wrapping */
        }

        div.dataTables_wrapper {
            width: 100%;
            margin: 0 auto;
        }
    </style>

    @include('alerts')
    <div class="card">
        <div class="card-body">
            <form action="{{ route('admin.filter') }}" method="get">
                <div class="row">
                    <div class="col-md-4">
                        <label> Filter Date </label>
                        <input class="form-control" type="date" name="date">
                    </div>
                    <div class="col-md-4">

                    </div>
                    <div class="col-md-4">

                    </div>
                </div>
                <button class="btn btn-secondary mt-4" type="submit">Filter </button>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <h5 class="card-title">Search Entries</h5>
            <table class="table-with-buttons1 table table-responsive cell-border" id="tokenFilter" style="width: 100%;">
                <thead>
                    <tr>
                        <th>DbId</th>
                        <th>Dua Type</th>
                        <th>Phone</th>
                        <th>Source</th>
                        <th>Token Url</th>
                        <th>Token</th>
                        <th style="width: 300px">Token Session Image </th>
                        <th style="width: 300px">Working Lady Session Image </th>
                        <th style="width: 300px">Checkin Time Stamp (PK Time Zone)</th>
                        <th style="width: 300px">Token Print Count</th>
                        <th style="width: 300px">1st Msg Sent Status</th>
                        <th style="width: 300px">1st Msg Sent Date</th>
                        <th style="width: 300px">1st Msg Sid </th>
                        <th style="width: 300px">Status</th>

                        <th style="width: 300px">Action</th>
                    </tr>
                    {{-- <tr>
                        <th><input type="text" placeholder="Search DbId" /></th>
                        <th><input type="text" placeholder="Search Dua Type" /></th>
                        <th><input type="text" placeholder="Search Phone" /></th>
                        <th><input type="text" placeholder="Search Source" /></th>
                        <th><input type="text" placeholder="Search Token Url" /></th>
                        <th><input type="text" placeholder="Search Token" /></th>
                        <th><input type="text" placeholder="Search Token Session Image" style="width: 100%;" /></th>
                        <th><input type="text" placeholder="Search Working Lady Session Image" style="width: 100%;" />
                        </th>
                        <th><input type="text" placeholder="Search Checkin Time Stamp" style="width: 100%;" /></th>
                        <th><input type="text" placeholder="Search Token Print Count" style="width: 100%;" /></th>
                        <th><input type="text" placeholder="Search 1st Msg Sent Status" style="width: 100%;" /></th>
                        <th><input type="text" placeholder="Search 1st Msg Sent Date" style="width: 100%;" /></th>
                        <th><input type="text" placeholder="Search 1st Msg Sid" style="width: 100%;" /></th>
                        <th><input type="text" placeholder="Search Status" style="width: 100%;" /></th>
                        <th><input type="text" placeholder="Search Action" style="width: 100%;" /></th>
                    </tr> --}}

                </thead>
                <tbody>
                    @foreach ($visitors as $visitor)
                        @php
                            $image = !empty($visitor->recognized_code) ? getImagefromS3($visitor->recognized_code) : '';
                            $workingLady = !empty($visitor->working_lady_id)
                                ? getWorkingLady($visitor->working_lady_id)
                                : [];
                            $workingLadySession = !empty($workingLady)
                                ? getImagefromS3($workingLady->session_image)
                                : '';

                        @endphp
                        <tr>
                            <td>{{ $visitor->id }}</td>
                            <td>{{ $visitor->dua_type }}</td>
                            <td>{{ $visitor->phone }}</td>
                            <td>{{ $visitor->source }}</td>
                            <td><a href="{{ route('booking.status', [$visitor->booking_uniqueid]) }}" target="_blank">
                                    Token Status </a> </td>
                            <td>{{ $visitor->booking_number }}</td>
                            <td>
                                @if ($image)
                                    <img src="data:image/jpeg;base64,{{ base64_encode($image) }}" alt="Preview Image"
                                        style="height: 150px; width:150px;border-radius:20%">
                                @else
                                    <img src="https://kahayfaqeer-general-bucket.s3.amazonaws.com/na+(1).png"
                                        alt="Preview Image" style="height: 150px; width:150px;border-radius:20%">
                                @endif
                            </td>
                            <td>
                                @if ($workingLadySession)
                                    <img src="data:image/jpeg;base64,{{ base64_encode($workingLadySession) }}"
                                        alt="Preview Image" style="height: 150px; width:150px;border-radius:20%">
                                @else
                                    <img src="https://kahayfaqeer-general-bucket.s3.amazonaws.com/na+(1).png"
                                        alt="Preview Image" style="height: 150px; width:150px;border-radius:20%">
                                @endif
                            </td>
                            <td>
                                {{ $visitor->confirmed_at }}
                            </td>
                            <td>{{ $visitor->print_count }}</td>
                            <td>{{ $visitor->msg_sent_status }}</td>
                            <td>{{ $visitor->msg_date }}</td>
                            <td>{{ $visitor->msg_sid }}</td>
                            <td>{{ $visitor->token_status }}</td>




                            <td>
                                @if ($visitor->token_status == 'invaild')
                                    <form method="post" action="{{ route('admin.filter.status', [$visitor->id]) }}">
                                        @csrf
                                        <input type="hidden" name="status" value="vaild">
                                        <button type="submit" class="btn btn-success">Vaild</button>
                                    </form>
                                @endif

                                @if ($visitor->token_status == 'vaild')
                                    <form method="post" action="{{ route('admin.filter.status', [$visitor->id]) }}">
                                        @csrf
                                        <input type="hidden" name="status" value="invaild">
                                        <button type="submit" class="btn btn-danger">Invaild</button>
                                    </form>
                                @endif

                            </td>
                        </tr>
                    @endforeach

                </tbody>

            </table>
        </div>
    </div>
@endsection
@section('page-script')
    <script>
        $(document).ready(function() {

            $('#tokenFilter thead th').each(function(i) {
                var title = $('#tokenFilter thead th')
                    .eq($(this).index())
                    .text();
                $(this).html(
                    '<input type="text" placeholder="' + title + '" data-index="' + i + '" />'
                );
            });



            var table = $('#tokenFilter').DataTable({
                "dom": 'lBfrtip',
                // scrollY: '300px',
                scrollX: true,
                "buttons": [{
                        extend: 'pdfHtml5',
                        orientation: 'landscape'
                    },
                    'csv',
                    'excel'
                ],
                "columnDefs": [{
                    "targets": [5], // index of the token_url_link column
                    "width": "500px" // set width to 500px
                }],
                "lengthMenu": [10, 25, 50, 100, 500, 1000, 1500, 2000, 2500, 3000],
                "pageLength": 500 // set default length to 1000
            });


            $(table.table().container()).on('keyup', 'thead input', function() {
                table
                    .column($(this).data('index'))
                    .search(this.value)
                    .draw();
            });

        })
    </script>
@endsection
