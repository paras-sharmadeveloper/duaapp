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
            <form method="POST" action="{{ route('your_route_name') }}">
                @csrf
                <table>
                    <thead>
                        <tr>
                            <!-- Select All Checkbox -->
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
                    <tbody>
                        @foreach ($visitorData as $visitor)
                            <tr>
                                <!-- Checkbox for individual row -->
                                <td>
                                    <input type="checkbox" name="visitor_ids[]" value="{{ $visitor['id'] }}">
                                </td>
                                <!-- Db Id -->
                                <td>{{ $visitor['id'] }}</td>
                                <!-- Date -->
                                <td>{{ $visitor['created_at'] }}</td>
                                <!-- Country Code -->
                                <td>{{ $visitor['country_code'] }}</td>
                                <!-- Phone Number -->
                                <td>{{ $visitor['phone_number'] }}</td>
                                <!-- User Image (Optional - Replace with actual image if you have a user image URL) -->
                                <td>
                                    @if ($visitor['user_image'])
                                        <img src="{{ asset('path_to_images/' . $visitor['user_image']) }}" alt="User Image" width="50">
                                    @else
                                        N/A
                                    @endif
                                </td>
                                <!-- Dua Type -->
                                <td>{{ $visitor['dua_type'] }}</td>
                                <!-- Instant Message -->
                                <td>{{ $visitor['msg_sid'] }}</td>
                                <!-- Last Dua Token -->
                                <td>{{ $visitor['recognized_code'] }}</td>
                                <!-- Repeat Visitor -->
                                <td>{{ $visitor['start_date'] }} - {{ $visitor['end_date'] }}</td>
                                <!-- Action (Example action buttons like Edit/Delete) -->
                                <td>
                                    <a href="{{ route('visitor.edit', ['id' => $visitor['id']]) }}">Edit</a> |
                                    <a href="{{ route('visitor.delete', ['id' => $visitor['id']]) }}" onclick="return confirm('Are you sure?')">Delete</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                <!-- Submit Button (Optional, if you want to take action on the selected rows) -->
                <button type="submit">Submit</button>
            </form>

            <!-- Pagination Links -->
            <div>
                @for ($i = 1; $i <= ceil($totalRecords / $perPage); $i++)
                    <a href="{{ route('your_route_name', ['filter_date' => $date, 'page' => $i]) }}">{{ $i }}</a>
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


$(document).ready(function() {
    var filterDate = $("#filter_date").val();
    var url = "{{ route('booking.manual.ajax') }}?filter_date=" + filterDate;

    $('#visitorTable').DataTable({
        processing: true,
        serverSide: true,
        deferRender: true, // Helps with large data sets
        pageLength: 50, // Set the default number of rows per page
        lengthMenu: [50, 100,200,500], // Options for page length (50 and 100 rows per page)
        ajax: {
            url: url,
            type: 'GET',
            dataSrc: function(json) {

                return json.data;
            }
        },
        columns: [
            {
                data: 'visitor_id',
                render: function(data, type, row) {
                    if(row.action_status){
                        return null
                    }else{
                        return '<input type="checkbox" class="bulk-checkbox" data-id="' + row.visitor_id + '">';
                    }

                }
            },
            {
                data: 'visitor_id',
                orderable: true
            },
            {
                data: 'created_at',  orderable: true
            },
            {
                data: 'country_code',  orderable: true
            },
            {
                data: 'phone',  orderable: true
            },
            {
                data: 'recognized_code',
                orderable: false ,
                render: function(data, type, row) {
                    if (data) {
                        const imgSrc = '/sessionImages/' + row.created_at + '/' + row.recognized_code;
                        return '<img class="lightgallery" src="' + imgSrc + '" />';
                    }
                }
            },
            {
                data: 'dua_type',  orderable: true
            },
            {
                data: 'msg_sid',  orderable: true
            },
            {
                data: 'last_visit',  orderable: true
            },
            {
                data: 'start_date',
                orderable: false,
                render: function(data, type, row) {

                        return (row.last_visit) ? '<button type="button" class="btn btn-warning ">Yes</button>' :  '';

                }
            },
            {
                data: 'action_at',
                render: function(data, type, row) {
                    if (data === null) {
                        return '<div class="actionBtns">' +
                            '<button type="button" class="btn btn-success approve" data-id="' + row.id + '"><span class="spinner-border spinner-border-sm" role="status" aria-hidden="true" style="display:none"></span><b>Approve (' + row.dua_type + ')</b></button>' +
                            '<button type="button" class="btn btn-danger disapprove" data-id="' + row.id + '"><span class="spinner-border spinner-border-sm" role="status" aria-hidden="true" style="display:none"></span><b>Disapprove (' + row.dua_type + ')</b></button>' +
                            '</div>';
                    } else {
                        return '<p>Action Taken: ' + (row.action_status ?
                            '<span class="btn ' + (row.action_status === 'approved' ?
                                'btn-success' : 'btn-danger') + ' btn-sm">' + row.action_status + '</span>' : '') + '</p>';
                    }
                },  orderable: false
            }
        ],
        rowCallback: function(row, data, index) {
            // Row callback for custom actions or adding event listeners
        },
        drawCallback: function(settings) {
            // Re-bind events on dynamically created elements after every redraw (ajax load)
            bindSelectAllCheckbox();
            // bindActionButtons();
        }
    });
});

function bindSelectAllCheckbox() {
        $('#selectAll').off('change').on('change', function() {
            $('.bulk-checkbox').prop('checked', $(this).prop('checked'));
        });
        $('.bulk-checkbox').off('change').on('change', function() {
            var totalCheckboxes = $('.bulk-checkbox').length;
            var checkedCheckboxes = $('.bulk-checkbox:checked').length;

            // If all checkboxes are selected, check the #selectAll checkbox
            if (totalCheckboxes === checkedCheckboxes) {
                $('#selectAll').prop('checked', true);
            } else {
                $('#selectAll').prop('checked', false);
            }
        });
    }
        $(document).on('click', '.approve', function() {
        var id = $(this).attr('data-id');
        AjaxCall(id, 'approve', $(this));
    });

    // Delegate the click event for .disapprove buttons
    $(document).on('click', '.disapprove', function() {
        var id = $(this).attr('data-id');
        AjaxCall(id, 'disapprove', $(this));
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
