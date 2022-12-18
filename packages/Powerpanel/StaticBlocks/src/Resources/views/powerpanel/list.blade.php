@extends('powerpanel.layouts.app')
@section('title')
{{Config::get('Constant.SITE_NAME')}} - PowerPanel
@endsection
@section('css')
<link href="{{ url('resources/global/plugins/datatables/datatables.min.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ url('resources/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.css') }}"
  rel="stylesheet" type="text/css" />
<link href="{{ url('resources/global/plugins/highslide/highslide.css') }}" rel="stylesheet" type="text/css" />
@endsection
@section('content')
<!-- @include('powerpanel.partials.breadcrumbs') -->
<div class="row">
  <div class="col-md-12">
    <!-- TITILE HEAD START -->
    <div class="title-dropdown_sec">
            @if (File::exists(base_path() . '/resources/views/powerpanel/partials/listbreadcrumbs.blade.php') != null)
            @include('powerpanel.partials.listbreadcrumbs',['ModuleName'=>'Manage Static Blocks'])
            @endif
    </div>
    @if(Session::has('message'))
    <div class="alert alert-success">
      <button class="close" data-close="alert"></button>
      {{ Session::get('message') }}
    </div>
    @endif
    <div class="portlet light portlet-fit portlet-datatable bordered">
      @if($iTotalRecords > 0)
      <div class="portlet-title select_box">
        <div class="col-md-12 nopadding">
          <div class="col-md-6 nopadding">
            <span class="title">{{ trans('static-blocks::template.common.filterby') }}:</span>
            <select id="statusfilter" data-sort data-order class="bs-select select2">
              <option value=" ">{{ trans('static-blocks::template.common.selectstatus') }}</option>
              <option value="Y">{{ trans('static-blocks::template.common.publish') }}</option>
              <option value="N">{{ trans('static-blocks::template.common.unpublish') }}</option>
            </select>
            <span class="btn btn-icon-only btn-green-drake green-new" type="button" id="refresh" title="Reset">
              <i class="fa fa-refresh" aria-hidden="true"></i>
            </span>
          </div>
          @can('static-block-create')
          @if(!$client_role)
          <div class="pull-right">
            <a class="btn btn-green-drake"
              href="{{ url('powerpanel/static-block/add') }}" title="{{ trans('static-blocks::template.staticblockModule.add') }}">{{ trans('static-blocks::template.staticblockModule.add') }}</a>
          </div>
          @endif
          @endcan
					<div class="col-md-2 pull-right">
            <input type="search" placeholder="Search By Title" class="form-control" id="searchfilter">
          </div>
        </div>
      </div>
      <div class="portlet-body">
        <div class="table-container">
          <table class="new_table_desing table table-striped table-bordered table-hover table-checkable"
            id="static_blocks_datatable_ajax">
            <thead>
              <tr role="row" class="heading">
                @if($client_role)
                 <th width="5%" align="center"> </th>
                @else
                 <th width="5%" align="center"><input type="checkbox" class="group-checkable"></th>
                @endif
                <th width="20%" align="left">{{ trans('static-blocks::template.common.title') }}</th>
                <th width="16%" align="center">{{ trans('static-blocks::template.common.description') }}</th>
                <th width="25%" align="center">{{ trans('static-blocks::template.common.dateandtime') }}</th>
                <th width="15%" align="center">{{ trans('static-blocks::template.common.publish') }}</th>
                <th width="20%" align="center">{{ trans('static-blocks::template.common.actions') }}</th>
              </tr>
            </thead>
            <tbody></tbody>
          </table>
          @can('static-block-delete')
           @if(!$client_role)
            <a href="javascriptavascript:;" class="btn-sm btn red btn-outline right_bottom_btn deleteMass">
             {{ trans('static-blocks::template.common.delete') }}
            </a>
          @endif
          @endcan
        </div>
      </div>
      @else
      @include('powerpanel.partials.addrecordsection',['type'=>Config::get('Constant.MODULE.TITLE'), 'adUrl' =>
      url('powerpanel/static-block/add')])
      @endif

    </div>
  </div>
</div>
@php  
  $tableState = true;
   $seg = url()->previous(); 
   $segArr = explode('/', $seg);
   if(!in_array('static-block', $segArr)){
     $tableState = false;
    }
 @endphp
@include('powerpanel.partials.deletePopup')
@endsection
@section('scripts')
<script type="text/javascript">
window.site_url = '{!! url("/") !!}';
var DELETE_URL = '{!! url("/powerpanel/static-block/DeleteRecord") !!}';
var tableState = '{{ $tableState }}';
</script>
<script src="{{ url('resources/global/plugins/jquery-cookie-master/src/jquery.cookie.js') }}" type="text/javascript">
</script>
<script src="{{ url('resources/global/scripts/datatable.js') }}" type="text/javascript"></script>
<script src="{{ url('resources/global/plugins/datatables/datatables.min.js') }}" type="text/javascript"></script>
<script src="{{ url('resources/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.js') }}"
  type="text/javascript"></script>
<script src="{{ url('resources/pages/scripts/packages/static-blocks/table-staticblocks-ajax.js') }}" type="text/javascript"></script>
<script src="{{ url('resources/pages/scripts/custom.js') }}" type="text/javascript"></script>
<script src="{{ url('resources/global/plugins/highslide/highslide-with-html.js') }}" type="text/javascript"></script>
<script type="text/javascript">

</script>
@endsection