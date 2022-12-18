@extends('powerpanel.layouts.app')
@section('title')
{{Config::get('Constant.SITE_NAME')}} - PowerPanel
@stop
@section('css')
<link href="{{ $CDN_PATH.'resources/global/plugins/datatables/datatables.min.css' }}" rel="stylesheet" type="text/css" />
<link href="{{ $CDN_PATH.'resources/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.css' }}" rel="stylesheet" type="text/css" />
<link href="{{ $CDN_PATH.'resources/global/plugins/highslide/highslide.css' }}" rel="stylesheet" type="text/css" />
<link href="{{ $CDN_PATH.'resources/global/plugins/bootstrap-datepicker/css/bootstrap-datepicker3.min.css' }}" rel="stylesheet" type="text/css"/>
<link href="{{ $CDN_PATH.'resources/global/plugins/bootstrap-daterangepicker/daterangepicker.min.css' }}" rel="stylesheet" type="text/css" />
@endsection
@section('content')

{!! csrf_field() !!}
<div class="row">
    <div class="col-md-12">
        <div class="title-dropdown_sec">
            <div class="title_bar">
                <div class="page-head">
                    <div class="page-title">
                        <h1>LIVE USERS</h1>
                    </div>
                    <ul class="page-breadcrumb breadcrumb">
                        <li>
                            <span aria-hidden="true" class="icon-home"></span>
                            <a href="{{ url('powerpanel') }}">Home</a>
                            <i class="fa fa-circle"></i>
                        </li>
                        <li class="active">Live Users</li>
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
                                        <span class="title">{!! trans('template.common.filterby') !!}:</span>
                                        <span class="select_input">

                                            <select class="form-control bs-select select2 category_filter" name="country" id="country">
                                                <option value="">Select Country</option>
                                                @foreach ($arrResultsCountry as $Country)
                                                <option value="{{ $Country['varCountry_name'] }}" >{{$Country['varCountry_name'] }}</option>
                                                @endforeach
                                            </select>
                                        </span>
                                        <span class="btn btn-icon-only btn-green-drake green-new" type="button" id="refresh" title="Reset">
                                            <i class="fa fa-refresh" aria-hidden="true"></i>
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-6 col-md-12 col-xs-12">
                                <a class="btn btn-green-drake pull-right btn-rh-refresh" id="refresh" title="Click to reset filters" href="javascript:;"><i class="fa fa-refresh"></i></a>
                                <a class="btn btn-green-drake pull-right btn-rh-search" id="liveUserRange" href="javascript:;"><i class="fa fa-search"></i></a>
                                <div class="event_datepicker pull-right">
                                    <div class="new_date_picker input-group input-large date-picker input-daterange" data-date-format="M/d/yyyy">
                                        <span class="input-group-addon"><i class="icon-calendar"></i></span>
                                        <input class="form-control datepicker" id="start_date" placeholder="{{ trans('liveuser::template.common.startdate') }}"  readonly="" type="text">
                                        <span class="input-group-addon to_addon"><i class="icon-calendar"></i></span>
                                        <input class="form-control datepicker" id="end_date" placeholder="{{ trans('liveuser::template.common.enddate') }}" readonly="" type="text">
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="portlet light portlet-fit portlet-datatable bordered">
            @if($iTotalRecords > 0)
            <div class="pw_tabs">
                <ul class="nav nav-tabs">
                    <li class="active"><a data-toggle="tab" href="#menu1" id="MenuItem1"><i class="icon-layers"></i>All</a></li>
                    <li class=""><a data-toggle="tab" href="#menu2" id="MenuItem2"><i class="fa fa-file-o"></i>Before 15 Days</a></li>
                </ul>
            </div>
            <div class="portlet-body">
                <div class="table-container">
                    <table class="new_table_desing table table-striped table-bordered table-hover table-checkable dataTable hide-mobile" id="datatable_ajax">
                        <thead>
                            <tr role="row" class="heading">
                                <th width="1%" align="center"><input type="checkbox" class="group-checkable"></th>
                                <th width="2%" align="left"></th>
                                <th width="10%" align="left">Country</th>
                                <th width="10%" align="center">{{ trans('template.common.ip') }}</th>
                                <th width="5%" align="center">Other Information</th>
                                <th width="10%" align="center">Date Time</th>
                                <th width="5%" align="center">Block / Unblock</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                    <a href="javascript:;" class="btn-sm btn btn-outline red right_bottom_btn blockMass">{{ trans('Block') }}</a>
                    <a href="#selectedRecords" class="btn-sm btn btn-green-drake right_bottom_btn ExportRecord" data-toggle="modal"> {{ trans('liveuser::template.liveUsersModule.export') }}</a>
                </div>
            </div>
            @else
            @include('powerpanel.partials.addrecordsection')
            @endif
        </div>
    </div>
</div>

<!-- END PAGE BASE CONTENT -->
<div class="new_modal modal fade" id="noRecords" tabindex="-1" role="basic" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-vertical">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                    {{ trans('newsletterlead::template.common.alert') }}
                </div>
                <div class="modal-body text-center">{{ trans('liveuser::template.liveUsersModule.noExport') }}</div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-green-drake" data-dismiss="modal">{{ trans('liveuser::template.common.ok') }}</button>
                </div>
            </div>
            <!-- /.modal-content -->
        </div>
    </div>
    <!-- /.modal-dialog -->
</div>

<div class="new_modal modal fade" id="selectedRecords" tabindex="-1" role="basic" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-vertical">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                    {{ trans('liveuser::template.common.alert') }}
                </div>
                <div class="modal-body text-center">{{ trans('liveuser::template.liveUsersModule.recordsExport') }}</div>
                <div class="modal-footer">
                    <div align="center">
                        <div class="md-radio-inline">
                            <div class="md-radio">
                                <input type="radio" value="selected_records" id="selected_records" name="export_type" class="md-radiobtn">
                                <label for="selected_records">
                                    <span class="inc"></span>
                                    <span class="check"></span>
                                    <span class="box"></span> {{ trans('liveuser::template.liveUsersModule.selectedRecords') }}
                                </label>
                            </div>
                            <div class="md-radio">
                                <input type="radio" value="all_records" id="all_records" name="export_type" class="md-radiobtn"  checked="checked">
                                <label for="all_records">
                                    <span class="inc"></span>
                                    <span class="check"></span>
                                    <span class="box"></span> {{ trans('liveuser::template.liveUsersModule.allRecords') }}
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
@include('powerpanel.partials.deletePopup',['module' => 'live-user'])
@include('powerpanel.partials.approveRecord')
@endsection
@section('scripts')
<script type="text/javascript">
    window.site_url = '{!! url("/") !!}';
    var MODULE_URL = '{!! url("/powerpanel/live-user") !!}';
    var DELETE_URL = '{!! url("/powerpanel/live-user/DeleteRecord") !!}';
    var BLOCK_Ml_URL = '{!! url("/powerpanel/live-user/BlockRecord") !!}';
</script>
<script src="{{ $CDN_PATH.'resources/global/plugins/jquery-cookie-master/src/jquery.cookie.js' }}" type="text/javascript"></script>
<script src="{{ $CDN_PATH.'resources/global/plugins/datatables/datatables.min.js' }}" type="text/javascript"></script>
<script src="{{ $CDN_PATH.'resources/global/scripts/datatable.js' }}" type="text/javascript"></script>
<script src="{{ $CDN_PATH.'resources/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.js' }}" type="text/javascript"></script>
<script src="{{ $CDN_PATH.'resources/global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js' }}" type="text/javascript"></script>
<script src="{{ $CDN_PATH.'resources/pages/scripts/packages/liveuser/liveusers-datatables-ajax.js' }}" type="text/javascript"></script>
<script src="{{ $CDN_PATH.'resources/pages/scripts/custom.js' }}" type="text/javascript"></script>
<script src="{{ $CDN_PATH.'resources/global/plugins/highslide/highslide-with-html.js' }}" type="text/javascript"></script>
<script type="text/javascript">
    var BLOCK_URL = '{!! url("/powerpanel/live-user/block_user") !!}';
    function Block(id) {
        $('#Approve .approveMsg').text("Are you sure you want to block this user on the website? Click on Yes to confirm.");
        $('#Approve1').show();
        $('#Approve').modal({
            backdrop: 'static',
            keyboard: false
        });
        $(document).on('click', '#Approve1', function () {
            $.ajax({
                type: 'POST',
                url: BLOCK_URL,
                data: 'id=' + id,
                success: function (msg) {
                    $('#Approved .approveMsg').text("The user has been successfully block on the website.");
                    $('#Approved').show();
                    $('#Approved').modal({
                        backdrop: 'static',
                        keyboard: false
                    });
                    $(document).on('click', '#ApprovedSuccess', function () {
                        $(".close").trigger('click');
                        var x = location.href;
                        window.location.href = x;
                    });
                }
            });
        });
    }
    var UN_BLOCK_URL = '{!! url("/powerpanel/live-user/un_block_user") !!}';
    function UnBlock(id) {
        $('#Approve .approveMsg').text("Are you sure you want to un-block this user on the website? Click on Yes to confirm.");
        $('#Approve1').show();
        $('#Approve').modal({
            backdrop: 'static',
            keyboard: false
        });
        $(document).on('click', '#Approve1', function () {
            $.ajax({
                type: 'POST',
                url: UN_BLOCK_URL,
                data: 'id=' + id,
                success: function (msg) {
                    $('#Approved .approveMsg').text("The user has been successfully un-block on the website.");
                    $('#Approved').show();
                    $('#Approved').modal({
                        backdrop: 'static',
                        keyboard: false
                    });
                    $(document).on('click', '#ApprovedSuccess', function () {
                        $(".close").trigger('click');
                        var x = location.href;
                        window.location.href = x;
                    });
                }
            });
        });
    }
    $(document).ready(function () {
			var today = moment.tz("{{Config::get('Constant.DEFAULT_TIME_ZONE')}}").format(DEFAULT_DT_FORMAT);
			$('#start_date').datepicker({
					autoclose: true,
					//startDate: today,
					minuteStep: 5
			}).on("changeDate", function (e) {
					$("#start_date").closest('.has-error').removeClass('has-error');
					$("#app_post_date-error").remove();
					$('#end_date').val('');
					var endingdate = $(this).val();
					var date = new Date(endingdate);
					var enddate = new Date(date.getFullYear(), date.getMonth(), date.getDate() + 1);
					$('#end_date').datepicker('remove');
					$('#end_date').datepicker({
							autoclose: true,
							startDate: enddate,
							minuteStep: 5,
                            format:DEFAULT_DT_FMT_FOR_DATEPICKER
					});
			});
			var endingdate = $('#start_date').val();
			var date = new Date(endingdate);
			var enddate = new Date(date.getFullYear(), date.getMonth(), date.getDate() + 1);
			$('#end_date').datepicker({
					autoclose: true,
					startDate: enddate,
					minuteStep: 5,
					format:DEFAULT_DT_FMT_FOR_DATEPICKER
			});
	});
</script>
<script src="{{ $CDN_PATH.'resources/global/plugins/moment.min.js' }}"></script>
<script src="{{ $CDN_PATH.'resources/global/plugins/moments-timezone.js' }}"></script>
@endsection
