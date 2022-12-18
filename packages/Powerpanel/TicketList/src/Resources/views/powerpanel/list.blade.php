@extends('powerpanel.layouts.app')
@section('title')
	{{Config::get('Constant.SITE_NAME')}} - PowerPanel
@stop
@section('css')
<link href="{{ $CDN_PATH.'resources/global/plugins/datatables/datatables.min.css' }}" rel="stylesheet" type="text/css" />
<link href="{{ $CDN_PATH.'resources/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.css' }}" rel="stylesheet" type="text/css" />
<link href="{{ $CDN_PATH.'resources/global/plugins/fancybox/source/helpers/jquery.fancybox-thumbs.css' }}" rel="stylesheet" type="text/css"/>
<link href="{{ $CDN_PATH.'resources/global/plugins/highslide/highslide.css' }}" rel="stylesheet" type="text/css" />

@endsection
@section('content')
@include('powerpanel.partials.breadcrumbs')
{!! csrf_field() !!}
<div class="row">
	<div class="col-md-12">
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
				<div class="table-actions-wrapper">
					<div class="search_rh_div pull-right">
						<span>{{ trans('ticketlist::template.common.search') }}: </span>
						<input type="search" class="form-control form-control-solid placeholder-no-fix" id="searchfilter" placeholder="Search by Name" name="searchfilter">
					</div>
				</div>
				<table class="new_table_desing table table-striped table-bordered table-hover table-checkable dataTable hide-mobile" id="datatable_ajax">
					<thead>
						<tr role="row" class="heading">
							<th width="2%" align="center"><input type="checkbox" class="group-checkable"></th>
							<th width="15%" align="left">{{ trans('ticketlist::template.SubmitTicketsModule.Title') }}</th>							
							<th width="10%" align="left">{{ trans('ticketlist::template.SubmitTicketsModule.Type') }}</th>
							<th width="10%" align="center">{{ trans('ticketlist::template.SubmitTicketsModule.Image') }}</th>
							<th width="10%" align="center">{{ trans('ticketlist::template.SubmitTicketsModule.Captcher') }}</th>
							
							<th width="5%" align="center">{{ trans('ticketlist::template.SubmitTicketsModule.message') }}</th>
							<th width="5%" align="center">{{ trans('ticketlist::template.SubmitTicketsModule.Link') }}</th>
							<th width="15%" align="center">Status</th>
							<th width="15%" align="center">{{ trans('ticketlist::template.common.received_date_time') }}</th>
						</tr>
					</thead>
					<tbody></tbody>
				</table>				
				@can('submit-tickets-delete')
				<a href="javascript:;" class="btn-sm btn btn-outline red right_bottom_btn deleteMass">{{ trans('ticketlist::template.common.delete') }}</a>
				@endcan
				<a href="#selectedRecords" class="btn-sm btn btn-green-drake right_bottom_btn ExportRecord" data-toggle="modal">{{ trans('ticketlist::template.SubmitTicketsModule.export') }}</a>				
			</div>
		</div>
		@else
			@include('powerpanel.partials.addrecordsection',['marketlink' => 'https://www.netclues.com/social-media-marketing', 'type'=>'contact'])
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
							{{ trans('ticketlist::template.common.alert') }} 
					</div>
					<div class="modal-body text-center">{{ trans('ticketlist::template.SubmitTicketsModule.noExport') }} </div>
					<div class="modal-footer">
							<button type="button" class="btn btn-green-drake" data-dismiss="modal">{{ trans('ticketlist::template.common.ok') }}</button>
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
						{{ trans('ticketlist::template.common.alert') }}
					</div>
					<div class="modal-body text-center">{{ trans('ticketlist::template.SubmitTicketsModule.recordsExport') }}</div>
					<div class="modal-footer">
						<div align="center">
							<div class="md-radio-inline">
								<div class="md-radio">
									<input type="radio" value="selected_records" id="selected_records" name="export_type" class="md-radiobtn" checked="checked">
									<label for="selected_records">
										<span class="inc"></span>
										<span class="check"></span>
										<span class="box"></span> {{ trans('ticketlist::template.SubmitTicketsModule.selectedRecords') }}
									</label>
								</div>
								<div class="md-radio">
									<input type="radio" value="all_records" id="all_records" name="export_type" class="md-radiobtn">
									<label for="all_records">
										<span class="inc"></span>
										<span class="check"></span>
										<span class="box"></span>{{ trans('ticketlist::template.SubmitTicketsModule.allRecords') }} 
									</label>
								</div>
							</div>
						</div>
						<button type="button" class="btn btn-green-drake" id="ExportRecord" data-dismiss="modal">{{ trans('ticketlist::template.common.ok') }} </button>
					</div>
			</div>
			<!-- /.modal-content -->
		</div>
	</div>
	<!-- /.modal-dialog -->
</div>


<div class="new_modal modal fade bs-modal-md" id="leadReplyModel" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-md">
        <div class="modal-vertical">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                    Status
                </div>
                <div class="modal-body replybody">
                    {!! Form::open(['method' => 'post','class'=>'leadReplyForm','id'=>'leadReplyForm']) !!}
                    {!! Form::hidden('reply_lead_Id','',array('id' => 'reply_lead_Id')) !!}
                    {!! Form::hidden('reply_lead_name','',array('id' => 'reply_lead_name')) !!}
                    {!! Form::hidden('ticketStatus','',array('id' => 'ticketStatus')) !!}
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="to">To: <span aria-required="true" class="required"> * </span></label>
                                {!! Form::text('reply_to_email',  old('reply_to_email') , array('id' => 'reply_to_email', 'class' => 'form-control', 'placeholder'=>'*User Email ID','readonly'=>'readonly')) !!}
                            </div>
                            <div class="form-group">
                                <label for="subject">Subject: <span aria-required="true" class="required"> * </span></label>
                                {!! Form::text('reply_to_subject',  old('reply_to_subject') , array('id' => 'reply_to_subject', 'class' => 'form-control', 'placeholder'=>'*Subject')) !!}
                            </div>
                            <div class="form-group">
                                <label for="subject">Message: <span aria-required="true" class="required"> * </span></label>
                                {!! Form::textarea('reply_to_message' ,old('reply_to_message'), array('class'=>'form-control','id'=>'reply_to_message','rows'=>'3','maxlength'=>'500')) !!}
                            </div>
                            <div class="success"></div>
                            <label class="error"></label>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <button type="submit" class="btn btn-green-drake" id="lead_submit" value="saveandexit">Submit</button>
                            </div>
                        </div>
                    </div>
                    {!! Form::close() !!}
                </div>
            </div>
        </div>
    </div>
</div>


<div class="new_modal modal fade" id="noSelectedRecords" tabindex="-1" role="basic" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-vertical">	
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
						{{ trans('ticketlist::template.common.alert') }} 
				</div>
				<div class="modal-body text-center">{{ trans('ticketlist::template.SubmitTicketsModule.leastRecord') }} </div>
				<div class="modal-footer">
					<button type="button" class="btn btn-green-drake" data-dismiss="modal">{{ trans('ticketlist::template.common.ok') }} </button>
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
		window.site_url =  '{!! url("/") !!}';
		var DELETE_URL =  '{!! url("/powerpanel/submit-tickets/DeleteRecord") !!}';
                var Email_reply_URL = '{!! url("/powerpanel/submit-tickets/emailreply") !!}';
	</script>
	<script src="{{ $CDN_PATH.'resources/global/plugins/jquery-cookie-master/src/jquery.cookie.js' }}" type="text/javascript"></script>	
	<script src="{{ $CDN_PATH.'resources/global/plugins/datatables/datatables.min.js' }}" type="text/javascript"></script>
	<script src="{{ $CDN_PATH.'resources/global/scripts/datatable.js' }}" type="text/javascript"></script>
	<script src="{{ $CDN_PATH.'resources/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.js' }}" type="text/javascript"></script>
	<script src="{{ $CDN_PATH.'resources/global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js' }}" type="text/javascript"></script>
	<script src="{{ $CDN_PATH.'resources/pages/scripts/packages/ticketlist/Tickets-datatables-ajax.js' }}" type="text/javascript"></script>
	<script src="{{ $CDN_PATH.'resources/pages/scripts/custom.js' }}" type="text/javascript"></script>
	<script src="{{ $CDN_PATH.'resources/global/plugins/highslide/highslide-with-html.js' }}" type="text/javascript"></script>
	<script src="{{ $CDN_PATH.'resources/global/plugins/fancybox/source/helpers/jquery.fancybox-media.js' }}" type="text/javascript"></script>
	<script src="{{ $CDN_PATH.'resources/global/plugins/fancybox/source/helpers/jquery.fancybox-thumbs.js' }}" type="text/javascript"></script>
				<script type="text/javascript">
				$('.fancybox-buttons').fancybox({
					autoWidth: true,
					autoHeight: true,
					autoResize: true,
					autoCenter: true,
					closeBtn: true,
					openEffect: 'elastic',
					closeEffect: 'elastic',
					helpers: {
						title: {
							type: 'inside',
							position: 'top'
						}
					},
					beforeShow: function() {
						this.title = $(this.element).data("title");
					}
				});

				$(".fancybox-thumb").fancybox({
					prevEffect	: 'none',
					nextEffect	: 'none',
					helpers	: 
					{
						title	: {
							type: 'outside'
						},
						thumbs	: {
							width: 60,
							height: 50
						}
					}
				});

	</script>
@endsection