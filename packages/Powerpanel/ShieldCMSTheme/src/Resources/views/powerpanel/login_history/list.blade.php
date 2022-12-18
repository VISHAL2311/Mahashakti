@extends('powerpanel.layouts.app')
@section('title')
{{Config::get('Constant.SITE_NAME')}} - PowerPanel
@stop
@section('css')
<link href="{{ $CDN_PATH.'resources/global/plugins/datatables/datatables.min.css' }}" rel="stylesheet" type="text/css" />
<link href="{{ $CDN_PATH.'resources/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.css' }}" rel="stylesheet" type="text/css" />
<link href="{{ $CDN_PATH.'resources/global/plugins/highslide/highslide.css' }}" rel="stylesheet" type="text/css" />
@endsection
@section('content')
<!-- @include('powerpanel.partials.breadcrumbs') -->
{!! csrf_field() !!}
<div class="row">
    <div class="col-md-12">
        <!-- TITILE HEAD START -->
		<div class="title-dropdown_sec">
			@if (File::exists(base_path() . '/resources/views/powerpanel/partials/listbreadcrumbs.blade.php') != null)
			@include('powerpanel.partials.listbreadcrumbs',['ModuleName'=>'Manage Login History'])
			@endif
		</div>
        <div class="portlet light portlet-fit portlet-datatable bordered">

            <div class="portlet-body">
                <div class="table-container">
                    <div class="table-actions-wrapper">
                        <div class="search_rh_div pull-right">
                            <span>{{  trans('shiledcmstheme::template.common.search') }}: </span>
                            <input type="search" class="form-control form-control-solid placeholder-no-fix" placeholder="Search by Name" id="searchfilter" name="searchfilter">
                        </div>
                    </div>
                    <table class="new_table_desing table table-striped table-bordered table-hover table-checkable dataTable" id="datatable_ajax">
                        <thead>
                            <tr role="row" class="heading">
                                <th width="2%" align="center"><input type="checkbox" class="group-checkable"></th>
                                <th width="10%" align="left"></th>
                                <th width="10%" align="left">Country</th>
                                <th width="20%" align="left">{{  trans('shiledcmstheme::template.common.name') }}</th>							
                                <th width="20%" align="left">{{  trans('shiledcmstheme::template.common.email') }}</th>
                                <th width="15%" align="center">{{  trans('shiledcmstheme::template.common.ip') }}</th>
                                <th width="20%" align="center">Login Time</th>
                                <th width="20%" align="center">LogOut Time</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                    @can('login-history-delete')
                    @if($iTotalRecords > 0)
                    <a href="javascript:;" class="btn-sm btn btn-outline red right_bottom_btn deleteMass">Delete
                    </a>
                    @endif
                    @endcan
                </div>
            </div>
        </div>
    </div>
</div>
<div class="new_modal modal fade" id="noRecords" tabindex="-1" role="basic" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-vertical">	
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                    Alert
                </div>
                <div class="modal-body text-center">No Records to export!</div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-green-drake" data-dismiss="modal">OK</button>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="new_modal modal fade" id="selectedRecords" tabindex="-1" role="basic" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-vertical">	
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                    Alert
                </div>
                <div class="modal-body text-center">Which records do you want to export?</div>
                <div class="modal-footer">
                    <div align="center">
                        <div class="md-radio-inline">
                            <div class="md-radio">
                                <input type="radio" value="selected_records" id="selected_records" name="export_type" class="md-radiobtn" checked="checked">
                                <label for="selected_records">
                                    <span class="inc"></span>
                                    <span class="check"></span>
                                    <span class="box"></span> Selected Records
                                </label>
                            </div>
                            <div class="md-radio">
                                <input type="radio" value="all_records" id="all_records" name="export_type" class="md-radiobtn">
                                <label for="all_records">
                                    <span class="inc"></span>
                                    <span class="check"></span>
                                    <span class="box"></span> All Records
                                </label>
                            </div>
                        </div>
                    </div>
                    <button type="button" class="btn btn-green-drake" id="ExportRecord" data-dismiss="modal">OK</button>
                </div>
            </div>
            <!-- /.modal-content -->
        </div>
    </div>
    <!-- /.modal-dialog -->
</div>
<div class="new_modal modal fade" id="noSelectedRecords" tabindex="-1" role="basic" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-vertical">	
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                    Alert
                </div>
                <div class="modal-body text-center">Please selecte at list one record.</div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-green-drake" data-dismiss="modal">OK</button>
                </div>
            </div>
            <!-- /.modal-content -->
        </div>
    </div>
    <!-- /.modal-dialog -->
</div>
<!-- /.modal -->
@include('powerpanel.partials.deletePopup')
@endsection
@section('scripts')
<script type="text/javascript">
    window.site_url = '{!! url("/") !!}';
    var MODULE_URL = '{!! url("/powerpanel/login-history/DeleteRecord") !!}';
</script>
<script src="{{ $CDN_PATH.'resources/global/plugins/jquery-cookie-master/src/jquery.cookie.js' }}" type="text/javascript"></script>	
<script src="{{ $CDN_PATH.'resources/global/plugins/datatables/datatables.min.js' }}" type="text/javascript"></script>
<script src="{{ $CDN_PATH.'resources/global/scripts/datatable.js' }}" type="text/javascript"></script>
<script src="{{ $CDN_PATH.'resources/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.js' }}" type="text/javascript"></script>
<script src="{{ $CDN_PATH.'resources/global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js' }}" type="text/javascript"></script>
<script src="{{ $CDN_PATH.'resources/pages/scripts/packages/login_history/loginhistory-datatables-ajax.js' }}" type="text/javascript"></script>
<script src="{{ $CDN_PATH.'resources/pages/scripts/packages/login_history/loginhistoryfunctions.js' }}" type="text/javascript"></script>
<script src="{{ $CDN_PATH.'resources/global/plugins/highslide/highslide-with-html.js' }}" type="text/javascript"></script>
@endsection