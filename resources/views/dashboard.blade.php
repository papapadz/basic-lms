@extends('layouts.main')

@section('styles')
<style>
    .jumbotron{
        background-image: url("{{asset('images/new-bg.jpg')}}");
        background-size: cover;
        height: 100%;
        }
    #footer {
        position: relative;
    }
</style>
@endsection

@section('content')
<main role="main">
    @include('components.validation')
    <section class="jumbotron text-center">
            <div class="container">
                <font color="white"><h1 class="jumbotron-heading">Learning Management System</h1>
                <p class="lead text-muted"></p>
                <p>The Learning Management System of Mariano Marcos Memorial Hospital and Medical Center provides employees with virtual and online learning experience. It provides them with learning materials anywhere and with ease. Select any course available and start learning.</p>
                </font>
            </div>
        </section>
        <div class="album py-5 bg-light">
            <div class="container">
                <div class="row">
                @forelse($courses as $course)
                    <div class="col-md-4">
                        <div class="card mb-4 shadow-sm">
                            <a href="course/{{$course->course_slug}}"><img src="@if($course->course_image) {{ asset('images/courses/'.$course->course_image) }} @else {{asset('images/noimage.jpg')}} @endif" width="100%" height="225"/></a>
                            <div class="card-body">
                                <h4><a href="course/{{$course->course_slug}}">{{$course->course_name}}</a></h4>
                                <p class="card-text">{{$course->course_description}}
                                </p>
                                
                                @if($course->is_active)
                                    @php
                                        $empCourse = Auth::user()->employeeCourses->where('course_id', $course->id)->first();
                                    @endphp  
                                    @if($empCourse)
                                        @if($empCourse->finished_date)
                                            <a href="course/{{$course->course_slug}}"><button class="btn btn-info col-md-12">View Course</button></a>
                                        @else
                                            <a href="course/{{$course->course_slug}}"><button class="btn btn-success col-md-12">Resume Course</button></a>
                                        @endif
                                    @else
                                        <a href="course/{{$course->course_slug}}"><button class="btn btn-info col-md-12">View Course</button></a>
                                    @endif
                                @else
                                    <button class="btn btn-warning col-md-12" disabled>Coming Soon!</button>
                                @endif
                                
                            </div>
                        </div>
                    </div>
                    @empty
                        <tr>
                            <td colspan="2">No courses found</td>
                        </tr>
                    @endforelse



                </div>
            </div>
        </div>
</main>
@endsection
