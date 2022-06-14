@extends('layouts.admin.main')
@section('content')
    <div class="col-sm-9 col-sm-offset-3 col-md-10 col-md-offset-2 main">
        <h1 class="page-header">Create New Course</h1>

        <form method="POST" action="{{route('admin.courses.store')}}" enctype="multipart/form-data">
            @csrf
            <div class="form-group">
               
                <div class="form group @error('course_name') has-error @enderror">
                    <label class="control-label" for="course_name"> Name:</label>
                    <input type="text" id="course_name" name="course_name" placeholder="ex: Name of Course" class="form-control" value="{{ old('course_name') }}"/>
                    <span class="help-block">@error('course_name') {{ $message }} @enderror</span>
                </div>
               
                <div class="form group @error('code') has-error @enderror">
                    <label class="control-label" for="code"> Code:</label>
                    <input type="text" name="code" placeholder="ex: ABC" class="form-control" value="{{ old('code') }}"/>
                    <span class="help-block">@error('code') {{ $message }} @enderror</span>
                </div>

                <div class="form group @error('course_slug') has-error @enderror">
                    <label class="control-label" for="course_slug"> Slug:</label>
                    <input type="text" id="course_slug" name="course_slug" placeholder="ex: name-of-course" class="form-control" value="{{ old('course_slug') }}"/>
                    <span class="help-block">@error('course_slug') {{ $message }} @enderror</span>
                </div>
                
                <div class="form group @error('course_description') has-error @enderror">
                    <label class="control-label" for="course_description"> Description:</label>
                    <textarea class="form-control" rows="5" placeholder="Short course description about the course" name="course_description">{{ old('course_description') }}</textarea>
                    <span class="help-block">@error('course_description') {{ $message }} @enderror</span>
                </div>

                <div class="form group @error('content') has-error @enderror">
                    <label class="control-label" for="content">Welcome Page:</label>
                    <textarea id="summernote" rows="15" name="content">{{ old('content') }}</textarea>
                    <span class="help-block">@error('content') {{ $message }} @enderror</span>
                </div>
                
                <div class="form group @error('postnote') has-error @enderror">
                    <label class="control-label" for="postnote">Post Notes:</label>
                    <textarea id="postnote" rows="15" name="postnote">{{ old('postnote') }}</textarea>
                    <span class="help-block">@error('postnote') {{ $message }} @enderror</span>
                </div>
                
                <br>
                <div class="form group @error('course_image') has-error @enderror">
                    <label class="control-label" for="course_image">Course Image:</label>
                    <input type="file" id="course_image" class="@error('course_image') is-invalid @enderror" name = "course_image" value="{{ old('course_image') }}">
                    <span class="help-block">@error('course_image') {{ $message }} @enderror</span>
                </div>
                
                <br>
                <div class="form group @error('course_certificate') has-error @enderror">
                    <label class="control-label" for="course_certificate">Course Certificate (PDF only):</label>
                    <input type="file" id="course_image" class="@error('course_certificate') is-invalid @enderror" name = "course_certificate" value="{{ old('course_certificate') }}">
                    <span class="help-block">@error('course_certificate') {{ $message }} @enderror</span>
                </div>

                <br>
                <div class="form group">
                    <label>Do you need to verify before issuing a certificate to learners?</label>
                    <div class="checkbox">
                        <label>
                          <input name="needs_verification" type="checkbox" value="1" checked>
                          Yes
                        </label>
                      </div>
                      <div class="checkbox disabled">
                        <label>
                          <input name="needs_verification" type="checkbox" value="0">
                          No
                        </label>
                      </div>
                </div>

                <br>
                <input type="submit" value="Save" class="btn btn-primary">
                <a href="{{url('admin/courses')}}"><button type="button" class="btn btn-light">Return to Courses</button></a>

            </div>
        </form>
    </div>
    <script>
        $('#summernote').summernote({
            tabsize: 2,
            height: 400
        });
        $('#postnote').summernote({
            tabsize: 2,
            height: 400
        });
    </script>
@endsection


@section('additional_scripts')
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>

<script>





    $(document).ready(function() {
        
        $('#course_name').on("input", function(e) {
        var str = $("#course_name").val();
        str = str.replace(/[^a-zA-Z0-9\s]/g,"");
        str = str.toLowerCase();
        str = str.replace(/\s/g,'-');
        $("#course_slug").val(str);

        });

        $("#course_slug").on("click", function (e) {
        $("#course_slug").attr("readonly", false);
        });
    });
</script>
@endsection