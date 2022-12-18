@extends('powerpanel.layouts.app')
@section('title')
{{Config::get('Constant.SITE_NAME')}} - PowerPanel
@endsection
@section('content')
@include('powerpanel.partials.breadcrumbs')
<div class="col-md-12">
    @if(Session::has('message'))
    <div class="alert alert-success">
        <button class="close" data-close="alert"></button>
        {{ Session::get('message') }}
    </div>
    @endif
    @if(Session::has('error'))
    <div class="row">
	    <div class="alert alert-danger display-hide" style="display: block;">
	        <button class="close" data-close="alert"></button>
	        {{ Session::get('error') }}
	    </div>
  	</div>
    @endif
    <div class="row">
        <div class="portlet light bdisplay_ordered" style="overflow:visible;">
            <div class="portlet-body form_pattern">
                <div class="tabbable tabbable-tabdrop">
                    <div class="tab-content">
                        {!! Form::open(['method' => 'post','id'=>'changePassword']) !!}
                        <input type="password" style="width: 0;height: 0; visibility: hidden;position:absolute;left:0;top:0;"/>
                        <div class="form-body">
                            <div class="form-group {{ $errors->has('old_password') ? ' has-error' : '' }} form-md-line-input">
                                <label class="form_title" for="old_password">{{  trans('shiledcmstheme::template.forgotPwd.oldpassword') }} <span aria-required="true" class="required"> * </span></label>
                                {!! Form::password('old_password', array('autocomplete' => 'off', 'placeholder'=> 'Old Password', 'maxlength'=>20,'class' => 'form-control')) !!}
                                <span class="help-block">
                                    {{ $errors->first('old_password') }}		
                                </span>
                            </div>
                            <div class="form-group {{ $errors->has('new_password') ? ' has-error' : '' }} form-md-line-input">
                                <label class="form_title" for="new_password">{{  trans('shiledcmstheme::template.forgotPwd.newpassword') }} <span aria-required="true" class="required"> * </span></label>
                                {!! Form::password('new_password', array('autocomplete' => 'off', 'placeholder'=> 'New Password', 'maxlength'=>20,'class' => 'form-control','id'=>'newpassword')) !!}
                                <span class="help-block">
                                    {{ $errors->first('new_password') }}		
                                </span>
                                <div class="pswd_info" id="newpassword_info">
                                    <h4>Password must meet the following requirements:</h4>
                                    <ul>
                                        <li id="letter" class="letterinfo invalid">At least <strong>one letter</strong></li>
                                        <li id="capital" class="capitalletterinfo invalid">At least <strong>one capital letter</strong></li>
                                        <li id="number" class="numberinfo invalid">At least <strong>one number</strong></li>
                                        <li id="length" class="lengthInfo invalid">Password should be <strong>6 to 20 characters</strong></li>
                                        <li id="special" class="specialinfo invalid">At least <strong>one special character</strong></li>
                                    </ul>
                                </div>
                            </div>
                            <div class="form-group {{ $errors->has('confirm_password') ? ' has-error' : '' }} form-md-line-input">
                                <label class="form_title" for="confirm_password">{{  trans('shiledcmstheme::template.forgotPwd.confirmpassword') }} <span aria-required="true" class="required"> * </span></label>
                                {!! Form::password('confirm_password', array('autocomplete' => 'off', 'placeholder'=>'Confirm Password', 'maxlength'=>20,'class' => 'form-control','id'=>'confirmpasswword')) !!}
                                <span class="help-block">
                                    {{ $errors->first('confirm_password') }}		
                                </span>
                                <div class="pswd_info" id="confirmpasswword_info">
                                    <h4>Password must meet the following requirements:</h4>
                                    <ul>
                                        <li id="letter" class="letterinfo invalid">At least <strong>one letter</strong></li>
                                        <li id="capital" class="capitalletterinfo invalid">At least <strong>one capital letter</strong></li>
                                        <li id="number" class="numberinfo invalid">At least <strong>one number</strong></li>
                                        <li id="length" class="lengthInfo invalid">Password should be <strong>6 to 20 characters</strong></li>
                                        <li id="special" class="specialinfo invalid">At least <strong>one special character</strong></li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <div class="form-actions">
                            <div class="row">
                                <div class="col-md-12">
                                    <button type="submit" name="save-settings" class="btn btn-green-drake" value="Update Password">{{  trans('shiledcmstheme::template.forgotPwd.updatepassword') }}</button>
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
@endsection
@section('scripts')
<script src="{{ Config::get('Constant.CDN_PATH').'resources/pages/scripts/packages/profile/change_password.js' }}" type="text/javascript"></script>
<script src="{{ Config::get('Constant.CDN_PATH').'resources/pages/scripts/password_rules.js' }}" type="text/javascript"></script>
@endsection