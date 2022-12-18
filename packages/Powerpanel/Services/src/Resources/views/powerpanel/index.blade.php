@extends('powerpanel.layouts.app')
@section('title')
{{Config::get('Constant.SITE_NAME')}} - PowerPanel
@endsection

@section('css')
<link href="{{ url('resources/global/plugins/datatables/datatables.min.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ url('resources/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.css') }}" rel="stylesheet" type="text/css" />
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
      @include('powerpanel.partials.listbreadcrumbs',['ModuleName'=>'Manage Services'])
      @endif
      <div id="cmspage_id" class="collapse in">
        <div class="collapse-inner">
          <div class="portlet-title select_box filter-group">
            <div class="row">
              <div class="col-lg-6 col-md-12 col-xs-12">
                <div class="portlet-lf-title">
                  <div class="sub_select_filter" id="hidefilter">
                    <span class="title">{!! trans('services::template.common.filterby') !!}:</span>
                    <span class="select_input">
                      <select id="statusfilter" data-sort data-order class="bs-select select2">
                        <option value=" ">{!! trans('services::template.common.selectstatus') !!}</option>
                        <option value="Y">{!! trans('services::template.common.publish') !!}</option>
                        <option value="N">{!! trans('services::template.common.unpublish') !!}</option>
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
                  @can('services-create')
                  @if( isset(App\Helpers\MyLibrary::getFront_Uri('services')['uri']) )
                  <div class="add_category_button pull-right">
                    <a class="add_category" href="{{ url('powerpanel/services/add') }}" title="{{ trans('services::template.serviceModule.addService') }}"><span>{{ trans('services::template.serviceModule.addService') }}</span> <i class="la la-plus"></i></a>
                  </div>
                  @endif
                  @endcan

                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
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
      <div class="portlet-title select_box service_select_box">
        <div class="portlet-body">
          <div class="table-container">
            <div class="col-md-12 col-sm-12 ">
              <div class="row">
                <div class="row col-md-6 col-sm-12 pull-right search_pages_div">
                  <div class="search_rh_div">
                    <span>{{ trans('services::template.common.search') }}:</span>
                    <input type="search" class="form-control form-control-solid placeholder-no-fix" placeholder="Search by Title" id="searchfilter" title="Search by Title" autocomplete="off">
                  </div>
                </div>
              </div>
            </div>
            <table class="new_table_desing table table-striped table-bordered table-hover table-checkable" id="datatable_ajax">
              <thead>
                <tr role="row" class="heading">
                  <th width="3%" align="center"><input type="checkbox" class="group-checkable"></th>
                  <th width="22%" align="left">{{ trans('services::template.common.title') }}</th>
                  <th width="11%" align="center">{{ trans('services::template.common.shortdescription') }} </th>
                  <th width="5%" align="center">{{ trans('services::template.common.image') }}</th>
                  <th width="5%" align="center">HITS</th>
                  <th width="10%" align="center">{{ trans('services::template.common.order') }}</th>
                  <th width="5%" align="center">{{ trans('services::template.common.publish') }}</th>
                  <th width="5%" align="right">{{ trans('services::template.common.actions') }}</th>
                </tr>
              </thead>
              <tbody> </tbody>
            </table>
            @can('services-delete')
            <a href="javascript:;" class="btn-sm btn btn-outline red right_bottom_btn deleteMass">
              {{ trans('services::template.common.delete') }}
            </a>
            @endcan
          </div>
        </div>
      </div>
      <!-- End: life time stats -->
      @else
      @include('powerpanel.partials.addrecordsection',['type'=>Config::get('Constant.MODULE.TITLE'), 'adUrl' =>
      url('powerpanel/services/add')])
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
  if(!in_array('services', $segArr)){
  $tableState = false;
  }
  @endphp
  @endsection
  @section('scripts')
  <script type="text/javascript">
    window.site_url = '{!! url("/") !!}';
    var DELETE_URL = '{!! url("/powerpanel/services/DeleteRecord") !!}';
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
  <script src="{{ url('resources/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.js') }}" type="text/javascript"></script>
  <script src="{{ url('resources/pages/scripts/packages/services/services-datatables-ajax.js') }}" type="text/javascript"></script>
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
      beforeShow: function() {
        this.title = $(this.element).data("title");
      }
    });
    $(".fancybox-thumb").fancybox({

      prevEffect: 'none',
      nextEffect: 'none',
      helpers: {
        title: {
          type: 'outside'
        },
        thumbs: {
          width: 60,
          height: 50
        }
      }
    });
    $(document).ready(function() {
      setInterval(function() {
        $('.addhiglight').closest("td").closest("tr").addClass('higlight');
      }, 800);
    });
  </script>




  @endsection