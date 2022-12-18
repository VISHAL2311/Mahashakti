@extends('powerpanel.layouts.app')
@section('title')
{{Config::get('Constant.SITE_NAME')}} - PowerPanel
@endsection
@section('css')
<link href="{{ url('resources/global/plugins/datatables/datatables.min.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ url('resources/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ url('resources/global/plugins/select2/css/select2.min.css') }}" rel="stylesheet" type="text/css"/>
<link href="{{ url('resources/global/plugins/select2/css/select2-bootstrap.min.css') }}" rel="stylesheet" type="text/css"/>
<!-- BEGIN PAGE LEVEL PLUGINS -->
<link href="{{ url('resources/global/plugins/highslide/highslide.css') }}" rel="stylesheet" type="text/css" />

@endsection
@section('content')
@include('powerpanel.partials.breadcrumbs')
<!-- BEGIN PAGE BASE CONTENT -->
{!! csrf_field() !!}
<div class="row">
    <div class="col-md-12">
        @if(Session::has('message'))
        <div class="alert alert-success">
            <button class="close" data-close="alert"></button>
            {{ Session::get('message') }}
        </div>
        @endif
        <div class="portlet light portlet-fit portlet-datatable">
            @if($iTotalRecords > 0)
            <div class="portlet-title select_box new_select_box">
                <span class="title">{{ trans('servicescategory::template.common.filterby') }}:</span>
                <select id="statusfilter" data-sort data-order class="form-control bs-select select2">
                    <option value=" ">{{ trans('servicescategory::template.common.selectstatus') }}</option>
                    <option value="Y">{{ trans('servicescategory::template.common.publish') }}</option>
                    <option value="N">{{ trans('servicescategory::template.common.unpublish') }}</option>
                </select>
                <span class="btn btn-icon-only green-new" type="button" id="refresh" title="Reset">
                    <i class="fa fa-refresh" aria-hidden="true"></i>
                </span>
                @can('service-category-create')
                <div class="pull-right">
                    <a class="btn btn-green-drake" href="{{ url('powerpanel/service-category/add') }}">{{ trans('servicescategory::template.serviceCategoryModule.addServiceCategory') }}</a>
                </div>
                @endcan

                <div class="col-md-2 pull-right">
                    <input type="search" placeholder="{{ trans('servicescategory::template.common.searchbytitle') }}" class="form-control" id="searchfilter">
                </div>

                <div class="clearfix"></div>
                <div class="portlet-body">
                    <div class="table-container">
                        <table class="new_table_desing table table-striped table-bordered table-hover table-checkable" id="datatable_ajax">
                            <thead>
                                <tr role="row" class="heading">									
                                    <th width="2%" align="center"><input type="checkbox" class="group-checkable"></th>									
                                    <th width="36%" align="left">{{ trans('servicescategory::template.common.title') }}</th>
                                    <th width="5%" align="left">{{ trans('servicescategory::template.common.shortdescription') }}</th>
                                    <th width="5%" align="left">{{ trans('servicescategory::template.common.parentcategory') }} </th>									
                                    <th width="5%" align="center">{{ trans('servicescategory::template.common.view') }}</th>
                                    <th width="20%" align="center">{{ trans('servicescategory::template.common.order') }}</th>
                                    <th width="15%" align="center">{{ trans('servicescategory::template.common.publish') }}</th>
                                    <th width="10%" align="right">{{ trans('servicescategory::template.common.actions') }}</th>
                                </tr>
                            </thead>
                            <tbody> </tbody>
                        </table>
                        @can('service-category-delete')						  
                        <a href="javascript:;" class="btn-sm btn red btn-outline right_bottom_btn deleteMass">{{ trans('servicescategory::template.common.delete') }} 
                        </a>							
                        @endcan
                    </div>
                </div>
            </div>
            @else
            @include('powerpanel.partials.addrecordsection',['type'=>Config::get('Constant.MODULE.TITLE'), 'adUrl' => url('powerpanel/service-category/add')])
            @endif		
        </div>
    </div>
</div>
@php  
$tableState = true;
$seg = url()->previous(); 
$segArr = explode('/', $seg);
if(!in_array('service-category', $segArr)){
$tableState = false;
}
@endphp
@include('powerpanel.partials.deletePopup')
@include('powerpanel.partials.moveto')
@endsection
@section('scripts')
<script type="text/javascript">
    window.site_url = '{!! url("/") !!}';
    var DELETE_URL = '{!! url("/powerpanel/service-category/DeleteRecord") !!}';
    var tableState = '{{ $tableState }}';
</script>
<script src="{{ url('resources/global/plugins/jquery-cookie-master/src/jquery.cookie.js') }}" type="text/javascript"></script>
<script src="{{ url('resources/global/plugins/select2/js/select2.full.min.js') }}" type="text/javascript"></script>
<script src="{{ url('resources/global/plugins/select2/js/components-select2.min.js') }}" type="text/javascript"></script>
<script src="{{ url('resources/global/scripts/datatable.js') }}" type="text/javascript"></script>
<script src="{{ url('resources/global/plugins/datatables/datatables.min.js') }}" type="text/javascript"></script>	
<script src="{{ url('resources/global/plugins/datatables/dataTables.editor.js') }}" type="text/javascript"></script>
<script src="{{ url('resources/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.js') }}" type="text/javascript"></script>
<script src="{{ url('resources/pages/scripts/packages/servicescategory/services-category-datatables-ajax.js') }}" type="text/javascript"></script>
<script src="{{ url('resources/pages/scripts/custom.js') }}" type="text/javascript"></script>
<script src="{{ url('resources/global/plugins/highslide/highslide-with-html.js') }}" type="text/javascript"></script>   

@endsection