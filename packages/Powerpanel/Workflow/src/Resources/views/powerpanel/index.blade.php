@extends('powerpanel.layouts.app')
@section('title')
{{Config::get('Constant.SITE_NAME')}} - PowerPanel
@endsection
@section('css')
<link href="{{ $CDN_PATH.'resources/global/plugins/datatables/datatables.min.css' }}" rel="stylesheet" type="text/css" />
<link href="{{ $CDN_PATH.'resources/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.css' }}" rel="stylesheet" type="text/css" />
<link href="{{ $CDN_PATH.'resources/global/plugins/fancybox/source/helpers/jquery.fancybox-thumbs.css' }}" rel="stylesheet" type="text/css"/>
<link href="{{ $CDN_PATH.'resources/global/plugins/highslide/highslide.css' }}" rel="stylesheet" type="text/css" />
<link href="{{ $CDN_PATH.'resources/css/packages/workflow/workflow.css' }}" rel="stylesheet" type="text/css" />
<style>
    .fancybox-wrap{width:50% !important;text-align:center}
    .fancybox-inner{width:100% !important;vertical-align:middle ;height:auto !important}
</style>
@endsection
@section('content')
<!--@include('powerpanel.partials.breadcrumbs')-->
{!! csrf_field() !!}
<div class="row">
    <div class="col-md-12">
        <!-- TITILE HEAD START -->
        <div class="title-dropdown_sec">
            <div class="title_bar">
                <div class="page-head">
                    <div class="page-title">
                        <h1>WORKFLOW</h1>                        
                    </div>   
                    <ul class="page-breadcrumb breadcrumb">
                        <li>
                            <span aria-hidden="true" class="icon-home"></span>
                            <a href="{{ url('powerpanel') }}">Home</a>
                            <i class="fa fa-circle"></i>
                        </li>
                        <li class="active">Workflow</li>
                    </ul>	
                    <a class="drop_toogle_arw" href="javascript:void(0);" data-toggle="collapse" data-target="#cmspage_id"><i class="la la-chevron-circle-up"></i></a>                                           
                </div> 
                <div class="add_category_button pull-right">
                    <a title="Help" class="add_category" target="_blank" href="{{ url('assets/videos/Shield_CMS_WorkFlow.mp4')}}">
                        <span title="Help">Help</span> <i class="la la-question-circle"></i>
                    </a>
                </div>
            </div>
            <div id="cmspage_id" class="collapse in">
                <div class="collapse-inner">
                    <div class="portlet-title select_box service_select_box">
                        <div class="row">
                            <div class="col-lg-6 col-md-12 col-xs-12">
                                <div class="portlet-lf-title">
                                    <div class="sub_select_filter" id="hidefilter">
                                        <span class="title">{{ trans('workflow::template.common.filterby') }}:</span>
                                        <span class="select_input">
                                            <select id="rolefilter" name="rolefilter" data-sort data-order class="bs-select select2">
                                                <option value=" ">-Select Role-</option>
                                                @if(count($modules) > 0)
                                                @foreach ($modules as $pagedata)
                                                <option data-model="{{ $pagedata->varModelName }}" data-module="{{ $pagedata->varModuleName }}" value="{{ $pagedata->id }}" {{ (isset($banners->fkModuleId) && $pagedata->id == $banners->fkModuleId) || $pagedata->id == old('modules')? 'selected' : '' }} >{{ $pagedata->display_name }}</option>
                                                @endforeach
                                                @endif
                                            </select>
                                        </span>
                                        <span class="btn btn-icon-only btn-green-drake green-new" type="button" id="refresh" title="Reset">
                                            <i class="fa fa-refresh" aria-hidden="true"></i>
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-6 col-md-12 col-xs-12">
                                <div class="portlet-rh-title">
                                    @can('workflow-create')
                                    <div class="add_category_button pull-right">
                                        <a class="add_category" href="{{ url('powerpanel/workflow/add') }}"><span>{{ trans('workflow::template.workflowModule.addWorkflow') }}</span> <i class="la la-plus"></i></a>
                                    </div>
                                    @endcan
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div> 
        </div>
        <!-- TITILE HEAD End... --> 
        @if(Session::has('message'))
        <div class="alert alert-success">
            <button class="close" data-close="alert"></button>
            {{ Session::get('message') }}
        </div>
        @endif
        <div class="portlet light portlet-fit portlet-datatable bordered">
            @if($total > 0)
            <div class="portlet-body">
                <div class="table-container">
                    <div class="workflow_listing">
                        <div class="tab-content setting-min">
                            <div id="menu1" class="tab-pane fade in active">
                                <table class="new_table_desing table table-striped table-bordered table-hover table-checkable" id="workflow_datatable_ajax">
                                    <thead>
                                        <tr role="row" class="heading">
                                            <th width="95%" align="left">Role</th>
                                            <th width="5%" align="right">{{trans('workflow::template.common.actions')}}</th>
                                        </tr>
                                    </thead>
                                    <tbody></tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    @can('workflow-delete')
                    <a href="javascript:;" class="btn-sm btn btn-outline red right_bottom_btn deleteMass">{{ trans('workflow::template.common.delete') }}</a>
                    @endcan
                </div>
            </div>

            @else
            @include('powerpanel.partials.addrecordsection',['type'=>Config::get('Constant.MODULE.TITLE'), 'adUrl' => url('powerpanel/workflow/add')])
            @endif
        </div>
    </div>
</div>
@include('powerpanel.partials.deletePopup')
@endsection
@section('scripts')
<script type="text/javascript">
    window.site_url = '{!! url("/") !!}';
    var DELETE_URL = '{!! url("/powerpanel/workflow/DeleteRecord") !!}';
    var getChildData = window.site_url + "/powerpanel/workflow/getChildData";
    var showChecker = true;
            @if (!$userIsAdmin)
            showChecker = false;
            @endif
</script>
<script src="{{ $CDN_PATH.'resources/global/plugins/jquery-cookie-master/src/jquery.cookie.js' }}" type="text/javascript"></script>
<script src="{{ $CDN_PATH.'resources/global/scripts/datatable.js' }}" type="text/javascript"></script>
<script src="{{ $CDN_PATH.'resources/global/plugins/datatables/datatables.min.js' }}" type="text/javascript"></script>
<script src="{{ $CDN_PATH.'resources/global/plugins/datatables/dataTables.editor.js' }}" type="text/javascript"></script>
<script src="{{ $CDN_PATH.'resources/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.js' }}" type="text/javascript"></script>
<script src="{{ $CDN_PATH.'resources/pages/scripts/packages/workflow/workflow-datatables-ajax.js' }}" type="text/javascript"></script>
<script src="{{ $CDN_PATH.'resources/pages/scripts/custom.js' }}" type="text/javascript"></script>
<script src="{{ $CDN_PATH.'resources/pages/scripts/user-updates-approval.js' }}" type="text/javascript"></script>
<script type="text/javascript">
    $(document).ready(function () {
        $('#statusfilter').select2({
            placeholder: "Select status"
        });
    });
</script>
@endsection