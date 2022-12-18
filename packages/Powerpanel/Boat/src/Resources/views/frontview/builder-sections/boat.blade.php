@if(isset($data['boat']) && count($data['boat']) > 0)
<section class="boat_sec" data-aos="fade-up">
    <div class="container-fluid">
        <h2 class="text-capitalize cm-title" data-aos="fade-up">{{$data['title']}}</h2>
        <div class="boat-card-slider swiper" data-aos="fade-left" data-aos-easing="linear" data-aos-duration="1500">
            <div class="swiper-wrapper">
                @foreach($data['boat'] as $boat)
                @php
                if(isset(App\Helpers\MyLibrary::getFront_Uri('boat')['uri'])){
                $moduelFrontPageUrl = App\Helpers\MyLibrary::getFront_Uri('boat')['uri'];
                $moduleFrontWithCatUrl = ($boat->varAlias != false ) ? $moduelFrontPageUrl . '/' . $boat->varAlias : $moduelFrontPageUrl;
                $recordLinkUrl = $moduleFrontWithCatUrl.'/'.$boat->alias->varAlias;
                }else{
                $recordLinkUrl = '';
                }
                @endphp
                <!-- <div class="col-lg-4 col-md-4 col-sm-12 col-12"> -->
                <div class="swiper-slide">
                    <div class="boat_img">
                        <div class="thumbnail-container">
                            <div class="thumbnail">
                                <picture>
                                    <source type="image/webp"  srcset="{!! App\Helpers\LoadWebpImage::resize($boat->fkIntImgId,606,404) !!}">
                                    <img data-src="{{ App\Helpers\resize_image::resize($boat->fkIntImgId,606,404)}}" src="{!! url('assets/images/loader.gif') !!}" alt="{{ htmlspecialchars_decode($boat->varTitle) }}" title="{{ htmlspecialchars_decode($boat->varTitle) }}">
                                </picture>
                            </div>
                            @php 
                                $boat_stock = DB::table('stock')->select('varTitle')->where('chrPublish', 'Y')->where('chrDelete', 'N')->where('id', $boat->intBoatStockId)->first();
                                $boat_stock = $boat_stock->varTitle;
                                $boat_stock_class = "";
                            @endphp
                            @if(!empty($boat_stock) && $boat_stock == "Available to Order")
                                @php $boat_stock_class = "available"; @endphp
                            @elseif (!empty($boat_stock) && $boat_stock == "Sold")
                                @php $boat_stock_class = "sold"; @endphp
                            @elseif (!empty($boat_stock) && $boat_stock == "Available")
                                @php $boat_stock_class = "in-stock"; @endphp
                            @elseif (!empty($boat_stock) && $boat_stock == "Coming Soon")
                                @php $boat_stock_class = "comingsoon"; @endphp
                            @elseif (!empty($boat_stock) && $boat_stock == "Sale Pending")
                                @php $boat_stock_class = "salepending"; @endphp
                            @else
                                @php $boat_stock_class = "available"; @endphp
                            @endif
                            <div class="status-tag-line {{ $boat_stock_class }}">{{ (!empty($boat_stock) ? $boat_stock : '') }}</div>
                        </div>
                        <span class="line"></span>
                    </div>
                    <div class="boat-desc-wrap">
                        <div class="boat_title">
                            <h4 class="title text-capitalize main-title"><a href="{{ $recordLinkUrl }}" title="{!! $boat->varTitle !!}">{!! $boat->varTitle !!}</a></h4>
                        </div>
                        <div class="boat_desc">
                            <p>US${!! number_format($boat->intPrice) !!}</p>
                            <p>Year: {!! $boat->yearYear !!}</p>
                            <p>Length: {!! $boat->varLength !!}ft</p>
                            @if(isset($boat->intBoatFuelId) && !empty($boat->intBoatFuelId))
                            @php
                            $result = DB::table('boat_fuel_type')->select('varTitle')->where('id',$boat->intBoatFuelId)->where('chrPublish','Y')->where('chrDelete','N')->first();
                            @endphp
                            <p>Fuel: {!! $result->varTitle !!}</p>
                            @endif
                        </div>
                        <a href="{{ $recordLinkUrl }}" class="boat-btn" title="More Details">MORE DETAILS</a>
                    </div>
                </div>
                <!-- </div> -->
                @endforeach
            </div>
            <div class="swiper-button-next"></div>
            <div class="swiper-button-prev"></div>
            <div class="swiper-scrollbar"></div>
        </div>
        <div class="view-more text-md-right text-center">
            <a href="{{url('boat')}}" class="ac-btn" title="View All Boats">View All Boats</a>
        </div>
    </div>
</section>
@endif