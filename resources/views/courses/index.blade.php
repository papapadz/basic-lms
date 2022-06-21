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
<br>
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
                    <div class="badge badge-info url_not_working" style="display: none">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-question-circle" viewBox="0 0 16 16">
                            <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z"/>
                            <path d="M5.255 5.786a.237.237 0 0 0 .241.247h.825c.138 0 .248-.113.266-.25.09-.656.54-1.134 1.342-1.134.686 0 1.314.343 1.314 1.168 0 .635-.374.927-.965 1.371-.673.489-1.206 1.06-1.168 1.987l.003.217a.25.25 0 0 0 .25.246h.811a.25.25 0 0 0 .25-.25v-.105c0-.718.273-.927 1.01-1.486.609-.463 1.244-.977 1.244-2.056 0-1.511-1.276-2.241-2.673-2.241-1.267 0-2.655.59-2.75 2.286zm1.557 5.763c0 .533.425.927 1.01.927.609 0 1.028-.394 1.028-.927 0-.552-.42-.94-1.029-.94-.584 0-1.009.388-1.009.94z"/>
                          </svg><a class="text-white" href="{{$module->video_url}}" target="_blank"> Link not working? Click Here</a>
                    </div>
                @elseif($module->module_type =='pre' || $module->module_type =='post')
                    @include('courses.exam')
                @elseif($module->module_type == 'link')
                <div class="video-responsive">
                    <iframe src="{{$module->video_url}}"  frameborder="0" allowfullscreen></iframe>
                    <div class="badge badge-info url_not_working" style="display: none">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-question-circle" viewBox="0 0 16 16">
                            <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z"/>
                            <path d="M5.255 5.786a.237.237 0 0 0 .241.247h.825c.138 0 .248-.113.266-.25.09-.656.54-1.134 1.342-1.134.686 0 1.314.343 1.314 1.168 0 .635-.374.927-.965 1.371-.673.489-1.206 1.06-1.168 1.987l.003.217a.25.25 0 0 0 .25.246h.811a.25.25 0 0 0 .25-.25v-.105c0-.718.273-.927 1.01-1.486.609-.463 1.244-.977 1.244-2.056 0-1.511-1.276-2.241-2.673-2.241-1.267 0-2.655.59-2.75 2.286zm1.557 5.763c0 .533.425.927 1.01.927.609 0 1.028-.394 1.028-.927 0-.552-.42-.94-1.029-.94-.584 0-1.009.388-1.009.94z"/>
                          </svg><a class="text-white" style="display: none" href="{{$module->video_url}}" target="_blank"> Link not working? Click Here</a>
                    </div>
                </div>
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
                            
                            @if(count($modules)==0)
                            <a href="{{url('/')}}/{{$course->course_slug}}/summary" class="not-active">
                                <div class="lesson-title">
                                    <strong><span class="badge badge-info text-uppercase float-right">Summary</span></strong>
                                </div>
                                <div class="lesson-scroller-item"><div class="image-container" id="summary"><img src="{{url('/images/summary.png')}}" alt=""></div>
                            </div></a>
                            @endif
    
                        </div>
    
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    
    setTimeout(function(){
            $('.url_not_working').show()
        }, 5000); //run this after 3 seconds
    $(document).ready(function() {

        $('footer').addClass('fixed-bottom')

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