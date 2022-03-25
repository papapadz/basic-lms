@extends('layouts.admin.main')
@section('content')
    <div class="col-sm-9 col-sm-offset-3 col-md-10 col-md-offset-2 main">
        <h1 class="page-header">Test Results</h1>
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
        <br>
        <br>

        <table class="table table-striped">
            <tr>
                <th>Employee ID</th>
                <th>Name</th>
                <th>Course</th>
                <th>Results</th>
                <th></th>
            </tr>
            @forelse($results as $result)
                
                <tr>
                    <td>{{ $result->emp_id }}</td>
                    <td>{{ $result->employee->lastname }}, {{ $result->employee->firstname }} {{ $result->employee->middlename }}</td>
                    <td>{{ $result->course->course_name }}</td>
                    <td>
                        @php 
                            $is_verified = false;
                            $cert_id = null;
                            $cert_control_num = null;
                        @endphp
                        
                        @foreach($result->lists($result->course_id,$result->emp_id) as $k => $attempt)
                            <span class="label label-info">Attempt #{{ $k+1 }}: {{ $attempt->score }}%</span> 
                            <span class="label label-primary">Time:
                                @if(Carbon\Carbon::parse($attempt->start)->diffInMinutes($attempt->created_at)<=0)
                                    {{ Carbon\Carbon::parse($attempt->start)->diffInSeconds($attempt->created_at) }} seconds
                                @else
                                    {{ Carbon\Carbon::parse($attempt->start)->diffInMinutes($attempt->created_at) }} mins
                                @endif
                            </span>
                            <br>
                            @if(count($result->lists($result->course_id,$result->emp_id))==$k+1)
                                @php
                                    if($attempt->verified_at!=null && $attempt->verified_by!=null)
                                        $is_verified = true;
                                    $cert_id = $attempt->certificate->id;
                                    $cert_control_num = $attempt->certificate->control_num;
                                @endphp
                            @endif
                        @endforeach

                        @if($is_verified)
                            <span class="label label-success">Verified</span>
                        @else
                            <span class="label label-warning">Not yet verified</span>
                        @endif
                    </td>
                    <td>
                        @if($is_verified)
                            <a href="{{ route('get-certificate',$cert_id) }}">{{ $cert_control_num }}</a>
                        @else
                            <form method="POST" action="{{route('admin.results.verify', ['course_id'=>$result->course_id, 'emp_id'=>$result->emp_id])}}">
                                @csrf
                                <input type="submit" value="Verify" onclick="return confirm('Are you sure you want to verify these test results?')"
                                    class="btn btn-xs btn-success" />
                            </form>
                        @endif
                    </td>
                </tr>

            @empty
                <tr>
                    <td colspan="2">No modules found</td>
                </tr>
            @endforelse
        </table>
        <a href="{{route('admin.courses.index')}}"><button type="button" class="btn btn-light">Return to Courses</button></a>

    </div>
@endsection
