
@if(!empty($data['videogallery']) && count($data['videogallery'])>0)

<section class="inner-page-container video_gallery">
    <div class="container">
        <div class="row">
            @foreach($data['videogallery'] as $index => $VideoGallerypage)        
            <div class="col-6 gap">
                <div class="img-v" data-aos="fade-up">
                    <a data-fancybox href="{{$VideoGallerypage->txtLink}}" class="fbox" title="{{ucwords($VideoGallerypage->varTitle)}}">
                        <div class="thumbnail-container">
                            <div class="thumbnail">
                                <img class="lazy" data-src="{{ App\Helpers\resize_image::resize($VideoGallerypage->fkIntImgId,537,303)}}" src="{{ App\Helpers\resize_image::resize($VideoGallerypage->fkIntImgId,537,303)}}" />
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
            {{--@if(isset($data['videogallery']) && $data['videogallery']->total() > $data['videogallery']->perPage())
            <div class="col-12">
                <div class="pagination">
                    {{ $data['videogallery']->links() }}
                </div>
            </div>
            @endif--}}
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