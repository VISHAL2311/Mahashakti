@section('css')
<link href="{{ $CDN_PATH.'resources/global/plugins/bootstrap-fileinput/bootstrap-fileinput.css' }}" rel="stylesheet" type="text/css" />
@endsection
@extends('powerpanel.layouts.app')
@section('title')
{{Config::get('Constant.SITE_NAME')}} - PowerPanel
@endsection
@php $settings = json_decode(Config::get("Constant.MODULE.SETTINGS")); @endphp
@section('content')
@include('powerpanel.partials.breadcrumbs')
<div class="row">
    <div class="col-sm-12">
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
                                <div class="tab-pane active" id="general">
                                    <div class="portlet-body form_pattern">
                                        {!! Form::open(['method' => 'post','id'=>'frmBlogCategory']) !!}
                                         @if(isset($blogCategory))
                                        @if (File::exists(base_path() . '/resources/views/powerpanel/partials/lockedpage.blade.php') != null)
                                        @include('powerpanel.partials.lockedpage',['pagedata'=>$blogCategory])
                                        @endif
                                        @endif
                                        
                                        <div class="form-body">
                                            <div class="row">
                                                <div class="col-md-12">

                                                    <div class="form-group @if($errors->first('title')) has-error @endif form-md-line-input">
                                                        @php if(isset($blogCategory_highLight->varTitle) && ($blogCategory_highLight->varTitle != $blogCategory->varTitle)){
                                                        $Class_title = " highlitetext";
                                                        }else{
                                                        $Class_title = "";
                                                        } @endphp
                                                        <label class="form_title {!! $Class_title !!}" for="site_name">{{ trans('blogcategory::template.common.name') }} <span aria-required="true" class="required"> * </span></label>
                                                        {!! Form::text('title', isset($blogCategory->varTitle)?$blogCategory->varTitle:old('title'), array('maxlength' => 150,'id'=>'title', 'class' => 'form-control hasAlias seoField maxlength-handler titlespellingcheck','autocomplete'=>'off','data-url' => 'powerpanel/blog-category')) !!}
                                                        <span class="help-block">
                                                            {{ $errors->first('title') }}
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                            <!-- code for alias -->
                                            {!! Form::hidden(null, null, array('class' => 'hasAlias','data-url' => 'powerpanel/blog-category')) !!}
                                            {!! Form::hidden('alias', isset($blogCategory->alias->varAlias)?$blogCategory->alias->varAlias:old('alias'), array('class' => 'aliasField')) !!}
                                            {!! Form::hidden('oldAlias', isset($blogCategory->alias->varAlias)?$blogCategory->alias->varAlias:old('alias')) !!}
                                            {!! Form::hidden('fkMainRecord', isset($blogCategory->fkMainRecord)?$blogCategory->fkMainRecord:old('fkMainRecord')) !!}
                                            {!! Form::hidden('previewId') !!}
                                            <div class="form-group alias-group {{!isset($blogCategory)?'hide':''}} ">
                                                <label class="form_title" for="{{ trans('template.url') }}">{{ trans('blogcategory::template.common.url') }} :</label>
                                                @if(isset($blogCategory->alias->varAlias) && !$userIsAdmin)
                                                    <a class="alias">{!! url("/") !!}</a>
                                                    @else
                                                        @if(auth()->user()->can('blog-category-create'))
                                                        <a href="javascript:void;" class="alias">{!! url("/") !!}</a>
                                                        <a href="javascript:void(0);" class="editAlias" title="{{ trans('blogcategory::template.common.edit') }}">
                                                            <i class="fa fa-edit"></i>
                                                        </a>
                                                        <a class="without_bg_icon openLink" title="Open Link" onClick="generatePreview('{{ url('/previewpage?url='.(App\Helpers\MyLibrary::getFrontUri('blog-category')['uri'])) }}');">
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
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="form-group @if($errors->first('description')) has-error @endif">
                                                    @if (Config::get('Constant.DEFAULT_VISUAL') == 'Y')
                                                        <div id="body-roll">											
                                                            @php
                                                                $sections = [];
                                                            @endphp
                                                            @if(isset($blogCategory))
                                                                @php
                                                                    $sections = json_decode($blogCategory->txtDescription);
                                                                @endphp
                                                            @endif
                                                            <!-- Builder include -->
                                                            @php
                                                                Powerpanel\VisualComposer\Controllers\VisualComposerController::page_section(['sections'=>$sections])
                                                            @endphp
                                                        </div>
                                                    @else
                                                        @php if(isset($blogCategory_highLight->txtDescription) && ($blogCategory_highLight->txtDescription != $blogCategory->txtDescription)){
                                                        $Class_Description = " highlitetext";
                                                        }else{
                                                        $Class_Description = "";
                                                        } @endphp
                                                        <label class="form_title {!! $Class_Description !!}">{{ trans('blogcategory::template.common.description') }}</label>
                                                        {!! Form::textarea('description', isset($blogCategory->txtDescription)?$blogCategory->txtDescription:old('description'), array('placeholder' => trans('blogcategory::template.common.description'),'class' => 'form-control','id'=>'txtDescription')) !!}
                                                    @endif
                                                    <span class="help-block">{{ $errors->first('description') }}</span>
                                                </div>
                                            </div>
                                        </div>
                                        @if(Config::get('Constant.CHRSearchRank') == 'Y')
                                        @if(isset($blogCategory->intSearchRank))
                                        @php $srank = $blogCategory->intSearchRank; @endphp
                                        @else
                                        @php
                                        $srank = null !== old('search_rank') ? old('search_rank') : 2 ;
                                        @endphp
                                        @endif
                                        @if(isset($blogCategory_highLight->intSearchRank) && ($blogCategory_highLight->intSearchRank != $blogCategory->intSearchRank))
                                        @php $Class_intSearchRank = " highlitetext"; @endphp
                                        @else
                                        @php $Class_intSearchRank = ""; @endphp
                                        @endif
                                        <div class="row">
                                            <div class="col-md-12">
                                                <label class="{{ $Class_intSearchRank }} form_title">Search Ranking</label>
                                                <a href="javascript:;" data-toggle="tooltip" class="config" data-placement="bottom" data-original-title="{{ trans('blogcategory::template.common.SearchEntityTools') }}" title="{{ trans('blogcategory::template.common.SearchEntityTools') }}"><i class="fa fa-question"></i></a>
                                                <div class="wrapper search_rank">
                                                    <label for="yes_radio" id="yes-lbl">High</label><input type="radio" value="1" name="search_rank" @if($srank == 1) checked @endif id="yes_radio">
                                                                                                           <label for="maybe_radio" id="maybe-lbl">Medium</label><input type="radio" value="2" name="search_rank" @if($srank == 2) checked @endif id="maybe_radio">
                                                                                                           <label for="no_radio" id="no-lbl">Low</label><input type="radio" value="3" name="search_rank" @if($srank == 3) checked @endif id="no_radio">
                                                                                                           <div class="toggle"></div>
                                                </div>
                                            </div>
                                        </div>
                                         @endif
                                        <h3 class="form-section">{{ trans('blogcategory::template.common.ContentScheduling') }}</h3>
                                        @php $defaultDt = (null !== old('start_date_time'))?old('start_date_time'):date('Y-m-d H:i'); @endphp
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group form-md-line-input">
                                                    @php if(isset($blogCategory_highLight->dtDateTime) && ($blogCategory_highLight->dtDateTime != $blogCategory->dtDateTime)){
                                                    $Class_date = " highlitetext";
                                                    }else{
                                                    $Class_date = "";
                                                    } @endphp
                                                    <label class="control-label form_title {!! $Class_date !!}">{{ trans('blogcategory::template.common.startDateAndTime') }}<span aria-required="true" class="required"> * </span></label>
                                                    <div class="input-group date form_meridian_datetime @if($errors->first('start_date_time')) has-error @endif" data-date="{{ Carbon\Carbon::today()->format('Y-m-d') }}T15:25:00Z">
                                                        <span class="input-group-btn date_default">
                                                            <button class="btn date-set fromButton" type="button">
                                                                <i class="fa fa-calendar"></i>
                                                            </button>
                                                        </span>
                                                        {!! Form::text('start_date_time', date('Y-m-d H:i',strtotime(isset($blogCategory->dtDateTime)?$blogCategory->dtDateTime:$defaultDt)), array('class' => 'form-control','maxlength'=>160,'size'=>'16','id'=>'start_date_time','autocomplete'=>'off','onkeypress'=>"javascript: return KeycheckOnlyDate(event);",'onpaste'=>'return false')) !!}
                                                    </div>
                                                    <span class="help-block">
                                                        {{ $errors->first('start_date_time') }}
                                                    </span>
                                                </div>
                                            </div>
                                            @php $defaultDt = (null !== old('end_date_time'))?old('end_date_time'):null; @endphp
                                            @if ((isset($blogCategory->dtEndDateTime)==null))
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
                                                         @php if(isset($blogCategory_highLight->varTitle) && ($blogCategory_highLight->dtEndDateTime != $blogCategory->dtEndDateTime)){
                                                         $Class_end_date = " highlitetext";
                                                         }else{
                                                         $Class_end_date = "";
                                                         } @endphp
                                                         <label class="control-label form_title {!! $Class_end_date !!}" >{{ trans('blogcategory::template.common.endDateAndTime') }} <span aria-required="true" class="required"> * </span></label>
                                                        <div class="pos_cal">
                                                            <span class="input-group-btn date_default">
                                                                <button class="btn date-set toButton" type="button">
                                                                    <i class="fa fa-calendar"></i>
                                                                </button>
                                                            </span>
                                                            {!! Form::text('end_date_time', isset($blogCategory->dtEndDateTime)?date('Y-m-d H:i',strtotime($blogCategory->dtEndDateTime)):$defaultDt, array('class' => 'form-control','maxlength'=>160,'size'=>'16','id'=>'end_date_time','data-exp'=> $expChecked_yes,'data-newvalue','autocomplete'=>'off','onkeypress'=>"javascript: return KeycheckOnlyDate(event);",'onpaste'=>'return false')) !!}
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
                                                <div class="nopadding">
                                                    @include('powerpanel.partials.seoInfo',['form'=>'frmBlogCategory','inf'=>isset($metaInfo)?$metaInfo:false,'inf_highLight'=> isset($metaInfo_highLight)?$metaInfo_highLight:false])
                                                </div>
                                            </div>
                                        </div>
                                        <h3 class="form-section">{{ trans('blogcategory::template.common.displayinformation') }}</h3>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group @if($errors->first('order')) has-error @endif form-md-line-input">
                                                    @php
                                                    $display_order_attributes = array('class' => 'form-control','maxlength'=>5,'placeholder'=>trans('blogcategory::template.common.displayorder'),'autocomplete'=>'off');
                                                    @endphp
                                                    @if(isset($blogCategory_highLight->intDisplayOrder) && ($blogCategory_highLight->intDisplayOrder != $blogCategory->intDisplayOrder))
                                                    @php $Class_intDisplayOrder = " highlitetext"; @endphp
                                                    @else
                                                    @php $Class_intDisplayOrder = ""; @endphp
                                                    @endif
                                                    <label class="form_title {{ $Class_intDisplayOrder }}" for="site_name">{{ trans('blogcategory::template.common.displayorder') }} <span aria-required="true" class="required"> * </span></label>
                                                    {!! Form::text('order', isset($blogCategory->intDisplayOrder)?$blogCategory->intDisplayOrder:'1', $display_order_attributes) !!}
                                                    <span style="color: red;">
                                                        {{ $errors->first('order') }}
                                                    </span>
                                                </div>
                                            </div>
                                            @if($hasRecords==0)
                                            <div class="col-md-6">
                                                @if(isset($blogCategory_highLight->chrPublish) && ($blogCategory_highLight->chrPublish != $blogCategory->chrPublish))
                                                @php $Class_chrPublish = " highlitetext"; @endphp
                                                @else
                                                @php $Class_chrPublish = ""; @endphp
                                                @endif
                                                @if((isset($blogCategory) && $blogCategory->chrDraft == 'D'))
                                                @include('powerpanel.partials.displayInfo',['Class_chrPublish'=>$Class_chrPublish,'display' => (isset($blogCategory->chrDraft)?$blogCategory->chrDraft:'D')])
                                                @else
                                                @include('powerpanel.partials.displayInfo',['Class_chrPublish'=>$Class_chrPublish,'display' => (isset($blogCategory->chrPublish)?$blogCategory->chrPublish:'Y')])
                                                @endif
                                            </div>
                                            @else
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label class="control-label form_title"> Publish/ Unpublish</label>
                                                    @if($hasRecords > 0)
                                                    <input type="hidden" id="chrMenuDisplay" name="chrMenuDisplay" value="{{ $blogCategory->chrPublish }}">
                                                    <p><b>NOTE:</b> This category is selected in {{ trans("template.sidebar.blogs") }}, so it can&#39;t be published/unpublished.</p>
                                                    @endif
                                                </div>
                                            </div>
                                            @endif
                                            
                                        </div>
                                        <div class="form-actions">
                                            <div class="row">
                                                <div class="col-md-12">
                                                    @if(isset($blogCategory->fkMainRecord) && $blogCategory->fkMainRecord != 0)
                                                    <button type="submit" name="saveandexit" class="btn btn-green-drake" value="saveandexit">{!! trans('blogcategory::template.common.approve') !!}</button>
                                                    @else
                                                    @if($userIsAdmin)
                                                    <button type="submit" name="saveandedit" class="btn btn-green-drake" value="saveandedit">{!! trans('blogcategory::template.common.saveandedit') !!}</button>
                                                    <button type="submit" name="saveandexit" class="btn btn-green-drake" value="saveandexit">{!! trans('blogcategory::template.common.saveandexit') !!}</button>
                                                    @else
                                                    @if((isset($chrNeedAddPermission) && $chrNeedAddPermission == 'N') && (isset($charNeedApproval) && $charNeedApproval == 'N'))
                                                    <button type="submit" name="saveandexit" class="btn btn-green-drake" value="saveandexit">{!! trans('blogcategory::template.common.saveandexit') !!}</button>
                                                    @else
                                                    <button type="submit" name="saveandexit" class="btn btn-green-drake" value="approvesaveandexit">{!! trans('blogcategory::template.common.approvesaveandexit') !!}</button>
                                                    @endif
                                                    @endif
                                                    @endif
                                                    <a class="btn red btn-outline" href="{{ url('powerpanel/blog-category') }}">{{ trans('blogcategory::template.common.cancel') }}</a>
                                                    @if(isset($blogCategory) && $userIsAdmin)
                                                    &nbsp;<a class="btn btn-green-drake" title="Preview" onClick="generatePreview('{{url('/previewpage?url='.(App\Helpers\MyLibrary::getFrontUri('blog-category')['uri']))}}');">Preview</a>
                                                    @endif
                                                </div>
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
</div>
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
            var seoFormId = 'frmBlogCategory';
            var user_action = "{{ isset($blogCategory)?'edit':'add' }}";
            var moduleAlias = "{{ App\Helpers\MyLibrary::getFrontUri('blog-category')['moduleAlias'] }}";
            var preview_add_route = '{!! route("powerpanel.blog-category.addpreview") !!}';
            var previewForm = $('#frmBlogCategory');
            var isDetailPage = false;
            function generate_seocontent1(formname) {
            var Meta_Title = document.getElementById('title').value + "";
                    var abcd = $('textarea#txtDescription').val();
                    var def = abcd.replace(/<a(\s[^>]*)?>.*?<\/a>/ig, "")
                    var abc = def.replace(/^(\s*)|(\s*)$/g, '').replace(/\s+/g, ' ');
                    var outString1 = abc.replace(/(<([^>]+)>)/ig, "");
                    var Meta_Description = outString1.substr(0, 200);
                    var Meta_Keyword = "";
                    $('#varMetaTitle').val(Meta_Title);
//                    $('#varMetaKeyword').val(Meta_Keyword);
                    $('#varMetaDescription').val(Meta_Description);
                    $('#meta_title').html(Meta_Title);
                    $('#meta_description').html(Meta_Description);
            }
</script>
<script src="{{ $CDN_PATH.'resources/global/plugins/bootstrap-fileinput/bootstrap-fileinput.js' }}" type="text/javascript"></script>

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
<script src="{{ $CDN_PATH.'resources/global/plugins/custom-alias/alias-generator.js' }}" type="text/javascript"></script>
<!-- BEGIN CORE PLUGINS -->
<script src="{{ $CDN_PATH.'resources/global/plugins/bootstrap/js/bootstrap.min.js' }}" type="text/javascript"></script>
<script src="{{ $CDN_PATH.'resources/global/plugins/bootstrap-hover-dropdown/bootstrap-hover-dropdown.min.js' }}" type="text/javascript"></script>
<script src="{{ $CDN_PATH.'resources/global/plugins/jquery-slimscroll/jquery.slimscroll.min.js' }}" type="text/javascript"></script>
<!-- END CORE PLUGINS -->
<!-- BEGIN PAGE LEVEL SCRIPTS -->
<script src="{{ $CDN_PATH.'resources/global/plugins/bootstrap-maxlength/bootstrap-maxlength.min.js' }}" type="text/javascript"></script>
<script src="{{ $CDN_PATH.'resources/global/plugins/seo-generator/seo-info-generator.js' }}" type="text/javascript"></script>
<script src="{{ $CDN_PATH.'resources/pages/scripts/packages/blogcategory/blog_category_validations.js' }}" type="text/javascript"></script>
<!-- END PAGE LEVEL SCRIPTS -->
@if (Config::get('Constant.DEFAULT_VISUAL') == 'Y')
    @php Powerpanel\VisualComposer\Controllers\VisualComposerController::get_builder_css_js(); @endphp
@endif
@endsection