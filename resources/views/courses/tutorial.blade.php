@extends('layouts.main')

@section('content')
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
                        <p>At the end of the training, participants will be able to acquire knowledge, attitude , and skills necessary in an emergency to help sustain life and minimize the consequences of respiratory and cardiac emergencies until more advanced medical help arrives
                        
                        <p>This is a self paced course, you can attend to the course and watch the video lectures any time.</p>
                        <p>Accomplish the Pre-tests and Post-tests before and after the course.</p>
                        <p>Pre Tests and Post Tests can be each answered 3 times only</p>
                        <p>The Post Test passing grades are as follows: </p>
                        <ul>
                            <li>1st Attempt: at least 75% score</li>
                            <li>2nd Attempt: at least 80% score</li>
                            <li>3rd Attempt: at least 85% score</li>
                        </ul>
                        <p>If you have failed to pass the Post Test, please coordinate with the PETU to schedule a face to face training</p>
                        <p>Good luck!</p>
                        <br>
                        <a class="btn btn-info btn-lg" href="{{ $url }}">
                            @if($empCourse)
                                Resume {{ $empCourse->module->module_name }}
                            @else
                                Start Course
                            @endif
                        </a>
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
    </div>
</div>
@endsection