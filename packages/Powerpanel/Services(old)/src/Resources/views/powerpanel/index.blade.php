@extends('powerpanel.layouts.app')
@section('title')
{{Config::get('Constant.SITE_NAME')}} - PowerPanel
@stop
@section('css')
<link href="{{ url('resources/global/plugins/datatables/datatables.min.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ url('resources/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.css') }}"rel="stylesheet" type="text/css" />
<link href="{{ $CDN_PATH.'resources/global/plugins/fancybox/source/helpers/jquery.fancybox-thumbs.css' }}" rel="stylesheet" type="text/css"/>
<link href="{{ $CDN_PATH.'resources/global/plugins/tooltips/tooltip.css' }}" rel="stylesheet" type="text/css"/>
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
            @include('powerpanel.partials.listbreadcrumbs',['ModuleName'=>'Services'])
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
                                            <select class="form-control bs-select select2 category_filter" name="category" id="category">
                                                <option value=" ">Select Category</option>
                                                @foreach ($ServicesCategory as $cat)
                                                @php $permissionName = 'faq-list' @endphp
                                                @php $selected = ''; @endphp
                                                @if(isset($service->intFKCategory))
                                                @if($cat['id'] == $service->intFKCategory)
                                                @php $selected = 'selected'; @endphp
                                                @endif
                                                @endif
                                                <option value="{{ $cat['id'] }}" {{ $selected }} >{{ $cat['varModuleName']== "managementteam"?'Select Category':$cat['varTitle'] }}</option>
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
                                <div class="portlet-rh-title">
                                    @can('services-create')
                                     @if( isset(App\Helpers\MyLibrary::getFront_Uri('service-category')['uri']) )
                                    <div class="add_category_button pull-right">
                                        <a class="add_category" href="{{ url('powerpanel/services/add') }}"><span>{{ trans('services::template.serviceModule.addService') }}</span> <i class="la la-plus"></i></a>
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
    <div class="portlet light portlet-fit portlet-datatable">

            @if( !isset(App\Helpers\MyLibrary::getFront_Uri('service-category')['uri']) )
            @include('powerpanel.partials.pagenotavailable')
            @elseif($iTotalRecords > 0)

            <div class="portlet-body">
                <div class="table-container">
                    <div class="col-md-12 col-sm-12 ">
                        <div class="row">
                            <div class="row col-md-6 col-sm-12 pull-right search_pages_div">
                                <div class="search_rh_div">
                                    <span>{{ trans('services::template.common.search') }}:</span>
                                    <input type="search" class="form-control form-control-solid placeholder-no-fix" placeholder="Search by Title" id="searchfilter">
                                </div>
                            </div>
                        </div>
                    </div>
                    @if (File::exists(base_path() . '/resources/views/powerpanel/partials/tabpanel.blade.php') != null)
                        @include('powerpanel.partials.tabpanel',['tabarray'=>[]])
                    @endif

                    @php
                        $tablearray = [
                            'DataTableTab'=>[
                                'ColumnSetting'=>[
                                    ['Identity_Name'=>'title','TabIndex'=>'3','Name'=>'title'],
                                    ['Identity_Name'=>'short_description','TabIndex'=>'4','Name'=>'Short Description'],
                                    ['Identity_Name'=>'image','TabIndex'=>'5','Name'=>'Image'],
                                    ['Identity_Name'=>'category','TabIndex'=>'6','Name'=>'Category'],
                                    ['Identity_Name'=>'icon','TabIndex'=>'7','Name'=>'Icon'],
                                    ['Identity_Name'=>'order','TabIndex'=>'8','Name'=>'Order'],
                                    ['Identity_Name'=>'publish','TabIndex'=>'9','Name'=>'Publish'],
                                    ['Identity_Name'=>'actions','TabIndex'=>'10','Name'=>'Action']
                                ],
                                'DataTableHead'=>[
                                    ['Title'=>'title','Align'=>'left'],
                                    ['Title'=>'Short Description','Align'=>'center'],
                                    ['Title'=>'Image','Align'=>'center'],
                                    ['Title'=>'Category','Align'=>'center'],
                                    ['Title'=>'Icon','Align'=>'center'],
                                    ['Title'=>'Order','Align'=>'center'],
                                    ['Title'=>'Publish','Align'=>'center'],
                                    ['Title'=>'Action','Align'=>'right']
                                ]
                            ]
                        ];
                        @endphp
                        @include('powerpanel.partials.datatable-view',['ModuleName'=>'Services','Permission_Delete'=>'services-delete','tablearray'=> $tablearray,'userIsAdmin'=> $userIsAdmin,'Module_ID'=>Config::get('Constant.MODULE.ID')])
                        </div>
                    </div>
                    <!-- <div class="portlet-body">
                        <div class="table-container">
                            <table class="new_table_desing table table-striped table-bordered table-hover table-checkable"
                            id="datatable_ajax">
                            <thead>
                                <tr role="row" class="heading">
                                <th width="3%" align="center"><input type="checkbox" class="group-checkable"></th>
                                <th width="22%" align="left">{{ trans('services::template.common.title') }}</th>
                                <th width="16%" align="center">{{ trans('services::template.common.shortdescription') }} </th>
                                <th width="5%" align="center">{{ trans('services::template.common.image') }}</th>
                                <th width="10%" align="center">{{ trans('services::template.common.category') }}</th>
                                <th width="8%" align="center">{{ trans('services::template.common.icon') }}</th>
                                <th width="10%" align="center">{{ trans('services::template.common.order') }}</th>
                                <th width="12%" align="center">{{ trans('services::template.common.featured') }}</th>
                                <th width="5%" align="center">{{ trans('services::template.common.publish') }}</th>
                                <th width="15%" align="right">{{ trans('services::template.common.actions') }}</th>
                                </tr>
                            </thead>
                            <tbody> </tbody>
                            </table>
                            </div>
                        </div> -->
             
                <!-- /.modal -->
                @if (File::exists(base_path() . '/resources/views/powerpanel/partials/quickeditpopup.blade.php') != null)
                @include('powerpanel.partials.quickeditpopup',['TableName'=>'blogs'])
                @endif
            @else
                @if (File::exists(base_path() . '/resources/views/powerpanel/partials/addrecordsection.blade.php') != null)
                @include('powerpanel.partials.addrecordsection',['type'=>Config::get('Constant.MODULE.TITLE'), 'adUrl' => url('powerpanel/blogs/add')])
                @endif
            @endif

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
        </div>
    </div>
</div>
@if (File::exists(base_path() . '/resources/views/powerpanel/partials/deletePopup.blade.php') != null)
@include('powerpanel.partials.deletePopup')
@endif
@if (File::exists(base_path() . '/resources/views/powerpanel/partials/onepushmodal.blade.php') != null)
@include('powerpanel.partials.onepushmodal',['moduleHasImage'=>false])
@endif
@if (File::exists(base_path() . '/resources/views/powerpanel/partials/approveRecord.blade.php') != null)
@include('powerpanel.partials.approveRecord')
@endif
@if (File::exists(base_path() . '/resources/views/powerpanel/partials/cmsPageComments.blade.php') != null)
@include('powerpanel.partials.cmsPageComments',['module'=>Config::get('Constant.MODULE.TITLE')])
@endif
@endsection
  @section('scripts')
  <script type="text/javascript">
  window.site_url = '{!! url("/") !!}';
  var DELETE_URL = '{!! url("/powerpanel/services/DeleteRecord") !!}';
    var APPROVE_URL = '{!! url("/powerpanel/services/ApprovedData_Listing") !!}';
    var getChildData = window.site_url + "/powerpanel/services/getChildData";
    var getChildData_rollback = window.site_url + "/powerpanel/services/getChildData_rollback";
    var ApprovedData_Listing = window.site_url + "/powerpanel/services/ApprovedData_Listing";
    var rollbackRoute = window.site_url + "/powerpanel/services/rollback-record"; 
    var showChecker = true;
            @if (!$userIsAdmin)
            showChecker = false;
            @endif 
    var onePushShare = '{!! url("/powerpanel/share") !!}';
  var onePushGetRec = '{!! url("/powerpanel/share/getrec") !!}';
  var categoryAllowed = false;
  @can('services-category-list')
  categoryAllowed = true;
  @endcan
  var tableState = '{{ $tableState }}';
</script>
<script src="{{ url('resources/global/plugins/jquery-cookie-master/src/jquery.cookie.js') }}" type="text/javascript">
</script>
<script src="{{ url('resources/global/plugins/select2/js/select2.full.min.js') }}" type="text/javascript"></script>
<script src="{{ url('resources/global/plugins/select2/js/components-select2.min.js') }}" type="text/javascript"></script>
<script src="{{ url('resources/global/scripts/datatable.js') }}" type="text/javascript"></script>
<script src="{{ url('resources/global/plugins/datatables/datatables.min.js') }}" type="text/javascript"></script>
<script src="{{ url('resources/global/plugins/datatables/dataTables.editor.js') }}" type="text/javascript"></script>
<script src="{{ $CDN_PATH.'resources/global/plugins/fancybox/source/helpers/jquery.fancybox-thumbs.js' }}" type="text/javascript"></script>
<script src="{{ url('resources/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.js') }}" type="text/javascript"></script>
<script src="{{ url('resources/pages/scripts/packages/services/services-datatables-ajax.js') }}" type="text/javascript"></script>
<script src="{{ url('resources/pages/scripts/custom.js') }}" type="text/javascript"></script>
<script src="{{ url('resources/pages/scripts/sharer-validations.js') }}" type="text/javascript"></script>
<script src="{{ url('resources/global/plugins/highslide/highslide-with-html.js') }}" type="text/javascript"></script>
@if((File::exists(app_path() . '/Workflow.php') != null || File::exists(base_path() . '/packages/Powerpanel/Workflow/src/Models/Workflow.php') != null))
<script src="{{ $CDN_PATH.'resources/pages/scripts/user-updates-approval.js' }}" type="text/javascript"></script>
@endif
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