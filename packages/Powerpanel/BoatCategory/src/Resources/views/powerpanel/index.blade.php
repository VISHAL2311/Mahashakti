@extends('powerpanel.layouts.app')
@section('title')
{{Config::get('Constant.SITE_NAME')}} - PowerPanel
@endsection
@section('css')
<link href="{{ url('resources/global/plugins/datatables/datatables.min.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ url('resources/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ url('resources/global/plugins/select2/css/select2.min.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ url('resources/global/plugins/select2/css/select2-bootstrap.min.css') }}" rel="stylesheet" type="text/css" />
<!-- BEGIN PAGE LEVEL PLUGINS -->
<link href="{{ url('resources/global/plugins/highslide/highslide.css') }}" rel="stylesheet" type="text/css" />

@endsection
@section('content')
<!-- BEGIN PAGE BASE CONTENT -->
{!! csrf_field() !!}
<div class="row">
    <div class="col-md-12">
        <!-- TITILE HEAD START -->
        <div class="title-dropdown_sec">
            @if (File::exists(base_path() . '/resources/views/powerpanel/partials/listbreadcrumbs.blade.php') != null)
            @include('powerpanel.partials.listbreadcrumbs',['ModuleName'=>'Manage Boat Category'])
            @endif
            <div id="cmspage_id" class="collapse in">
                <div class="collapse-inner">
                    <div class="portlet-title select_box filter-group">
                        <div class="row">
                            <div class="col-lg-6 col-md-12 col-xs-12">
                                <div class="portlet-lf-title">
                                    <div class="sub_select_filter" id="hidefilter">
                                        <span class="title">{!! trans('boatcategory::template.common.filterby') !!}:</span>
                                        <span class="select_input">
                                            <select id="statusfilter" data-sort data-order class="bs-select select2">
                                                <option value=" ">{!! trans('boatcategory::template.common.selectstatus') !!}</option>
                                                <option value="Y">{!! trans('boatcategory::template.common.publish') !!}</option>
                                                <option value="N">{!! trans('boatcategory::template.common.unpublish') !!}</option>
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
                                    @can('boat-category-create')
                                    <div class="add_category_button pull-right">
                                        <a class="add_category" href="{{ url('powerpanel/boat-category/add') }}" title="{{ trans('boatcategory::template.boatCategoryModule.addBoatCategory') }}"><span>{{ trans('boatcategory::template.boatCategoryModule.addBoatCategory') }}</span> <i class="la la-plus"></i></a>
                                    </div>
                                    @endcan
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- TITILE HEAD End... -->
        @if(Session::has('message'))
        <div class="alert alert-success">
            <button class="close" data-close="alert"></button>
            {{ Session::get('message') }}
        </div>
        @endif
        <div class="portlet light portlet-fit portlet-datatable">
            @if($iTotalRecords > 0)
            <div class="portlet-title select_box new_select_box">
                <div class="portlet-body">
                    <div class="table-container">
                        <div class="col-md-12 col-sm-12 ">
                            <div class="row">
                                <div class="row col-md-6 col-sm-12 pull-right search_pages_div">
                                    <div class="search_rh_div">
                                        <span>{{ trans('boatcategory::template.common.search') }}:</span>
                                        <input type="search" class="form-control form-control-solid placeholder-no-fix" placeholder="Search by Title" id="searchfilter" title="Search by Title" autocomplete="off">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <table class="new_table_desing table table-striped table-bordered table-hover table-checkable" id="datatable_ajax">
                            <thead>
                                <tr role="row" class="heading">
                                    <th width="2%" align="center"><input type="checkbox" class="group-checkable"></th>
                                    <th width="36%" align="left">{{ trans('boatcategory::template.common.title') }}</th>

                                    <th width="20%" align="center">{{ trans('boatcategory::template.common.order') }}</th>
                                    <th width="15%" align="center">{{ trans('boatcategory::template.common.publish') }}</th>
                                    <th width="10%" align="right">{{ trans('boatcategory::template.common.actions') }}</th>
                                </tr>
                            </thead>
                            <tbody> </tbody>
                        </table>
                        @can('boat-category-delete')
                        <a href="javascript:;" class="btn-sm btn red btn-outline right_bottom_btn deleteMass">{{ trans('boatcategory::template.common.delete') }}
                        </a>
                        @endcan
                    </div>
                </div>
            </div>
            @else
            @include('powerpanel.partials.addrecordsection',['type'=>Config::get('Constant.MODULE.TITLE'), 'adUrl' => url('powerpanel/boat-category/add')])
            @endif
        </div>
    </div>
</div>
@php
$tableState = true;
$seg = url()->previous();
$segArr = explode('/', $seg);
if(!in_array('boat-category', $segArr)){
$tableState = false;
}
@endphp
@include('powerpanel.partials.deletePopup')
@include('powerpanel.partials.moveto')
@endsection
@section('scripts')
<script type="text/javascript">
    window.site_url = '{!! url("/") !!}';
    var DELETE_URL = '{!! url("/powerpanel/boat-category/DeleteRecord") !!}';
    var tableState = '{{ $tableState }}';
</script>
<script src="{{ url('resources/global/plugins/jquery-cookie-master/src/jquery.cookie.js') }}" type="text/javascript"></script>
<script src="{{ url('resources/global/plugins/select2/js/select2.full.min.js') }}" type="text/javascript"></script>
<script src="{{ url('resources/global/plugins/select2/js/components-select2.min.js') }}" type="text/javascript"></script>
<script src="{{ url('resources/global/scripts/datatable.js') }}" type="text/javascript"></script>
<script src="{{ url('resources/global/plugins/datatables/datatables.min.js') }}" type="text/javascript"></script>
<script src="{{ url('resources/global/plugins/datatables/dataTables.editor.js') }}" type="text/javascript"></script>
<script src="{{ url('resources/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.js') }}" type="text/javascript"></script>
<script src="{{ url('resources/pages/scripts/packages/boatcategory/boat-category-datatables-ajax.js') }}" type="text/javascript"></script>
<script src="{{ url('resources/pages/scripts/custom.js') }}" type="text/javascript"></script>
<script src="{{ url('resources/global/plugins/highslide/highslide-with-html.js') }}" type="text/javascript"></script>

@endsection