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
        <ul id="user-nav" class="{{ ( $currentPath == 'users.index' || $currentPath == 'roles.index' || $currentPath == 'permissions.index' ) ? 'nav-content collapse show' : 'nav-content collapse' }}" data-bs-parent="#sidebar-nav">
          <li>
            <a href="{{ route('users.index') }}" class="{{ ( $currentPath == 'users.index') ? 'active' : '' }}">
              <i class="bi bi-circle"></i><span>Manage User</span>
            </a>
          </li>
          <li>
            <a href="{{ route('roles.index') }}"  class="{{ ( $currentPath == 'roles.index') ? 'active' : '' }}">
              <i class="bi bi-circle"></i><span>Manage Role</span>
            </a>
          </li>
          <li>
            <a href="{{ route('permissions.index') }}" class="{{ ( $currentPath == 'permissions.index') ? 'active' : '' }}">
              <i class="bi bi-circle"></i><span>Manage Permission</span>
            </a>
          </li>
        </ul>
      </li>
      @endcanany

      @can('user-management-access')
      <li class="nav-item">
        <a class="nav-link collapsed" data-bs-target="#venue-nav" data-bs-toggle="collapse" href="#">
          <i class="bi bi-person"></i><span>Venue Management</span><i class="bi bi-chevron-down ms-auto"></i>
        </a>
        <ul id="venue-nav" 
        class="{{ ( $currentPath == 'venues.index' || $currentPath == 'venues.show' 
        || $currentPath == 'venues.edit' || $currentPath == 'country.create' || $currentPath == 'country.edit'  ) ? 'nav-content collapse show' : 'nav-content collapse' }}" 
        data-bs-parent="#sidebar-nav">
           
          <li>
            <a data-href="{{ $currentPath }}" href="{{ route('country.create') }}" class="{{ ( $currentPath == 'country.create' || $currentPath == 'country.edit'   ) ? 'active' : '' }}">
              <i class="bi bi-circle"></i><span>Add Venue Country</span>
            </a>
          </li>
          <li>
            <a data-href="{{ $currentPath }}" href="{{ route('country.index') }}" class="{{ ( $currentPath == 'venues.list-country' || $currentPath == 'venues.edit'   ) ? 'active' : '' }}">
              <i class="bi bi-circle"></i><span>List Venue Country</span>
            </a>
          </li>
          
          <li>
            <a href="{{ route('venues.create') }}" class="{{ ($currentPath == 'venues.edit' || $currentPath == 'venues.create' ) ? 'active' : '' }}">
              <i class="bi bi-circle"></i><span>Add Venues</span>
            </a>
          </li>

          <li>
            <a href="{{ route('venues.index') }}" class="{{ ( $currentPath == 'venues.index' ) ? 'active' : '' }}">
              <i class="bi bi-circle"></i><span>List Venues</span>
            </a>
          </li>

         
           
        </ul>
      </li>
      @endcan 

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

      @can('user-management-access')
      <li class="nav-item">
        <a class="nav-link collapsed" data-bs-target="#siteadmin-nav" data-bs-toggle="collapse" href="#">
          <i class="bi bi-person"></i><span>Site Admin</span><i class="bi bi-chevron-down ms-auto"></i>
        </a>
        <ul id="siteadmin-nav" 
        class="{{ ( $currentPath == 'siteadmin.queue.show'  ) ? 'nav-content collapse show' : 'nav-content collapse' }}" 
        data-bs-parent="#sidebar-nav">
           
          <li>
            <a data-href="{{ $currentPath }}" href="{{ route('siteadmin.queue.show') }}" class="{{ ( $currentPath == 'siteadmin.queue.show') ? 'active' : '' }}">
              <i class="bi bi-circle"></i><span>Show</span>
            </a>
          </li>
             
           
        </ul>
      </li>
      @endcan 

       @can('user-management-access')
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

          {{-- <li>
            <a data-href="{{ $currentPath }}" href="{{ route('siteadmin.queue.show') }}" 
            class="{{ ( $currentPath == 'siteadmin.queue.show') ? 'active' : '' }}">
              <i class="bi bi-circle"></i><span>Join</span>
            </a>
          </li> --}}
             
           
        </ul>
      </li>
      @endcan 
    


      
    </ul>

  </aside><!-- End Sidebar-->
