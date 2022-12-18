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
                                        {!! Form::open(['method' => 'post','id'=>'frmFaqCategory']) !!}
                                        <div class="form-body">
                                             @if(isset($faqCategory))
                                            @if (File::exists(base_path() . '/resources/views/powerpanel/partials/lockedpage.blade.php') != null)
                                            @include('powerpanel.partials.lockedpage',['pagedata'=>$faqCategory])
                                            @endif
                                            @endif
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <div class="form-group @if($errors->first('title')) has-error @endif form-md-line-input">
                                                        @php if(isset($faqCategory_highLight->varTitle) && ($faqCategory_highLight->varTitle != $faqCategory->varTitle)){
                                                        $Class_title = " highlitetext";
                                                        }else{
                                                        $Class_title = "";
                                                        } @endphp
                                                        <label class="form_title {!! $Class_title !!}" for="site_name">{{ trans('faq-category::template.common.name') }} <span aria-required="true" class="required"> * </span></label>
                                                        {!! Form::text('title', isset($faqCategory->varTitle)?$faqCategory->varTitle:old('title'), array('maxlength' => 150,'id'=>'title', 'class' => 'form-control hasAlias seoField maxlength-handler titlespellingcheck','autocomplete'=>'off','data-url' => 'powerpanel/faq-category')) !!}
                                                        <span class="help-block">
                                                            {{ $errors->first('title') }}
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                            <!-- code for alias -->
                                            {!! Form::hidden(null, null, array('class' => 'hasAlias','data-url' => 'powerpanel/faq-category')) !!}
                                            {!! Form::hidden('alias', isset($faqCategory->alias->varAlias)?$faqCategory->alias->varAlias:old('alias'), array('class' => 'aliasField')) !!}
                                            {!! Form::hidden('oldAlias', isset($faqCategory->alias->varAlias)?$faqCategory->alias->varAlias:old('alias')) !!}
                                            {!! Form::hidden('fkMainRecord', isset($faqCategory->fkMainRecord)?$faqCategory->fkMainRecord:old('fkMainRecord')) !!}
                                            {!! Form::hidden('previewId') !!}
                                            <div class="form-group alias-group {{!isset($faqCategory)?'hide':''}} ">
                                                <label class="form_title" for="{{ trans('template.url') }}">{{ trans('faq-category::template.common.url') }} :</label>
                                                @if(isset($faqCategory->alias->varAlias) && !$userIsAdmin)
                                                @if(isset($faqCategory->alias->varAlias))
                                                @php
                                                $aurl = App\Helpers\MyLibrary::getFrontUri('faq-category')['uri'];
                                                @endphp
                                                {{url($aurl.'/'.$faqCategory->alias->varAlias)}}
                                                @endif
                                                @else
                                                @if(auth()->user()->can('faq-category-create'))
                                                <a href="javascript:void;" class="alias">{!! url("/") !!}</a>
                                                <a href="javascript:void(0);" class="editAlias" title="{{ trans('faq-category::template.common.edit') }}">
                                                    <i class="fa fa-edit"></i>
                                                </a>
                                                <a class="without_bg_icon openLink" title="Open Link" onClick="generatePreview('{{url('/previewpage?url='.(App\Helpers\MyLibrary::getFrontUri('faq-category')['uri']))}}');">
                                                    <i class="fa fa-external-link" aria-hidden="true"></i>
                                                </a>
                                                @endif
                                                @endif
                                            </div>
                                            <span class="help-block">
                                                {{ $errors->first('alias') }}
                                            </span>
                                            <!-- code for alias -->
                                        
                                        <div class="row">
                                            <div class="col-md-12">
                                                 @if (Config::get('Constant.DEFAULT_VISUAL') == 'Y')

                                                <div id="body-roll">											
                                                    @php
                                                    $sections = [];
                                                    @endphp
                                                    @if(isset($faqCategory))
                                                    @php
                                                    $sections = json_decode($faqCategory->txtDescription);
                                                    @endphp
                                                    @endif
                                                    <!-- Builder include -->
                                                   @php Powerpanel\VisualComposer\Controllers\VisualComposerController::page_section(['sections'=>$sections])@endphp
                                                    {{--@include('visualcomposer::page-sections',['sections'=>$sections])--}}
                                                    <!--{{-- @include('powerpanel.partials.page-sections',['sections'=>$sections]) --}}-->
                                                </div>
                                                @else
                                                <div class="form-group @if($errors->first('description')) has-error @endif">
                                                    @php if(isset($faqCategory_highLight->txtDescription) && ($faqCategory_highLight->txtDescription != $faqCategory->txtDescription)){
                                                    $Class_Description = " highlitetext";
                                                    }else{
                                                    $Class_Description = "";
                                                    } @endphp
                                                    <label class="form_title {!! $Class_Description !!}">{{ trans('faq-category::template.common.description') }}</label>
                                                    {!! Form::textarea('description', isset($faqCategory->txtDescription)?$faqCategory->txtDescription:old('description'), array('placeholder' => trans('faq-category::template.common.description'),'class' => 'form-control','id'=>'txtDescription')) !!}
                                                    <span class="help-block">{{ $errors->first('description') }}</span>
                                                </div>
                                                @endif
                                            </div>
                                        </div>
                                        @if(Config::get('Constant.CHRSearchRank') == 'Y')    
                                        @if(isset($faqCategory->intSearchRank))
                                        @php $srank = $faqCategory->intSearchRank; @endphp
                                        @else
                                        @php
                                        $srank = null !== old('search_rank') ? old('search_rank') : 2 ;
                                        @endphp
                                        @endif
                                        @if(isset($faqCategory_highLight->intSearchRank) && ($faqCategory_highLight->intSearchRank != $faqCategory->intSearchRank))
                                        @php $Class_intSearchRank = " highlitetext"; @endphp
                                        @else
                                        @php $Class_intSearchRank = ""; @endphp
                                        @endif
                                        <div class="row">
                                            <div class="col-md-12">
                                                <label class="{{ $Class_intSearchRank }} form_title">Search Ranking</label>
                                                <a href="javascript:;" data-toggle="tooltip" class="config" data-placement="bottom" data-original-title="{{ trans('faq-category::template.common.SearchEntityTools') }}" title="{{ trans('faq-category::template.common.SearchEntityTools') }}"><i class="fa fa-question"></i></a>
                                                <div class="wrapper search_rank">
                                                    <label for="yes_radio" id="yes-lbl">High</label><input type="radio" value="1" name="search_rank" @if($srank == 1) checked @endif id="yes_radio">
                                                                                                           <label for="maybe_radio" id="maybe-lbl">Medium</label><input type="radio" value="2" name="search_rank" @if($srank == 2) checked @endif id="maybe_radio">
                                                                                                           <label for="no_radio" id="no-lbl">Low</label><input type="radio" value="3" name="search_rank" @if($srank == 3) checked @endif id="no_radio">
                                                                                                           <div class="toggle"></div>
                                                </div>
                                            </div>
                                        </div>
                                        @endif
                                        <h3 class="form-section">{{ trans('faq-category::template.common.ContentScheduling') }}</h3>
                                        @php $defaultDt = (null !== old('start_date_time'))?old('start_date_time'):date('Y-m-d H:i'); @endphp
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group form-md-line-input">
                                                    @php if(isset($faqCategory_highLight->dtDateTime) && ($faqCategory_highLight->dtDateTime != $faqCategory->dtDateTime)){
                                                    $Class_date = " highlitetext";
                                                    }else{
                                                    $Class_date = "";
                                                    } @endphp
                                                    <label class="control-label form_title {!! $Class_date !!}">{{ trans('faq-category::template.common.startDateAndTime') }}<span aria-required="true" class="required"> * </span></label>
                                                    <div class="input-group date form_meridian_datetime @if($errors->first('start_date_time')) has-error @endif" data-date="{{ Carbon\Carbon::today()->format('Y-m-d') }}T15:25:00Z">
                                                        <span class="input-group-btn date_default">
                                                            <button class="btn date-set fromButton" type="button">
                                                                <i class="fa fa-calendar"></i>
                                                            </button>
                                                        </span>
                                                        {!! Form::text('start_date_time', date('Y-m-d H:i',strtotime(isset($faqCategory->dtDateTime)?$faqCategory->dtDateTime:$defaultDt)), array('class' => 'form-control','maxlength'=>160,'size'=>'16','id'=>'start_date_time','autocomplete'=>'off','onkeypress'=>"javascript: return KeycheckOnlyDate(event);",'onpaste'=>'return false')) !!}
                                                    </div>
                                                    <span class="help-block">
                                                        {{ $errors->first('start_date_time') }}
                                                    </span>
                                                </div>
                                            </div>
                                            @php $defaultDt = (null !== old('end_date_time'))?old('end_date_time'):null; @endphp
                                            @if ((isset($faqCategory->dtEndDateTime)==null))
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
                                                         @php if(isset($faqCategory_highLight->varTitle) && ($faqCategory_highLight->dtEndDateTime != $faqCategory->dtEndDateTime)){
                                                         $Class_end_date = " highlitetext";
                                                         }else{
                                                         $Class_end_date = "";
                                                         } @endphp
                                                         <label class="control-label form_title {!! $Class_end_date !!}" >{{ trans('faq-category::template.common.endDateAndTime') }} <span aria-required="true" class="required"> * </span></label>
                                                        <div class="pos_cal">
                                                            <span class="input-group-btn date_default">
                                                                <button class="btn date-set toButton" type="button">
                                                                    <i class="fa fa-calendar"></i>
                                                                </button>
                                                            </span>
                                                            {!! Form::text('end_date_time', isset($faqCategory->dtEndDateTime)?date('Y-m-d H:i',strtotime($faqCategory->dtEndDateTime)):$defaultDt, array('class' => 'form-control','maxlength'=>160,'size'=>'16','id'=>'end_date_time','data-exp'=> $expChecked_yes,'data-newvalue','autocomplete'=>'off','onkeypress'=>"javascript: return KeycheckOnlyDate(event);",'onpaste'=>'return false')) !!}
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
                                                    @include('powerpanel.partials.seoInfo',['form'=>'frmFaqCategory','inf'=>isset($metaInfo)?$metaInfo:false,'inf_highLight'=> isset($metaInfo_highLight)?$metaInfo_highLight:false])
                                                </div>
                                            </div>
                                        </div>
                                        <h3 class="form-section">{{ trans('faq-category::template.common.displayinformation') }}</h3>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group @if($errors->first('order')) has-error @endif form-md-line-input">
                                                    @php
                                                    $display_order_attributes = array('class' => 'form-control','maxlength'=>5,'placeholder'=>trans('faq-category::template.common.displayorder'),'autocomplete'=>'off');
                                                    @endphp
                                                    @if(isset($faqCategory_highLight->intDisplayOrder) && ($faqCategory_highLight->intDisplayOrder != $faqCategory->intDisplayOrder))
                                                    @php $Class_intDisplayOrder = " highlitetext"; @endphp
                                                    @else
                                                    @php $Class_intDisplayOrder = ""; @endphp
                                                    @endif
                                                    <label class="form_title {{ $Class_intDisplayOrder }}" for="site_name">{{ trans('faq-category::template.common.displayorder') }} <span aria-required="true" class="required"> * </span></label>
                                                    {!! Form::text('order', isset($faqCategory->intDisplayOrder)?$faqCategory->intDisplayOrder:'1', $display_order_attributes) !!}
                                                    <span style="color: red;">
                                                        {{ $errors->first('order') }}
                                                    </span>
                                                </div>
                                            </div>
                                            @if($hasRecords==0)
                                            <div class="col-md-6">
                                            	@if(isset($faqCategory_highLight->chrPublish) && ($faqCategory_highLight->chrPublish != $faqCategory->chrPublish))
                                              @php $Class_chrPublish = " highlitetext"; @endphp
                                              @else
                                              @php $Class_chrPublish = ""; @endphp
                                              @endif
                                              @if((isset($faqCategory) && $faqCategory->chrDraft == 'D'))
                                              @include('powerpanel.partials.displayInfo',['Class_chrPublish'=>$Class_chrPublish,'display' => (isset($faqCategory->chrDraft)?$faqCategory->chrDraft:'D')])
                                              @else
                                              @include('powerpanel.partials.displayInfo',['Class_chrPublish'=>$Class_chrPublish,'display' => (isset($faqCategory->chrPublish)?$faqCategory->chrPublish:'Y')])
                                              @endif
                                                
                                            </div>
                                            @else
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label class="control-label form_title"> Publish/ Unpublish</label>
                                                    @if($hasRecords > 0)
                                                    <input type="hidden" id="chrMenuDisplay" name="chrMenuDisplay" value="{{ $faqCategory->chrPublish }}">
                                                    <p><b>NOTE:</b> This category is selected in {{ trans("faq-category::template.sidebar.faq") }}, so it can&#39;t be published/unpublished.</p>
                                                    @endif
                                                </div>
                                            </div>
                                            @endif
                                            
                                        </div>
                                        <div class="form-actions">
                                            <div class="row">
                                                <div class="col-md-12">
                                                    @if(isset($faqCategory->fkMainRecord) && $faqCategory->fkMainRecord != 0)
                                                    <button type="submit" name="saveandexit" class="btn btn-green-drake" value="saveandexit">{!! trans('faq-category::template.common.approve') !!}</button>
                                                    @else
                                                    @if($userIsAdmin)
                                                    <button type="submit" name="saveandedit" class="btn btn-green-drake" value="saveandedit">{!! trans('faq-category::template.common.saveandedit') !!}</button>
                                                    <button type="submit" name="saveandexit" class="btn btn-green-drake" value="saveandexit">{!! trans('faq-category::template.common.saveandexit') !!}</button>
                                                    @else
                                                    @if((isset($chrNeedAddPermission) && $chrNeedAddPermission == 'N') && (isset($charNeedApproval) && $charNeedApproval == 'N'))
                                                    <button type="submit" name="saveandexit" class="btn btn-green-drake" value="saveandexit">{!! trans('faq-category::template.common.saveandexit') !!}</button>
                                                    @else
                                                    <button type="submit" name="saveandexit" class="btn btn-green-drake" value="approvesaveandexit">{!! trans('faq-category::template.common.approvesaveandexit') !!}</button>
                                                    @endif
                                                    @endif
                                                    @endif
                                                    <a class="btn red btn-outline" href="{{ url('powerpanel/faq-category') }}">{{ trans('faq-category::template.common.cancel') }}</a>
                                                    @if(isset($faqCategory) && $userIsAdmin)
                                                    &nbsp;<a class="btn btn-green-drake" title="Preview" onClick="generatePreview('{{url('/previewpage?url='.(App\Helpers\MyLibrary::getFrontUri('faq-category')['uri']))}}');">Preview</a>
                                                    @endif
                                                </div>
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
        {{--@include('powerpanel.partials.dialog-maker') --}}
        @php Powerpanel\VisualComposer\Controllers\VisualComposerController::get_dialog_maker()@endphp
    @endif
    @if (Config::get('Constant.DEFAULT_VISUAL') == 'Y')
        @php Powerpanel\VisualComposer\Controllers\VisualComposerController::get_visual_checkEditor()@endphp
    @else
        @include('powerpanel.partials.ckeditor',['config'=>'docsConfig'])
    @endif
@endsection
@section('scripts')
<script type="text/javascript">
            window.site_url = '{!! url("/") !!}';
            var seoFormId = 'frmFaqCategory';
            var user_action = "{{ isset($faqCategory)?'edit':'add' }}";
            var moduleAlias = "{{ App\Helpers\MyLibrary::getFrontUri('faq-category')['moduleAlias'] }}";
            var preview_add_route = '{!! route("powerpanel.faq-category.addpreview") !!}';
            var previewForm = $('#frmFaqCategory');
            var isDetailPage = true;
            function generate_seocontent1(formname) {
            var Meta_Title = document.getElementById('title').value + "";
            var Meta_Description = document.getElementById('title').value + "";
                   
                    var Meta_Keyword = "";
                    $('#varMetaTitle').val(Meta_Title);
//								$('#varMetaKeyword').val(Meta_Keyword);
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
<script src="{{ $CDN_PATH.'resources/pages/scripts/packages/faqcategory/faq_category_validations.js' }}" type="text/javascript"></script>
<!-- END PAGE LEVEL SCRIPTS -->
 @if (Config::get('Constant.DEFAULT_VISUAL') == 'Y')
        @php Powerpanel\VisualComposer\Controllers\VisualComposerController::get_builder_css_js()@endphp
    @endif
@endsection