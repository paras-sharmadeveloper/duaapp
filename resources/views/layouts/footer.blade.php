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
<script src="{{ asset('assets/theme/vendor/simple-datatables/simple-datatables.js') }}"></script>
<script src="{{ asset('assets/theme/vendor/tinymce/tinymce.min.js') }}"></script>
<script src="{{ asset('assets/theme/vendor/php-email-form/validate.js') }}"></script>

<!-- Template Main JS File -->
<script src="{{ asset('assets/theme/js/main.js') }}"></script>

<script src="https://cdn.jsdelivr.net/npm/@yaireo/tagify@3.1.0/dist/tagify.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/jquery@3.4.1/dist/jquery.min.js"></script>

@yield('page-script')
<script type="text/javascript">
 <script src="https://js.pusher.com/8.2.0/pusher.min.js"></script>
    $(document).ready(function() {

        Pusher.logToConsole = false;

        var pusher = new Pusher('0d51a97603f510fb700e', {
            cluster: 'ap2'
        });
        var channel = pusher.subscribe('booking-notification-admin');
        pusher.connection.bind('connected', function() {
            console.log('Pusher connected');
        });
        channel.bind('booking.notification', function(data) {
          var response = JSON.stringify(data);
            var resp = JSON.parse(response)
            var html = `<li class="notification-item">
                      <i class="bi bi-exclamation-circle text-warning"></i>
                      <div>
                        <h4>Booking Received</h4>
                        <p>${resp.message}</p>
                        <p></p>
                      </div>
                    </li>`; 
          
            $("#notification-center").appened(html); 


        });

    })









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
</script>
</body>

</html>
