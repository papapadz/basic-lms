@extends('layouts.main')

@section('styles')
<style>
    .parent {
        display: flex;
        flex-wrap: wrap;
        }
    .child {
        flex: 1 0 15%; /* explanation below */
        margin: 5px;
        height: 100px;
    }
    .child:hover {
        background-color: green;
        color: white;
        background-image: none
    }
    .owl-carousel{
        touch-action: none;
    }
</style>
@endsection

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">{{$course->course_name}} - Online Course 
                    <a class="btn border-danger btn-sm float-right" href="{{route('homepage')}}"><i class="fa fa-home"></i> Home</a>
                </div>

                <div class="card-body">
                    <h4>Summary</h4>
                    <div class="card-body" id="content">
                        <div class="parent" style="padding: 10px;">
                            @foreach($modules as $module)
                                <a class="btn border-success child" href="{{route('module', ['course' => $module->course->course_slug, 'module' => $module->module_slug])}}">
                                    {{ $module->module_name }}<br><br><b class="text-white">Revisit</b>
                                </a>
                            @endforeach
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
    $(document).ready(function() {
        $('.owl-carousel').owlCarousel({
            margin: 10,
            nav: true,
            navText : ["<i class='fa fa-chevron-left'></i>","<i class='fa fa-chevron-right'></i>"],
            responsive: {
                0: {
                    items: 1
                },
                600: {
                    items: 3
                },
                1000: {
                    items: 5
                }
            }
        });
        /*get active module number */
        var get_active = $('div.image-container.active').attr('id');
        $('.owl-carousel').trigger('to.owl.carousel', get_active);
    })

</script>
@endsection