@section('css')
<link href="{{ $CDN_PATH.'resources/global/css/rank-button.css' }}" rel="stylesheet" type="text/css" />
<link href="{{ url('resources/global/plugins/bootstrap-datetimepicker/css/bootstrap-datetimepicker.min.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ url('resources/global/plugins/bootstrap-timepicker/css/timepicki.css') }}" rel="stylesheet" type="text/css" />
<style type="text/css">
    .cus_heading {
        padding-left: 11px;
    }
    .cus_heading span:first-child {
        padding-left: 8%;
    }
    .cus_heading span {
        width: 50%;
        display: block;
        float: left;
        padding-top: 8px;
        padding-bottom: 4px;
        font-weight: 600;
        font-size: 13px;    
    }
    @media (min-width:1600px) and (max-width:1600px){
        .cus_heading span:last-child {padding-left:8px;}
    }
    
    /* Chrome, Safari, Edge, Opera */
input::-webkit-outer-spin-button,
input::-webkit-inner-spin-button {
  -webkit-appearance: none;
  margin: 0;
}

/* Firefox */
input[type=number] {
  -moz-appearance: textfield;
}
</style>
@endsection
@extends('powerpanel.layouts.app')
@section('title')
{{Config::get('Constant.SITE_NAME')}} - PowerPanel
@stop
@section('content')
@php $settings = json_decode(Config::get("Constant.MODULE.SETTINGS")); @endphp
<!--@include('powerpanel.partials.breadcrumbs')-->
<div class="title_bar">
    <div class="page-head">
        <div class="page-title">
            <h1>Add Form </h1>
        </div>
    </div>	
    <ul class="page-breadcrumb breadcrumb">
        <li>
            <span aria-hidden="true" class="icon-home"></span>
            <a href="{{ url('powerpanel')}}">Home</a>
            <i class="fa fa-circle"></i>
        </li>
        <li>
            <a href="{{ url('powerpanel/formbuilder')}}">Manage Forms</a>
            <i class="fa fa-circle"></i>
        </li>

        <li class="active">
            Add Form
        </li>
    </ul>		
    <div class="add_category_button pull-right">
        <a title="Go to list" class="add_category" href="{{ url('powerpanel/formbuilder')}}">
            <span title="Go to list">Back</span> <i class="la la-arrow-left"></i>
        </a>
    </div>

</div>
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
                                    {!! Form::open(['method' => 'post','name'=>'frmfrombuilder','id'=>'frmfrombuilder']) !!}
                                    {!! Form::hidden('fkMainRecord', isset($frombuilder->fkMainRecord)?$frombuilder->fkMainRecord:old('fkMainRecord')) !!}
                                    @if(isset($frombuilder->varFormDescription))
                                    <input type="hidden" id="formdatadesc" name="formdatadesc" value="{{ $frombuilder->varFormDescription }}">
                                    @endif
                                    <div class="row">
                                        <div class="col-lg-12">
                                            <div class="form-group @if($errors->first('tag_line')) has-error @endif form-md-line-input">
                                                @php if(isset($frombuilder->varName)){
                                                $name = $frombuilder->varName;
                                                }else{
                                                $name = '';
                                                } @endphp
                                                <label class="form_title" for="site_name">Form Name <span aria-required="true" class="required"> * </span></label>
                                                <input maxlength="150" placeholder="Form Name" class="form-control seoField maxlength-handler" value="{{ $name }}" autocomplete="off" name="title" id="formtitle" type="text">
                                                <span class="help-block">
                                                    {{ $errors->first('title') }}
                                                </span>
                                            </div>   
                                        </div>
<!--                                        <div class="col-lg-4">
                                            <div class="form-group @if($errors->first('tag_line')) has-error @endif form-md-line-input">
                                                @php if(isset($frombuilder->FormTitle)){
                                                $form_title = $frombuilder->FormTitle;
                                                }else{
                                                $form_title = '';
                                                } @endphp
                                                <label class="form_title" for="site_name">Form Title </label>
                                                <input maxlength="150" placeholder="Form Title" class="form-control seoField maxlength-handler namespellingcheck" value="{{ $form_title }}" autocomplete="off" name="formtitles" id="formtitles" type="text">
                                                <span class="help-block">
                                                    {{ $errors->first('formtitles') }}
                                                </span>
                                            </div>   
                                        </div>
                                        <div class="col-lg-4">
                                            <div class="form-group @if($errors->first('tag_line')) has-error @endif form-md-line-input">
                                                @php if(isset($frombuilder->Description)){
                                                $form_description = $frombuilder->Description;
                                                }else{
                                                $form_description = '';
                                                } @endphp
                                                <label class="form_title" for="site_name">Form Description </label>
                                                <textarea maxlength="400" class="form-control seoField maxlength-handler metatitlespellingcheck" id="description" rows="3" placeholder="Form Description" name="description" cols="50" >{{ $form_description }}</textarea>
                                                <input maxlength="150" placeholder="Form Description" class="form-control seoField maxlength-handler shortdescspellingcheck" value="{{ $form_description }}" autocomplete="off" name="description" id="description" type="textarea">
                                                <span class="help-block">
                                                    {{ $errors->first('formdescription') }}
                                                </span>
                                            </div>   
                                        </div>-->
                                        
                                    </div>  
                                    <div class="row">
                                        <div class="col-md-12">
                                            @if(isset($frombuilder_highLight->fkIntImgId) && ($frombuilder_highLight->fkIntImgId != $frombuilder->fkIntImgId))
                                            @php $Class_fkIntImgId = " highlitetext"; @endphp
                                            @else
                                            @php $Class_fkIntImgId = ""; @endphp
                                            @endif
                                            <div class="image_thumb multi_upload_images">
                                                <div class="form-group">
                                                    <label class="form_title {{ $Class_fkIntImgId }}" for="front_logo">Background Image</label>
                                                    <div class="clearfix"></div>
                                                    <div class="fileinput fileinput-new" data-provides="fileinput">
                                                        <div class="fileinput-preview thumbnail frombuilder_image_img" data-trigger="fileinput" style="width:100%;float:left; height:120px;position: relative;">
                                                            @if(old('image_url'))
                                                            <img src="{{ old('image_url') }}" />
                                                            @elseif(isset($frombuilder->fkIntImgId))
                                                            <img src="{!! App\Helpers\resize_image::resize($frombuilder->fkIntImgId,120,120) !!}" />
                                                            @else
                                                            <img class="img_opacity" src="{{ $CDN_PATH.'resources/images/upload_file.gif' }}" />
                                                            @endif
                                                        </div>

                                                        <div class="input-group">
                                                            <a class="media_manager" data-multiple="false" onclick="MediaManager.open('frombuilder_image');"><span class="fileinput-new"></span></a>
                                                            <input class="form-control" type="hidden" id="frombuilder_image" name="img_id" value="{{ isset($frombuilder->fkIntImgId)?$frombuilder->fkIntImgId:old('img_id') }}" />
                                                            @php
                                                                    if(isset($frombuilder->fkIntImgId)){
                                                                    $folderid = App\Helpers\MyLibrary::GetFolderID($frombuilder->fkIntImgId);
                                                                    @endphp
                                                                    @if(isset($folderid->fk_folder) && $folderid->fk_folder != '0')
                                                                    <input class="form-control" type="hidden" id="folder_id" name="folder_id" value="{{ $folderid->fk_folder }}" />
                                                                    @endif
                                                                    @php
                                                                    }
                                                                    @endphp
                                                            <input class="form-control" type="hidden" id="image_url" name="image_url" value="{{ old('image_url') }}" />
                                                        </div>
                                                        <div class="overflow_layer">
                                                            <a onclick="MediaManager.open('frombuilder_image');" class="media_manager remove_img"><i class="fa fa-pencil"></i></a>
                                                            <a href="javascript:;" class="fileinput-exists remove_img removeimg" data-dismiss="fileinput"><i class="fa fa-trash-o"></i></a>
                                                        </div>

                                                    </div>
                                                    <div class="clearfix"></div>
                                                    @php $height = isset($settings->height)?$settings->height:100; $width = isset($settings->width)?$settings->width:200; @endphp <span>{{ trans('formbuilder::template.common.imageSize',['height'=>$height, 'width'=>$width]) }}</span>
                                                </div>
                                                <span class="help-block">
                                                    {{ $errors->first('img_id') }}
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                    

                                    <div class="dropdown build_setting_acc">
                                        <div class="dropdown-toggle title_build_email">Email Settings  <i class="la la-angle-down"></i></div>
                                        <div class="dropdown-menu">
                                            <div class="build_stng_body">                                                
                                                <div class="row">
                                                    <div class="col-lg-6">
                                                        <div class="form-group @if($errors->first('tag_line')) has-error @endif form-md-line-input">
                                                            @php if(isset($frombuilder->varEmail)){
                                                            $email = $frombuilder->varEmail;
                                                            }else{
                                                            $email = "";
                                                            } @endphp
                                                            <label class="form_title {!! $email !!}" for="site_name">{{ trans('Admin Email Id') }} <span aria-required="true" class="required"> * </span></label>
                                                            {!! Form::text('email', isset($frombuilder->varEmail) ? $frombuilder->varEmail:old('email'), array('maxlength'=>'150','placeholder' => trans('Admin Email Id'),'id'=>'email','class' => 'form-control seoField maxlength-handler emailspellingcheck','autocomplete'=>'off')) !!}
                                                            <span class="help-block">
                                                                {{ $errors->first('email') }}
                                                            </span>
                                                        </div>    
                                                    </div>
                                                    <div class="col-lg-6">
                                                        <div class="form-group @if($errors->first('tag_line')) has-error @endif form-md-line-input">
                                                            @php if(isset($frombuilder->varAdminSubject)){
                                                            $admin_subject = $frombuilder->varAdminSubject;
                                                            }else{
                                                            $admin_subject = '';
                                                            } @endphp
                                                            <label class="form_title" for="site_name">Admin Email Subject <span aria-required="true" class="required"> * </span></label>
                                                            <input maxlength="150" placeholder="Admin Email Subject"  class="form-control seoField maxlength-handler designationspellingcheck" value="{{ $admin_subject }}" autocomplete="off" name="admin_subject" id="admin_subject" type="text">
                                                            <span class="help-block">
                                                                {{ $errors->first('admin_subject') }}
                                                            </span>
                                                        </div>    
                                                    </div>    
                                                    
                                                </div>
                                               <div class="row">
                                                    <div class="col-lg-6">
                                                        <div class="form-group @if($errors->first('tag_line')) has-error @endif form-md-line-input">
                                                            @php if(isset($frombuilder->varAdminContent)){
                                                            $admin_content = $frombuilder->varAdminContent;
                                                            }else{
                                                            $admin_content = '';
                                                            } @endphp
                                                            <label class="form_title" for="site_name">Admin Email Content <span aria-required="true" class="required"> * </span></label>
                                                            <textarea maxlength="400" class="form-control seoField maxlength-handler metatitlespellingcheck" id="admin_content" rows="3" placeholder="Admin Email Content" name="admin_content" cols="50" >{{ $admin_content }}</textarea>
                                                            <!--<textarea maxlength="500" placeholder="Admin Email Content" class="form-control seoField maxlength-handler metatitlespellingcheck" value="{{ $admin_content }}" autocomplete="off" name="admin_content" id="admin_content" />-->
                                                            <span class="help-block">
                                                                {{ $errors->first('admin_content') }}
                                                            </span>
                                                        </div>   
                                                    </div>   
                                                    <div class="col-lg-6">
                                                        <div class="form-group @if($errors->first('tag_line')) has-error @endif form-md-line-input">
                                                            @php if(isset($frombuilder->varThankYouMsg)){
                                                            $varThankYouMsg = $frombuilder->varThankYouMsg;
                                                            }else{
                                                            $varThankYouMsg = '';
                                                            } @endphp
                                                            <label class="form_title" for="site_name">Thank You Massage <span aria-required="true" class="required"> * </span></label>
                                                            <input placeholder="Thank You Massage"  class="form-control seoField maxlength-handler designationspellingcheck" value="{{ $varThankYouMsg }}" autocomplete="off" name="varThankYouMsg" id="varThankYouMsg" type="text">
                                                            <span class="help-block">
                                                                {{ $errors->first('varThankYouMsg') }}
                                                            </span>
                                                        </div>    
                                                    </div>
                                                </div>

                                                <div class="row">
                                                    <div class="col-md-12">
                                                        <div class="form-group ">
                                                            <label class="form_title">Email to User:</label>
                                                          
                                                            @if (isset($frombuilder->chrCheckUser) && $frombuilder->chrCheckUser == 'Y')
                                                            @php $checked_section = true; @endphp
                                                            @php $display_Section = ''; @endphp
                                                            @else
                                                            @php $checked_section = null; 
                                                            @endphp
                                                            @php $display_Section = 'none'; @endphp
                                                            @endif
                                                            {{ Form::checkbox('chrDisplayUser',null,$checked_section, array('id'=>'chrDisplayUser')) }}
                                                        </div>
                                                    </div>
                                                </div>
                                                 @if (isset($frombuilder->chrCheckUser) && $frombuilder->chrCheckUser == 'Y')
                                                  @php $opensection = ''; @endphp
                                                  @else
                                                   @php $opensection = "style='display: none;'"; @endphp
                                                 @endif
                                                <div id="formhiddenfield" {!! $opensection !!}>
                                                    <div class="row">
                                                        <div class="col-lg-6">
                                                            <div class="form-group @if($errors->first('tag_line')) has-error @endif form-md-line-input">
                                                                @php if(isset($frombuilder->varUserSubject)){
                                                                $user_subject = $frombuilder->varUserSubject;
                                                                }else{
                                                                $user_subject = '';
                                                                } @endphp
                                                                <label class="form_title" for="site_name">User Email Subject <span aria-required="true" class="required"> * </span></label>
                                                                <input maxlength="150" placeholder="User Email Subject" class="form-control seoField maxlength-handler userspellingcheck" value="{{ $user_subject }}" autocomplete="off" name="user_subject" id="user_subject" type="text">
                                                                <span class="help-block">
                                                                    {{ $errors->first('user_subject') }}
                                                                </span>
                                                            </div>
                                                        </div>
                                                        <div class="col-lg-6">
                                                            <div class="form-group @if($errors->first('tag_line')) has-error @endif form-md-line-input">
                                                                @php if(isset($frombuilder->varUserContent)){
                                                                $user_content = $frombuilder->varUserContent;
                                                                }else{
                                                                $user_content = '';
                                                                } @endphp
                                                                <label class="form_title" for="site_name">User Email Content <span aria-required="true" class="required"> * </span></label>
                                                                <!--<input maxlength="500" placeholder="User Email Content" class="form-control seoField maxlength-handler metadescspellingcheck" value="{{ $user_content }}" autocomplete="off" name="user_content" id="user_content" type="textarea">-->
                                                                <textarea maxlength="400" class="form-control seoField maxlength-handler metadescspellingcheck" id="user_content" rows="3" placeholder="User Email Content" name="user_content" cols="50" >{{ $user_content }}</textarea>
                                                                <span class="help-block">
                                                                    {{ $errors->first('user_content') }}
                                                                </span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                            </div>
                                        </div>
                                    </div>
                                    <div id="fb-editor"></div>
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

@include('powerpanel.partials.ckeditor',['config'=>'docsConfig'])

<script src="{{ url('assets/formBuilder/jquery-ui.min.js') }}"></script>
<script src="{{ url('assets/formBuilder/form-builder.min.js') }}"></script>
<script src="{{ url('assets/formBuilder/form-render.min.js') }}"></script>

<script>
var user_action = "{{ isset($frombuilder)?'edit':'add' }}";
<?php if(isset($frombuilder)){
    $fdata = addslashes($frombuilder->varFormDescription);
}else{
    $fdata = '';
}
?>
var formEditData = '<?php echo $fdata;?>';
var fbEditor = document.getElementById('fb-editor');

$(function () {
    $("#chrDisplayUser").click(function () {
        if ($(this).is(":checked")) {
            $("#formhiddenfield").show();
        } else {
            $("#formhiddenfield").hide();

        }
    });

});

jQuery(function($) {
    if ('<?php echo Request::segment(4); ?>' == 'edit?tab=P' || '<?php echo Request::segment(4); ?>' == 'edit') {
        var fbTemplate = document.getElementById('fb-editor'),
        options = {
            formData: formEditData,
            fields: [{
                type: 'text',
                className: 'datetimepicker',
                label: 'DateTime Picker'
            }, {
                type: 'text',
                className: 'time_element',
                label: 'Time Picker'
            },{
              type: 'checkbox-group',
              label: 'Predefined Option',
              className: 'predefine',
              values: [
                {
                  label: 'Country',
                  value: 'countries',
                  selected: false
                },
                {
                  label: 'State',
                  value: 'states',
                  selected: false
                },
                {
                  label: 'Gender',
                  value: 'gender',
                  selected: false
                },
                {
                  label: 'Month',
                  value: 'months',
                  selected: false
                }
              ]
            },{
                type: 'text',
                className: 'datepicker',
                label: 'Date Picker'
            },{
                type: 'text',
                className: 'form-control urlclass',
                label: 'URL'
            },{
                type: 'text',
                className: 'form-control uniqueclass',
                label: 'User Name'
            }]
        };
        setTimeout(function () {
          $(".time_element").parents(".form-field").find(".subtype-wrap").hide();
          $(".time_element").parents(".form-field").find(".maxlength-wrap").hide();
          $(".time_element").parents(".form-field").find(".value-wrap").hide();
          $(".datetimepicker").parents(".form-field").find(".subtype-wrap").hide();
          $(".datetimepicker").parents(".form-field").find(".maxlength-wrap").hide();
          $(".datetimepicker").parents(".form-field").find(".value-wrap").hide();
          $(".predefine").parents(".form-field").find(".field-options").hide();
           $(".datepicker").parents(".form-field").find(".subtype-wrap").hide();
          $(".datepicker").parents(".form-field").find(".maxlength-wrap").hide();
          $(".datepicker").parents(".form-field").find(".value-wrap").hide();
            $(".uniqueclass").parents(".form-field").find(".subtype-wrap").hide();
            $(".urlclass").parents(".form-field").find(".subtype-wrap").hide();
        }, 2000);
        $(fbTemplate).formBuilder(options);
       
    }
});

$(document).ready(function () {
    
    $(document).on("click", ".build_setting_acc", function () {
        $(".build_setting_acc .dropdown-menu").show();
    });
    /*code for key up event of option textboxes*/
    $(document).on("keyup", ".sortable-options-wrap .sortable-options .option-label", function () {
        var gettextval = $(this).val();
//        var gettextNewval = gettextval.replace(/[&\/\\#,+()$~%.'":*?<>{}\s]/g, '-');
        var gettextNewval = gettextval.replace(/[*\s]/g, '-');
        $(this).parent('li').find('.option-value').val(gettextNewval);
    });
    
    $(document).on("focusout", ".fld-max", function (e) {
     var maxval = $(this).val();
        var minval = $(this).parents('.form-elements').find('.min-wrap .fld-min').val();
        if(parseInt(maxval) < parseInt(minval)){
            alert('Min value can not gather then max value.');
            $(this).val('');
        }
    });
    
    $(document).on("focusout", ".fld-min", function (e) {
     var minval = $(this).val();
        var maxval = $(this).parents('.form-elements').find('.max-wrap .fld-max').val();
        if(parseInt(minval) > parseInt(maxval)){
            alert('Min value can not gather then max value.');
            $(this).val('');
        }
    });
    
    /*end of code for key up event of option textboxes*/
     $(document).on("keypress", ".fld-min", function (e) {
         var t = 0;
            t = document.all ? 3 : document.getElementById ? 1 : document.layers ? 2 : 0;
            if (document.all)
                e = window.event;
            var n = "";
            var r = "";
            if (t == 2) {
                if (e.which > 0)
                    n = "(" + String.fromCharCode(e.which) + ")";
                r = e.which
            } else {
                if (t == 3) {
                    r = window.event ? event.keyCode : e.which
                } else {
                    if (e.charCode > 0)
                        n = "(" + String.fromCharCode(e.charCode) + ")";
                    r = e.charCode
                }
            }
            if (r >= 65 && r <= 90 || r >= 97 && r <= 122 || r >= 33 && r <= 39 || r >= 42 && r <= 42 ||  r >= 43 && r <= 43 || r >= 44 && r <= 44 || r >= 45 && r <= 45 || r >= 46 && r <= 47 || r >= 58 && r <= 64 || r >= 91 && r <= 96 || r >= 123 && r <= 126) {
                return false
            }
            return true
    });
    
    $(document).on("keypress", ".fld-max", function (e) {
       
         var t = 0;
            t = document.all ? 3 : document.getElementById ? 1 : document.layers ? 2 : 0;
            if (document.all)
                e = window.event;
            var n = "";
            var r = "";
            if (t == 2) {
                if (e.which > 0)
                    n = "(" + String.fromCharCode(e.which) + ")";
                r = e.which
            } else {
                if (t == 3) {
                    r = window.event ? event.keyCode : e.which
                } else {
                    if (e.charCode > 0)
                        n = "(" + String.fromCharCode(e.charCode) + ")";
                    r = e.charCode
                }
            }
            if (r >= 65 && r <= 90 || r >= 97 && r <= 122 || r >= 33 && r <= 39 || r >= 42 && r <= 42 ||  r >= 43 && r <= 43 || r >= 44 && r <= 44 || r >= 45 && r <= 45 || r >= 46 && r <= 47 || r >= 58 && r <= 64 || r >= 91 && r <= 96 || r >= 123 && r <= 126) {
                return false
            }
            return true
    });
     $(document).on("keypress", ".fld-maxlength", function (e) {
         var t = 0;
            t = document.all ? 3 : document.getElementById ? 1 : document.layers ? 2 : 0;
            if (document.all)
                e = window.event;
            var n = "";
            var r = "";
            if (t == 2) {
                if (e.which > 0)
                    n = "(" + String.fromCharCode(e.which) + ")";
                r = e.which
            } else {
                if (t == 3) {
                    r = window.event ? event.keyCode : e.which
                } else {
                    if (e.charCode > 0)
                        n = "(" + String.fromCharCode(e.charCode) + ")";
                    r = e.charCode
                }
            }
            if (r >= 65 && r <= 90 || r >= 97 && r <= 122 || r >= 33 && r <= 39 || r >= 42 && r <= 42 ||  r >= 43 && r <= 43 || r >= 44 && r <= 44 || r >= 45 && r <= 45 || r >= 46 && r <= 47 || r >= 58 && r <= 64 || r >= 91 && r <= 96 || r >= 123 && r <= 126) {
                return false
            }
            return true
    });
});
if ('<?php echo Request::segment(3); ?>' == 'add') {
    jQuery(function ($) {
        var fields = [{
                type: 'text',
                className: 'datetimepicker',
                label: 'DateTime Picker'
            }, {
                type: 'text',
                className: 'time_element',
                label: 'Time Picker'
            },{
              type: 'checkbox-group',
              label: 'Predefined Option',
              className: 'predefine',
              values: [
                {
                  label: 'Country',
                  value: 'countries',
                  selected: false
                },
                {
                  label: 'State',
                  value: 'states',
                  selected: false
                },
                {
                  label: 'Gender',
                  value: 'gender',
                  selected: false
                },
                {
                  label: 'Month',
                  value: 'months',
                  selected: false
                }
              ]
            }, {
                type: 'text',
                className: 'datepicker',
                label: 'Date Picker'
            },{
                type: 'text',
                className: 'form-control urlclass',
                label: 'URL'
            },{
                type: 'text',
                className: 'form-control uniqueclass',
                label: 'User Name'
            }];
        $(document.getElementById('fb-editor')).formBuilder({fields});
    });
}
</script>
<script src="{{ url('resources/global/plugins/bootstrap-datetimepicker/js/bootstrap-datetimepicker.js') }}" type="text/javascript"></script>
<script src="{{ url('resources/global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js') }}" type="text/javascript"></script>
<script type="text/javascript">
    
     $(document).on("click", ".input-control-15", function () {
          $(".time_element").parents(".form-field").find(".subtype-wrap").hide();
          $(".time_element").parents(".form-field").find(".maxlength-wrap").hide();
          $(".time_element").parents(".form-field").find(".value-wrap").hide();
    });
    $(document).on("click", ".input-control-14", function () {
        $(".datetimepicker").parents(".form-field").find(".subtype-wrap").hide();
        $(".datetimepicker").parents(".form-field").find(".maxlength-wrap").hide();
        $(".datetimepicker").parents(".form-field").find(".value-wrap").hide();
    });
    $(document).on("click", ".input-control-17", function () {
        $(".datepicker").parents(".form-field").find(".subtype-wrap").hide();
        $(".datepicker").parents(".form-field").find(".maxlength-wrap").hide();
        $(".datepicker").parents(".form-field").find(".value-wrap").hide();
    });
    $(document).on("click", ".input-control-16", function () {
          $(".predefine").parents(".form-field").find(".field-options").hide();
    });
    $(document).on("click", ".input-control-19", function () {
          $(".uniqueclass").parents(".form-field").find(".subtype-wrap").hide();
    });
    $(document).on("click", ".input-control-18", function () {
          $(".urlclass").parents(".form-field").find(".subtype-wrap").hide();
    });
   
    $(document).on("click", ".datepicker", function () {
        setTimeout(function () {
            GetDate();
        }, 500);
    });
    $(document).on("click", ".time_element", function () {
        setTimeout(function () {
            GetTime();
        }, 500);
    });
    $(document).on("click", ".datetimepicker", function () {
        setTimeout(function () {
            GetDateTime();
        }, 500);
    });
    function GetTime() {
        $(".time_element").timepicki();
    }

    function GetDateTime() { 
        $('.datetimepicker').datetimepicker({
            autoclose: true,
            showMeridian: true,
            minuteStep: 5,
            format: DEFAULT_DT_FMT_FOR_DATEPICKER + ' HH:ii P'
        });
    }
    function GetDate() {
        $('.datepicker').datepicker({
            format: DEFAULT_DT_FMT_FOR_DATEPICKER,
            step: 5
        });
    }
</script>
<script src="{{ url('resources/global/plugins/bootstrap-timepicker/js/timepicki.js') }}"></script>
@endsection