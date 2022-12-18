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
<!--@include('powerpanel.partials.breadcrumbs')-->
{!! csrf_field() !!}
<div class="row">
    <div class="col-md-12">

        <div class="title-dropdown_sec">
            <div class="title_bar">
                <div class="page-head">
                    <div class="page-title">
                        <h1>Search Statistics</h1>                        
                    </div>   
                    <ul class="page-breadcrumb breadcrumb">
                        <li>
                            <span aria-hidden="true" class="icon-home"></span>
                            <a href="{{ url('powerpanel') }}">Home</a>
                            <i class="fa fa-circle"></i>
                        </li>
                        <li class="active">Search Statistics</li>
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
                    <div class="portlet-title select_box service_select_box">
                        <div class="row">
                            <div class="col-lg-6 col-md-12 col-xs-12">
                                <div class="portlet-lf-title">
                                    <div class="sub_select_filter" id="hidefilter">
                                        <span class="title">{{ trans('searchstatictics::template.common.filterby') }}:</span>
                                        <span class="select_input">
                                            <select data-sort data-order class="form-control bs-select select2 category_filter" name="yearFilter" id="yearFilter">
                                                <option value="">-- Select Year --</option>
                                                @php 
                                                $currentYear = date('Y');
                                                $pastTenYears = $currentYear - 10;
                                                $futureTenYears = $currentYear + 10; 
                                                $yearOptions = range($pastTenYears,$futureTenYears);
                                                arsort($yearOptions);
                                                @endphp
                                                @foreach($yearOptions as $year)
                                                <option value="{{ $year }}">{{ $year }}</option>
                                                @endforeach
                                            </select>
                                        </span>
                                        <span class="select_input">
                                            <select data-sort data-order class="form-control bs-select select2 category_filter" name="monthFilter" id="monthFilter">
                                                <option value="">-- Select Month --</option>
                                                @php 
                                                $selected ="";
                                                @endphp
                                                <option value="" {{ $selected }} ></option>
                                                <option value="1">January</option> 
                                                <option value="2">February</option>
                                                <option value="3">March</option> 
                                                <option value="4">April</option> 
                                                <option value="5">May</option> 
                                                <option value="6">June</option> 
                                                <option value="7">July</option> 
                                                <option value="8">August</option> 
                                                <option value="9">September</option> 
                                                <option value="10">October</option> 
                                                <option value="11">November</option> 
                                                <option value="12">December</option>
                                            </select>
                                        </span>
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
        <div class="portlet light portlet-fit portlet-datatable bordered">
            @if($iTotalRecords > 0)
            <div class="portlet-body">
                <div class="table-container">
                    <div class="table-actions-wrapper">
                        <div class="search_rh_div pull-right">
                            <span>{{ trans('searchstatictics::template.common.search') }}:</span>
                            <input type="search" class="form-control form-control-solid placeholder-no-fix" placeholder="Search by Keyword" id="searchfilter">
                        </div>
                    </div>
                    <table class="new_table_desing table table-striped table-bordered table-hover table-checkable dataTable" id="datatable_ajax">
                        <thead>
                            <tr role="row" class="heading">
                                <th width="2%" align="center"><input type="checkbox" class="group-checkable"></th>
                                <th width="20%" align="left">Search Keyword</th>							
                                <th width="20%" align="center">No of Search</th>
                                <th width="20%" align="center">Month</th>
                                <th width="10%" align="center">Year</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>				
                    @can('contact-us-delete')
                    <a href="javascript:;" class="btn-sm btn btn-outline red right_bottom_btn deleteMass">{{ trans('searchstatictics::template.common.delete') }}</a>
                    @endcan

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
                    {{ trans('searchstatictics::template.common.alert') }} 
                </div>
                <div class="modal-body text-center">{{ trans('searchstatictics::template.contactleadModule.noExport') }} </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-green-drake" data-dismiss="modal">{{ trans('searchstatictics::template.common.ok') }}</button>
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
                    {{ trans('searchstatictics::template.common.alert') }}
                </div>
                <div class="modal-body text-center">{{ trans('searchstatictics::template.contactleadModule.recordsExport') }}</div>
                <div class="modal-footer">
                    <div align="center">
                        <div class="md-radio-inline">
                            <div class="md-radio">
                                <input type="radio" value="selected_records" id="selected_records" name="export_type" class="md-radiobtn" checked="checked">
                                <label for="selected_records">
                                    <span class="inc"></span>
                                    <span class="check"></span>
                                    <span class="box"></span> {{ trans('searchstatictics::template.contactleadModule.selectedRecords') }}
                                </label>
                            </div>

                        </div>
                    </div>
                    <button type="button" class="btn btn-green-drake" id="ExportRecord" data-dismiss="modal">{{ trans('searchstatictics::template.common.ok') }} </button>
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
                    {{ trans('searchstatictics::template.common.alert') }} 
                </div>
                <div class="modal-body text-center">{{ trans('searchstatictics::template.contactleadModule.leastRecord') }} </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-green-drake" data-dismiss="modal">{{ trans('searchstatictics::template.common.ok') }} </button>
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
    var DELETE_URL = '{!! url("/powerpanel/search-statictics/DeleteRecord") !!}';
</script>
<script src="{{ $CDN_PATH.'resources/global/plugins/jquery-cookie-master/src/jquery.cookie.js' }}" type="text/javascript"></script>	
<script src="{{ $CDN_PATH.'resources/global/plugins/datatables/datatables.min.js' }}" type="text/javascript"></script>
<script src="{{ $CDN_PATH.'resources/global/scripts/datatable.js' }}" type="text/javascript"></script>
<script src="{{ $CDN_PATH.'resources/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.js' }}" type="text/javascript"></script>
<script src="{{ $CDN_PATH.'resources/global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js' }}" type="text/javascript"></script>
<script src="{{ $CDN_PATH.'resources/pages/scripts/packages/searchstatictics/searchstatictics-datatables-ajax.js' }}" type="text/javascript"></script>
<script src="{{ $CDN_PATH.'resources/pages/scripts/custom.js' }}" type="text/javascript"></script>
<script src="{{ $CDN_PATH.'resources/global/plugins/highslide/highslide-with-html.js' }}" type="text/javascript"></script>
@endsection