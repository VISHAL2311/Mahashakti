@section('css')
<link href="{{ $CDN_PATH.'resources/global/plugins/bootstrap-fileinput/bootstrap-fileinput.css' }}" rel="stylesheet" type="text/css" />
<link href="{{ $CDN_PATH.'resources/css/packages/workflow/workflow.css' }}" rel="stylesheet" type="text/css" />
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
										{{-- @if(!isset($workflow->varType))
										<div class="form-group hide">
											<label class="form_title" class="site_name">Type <span aria-required="true" class="required"> * </span></label>
											<div class="clearfix"></div>
											<div class="input_box">
												@if( null !== old('type') )
												@php $selected = old('type'); @endphp
												@else
												@php $selected = ''; @endphp
												@endif
												@php $catSelect = []; @endphp
												@foreach($moduleCategory as $id=>$category)
												@if(!in_array($id, $approvalWorkFlows))
												@php $catSelect[$id] = $category; @endphp
												@endif
												@endforeach
												<select id="type" name="type" data-sort data-order class="form-control bs-select select2 status_select">
													<option value="">Select type</option>
													<option @if($selected == 'leads' || $selected == '') selected @endif value="leads">Leads</option>
													<option @if(empty($catSelect)) disabled @endif  @if($selected == 'approvals') selected @endif value="approvals">Approvals</option>
												</select>
											</div>
										</div>
										@endif --}}
										@php $workflow = isset($workflow)?$workflow:null; @endphp
										@if(!isset($workflow->varType))
										{{-- <div class="leads">
											@include('workflow::powerpanel.partials.leads',['workflow'=>$workflow,'adminUsers'=>$adminUsers,'moduleCategory'=>$moduleCategory,'approvalWorkFlows'=>$approvalWorkFlows])
										</div> --}}
										<div class="approvals">
											@include('workflow::powerpanel.partials.user-approvals',['workflow'=>$workflow,'adminUsers'=>$adminUsers,'moduleCategory'=>$moduleCategory,'approvalWorkFlows'=>$approvalWorkFlows])
										</div>
										@elseif($workflow->varType == "leads")
										<div class="leads">@include('workflow::powerpanel.partials.leads',['workflow'=>$workflow])</div>
										@elseif($workflow->varType == "approvals")
										<div class="approvals">@include('workflow::powerpanel.partials.user-approvals',['workflow'=>$workflow])</div>
										@endif
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
var seoFormId = 'frmWorkflow';
var user_action = "{{ isset($workflow)?'edit':'add' }}";
var moduleAlias = 'workflow';
var categoryAllowed = false;
</script>
<script src="{{ $CDN_PATH.'resources/global/plugins/bootstrap-fileinput/bootstrap-fileinput.js' }}" type="text/javascript"></script>
<script src="{{ $CDN_PATH.'resources/pages/scripts/custom.js' }}" type="text/javascript"></script>
<!-- BEGIN CORE PLUGINS -->
<script src="{{ $CDN_PATH.'resources/global/plugins/bootstrap/js/bootstrap.min.js' }}" type="text/javascript"></script>
<script src="{{ $CDN_PATH.'resources/global/plugins/bootstrap-hover-dropdown/bootstrap-hover-dropdown.min.js' }}" type="text/javascript"></script>
<script src="{{ $CDN_PATH.'resources/global/plugins/jquery-slimscroll/jquery.slimscroll.min.js' }}" type="text/javascript"></script>
<!-- END CORE PLUGINS -->
<!-- BEGIN PAGE LEVEL SCRIPTS -->
<script src="{{ $CDN_PATH.'resources/global/plugins/bootstrap-maxlength/bootstrap-maxlength.min.js' }}" type="text/javascript"></script>
<script src="{{ $CDN_PATH.'resources/pages/scripts/packages/workflow/workflow-validations.js' }}" type="text/javascript"></script>
<script type="text/javascript">
$(document).ready(function() {
		$('#undo_redo').multiselect();
});
$('#approvalid').click(function() {
    $('#undo_redo option').prop('selected', true);
});
$('#noapprovalid').click(function() {
    $('#undo_redo_to option').prop('selected', true);
});
</script>     
<script src="{{ $CDN_PATH.'resources/pages/scripts/packages/workflow/prettify.min.js' }}" type="text/javascript"></script>
<script src="{{ $CDN_PATH.'resources/pages/scripts/packages/workflow/multiselect.js' }}" type="text/javascript"></script>
<!-- END PAGE LEVEL SCRIPTS -->
@endsection