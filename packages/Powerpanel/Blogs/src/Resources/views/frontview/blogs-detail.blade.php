@extends('layouts.app')
@section('content')
@include('layouts.inner_banner')
<section>
    <div class="inner-page-container cms events_detail blog_detail">
        <div class="container">
            <!-- Main Section S -->
            <div class="row">

                <div class="col-xl-3 col-lg-4 col-xs-12 d-lg-block d-none">
                    <div class="detail-panel">
                        @if(isset($similarServices) && count($similarServices) > 0)
                        <div class="item rec-blog">
                            <h3 class="title-a">Our Other Services</h3>
                            <ul>
                                @foreach($similarServices as $index => $similarServices)
                                @php
                                if(isset(App\Helpers\MyLibrary::getFront_Uri('Services')['uri'])){
                                $moduelFrontPageUrl = App\Helpers\MyLibrary::getFront_Uri('Services')['uri'];
                                $moduleFrontWithCatUrl = ($similarServices->varAlias != false ) ? $moduelFrontPageUrl . '/' . $similarServices->varAlias : $moduelFrontPageUrl;
                                $recordLinkserviceUrl = $moduleFrontWithCatUrl.'/'.$similarServices->alias->varAlias;
                                }else{
                                $recordLinkserviceUrl = '';
                                }
                                @endphp
                                <li class="active">
                                    <a href="{{ $recordLinkserviceUrl }}" title="{{ucwords($similarServices->varTitle)}}">
                                        <div class="thumbnail-container">
                                            <div class="thumbnail">
                                                <img class="lazy" data-src="{{ App\Helpers\resize_image::resize($similarServices->fkIntImgId,70,46)}}" src="{{ App\Helpers\resize_image::resize($similarServices->fkIntImgId,70,46)}}" alt="{!!$similarServices->varTitle!!}" title="{!! ucwords($similarServices->varTitle) !!}">
                                            </div>
                                        </div>
                                        {{$similarServices->varTitle}}
                                    </a>
                                    <div class="clearfix"></div>
                                </li>
                                @endforeach
                            </ul>
                        </div>
                        @endif

                        @if(isset($similarBoats) && count($similarBoats) > 0)
                        <div class="item rec-blog">
                            <h3 class="title-a">Our Other Boats</h3>
                            <ul>
                                @foreach($similarBoats as $index => $similarBoats)
                                @php
                                if(isset(App\Helpers\MyLibrary::getFront_Uri('boat')['uri'])){
                                $moduelFrontPageUrl = App\Helpers\MyLibrary::getFront_Uri('boat')['uri'];
                                $moduleFrontWithCatUrl = ($similarBoats->varAlias != false ) ? $moduelFrontPageUrl . '/' . $similarBoats->varAlias : $moduelFrontPageUrl;
                                $recordLinkserviceUrl = $moduleFrontWithCatUrl.'/'.$similarBoats->alias->varAlias;
                                }else{
                                $recordLinkserviceUrl = '';
                                }
                                @endphp
                                <li class="active">
                                    <a href="{{ $recordLinkserviceUrl }}" title="{{ucwords($similarBoats->varTitle)}}">
                                        <div class="thumbnail-container">
                                            <div class="thumbnail">
                                                <img class="lazy" data-src="{{ App\Helpers\resize_image::resize($similarBoats->fkIntImgId,70,46)}}" src="{{ App\Helpers\resize_image::resize($similarBoats->fkIntImgId,70,46)}}" alt="{!!$similarBoats->varTitle!!}" title="{!! ucwords($similarBoats->varTitle) !!}">
                                            </div>
                                        </div>
                                        {{$similarBoats->varTitle}}
                                    </a>
                                    <div class="clearfix"></div>
                                </li>
                                @endforeach
                            </ul>
                        </div>
                        @endif

                        @if(isset($workSlidebar) && count($workSlidebar) > 0)
                        <div class="item rec-work">
                            <h3 class="title-a">Recent Works</h3>
                            <ul>
                                @foreach($workSlidebar as $index => $similarWorks)
                                <li>
                                    <a href="{{ url('work/'.$similarWorks->alias->varAlias) }}" title="{{ucwords($similarWorks->varTitle)}}">
                                        <div class="thumbnail-container">
                                            <div class="thumbnail">
                                                <img class="lazy" data-src="{{ App\Helpers\resize_image::resize($similarWorks->fkIntImgId,70,46)}}" src="{{ App\Helpers\resize_image::resize($similarWorks->fkIntImgId,70,46)}}" alt="{!!$similarWorks->varTitle!!}" title="{!! ucwords($similarWorks->varTitle) !!}">
                                            </div>
                                        </div>
                                        {{$similarWorks->varTitle}}
                                    </a>
                                    <div class="clearfix"></div>
                                </li>
                                @endforeach
                            </ul>
                        </div>
                        @endif
                    </div>
                </div>
                <div class="col-xl-9 col-lg-8 col-xs-12" data-aos="fade-up">
                    <div class="right_content">
                        <div class="back_div">
                            @php
                            $start_date_time = '';
                            if(isset($_REQUEST['start_date_time']) && strip_tags($_REQUEST['start_date_time']) != ''){
                            $start_date_time = '&start_date_time='.strip_tags($_REQUEST['start_date_time']);
                            }
                            $end_date_time = '';
                            if(isset($_REQUEST['end_date_time']) && strip_tags($_REQUEST['end_date_time']) != ''){
                            $end_date_time = '&end_date_time='.strip_tags($_REQUEST['end_date_time']);
                            }
                            $page = '';
                            if(isset($_REQUEST['page']) && strip_tags($_REQUEST['page']) != ''){
                            $page = '&page='.strip_tags($_REQUEST['page']);
                            }
                            $catid = '';
                            if(isset($_REQUEST['catid']) && intval($_REQUEST['catid']) != ''){
                            $catid = '&catid='.intval($_REQUEST['catid']);
                            }
                            if(isset($_REQUEST['N']) && intval($_REQUEST['N']) == 'C'){
                            $Nurl = '?N='.strip_tags($_REQUEST['N']);
                            }else{
                            $Nurl = '?N=N';
                            }

                            if($Nurl == "?N=N"){
                            $backpageUrl = $moduleFrontWithCatUrl;
                            }else{
                            $backpageUrl = App\Helpers\MyLibrary::getFront_Uri('blogs')['uri'];
                            }

                            @endphp
                            <!-- <a href="{{ $backpageUrl }}" title="Back"><i class="fi flaticon-right"></i> Back</a> -->
                        </div>
                    </div>
                    <div class="col-md-12 col-sm-12 col-xs-12 pl-xl-5 pl-md-4 animated fadeInUp">
                        @php
                        $blogstartDate = date('d M, Y',strtotime($blogs->dtDateTime));
                        $blogDisplayDate = $blogstartDate;
                        if(!empty($blogs->dtEndDateTime) && $blogs->dtEndDateTime != null){
                        $blogExpityDate = date('d M, Y',strtotime($blogs->dtEndDateTime));
                        $blogDisplayDate = $blogstartDate." to ".$blogExpityDate;
                        }
                        @endphp
                        <h3 class="cm-title blog_img_title mb-3 mb-md-4">{{ $blogs->varTitle }}
                            <span class="date">{{ $blogDisplayDate }}</span>
                        </h3>
                        @if(isset($blogs->fkIntImgId) && $blogs->fkIntImgId != '')
                        @php
                        $itemImg = App\Helpers\resize_image::resize($blogs->fkIntImgId, 966, 645);
                        $webpImg = App\Helpers\LoadWebpImage::resize($blogs->fkIntImgId, 966, 645);
                        @endphp
                        <div class="blog__image">
                            <div class="thumbnail-container">
                                <div class="thumbnail">
                                    <picture>
                                        <source type="image/webp" class="lazy" data-srcset="{!! $webpImg !!}" srcset="{!! $webpImg !!}">
                                        <img class="lazy" data-src="{{ $itemImg }}" src="{!! url('assets/images/default.png') !!}" alt="{{ ucwords($blogs->varTitle) }}" title="{{ ucwords($blogs->varTitle) }}">
                                    </picture>
                                </div>
                            </div>
                        </div>
                        @endif

                        @if(isset($blogs->varShortDescription) && !empty($blogs->varShortDescription))
                        <!-- <h6>Short Description</h6> -->
                        @endif

                        @if(isset($txtDescription['response']) && !empty($txtDescription['response']) && $txtDescription['response'] != [])
                        {!! $txtDescription['response'] !!}
                        @else
                        <p>{!! $blogs->varShortDescription !!}</p>
                        @endif

                        @if(!empty($blogs->fkIntDocId))
                        @php
                        $docsAray = explode(',', $blogs->fkIntDocId);
                        $docObj = App\Document::getDocDataByIds($docsAray);
                        @endphp
                        @if(count($docObj) > 0)
                        <div class="download_files clearfix">
                            <h6>Download(s)</h6>
                            <ul>
                                @foreach($docObj as $key => $val)
                                @php
                                if($val->varDocumentExtension == 'pdf' || $val->varDocumentExtension == 'PDF'){
                                $blank = 'target="_blank"';
                                }else{
                                $blank = '';
                                }
                                if($val->varDocumentExtension == 'pdf' || $val->varDocumentExtension == 'PDF'){
                                $icon = "flaticon-pdf-file";
                                }elseif($val->varDocumentExtension == 'doc' || $val->varDocumentExtension == 'docx'){
                                $icon = "flaticon-doc-file";
                                }elseif($val->varDocumentExtension == 'xls' || $val->varDocumentExtension == 'xlsx'){
                                $icon = "flaticon-xls-file";
                                }else{
                                $icon = "flaticon-doc-file";
                                }
                                @endphp
                                <li><a {!! $blank !!} href="{{ $CDN_PATH.'documents/'.$val->txtSrcDocumentName.'.'.$val->varDocumentExtension }}" data-viewid="{{ $val->id }}" data-viewtype="download" class="docHitClick" title="{{ $val->txtDocumentName }}"><i class="fi {{ $icon }}"></i>{{ $val->txtDocumentName }}</a></li>
                                @endforeach
                            </ul>
                        </div>
                        @endif
                        @endif
                    </div>
                </div>
            </div>
        </div>
        <!-- Main Section E -->
    </div>
</section>
<section class="inner-page-container blogs_page detail_related_blog">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <h3 class="cm-title m-title">Related Blogs</h3>
            </div>
            <div class="col-md-12 col-xs-12">
                <div class="row">
                    @foreach($similarBlogs as $index => $blog)
                    @php
                    if(isset(App\Helpers\MyLibrary::getFront_Uri('Blogs')['uri'])){
                    $moduelFrontPageUrl = App\Helpers\MyLibrary::getFront_Uri('Blogs')['uri'];
                    $moduleFrontWithCatUrl = ($blog->varAlias != false ) ? $moduelFrontPageUrl . '/' . $blog->varAlias : $moduelFrontPageUrl;
                    $recordLinkUrl = $moduleFrontWithCatUrl.'/'.$blog->alias->varAlias;
                    }else{
                    $recordLinkUrl = '';
                    }
                    @endphp
                    <div class="col-12 col-sm-6 col-lg-4 b-line mb-5 px-4" data-aos="fade-up">
                        <div class="service_box">
                            <div class="img">
                                <div class="image img_hvr">
                                    <a href="{{ $recordLinkUrl }}" title="{{ ucwords($blog->varTitle) }}" class="img-effect-right">
                                        @if(isset($blog->fkIntImgId) && !empty($blog->fkIntImgId))
                                        <div class="thumbnail-container" data-aos="fade-down">
                                            <div class="thumbnail img-hvr_hvr">
                                                <picture>
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
                               
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            <!-- Static Code Ends-->

        </div>
    </div>
</section>
@if(!Request::ajax())
@section('footer_scripts')
@endsection
@endsection
@endif