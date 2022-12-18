@section('css')
<link href="{{ $CDN_PATH.'resources/global/plugins/select2/css/select2.min.css' }}" rel="stylesheet" type="text/css"/>
<link href="{{ $CDN_PATH.'resources/global/plugins/select2/css/select2-bootstrap.min.css' }}" rel="stylesheet" type="text/css"/>
@endsection
@extends('powerpanel.layouts.app')
@section('title')
{{Config::get('Constant.SITE_NAME')}} - PowerPanel
@endsection
@section('content')
@php $settings = json_decode(Config::get("Constant.MODULE.SETTINGS")); @endphp
@include('powerpanel.partials.breadcrumbs')
<div class="col-md-12 settings">
	{{-- @if (count($errors) > 0)
	<div class="alert alert-danger">
		<strong>Whoops!</strong> There were some problems with your input.<br><br>
		<ul>
			@foreach ($errors->all() as $error)
			<li>{{ $error }}</li>
			@endforeach
		</ul>
	</div>
	@endif --}}
	@if(isset($user))
	{!! Form::model($user, ['method' => 'POST','id'=>'frmUsers','route' => ['powerpanel.users.edit', $user->id]]) !!}
	@else
	{!! Form::model(null,['method' => 'POST','id'=>'frmUsers','route' => ['powerpanel.users.add']]) !!}
	@endif
	<input type="password" style="width: 0;height: 0; visibility: hidden;position:absolute;left:0;top:0;"/>
	<div class="row">
		@if(Session::has('message'))
		<div class="alert alert-success">
			<button class="close" data-close="alert"></button>
			{{ Session::get('message') }}
		</div>
		@endif
		<div class="portlet light bordered">
			<div class="portlet-body form_pattern">
				<div class="tabbable tabbable-tabdrop">
					<div class="tab-content settings">
						<div class="form-body">
							<div class="form-group {{ $errors->has('name') ? ' has-error' : '' }} form-md-line-input">
								<label class="form_title focus-none" for="name">{{  trans('shiledcmstheme::template.common.name') }} <span aria-required="true" class="required"> * </span></label>	
								{!! Form::text('name', null, array('maxlength'=>150,'placeholder'=> trans('shiledcmstheme::template.common.name'),'class' => 'form-control input-sm maxlength-handler','autocomplete'=>'off')) !!}
								
								<span style="color: red;">
									{{ $errors->first('name') }}
								</span>
							</div>
							<div class="form-group {{ $errors->has('email') ? ' has-error' : '' }} form-md-line-input">
								<label class="form_title focus-none" for="email">{{  trans('shiledcmstheme::template.common.email') }}  <span aria-required="true" class="required"> * </span></label>
								{!! Form::text('email', null, array('maxlength'=>150,'placeholder'=> trans('shiledcmstheme::template.common.email'),'class' => 'form-control input-sm maxlength-handler','autocomplete'=>'off')) !!}
								
								<span style="color: red;">
									{{ $errors->first('email') }}
								</span>
							</div>
							<div class="form-group {{ $errors->has('password') ? ' has-error' : '' }} form-md-line-input">
								<label class="form_title focus-none" for="password">{{  trans('shiledcmstheme::template.common.password') }} </label>	
								{!! Form::password('password',array('autocomplete' => 'off','maxlength'=>20,'placeholder'=> trans('shiledcmstheme::template.common.password'),'class' => 'form-control input-sm maxlength-handler','id'=>'password')) !!}
								
								<span style="color: red;">
									{{ $errors->first('password') }}
								</span>
								<div class="pswd_info" id="password_info">
									<h4>Password must meet the following requirements:</h4>
									<ul>
										<li id="letter" class="letterinfo invalid">At least <strong>one lowercase letter</strong></li>
										<li id="capital" class="capitalletterinfo invalid">At least <strong>one uppercase letter</strong></li>
										<li id="number" class="numberinfo invalid">At least <strong>one number</strong></li>
										<li id="length" class="lengthInfo invalid">Password should be <strong>6 to 20 characters</strong></li>
										<li id="special" class="specialinfo invalid">At least <strong>one special character</strong></li>
									</ul>
								</div>
							</div>
							<div class="form-group form-md-line-input">
								<label class="form_title focus-none" for="confirm-password">{{  trans('shiledcmstheme::template.common.confirmpassword') }}</label>	
								{!! Form::password('confirm-password', array('autocomplete' => 'off','maxlength'=>20,'placeholder' =>  trans('shiledcmstheme::template.common.confirmpassword'),'class' => 'form-control input-sm maxlength-handler','id'=>'confirmpassword')) !!}
								
								<!--                            <div class="pswd_info" id="confirmpassword_info">
																									<h4>Password must meet the following requirements:</h4>
																									<ul>
																												<li id="letter" class="letterinfo invalid">At least <strong>one lowercase letter</strong></li>
																												<li id="capital" class="capitalletterinfo invalid">At least <strong>one uppercase letter</strong></li>
																												<li id="number" class="numberinfo invalid">At least <strong>one number</strong></li>
																												<li id="length" class="lengthInfo invalid">Password should be <strong>6 to 20 characters</strong></li>
																												<li id="special" class="specialinfo invalid">At least <strong>one special character</strong></li>
																									</ul>
								</div>-->
							</div>
							@if(isset($user) && $user->id==1)
							{!! Form::hidden('roles[]','1') !!}
							@else
							<div class="form-group {{ $errors->has('roles') ? ' has-error' : '' }} form-md-line-input">
								<label class="form_title focus-none" for="roles">Assign Role <span aria-required="true" class="required"> * </span></label>	
								{!! Form::select('roles[]',$roles,isset($userRole)?$userRole:old('roles'), array('class' => 'form-control bs-select select2')) !!}
								
								<span style="color: red;">
									{{ $errors->first('roles') }}
								</span>
							</div>
							@endif
							@if(isset($user) && $user->id == '1')
              	{!! Form::hidden('chrMenuDisplay', 'Y') !!}
              @else
								@include('powerpanel.partials.displayInfo',['display' => isset($user->chrPublish)?$user->chrPublish:null])
							@endif
							<div class="form-actions">
								<div class="row">
									<div class="col-md-12">
										<button type="submit" name="saveandedit" class="btn btn-green-drake" value="saveandedit" title="{!!  trans('shiledcmstheme::template.common.saveandedit') !!}">{!!  trans('shiledcmstheme::template.common.saveandedit') !!}</button>
										<button type="submit" name="saveandexit" class="btn btn-green-drake" value="saveandexit" title="{!!  trans('shiledcmstheme::template.common.saveandexit') !!}">{!!  trans('shiledcmstheme::template.common.saveandexit') !!} </button>
										<a class="btn red btn-outline" href="{{url('powerpanel/users')}}" title="{{  trans('shiledcmstheme::template.common.cancel') }}">{{  trans('shiledcmstheme::template.common.cancel') }}</a>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	{!! Form::close() !!}
</div>
<div class="clearfix"></div>
@endsection
@section('scripts')
<script type="text/javascript">
		var userAction = "{{ (isset($user)) ? 'edit':'' }}";
</script>
<script src="{{ $CDN_PATH.'resources/pages/scripts/packages/users/user_validations.js' }}" type="text/javascript"></script>
<script src="{{ $CDN_PATH.'resources/global/plugins/bootstrap-maxlength/bootstrap-maxlength.min.js' }}" type="text/javascript"></script>
<script src="{{ $CDN_PATH.'resources/pages/scripts/password_rules.js' }}" type="text/javascript"></script>
@endsection