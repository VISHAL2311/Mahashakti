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
                                    {!! Form::open(['method' => 'post','id'=>'frmphotoGallery']) !!}
                                    {!! Form::hidden('fkMainRecord', isset($photoGallery->fkMainRecord)?$photoGallery->fkMainRecord:old('fkMainRecord')) !!}
                                     @if(isset($photoGallery))
                                    @if (File::exists(base_path() . '/resources/views/powerpanel/partials/lockedpage.blade.php') != null)
                                    @include('powerpanel.partials.lockedpage',['pagedata'=>$photoGallery])
                                    @endif
                                    @endif

                                    <div class="form-group @if($errors->first('tag_line')) has-error @endif form-md-line-input">
                                        @php if(isset($photoGallery_highLight->varTitle) && ($photoGallery_highLight->varTitle != $photoGallery->varTitle)){
                                        $Class_title = " highlitetext";
                                        }else{
                                        $Class_title = "";
                                        } @endphp
                                        <label class="form_title {!! $Class_title !!}" for="site_name">{{ trans('photogallery::template.common.title') }} <span aria-required="true" class="required"> * </span></label>
                                        {!! Form::text('title', isset($photoGallery->varTitle) ? $photoGallery->varTitle:old('title'), array('maxlength'=>'150','placeholder' => trans('photogallery::template.common.title'),'class' => 'form-control seoField maxlength-handler titlespellingcheck','autocomplete'=>'off')) !!}
                                        <span class="help-block">
                                            {{ $errors->first('title') }}
                                        </span>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-12">
                                            @php if(isset($photoGallery_highLight->varTitle) && ($photoGallery_highLight->fkIntImgId != $photoGallery->fkIntImgId)){
                                            $Class_file = " highlitetext";
                                            }else{
                                            $Class_file = "";
                                            } @endphp
                                            <div class="image_thumb multi_upload_images">
                                                <div class="form-group">
                                                    <label class="form_title {{ $Class_file }}" for="front_logo">{{ trans('photogallery::template.common.selectimage') }}<span aria-required="true" class="required"> * </span></label>
                                                    <div class="clearfix"></div>
                                                    <div class="fileinput fileinput-new" data-provides="fileinput">
                                                        <div class="fileinput-preview thumbnail bankcoin_image_img" data-trigger="fileinput" style="width:100%;float:left; height:120px;position: relative;">
                                                            @if(old('image_url'))
                                                            <img src="{{ old('image_url') }}" />
                                                            @elseif(isset($photoGallery->fkIntImgId))
                                                            <img src="{!! App\Helpers\resize_image::resize($photoGallery->fkIntImgId,120,120) !!}" />
                                                            @else
                                                            <img class="img_opacity" src="{{ $CDN_PATH.'resources/images/upload_file.gif' }}" />
                                                            @endif
                                                            <!-- <img class="img_opacity" src="{{ $CDN_PATH.'resources/images/upload_file.gif' }}" /> -->
                                                        </div>
                                                        <div class="input-group">
                                                            <a class="media_manager" data-multiple="false" onclick="MediaManager.open('bankcoin_image');"><span class="fileinput-new"></span></a>
                                                            <input class="form-control" type="hidden" id="bankcoin_image" name="img_id" value="{{ isset($photoGallery->fkIntImgId)?$photoGallery->fkIntImgId:old('img_id') }}" />
                                                             @php
                                                             if (method_exists($MyLibrary, 'GetFolderID')) {
                                                                    if(isset($photoGallery->fkIntImgId)){
                                                                    $folderid = App\Helpers\MyLibrary::GetFolderID($photoGallery->fkIntImgId);
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
                                                            <a onclick="MediaManager.open('bankcoin_image');" class="media_manager remove_img"><i class="fa fa-pencil"></i></a>
                                                            <a href="javascript:;" class="fileinput-exists remove_img removeimg" data-dismiss="fileinput"><i class="fa fa-trash-o"></i></a>
                                                        </div>
                                                    </div>
                                                    <div class="clearfix"></div>
                                                    @php $height = 1080; $width = 1920; @endphp <span>{{ trans('photogallery::template.common.imageSize',['height'=>$height, 'width'=>$width]) }}</span>
                                                </div>
                                                <span class="help-block">
                                                    {{ $errors->first('img_id') }}
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                     
                                    <h3 class="form-section">{{ trans('photogallery::template.common.ContentScheduling') }}</h3>
                                    @php $defaultDt = (null !== old('start_date_time'))?old('start_date_time'):date('Y-m-d H:i'); @endphp
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group form-md-line-input">
                                                @php if(isset($photoGallery_highLight->dtDateTime) && ($photoGallery_highLight->dtDateTime != $photoGallery->dtDateTime)){
                                                $Class_date = " highlitetext";
                                                }else{
                                                $Class_date = "";
                                                } @endphp
                                                <label class="control-label form_title {!! $Class_date !!}">{{ trans('photogallery::template.common.startDateAndTime') }}<span aria-required="true" class="required"> * </span></label>
                                                <div class="input-group date form_meridian_datetime @if($errors->first('start_date_time')) has-error @endif" data-date="{{ Carbon\Carbon::today()->format('Y-m-d') }}T15:25:00Z">
                                                    <span class="input-group-btn date_default">
                                                        <button class="btn date-set fromButton" type="button">
                                                            <i class="fa fa-calendar"></i>
                                                        </button>
                                                    </span>
                                                    {!! Form::text('start_date_time', date('Y-m-d H:i',strtotime(isset($photoGallery->dtDateTime)?$photoGallery->dtDateTime:$defaultDt)), array('class' => 'form-control','maxlength'=>160,'size'=>'16','id'=>'start_date_time','autocomplete'=>'off','onkeypress'=>"javascript: return KeycheckOnlyDate(event);",'onpaste'=>'return false')) !!}
                                                </div>
                                                <span class="help-block">
                                                    {{ $errors->first('start_date_time') }}
                                                </span>
                                            </div>
                                        </div>
                                        @php $defaultDt = (null !== old('end_date_time'))?old('end_date_time'):null; @endphp
                                        @if ((isset($photoGallery->dtEndDateTime)==null))
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
                                                     @php if(isset($photoGallery_highLight->varTitle) && ($photoGallery_highLight->dtEndDateTime != $photoGallery->dtEndDateTime)){
                                                     $Class_end_date = " highlitetext";
                                                     }else{
                                                     $Class_end_date = "";
                                                     } @endphp
                                                     <label class="control-label form_title {!! $Class_end_date !!}" >{{ trans('photogallery::template.common.endDateAndTime') }} <span aria-required="true" class="required"> * </span></label>
                                                    <div class="pos_cal">
                                                        <span class="input-group-btn date_default">
                                                            <button class="btn date-set toButton" type="button">
                                                                <i class="fa fa-calendar"></i>
                                                            </button>
                                                        </span>
                                                        {!! Form::text('end_date_time', isset($photoGallery->dtEndDateTime)?date('Y-m-d H:i',strtotime($photoGallery->dtEndDateTime)):$defaultDt, array('class' => 'form-control','maxlength'=>160,'size'=>'16','id'=>'end_date_time','data-exp'=> $expChecked_yes,'data-newvalue','autocomplete'=>'off','onkeypress'=>"javascript: return KeycheckOnlyDate(event);",'onpaste'=>'return false')) !!}
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
                                    
                                    <h3 class="form-section">{{ trans('photogallery::template.common.displayinformation') }}</h3>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group @if($errors->first('order')) has-error @endif form-md-line-input">
                                                @php
                                                $display_order_attributes = array('class' => 'form-control','maxlength'=>5,'placeholder'=>trans('photogallery::template.common.displayorder'),'autocomplete'=>'off');
                                                @endphp
                                                @if(isset($photoGallery_highLight->intDisplayOrder) && ($photoGallery_highLight->intDisplayOrder != $photoGallery->intDisplayOrder))
                                                @php $Class_intDisplayOrder = " highlitetext"; @endphp
                                                @else
                                                @php $Class_intDisplayOrder = ""; @endphp
                                                @endif
                                                <label class="form_title {{ $Class_intDisplayOrder }}" for="site_name">{{ trans('photogallery::template.common.displayorder') }} <span aria-required="true" class="required"> * </span></label>
                                                {!! Form::text('order', isset($photoGallery->intDisplayOrder)?$photoGallery->intDisplayOrder:'1', $display_order_attributes) !!}
                                                <span style="color: red;">
                                                    {{ $errors->first('order') }}
                                                </span>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            @if(isset($photoGallery_highLight->chrPublish) && ($photoGallery_highLight->chrPublish != $photoGallery->chrPublish))
			                                              @php $Class_chrPublish = " highlitetext"; @endphp
			                                              @else
			                                              @php $Class_chrPublish = ""; @endphp
			                                              @endif
			                                              @if((isset($photoGallery) && $photoGallery->chrDraft == 'D'))
			                                              @include('powerpanel.partials.displayInfo',['Class_chrPublish'=>$Class_chrPublish,'display' => (isset($photoGallery->chrDraft)?$photoGallery->chrDraft:'D')])
			                                              @else
			                                              @include('powerpanel.partials.displayInfo',['Class_chrPublish'=>$Class_chrPublish,'display' => (isset($photoGallery->chrPublish)?$photoGallery->chrPublish:'Y')])
			                                              @endif
                                        </div>
                                    </div>
                                    <div class="form-actions">
                                        <div class="row">
                                            <div class="col-md-12">
                                                @if(isset($photoGallery->fkMainRecord) && $photoGallery->fkMainRecord != 0)
                                                <button type="submit" name="saveandexit" class="btn btn-green-drake" value="saveandexit" title="{!! trans('photogallery::template.common.approve') !!}">{!! trans('photogallery::template.common.approve') !!}</button>
                                                @else
                                                @if($userIsAdmin)
                                                <button type="submit" name="saveandedit" class="btn btn-green-drake" value="saveandedit" title="{!! trans('photogallery::template.common.saveandedit') !!}">{!! trans('photogallery::template.common.saveandedit') !!}</button>
                                                <button type="submit" name="saveandexit" class="btn btn-green-drake" value="saveandexit" title="{!! trans('photogallery::template.common.saveandexit') !!}">{!! trans('photogallery::template.common.saveandexit') !!}</button>
                                                @else
                                                @if((isset($chrNeedAddPermission) && $chrNeedAddPermission == 'N') && (isset($charNeedApproval) && $charNeedApproval == 'N'))
                                                <button type="submit" name="saveandexit" class="btn btn-green-drake" value="saveandexit" title="{!! trans('photogallery::template.common.saveandexit') !!}">{!! trans('photogallery::template.common.saveandexit') !!}</button>
                                                @else
                                                <button type="submit" name="saveandexit" class="btn btn-green-drake" value="approvesaveandexit" title="{!! trans('photogallery::template.common.approvesaveandexit') !!}">{!! trans('photogallery::template.common.approvesaveandexit') !!}</button>
                                                @endif
                                                @endif  
                                                @endif
                                                <a class="btn red btn-outline" href="{{ url('powerpanel/photo-gallery') }}" title="{{ trans('photogallery::template.common.cancel') }}">{{ trans('photogallery::template.common.cancel') }}</a>
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
<div class="clearfix"></div>
@endsection
@section('scripts')
<script type="text/javascript">
    window.site_url = '{!! url("/") !!}';
    var user_action = "{{ isset($photoGallery)?'edit':'add' }}";
    var moduleAlias = 'photo-gallery';
</script>
<script src="{{ $CDN_PATH.'resources/pages/scripts/packages/photogallery/photo_gallery_validations.js' }}" type="text/javascript"></script>
<script src="{{ $CDN_PATH.'resources/pages/scripts/custom.js' }}" type="text/javascript"></script>
<script src="{{ $CDN_PATH.'resources/global/plugins/bootstrap-maxlength/bootstrap-maxlength.min.js' }}" type="text/javascript"></script>
<script src="{{ $CDN_PATH.'resources/global/plugins/seo-generator/seo-info-generator.js' }}" type="text/javascript"></script>
@endsection