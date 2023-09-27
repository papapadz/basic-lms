@extends('layouts.admin.main')
@section('content')
<style>
    .float-right {
        float: right !important;

    . row > & {
        margin-left: auto !important;
    }
    }
</style>
    <div class="col-sm-9 col-sm-offset-3 col-md-10 col-md-offset-2 main">
        @include('components.validation')
        <h1 class="page-header">{{ $employeeCourse->employee->lastname }}, {{ $employeeCourse->employee->firstname }} {{ $employeeCourse->employee->middlename }}</h1>
        <div class="panel panel-info">
            <div class="panel-heading">
                <h3>{{ $employeeCourse->course->course_name }} <button class="btn btn-danger float-right" onclick="showAlertMessage()"> <span class="glyphicon glyphicon-repeat" aria-hidden="true"></span> Reset</button></h3>
            </div>
            <div class="panel-body">
                  <div class="panel panel-danger">
                    <!-- Default panel contents -->
                    <div class="panel-heading">History</div>
                    <!-- List group -->
                    <ul class="list-group">
                        @foreach($history as $empCourse)
                            <li class="list-group-item">
                                <h3>{{ Carbon\Carbon::parse($empCourse->created_at)->toFormattedDateString() }}</h3>
                                <table class="table">
                                    <th>#</th>
                                    <th>Date</th>
                                    <th>Score (Passing)</th>
                                    <th>Time</th>
                                    <th>Remarks</th>
                                    <th>Action</th>
                                    @php $attempts = $empCourse->quiz->where('quiz_type','post'); $ctr = 0; @endphp
                                    @foreach($attempts as $attempt)
                                        @php 
                                            $ctr++;
                                            $passingScore = $passing->where('attempt',$ctr)->first(); 
                                        @endphp
                                        @if($passingScore)
                                            <tr>
                                                <td>{{ $ctr }}</td>
                                                <td>{{ Carbon\Carbon::parse($attempt->created_at)->toDateString() }}</td>
                                                <td>
                                                    {{ $attempt->score }}% (>={{$passingScore->score}}%)
                                                </td>
                                                <td>
                                                    @if($attempt->course->course->modules->where('module_type','post')->first())
                                                        @if(Carbon\Carbon::parse($attempt->start)->diffInMinutes($attempt->created_at)<=0)
                                                            {{ Carbon\Carbon::parse($attempt->start)->diffInSeconds($attempt->created_at) }} seconds
                                                        @else
                                                            {{ Carbon\Carbon::parse($attempt->start)->diffInMinutes($attempt->created_at) }} mins
                                                        @endif
                                                    @endif
                                                </td>
                                                <td>
                                                    @php $passedStatus = 0; @endphp
                                                    @if($attempt->score>=$passingScore->score)
                                                        @if($attempt->course->course->needs_verification)
                                                            @if($attempt->verified_by && $attempt->verified_at)
                                                                @php $passedStatus = 2 @endphp
                                                                <span class="badge badge-success">Passed</span>
                                                            @else
                                                                <span class="badge badge-success">Passed - Awaiting Verification</span>
                                                                @php $passedStatus = 1 @endphp
                                                            @endif
                                                        @else
                                                            @php $passedStatus = 2 @endphp
                                                            <span class="badge badge-success">Passed</span>
                                                        @endif
                                                    @else
                                                        @php $passedStatus = 0 @endphp
                                                        <span class="badge badge-danger">Failed</span>
                                                    @endif    
                                                </td>
                                                <td>
                                                    @if($attempt->course->finished_date)
                                                        @if($passedStatus == 2)
                                                        <a target="_blank" class="btn btn-xs btn-primary" href="{{ url('/course/get/certificate/'.$attempt->certificate->id) }}">view certificate</a>
                                                        @elseif($passedStatus == 1)
                                                            @if((Auth::User()->courseReviewer->where('course_id',$attempt->course->course->id)->first() || Auth::User()->role == 1))
                                                            <button onclick="verify({{$attempt->id}})" class="btn btn-xs btn-success">Verify</button>
                                                            @endif
                                                        @endif
                                                    @else
                                                        @if($passedStatus == 1 || $passedStatus == 2)
                                                            @if((Auth::User()->courseReviewer->where('course_id',$attempt->course->course->id)->first() || Auth::User()->role == 1))
                                                            <button onclick="verify({{$attempt->id}})" class="btn btn-xs btn-success">Verify</button>
                                                            @endif
                                                        @endif
                                                    @endif
                                                </td>
                                            </tr>
                                        @endif
                                    @endforeach
                                </table>
                            </li>
                        @endforeach
                    </ul>
                  </div>
            </div>
          </div>
          
        
    </div>
@endsection

@section('additional_scripts')
<script>
function showAlertMessage() {
    Swal.fire({
        title: 'Are you sure you want reset the employees progress?',
        showDenyButton: true,
        confirmButtonText: 'Yes, Reset',
        denyButtonText: 'Cancel',
    }).then((result) => {
    /* Read more about isConfirmed, isDenied below */
        if (result.isConfirmed) {
            location.replace("{{ url('admin/enrollees/course/delete/'.$employeeCourse->id) }}");
        }
    })
}

function verify(id) {
    Swal.fire({
        title: 'Are you sure you want verify this employees score?',
        showDenyButton: true,
        confirmButtonText: 'Yes, Verify Score',
        denyButtonText: 'Cancel',
    }).then((result) => {
    /* Read more about isConfirmed, isDenied below */
        if (result.isConfirmed) {
            $.ajax({
                url: '{{ url("admin/results/verify/quiz") }}/'+id
            }).then((result) => {
                console.log(result)
            })
        }
    })
}

function showReleaseForm(emp_id,course_id) {
    
    Swal.fire({
  title: 'Enter score (1-100)',
  input: 'number',
  inputAttributes: {
    autocapitalize: 'off'
  },
  showCancelButton: true,
  confirmButtonText: 'Generate',
  showLoaderOnConfirm: true,
  preConfirm: (score) => {
    
    return $.ajax({
        url: '{{ url("admin/generate/certificate") }}',
        method: 'post',
        data: {
            _token: '{{ csrf_token() }}', emp_id: emp_id, course_id: course_id, score: score
        }
    }).done(function() {
        Swal.fire({
            position: 'top-end',
            icon: 'success',
            title: 'Certificate has been generated!',
            showConfirmButton: false,
            timer: 1500
        })
    }).error(function() {
        Swal.fire({
            position: 'top-end',
            icon: 'error',
            title: 'Please enter a valid input from 1 to 100!',
            showConfirmButton: false,
            timer: 1500
        })
    })
  },
  allowOutsideClick: () => !Swal.isLoading()
}).then((result) => {
  if (result.isConfirmed) {
    location.reload()
  }
})
}
</script>
@endsection
