@extends('powerpanel.layouts.app')
@section('title')
{{Config::get('Constant.SITE_NAME')}} - PowerPanel
@stop
@section('css')
<link href="{{ $CDN_PATH.'resources/global/plugins/datatables/datatables.min.css' }}" rel="stylesheet" type="text/css" />
<link href="{{ $CDN_PATH.'resources/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.css' }}" rel="stylesheet" type="text/css" />
<link href="{{ url('resources/global/plugins/bootstrap-datepicker/css/bootstrap-datepicker3.min.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ $CDN_PATH.'resources/global/plugins/highslide/highslide.css' }}" rel="stylesheet" type="text/css" />
@endsection
@section('content')
{!! csrf_field() !!}
<div class="row">
    <div class="col-md-12">


    <div class="title-dropdown_sec">
				@if (File::exists(base_path() . '/resources/views/powerpanel/partials/listbreadcrumbs.blade.php') != null)
				@include('powerpanel.partials.listbreadcrumbs',['ModuleName'=>'Form Builder Lead'])
				@endif
				
				<div id="cmspage_id" class="collapse in">
					<div class="collapse-inner">
						<div class="portlet-title select_box filter-group">
							<div class="row">
								<div class="col-lg-6 col-md-12 col-xs-12">
									<div class="portlet-lf-title">
                                        <!-- anything -->
									</div>    
								</div>
								<div class="col-lg-6 col-md-12 col-xs-12">
									<div class="portlet-rh-title">
                                        <a class="btn btn-green-drake pull-right btn-rh-refresh" id="refresh" title="{!! trans('bookappointment::template.common.altResetFilters') !!}" href="javascript:;"><i class="fa fa-refresh"></i></a>
                                        <a class="btn btn-green-drake pull-right btn-rh-search" style="margin-right:15px;" id="formbuilderRange" href="javascript:;"><i class="fa fa-search"></i></a>
                                        <div class="event_datepicker pull-right">
                                            <div class="new_date_picker input-group input-large date-picker input-daterange" data-date-format="{{Config::get('Constant.DEFAULT_DATE_FORMAT')}}">
                                                <span class="input-group-addon"><i class="icon-calendar"></i></span>
                                                <input type="text" class="form-control datepicker" id="start_date" name="start_date" placeholder="From Date" readonly>
                                                <span class="input-group-addon"><i class="icon-calendar"></i></span>
                                                <input type="text" class="form-control datepicker" id="end_date" name="end_date" placeholder="To Date" readonly>
                                            </div>
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
            <div class="portlet-body">
                <div class="table-container">
                    <div class="table-actions-wrapper">
                        <div class="search_rh_div pull-right">
                            <span>{{ trans('formbuilderlead::template.common.search') }}: </span>
                            <input type="search" class="form-control form-control-solid placeholder-no-fix" id="searchfilter" placeholder="Search by Name" name="searchfilter">
                        </div>
                    </div>
                    <table class="new_table_desing table table-striped table-bordered table-hover table-checkable dataTable" id="datatable_ajax">
                        <thead>
                            <tr role="row" class="heading">
                                <th width="2%" align="center"><input type="checkbox" class="group-checkable"></th>
                                <th width="10%" align="left">{{ trans('Form Name') }}</th>
                                <th width="10%" align="left">{{ trans('Admin Email') }}</th>
                                <th width="5%" align="center">{{ trans('Contents') }}</th>
                                <th width="8%" align="center">{{ trans('formbuilderlead::template.common.ip') }}</th>
                                <th width="10%" align="center">{{ trans('formbuilderlead::template.common.received_date_time') }}</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>				
                    @can('formbuilder-lead-delete')
                    <a href="javascript:;" class="btn-sm btn btn-outline red right_bottom_btn deleteMass">{{ trans('formbuilderlead::template.common.delete') }}</a>
                    @endcan
                    <a href="#selectedRecords" class="btn-sm btn btn-green-drake right_bottom_btn ExportRecord" data-toggle="modal">{{ trans('formbuilderlead::template.formbuilderleadModule.export') }}</a>				
                </div>
            </div>
            @else
            @include('powerpanel.partials.addrecordsection',['marketlink' => 'https://www.netclues.com/social-media-marketing', 'type'=>'formbuilder'])
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
                    {{ trans('formbuilderlead::template.common.alert') }} 
                </div>
                <div class="modal-body text-center">{{ trans('formbuilderlead::template.formbuilderleadModule.noExport') }} </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-green-drake" data-dismiss="modal">{{ trans('formbuilderlead::template.common.ok') }}</button>
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
                    {{ trans('formbuilderlead::template.common.alert') }}
                </div>
                <div class="modal-body text-center">{{ trans('formbuilderlead::template.formbuilderleadModule.recordsExport') }}</div>
                <div class="modal-footer">
                    <div align="center">
                        <div class="md-radio-inline">
                            <div class="md-radio">
                                <input type="radio" value="selected_records" id="selected_records" name="export_type" class="md-radiobtn" checked="checked">
                                <label for="selected_records">
                                    <span class="inc"></span>
                                    <span class="check"></span>
                                    <span class="box"></span> {{ trans('formbuilderlead::template.formbuilderleadModule.selectedRecords') }}
                                </label>
                            </div>
                            <div class="md-radio">
                                <input type="radio" value="all_records" id="all_records" name="export_type" class="md-radiobtn">
                                <label for="all_records">
                                    <span class="inc"></span>
                                    <span class="check"></span>
                                    <span class="box"></span>{{ trans('formbuilderlead::template.formbuilderleadModule.allRecords') }} 
                                </label>
                            </div>
                        </div>
                    </div>
                    <button type="button" class="btn btn-green-drake" id="ExportRecord" data-dismiss="modal">{{ trans('formbuilderlead::template.common.ok') }} </button>
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
                    {{ trans('formbuilderlead::template.common.alert') }} 
                </div>
                <div class="modal-body text-center">{{ trans('formbuilderlead::template.formbuilderleadModule.leastRecord') }} </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-green-drake" data-dismiss="modal">{{ trans('formbuilderlead::template.common.ok') }} </button>
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
    var DELETE_URL = '{!! url("/powerpanel/formbuilder-lead/DeleteRecord") !!}';
     <?php if (isset($_REQUEST['id'])) { ?>
            var searchid = '<?php echo $_REQUEST['id'] ?>';
    <?php } else { ?>
            var searchid = '';
    <?php } ?>
</script>
<script src="{{ $CDN_PATH.'resources/global/plugins/jquery-cookie-master/src/jquery.cookie.js' }}" type="text/javascript"></script>	
<script src="{{ $CDN_PATH.'resources/global/plugins/datatables/datatables.min.js' }}" type="text/javascript"></script>
<script src="{{ $CDN_PATH.'resources/global/scripts/datatable.js' }}" type="text/javascript"></script>
<script src="{{ $CDN_PATH.'resources/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.js' }}" type="text/javascript"></script>
<script src="{{ $CDN_PATH.'resources/global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js' }}" type="text/javascript"></script>
<script src="{{ $CDN_PATH.'resources/pages/scripts/packages/formbuilderlead/formbuilderlead-datatables-ajax.js' }}" type="text/javascript"></script>
<script src="{{ $CDN_PATH.'resources/pages/scripts/custom.js' }}" type="text/javascript"></script>
<script src="{{ $CDN_PATH.'resources/global/plugins/highslide/highslide-with-html.js' }}" type="text/javascript"></script>
    <script type="text/javascript">
		$(document).ready(function () {
			var today = moment.tz("{{Config::get('Constant.DEFAULT_TIME_ZONE')}}").format(DEFAULT_DT_FORMAT);
			$('#start_date').datepicker({
					autoclose: true,
					//startDate: today,
					minuteStep: 5,
					format: DEFAULT_DT_FMT_FOR_DATEPICKER
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
							format: DEFAULT_DT_FMT_FOR_DATEPICKER
					});
            });
			var endingdate = $('#start_date').val();
			var date = new Date(endingdate);
			var enddate = new Date(date.getFullYear(), date.getMonth(), date.getDate() + 1);
			$('#end_date').datepicker({
					autoclose: true,
					startDate: enddate,
					minuteStep: 5,
					format: DEFAULT_DT_FMT_FOR_DATEPICKER
            });
		});
    </script>
@endsection