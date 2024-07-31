<!-- ======= Footer ======= -->
<footer id="footer" class="footer" style="border: none">
    <!-- <div class="copyright">
      &copy; Copyright <strong><span>NiceAdmin</span></strong>. All Rights Reserved
    </div> -->
    <div class="credits">
    </div>
</footer><!-- End Footer -->

{{-- <a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a> --}}

<!-- Vendor JS Files -->
<script src="{{ asset('assets/theme/vendor/apexcharts/apexcharts.min.js') }}"></script>
<script src="{{ asset('assets/theme/vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
<script src="{{ asset('assets/theme/vendor/chart.js/chart.umd.js') }}"></script>
<script src="{{ asset('assets/theme/vendor/echarts/echarts.min.js') }}"></script>
<script src="{{ asset('assets/theme/vendor/quill/quill.min.js') }}"></script>
<script src="{{ asset('assets/theme/vendor/tinymce/tinymce.min.js') }}"></script>
<script src="{{ asset('assets/theme/vendor/php-email-form/validate.js') }}"></script>

<!-- Template Main JS File -->
<script src="{{ asset('assets/theme/js/main.js?ver=') . time() }}"></script>





<script src="https://cdn.jsdelivr.net/npm/@yaireo/tagify@3.1.0/dist/tagify.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/jquery@3.4.1/dist/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/js/select2.min.js"></script>
{{-- DataTable Handling --}}

<!-- Include DataTables CSS and JS -->
<script type="text/javascript" src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.js"></script>

<script type="text/javascript" src="https://cdn.datatables.net/buttons/2.2.2/js/dataTables.buttons.min.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/buttons/2.2.2/js/buttons.html5.min.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.7.1/jszip.min.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.72/pdfmake.min.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.72/vfs_fonts.js"></script>



{{-- DataTable Handling End --}}

{{-- <script src="https://cdn.jsdelivr.net/npm/ag-grid-enterprise@27.1.0/dist/ag-grid-enterprise.min.js"></script> --}}

<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>


@yield('page-script')
{{-- <script src="https://js.pusher.com/8.2.0/pusher.min.js"></script> --}}

<script type="text/javascript">
    // let pusherKey = "{{ env('PUSHER_JS_KEY') }}";
    // let pusherKeyCluster = "{{ env('PUSHER_JS_CLUSTER') }}";
    $(document).ready(function() {
        $("#country_id").select2({
            placeholder: "Select country",
            allowClear: true
        });

        $("#citsy").select2({
            placeholder: "Select City",
            allowClear: true
        });

        $("#staste").select2({
            placeholder: "Select State",
            allowClear: true
        });
    });



    $(document).ready(function() {
        $('.table-with-buttons').DataTable({
            dom: 'Blfrtip',
            paging: true,
            autoWidth: true,
            responsive: true,
            // scrollX: '1200px',
            // scrollCollapse: true,
            // pagingType: 'full_numbers',
            "lengthMenu": [10, 25, 50, 75, 100],
            aoColumnDefs: [{
                    "aTargets": [0],
                    "bSortable": true
                },
                {
                    "aTargets": [6],
                    "bSortable": true
                },
            ],
            buttons: [
                'csv',
                'excel',
            ],

        });
    });

    $(document).ready(function() {
        // getNotificaitons();
        // Pusher.logToConsole = false;

        // var BookingPusher = new Pusher(pusherKey, {
        //     cluster: pusherKeyCluster
        // });

        // var BookingNotficationChannel = BookingPusher.subscribe('booking-notification-admin');
        // BookingPusher.connection.bind('connected', function() {
        //     console.log('Pusher connected', BookingNotficationChannel);
        // });
        // BookingNotficationChannel.bind('booking.notification', function(data) {
        //     var unreadNotificationCount = $("#notification-count").text();
        //     var response = JSON.stringify(data);
        //     var resp = JSON.parse(response)
        //     var html = `<li><hr class="dropdown-divider"></li><li class="notification-item notification" data-id="${item.id}">
        //               <i class="bi bi-check-circle text-success"></i>
        //               <div>
        //                 <h4>Booking Received</h4>
        //                 <p>${resp.message}</p>
        //                 <p></p>
        //               </div>
        //             </li><li><hr class="dropdown-divider"></li>`;

        //     $("#notification-center").append(html);
        //     unreadNotificationCount++;
        //     $('#notification-count').text(unreadNotificationCount).show();

        // });

    });

    // $(document).on("click", '.notification', function() {
    //     var notificationId = $(this).data('id');
    //     var unreadNotificationCount = $("#notification-count").text();
    //     var data = {
    //         _token: "{{ csrf_token() }}",
    //         read: true,
    //         id: notificationId
    //     };
    //     $.post('/admin/notifications/' + notificationId + '/read', data, function(response) {
    //         // Handle the response if needed
    //     });
    //     $(this).remove();
    //     unreadNotificationCount--;
    //     $('#notification-count').text(unreadNotificationCount).show();
    // })

    // function getNotificaitons() {
    //     var count = 0;
    //     $.get("{{ route('notification.get') }}", function(response) {
    //         count = response.length;
    //         var html = '';
    //         $.each(response, function(key, item) {
    //             html += `<li><hr class="dropdown-divider"></li><li class="notification-item notification" data-id="${item.id}">
    //                   <i class="bi bi-check-circle text-success"></i>
    //                   <div>
    //                     <h4>Booking Received</h4>
    //                     <p>${item.message}</p>
    //                     <p></p>
    //                   </div>
    //                 </li><li><hr class="dropdown-divider"></li>`;



    //         })
    //         $("#notification-center").html(html);
    //         $("#notification-count").show().text(count);
    //     });
    // }



    function ShowSuccess(message) {
        $("#success-alert").find('span').text(message);
        $("#success-alert").fadeIn(500);
        setTimeout(function() {
            $("#success-alert").fadeOut(500);
        }, 2500);
    }

    function ShowError(message) {
        $("#error-alert").find('span').text(message);
        $("#error-alert").fadeIn(500);
        setTimeout(function() {
            $("#error-alert").fadeOut(500);
        }, 2500);
    }

    $(document).ready(function() {
        // Users can skip the loading process if they want.

        // Will wait for everything on the page to load.
        $(window).bind('load', function() {
            $('.overlay, body').addClass('loaded');
            setTimeout(function() {
                $('.overlay').css({
                    'display': 'none'
                })
            }, 2000)
        });
        $('.overlay, body').addClass('loaded');
        // Will remove overlay after 1min for users cannnot load properly.
        setTimeout(function() {

        }, 60000);
    })
    $(document).ready(function() {

        $(document).on("click", ".copyButton", function() {
            var $this = $(this);

            // Get the data-href attribute value
            var linkToCopy = $this.attr('data-href');

            // Create a temporary input element to copy the text to the clipboard
            var tempInput = $("<input>");
            $("body").append(tempInput);
            tempInput.val(linkToCopy).select();
            document.execCommand('copy');
            tempInput.remove();

            // Change button text to "Copied" temporarily
            $this.text("Copied");

            // Reset button text after 1000 milliseconds (1 second)
            setTimeout(() => {
                $this.text("Copy Link");
                window.open(linkToCopy)
            }, 1000);
        })
        // Click event for the Copy Link button

    });

    toastr.options = {
        "closeButton": true,
        "newestOnTop": false,
        "progressBar": true,
        "positionClass": "toast-bottom-center",
        "preventDuplicates": false,
        "onclick": null,
        "showDuration": "3000",
        "hideDuration": "1000",
        "timeOut": "3000",
        "extendedTimeOut": "1000",
        "showEasing": "swing",
        "hideEasing": "linear",
        "showMethod": "fadeIn",
        "hideMethod": "fadeOut"
    }

    $('input[name="pick_venue_date"]').daterangepicker();



    // Set the options that I want
</script>
</body>

</html>
