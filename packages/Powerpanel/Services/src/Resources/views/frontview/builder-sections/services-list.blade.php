@if(!empty($data['services']) && count($data['services'])>0)
<section class="service-listing inner-page-container">
    <div class="container">
        <div class="row">
            @if(isset($PageData['response']) && !empty($PageData['response']) && $PageData['response'] != '[]')
            <div class="col-12 text-center">
                {!! $PageData['response'] !!}
            </div>
            @endif

            @foreach($data['services'] as $services)      
            @php

            if(isset(App\Helpers\MyLibrary::getFront_Uri('services')['uri'])){
            $moduelFrontPageUrl = App\Helpers\MyLibrary::getFront_Uri('services')['uri'];
            $moduleFrontWithCatUrl = ($services->varAlias != false ) ? $moduelFrontPageUrl . '/' . $services->varAlias : $moduelFrontPageUrl;
            $recordLinkUrl = $moduleFrontWithCatUrl.'/'.$services->alias->varAlias;
            }else{
            $recordLinkUrl = '';
            }
            @endphp   
            <div class="col-lg-6 col-md-6 col-sm-12 col-12 service_box_wrap" data-aos="fade-up">
                <div class="service_box text-center">                    
                    <div class="service_title">
                        <h3 class="title text-capitalize"><a href="{{ $recordLinkUrl }}" title="{!! $services->varTitle !!}">{!! $services->varTitle !!}</a></h3>
                    </div>
                    <div class="service_desc">
                        <p>{!! $services->txtShortDescription !!}</p>
                    </div>
                    <div class="service_img ">
                    <a href="{{ $recordLinkUrl }}" title="{!! $services->varTitle !!}">
                        <picture>
                            <source type="image/webp" data-srcset="{!! App\Helpers\LoadWebpImage::resize($services->fkIntImgId,601,401) !!}" srcset="{!! App\Helpers\LoadWebpImage::resize($services->fkIntImgId,522,294) !!}">
                            <img class="lazy" data-src="{{ App\Helpers\resize_image::resize($services->fkIntImgId,601,401)}}" src="{!! url('assets/images/loader.gif') !!}" alt="{{ htmlspecialchars_decode($services->varTitle) }}" title="{{ htmlspecialchars_decode($services->varTitle) }}">
                        </picture>
                    </a>
                    </div>
                </div>
            </div>
            @endforeach

            @if(isset($data['services']) && $data['services']->total() > $data['services']->perPage())
            <div class="col-12">
                <div class="pagination" id="ClientReviews">
                    {{ $data['services']->links() }}
                </div>
            </div>
            @endif

        </div>
    </div>
</section>
@else

<section>
    <div class="inner-page-container cms">
        <div class="container">
            <section class="page_section">
                <div class="container">
                    <div class="row">
                        <div class="col-12 text-center">
                            <h2>Coming Soon...</h2>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </div>
</section>
@endif