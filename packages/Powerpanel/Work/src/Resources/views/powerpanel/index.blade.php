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
<!-- BEGIN PAGE BASE CONTENT -->
{!! csrf_field() !!}
<div class="row">
  <div class="col-md-12">
    <!-- TITILE HEAD START -->
    <div class="title-dropdown_sec">
      @if (File::exists(base_path() . '/resources/views/powerpanel/partials/listbreadcrumbs.blade.php') != null)
      @include('powerpanel.partials.listbreadcrumbs',['ModuleName'=>'Manage Work'])
      @endif
    </div>
    <!-- TITILE HEAD End... -->  
    <!-- Begin: life time stats -->
    @if(Session::has('message'))
    <div class="alert alert-success">
      <button class="close" data-close="alert"></button>
      {{ Session::get('message') }}
    </div>
    @endif
    <div class="portlet light portlet-fit portlet-datatable bordered">
      @if($iTotalRecords > 0)
      <div class="portlet-title select_box work_select_box">
        <span class="title">{{ trans('work::template.common.filterby') }}:</span>
        <select id="statusfilter" data-sort data-order class="form-control bs-select select2">
          <option value=" ">--{{ trans('work::template.common.selectstatus') }}--</option>
          <option value="Y">{{ trans('work::template.common.publish') }}</option>
          <option value="N">{{ trans('work::template.common.unpublish') }}</option>
        </select>

        
        <span class="btn btn-icon-only btn-green-drake green-new" type="button" id="refresh" title="Reset">
          <i class="fa fa-refresh" aria-hidden="true"></i>
        </span>
        @can('work-create')
        <div class="pull-right">
          <a class="btn btn-green-drake"
            href="{{ url('powerpanel/work/add') }}" title="{{ trans('work::template.workModule.addWork') }}">{{ trans('work::template.workModule.addWork') }}</a>
        </div>
        @endcan
        <div class="col-md-2 pull-right">
          <input type="search" placeholder="Search By Title" class="form-control" id="searchfilter">
        </div>
        <div class="clearfix"></div>
        <div class="portlet-body">
          <div class="table-container">
            <table class="new_table_desing table table-striped table-bordered table-hover table-checkable"
              id="datatable_ajax">
              <thead>
                <tr role="row" class="heading">
                  <th width="3%" align="center"><input type="checkbox" class="group-checkable"></th>
                  <th width="22%" align="left">{{ trans('work::template.common.title') }}</th>
                  <th width="16%" align="center">{{ trans('work::template.common.shortdescription') }} </th>
                  <th width="5%" align="center">{{ trans('work::template.common.image') }}</th>
                  <th width="10%" align="center">{{ trans('work::template.common.order') }}</th>
                  <th width="5%" align="center">{{ trans('work::template.common.publish') }}</th>
                  <th width="5%" align="right">{{ trans('work::template.common.actions') }}</th>
                </tr>
              </thead>
              <tbody> </tbody>
            </table>
            @can('work-delete')
            <a href="javascript:;" class="btn-sm btn btn-outline red right_bottom_btn deleteMass">
              {{ trans('work::template.common.delete') }}
            </a>
            @endcan
          </div>
        </div>
      </div>
      <!-- End: life time stats -->
      @else
      @include('powerpanel.partials.addrecordsection',['type'=>Config::get('Constant.MODULE.TITLE'), 'adUrl' =>
      url('powerpanel/work/add')])
      @endif
    </div>
  </div>
  <!-- /.modal -->
  @include('powerpanel.partials.deletePopup')
  @include('powerpanel.partials.onepushmodal')
  @include('powerpanel.partials.moveto')
  <!-- END PAGE BASE CONTENT -->
  @php  
  $tableState = true;
    $seg = url()->previous(); 
    $segArr = explode('/', $seg);
    if(!in_array('work', $segArr)){
     $tableState = false;
    }
  @endphp
  @endsection
  @section('scripts')
  <script type="text/javascript">
  window.site_url = '{!! url("/") !!}';
  var DELETE_URL = '{!! url("/powerpanel/work/DeleteRecord") !!}';
  var onePushShare = '{!! url("/powerpanel/share") !!}';
  var onePushGetRec = '{!! url("/powerpanel/share/getrec") !!}';
  
  var tableState = '{{ $tableState }}';
</script>
<script src="{{ url('resources/global/plugins/jquery-cookie-master/src/jquery.cookie.js') }}" type="text/javascript">
</script>
<script src="{{ url('resources/global/plugins/select2/js/select2.full.min.js') }}" type="text/javascript"></script>
<script src="{{ url('resources/global/plugins/select2/js/components-select2.min.js') }}" type="text/javascript">
</script>
<script src="{{ url('resources/global/scripts/datatable.js') }}" type="text/javascript"></script>
<script src="{{ url('resources/global/plugins/datatables/datatables.min.js') }}" type="text/javascript"></script>
<script src="{{ url('resources/global/plugins/datatables/dataTables.editor.js') }}" type="text/javascript"></script>
<script src="{{ $CDN_PATH.'resources/global/plugins/fancybox/source/helpers/jquery.fancybox-thumbs.js' }}" type="text/javascript"></script>
<script src="{{ url('resources/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.js') }}"
  type="text/javascript"></script>
<script src="{{ url('resources/pages/scripts/packages/work/work-datatables-ajax.js') }}" type="text/javascript"></script>
<script src="{{ url('resources/pages/scripts/custom.js') }}" type="text/javascript"></script>
<script src="{{ url('resources/pages/scripts/sharer-validations.js') }}" type="text/javascript"></script>
<script src="{{ url('resources/global/plugins/highslide/highslide-with-html.js') }}" type="text/javascript"></script>
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
        beforeShow: function () {
            this.title = $(this.element).data("title");
        }
    });
    $(".fancybox-thumb").fancybox({
        
        prevEffect: 'none',
        nextEffect: 'none',
        helpers:
                {
                    title: {
                        type: 'outside'
                    },
                    thumbs: {
                        width: 60,
                        height: 50
                    }
                }
    });
    $(document).ready(function () {
        setInterval(function () {
            $('.addhiglight').closest("td").closest("tr").addClass('higlight');
        }, 800);
    });
</script>




@endsection