@section('css')
<link href="{{ url('resources/global/plugins/bootstrap-fileinput/bootstrap-fileinput.css') }}" rel="stylesheet" type="text/css" />
<!-- BEGIN PAGE LEVEL PLUGINS -->
<!-- END PAGE LEVEL PLUGINS -->
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
										
										{{-- @if (count($errors) > 0)
										<div class="alert alert-danger">
											<strong>Whoops!</strong> {{ trans('boatcategory::template.common.inputProblem') }} <br><br>
											<ul>
												@foreach ($errors->all() as $error)
												<li>{{ $error }}</li>
												@endforeach
											</ul>
										</div>
										@endif --}}
										
										{!! Form::open(['method' => 'post','id'=>'frmBoatCategory']) !!}
										
										<div class="form-body">
											<div class="row">
												<div class="col-md-12">
													<div class="form-group @if($errors->first('title')) has-error @endif form-md-line-input">
														<label class="form_title" for="site_name">{{ trans('boatcategory::template.common.title') }} <span aria-required="true" class="required"> * </span></label>
														{!! Form::text('title', isset($boatCategory->varTitle) ? $boatCategory->varTitle : old('title'), array('maxlength' => 150, 'class' => 'form-control hasAlias seoField maxlength-handler','data-url' => 'powerpanel/boat-category','placeholder' => trans('boatcategory::template.common.title'),'autocomplete'=>'off')) !!}
														<span class="help-block">
															{{ $errors->first('title') }}
														</span>
													</div>
												</div>
											</div>
											
											
											
											<h3 class="form-section">{{ trans('boatcategory::template.common.displayinformation') }}</h3>
											<div class="row">
												
												<div class="col-md-6">
													@php
													$display_order_attributes = array('class' => 'form-control','maxlength'=>10,'placeholder'=>trans('boatcategory::template.common.displayorder'),'autocomplete'=>'off');
													
													@endphp
													<div class="form-group @if($errors->first('display_order')) has-error @endif form-md-line-input">
														<label class="form_title" class="site_name">{{ trans('boatcategory::template.common.displayorder') }} <span aria-required="true" class="required"> * </span></label>
														{!! Form::text('display_order', isset($boatCategory->intDisplayOrder)?$boatCategory->intDisplayOrder : 1, $display_order_attributes) !!}
														<span class="help-block">
															<strong>{{ $errors->first('display_order') }}</strong>
														</span>
													</div>
												</div>
												@if($hasRecords==0 && $isParent==0)
												<div class="col-md-6">
													@include('powerpanel.partials.displayInfo',['display' => isset($boatCategory->chrPublish)?$boatCategory->chrPublish:null])
												</div>
												@else
												<div class="col-md-6">
													<div class="form-group">
														<label class="control-label form_title"> Publish/ Unpublish</label>
														@if($hasRecords > 0)
														<p><b>NOTE:</b> This category is selected in {{$hasRecords}} record(s) so it can&#39;t be unpublished.</p>
														@endif
														@if($isParent > 0)
														<p><b>NOTE:</b> This category is selected as Parent Category in {{$isParent}} record(s) so it can&#39;t be deleted or unpublished.</p>
														@endif
													</div>
												</div>
												@endif
											</div>
										</div>
										<div class="form-actions">
											<div class="row">
												<div class="col-md-12">
													<button type="submit" name="saveandedit" class="btn btn-green-drake" value="saveandedit">{!! trans('boatcategory::template.common.saveandedit') !!}</button>
													<button type="submit" name="saveandexit" class="btn btn-green-drake" value="saveandexit">{!! trans('boatcategory::template.common.saveandexit') !!}</button>
													<a class="btn btn-outline red" href="{{ url('powerpanel/boat-category') }}">{{ trans('boatcategory::template.common.cancel') }}</a>
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
@endsection
@section('scripts')

<script src="{{ url('resources/global/plugins/bootstrap-fileinput/bootstrap-fileinput.js') }}" type="text/javascript"></script>
<script src="{{ url('resources/pages/scripts/custom.js') }}" type="text/javascript"></script>

<!-- BEGIN CORE PLUGINS -->

<script src="{{ url('resources/global/plugins/bootstrap-hover-dropdown/bootstrap-hover-dropdown.min.js') }}" type="text/javascript"></script>
<script src="{{ url('resources/global/plugins/jquery-slimscroll/jquery.slimscroll.min.js') }}" type="text/javascript"></script>
<!-- END CORE PLUGINS -->
<!-- BEGIN PAGE LEVEL SCRIPTS -->
<script src="{{ url('resources/global/plugins/bootstrap-maxlength/bootstrap-maxlength.min.js') }}" type="text/javascript"></script>
<script src="{{ url('resources/pages/scripts/packages/boatcategory/boat_category_validations.js') }}" type="text/javascript"></script>
<!-- END PAGE LEVEL SCRIPTS -->
@endsection