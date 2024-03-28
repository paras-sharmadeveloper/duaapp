<main id="main" class="main">

    <div class="pagetitle">
      <h1>{{ ucwords(str_replace('/' , ' > ' , request()->path())) }}</h1>
      <nav>
        <div class="row">
          <div class="col-lg-8">
             <ol class="breadcrumb d-none">
              <li class="breadcrumb-item"><a href="index.html">{{ request()->path() }}</a></li>
              <li class="breadcrumb-item active"> {{ request()->path() }} </li>
            </ol>
          </div>
          <div class="col-lg-4 mt-2">
              <div class="alert alert-success alert-dismissible fade show" role="alert" id="success-alert" style="display:none;">
                <i class="bi bi-check-circle me-1"></i>
                 <span>...</span>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
              </div>
              <div class="alert alert-danger alert-dismissible fade show" role="alert" id="error-alert" style="display:none;">
                <i class="bi bi-exclamation-octagon me-1"></i>
                  <span>...</span>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
              </div>
          </div>
        </div>

      </nav>

    </div><!-- End Page Title -->
       @yield('content')


  </main><!-- End #main -->
