@extends('powerpanel.layouts.app')
@section('title')
{{Config::get('Constant.SITE_NAME')}} - PowerPanel
@stop
@section('css')
<link href="{{ $CDN_PATH.'resources/global/plugins/datatables/datatables.min.css' }}" rel="stylesheet" type="text/css" />
<link href="{{ $CDN_PATH.'resources/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.css' }}" rel="stylesheet" type="text/css" />
<link href="{{ $CDN_PATH.'resources/global/plugins/highslide/highslide.css' }}" rel="stylesheet" type="text/css"/>
<link href="{{ $CDN_PATH.'resources/global/css/rank-button.css' }}" rel="stylesheet" type="text/css" />
@endsection
@section('content')
<!--@include('powerpanel.partials.breadcrumbs')-->
<!-- BEGIN PAGE BASE CONTENT -->
{!! csrf_field() !!}
<div class="row">
    <div class="col-md-12">
        <!-- TITILE HEAD START -->
        <div class="title-dropdown_sec">
            <div class="title_bar">
                <div class="page-head">
                    <div class="page-title">
                        <h1>Forms </h1>                        
                    </div>      
                    <ul class="page-breadcrumb breadcrumb">
                        <li>
                            <span aria-hidden="true" class="icon-home"></span>
                            <a href="{{ url('powerpanel') }}">Home</a>
                            <i class="fa fa-circle"></i>
                        </li>
                        <li class="active">Forms</li>
                    </ul>
                    <a class="drop_toogle_arw" href="javascript:void(0);" data-toggle="collapse" data-target="#cmspage_id"><i class="la la-chevron-circle-up"></i></a>                                           
                </div> 
                <div class="add_category_button pull-right">
                    <a title="Help" class="add_category" href="{{ url('assets/videos/Shield_CMS_WorkFlow.mp4')}}">
                        <span title="Help">Help</span> <i class="la la-question-circle"></i>
                    </a>
                </div>
            </div>
            <div id="cmspage_id" class="collapse in">
                <div class="collapse-inner">
                    <div class="portlet-title select_box filter-group">
                        <div class="row">
                            <div class="col-lg-6 col-md-12 col-xs-12">
                                <div class="portlet-lf-title">
                                    <div class="sub_select_filter" id="hidefilter">
                                        <span class="title">{!! trans('formbuilder::template.common.filterby') !!}:</span>
                                        <span class="select_input">
                                            <select id="statusfilter" data-sort data-order class="bs-select select2">
                                                <option value=" ">{!! trans('formbuilder::template.common.selectstatus') !!}</option>
                                                <option value="Y">{!! trans('formbuilder::template.common.publish') !!}</option>
                                                <option value="N">{!! trans('formbuilder::template.common.unpublish') !!}</option>
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
                                    @can('formbuilder-create')
                                    <div class="add_category_button pull-right">
                                        <a class="add_category" href="{{ url('powerpanel/formbuilder/add') }}"><span>ADD Form</span> <i class="la la-plus"></i></a>
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
        <!-- Begin: life time stats -->
        @if(Session::has('message'))
        <div class="alert alert-success">
            <button class="close" data-close="alert"></button>
            {{ Session::get('message') }}
        </div>
        @endif
        <div class="portlet light portlet-fit portlet-datatable">
            @if($iTotalRecords > 0)
            <div class="portlet-body">
                <div class="table-container">
                    <div class="pw_tabs">
                        <ul class="nav nav-tabs"></ul>
                        <div class="col-md-12 col-sm-12 ">
                            <div class="row">
                                <div class="row col-md-6 col-sm-12 pull-right search_pages_div">
                                    <div class="search_rh_div">
                                        <span>{{ trans('formbuilder::template.common.search') }}:</span>
                                        <input type="search" class="form-control form-control-solid placeholder-no-fix" placeholder="Search by Form Name" id="searchfilter">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="tab-content setting-min">
                            <div id="menu1" class="tab-pane fade in active">
                                
                                <table class="new_table_desing table table-striped table-bordered table-hover table-checkable dataTable email_log_datatable_ajax hide-mobile" id="datatable_ajax">
                                    <thead>
                                        <tr role="row" class="heading">
                                            <th width="2%" align="center"><input type="checkbox" class="group-checkable"></th>
                                            <th width="20%" align="left">Form Name</th>
                                            <th width="10%" align="center">Email Information</th>
                                            <th width="20%" align="center">{{ trans('formbuilder::template.common.publish') }}</th>
                                            <th width="20%" align="center">Date</th>
                                            <th width="15%" align="right">{{ trans('formbuilder::template.common.actions') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody></tbody>
                                </table>
                            </div>
                        </div>
                       <button href="javascript:;" class="btn-sm btn btn-outline red right_bottom_btn deleteMass hide-btn-mob" value="C">
                        {{ trans('formbuilder::template.common.delete') }}
                    </button>
                    </div>
                </div>
            </div>
            @else
            @include('powerpanel.partials.addrecordsection',['type'=>Config::get('Constant.MODULE.TITLE'), 'adUrl' => url('powerpanel/formbuilder/add')])
            @endif
        </div>
    </div>
</div>
@include('powerpanel.partials.deletePopup')
@include('powerpanel.partials.approveRecord')
@include('powerpanel.partials.cmsPageComments',['module'=>Config::get('Constant.MODULE.TITLE')])
@endsection
@section('scripts')
<script type="text/javascript">
    window.site_url = '{!! url("/") !!}';
    var DELETE_URL = '{!! url("/powerpanel/formbuilder/DeleteRecord") !!}';
    var APPROVE_URL = '{!! url("/powerpanel/formbuilder/ApprovedData_Listing") !!}';
    var getChildData = window.site_url + "/powerpanel/formbuilder/getChildData";
    var getChildData_rollback = window.site_url + "/powerpanel/formbuilder/getChildData_rollback";
    var ApprovedData_Listing = window.site_url + "/powerpanel/formbuilder/ApprovedData_Listing";
    var Get_Comments = '{!! url("/powerpanel/formbuilder/Get_Comments") !!}';
    var Quick_module_id = '<?php echo Config::get('Constant.MODULE.ID'); ?>';
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

<script src="{{ $CDN_PATH.'resources/pages/scripts/packages/formbuilder/formbuilder-datatables-ajax.js' }}" type="text/javascript"></script>
<script src="{{ $CDN_PATH.'resources/pages/scripts/custom.js' }}" type="text/javascript"></script>
<script src="{{ $CDN_PATH.'resources/global/plugins/highslide/highslide-with-html.js' }}" type="text/javascript"></script>
<script src="{{ $CDN_PATH.'resources/pages/scripts/table-grid-quick-fun-ajax.js' }}" type="text/javascript"></script>
<script type="text/javascript">
    $(document).ready(function () {
        setInterval(function () {
            $('.addhiglight').closest("td").closest("tr").addClass('higlight');
        }, 800);
    });
</script>
@endsection