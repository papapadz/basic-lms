@extends('layouts.admin.main')
@section('content')
    <div class="col-sm-9 col-sm-offset-3 col-md-10 col-md-offset-2 main">

        <h1 class="page-header">Create New Module</h1>
        @include('components.validation')

        <form method="POST" action="{{route('admin.modules.store')}}" enctype="multipart/form-data">
            @csrf
            <div class="form-group">
                <input type="hidden" id="course_id" name="course_id" value ="{{ app('request')->input('course_id') }}" class="form-control"/>
                    
                Module Type:
                <select class="form-control" id="module_type" name="module_type">
                    <option value="text">Text</option>
                    <option value="video">Video</option>
                    <option value="pre">Pre Test</option>
                    <option value="post">Post Test</option>
                    <option value="link">Link</option>
                </select>
                <div class="textvidonly">
                    Name:
                    <input type="text" id="module_name" name="module_name" placeholder="Name of Module" class="form-control"/>
                    Slug:
                    <input type="text" id="module_slug" name="module_slug" placeholder="module-slug-test-1" class="form-control"/>
                    <div id="text" class="group">
                        Content:
                        <textarea id="summernote" rows="15" name="module_content"></textarea>
                    </div>
                    <div id="video" class="group">
                        Video Embed URL: eg; https://player.vimeo.com/video/8733915 or https://www.youtube.com/embed/6p_yaNFSYao
                        <input type="text" name="video_url" placeholder="e.g.; https://www.youtube.com/embed/6p_yaNFSYao" class="form-control"/>
                    </div>
                    </div>
                    <div class="group testonly">
                        <hr>
                        <div class="form-group">
                            <div class="input-group">
                            <input type="number" id="passing_rate" min="0" max="100" step="0.01" class="form-control" placeholder="Passing Rate must be 0-100 %">
                            <div class="input-group-addon">%</div>
                            </div>
                        </div>
                    <div class="list-group">
                        {{-- <li class="list-group-item">
                          <h4 class="list-group-item-heading">Question #1</h4>
                          <p class="list-group-item-text">
                            <div class="row">
                                <div class="col-md-10">
                                    <input onchange="onChangeTextQ()" type="text" class="form-control handleOnChangeQ Q1" id="preQ1" placeholder="Type a Question"/>
                                </div>
                                <div class="col-md-2">
                                    <div class="pull-left">
                                        <button type="button" class="btn btn-success Q1" onclick="addC()">Add choices</button>
                                    </div>
                                </div>
                            </div> --}}
                            {{-- <div id="preQappendChoices1">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-inline">
                                            <div class="form-group">
                                            <div class="input-group">
                                                <div class="input-group-addon">
                                                    <div class="radio">
                                                        <label>
                                                        <input type="radio" onchange="onSetCorrectOption()" name="optionQ1" id="isCorrectOptionQ1C1" value="1">
                                                        </label>
                                                    </div>
                                                </div>
                                                <input type="text" class="form-control Q1" onchange="onChangeTextC()" id="optionQ1C1" placeholder="Type a choice">
                                            </div>
                                            </div>
                                            <button type="button" class="btn btn-danger">X</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                          </p>
                        </li> --}}
                        <div id="preQappendTo"></div>
                        <div href="#" class="btn btn-success list-group-item" onclick="addQ()">
                            <h4 class="list-group-item-heading text-success">Add</h4>
                        </div>
                    </div>
                    
                </div>
                <div class="textvidonly">
                    Module Image:
                    <br>
                    <input type="file" id="module_image" name = "module_image">
                </div>
                <br>
                <input type="submit" value="Save" class="btn btn-primary textvidonly">
                <button type="button" class="btn btn-primary testonly" onclick="saveQ()">Save</button>
                <a href="{{url('admin/modules?course_id=')}}{{ app('request')->input('course_id') }}"><button type="button" class="btn btn-light">Return to Modules</button></a>
            </div>
        </form>
        <script>
            $('#summernote').summernote({
                tabsize: 2,
                height: 400
            });
        </script>
    </div>
@endsection


@section('additional_scripts')
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>

<script>
    

    var questions = []

    function addQ() {

        $('#delButtonQ_'+questions.length).hide()

        var numx=Math.floor(this.event.timeStamp)
        $('#preQappendTo').append(
            '<li id="liQ_'+(questions.length+1)+'" class="list-group-item"><h4 class="list-group-item-heading">Question #'+(questions.length+1)+'<span class="pull-right"><button class="btn btn-xs btn-outline" onclick="delQ()" id="delButtonQ_'+(questions.length+1)+'" type="button">X</button></span></h4><p class="list-group-item-text"><div class="row"><div class="col-md-10">'+
                '<input onchange="onChangeTextQ('+numx+')" type="text" class="form-control Q_'+numx+'" id="'+numx+'" placeholder="Type a Question"/></div><div class="col-md-2"><div class="pull-left">'+
                '<button type="button" class="btn btn-success" onclick="addC('+numx+')">Add choices</button></div></div></div>'+
                '<div id="preQappendChoices_'+numx+'"></div></p></li>'
        )

        questions.push({
            text: null,
            id: numx,
            choices: []
        })
    }

    function addC(id) {

        numx=id
        for(key in questions) {
            if(questions[key].id==numx) {
                numy = Math.floor(this.event.timeStamp)
                questions[key].choices.push({
                    id: numy,
                    text: null,
                    isCorrect: 0
                })
                break
            }
        }
        $('#preQappendChoices_'+numx).append(
            '<div id="rowC_'+numy+'" class="row"><div class="col-md-12"><div class="form-inline"><div class="form-group">'+
                '<div class="input-group"><div class="input-group-addon"><div class="radio"><label>'+
                    '<input onchange="onSetCorrectOption('+numy+')" type="radio" name="optionQ_'+numx+'"></label> </div></div>'+
                    '<input type="text" class="form-control Q_'+numx+'" onchange="onChangeTextC('+numy+')" id="'+numy+'" placeholder="Type a choice"></div></div>'+
                    '<button type="button" class="btn btn-danger Q_'+numx+'" onclick="delC('+numy+')">X</button></div></div></div>'
        )
        console.log(questions)
    }
    
    function onChangeTextQ(id) {
        
        for(key in questions)
            if(questions[key].id==id) {
                questions[key].text = this.event.target.value
                break
            } 
    }

    function onChangeTextC(id) {

        numx = this.event.srcElement.className.split('_')[1]
        numy = id
        
        for(key in questions)
            if(questions[key].id==numx) {
                for(index in questions[key].choices)
                    if(questions[key].choices[index].id==numy) {
                        questions[key].choices[index].text = this.event.target.value
                        break
                    }
                break
            }
    }

    function onSetCorrectOption(id) {
        numx = this.event.srcElement.name.split('_')[1]
        numy = id
        
        for(key in questions)
            if(questions[key].id==numx) {
                for(index in questions[key].choices)
                    if(questions[key].choices[index].id==numy) {
                        questions[key].choices[index].isCorrect = 1
                    } else
                        questions[key].choices[index].isCorrect = 0
                break
            }
    }

    function delQ() {
        $('#delButtonQ_'+(questions.length-1)).show()
        $('#liQ_'+questions.length).remove()
        questions.pop()
    }

    function delC(id) {
        numx = this.event.srcElement.className.split('_')[1]
        numy = id

        for(key in questions)
            if(questions[key].id==numx) {
                for(index in questions[key].choices)
                    if(questions[key].choices[index].id==numy) {
                        questions[key].choices.splice(index,1)
                    }
                break
            }
        $('#rowC_'+numy).remove()
    }

    function saveQ() {
        
        module_name = 'Pre Test'
        module_slug = 'pre-test'
        exam_type = 'pre'
        if($('#module_type').val()=='post') {
            module_name = 'Post Test'
            module_slug = 'post-test'
            exam_type = 'post'
        }
        
        $.ajax({
            method: 'post',
            url: '{{ url("admin/modules") }}',
            data: {
                _token: '{{ csrf_token() }}',
                questions: questions,
                course_id: $('#course_id').val(),
                passing_rate: $('#passing_rate').val(),
                module_name: module_name,
                module_slug: module_slug,
                exam_type: exam_type,
                module_type: $('#module_type').val(),
                video_url: null,
                module_content: null
            }
        }).done(function(response) {
            window.location.href = '{{ url("admin/modules?course_id=") }}'+$('#course_id').val();
        })
    }

    $(document).ready(function() {
        
        $('.group').hide();
        $('.testonly').hide();
        $('#text').show();
        $('#module_type').change(function () {
            $('.group').hide();

            if($(this).val()=="pre" || $(this).val()=="post") {
                $('.textvidonly').hide();
                $('.testonly').show();
            } else {
                $('#'+$(this).val()).show();
                $('.textvidonly').show();
                $('.testonly').hide();
            }
        })

        $('#module_name').on("input", function(e) {
            var str = $("#module_name").val();
            str = str.replace(/[^a-zA-Z0-9\s]/g,"");
            str = str.toLowerCase();
            str = str.replace(/\s/g,'-');
            $("#module_slug").val(str);

        });

        $("#module_slug").on("click", function (e) {
            $("#module_slug").attr("readonly", false);
        });
    });
</script>
@endsection