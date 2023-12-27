 
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
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  {{-- <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script> --}}
  <!-- Select2 -->
  {{-- <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/mdb-ui-kit/7.0.0/mdb.min.js"></script> --}}
  <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>  
  <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/js/select2.min.js"></script>
 <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>

 <script>
	window.fwSettings={
	'widget_id':151000000123
	};
	!function(){if("function"!=typeof window.FreshworksWidget){var n=function(){n.q.push(arguments)};n.q=[],window.FreshworksWidget=n}}() 
</script>
<script type='text/javascript' src='https://widget.freshworks.com/widgets/151000000123.js' async defer></script>
  @yield('page-script')
</body>

</html>