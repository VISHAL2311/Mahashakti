@extends('powerpanel.layouts.app')
@section('title')
{{Config::get('Constant.SITE_NAME')}} - PowerPanel
@endsection
@section('css')
<link href="{{ url('resources/global/plugins/bootstrap-datepicker/css/bootstrap-datepicker.css') }}" rel="stylesheet"
  type="text/css" />
<link href="{{ url('resources/global/plugins/datatables/datatables.min.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ url('resources/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.css') }}"
  rel="stylesheet" type="text/css" />
<link href="{{ url('resources/global/plugins/highslide/highslide.css') }}" rel="stylesheet" type="text/css" />
@endsection
@section('content')
<div class="row">
  <div class="col-md-12">
    <!-- TITILE HEAD START -->
    <div class="title-dropdown_sec">
            @if (File::exists(base_path() . '/resources/views/powerpanel/partials/listbreadcrumbs.blade.php') != null)
            @include('powerpanel.partials.listbreadcrumbs',['ModuleName'=>'Manage Testimonials'])
            @endif

            @if(Session::has('message'))
            <div class="alert alert-success">
              <button class="close" data-close="alert"></button>
              {{ Session::get('message') }}
            </div>
            @endif
            
            <div id="cmspage_id" class="collapse in">
                <div class="collapse-inner">
                    <div class="portlet-title select_box filter-group">
                        <div class="row">
                            <div class="col-lg-6 col-md-12 col-xs-12">
                                <div class="portlet-lf-title">
                                  <div class="portlet-title select_box">
                                    <span class="title">{{ trans('testimonial::template.common.filterby') }}:</span>
                                    <select id="statusfilter" data-sort data-order class="bs-select select2">
                                      <option value=" ">{{ trans('testimonial::template.common.selectstatus') }}</option>
                                      <option value="Y">{{ trans('testimonial::template.common.publish') }}</option>
                                      <option value="N">{{ trans('testimonial::template.common.unpublish') }}</option>
                                    </select>
                                    <span class="btn btn-icon-only btn-green-drake green-new" type="button" id="refresh" title="Reset">
                                      <i class="fa fa-refresh" aria-hidden="true"></i>
                                    </span>
                                  </div>
                                </div>    
                            </div>
                            <div class="col-lg-6 col-md-12 col-xs-12">
                                <div class="portlet-rh-title">
                                    @can('testimonial-create')
                                    <div class="add_category_button pull-right">
                                        <a class="add_category" href="{{ url('powerpanel/testimonial/add') }}" title="{{ trans('testimonial::template.testimonialModule.addTestimonial') }}"><span>{{ trans('testimonial::template.testimonialModule.addTestimonial') }}</span> <i class="la la-plus"></i></a>
                                    </div>
                                    @endcan
                                </div>
                            </div>
                            <div class="col-lg-6 col-md-12 col-xs-12">
                                <div class="portlet-rh-title">
                                  <div class="event_datepicker pull-right" style="display:none;">
                                    <div class="new_date_picker input-group input-large date-picker"
                                      data-date-format="{{Config::get('Constant.DEFAULT_DATE_FORMAT')}}">
                                      <span class="input-group-addon"><i class="icon-calendar"></i></span>
                                      <input type="text" class="form-control datepicker" id="testimonialdate" name="testimonialdate"
                                        placeholder="Select Date" readonly>
                                        <a class="btn btn-green-drake pull-right btn-rh-refresh" id="refresh" title="Click to reset filters" href="javascript:;"><i class="fa fa-refresh"></i></a>
                                        <button class="btn btn-green-drake pull-right" title="{{ trans('testimonial::template.common.search') }}" id="dateFilter" style="margin:0 15px 0 0">
                                          <i class="fa fa-search"></i>
                                        </button>
                                    </div>
                                  </div>
                                </div>  
                            </div>    
                        </div>  
                    </div>
                </div>
            </div> 
        </div>
        <!-- TITILE HEAD End... --> 
    <div class="portlet light portlet-fit portlet-datatable">
      @if($total > 0)
      <div class="portlet-body">
        <div class="table-container">
          <div class="col-md-12 col-sm-12 ">
              <div class="row">
                  <div class="row col-md-6 col-sm-12 pull-right search_pages_div">
                      <div class="search_rh_div">
                          <span>{{ trans('testimonial::template.common.search') }}:</span>
                          <input type="search" class="form-control form-control-solid placeholder-no-fix" placeholder="Search by Testimonial by" id="searchfilter">
                      </div>
                  </div>
              </div>
          </div>
          <table class="new_table_desing table table-striped table-bordered table-hover table-checkable"
            id="testimonial_datatable_ajax">
            <thead>
              <tr role="row" class="heading">
                <th width="3%" align="center"><input type="checkbox" class="group-checkable"></th>
                <th width="30%" align="left">{{ trans('testimonial::template.testimonialModule.testimonialBy') }}</th>
                <th width="5%" align="left">{{ trans('testimonial::template.common.testimonial') }}</th>
                <th width="10%" align="left">{{ trans('testimonial::template.common.rating') }}</th>
                <!-- <th width="10%" align="center">Photo</th> -->
                <th width="25%" align="left">{{ trans('testimonial::template.testimonialModule.testimonialDate') }}</th>
                <th width="15%" align="center">{{ trans('testimonial::template.common.publish') }}</th>
                <th width="20%" align="right">{{ trans('testimonial::template.common.actions') }}</th>
              </tr>
            </thead>
            <tbody></tbody>
          </table>
          @can('testimonial-delete')
          <a href="javascript:;"
            class="btn-sm btn btn-outline red right_bottom_btn deleteMass">{{ trans('testimonial::template.common.delete') }}</a>
          @endcan
        </div>
      </div>
      @else
      @include('powerpanel.partials.addrecordsection',['type'=>Config::get('Constant.MODULE.TITLE'), 'adUrl' =>
      url('powerpanel/testimonial/add')])
      @endif
    </div>
  </div>
</div>
  @php  
  $tableState = true;
    $seg = url()->previous(); 
    $segArr = explode('/', $seg);
    if(!in_array('testimonial', $segArr)){
     $tableState = false;
    }
  @endphp
@include('powerpanel.partials.deletePopup')

@endsection
@section('scripts')
<script src="{{ url('resources/global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.js') }}"
  type="text/javascript"></script>
<script type="text/javascript">
window.site_url = '{!! url("/") !!}';
var DELETE_URL = '{!! url("/powerpanel/testimonial/DeleteRecord") !!}';
$(document).ready(function() {
  $('.datepicker').datepicker({
    autoclose: true,
    endDate: new Date(),
    format: DEFAULT_DT_FMT_FOR_DATEPICKER
  });
});
var tableState = '{{ $tableState }}';
</script>
<script src="{{ url('resources/global/plugins/jquery-cookie-master/src/jquery.cookie.js') }}" type="text/javascript">
</script>
<script src="{{ url('resources/global/scripts/datatable.js') }}" type="text/javascript"></script>
<script src="{{ url('resources/global/plugins/datatables/datatables.min.js') }}" type="text/javascript"></script>
<script src="{{ url('resources/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.js') }}"
  type="text/javascript"></script>
<script src="{{ url('resources/pages/scripts/packages/testimonial/table-testimonial-ajax.js') }}" type="text/javascript"></script>
<script src="{{ url('resources/pages/scripts/custom.js') }}" type="text/javascript"></script>
<script src="{{ url('resources/global/plugins/highslide/highslide-with-html.js') }}" type="text/javascript"></script>
<script src="{{ $CDN_PATH.'resources/global/plugins/fancybox/source/helpers/jquery.fancybox-thumbs.js' }}" type="text/javascript"></script>
<script type="text/javascript">
$(document).ready(function() {
  $('#statusfilter').select2({
    placeholder: "Select status"
  });
});
</script>
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