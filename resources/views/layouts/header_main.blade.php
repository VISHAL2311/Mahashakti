<!-- Header Section -->
<header>
    <div class="header-section">
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-12">
                    <div class="h-s__row">
                        <div class="h-s__logo pull-left" itemscope="" itemtype="http://schema.org/Organization">
                            <a href="{{ url('/') }}" title="{{ Config::get("Constant.SITE_NAME") }}">
                                <meta itemprop="name" content="{{ Config::get("Constant.SITE_NAME") }}">
                                <meta itemprop="address" content="{{(isset($objContactInfo->txtAddress) && !empty($objContactInfo->txtAddress))?$objContactInfo->txtAddress:''}}">
                                @php $segment = Request::segment(2); @endphp
                                @if($segment == 'aceessdenied')
                                <img src="{{ url('/') }}/{{ ('assets/images/logo.png') }}" alt="Douglas Construction" title="Douglas Construction">
                                @else
                                <picture>
                                    <source type="image/webp" data-srcset="{!! App\Helpers\LoadWebpImage::resize(Config::get('Constant.FRONT_LOGO_ID')) !!}" srcset="{!! App\Helpers\LoadWebpImage::resize(Config::get('Constant.FRONT_LOGO_ID')) !!}">
                                    <img class="lazy" data-src="{{ App\Helpers\resize_image::resize(Config::get('Constant.FRONT_LOGO_ID'))}}" src="{!! url('assets/images/loader.gif') !!}" alt="{{ Config::get("Constant.SITE_NAME") }}">
                                </picture>
                                {{-- <img itemprop="image" src="{{ App\Helpers\resize_image::resize(Config::get('Constant.FRONT_LOGO_ID')) }}" alt="{{ Config::get("Constant.SITE_NAME") }}"> --}}
                                @endif
                            </a>
                        </div>
                        <div class="h-s__search pull-right">
                            <div class="nav-overlay" onclick="closeNav()"></div>
                            <div class="menu_open_close  text-right">
                                <a href="javascript:void(0)" class="menu__open" id="menu__open" onclick="openNav()"><span></span></a>
                                <a href="javascript:void(0)" class="menu__close" id="menu__close" onclick="closeNav()"><span></span></a>
                            </div>
                            <div class="contact-mobile d-none">
                                @if(isset($objContactInfo) && !empty($objContactInfo))
                                @php
                                $phone = unserialize($objContactInfo[0]->varPhoneNo);
                                $phone = count($phone)>0?$phone[0]:$phone;
                                $phone1 = str_replace(' ','',$phone);
                                @endphp
                                @endif
                                <ul>
                                    @if(isset($phone) && !empty($phone))
                                    <li>
                                        <a href="tel:{{$phone1}}" title="Call Us On : {{$phone1}}"><i class="icon-call"></i></a>
                                    </li>
                                    @endif
                                </ul>
                            </div>
                            {{-- <a href="javascript:void(0)" title="Search" data-toggle="modal" data-target="#search_box" data-backdrop="static" class="top_search"><i class="fa fa-search"></i></a> --}}
                        </div>
                        <div class="menu-wrap">
                            <div class="h-s__menu">
                                <nav class="menu brand-center" id="menu">
                                    <div class="menu_mobile_visibility">
                                        <a href="{{ Config::get("Constant.SITE_PATH") }}" title="{{ Config::get("Constant.SITE_NAME") }}">
                                            <div class="menu_title">
                                                <img src="{{ App\Helpers\resize_image::resize(Config::get('Constant.FRONT_LOGO_ID')) }}" alt="{{ Config::get("Constant.SITE_NAME") }}">
                                            </div>
                                        </a>
                                    </div>
                                    @if(isset($HeadreMenuhtml))
                                    {!! $HeadreMenuhtml !!}
                                    @endif
                                    <div class="d-xl-none d-md-block d-sm-block mobile-social-contact">
                                        <div class="contact-menu ">
                                            @if(isset($objContactInfo) && !empty($objContactInfo))
                                            @php
                                            $email = unserialize($objContactInfo[0]->varEmail);
                                            $email = count($email)>0?$email[0]:$email;
                                            $phone = unserialize($objContactInfo[0]->varPhoneNo);
                                            $phone = count($phone)>0?$phone[0]:$phone;
                                            $phone1 = str_replace(' ','',$phone);
                                            @endphp
                                            @endif
                                            <div class="action_menu" onclick="actionToggle();">
                                                
                                                <ul> 
                                                @if(isset($phone) && !empty($phone))
                                                    <li><a href="tel:{{ $phone1 }}" title="Call Us On : {{ $phone1 }}"> <i class="icon-call"></i>{{$phone1}}</a></li>
                                                    @endif
                                                    @if(isset($objContactInfo) && !empty($objContactInfo[0]->varMobileNo))
                                                    <li><a href="tel:{{str_replace(' ','',$objContactInfo[0]->varMobileNo)}}" title="Call Us On : {{str_replace(' ','',$objContactInfo[0]->varMobileNo)}}"><i class="icon-call"></i>{{$objContactInfo[0]->varMobileNo}}</a></li>
                                                    @endif
                                                    @if(isset($email) && !empty($email))
                                                    <li><a href="mailto:{{ $email }}" title="Email Us On : {{ $email }}"><i class="fa fa-envelope-o" aria-hidden="true"></i>{{$email}}</a></li>
                                                    @endif
                                                    @if(null!==(Config::get('Constant.SOCIAL_FB_LINK')) && strlen(Config::get('Constant.SOCIAL_FB_LINK')) > 0)
                                                    <li><a href="{{ Config::get('Constant.SOCIAL_FB_LINK') }}" title="Follow Us On Facebook" target="_blank"><i class="fa fa-facebook"></i></a></li>
                                                    @endif
                                                </ul>
                                            </div>

                                        </div>
                                    </div>
                                </nav>
                            </div>
                            <div class="contact-info">
                                <ul>
                                    @if(isset($phone) && !empty($phone))
                                    <li>
                                        <a href="tel:{{$phone1}}" title="Call Us On : {{$phone1}}"><span class="number">{{$phone}}</span><span class="icon"><i class="icon-call"></i></span></a>
                                    </li>
                                    @endif
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</header>

<script type="text/javascript">
    function actionToggle() {
        var action = document.querySelector('.action_menu');
        action.classList.toggle('active')
    }
</script>