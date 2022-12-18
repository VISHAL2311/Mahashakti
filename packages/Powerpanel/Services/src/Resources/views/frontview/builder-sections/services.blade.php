@if(isset($data['services']) && count($data['services']) > 0)

<section class="services_sec text-center">

    <div class="container">

        <h2 class="text-capitalize cm-title" data-aos="fade-up">{{$data['title']}}</h2>

        <div class="service_slider owl-carousel owl-theme" data-aos="fade-up">

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

            <div class="item">

                <div class="service_box">

                    <div class="service_title">

                        <h3 class="title text-capitalize"><a href="{{ $recordLinkUrl }}" title="{!! $services->varTitle !!}">{!! $services->varTitle !!}</a></h3>

                    </div>

                    <div class="service_desc">

                        <p>{!! $services->txtShortDescription !!}</p>

                    </div>

                    <div class="service_img">

                        <a href="{{ $recordLinkUrl }}" title="{!! $services->varTitle !!}">

                            <picture>

                                <source type="image/webp" data-srcset="{!! App\Helpers\LoadWebpImage::resize($services->fkIntImgId,526,351) !!}" srcset="{!! App\Helpers\LoadWebpImage::resize($services->fkIntImgId,522,294) !!}">

                                <img class="lazy" data-src="{{ App\Helpers\resize_image::resize($services->fkIntImgId,526,351)}}" src="{!! url('assets/images/loader.gif') !!}" alt="{{ htmlspecialchars_decode($services->varTitle) }}" title="{{ htmlspecialchars_decode($services->varTitle) }}">

                            </picture>

                        </a>

                    </div>

                </div>

            </div>

            @endforeach

        </div>

    </div>

</section>

@endif