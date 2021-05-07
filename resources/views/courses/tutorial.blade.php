
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
            }
        }
        #content, #sidecontent{
        min-height: 40vh;
    }
    </style>
    <!-- Custom styles for this template -->
    <link href="https://getbootstrap.com/docs/4.3/examples/album/album.css" rel="stylesheet">
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
            <li  class="nav-item">
                <a class="nav-link" href="{{route('admin')}}"><i class="fa fa-user"></i> Login</a>
            </li>
          </ul>
        </div>
      </nav>
</header>

<style>
    .navbar {
        background-color: #2F3955;
    }
</style>
<br>
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-9">
            <div class="card">
                <div class="card-header">
                    {{$course->course_name}} - Online Course <a class="btn border-danger btn-sm float-right" href="{{route('homepage')}}"><i class="fa fa-home"></i> Home</a>
                </div>

                <div class="card-body">

                    <div class="card-body" id="content">
                        <h2>Welcome to MMMHMC BLS-Recertification Course!</h2>
                        <p>At the end of this course you are expected to.</p>
                        <ul>
                            <li>1. bla bla bla</li>
                            <li>2. bla bla bla</li>
                            <li>3. bla bla bla</li>
                            <li>4. bla bla bla</li>
                        </ul>
                        <p>This is a self paced course, you can attend to the course and watch the video lectures.</p>
                        <p>Do accomplish the pre-tests and post-tests before and after the course.</p>
                        <p>Good luck!.</p>
                        <br>
                        <a class="btn btn-info btn-lg" href="{{url('/')}}/course/{{$course->course_slug}}/{{$modules[0]->module_slug}}">Start Course</a>
                    </div>
                </div>
            </div>
            <br>
        </div>
        <div class="col-md-3">
            <div class="card">
                <div class="card-header">Modules</div>

                <div class="card-body">
                    <ul>
                        @foreach($modules as $module)
                        <li><span class="badge badge-info">{{ $loop->iteration }}</span> {{$module->module_name}}</li>
                        @endforeach
                      </ul>
                </div>
            </div>
        </div>
<script>
    $(document).ready(function() {
        $('.owl-carousel').owlCarousel({
            margin: 10,
            items: 1
            // nav: true,
            // navText : ["<i class='fa fa-chevron-left'></i>","<i class='fa fa-chevron-right'></i>"],
            // responsive: {
            //     0: {
            //         items: 1
            //     },
            //     600: {
            //         items: 3
            //     },
            //     1000: {
            //         items: 5
            //     }
            // }
        });
        $('.owl-carousel').trigger('to.owl.carousel', 0)
    })
</script>
<script>window.jQuery || document.write('<script src="https://getbootstrap.com/docs/4.3/assets/js/vendor/jquery-slim.min.js"><\/script>')</script><script src="https://getbootstrap.com/docs/4.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-xrRywqdh3PHs8keKZN+8zzc5TX0GRTLCcmivcbNJWm2rs5C8PRhcEn3czEjhAO9o" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/feather-icons/4.9.0/feather.min.js"></script>
<script>
    (function () {
        'use strict'

        feather.replace()
    }())
</script>
</body>
</html>
