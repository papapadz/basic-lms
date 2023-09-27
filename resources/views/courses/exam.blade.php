<div class="col-12 mb-2">
    @if((count($attempts)>=count($passing)) || $passed)
    <br>
        {{-- @if($passed)
            <div class="alert alert-success">View your Certificate of Completion <strong><a target="_blank" href="{{ url('/course/get/certificate/'.$course->id) }}">here.</a></strong></div>
        @endif 
        <div class="alert alert-warning">You have already exhausted your quiz attempts</div> --}}
        <ul class="list-group">
            @foreach ($attempts as $k => $attempt)
                <li class="list-group-item">Attempt #{{ ($k+1) }} 
                    <span class="badge badge-info">Date: {{ Carbon\Carbon::parse($attempt->created_at)->toDateString() }}</span>
                    <span class="badge badge-primary">Score: {{ $attempt->score }}%</span>
                    <span class="badge badge-warning">Time: {{ Carbon\Carbon::parse($attempt->start)->diffInMinutes($attempt->created_at) }} mins</span>
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
                            @elseif($module->module_type=='post')
                                <span class="badge badge-danger">Failed</span>
                            @endif
                </li>
            @endforeach
          </ul>
    @else
        <form id="exam">
            @csrf
            <input type="text" name="time_start" value="{{ Carbon\Carbon::now()->toDateTimeString() }}" hidden>
            <input type="text" name="emp_course_id" value="{{ $empCourse->id }}" hidden>
            <input type="text" name="course_id" value="{{ $course->id }}" hidden>
            <input type="text" name="quiz_type" value="{{ $module->module_type }}" hidden>
            @foreach($questions as $k => $question)
                <div id="{{ $question->id }}">
                    <label>{{$k+1}}. {{ $question->question }}</label>
                    <br>
                    @foreach($question->choices as $choice)
                        <div class="form-row col-12 ml-4">
                            <input class="form-check-input @if($choice->is_correct) r-success @endif" type="radio" name="q{{ $question->id }}" value="{{ $choice->id }}">
                            <label id="label{{ $choice->id }}" class="form-check-label @if($choice->is_correct) label-success @endif">{{ $choice->choice }}
                        </div>
                    @endforeach
                    <hr>
                </div>
            @endforeach
        </form>
        <button id="submit-button" type="button" onclick="submit()" class="btn btn-primary">Submit</button>
        <span class="float-left" id="span-score"></span>
        <span class="float-right">Attempts: <h4><b id="span-attempt">{{ count($attempts) }}/{{ count($passing) }}</b></h4></span>    
    @endif
</div>
<script>
var parentDiv = [];
$(document).ready(function() {
    
    $("#exam > div ").each((index, elem) => {
        parentDiv.push(elem.id);
    });

    console.log(parentDiv);
})

function submit() {
    
    Swal.fire({
        title: 'Are you sure you want to save your answers?',
        text: 'Once submitted, it shall be automatically recorded.',
        icon: 'warning',
        showDenyButton: true,
        confirmButtonText: `Save`,
        denyButtonText: `Don't save`,
        }).then((result) => {
        /* Read more about isConfirmed, isDenied below */
        if (result.isConfirmed) {
            save()
        }
    })
}

function save() {
    var form = $('#exam').serialize()+'&questions='+JSON.stringify(parentDiv)

    $.ajax({
        method:'post',
        url: "{{ route('quiz.submit') }}",
        data: form
    }).done(function(response) {
        
        //disable input
        $('#submit-button').remove()
        $("#exam :input").prop("disabled", true);

        //red all wrong answers
        $("#exam input[type=radio]:checked").each(function() {
        if(this.checked == true) {
            rclass = this.className
            if(!rclass.includes('r-success')) {
                $('#label'+this.value).css({'color': 'red'})
                $('#label'+this.value).append('<span class="badge badge-danger"><i class="fa fa-times"></i></span>')
            }
        }
        })
        
        //show correct anwers
        $('label.label-success').append('<span class="badge badge-success">Correct Answer</span>')
        $('label.label-success').css({'color':'green'})

        Swal.fire({
            position: 'top-center',
            icon: 'success',
            title: 'Congratulations, you got '+parseFloat(response).toFixed(2)+'%! You may proceed to the next module now.',
            showConfirmButton: false,
            timer: 5000
        })
        // percentage = Number(response/parentDiv.length) * 100
        attempts = Number('{{ count($attempts) }}')+1
        passing = Number('{{ count($passing) }}')
        btnTryAgain = ''
        if(attempts<passing)
            btnTryAgain = '<button class="btn btn-xs btn-warning" onclick="location.reload()">Try Again</button>'
        $('#span-score').append('Score: <h4><b>'+parseFloat(response).toFixed(2)+'%</b></h4>'+btnTryAgain)
        document.getElementById("span-attempt").innerHTML = attempts + '/' + passing
    });
}
</script>
