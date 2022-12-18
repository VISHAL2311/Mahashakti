@section('css')
<link href="{{ $CDN_PATH.'resources/global/css/rank-button.css' }}" rel="stylesheet" type="text/css" />
@endsection
@extends('powerpanel.layouts.app')
@section('title')
{{Config::get('Constant.SITE_NAME')}} - PowerPanel
@endsection
@section('content')
@include('powerpanel.partials.builder-css') <!-- Builder include -->
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
                                    {!! Form::open(['method' => 'post','id'=>'frmPageTemplate']) !!}
                                    <div class="form-body">
                                        
                                        <div class="form-group {{ $errors->has('title') ? ' has-error' : '' }} form-md-line-input">
                                            
                                            <label class="form_title" for="title">Template Name <span aria-required="true" class="required"> * </span></label>
                                            {!! Form::text('title', (isset($pageTemplate->varTemplateName)?$pageTemplate->varTemplateName:old('title')), array('maxlength'=>'150','class' => 'form-control input-sm hasAlias seoField maxlength-handler', 'data-url' => 'powerpanel/page_template','id' => 'title','placeholder' =>'Template Name','autocomplete'=>'off')) !!}
                                            <span style="color: red;">
                                                {{ $errors->first('title') }}
                                            </span>
                                        </div>
                                        <!-- code for alias -->
                                        {!! Form::hidden(null, null, array('class' => 'hasAlias','data-url' => 'powerpanel/page_template')) !!}
                                        {!! Form::hidden('alias', isset($pageTemplate->alias->varAlias)?$pageTemplate->alias->varAlias:old('alias'), array('class' => 'aliasField')) !!}
                                        {!! Form::hidden('oldAlias', isset($pageTemplate->alias->varAlias)?$pageTemplate->alias->varAlias:old('alias')) !!}
                                        {!! Form::hidden('fkMainRecord', isset($pageTemplate->fkMainRecord)?$pageTemplate->fkMainRecord:old('fkMainRecord')) !!}
                                        {!! Form::hidden('previewId') !!}
                                        <div class="form-group alias-group {{!isset($pageTemplate->alias)?'hide':''}}">
                                            <label class="form_title" for="title">Preview Link: </label>
	                                            <a class="without_bg_icon openLink" title="{{ trans('pagetemplates::template.common.openLink') }}" onClick="generatePreview('{{url('/previewpage?url='.(App\Helpers\MyLibrary::getFrontUri('page_template')['uri']))}}');"><i class="fa fa-external-link" aria-hidden="true"></i> Preview</a>
                                           
                                        </div>
                                        <!-- code for alias -->
                                        

                                        @if (Config::get('Constant.DEFAULT_VISUAL') == 'Y')
                                        <div id="body-roll">											
                                            @php
                                            $sections = [];
                                            @endphp
                                            @if(isset($pageTemplate))
                                            @php
                                            $sections = json_decode($pageTemplate->txtDesc);
                                            @endphp
                                            @endif
                                            <!-- Builder include -->
                                            @php Powerpanel\VisualComposer\Controllers\VisualComposerController::page_section(['sections'=>$sections])@endphp
                                        </div>
                                        @else
                                        @php if(isset($pageTemplate_highLight->txtDesc) && ($pageTemplate_highLight->txtDesc != $pageTemplate->txtDesc)){
                                        $Class_Description = " highlitetext";
                                        }else{
                                        $Class_Description = "";
                                        } @endphp
                                        <div  class="form-group {{ $errors->has('contents') ? ' has-error' : '' }}">
                                            <label for="default_page_size" class="form_title {!! $Class_Description !!}">{{ trans('pagetemplates::template.common.description') }}</label>
                                            {!! Form::textarea('contents',(isset($pageTemplate->txtDesc)?$pageTemplate->txtDesc:old('contents')) , array('class' => 'form-control cms','id'=>'txtDesc')) !!}
                                        </div>
                                        @endif

                                        
                                     
                                        <div class="{{ $errors->has('display') ? ' has-error' : '' }} ">
                                           
                                            @if(isset($publishActionDisplay))
                                            <div class="row">
                                                <div class="col-md-6">
                                                    @if(isset($pageTemplate_highLight->chrPublish) && ($pageTemplate_highLight->chrPublish != $pageTemplate->chrPublish))
                                                    @php $Class_chrPublish = " highlitetext"; @endphp
                                                    @else
                                                    @php $Class_chrPublish = ""; @endphp
                                                    @endif
                                                    @if((isset($pageTemplate) && $pageTemplate->chrDraft == 'D'))
                                                    @include('powerpanel.partials.displayInfo',['Class_chrPublish'=>$Class_chrPublish,'display' => (isset($pageTemplate->chrDraft)?$pageTemplate->chrDraft:'D')])
                                                    @else
                                                    @include('powerpanel.partials.displayInfo',['Class_chrPublish'=>$Class_chrPublish,'display' => (isset($pageTemplate->chrPublish)?$pageTemplate->chrPublish:'Y')])
                                                    @endif
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label class="form_title   " for="site_name">Accessible</label>
                                                         @if(isset($pageTemplate->chrDisplayStatus) && $pageTemplate->chrDisplayStatus == 'PU')
                                                    @php $Class_PU = 'checked="checked"';
                                                    $Class_PR = ''; @endphp
                                                    @elseif(isset($pageTemplate->chrDisplayStatus) && $pageTemplate->chrDisplayStatus == 'PR')
                                                    @php $Class_PR = 'checked="checked"';
                                                    $Class_PU = '';@endphp
                                                    @else
                                                    @php $Class_PU = 'checked="checked"';
                                                    $Class_PR = ''; @endphp
                                                    @endif
                                                        <div class="md-radio-inline">
                                                            <div class="md-radio">
                                                                <input class="md-radiobtn" type="radio" value="PU" name="chrDisplayStatus" id="chrDisplayStatus0" {{ $Class_PU }}> 
                                                                <label for="chrDisplayStatus0"> <span></span> <span class="check"></span> <span class="box"></span> All </label>
                                                            </div>
                                                            <div class="md-radio">               


                                                                <input class="md-radiobtn" type="radio" value="PR" name="chrDisplayStatus"  {{ $Class_PR }}  id="chrDisplayStatus1">

                                                                <label for="chrDisplayStatus1"> <span></span> <span class="check"></span> <span class="box"></span> Only Me </label>

                                                            </div>
                                                            <span class="help-block">
                                                                <strong></strong>
                                                            </span>
                                                            <div id="frmmail_membership_error">
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>                                             
                                            </div>
                                            <span style="color: red;">
                                                {{ $errors->first('display') }}
                                            </span>
                                            @endif
                                            <div class="form-actions">
                                                <div class="row">
                                                    <div class="col-md-12">
                                                        @if(isset($pageTemplate->fkMainRecord) && $pageTemplate->fkMainRecord != 0)
                                                        <button type="submit" name="saveandexit" class="btn btn-green-drake" value="saveandexit">{!! trans('pagetemplates::template.common.approve') !!}</button>
                                                        @else
                                                        @if($userIsAdmin)
                                                        <button type="submit" name="saveandedit" class="btn btn-green-drake" value="saveandedit">{!! trans('pagetemplates::template.common.saveandedit') !!}</button>
                                                        <button type="submit" name="saveandexit" class="btn btn-green-drake" value="saveandexit">{!! trans('pagetemplates::template.common.saveandexit') !!}</button>
                                                        @else
                                                        @if((isset($chrNeedAddPermission) && $chrNeedAddPermission == 'N') && (isset($charNeedApproval) && $charNeedApproval == 'N'))
                                                        <button type="submit" name="saveandexit" class="btn btn-green-drake" value="saveandexit">{!! trans('pagetemplates::template.common.saveandexit') !!}</button>
                                                        @else
                                                        <button type="submit" name="saveandexit" class="btn btn-green-drake" value="saveandexit">{!! trans('pagetemplates::template.common.saveandexit') !!}</button>
                                                        @endif
                                                        @endif
                                                        @endif
                                                        @php
                                                        if(isset($_REQUEST['tab']) && $_REQUEST['tab'] == 'P'){
                                                        $tab = '?tab=P';
                                                        }else if(isset($_REQUEST['tab']) && $_REQUEST['tab'] == 'A'){
                                                        $tab = '?tab=A';
                                                        }else if(isset($_REQUEST['tab']) && $_REQUEST['tab'] == 'D'){
                                                        $tab = '?tab=D';
                                                        }else if(isset($_REQUEST['tab']) && $_REQUEST['tab'] == 'T'){
                                                        $tab = '?tab=T';
                                                        }else{
                                                        $tab = '';
                                                        }
                                                        @endphp
                                                        <a class="btn red btn-outline" href="{{ url('powerpanel/page_template'.$tab) }}">{{ trans('pagetemplates::template.common.cancel') }}</a>
                                                        <span id="previewid" style="margin-left:10px"></span>
                                                        @if(isset($pageTemplate) && $userIsAdmin)
                                                        &nbsp;<a class="btn btn-green-drake" title="Preview" onClick="generatePreview('{{url('/previewpage?url='.(App\Helpers\MyLibrary::getFrontUri('page_template')['uri']))}}');">Preview</a>
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
     @if (Config::get('Constant.DEFAULT_VISUAL') == 'Y')
        {{--@include('powerpanel.partials.dialog-maker') --}}
        @php Powerpanel\VisualComposer\Controllers\VisualComposerController::get_dialog_maker()@endphp
    @endif
    @endsection
    @section('scripts')
    @if (Config::get('Constant.DEFAULT_VISUAL') == 'Y')
        @php Powerpanel\VisualComposer\Controllers\VisualComposerController::get_visual_checkEditor()@endphp
    @else
        @include('powerpanel.partials.ckeditor',['config'=>'docsConfig'])
    @endif

    <script type="text/javascript">
                        window.site_url = '{!! url("/") !!}';
                        var seoFormId = 'frmPageTemplate';
                        var user_action = "{{ isset($pageTemplate)?'edit':'add' }}";
                        var moduleAlias = "{{ App\Helpers\MyLibrary::getFrontUri('page_template')['moduleAlias'] }}";
                        var preview_add_route = '{!! route("powerpanel.page_template.addpreview") !!}';
                        var previewForm = $('#frmPageTemplate');
                        var isDetailPage = false;
                        function generate_seocontent1(formname) {
                        var Meta_Title = document.getElementById('title').value + "";
                                var abcd = $('textarea#txtDesc').val();
                                if (abcd != undefined){
                        var def = abcd.replace(/<a(\s[^>]*)?>.*?<\/a>/ig, "");
                                var abc = def.replace(/^(\s*)|(\s*)$/g, '').replace(/\s+/g, ' ');
                                var outString1 = abc.replace(/(<([^>]+)>)/ig, "");
                                var Meta_Description = outString1.substr(0, 200);
                        } else{
                        var Meta_Description = document.getElementById('title').value + "";
                        }

                                $('#varMetaTitle').val(Meta_Title);
                                $('#varMetaDescription').val(Meta_Description);
                                $('#meta_title').html(Meta_Title);
                                $('#meta_description').html(Meta_Description);
                        }
    </script>
    <script src="{{ $CDN_PATH.'resources/global/plugins/seo-generator/seo-info-generator.js' }}" type="text/javascript"></script>
    <script src="{{ $CDN_PATH.'resources/global/plugins/custom-alias/alias-generator.js' }}" type="text/javascript"></script>
    <script src="{{ $CDN_PATH.'messages.js' }}" type="text/javascript"></script>
    <script src="{{ $CDN_PATH.'resources/pages/scripts/packages/pagetemplates/page_template_validations.js' }}" type="text/javascript"></script>
    <script src="{{ $CDN_PATH.'resources/pages/scripts/custom.js' }}" type="text/javascript"></script>
    <script src="{{ $CDN_PATH.'resources/global/plugins/bootstrap-maxlength/bootstrap-maxlength.min.js' }}" type="text/javascript"></script>
    
    <script src="{{ $CDN_PATH.'resources/pages/scripts/custom.js' }}" type="text/javascript"></script>
    <script src="{{ $CDN_PATH.'resources/global/plugins/custom-alias/alias-generator.js' }}" type="text/javascript"></script>
    <!-- BEGIN CORE PLUGINS -->
    <script src="{{ $CDN_PATH.'resources/global/plugins/bootstrap/js/bootstrap.min.js' }}" type="text/javascript"></script>
    <script src="{{ $CDN_PATH.'resources/global/plugins/bootstrap-hover-dropdown/bootstrap-hover-dropdown.min.js' }}" type="text/javascript"></script>
    <script src="{{ $CDN_PATH.'resources/global/plugins/jquery-slimscroll/jquery.slimscroll.min.js' }}" type="text/javascript"></script>
    <script src="{{ Config::get('Constant.CDN_PATH').'resources/pages/scripts/pages_password_rules.js' }}" type="text/javascript"></script>
    <!-- END CORE PLUGINS -->
      @if (Config::get('Constant.DEFAULT_VISUAL') == 'Y')
        @php Powerpanel\VisualComposer\Controllers\VisualComposerController::get_builder_css_js()@endphp
    @endif
    @endsection