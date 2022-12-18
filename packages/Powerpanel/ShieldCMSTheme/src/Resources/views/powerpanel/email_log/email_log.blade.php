@extends('powerpanel.layouts.app')
@section('title')
{{Config::get('Constant.SITE_NAME')}} - PowerPanel
@endsection
@section('css')
<link href="{{ $CDN_PATH.'resources/global/plugins/datatables/datatables.min.css' }}" rel="stylesheet" type="text/css" />
<link href="{{ $CDN_PATH.'resources/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.css' }}" rel="stylesheet" type="text/css" />
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
                        <h1>Manage EMAIL LOG</h1>                        
                    </div>   
                    <ul class="page-breadcrumb breadcrumb">
                        <li>
                            <span aria-hidden="true" class="icon-home"></span>
                            <a href="{{ url('powerpanel') }}">Home</a>
                            <i class="fa fa-circle"></i>
                        </li>
                        <li class="active">Manage Email log</li>
                    </ul>	
                    <a class="drop_toogle_arw" href="javascript:void(0);" data-toggle="collapse" data-target="#cmspage_id"><i class="la la-chevron-circle-up"></i></a>                                           
                </div> 
                 <!-- <div class="add_category_button pull-right">
                    <a title="Help" class="add_category" target="_blank" href="{{ url('assets/videos/Shield_CMS_WorkFlow.mp4')}}">
                        <span title="Help">Help</span> <i class="la la-question-circle"></i>
                    </a>
                </div> -->
            </div>
            <div id="cmspage_id" class="collapse in">
                <div class="collapse-inner">
                    <div class="portlet-title select_box filter-group">
                        <div class="row">
                            <div class="col-lg-6 col-md-12 col-xs-12">
                                <div class="portlet-lf-title">
                                    <div class="sub_select_filter" id="hidefilter">
                                        <span class="title" style="margin-right:0.5em;margin-bottom:5px;display:inline-block;">Filter By:</span>
                                        <select id="emailtypefilter" data-sort data-order class="bs-select select2 form-control input-inline input-medium input-sm">
                                            <option value=" ">Select Email Type</option>
                                            @if(isset($emailTypes) && $emailTypes != '')
                                            @foreach ($emailTypes as $types)
                                            <option value="{{ $types->id }}">{{ $types->varEmailType }}</option>
                                            @endforeach
                                            @endif
                                        </select>  
                                        <span class="btn btn-icon-only btn-green-drake green-new" type="button" id="refresh" title="Reset">
                                            <i class="fa fa-refresh" aria-hidden="true"></i>
                                        </span>
                                    </div>
                                </div>    
                            </div>

                        </div>  
                    </div>
                </div>
            </div> 
        </div>
        <!-- TITILE HEAD End... -->  
        <div class="portlet light portlet-fit portlet-datatable">
            @if($iTotalRecords > 0)

            <div class="portlet-body">
                <div class="table-container">
                    <div class="table-actions-wrapper">
                        <div class="search_rh_div pull-right">
                            <span>Search:</span>
                            <input type="search" class="form-control form-control-solid placeholder-no-fix" placeholder="Search by Email Type" id="searchfilter">
                        </div>
                    </div>
                    <table class="new_table_desing table table-striped table-bordered table-hover table-checkable" id="email_log_datatable_ajax">
                        <thead>
                            <tr role="row" class="heading">
                                <th width="2%" align="center"><input type="checkbox" class="group-checkable"></th>
                                <th width="10%" align="left">{{ trans('shiledcmstheme::template.emailLogModule.emailType') }}</th>
                                <th width="20%" align="left">{{ trans('shiledcmstheme::template.common.from') }}</th>
                                <th width="20%" align="left">{{ trans('shiledcmstheme::template.common.to') }}</th>
                                <th width="5%" align="left">{{ trans('shiledcmstheme::template.emailLogModule.isSent') }}</th>
                                <th width="5%" align="center">{{ trans('shiledcmstheme::template.common.attachment') }}</th>
                                <th width="10%" align="center">{{ trans('shiledcmstheme::template.emailLogModule.dateTime') }}</th>
                                <!-- <th width="15%">Action</th> -->
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                    @can('email-log-delete')
                    <a href="javascript:;" class="btn-sm btn btn-outline red right_bottom_btn deleteMass"> Delete
                    </a>
                    @endcan
                </div>
            </div>
            @else
            @include('powerpanel.partials.addrecordsection')
            @endif
        </div>
    </div>
</div>
<div class="new_modal modal fade DetailsEmailLog" tabindex="-1" aria-hidden="true">
</div>
@include('powerpanel.partials.deletePopup')
@endsection
@section('scripts')
<script type="text/javascript">
    window.site_url = '{!! url("/") !!}';
    var DELETE_URL = '{!! url("/powerpanel/email-log/DeleteRecord") !!}';
</script>
<script src="{{ $CDN_PATH.'resources/global/plugins/jquery-cookie-master/src/jquery.cookie.js' }}" type="text/javascript"></script>
<script src="{{ $CDN_PATH.'resources/global/scripts/datatable.js' }}" type="text/javascript"></script>
<script src="{{ $CDN_PATH.'resources/global/plugins/datatables/datatables.min.js' }}" type="text/javascript"></script>
<script src="{{ $CDN_PATH.'resources/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.js' }}" type="text/javascript"></script>
<script src="{{ $CDN_PATH.'resources/pages/scripts/table-email-log-ajax.js' }}" type="text/javascript"></script>
<script src="{{ $CDN_PATH.'resources/pages/scripts/custom.js' }}" type="text/javascript"></script>
<script type="text/javascript">
    $(document).ready(function () {
        $('#emailtypefilter').select2({
            placeholder: "Select Email Type"
        });
    });
</script>
@endsection