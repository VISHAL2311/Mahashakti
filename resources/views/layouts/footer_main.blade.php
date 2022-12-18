<footer id="footer_div">
    <!-- Scroll To Top S -->
        <div id="back-top" title="Scroll To Top" style="display: none;">
            <i class="fa fa-angle-up"></i>
        </div>
    <!-- Scroll To Top E -->
    <div class="cookie-note" id="js-gdpr-consent-banner" style="display:none">
        <img src="{{ $CDN_PATH.'assets/images/cookie.svg'}}" loading="lazy" class="cookie-img" alt="Cookie">
        <h6>Improve Site Experience</h6>
        @php
        $var_privacy_policy=\App\Helpers\static_block::get_page_title('10');
        $class='';
        @endphp
        @if(Request::segment(1) == 'privacy-policy')
        @php $class='active';@endphp
        @endif
        @if(isset($var_privacy_policy) && count($var_privacy_policy) > 0)
        <p>This website uses cookies in order to improve the user experience Cookies</p>
        @endif
        <div class="d-flex justify-content-around">
            <button type="submit" class="ac-btn mt-4" title="Accept" id="cookie_policy" onclick="GetGDPRCLOSE()">Accept</button>
        </div>
    </div>
    <div class="messanger-box">

    </div>

    <div class="container">
        <div class="footer-main">
            <div class="row">
                <div class="col-12 col-lg-4 col-md-6 text-center text-lg-left text-md-center items" data-aos="fade-in">
                    <div class="footer_box mail_box">
                        <div class="footer_logo">
                            @php $segment = Request::segment(2); @endphp
                            @if($segment == 'aceessdenied')
                            <a href="{{ url('/') }}" title="{{ Config::get('Constant.SITE_NAME') }}">
                                <picture>
                                    <source type="image/webp" data-srcset="{!! App\Helpers\LoadWebpImage::resize(Config::get('Constant.FRONT_FOOTER_LOGO_ID'),391,123) !!}" srcset="{!! App\Helpers\LoadWebpImage::resize(Config::get('Constant.FRONT_FOOTER_LOGO_ID'),391,123) !!}">
                                    <img loading="lazy" src="{{ App\Helpers\resize_image::resize(Config::get('Constant.FRONT_FOOTER_LOGO_ID'),391,123)}}" alt="Footer Logo">
                                </picture>
                            </a>
                            @else
                            <a href="{{ url('/') }}" title="{{ Config::get('Constant.SITE_NAME') }}">
                                <picture>
                                    <source type="image/webp" data-srcset="{!! App\Helpers\LoadWebpImage::resize(Config::get('Constant.FRONT_FOOTER_LOGO_ID'),391,123) !!}" srcset="{!! App\Helpers\LoadWebpImage::resize(Config::get('Constant.FRONT_FOOTER_LOGO_ID'),391,123) !!}">
                                    <img loading="lazy" src="{{ App\Helpers\resize_image::resize(Config::get('Constant.FRONT_FOOTER_LOGO_ID'),391,123)}}" alt="Footer Logo">
                                </picture>
                            </a>
                            @endif
                        </div>

                    </div>
                </div>
                <div class="col-12 col-lg-5 col-md-6 text-center text-lg-left text-md-center items" data-aos="fade-in">
                    <div class="footer_box quick_box">
                        @if(isset($objContactInfo) && !empty($objContactInfo))
                        @php $address = $objContactInfo[0]->txtAddress; @endphp
                        @endif
                        @if(isset($address) && !empty($address))
                        <div class="about_foot">
                            <p>{!! nl2br($address) !!}</p>
                        </div>
                        @else
                        <div class="about_foot">
                            <p>10 Market st, P.O 507<br /> KY1-9006 Camana Bay, Grand Cayman<br /> Cayman Island</p>
                        </div>
                        @endif
                        @if(isset($objContactInfo) && !empty($objContactInfo))
                        @php
                        $email = unserialize($objContactInfo[0]->varEmail);
                        $email = count($email)>0?$email[0]:$email;
                        $phone = unserialize($objContactInfo[0]->varPhoneNo);
                        $phone = count($phone)>0?$phone[0]:$phone;
                        $phone1 = str_replace(' ','',$phone);
                        @endphp
                        @endif
                        <ul class="contact-info">
                            @if(isset($email) && !empty($email))
                            <li><a href="mailto:{{ $email }}" title="Email Us On : {{ $email }}">{{ $email }}</a></li>
                            @endif
                            @if(isset($phone) && !empty($phone))
                            <li><a href="tel:{{ $phone1 }}" title="Call Us On : {{ $phone1 }}"> {{ $phone }}</a></li>
                            @endif
                            @if(isset($objContactInfo) && !empty($objContactInfo[0]->varMobileNo))
                            <li><a href="tel:{{str_replace(' ','',$objContactInfo[0]->varMobileNo)}}" title="Call Us On : {{str_replace(' ','',$objContactInfo[0]->varMobileNo)}}">{{$objContactInfo[0]->varMobileNo}}</a>
                            </li>
                            @endif

                        </ul>

                    </div>
                </div>
                <div class="col-12 col-lg-3 col-md-12 text-center text-lg-left text-md-center items py-3 py-md-0" data-aos="fade-in">
                    <ul class="footer_images">

                        <li>
                            @if(null!==(Config::get('Constant.SOCIAL_ECAYONLINE_LINK')) &&
                            strlen(Config::get('Constant.SOCIAL_ECAYONLINE_LINK')) > 0)
                            <a href="{{ Config::get('Constant.SOCIAL_ECAYONLINE_LINK') }}" target="_blank" title="Follow Us On Ecayonline"><img src="{{ url('/') }}/{{ ('assets/images/logo.png') }}" alt="ecay online"> </a>
                            @endif
                        </li>
                        <li class="footer_view-boat">
                            @if(null!==(Config::get('Constant.SOCIAL_YACHTWORLD_LINK')) &&
                            strlen(Config::get('Constant.SOCIAL_YACHTWORLD_LINK')) > 0)
                            <a href="{{ Config::get('Constant.SOCIAL_YACHTWORLD_LINK') }}" target="_blank" title="Follow Us On Yacht World">
                                <img src="{{ url('/') }}/{{ ('assets/images/Group-9969.png') }}" alt=" view boat">
                            </a>
                            @endif
                        </li>
                    </ul>
                </div>
            </div>
        </div>
        <div class="footer-bottom">
            <div class="row">
                <div class="col-lg-6 col-md-12 col-sm-12 col-xs-12">
                    <ul class="f-m_link">
                        @foreach($FooterMenu as $menuDetail)
                        @php
                        $page=\App\Helpers\static_block::get_page_title($menuDetail->intPageId);
                        $class='';
                        @endphp
                        @if(Request::segment(1) == $menuDetail->txtPageUrl)
                        @php $class='active';@endphp
                        @endif
                        @if(isset($page) && count($page) > 0)
                        <li><a class="{{$class}}" href="{{ url($page['data']->varAlias) }}" title="{{$menuDetail->varTitle}}">{{$menuDetail->varTitle}}</a></li>
                        @endif
                        @endforeach
                    </ul>
                </div>
                <!-- <div class="col-lg-3 col-md-12 col-sm-12 col-xs-12">
                    <div class="f-m_copyright">&copy; <?php echo date("Y"); ?> {{ Config::get("Constant.SITE_NAME") }}.
                    </div>

                </div> -->
                <div class="col-lg-6 col-md-12 col-sm-12 col-xs-12">
                    <div class="social-craft-wrap">
                        <div class="footer_box quick_box">
                            @php $socialAvailable = false; @endphp
                            @if((null!==Config::get('Constant.SOCIAL_FB_LINK') &&
                            strlen(Config::get('Constant.SOCIAL_FB_LINK')) > 0) ||
                            (null!==Config::get('Constant.SOCIAL_TWITTER_LINK') &&
                            strlen(Config::get('Constant.SOCIAL_TWITTER_LINK')) > 0) ||
                            (null!==Config::get('Constant.SOCIAL_YOUTUBE_LINK') &&
                            strlen(Config::get('Constant.SOCIAL_YOUTUBE_LINK')) > 0))
                            @php $socialAvailable = true; @endphp
                            <ul class="social">
                                @if(null!==(Config::get('Constant.SOCIAL_FB_LINK')) &&
                                strlen(Config::get('Constant.SOCIAL_FB_LINK')) > 0)
                                <li><a href="{{ Config::get('Constant.SOCIAL_FB_LINK') }}" title="Follow Us On Facebook" target="_blank"><i class="fa fa-facebook" aria-hidden="true"></i></a></li>
                                @endif
                                @if(null!==(Config::get('Constant.SOCIAL_TWITTER_LINK')) &&
                                strlen(Config::get('Constant.SOCIAL_TWITTER_LINK')) > 0)
                                <li><a href="{{ Config::get('Constant.SOCIAL_TWITTER_LINK') }}" title="Follow Us On Twitter" target="_blank"><i class="fa fa-twitter"></i></a>
                                </li>
                                @endif
                                @if(null!==(Config::get('Constant.SOCIAL_INSTAGRAM_LINK')) &&
                                strlen(Config::get('Constant.SOCIAL_INSTAGRAM_LINK')) > 0)
                                <li><a href="{{ Config::get('Constant.SOCIAL_INSTAGRAM_LINK') }}" title="Follow Us On Instagram" target="_blank"><i class="fa fa-instagram"></i></a></li>
                                @endif
                                @if(null!==(Config::get('Constant.SOCIAL_PINTEREST_LINK')) &&
                                strlen(Config::get('Constant.SOCIAL_PINTEREST_LINK')) > 0)
                                <li><a href="{{ Config::get('Constant.SOCIAL_PINTEREST_LINK') }}" title="Follow Us On Pinterest" target="_blank"><i class="fa fa-pinterest"></i></a></li>
                                @endif
                                @if(null!==(Config::get('Constant.SOCIAL_YELP_LINK')) &&
                                strlen(Config::get('Constant.SOCIAL_YELP_LINK')) > 0)
                                <li><a href="{{ Config::get('Constant.SOCIAL_YELP_LINK') }}" title="Follow Us On Yelp" target="_blank"><i class="fa fa-yelp"></i></a></li>
                                @endif
                            </ul>
                            @endif
                        </div>
                        <div class="f-m_copyright">&copy; <?php echo date("Y"); ?> {{ Config::get("Constant.SITE_NAME") }}.
                    </div>
                        <div class="f-m_designed">Crafted By: <a class="f-m_d_logo" href="https://www.netclues.ky" target="_blank" rel="noopener noreferrer nofollow" title="Netclues!"></a></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</footer>
@if(Config::get('Constant.DEFAULT_FEEDBACKFORM') == "Y")
<div class="feedback_form">
    <div style="top:calc(50% - 62px);" class="feedback_icon" id='feedback_form_model' title="Feedback" data-toggle="modal" data-target="#exampleModal"><i class="fa fa-comments"></i></div>
</div>
@endif
<!-- Messenger Chat Plugin Code -->
<div id="fb-root"></div>

<!-- Your Chat Plugin code -->
<div id="fb-customer-chat" class="fb-customerchat">
</div>

