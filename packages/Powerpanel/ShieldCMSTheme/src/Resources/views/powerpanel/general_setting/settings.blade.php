@extends('powerpanel.layouts.app')
@section('title')
{{Config::get('Constant.SITE_NAME')}} - PowerPanel
@endsection
@section('content')
@include('powerpanel.partials.breadcrumbs')
<div class="row">
    <div class="col-md-12">
        @if(Session::has('message'))
        <div class="alert alert-success">
            <button class="close" data-close="alert"></button>
            {{ Session::get('message') }}
        </div>
        @endif
        <div class="portlet light bordered">
            <div class="portlet-body form_pattern">
                <div class="tabbable tabbable-tabdrop">
                    <?php // echo $tab_value;exit;?>
                    @if(empty($tab_value))
                    @php
                    $general_blank_tab_active = 'active'
                    @endphp
                    @else
                    @php
                    $general_blank_tab_active = ''
                    @endphp
                    @endif
                    @can('settings-general-setting-management')
                    @if($tab_value=='general_settings')
                    @php
                    $general_tab_active = 'active'
                    @endphp
                    @else
                    @php
                    $general_tab_active = ''
                    @endphp
                    @endif
                    @endcan
                    @can('settings-smtp-mail-setting')
                    @if($tab_value=='smtp_settings')
                    @php
                    $smtp_tab_active = 'active'
                    @endphp
                    @else
                    @php
                    $smtp_tab_active = ''
                    @endphp
                    @endif
                    @endcan
                    @can('settings-seo-setting')
                    @if($tab_value=='seo_settings')
                    @php
                    $seo_tab_active = 'active'
                    @endphp
                    @else
                    @php
                    $seo_tab_active = ''
                    @endphp
                    @endif
                    @endcan
                    @can('settings-social-setting')
                    @if($tab_value=='social_settings')
                    @php
                    $social_tab_active = 'active'
                    @endphp
                    @else
                    @php
                    $social_tab_active = ''
                    @endphp
                    @endif
                    @endcan
                    @can('settings-social-media-share-setting')
                    @if($tab_value=='social_share_settings')
                    @php
                    $social_share_tab_active = 'active'
                    @endphp
                    @else
                    @php
                    $social_share_tab_active = ''
                    @endphp
                    @endif
                    @endcan
                    @can('settings-other-setting')
                    @if($tab_value=='other_settings')
                    @php
                    $other_tab_active = 'active'
                    @endphp
                    @else
                    @php
                    $other_tab_active = ''
                    @endphp
                    @endif
                    @endcan
                    @can('settings-security-setting')
                    @if($tab_value=='security_settings')
                    @php
                    $security_tab_active = 'active'
                    @endphp
                    @else
                    @php
                    $security_tab_active = ''
                    @endphp
                    @endif
                    @endcan

                    @can('settings-cron-setting')
                    @if($tab_value=='cron_settings')
                    @php
                    $cron_tab_active = 'active'
                    @endphp
                    @else
                    @php
                    $cron_tab_active = ''
                    @endphp
                    @endif
                    @endcan

                    @can('settings-features-setting')
                    @if($tab_value=='features_settings')
                    @php
                    $features_tab_active = 'active'
                    @endphp
                    @else
                    @php
                    $features_tab_active = ''
                    @endphp
                    @endif
                    @endcan


                    @can('settings-magic-setting')
                    @if($tab_value=='magic_settings')
                    @php
                    $magic_tab_active = 'active'
                    @endphp
                    @else
                    @php
                    $magic_tab_active = ''
                    @endphp
                    @endif
                    @endcan
                    @can('settings-maintenancenew-setting')
                    @if($tab_value=='maintenancenew_settings')
                    @php
                    $maintenancenew_tab_active = 'active'
                    @endphp
                    @else
                    @php
                    $maintenancenew_tab_active = ''
                    @endphp
                    @endif
                    @endcan


                    @can('settings-maintenance-setting')
                    @if($tab_value=='maintenance')
                    @php
                    $maintenance_tab_active = 'active'
                    @endphp
                    @else
                    @php
                    $maintenance_tab_active = ''
                    @endphp
                    @endif
                    @endcan
                    @can('settings-module-setting')
                    @if($tab_value=='module')
                    @php
                    $module_tab_active = 'active';
                    $general_tab_active='';
                    $general_blank_tab_active='';
                    @endphp
                    @else
                    @php
                    $module_tab_active = ''
                    @endphp
                    @endif
                    @endcan
                    <div class="notify"></div>
                    <ul class="nav nav-pills tab_section">
                        @can('settings-general-setting-management')
                        <li class="{{$general_tab_active}} {{$general_blank_tab_active}}">
                            <a href="#general" data-toggle="tab" onclick="getAttributes('general')">{{  trans('shiledcmstheme::template.setting.general') }}</a>
                        </li>
                        @endcan
                        @can('settings-smtp-mail-setting')
                         @if(auth()->user()->name == 'Super Admin')
                        <li class="{{$smtp_tab_active}}">																												
                            <a href="#smtp-mail" data-toggle="tab" onclick="getAttributes('smtp-mail')">
                                {{  trans('shiledcmstheme::template.setting.SMTPMail') }} </a>
                        </li>
                        @endif
                        @endcan
                        @can('settings-seo-setting')
                        <li class="{{$seo_tab_active}}">
                            <a href="#seo" data-toggle="tab" onclick="getAttributes('seo')">{{  trans('shiledcmstheme::template.setting.seo') }}</a>
                        </li>
                        @endcan
                        @can('settings-social-setting')
                        <li class="{{$social_tab_active}}">
                            <a href="#social" data-toggle="tab" onclick="getAttributes('social')">{{  trans('shiledcmstheme::template.setting.social') }}</a>
                        </li>
                        @endcan
                        @can('settings-social-media-share-setting')
                         @if(auth()->user()->name == 'Super Admin')
                        <li class="{{$social_share_tab_active}}">
                            <a href="#socialshare" data-toggle="tab" onclick="getAttributes('socialshare')">{{  trans('shiledcmstheme::template.setting.socialMediaShare') }}</a>
                        </li>
                        @endif
                        @endcan
                        @can('settings-magic-setting')
                        <li class="{{$magic_tab_active}}">
                            <a href="#magic" data-toggle="tab" onclick="getAttributes('magic')">Magic Upload</a>
                        </li>
                        @endcan

                        @can('settings-security-setting')
                        <li class="{{$security_tab_active}}">
                            <a href="#security" data-toggle="tab">{{  trans('shiledcmstheme::template.setting.securitySettings') }}</a>
                        </li>
                        @endcan

                        @can('settings-cron-setting')
                        @if(auth()->user()->name == 'Super Admin')
                        <li class="{{$cron_tab_active}}">
                            <a href="#cron" data-toggle="tab">{{  trans('shiledcmstheme::template.setting.cronSettings') }}</a>
                        </li>
                        @endif
                        @endcan

                        @can('settings-features-setting')
                        @if(auth()->user()->name == 'Super Admin')
                        <li class="{{$features_tab_active}}">
                            <a href="#features" data-toggle="tab">{{  trans('shiledcmstheme::template.setting.featuresSettings') }}</a>
                        </li>
                        @endif
                        @endcan
                        @can('settings-other-setting')
                        <li class="{{$other_tab_active}}" id="one_tab">
                            <a href="#other" data-toggle="tab" onclick="getAttributes('other')">{{  trans('shiledcmstheme::template.setting.otherSettings') }}</a>
                        </li>
                        @endcan
                        @can('settings-maintenance-setting')
                        <li class="{{$maintenance_tab_active}}">
                            <a href="#maintenance" data-toggle="tab" onclick="getAttributes('maintenance')">Reset Logs</a>
                        </li>
                        @endcan
                        @can('settings-maintenancenew-setting')
                         @if(auth()->user()->name == 'Super Admin')
                        <li class="{{$maintenancenew_tab_active}}">
                            <a href="#maintenancenew" data-toggle="tab" onclick="getAttributes('maintenancenew')">Maintenance</a>
                        </li>
                        @endif
                        @endcan
                        <!--@can('settings-module-setting')
                                <li class="modulewisesettings {{$module_tab_active}}">
                                <a href="#modulesettings"  data-toggle="tab">{{  trans('shiledcmstheme::template.setting.modulesettings') }}</a>
                        </li>
                        @endcan-->
                    </ul>
                    <!--                    <ul class="nav nav-pills tab_section tab_section_setting" style="display: none;">
                                            @can('settings-other-setting')
                                            <li class="{{$other_tab_active}}">
                                                <a href="#other" data-toggle="tab" onclick="getAttributes('other')" id="second_tab">Settings</a>
                                            </li>
                                            @endcan
                                            @can('settings-security-setting')
                                            <li class="{{$security_tab_active}}">
                                                <a href="#security" data-toggle="tab" onclick="getAttributes('security')">{{  trans('shiledcmstheme::template.setting.securitySettings') }}</a>
                                            </li>
                                            @endcan
                    
                                            @can('settings-cron-setting')
                                            <li class="{{$cron_tab_active}}">
                                                <a href="#cron" data-toggle="tab" onclick="getAttributes('cron')">{{  trans('shiledcmstheme::template.setting.cronSettings') }}</a>
                                            </li>
                                            @endcan
                    
                                            @can('settings-features-setting')
                                            <li class="{{$features_tab_active}}">
                                                <a href="#features" data-toggle="tab" onclick="getAttributes('features')">{{  trans('shiledcmstheme::template.setting.featuresSettings') }}</a>
                                            </li>
                                            @endcan
                                            @can('settings-magic-setting')
                                            <li class="{{$magic_tab_active}}">
                                                <a href="#magic" data-toggle="tab" onclick="getAttributes('magic')">Magic Upload</a>
                                            </li>
                                            @endcan
                                        </ul>-->
                    <div class="tab-content settings">
                        @can('settings-general-setting-management')
                        <div class="tab-pane {{$general_tab_active}} {{$general_blank_tab_active}}" id="general">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="portlet-form">
                                        {!! Form::open(['method' => 'post','id'=>'frmSettings']) !!}
                                        {!! Form::hidden('tab', 'general_settings', ['id' => 'general']) !!}
                                        <div class="form-body">
                                            <div class="form-group {{ $errors->has('site_name') ? ' has-error' : '' }} form-md-line-input">
                                                <label class="form_title" for="site_name">{{  trans('shiledcmstheme::template.setting.siteName') }} <span aria-required="true" class="required"> * </span></label>
                                                {!! Form::text('site_name', Config::get('Constant.SITE_NAME') , array('maxlength' => '150', 'class' => 'form-control maxlength-handler', 'id' => 'site_name' , 'placeholder' =>  trans('shiledcmstheme::template.setting.siteName'),'autocomplete'=>'off')) !!}
                                                <span class="help-block">
                                                    {{ $errors->first('site_name') }}
                                                </span>
                                            </div>
                                            <div class="form-group {{ $errors->has('front_logo_id') ? ' has-error' : '' }}">
                                                <div class="image_thumb">
                                                    <label for="front_logo" class="form_title">{{  trans('shiledcmstheme::template.setting.frontLogo') }} <span aria-required="true" class="required"> * </span></label>
                                                    <div class="clearfix"></div>
                                                    <div class="fileinput fileinput-new" data-provides="fileinput">
                                                        <div class="fileinput-preview thumbnail front_logo_img" data-trigger="fileinput" style="width:100%; height:120px;position: relative;">
                                                            @if (!empty(Config::get('Constant.FRONT_LOGO_ID')))
                                                            <img src="{{ App\Helpers\resize_image::resize(Config::get('Constant.FRONT_LOGO_ID')) }}"/>
                                                            @else
                                                            <img src="{{ $CDN_PATH.'resources/images/upload_file.gif' }}"/>
                                                            @endif
                                                        </div>
                                                        <div class="input-group">
                                                            <a class="media_manager" onclick="MediaManager.open('front_logo');"><span class="fileinput-new"></span></a>
                                                        </div>
                                                        {!! Form::hidden('front_logo_id',!empty(Config::get('Constant.FRONT_LOGO_ID'))?Config::get('Constant.FRONT_LOGO_ID'):old('image_upload') , array('class' => 'form-control', 'id' => 'front_logo')) !!}
                                                    </div>
                                                    <div class="clearfix"></div>
                                                    <span>{{  trans('shiledcmstheme::template.common.imageSize',['height'=>'300','width'=>'600']) }}</span>
                                                    <span class="help-block">
                                                        {{ $errors->first('front_logo_id') }}
                                                    </span>
                                                </div>
                                            </div>
                                            <div class="form-group {{ $errors->has('front_footer_logo_id') ? ' has-error' : '' }}">
                                                <div class="image_thumb">
                                                    <label for="front_logo" class="form_title">{{  trans('shiledcmstheme::template.setting.frontFooterLogo') }} <span aria-required="true" class="required"> * </span></label>
                                                    <div class="clearfix"></div>
                                                    <div class="fileinput fileinput-new" data-provides="fileinput">
                                                        <div class="fileinput-preview thumbnail front_footer_logo_img" data-trigger="fileinput" style="width:100%; height:120px;position: relative;">
                                                            @if (!empty(Config::get('Constant.FRONT_FOOTER_LOGO_ID')))
                                                            <img src="{{ App\Helpers\resize_image::resize(Config::get('Constant.FRONT_FOOTER_LOGO_ID')) }}" style="background-color:#0071c0"/>
                                                            @else
                                                            <img src="{{ $CDN_PATH.'resources/images/upload_file.gif' }}"/>
                                                            @endif
                                                        </div>
                                                        <div class="input-group">
                                                            <a class="media_manager" onclick="MediaManager.open('front_footer_logo');"><span class="fileinput-new"></span></a>
                                                        </div>
                                                        {!! Form::hidden('front_footer_logo_id',!empty(Config::get('Constant.FRONT_FOOTER_LOGO_ID'))?Config::get('Constant.FRONT_FOOTER_LOGO_ID'):old('image_upload') , array('class' => 'form-control', 'id' => 'front_footer_logo')) !!}
                                                    </div>
                                                    <div class="clearfix"></div>
                                                    <span>{{  trans('shiledcmstheme::template.common.imageSize',['height'=>'123','width'=>'391']) }}</span>
                                                    <span class="help-block">
                                                        {{ $errors->first('front_footer_logo_id') }}
                                                    </span>
                                                </div>
                                            </div>
                                            @if(!empty($timezone))
                                            <div class="form-group">
                                                <label class="form_title" for="timezone">{{  trans('shiledcmstheme::template.setting.timezone') }}</label>
                                                <select class="form-control bs-select select2" name="timezone" id="timezone" style="width: 100%">
                                                    @foreach ($timezone as $allzones)
                                                    @if(!empty(Config::get('Constant.DEFAULT_TIME_ZONE')))
                                                    @if($allzones->zone_name == Config::get('Constant.DEFAULT_TIME_ZONE'))
                                                    @php  $selected = 'selected'  @endphp
                                                    @else
                                                    @php  $selected = ''  @endphp
                                                    @endif
                                                    @elseif($allzones->zone_name == 'America/Cayman')
                                                    @php  $selected = 'selected'  @endphp
                                                    @else
                                                    @php  $selected = ''  @endphp
                                                    @endif
                                                    <option {{$selected}} value="{{$allzones->zone_name}}">{{$allzones->zone_name}}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            @endif
                                            <div class="form-group form-md-line-input" style="display:none;">
                                                <label class="form_title">Enable Inquiries Notifcation for Department Email:</label>
                                                @if (Config::get('Constant.DEFAULT_NOTIFCATION_DEPARTMENT_EMAIL') == 'Y')
                                                @php $checked_section = true; @endphp

                                                @else
                                                @php $checked_section = null; 
                                                @endphp
                                                @php $display_Section = 'none'; @endphp
                                                @endif
                                                {{ Form::checkbox('chrDepartmentEmail',null,$checked_section, array('id'=>'chrDepartmentEmail')) }}
                                            </div>
                                            <div class="form-group form-md-line-input">
                                                <label class="form_title" for="default_custom_email">Default Email<span aria-required="true" class="required"> * </span></label>
                                                {!! Form::text('default_custom_email', Crypt::decrypt(Config::get('Constant.DEFAULT_CUSTOM_EMAIL')) , array('maxlength' => '150','class' => 'form-control', 'id' => 'default_custom_email' , 'autocomplete'=>'off')) !!}
                                            </div>
                                            <div class="form-group form-md-line-input">
                                                <label class="form_title" for="default_contactus_email">{{  trans('shiledcmstheme::template.setting.contactUsEmail') }}<span aria-required="true" class="required"> * </span></label>
                                                {!! Form::text('default_contactus_email', Crypt::decrypt(Config::get('Constant.DEFAULT_CONTACTUS_EMAIL')) , array('maxlength' => '150','class' => 'form-control', 'id' => 'default_contactus_email' , 'autocomplete'=>'off')) !!}
                                            </div>
                                            <div class="form-group form-md-line-input" style="display:none;">
                                                <label class="form_title" for="default_getaestimate_email">{{  trans('shiledcmstheme::template.setting.getaEstimateEmail') }} <span aria-required="true" class="required"> * </span></label>
                                                {!! Form::text('default_getaestimate_email', Crypt::decrypt(Config::get('Constant.DEFAULT_GETESTIMATE_EMAIL')) , array('maxlength' => '150','class' => 'form-control', 'id' => 'default_requestaquote_email' , 'autocomplete'=>'off')) !!}
                                            </div>
                                            <div class="form-group form-md-line-input">
                                                <label class="form_title" for="default_serviceinquiry_email">{{  trans('shiledcmstheme::template.setting.serviceinquiryEmail') }} <span aria-required="true" class="required"> * </span></label>
                                                {!! Form::text('default_serviceinquiry_email', Crypt::decrypt(Config::get('Constant.DEFAULT_SERVICEINQUIRY_EMAIL')) , array('maxlength' => '150','class' => 'form-control', 'id' => 'default_serviceinquiry_email' , 'autocomplete'=>'off')) !!}
                                            </div>
                                            <div class="form-group form-md-line-input">
                                                <label class="form_title" for="default_boatinquiry_email">{{  trans('shiledcmstheme::template.setting.boatinquiryEmail') }} <span aria-required="true" class="required"> * </span></label>
                                                {!! Form::text('default_boatinquiry_email', Crypt::decrypt(Config::get('Constant.DEFAULT_BOATINQUIRY_EMAIL')) , array('maxlength' => '150','class' => 'form-control', 'id' => 'default_boatinquiry_email' , 'autocomplete'=>'off')) !!}
                                            </div>
                                            <div class="form-group form-md-line-input" style="display:none;">
                                                <label class="form_title" for="default_submit_ticket_email" >{{ trans('Default Submit a Ticket Email') }}</label>
                                                {!! Form::text('default_submit_ticket_email', Crypt::decrypt(Config::get('Constant.SUBMIT_TICKET')) , array('maxlength' => '150','class' => 'form-control', 'id' => 'default_submit_ticket_email' , 'autocomplete'=>'off')) !!}
                                            </div>
                                            <div class="form-group form-md-line-input" style="display:none;">
                                                <label class="form_title" for="default_feedback_email" >{{  trans('shiledcmstheme::template.setting.feedbackEmail') }}</label>
                                                {!! Form::text('default_feedback_email', Crypt::decrypt(Config::get('Constant.DEFAULT_FEEDBACK_EMAIL')) , array('maxlength' => '150','class' => 'form-control', 'id' => 'default_feedback_email' , 'autocomplete'=>'off')) !!}
                                            </div>
                                            <div class="form-group form-md-line-input" style="display:none;">
                                                <label class="form_title" for="default_newsletter_email">{{  trans('shiledcmstheme::template.setting.newsletterEmail') }} <span aria-required="true" class="required"> * </span></label>
                                                {!! Form::text('default_newsletter_email', Crypt::decrypt(Config::get('Constant.DEFAULT_NEWSLETTER_EMAIL')) , array('maxlength' => '150','class' => 'form-control', 'id' => 'default_newsletter_email' , 'autocomplete'=>'off')) !!}
                                            </div>
                                            <div class="form-group form-md-line-input">
                                                <label class="form_title" for="default_dataremoval_email">{{  trans('Default Data Removal Email') }} <span aria-required="true" class="required"> * </span></label>
                                                {!! Form::text('default_dataremoval_email', Crypt::decrypt(Config::get('Constant.DEFAULT_DATAREMOVAL_EMAIL')) , array('maxlength' => '150','class' => 'form-control', 'id' => 'default_dataremoval_email' , 'autocomplete'=>'off')) !!}
                                            </div>
                                            <button type="submit" class="btn btn-green-drake" title="{!!  trans('shiledcmstheme::template.common.saveandedit') !!}">{!!  trans('shiledcmstheme::template.common.saveandedit') !!}</button>
                                            {!! Form::close() !!}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endcan
                        @can('settings-smtp-mail-setting')
                        <div class="tab-pane {{$smtp_tab_active}}" id="smtp-mail">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="portlet-form">
                                        {!! Form::open(['method' => 'post','id'=>'smtpForm']) !!}
                                        <input type="password" style="width: 0;height: 0; visibility: hidden;position:absolute;left:0;top:0;"/>
                                        {!! Form::hidden('tab', 'smtp_settings', ['id' => 'smtp']) !!}
                                        <div class="form-body">
                                            {{-- <div class="form-actions form-group" style="padding:0">
                                                <div class="pull-left">
                                                    <button type="button" id="testSMTP" class="btn btn-green-drake">{{  trans('shiledcmstheme::template.setting.testSMTPSettings') }}</button>
                                        </div>
                                    </div> --}}

                                    <div class="form-group form-md-line-input">
                                        <label class="form_title">Use SMTP Settings:</label>
                                        @if (Config::get('Constant.USE_SMTP_SETTING') == 'Y')
                                        @php $checked_section = true; @endphp
                                        @php $display_Section = ''; @endphp
                                        @else
                                        @php $checked_section = null; 
                                        @endphp
                                        @endif
                                        {{ Form::checkbox('chrUseSMTP',null,$checked_section, array('id'=>'chrUseSMTP')) }}
                                    </div>

                                    <div class="form-group {{ $errors->has('mailer') ? ' has-error' : '' }} form-md-line-input">
                                        <label class="form_title" for="mailer">{{  trans('shiledcmstheme::template.setting.mailer') }} <span aria-required="true" class="required"> * </span></label>
                                        <select class="form-control bs-select select2" name="mailer" id="mailer" style="width: 100%;">
                                            @php $smtp_selected = '' @endphp
                                            @php $sent_mail = '' @endphp
                                            @php $mail_trap = '' @endphp
                                            @php $log = '' @endphp
                                            @if (Config::get('Constant.MAILER') == 'smtp')
                                            @php $smtp_selected = 'selected' @endphp
                                            @elseif (Config::get('Constant.MAILER') == 'log')
                                            @php $log = 'selected' @endphp
                                            @else
                                            @php $smtp_selected = '' @endphp
                                            @php $sent_mail = '' @endphp
                                            @php $mail_trap = '' @endphp
                                            @endif
                                            <option {{ $smtp_selected }} value="smtp">{{  trans('shiledcmstheme::template.setting.smtp') }}</option>
                                            <option {{ $log }} value="log">Log</option>
                                        </select>
                                        
                                    </div>
                                    <div class="form-group {{ $errors->has('smtp_server') ? ' has-error' : '' }} form-md-line-input">
                                        <label class="form_title" for="smtp_server">{{  trans('shiledcmstheme::template.setting.smtpServer') }}<span aria-required="true" class="required"> * </span></label>
                                        {!! Form::text('smtp_server', Config::get('Constant.SMTP_SERVER') , array('maxlength' => '150','class' => 'form-control maxlength-handler', 'id' => 'smtp_server' , 'autocomplete'=>'off')) !!}
                                        <span class="help-block">
                                            {{ $errors->first('smtp_server') }}
                                        </span>
                                    </div>
                                    <div class="form-group {{ $errors->has('smtp_username') ? ' has-error' : '' }} form-md-line-input">
                                        <label class="form_title" for="smtp_username">{{  trans('shiledcmstheme::template.setting.smtpUsername') }} <span aria-required="true" class="required"> * </span></label>
                                        {!! Form::text('smtp_username', Config::get('Constant.SMTP_USERNAME') , array('maxlength' => '150','class' => 'form-control maxlength-handler', 'id' => 'smtp_username' , 'autocomplete'=>'off')) !!}
                                        <span class="help-block">
                                            {{ $errors->first('smtp_username') }}
                                        </span>
                                    </div>
                                    <div class="form-group {{ $errors->has('smtp_password') ? ' has-error' : '' }} form-md-line-input">
                                        <label class="form_title" for="smtp_password">{{  trans('shiledcmstheme::template.setting.smtpPassword') }} <span aria-required="true" class="required"> * </span></label>
                                        <input type="password" maxlength="150" class="form-control maxlength-handler" name="smtp_password" id="smtp_password" value="{{Config::get('Constant.SMTP_PASSWORD') }}" autocomplete="off">
                                        <span class="help-block">
                                            {{ $errors->first('smtp_password') }}
                                        </span>
                                    </div>
                                    <div class="form-group {{ $errors->has('smtp_encryption') ? ' has-error' : '' }}">
                                        <label class="form_title" for="smtp_encryption">{{  trans('shiledcmstheme::template.setting.smtpEncryption') }}<span aria-required="true" class="required"> * </span></label>
                                        <select class="bs-select select2" name="smtp_encryption" id="smtp_encryption">
                                            @php $smtp_encryption_selected = '' @endphp
                                            @php $null_mail = '' @endphp
                                            @php $tls_mail = '' @endphp
                                            @php $ssl_mail = '' @endphp
                                            @if (Config::get('Constant.SMTP_ENCRYPTION') == 'null')
                                            @php $smtp_encryption_selected = 'selected' @endphp
                                            @elseif (Config::get('Constant.SMTP_ENCRYPTION') == 'tls')
                                            @php $tls_mail = 'selected' @endphp
                                            @elseif (Config::get('Constant.SMTP_ENCRYPTION') == 'ssl')
                                            @php $ssl_mail = 'selected' @endphp
                                            @else
                                            @php $smtp_encryption_selected = '' @endphp
                                            @php $tls_mail = '' @endphp
                                            @php $ssl_mail = '' @endphp
                                            @endif
                                            <option {{ $smtp_encryption_selected }} value="null">{{  trans('shiledcmstheme::template.setting.none') }}</option>
                                            <option {{ $tls_mail }} value="tls">{{  trans('shiledcmstheme::template.setting.tls') }}</option>
                                            <option {{ $ssl_mail }} value="ssl">{{  trans('shiledcmstheme::template.setting.ssl') }}</option>
                                        </select>
                                    </div>
                                    <div class="form-group form-md-radios">
                                        <label class="form_title control-label" for="form_control_1">{{  trans('shiledcmstheme::template.setting.smtpAuthentication') }} <span aria-required="true" class="required"> * </span></label>
                                        <div class="md-radio-inline">
                                            <div class="md-radio">
                                                <input type="radio" id="yes" name="smtp_authenticattion" value="Y" class="md-radiobtn" <?php
                                                if (Config::get('Constant.SMTP_AUTHENTICATION') == "Y") {
                                                    echo 'checked="checked"';
                                                }
                                                ?> >
                                                <label for="yes">
                                                    <span></span>
                                                    <span class="check"></span>
                                                    <span class="box"></span>{{  trans('shiledcmstheme::template.common.yes') }}
                                                </label>
                                            </div>
                                            <div class="md-radio">
                                                <input type="radio" id="no" name="smtp_authenticattion" value="N" class="md-radiobtn" <?php
                                                if (Config::get('Constant.SMTP_AUTHENTICATION') == "N") {
                                                    echo 'checked="checked"';
                                                }
                                                ?> >
                                                <label for="no">
                                                    <span></span>
                                                    <span class="check"></span>
                                                    <span class="box"></span> {{  trans('shiledcmstheme::template.common.no') }}
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group {{ $errors->has('smtp_port') ? ' has-error':'' }} form-md-line-input">
                                        <label class="form_title" for="smtp_port">{{  trans('shiledcmstheme::template.setting.smtpPort') }} <span aria-required="true" class="required"> * </span></label>
                                        {!! Form::text('smtp_port',Config::get('Constant.SMTP_PORT'), array('maxlength' => '150', 'class' => 'form-control maxlength-handler', 'id' => 'smtp_port_no')) !!}
                                        <span class="help-block">
                                            {{ $errors->first('smtp_port') }}
                                        </span>
                                    </div>
                                    <div class="form-group {{ $errors->has('smtp_sender_name') ? ' has-error' : '' }} form-md-line-input">
                                        <label class="form_title" for="smtp_sender_name">{{  trans('shiledcmstheme::template.setting.senderName') }} <span aria-required="true" class="required"> * </span></label>
                                        {!! Form::text('smtp_sender_name',Config::get('Constant.SMTP_SENDER_NAME'), array('maxlength' => '150', 'class' => 'form-control maxlength-handler', 'id' => 'smtp_sender_name','autocomplete'=>'off')) !!}
                                        <span class="help-block">
                                            {{ $errors->first('smtp_sender_name') }}
                                        </span>
                                    </div>
                                    <div class="form-group {{ $errors->has('smtp_sender_id') ? ' has-error' : '' }} form-md-line-input">
                                        <label class="form_title" for="smtp_sender_id">{{  trans('shiledcmstheme::template.setting.senderEmail') }} <span aria-required="true" class="required"> * </span></label>
                                        {!! Form::text('smtp_sender_id',Crypt::decrypt(Config::get('Constant.DEFAULT_EMAIL')), array('maxlength' => '150', 'class' => 'form-control', 'id' => 'smtp_sender_id','autocomplete'=>'off')) !!}
                                        <span class="help-block">
                                            {{ $errors->first('smtp_sender_id') }}
                                        </span>
                                    </div>
                                    {{-- <div class="form-group {{ $errors->has('mail_content') ? ' has-error' : '' }}">
                                    <label for="mail_content" class="form_title">{{  trans('shiledcmstheme::template.setting.emailSignature') }} <span aria-required="true" class="required"> * </span></label>
                                    {!! Form::textarea('mail_content' , Config::get('Constant.DEFAULT_SIGNATURE_CONTENT'), array('class' => 'form-control', 'id' => 'txtDescription')) !!}
                                    <span class="help-block">
                                        {{ $errors->first('mail_content') }}
                                    </span>
                                </div> --}}
                                <button type="submit" class="btn btn-green-drake">{!!  trans('shiledcmstheme::template.common.saveandedit') !!}</button>
                                {!! Form::close() !!}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endcan
            @can('settings-seo-setting')
            <div class="tab-pane {{$seo_tab_active}}" id="seo">
                <div class="row">
                    <div class="col-md-12">
                        <div class="portlet-form">
                            {!! Form::open(['method' => 'post','id' => 'frmSeo','enctype'=>'multipart/form-data']) !!}
                            {!! Form::hidden('tab', 'seo_settings', ['id' => 'seo']) !!}
                            <div class="form-body">
                                <div class="form-group {{ $errors->has('google_analytic_code') ? ' has-error' : '' }} form-md-line-input">
                                    <label class="form_title" for="google_analytic_code">{{  trans('shiledcmstheme::template.setting.googleAnalytic') }} </label>
                                    {!! Form::textarea('google_analytic_code' , Config::get('Constant.GOOGLE_ANALYTIC_CODE'), array('class' => 'form-control', 'id' => 'google_analytic_code','rows' => '4')) !!}
                                    <span class="help-block">
                                        {{ $errors->first('google_analytic_code') }}
                                    </span>
                                </div>
                                <div class="form-group {{ $errors->has('google_tag_manager_for_body') ? ' has-error' : '' }} form-md-line-input">
                                    <label class="form_title" for="google_tag_manager_for_body">{{  trans('shiledcmstheme::template.setting.googleTagManager') }}</label>
                                    {!! Form::textarea('google_tag_manager_for_body' , Config::get('Constant.GOOGLE_TAG_MANAGER_FOR_BODY'), array('class' => 'form-control', 'id' => 'google_tag_manager_for_body', 'rows' => '4')) !!}
                                    <span class="help-block">
                                        {{ $errors->first('google_tag_manager_for_body') }}
                                    </span>
                                </div>
                                <div class="form-group {{ $errors->has('meta_title') ? ' has-error' : '' }} form-md-line-input">
                                    <label class="form_title" for="meta_title">{{  trans('shiledcmstheme::template.common.metatitle') }} <span aria-required="true" class="required"> * </span></label>
                                    {!! Form::text('meta_title' , Config::get('Constant.DEFAULT_META_TITLE'), array('maxlength' => '150','class' => 'form-control maxlength-handler', 'id' => 'meta_title', 'autocomplete'=>"off")) !!}
                                    <span class="help-block">
                                        {{ $errors->first('meta_title') }}
                                    </span>
                                </div>
                                <div class="form-group {{ $errors->has('meta_description') ? ' has-error' : '' }} form-md-line-input">
                                    <label class="form_title" for="form_control_1">{{  trans('shiledcmstheme::template.common.metadescription') }} <span aria-required="true" class="required"> * </span></label>
                                    {!! Form::textarea('meta_description' , Config::get('Constant.DEFAULT_META_DESCRIPTION'), array('class' => 'form-control', 'id' => 'meta_description', 'rows' => '4')) !!}
                                    <span class="help-block">
                                        {{ $errors->first('meta_description') }}
                                    </span>
                                </div>
                                {{-- <div class="form-group {{ $errors->has('robotfile_content') ? ' has-error' : '' }} form-md-line-input">
                                {!! Form::textarea('robotfile_content' , $robotFileContent, array('class' => 'form-control', 'id' => 'robotfile_content', 'autocomplete'=>"off")) !!}
                                <label class="form_title" for="ROBOT_FILR">Robot TXT File Content</label>
                                <span class="help-block">
                                    {{ $errors->first('robotfile_content') }}
                                </span>
                            </div> --}}
                            <div class="form-group form-md-line-input">
                                <label class="form_title" for="BingFile">Upload Bing File</label>
                                {!! Form::file('xml_file' , array('class' => 'form-control', 'id' => 'bingfile','accept'=>"text/xml")) !!}
                                @php
                                $BingfileName = Config::get('Constant.BING_FILE_PATH');
                                @endphp
                                <div class="clearfix"></div>
                                <span>Recommended File type (.xml)</span>
                                <div class="clearfix"></div>
                                @if($BingfileName != "" || $BingfileName != null)
                                <span>File Name:{{ $BingfileName }}</span>
                                @endif
                                <span class="help-block">
                                    {{ $errors->first('xml_file') }}
                                </span>
                                <div id="xml_file_error"></div>
                            </div>
                            <div class="form-group form-md-line-input hidden">
                                <label class="form_title" for="generate_sitemap">Sitemap:&nbsp;</label>
                                <a target="_blank" href="{{url('generateSitemap')}}" class="btn default"><i class="fa fa-sitemap" aria-hidden="true"></i> Click to generate sitemap</a>
                            </div>
                            <button type="submit" class="btn btn-green-drake">{!!  trans('shiledcmstheme::template.common.saveandedit') !!}</button>
                            {!! Form::close() !!}
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endcan
        @can('settings-social-setting')
        <div class="tab-pane setting {{$social_tab_active}}" id="social">
            <div class="row">
                <div class="col-md-12">
                    <div class="portlet-form">
                        {!! Form::open(['method' => 'post','id' => 'frmSocial']) !!}
                        {!! Form::hidden('tab', 'social_settings', ['id' => 'social']) !!}
                        <div class="form-body">
                            <div class="form-group {{ $errors->has('fb_link') ? ' has-error' : '' }} form-md-line-input">
                                <div class="input-group">
                                    <span class="input-group-addon">
                                        <i class="fa fa-facebook"></i>
                                    </span>
                                    <label class="form_title" for="fb_link">{{  trans('shiledcmstheme::template.setting.facebookLink') }} </label>
                                    {!! Form::text('fb_link' , Config::get('Constant.SOCIAL_FB_LINK'), array('class' => 'form-control', 'id' => 'fb_link', 'autocomplete'=>"off")) !!}
                                    <span class="help-block">
                                        {{ $errors->first('fb_link') }}
                                    </span>
                                </div>
                            </div>
                            <div class="form-group {{ $errors->has('twitter_link') ? ' has-error' : '' }} form-md-line-input" >
                                <div class="input-group">
                                    <span class="input-group-addon">
                                        <i class="fa fa-twitter"></i>
                                    </span>
                                    <label class="form_title" for="twitter_link">{{  trans('shiledcmstheme::template.setting.twitterLink') }}</label> 
                                    {!! Form::text('twitter_link' , Config::get('Constant.SOCIAL_TWITTER_LINK'), array('class' => 'form-control', 'id' => 'twitter_link', 'autocomplete'=>"off")) !!}
                                    <span class="help-block">
                                        {{ $errors->first('twitter_link') }}
                                    </span>
                                </div>
                            </div>
                            <div class="form-group {{ $errors->has('instagram_link') ? ' has-error' : '' }} form-md-line-input" >
                                <div class="input-group">
                                    <span class="input-group-addon">
                                        <i class="fa fa-instagram"></i>
                                    </span>
                                    <label class="form_title" for="instagram_link">{{  trans('shiledcmstheme::template.setting.instagramLink') }} </label> 
                                    {!! Form::text('instagram_link' , Config::get('Constant.SOCIAL_INSTAGRAM_LINK'), array('class' => 'form-control', 'id' => 'instagram_link', 'autocomplete'=>"off")) !!}
                                    <span class="help-block">
                                        {{ $errors->first('instagram_link') }}
                                    </span>
                                </div>
                            </div>
                            <div class="form-group {{ $errors->has('pinterest_link') ? ' has-error' : '' }} form-md-line-input" >
                                <div class="input-group">
                                    <span class="input-group-addon">
                                        <i class="fa fa-pinterest"></i>
                                    </span>
                                    <label class="form_title" for="pinterest_link">{{  trans('shiledcmstheme::template.setting.pinterestLink') }} </label> 
                                    {!! Form::text('pinterest_link' , Config::get('Constant.SOCIAL_PINTEREST_LINK'), array('class' => 'form-control', 'id' => 'pinterest_link', 'autocomplete'=>"off")) !!}
                                    <span class="help-block">
                                        {{ $errors->first('pinterest_link') }}
                                    </span>
                                </div>
                            </div>
                            <div class="form-group {{ $errors->has('ecayonline_link') ? ' has-error' : '' }} form-md-line-input">
                                <div class="input-group">
                                    <span class="input-group-addon">
                                        <i class="fa fa-etsy"></i>
                                    </span>
                                    <label class="form_title" for="ecayonline_link">{{  trans('shiledcmstheme::template.setting.ecayonlineLink') }} </label> 
                                    {!! Form::text('ecayonline_link' , Config::get('Constant.SOCIAL_ECAYONLINE_LINK'), array('class' => 'form-control', 'id' => 'ecayonline_link', 'autocomplete'=>"off")) !!}
                                    <span class="help-block">
                                        {{ $errors->first('ecayonline_link') }}
                                    </span>
                                </div>
                            </div>
                            <div class="form-group {{ $errors->has('yachtworld_link') ? ' has-error' : '' }} form-md-line-input">
                                <div class="input-group">
                                    <span class="input-group-addon">
                                        <i class="fa fa-globe"></i>
                                    </span>
                                    <label class="form_title" for="yachtworld_link">{{  trans('shiledcmstheme::template.setting.yachtworldLink') }} </label> 
                                    {!! Form::text('yachtworld_link' , Config::get('Constant.SOCIAL_YACHTWORLD_LINK'), array('class' => 'form-control', 'id' => 'yachtworld_link', 'autocomplete'=>"off")) !!}
                                    <span class="help-block">
                                        {{ $errors->first('yachtworld_link') }}
                                    </span>
                                </div>
                            </div>
                            <div class="form-group {{ $errors->has('yelp_link') ? ' has-error' : '' }} form-md-line-input" style="display:none;">
                                <div class="input-group">
                                    <span class="input-group-addon">
                                        <i class="fa fa-yelp"></i>
                                    </span>
                                    <label class="form_title" for="yelp_link">{{  trans('shiledcmstheme::template.setting.yelpLink') }} </label> 
                                    {!! Form::text('yelp_link' , Config::get('Constant.SOCIAL_YELP_LINK'), array('class' => 'form-control', 'id' => 'yelp_link', 'autocomplete'=>"off")) !!}
                                    <span class="help-block">
                                        {{ $errors->first('yelp_link') }}
                                    </span>
                                </div>
                            </div>
                            <div class="form-group {{ $errors->has('trip_advisor_link') ? ' has-error' : '' }} form-md-line-input" style="display:none;">
                                <div class="input-group">
                                    <span class="input-group-addon">
                                        <i class="fa fa-tripadvisor"></i>
                                    </span>
                                    <label class="form_title" for="youtube_link">{{  trans('shiledcmstheme::template.setting.tripadvisorlink') }}</label> ( <a href="javascript:;" class="config" data-placement="bottom" data-original-title="" title="Please add your Trip Advisor page link (eg: https://www.tripadvisor.com/your_page)"><i class="fa fa-info"></i></a> )
                                    {!! Form::text('trip_advisor_link' , Config::get('Constant.SOCIAL_TRIP_ADVISOR_LINK'), array('class' => 'form-control', 'id' => 'trip_advisor_link', 'autocomplete'=>"off")) !!}
                                    <span class="help-block">
                                        {{ $errors->first('trip_advisor_link') }}
                                    </span>
                                </div>
                            </div>
                            <div class="form-group {{ $errors->has('youtube_link') ? ' has-error' : '' }} form-md-line-input" style="display:none;">
                                <div class="input-group">
                                    <span class="input-group-addon">
                                        <i class="fa fa-youtube-play"></i>
                                    </span>
                                    <label class="form_title" for="youtube_link">{{  trans('shiledcmstheme::template.setting.youtubeLink') }}</label> ( <a href="javascript:;" class="config" data-placement="bottom" data-original-title="" title="Please add your Youtube page link (eg: https://www.youtube.com/your_page"><i class="fa fa-info"></i></a> )
                                    {!! Form::text('youtube_link' , Config::get('Constant.SOCIAL_YOUTUBE_LINK'), array('class' => 'form-control', 'id' => 'youtube_link', 'autocomplete'=>"off")) !!}
                                    <span class="help-block">
                                        {{ $errors->first('youtube_link') }}
                                    </span>
                                </div>
                            </div>
                            <button type="submit" class="btn btn-green-drake submit">{!!  trans('shiledcmstheme::template.common.saveandedit') !!}</button>
                            {!! Form::close() !!}
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endcan
        @can('settings-social-media-share-setting')
        <div class="tab-pane setting {{$social_share_tab_active}}" id="socialshare">
            <div class="row">
                <div class="col-md-12">
                    <div class="portlet-form">
                        {!! Form::open(['method' => 'post','id' => 'frmSocialShare']) !!}
                        {!! Form::hidden('tab', 'social_share_settings', ['id' => 'socialshare']) !!}
                        <div class="form-body">
                            <label><i class="fa fa-check"></i> {{  trans('shiledcmstheme::template.setting.facebookShare') }}</label>
                            <div class="form-group {{ $errors->has('fb_id') ? ' has-error' : '' }} form-md-line-input">
                                <div class="input-group">
                                    <span class="input-group-addon">
                                        <i class="fa fa-facebook"></i>
                                    </span>
                                    <label class="form_title" for="fb_id">{{  trans('shiledcmstheme::template.setting.facebookPageID') }} <span aria-required="true" class="required"> * </span></label>
                                    {!! Form::text('fb_id' , Config::get('Constant.SOCIAL_SHARE_FB_ID'), array('class' => 'form-control', 'id' => 'fb_id', 'autocomplete'=>"off", 'onkeypress' => "return isNumberKey(event)")) !!}
                                    <span class="help-block">
                                        {{ $errors->first('fb_id') }}
                                    </span>
                                </div>
                            </div>
                            <div class="form-group {{ $errors->has('fb_api') ? ' has-error' : '' }} form-md-line-input">
                                <div class="input-group">
                                    <span class="input-group-addon">
                                        <i class="fa fa-facebook"></i>
                                    </span>
                                    <label class="form_title" for="fb_api">{{  trans('shiledcmstheme::template.setting.facebookApiKey') }} <span aria-required="true" class="required"> * </span></label>
                                    {!! Form::text('fb_api' , Config::get('Constant.SOCIAL_SHARE_FB_API_KEY'), array('class' => 'form-control', 'id' => 'fb_api', 'autocomplete'=>"off")) !!}
                                    <span class="help-block">
                                        {{ $errors->first('fb_api') }}
                                    </span>
                                </div>
                            </div>
                            <div class="form-group {{ $errors->has('fb_secret_key') ? ' has-error' : '' }} form-md-line-input">
                                <div class="input-group">
                                    <span class="input-group-addon">
                                        <i class="fa fa-facebook"></i>
                                    </span>
                                    <label class="form_title" for="fb_secret_key">{{  trans('shiledcmstheme::template.setting.facebookSecretKey') }} <span aria-required="true" class="required"> * </span></label>
                                    {!! Form::text('fb_secret_key' , Config::get('Constant.SOCIAL_SHARE_FB_SECRET_KEY'), array('class' => 'form-control', 'id' => 'fb_secret_key', 'autocomplete'=>"off")) !!}
                                    <span class="help-block">
                                        {{ $errors->first('fb_secret_key') }}
                                    </span>
                                </div>
                            </div>
                            <div class="form-group {{ $errors->has('fb_access_token') ? ' has-error' : '' }} form-md-line-input">
                                <div class="input-group">
                                    <span class="input-group-addon">
                                        <i class="fa fa-facebook"></i>
                                    </span>
                                    <label class="form_title" for="fb_access_token">{{  trans('shiledcmstheme::template.setting.facebookAccessToken') }} <span aria-required="true" class="required"> * </span></label>
                                    {!! Form::text('fb_access_token' , Config::get('Constant.SOCIAL_SHARE_FB_ACCESS_TOKEN'), array('class' => 'form-control', 'id' => 'fb_access_token', 'autocomplete'=>"off")) !!}
                                    <span class="help-block">
                                        {{ $errors->first('fb_access_token') }}
                                    </span>
                                </div>
                            </div>
                            <div class="clearfix"></div>
                            <label><i class="fa fa-check"></i> {{  trans('shiledcmstheme::template.setting.twitterShare') }}</label>
                            <div class="form-group {{ $errors->has('twitter_api') ? ' has-error' : '' }} form-md-line-input">
                                <div class="input-group">
                                    <span class="input-group-addon">
                                        <i class="fa fa-twitter"></i>
                                    </span>
                                    <label class="form_title" for="twitter_api">{{  trans('shiledcmstheme::template.setting.twitterApiKey') }} <span aria-required="true" class="required"> * </span></label>
                                    {!! Form::text('twitter_api' , Config::get('Constant.SOCIAL_SHARE_TWITTER_API_KEY'), array('class' => 'form-control', 'id' => 'twitter_api', 'autocomplete'=>"off")) !!}
                                    <span class="help-block">
                                        {{ $errors->first('twitter_api') }}
                                    </span>
                                </div>
                            </div>
                            <div class="form-group {{ $errors->has('twitter_secret_key') ? ' has-error' : '' }} form-md-line-input">
                                <div class="input-group">
                                    <span class="input-group-addon">
                                        <i class="fa fa-twitter"></i>
                                    </span>
                                    <label class="form_title" for="twitter_secret_key">{{  trans('shiledcmstheme::template.setting.twitterSecretKey') }} <span aria-required="true" class="required"> * </span></label>
                                    {!! Form::text('twitter_secret_key' , Config::get('Constant.SOCIAL_SHARE_TWITTER_SECRET_KEY'), array('class' => 'form-control', 'id' => 'twitter_secret_key', 'autocomplete'=>"off")) !!}
                                    <span class="help-block">
                                        {{ $errors->first('twitter_secret_key') }}
                                    </span>
                                </div>
                            </div>
                            <div class="form-group {{ $errors->has('twitter_access_token') ? ' has-error' : '' }} form-md-line-input">
                                <div class="input-group">
                                    <span class="input-group-addon">
                                        <i class="fa fa-twitter"></i>
                                    </span>
                                    <label class="form_title" for="twitter_access_token">{{  trans('shiledcmstheme::template.setting.twitterAccessToken') }} <span aria-required="true" class="required"> * </span></label>
                                    {!! Form::text('twitter_access_token' , Config::get('Constant.SOCIAL_SHARE_TWITTER_ACCESS_TOKEN'), array('class' => 'form-control', 'id' => 'twitter_access_token', 'autocomplete'=>"off")) !!}
                                    <span class="help-block">
                                        {{ $errors->first('twitter_access_token') }}
                                    </span>
                                </div>
                            </div>
                            <div class="form-group {{ $errors->has('twitter_access_token_key') ? 'has-error' : '' }} form-md-line-input">
                                <div class="input-group">
                                    <span class="input-group-addon">
                                        <i class="fa fa-twitter"></i>
                                    </span>
                                    <label class="form_title" for="twitter_access_token_key">{{  trans('shiledcmstheme::template.setting.twitterAccessTokenSceretKey') }} <span aria-required="true" class="required"> * </span></label>
                                    {!! Form::text('twitter_access_token_key' , Config::get('Constant.SOCIAL_SHARE_TWITTER_ACCESS_SECRET_KEY'), array('class' => 'form-control', 'id' => 'twitter_access_token_key', 'autocomplete'=>"off")) !!}
                                    
                                    <span class="help-block">
                                        {{ $errors->first('twitter_access_token_key') }}
                                    </span>
                                </div>
                            </div>
                            <div class="clearfix"></div>
                            <label><i class="fa fa-check"></i> {{  trans('shiledcmstheme::template.setting.linkedinShare') }}</label>
                            <div class="form-group {{ $errors->has('linkedin_api') ? ' has-error' : '' }} form-md-line-input">
                                <div class="input-group">
                                    <span class="input-group-addon">
                                        <i class="fa fa-linkedin"></i>
                                    </span>
                                    <label class="form_title" for="linkedin_api">{{  trans('shiledcmstheme::template.setting.linkedinApiKey') }} <span aria-required="true" class="required"> * </span></label>
                                    {!! Form::text('linkedin_api' , Config::get('Constant.SOCIAL_SHARE_LINKEDIN_API_KEY'), array('class' => 'form-control', 'id' => 'linkedin_api', 'autocomplete'=>"off")) !!}
                                    <span class="help-block">
                                        {{ $errors->first('linkedin_api') }}
                                    </span>
                                </div>
                            </div>
                            <div class="form-group {{ $errors->has('linkedin_secret_key') ? ' has-error' : '' }} form-md-line-input">
                                <div class="input-group">
                                    <span class="input-group-addon">
                                        <i class="fa fa-linkedin"></i>
                                    </span>
                                    <label class="form_title" for="linkedin_secret_key">{{  trans('shiledcmstheme::template.setting.linkedinSecretKey') }} <span aria-required="true" class="required"> * </span></label>
                                    {!! Form::text('linkedin_secret_key' , Config::get('Constant.SOCIAL_SHARE_LINKEDIN_SECRET_KEY'), array('class' => 'form-control', 'id' => 'linkedin_secret_key', 'autocomplete'=>"off")) !!}
                                    <span class="help-block">
                                        {{ $errors->first('linkedin_secret_key') }}
                                    </span>
                                </div>
                            </div>
                            <div class="form-group {{ $errors->has('linkedin_access_token') ? ' has-error' : '' }} form-md-line-input">
                                <div class="input-group">
                                    <span class="input-group-addon">
                                        <i class="fa fa-linkedin"></i>
                                    </span>
                                    <label class="form_title" for="linkedin_access_token">{{  trans('shiledcmstheme::template.setting.linkedinAccessToken') }} <span aria-required="true" class="required"> * </span></label>
                                    {!! Form::text('linkedin_access_token' , Config::get('Constant.SOCIAL_SHARE_LINKEDIN_ACCESS_TOKEN'), array('class' => 'form-control', 'id' => 'linkedin_access_token', 'autocomplete'=>"off")) !!}
                                    <span class="help-block">
                                        {{ $errors->first('linkedin_access_token') }}
                                    </span>
                                </div>
                            </div>
                            <div class="form-group {{ $errors->has('linkedin_access_token_key') ? ' has-error' : '' }} form-md-line-input">
                                <div class="input-group">
                                    <span class="input-group-addon">
                                        <i class="fa fa-linkedin"></i>
                                    </span>
                                    <label class="form_title" for="linkedin_access_token_key">{{  trans('shiledcmstheme::template.setting.linkedinAccessTokenSceretKey') }} <span aria-required="true" class="required"> * </span></label>
                                    {!! Form::text('linkedin_access_token_key' , Config::get('Constant.SOCIAL_SHARE_LINKEDIN_ACCESS_SECRET_KEY'), array('class' => 'form-control', 'id' => 'linkedin_access_token_key', 'autocomplete'=>"off")) !!}
                                    <span class="help-block">
                                        {{ $errors->first('linkedin_access_token_key') }}
                                    </span>
                                </div>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-green-drake submit">{!!  trans('shiledcmstheme::template.common.saveandedit') !!}</button>
                        {!! Form::close() !!}
                    </div>
                </div>
            </div>
        </div>
        @endcan
        @can('settings-other-setting')
        <div class="tab-pane setting {{$other_tab_active}}" id="other">
            <div class="row">
                <div class="col-md-12">
                    <div class="portlet-form">
                        {!! Form::open(['method' => 'post','id' => 'otherSettings']) !!}
                        {!! Form::hidden('tab', 'other_settings', ['id' => 'other']) !!}
                        <div class="form-body">
                            {{-- @php
										<div class="form-group">
											<label class="form_title" for="default_page_size">{{  trans('shiledcmstheme::template.setting.defaultPagesize') }}</label>
                            <select class="form-control select2" name="default_page_size" id="default_page_size" style="width:100%">
                                @php $ten_selected = '' @endphp
                                @php $twenty_selected = '' @endphp
                                @php $fifty_selected = '' @endphp
                                @php $hundred_selected = '' @endphp
                                @php $all_selected = '' @endphp
                                @if (Config::get('Constant.DEFAULT_PAGE_SIZE')  == '10')
                                @php $ten_selected = 'selected' @endphp
                                @elseif (Config::get('Constant.DEFAULT_PAGE_SIZE') == '20')
                                @php $twenty_selected = 'selected' @endphp
                                @elseif (Config::get('Constant.DEFAULT_PAGE_SIZE') == '50')
                                @php $fifty_selected = 'selected' @endphp
                                @elseif (Config::get('Constant.DEFAULT_PAGE_SIZE') == '100')
                                @php $hundred_selected = 'selected' @endphp
                                @elseif (Config::get('Constant.DEFAULT_PAGE_SIZE') == 'All')
                                @php $all_selected = 'selected' @endphp
                                @else
                                @php $ten_selected = '' @endphp
                                @php $twenty_selected = '' @endphp
                                @php $fifty_selected = '' @endphp
                                @php $hundred_selected = '' @endphp
                                @php $all_selected = '' @endphp
                                @endif
                                <option {{ $ten_selected }} value="10">10</option>
                                <option {{ $twenty_selected }} value="20">20</option>
                                <option {{ $fifty_selected }} value="50">50</option>
                                <option {{ $hundred_selected }} value="100">100</option>
                                <option {{ $all_selected }} value="All">{{  trans('shiledcmstheme::template.common.all') }}</option>
                            </select>
                        </div> --}}
                        <div class="clearfix"></div>
                        <div class="form-group form-md-line-input">
                            <label class="form_title" for="default_date_format">{{  trans('shiledcmstheme::template.common.defaultDateFormat') }} (d/m/Y)</label>
                            <select class="form-control bs-select select2" name="default_date_format" id="default_date_format" style="width:100%">
                                <option value="d/m/Y" <?php
                                if (Config::get('Constant.DEFAULT_DATE_FORMAT') == "d/m/Y") {
                                    echo 'selected="selected"';
                                }
                                ?> >d/m/Y (Eg: {{ Carbon\Carbon::today()->format('d/m/Y') }})  </option>
                                <option value="m/d/Y" <?php
                                if (Config::get('Constant.DEFAULT_DATE_FORMAT') == "m/d/Y") {
                                    echo 'selected="selected"';
                                }
                                ?> >m/d/Y (Eg: {{ Carbon\Carbon::today()->format('m/d/Y') }})  </option>
                                <option value="Y/m/d" <?php
                                if (Config::get('Constant.DEFAULT_DATE_FORMAT') == "Y/m/d") {
                                    echo 'selected="selected"';
                                }
                                ?> >Y/m/d (Eg: {{ Carbon\Carbon::today()->format('Y/m/d') }})  </option>
                                <option value="Y/d/m" <?php
                                if (Config::get('Constant.DEFAULT_DATE_FORMAT') == "Y/d/m") {
                                    echo 'selected="selected"';
                                }
                                ?> >Y/d/m (Eg: {{ Carbon\Carbon::today()->format('Y/d/m') }})  </option>
                                <option value="M/d/Y" <?php
                                if (Config::get('Constant.DEFAULT_DATE_FORMAT') == "M/d/Y") {
                                    echo 'selected="selected"';
                                }
                                ?> >M/d/Y (Eg: {{ Carbon\Carbon::today()->format('M/d/Y') }})  </option>
                                <option value="M d Y" <?php
                                if (Config::get('Constant.DEFAULT_DATE_FORMAT') == "M d Y") {
                                    echo 'selected="selected"';
                                }
                                ?> >M d Y (Eg: {{ Carbon\Carbon::today()->format('M d Y') }})  </option>
                            </select>
                            
                        </div>
                        <div class="form-group form-md-line-input">
                             <label class="form_title" for="time_format">{{  trans('shiledcmstheme::template.common.defaultTimeFormat') }}</label>
                            <select class="form-control bs-select select2" name="time_format" id="time_format" style="width:100%">
                                <option value="h:i A" <?php
                                if (Config::get('Constant.DEFAULT_TIME_FORMAT') == "h:i A") {
                                    echo 'selected="selected"';
                                }
                                ?> >12 {{  trans('shiledcmstheme::template.common.hours') }}</option>
                                <option value="H:i" <?php
                                if (Config::get('Constant.DEFAULT_TIME_FORMAT') == "H:i") {
                                    echo 'selected="selected"';
                                }
                                ?> >24 {{  trans('shiledcmstheme::template.common.hours') }}</option>
                            </select>
                           
                        </div>
                        <div class="form-group {{ $errors->has('google_map_key') ? ' has-error' : '' }} form-md-line-input">
                            <label class="form_title" for="google_map_key">{{  trans('shiledcmstheme::template.setting.googleMapKey') }} <span aria-required="true" class="required"> * </span></label>
                            {!! Form::text('google_map_key' , Config::get('Constant.GOOGLE_MAP_KEY'), array('class' => 'form-control', 'id' => 'google_map_key', 'autocomplete'=>"off")) !!}
                            <span class="help-block">
                                {{ $errors->first('google_map_key') }}
                            </span>
                        </div>
                        <div class="form-group {{ $errors->has('google_capcha_key') ? ' has-error' : '' }} form-md-line-input">
                            <label class="form_title" for="google_map_key">{{  trans('shiledcmstheme::template.setting.googleCapchaKey') }}  <span aria-required="true" class="required"> * </span></label>
                            {!! Form::text('google_capcha_key' ,!empty(Config::get('Constant.GOOGLE_CAPCHA_KEY'))?Config::get('Constant.GOOGLE_CAPCHA_KEY'):'', array('class' => 'form-control', 'id' => 'google_capcha_key', 'autocomplete'=>"off")) !!}
                            <span class="help-block">
                                {{ $errors->first('google_capcha_key') }}
                            </span>
                        </div>
                        <div class="form-group {{ $errors->has('google_capcha_secret') ? ' has-error' : '' }} form-md-line-input">
                            <label class="form_title" for="google_map_key">{{  trans('Google Capcha Secret Key') }}  <span aria-required="true" class="required"> * </span></label>
                            {!! Form::text('google_capcha_secret' ,!empty(Config::get('Constant.GOOGLE_CAPTCHA_SECRET'))?Config::get('Constant.GOOGLE_CAPTCHA_SECRET'):'', array('class' => 'form-control', 'id' => 'google_capcha_secret', 'autocomplete'=>"off")) !!}
                            <span class="help-block">
                                {{ $errors->first('google_capcha_secret') }}
                            </span>
                        </div>
                        @if (Config::get('Constant.DEFAULT_AUTHENTICATION') == 'Y')
                        <div class="form-group {{ $errors->has('Authentication_Time') ? ' has-error' : '' }} form-md-line-input">
                            <label class="form_title" for="Authentication_Time">Authentication Time (Minute)</label>
                            {!! Form::text('Authentication_Time' ,!empty(Config::get('Constant.DEFAULT_Authentication_TIME'))?Config::get('Constant.DEFAULT_Authentication_TIME'):'', array('class' => 'form-control', 'id' => 'Authentication_Time', 'autocomplete'=>"off")) !!}
                            <span class="help-block">
                                {{ $errors->first('Authentication_Time') }}
                            </span>
                        </div>
                        @endif
                        <div class="form-group">
                            <label for="banner_type" class="form_title">{{  trans('shiledcmstheme::template.setting.filterBadWords') }}:</label>
                            <div class="md-radio-inline">
                                <div class="md-radio">
                                    @if ((!empty(Config::get('Constant.BAD_WORDS')) && Config::get('Constant.BAD_WORDS') == 'Y') || (null == old('bad_words') || old('bad_words') == 'Y'))
                                    @php  $checked_yes = 'checked'  @endphp
                                    @else
                                    @php  $checked_yes = ''  @endphp
                                    @endif
                                    <input type="radio" {{ $checked_yes }} value="Y" id="badWordsYes" name="bad_words" class="md-radiobtn">
                                    <label for="badWordsYes">
                                        <span class="inc"></span>
                                        <span class="check"></span>
                                        <span class="box"></span> {{  trans('shiledcmstheme::template.common.yes') }}
                                    </label>
                                </div>
                                <div class="md-radio">
                                    @if (Config::get('Constant.BAD_WORDS') == 'N' || (!empty(Config::get('Constant.BAD_WORDS')) && Config::get('Constant.BAD_WORDS') == 'N'))
                                    @php  $checked_yes = 'checked'  @endphp
                                    @else
                                    @php  $checked_yes = ''  @endphp
                                    @endif
                                    <input type="radio" {{ $checked_yes }} value="N" id="badWordsNo" name="bad_words" class="md-radiobtn">
                                    <label for="badWordsNo">
                                        <span class="inc"></span>
                                        <span class="check"></span>
                                        <span class="box"></span> {{  trans('shiledcmstheme::template.common.no') }}
                                    </label>
                                </div>
                            </div>
                        </div>
                        {{-- <div class="form-group {{ $errors->has('php_ini_content') ? ' has-error' : '' }} form-md-line-input">
                        {!! Form::textarea('php_ini_content' , $phpIniContent, array('class' => 'form-control', 'id' => 'php_ini_content', 'autocomplete'=>"off")) !!}
                        <label class="form_title" for="PHP_INI_CONTENT">{{  trans('shiledcmstheme::template.setting.phpIniSettings') }}</label>
                        <span class="help-block">
                            {{ $errors->first('php_ini_content') }}
                        </span>
                    </div> --}}
                    <!-- <div class="row" style="margin-bottom: 10px;">																															<div class="col-md-6 col-sm-6 col-xs-12">																																																									<label class="form_title" for="AVAILABLE_SOCIAL_LINKS_FOR_TEAM_MEMEBER">Available Social Links For Team Member</label>																																																	</div>
                    <div class="col-md-6 col-sm-6 col-xs-12 text-right">
                                            <a href="javascript:void(0);" class="addMoreSocial add_more" title="Add More"><i class="fa fa-plus"></i> Add Social Link</a>
                                    </div>
                                    <div class="clearfix"></div>
                                    <div class="multi_social_links">
                                            @php
                                            $socialcnt=0;
                                            $selectedSocialLinks=unserialize(Config::get('Constant.AVAILABLE_SOCIAL_LINKS_FOR_TEAM_MEMBER'));
                                            @endphp
                                            @if(is_array($selectedSocialLinks) && count($selectedSocialLinks)>0 &&!empty($selectedSocialLinks))
                                            @foreach($selectedSocialLinks as $socialLinks)
                    
                                            <div class="single_social_link">
                                                    <div class="col-md-4">
                                                            <div class="form-group {{ $errors->has('AVAILABLE_SOCIAL_LINKS_FOR_TEAM_MEMEBER') ? ' has-error' : '' }} form-md-line-input">
                                                                    {!! Form::text('available_social_links_for_team['.($socialcnt).'][title]' , $socialLinks['title'], array('class' => 'form-control', 'id' => 'available_social_links_for_team'.($socialcnt).'_1', 'autocomplete'=>"off")) !!}
                                                                    <label class="form_title" for="AVAILABLE_SOCIAL_LINKS_FOR_TEAM_MEMEBER">Title</label>
                                                            </div>
                                                    </div>
                                                    <div class="col-md-4">
                                                            <div class="form-group {{ $errors->has('AVAILABLE_SOCIAL_LINKS_FOR_TEAM_MEMEBER') ? ' has-error' : '' }} form-md-line-input">
                                                                    {!! Form::text('available_social_links_for_team['.($socialcnt).'][placeholder]' , $socialLinks['placeholder'], array('class' => 'form-control', 'id' => 'available_social_links_for_team'.($socialcnt).'_2', 'autocomplete'=>"off")) !!}
                                                                    <label class="form_title" for="AVAILABLE_SOCIAL_LINKS_FOR_TEAM_MEMEBER">Place Holder</label>
                                                            </div>
                                                    </div>
                                                    <div class="col-md-4">
                                                            <div class="form-group {{ $errors->has('AVAILABLE_SOCIAL_LINKS_FOR_TEAM_MEMEBER') ? ' has-error' : '' }} form-md-line-input">
                                                                    {!! Form::text('available_social_links_for_team['.($socialcnt).'][class]', $socialLinks['class'], array('class' => 'form-control', 'id' => 'available_social_links_for_team'.($socialcnt).'_3', 'autocomplete'=>"off")) !!}
                                                                    <label class="form_title" for="AVAILABLE_SOCIAL_LINKS_FOR_TEAM_MEMEBER">Class</label>
                                                                    <a href="javascript:void(0);" class="removeSocial add_more" title="Remove"><i class="fa fa-times"></i> Remove</a>
                                                            </div>
                                                    </div>
                                            </div>
                                            @php $socialcnt++; @endphp
                                            @endforeach
                                            @endif
                                    </div>
                                                                                                    </div>-->
                    <button type="submit" class="btn btn-green-drake submit">{!!  trans('shiledcmstheme::template.common.saveandedit') !!}</button>
                    {!! Form::close() !!}
                </div>
            </div>
        </div>
    </div>
</div>
@endcan


@can('settings-security-setting')
<div class="tab-pane setting {{$security_tab_active}}" id="security">
    <div class="row">
        <div class="col-md-12">
            <div class="portlet-form">
                {!! Form::open(['method' => 'post','id' => 'securitySettings']) !!}
                {!! Form::hidden('tab', 'security_settings', ['id' => 'security']) !!}
                <div class="form-body">
                    <div class="form-group {{ $errors->has('max_login_attempts') ? ' has-error' : '' }} form-md-line-input">
                        <label class="form_title" for="max_login_attempts">{{  trans('shiledcmstheme::template.setting.maxloginattempts') }}  <span aria-required="true" class="required"> * </span></label>
                        {!! Form::text('max_login_attempts' ,!empty(Config::get('Constant.MAX_LOGIN_ATTEMPTS'))?Config::get('Constant.MAX_LOGIN_ATTEMPTS'):'', array('class' => 'form-control', 'id' => 'max_login_attempts', 'autocomplete'=>"off", 'maxlength'=>"3", 'onkeypress'=>"javascript: return KeycheckOnlyAmount(event);",'onpaste'=>'return false')) !!}
                        <span class="help-block">
                            {{ $errors->first('max_login_attempts') }}
                        </span>
                    </div>
                    <div class="form-group {{ $errors->has('retry_time_period') ? ' has-error' : '' }} form-md-line-input">
                        <label class="form_title" for="retry_time_period">{{  trans('shiledcmstheme::template.setting.retrytimeperiod') }}  <span aria-required="true" class="required"> * </span></label>
                        {!! Form::text('retry_time_period' ,!empty(Config::get('Constant.RETRY_TIME_PERIOD'))?Config::get('Constant.RETRY_TIME_PERIOD'):'', array('class' => 'form-control', 'id' => 'retry_time_period', 'autocomplete'=>"off", 'maxlength'=>"3", 'onkeypress'=>"javascript: return KeycheckOnlyAmount(event);",'onpaste'=>'return false')) !!}
                        <span class="help-block">
                            {{ $errors->first('retry_time_period') }}
                        </span>
                    </div>
                    <div class="form-group {{ $errors->has('lockout_time') ? ' has-error' : '' }} form-md-line-input">
                        <label class="form_title" for="lockout_time">{{  trans('shiledcmstheme::template.setting.lockouttime') }}  <span aria-required="true" class="required"> * </span></label>
                        {!! Form::text('lockout_time' ,!empty(Config::get('Constant.LOCKOUT_TIME'))?Config::get('Constant.LOCKOUT_TIME'):'', array('class' => 'form-control', 'id' => 'lockout_time', 'autocomplete'=>"off", 'maxlength'=>"3", 'onkeypress'=>"javascript: return KeycheckOnlyAmount(event);",'onpaste'=>'return false')) !!}
                        <span class="help-block">
                            {{ $errors->first('lockout_time') }}
                        </span>
                    </div>
                    <div class="form-group {{ $errors->has('ip_setting') ? ' has-error' : '' }} form-md-line-input">
                        <label class="form_title" for="ip_setting">{{  trans('shiledcmstheme::template.setting.IPsetting') }} </label>
                        {!! Form::textarea('ip_setting' , !empty(Config::get('Constant.IP_SETTING'))?Config::get('Constant.IP_SETTING'):'', array('class' => 'form-control', 'id' => 'ip_setting','rows' => '4', 'onkeypress'=>"javascript: return KeycheckOnlyAmount(event);")) !!}
                        <span class="help-block">
                            {{ $errors->first('ip_setting') }}
                        </span>
                        </br>
                        <p style="color: #000000;font-size: 10;font-weight: bold;">
                            <!--Note: You can enter multiple IP addresses separated by commas (e.g: 115.42.150.37,192.168.0.1,110.234.52.124).-->
                            Note: You can enter multiple IP addresses separated by commas (e.g: 115.42.150.37,192.168.0.1,110.234.52.124) who will access the PowerPanel.
                        </p>
                    </div>
                    <button type="submit" class="btn btn-green-drake submit">{!!  trans('shiledcmstheme::template.common.saveandedit') !!}</button>
                    {!! Form::close() !!}
                </div>
            </div>
        </div>
    </div>
</div>
@endcan


@can('settings-cron-setting')
<div class="tab-pane setting {{$cron_tab_active}}" id="cron">
    <div class="row">
        <div class="col-md-12">
            <div class="portlet-form">
                {!! Form::open(['method' => 'post','id' => 'cronSettings']) !!}
                {!! Form::hidden('tab', 'cron_settings', ['id' => 'cron']) !!}
                <div class="form-body">
                    <div class="form-group {{ $errors->has('log_remove_time') ? ' has-error' : '' }} form-md-line-input">
                        <label class="form_title" for="log_remove_time">{{  trans('shiledcmstheme::template.setting.logremove') }}  <span aria-required="true" class="required"> * </span></label>
                        {!! Form::text('log_remove_time' ,!empty(Config::get('Constant.LOG_REMOVE_TIME'))?Config::get('Constant.LOG_REMOVE_TIME'):'', array('class' => 'form-control', 'id' => 'log_remove_time', 'autocomplete'=>"off", 'maxlength'=>"2", 'onkeypress'=>"javascript: return KeycheckOnlyAmount(event);",'onpaste'=>'return false')) !!}
                        <span class="help-block">
                            {{ $errors->first('log_remove_time') }}
                        </span>
                    </div>
                    <button type="submit" class="btn btn-green-drake submit">{!!  trans('shiledcmstheme::template.common.saveandedit') !!}</button>
                    {!! Form::close() !!}
                </div>
            </div>
        </div>
    </div>
</div>
@endcan

@can('settings-magic-setting')
<div class="tab-pane setting {{$magic_tab_active}}" id="magic">
    <div class="row">
        <div class="col-md-12">
            <div class="portlet-form">
                {!! Form::open(['method' => 'post','id' => 'MagicSettings']) !!}
                {!! Form::hidden('tab', 'magic_settings', ['id' => 'magic']) !!}
                <div class="form-body">

                    <div class="form-group {{ $errors->has('Magic_Receive_Email') ? ' has-error' : '' }} form-md-line-input">
                        <label class="form_title" for="Magic_Receive_Email">Your Website Email<span aria-required="true" class="required"> * </span></label>
                        ( <a href="javascript:;" class="config" data-placement="bottom" data-original-title="" title="Whatever content you want to publish instantaly, please email those content on the email address"><i class="fa fa-info"></i></a> )
                        {!! Form::text('Magic_Receive_Email' ,!empty(Config::get('Constant.Magic_Receive_Email'))?Config::get('Constant.Magic_Receive_Email'):'', array('class' => 'form-control maxlength-handler', 'id' => 'Magic_Receive_Email', 'autocomplete'=>"off")) !!}
                        <span class="help-block">
                            {{ $errors->first('Magic_Receive_Email') }}
                        </span>
                    </div>

                    <div class="form-group {{ $errors->has('Magic_Receive_Password') ? ' has-error' : '' }} form-md-line-input">
                        <label class="form_title" for="Magic_Receive_Password">Your Website Email Password  <span aria-required="true" class="required"> * </span></label>
                    	<input type="password" class="form-control maxlength-handler" id="Magic_Receive_Password" name="Magic_Receive_Password" value="{{ !empty(Config::get('Constant.Magic_Receive_Password'))?Config::get('Constant.Magic_Receive_Password'):''}}"  autocomplete="off">
                        <span class="help-block">
                            {{ $errors->first('Magic_Receive_Password') }}
                        </span>
                    </div>

                    <div class="form-group {{ $errors->has('Magic_Send_Email') ? ' has-error' : '' }} form-md-line-input">
                        <label class="form_title" for="Magic_Send_Email">Assigned Email(s)<span aria-required="true" class="required"> * </span></label>
                        ( <a href="javascript:;" class="config" data-placement="bottom" data-original-title="" title="The content will be published if email will come from assigned email address. Email should be comma seprated if it's multiple"><i class="fa fa-info"></i></a> )
                        {!! Form::text('Magic_Send_Email' ,!empty(Config::get('Constant.Magic_Send_Email'))?Config::get('Constant.Magic_Send_Email'):'', array('class' => 'form-control maxlength-handler', 'id' => 'Magic_Send_Email', 'autocomplete'=>"off")) !!}
                        <span class="help-block">
                            {{ $errors->first('Magic_Send_Email') }}
                        </span>
                    </div>

                    <div class="form-group">
                        <label class="form_title" for="publish_content_module">Select Module to publish content <span aria-required="true" class="required"> * </span></label>
                        ( <a href="javascript:;" class="config" data-placement="bottom" data-original-title="" title="The content will be published as new record in selected module"><i class="fa fa-info"></i></a> )
                        <select class="form-control bs-select select2" name="publish_content_module" id="publish_content_module" style="width: 100%">
                            @foreach ($frontModuleList as $key => $value)
                                @php  $selected = ''  @endphp
                                @if($value['id'] == Config::get('Constant.PUBLISH_CONTENT_MODULE'))
                                    @php  $selected = 'selected'  @endphp
                                @endif
                                <option {{ $selected }} value="{{ $value['id'] }}">{{ ucwords($value['varTitle']) }}</option>
                            @endforeach    
                        </select>
                    </div>

                    <div class="note note-info">
                        <b>Note: </b> Email subject will be published as title and email content will published as content on page.
                    </div>              
                    
                    <!-- <div class="form-group {{ $errors->has('Magic_Auth_Password') ? ' has-error' : '' }} form-md-line-input">
                        <label class="form_title" for="Magic_Auth_Password">Magic Upload Authentication Password <small>(Email Subject-*-AUTHPASS)</small> <span aria-required="true" class="required"> * </span></label>
                        {!! Form::text('Magic_Auth_Password' ,!empty(Config::get('Constant.Magic_Auth_Password'))?Config::get('Constant.Magic_Auth_Password'):'', array('class' => 'form-control maxlength-handler', 'id' => 'Magic_Auth_Password', 'autocomplete'=>"off")) !!}
                        <span class="help-block">
                            {{ $errors->first('Magic_Auth_Password') }}
                        </span>
                    </div> -->

                    <button type="submit" class="btn btn-green-drake submit">{!!  trans('shiledcmstheme::template.common.saveandedit') !!}</button>
                    {!! Form::close() !!}
                </div>
            </div>
        </div>
    </div>
</div>
@endcan

@can('settings-maintenancenew-setting')
<div class="tab-pane setting {{$maintenancenew_tab_active}}" id="maintenancenew">
    <div class="row">
        <div class="col-md-12">
            <div class="portlet-form">
                {!! Form::open(['method' => 'post','id' => 'MaintenancenewSettings']) !!}
                {!! Form::hidden('tab', 'maintenancenew_settings', ['id' => 'maintenancenew']) !!}
                <div class="form-body">
                    <div class="form-group {{ $errors->has('Maintenancenew_Send_Email') ? ' has-error' : '' }} form-md-line-input">
                        <label class="form_title" for="Maintenancenew_Send_Email">Payment Type<span aria-required="true" class="required"> * </span></label>
                        <select class="form-control bs-select select2" name="paymenttype" id="paymenttype" style="width: 100%;">
                                        @php $paymenttype_selected = '' @endphp
                                            @php $M_type = '' @endphp
                                            @php $Y_type = '' @endphp
                                            @if (Config::get('Constant.paymenttype') == 'null')
                                            @php $paymenttype_selected = 'selected' @endphp
                                            @elseif (Config::get('Constant.paymenttype') == 'Y')
                                            @php $Y_type = 'selected' @endphp
                                            @elseif (Config::get('Constant.paymenttype') == 'M')
                                            @php $M_type = 'selected' @endphp
                                            @else
                                            @php $paymenttype_selected = '' @endphp
                                            @php $M_type = '' @endphp
                                            @php $Y_type = '' @endphp
                                            @endif
                                           <option   {{$paymenttype_selected}} value="">{{  trans('shiledcmstheme::template.setting.none') }}</option>
                                            <option {{$M_type}} value="M">Monthly</option>
                                            <option  {{$Y_type}} value="Y">Yearly</option>
                                        </select>
                        <span class="help-block">
                            {{ $errors->first('Maintenancenew_paymenttype') }}
                        </span>
                    </div>
                    <div class="form-group {{ $errors->has('Maintenancenew_Hour') ? ' has-error' : '' }} form-md-line-input">
                        <label class="form_title" for="Maintenancenew_Hour">Hour<span aria-required="true" class="required"> * </span></label>
                        {!! Form::text('Maintenancenew_Hour' ,!empty(Config::get('Constant.Maintenancenew_Hour'))?Config::get('Constant.Maintenancenew_Hour'):'', array('class' => 'form-control maxlength-handler', 'id' => 'Maintenancenew_Hour', 'autocomplete'=>"off",'onkeypress' => 'javascript: return KeycheckOnlyHour(event);','maxlength'=>'5')) !!}
                        <span class="help-block">
                            {{ $errors->first('Maintenancenew_Hour') }}
                        </span>
                    </div>
                    <div class="form-group form-md-line-input">
                       <ul class="checkbox_listing clearfix">
                            <li class="col-md-4">
                                <label class="checkbox">
                                       @if (Config::get('Constant.extebdmonth') == 'Y')
                                        @php $checked_section = true; @endphp
                                        @php $display_Section = ''; @endphp
                                        @else
                                        @php $checked_section = null; 
                                        @endphp
                                        @endif
                                        {{ Form::checkbox('extebdmonth','Y',$checked_section, array('id'=>'extebdmonth')) }}
                                    <span class="check"></span>
                                   If monthly maintenance hours extends then send email.
                                </label>
                            </li>
                        </ul>
                    </div>
                    <div class="form-group {{ $errors->has('Maintenancenew_Rep_Send_Email') ? ' has-error' : '' }} form-md-line-input">
                        <label class="form_title" for="Maintenancenew_Rep_Send_Email">Reporting Email Address  <span aria-required="true" class="required"> * </span></label>
                        {!! Form::text('Maintenancenew_Rep_Send_Email' ,!empty(Config::get('Constant.Maintenancenew_Rep_Send_Email'))?Config::get('Constant.Maintenancenew_Rep_Send_Email'):'', array('class' => 'form-control maxlength-handler', 'id' => 'Maintenancenew_Rep_Send_Email', 'autocomplete'=>"off")) !!}
                        <span class="help-block">
                            {{ $errors->first('Maintenancenew_Rep_Send_Email') }}
                        </span>
                    </div>
                    
                    <button type="submit" class="btn btn-green-drake submit">{!!  trans('shiledcmstheme::template.common.saveandedit') !!}</button>
                    {!! Form::close() !!}
                </div>
            </div>
        </div>
    </div>
</div>
@endcan

@can('settings-features-setting')
<div class="tab-pane setting {{$features_tab_active}}" id="features">
    <div class="row">
        <div class="col-md-12">
            <div class="portlet-form">
                {!! Form::open(['method' => 'post','id' => 'featuresSettings']) !!}
                {!! Form::hidden('tab', 'features_settings', ['id' => 'features']) !!}
                <div class="form-body">
                    <div class="form-group">
                        <ul class="clearfix features_switch_list">
                            <li>
                                <label>
                                    @if (Config::get('Constant.DEFAULT_DRAFT') == 'Y')
                                    @php $checked_Draft = 'checked'; @endphp
                                    @else
                                    @php $checked_Draft = null; 
                                    @endphp
                                    @endif
                                    <span class="checked_off_on title_checked">
                                        {{ Form::checkbox('chrDraft',null,$checked_Draft, array('class' => 'make-switch switch-large', 'id' => 'chrDraft', 'data-label-icon' => 'fa fa-fullscreen', 'data-on-text' => 'Yes', 'data-off-text' => 'No')) }}
                                    </span>
                                    Draft
                                </label>
                            </li>
                            <li>
                                <label>
                                    @if (Config::get('Constant.DEFAULT_TRASH') == 'Y')
                                    @php $checked_Trash = 'checked'; @endphp
                                    @else
                                    @php $checked_Trash = null; 
                                    @endphp
                                    @endif
                                    <span class="checked_off_on title_checked">
                                        {{ Form::checkbox('chrTrash',null,$checked_Trash, array('class' => 'make-switch switch-large', 'id' => 'chrTrash', 'data-label-icon' => 'fa fa-fullscreen', 'data-on-text' => 'Yes', 'data-off-text' => 'No')) }}
                                    </span>
                                    Trash / Restore
                                </label>
                            </li>
                            <li>
                                <label>
                                    @if (Config::get('Constant.DEFAULT_QUICK') == 'Y')
                                    @php $checked_Quick = 'checked'; @endphp
                                    @else
                                    @php $checked_Quick = null; 
                                    @endphp
                                    @endif
                                    <span class="checked_off_on title_checked">
                                        {{ Form::checkbox('chrQuick',null,$checked_Quick, array('class' => 'make-switch switch-large', 'id' => 'chrQuick', 'data-label-icon' => 'fa fa-fullscreen', 'data-on-text' => 'Yes', 'data-off-text' => 'No')) }}
                                    </span>
                                    Quick Edit
                                </label>
                            </li>
                            <li>
                                <label>
                                    @if (Config::get('Constant.DEFAULT_DUPLICATE') == 'Y')
                                    @php $checked_Duplicate = 'checked'; @endphp
                                    @else
                                    @php $checked_Duplicate = null; 
                                    @endphp
                                    @endif
                                    <span class="checked_off_on title_checked">
                                        {{ Form::checkbox('chrDuplicate',null,$checked_Duplicate, array('class' => 'make-switch switch-large', 'id' => 'chrDuplicate', 'data-label-icon' => 'fa fa-fullscreen', 'data-on-text' => 'Yes', 'data-off-text' => 'No')) }}
                                    </span>
                                    Duplicate
                                </label>
                            </li>
                            <li>
                                <label>
                                    @if (Config::get('Constant.DEFAULT_VISIBILITY') == 'Y')
                                    @php $checked_Visibility = 'checked'; @endphp
                                    @else
                                    @php $checked_Visibility = null; 
                                    @endphp
                                    @endif
                                    <span class="checked_off_on title_checked">
                                        {{ Form::checkbox('chrVisibility',null,$checked_Visibility, array('class' => 'make-switch switch-large', 'id' => 'chrVisibility', 'data-label-icon' => 'fa fa-fullscreen', 'data-on-text' => 'Yes', 'data-off-text' => 'No')) }}
                                    </span>
                                    Visibility (Public, Private, Password Protected)
                                </label>
                            </li>
                            <li>
                                <label>
                                    @if (Config::get('Constant.DEFAULT_VISUAL') == 'Y')
                                    @php $checked_Visual = 'checked'; @endphp
                                    @else
                                    @php $checked_Visual = null; 
                                    @endphp
                                    @endif
                                    <span class="checked_off_on title_checked">
                                        {{ Form::checkbox('chrVisual',null,$checked_Visual, array('class' => 'make-switch switch-large', 'id' => 'chrVisual', 'data-label-icon' => 'fa fa-fullscreen', 'data-on-text' => 'Yes', 'data-off-text' => 'No')) }}
                                    </span>
                                    Visual Composer
                                </label>
                            </li>
                            <li>
                                <label>
                                    @if (Config::get('Constant.DEFAULT_FAVORITE') == 'Y')
                                    @php $checked_Favorite = 'checked'; @endphp
                                    @else
                                    @php $checked_Favorite = null; 
                                    @endphp
                                    @endif
                                    <span class="checked_off_on title_checked">
                                        {{ Form::checkbox('chrFavorite',null,$checked_Favorite, array('class' => 'make-switch switch-large', 'id' => 'chrFavorite', 'data-label-icon' => 'fa fa-fullscreen', 'data-on-text' => 'Yes', 'data-off-text' => 'No')) }}
                                    </span>
                                    Favorite
                                </label>
                            </li>
                            <li>
                                <label>
                                    @if (Config::get('Constant.DEFAULT_ARCHIVE') == 'Y')
                                    @php $checked_Archive = 'checked'; @endphp
                                    @else
                                    @php $checked_Archive = null; 
                                    @endphp
                                    @endif
                                    <span class="checked_off_on title_checked">
                                        {{ Form::checkbox('chrArchive',null,$checked_Archive, array('class' => 'make-switch switch-large', 'id' => 'chrArchive', 'data-label-icon' => 'fa fa-fullscreen', 'data-on-text' => 'Yes', 'data-off-text' => 'No')) }}
                                    </span>
                                    Archive
                                </label>
                            </li>
                            <li>
                                <label>
                                    @if (Config::get('Constant.DEFAULT_FORMBUILDER') == 'Y')
                                    @php $checked_Formbuilder = 'checked'; @endphp
                                    @else
                                    @php $checked_Formbuilder = null; 
                                    @endphp
                                    @endif
                                    <span class="checked_off_on title_checked">
                                        {{ Form::checkbox('chrFormbuilder',null,$checked_Formbuilder, array('class' => 'make-switch switch-large', 'id' => 'chrFormbuilder', 'data-label-icon' => 'fa fa-fullscreen', 'data-on-text' => 'Yes', 'data-off-text' => 'No')) }}
                                    </span>
                                    Form Builder
                                </label>
                            </li>
                            <li>
                                <label>
                                    @if (Config::get('Constant.DEFAULT_PAGETEMPLATE') == 'Y')
                                    @php $checked_PageTemplate = 'checked'; @endphp
                                    @else
                                    @php $checked_PageTemplate = null; 
                                    @endphp
                                    @endif
                                    <span class="checked_off_on title_checked">
                                        {{ Form::checkbox('chrPageTemplate',null,$checked_PageTemplate, array('class' => 'make-switch switch-large', 'id' => 'chrPageTemplate', 'data-label-icon' => 'fa fa-fullscreen', 'data-on-text' => 'Yes', 'data-off-text' => 'No')) }}
                                    </span>
                                    Page Template
                                </label>
                            </li>
                            <li>
                                <label>
                                    @if (Config::get('Constant.DEFAULT_SPELLCHCEK') == 'Y')
                                    @php $checked_SpellChcek = 'checked'; @endphp
                                    @else
                                    @php $checked_SpellChcek = null; 
                                    @endphp
                                    @endif
                                    <span class="checked_off_on title_checked">
                                        {{ Form::checkbox('chrSpellChcek',null,$checked_SpellChcek, array('class' => 'make-switch switch-large', 'id' => 'chrSpellChcek', 'data-label-icon' => 'fa fa-fullscreen', 'data-on-text' => 'Yes', 'data-off-text' => 'No')) }}
                                    </span>
                                    Spell Check
                                </label>
                            </li>
                            <li>
                                <label>
                                    @if (Config::get('Constant.DEFAULT_MESSAGINGSYSTEM') == 'Y')
                                    @php $checked_MessagingSystem = 'checked'; @endphp
                                    @else
                                    @php $checked_MessagingSystem = null; 
                                    @endphp
                                    @endif
                                    <span class="checked_off_on title_checked">
                                        {{ Form::checkbox('chrMessagingSystem',null,$checked_MessagingSystem, array('class' => 'make-switch switch-large', 'id' => 'chrMessagingSystem', 'data-label-icon' => 'fa fa-fullscreen', 'data-on-text' => 'Yes', 'data-off-text' => 'No')) }}
                                    </span>
                                    Messaging System
                                </label>
                            </li>
                            <li>
                                <label>
                                    @if (Config::get('Constant.DEFAULT_CONTENTLOCK') == 'Y')
                                    @php $checked_ContentLock = 'checked'; @endphp
                                    @else
                                    @php $checked_ContentLock = null; 
                                    @endphp
                                    @endif
                                    <span class="checked_off_on title_checked">
                                        {{ Form::checkbox('chrContentLock',null,$checked_ContentLock, array('class' => 'make-switch switch-large', 'id' => 'chrContentLock', 'data-label-icon' => 'fa fa-fullscreen', 'data-on-text' => 'Yes', 'data-off-text' => 'No')) }}
                                    </span>
                                    Content Lock
                                </label>
                            </li>
                            <li>
                                <label>
                                    @if (Config::get('Constant.DEFAULT_AUDIO') == 'Y')
                                    @php $checked_Audio = 'checked'; @endphp
                                    @else
                                    @php $checked_Audio = null; 
                                    @endphp
                                    @endif
                                    <span class="checked_off_on title_checked">
                                        {{ Form::checkbox('chrAudio',null,$checked_Audio, array('class' => 'make-switch switch-large', 'id' => 'chrAudio', 'data-label-icon' => 'fa fa-fullscreen', 'data-on-text' => 'Yes', 'data-off-text' => 'No')) }}
                                    </span>
                                    Audio
                                </label>
                            </li>
                            
                            <li>
                                <label>
                                    @if (Config::get('Constant.DEFAULT_AUTHENTICATION') == 'Y')
                                    @php $checked_Authentication = 'checked'; @endphp
                                    @else
                                    @php $checked_Authentication = null; 
                                    @endphp
                                    @endif
                                    <span class="checked_off_on title_checked">
                                        {{ Form::checkbox('chrAuthentication',null,$checked_Authentication, array('class' => 'make-switch switch-large', 'id' => 'chrAuthentication', 'data-label-icon' => 'fa fa-fullscreen', 'data-on-text' => 'Yes', 'data-off-text' => 'No')) }}
                                    </span>
                                    Two Factor Authentication
                                </label>
                            </li>
                            <li>
                                <label>
                                    @if (Config::get('Constant.DEFAULT_FEEDBACKFORM') == 'Y')
                                    @php $checked_Feedbackform = 'checked'; @endphp
                                    @else
                                    @php $checked_Feedbackform = null; 
                                    @endphp
                                    @endif
                                    <span class="checked_off_on title_checked">
                                        {{ Form::checkbox('chrFrontFeedbackForm',null,$checked_Feedbackform, array('class' => 'make-switch switch-large', 'id' => 'chrFrontFeedbackForm', 'data-label-icon' => 'fa fa-fullscreen', 'data-on-text' => 'Yes', 'data-off-text' => 'No')) }}
                                    </span>
                                    Feedback Form
                                </label>
                            </li>
                            <li>
                                <label>
                                    @if (Config::get('Constant.DEFAULT_ONLINEPOLLINGFORM') == 'Y')
                                    @php $checked_OnlinePollingform = 'checked'; @endphp
                                    @else
                                    @php $checked_OnlinePollingform = null; 
                                    @endphp
                                    @endif
                                    <span class="checked_off_on title_checked">
                                        {{ Form::checkbox('chrOnlinePollingForm',null,$checked_OnlinePollingform, array('class' => 'make-switch switch-large', 'id' => 'chrOnlinePollingForm', 'data-label-icon' => 'fa fa-fullscreen', 'data-on-text' => 'Yes', 'data-off-text' => 'No')) }}
                                    </span>
                                    Online Polling
                                </label>
                            </li>
                            <li>
                                <label>
                                    @if (Config::get('Constant.DEFAULT_SHARINGOPTION') == 'Y')
                                    @php $checked_SharingOption = 'checked'; @endphp
                                    @else
                                    @php $checked_SharingOption = null; 
                                    @endphp
                                    @endif
                                    <span class="checked_off_on title_checked">
                                        {{ Form::checkbox('chrSharingOption',null,$checked_SharingOption, array('class' => 'make-switch switch-large', 'id' => 'chrSharingOption', 'data-label-icon' => 'fa fa-fullscreen', 'data-on-text' => 'Yes', 'data-off-text' => 'No')) }}
                                    </span>
                                    Sharing Option
                                </label>
                            </li>
                            <li>
                                <label>
                                    @if (Config::get('Constant.DEFAULT_EMAILTOFRIENDOPTION') == 'Y')
                                    @php $checked_EmailtofriendOption = 'checked'; @endphp
                                    @else
                                    @php $checked_EmailtofriendOption = null; 
                                    @endphp
                                    @endif
                                    <span class="checked_off_on title_checked">
                                        {{ Form::checkbox('chrEmailtofriendOption',null,$checked_EmailtofriendOption, array('class' => 'make-switch switch-large', 'id' => 'chrEmailtofriendOption', 'data-label-icon' => 'fa fa-fullscreen', 'data-on-text' => 'Yes', 'data-off-text' => 'No')) }}
                                    </span>
                                    Email To Friend
                                </label>
                            </li>
                        </ul>
                    </div>
                    <hr>
                    <button type="submit" class="btn btn-green-drake submit">{!!  trans('shiledcmstheme::template.common.saveandedit') !!}</button>
                    {!! Form::close() !!}
                </div>
            </div>
        </div>
    </div>
</div>
@endcan

@can('settings-maintenance-setting')
<div class="tab-pane setting {{$maintenance_tab_active}}" id="maintenance">
    <div class="row">
        <div class="col-md-12">
            <div class="portlet-form">
                {!! Form::open(['method' => 'post','id' => 'frmMaintenance']) !!}
                {!! Form::hidden('tab', 'maintenance', ['id' => 'maintenance']) !!}
                <div class="form-body">
                    <div class="form-group hidden">
                        <label><i class="fa fa-refresh"></i> {{  trans('shiledcmstheme::template.setting.resetCounter') }}</label>
                        <a href="{{url('powerpanel/settings/getDBbackUp')}}"><i class="fa fa-hdd-o" aria-hidden="true"></i> Database Backup</a>
                    </div>
                    <div class="form-group">
                        <div class="checkbox-list-validation">
                        <ul class="checkbox_listing clearfix">
                            <li class="col-md-4">
                                <label class="checkbox">
                                    {!! Form::checkbox('reset[]', 'moblihits') !!}
                                    <span class="check"></span>
                                    {{  trans('shiledcmstheme::template.setting.resetMobileHits') }}
                                </label>
                            </li>
                            <li class="col-md-4">
                                <label class="checkbox">
                                    {!! Form::checkbox('reset[]', 'emaillog') !!}
                                    <span class="check"></span>
                                    {{  trans('shiledcmstheme::template.setting.resetEmailLogs') }}
                                </label>
                            </li>
                            <li class="col-md-4">
                                <label class="checkbox">
                                    {!! Form::checkbox('reset[]', 'webhits') !!}
                                    <span class="check"></span>
                                    {{  trans('shiledcmstheme::template.setting.resetWebHits') }}

                                </label>
                            </li>
                            <li class="col-md-4">
                                <label class="checkbox">
                                    {!! Form::checkbox('reset[]', 'contactleads') !!}
                                    <span class="check"></span>
                                    {{  trans('shiledcmstheme::template.setting.resetContactLeads') }}
                                </label>
                            </li>
                            <li class="col-md-4" style="display:none;">
                                <label class="checkbox">
                                    {!! Form::checkbox('reset[]', 'getaestimateleads') !!}
                                    <span class="check"></span>
                                    {{  trans('shiledcmstheme::template.setting.resetGetaEstimateLeads') }}
                                </label>
                            </li>
                            <li class="col-md-4">
                                <label class="checkbox">
                                    {!! Form::checkbox('reset[]', 'serviceinquiryleads') !!}
                                    <span class="check"></span>
                                    {{  trans('shiledcmstheme::template.setting.resetServiceInquiryLeads') }}
                                </label>
                            </li>
                            <li class="col-md-4">
                                <label class="checkbox">
                                    {!! Form::checkbox('reset[]', 'boatinquiryleads') !!}
                                    <span class="check"></span>
                                    {{  trans('shiledcmstheme::template.setting.resetBoatInquiryLeads') }}
                                </label>
                            </li>
                            <li class="col-md-4" style="display: none;">
                                <label class="checkbox">
                                    {!! Form::checkbox('reset[]', 'newsletterleads') !!}
                                    <span class="check"></span>
                                    {{  trans('shiledcmstheme::template.setting.resetNewsletterLeads') }}
                                </label>
                            </li>
                            <li class="col-md-4">
                                <label class="checkbox">
                                    {!! Form::checkbox('reset[]', 'dataremovalleads') !!}
                                    <span class="check"></span>
                                    {{  trans('Reset Data Removal Leads') }}
                                </label>
                            </li>
                            <!--                                        <li class="col-md-4">
                                                                        <label class="checkbox">
                                                                            {!! Form::checkbox('reset[]', 'viewcache') !!}
                                                                            <span class="check"></span>
                                                                            All View Cache
                                                                        </label>
                                                                    </li>-->
                            <li class="col-md-4">
                                <label class="checkbox">
                                    {!! Form::checkbox('reset[]', 'flushAllCache') !!}
                                    <span class="check"></span>
                                    Flush All Cache
                                </label>
                            </li>
                        </ul>
                        </div>
                        <span class="help-block">
                            {{ $errors->first('reset') }}
                        </span>
                    </div>
                    <hr>
                    <button type="submit" class="btn btn-green-drake submit">{{  trans('shiledcmstheme::template.common.reset') }}</button>
                    {!! Form::close() !!}
                </div>
            </div>
        </div>
    </div>
</div>
@endcan

@can('settings-module-setting')
<div class="tab-pane setting {{$module_tab_active}}" id="modulesettings">
    <div class="row">
        <div class="col-md-12">
            <div class="portlet-form">
                <div class="col-md-6">
                    {!! Form::text('search' , null, array('id' => 'moduleSearch', 'class' => 'form-control', 'placeholder'=>'Module Search', 'autocomplete'=>"off")) !!}
                </div>
                <div class="col-md-2">
                    <a href="javascript:;" class="btn btn-green-drake search-module-settings submit"><i class="fa fa-search"></i></a>
                    <a href="javascript:;" class="btn btn-green-drake modulewisesettings submit"><i class="fa fa-refresh"></i></a>
                </div><br/><br/><br/>
                <div class="clearfix"></div>
                <div id='moduleDiv'></div>
            </div>
        </div>
    </div>
</div>
@endcan

</div>
</div>
</div>
</div>
</div>
</div>
@endsection
@section('scripts')
<script type="text/javascript">window.site_url = '{!! url("/") !!}';</script>
<script src="{{ $CDN_PATH.'resources/pages/scripts/setting.js' }}" type="text/javascript"></script>
@include('powerpanel.partials.ckeditor',['config'=>'docsConfig'])
<script src="{{ $CDN_PATH.'resources/global/plugins/bootstrap-maxlength/bootstrap-maxlength.min.js' }}" type="text/javascript"></script>
<script type="text/javascript">
    function isNumberKey(evt) {
        var charCode = (evt.which) ? evt.which : event.keyCode
        if (charCode > 31 && (charCode < 48 || charCode > 57))
            return false;
        return true;
    }
    $(document).ready(function () {
        $('#timezone').select2({
            placeholder: "Select timezone",
            width: '100%'
        }).on("change", function (e) {
            $("#timezone").closest('.has-error').removeClass('has-error');
            $("#timezone-error").remove();
        });

        $('#mailer').select2({
            placeholder: "Select mailer",
            width: '100%'
        }).on("change", function (e) {
            $("#mailer").closest('.has-error').removeClass('has-error');
            $("#mailer-error").remove();
        });
        $('#default_page_size').select2({
            placeholder: "Select default page size",
            width: '100%'
        }).on("change", function (e) {
            $("#default_page_size").closest('.has-error').removeClass('has-error');
            $("#default_page_size-error").remove();
        });
        $('#default_date_format').select2({
            placeholder: "Select default date format",
            width: '100%'
        }).on("change", function (e) {
            $("#default_date_format").closest('.has-error').removeClass('has-error');
            $("#default_date_format-error").remove();
        });
        $('#time_format').select2({
            placeholder: "Select default time format",
            width: '100%'
        }).on("change", function (e) {
            $("#time_format").closest('.has-error').removeClass('has-error');
            $("#time_format-error").remove();
        });

        $('#publish_content_module').select2({
            placeholder: "Select Module",
            width: '100%'
        }).on("change", function (e) {
            $("#publish_content_module").closest('.has-error').removeClass('has-error');
            $("#publish_content_module-error").remove();
        });


    });</script>
<script type="text/javascript">
    function getAttributes(val)
    {
        if (val == 'other') {
            document.getElementById("second_tab").click();
        }
        if (val == 'other' || val == 'security' || val == 'cron' || val == 'features' || val == 'magic') {
            $('.tab_section_setting').css('display', 'block');
        } else {
            $('.tab_section_setting').css('display', 'none');
        }
    }
</script>
<script type="text/javascript">
    value = "{{$tab_value}}"
    $(document).ready(function () {
        if (value == 'other_settings' || value == 'security_settings' || value == 'cron_settings' || value == 'features_settings' || value == 'magic_settings') {
            $('.tab_section_setting').css('display', 'block');
            //$("#one_tab").addClass("active");
        }
    });

</script>
@endsection