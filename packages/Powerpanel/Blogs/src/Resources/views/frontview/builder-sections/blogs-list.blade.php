@if(!empty($data['blogs']) && count($data['blogs'])>0)

<section class="services_list inner-page-container blogs_page">

    <div class="container">

        <div class="row">

            <div class="col-md-12 col-xs-12">

                <div class="row">

                    @foreach($data['blogs'] as $index => $blog)

                    @php

                    if(isset(App\Helpers\MyLibrary::getFront_Uri('Blogs')['uri'])){

                    $moduelFrontPageUrl = App\Helpers\MyLibrary::getFront_Uri('Blogs')['uri'];

                    $moduleFrontWithCatUrl = ($blog->varAlias != false ) ? $moduelFrontPageUrl . '/' . $blog->varAlias : $moduelFrontPageUrl;

                    $recordLinkUrl = $moduleFrontWithCatUrl.'/'.$blog->alias->varAlias;

                    }else{

                    $recordLinkUrl = '';

                    }

                    @endphp

                    <div class="col-12 col-sm-6 col-lg-4 b-line" data-aos="fade-up">

                        <div class="service_box" data-aos="fade-in">

                            <div class="img">

                                <div class="image img_hvr ">

                                    <a href="{{ $recordLinkUrl }}" title="{{ ucwords($blog->varTitle) }}" class="img-effect-right">

                                        @if(isset($blog->fkIntImgId) && !empty($blog->fkIntImgId))

                                        <div class="thumbnail-container" data-aos="fade-down">

                                            <div class="thumbnail">

                                                <picture class="img-hvr_hvr">

                                                    <source type="image/webp" data-srcset="{!! App\Helpers\LoadWebpImage::resize($blog->fkIntImgId,423,240) !!}" srcset="{!! App\Helpers\LoadWebpImage::resize($blog->fkIntImgId,423,240) !!}">

                                                    <img class="lazy" data-src="{{ App\Helpers\resize_image::resize($blog->fkIntImgId,423,240)}}" src="{!! url('assets/images/loader.gif') !!}" alt="{{ htmlspecialchars_decode($blog->varTitle) }}" title="{{ htmlspecialchars_decode($blog->varTitle) }}">

                                                </picture>

                                            </div>                                           

                                        </div>

                                        @endif

                                    </a>

                                </div>

                            </div>

                            <div class="info">

                                @if(isset($blog) && !empty($blog->dtDateTime))

                                @php $date = strtotime($blog->dtDateTime) @endphp

                                @php

                                $blogstartDate = date('d',strtotime($blog->dtDateTime));

                                $blogstartfull = date('M Y',strtotime($blog->dtDateTime));

                                $blogDisplayDate = $blogstartDate;

                                if(!empty($blog->dtEndDateTime) && $blog->dtEndDateTime != null){

                                $blogExpityDate = date('d m Y',strtotime($blog->dtEndDateTime));

                                $blogDisplayDate = $blogstartDate." to ".$blogExpityDate;

                                }

                                @endphp

                                @endif

                                <div class="b_date">

                                    <i class="fa fa-calendar" aria-hidden="true"></i>

                                    <span class="day">{{ $blogstartDate }}</span>

                                    <span class="m-y">{{ $blogstartfull }}</span>

                                </div>

                                <h3 class="cm-title">

                                    <a href="{{ $recordLinkUrl }}" title="{{ ucwords($blog->varTitle) }}">

                                        {{ htmlspecialchars_decode(str_limit($blog->varTitle, 55)) }}

                                    </a>

                                </h3>

                                <div class="short_desc">

                                    <p>{!! (strlen($blog->varShortDescription) > 120) ? substr($blog->varShortDescription, 0, 120).'...' : $blog->varShortDescription !!}</p>

                                </div>

                            </div>

                        </div>

                    </div>

                    @endforeach

                </div>

            </div>

            <!-- Static Code Ends-->

            @if(isset($data['blogs']) && $data['blogs']->total() > $data['blogs']->perPage())

            <div class="col-12">

                <div class="pagination" id="ClientReviews">

                    {{ $data['blogs']->links() }}

                </div>

            </div>

            @endif

            <?php $similarServices = Powerpanel\Services\Models\Services::getSidebarRecordList(); ?>

            @if(isset($similarServices) && count($similarServices) > 0)

            <div class="col-12 related_services">

                <div class="row mt-5">

                    <div class="col-12 text-center">

                        <h3 class="cm-title our_service">Our Services</h3>

                    </div>

                    <div class="col-12">

                        <div class="owl-carousel">

                            @foreach($similarServices as $index => $similarService)

                            @php

                            if(isset(App\Helpers\MyLibrary::getFront_Uri('Services')['uri'])){

                            $moduelFrontPageUrl = App\Helpers\MyLibrary::getFront_Uri('Services')['uri'];

                            $moduleFrontWithCatUrl = ($similarService->varAlias != false ) ? $moduelFrontPageUrl . '/' . $similarService->varAlias : $moduelFrontPageUrl;

                            $recordLinkserviceUrl = $moduleFrontWithCatUrl.'/'.$similarService->alias->varAlias;

                            }else{

                            $recordLinkserviceUrl = '';

                            }

                            @endphp

                            <a href="{{ $recordLinkserviceUrl }}" title="{{ucwords($similarService->varTitle)}}">

                                <div class="list">

                                    @if(isset($similarService->fkIntImgId) && !empty($similarService->fkIntImgId))

                                    <div class="img img_hvr img-hvr_hvr">

                                        <div class="thumbnail-container">

                                            <div class="thumbnail">

                                                <picture>

                                                    <source type="image/webp" data-srcset="{!! App\Helpers\LoadWebpImage::resize($similarService->fkIntImgId,441,251) !!}" srcset="{!! App\Helpers\LoadWebpImage::resize($similarService->fkIntImgId,441,251) !!}">

                                                    <img  data-src="{{ App\Helpers\resize_image::resize($similarService->fkIntImgId,441,251)}}" src="{!! url('assets/images/loader.gif') !!}" alt="{{ htmlspecialchars_decode($similarService->varTitle) }}" title="{{ htmlspecialchars_decode($similarService->varTitle) }}">

                                                </picture>

                                            </div>

                                        </div>

                                    </div>

                                    @endif

                                    <div class="text">

                                        <h3 class="cm-title">{{$similarService->varTitle}}

                                            @php

                                            $servicestartDate = date('l F jS, Y',strtotime($similarService->dtDateTime));

                                            $servicesDisplayDate = $servicestartDate;

                                            if(!empty($similarService->dtEndDateTime) && $similarService->dtEndDateTime != null){

                                            $serviceExpityDate = date('l F jS, Y',strtotime($similarService->dtEndDateTime));

                                            $servicesDisplayDate = $servicestartDate." to ".$serviceExpityDate;

                                            }

                                            @endphp

                                        </h3>

                                    </div>

                                </div>

                            </a>

                            @endforeach

                        </div>

                    </div>

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