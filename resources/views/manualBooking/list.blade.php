@extends('layouts.app')

@section('content')
    <style>
        .popup {
            margin: auto;
            text-align: center
        }

        .popup img {
            width: 100px;
            height: 100px;
            cursor: pointer
        }

        .show {
            z-index: 999;
            display: none;
        }

        .show .overlayMy {
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, .66);
            position: absolute;
            top: 0;
            left: 0;
        }

        .show .img-show {
            width: 600px;
            height: 400px;
            background: #FFF;
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            overflow: hidden
        }

        .img-show span {
            position: absolute;
            top: 10px;
            right: 10px;
            z-index: 99;
            cursor: pointer;
        }

        .img-show img {
            width: 100%;
            height: 100%;
            position: absolute;
            top: 0;
            left: 0;
        }

        .img-show span {
            color: red;
        }

        td.imgc {
            text-align: center;
        }

        img.lightgallery {
            height: 150px;
            width: auto;
            text-align: center;
        }

        /*End style*/
    </style>

    <div class="card">
        @include('alerts')


        <div class="card-body">

            <h5 class="card-title">Manual List for Visitor </h5>
            <div class="text-center mt-4">


                <button id="bulkApproveBtn" type="button" class="btn btn-success " data-loading="Loading..." data-success="Done"
                    data-default="Approve">
                    <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true" style="display:none">
                    </span>
                    <b>Bulk Approve</b>
                </button>

                <button type="button" id="bulkDisapproveBtn" class="btn  btn-danger" data-loading="Loading..."
                    data-success="Done" data-default="Disapprove">
                    <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true" style="display:none">
                    </span>
                    <b>Bulk Disapprove</b>
                </button>
            </div>
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('booking.manual.list') }}" method="get">
                        <div class="row">
                            <div class="col-md-4">
                                <label> Filter Date </label>
                                <input class="form-control" type="date" name="filter_date" id="filter_date"
                                    value="{{ request()->get('filter_date') ? request()->get('filter_date') : '' }}">
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
            <table id="visitorTable" class="table-with-button1s table table-responsive cell-border">
                <thead>
                    <tr>
                        <th>
                            <input type="checkbox" id="selectAll">
                        </th>
                        <th>Db Id</th>
                        <th>Date</th>
                        <th>CountryCode</th>
                        <th>Phone</th>
                        <th>User Image</th>
                        <th>Dua Type</th>
                        <th>Instant Message</th>
                        <th>Last Dua Token</th>
                        <th>Repeat Visitor</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>

            {{-- <table class="table-with-buttons table table-responsive cell-border">
                <thead>
                    <tr>
                        <th>
                            <input type="checkbox" id="selectAll">
                        </th>
                        <th>Db Id</th>
                        <th>Date</th>
                        <th>CountryCode</th>
                        <th>phone </th>
                        <th>User Image </th>
                        <th>Dua Type</th>
                        <th>instant Message</th>
                        <!-- <th>Last Dua Token</th>
                        <th>Repeat Visitor</th> -->
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>

                    @foreach ($visitorList as $list)
                        @php
                            $repeat_visitor_days = $list->venueAddress->repeat_visitor_days;
                            $loclpath = '/sessionImages/' . date('d-m-Y') . '/';

                        @endphp
                        @php
                            $localImage = '';
                            $localImageStroage =
                                'sessionImages/' . date('d-m-Y') . '/' . !empty($list->recognized_code)
                                    ? $list->recognized_code
                                    : '';
                            if (
                                !empty($list->recognized_code) &&
                                !Storage::disk('public_uploads')->exists($localImageStroage)
                            ) {
                                $localImage = !empty($list->recognized_code) ? $list->recognized_code : '';
                            }

                        @endphp
                        <tr>
                            <td>
                                @if (empty($list->action_at))
                                    <input type="checkbox" class="bulk-checkbox" data-id="{{ $list->id }}">
                                @endif
                            </td>
                            <td>{{ $list->id }}</td>
                            <td>{{ $list->created_at->format('d M Y H:i:s A') }}</td>
                            <td>{{ $list->country_code }}</td>
                            <td>{{ $list->phone }}  </td>

                            <td class="imgc">
                                 <img class="lightgallery" src="{{ $loclpath . $localImageStroage }}" />

                            </td>
                            <td>{{ ucwords($list->dua_type) }}</td>
                            <td>{{ $list->msg_sid .'/' . $list->msg_sent_status}}</td>

                            <td>
                                @if (empty($list->action_at))
                                    <div class="row py-4 actionBtns">

                                        <button type="button" class="btn btn-success approve mb-3"
                                            data-id="{{ $list->id }}" data-loading="Loading..." data-success="Done"
                                            data-default="Approve">
                                            <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"
                                                style="display:none">
                                            </span>
                                            <b>Approve ({{ ucwords($list->dua_type) }})</b>
                                        </button>

                                        <button type="button" class="btn  btn-danger disapprove"
                                            data-id="{{ $list->id }}" data-loading="Loading..." data-success="Done"
                                            data-default="Disapprove">
                                            <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"
                                                style="display:none">
                                            </span>
                                            <b>Disapprove ({{ ucwords($list->dua_type) }})</b>
                                        </button>
                                    </div>
                                @else
                                    <p> Action Taken
                                        @if ($list->action_status)
                                        <span class="{{ ($list->action_status == 'approved')? 'btn btn-success btn-sm':'btn btn-danger btn-sm' }}">{{ $list->action_status}} </span>
                                        @endif
                                    </p>

                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table> --}}
        </div>


    </div>

    {{-- <div class="show">
        <div class="overlayMy"></div>
        <div class="img-show">
            <span><i class="fa fa-times"></i></span>
            <img src="">
        </div>
    </div> --}}
@endsection

@section('page-script')
    <script>
$(document).ready(function() {
    var filterDate = $("#filter_date").val();
    var url = "{{ route('booking.manual.ajax') }}?filter_date=" + filterDate;

    $('#visitorTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: url,
            type: 'GET',
            dataSrc: function(json) {
                console.log(json); // Check the data structure returned
                return json.data;
            }
        },
        columns: [
            { data: 'visitor_id' },
            { data: 'phone_number' },
            { data: 'total_visits' },
            { data: 'last_visit' },
            { data: 'start_date' },
            { data: 'end_date' },
            { data: 'country_code' },
            { data: 'phone' },
            { data: 'recognized_code' },
            { data: 'dua_type' },
            { data: 'msg_sid' },
            { data: 'action_at' }
        ],
        order: [[1, 'asc']]
    });
});



        // var filterDate = $("#filter_date").val();
        // var url = "{{route('booking.manual.ajax')}}?filter_date="+filterDate
        // $(document).ready(function() {
        //     $('#visitorTable').DataTable({
        //         processing: true, // Show processing indicator
        //         serverSide: true, // Enable server-side processing
        //         ajax: {
        //             url: url, // Server-side endpoint
        //             type: 'GET',
        //             dataSrc: function(json) {
        //                 return json.data; // Ensure your response has 'data' key
        //             }
        //         },
        //         columns: [{
        //                 data: 'id',
        //                 render: function(data, type, row) {
        //                     if (row.action_at === null) {
        //                         return '<input type="checkbox" class="bulk-checkbox" data-id="' +
        //                             row.id + '">';
        //                     }
        //                     return '';
        //                 }
        //             },
        //             {
        //                 data: 'id',
        //                 render: function(data) {
        //                     return data.visitorList.id; // Format date
        //                 }
        //             },
        //             {
        //                 data: 'created_at',
        //                 render: function(data) {
        //                     return new Date(data.visitorList.created_at).toLocaleString(); // Format date
        //                 }
        //             },
        //             {
        //                 data: 'country_code',
        //                 render: function(data) {
        //                     return data.visitorList.country_code; // Format date
        //                 }
        //             },
        //             {
        //                 data: 'phone',
        //                 render: function(data) {
        //                     return data.visitorList.phone; // Format date
        //                 }
        //             },
        //             {
        //                 data: 'recognized_code',
        //                 render: function(data, type, row) {
        //                     const imgSrc = '/sessionImages/' + new Date().toLocaleDateString() +
        //                         '/' + data;
        //                     return '<img class="lightgallery" src="' + imgSrc + '" />';
        //                 }
        //             },
        //             {
        //                 data: 'dua_type',
        //                 render: function(data) {
        //                     return data.charAt(0).toUpperCase() + data.slice(1);
        //                 }
        //             },
        //             {
        //                 data: 'msg_sid'
        //             },
        //             {
        //                 data: 'action_at',
        //                 render: function(data, type, row) {
        //                     if (!data) {
        //                         return '<div class="row py-4 actionBtns">' +
        //                             '<button type="button" class="btn btn-success approve" data-id="' +
        //                             row.id + '"><b>Approve (' + row.dua_type + ')</b></button>' +
        //                             '<button type="button" class="btn btn-danger disapprove" data-id="' +
        //                             row.id + '"><b>Disapprove (' + row.dua_type + ')</b></button>' +
        //                             '</div>';
        //                     } else {
        //                         return '<p>Action Taken: ' + (row.action_status ?
        //                             '<span class="btn ' + (row.action_status === 'approved' ?
        //                                 'btn-success' : 'btn-danger') + ' btn-sm">' + row
        //                             .action_status + '</span>' : '') + '</p>';
        //                     }
        //                 }
        //             }
        //         ],
        //         rowCallback: function(row, data, index) {
        //             // Row callback for custom actions or adding event listeners, if needed
        //         }
        //     });
        // });



        $(".approve").click(function() {
            var id = $(this).attr('data-id');
            AjaxCall(id, 'approve', $(this))
        });

        $(".disapprove").click(function() {
            var id = $(this).attr('data-id');
            AjaxCall(id, 'disapprove', $(this))
        });




        function AjaxCall(id, type, event) {

            var loadingText = event.attr('data-loading');
            var successText = event.attr('data-success');
            var defaultText = event.attr('data-default');

            event.find('span').show()
            event.find('b').text(loadingText)


            $.ajax({
                url: "{{ route('booking.manual.approve') }}",
                method: 'POST',
                data: {
                    id: id,
                    type: type,
                    "_token": "{{ csrf_token() }}"
                },
                success: function(response) {

                    event.find('span').hide()
                    event.find('b').text(defaultText);
                    event.parents('.actionBtns').fadeOut();
                    //    event.parents('tr').fadeOut();
                    if (response.status) {
                        toastr.success(response.message)
                    } else {
                        toastr.error(response.message)
                    }

                },
                error: function(xhr, status, error) {
                    event.find('span').hide()
                    event.find('b').text(defaultText)
                    console.error(error);
                    toastr.error(error)
                }
            });
        }

        $('#selectAll').on('change', function() {
            $('.bulk-checkbox').prop('checked', $(this).prop('checked'));
        });



        function AjaxCallBulk(id, type, event) {

            var loadingText = event.attr('data-loading');
            var successText = event.attr('data-success');
            var defaultText = event.attr('data-default');

            event.find('span').show()
            event.find('b').text(loadingText)


            $.ajax({
                url: "{{ route('booking.manual.approve.bulk') }}",
                method: 'POST',
                data: {
                    ids: id,
                    type: type,
                    "_token": "{{ csrf_token() }}"
                },
                success: function(response) {

                    event.find('span').hide()
                    event.find('b').text(defaultText)

                    event.parents('.actionBtns').fadeOut();

                    //    event.parents('tr').fadeOut();


                    if (response.status) {
                        toastr.success(response.message)
                    } else {
                        toastr.error(response.message)
                    }

                },
                error: function(xhr, status, error) {
                    event.find('span').hide()
                    event.find('b').text(defaultText)
                    console.error(error);
                    toastr.error(error)
                }
            });
        }



        // Bulk Approve button click handler
        $('#bulkApproveBtn').on('click', function() {


            var selectedIds = $('.bulk-checkbox:checked').map(function() {
                return $(this).data('id');
            }).get();
            if (selectedIds.length > 0) {
                if (confirm('Do you really want to continue ?')) {
                    AjaxCallBulk(selectedIds, 'approve', $(this));
                }
            } else {
                alert("Select Checkbox")
            }

        });

        // Bulk Disapprove button click handler
        $('#bulkDisapproveBtn').on('click', function() {

            var selectedIds = $('.bulk-checkbox:checked').map(function() {
                return $(this).data('id');
            }).get();
            if (selectedIds.length > 0) {
                if (confirm('Do you really want to continue ?')) {
                    AjaxCallBulk(selectedIds, 'disapprove', $(this));
                }
            } else {
                alert("Select Checkbox")
            }


        });
    </script>
@endsection
