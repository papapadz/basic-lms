@extends('layouts.main')

@section('styles')
<style>
    .parent {
        display: flex;
        flex-wrap: wrap;
        }
    .child {
        flex: 1 0 15%; /* explanation below */
        margin: 5px;
        height: 100px;
    }
    .child:hover {
        background-color: green;
        color: white;
        background-image: none
    }
    .owl-carousel{
        touch-action: none;
    }
</style>
@endsection

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">{{$course->course_name}} - Online Course 
                    
                    <a class="btn border-danger btn-sm float-right mr-2" href="@if($course->is_active) {{ url('course/'.$course->course_slug) }} @else {{ route('homepage') }} @endif"><i class="fa fa-home"></i> Home</a>
                </div>

                <div class="card-body">
                    @if($course->post_notes)
                    <h4>Post Notes</h4>
                    {!! $course->post_notes !!}
                    <hr>
                    @endif
                    @if($course->is_active)
                    <h4>Module List</h4>
                    <div class="card-body" id="content">
                        <div class="parent" style="padding: 10px;">
                            @foreach($modules as $module)
                                <a class="btn border-success child" href="{{route('module', ['course' => $module->course->course_slug, 'module' => $module->module_slug])}}">
                                    {{ $module->module_name }}<br><br><b class="text-white">Revisit</b>
                                </a>
                            @endforeach
                        </div>
                    </div>
                    @endif
                </div>

                <div class="card-footer">
                    <h4>Post Test Results</h4>
                    @forelse ($attempts as $k => $attempt)
                        <li class="list-group-item">Attempt #{{ ($k+1) }} 
                            <span class="badge badge-info">Date: {{ Carbon\Carbon::parse($attempt->created_at)->toDateString() }}</span>
                            <span class="badge badge-primary">Score: {{ $attempt->score }}%</span>
                            <span class="badge badge-warning">Time: 
                                @if(Carbon\Carbon::parse($attempt->start)->diffInMinutes($attempt->created_at)<=0)
                                    {{ Carbon\Carbon::parse($attempt->start)->diffInSeconds($attempt->created_at) }} seconds
                                @else
                                    {{ Carbon\Carbon::parse($attempt->start)->diffInMinutes($attempt->created_at) }} mins
                                @endif
                            </span>
                            @if($attempt->score>=$passing[$k]->score)
                                @if($attempt->course->needs_verification)
                                    @if($attempt->verified_by!=null && $attempt->verified_at!=null)
                                        <span class="badge badge-success">Passed</span>
                                        <span class="badge badge-info"><a target="_blank" class="text-white" href="{{ url('/course/get/certificate/'.$attempt->id) }}">view certificate</a></span>
                                    @else
                                        <span class="badge badge-success">Passed - Awaiting Verification from PETRO</span>
                                    @endif
                                @else
                                    <span class="badge badge-success">Passed</span>
                                    <span class="badge badge-info"><a target="_blank" class="text-white" href="{{ url('/course/get/certificate/'.$attempt->id) }}">view certificate</a></span>
                                @endif
                            @else
                                <span class="badge badge-danger">Failed</span>
                            @endif
                        </li>
                    @empty
                    <i class="text-danger">Awaiting Results...</i>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        $('.owl-carousel').owlCarousel({
            margin: 10,
            nav: true,
            navText : ["<i class='fa fa-chevron-left'></i>","<i class='fa fa-chevron-right'></i>"],
            responsive: {
                0: {
                    items: 1
                },
                600: {
                    items: 3
                },
                1000: {
                    items: 5
                }
            }
        });
        /*get active module number */
        var get_active = $('div.image-container.active').attr('id');
        $('.owl-carousel').trigger('to.owl.carousel', get_active);
    })
</script>
@endsection