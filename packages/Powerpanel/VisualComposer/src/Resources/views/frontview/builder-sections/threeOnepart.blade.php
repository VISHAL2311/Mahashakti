@if($data['Columns'] == '1')
<div class='row'>
    @endif
    @if($data['subtype'] == 'TwoColumns_1')
    <div class='col-sm-12 col-md-12 col-lg-12 col-xl-9'>
        <div class='card-box'>
            @if($data['type'] == 'onlyimage')
            @if(isset($data['content']['image']) && $data['content']['image'] != '')
            @if(isset($data['content']['alignment']) && $data['content']['alignment'] == 'image-lft-txt')
            <div class="left_img_cms">
                <picture>
                    <source type="image/webp" data-srcset="{!! App\Helpers\LoadWebpImage::resize($data['content']['image']) !!}" srcset="{!! App\Helpers\LoadWebpImage::resize($data['content']['image']) !!}">
                    <img class="lazy" data-src="{{ App\Helpers\resize_image::resize($data['content']['image']) }}" src="{{ App\Helpers\resize_image::resize($data['content']['image']) }}" alt="{{ htmlspecialchars_decode($data['content']['title']) }}" title="{{ htmlspecialchars_decode($data['content']['title']) }}">
                </picture>
            </div>
            @elseif(isset($data['content']['alignment']) && $data['content']['alignment'] == 'image-rt-txt')
            <div class="right_img_cms">
                <picture>
                    <source type="image/webp" data-srcset="{!! App\Helpers\LoadWebpImage::resize($data['content']['image']) !!}" srcset="{!! App\Helpers\LoadWebpImage::resize($data['content']['image']) !!}">
                    <img class="lazy" data-src="{{ App\Helpers\resize_image::resize($data['content']['image']) }}" src="{{ App\Helpers\resize_image::resize($data['content']['image']) }}" alt="{{ htmlspecialchars_decode($data['content']['title']) }}" title="{{ htmlspecialchars_decode($data['content']['title']) }}">
                </picture>
            </div>
            @elseif(isset($data['content']['alignment']) && $data['content']['alignment'] == 'image-center-txt')
            <div class="center_img_cms">
                <picture>
                    <source type="image/webp" data-srcset="{!! App\Helpers\LoadWebpImage::resize($data['content']['image']) !!}" srcset="{!! App\Helpers\LoadWebpImage::resize($data['content']['image']) !!}">
                    <img class="lazy" data-src="{{ App\Helpers\resize_image::resize($data['content']['image']) }}" src="{{ App\Helpers\resize_image::resize($data['content']['image']) }}" alt="{{ htmlspecialchars_decode($data['content']['title']) }}" title="{{ htmlspecialchars_decode($data['content']['title']) }}">
                </picture>
            </div>
            @endif
            @endif
            @endif


            @if($data['type'] == 'onlydocument')
            <!-- Only Document -->
            @if(isset($data['content']['document']) && $data['content']['document'] != '')
            @if(!empty($data['content']['document']))
            <div class="ac-mb-xs-15"></div>
            <div class="download_files clearfix">
                @php
                $docsAray = explode(',', $data['content']['document']);
                $docObj = App\Document::getDocDataByIds($docsAray);
                @endphp
                @if(count($docObj) > 0)
                <ul>
                    @foreach($docObj as $key => $val)
                    @php
                    if($val->varDocumentExtension == 'pdf' || $val->varDocumentExtension == 'PDF'){
                    $blank = 'target="_blank"';
                    }else{
                    $blank = '';
                    }
                    if($val->varDocumentExtension == 'pdf' || $val->varDocumentExtension == 'PDF'){
                    $icon = "fi flaticon-pdf-file";
                    }elseif($val->varDocumentExtension == 'doc' || $val->varDocumentExtension == 'docx'){
                    $icon = "fi flaticon-doc-file";
                    }elseif($val->varDocumentExtension == 'xls' || $val->varDocumentExtension == 'xlsx'){
                    $icon = "fi flaticon-xls-file";
                    }else{
                    $icon = "fi flaticon-doc-file";
                    }
                    @endphp
                    <li><a {!! $blank !!} href="{{ $CDN_PATH.'documents/'.$val->txtSrcDocumentName.'.'.$val->varDocumentExtension }}" title="{{ $val->txtDocumentName }}.{{ $val->varDocumentExtension }}"><i class="{{ $icon }}"></i>{{ $val->txtDocumentName }}.{{ $val->varDocumentExtension }}</a></li>
                    @endforeach
                </ul>
                @endif
            </div>
            @endif
            @endif
            @endif


            @if($data['type'] == 'imgcontent')
            <!-- Only Left Text and Right Image -->
            @if(isset($data['content']['alignment']) && $data['content']['alignment'] == 'lft-txt')
            <div class="left_img_cms">
                <picture>
                    <source type="image/webp" data-srcset="{!! App\Helpers\LoadWebpImage::resize($data['content']['image']) !!}" srcset="{!! App\Helpers\LoadWebpImage::resize($data['content']['image']) !!}">
                    <img class="lazy" data-src="{{ App\Helpers\resize_image::resize($data['content']['image']) }}" src="{{ App\Helpers\resize_image::resize($data['content']['image']) }}" alt="{{ htmlspecialchars_decode($data['content']['title']) }}" title="{{ htmlspecialchars_decode($data['content']['title']) }}">
                </picture>
            </div>
            @if($data['content']['title'] != '')
            <h5>{{ $data['content']['title'] }}</h5>
            @endif
            {!! $data['content']['content'] !!}
            <!-- Only Right Text and Left Image -->
            @elseif(isset($data['content']['alignment']) && $data['content']['alignment'] == 'rt-txt')
            <div class="right_img_cms">
                <picture>
                    <source type="image/webp" data-srcset="{!! App\Helpers\LoadWebpImage::resize($data['content']['image']) !!}" srcset="{!! App\Helpers\LoadWebpImage::resize($data['content']['image']) !!}">
                    <img class="lazy" data-src="{{ App\Helpers\resize_image::resize($data['content']['image']) }}" src="{{ App\Helpers\resize_image::resize($data['content']['image']) }}" alt="{{ htmlspecialchars_decode($data['content']['title']) }}" title="{{ htmlspecialchars_decode($data['content']['title']) }}">
                </picture>
            </div>
            @if($data['content']['title'] != '')
            <h5>{{ $data['content']['title'] }}</h5>
            @endif
            {!! $data['content']['content'] !!}
            <!-- Only Top Image -->
            @elseif(isset($data['content']['alignment']) && $data['content']['alignment'] == 'top-txt')
            <picture class="two_clm_top">
                <source type="image/webp" data-srcset="{!! App\Helpers\LoadWebpImage::resize($data['content']['image']) !!}" srcset="{!! App\Helpers\LoadWebpImage::resize($data['content']['image']) !!}">
                <img class="lazy" data-src="{{ App\Helpers\resize_image::resize($data['content']['image']) }}" src="{{ App\Helpers\resize_image::resize($data['content']['image']) }}" alt="{{ htmlspecialchars_decode($data['content']['title']) }}" title="{{ htmlspecialchars_decode($data['content']['title']) }}">
            </picture>
            @if($data['content']['title'] != '')
            <h5>{{ $data['content']['title'] }}</h5>
            @endif
            {!! $data['content']['content'] !!}
            <!-- Only Bottom Image -->

            @elseif(isset($data['content']['alignment']) && $data['content']['alignment'] == 'bot-txt')
            @if($data['content']['title'] != '')
            <div class="responsive__bottom">

            </div>
            <h5>{{ $data['content']['title'] }}</h5>
            @endif
            {!! $data['content']['content'] !!}
            <div class="ac-mb-xs-15"></div>
            <picture class="two_clm_top">
                <source type="image/webp" data-srcset="{!! App\Helpers\LoadWebpImage::resize($data['content']['image']) !!}" srcset="{!! App\Helpers\LoadWebpImage::resize($data['content']['image']) !!}">
                <img class="lazy" data-src="{{ App\Helpers\resize_image::resize($data['content']['image']) }}" src="{{ App\Helpers\resize_image::resize($data['content']['image']) }}" alt="{{ htmlspecialchars_decode($data['content']['title']) }}" title="{{ htmlspecialchars_decode($data['content']['title']) }}">
            </picture>
            @elseif(isset($data['content']['alignment']) && $data['content']['alignment'] == 'center-txt')

            <div class="ac-mb-xs-15"></div>
            <picture class="two_clm_top two_clm_center">
                <source type="image/webp" data-srcset="{!! App\Helpers\LoadWebpImage::resize($data['content']['image']) !!}" srcset="{!! App\Helpers\LoadWebpImage::resize($data['content']['image']) !!}">
                <img class="lazy" data-src="{{ App\Helpers\resize_image::resize($data['content']['image']) }}" src="{{ App\Helpers\resize_image::resize($data['content']['image']) }}" alt="{{ htmlspecialchars_decode($data['content']['title']) }}" title="{{ htmlspecialchars_decode($data['content']['title']) }}">
            </picture>
            @if($data['content']['title'] != '')
            <h5>{{ $data['content']['title'] }}</h5>
            @endif
            {!! $data['content']['content'] !!}

            @endif
            @endif


            @if($data['type'] == 'videocontent')
            <!-- Only Left Text and Right Video -->
            @if(isset($data['content']['title']) && $data['content']['alignment'] == 'lft-txt')
            <div class='col-sm-12'>
                <!-- <div class="row"> -->
                <div class="cms about-left animated fadeInLeft">
                    <div class="about_image one_three_second">
                        <iframe width="100%" height="315" src="{{ $data['content']['vidId'] }}?rel=0;&autoplay=1" frameborder="0" allow="autoplay;"></iframe>
                    </div>
                </div>
                <div class="cms about-left animated fadeInRight">
                    @if($data['content']['title'] != '')
                    <div class="same_title">
                        <h2 class="title_div">{{ $data['content']['title'] }}</h2>
                    </div>
                    @endif
                    <div class="info">
                        {!! $data['content']['content'] !!}
                    </div>
                </div>
                <!-- </div> -->
            </div>
            <!-- Only Right Text and Left Video -->
            @elseif(isset($data['content']['title']) && $data['content']['alignment'] == 'rt-txt')
            <div class='col-sm-12'>
                <!-- <div class="row"> -->
                <div class="cms about-left animated fadeInRight hidden-xs">
                    <div class="about_image one_right_second">
                        <iframe width="100%" height="315" src="{{ $data['content']['vidId'] }}?rel=0;&autoplay=1" frameborder="0" allow="autoplay;"></iframe>
                    </div>
                </div>

                <div class="cms about-left animated fadeInLeft">
                    @if($data['content']['title'] != '')
                    <div class="same_title">
                        <h2 class="title_div">{{ $data['content']['title'] }}</h2>
                    </div>
                    @endif
                    <div class="info">
                        {!! $data['content']['content'] !!}
                    </div>
                </div>

                <!-- </div> -->
            </div>
            <!-- Only Top Video -->
            @elseif(isset($data['content']['title']) && $data['content']['alignment'] == 'top-txt')
            <div class="cms about-left animated fadeInUp">
                <div class='about_full'>
                    <div class="about_image about_image one_three_top_first">
                        <iframe width="100%" height="315" src="{{ $data['content']['vidId'] }}?rel=0;&autoplay=1" frameborder="0" allow="autoplay;"></iframe>
                    </div>
                    @if($data['content']['title'] != '')
                    <div class="same_title">
                        <h2 class="title_div">{{ $data['content']['title'] }}</h2>
                    </div>
                    @endif
                    <div class="info">
                        {!! $data['content']['content'] !!}
                    </div>
                </div>
            </div>
            <!-- Only Bottom Video -->
            @elseif(isset($data['content']['title']) && $data['content']['alignment'] == 'bot-txt')
            <div class="cms about-left animated fadeInUp">
                <div class='about_full about_respons'>
                    @if($data['content']['title'] != '')
                    <div class="same_title">
                        <h2 class="title_div">{{ $data['content']['title'] }}</h2>
                    </div>
                    @endif
                    <div class="info">
                        {!! $data['content']['content'] !!}
                    </div>
                    <div class="about_image one_three_top_first">
                        <iframe width="100%" height="315" src="{{ $data['content']['vidId'] }}?rel=0;&autoplay=1" frameborder="0" allow="autoplay;"></iframe>
                    </div>
                </div>
            </div>
            @elseif(isset($data['content']['title']) && $data['content']['alignment'] == 'center-txt')

            <div class="col-sm-12 col-xs-12 cms about-left animated fadeInUp">
                <div class='about_full'>
                    <div class="about_image one_three_top_first one_clm_center">
                        <iframe width="100%" height="315" src="{{ $data['content']['vidId'] }}?rel=0;&autoplay=1" frameborder="0" allow="autoplay;"></iframe>
                    </div>
                    @if($data['content']['title'] != '')
                    <div class="same_title">
                        <h2 class="title_div">{{ $data['content']['title'] }}</h2>
                    </div>
                    @endif
                    <div class="info">
                        {!! $data['content']['content'] !!}
                    </div>
                </div>
            </div>
            @endif
            @endif

            @if($data['type'] == 'buttondata')
            <!-- Only Right Button -->
            @if(isset($data['content']['alignment']) && $data['content']['alignment'] == 'button-rt-txt')
            <div class="animated fadeInUp text-right load">
                <a class="btn ac-border " href="{{ $data['content']['content'] }}" target='{{ $data['content']['target'] }}' title="{{ $data['content']['title'] }}">{{ $data['content']['title'] }}</a>
            </div>
            <!-- Only Left Button -->
            @elseif(isset($data['content']['alignment']) && $data['content']['alignment'] == 'button-lft-txt')
            <div class="animated fadeInUp text-left load">
                <a class="btn ac-border " href="{{ $data['content']['content'] }}" target='{{ $data['content']['target'] }}' title="{{ $data['content']['title'] }}">{{ $data['content']['title'] }}</a>
            </div>
            <!-- Only Center Button -->
            @elseif(isset($data['content']['alignment']) && $data['content']['alignment'] == 'button-center-txt')
            <div class="animated fadeInUp text-center load">
                <a class="btn ac-border " href="{{ $data['content']['content'] }}" target='{{ $data['content']['target'] }}' title="{{ $data['content']['title'] }}">{{ $data['content']['title'] }}</a>
            </div>
            @endif
            @endif


            @if($data['type'] == 'twotextarea')
            <!-- 2 Part Content -->
            @if(isset($data['content']['leftcontent']) && $data['content']['leftcontent'] != '')
            <div class="row">
                <div class="col-sm-6 col-md-6">
                    {!! $data['content']['leftcontent'] !!}
                </div>
                <div class="col-sm-6 col-md-6">
                    {!! $data['content']['rightcontent'] !!}
                </div>
            </div>
            @endif
            @endif

            @if($data['type'] == 'textarea')
            <!-- Only Content -->
            @if(isset($data['content']['content']) && $data['content']['content'] != '')
            {!! $data['content']['content'] !!}
            @endif
            @endif


            @if($data['type'] == 'onlyvideo')
            <!-- Only Video -->
            @if(isset($data['content']['vidId']) && $data['content']['vidId'] != '')
            <h5>{{ $data['content']['title'] }}</h5>
            <br />
            <iframe width="100%" height="315" src="{{ $data['content']['vidId'] }}?rel=0;&autoplay=1" frameborder="0" allow="autoplay;"></iframe>
            @endif
            @endif


            @if($data['type'] == 'mapdata')
            <!-- Only Map -->
            @if(isset($data['content']['latitude']) && $data['content']['latitude'] != '')
            <div class="location_map">
                <iframe src="https://maps.google.com/maps?q={{ $data['content']['latitude'] }}, {{ $data['content']['longitude'] }}&output=embed&zoom=9" width="100%" height="300" frameborder="0" style="border:0"></iframe>
            </div>
            @endif
            @endif


            @if($data['type'] == 'contactinfodata')
            <!-- Only Contact Info -->
            @if(isset($data['content']['section_address']) && $data['content']['section_address'] != '')

            <div class="mailing_box animated fadeInUp load">
                @if(isset($data['content']['section_address']) && $data['content']['section_address'] != '')
                <h4>Address</h4>
                <p>{{ $data['content']['section_address'] }}</p>
                @endif
                <p>
                    @if(isset($data['content']['section_email']) && $data['content']['section_email'] != '')
                    <b>Email:-</b> <a href="mailto:{{ $data['content']['section_email'] }}" title="{{ $data['content']['section_email'] }}">{{ $data['content']['section_email'] }}</a><br>
                    @endif
                    @if(isset($data['content']['section_phone']) && $data['content']['section_phone'] != '')
                    <b>Phone:-</b><a href="tel:{{ $data['content']['section_phone'] }}"> {{ $data['content']['section_phone'] }}</a><br>
                    @endif
                </p>
                @if(isset($data['content']['content']) && $data['content']['content'] != '')
                <p>{!! $data['content']['content'] !!}</p>
                @endif
            </div>

            @endif
            @endif


            @if($data['type'] == 'onlytitle')
            <!-- Only Title -->
            @if(isset($data['content']['content']) && $data['content']['content'] != '')
            <h2>
                {!! $data['content']['content'] !!}
            </h2>
            @endif
            @endif

            @if($data['type'] == 'formdata')
            @include('layouts.builder-sections.formbuilder')
            @endif


        </div>
    </div>
    @endif
    @if($data['subtype'] == 'TwoColumns_2')
    <div class='col-sm-12 col-md-12 col-lg-12 col-xl-3'>
        <div class='card-box'>
            @if($data['type'] == 'onlyimage')
            @if(isset($data['content']['image']) && $data['content']['image'] != '')
            @if(isset($data['content']['alignment']) && $data['content']['alignment'] == 'image-lft-txt')
            <div class="left_img_cms">
                <picture>
                    <source type="image/webp" data-srcset="{!! App\Helpers\LoadWebpImage::resize($data['content']['image']) !!}" srcset="{!! App\Helpers\LoadWebpImage::resize($data['content']['image']) !!}">
                    <img class="lazy" data-src="{{ App\Helpers\resize_image::resize($data['content']['image']) }}" src="{{ App\Helpers\resize_image::resize($data['content']['image']) }}" alt="{{ htmlspecialchars_decode($data['content']['title']) }}" title="{{ htmlspecialchars_decode($data['content']['title']) }}">
                </picture>
            </div>
            @elseif(isset($data['content']['alignment']) && $data['content']['alignment'] == 'image-rt-txt')
            <div class="right_img_cms">
                <picture>
                    <source type="image/webp" data-srcset="{!! App\Helpers\LoadWebpImage::resize($data['content']['image']) !!}" srcset="{!! App\Helpers\LoadWebpImage::resize($data['content']['image']) !!}">
                    <img class="lazy" data-src="{{ App\Helpers\resize_image::resize($data['content']['image']) }}" src="{{ App\Helpers\resize_image::resize($data['content']['image']) }}" alt="{{ htmlspecialchars_decode($data['content']['title']) }}" title="{{ htmlspecialchars_decode($data['content']['title']) }}">
                </picture>
            </div>
            @elseif(isset($data['content']['alignment']) && $data['content']['alignment'] == 'image-center-txt')
            <div class="center_img_cms">
                <picture>
                    <source type="image/webp" data-srcset="{!! App\Helpers\LoadWebpImage::resize($data['content']['image']) !!}" srcset="{!! App\Helpers\LoadWebpImage::resize($data['content']['image']) !!}">
                    <img class="lazy" data-src="{{ App\Helpers\resize_image::resize($data['content']['image']) }}" src="{{ App\Helpers\resize_image::resize($data['content']['image']) }}" alt="{{ htmlspecialchars_decode($data['content']['title']) }}" title="{{ htmlspecialchars_decode($data['content']['title']) }}">
                </picture>
            </div>
            @endif
            @endif
            @endif


            @if($data['type'] == 'onlydocument')
            <!-- Only Document -->
            @if(isset($data['content']['document']) && $data['content']['document'] != '')
            @if(!empty($data['content']['document']))
            <div class="ac-mb-xs-15"></div>
            <div class="download_files clearfix">
                @php
                $docsAray = explode(',', $data['content']['document']);
                $docObj = App\Document::getDocDataByIds($docsAray);
                @endphp
                @if(count($docObj) > 0)
                <ul>
                    @foreach($docObj as $key => $val)
                    @php
                    if($val->varDocumentExtension == 'pdf' || $val->varDocumentExtension == 'PDF'){
                    $blank = 'target="_blank"';
                    }else{
                    $blank = '';
                    }
                    if($val->varDocumentExtension == 'pdf' || $val->varDocumentExtension == 'PDF'){
                    $icon = "fi flaticon-pdf-file";
                    }elseif($val->varDocumentExtension == 'doc' || $val->varDocumentExtension == 'docx'){
                    $icon = "fi flaticon-doc-file";
                    }elseif($val->varDocumentExtension == 'xls' || $val->varDocumentExtension == 'xlsx'){
                    $icon = "fi flaticon-xls-file";
                    }else{
                    $icon = "fi flaticon-doc-file";
                    }
                    @endphp
                    <li><a {!! $blank !!} href="{{ $CDN_PATH.'documents/'.$val->txtSrcDocumentName.'.'.$val->varDocumentExtension }}" title="{{ $val->txtDocumentName }}.{{ $val->varDocumentExtension }}"><i class="{{ $icon }}"></i>{{ $val->txtDocumentName }}.{{ $val->varDocumentExtension }}</a></li>
                    @endforeach
                </ul>
                @endif
            </div>
            @endif
            @endif
            @endif


            @if($data['type'] == 'imgcontent')
            <!-- Only Left Text and Right Image -->
            @if(isset($data['content']['alignment']) && $data['content']['alignment'] == 'lft-txt')
            <div class="left_img_cms">
                <picture class="three_one_left">
                    <source type="image/webp" data-srcset="{!! App\Helpers\LoadWebpImage::resize($data['content']['image']) !!}" srcset="{!! App\Helpers\LoadWebpImage::resize($data['content']['image']) !!}">
                    <img class="lazy" data-src="{{ App\Helpers\resize_image::resize($data['content']['image']) }}" src="{{ App\Helpers\resize_image::resize($data['content']['image']) }}" alt="{{ htmlspecialchars_decode($data['content']['title']) }}" title="{{ htmlspecialchars_decode($data['content']['title']) }}">
                </picture>
            </div>
            @if($data['content']['title'] != '')
            <h5>{{ $data['content']['title'] }}</h5>
            @endif
            {!! $data['content']['content'] !!}
            <!-- Only Right Text and Left Image -->
            @elseif(isset($data['content']['alignment']) && $data['content']['alignment'] == 'rt-txt')
            <div class="right_img_cms">
                <picture class="three_one_left">
                    <source type="image/webp" data-srcset="{!! App\Helpers\LoadWebpImage::resize($data['content']['image']) !!}" srcset="{!! App\Helpers\LoadWebpImage::resize($data['content']['image']) !!}">
                    <img class="lazy" data-src="{{ App\Helpers\resize_image::resize($data['content']['image']) }}" src="{{ App\Helpers\resize_image::resize($data['content']['image']) }}" alt="{{ htmlspecialchars_decode($data['content']['title']) }}" title="{{ htmlspecialchars_decode($data['content']['title']) }}">
                </picture>
            </div>
            @if($data['content']['title'] != '')
            <h5>{{ $data['content']['title'] }}</h5>
            @endif
            {!! $data['content']['content'] !!}
            <!-- Only Top Image -->
            @elseif(isset($data['content']['alignment']) && $data['content']['alignment'] == 'top-txt')
            <picture class="three_one_left">
                <source type="image/webp" data-srcset="{!! App\Helpers\LoadWebpImage::resize($data['content']['image']) !!}" srcset="{!! App\Helpers\LoadWebpImage::resize($data['content']['image']) !!}">
                <img class="lazy" data-src="{{ App\Helpers\resize_image::resize($data['content']['image']) }}" src="{{ App\Helpers\resize_image::resize($data['content']['image']) }}" alt="{{ htmlspecialchars_decode($data['content']['title']) }}" title="{{ htmlspecialchars_decode($data['content']['title']) }}">
            </picture>
            @if($data['content']['title'] != '')
            <h5>{{ $data['content']['title'] }}</h5>
            @endif
            {!! $data['content']['content'] !!}
            <!-- Only Bottom Image -->
            @elseif(isset($data['content']['alignment']) && $data['content']['alignment'] == 'bot-txt')
            @if($data['content']['title'] != '')
            <h5>{{ $data['content']['title'] }}</h5>
            @endif
            {!! $data['content']['content'] !!}
            <div class="ac-mb-xs-15"></div>
            <picture class="three_one_left">
                <source type="image/webp" data-srcset="{!! App\Helpers\LoadWebpImage::resize($data['content']['image']) !!}" srcset="{!! App\Helpers\LoadWebpImage::resize($data['content']['image']) !!}">
                <img class="lazy" data-src="{{ App\Helpers\resize_image::resize($data['content']['image']) }}" src="{{ App\Helpers\resize_image::resize($data['content']['image']) }}" alt="{{ htmlspecialchars_decode($data['content']['title']) }}" title="{{ htmlspecialchars_decode($data['content']['title']) }}">
            </picture>
            @elseif(isset($data['content']['alignment']) && $data['content']['alignment'] == 'center-txt')

            <picture class="three_one_left two_clm_center">
                <source type="image/webp" data-srcset="{!! App\Helpers\LoadWebpImage::resize($data['content']['image']) !!}" srcset="{!! App\Helpers\LoadWebpImage::resize($data['content']['image']) !!}">
                <img class="lazy" data-src="{{ App\Helpers\resize_image::resize($data['content']['image']) }}" src="{{ App\Helpers\resize_image::resize($data['content']['image']) }}" alt="{{ htmlspecialchars_decode($data['content']['title']) }}" title="{{ htmlspecialchars_decode($data['content']['title']) }}">
            </picture>
            @if($data['content']['title'] != '')
            <h5>{{ $data['content']['title'] }}</h5>
            @endif
            {!! $data['content']['content'] !!}
            <div class="ac-mb-xs-15"></div>

            @endif
            @endif


            @if($data['type'] == 'videocontent')
            <!-- Only Left Text and Right Video -->
            @if(isset($data['content']['title']) && $data['content']['alignment'] == 'lft-txt')
            <div class='col-sm-12'>
                <!-- <div class="row"> -->
                    <div class="cms about-left animated fadeInLeft">
                        <div class="about_image one_three_video">
                            <iframe width="100%" height="315" src="{{ $data['content']['vidId'] }}?rel=0;&autoplay=1" frameborder="0" allow="autoplay;"></iframe>
                        </div>
                    </div>
                    <div class="cms about-left animated fadeInRight">
                        @if($data['content']['title'] != '')
                        <div class="same_title">
                            <h2 class="title_div">{{ $data['content']['title'] }}</h2>
                        </div>
                        @endif
                        <div class="info">
                            {!! $data['content']['content'] !!}
                        </div>
                    </div>
                <!-- </div> -->
            </div>
            <!-- Only Right Text and Left Video -->
            @elseif(isset($data['content']['title']) && $data['content']['alignment'] == 'rt-txt')
            <div class='col-sm-12'>
                <!-- <div class="row"> -->
                <div class="cms about-left animated fadeInRight hidden-xs">
                        <div class="about_image right__video">
                            <iframe width="100%" height="315" src="{{ $data['content']['vidId'] }}?rel=0;&autoplay=1" frameborder="0" allow="autoplay;"></iframe>
                        </div>
                    </div>
                    <div class="cms about-left animated fadeInLeft">
                        @if($data['content']['title'] != '')
                        <div class="same_title">
                            <h2 class="title_div">{{ $data['content']['title'] }}</h2>
                        </div>
                        @endif
                        <div class="info">
                            {!! $data['content']['content'] !!}
                        </div>
                    </div>
                   
                <!-- </div> -->
            </div>
            <!-- Only Top Video -->
            @elseif(isset($data['content']['title']) && $data['content']['alignment'] == 'top-txt')
            <div class="cms about-left animated fadeInUp">
                <div class='about_full'>
                    <div class="about_image one_three_top">
                        <iframe width="100%" height="315" src="{{ $data['content']['vidId'] }}?rel=0;&autoplay=1" frameborder="0" allow="autoplay;"></iframe>
                    </div>
                    @if($data['content']['title'] != '')
                    <div class="same_title">
                        <h2 class="title_div">{{ $data['content']['title'] }}</h2>
                    </div>
                    @endif
                    <div class="info">
                        {!! $data['content']['content'] !!}
                    </div>
                </div>
            </div>
            <!-- Only Bottom Video -->
            @elseif(isset($data['content']['title']) && $data['content']['alignment'] == 'bot-txt')
            <div class="cms about-left animated fadeInUp">
                <div class='about_full about_respons'>
                    @if($data['content']['title'] != '')
                    <div class="same_title">
                        <h2 class="title_div">{{ $data['content']['title'] }}</h2>
                    </div>
                    @endif
                    <div class="info">
                        {!! $data['content']['content'] !!}
                    </div>
                    <div class="about_image one_three_top">
                        <iframe width="100%" height="315" src="{{ $data['content']['vidId'] }}?rel=0;&autoplay=1" frameborder="0" allow="autoplay;"></iframe>
                    </div>
                </div>
            </div>
            @elseif(isset($data['content']['title']) && $data['content']['alignment'] == 'center-txt')

            <div class="col-sm-12 col-xs-12 cms about-left animated fadeInUp">
                <div class='about_full'>
                    <div class="about_image one_three_top one_clm_center">
                        <iframe width="100%" height="315" src="{{ $data['content']['vidId'] }}?rel=0;&autoplay=1" frameborder="0" allow="autoplay;"></iframe>
                    </div>
                    @if($data['content']['title'] != '')
                    <div class="same_title">
                        <h2 class="title_div">{{ $data['content']['title'] }}</h2>
                    </div>
                    @endif
                    <div class="info">
                        {!! $data['content']['content'] !!}
                    </div>
                </div>
            </div>
            @endif
            @endif

            @if($data['type'] == 'buttondata')
            <!-- Only Right Button -->
            @if(isset($data['content']['alignment']) && $data['content']['alignment'] == 'button-rt-txt')
            <div class="animated fadeInUp text-right load">
                <a class="btn ac-border " href="{{ $data['content']['content'] }}" target='{{ $data['content']['target'] }}' title="{{ $data['content']['title'] }}">{{ $data['content']['title'] }}</a>
            </div>
            <!-- Only Left Button -->
            @elseif(isset($data['content']['alignment']) && $data['content']['alignment'] == 'button-lft-txt')
            <div class="animated fadeInUp text-left load">
                <a class="btn ac-border " href="{{ $data['content']['content'] }}" target='{{ $data['content']['target'] }}' title="{{ $data['content']['title'] }}">{{ $data['content']['title'] }}</a>
            </div>
            <!-- Only Center Button -->
            @elseif(isset($data['content']['alignment']) && $data['content']['alignment'] == 'button-center-txt')
            <div class="animated fadeInUp text-center load">
                <a class="btn ac-border " href="{{ $data['content']['content'] }}" target='{{ $data['content']['target'] }}' title="{{ $data['content']['title'] }}">{{ $data['content']['title'] }}</a>
            </div>
            @endif
            @endif


            @if($data['type'] == 'twotextarea')
            <!-- 2 Part Content -->
            @if(isset($data['content']['leftcontent']) && $data['content']['leftcontent'] != '')
            <div class="row">
                <div class="col-sm-6 col-md-6">
                    {!! $data['content']['leftcontent'] !!}
                </div>
                <div class="col-sm-6 col-md-6">
                    {!! $data['content']['rightcontent'] !!}
                </div>
            </div>
            @endif
            @endif

            @if($data['type'] == 'textarea')
            <!-- Only Content -->
            @if(isset($data['content']['content']) && $data['content']['content'] != '')
            {!! $data['content']['content'] !!}
            @endif
            @endif


            @if($data['type'] == 'onlyvideo')
            <!-- Only Video -->
            @if(isset($data['content']['vidId']) && $data['content']['vidId'] != '')
            <h5>{{ $data['content']['title'] }}</h5>
            <br />
            <iframe width="100%" height="315" src="{{ $data['content']['vidId'] }}?rel=0;&autoplay=1" frameborder="0" allow="autoplay;"></iframe>
            @endif
            @endif


            @if($data['type'] == 'mapdata')
            <!-- Only Map -->
            @if(isset($data['content']['latitude']) && $data['content']['latitude'] != '')
            <div class="location_map">
                <iframe src="https://maps.google.com/maps?q={{ $data['content']['latitude'] }}, {{ $data['content']['longitude'] }}&output=embed&zoom=9" width="100%" height="300" frameborder="0" style="border:0"></iframe>
            </div>
            @endif
            @endif


            @if($data['type'] == 'contactinfodata')
            <!-- Only Contact Info -->
            @if(isset($data['content']['section_address']) && $data['content']['section_address'] != '')

            <div class="mailing_box animated fadeInUp load">
                @if(isset($data['content']['section_address']) && $data['content']['section_address'] != '')
                <h4>Address</h4>
                <p>{{ $data['content']['section_address'] }}</p>
                @endif
                <p>
                    @if(isset($data['content']['section_email']) && $data['content']['section_email'] != '')
                    <b>Email:-</b> <a href="mailto:{{ $data['content']['section_email'] }}" title="{{ $data['content']['section_email'] }}">{{ $data['content']['section_email'] }}</a><br>
                    @endif
                    @if(isset($data['content']['section_phone']) && $data['content']['section_phone'] != '')
                    <b>Phone:-</b><a href="tel:{{ $data['content']['section_phone'] }}"> {{ $data['content']['section_phone'] }}</a><br>
                    @endif
                </p>
                @if(isset($data['content']['content']) && $data['content']['content'] != '')
                <p>{!! $data['content']['content'] !!}</p>
                @endif
            </div>

            @endif
            @endif


            @if($data['type'] == 'onlytitle')
            <!-- Only Title -->
            @if(isset($data['content']['content']) && $data['content']['content'] != '')
            <h2>
                {!! $data['content']['content'] !!}
            </h2>
            @endif
            @endif

            @if($data['type'] == 'formdata')
            @include('layouts.builder-sections.formbuilder')
            @endif
        </div>
    </div>
    @endif
    @if($data['Columns'] == '2')
</div>
<hr />
@endif