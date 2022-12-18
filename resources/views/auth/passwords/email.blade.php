@extends('powerpanel.layouts.app_login')
<!-- Main Content -->
@section('content')
<div class="login-content">
  <div class="login-bg hidden-lg visible-sm">
    <a href="https://www.netclues.com/in" class="logo">
        <img src="https://www.hometech.ky/resources/images/netclues-logo.png" alt="netclues-logo">
    </a>
</div>
	<h1>
    <div class="login_logo">
      <img src="{{ App\Helpers\resize_image::resize(Config::get('Constant.FRONT_LOGO_ID')) }}" alt="{{ Config::get('Constant.SITE_NAME') }}">
    </div>
  </h1>
	<h3 class="text-center log-title">{!! trans('template.forgotPwd.forgotpassword') !!} ?</h3>
	<?php /*<p class="content_center"> {!! trans('template.forgotPwd.enteremailandpassword') !!}. </p>*/ ?>

	<form class="form-horizontal login-form forgotpwd" role="form" method="POST" action="{{ url('/powerpanel/password/email') }}">
  <div class="width_set"> 
		{{ csrf_field() }}
		@if(Session::has('status'))
      <div class="alert alert-success">
        <button type="button" class="close" data-dismiss="alert" aria-hidden="true"></button>
        {{ Session::get('status') }}
      </div>
    @endif
		<div class="form-group">
      <span class=" icons">
        <svg xmlns="http://www.w3.org/2000/svg" version="1.2" baseProfile="tiny-ps" viewBox="0 0 20 16" width="20" height="16">
            <defs>
                <image width="20" height="16" id="img1" href="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABQAAAAQAQMAAAAs1s1YAAAAAXNSR0IB2cksfwAAAANQTFRFAAAAp3o92gAAAAF0Uk5TAEDm2GYAAAAMSURBVHicY2CgDAAAAEAAAbc0fO8AAAAASUVORK5CYII="></image>
                <image width="20" height="16" id="img2" href="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABQAAAAQCAMAAAAhxq8pAAAAAXNSR0IB2cksfwAAAGBQTFRFMoL/MoL/MoL/MoL/MoL/MoL/MoL/MoL/MoL/MoL/MoL/MoL/MoL/MoL/MoL/MoL/MoL/MoL/MoL/MoL/MoL/MoL/MoL/MoL/MoL/MoL/MoL/MoL/MoL/MoL/MoL/MoL/rMnRFgAAACB0Uk5TV+z/yjQQNsv4fAYAB35t69Y9P9fqXBOenBJeJr28JAEH+ZAHAAAAZklEQVR4nKXQRw6AMAwEwLB0TO+d//8SSEyESG7sxdLI3oOFcPCJI0y7FXA9/5UgBC5EFCfESbMIEvMCZaWsbtB2Ev1+AMaJiKdC3pj54kHVxd0aiZZ121XxC+k4yESd/2h9iO11JxHxCWmhUp/LAAAAAElFTkSuQmCC"></image>
            </defs>
            <style>
                tspan { white-space:pre }
            </style>
            <use id="Background" href="#img1" x="0" y="0"></use>
            <use id="Layer 1" href="#img2" x="0" y="0"></use>
        </svg>
    </span>
		  <input class="form-control placeholder-no-fix form-group {{ $errors->has('email') ? ' has-error' : '' }}" type="text" placeholder="{!! trans('template.frontLogin.email') !!}" name="email" autocomplete="off"> 
      @if ($errors->has('email'))
          <span class="help-block">{{ $errors->first('email') }}</span>
      @endif
		</div>
    <div class="login-content">
      <p class="content_center">{!! trans('template.forgotPwd.note') !!}: {!! trans('template.forgotPwd.forgotmailsent') !!}.</p>
    </div>
    <div class="row">
      <div class="col-sm-6 text-center">
        <div class="forgot-password">
          <button type="submit" title="    {!! trans('template.common.submit') !!}" class="btn blue">
            {!! trans('template.common.submit') !!}
          </button>
        </div>
      <?php  /*<a class="btn blue mobile_full" href="{{ url('/powerpanel') }}">{!! trans('template.forgotPwd.login') !!}</a>*/ ?>
      </div>
      <div class="col-sm-6 text-center">
          <a class="btn blue" title="{!! trans('template.forgotPwd.login') !!}" href="{{ url('/powerpanel') }}">
            {!! trans('template.forgotPwd.login') !!}
          </a>
      </div>
    </div>
    </div>
  </form>
</div> 
@endsection

@section('scripts')
<script src="{{ url('resources/pages/scripts/forgotpwd_validation.js') }}" type="text/javascript"></script>
@endsection