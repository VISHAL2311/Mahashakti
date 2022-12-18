@section('css')
<link href="{{ $CDN_PATH.'resources/global/plugins/bootstrap-fileinput/bootstrap-fileinput.css' }}" rel="stylesheet" type="text/css" />
@endsection
@extends('powerpanel.layouts.app')
@section('title')
{{Config::get('Constant.SITE_NAME')}} - PowerPanel
@endsection
@section('content')
@php $settings = json_decode(Config::get("Constant.MODULE.SETTINGS")); @endphp
@include('powerpanel.partials.breadcrumbs')
<div class="col-md-12 settings">
    @if(Session::has('message'))
    <div class="row">
        <div class="alert alert-success">
            <button class="close" data-close="alert"></button>
            {{ Session::get('message') }}
        </div>
    </div>
    @endif
    <div class="row">
        <div class="portlet light bordered">
            <div class="portlet-body">
                <div class="tabbable tabbable-tabdrop">
                    <div class="tab-content">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="portlet-body form_pattern">
                                    {!! Form::open(['method' => 'post','enctype' => 'multipart/form-data','id'=>'frmBanner']) !!}
                                    <div class="form-body">
                                        @if(isset($banners))
                                        @if (File::exists(base_path() . '/resources/views/powerpanel/partials/lockedpage.blade.php') != null)
                                        @include('powerpanel.partials.lockedpage',['pagedata'=>$banners])
                                        @endif
                                        @endif
                                        @php if(isset($banners_highLight->varTitle) && ($banners_highLight->varTitle != $banners->varTitle)){
                                        $Class_title = " highlitetext";
                                        }else{
                                        $Class_title = "";
                                        } @endphp
                                        <div class="form-group {{ $errors->has('title') ? ' has-error' : '' }} form-md-line-input">
                                            <label class="form_title {!! $Class_title !!}" for="title">{!! trans('banner::template.common.title') !!} <span aria-required="true" class="required"> * </span></label>
                                            {!! Form::text('title', isset($banners->varTitle)?$banners->varTitle:old('title'), array('maxlength'=>'150','class' => 'form-control input-sm maxlength-handler titlespellingcheck', 'data-url' => 'powerpanel/banners','id' => 'title','placeholder' => trans('banner::template.common.title'),'autocomplete'=>'off')) !!}
                                            <span style="color:#e73d4a">
                                                {{ $errors->first('title') }}
                                            </span>
                                        </div>

                                        <div class="form-group @if($errors->first('tag_line')) has-error @endif form-md-line-input" id="txtTagLine">
                                            <label class="form_title" for="site_name">{{ trans('banner::template.common.tagline') }}</label>
                                            {!! Form::text('tag_line', isset($banners->varTagLine)?$banners->varTagLine:old('tag_line'), array('maxlength'=>100,'placeholder' => trans("banner::template.common.tagline"),'class' => 'form-control maxlength-handler','autocomplete'=>'off')) !!}
                                            <span class="help-block">
                                                {{ $errors->first('tag_line') }}
                                            </span>
							            </div>

                                        {!! Form::hidden('fkMainRecord', isset($banners->fkMainRecord)?$banners->fkMainRecord:old('fkMainRecord')) !!}
                                        @if ((isset($banners->varBannerType) && $banners->varBannerType == 'home_banner') || old('banner_type') == 'home_banner' || (!isset($banners->varBannerType) && old('banner_type') == null))
                                        @php $checked_yes = 'checked' @endphp
                                        @else
                                        @php $checked_yes = '' @endphp
                                        @endif
                                        @if ((isset($banners->varBannerType) && $banners->varBannerType == 'inner_banner') || old('banner_type') == 'inner_banner')
                                        @php $ichecked_innerbaner_yes = 'checked' @endphp
                                        @else
                                        @php $ichecked_innerbaner_yes = '' @endphp
                                        @endif 
                                        @php if(isset($banners_highLight->varBannerType) && ($banners_highLight->varBannerType != $banners->varBannerType)){
                                        $Class_banner_type = " highlitetext";
                                        }else{
                                        $Class_banner_type = "";
                                        } @endphp
                                        {{-- @php $checked_yes = 'checked' @endphp
										@php $ichecked_innerbaner_yes = '' @endphp --}}
                                        <div class="form-group {{ $errors->has('banner_type') ? ' has-error' : '' }}" style="display:none;">
                                            <label class="form_title {!! $Class_banner_type !!}" for="banner_type">{!! trans('banner::template.bannerModule.bannerType') !!} <span aria-required="true" class="required"> * </span></label>
                                            <div class="md-radio-inline">
                                                <div class="md-radio">
                                                    <input type="radio" checked  value="home_banner" id="home_banner" name="banner_type" class="md-radiobtn banner">
                                                    <label for="home_banner">
                                                        <span class="inc"></span>
                                                        <span class="check"></span>
                                                        <span class="box"></span> {!! trans('banner::template.bannerModule.homeBanner') !!}
                                                    </label>
                                                </div>
                                               
                                            </div>
                                            <span class="help-block">
                                                <strong>{{ $errors->first('banner_type') }}</strong>
                                            </span>
                                        </div>
                                        @if ((isset($banners->varBannerVersion) && $banners->varBannerVersion == 'img_banner') || old('bannerversion')=='img_banner' || (!isset($banners->varBannerVersion) && old('bannerversion') == null))
                                        @php $checked_yes = 'checked' @endphp
                                        @else
                                        @php $checked_yes = '' @endphp
                                        @endif
                                        @if ((isset($banners->varBannerVersion) && $banners->varBannerVersion == 'vid_banner') || old('bannerversion')=='vid_banner')
                                        @php $ichecked_vid_yes = 'checked' @endphp
                                        @else
                                        @php $ichecked_vid_yes = '' @endphp
                                        @endif
                                        @php if(isset($banners_highLight->varBannerVersion) && ($banners_highLight->varBannerVersion != $banners->varBannerVersion)){
                                        $Class_banner_virsion = " highlitetext";
                                        }else{
                                        $Class_banner_virsion = "";
                                        } @endphp
                                        <div style="display: none;" class="form-group bannerversion {{ $errors->has('bannerversion') ? ' has-error' : '' }}">
                                            <label class="form_title {!! $Class_banner_virsion !!}" for="bannerversion">{!! trans('banner::template.bannerModule.version') !!} <span aria-required="true" class="required"> * </span></label>
                                            <div class="md-radio-inline">
                                                <div class="md-radio">
                                                    <input type="radio" {{ $checked_yes }}  value="img_banner" id="img_banner" name="bannerversion" class="md-radiobtn versionradio">
                                                    <label for="img_banner">
                                                        <span class="inc"></span>
                                                        <span class="check"></span>
                                                        <span class="box"></span> {!! trans('banner::template.bannerModule.imageBanner') !!}
                                                    </label>
                                                </div>
                                                <div class="md-radio">
                                                    <input type="radio" {{ $ichecked_vid_yes }} value="vid_banner" id="vid_banner" name="bannerversion" class="md-radiobtn versionradio">
                                                    <label for="vid_banner">
                                                        <span class="inc"></span>
                                                        <span class="check"></span>
                                                        <span class="box"></span> {!! trans('banner::template.bannerModule.videoBanner') !!}
                                                    </label>
                                                </div>
                                            </div>
                                            <span class="help-block">
                                                <strong>{{ $errors->first('bannerversion') }}</strong>
                                            </span>
                                        </div>
                                        @php if(isset($banners_highLight->fkModuleId) && ($banners_highLight->fkModuleId != $banners->fkModuleId)){
                                        $Class_module = " highlitetext";
                                        }else{
                                        $Class_module = "";
                                        } @endphp
                                        <div class="form-group" id="pages" style="display: none;">
                                            <label class="form_title {!! $Class_module !!}" for="pages">{!! trans('banner::template.common.selectmodule') !!} <span aria-required="true" class="required"> * </span></label>
                                            <select class="form-control bs-select select2" name="modules" id="modules">
                                                <option value=" ">-{!! trans('banner::template.common.selectmodule') !!}-</option>
                                                @if(count($modules) > 0)
                                                @foreach ($modules as $pagedata)
                                                @php
                                                $avoidModules = array('faq','contact-us','testimonial');
                                                @endphp
                                                @if (ucfirst($pagedata->varTitle)!='Home' && !in_array($pagedata->varModuleName,$avoidModules))
                                                <option data-model="{{ $pagedata->varModelName }}" data-module="{{ $pagedata->varModuleName }}" value="{{ $pagedata->id }}" {{ (isset($banners->fkModuleId) && $pagedata->id == $banners->fkModuleId) || $pagedata->id == old('modules')? 'selected' : '' }} >{{ $pagedata->varTitle }}</option>
                                                @endif
                                                @endforeach
                                                @endif
                                            </select>
                                            <span style="color:#e73d4a">
                                                {{ $errors->first('modules') }}
                                            </span>
                                        </div>
                                        @php if(isset($banners_highLight->fkIntPageId) && ($banners_highLight->fkIntPageId != $banners->fkIntPageId)){
                                        $Class_page = " highlitetext";
                                        }else{
                                        $Class_page = "";
                                        } @endphp
                                        <div class="form-group" id="records" style="display: none;">
                                            <label class="form_title {!! $Class_page !!}" for="pages">{!! trans('banner::template.bannerModule.selectPage') !!}<span aria-required="true" class="required"> * </span></label>
                                            <select class="form-control bs-select select2" name="foritem" id="foritem" style="width:100%">
                                                <option value=" ">--{!! trans('banner::template.bannerModule.selectPage') !!}--</option>
                                            </select>
                                            <span style="color:#e73d4a">
                                                {{ $errors->first('foritem') }}
                                            </span>
                                        </div>
                                        @php if(isset($banners_highLight->fkIntImgId) && ($banners_highLight->fkIntImgId != $banners->fkIntImgId)){
                                        $Class_image = " highlitetext";
                                        }else{
                                        $Class_image = "";
                                        } @endphp
                                        <div class="form-group imguploader {{ $errors->has('img_id') ? ' has-error' : '' }}"  >
                                            <div class="image_thumb">
                                                <label class="form_title {!! $Class_image !!}" for="front_logo">{!! trans('banner::template.bannerModule.selectBanner') !!} <span aria-required="true" class="required"> * </span></label>
                                                <div class="fileinput fileinput-new" data-provides="fileinput">
                                                    <div class="fileinput-preview thumbnail banner_image_img" data-trigger="fileinput" style="width:100%; height:120px;position: relative;">
                                                        @if(old('image_url'))
                                                        <img src="{{ old('image_url') }}" />
                                                        @elseif(isset($banners->fkIntImgId) && $banners->fkIntImgId > 0)
                                                        <img  src="{!! App\Helpers\resize_image::resize($banners->fkIntImgId) !!}" />
                                                        @else
                                                        <img class="img_opacity" src="{{ $CDN_PATH.'resources/images/upload_file.gif' }}" />
                                                        @endif
                                                    </div>
                                                    <div class="input-group">
                                                        <a class="media_manager" onclick="MediaManager.open('banner_image');"><span class="fileinput-new"></span></a>
                                                        <input class="form-control" type="hidden" id="banner_image" name="img_id" value="{{ isset($banners->fkIntImgId)?$banners->fkIntImgId:old('img_id') }}" />
                                                        <input class="form-control" type="hidden" id="image_url" name="image_url" value="{{ old('image_url') }}" />
                                                        @php
                                                         if (method_exists($MyLibrary, 'GetFolderID')) {
                                                        if(isset($banners->fkIntImgId)){
                                                        $folderid = App\Helpers\MyLibrary::GetFolderID($banners->fkIntImgId);
                                                        @endphp
                                                        @if(isset($folderid->fk_folder) && $folderid->fk_folder != '0')
                                                        <input class="form-control" type="hidden" id="folder_id" name="folder_id" value="{{ $folderid->fk_folder }}" />
                                                        @endif
                                                        @php
                                                        }
                                                        }
                                                        @endphp
                                                    </div>
                                                    <!--<input class="form-control" type="hidden" id="banner_image" name="img_id" value="{{ isset($banners->fkIntImgId)?$banners->fkIntImgId:old('img_id') }}" />-->
                                                </div>
                                                <div class="clearfix"></div>
                                                @php $height = isset($settings->height)?$settings->height:1080; $width = isset($settings->width)?$settings->width:1920; @endphp 
                                                <span style="display: none;" id="HomeBannerSize">{{ trans('banner::template.common.imageSize',['height'=>'1080', 'width'=>'1920']) }}</span>
                                                <span id="InnerBannerSize">{{ trans('banner::template.common.imageSize',['height'=>'312', 'width'=>'1920']) }}</span>
                                                <span style="color:#e73d4a;margin:0;display:inline;">
                                                    {{ $errors->first('img_id') }}
                                                </span>
                                            </div>
                                        </div>
                                        
                                        <div id="DisplayVideo" style="display: none">
                                            @if(isset($banners_highLight->chrDisplayVideo) && ($banners_highLight->chrDisplayVideo != $banners->chrDisplayVideo))
                                            @php $Class_Applicable = " highlitetext"; @endphp
                                            @else
                                            @php $Class_Applicable = ""; @endphp
                                            @endif
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <div class="form-group form-md-line-input">
                                                        <label class="form_title {{ $Class_Applicable }}">Display Video:</label>
                                                        @if (isset($banners->chrDisplayVideo) && $banners->chrDisplayVideo == 'Y')
                                                        @php $checked_section = true; @endphp
                                                        @php $display_Section = ''; @endphp
                                                        @else
                                                        @php $checked_section = null; 
                                                        @endphp
                                                        @php $display_Section = 'none'; @endphp
                                                        @endif
                                                        {{ Form::checkbox('chrDisplayVideo',null,$checked_section, array('id'=>'chrDisplayVideo')) }}
                                                    </div>
                                                </div>
                                            </div>
                                            @php if(isset($banners_highLight->varVideoLink) && ($banners_highLight->varVideoLink != $banners->varVideoLink)){
                                            $Class_VideoLink = " highlitetext";
                                            }else{
                                            $Class_VideoLink = "";
                                            } @endphp
                                            <div class="form-group {{ $errors->has('videolink') ? ' has-error' : '' }} form-md-line-input" id="VideoLinkTEXT" style="display: none;">
                                                <label class="form_title {!! $Class_VideoLink !!}" for="videolink">Video Link<span aria-required="true" class="required"> * </span></label>
                                                {!! Form::text('videolink', isset($banners->varVideoLink)?$banners->varVideoLink:old('videolink'), array('maxlength'=>'500','class' => 'form-control input-sm maxlength-handler', 'id' => 'videolink','placeholder' => 'Video Link','autocomplete'=>'off')) !!}
                                                <span style="color:#e73d4a">
                                                    {{ $errors->first('videolink') }}
                                                </span>
                                            </div>
                                        </div>

                                        
                                        <!-- <div style="display: none;" class="row" id="txtshortdesc">
                                            <div class="col-md-12">
                                                <div class="form-group @if($errors->first('short_description')) has-error @endif form-md-line-input">
                                                    @php if(isset($banners_highLight->varShortDescription) && ($banners_highLight->varShortDescription != $banners->varShortDescription)){
                                                    $Class_ShortDescription = " highlitetext";
                                                    }else{
                                                    $Class_ShortDescription = "";
                                                    } @endphp
                                                    <div class="form-group" id="pagesDescri" style="display: none;">
                                                        <label class="form_title {!! $Class_ShortDescription !!}">Short Description</label>
                                                        {!! Form::textarea('short_description', isset($banners->varShortDescription)?$banners->varShortDescription:old('short_description'), array('maxlength' => isset($settings->short_desc_length)?$settings->short_desc_length:400,'class' => 'form-control seoField maxlength-handler shortdescspellingcheck','id'=>'varShortDescription','rows'=>'3','placeholder'=>'Short Description')) !!}
                                                        <span class="help-block">{{ $errors->first('short_description') }}</span> 
                                                    </div>
                                                </div>
                                            </div>
                                        </div> -->


                                        @if(isset($banners_highLight->chrDisplayLink) && ($banners_highLight->chrDisplayLink != $banners->chrDisplayLink))
                                        @php $Class_NewTab = " highlitetext"; @endphp
                                        @else
                                        @php $Class_NewTab = ""; @endphp
                                        @endif
                                        <div class="row" id="DisplayLink" style="display: none;">
                                            <div class="col-md-12" style="display: none;">
                                                <div class="form-group form-md-line-input">
                                                    <label class="form_title {{ $Class_NewTab }}">Open in New Tab:</label>
                                                    @if (isset($banners->chrDisplayLink) && $banners->chrDisplayLink == 'Y')
                                                    @php $checked_section_link = true; @endphp

                                                    @else
                                                    @php $checked_section_link = null; 
                                                    @endphp                                                    
                                                    @endif
                                                    {{ Form::checkbox('chrDisplayLink',null,$checked_section_link, array('id'=>'chrDisplayLink')) }}
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group {{ $errors->has('title') ? ' has-error' : '' }} form-md-line-input" id="button_title">
                                            <label class="form_title {!! $Class_title !!}" for="button_name">Button Name</label>
                                            {!! Form::text('button_name', isset($banners->varButtonName)?$banners->varButtonName:old('title'), array('maxlength'=>'150','class' => 'form-control input-sm maxlength-handler', 'id' => 'button_name','placeholder' => 'Button Name','autocomplete'=>'off')) !!}
                                            <span style="color:#e73d4a">
                                                {{ $errors->first('title') }}
                                            </span>
                                        </div>
                                        @php if(isset($banners_highLight->varLink) && ($banners_highLight->varLink != $banners->varLink)){
                                        $Class_Link = " highlitetext";
                                        }else{
                                        $Class_link = "";
                                        } @endphp
                                        <div class="form-group {{ $errors->has('link') ? ' has-error' : '' }} form-md-line-input" id="linkTEXT" style="display: none;">
                                            <label class="form_title {!! $Class_link !!}" for="link">Button Link </label>
                                            {!! Form::text('link', isset($banners->varLink)?$banners->varLink:old('link'), array('maxlength'=>'500','class' => 'form-control input-sm maxlength-handler', 'id' => 'link','placeholder' => 'Button Link','autocomplete'=>'off')) !!}
                                            <span style="color:#e73d4a">
                                                {{ $errors->first('link') }}
                                            </span>
                                        </div>
                                        @php if(isset($banners_highLight->fkIntVideoId) && ($banners_highLight->fkIntVideoId != $banners->fkIntVideoId)){
                                        $Class_video = " highlitetext";
                                        }else{
                                        $Class_video = "";
                                        } @endphp
                                        @if(Config::get('Constant.CHRContentScheduling') == 'Y')
                                        <h3 style="display:none;" class="form-section">{{ trans('banner::template.common.ContentScheduling') }}</h3>
                                        @php $defaultDt = (null !== old('start_date_time'))?old('start_date_time'):date('Y-m-d H:i'); @endphp
                                        <div class="row" style="display:none;">
                                            <div class="col-md-6">
                                                <div class="form-group form-md-line-input">
                                                    @php if(isset($banners_highLight->dtDateTime) && ($banners_highLight->dtDateTime != $banners->dtDateTime)){
                                                    $Class_date = " highlitetext";
                                                    }else{
                                                    $Class_date = "";
                                                    } @endphp
                                                    <label class="control-label form_title {!! $Class_date !!}">{{ trans('banner::template.common.startDateAndTime') }}<span aria-required="true" class="required"> * </span></label>
                                                    <div class="input-group date form_meridian_datetime @if($errors->first('start_date_time')) has-error @endif" data-date="{{ Carbon\Carbon::today()->format('Y-m-d') }}T15:25:00Z">
                                                        <span class="input-group-btn date_default">
                                                            <button class="btn date-set fromButton" type="button">
                                                                <i class="fa fa-calendar"></i>
                                                            </button>
                                                        </span>
                                                        {!! Form::text('start_date_time', date('Y-m-d H:i',strtotime(isset($banners->dtDateTime)?$banners->dtDateTime:$defaultDt)), array('class' => 'form-control','maxlength'=>160,'size'=>'16','id'=>'start_date_time','autocomplete'=>'off','onkeypress'=>"javascript: return KeycheckOnlyDate(event);",'onpaste'=>'return false')) !!}
                                                    </div>
                                                    <span class="help-block">
                                                        {{ $errors->first('start_date_time') }}
                                                    </span>
                                                </div>
                                            </div>
                                            @php $defaultDt = (null !== old('end_date_time'))?old('end_date_time'):null; @endphp
                                            @if ((isset($banners->dtEndDateTime)==null))
                                            @php
                                            $expChecked_yes = 1;
                                            $expclass='';
                                            @endphp
                                            @else
                                            @php
                                            $expChecked_yes = 0;
                                            $expclass='no_expiry';
                                            @endphp
                                            @endif
                                            <div class="col-md-6">
                                                <div class="form-group form-md-line-input">
                                                    <div class="input-group date  form_meridian_datetime expirydate @if($errors->first('end_date_time')) has-error @endif" data-date="{{ Carbon\Carbon::today()->format('Y-m-d') }}T15:25:00Z" @if ($expChecked_yes==1) style="display:none;" @endif>
                                                         @php if(isset($banners_highLight->varTitle) && ($banners_highLight->dtEndDateTime != $banners->dtEndDateTime)){
                                                         $Class_end_date = " highlitetext";
                                                         }else{
                                                         $Class_end_date = "";
                                                         } @endphp
                                                         <label class="control-label form_title {!! $Class_end_date !!}" >{{ trans('banner::template.common.endDateAndTime') }} <span aria-required="true" class="required"> * </span></label>
                                                        <div class="pos_cal">
                                                            <span class="input-group-btn date_default">
                                                                <button class="btn date-set toButton" type="button">
                                                                    <i class="fa fa-calendar"></i>
                                                                </button>
                                                            </span>
                                                            {!! Form::text('end_date_time', isset($banners->dtEndDateTime)?date('Y-m-d H:i',strtotime($banners->dtEndDateTime)):$defaultDt, array('class' => 'form-control','maxlength'=>160,'size'=>'16','id'=>'end_date_time','data-exp'=> $expChecked_yes,'data-newvalue','autocomplete'=>'off','onkeypress'=>"javascript: return KeycheckOnlyDate(event);",'onpaste'=>'return false')) !!}
                                                        </div>
                                                    </div>
                                                    <span class="help-block">
                                                        {{ $errors->first('end_date_time') }}
                                                    </span>
                                                    <label class="expdatelabel {{ $expclass }}">
                                                        <a id="noexpiry" name="noexpiry" href="javascript:void(0);">
                                                            <b class="expiry_lbl {!! $Class_end_date !!}"></b>
                                                        </a>
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                        @endif
                                        <h3 class="form-section">{!! trans('banner::template.common.displayinformation') !!}</h3>
                                        <div class="row">
                                            <div class="col-md-6" id="DisplayOrder" style="display: none;">
                                                @php
                                                $display_order_attributes = array('class' => 'form-control','autocomplete'=>'off','maxlength'=>'5');
                                                @endphp
                                                @php if(isset($banners_highLight->intDisplayOrder) && ($banners_highLight->intDisplayOrder != $banners->intDisplayOrder)){
                                                $Class_displayorder = " highlitetext";
                                                }else{
                                                $Class_displayorder = "";
                                                } @endphp
                                                <div class="form-group @if($errors->first('display_order')) has-error @endif form-md-line-input">
                                                    <label class="form_title {!! $Class_displayorder !!}" for="display_order">{!! trans('banner::template.common.displayorder') !!} <span aria-required="true" class="required"> * </span></label>
                                                    {!! Form::text('display_order',isset($banners->intDisplayOrder)?$banners->intDisplayOrder:'1', $display_order_attributes) !!}
                                                    <span class="help-block">
                                                        <strong>{{ $errors->first('display_order') }}</strong>
                                                    </span>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                @if(isset($banners_highLight->chrPublish) && ($banners_highLight->chrPublish != $banners->chrPublish))
                                                @php $Class_chrPublish = " highlitetext"; @endphp
                                                @else
                                                @php $Class_chrPublish = ""; @endphp
                                                @endif
                                                @if((isset($banners) && $banners->chrDraft == 'D'))
                                                @include('powerpanel.partials.displayInfo',['Class_chrPublish'=>$Class_chrPublish,'display' => (isset($banners->chrDraft)?$banners->chrDraft:'D')])
                                                @else
                                                @include('powerpanel.partials.displayInfo',['Class_chrPublish'=>$Class_chrPublish,'display' => (isset($banners->chrPublish)?$banners->chrPublish:'Y')])
                                                @endif
                                            </div>

                                        </div>
                                    </div>
                                    <div class="form-actions">
                                        <div class="row">
                                            <div class="col-md-12">
                                                @if(isset($banners->fkMainRecord) && $banners->fkMainRecord != 0)
                                                <button type="submit" name="saveandexit" class="btn btn-green-drake" value="saveandexit" title="{!! trans('banner::template.common.approve') !!}">{!! trans('banner::template.common.approve') !!}</button>
                                                @else
                                                @if($userIsAdmin)
                                                <button type="submit" name="saveandedit" class="btn btn-green-drake" value="saveandedit" title="{!! trans('banner::template.common.saveandedit') !!}">{!! trans('banner::template.common.saveandedit') !!}</button>
                                                <button type="submit" name="saveandexit" class="btn btn-green-drake" value="saveandexit" title="{!! trans('banner::template.common.saveandexit') !!}">{!! trans('banner::template.common.saveandexit') !!}</button>
                                                @else
                                                @if((isset($chrNeedAddPermission) && $chrNeedAddPermission == 'N') && (isset($charNeedApproval) && $charNeedApproval == 'N'))
                                                <button type="submit" name="saveandexit" class="btn btn-green-drake" value="saveandexit" title="{!! trans('banner::template.common.saveandexit') !!}">{!! trans('banner::template.common.saveandexit') !!}</button>
                                                @else
                                                <button type="submit" name="saveandexit" class="btn btn-green-drake" value="approvesaveandexit" title="{!! trans('banner::template.common.approvesaveandexit') !!}">{!! trans('banner::template.common.approvesaveandexit') !!}</button>
                                                @endif
                                                @endif

                                                @endif
                                                <a class="btn red btn-outline" href="{{ url('powerpanel/banners') }}" title="{{ trans('banner::template.common.cancel') }}">{{ trans('banner::template.common.cancel') }}</a>
                                            </div>
                                        </div>
                                    </div>
                                    {!! Form::close() !!}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@section('scripts')
<script src="{{ $CDN_PATH.'resources/global/plugins/bootstrap-fileinput/bootstrap-fileinput.js' }}" type="text/javascript"></script>
<script type="text/javascript">
                                                            window.site_url = '{!! url("/") !!}';
                                                            var selectedRecord = '{{ isset($banners->fkIntPageId)?$banners->fkIntPageId:' ' }}';
                                                            var user_action = "{{ isset($banners)?'edit':'add' }}";
</script>
<script type="text/javascript">
    function OpenPassword(val) {
        if (val == 'PP') {
            $("#passid").show();
        } else {
            $("#passid").hide();
        }
    }
</script>
<script src="{{ $CDN_PATH.'resources/pages/scripts/custom.js' }}" type="text/javascript"></script>
<script src="{{ $CDN_PATH.'resources/pages/scripts/packages/banner/banners.js' }}" type="text/javascript"></script>
<script src="{{ $CDN_PATH.'resources/global/plugins/bootstrap-maxlength/bootstrap-maxlength.min.js' }}" type="text/javascript"></script>
<script src="{{ $CDN_PATH.'resources/global/plugins/seo-generator/seo-info-generator.js' }}" type="text/javascript"></script>
<script type="text/javascript">
    function isNumber(evt) {
        evt = (evt) ? evt : window.event;
        var charCode = (evt.which) ? evt.which : evt.keyCode;
        if (charCode > 31 && (charCode < 48 || charCode > 57)) {
            return false;
        }
        return true;
    }
    $('#modules').select2({
        placeholder: "Select Module",
        width: '100%',
        minimumResultsForSearch: 5
    }).on("change", function (e) {
        $("#modules").closest('.has-error').removeClass('has-error');
        $("#modules-error").remove();
        $('#records').show();
    });
    $('#foritem').select2({
        placeholder: "Select Module",
        width: '100%'
    }).on("change", function (e) {
        $("#foritem").closest('.has-error').removeClass('has-error');
        $("#foritem-error").remove();
    });
</script>
@endsection