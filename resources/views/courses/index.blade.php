@extends('layouts.main')

@section('styles')
<style>

    .video-responsive{
        overflow:hidden;
        padding-bottom:56.25%;
        position:relative;
        height:0;
    }
    .video-responsive iframe{
        left:0;
        top:0;
        height:100%;
        width:100%;
        position:absolute;
    }
    /* #content, #sidecontent{
        min-height: 50vh;
    } */

    /*center slate to center and make 240, 140px */
    .lesson-scroller-item {
        max-height: 180px;
        max-width: 240px;
        margin: 0 auto;
    }
            
    div.image-container.active {
        border: 5px;
        border-style: solid;
        border-color: #80A441;
    }
    
    .not-active {
        text-decoration: none;
        color: black;
    }
            
    a:hover{
        color: black;
        text-decoration: none;
    }

    .owl-carousel{
        touch-action: none;
    }
</style>
@endsection

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col">
            <div class="card">
                <div class="card-header">
                    {{$course->course_name}} - Online Course 
                    <b class="text-center" style="font-size: 20px">{{$module->module_name}}</b>
                    <a class="btn border-danger btn-sm float-right" href="{{ url('course/'.$course->course_slug) }}"><i class="fa fa-folder-open"></i> Parent Folder</a></div>
                @if(!is_null($module->video_url) and $module->module_type =='video')
                    <div class="video-responsive">
                        <iframe sandbox="allow-same-origin allow-scripts allow-forms" src="{{$module->video_url}}?rel=0"  frameborder="0" allowfullscreen></iframe>
                    </div>
                @elseif($module->module_type =='exam')
                    @include('courses.exam')
                @else
                    <div class="card-body">

                        <div class="card-body" id="content">
                            {!! $module->module_content !!}
                        </div>
                    </div>
                @endif
            </div>
            <br>
        </div>
        <div class="col-md-3 col-lg-4">
            <div class="card">
                <div class="card-header">Up Next</div>
                <div class="card-body" id="sidecontent">
                    <div class="container lesson-footer">
                        <div class="owl-carousel">
    
                            @foreach ($modules as $module)
                            <a href="{{route('module', ['course' => $course->course_slug, 'module' => $module->module_slug])}}" class="not-active">
                                <div class="lesson-title">
                                    <strong><span class="badge badge-info text-uppercase float-right">{!! $module->module_name !!}</span></strong>
                                </div>
                                <div class="lesson-scroller-item">
                                    <div class="image-container {{ (request()->is('*/'. $module->module_slug)) ? 'active' : '' }}" id="{{ $loop->iteration }}">
                                        
                                        @if($module->module_image)      
                                        <img src="{{ asset('images/modules/'.$module->module_image) }}" alt="">
                                        @else
                                        <img src="https://via.placeholder.com/250x140?text={{$module->module_name}}" alt="">
                                        @endif
                                    </div>
                                </div>
                            </a>
                            @endforeach
                            
                            <a href="{{url('/')}}/{{$course->course_slug}}/summary" class="not-active">
                                <div class="lesson-title">
                                    <strong><span class="badge badge-info text-uppercase float-right">Summary</span></strong>
                                </div>
                                <div class="lesson-scroller-item"><div class="image-container" id="summary"><img src="{{url('/images/summary.png')}}" alt=""></div>
                            </div></a>
                            
    
                        </div>
    
                    </div>
                </div>
            </div>
        </div>
        {{-- <hr>
        <div class="col-md-12">
            <hr>
            <div class="row">
                
            </div>
        </div> --}}
    </div>
</div>
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        owl = $('.owl-carousel').owlCarousel({
            margin: 10,
            items: 1
            // nav: true,
            // navText : ["<i class='fa fa-chevron-left'></i>","<i class='fa fa-chevron-right'></i>"],
            // responsive: {
            //     0: {
            //         items: 1
            //     },
            //     600: {
            //         items: 3
            //     },
            //     1000: {
            //         items: 5
            //     },
            // }
        });

        /*get active module number */
        var get_active = $('div.image-container.active').attr('id');
        $('.owl-carousel').trigger('to.owl.carousel', get_active);
    })

</script>
@endsection