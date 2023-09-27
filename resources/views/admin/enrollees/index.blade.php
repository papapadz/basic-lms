@extends('layouts.admin.main')
@section('content')
    <div class="col-sm-9 col-sm-offset-3 col-md-10 col-md-offset-2 main">
        <h1 class="page-header">Enrollees of {{$course->course_name}}</h1>
        <style>
            .float-right {
                float: right !important;

            . row > & {
                margin-left: auto !important;
            }
            }
        </style>
                @include('components.validation')

        <table id="myTable" class="table table-striped">
           <thead>
                <th>Employee ID</th>
                <th>Name</th>
                <th>Department</th>
                <th>Date Started</th>
                <th>Date Finished</th>
                <th>Status</th>
                {{-- <th>Action</th> --}}
            </thead>
            <tbody>
                @foreach($coursEnrollees as $k => $enrollee)
                    @if($enrollee->employee)
                    <tr>
                        <td>{{$enrollee->employee->emp_id}}</td>
                        <td>
                            <a href="{{ url('admin/enrollees/info/'.$enrollee->id) }}">{{ $enrollee->employee->lastname }}, {{ $enrollee->employee->firstname }} {{ $enrollee->employee->middlename }}</a>
                        </td>
                        <td>{{ $enrollee->employee->department ? $enrollee->employee->department->department : '' }}</td>
                        <td>{{ Carbon\Carbon::parse($enrollee->created_at)->toDateString() }}</td>
                        <td>{{ $enrollee->finished_date ? Carbon\Carbon::parse($enrollee->finished_date)->toDateString() : '-' }}</td>
                        <td>
                            @if($enrollee->finished_date)
                                <span class="badge badge-success">Completed</span>
                            @else
                                @php
                                    $progress = floor(($enrollee->module->module_order/count($course->modules))*100);
                                @endphp
                                                                
                                @if($progress<100)
                                    <div class="progress">
                                        <div class="progress-bar" role="progressbar" aria-valuenow="{{$progress}}" aria-valuemin="0" aria-valuemax="100" style="width: {{$progress}}%;">
                                        {{ $progress }}%
                                        </div>
                                    </div>
                                @else
                                    @php 
                                        $ctr = 0;
                                        $passed = false;
                                        $employeeQuizzes = $enrollee->quiz->where('quiz_type','post');
                                    @endphp
                                    @if(count($employeeQuizzes)>0)
                                        @foreach($employeeQuizzes as $quiz)
                                            @php 
                                                $ctr++;
                                                $passingScore = $enrollee->course->passingRates->where('exam_type','post')->where('attempt',$ctr)->first();
                                            @endphp
                                            
                                            @if($passingScore)
                                                @if($quiz->score>=$passingScore->score)
                                                    @php $passed = true; @endphp
                                                @endif
                                            @endif
                                        @endforeach   
                                        @if($passed)
                                            <span class="badge badge-success">Post Tests Passed - Awaiting Verification</span>
                                        @else
                                            <span class="badge badge-danger">Failed</span>
                                        @endif
                                    @elseif(count($enrollee->course->quizzes->where('quiz_type','post'))==0) 
                                        @php $certCounter = 0; @endphp
                                        @foreach($enrollee->quiz->where('course_id',$course->id) as $enrolleeQuiz)
                                            @if($enrolleeQuiz)
                                                @if($enrolleeQuiz->certificate)
                                                    @php $certCounter++; @endphp
                                                    <a href="{{ route('get-certificate',$enrolleeQuiz->certificate->id) }}">{{ $enrolleeQuiz->certificate->control_num }}</a>
                                                @endif
                                            @endif
                                        @endforeach
                                        @if($certCounter==0)
                                            <button onclick="showReleaseForm('{{ $enrollee->emp_id }}','{{ $enrollee->id }}')" class="btn btn-xs btn-warning">Release Certificate</button>
                                        @endif  
                                    @else
                                        <span class="badge badge-warning">No Post Test Attempts yet</span>
                                    @endif
                                @endif
                            @endif
                        </td>
                        {{-- <td>
                            @if($enrollee->finished_date || ($enrollee->module->module_order/count($course->modules))==1)
                                @if($course->modules->where('module_type','post')->first())
                                    @if(count($enrollee->quiz->where('course_id',$course->id))>0)
                                        @php $certCounter = 0; @endphp
                                        @foreach($enrollee->quiz->where('course_id',$course->id) as $enrolleeQuiz)
                                            @if($enrolleeQuiz)
                                                @if($enrolleeQuiz->certificate)
                                                    @php $certCounter++; @endphp
                                                    <a href="{{ route('get-certificate',$enrolleeQuiz->certificate->id) }}">{{ $enrolleeQuiz->certificate->control_num }}</a>
                                                @endif
                                            @endif
                                        @endforeach
                                        @if($certCounter==0)
                                            <span class="text-danger"> <i>Not yet passed</i></span>
                                        @endif
                                    @else
                                        <span class="text-warning"> <i>No Post Test Attempt yet</i></span>
                                    @endif
                                @elseif($course->needs_verification) 
                                    @php $certCounter = 0; @endphp
                                    @foreach($enrollee->quiz->where('course_id',$course->id) as $enrolleeQuiz)
                                        @if($enrolleeQuiz)
                                            @if($enrolleeQuiz->certificate)
                                                @php $certCounter++; @endphp
                                                <a href="{{ route('get-certificate',$enrolleeQuiz->certificate->id) }}">{{ $enrolleeQuiz->certificate->control_num }}</a>
                                            @endif
                                        @endif
                                    @endforeach
                                    @if($certCounter==0)
                                        <button onclick="showReleaseForm('{{ $enrollee->emp_id }}','{{ $enrollee->course_id }}')" class="btn btn-xs btn-success">Release Certificate</button>
                                    @endif
                                @endif

                            @else
                                <span class="text-danger"> <i>Pending...</i></span>
                            @endif
                        </td> --}}
                    </tr>
                    @endif
                @endforeach
            </tbody>
        </table>
    </div>
@endsection

@section('additional_scripts')
<script>
    $('#myTable').DataTable();
function setActive(id) {
    $.ajax({
        method: 'post',
        url: '{{ url("admin/course/set/active") }}',
        data: {_token:'{{ csrf_token() }}', id:id}
    }).done(function(response) {
        location.reload();
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
