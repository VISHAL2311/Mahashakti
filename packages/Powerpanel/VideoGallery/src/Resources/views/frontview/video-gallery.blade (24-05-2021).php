@if(!Request::ajax())
@extends('layouts.app')
@section('content')
@include('layouts.inner_banner')
@endif
@if(!empty($VideoGallery) && count($VideoGallery)>0)

<section class="inner-page-container video_gallery">
    <div class="container">
        <div class="row">
            @foreach($VideoGallery as $index => $VideoGallerypage)        
            <div class="col-6 gap">
                <div class="img-v" data-aos="fade-up">
                    <a data-fancybox href="{{$VideoGallerypage->txtLink}}" class="fbox">
                        <div class="thumbnail-container">
                            <div class="thumbnail">
                                <img class="lazy" data-src="{{ App\Helpers\resize_image::resize($VideoGallerypage->fkIntImgId)}}" src="{{ App\Helpers\resize_image::resize($VideoGallerypage->fkIntImgId)}}" />
                                <span class="mask">
                                    <i class="fa fa-play"></i>                            
                                </span>
                            </div>
                        </div>
                        <h3 class="title-video">{{$VideoGallerypage->varTitle}}</h3>
                    </a>
                </div>
            </div>
            @endforeach
            @if(isset($VideoGallery) && count($VideoGallery)>0)
            <div class="col-12">
                <div class="pagination">
                    {{ $VideoGallery->links() }}
                </div>
            </div>
            @endif
        </div>
    </div>
</section>
@else 
<section class="inner-page-container">
    <div class="container">
        <div class="row">
            <div class="col-12 text-center">
                <h2>Coming Soon...</h2>
            </div>	
        </div>
    </div>  
</section>    
@endif
@if(!Request::ajax())
@section('footer_scripts')
<script type="text/javascript" src="{{ $CDN_PATH.'assets/libraries/masonry/js/masonry.pkgd.min.js'}}"></script>
<script type="text/javascript" src="{{ $CDN_PATH.'assets/libraries/masonry/js/masonry-function.js'}}"></script>
@endsection
@endsection
@endif
