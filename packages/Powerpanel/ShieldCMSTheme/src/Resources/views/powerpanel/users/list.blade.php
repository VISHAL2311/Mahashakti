@extends('powerpanel.layouts.app')
@section('title')
{{Config::get('Constant.SITE_NAME')}} - PowerPanel
@endsection
@section('css')
<link href="{{ $CDN_PATH.'resources/global/plugins/datatables/datatables.min.css' }}" rel="stylesheet" type="text/css" />
<link href="{{ $CDN_PATH.'resources/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.css' }}" rel="stylesheet" type="text/css" />
<link href="{{ $CDN_PATH.'resources/global/plugins/bootstrap-datepicker/css/bootstrap-datepicker3.min.css' }}" rel="stylesheet" type="text/css" />
@endsection
@section('content')
<!--@include('powerpanel.partials.breadcrumbs')-->
<div class="row">
    <div class="col-lg-12">
        <div class="title-dropdown_sec">
            <div class="title_bar">
                <div class="page-head">
                    <div class="page-title">
                        <h1>Manage Users</h1>                        
                    </div>   
                    <ul class="page-breadcrumb breadcrumb">
                        <li>
                            <span aria-hidden="true" class="icon-home"></span>
                            <a href="{{ url('powerpanel') }}">Home</a>
                            <i class="fa fa-circle"></i>
                        </li>
                        <li class="active">Manage Users</li>
                    </ul>	
                    <a class="drop_toogle_arw" href="javascript:void(0);" data-toggle="collapse" data-target="#cmspage_id"><i class="la la-chevron-circle-up"></i></a>                                           
                </div> 
                <div class="add_category_button pull-right" style="display:none;">
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
                                        <span class="title">{{  trans('shiledcmstheme::template.common.filterby') }}:</span>
                                        <span class="select_input">
                                            <select id="statusfilter" data-sort data-order class="bs-select select2">
                                                <option value=" ">{!!  trans('shiledcmstheme::template.common.selectstatus') !!}</option>
                                                <option value="Y">{!!  trans('shiledcmstheme::template.common.publish') !!}</option>
                                                <option value="N">{!!  trans('shiledcmstheme::template.common.unpublish') !!}</option>
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
                                    @can('users-create')
                                    <div class="add_category_button pull-right">
                                        <a class="add_category" href="{{ route('powerpanel.users.add') }}" title="{{  trans('shiledcmstheme::template.userModule.createUser') }}"><span>{{  trans('shiledcmstheme::template.userModule.createUser') }}</span> <i class="la la-plus"></i></a>
                                    </div>
                                    @endcan
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div> 
        </div>
        @if(Session::has('message'))
        <div class="alert alert-success">
            <button class="close" data-close="alert"></button>
            {{ Session::get('message') }}
        </div>
        @endif

        <div class="portlet light portlet-fit portlet-datatable">
            @if($iTotalRecords > 0)
            <!--<div class="portlet-title select_box">
                    @can('users-create')
                    <div class="pull-right">
                            <a class="btn btn-green-drake" href="{{ route('powerpanel.users.add') }}">{{  trans('shiledcmstheme::template.userModule.createUser') }} </a>
                    </div>
                    @endcan
            </div>-->
            <div class="portlet-body">
                <div class="table-container">
                    @if ($message = Session::get('success'))
                    <div class="alert alert-success">
                        <p>{{ $message }}</p>
                    </div>
                    @endif
                    <!--<p class="highlite_row"> {{  trans('shiledcmstheme::template.userModule.listingNote') }}</p>-->
                    <div class="table-actions-wrapper">
                        <div class="search_rh_div pull-right">
                            <span>{{  trans('shiledcmstheme::template.common.search') }}:</span>
                            <input type="search" class="form-control form-control-solid placeholder-no-fix" placeholder="Search by Name" id="searchfilter">
                        </div>
                    </div>
                    <table class="new_table_desing table table-striped table-bordered table-hover table-checkable hide-mobile" id="datatable_ajax">
                        <thead>
                            <tr role="row" class="heading">
                                <th width="2%" align="center">
                                    <input type="checkbox" class="group-checkable">
                                </th>
                                <th width="10%" align="left">{{  trans('shiledcmstheme::template.common.name') }} </th>
                                <th width="20%" align="left">{{  trans('shiledcmstheme::template.common.email') }} </th>
                                <th width="15%" align="left">Reset</th>
                                <th width="5%" align="left">{{  trans('shiledcmstheme::template.common.roles') }} </th>
                                <th width="5%" align="center">2-Step Verification</th>
                                <th width="5%" align="center">{{  trans('shiledcmstheme::template.common.publish') }}</th>
                                <th width="10%" align="right">{{  trans('shiledcmstheme::template.common.actions') }} </th>
                            </tr>
                        </thead>
                        <tbody> </tbody>
                    </table>
                    @can('users-delete')
                    <a href="javascript:;" class="btn-sm btn red btn-outline right_bottom_btn deleteMass hide-btn-mob">{{  trans('shiledcmstheme::template.common.delete') }}
                    </a>
                    @endcan
                </div>
                {{-- {!! $data->render() !!} --}}
            </div>
            @else
            @include('powerpanel.partials.addrecordsection',['type'=>Config::get('Constant.MODULE.TITLE'), 'adUrl' => url('powerpanel/users/add')])
            @endif
        </div>
    </div>
</div>
@include('powerpanel.partials.deletePopup')
@endsection
@section('scripts')
<script type="text/javascript">
    window.site_url = '{!! url("/") !!}';
    var DELETE_URL = '{!! url("/powerpanel/users/DeleteRecord") !!}';
</script>
<script src="{{ $CDN_PATH.'resources/global/plugins/jquery-cookie-master/src/jquery.cookie.js' }}" type="text/javascript"></script>
<script src="{{ $CDN_PATH.'resources/global/scripts/datatable.js' }}" type="text/javascript"></script>
<script src="{{ $CDN_PATH.'resources/global/plugins/datatables/datatables.min.js' }}" type="text/javascript"></script>
<script src="{{ $CDN_PATH.'resources/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.js' }}" type="text/javascript"></script>
<script src="{{ $CDN_PATH.'resources/global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js' }}" type="text/javascript"></script>
<script src="{{ $CDN_PATH.'resources/pages/scripts/packages/users/users-datatables-ajax.js' }}" type="text/javascript"></script>
<script src="{{ $CDN_PATH.'resources/pages/scripts/custom.js' }}" type="text/javascript"></script>
@endsection