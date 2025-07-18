@extends('layouts.app')
@section('content')
<style>
    .ag-input-field-input {
        color: #fff !important;
    }

    button.btn.btn-sm.btn-primary.show-logs {
        z-index: 99999999;
    }
</style>
<x-slot name="header">
    <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
        {{ __('Leads') }}
    </h2>
</x-slot>

<div class="py-12">
    <div class="max-w-10xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">

            <div class="p-6 text-gray-900 dark:text-gray-100">


                <div class="mt-1 d-flex justify-content-end mb-1 d-flex mx-3">

                    <button type="button" class="btn btn-info ml-6 mr-3 mt-3 mb-3 start-export"
                    data-loading="Exporting..." data-success="Started" data-default="Export">
                    <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"
                        style="display:none">
                    </span>
                    <b> Export</b>
                </button>  &nbsp;

                    {{-- <button class="btn btn-info ml-6 mr-3 mt-3 mb-3 start-export">Export </button>  --}}
                    <button onclick="deleteModel()" type="button" class="btn btn-danger ml-6 mr-3 mt-3 mb-3"> Delete Selected rows</button>
                </div>

                <!-- start Ag-Grid container -->
                <div id="ag-grid" class="ag-theme-alpine-dark" style="height:800px;"></div>
                <!-- end Ag-Grid container -->

            </div>
        </div>
    </div>
</div>

<!-- Confirmation Modal -->
<div id="del-model" class="modal fade" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Do you really want to Delete ?</h5>
                <button type="button" class="close" data-dismiss="modal">×</button>
            </div>
            <div class="modal-body">
                <p> These Selection will be deleted permantly. If you want to proceed with this Please write
                    <b>confirm delete</b> below and Proceed for deletion.
                </p>
                <input type="text" name="confirm_delete" class="form-control" placeholder="confirm delete"
                    id="confirm-delete">
                <span class="eror"></span>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" onclick="DeleteRows()">Delete Now</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('page-script')
<script>
    var licenseKey = "{{ env('AG_GRID_KEY') }}"


    const columnDefs = [
        {
            headerName: 'BookingId',
            field: 'booking_number',
            enableValue: true,
            enableRowGroup: true,
            floatingFilter: true,
            pivot: true,
            enablePivot: true,
            sort: 'desc',
            filter: "agNumberColumnFilter",
            checkboxSelection:true

        },
        {
            headerName: 'Booking Source',
            field: 'source',
            enableValue: true,
            enableRowGroup: true,
            floatingFilter: true,
            pivot: true,
            enablePivot: true,
            filter: "agTextColumnFilter",

        },

        {
            headerName: 'Venue Country',
            field: 'country_name',
            enableValue: true,
            enableRowGroup: true,
            floatingFilter: true,
            pivot: true,
            enablePivot: true,
            filter: "agTextColumnFilter"
        },

        {
            headerName: 'Venue Address',
            field: 'address',
            enableValue: true,
            enableRowGroup: true,
            floatingFilter: true,
            pivot: true,
            enablePivot: true,
            filter: "agTextColumnFilter"
        },

        {
            headerName: 'Venue Date',
            field: 'venue_date',
            enableValue: true,
            enableRowGroup: true,
            floatingFilter: true,
            pivot: true,
            enablePivot: true,
            filter: "agTextColumnFilter"
        },
        {
            headerName: 'Slot Time',
            field: 'slot_time',
            enableValue: true,
            enableRowGroup: true,
            floatingFilter: true,
            pivot: true,
            enablePivot: true,
            filter: "agTextColumnFilter"
        },
        {
            headerName: 'Sahib-e-Dua Name',
            field: 'name',
            enableValue: true,
            enableRowGroup: true,
            floatingFilter: true,
            pivot: true,
            enablePivot: true,
            filter: "agTextColumnFilter"
        },
        {
            headerName: 'Venue State',
            field: 'state',
            enableValue: true,
            enableRowGroup: true,
            floatingFilter: true,
            pivot: true,
            enablePivot: true,
            filter: "agTextColumnFilter"
        },
        {
            headerName: 'Venue City',
            field: 'city',
            enableValue: true,
            enableRowGroup: true,
            floatingFilter: true,
            pivot: true,
            enablePivot: true,
            filter: "agTextColumnFilter"
        },

        {
            headerName: 'FirstName',
            field: 'fname',
            enableValue: true,
            enableRowGroup: true,
            floatingFilter: true,
            pivot: true,
            enablePivot: true,
            filter: "agTextColumnFilter",

        },
        {
            headerName: 'Last Name',
            field: 'lname',
            enableValue: true,
            enableRowGroup: true,
            floatingFilter: true,
            pivot: true,
            enablePivot: true,
            filter: "agTextColumnFilter"
        },
        {
            headerName: 'Phone',
            field: 'phone',
            enableValue: true,
            enableRowGroup: true,
            floatingFilter: true,
            pivot: true,
            enablePivot: true,
            filter: "agTextColumnFilter"
        },

        {
            headerName: 'Email',
            field: 'email',
            enableValue: true,
            enableRowGroup: true,
            floatingFilter: true,
            pivot: true,
            enablePivot: true,
            filter: "agTextColumnFilter"
        },
        {
            headerName: 'countryCode',
            field: 'country_code',
            enableValue: true,
            enableRowGroup: true,
            floatingFilter: true,
            pivot: true,
            enablePivot: true,
            filter: "agTextColumnFilter"
        },

        {
            headerName: 'WhatsApp',
            field: 'is_whatsapp',
            enableValue: true,
            enableRowGroup: true,
            floatingFilter: true,
            pivot: true,
            enablePivot: true,
            filter: "agTextColumnFilter"
        },


        {
            headerName: 'User Ip',
            field: 'user_ip',
            enableValue: true,
            enableRowGroup: true,
            floatingFilter: true,
            pivot: true,
            enablePivot: true,
            filter: "agTextColumnFilter"
        },

        {
            headerName: 'Booking UniqueCode',
            field: 'booking_uniqueid',
            enableValue: true,
            enableRowGroup: true,
            floatingFilter: true,
            pivot: true,
            enablePivot: true,
            filter: "agTextColumnFilter"
        },


        {
            headerName: 'Meeting Type',
            field: 'meeting_type',
            enableValue: true,
            enableRowGroup: true,
            floatingFilter: true,
            pivot: true,
            enablePivot: true,
            filter: "agTextColumnFilter",

        },

        {
            headerName: 'Meeting StartAt',
            field: 'meeting_start_at',
            enableValue: true,
            enableRowGroup: true,
            floatingFilter: true,
            pivot: true,
            enablePivot: true,
            filter: "agTextColumnFilter"
        },
        {
            headerName: 'Meeting EndsAt',
            field: 'meeting_ends_at',
            enableValue: true,
            enableRowGroup: true,
            floatingFilter: true,
            pivot: true,
            enablePivot: true,
            filter: "agTextColumnFilter"
        },
        {
            headerName: 'Sms SentAt',
            field: 'sms_sent_at',
            enableValue: true,
            enableRowGroup: true,
            floatingFilter: true,
            pivot: true,
            enablePivot: true,
            filter: "agTextColumnFilter"
        },
        {
            headerName: 'Email SentAt',
            field: 'email_sent_at',
            enableValue: true,
            enableRowGroup: true,
            floatingFilter: true,
            pivot: true,
            enablePivot: true,
            filter: "agTextColumnFilter"
        },
        {
            headerName: 'Meeting ConfirmedAt',
            field: 'confirmed_at',
            enableValue: true,
            enableRowGroup: true,
            floatingFilter: true,
            pivot: true,
            enablePivot: true,
            filter: "agTextColumnFilter"
        },
        {
            headerName: 'BookingId',
            field: 'id',
            enableValue: true,
            enableRowGroup: true,
            floatingFilter: true,
            pivot: true,
            enablePivot: true,
            filter: "agTextColumnFilter"
        },


        {
            headerName: 'Meeting Total Time (In Seconds)',
            field: 'meeting_total_time',
            enableValue: true,
            enableRowGroup: true,
            floatingFilter: true,
            pivot: true,
            enablePivot: true,
            filter: "agTextColumnFilter"
        },

        {
            headerName: 'BookingFrom',
            field: 'user_question',
            enableValue: true,
            enableRowGroup: true,
            floatingFilter: true,
            pivot: true,
            enablePivot: true,
            filter: "agTextColumnFilter"
        },
        // Add more columns as needed
    ];

    // Define grid options
    const gridOptions = {
        pagination: true,
        paginationPageSize: 100,
        enableRangeSelection: true,
        rowSelection: 'multiple',
        rowMultiSelectWithClick: true,
        columnDefs: columnDefs,
        pagination: false, // Enable pagination
        paginationPageSize: 200, // Number of rows per page,
        defaultColDef: {
            flex: 1,
            minWidth: 200,
            resizable: true,
            sortable: true,
            enablePivot: true,
            allowedAggFuncs: ["count", "sum", "min", "max", "avg"],
            filterParams: {
                newRowsAction: "keep",
                browserDatePicker: true,
                // caseSensitive:true
            }
        },
        rowModelType: 'serverSide',
        serverSideStoreType: 'partial',
        rowGroupPanelShow: 'always',
        pivotPanelShow: "always",
        animateRows: true,
        sideBar: true,
        suppressAggFuncInHeader: true,
        maxConcurrentDatasourceRequests: 1,
        cacheBlockSize: 100,
        maxBlocksInCache: 2,
        purgeClosedRowNodes: true,
        onFirstDataRendered: onFirstDataRendered,
        // Add other grid options as needed
    };
    document.addEventListener('DOMContentLoaded', function() {
        agGrid.LicenseManager.setLicenseKey(licenseKey);
        const gridDiv = document.querySelector('#ag-grid');
        new agGrid.Grid(gridDiv, gridOptions);
        getServerSideDatasourceOnPage(gridOptions);

    });

    function onFirstDataRendered(params) {
        params.api.sizeColumnsToFit();
    }

    function getServerSideDatasourceOnPage(gridOptions) {

        const datasource = {
            getRows(params) {
                $.ajax({
                    type: 'POST',
                    data: JSON.stringify(params.request),
                    dataType: "json",
                    contentType: "application/json",
                    url: "{{ route('fetch.bookings') }}",
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        if (response) {
                            params.success({
                                rowData: response.rows,
                                rowCount: response.totalRows,
                            });
                            $("#lead-count").text(response.totalRows);
                            if (response.FilteredRow) {
                                $("#lead-count").text(response.FilteredRow);
                            }
                        } else {
                            params.fail();
                        }

                    },
                    error: function(e) {
                        params.fail();
                    },
                });

            }
        };

        // console.log("datasource",datasource)
        gridOptions.api.setServerSideDatasource(datasource);
    }

    function OnBtnRefresh() {
        gridOptions.api.refreshServerSideStore({
            purge: true
        });

    }

    function OnBtnRefresh() {
        gridOptions.api.refreshServerSideStore({
            purge: true
        });
    }

    function OnResetFilters() {
        gridOptions.api.setFilterModel(null);
        gridOptions.api.onFilterChanged();
    }

    function OnResetState() {
        gridOptions.columnApi.resetColumnState(null);
        gridOptions.columnApi.setPivotMode();
    }





    function onFirstDataRendered(params) {
        params.api.sizeColumnsToFit();
    }

    function getServerSideDatasource(gridOptions, table) {

        const datasource = {
            getRows(params) {
                $.ajax({
                    type: 'POST',
                    data: JSON.stringify(params.request),
                    dataType: "json",
                    contentType: "application/json",
                    url: gridUrl,
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        if (response) {
                            params.success({
                                rowData: response.rows,
                                rowCount: response.lastRow,
                            });
                            $("#lead-count").text(response.totalRows);
                            if (response.FilteredRow) {
                                $("#lead-count").text(response.FilteredRow);
                            }
                        } else {
                            params.fail();
                        }

                    },
                    error: function(e) {
                        params.fail();
                    },
                });




            }
        };

        // console.log("datasource",datasource)
        gridOptions.api.setServerSideDatasource(datasource);
    }

    function deleteModel() {
      var selected = gridOptions.api.getSelectedRows();
    if(selected.length > 0){

    $('#del-model').modal('show');
    }else{
        alert('Please select row to delete');
    }

  }

  function  DeleteRows() {
    var table = 'vistors';
    var confirm = $("#confirm-delete").val();

    if(confirm == ''){
        $(".eror").text('Please write confirm delete');
        return false;
    }

    if(confirm === 'confirm delete'){

      var selected = gridOptions.api.getSelectedRows();
    //   console.log("selected",selected)
      var ids = [];
      for (var i = 0; i < selected.length ; i++ ) {
        ids[i] = selected[i]['id'];

      }

       $.ajax({
         type:'get',
         dataType: "json",
         url:"{{ route('delete-row') }}"+'?table_name='+table,
         data: { idsToDelete : ids },

         headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
         success:function(response){
             alert("Row Deleted Successfully")
             location.reload()
            // toastr.success('Row Deleted', "Success");
            $('#del-model').modal('hide');
          },
          error: function(e) {
            // toastr.error('failed', "falied to load");
            $('#del-model').modal('hide');
          },
     });

    }else{
        $(".eror").text('write correctly.. spelling mistake');
        return false;
    }

  }

  $(".start-export").click(function(){
        $this = $(this);

        let UserEmail = prompt('Please enter email address you want to sent email ?');
        if(UserEmail){
            console.log("UserEmail",UserEmail)
            let getFilterModel = gridOptions.api.getFilterModel();
            let getFilterModel2 = gridOptions.columnApi.getColumnState();
            const data = { filterModel: getFilterModel };
            exportData( $this , data , UserEmail);
        }else{
            alert("You have to enter email to start export");
            return false;
        }



   });

  function exportData(btnClick , data ,userEmail){
      var loadingText = btnClick.attr('data-loading');
        var successText = btnClick.attr('data-success');
        var defaultText = btnClick.attr('data-default');

        btnClick.find('span').show()
        btnClick.find('b').text(loadingText)
    $.ajax({
        type:'POST',
        data:JSON.stringify(data),
        dataType: "json",
        contentType: "application/json",
        url:"{{ route('fetch.bookings') }}"+'?export=1&userEmail='+userEmail,
        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
        success:function(response){

            if(response.success){
                btnClick.find('b').text(successText)

                setTimeout(() => { btnClick.find('b').text(defaultText)   }, 2000);
                btnClick.find('span').hide()
            }else{
                alert(error)

            }


        },error: function(e) {


        },
    });
    }


</script>
@endsection
