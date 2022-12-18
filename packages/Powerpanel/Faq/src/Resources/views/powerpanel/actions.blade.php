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
                                    {!! Form::open(['method' => 'post','id'=>'frmFaq']) !!}
                                     <div class="form-body">
                                    {!! Form::hidden('fkMainRecord', isset($faq->fkMainRecord)?$faq->fkMainRecord:old('fkMainRecord')) !!}
                                    @if(isset($faq))
                                    @if (File::exists(base_path() . '/resources/views/powerpanel/partials/lockedpage.blade.php') != null)
                                    @include('powerpanel.partials.lockedpage',['pagedata'=>$faq])
                                    @endif
                                    @endif
                                    <!-- <div class="form-group @if($errors->first('tag_line')) has-error @endif form-md-line-input" style="display:none;">
                                        @php
                                        if(isset($faq_highLight->intFKCategory) && ($faq_highLight->intFKCategory != $faq->intFKCategory)){
                                        $Class_title = " highlitetext";
                                        }else{
                                        $Class_title = "";
                                        }
                                        $currentCatAlias = '';
                                        @endphp
                                        <label class="form_title {{ $Class_title }}" for="site_name">Select Category <span aria-required="true" class="required"> * </span></label>
                                        <select class="form-control bs-select select2" name="category_id">
                                            <option value=" ">-- Select Category --</option>
                                            @foreach ($faqCategory as $cat)
                                            @php $permissionName = 'faq-list' @endphp
                                            @php $selected = ''; @endphp
                                            @if(isset($faq->intFKCategory))
                                            @if($cat['id'] == $faq->intFKCategory)
                                            @php $selected = 'selected'; $currentCatAlias = $cat['alias']['varAlias'];  @endphp
                                            @endif
                                            @endif
                                            <option value="{{ $cat['id'] }}" data-categryalias="{{ $cat['alias']['varAlias'] }}" {{ $selected }} >{{ $cat['varModuleName']== "faq"?'Select Category':$cat['varTitle'] }}</option>
                                            @endforeach
                                        </select>
                                        <span class="help-block">
                                            {{ $errors->first('category') }}
                                        </span>
                                    </div> -->
                                    <div class="form-group @if($errors->first('title')) has-error @endif form-md-line-input">
                                        @php if(isset($faq_highLight->varTitle) && ($faq_highLight->varTitle != $faq->varTitle)){
                                        $Class_title = " highlitetext";
                                        }else{
                                        $Class_title = "";
                                        } @endphp
                                        <label class="form_title {!! $Class_title !!}" for="site_name">{{ trans('faq::template.common.question') }} <span aria-required="true" class="required"> * </span></label>
                                        {!! Form::text('title', isset($faq->varTitle) ? $faq->varTitle:old('title'), array('maxlength'=>'250','id'=>'title','placeholder' => trans('faq::template.common.question'),'class' => 'form-control seoField maxlength-handler titlespellingcheck','autocomplete'=>'off')) !!}
                                        <span class="help-block">
                                            {{ $errors->first('title') }}
                                        </span>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group @if($errors->first('description')) has-error @endif form-md-line-input">
                                                @php if(isset($faq_highLight->txtDescription) && ($faq_highLight->txtDescription != $faq->txtDescription)){
                                                $Class_Description = " highlitetext";
                                                }else{
                                                $Class_Description = "";
                                                } @endphp
                                                <label class="form_title {!! $Class_Description !!}">{{ trans('faq::template.common.answer') }} <span aria-required="true" class="required"> * </span></label>
                                                {!! Form::textarea('description', isset($faq->txtDescription)?$faq->txtDescription:old('description'), array('placeholder' => trans('faq::template.common.answer'),'class' => 'form-control','id'=>'txtDescription')) !!}
                                                <span class="help-block">{{ $errors->first('description') }}</span>
                                            </div>
                                        </div>
                                    </div>
                                    @if(Config::get('Constant.CHRSearchRank') == 'Y')
                                    @if(isset($faq->intSearchRank))
                                    @php $srank = $faq->intSearchRank; @endphp
                                    @else
                                    @php
                                    $srank = null !== old('search_rank') ? old('search_rank') : 2 ;
                                    @endphp
                                    @endif
                                    @if(isset($faq_highLight->intSearchRank) && ($faq_highLight->intSearchRank != $faq->intSearchRank))
                                    @php $Class_intSearchRank = " highlitetext"; @endphp
                                    @else
                                    @php $Class_intSearchRank = ""; @endphp
                                    @endif
                                    <div class="row" style="display:none;">
                                        <div class="col-md-12">
                                            <label class="{{ $Class_intSearchRank }} form_title">Search Ranking</label>
                                            <a href="javascript:;" data-toggle="tooltip" class="config" data-placement="bottom" data-original-title="{{ trans('faq::template.common.SearchEntityTools') }}" title="{{ trans('template.common.SearchEntityTools') }}"><i class="fa fa-question"></i></a>
                                            <div class="wrapper search_rank">
                                                <label for="yes_radio" id="yes-lbl">High</label><input type="radio" value="1" name="search_rank" @if($srank == 1) checked @endif id="yes_radio">
                                                                                                       <label for="maybe_radio" id="maybe-lbl">Medium</label><input type="radio" value="2" name="search_rank" @if($srank == 2) checked @endif id="maybe_radio">
                                                                                                       <label for="no_radio" id="no-lbl">Low</label><input type="radio" value="3" name="search_rank" @if($srank == 3) checked @endif id="no_radio">
                                                                                                       <div class="toggle"></div>
                                            </div>
                                        </div>
                                    </div>
                                    @endif
                                    
                                    <h3 style="display:none;" class="form-section">{{ trans('faq::template.common.ContentScheduling') }}</h3>
                                    @php $defaultDt = (null !== old('start_date_time'))?old('start_date_time'):date('Y-m-d H:i'); @endphp
                                    <div class="row" style="display:none;">
                                        <div class="col-md-6">
                                            <div class="form-group form-md-line-input">
                                                @php if(isset($faq_highLight->dtDateTime) && ($faq_highLight->dtDateTime != $faq->dtDateTime)){
                                                $Class_date = " highlitetext";
                                                }else{
                                                $Class_date = "";
                                                } @endphp
                                                <label class="control-label form_title {!! $Class_date !!}">{{ trans('faq::template.common.startDateAndTime') }}<span aria-required="true" class="required"> * </span></label>
                                                <div class="input-group date form_meridian_datetime @if($errors->first('start_date_time')) has-error @endif" data-date="{{ Carbon\Carbon::today()->format('Y-m-d') }}T15:25:00Z">
                                                    <span class="input-group-btn date_default">
                                                        <button class="btn date-set fromButton" type="button">
                                                            <i class="fa fa-calendar"></i>
                                                        </button>
                                                    </span>
                                                    {!! Form::text('start_date_time', date('Y-m-d H:i',strtotime(isset($faq->dtDateTime)?$faq->dtDateTime:$defaultDt)), array('class' => 'form-control','maxlength'=>160,'size'=>'16','id'=>'start_date_time','autocomplete'=>'off','onkeypress'=>"javascript: return KeycheckOnlyDate(event);",'onpaste'=>'return false')) !!}
                                                </div>
                                                <span class="help-block">
                                                    {{ $errors->first('start_date_time') }}
                                                </span>
                                            </div>
                                        </div>
                                        @php $defaultDt = (null !== old('end_date_time'))?old('end_date_time'):null; @endphp
                                        @if ((isset($faq->dtEndDateTime)==null))
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
                                                     @php if(isset($faq_highLight->varTitle) && ($faq_highLight->dtEndDateTime != $faq->dtEndDateTime)){
                                                     $Class_end_date = " highlitetext";
                                                     }else{
                                                     $Class_end_date = "";
                                                     } @endphp
                                                     <label class="control-label form_title {!! $Class_end_date !!}" >{{ trans('faq::template.common.endDateAndTime') }} <span aria-required="true" class="required"> * </span></label>
                                                    <div class="pos_cal">
                                                        <span class="input-group-btn date_default">
                                                            <button class="btn date-set toButton" type="button">
                                                                <i class="fa fa-calendar"></i>
                                                            </button>
                                                        </span>
                                                        {!! Form::text('end_date_time', isset($faq->dtEndDateTime)?date('Y-m-d H:i',strtotime($faq->dtEndDateTime)):$defaultDt, array('class' => 'form-control','maxlength'=>160,'size'=>'16','id'=>'end_date_time','data-exp'=> $expChecked_yes,'data-newvalue','autocomplete'=>'off','onkeypress'=>"javascript: return KeycheckOnlyDate(event);",'onpaste'=>'return false')) !!}
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
                                   
                                    <h3 class="form-section">{{ trans('faq::template.common.displayinformation') }}</h3>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group @if($errors->first('order')) has-error @endif form-md-line-input">
                                                @php
                                                $display_order_attributes = array('class' => 'form-control','maxlength'=>5,'placeholder'=>trans('faq::template.common.displayorder'),'autocomplete'=>'off');
                                                @endphp
                                                @if(isset($faq_highLight->intDisplayOrder) && ($faq_highLight->intDisplayOrder != $faq->intDisplayOrder))
                                                @php $Class_intDisplayOrder = " highlitetext"; @endphp
                                                @else
                                                @php $Class_intDisplayOrder = ""; @endphp
                                                @endif
                                                <label class="form_title {{ $Class_intDisplayOrder }}" for="site_name">{{ trans('faq::template.common.displayorder') }} <span aria-required="true" class="required"> * </span></label>
                                                {!! Form::text('order', isset($faq->intDisplayOrder)?$faq->intDisplayOrder:1, $display_order_attributes) !!}
                                                <span style="color: red;">
                                                    {{ $errors->first('order') }}
                                                </span>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            @if(isset($faq_highLight->chrPublish) && ($faq_highLight->chrPublish != $faq->chrPublish))
                                            @php $Class_chrPublish = " highlitetext"; @endphp
                                            @else
                                            @php $Class_chrPublish = ""; @endphp
                                            @endif
                                            @if((isset($faq) && $faq->chrDraft == 'D'))
                                            @include('powerpanel.partials.displayInfo',['Class_chrPublish'=>$Class_chrPublish,'display' => (isset($faq->chrDraft)?$faq->chrDraft:'D')])
                                            @else
                                            @include('powerpanel.partials.displayInfo',['Class_chrPublish'=>$Class_chrPublish,'display' => (isset($faq->chrPublish)?$faq->chrPublish:'Y')])
                                            @endif
                                            
                                        </div>
                                    </div>
                                    <div class="form-actions">
                                        <div class="row">
                                            <div class="col-md-12">
                                                @if(isset($faq->fkMainRecord) && $faq->fkMainRecord != 0)
                                                <button type="submit" name="saveandexit" class="btn btn-green-drake" value="saveandexit" title="{!! trans('faq::template.common.approve') !!}">{!! trans('faq::template.common.approve') !!}</button>
                                                @else
                                                @if($userIsAdmin)
                                                <button type="submit" name="saveandedit" class="btn btn-green-drake" value="saveandedit" title="{!! trans('faq::template.common.saveandedit') !!}">{!! trans('faq::template.common.saveandedit') !!}</button>
                                                <button type="submit" name="saveandexit" class="btn btn-green-drake" value="saveandexit" title="{!! trans('faq::template.common.saveandexit') !!}">{!! trans('faq::template.common.saveandexit') !!}</button>
                                                @else
                                                @if((isset($chrNeedAddPermission) && $chrNeedAddPermission == 'N') && (isset($charNeedApproval) && $charNeedApproval == 'N'))
                                                <button type="submit" name="saveandexit" class="btn btn-green-drake" value="saveandexit" title="{!! trans('faq::template.common.saveandexit') !!}">{!! trans('faq::template.common.saveandexit') !!}</button>
                                                @else
                                                <button type="submit" name="saveandexit" class="btn btn-green-drake" value="approvesaveandexit" title="{!! trans('faq::template.common.approvesaveandexit') !!}">{!! trans('faq::template.common.approvesaveandexit') !!}</button>
                                                @endif
                                                @endif
                                                @endif
                                                <a class="btn red btn-outline" href="{{ url('powerpanel/faq') }}" title="{{ trans('faq::template.common.cancel') }}">{{ trans('faq::template.common.cancel') }}</a>
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
<div class="clearfix"></div>
@endsection
@section('scripts')
<script type="text/javascript">
    window.site_url = '{!! url("/") !!}';
    var seoFormId = 'frmFaq';
    var user_action = "{{ isset($faq)?'edit':'add' }}";
    var moduleAlias = "{{ App\Helpers\MyLibrary::getFrontUri('faq-category')['moduleAlias'] }}";
    var preview_add_route = '{!! route("powerpanel.faq.addpreview") !!}';
    var previewForm = $('#frmFaq');
    var isDetailPage = true;
    function generate_seocontent1(formname) {
        var Meta_Title = document.getElementById('title').value + "";
        var abcd = $('textarea#txtDescription').val();
        var def = abcd.replace(/<a(\s[^>]*)?>.*?<\/a>/ig, "")
        var abc = def.replace(/^(\s*)|(\s*)$/g, '').replace(/\s+/g, ' ');
        var outString1 = abc.replace(/(<([^>]+)>)/ig, "");
        var Meta_Description = "" + document.getElementById('title').value;
        var Meta_Keyword = "";
        $('#varMetaTitle').val(Meta_Title);
        $('#varMetaKeyword').val(Meta_Keyword);
        $('#varMetaDescription').val(Meta_Description);
        $('#meta_title').html(Meta_Title);
        $('#meta_description').html(Meta_Description);
    }
</script>
<script src="{{ $CDN_PATH.'resources/pages/scripts/packages/faq/faq_validations.js' }}" type="text/javascript"></script>
@include('powerpanel.partials.ckeditor',['config'=>'docsConfig'])
<script src="{{ $CDN_PATH.'resources/pages/scripts/custom.js' }}" type="text/javascript"></script>
<script src="{{ $CDN_PATH.'resources/global/plugins/bootstrap-maxlength/bootstrap-maxlength.min.js' }}" type="text/javascript"></script>
<script src="{{ $CDN_PATH.'resources/global/plugins/seo-generator/seo-info-generator.js' }}" type="text/javascript"></script>

@endsection