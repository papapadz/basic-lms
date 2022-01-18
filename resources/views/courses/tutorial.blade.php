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
                            @if($attempts['passed'])
                                <a target="_blank" class="text-white btn btn-lg btn-success" href="{{ $attempts['certificate_url'] }}">
                                    <i class="fa fa-trophy" aria-hidden="true"></i>  view certificate
                                </a>
                            @elseif($attempts['attempts']<3)
                                <a class="btn btn-info btn-lg" href="{{ $url }}">
                                    @if($empCourse)
                                        Resume {{ $empCourse->module->module_name }}
                                    @else
                                        Start Course
                                    @endif
                                </a>
                            @endif
                    </div>
                </div>
            </div>
            <br>
        </div>
        <div class="col-md-3">
            <div class="card">
                <div class="card-header">
                    <div class="progress">
                        <div class="progress-bar bg-success" role="progressbar" style="width: @if($attempts['attempts']<3) {{ ($empCourse->module->module_order/count($modules)) *100 }}% @else 100% @endif" aria-valuemin="0" aria-valuemax="100">
                            @if($attempts['attempts']<3)
                                {{ $empCourse->module->module_order }} of {{ count($modules) }}
                            @else
                                Completed
                            @endif
                        </div>
                    </div>
                </div>
                <ul class="list-group list-group-flush">
                    @foreach($modules as $module)
                        <li class="list-group-item @if($empCourse->module->module_order==$loop->iteration && $attempts['attempts']<3) bg-primary @endif" @if($loop->iteration>5) hidden @endif>
                            <span class="badge badge-info">{{ $loop->iteration }}</span> 
                            @if($loop->iteration<$empCourse->module->module_order || $attempts['attempts']>=3)
                                <a href="{{route('module', ['course' => $course->course_slug, 'module' => $module->module_slug])}}">{{$module->module_name}}</a>
                                <i class="fa fa-check-circle text-success float-right"></i>
                            @else
                                {{$module->module_name}}
                            @endif
                        </li>
                    @endforeach
                    @if($attempts['attempts']>=3)
                    <li class="list-group-item @if($attempts['attempts']>=3) bg-primary @endif">
                        <a class="text-white" href="{{ url($course->course_slug.'/'.'summary') }}"><i class="fa fa-list-ul" aria-hidden="true"></i> Summary</a>
                    </li>
                    @endif
                    <li id="view-all-button" class="list-group-item"><button class="btn btn-sm" onclick="loadAll()">View all</button></li>
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    function loadAll() {
        $('#view-all-button').remove()
        $('li.list-group-item').prop('hidden',false)
    }
</script>
@endsection