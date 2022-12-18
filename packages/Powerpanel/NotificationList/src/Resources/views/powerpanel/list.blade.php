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
@include('powerpanel.partials.breadcrumbs')
{!! csrf_field() !!}
<div class="row">
    <div class="col-md-12">
        <div class="portlet light portlet-fit portlet-datatable bordered">

            @if($iTotalRecords > 0)
            <div class="portlet-body">
                <div class="table-container">
                    {{-- <div class="table-actions-wrapper">
                        <div class="dataTables_filter">
                            <span>{{ trans('notificationlist::template.common.search') }}: </span>
                            <input type="search" class="form-control form-control-solid placeholder-no-fix" id="searchfilter" placeholder="Search by Name" name="searchfilter">
                        </div>
                    </div> --}}
                    <table class="new_table_desing table table-striped table-bordered table-hover table-checkable dataTable hide-mobile" id="datatable_ajax">
                        <thead>
                            <tr role="row" class="heading">
                                <th width="2%" align="center"><input type="checkbox" class="group-checkable"></th>
                                <th width="10%" align="center">{{ trans('Record Name') }}</th>
                                <th width="20%" align="center">Module Name</th>	
                                <th width="10%" align="center">{{ trans('Message') }}</th>
                                <th width="10%" align="center">{{ trans('Date/Time') }}</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>				
                    @can('notificationlist-delete')
                    <button class="btn-sm btn btn-outline red right_bottom_btn deleteMass" value="notification">{{ trans('notificationlist::template.common.delete') }}</button>
                    @endcan
                </div>
            </div>
            @else
            @include('powerpanel.partials.addrecordsection',['marketlink' => 'https://www.netclues.com/social-media-marketing', 'type'=>'notificationlist'])
            @endif
        </div>
    </div>
</div>
<div class="new_modal modal fade" id="noRecords" tabindex="-1" role="basic" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-vertical">	
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                    {{ trans('notificationlist::template.common.alert') }} 
                </div>
                <div class="modal-body text-center">{{ trans('notificationlist::template.notificationlistModule.noExport') }} </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-green-drake" data-dismiss="modal">{{ trans('notificationlist::template.common.ok') }}</button>
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
                    {{ trans('notificationlist::template.common.alert') }}
                </div>
                <div class="modal-body text-center">{{ trans('notificationlist::template.notificationlistModule.recordsExport') }}</div>
                <div class="modal-footer">
                    <div align="center">
                        <div class="md-radio-inline">
                            <div class="md-radio">
                                <input type="radio" value="selected_records" id="selected_records" name="export_type" class="md-radiobtn" checked="checked">
                                <label for="selected_records">
                                    <span class="inc"></span>
                                    <span class="check"></span>
                                    <span class="box"></span> {{ trans('notificationlist::template.notificationlistModule.selectedRecords') }}
                                </label>
                            </div>
                            <div class="md-radio">
                                <input type="radio" value="all_records" id="all_records" name="export_type" class="md-radiobtn">
                                <label for="all_records">
                                    <span class="inc"></span>
                                    <span class="check"></span>
                                    <span class="box"></span>{{ trans('notificationlist::template.notificationlistModule.allRecords') }} 
                                </label>
                            </div>
                        </div>
                    </div>
                    <button type="button" class="btn btn-green-drake" id="ExportRecord" data-dismiss="modal">{{ trans('notificationlist::template.common.ok') }} </button>
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
                    {{ trans('notificationlist::template.common.alert') }} 
                </div>
                <div class="modal-body text-center">{{ trans('notificationlist::template.notificationlistModule.leastRecord') }} </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-green-drake" data-dismiss="modal">{{ trans('notificationlist::template.common.ok') }} </button>
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
    var DELETE_URL = '{!! url("/powerpanel/notificationlist/DeleteRecord") !!}';
    var showChecker = true;
    @if (!$userIsAdmin)
    showChecker = false;
    @endif
</script>
<script src="{{ $CDN_PATH.'resources/global/plugins/jquery-cookie-master/src/jquery.cookie.js' }}" type="text/javascript"></script>	
<script src="{{ $CDN_PATH.'resources/global/plugins/datatables/datatables.min.js' }}" type="text/javascript"></script>
<script src="{{ $CDN_PATH.'resources/global/scripts/datatable.js' }}" type="text/javascript"></script>
<script src="{{ $CDN_PATH.'resources/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.js' }}" type="text/javascript"></script>
<script src="{{ $CDN_PATH.'resources/global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js' }}" type="text/javascript"></script>
<script src="{{ $CDN_PATH.'resources/pages/scripts/packages/notificationlist/notificationlist-datatables-ajax.js' }}" type="text/javascript"></script>
<script src="{{ $CDN_PATH.'resources/pages/scripts/custom.js' }}" type="text/javascript"></script>
<script src="{{ $CDN_PATH.'resources/global/plugins/highslide/highslide-with-html.js' }}" type="text/javascript"></script>
@endsection