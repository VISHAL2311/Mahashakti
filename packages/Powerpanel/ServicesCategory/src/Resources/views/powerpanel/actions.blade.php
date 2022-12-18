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
											<strong>Whoops!</strong> {{ trans('servicescategory::template.common.inputProblem') }} <br><br>
											<ul>
												@foreach ($errors->all() as $error)
												<li>{{ $error }}</li>
												@endforeach
											</ul>
										</div>
										@endif --}}
										
										{!! Form::open(['method' => 'post','id'=>'frmServicesCategory']) !!}
										
										<div class="form-body">
											<div class="row">
												<div class="col-md-12">
													<div class="form-group @if($errors->first('title')) has-error @endif form-md-line-input">
														<label class="form_title" for="site_name">{{ trans('servicescategory::template.common.title') }} <span aria-required="true" class="required"> * </span></label>
														{!! Form::text('title', isset($serviceCategory->varTitle) ? $serviceCategory->varTitle : old('title'), array('maxlength' => 150, 'class' => 'form-control hasAlias seoField maxlength-handler','data-url' => 'powerpanel/service-category','placeholder' => trans('servicescategory::template.common.title'),'autocomplete'=>'off')) !!}
														<span class="help-block">
															{{ $errors->first('title') }}
														</span>
													</div>
												</div>
											</div>
											<div class="row">
												<div class="col-md-12">
													<!-- code for alias -->
													{!! Form::hidden(null, null, array('class' => 'hasAlias','data-url' => 'powerpanel/service-category')) !!}
													{!! Form::hidden('alias', isset($serviceCategory->alias->varAlias) ? $serviceCategory->alias->varAlias : old('alias'), array('class' => 'aliasField')) !!}
													{!! Form::hidden('oldAlias', isset($serviceCategory->alias->varAlias)?$serviceCategory->alias->varAlias : old('alias')) !!}
													<div class="form-group alias-group {{!isset($serviceCategory->alias)?'hide':''}}">
														<label class="form_title" for="Url">{{ trans('servicescategory::template.common.url') }} :</label>
														<a href="javascript:void;" class="alias">{!! url("/") !!}</a>
														<a href="javascript:void(0);" class="editAlias" title="Edit">
															<i class="fa fa-edit"></i>
														</a>
													</div>
													<span class="help-block">
														{{ $errors->first('alias') }}
													</span>
													<!-- code for alias -->
												</div>
											</div>
											<div class="row">
												<div class="col-md-12">
													<div class="form-group">
														<label class="form_title" for="parent_category_id">{{ trans('servicescategory::template.common.selectparentcategory') }}</label>
														@php echo $categories; @endphp
													</div>
												</div>
											</div>
											<div class="row">
												<div class="col-md-12">
													<div class="form-group @if($errors->first('short_description')) has-error @endif form-md-line-input">
														<label class="form_title">{{ trans('servicescategory::template.common.shortdescription') }} </label>
														{!! Form::textarea('short_description', isset($serviceCategory->txtShortDescription) ? $serviceCategory->txtShortDescription : old('short_description'), array('maxlength' => isset($settings->short_desc_length)?$settings->short_desc_length:400,'class' => 'form-control seoField maxlength-handler','id'=>'varShortDescription','rows'=>'3')) !!}
														<span class="help-block">{{ $errors->first('short_description') }}</span>
													</div>
												</div>
											</div>
											<div class="row">
												<div class="col-md-12">
													<div class="form-group @if($errors->first('description')) has-error @endif ">
														<label class="control-label form_title">{{ trans('servicescategory::template.common.description') }}</label>
														{!! Form::textarea('description', isset($serviceCategory->txtDescription) ? $serviceCategory->txtDescription : old('description'), array('placeholder' => trans('servicescategory::template.common.description'),'class' => 'form-control','id'=>'txtDescription')) !!}
														<span class="help-block">{{ $errors->first('description') }}</span>
													</div>
												</div>
											</div>
											<div class="row">
												<div class="col-md-12">
													<div class=" form-md-line-input nopadding">
														@include('powerpanel.partials.seoInfo',['form'=>'frmServicesCategory','inf'=>isset($metaInfo)?$metaInfo:false,'metaRequired'=>false])
													</div>
												</div>
											</div>
											<h3 class="form-section">{{ trans('servicescategory::template.common.displayinformation') }}</h3>
											<div class="row">
												
												<div class="col-md-6">
													@php
													$display_order_attributes = array('class' => 'form-control','maxlength'=>10,'placeholder'=>trans('servicescategory::template.common.displayorder'),'autocomplete'=>'off');
													
													@endphp
													<div class="form-group @if($errors->first('display_order')) has-error @endif form-md-line-input">
														<label class="form_title" class="site_name">{{ trans('servicescategory::template.common.displayorder') }} <span aria-required="true" class="required"> * </span></label>
														{!! Form::text('display_order', isset($serviceCategory->intDisplayOrder)?$serviceCategory->intDisplayOrder : $total, $display_order_attributes) !!}
														<span class="help-block">
															<strong>{{ $errors->first('display_order') }}</strong>
														</span>
													</div>
												</div>
												@if($hasRecords==0 && $isParent==0)
												<div class="col-md-6">
													@include('powerpanel.partials.displayInfo',['display' => isset($serviceCategory->chrPublish)?$serviceCategory->chrPublish:null])
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
													<button type="submit" name="saveandedit" class="btn btn-green-drake" value="saveandedit">{!! trans('servicescategory::template.common.saveandedit') !!}</button>
													<button type="submit" name="saveandexit" class="btn btn-green-drake" value="saveandexit">{!! trans('servicescategory::template.common.saveandexit') !!}</button>
													<a class="btn btn-outline red" href="{{ url('powerpanel/service-category') }}">{{ trans('servicescategory::template.common.cancel') }}</a>
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
<script type="text/javascript">
	window.site_url =  '{!! url("/") !!}';
	var seoFormId = 'frmServicesCategory';
	var user_action = "{{ isset($serviceCategory)?'edit':'add' }}";
		var moduleAlias = 'service-category';
</script>
<script src="{{ url('resources/global/plugins/bootstrap-fileinput/bootstrap-fileinput.js') }}" type="text/javascript"></script>
<script src="{{ url('resources/pages/scripts/custom.js') }}" type="text/javascript"></script>
<script src="{{ url('resources/global/plugins/seo-generator/seo-info-generator.js') }}" type="text/javascript"></script>
<script src="{{ url('resources/global/plugins/custom-alias/alias-generator.js') }}" type="text/javascript"></script>
<!-- BEGIN CORE PLUGINS -->

<script src="{{ url('resources/global/plugins/bootstrap-hover-dropdown/bootstrap-hover-dropdown.min.js') }}" type="text/javascript"></script>
<script src="{{ url('resources/global/plugins/jquery-slimscroll/jquery.slimscroll.min.js') }}" type="text/javascript"></script>
<!-- END CORE PLUGINS -->
<!-- BEGIN PAGE LEVEL SCRIPTS -->
<script src="{{ url('resources/global/plugins/bootstrap-maxlength/bootstrap-maxlength.min.js') }}" type="text/javascript"></script>
<script src="{{ url('resources/pages/scripts/packages/servicescategory/services_category_validations.js') }}" type="text/javascript"></script>
<!-- END PAGE LEVEL SCRIPTS -->
@endsection