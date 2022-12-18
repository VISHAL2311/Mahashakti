@extends('layouts.app')
@section('content')
@include('layouts.inner_banner')
@if(!empty($work))
<section>
    <div class="inner-page-container cms work_detail">
        <div class="container">
            <!-- Main Section S -->
            <div class="row">
                <div class="col-3 d-none"> <!-- d-sm-block -->
                    <div class="detail-panel">
                        @if(isset($similarWork) && count($similarWork) > 0)
                        <div class="item rec-blog">
                            <h3 class="title-a">Other Works</h3>
                            <ul>
                                @foreach($similarWork as $index => $similarWork)
                                <li class="active">
                                    <a href="{{ url('our-work/'.$similarWork->alias->varAlias) }}" title="{{ucwords($similarWork->varTitle)}}">{{$similarWork->varTitle}}</a>
                                </li>
                                @endforeach
                            </ul>
                        </div>
                        @endif
                        @if(isset($blogsSlidebar) && count($blogsSlidebar) > 0)
                        <div class="item rec-work">
                            <h3 class="title-a">Recent Blogs</h3>
                            <ul>
                                @foreach($blogsSlidebar as $index => $similarBlogs)
                                <li>
                                    <a href="{{ url('blogs/'.$similarBlogs->alias->varAlias) }}" title="{{ucwords($similarBlogs->varTitle)}}">{{$similarBlogs->varTitle}}</a>
                                </li>
                                @endforeach
                            </ul>
                        </div>
                        @endif
                        <div class="item contact">
                            <img class="img-qc" src="{{ $CDN_PATH.'assets/images/form-q.svg' }}" alt="svg">
                            <h3 class="title-a">Get a Free Estimate</h3>
                            <p class="text-d">Lorem Ipsum is simply dummy text of the printing and typesetting industry.</p>
                            <div class="text-center btn-qc">
                                <a class="ac-border" href="{{ url('contact') }}" title="Contact Us">
                                    <span class="text">Contact Us</span>
                                    <span class="line"></span>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-12 col-xs-12" data-aos="fade-up">
                    
                    <div class="cms serv-detail-content">
                        <h2 class="cm-title">{!!$work->varTitle!!}</h2>
                        <div id="gallery" style="display:none;" class="mb-3">
                        @php $imgArr = explode(',', $work->fkIntImgId);@endphp
                        @if(!empty($imgArr))
                        @foreach($imgArr as $img)
                            <img alt="{!!$work->varTitle!!}" src="{{ App\Helpers\resize_image::resize($img,928,415)}}" data-image="{{ App\Helpers\resize_image::resize($img,928,415)}}" data-description="{!!$work->varTitle!!}">
                        @endforeach
                        @endif
                        </div>
                        <input type="hidden" id="galleryCount" value="{{ count($imgArr) }}">
                        <div class="right_content">
                            @if(isset($txtDescription['response']) && !empty($txtDescription['response']) && $txtDescription['response'] != '[]')
                            {!!$txtDescription['response']!!}
                            @else
                            <p>{!!$work->txtShortDescription!!}</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>    
</section>
@endif
@if(!Request::ajax())
@section('footer_scripts')
<script src="{{ $CDN_PATH . 'assets/libraries/unite/js/unitegallery.min.js' }}?{{ Config::get('Constant.VERSION') }}"></script>
<script src="{{ $CDN_PATH . 'assets/libraries/unite/js/ug-theme-default.js' }}?{{ Config::get('Constant.VERSION') }}"></script>
<script>
    jQuery(document).ready(function(){
      jQuery("#gallery").unitegallery();
    });
    $(document).ready(function(){
    setTimeout(function(){ 
            if($('#gallery').length > "0"){
                if($('#galleryCount').val() <= 1){
                    $('.ug-strip-panel').hide();
                    $('.ug-arrow-right').hide();
                    $('.ug-arrow-left').hide();
                    $('.ug-slider-control').hide();                
                    $('.ug-default-button-play-single').hide();
                    $('.ug-default-button-play-single').hide();
                    $('.ug-default-button-hidepanel').addClass('one-item-css');
                    // $('.ug-slider-wrapper').setAttribute("style", "left:0;right:0;margin:auto");                
                }
            }
        }, 500);
    });
</script>
@endsection
@endsection
@endif