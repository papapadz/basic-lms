@extends('layouts.admin.main')
@section('content')
    <div class="col-sm-9 col-sm-offset-3 col-md-10 col-md-offset-2 main">
        <h1 class="page-header">Courses</h1>
        <style>
            .float-right {
                float: right !important;

            . row > & {
                margin-left: auto !important;
            }
            }
        </style>
                @include('components.validation')

        <br/>
        @if(Auth::User()->role==1)
        <a href="{{route('admin.courses.create')}}" class="btn float-right btn-primary">Create New Course</a>   
        <br/>
        <br>
        <br>
        @endif
        <table class="table table-striped">
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Number of Enrollees</th>
                <th>Action</th>
            </tr>
            @forelse($courses as $course)

                <tr>
                    <td>{{ $course->id }}</td>
                    <td><a href="{{route('course', ['course' => $course->course_slug])}}" target="_blank">{{$course->course_name}}</a></td>
                    <td>{{ count($course->enrollees) }}</td>
                    <td>
                        <a href="{{ route('admin.courses.edit', $course->id) }}" class="btn btn-xs btn-primary">Edit</a>
                        <a href="{{url('admin/modules?course_id=')}}{{$course->id}}" class="btn btn-xs btn-warning">Edit Modules</a>
                        <form method="POST" action="{{route('admin.courses.destroy', $course->id)}}">
                            @csrf
                            {{method_field('DELETE')}}
                            <input type="submit" value="Delete" onclick="return confirm('Are you sure you want to delete this course?')"
                                   class="btn btn-xs btn-danger" />
                        </form>
                        <a class="btn btn-xs btn-info" href="{{ route('admin.enrollees.index', $course->id) }}">View Enrollees</a>
                        @if($course->is_active)
                            <button class="btn btn-xs btn-disabled" onclick="setActive('{{$course->id}}')">Deactivate</button>
                        @else
                            <button class="btn btn-xs btn-success" onclick="setActive('{{$course->id}}')">Activate</button>
                        @endif
                    </td>
                </tr>

            @empty
                <tr>
                    <td colspan="2">No courses found</td>
                </tr>
            @endforelse
        </table>
    </div>
@endsection

@section('additional_scripts')
<script>

function setActive(id) {
    $.ajax({
        method: 'post',
        url: '{{ url("admin/course/set/active") }}',
        data: {_token:'{{ csrf_token() }}', id:id}
    }).done(function(response) {
        location.reload();
    })
}
</script>
@endsection
