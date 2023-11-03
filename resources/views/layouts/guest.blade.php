@include('layouts.guest-head')
<main>
  <style>
    .container-fluid {
    padding: 0px 80px;
}
</style>
 
    <div class="container"> 
      @yield('content') 
    </div>
  </main><!-- End #main -->
@include('layouts.guest-foot')
