@if(!empty($data['GalleryPage']) && count($data['GalleryPage'])>0)
<section class="gallary_main">
    <div class="container-fuild" data-aos="fade-up">
        <div class="grid" data-aos="zoom-in">
            <!-- <div class="grid-sizer"></div> -->
            @foreach($data['GalleryPage'] as $index => $GalleryPages)
            
            @if($index == 0 || $index == 7)
            <div class="grid-item">
                <div class="gallery-box h-100 w-100">
                    <a href="{{ App\Helpers\resize_image::resize($GalleryPages->fkIntImgId)}}" title="Click here to zoom" data-fancybox="gallery" class="g-item">
                        <div class="thumbnail-container">
                            <div class="thumbnail">
                                <picture>
                                    <source type="image/webp" data-srcset="{!! App\Helpers\LoadWebpImage::resize($GalleryPages->fkIntImgId,710,476) !!}" srcset="{!! App\Helpers\LoadWebpImage::resize($GalleryPages->fkIntImgId,100,75) !!}">
                                    <img class="lazy" data-src="{{ App\Helpers\resize_image::resize($GalleryPages->fkIntImgId,100,75)}}" src="{{ App\Helpers\resize_image::resize($GalleryPages->fkIntImgId,100,75)}}" alt="{{ htmlspecialchars_decode($GalleryPages->varTitle) }}" title="{{ htmlspecialchars_decode($GalleryPages->varTitle) }}">
                                </picture>
                            </div>
                        </div>
                        <span class="mask">
                            <img src="{{ url('/') }}/{{ ('assets/images/plus.svg') }}" alt="plus">
                        </span>
                    </a>
                </div>
            </div>
            @else
            <div class="grid-item">
                <div class="gallery-box h-100 w-100">
                    <a href="{{ App\Helpers\resize_image::resize($GalleryPages->fkIntImgId)}}" title="Click here to zoom" data-fancybox="gallery" class="g-item">
                        <div class="thumbnail-container">
                            <div class="thumbnail">
                                <picture>
                                    <source type="image/webp" data-srcset="{!! App\Helpers\LoadWebpImage::resize($GalleryPages->fkIntImgId,350,234) !!}" srcset="{!! App\Helpers\LoadWebpImage::resize($GalleryPages->fkIntImgId,100,75) !!}">
                                    <img class="lazy" data-src="{{ App\Helpers\resize_image::resize($GalleryPages->fkIntImgId,100,75)}}" src="{{ App\Helpers\resize_image::resize($GalleryPages->fkIntImgId,100,75)}}" alt="{{ htmlspecialchars_decode($GalleryPages->varTitle) }}" title="{{ htmlspecialchars_decode($GalleryPages->varTitle) }}">
                                </picture>
                            </div>
                        </div>                        
                        <span class="mask">
                            <img src="{{ url('/') }}/{{ ('assets/images/plus.svg') }}" alt="plus">
                        </span>
                    </a>
                </div>
            </div>
            @endif
            
            @endforeach
        </div>
        {{ $data['GalleryPage']->links() }}
    </div>
</section>
@endif