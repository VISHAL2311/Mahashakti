@section('css')
@endsection
@extends('powerpanel.layouts.app')
@section('title')
{{Config::get('Constant.SITE_NAME')}} - PowerPanel
@endsection
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
                                    {!! Form::open(['method' => 'post','id'=>'frmBlogs']) !!}
                                    {!! Form::hidden('fkMainRecord', isset($blogs->fkMainRecord)?$blogs->fkMainRecord:old('fkMainRecord')) !!}
                                    <div class="form-group @if($errors->first('tag_line')) has-error @endif form-md-line-input" style="display:none">
                                          @if(isset($blogs))
                                        @if (File::exists(base_path() . '/resources/views/powerpanel/partials/lockedpage.blade.php') != null)
                                        @include('powerpanel.partials.lockedpage',['pagedata'=>$blogs])
                                        @endif
                                        @endif
                                        @php
                                        if(isset($blogs_highLight->intFKCategory) && ($blogs_highLight->intFKCategory != $blogs->intFKCategory)){
                                        $Class_title = " highlitetext";
                                        }else{
                                        $Class_title = "";
                                        }
                                        $currentCatAlias = '';
                                        @endphp
                                        <label class="form_title {{ $Class_title }}" for="site_name">Select Category <span aria-required="true" class="required"> * </span></label>
                                        <select class="form-control bs-select select2" name="category_id">
                                            <option value=" ">-- Select Category --</option>
                                            @foreach ($blogCategory as $cat)
                                            @php $permissionName = 'blogs-list' @endphp
                                            @php $selected = ''; @endphp
                                            @if(isset($blogs->intFKCategory))
                                            @if($cat['id'] == $blogs->intFKCategory)
                                            @php $selected = 'selected'; $currentCatAlias = $cat['alias']['varAlias'];  @endphp
                                            @endif
                                            @endif
                                            <option value="{{ $cat['id'] }}" data-categryalias="{{ $cat['alias']['varAlias'] }}" {{ $selected }} >{{ $cat['varModuleName']== "blogs"?'Select Category':$cat['varTitle'] }}</option>
                                            @endforeach
                                        </select>
                                        <span class="help-block">
                                            {{ $errors->first('category') }}
                                        </span>
                                    </div>
                                    <div class="form-group @if($errors->first('title')) has-error @endif form-md-line-input">
                                        @php if(isset($blogs_highLight->varTitle) && ($blogs_highLight->varTitle != $blogs->varTitle)){
                                        $Class_title = " highlitetext";
                                        }else{
                                        $Class_title = "";
                                        } @endphp
                                        <label class="form_title {!! $Class_title !!}" for="site_name">{{ trans('blogs::template.common.title') }} <span aria-required="true" class="required"> * </span></label>
                                        {!! Form::text('title', isset($blogs->varTitle) ? $blogs->varTitle:old('title'), array('maxlength'=>'110','id'=>'title','placeholder' => trans('blogs::template.common.title'),'class' => 'form-control hasAlias seoField maxlength-handler titlespellingcheck','autocomplete'=>'off')) !!}
                                        <span class="help-block">
                                            {{ $errors->first('title') }}
                                        </span>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <!-- code for alias -->
                                            {!! Form::hidden(null, null, array('class' => 'hasAlias','data-url' => 'powerpanel/blogs')) !!}
                                            {!! Form::hidden('alias', isset($blogs->alias->varAlias) ? $blogs->alias->varAlias : old('alias'), array('class' => 'aliasField')) !!}
                                            {!! Form::hidden('oldAlias', isset($blogs->alias->varAlias)?$blogs->alias->varAlias : old('alias')) !!}
                                            {!! Form::hidden('previewId') !!}
                                            <div class="form-group alias-group {{!isset($blogs->alias)?'hide':''}}">
                                                <label class="form_title" for="Url">{{ trans('blogs::template.common.url') }} :</label>
                                                @if(isset($blogs->alias->varAlias) && !$userIsAdmin)
                                               <a class="alias">
                                                {!! url("/") !!}
                                                </a>
                                                @else
                                                @if(auth()->user()->can('blogs-create'))
                                                <a href="javascript:void;" class="alias">{!! url("/") !!}</a>
                                                <a href="javascript:void(0);" class="editAlias" title="Edit">
                                                    <i class="fa fa-edit"></i>
                                                </a>
                                                <a class="without_bg_icon openLink" title="Open Link" href="{{ url('blogs/'.(isset($blogs->alias->varAlias) && isset($blogs) ? $blogs->alias->varAlias : '' ))}}" target="_blank">
                                                    <i class="fa fa-external-link" aria-hidden="true"></i>
                                                </a>
                                                @endif
                                                @endif
                                            </div>
                                            <span class="help-block">
                                                {{ $errors->first('alias') }}
                                            </span>
                                            <!-- code for alias -->
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-12">
                                            @if(isset($blogs_highLight->fkIntImgId) && ($blogs_highLight->fkIntImgId != $blogs->fkIntImgId))
                                            @php $Class_fkIntImgId = " highlitetext"; @endphp
                                            @else
                                            @php $Class_fkIntImgId = ""; @endphp
                                            @endif
                                            <div class="image_thumb multi_upload_images">
                                                <div class="form-group">
                                                    <label class="form_title {{ $Class_fkIntImgId }}" for="front_logo">{{ trans('blogs::template.common.selectimage') }} <span aria-required="true" class="required"> * </span></label>
                                                    <div class="clearfix"></div>
                                                    <div class="fileinput fileinput-new" data-provides="fileinput">
                                                        <div class="fileinput-preview thumbnail blog_image_img" data-trigger="fileinput" style="width:100%;float:left; height:120px;position: relative;">
                                                            @if(old('image_url'))
                                                            <img src="{{ old('image_url') }}" />
                                                            @elseif(isset($blogs->fkIntImgId))
                                                            <img src="{!! App\Helpers\resize_image::resize($blogs->fkIntImgId,120,120) !!}" />
                                                            @else
                                                            <img class="img_opacity" src="{{ $CDN_PATH.'resources/images/upload_file.gif' }}" />
                                                            @endif
                                                        </div>

                                                        <div class="input-group">
                                                            <a class="media_manager" data-multiple="false" onclick="MediaManager.open('blog_image');"><span class="fileinput-new"></span></a>
                                                            <input class="form-control" type="hidden" id="blog_image" name="img_id" value="{{ isset($blogs->fkIntImgId)?$blogs->fkIntImgId:old('img_id') }}" />
                                                             @php
                                                             if (method_exists($MyLibrary, 'GetFolderID')) {
                                                                    if(isset($blogs->fkIntImgId)){
                                                                    $folderid = App\Helpers\MyLibrary::GetFolderID($blogs->fkIntImgId);
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
                                                            <a onclick="MediaManager.open('blog_image');" class="media_manager remove_img"><i class="fa fa-pencil"></i></a>
                                                            <a href="javascript:;" class="fileinput-exists remove_img removeimg" data-dismiss="fileinput"><i class="fa fa-trash-o"></i></a>
                                                        </div>

                                                    </div>
                                                    <div class="clearfix"></div>
                                                    @php $height = isset($settings->height)?$settings->height:645; $width = isset($settings->width)?$settings->width:966; @endphp <span>{{ trans('blogs::template.common.imageSize',['height'=>$height, 'width'=>$width]) }}</span>
                                                </div>
                                                <span class="help-block">
                                                    {{ $errors->first('img_id') }}
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                    @php $defaultDt = (null !== old('start_date_time'))?old('start_date_time'):date('Y-m-d H:i'); @endphp
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group form-md-line-input">
                                                @php if(isset($blogs_highLight->dtDateTime) && ($blogs_highLight->dtDateTime != $blogs->dtDateTime)){
                                                $Class_date = " highlitetext";
                                                }else{
                                                $Class_date = "";
                                                } @endphp
                                                <label class="control-label form_title {!! $Class_date !!}">{{ trans('blogs::template.common.startDateAndTime') }}<span aria-required="true" class="required"> * </span></label>
                                                <div class="input-group date form_meridian_datetime @if($errors->first('start_date_time')) has-error @endif" data-date="{{ Carbon\Carbon::today()->format('Y-m-d') }}T15:25:00Z">
                                                    <span class="input-group-btn date_default">
                                                        <button class="btn date-set fromButton" type="button">
                                                            <i class="fa fa-calendar"></i>
                                                        </button>
                                                    </span>
                                                    {!! Form::text('start_date_time', date('Y-m-d H:i',strtotime(isset($blogs->dtDateTime)?$blogs->dtDateTime:$defaultDt)), array('class' => 'form-control','maxlength'=>160,'size'=>'16','id'=>'start_date_time','autocomplete'=>'off','onkeypress'=>"javascript: return KeycheckOnlyDate(event);",'onpaste'=>'return false')) !!}
                                                </div>
                                                <span class="help-block">
                                                    {{ $errors->first('start_date_time') }}
                                                </span>
                                            </div>
                                        </div>
                                        @php $defaultDt = (null !== old('end_date_time'))?old('end_date_time'):null; @endphp
                                        @if ((isset($blogs->dtEndDateTime)==null))
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
                                                     @php if(isset($blogs_highLight->dtEndDateTime) && ($blogs_highLight->dtEndDateTime != $blogs->dtEndDateTime)){
                                                     $Class_end_date = " highlitetext";
                                                     }else{
                                                     $Class_end_date = "";
                                                     } @endphp
                                                     <label class="control-label form_title {!! $Class_end_date !!}" >{{ trans('blogs::template.common.endDateAndTime') }} <span aria-required="true" class="required"> * </span></label>
                                                    <div class="pos_cal">
                                                        <span class="input-group-btn date_default">
                                                            <button class="btn date-set toButton" type="button">
                                                                <i class="fa fa-calendar"></i>
                                                            </button>
                                                        </span>
                                                        {!! Form::text('end_date_time', isset($blogs->dtEndDateTime)?date('Y-m-d H:i',strtotime($blogs->dtEndDateTime)):$defaultDt, array('class' => 'form-control','maxlength'=>160,'size'=>'16','id'=>'end_date_time','data-exp'=> $expChecked_yes,'data-newvalue','autocomplete'=>'off','onkeypress'=>"javascript: return KeycheckOnlyDate(event);",'onpaste'=>'return false')) !!}
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
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group @if($errors->first('short_description')) has-error @endif form-md-line-input">
                                                @php if(isset($blogs_highLight->varShortDescription) && ($blogs_highLight->varShortDescription != $blogs->varShortDescription)){
                                                $Class_ShortDescription = " highlitetext";
                                                }else{
                                                $Class_ShortDescription = "";
                                                } @endphp
                                                <label class="form_title {!! $Class_ShortDescription !!}">Short Description<span aria-required="true" class="required"> * </span></label>
                                                {!! Form::textarea('short_description', isset($blogs->varShortDescription)?$blogs->varShortDescription:old('short_description'), array('maxlength' => isset($settings->short_desc_length)?$settings->short_desc_length:500,'class' => 'form-control seoField maxlength-handler shortdescspellingcheck','id'=>'varShortDescription','rows'=>'3','placeholder'=>'Short Description')) !!}
                                                <span class="help-block">{{ $errors->first('short_description') }}</span> </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group @if($errors->first('description')) has-error @endif form-md-line-input">
                                                @if (Config::get('Constant.DEFAULT_VISUAL') == 'Y')
                                                    <div id="body-roll">											
                                                        @php
                                                            $sections = [];
                                                        @endphp
                                                        @if(isset($blogs))
                                                            @php
                                                                $sections = json_decode($blogs->txtDescription);
                                                            @endphp
                                                        @endif
                                                        <!-- Builder include -->
                                                        @php
                                                            Powerpanel\VisualComposer\Controllers\VisualComposerController::page_section(['sections'=>$sections])
                                                        @endphp
                                                    </div>
                                                @else
                                                    @php if(isset($blogs_highLight->txtDescription) && ($blogs_highLight->txtDescription != $blogs->txtDescription)){
                                                    $Class_Description = " highlitetext";
                                                    }else{
                                                    $Class_Description = "";
                                                    } @endphp
                                                    <label class="form_title {!! $Class_Description !!}">{{ trans('blogs::template.common.description') }}</label>
                                                    {!! Form::textarea('description', isset($blogs->txtDescription)?$blogs->txtDescription:old('description'), array('placeholder' => trans('blogs::template.common.description'),'class' => 'form-control','id'=>'txtDescription')) !!}
                                                @endif
                                                <span class="help-block">{{ $errors->first('description') }}</span>
                                            </div>
                                        </div>
                                    </div>
                                    @if(Config::get('Constant.CHRSearchRank') == 'Y')
                                    @if(isset($blogs->intSearchRank))
                                    @php $srank = $blogs->intSearchRank; @endphp
                                    @else
                                    @php
                                    $srank = null !== old('search_rank') ? old('search_rank') : 2 ;
                                    @endphp
                                    @endif
                                    @if(isset($blogs_highLight->intSearchRank) && ($blogs_highLight->intSearchRank != $blogs->intSearchRank))
                                    @php $Class_intSearchRank = " highlitetext"; @endphp
                                    @else
                                    @php $Class_intSearchRank = ""; @endphp
                                    @endif
                                    <div class="row" style="display:none;">
                                        <div class="col-md-12">
                                            <label class="{{ $Class_intSearchRank }} form_title">Search Ranking</label>
                                            <a href="javascript:;" data-toggle="tooltip" class="config" data-placement="bottom" data-original-title="{{ trans('blogs::template.common.SearchEntityTools') }}" title="{{ trans('blogs::template.common.SearchEntityTools') }}"><i class="fa fa-question"></i></a>
                                            <div class="wrapper search_rank">
                                                <label for="yes_radio" id="yes-lbl">High</label><input type="radio" value="1" name="search_rank" @if($srank == 1) checked @endif id="yes_radio">
                                                                                                       <label for="maybe_radio" id="maybe-lbl">Medium</label><input type="radio" value="2" name="search_rank" @if($srank == 2) checked @endif id="maybe_radio">
                                                                                                       <label for="no_radio" id="no-lbl">Low</label><input type="radio" value="3" name="search_rank" @if($srank == 3) checked @endif id="no_radio">
                                                                                                       <div class="toggle"></div>
                                            </div>
                                        </div>
                                    </div>
                                    @endif
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="nopadding">
                                                @include('powerpanel.partials.seoInfo',['form'=>'frmBlogs','inf'=>isset($metaInfo)?$metaInfo:false,'inf_highLight'=> isset($metaInfo_highLight)?$metaInfo_highLight:false])
                                            </div>
                                        </div>
                                    </div>
                                    <h3 class="form-section">{{ trans('blogs::template.common.displayinformation') }}</h3>
                                    <div class="row">
                                        <div class="col-md-6">
                                            @if(isset($blogs_highLight->chrPublish) && ($blogs_highLight->chrPublish != $blogs->chrPublish))
                                            @php $Class_chrPublish = " highlitetext"; @endphp
                                            @else
                                            @php $Class_chrPublish = ""; @endphp
                                            @endif
                                            @include('powerpanel.partials.displayInfo',['Class_chrPublish'=>$Class_chrPublish,'display' => isset($blogs->chrPublish)?$blogs->chrPublish:null])
                                        </div>
                                        
                                    </div>
                                    <div class="form-actions">
                                        <div class="row">
                                            <div class="col-md-12">
                                                @if(isset($blogs->fkMainRecord) && $blogs->fkMainRecord != 0)
                                                <button type="submit" name="saveandexit" class="btn btn-green-drake" value="saveandexit" title="{!! trans('blogs::template.common.approve') !!}">{!! trans('blogs::template.common.approve') !!}</button>
                                                @else
                                                @if($userIsAdmin)
                                                <button type="submit" name="saveandedit" class="btn btn-green-drake" value="saveandedit" title="{!! trans('blogs::template.common.saveandedit') !!}">{!! trans('blogs::template.common.saveandedit') !!}</button>
                                                <button type="submit" name="saveandexit" class="btn btn-green-drake" value="saveandexit" title="{!! trans('blogs::template.common.saveandexit') !!}">{!! trans('blogs::template.common.saveandexit') !!}</button>
                                                @else
                                                @if((isset($chrNeedAddPermission) && $chrNeedAddPermission == 'N') && (isset($charNeedApproval) && $charNeedApproval == 'N'))
                                                <button type="submit" name="saveandexit" class="btn btn-green-drake" value="saveandexit" title="{!! trans('blogs::template.common.saveandexit') !!}">{!! trans('blogs::template.common.saveandexit') !!}</button>
                                                @else
                                                <button type="submit" name="saveandexit" class="btn btn-green-drake" value="approvesaveandexit" title="{!! trans('blogs::template.common.approvesaveandexit') !!}">{!! trans('blogs::template.common.approvesaveandexit') !!}</button>
                                                @endif
                                                @endif
                                                @endif
                                                <a class="btn red btn-outline" href="{{ url('powerpanel/blogs') }}" title="{{ trans('blogs::template.common.cancel') }}">{{ trans('blogs::template.common.cancel') }}</a>
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
@if (Config::get('Constant.DEFAULT_VISUAL') == 'Y')
    @php Powerpanel\VisualComposer\Controllers\VisualComposerController::get_dialog_maker()@endphp
    @php Powerpanel\VisualComposer\Controllers\VisualComposerController::get_visual_checkEditor()@endphp
@else
    @include('powerpanel.partials.ckeditor',['config'=>'docsConfig'])
@endif
@endsection
@section('scripts')
<script type="text/javascript">
            window.site_url = '{!! url("/") !!}';
            var seoFormId = 'frmBlogs';
            var user_action = "{{ isset($blogs)?'edit':'add' }}";
            var moduleAlias = "{{ App\Helpers\MyLibrary::getFrontUri('blogs')['moduleAlias'] }}";
            var preview_add_route = '{!! route("powerpanel.blogs.addpreview") !!}';
            var previewForm = $('#frmBlogs');
            var isDetailPage = true;
            function generate_seocontent1(formname) {
            var Meta_Title = document.getElementById('title').value + "";
                    var abcd = $('textarea#txtDescription').val();
                    var def = abcd.replace(/<a(\s[^>]*)?>.*?<\/a>/ig, "")
                    var abc = def.replace(/^(\s*)|(\s*)$/g, '').replace(/\s+/g, ' ');
                    var outString1 = abc.replace(/(<([^>]+)>)/ig, "");
                    var Meta_Description = outString1.substr(0, 200);
                    var Meta_Keyword = "";
                    $('#varMetaTitle').val(Meta_Title);
//                                    $('#varMetaKeyword').val(Meta_Keyword);
                    $('#varMetaDescription').val(Meta_Description);
                    $('#meta_title').html(Meta_Title);
                    $('#meta_description').html(Meta_Description);
            }

    function OpenPassword(val) {
    if (val == 'PP') {
    $("#passid").show();
    } else {
    $("#passid").hide();
    }
    }
</script>
<script src="{{ $CDN_PATH.'resources/pages/scripts/packages/blogs/blogs_validations.js' }}" type="text/javascript"></script>
<script src="{{ $CDN_PATH.'resources/pages/scripts/custom.js' }}" type="text/javascript"></script>
<script src="{{ $CDN_PATH.'resources/global/plugins/custom-alias/alias-generator.js' }}" type="text/javascript"></script>
<script src="{{ $CDN_PATH.'resources/global/plugins/bootstrap-maxlength/bootstrap-maxlength.min.js' }}" type="text/javascript"></script>
<script src="{{ $CDN_PATH.'resources/global/plugins/seo-generator/seo-info-generator.js' }}" type="text/javascript"></script>
@if (Config::get('Constant.DEFAULT_VISUAL') == 'Y')
    @php Powerpanel\VisualComposer\Controllers\VisualComposerController::get_builder_css_js()@endphp
@endif
@endsection