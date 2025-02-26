@extends('layouts.app')

@section('content')
<style>
    /* Existing CSS */
    .popup {
        margin: auto;
        text-align: center;
    }

    .popup img {
        width: 100px;
        height: 100px;
        cursor: pointer;
    }

    .show {
        z-index: 999;
        display: none;
    }

    .actionBtns {
        display: flex;
        justify-content: space-between;
        gap: 30px;
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
        overflow: hidden;
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

    .action-pagination {
        text-align: center;
        margin: 20px;
        padding: 10px;
    }

    .filteraction {
        text-align: end;
        cursor: pointer;
    }

    /* Responsive Table CSS */
    .table-responsive {
        overflow-x: auto;
        -webkit-overflow-scrolling: touch; /* Smooth scrolling on iOS */
    }

    .table-custom {
        width: 100%;
        border-collapse: collapse;
    }

    .table-custom th,
    .table-custom td {
        padding: 8px;
        text-align: left;
        border: 1px solid #ddd;
    }

    .table-custom th {
        background-color: #f2f2f2;
    }

    @media (max-width: 767px) {
        .bulk-app-dis {
            display: flex;
            justify-content: space-between;
            gap: 20px;
        }

        .table-custom {
            display: block;
            overflow-x: auto;
            -webkit-overflow-scrolling: touch; /* Smooth scrolling on iOS */
        }

        .table-custom thead,
        .table-custom tbody,
        .table-custom th,
        .table-custom td,
        .table-custom tr {
            display: block;
        }

        .table-custom thead tr {
            position: absolute;
            top: -9999px;
            left: -9999px;
        }

        .table-custom tr {
            border: 1px solid #ccc;
        }

        .table-custom td {
            border: none;
            border-bottom: 1px solid #eee;
            position: relative;
            padding-left: 50%;
        }

        .table-custom td:before {
            position: absolute;
            top: 6px;
            left: 6px;
            width: 45%;
            padding-right: 10px;
            white-space: nowrap;
            content: attr(data-label);
        }
    }

    .expanded-header {
  width: 250px;
}
</style>

    <div class="card">
        @include('alerts')


        <div class="card-body">

            <h5 class="card-title">Manual List for Visitor </h5>

            <div class="card ">
                <div class="card-body">
                    <div class="text-center mt-4 bulk-app-dis">


                        <button id="bulkApproveBtn" type="button" class="btn btn-success " data-loading="Loading..."
                            data-success="Done" data-default="Approve">
                            <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"
                                style="display:none">
                            </span>
                            <b>Bulk Approve</b>
                        </button>

                        <button type="button" id="bulkDisapproveBtn" class="btn  btn-danger" data-loading="Loading..."
                            data-success="Done" data-default="Disapprove">
                            <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"
                                style="display:none">
                            </span>
                            <b>Bulk Disapprove</b>
                        </button>
                    </div>
                    <form action="{{ route('booking.manual.list.new') }}" class="mt-3" method="get">
                        <div class="row">
                            <div class="col-md-4">
                                <label> Filter Date </label>
                                <input class="form-control" type="date" name="filter_date" id="filter_date"
                                    value="{{ request()->get('filter_date') ? request()->get('filter_date') : '' }}">
                            </div>
                            <div class="col-md-4">
                                <label> Pagination Date </label>
                                <select class="form-control" name="pagination">
                                    <option @if (request()->get('pagination') == '50') ? selected : '' @endif value="50"> 50
                                    </option>
                                    <option @if (request()->get('pagination') == '100') ? selected : '' @endif value="100"> 100
                                    </option>
                                    <option @if (request()->get('pagination') == '200') ? selected : '' @endif value="200"> 200
                                    </option>
                                    <option @if (request()->get('pagination') == '500') ? selected : '' @endif value="500"> 500
                                    </option>
                                </select>

                            </div>
                            <div class="col-md-4">
                                <label> Search by Phone </label>
                                <input type="text" class="form-control" name="search_phone" placeholder="Search by Phone"
                                    value="{{ request('search_phone') }}">
                            </div>
                        </div>
                        <div class="row mt-2">

                            <div class="col-md-4">
                                <label> Search Action Type</label>
                                <select class="form-control" name="action_type">
                                    <option @if (request()->get('action_type') == 'all') ? selected : '' @endif value="all"> All
                                    </option>
                                    <option @if (request()->get('action_type') == 'approved') ? selected : '' @endif value="approved">
                                        Approved
                                    </option>
                                    <option @if (request()->get('action_type') == 'disapproved') ? selected : '' @endif value="disapproved">
                                        Disapproved
                                    </option>
                                    <option @if (request()->get('action_type') == 'warning') ? selected : '' @endif value="warning">
                                        Warning
                                    </option>
                                    <option @if (request()->get('action_type') == 'pending') ? selected : '' @endif value="pending">
                                        Pending
                                    </option>
                                    {{-- <option @if (request()->get('action_type') == 'working_lady_dua') ? selected : '' @endif value="working_lady_dua"> Working Lady Dua
                                    </option> --}}

                                </select>

                            </div>
                            <div class="col-md-4">
                                <label>Search by Dua Type </label>

                                <select class="form-control" name="dua_type">
                                    <option @if (request()->get('dua_type') == 'all') ? selected : '' @endif value="all"> All
                                    </option>
                                    <option @if (request()->get('dua_type') == 'dua') ? selected : '' @endif value="dua"> Dua
                                    </option>
                                    <option @if (request()->get('dua_type') == 'dum') ? selected : '' @endif value="dum"> Dum
                                    </option>
                                    <option @if (request()->get('dua_type') == 'working_lady_dua') ? selected : '' @endif
                                        value="working_lady_dua"> Working Lady Dua
                                    </option>

                                </select>
                            </div>

                            <div class="col-md-4">
                                <label>Search anything </label>
                                <input type="text" class="form-control" name="serach_all" placeholder="Search anything"
                                    value="{{ request('serach_all') }}">
                            </div>
                        </div>
                        <input type="hidden" value="page" value="{{ request('page', 1) }}">
                        <div class="filteraction">
                            <button class="btn btn-dark mt-4" style="" type="submit">Filter </button>
                            <a href="{{ route('booking.manual.list.new', ['filter_date' => $date]) }}"
                                class="btn btn-secondary mt-4" style="">Reset</a>

                        </div>


                    </form>
                </div>


            </div>
            <form method="POST" action="{{ route('booking.manual.list.new') }}" class="custom-serach-table">

                @csrf
                <div class="table-responsive">
                <table class="table table-responsive cell-border table-custom">
                    <thead>
                        <tr>
                            <th>
                                <input type="checkbox" id="selectAll">
                            </th>
                            <th>Db Id</th>
                            <th class="expanded-header">Date</th>
                            <th>CountryCode</th>
                            <th>Phone</th>
                            <th>Dua Type</th>
                            <th class="expanded-header">Last Dua Token</th>
                            <th>User Image</th>
                            {{-- <th>Repeat Visitor</th> --}}
                            <th>Action</th>
                            <th>Message Sid</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($visitorData as $visitor)
                            @php
                             $filterDate =  \Carbon\Carbon::parse(request()->query('filter_date', date('d-m-Y')))->format('d-m-Y') ;
                                $loclpath = '/sessionImages/' . $filterDate. '/';
                            @endphp
                            @php
                                $localImage = '';
                                $localImageStroage =
                                    'sessionImages/' . $filterDate . '/' . !empty($visitor['recognized_code'])
                                        ? $visitor['recognized_code']
                                        : '';
                                if (
                                    !empty($visitor['recognized_code']) &&
                                    !Storage::disk('public_uploads')->exists($localImageStroage)
                                ) {
                                    $localImage = !empty($visitor['recognized_code'])
                                        ? $visitor['recognized_code']
                                        : '';
                                }

                            @endphp
                            <tr>
                                <!-- Checkbox for individual row -->
                                <td>
                                    @if (empty($visitor['action_at']))
                                        <input type="checkbox" class="bulk-checkbox" data-id="{{ $visitor['id'] }}">
                                    @endif
                                </td>
                                <!-- Db Id -->
                                <td>{{ $visitor['id'] }}</td>
                                <!-- Date -->
                                <td>{{ \Carbon\Carbon::parse($visitor['created_at'])->format('Y-m-d g:i:s A') }}
                                </td>
                                <!-- Country Code -->
                                <td>{{ $visitor['country_code'] }}</td>
                                <!-- Phone Number -->
                                <td>{{ $visitor['phone_number'] }}</td>
                                <td>{{ $visitor['dua_type'] }}</td>
                                <!-- Instant Message -->

                                <!-- Last Dua Token -->
                                <td>{{ \Carbon\Carbon::parse($visitor['last_visit'])->format('j M Y') }}
                                </td>
                                <!-- User Image (Optional - Replace with actual image if you have a user image URL) -->
                                <td>
                                    <img class="lightgallery" src="{{ $loclpath . $localImageStroage }}" />
                                </td>
                                <!-- Dua Type -->

                                <!-- Repeat Visitor -->

                                {{-- <td>
                                    @if ($visitor['last_visit'])
                                        <button type="button" class="btn btn-warning ">Yes</button>
                                    @endif
                                </td> --}}
                                <!-- Action (Example action buttons like Edit/Delete) -->
                                <td>
                                    @if (empty($visitor['action_at']))
                                        <div class="row py-4 actionBtns">

                                            <button type="button" class="btn btn-success approve mb-3"
                                                data-id="{{ $visitor['id'] }}" data-loading="Loading..."
                                                data-success="Done" data-default="Approve">
                                                <span class="spinner-border spinner-border-sm" role="status"
                                                    aria-hidden="true" style="display:none">
                                                </span>
                                                <b>Approve ({{ ucwords($visitor['dua_type']) }})</b>
                                            </button>

                                            <button type="button"
                                                class="btn btn-secondary undo undobtn-{{ $visitor['id'] }} mb-3"
                                                style="display: none" data-id="{{ $visitor['id'] }}"
                                                data-loading="Loading..." data-success="Done" data-default="Undo">
                                                <span class="spinner-border spinner-border-sm" role="status"
                                                    aria-hidden="true" style="display:none">
                                                </span>
                                                <b>Undo
                                                    ({{ $visitor['dua_type'] == 'working_lady_dua' ? 'wl dua' : ucwords($visitor['dua_type']) }})
                                                </b>
                                            </button>
                                            <button type="button" class="btn  btn-warning warning"
                                                data-id="{{ $visitor['id'] }}" data-loading="Loading..."
                                                data-success="Done" data-default="Warning">
                                                <span class="spinner-border spinner-border-sm" role="status"
                                                    aria-hidden="true" style="display:none">
                                                </span>
                                                <b>Warning ({{ ucwords($visitor['dua_type']) }})</b>
                                            </button>

                                            <button type="button" class="btn  btn-danger disapprove"
                                                data-id="{{ $visitor['id'] }}" data-loading="Loading..."
                                                data-success="Done" data-default="Disapprove">
                                                <span class="spinner-border spinner-border-sm" role="status"
                                                    aria-hidden="true" style="display:none">
                                                </span>
                                                <b>Disapprove ({{ ucwords($visitor['dua_type']) }})</b>
                                            </button>
                                        </div>
                                    @elseif (!empty($visitor['action_at']) && $visitor['action_status'] == 'approved')
                                        <span
                                            class="badge badge-success text-success">{{ $visitor['action_status'] }}</span><br>
                                        <button type="button" class="btn btn-secondary undo mb-3"
                                            data-id="{{ $visitor['id'] }}" data-loading="Loading..." data-success="Done"
                                            data-default="Undo">
                                            <span class="spinner-border spinner-border-sm" role="status"
                                                aria-hidden="true" style="display:none">
                                            </span>
                                            <b>Undo
                                                ({{ $visitor['dua_type'] == 'working_lady_dua' ? 'wl dua' : ucwords($visitor['dua_type']) }})</b>
                                        </button>
                                    @else
                                        <p>
                                            @if ($visitor['action_status'])
                                                <span
                                                    class="{{ $visitor['action_status'] == 'approved' ? 'btn btn-success btn-sm' : 'btn btn-danger btn-sm' }}">{{ $visitor['action_status'] }}
                                                </span>
                                            @endif
                                        </p>
                                    @endif
                                </td>
                                <td>{{ $visitor['msg_sid'] }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                </div>

            </form>

            <!-- Pagination Links -->

            <div class="action-pagination">
                @for ($i = 1; $i <= ceil($totalRecords / $perPage); $i++)
                    <a href="{{ route('booking.manual.list.new', [
                        'filter_date' => $date,
                        'page' => $i,
                        'pagination' => request()->get('pagination'),
                        'search_phone' => request()->get('search_phone'),
                        'search_country_code' => request()->get('search_country_code'),
                        'dua_type' => request()->get('dua_type'),
                        'action_type' => request()->get('action_type'),
                        'serach_all' => request()->get('serach_all'),
                    ]) }}"
                        @if (request()->get('page') == $i) class="btn btn-primary"
                        @else
                             class="btn btn-dark" @endif>{{ $i }}</a>
                @endfor
            </div>

            <!-- Select All Checkbox Script (for handling the Select All functionality) -->
            <script>
                // Handle "Select All" checkbox click
                document.getElementById('selectAll').addEventListener('click', function() {
                    var checkboxes = document.querySelectorAll('input[name="visitor_ids[]"]');
                    checkboxes.forEach(function(checkbox) {
                        checkbox.checked = this.checked;
                    }, this);
                });
            </script>

        </div>


    </div>
@endsection

@section('page-script')
    <script>
        $(document).on('click', '.approve', function() {
            var id = $(this).attr('data-id');
            AjaxCall(id, 'approve', $(this));

        });

        // Delegate the click event for .disapprove buttons
        $(document).on('click', '.disapprove', function() {
            var id = $(this).attr('data-id');
            AjaxCall(id, 'disapprove', $(this));
        });

        $(document).on('click', '.undo', function() {
            if (confirm("Are you sure you want to undo this?")) {
                var id = $(this).attr('data-id');
                AjaxCall(id, 'undo', $(this));
            }

        });
        $(document).on('click', '.warning', function() {
            if (confirm("Are you sure you want to send warning to this person?")) {
                var id = $(this).attr('data-id');
                AjaxCall(id, 'warning', $(this));
            }

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
                    if (type == 'approve') {
                        $(".undobtn-" + id).fadeIn();
                    }
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

        $(document).ready(function() {
            $('#selectAll').on('change', function() {
                $('.bulk-checkbox').prop('checked', $(this).prop('checked'));
            });
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
        $('#bulkApproveBtn').on('click', function(e) {
            e.preventDefault();

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
