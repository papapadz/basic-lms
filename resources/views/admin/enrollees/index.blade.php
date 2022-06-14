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

        <br/>
        <a href="{{route('admin.courses.create')}}" class="btn float-right btn-primary">Create New Course</a>
        <br/>
        <br>
        <br>

        <table class="table table-striped">
            <tr>
                <th>#</th>
                <th>Employee ID</th>
                <th>Name</th>
                <th>Date Started</th>
                <th>Date Finished</th>
                <th>Status</th>
                <th>Action</th>
            </tr>
            @forelse($course->enrollees as $k => $enrollee)
                <tr>
                    <td>{{ $k+1 }}</td>
                    <td>{{ $enrollee->emp_id }}</td>
                    <td>{{ $enrollee->employee->lastname }}, {{ $enrollee->employee->firstname }} {{ $enrollee->employee->middlename }}</td>
                    <td>{{ $enrollee->created_at }}</td>
                    <td>{{ $enrollee->finished_date ? $enrollee->finished_date : '-' }}</td>
                    <td>
                        @if($enrollee->finished_date || ($enrollee->module->module_order/count($enrollee->course->modules))==1)
                            <span class="btn btn-xs btn-success">Completed</span>
                        @else
                        <div class="progress">
                            <div class="progress-bar" role="progressbar" aria-valuenow="{{($enrollee->module->module_order/count($course->modules))*100}}" aria-valuemin="0" aria-valuemax="100" style="width: {{($enrollee->module->module_order/count($course->modules))*100}}%;">
                                {{floor(($enrollee->module->module_order/count($course->modules))*100)}}%
                            </div>
                          </div>
                        @endif
                    </td>
                    <td>
                        @if($enrollee->finished_date && ($enrollee->module->module_order/count($course->modules))==1)
                            
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
                                <button onclick="showReleaseForm({{ $enrollee->emp_id }},{{ $enrollee->course_id }})" class="btn btn-xs btn-success">Release Certificate</button>
                            @endif

                        @else
                            <span class="text-danger"> <i>Pending...</i></span>
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
