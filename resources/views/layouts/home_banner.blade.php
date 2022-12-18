@if (!empty($bannerData) && count($bannerData) > 0)
    <section class="home-banner">
        <div class="container-fluid">
            <div class="home-banner-02">
                <div id="home-banner" class="carousel slide carousel-fade" data-ride="carousel" data-interval="5000">
                    <!-- Wrapper for slides -->
                    <div class="carousel-inner h-b_radisu">
                        @foreach ($bannerData as $key => $banner)
                            <div class="carousel-item @if ($key == 0) active @endif" data-interval="">
                                <div class="h-b_fill lazy" data-bg="{!! App\Helpers\LoadWebpImage::resize($banner->fkIntImgId, 1920, 1080) !!}" style="background-image: url('{!! App\Helpers\LoadWebpImage::resize($banner->fkIntImgId, 1920, 1080) !!}');">
                                </div>
                                <div class="carousel-caption h-b_caption" data-aos="fade-right">
                                    <div class="h-b_item">
                                        <div class="h-b_center">
                                            @if ($banner->chrDisplayVideo == 'Y')
                                            <div class="container">
                                                <div class="banner_box">
                                                    <div class="sub-title">{{ $banner->varTitle }}</div>
                                                    @if (!empty($banner->varTagLine))
                                                        <div class="h-b_title">{{ $banner->varTagLine }}</div>
                                                    @endif
                                                    <div class="h-b_video">
                                                        <a data-fancybox class="h-b_video_play" href="{{ $banner->varVideoLink }}?autoplay;">
                                                           <i class="fa fa-play" aria-hidden="true"></i>
                                                        </a>
                                                    </div>
                                                </div>
                                            </div>
                                            @else
                                                <div class="container">
                                                    <div class="row">
                                                        <div class="col-sm-12">
                                                            <div class="banner_box" data-aos="fade-right" data-aos-easing="ease-in-sine">
                                                                <div class="sub-title">{{ $banner->varTitle }}</div>
                                                                @if (!empty($banner->varTagLine))
                                                                    <div class="h-b_title">{{ $banner->varTagLine }}</div>
                                                                @endif
                                                                @if (!empty($banner->varButtonName) && !empty($banner->varLink))
                                                                    @if ($banner->chrDisplayLink == 'Y')
                                                                        @php $taeget = 'target="_blank"'; @endphp
                                                                    @else
                                                                        @php $taeget = ""; @endphp
                                                                    @endif
                                                                    <div class="home-banner-btn">
                                                                        <a class="ac-btn ac-wht" {{ $taeget }} href="{{ $banner->varLink }}" title="{{ $banner->varButtonName }}">{{ $banner->varButtonName }}</a>
                                                                    </div>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    <!-- Indicators -->
                    @if (count($bannerData) > 1)
                        <ol class="carousel-indicators h-b_indicators">
                            @foreach ($bannerData as $key => $banner)
                                <li data-target="#home-banner" data-slide-to="{{ $key }}" class="@if ($key == 0) active @endif"><span></span></li>
                            @endforeach
                        </ol>
                        <!-- Navigation Banner controls -->
                        <a class="left h-b_control carousel-control" href="#home-banner" data-slide="prev"></a>
                        <a class="right h-b_control carousel-control" href="#home-banner" data-slide="next"></a>
                    @endif
                </div>
            </div>
        </div>
    </section>
@else
    <section class="home-banner">
        <div class="container-fluid">
            <div class="home-banner-02">
                <div id="home-banner" class="carousel slide carousel-fade" data-ride="carousel" data-interval="5000">
                    <!-- Wrapper for slides -->
                    <div class="carousel-inner h-b_radisu">
                        <div class="carousel-item active" data-interval="">
                            <div class="h-b_fill lazy" data-bg="{{url('/assets/images/default-home-banner.jpg')}}" style="background-image: url('{{url('/assets/images/default-home-banner.jpg')}}');">
                            </div>
                            <div class="carousel-caption h-b_caption">
                                <div class="h-b_item">
                                    <div class="h-b_center">
                                        <div class="container">
                                            <div class="row">
                                                <div class="col-sm-12">
                                                    <div class="banner_box" data-aos="fade-right" data-aos-easing="ease-in-sine">
                                                        <div class="sub-title">Avalon Marine</div>
                                                        <div class="h-b_title">A full complement of boat brokerage, maintenance, repair and management services</div>
                                                        <div class="home-banner-btn">
                                                            <a class="ac-btn ac-wht" href="{{url('/contact')}}" title="Request A Service">Request A Service</a>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endif
<script>
    var t;
    var start = $('#home-banner').find('.active').attr('data-interval');
    // t = setTimeout("$('#home-banner').carousel({interval: 1000});", start - 1000);
    $('#home-banner').on('slid.bs.carousel', function() {
        clearTimeout(t);
        $('.right.carousel-control').show();
        $('.left.carousel-control').show();
        if ($('.carousel-item:last-child').hasClass('active')) {
            $('.right.carousel-control').hide();
        } else if ($('.carousel-item:first-child').hasClass('active')) {
            $('.left.carousel-control').hide();
        };
        var duration = $(this).find('.active').attr('data-interval');
        $('#home-banner').carousel('pause');
        t = setTimeout("$('#home-banner').carousel();", duration - 1000);
    })
    $('.carousel-control.right').on('click', function() {
        clearTimeout(t);
    });

    $('.carousel-control.left').on('click', function() {
        clearTimeout(t);
    });
    $().ready(function() {
        if ($('.carousel-item:last-child').hasClass('active')) {
            $('.right.carousel-control').hide();
        } else if ($('.carousel-item:first-child').hasClass('active')) {
            $('.left.carousel-control').hide();
        };
    });
</script>