<!-- ======= Sidebar ======= -->
@php
  $currentPath = Route::currentRouteName();
@endphp
  <aside id="sidebar" class="sidebar" data-route="{{ Route::currentRouteName() }}">

    <ul class="sidebar-nav" id="sidebar-nav">

      <li class="nav-item">
        <a class="{{ ( $currentPath == 'home') ? 'nav-link' : 'nav-link collapsed' }}"  href="{{ route('home') }}">
          <i class="bi bi-grid"></i>
          <span>Dashboard</span>
        </a>
      </li>
       
  
      
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
          <i class="bi bi-person"></i><span>Venue Management</span><i class="bi bi-chevron-down ms-auto"></i>
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
      <li class="nav-item">
        <a class="nav-link collapsed" data-bs-target="#vistor-nav" data-bs-toggle="collapse" href="#">
          <i class="bi bi-person"></i><span>Vistor Management</span><i class="bi bi-chevron-down ms-auto"></i>
        </a>
        <ul id="vistor-nav" class="{{ ( $currentPath == 'visitor.index' ) ? 'nav-content collapse show' : 'nav-content collapse' }}" data-bs-parent="#sidebar-nav">
          <li>
            <a href="{{ route('visitor.index') }}" class="{{ ( $currentPath == 'visitor.index') ? 'active' : '' }}">
              <i class="bi bi-circle"></i><span>List</span>
            </a>
          </li>
           
        </ul>
      </li>
      @endcanany
      @canany('site-admin-access')
      <li class="nav-item">
        <a class="nav-link collapsed" data-bs-target="#siteadmin-nav" data-bs-toggle="collapse" href="#">
          <i class="bi bi-person"></i><span>Site Admin</span><i class="bi bi-chevron-down ms-auto"></i>
        </a>
        <ul id="siteadmin-nav" 
        class="{{ ( 
          $currentPath == 'siteadmin.queue.show'  
          || $currentPath =='siteadmin.queue.list.request'
           ) ? 'nav-content collapse show' : 'nav-content collapse' }}" 
        data-bs-parent="#sidebar-nav">
           
          <li>
            <a data-href="{{ $currentPath }}" href="{{ route('siteadmin.queue.show') }}" 
            class="{{ ( $currentPath == 'siteadmin.queue.show') ? 'active' : '' }}">
              <i class="bi bi-circle"></i><span>Offline Request List</span>
            </a>
          </li>

          <li>
            <a data-href="{{ $currentPath }}" href="{{ route('siteadmin.queue.list.request') }}" class="{{ ( $currentPath == 'siteadmin.queue.list.request') ? 'active' : '' }}">
              <i class="bi bi-circle"></i><span>Online Request List</span>
            </a>
          </li>
  
        </ul>
      </li>
      @endcanany

       @canany('vedio-call-access')
      <li class="nav-item">
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
      </li>
      @endcanany

      @canany('visitor-booking-access')
      <li class="nav-item">
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
    


      
    </ul>

  </aside><!-- End Sidebar-->
