@extends('powerpanel.layouts.app_login')
@section('content')
<div class="login-bg hidden-lg visible-sm">
    <a href="https://www.netclues.com/in" class="logo">
        <img src="https://www.hometech.ky/resources/images/netclues-logo.png" alt="netclues-logo">
    </a>
</div>
<div class="login-content login_form"> 
	<h1>
		<div class="login_logo">
			<img src="{{ App\Helpers\resize_image::resize(Config::get('Constant.FRONT_LOGO_ID')) }}" alt="{{ Config::get('Constant.SITE_NAME') }}">
		</div>
	</h1>
	<h3 class="text-center log-title">Login</h3>
	<?php /*<p class="content_center">
		 {!! trans('template.frontLogin.frontcmspp') !!}
	</p>*/?>
	<form class="form-horizontal login-form" role="form" method="POST" action="{{ url('/powerpanel/login') }}">
		<input type="password" style="width: 0;height: 0; visibility: hidden;position:absolute;left:0;top:0;"/>
		<div class="width_set">
			@if(Session::has('message'))
				<div class="alert alert-info fade in">
					<button type="button" class="close" data-dismiss="alert" aria-hidden="true"></button>
					{{ Session::get('message') }}
				</div>
			@endif
			@if(isset($expiredToken))
				<div class="alert alert-danger fade in">	
					<button type="button" class="close" data-dismiss="alert" aria-hidden="true"></button>
					{{ $expiredToken }}
				</div>
			@endif
			{!! csrf_field() !!}
			@if (session('error'))
				<div class="alert alert-danger fade in">
					{{ session('error') }}
				</div>
			@endif
			<div class="row">
				<div class="col-sm-12 col-xs-12 form-group{{ $errors->has('email') ? ' has-error' : '' }}" >
					@if(Cookie::get('cookie_login_email'))
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
						<input type="email" class="form-control form-control-solid placeholder-no-fix form-group" name="email" value="{{Cookie::get('cookie_login_email')}}" placeholder="{!! trans('template.frontLogin.email') !!}" autocomplete="off">
					@else
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
						<input type="email" class="form-control form-control-solid placeholder-no-fix form-group" name="email" value="{{ old('email') }}" placeholder="{!! trans('template.frontLogin.email') !!}" autocomplete="off">
					@endif
					@if ($errors->has('email'))
						<span class="help-block">
							{{ $errors->first('email') }}
						</span>
					@endif
				</div>
				<div class="col-sm-12 col-xs-12 form-group {{ $errors->has('password') ? ' has-error' : '' }}">
					@if(Cookie::get('cookie_login_password'))
						<span class=" icons">
		                    <svg xmlns="http://www.w3.org/2000/svg" version="1.2" baseProfile="tiny-ps" viewBox="0 0 24 14" width="24" height="14">
		                        <style>
		                            tspan { white-space:pre }
		                            .shp0 { fill: #3282ff} 
		                        </style>
		                        <path id="Layer" fill-rule="evenodd" class="shp0" d="M22 14L16 14L16 10L13.32 10C12.18 12.42 9.72 14 7 14C3.14 14 0 10.86 0 7C0 3.14 3.14 0 7 0C9.72 0 12.17 1.58 13.32 4L24 4L24 10L22 10L22 14ZM18 12L20 12L20 8L22 8L22 6L11.94 6L11.71 5.33C11.01 3.34 9.11 2 7 2C4.24 2 2 4.24 2 7C2 9.76 4.24 12 7 12C9.11 12 11.01 10.66 11.71 8.67L11.94 8L18 8L18 12ZM7 10C5.35 10 4 8.65 4 7C4 5.35 5.35 4 7 4C8.65 4 10 5.35 10 7C10 8.65 8.65 10 7 10ZM7 6C6.45 6 6 6.45 6 7C6 7.55 6.45 8 7 8C7.55 8 8 7.55 8 7C8 6.45 7.55 6 7 6Z"></path>
		                    </svg>
		                </span>
						<input type="password" class="form-control form-control-solid placeholder-no-fix form-group" name="password" value="{{Cookie::get('cookie_login_password')}}" placeholder="{!! trans('template.frontLogin.password') !!}"  autocomplete="off">
					@else
						<span class=" icons">
		                    <svg xmlns="http://www.w3.org/2000/svg" version="1.2" baseProfile="tiny-ps" viewBox="0 0 24 14" width="24" height="14">
		                        <style>
		                            tspan { white-space:pre }
		                            .shp0 { fill: #3282ff} 
		                        </style>
		                        <path id="Layer" fill-rule="evenodd" class="shp0" d="M22 14L16 14L16 10L13.32 10C12.18 12.42 9.72 14 7 14C3.14 14 0 10.86 0 7C0 3.14 3.14 0 7 0C9.72 0 12.17 1.58 13.32 4L24 4L24 10L22 10L22 14ZM18 12L20 12L20 8L22 8L22 6L11.94 6L11.71 5.33C11.01 3.34 9.11 2 7 2C4.24 2 2 4.24 2 7C2 9.76 4.24 12 7 12C9.11 12 11.01 10.66 11.71 8.67L11.94 8L18 8L18 12ZM7 10C5.35 10 4 8.65 4 7C4 5.35 5.35 4 7 4C8.65 4 10 5.35 10 7C10 8.65 8.65 10 7 10ZM7 6C6.45 6 6 6.45 6 7C6 7.55 6.45 8 7 8C7.55 8 8 7.55 8 7C8 6.45 7.55 6 7 6Z"></path>
		                    </svg>
		                </span>
						<input type="password" class="form-control form-control-solid placeholder-no-fix form-group" name="password" placeholder="{!! trans('template.frontLogin.password') !!}"  autocomplete="off">
					@endif
					@if ($errors->has('password'))
						<span class="help-block">
							{{ $errors->first('password') }}
						</span>
					@endif
				</div>
			</div>
			<div class="row">
				<div class="col-sm-5 col-xs-12">
					<div class="rem-password">
						<p>
							<label for="remember_me">
							@if(Cookie::get('cookie_login_password') && Cookie::get('cookie_login_email'))
								<input type="checkbox" class="rem-checkbox" id="remember_me" name="remember" checked/>
							@else
								<input type="checkbox" class="rem-checkbox" id="remember_me" name="remember"/>
							@endif
							{!! trans('template.frontLogin.rememberme') !!}</label>
						</p>
					</div>
				</div>
				<div class="col-xs-12 text-center">
					<button class="btn blue browser_show" title="{!! trans('template.frontLogin.signin') !!}" type="submit">{!! trans('template.frontLogin.signin') !!} </button>
					<div class="forgot-password">
						<a class="forget_link btn-link" href="{{ url('/powerpanel/password/reset') }}">{!! trans('template.frontLogin.forgotpasswordques') !!}</a>
					</div>
					
				</div>
				<div class="col-xs-12 mobile_show">
					<button class="btn blue" title="{!! trans('template.frontLogin.signin') !!}" type="submit">{!! trans('template.frontLogin.signin') !!} </button>
				</div>
			</div>
		</div>
	</form>
</div>

@endsection
@section('scripts')
<script src="{{ url('resources/app-assets/js/pages/login-5.js') }}"></script>
@endsection