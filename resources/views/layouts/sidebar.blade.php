<!-- ======= Sidebar ======= -->
@php
  $currentPath = Route::currentRouteName();
@endphp
  <aside id="sidebar" class="sidebar" data-route="{{ Route::currentRouteName() }}">

    <ul class="sidebar-nav" id="sidebar-nav">
        @canany('user-management-access')
      <li class="nav-item">
        <a class="{{ ( $currentPath == 'home') ? 'nav-link' : 'nav-link collapsed' }}"  href="{{ route('home') }}">
            <i class="bi bi-kanban"></i>

          <span>Dashboard</span>
        </a>
      </li>
      @endcanany



      <!-- End Dashboard Nav -->
       @canany('user-management-access')
      <li class="nav-item">
        <a class="nav-link collapsed" data-bs-target="#user-nav" data-bs-toggle="collapse" href="#">
          <i class="bi bi-person"></i><span>User Management</span><i class="bi bi-chevron-down ms-auto"></i>
        </a>
        <ul id="user-nav" class="{{
        (
          $currentPath == 'users.index' ||
          $currentPath == 'roles.index' ||
          $currentPath == 'users.create' ||
          $currentPath == 'users.show' ||
          $currentPath == 'users.edit' ||
          $currentPath =='permissions.create' ||
          $currentPath =='permissions.edit'  ||
          $currentPath =='permissions.index'  ||
          $currentPath =='permissions.show'  ||

          $currentPath =='roles.index' ||
          $currentPath =='roles.show' ||
          $currentPath =='roles.edit' ||
          $currentPath =='roles.create'


          ) ? 'nav-content collapse show' : 'nav-content collapse' }}" data-bs-parent="#sidebar-nav">
          <li>
            <a href="{{ route('users.index') }}"
            class="{{ (
              $currentPath == 'users.index' || $currentPath == 'users.edit' || $currentPath == 'users.create' || $currentPath == 'users.show'
              ) ? 'active' : '' }}">
              <i class="bi bi-circle"></i><span>Manage User</span>
            </a>
          </li>
          <li>
            <a href="{{ route('roles.index') }}"  class="{{ (
              $currentPath == 'roles.index' ||
               $currentPath =='roles.create' ||
               $currentPath =='roles.edit' ||
               $currentPath =='roles.show'

               ) ? 'active' : '' }}">
              <i class="bi bi-circle"></i><span>Manage Role</span>
            </a>
          </li>
          <li>
            <a href="{{ route('permissions.index') }}" class="{{ (
              $currentPath == 'permissions.index' ||
              $currentPath == 'permissions.create' ||
              $currentPath == 'permissions.edit' ||
              $currentPath == 'permissions.show'

              ) ? 'active' : '' }}">
              <i class="bi bi-circle"></i><span>Manage Permission</span>
            </a>
          </li>
        </ul>
      </li>
      @endcanany

      @canany('user-management-access')
      <li class="nav-item">
        <a class="nav-link collapsed" data-bs-target="#venue-nav" data-bs-toggle="collapse" href="#">
            <i class="bi bi-layout-wtf"></i><span>Venue Management</span><i class="bi bi-chevron-down ms-auto"></i>
        </a>
        <ul id="venue-nav"
        class="{{ ( $currentPath == 'venues.index'
                  || $currentPath == 'venues.show'
                  || $currentPath == 'venues.create'
                  || $currentPath == 'venues.edit'
                  || $currentPath == 'country.create'
                  || $currentPath == 'country.edit'
                  || $currentPath =='country.index'

                  ) ? 'nav-content collapse show' : 'nav-content collapse' }}"
        data-bs-parent="#sidebar-nav">

          <li>
            <a data-href="{{ $currentPath }}" href="{{ route('country.create') }}" class="{{ ( $currentPath == 'country.create' || $currentPath == 'country.edit'   ) ? 'active' : '' }}">
              <i class="bi bi-circle"></i><span>Create Country</span>
            </a>
          </li>
          <li>
            <a data-href="{{ $currentPath }}" href="{{ route('country.index') }}" class="{{ ( $currentPath == 'country.index' || $currentPath == 'country.edit'   ) ? 'active' : '' }}">
              <i class="bi bi-circle"></i><span>List Country</span>
            </a>
          </li>

          <li>
            <a href="{{ route('venues.create') }}" class="{{ ($currentPath == 'venues.edit' || $currentPath == 'venues.create' ) ? 'active' : '' }}">
              <i class="bi bi-circle"></i><span>Create Venues</span>
            </a>
          </li>

          <li>
            <a href="{{ route('venues.index') }}" class="{{ ( $currentPath == 'venues.index' ) ? 'active' : '' }}">
              <i class="bi bi-circle"></i><span>List Venues</span>
            </a>
          </li>



        </ul>
      </li>
      @endcanany
      @canany('vistor-schduling-access')
      {{-- <li class="nav-item">
        <a class="nav-link collapsed" data-bs-target="#vistor-nav" data-bs-toggle="collapse" href="#">
          <i class="bi bi-person"></i><span>Public Booking</span><i class="bi bi-chevron-down ms-auto"></i>
        </a>
        <ul id="vistor-nav" class="{{ ( $currentPath == 'visitor.index' ) ? 'nav-content collapse show' : 'nav-content collapse' }}" data-bs-parent="#sidebar-nav">
          <li>
            <a href="{{ route('visitor.index') }}" class="{{ ( $currentPath == 'visitor.index') ? 'active' : '' }}">
              <i class="bi bi-circle"></i><span>List</span>
            </a>
          </li>
S
        </ul>
      </li> --}}
      @endcanany
      @canany('site-admin-access')
      <li class="nav-item">
        <a class="nav-link collapsed" data-bs-target="#siteadmin-nav" data-bs-toggle="collapse" href="#">
            <i class="bi bi-microsoft-teams"></i><span>Site Admin</span><i class="bi bi-chevron-down ms-auto"></i>
        </a>
        <ul id="siteadmin-nav"
        class="{{ (
          $currentPath == 'siteadmin.queue.show'
          || $currentPath =='siteadmin.queue.list.request'
          || $currentPath == 'siteadmin.queue.list'
          || $currentPath =='siteadmin.pending.show'
          || $currentPath == 'siteadmin.pending.list'
          || $currentPath =='qr.show.scan'
          || $currentPath == 'manual.token'
           ) ? 'nav-content collapse show' : 'nav-content collapse' }}"
        data-bs-parent="#sidebar-nav">
        <li>
            <a data-href="{{ $currentPath }}" href="{{ route('siteadmin.pending.show') }}" class="{{ ( $currentPath == 'siteadmin.pending.list' ||  $currentPath == 'siteadmin.pending.show' ) ? 'active' : '' }}">
              <i class="bi bi-circle"></i><span>Search Tokens </span>
            </a>
          </li>
          <li>
            <a data-href="{{ $currentPath }}" href="{{ route('qr.show.scan') }}" class="{{ ( $currentPath == 'qr.show.scan') ? 'active' : '' }}">
              <i class="bi bi-circle"></i><span>Print Tokens</span>
            </a>
          </li>


          <li>
            <a data-href="{{ $currentPath }}" href="{{ route('siteadmin.queue.show') }}"
            class="{{ ( $currentPath == 'siteadmin.queue.show' ||  $currentPath == 'siteadmin.queue.list' ) ? 'active' : '' }}">
              <i class="bi bi-circle"></i><span>Launch Tokens</span>
            </a>
          </li>

          <li>
            <a data-href="{{ $currentPath }}" href="{{ route('manual.token') }}"
            class="{{ ( $currentPath == 'manual.token' ||  $currentPath == 'manual.token' ) ? 'active' : '' }}">
              <i class="bi bi-circle"></i><span>Manual Special Token</span>
            </a>
          </li>

        </ul>
      </li>
      @endcanany

       @canany('vedio-call-access')
      {{-- <li class="nav-item">
        <a class="nav-link collapsed" data-bs-target="#video-nav" data-bs-toggle="collapse" href="#">
          <i class="bi bi-person"></i><span>Video Conference</span><i class="bi bi-chevron-down ms-auto"></i>
        </a>
        <ul id="video-nav"
        class="{{ ( $currentPath == 'join.conference.show'  ) ? 'nav-content collapse show' : 'nav-content collapse' }}"
        data-bs-parent="#sidebar-nav">

          <li>
            <a data-href="{{ $currentPath }}" href="{{ route('join.conference.show') }}"
            class="{{ ( $currentPath == 'join.conference.show') ? 'active' : '' }}">
              <i class="bi bi-circle"></i><span>Show</span>
            </a>
          </li>


        </ul>
      </li> --}}
      @endcanany

      @canany('visitor-booking-access')
      <li class="nav-item d-none">
        <a class="nav-link collapsed" data-bs-target="#visitor-booking-nav" data-bs-toggle="collapse" href="#">
          <i class="bi bi-person"></i><span>Visitor Booking</span><i class="bi bi-chevron-down ms-auto"></i>
        </a>
        <ul id="visitor-booking-nav"
        class="{{ ( $currentPath == 'booking.create'  || $currentPath == 'booking.list'|| $currentPath == 'booking.edit') ? 'nav-content collapse show' : 'nav-content collapse' }}"
        data-bs-parent="#sidebar-nav">

          <li>
            <a data-href="{{ $currentPath }}" href="{{ route('booking.create') }}"
            class="{{ ( $currentPath == 'booking.create') ? 'active' : '' }}">
              <i class="bi bi-circle"></i><span>Create</span>
            </a>
          </li>

          <li>
            <a data-href="{{ $currentPath }}" href="{{ route('booking.list') }}"
            class="{{ ( $currentPath == 'booking.list') ? 'active' : '' }}">
              <i class="bi bi-circle"></i><span>List</span>
            </a>
          </li>


        </ul>
      </li>
      @endcanany
      @canany('user-management-access')
      <li class="nav-item ">
        <a class="nav-link collapsed" data-bs-target="#visitor-booking-nav" data-bs-toggle="collapse" href="#">
            <i class="bi bi-pin-angle-fill"></i><span>Reject Reason</span><i class="bi bi-chevron-down ms-auto"></i>
        </a>
        <ul id="visitor-booking-nav"
        class="{{ ( $currentPath == 'reasons.index'  || $currentPath == 'reasons.create'|| $currentPath == 'reasons.edit' || $currentPath == 'reasons.announcement') ? 'nav-content collapse show' : 'nav-content collapse' }}"
        data-bs-parent="#sidebar-nav">


          <li>
            <a data-href="{{ $currentPath }}" href="{{ route('reasons.index') }}"
            class="{{ ( $currentPath == 'reasons.index') ? 'active' : '' }}">
              <i class="bi bi-circle"></i><span>Reason/Announcement List</span>
            </a>
          </li>


        </ul>
      </li>

      <li class="nav-item ">
        <a class="nav-link collapsed" data-bs-target="#whatsapp-not-nav" data-bs-toggle="collapse" href="#">
            <i class="bi bi-pin-angle-fill"></i><span>Notifications</span><i class="bi bi-chevron-down ms-auto"></i>
        </a>
        <ul id="whatsapp-not-nav"
        class="{{ ( $currentPath == 'reasons.index'  || $currentPath == 'reasons.create'|| $currentPath == 'reasons.edit' || $currentPath == 'reasons.announcement') ? 'nav-content collapse show' : 'nav-content collapse' }}"
        data-bs-parent="#sidebar-nav">


          <li>
            <a class="{{ ( $currentPath == 'whatsapp.notication.show') ? 'nav-link' : 'nav-link collapsed' }}"  href="{{ route('whatsapp.notication.show') }}">
                <i class="bi bi-whatsapp"></i>
                <span>Whatsapp Notification </span>
              </a>
          </li>

          <li>
            <a class="{{ ( $currentPath == 'whatsapp.form') ? 'nav-link' : 'nav-link collapsed' }}"  href="{{ route('whatsapp.form') }}">
                <i class="bi bi-whatsapp"></i>
                <span>Whatsapp Import </span>
              </a>
          </li>


        </ul>
      </li>


      <li class="nav-item">
        <a class="{{ ( $currentPath == 'working.lady.list') ? 'nav-link' : 'nav-link collapsed' }}"  href="{{ route('working.lady.list') }}">
            <i class="bi bi-person-vcard"></i>
          <span>Working Lady </span>
        </a>
      </li>

      <li class="nav-item">
        <a  href="{{ route('booking.manual.list') }}" class="{{ ( $currentPath == 'booking.manual.list') ? 'nav-link' : 'nav-link collapsed' }}" >
            <i class="bi bi-person-vcard"></i>
          <span>Manual Bookings</span>
        </a>
      </li>

      <li class="nav-item">
        <a class="{{ ( $currentPath == 'admin.filter') ? 'nav-link' : 'nav-link collapsed' }}"  href="{{ route('admin.filter') }}?date={{ date('Y-m-d')}}">
            <i class="bi bi-person-vcard"></i>
          <span>Token Filter </span>
        </a>
      </li>
      <li class="nav-item">
        <a  href="{{ route('admin.doorlog') }}" class="{{ ( $currentPath == 'admin.doorlog') ? 'nav-link' : 'nav-link collapsed' }}" >
            <i class="bi bi-person-vcard"></i>
          <span>Door Open Logs</span>
        </a>
      </li>

      <li class="nav-item ">
        <a class="nav-link collapsed" data-bs-target="#setting-dev-nav" data-bs-toggle="collapse" href="#">
            <i class="bi bi-pin-angle-fill"></i><span>Setting Developer</span><i class="bi bi-chevron-down ms-auto"></i>
        </a>
        <ul id="setting-dev-nav"
        class="{{ ( $currentPath == 'reasons.index'  || $currentPath == 'reasons.create'|| $currentPath == 'reasons.edit' || $currentPath == 'reasons.announcement') ? 'nav-content collapse show' : 'nav-content collapse' }}"
        data-bs-parent="#sidebar-nav">


          <li>
            <a data-href="{{ $currentPath }}" target="_blank" href="{{ route('config.clear') }}" class="{{ ( $currentPath == 'config.clear') ? 'active' : '' }}">
              <i class="bi bi-circle"></i><span>Clear Cache</span>
            </a>
          </li>

          <li>
            <a data-href="{{ $currentPath }}" target="_blank" href="{{ route('debug.enable',['debug' => 'true']) }}" class="{{ ( $currentPath == 'config.clear') ? 'active' : '' }}">
              <i class="bi bi-circle"></i><span>Enable Debug True</span>
            </a>
          </li>
          <li>
            <a data-href="{{ $currentPath }}" target="_blank" href="{{ route('debug.enable',['debug' => 'false']) }}" class="{{ ( $currentPath == 'config.clear') ? 'active' : '' }}">
              <i class="bi bi-circle"></i><span>Enable Debug False</span>
            </a>
          </li>

          <li>
            <a data-href="{{ $currentPath }}" target="_blank" href="{{ route('admin.logs') }}" class="{{ ( $currentPath == 'admin.logs') ? 'active' : '' }}">
              <i class="bi bi-circle"></i><span>Server Logs</span>
            </a>
          </li>

          <li>
            <a data-href="{{ $currentPath }}" target="_blank" href="{{ route('horizon.index') }}" class="{{ ( $currentPath == 'horizon.index') ? 'active' : '' }}">
              <i class="bi bi-circle"></i><span>Horizon</span>
            </a>
          </li>








        </ul>
      </li>





      @endcan

    </ul>

  </aside><!-- End Sidebar-->
