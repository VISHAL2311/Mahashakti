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
    <div class="col-md-12">
        <!-- <div class="title_bar">
            <div class="page-head">
                <div class="page-title">
                    <h1>Roles</h1>                        
                </div>   
            </div> 
            <ul class="page-breadcrumb breadcrumb">
                <li>
                    <span aria-hidden="true" class="icon-home"></span>
                    <a href="{{ url('powerpanel') }}">Home</a>
                    <i class="fa fa-circle"></i>
                </li>
                <li class="active">Roles</li>
                <div class="pull-right">
                @can('roles-create')
                <a class="add_category" href="{{ route('powerpanel.roles.add') }}"><span>{{ trans('rolemanager::template.roleModule.createRole') }}</span> <i class="la la-plus"></i></a>
                @endcan
            </div>
            </ul>
            
        </div> -->
        <div class="title-dropdown_sec">
            @if (File::exists(base_path() . '/resources/views/powerpanel/partials/listbreadcrumbs.blade.php') != null)
            @include('powerpanel.partials.listbreadcrumbs',['ModuleName'=>'Manage Roles'])
            @endif
		</div>
        @if(Session::has('message'))
        <div class="alert alert-success">
            <button class="close" data-close="alert"></button>
            {{ Session::get('message') }}
        </div>
        @endif
        <!-- Begin: life time stats -->
        <div class="portlet light portlet-fit portlet-datatable">
            <div class="portlet-title select_box">
                <div class="pull-right">
                    @can('roles-create')
                    <a class="add_category" href="{{ route('powerpanel.roles.add') }}" title="{{ trans('rolemanager::template.roleModule.createRole') }}"><span>{{ trans('rolemanager::template.roleModule.createRole') }}</span> <i class="la la-plus"></i></a>
                    @endcan
                </div>
            </div>
            @if($iTotalRecords > 0)
            <div class="portlet-body">
                @if ($message = Session::get('success'))
                <div class="alert alert-success">
                    <p>{{ $message }}</p>
                </div>
                @endif
                <div class="table-container">
                    <div class="table-actions-wrapper">      
                        <div class="search_rh_div pull-right">
                            <span>{{ trans('rolemanager::template.common.search') }} :</span>
                            <input type="search" class="form-control form-control-solid placeholder-no-fix" placeholder="Search by Name" id="searchfilter">
                        </div>                        
                    </div>
                    <table class="new_table_desing table table-striped table-bordered table-hover table-checkable hide-mobile" id="datatable_ajax">
                        <thead>
                            <tr role="row" class="heading">
                                <th width="3%" align="center">
                                    <input type="checkbox" class="group-checkable">
                                </th>
                                <th width="40%" align="left"> {{ trans('rolemanager::template.common.name') }}  </th>
                                <th width="20%" align="center"> Admin / User  </th>
                                <th width="20%" align="center"> Modified Date  </th>
                                <th width="17%" align="right"> {{ trans('rolemanager::template.common.actions') }}  </th>
                            </tr>
                        </thead>
                        <tbody> </tbody>
                    </table>
                    @can('roles-delete')
                    <a href="javascript:;" class="btn-sm btn btn-outline red right_bottom_btn deleteMass">Delete</a>
                    @endcan
                </div>
            </div>
            @else
            @include('powerpanel.partials.addrecordsection',['type'=>Config::get('Constant.MODULE.TITLE'), 'adUrl' => url('powerpanel/roles/add')])
            @endif
        </div>
    </div>
</div>
<div class="clearfix"></div>
<!-- /.modal -->
@include('powerpanel.partials.deletePopup')
@endsection
@section('scripts')
<script type="text/javascript">
    window.site_url = '{!! url("/") !!}';
    var DELETE_URL = '{!! url("/powerpanel/roles/DeleteRecord") !!}';
</script>
<script src="{{ $CDN_PATH.'resources/global/plugins/jquery-cookie-master/src/jquery.cookie.js' }}" type="text/javascript"></script>
<script src="{{ $CDN_PATH.'resources/global/scripts/datatable.js' }}" type="text/javascript"></script>
<script src="{{ $CDN_PATH.'resources/global/plugins/datatables/datatables.min.js' }}" type="text/javascript"></script>
<script src="{{ $CDN_PATH.'resources/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.js' }}" type="text/javascript"></script>
<script src="{{ $CDN_PATH.'resources/global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js' }}" type="text/javascript"></script>
<script src="{{ $CDN_PATH.'resources/pages/scripts/packages/rolemanager/table-role_manager-ajax.js' }}" type="text/javascript"></script>
<script src="{{ $CDN_PATH.'resources/pages/scripts/custom.js' }}" type="text/javascript"></script>
@endsection