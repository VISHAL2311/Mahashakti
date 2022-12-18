@if(!Request::ajax())
@extends('layouts.app')
@section('content')
@include('layouts.inner_banner')
@endif



<!-- sitemap_01 S -->
{{--<!-- @if(isset($PAGE_CONTENT) && $PAGE_CONTENT != '[]')
    {!!  $PAGE_CONTENT !!}
@endif -->--}}
<section class="inner-page-container sitemap_01">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <ul class="list cols sitemap_main">
                    {!! $siteMap !!}
                    <li><a href="{{ url('sitemap.xml') }}" title="XML Sitemap" target="_blank">XML Site Map</a></li>
                    <!-- <li><a href="{{ url('/data-removal') }}" title="Privacy Data Removal">Privacy Data Removal</a></li> -->
                </ul>
                @if(
                    null!==(Config::get('Constant.SOCIAL_FB_LINK')) && strlen(Config::get('Constant.SOCIAL_FB_LINK')) > 0 || 
                    null!==(Config::get('Constant.SOCIAL_TWITTER_LINK')) && strlen(Config::get('Constant.SOCIAL_TWITTER_LINK')) > 0 || 
                    null!==(Config::get('Constant.SOCIAL_INSTAGRAM_LINK')) && strlen(Config::get('Constant.SOCIAL_INSTAGRAM_LINK')) > 0 || 
                    null!==(Config::get('Constant.SOCIAL_PINTEREST_LINK')) && strlen(Config::get('Constant.SOCIAL_PINTEREST_LINK')) > 0 || 
                    null!==(Config::get('Constant.SOCIAL_ECAYONLINE_LINK')) && strlen(Config::get('Constant.SOCIAL_ECAYONLINE_LINK')) > 0 || 
                    null!==(Config::get('Constant.SOCIAL_YACHTWORLD_LINK')) && strlen(Config::get('Constant.SOCIAL_YACHTWORLD_LINK')) > 0 || 
                    null!==(Config::get('Constant.SOCIAL_YELP_LINK')) && strlen(Config::get('Constant.SOCIAL_YELP_LINK')) > 0 
                )
                    <hr>
                    <h2 class="nqtitle mb-xs-30">Social Media</h2>
                    <ul class="list social-icon cols">
                        @if(null!==(Config::get('Constant.SOCIAL_FB_LINK')) && strlen(Config::get('Constant.SOCIAL_FB_LINK')) > 0)
                            <li><a href="{{ Config::get('Constant.SOCIAL_FB_LINK') }}" title="Follow Us On Facebook" target="_blank"><i class="fa fa-facebook social_m_icon" aria-hidden="true"></i>Facebook</a></li>
                        @endif
                        @if(null!==(Config::get('Constant.SOCIAL_TWITTER_LINK')) && strlen(Config::get('Constant.SOCIAL_TWITTER_LINK')) > 0)
                            <li><a href="{{ Config::get('Constant.SOCIAL_TWITTER_LINK') }}" title="Follow Us On Twitter" target="_blank"><i class="fa fa-twitter social_m_icon" aria-hidden="true"></i>Twitter</a></li>
                        @endif
                        @if(null!==(Config::get('Constant.SOCIAL_INSTAGRAM_LINK')) && strlen(Config::get('Constant.SOCIAL_INSTAGRAM_LINK')) > 0)
                            <li><a href="{{ Config::get('Constant.SOCIAL_INSTAGRAM_LINK') }}" title="Follow Us On Instagram" target="_blank"><i class="fa fa-instagram social_m_icon " aria-hidden="true"></i>Instagram</a></li>
                        @endif
                        @if(null!==(Config::get('Constant.SOCIAL_PINTEREST_LINK')) && strlen(Config::get('Constant.SOCIAL_PINTEREST_LINK')) > 0)
                            <li><a href="{{ Config::get('Constant.SOCIAL_PINTEREST_LINK') }}" title="Follow Us On Pinterest" target="_blank"><i class="fa fa-pinterest-p social_m_icon" aria-hidden="true"></i>Pinterest</a></li>
                        @endif
                        @if(null!==(Config::get('Constant.SOCIAL_YELP_LINK')) && strlen(Config::get('Constant.SOCIAL_YELP_LINK')) > 0)
                            <li><a href="{{ Config::get('Constant.SOCIAL_YELP_LINK') }}" title="Follow Us On Yelp" target="_blank"><i class="fa fa-yelp social_m_icon" aria-hidden="true"></i>Yelp</a></li>
                        @endif                        
                        @if(null!==(Config::get('Constant.SOCIAL_ECAYONLINE_LINK')) && strlen(Config::get('Constant.SOCIAL_ECAYONLINE_LINK')) > 0)
                            <li><a href="{{ Config::get('Constant.SOCIAL_ECAYONLINE_LINK') }}" title="Follow Us On Ecay Online" target="_blank"><i class="fa fa-etsy social_m_icon" aria-hidden="true"></i>Ecay Online</a></li>
                        @endif                        
                        @if(null!==(Config::get('Constant.SOCIAL_YACHTWORLD_LINK')) && strlen(Config::get('Constant.SOCIAL_YACHTWORLD_LINK')) > 0)
                            <li><a href="{{ Config::get('Constant.SOCIAL_YACHTWORLD_LINK') }}" title="Follow Us On Yacht World" target="_blank"><i class="fa fa-ship social_m_icon" aria-hidden="true"></i>Yacht World</a></li>
                        @endif                        
                    </ul>
                @endif
            </div>
        </div>
    </div> 
</section>
<!-- sitemap_01 E -->
@if(!Request::ajax())
@section('footer_scripts')
<!-- <script src="{{ url('assets/js/sitemap.js') }}"></script> -->
<script>

    $('.sitemap_01 a').each(function(index, value){
        var txt = $(this).text();
        if(txt == '')
        {
            $(this).remove();
        }
    });

</script>

@endsection

@endsection
@endif