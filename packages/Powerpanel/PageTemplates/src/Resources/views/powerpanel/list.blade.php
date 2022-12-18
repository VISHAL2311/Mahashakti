@extends('powerpanel.layouts.app')
@section('title')
{{Config::get('Constant.SITE_NAME')}} - PowerPanel
@endsection
@section('css')
<link href="{{ $CDN_PATH.'resources/global/plugins/datatables/datatables.min.css' }}" rel="stylesheet" type="text/css"/>
<link href="{{ $CDN_PATH.'resources/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.css' }}" rel="stylesheet" type="text/css"/>
<link href="{{ $CDN_PATH.'resources/global/plugins/tooltips/tooltip.css' }}" rel="stylesheet" type="text/css"/>
<link href="{{ $CDN_PATH.'resources/global/plugins/highslide/highslide.css' }}" rel="stylesheet" type="text/css"/>
@endsection
@section('content')
<!--@include('powerpanel.partials.breadcrumbs')-->
<div class="row">
    <div class="col-md-12">

        <!-- TITILE HEAD START -->

        <div class="title-dropdown_sec">
            <div class="title_bar">
                <div class="page-head">
                    <div class="page-title">
                        <h1>Page Template </h1>   	
                    </div> 
                    <ul class="page-breadcrumb breadcrumb">
                        <li>
                            <span aria-hidden="true" class="icon-home"></span>
                            <a href="{{ url('powerpanel') }}">Home</a>
                            <i class="fa fa-circle"></i>
                        </li>
                        <li class="active">Page Template</li>
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
                    <div class="portlet-title select_box filter-group">
                        <div class="row">
                            <div class="col-lg-6 col-md-12 col-xs-12">
                                <div class="portlet-lf-title">
                                    <div class="sub_select_filter" id="hidefilter">
                                        <span class="title">{{ trans('pagetemplates::template.common.filterby') }}:</span>
                                        <span class="select_input">
                                            <select id="statusfilter" data-sort data-order class="bs-select select2">
                                                <option value=" ">{{ trans('pagetemplates::template.common.selectstatus') }}</option>
                                                <option value="Y">{{ trans('pagetemplates::template.common.publish') }}</option>
                                                <option value="N">{{ trans('pagetemplates::template.common.unpublish') }}</option>
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
                                    @can('page_template-create')
                                    <div class="add_category_button pull-right">
                                        <a class="add_category" href="{{ url('powerpanel/page_template/add') }}"><span>{{ trans('Add Page Template') }}</span> <i class="la la-plus"></i></a>
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
            @if($iTotalRecords > 0)
            <div class="portlet-body">
                <div class="table-container">
                    <div class="pw_tabs">
                        <ul class="nav nav-tabs"></ul>
                        <div class="col-md-12 col-sm-12 ">
                            <div class="row">
                                <div class="row col-md-6 col-sm-12 pull-right search_pages_div">
                                    <div class="search_rh_div">
                                        <span>{{ trans('pagetemplates::template.common.search') }}:</span>
                                        <input type="search" class="form-control form-control-solid placeholder-no-fix" placeholder="Search by Template name" id="searchfilter">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="tab-content setting-min">
                            <div id="menu1" class="tab-pane fade in active">

                                <table class="new_table_desing table table-striped table-bordered table-hover table-checkable dataTable hide-mobile" id="datatable_ajax">
                                    <thead>
                                        <tr role="row" class="heading">
                                            <th width="2%" align="center" ><input type="checkbox" class="group-checkable"></th>
                                            <th width="5%" align="left" >Template Name</th>
                                            <th width="10%" align="center">Date</th>
                                            <th width="5%" align="center" >{{ trans('pagetemplates::template.common.publish') }}</th>
                                            <th width="5%" align="center" >{{ trans('pagetemplates::template.common.actions') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody></tbody>
                                </table>
                            </div>
                           <button href="javascript:;" class="btn-sm btn btn-outline red right_bottom_btn deleteMass hide-btn-mob" value="C">
                        {{ trans('pagetemplates::template.common.delete') }}
                    </button>
                        </div>

                    </div>
                </div>
            </div>
            <!-- Modal -->
            <div class="new_modal modal fade" style="display: none" id="modalForm" role="dialog">
                <div class="modal-dialog">
                    <div class="modal-vertical">
                        <div class="modal-content">
                            <!-- Modal Header -->
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal">
                                    <span aria-hidden="true">&times;</span>
                                    <span class="sr-only">Close</span>
                                </button>
                                <h4 class="modal-title" id="myModalLabel">Quick Edit</h4>
                            </div>
                            <!-- Modal Body -->
                            <div class="modal-body form_pattern">
                                {!! Form::open(['method' => 'post','class'=>'QuickEditForm','id'=>'QuickEditForm']) !!}
                                {!! Form::hidden('id','',array('id' => 'id')) !!}
                                {!! Form::hidden('quickedit','',array('id' => 'quickedit')) !!}
                                <div class="form-group">
                                    <label for="name">Name <span aria-required="true" class="required"> * </span></label>
                                    {!! Form::text('name',  old('name') , array('id' => 'name', 'class' => 'form-control', 'placeholder'=>'Enter your name')) !!}
                                </div>
                                <div class="row">
                                    <div class="col-md-12">
                                        <label class="form_title">Search Ranking</label>
                                        <a href="javascript:;" data-toggle="tooltip" class="config" data-placement="bottom" data-original-title="{{ trans('pagetemplates::template.common.SearchEntityTools') }}" title="{{ trans('pagetemplates::template.common.SearchEntityTools') }}"><i class="fa fa-question"></i></a>
                                        <div class="wrapper search_rank">
                                            <label for="yes_radio" id="yes-lbl">High</label><input type="radio" value="1" name="search_rank" id="yes_radio">
                                            <label for="maybe_radio" id="maybe-lbl">Medium</label><input type="radio" value="2" name="search_rank" id="maybe_radio">
                                            <label for="no_radio" id="no-lbl">Low</label><input type="radio" value="3" name="search_rank" id="no_radio">
                                            <div class="toggle"></div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group form-md-line-input">
                                            <label class="control-label form_title">{{ trans('pagetemplates::template.common.startDateAndTime') }} <span aria-required="true" class="required"> * </span></label>
                                            <div class="input-group date form_meridian_datetime" data-date="{{ Carbon\Carbon::today()->format('Y-m-d') }}T15:25:00Z">
                                                <span class="input-group-btn date_default">
                                                    <button class="btn date-set fromButton" type="button">
                                                        <i class="fa fa-calendar"></i>
                                                    </button>
                                                </span>
                                                {!! Form::text('start_date_time', isset($Cmspage->dtDateTime)?date('Y-m-d H:i',strtotime($Cmspage->dtDateTime)):date('Y-m-d H:i'), array('class' => 'form-control','maxlength'=>160,'size'=>'16','id'=>'start_date_time','autocomplete'=>'off','onkeypress'=>"javascript: return KeycheckOnlyDate(event);",'onpaste'=>'return false')) !!}
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group form-md-line-input">
                                            <div class="input-group date  form_meridian_datetime expirydate" data-date="{{ Carbon\Carbon::today()->format('Y-m-d') }}T15:25:00Z">
                                                <label class="control-label form_title" >{{ trans('pagetemplates::template.common.endDateAndTime') }} <span aria-required="true" class="required"> * </span></label>
                                                <div class="pos_cal">
                                                    <span class="input-group-btn date_default">
                                                        <button class="btn date-set toButton" type="button">
                                                            <i class="fa fa-calendar"></i>
                                                        </button>
                                                    </span>
                                                    {!! Form::text('end_date_time', isset($Cmspage->dtEndDateTime)?date('Y-m-d H:i',strtotime($Cmspage->dtEndDateTime)):date('Y-m-d H:i'), array('class' => 'form-control','maxlength'=>160,'size'=>'16','id'=>'end_date_time','data-exp'=> '','data-newvalue','autocomplete'=>'off','onkeypress'=>"javascript: return KeycheckOnlyDate(event);",'onpaste'=>'return false')) !!}
                                                </div>
                                            </div>
                                            <label class="expdatelabel">
                                                <a id="noexpiry" name="noexpiry" href="javascript:void(0);">
                                                    <b class="expiry_lbl"></b>
                                                </a>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="submit" class="btn btn-green-drake" id="quick_submit" value="saveandexit">Submit</button>
                                </div>
                                {!! Form::close() !!}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @else
            @include('powerpanel.partials.addrecordsection',['type'=>Config::get('Constant.MODULE.TITLE'), 'adUrl' => url('powerpanel/page_template/add')])
            @endif
        </div>
    </div>
</div>
@include('powerpanel.partials.deletePopup')
@include('powerpanel.partials.approveRecord')
@endsection
@section('scripts')
<script type="text/javascript">
    window.site_url = '{!! url("/") !!}';
    var DELETE_URL = '{!! url("/powerpanel/page_template/DeleteRecord") !!}';
    var APPROVE_URL = '{!! url("/powerpanel/page_template/ApprovedData_Listing") !!}';
    $(document).ready(function () {
        $('[data-toggle="tooltip"]').tooltip();
    });
    var APPROVE_URL = '{!! url("/powerpanel/page_template/ApprovedData_Listing") !!}';
    var getChildData = window.site_url + "/powerpanel/page_template/getChildData";
    var getChildData_rollback = window.site_url + "/powerpanel/page_template/getChildData_rollback";
    var ApprovedData_Listing = window.site_url + "/powerpanel/page_template/ApprovedData_Listing";
    var Get_Comments = window.site_url + "/powerpanel/page_template/Get_Comments";

    var showChecker = true;
            @if (!$userIsAdmin)
            showChecker = false;
            @endif
</script>
<script src="{{ $CDN_PATH.'resources/global/plugins/jquery-cookie-master/src/jquery.cookie.js' }}" type="text/javascript"></script>
<script src="{{ $CDN_PATH.'resources/global/scripts/datatable.js' }}" type="text/javascript"></script>
<script src="{{ $CDN_PATH.'resources/global/plugins/datatables/datatables.min.js' }}" type="text/javascript"></script>
<script src="{{ $CDN_PATH.'resources/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.js' }}" type="text/javascript"></script>
<script src="{{ $CDN_PATH.'resources/global/plugins/tooltips/tooltip.js' }}" type="text/javascript"></script>
<script src="{{ $CDN_PATH.'resources/pages/scripts/packages/pagetemplates/table-pages-template-ajax.js' }}" type="text/javascript"></script>
<script src="{{ $CDN_PATH.'resources/pages/scripts/custom.js' }}" type="text/javascript"></script>
<script src="{{ $CDN_PATH.'resources/global/plugins/highslide/highslide-with-html.js' }}" type="text/javascript"></script>

@if(Auth::user()->hasRole('user_account'))
<script type="text/javascript">
    $(document).ready(function () {
        setInterval(function () {
            $('.checker').closest("td").hide();
            $('.checker').closest("th").hide();
        }, 800);
    });
    var moduleName = 'page_template';</script>
@endif
@endsection