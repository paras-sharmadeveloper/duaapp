<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}"> 

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=Nunito" rel="stylesheet">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap4.min.css"> 

   

    <!-- Scripts -->
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])
     
</head>
<style type="text/css">
    .margin-tb {
    margin: 10px 0;
}
.input-group-btn {
    display: initial;
}
.pull-right {
    float: right;
}
.input-group-btn {
    display: flex;
}
</style>
<body>
    <div id="app">
        <nav class="navbar navbar-expand-md navbar-light bg-white shadow-sm">
            <div class="container">
                <a class="navbar-brand" href="{{ url('/') }}">
                    {{ config('app.name', 'Laravel') }}
                </a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <!-- Left Side Of Navbar -->
                    <ul class="navbar-nav me-auto">

                    </ul>

                    <!-- Right Side Of Navbar -->
                    <ul class="navbar-nav ms-auto">
                        <!-- Authentication Links -->
                         @guest
                            <li><a class="nav-link" href="{{ route('login') }}">{{ __('Login') }}</a></li>
                            @if (Route::has('register'))
                            <li><a class="nav-link" href="{{ route('register') }}">{{ __('Register') }}</a></li>
                            @endif
                        @else  

                            
                            @canany('create-user','edit-user','delete-user','list-user')
                            <div class="dropdown">
                              <button class="btn dropdown-toggle" type="button" data-toggle="dropdown">User Management
                              <span class="caret"></span></button>
                              <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="{{ route('users.index') }}">Manage User</a></li>
                                <li><a class="dropdown-item" href="{{ route('roles.index') }}">Manage Roles</a></li> 
                                <li><a class="dropdown-item" href="{{ route('permissions.index') }}">Manage Permission</a></li> 
                                 
                              </ul>
                            </div>
                            @endcanany
                            <li><a class="nav-link" href="{{ route('grid-show') }}">{{ __('Analytic') }}</a></li>
                           
                             @canany('create-campaign','edit-campaign','delete-campaign','list-campaign')
                            <div class="dropdown">
                              <button class="btn dropdown-toggle" type="button" data-toggle="dropdown">Campaign
                              <span class="caret"></span></button>
                              <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="{{ route('campaign.index') }}">add</a></li>  
                                <li><a class="dropdown-item" href="{{ route('campaign.list') }}">list</a></li>  

                              </ul>
                            </div>
                             @endcanany


                            <li class="nav-item dropdown">
                                <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                    {{ Auth::user()->name }} <span class="caret"></span>
                                </a>


                                <div class="dropdown-menu" aria-labelledby="navbarDropdown">
                                    <a class="dropdown-item" href="{{ route('logout') }}"
                                       onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();">
                                        {{ __('Logout') }}
                                    </a>


                                    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                        @csrf
                                    </form>
                                </div>
                            </li>
                        @endguest
                    </ul>
                </div>
            </div>
        </nav>

        <main class="py-4">
            <div class="container" id="body-container">
            @yield('content')
            </div>
        </main>
    </div>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js">
    </script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js">
    </script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js">
    </script>
    <script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap4.min.js">
    </script>
     <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js">
    </script>
    
    <!-- <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script> -->
    @yield('page-script-custom')
</body>
</html>
