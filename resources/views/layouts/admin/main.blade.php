<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta https-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="canonical" href="https://getbootstrap.com/docs/3.3/examples/dashboard/">

    <title>LMS Admin</title>
    <script src="https://unpkg.com/feather-icons"></script>

    <!-- Bootstrap core CSS -->
    <link href="https://getbootstrap.com/docs/3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- IE10 viewport hack for Surface/desktop Windows 8 bug -->
    <link href="https://getbootstrap.com/docs/3.3/assets/css/ie10-viewport-bug-workaround.css" rel="stylesheet">

    <!-- Custom styles for this template -->
    <link href="https://getbootstrap.com/docs/3.3/examples/dashboard/dashboard.css" rel="stylesheet">


    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->

    <!-- include libraries(jQuery, bootstrap) -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.js"></script>
    <script src="https://netdna.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.js"></script>


    <link href="https://cdn.datatables.net/v/bs/dt-1.13.4/date-1.4.0/r-2.4.1/datatables.min.css" rel="stylesheet"/>
    
    <!-- include summernote css/js -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.12/summernote.css" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.12/summernote.js"></script>
    <style>
        .feather-16{
            width: 16px;
            height: 16px;
        }

        .badge {
        display: inline-block;
        font-size: 11px;
        font-weight: 600;
        padding: 3px 6px;
        border: 1px solid transparent;
        min-width: 10px;
        line-height: 1;
        color: #fff;
        text-align: center;
        white-space: nowrap;
        vertical-align: middle;
        border-radius: 99999px
        }

        .badge.badge-default {
        background-color: #B0BEC5
        }

        .badge.badge-primary {
        background-color: #2196F3
        }

        .badge.badge-secondary {
        background-color: #323a45
        }

        .badge.badge-success {
        background-color: #64DD17
        }

        .badge.badge-warning {
        background-color: #FFD600
        }

        .badge.badge-info {
        background-color: #29B6F6
        }

        .badge.badge-danger {
        background-color: #ef1c1c
        }

        .badge.badge-outlined {
        background-color: transparent
        }

        .badge.badge-outlined.badge-default {
        border-color: #B0BEC5;
        color: #B0BEC5
        }

        .badge.badge-outlined.badge-primary {
        border-color: #2196F3;
        color: #2196F3
        }

        .badge.badge-outlined.badge-secondary {
        border-color: #323a45;
        color: #323a45
        }

        .badge.badge-outlined.badge-success {
        border-color: #64DD17;
        color: #64DD17
        }

        .badge.badge-outlined.badge-warning {
        border-color: #FFD600;
        color: #FFD600
        }

        .badge.badge-outlined.badge-info {
        border-color: #29B6F6;
        color: #29B6F6
        }

        .badge.badge-outlined.badge-danger {
        border-color: #ef1c1c;
        color: #ef1c1c
        }
    </style>
</head>

<body>

<nav class="navbar navbar-inverse navbar-fixed-top">
    <div class="container-fluid">
        <div class="navbar-header">
            <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <a class="navbar-brand" href="{{route('homepage')}}">LMS ADMIN</a>
        </div>
        <div id="navbar" class="navbar-collapse collapse">
            <ul class="nav navbar-nav navbar-right">
                <li class="dropdown-submenu">
                    <a href="{{route('admin')}}" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false"> <span class="nav-label">Admin Menu</span><span class="caret"></span></a>
                    <ul class="dropdown-menu">
                        <li><a href="{{route('admin.courses.index')}}">Manage Courses</a></li>
                        <!--<li><a href="{{route('admin.modules.index')}}">Modules</a></li> -->
                    </ul>
                <li><a href="{{route('homepage')}}">Go Back to Dashboard</a></li>
            </ul>
        </div>
    </div>
</nav>

<div class="container-fluid">
    <div class="row">
        <div class="col-sm-3 col-md-2 sidebar">
            <ul class="nav nav-sidebar">
                <li class="{{ (request()->is('admin')) ? 'active' : '' }}"><a href="{{url('/admin')}}"><i class="feather-16" data-feather="home"></i> Dashboard</a></li>
                <li class="{{ (request()->is('admin/courses*')) ? 'active' : '' }}"><a href="{{url('/admin/courses')}}"><i class="feather-16" data-feather="book"></i> Courses</a></li>
                 <!--<li class="{{ (request()->is('admin/results*')) ? 'active' : '' }}"><a href="{{url('/admin/results')}}"><i class="feather-16" data-feather="bar-chart"></i> Post Test Results</a></li>
               <li class="{{ (request()->is('admin/modules*')) ? 'active' : '' }}"><a href="{{url('/admin/modules')}}"><i class="feather-16" data-feather="file-text"></i> Modules</a></li> -->
            </ul>
        </div>
        @yield('content')
    </div>
</div>

<!-- Bootstrap core JavaScript
================================================== -->
<!-- Placed at the end of the document so the pages load faster -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
<script>window.jQuery || document.write('<script src="https://getbootstrap.com/docs/3.3/examples/dashboard/assets/js/vendor/jquery.min.js"><\/script>')</script>
<script src="https://getbootstrap.com/docs/3.3/dist/js/bootstrap.min.js"></script>
<!-- Just to make our placeholder images work. Don't actually copy the next line! -->
<script src="https://getbootstrap.com/docs/3.3/assets/js/vendor/holder.min.js"></script>
<!-- IE10 viewport hack for Surface/desktop Windows 8 bug -->
<script src="https://getbootstrap.com/docs/3.3/assets/js/ie10-viewport-bug-workaround.js"></script>
</body>
 
<script src="https://cdn.datatables.net/v/bs/dt-1.13.4/date-1.4.0/r-2.4.1/datatables.min.js"></script>

<script>window.jQuery || document.write('<script src="https://getbootstrap.com/docs/4.3/assets/js/vendor/jquery-slim.min.js"><\/script>')</script><script src="https://getbootstrap.com/docs/4.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-xrRywqdh3PHs8keKZN+8zzc5TX0GRTLCcmivcbNJWm2rs5C8PRhcEn3czEjhAO9o" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/feather-icons/4.9.0/feather.min.js"></script>

<!-- Sweet Alert 2 -->
<script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<!-- Chart.js -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.2.1/chart.min.js" integrity="sha512-v3ygConQmvH0QehvQa6gSvTE2VdBZ6wkLOlmK7Mcy2mZ0ZF9saNbbk19QeaoTHdWIEiTlWmrwAL4hS8ElnGFbA==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script>
    (function () {
        'use strict'

        feather.replace()
    }())
</script>
@yield('additional_scripts')

</html>
