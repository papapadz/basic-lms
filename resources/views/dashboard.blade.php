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
                <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Integer nisi metus, accumsan a tellus eu, dictum porta quam. Mauris vitae nisi vel turpis faucibus congue non vel justo.</p>
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
                              
                                @if(count($course->modules)>=1 && $course->is_active)
                                <a href="course/{{$course->course_slug}}"><button class="btn btn-info col-md-12">View Course</button></a>
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
