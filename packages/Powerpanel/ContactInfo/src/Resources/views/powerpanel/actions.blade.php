@extends('powerpanel.layouts.app')
@section('title')
{{Config::get('Constant.SITE_NAME')}} - PowerPanel
@endsection
@section('content')
@php $settings = json_decode(Config::get("Constant.MODULE.SETTINGS")); @endphp
@include('powerpanel.partials.breadcrumbs')
<style type="text/css">
	.removePhone,
	.removeEmail,
	.removePhone:hover,
	.removeEmail:hover {
		color: #e73d4a;
	}
</style>
<div class="col-md-12 settings">
	@if(Session::has('message'))
	<div class="row">
		<div class="alert alert-success">
			<button class="close" data-close="alert"></button>
			{{ Session::get('message') }}
		</div>
	</div>
	@endif
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
<div class="row">
	<div class="portlet light bordered">
		<div class="portlet-body">
			<div class="tabbable tabbable-tabdrop">
				<div class="tab-content">
					<div class="form_pattern">
						{!! Form::open(['method' => 'post','id'=>'frmContactUS']) !!}
						<div class="form-body">
							<div class="form-group {{ $errors->has('name') ? 'has-error' : '' }} form-md-line-input">
								<label class="form_title" for="name">{{ trans('contactinfo::template.common.name') }} <span aria-required="true" class="required"> * </span></label>
								{!! Form::text('name',isset($contactInfo->varTitle)?$contactInfo->varTitle:old('name'), array('class' => 'form-control input-sm maxlength-handler', 'placeholder'=>'Name', 'maxlength'=>'150','id' => 'name','autocomplete'=>'off')) !!}
								<span class="help-block">
									{{ $errors->first('name') }}
								</span>
							</div>
							@if(isset($contactInfo))
							<div class="multi-email">
								@php
								$emcnt=0;
								$selectedEmail=unserialize($contactInfo->varEmail);
								@endphp
								@if(count($selectedEmail)>1)
								@foreach($selectedEmail as $email)
								<div class="form-group emailField {{ $errors->has('email') ? 'has-error' : '' }} form-md-line-input">
									<label class="form_title" for="email">{{ trans('contactinfo::template.common.email') }} @if($emcnt==0)<span aria-required="true" class="required"> * </span>@endif</label>
									{!! Form::text('email['.($emcnt).']',$email, array('class' => 'form-control input-sm', 'placeholder'=>'Email', 'maxlength'=>'100','id' => 'email'.($emcnt),'autocomplete'=>'off')) !!}
									@if($emcnt==0)
									<!--<a href="javascript:void(0);" class="addMoreEmail add_more" title="Add More"><i class="fa fa-plus"></i> Add More</a>-->
									@else
									<a href="javascript:void(0);" class="removeEmail add_more" title="Remove"><i class="fa fa-times"></i> Remove</a>
									@endif
									<span class="help-block">
										{{ $errors->first('email['.($emcnt).']') }}
									</span>
								</div>
								@php if( $emcnt < count($selectedEmail)-1){ $emcnt++; } @endphp @endforeach @else <div class="form-group emailField {{ $errors->has('email') ? 'has-error' : '' }} form-md-line-input">
									<label class="form_title" for="email">{{ trans('contactinfo::template.common.email') }} @if($emcnt==0)<span aria-required="true" class="required"> * </span>@endif</label>
									{!! Form::text('email['.($emcnt).']',$selectedEmail[0], array('class' => 'form-control input-sm', 'placeholder'=>'Email', 'maxlength'=>'100','id' => 'email'.($emcnt),'autocomplete'=>'off')) !!}
									@if($emcnt==0)
									<!--<a href="javascript:void(0);" class="addMoreEmail add_more" title="Add More"><i class="fa fa-plus"></i> Add More</a>-->
									@else
									<a href="javascript:void(0);" class="removeEmail add_more" title="Remove"><i class="fa fa-times"></i> Remove</a>
									@endif
									<span class="help-block">
										{{ $errors->first('email['.($emcnt).']') }}
									</span>
							</div>
							@endif
						</div>
						<div class="multi-phone">
							@php
							$phcnt=0;
							$selectedPhone = unserialize($contactInfo->varPhoneNo);
							@endphp
							@if(count($selectedPhone)>1)
							@foreach($selectedPhone as $key=>$phone)
							<div class="form-group phoneField {{ $errors->has('phone_no') ? 'has-error' : '' }} form-md-line-input">
								<label class="form_title" for="phone_no">{{ trans('contactinfo::template.common.phoneno') }} @if($phcnt==0)<span aria-required="true" class="required"> * </span>@endif</label>
								{!! Form::text('phone_no['.($phcnt).']',$phone, array('class' => 'form-control input-sm','id' => 'phone_no'.($phcnt),'placeholder' => 'Phone','autocomplete'=>'off','maxlength'=>"20", 'onkeypress'=>"javascript: return KeycheckOnlyPhonenumber(event);",'onpaste'=>'return false')) !!}
								@if($phcnt==0)
								<!--<a href="javascript:void(0);" class="addMorePhone add_more" title="Add More"><i class="fa fa-plus"></i> Add More</a>-->
								@else
								<a href="javascript:void(0);" class="removePhone add_more" title="Remove"><i class="fa fa-times"></i> Remove</a>
								@endif
								<span class="help-block">
									{{ $errors->first('phone_no') }}
								</span>
							</div>
							@php if( $phcnt < count($selectedPhone)-1){ $phcnt++; } @endphp @endforeach @else <div class="form-group phoneField {{ $errors->has('phone_no') ? 'has-error' : '' }} form-md-line-input">
								<label class="form_title" for="phone_no">{{ trans('contactinfo::template.common.phoneno') }} @if($phcnt==0)<span aria-required="true" class="required"> * </span>@endif</label>
								{!! Form::text('phone_no['.($phcnt).']',$selectedPhone[0], array('class' => 'form-control input-sm','id' => 'phone_no'.($phcnt),'placeholder' => 'Phone','autocomplete'=>'off','maxlength'=>"20", 'onkeypress'=>"javascript: return KeycheckOnlyPhonenumber(event);",'onpaste'=>'return false')) !!}
								@if($phcnt==0)
								<!--<a href="javascript:void(0);" class="addMorePhone add_more" title="Add More"><i class="fa fa-plus"></i> Add More</a>-->
								@else
								<a href="javascript:void(0);" class="removePhone add_more" title="Remove"><i class="fa fa-times"></i> Remove</a>
								@endif
								<span class="help-block">
									{{ $errors->first('phone_no') }}
								</span>
						</div>
						@endif
					</div>
					@else
					<div class="multi-email">
						<div class="emailField form-group {{ $errors->has('email') ? 'has-error' : '' }} form-md-line-input">
							<label class="form_title" for="email[0]">{{ trans('contactinfo::template.common.email') }}<span aria-required="true" class="required"> * </span></label>
							{!! Form::text('email[0]', Request::old('email'), array('class' => 'form-control input-sm email', 'maxlength'=>'100','id' => 'email0','placeholder' => 'Email','autocomplete'=>'off')) !!}
							<!--<a href="javascript:void(0);" class="addMoreEmail add_more" title="Add More"><i class="fa fa-plus"></i> Add More</a>-->
							<span class="help-block">
								{{ $errors->first('email') }}
							</span>
						</div>
					</div>
					<div class="multi-phone">
						<div class="phoneField form-group {{ $errors->has('phone_no') ? 'has-error' : '' }} form-md-line-input">
							<label class="form_title" for="phone_no[0]">{{ trans('contactinfo::template.common.phoneno') }} <span aria-required="true" class="required"> * </span></label>
							{!! Form::text('phone_no[0]', Request::old('phone_no'), array('class' => 'form-control input-sm','id' => 'phone_no0','placeholder' => 'Phone','autocomplete'=>'off', 'maxlength'=>"20", 'onkeypress'=>"javascript: return KeycheckOnlyPhonenumber(event);",'onpaste'=>'return false')) !!}
							<!--<a href="javascript:void(0);" class="addMorePhone add_more" title="Add More"><i class="fa fa-plus"></i> Add More</a>-->
							<span class="help-block">
								{{ $errors->first('phone_no') }}
							</span>
						</div>
					</div>
					@endif
					<div class="row">
						<div class="col-md-12">
							<div class="phoneField form-group {{ $errors->has('mobile_no') ? 'has-error' : '' }} form-md-line-input">
								<label class="form_title" for="mobile_no">{{ trans('contactinfo::template.common.secondryphone') }} <span aria-required="true" class="required"> * </span></label>
								{!! Form::text('mobile_no', isset($contactInfo->varMobileNo) ? $contactInfo->varMobileNo : old('mobile_no'), array('class' => 'form-control input-sm','id' => 'mobile_no','placeholder' => 'Secondry Phone','autocomplete'=>'off', 'maxlength'=>"20", 'onkeypress'=>"javascript: return KeycheckOnlyPhonenumber(event);",'onpaste'=>'return false')) !!}
								<!--<a href="javascript:void(0);" class="addMorePhone add_more" title="Add More"><i class="fa fa-plus"></i> Add More</a>-->
								<span class="help-block">
									{{ $errors->first('mobile_no') }}
								</span>
							</div>
						</div>
					</div>
					<div class="phoneField form-group {{ $errors->has('fax') ? 'has-error' : '' }} form-md-line-input" style="display:none;">
						<label class="form_title" for="faxZ">{{ trans('contactinfo::template.common.fax') }} </label>
						{!! Form::text('fax', isset($contactInfo->varFax) ? $contactInfo->varFax:old('fax'), array('class' => 'form-control input-sm','id' => 'fax','placeholder' => 'Fax','autocomplete'=>'off', 'maxlength'=>"20", 'onkeypress'=>"javascript: return KeycheckOnlyPhonenumber(event);",'onpaste'=>'return false')) !!}
						<span class="help-block">
							{{ $errors->first('fax') }}
						</span>
					</div>
					<div class="row" style="display: none;">
						<div class="col-md-12">

							<div class="form-group @if($errors->first('description')) has-error @endif ">
								<label class="control-label form_title">Working Hours</label>
								{!! Form::textarea('description', isset($contactInfo->txtDescription) ? $contactInfo->txtDescription : old('description'), array('placeholder' => trans('Working Hours'),'class' => 'form-control','id'=>'txtDescription')) !!}
								<span class="help-block">{{ $errors->first('description') }}</span>
							</div>
						</div>
					</div>
					<div class="row">

						<div class="col-md-12">
							<div class="form-group form-md-line-input @if($errors->first('address')) has-error @endif">
								<label class="form_title" for="address">{{ trans('contactinfo::template.common.address') }} <span aria-required="true" class="required"> * </span></label>
								{!! Form::textarea('address',isset($contactInfo->txtAddress)?$contactInfo->txtAddress:old('address'), array('class' => 'form-control','id'=>'address','placeholder'=>'Address','style'=>'max-height:80px;')) !!}
								<span class="help-block">
									{{ $errors->first('address') }}
								</span>
							</div>
						</div>
						<div class="col-md-12" style="display: none;">
							<div class="form-group form-md-line-input @if($errors->first('address')) has-error @endif">
								<label class="form_title" for="address">Mailing Address <span aria-required="true" class="required"> * </span></label>
								{!! Form::textarea('mailingaddress',isset($contactInfo->mailingaddress)?$contactInfo->mailingaddress:old('mailingaddress'), array('class' => 'form-control','id'=>'mailingaddress','placeholder'=>'Mailing Address','style'=>'max-height:80px;')) !!}
								<span class="help-block">
									{{ $errors->first('mailingaddress') }}
								</span>
							</div>
						</div>
					</div>
				</div>
				{{--<div class="row">
								<div class="col-md-12">
									<div class="form-group @if($errors->first('order')) has-error @endif form-md-line-input">
										{!! Form::text('order', isset($contactInfo->intDisplayOrder)?$contactInfo->intDisplayOrder:$total, array('maxlength'=>5,'placeholder' => trans('contactinfo::template.common.order'),'class' => 'form-control','autocomplete'=>'off')) !!}
										<label class="form_title" class="site_name">{{ trans('contactinfo::template.common.displayorder') }} <span aria-required="true" class="required"> * </span></label>
				<span style="color: red;">
					<strong>{{ $errors->first('order') }}</strong>
				</span>
			</div>
		</div>
	</div>
	<div class="row">
		<div class="col-md-6">
			<div class="form-group {{ $errors->has('primary') ? ' has-error' : '' }} ">
				<label class="form_title" for="primary">{{ trans('contactinfo::template.common.primary') }} <span aria-required="true" class="required"> * </span></label>
				<div class="md-radio-inline">
					<div class="md-radio">
						@if ((isset($contactInfo->chrIsPrimary) && $contactInfo->chrIsPrimary == 'Y') || (null == Request::old('primary') || Request::old('primary') == 'Y'))
						@php $checked_yes = 'checked' @endphp
						@else
						@php $checked_yes = '' @endphp
						@endif
						<input type="radio" {{ $checked_yes }} value="Y" id="radio6" name="primary" class="md-radiobtn">
						<label for="radio6">
							<span class="inc"></span>
							<span class="check"></span>
							<span class="box"></span> Yes
						</label>
					</div>
					<div class="md-radio">
						@if ( (isset($contactInfo->chrIsPrimary) && $contactInfo->chrIsPrimary == 'N') || old('primary')=='N')
						@php $checked_no = 'checked' @endphp
						@else
						@php $checked_no = '' @endphp
						@endif
						<input type="radio" {{ $checked_no }} value="N" id="radio7" name="primary" class="md-radiobtn">
						<label for="radio7">
							<span class="inc"></span>
							<span class="check"></span>
							<span class="box"></span> No
						</label>
					</div>
				</div>
				<span class="help-block">
					{{ $errors->first('primary') }}
				</span>
			</div>
		</div>
		<div class="col-md-6">
			@include('powerpanel.partials.displayInfo',['display'=> isset($contactInfo->chrPublish)?$contactInfo->chrPublish:null])
		</div>
	</div>
	--}}
	<div class="form-actions">
		<div class="row">
			<div class="col-md-12">
				<button type="submit" name="saveandedit" class="btn btn-green-drake" value="saveandedit" title="{!! trans('contactinfo::template.common.saveandedit') !!}">{!! trans('contactinfo::template.common.saveandedit') !!}</button>
				<button type="submit" name="saveandexit" class="btn btn-green-drake" value="saveandexit" title="{!! trans('contactinfo::template.common.saveandexit') !!}">{!! trans('contactinfo::template.common.saveandexit') !!}</button>
				<!-- <a class="btn btn-outline red" href="{{ url('powerpanel/contact-info') }}" title="{{ trans('contactinfo::template.common.cancel') }}">{{ trans('contactinfo::template.common.cancel') }}</a> -->
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
<div class="clearfix"></div>
@php
$contactAddress = isset($contactInfo->txtAddress)?$contactInfo->txtAddress:'';
$contactAddress = trim(preg_replace('/\s\s+/', ' ', $contactAddress));
@endphp
@endsection
@section('scripts')
<script src="{{ $CDN_PATH.'resources/global/plugins/jquery-inputmask/jquery.inputmask.bundle.min.js' }}" type="text/javascript"></script>

<script type="text/javascript">
	window.site_url = '{!! url("/") !!}';
</script>
@include('powerpanel.partials.ckeditor',['config'=>'docsConfig'])
<script src="{{ $CDN_PATH.'resources/pages/scripts/packages/contactinfo/contacts_validations.js' }}" type="text/javascript"></script>
<script src="{{ $CDN_PATH.'resources/pages/scripts/numbervalidation.js' }}" type="text/javascript"></script>
<script src="{{ $CDN_PATH.'resources/pages/scripts/custom.js' }}" type="text/javascript"></script>
@endsection