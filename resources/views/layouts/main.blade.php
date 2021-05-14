<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <title>Learning Management System - Laravel </title>

    <link rel="canonical" href="https://getbootstrap.com/docs/4.3/examples/album/">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">

    <!-- Bootstrap core CSS -->
    <link href="https://getbootstrap.com/docs/4.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">

    <!-- Owl Stylesheets -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.0.0-beta.2.4/assets/owl.carousel.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.0.0-beta.2.4/assets/owl.theme.default.css">

    <!-- javascript -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2//2.0.0-beta.2.4/owl.carousel.min.js"></script>

    <!-- Custom styles for this template -->
    <link href="https://getbootstrap.com/docs/4.3/examples/album/album.css" rel="stylesheet">

    <style>
        .bd-placeholder-img {
            font-size: 1.125rem;
            text-anchor: middle;
            -webkit-user-select: none;
            -moz-user-select: none;
            -ms-user-select: none;
            user-select: none;
        }

        @media (min-width: 768px) {
            .bd-placeholder-img-lg {
                font-size: 3.5rem;
            },
            .footer-dark {
                margin-bottom: 0
            }
        }

        a {
            color: #000000 ;
            text-decoration: none;
        }

        a:hover, a:focus {
            color: #2A6496;
            text-decoration: none;
        }
        .navbar {
            background-color: #2F3955;
        }

        footer {
            background-color: #2F3955;
            color: white
        }
    </style>
    @yield('styles')
</head>
<body>
    <header>
        <nav class="navbar navbar-dark shadow-sm">
            <a class="navbar-brand" href="{{route('homepage')}}">MMMH&MC Online Learning System</a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
              <span class="navbar-toggler-icon"></span>
            </button>
          
            <div class="collapse navbar-collapse" id="navbarSupportedContent">
              <ul class="navbar-nav mr-auto">
                <li class="nav-item">
                    <a class="nav-link" href="{{route('homepage')}}"><i class="fa fa-home"></i> Home</a>
                </li>
              </ul>
              <ul class="navbar-nav ml-auto">
                <!-- Authentication Links -->
                @guest
                <li  class="nav-item">
                    <a class="nav-link" href="{{route('admin')}}"><i class="fa fa-user"></i> Login</a>
                </li>
                    @if (Route::has('register'))
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('register') }}"><i class="fa fa-edit"></i> {{ __('Register') }}</a>
                        </li>
                    @endif
                @else
                    @if(Auth::user()->role==1)
                    <li  class="nav-item">
                        <a class="nav-link" href="{{route('admin')}}"><i class="fa fa-dashboard"></i> Dashboard</a>
                    </li>
                    @endif
                    <li class="nav-item dropdown">
                        <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                            <i class="fa fa-user"></i> {{ Illuminate\Support\Str::title(Auth::user()->employee->lastname) }}, {{ Illuminate\Support\Str::title(Auth::user()->employee->firstname) }}<span class="caret"></span>
                        </a>

                        <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
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
          </nav>
    </header>
    @yield('content')
</body>


<script>window.jQuery || document.write('<script src="https://getbootstrap.com/docs/4.3/assets/js/vendor/jquery-slim.min.js"><\/script>')</script><script src="https://getbootstrap.com/docs/4.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-xrRywqdh3PHs8keKZN+8zzc5TX0GRTLCcmivcbNJWm2rs5C8PRhcEn3czEjhAO9o" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/feather-icons/4.9.0/feather.min.js"></script>
<script src="//cdn.jsdelivr.net/npm/sweetalert2@10"></script>
<script>
    (function () {
        'use strict'

        feather.replace()
    }())
</script>
@yield('scripts')
</html>

