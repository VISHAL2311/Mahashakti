@section('css')
@endsection
@extends('powerpanel.layouts.app')
@section('title')
{{Config::get('Constant.SITE_NAME')}} - PowerPanel
@stop
@section('content')
@php $settings = json_decode(Config::get("Constant.MODULE.SETTINGS")); @endphp
@include('powerpanel.partials.breadcrumbs')
<div class="col-md-12 settings">
    <div class="row">
        @if(Session::has('message'))
        <div class="alert alert-success">
            <button class="close" data-close="alert"></button>
            {{ Session::get('message') }}
        </div>
        @endif
        <div class="portlet light bordered">
            <div class="portlet-body">
                <div class="tabbable tabbable-tabdrop">
                    <div class="tab-content">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="portlet-body form_pattern">
                                    {!! Form::open(['method' => 'post','id'=>'frmvideoGallery']) !!}
                                    {!! Form::hidden('fkMainRecord', isset($videoGallery->fkMainRecord)?$videoGallery->fkMainRecord:old('fkMainRecord')) !!}
                                    @if(isset($videoGallery))
                                    @if (File::exists(base_path() . '/resources/views/powerpanel/partials/lockedpage.blade.php') != null)
                                    @include('powerpanel.partials.lockedpage',['pagedata'=>$videoGallery])
                                    @endif
                                    @endif
                                    <div class="form-group @if($errors->first('tag_line')) has-error @endif form-md-line-input">
                                        @php if(isset($videoGallery_highLight->varTitle) && ($videoGallery_highLight->varTitle != $videoGallery->varTitle)){
                                        $Class_title = " highlitetext";
                                        }else{
                                        $Class_title = "";
                                        } @endphp
                                        <label class="form_title {!! $Class_title !!}" for="site_name">{{ trans('videogallery::template.common.title') }} <span aria-required="true" class="required"> * </span></label>
                                        {!! Form::text('title', isset($videoGallery->varTitle) ? $videoGallery->varTitle:old('title'), array('maxlength'=>'150','placeholder' => trans('videogallery::template.common.title'),'class' => 'form-control seoField maxlength-handler titlespellingcheck','autocomplete'=>'off')) !!}
                                        <span class="help-block">
                                            {{ $errors->first('title') }}
                                        </span>
                                    </div>
                                    <div class="form-group {{ $errors->has('link') ? ' has-error' : '' }} form-md-line-input">
                                        @if(isset($videoGallery_highLight->txtLink) && ($videoGallery_highLight->txtLink != $videoGallery->txtLink))
                                        @php $Class_txtLink = " highlitetext"; @endphp
                                        @else
                                        @php $Class_txtLink = ""; @endphp
                                        @endif
                                        <label class="form_title {{ $Class_txtLink }}" for="link">{!! trans('videogallery::template.videoGalleryModule.extLink') !!} <span aria-required="true" class="required"> * </span></label>
                                        {!! Form::text('link', isset($videoGallery->txtLink)?$videoGallery->txtLink:old('link'), array('class' => 'form-control input-sm', 'data-url' => 'powerpanel/video-gallery','id' => 'Link','placeholder' => 'Link','autocomplete'=>'off')) !!}
                                        <span style="color:#e73d4a">
                                            {{ $errors->first('link') }}
                                        </span>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-12">
                                            @php if(isset($videoGallery_highLight->varTitle) && ($videoGallery_highLight->fkIntImgId != $videoGallery->fkIntImgId)){
                                            $Class_file = " highlitetext";
                                            }else{
                                            $Class_file = "";
                                            } @endphp
                                            <div class="image_thumb multi_upload_images">
                                                <div class="form-group">
                                                    <label class="form_title {{ $Class_file }}" for="front_logo">Select Video Cover Image<span aria-required="true" class="required"> * </span></label>
                                                    <div class="clearfix"></div>
                                                    <div class="fileinput fileinput-new" data-provides="fileinput">
                                                        <div class="fileinput-preview thumbnail video_url_img" data-trigger="fileinput" style="width:100%;float:left; height:120px;position: relative;">
                                                            @if(old('image_url'))
                                                            <img src="{{ old('image_url') }}" />
                                                            @elseif(isset($videoGallery->fkIntImgId))
                                                            <img src="{!! App\Helpers\resize_image::resize($videoGallery->fkIntImgId,120,120) !!}" />
                                                            @else
                                                            <img class="img_opacity" src="{{ $CDN_PATH.'resources/images/upload_file.gif' }}" />
                                                            @endif
                                                            <!-- <img class="img_opacity" src="{{ $CDN_PATH.'resources/images/upload_file.gif' }}" /> -->
                                                        </div>
                                                        <div class="input-group">
                                                            <a class="media_manager" data-multiple="false" onclick="MediaManager.open('video_url');"><span class="fileinput-new"></span></a>
                                                            <input class="form-control" type="hidden" id="video_url" name="img_id" value="{{ isset($videoGallery->fkIntImgId)?$videoGallery->fkIntImgId:old('img_id') }}" />
                                                              @php
                                                               if (method_exists($MyLibrary, 'GetFolderID')) {
                                                                    if(isset($videoGallery->fkIntImgId)){
                                                                    $folderid = App\Helpers\MyLibrary::GetFolderID($videoGallery->fkIntImgId);
                                                                    @endphp
                                                                    @if(isset($folderid->fk_folder) && $folderid->fk_folder != '0')
                                                                    <input class="form-control" type="hidden" id="folder_id" name="folder_id" value="{{ $folderid->fk_folder }}" />
                                                                    @endif
                                                                    @php
                                                                    }
                                                                    }
                                                                    @endphp
                                                             <input class="form-control" type="hidden" id="image_url" name="image_url" value="{{ old('image_url') }}" />
                                                        </div>
                                                        <div class="overflow_layer">
                                                            <a onclick="MediaManager.open('video_url');" class="media_manager remove_img"><i class="fa fa-pencil"></i></a>
                                                            <a href="javascript:;" class="fileinput-exists remove_img removeimg" data-dismiss="fileinput"><i class="fa fa-trash-o"></i></a>
                                                        </div>
                                                    </div>
                                                    <div class="clearfix"></div>
                                                    @php $height = isset($settings->height)?$settings->height:303; $width = isset($settings->width)?$settings->width:537; @endphp <span>{{ trans('videogallery::template.common.imageSize',['height'=>$height, 'width'=>$width]) }}</span>
                                                </div>
                                                <span class="help-block">
                                                    {{ $errors->first('img_id') }}
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                    @if(Config::get('Constant.CHRSearchRank') == 'Y')
                                    @if(isset($videoGallery->intSearchRank))
                                    @php $srank = $videoGallery->intSearchRank; @endphp
                                    @else
                                    @php
                                    $srank = null !== old('search_rank') ? old('search_rank') : 2 ;
                                    @endphp
                                    @endif
                                    @if(isset($videoGallery_highLight->intSearchRank) && ($videoGallery_highLight->intSearchRank != $videoGallery->intSearchRank))
                                    @php $Class_intSearchRank = " highlitetext"; @endphp
                                    @else
                                    @php $Class_intSearchRank = ""; @endphp
                                    @endif
                                    <div class="row" style="display:none;">
                                        <div class="col-md-12">
                                            <label class="{{ $Class_intSearchRank }} form_title">Search Ranking</label>
                                            <a href="javascript:;" data-toggle="tooltip" class="config" data-placement="bottom" data-original-title="{{ trans('videogallery::template.common.SearchEntityTools') }}" title="{{ trans('videogallery::template.common.SearchEntityTools') }}"><i class="fa fa-question"></i></a>
                                            <div class="wrapper search_rank">
                                                <label for="yes_radio" id="yes-lbl">High</label><input type="radio" value="1" name="search_rank" @if($srank == 1) checked @endif id="yes_radio">
                                                                                                       <label for="maybe_radio" id="maybe-lbl">Medium</label><input type="radio" value="2" name="search_rank" @if($srank == 2) checked @endif id="maybe_radio">
                                                                                                       <label for="no_radio" id="no-lbl">Low</label><input type="radio" value="3" name="search_rank" @if($srank == 3) checked @endif id="no_radio">
                                                                                                       <div class="toggle"></div>
                                            </div>
                                        </div>
                                    </div>
                                    @endif
                                    
                                    <h3 style="display:none;" class="form-section">{{ trans('videogallery::template.common.ContentScheduling') }}</h3>
                                    @php $defaultDt = (null !== old('start_date_time'))?old('start_date_time'):date('Y-m-d H:i'); @endphp
                                    <div class="row" style="display:none;">
                                        <div class="col-md-6">
                                            <div class="form-group form-md-line-input">
                                                @php if(isset($videoGallery_highLight->dtDateTime) && ($videoGallery_highLight->dtDateTime != $videoGallery->dtDateTime)){
                                                $Class_date = " highlitetext";
                                                }else{
                                                $Class_date = "";
                                                } @endphp
                                                <label class="control-label form_title {!! $Class_date !!}">{{ trans('videogallery::template.common.startDateAndTime') }}<span aria-required="true" class="required"> * </span></label>
                                                <div class="input-group date form_meridian_datetime @if($errors->first('start_date_time')) has-error @endif" data-date="{{ Carbon\Carbon::today()->format('Y-m-d') }}T15:25:00Z">
                                                    <span class="input-group-btn date_default">
                                                        <button class="btn date-set fromButton" type="button">
                                                            <i class="fa fa-calendar"></i>
                                                        </button>
                                                    </span>
                                                    {!! Form::text('start_date_time', date('Y-m-d H:i',strtotime(isset($videoGallery->dtDateTime)?$videoGallery->dtDateTime:$defaultDt)), array('class' => 'form-control','maxlength'=>160,'size'=>'16','id'=>'start_date_time','autocomplete'=>'off','onkeypress'=>"javascript: return KeycheckOnlyDate(event);",'onpaste'=>'return false')) !!}
                                                </div>
                                                <span class="help-block">
                                                    {{ $errors->first('start_date_time') }}
                                                </span>
                                            </div>
                                        </div>
                                        @php $defaultDt = (null !== old('end_date_time'))?old('end_date_time'):null; @endphp
                                        @if ((isset($videoGallery->dtEndDateTime)==null))
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
                                                     @php if(isset($videoGallery_highLight->varTitle) && ($videoGallery_highLight->dtEndDateTime != $videoGallery->dtEndDateTime)){
                                                     $Class_end_date = " highlitetext";
                                                     }else{
                                                     $Class_end_date = "";
                                                     } @endphp
                                                     <label class="control-label form_title {!! $Class_end_date !!}" >{{ trans('videogallery::template.common.endDateAndTime') }} <span aria-required="true" class="required"> * </span></label>
                                                    <div class="pos_cal">
                                                        <span class="input-group-btn date_default">
                                                            <button class="btn date-set toButton" type="button">
                                                                <i class="fa fa-calendar"></i>
                                                            </button>
                                                        </span>
                                                        {!! Form::text('end_date_time', isset($videoGallery->dtEndDateTime)?date('Y-m-d H:i',strtotime($videoGallery->dtEndDateTime)):$defaultDt, array('class' => 'form-control','maxlength'=>160,'size'=>'16','id'=>'end_date_time','data-exp'=> $expChecked_yes,'data-newvalue','autocomplete'=>'off','onkeypress'=>"javascript: return KeycheckOnlyDate(event);",'onpaste'=>'return false')) !!}
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
                                    
                                    <h3 class="form-section">{{ trans('videogallery::template.common.displayinformation') }}</h3>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group @if($errors->first('order')) has-error @endif form-md-line-input">
                                                @php
                                                $display_order_attributes = array('class' => 'form-control','maxlength'=>5,'placeholder'=>trans('videogallery::template.common.displayorder'),'autocomplete'=>'off');
                                                @endphp
                                                @if(isset($videoGallery_highLight->intDisplayOrder) && ($videoGallery_highLight->intDisplayOrder != $videoGallery->intDisplayOrder))
                                                @php $Class_intDisplayOrder = " highlitetext"; @endphp
                                                @else
                                                @php $Class_intDisplayOrder = ""; @endphp
                                                @endif
                                                <label class="form_title {{ $Class_intDisplayOrder }}" for="site_name">{{ trans('videogallery::template.common.displayorder') }} <span aria-required="true" class="required"> * </span></label>
                                                {!! Form::text('order', isset($videoGallery->intDisplayOrder)?$videoGallery->intDisplayOrder:'1', $display_order_attributes) !!}
                                                <span style="color: red;">
                                                    {{ $errors->first('order') }}
                                                </span>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            @if(isset($videoGallery_highLight->chrPublish) && ($videoGallery_highLight->chrPublish != $videoGallery->chrPublish))
                                            @php $Class_chrPublish = " highlitetext"; @endphp
                                            @else
                                            @php $Class_chrPublish = ""; @endphp
                                            @endif
                                            @if((isset($videoGallery) && $videoGallery->chrDraft == 'D'))
                                            @include('powerpanel.partials.displayInfo',['Class_chrPublish'=>$Class_chrPublish,'display' => (isset($videoGallery->chrDraft)?$videoGallery->chrDraft:'D')])
                                            @else
                                            @include('powerpanel.partials.displayInfo',['Class_chrPublish'=>$Class_chrPublish,'display' => (isset($videoGallery->chrPublish)?$videoGallery->chrPublish:'Y')])
                                            @endif
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12">
                                            @if(isset($videoGallery->fkMainRecord) && $videoGallery->fkMainRecord != 0)
                                            <button type="submit" name="saveandexit" class="btn btn-green-drake" value="saveandexit" title="{!! trans('videogallery::template.common.approve') !!}">{!! trans('videogallery::template.common.approve') !!}</button>
                                            @else
                                            @if($userIsAdmin)
                                            <button type="submit" name="saveandedit" class="btn btn-green-drake" value="saveandedit" title="{!! trans('videogallery::template.common.saveandedit') !!}">{!! trans('videogallery::template.common.saveandedit') !!}</button>
                                            <button type="submit" name="saveandexit" class="btn btn-green-drake" value="saveandexit" title="{!! trans('videogallery::template.common.saveandexit') !!}">{!! trans('videogallery::template.common.saveandexit') !!}</button>
                                            @else
                                            @if((isset($chrNeedAddPermission) && $chrNeedAddPermission == 'N') && (isset($charNeedApproval) && $charNeedApproval == 'N'))
                                            <button type="submit" name="saveandexit" class="btn btn-green-drake" value="saveandexit" title="{!! trans('videogallery::template.common.saveandexit') !!}">{!! trans('videogallery::template.common.saveandexit') !!}</button>
                                            @else
                                            <button type="submit" name="saveandexit" class="btn btn-green-drake" value="approvesaveandexit" title="{!! trans('videogallery::template.common.approvesaveandexit') !!}">{!! trans('videogallery::template.common.approvesaveandexit') !!}</button>
                                            @endif
                                            @endif  
                                            @endif
                                            <a class="btn red btn-outline" href="{{ url('powerpanel/video-gallery') }}" title="{{ trans('videogallery::template.common.cancel') }}">{{ trans('videogallery::template.common.cancel') }}</a>
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
<div class="clearfix"></div>
@endsection
@section('scripts')
<script type="text/javascript">
    window.site_url = '{!! url("/") !!}';
    var user_action = "{{ isset($videoGallery)?'edit':'add' }}";
    var moduleAlias = 'videoGallery';
</script>
<script src="{{ $CDN_PATH.'resources/pages/scripts/packages/videogallery/video_gallery_validations.js' }}" type="text/javascript"></script>
<script src="{{ $CDN_PATH.'resources/pages/scripts/custom.js' }}" type="text/javascript"></script>
<script src="{{ $CDN_PATH.'resources/global/plugins/bootstrap-maxlength/bootstrap-maxlength.min.js' }}" type="text/javascript"></script>
<script src="{{ $CDN_PATH.'resources/global/plugins/seo-generator/seo-info-generator.js' }}" type="text/javascript"></script>
@endsection