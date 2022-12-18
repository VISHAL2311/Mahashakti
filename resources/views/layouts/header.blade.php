@php

$CDN_PATH = Config::get('Constant.CDN_PATH');

$requestedFullUrl = Request::Url();

$homePageUrl = url('/');

$seg = request()->segments();

@endphp

<!DOCTYPE html>

<html lang="en-US">

    <head>

    @if(!empty(Config::get('Constant.GOOGLE_ANALYTIC_CODE'))) {!! Config::get('Constant.GOOGLE_ANALYTIC_CODE') !!} @endif

        <meta http-equiv="Content-Type" content="text/html; charset=utf-8">

        <meta http-equiv="X-UA-Compatible" content="IE=edge" />

        <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1, user-scalable=0" />

        <title>{!! str_replace('&amp;', '&',$META_TITLE) !!}</title>

        <meta name="title" content="{!! str_replace('&amp;', '&',$META_TITLE) !!}">

        <meta name="keywords" content="{!! str_replace('&amp;', '&',$META_KEYWORD) !!}">

        <meta name="description" content="{!! str_replace('&amp;', '&',$META_DESCRIPTION) !!}">

        <!-- <meta name="author" content="" /> -->

        @if(!empty($site_monitor))

        <meta name="Monitoring" content="{{ $site_monitor->varTitle }}">

        @endif

        <meta property="og:url" content="{{ Request::Url() }}" />

        <meta property="og:type" content="website" />

        <meta property="og:title" content="{!! str_replace('&amp;', '&',$META_TITLE) !!}" />

        <meta property="og:description" content="{!! str_replace('&amp;', '&',$META_DESCRIPTION) !!}" />

        <meta property="og:image" content="{{ $CDN_PATH.'assets/images/sharelogo.png' }}" />

        <meta name="twitter:card" content="summary_large_image" />

        <meta name="twitter:title" content="{!! str_replace('&amp;', '&',$META_TITLE) !!}" />

        <meta name="twitter:url" content="{{ Request::Url() }}" />

        <meta name="twitter:description" content="{!! str_replace('&amp;', '&',$META_DESCRIPTION) !!}" />

        <meta name="twitter:image" content="{{ $CDN_PATH.'assets/images/sharelogo.png' }}" />

        <meta name="csrf-token" content="{{ csrf_token() }}">

        <link rel="canonical" href="{{ Request::Url() }}" />

        <link rel="stylesheet" href="{{ $CDN_PATH.'assets/css/main.css' }}?{{ Config::get('Constant.VERSION') }}" media="all" />

        <!-- swiper css -->

        <link rel="stylesheet" href="{{ $CDN_PATH.'assets/libraries/swiper/css/swiper-bundle.min.css' }}?{{ Config::get('Constant.VERSION') }}" media="all" />

        <!-- Fonts S -->

        <link rel="dns-prefetch" href="https://fonts.googleapis.com">

        <link rel="preload" as="font" href="{{ $CDN_PATH.'assets/fonts/fontawesome-webfont.woff2' }}?{{ Config::get('Constant.VERSION') }}" type="font/woff" crossorigin="anonymous" />

        <link rel="preload" as="font" href="{{ $CDN_PATH.'assets/fonts/icomoon.ttf' }}?{{ Config::get('Constant.VERSION') }}" type="font/woff" crossorigin="anonymous" />

        <link href="https://fonts.googleapis.com/css2?family=Jost:wght@300;400;500;600&display=swap" rel="stylesheet">

        <!-- Fonts E -->



        <!-- Favicon Icon S -->

        <link rel="icon" href="{{ $CDN_PATH.'assets/images/favicon_package/favicon.ico' }}" type="image/x-icon" />

        <link rel="apple-touch-icon" sizes="180x180" href="{{ $CDN_PATH.'assets/images/favicon_package/apple-touch-icon.png' }}" />

        <link rel="apple-touch-icon" sizes="32x32" href="{{ $CDN_PATH.'assets/images/favicon_package/favicon-32x32.png' }}" />

        <link rel="apple-touch-icon" sizes="16x16" href="{{ $CDN_PATH.'assets/images/favicon_package/favicon-16x16.png' }}" />

        <!-- Favicon Icon E -->



        <!-- Java Script S -->

        <script src="{{ $CDN_PATH.'assets/js/jquery.min.js' }}?{{ Config::get('Constant.VERSION') }}" rel="preload"></script>

        <!-- Java Script E -->



        

        <!-- Fonts E -->

        <!--[if IE 8]>     <html class="ie8"> <![endif]-->

        <script>

            var site_url = "{{ url('/') }}";

            var deviceType = "{{ Config::get('Constant.DEVICE') }}";

            var segments = {!! json_encode($seg) !!};

            var CDN_PATH = "{{ $CDN_PATH }}";

            $.ajaxSetup({

                headers: {

                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')

                }

            });

        </script>

        <style>

            .form_nxt_prev ul {

                margin: 0;

                padding: 0px 0 20px 0;

                left: auto;

                text-align: center;

            }

            .form_nxt_prev ul li {

                list-style: none;

                margin:0 5px;

                padding: 7px 0px;

                background: #1c4da0;

                display: inline-block;

                color: #fff;

                border-radius: 20px;

                font-weight: 600;

                letter-spacing: 1px;

                min-width: 125px;

                text-align: center;

            }</style>

    </head>

    <!-- Body S -->

    <body onload="checkCookie_Footer()">

    @if(!empty(Config::get('Constant.GOOGLE_TAG_MANAGER_FOR_BODY'))) {!! Config::get('Constant.GOOGLE_TAG_MANAGER_FOR_BODY') !!} @endif



        <!-- Browser Upgrade S -->



        <div id="ie-note" class="ie-note">



            <div class="container">



                <div class="-ie-item">



                    <div class="-iep1">



                        <div class="d-flex align-items-center">



                            <div class="-icon">



                                <img src="{{$CDN_PATH.'assets/images/edge.png'}}" alt="Edge">



                            </div>



                            <div class="-text">



                                <div class="-t1">Use the latest browser recommended by Microsoft </div>



                                <div class="-t2">Get speed, security and privacy with Microsoft Edge</div>



                            </div>



                        </div>



                    </div>



                    <div class="-iep2 -btn">



                        <a class="-close" id="ie-close" href="#" title="Close">Close</a>



                        <a class="-launch" href="Microsoft-edge:https://go.microsoft.com/fwlink/?linkid=2156983&pc=EE05&form=MY01TE&OCID=MY01TE" target="_blank" rel="noopener noreferrer" title="Launch now">Launch now</a>



                    </div>



                </div>



            </div>



        </div>



        <!-- Browser Upgrade E -->

        <!-- Loader S -->

        <div class="loader-n" style="display: none;">

        <!-- <div class="lds-roller"><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div></div> -->

            <!-- <img src="{{ $CDN_PATH.'/assets/images/A.png' }} "> -->

            <img src="{!! url('assets/images/A.png') !!}" alt="A image">

            

        </div>

        <!-- Loader E -->

        <!-- Browser Upgrade S -->

        <div id="buorg" class="buorg">

            <div class="buorg__text"><i class="fa fa-exclamation-triangle"></i> For a better view on

                {{ Config::get("Constant.SITE_NAME") }}, <a href="https://support.microsoft.com/en-us/help/17621/internet-explorer-downloads" title="Update Your Browser" target="_blank">Update Your Browser.</a></div>

        </div>

        <!-- Browser Upgrade E -->

        

        <!-- Live Chat S -->

        <!--<div class="live_chat">

            <a href="#" title="Live Chat"><i class="fa fa-comments-o"></i><span>Live Chat</span></a>

        </div> -->

        <!-- Live Chat E -->

        <!-- Main Wrapper S -->

        <div id="wrapper">

        {{-- <div class="messanger">

            <a href="#"><span class="icon-messenger"><span class="path1"></span><span class="path2"></span><span class="path3"></span></span></a>

        </div> --}}