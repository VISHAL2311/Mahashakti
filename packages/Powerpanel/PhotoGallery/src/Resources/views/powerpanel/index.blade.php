@extends('powerpanel.layouts.app')
@section('title')
{{Config::get('Constant.SITE_NAME')}} - PowerPanel
@stop
@section('css')
<link href="{{ $CDN_PATH.'resources/global/plugins/datatables/datatables.min.css' }}" rel="stylesheet" type="text/css" />
<link href="{{ $CDN_PATH.'resources/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.css' }}" rel="stylesheet" type="text/css" />
<link href="{{ $CDN_PATH.'resources/global/plugins/fancybox/source/helpers/jquery.fancybox-thumbs.css' }}" rel="stylesheet" type="text/css"/>
<link href="{{ $CDN_PATH.'resources/global/plugins/highslide/highslide.css' }}" rel="stylesheet" type="text/css"/>
@endsection
@section('content')
<!-- BEGIN PAGE BASE CONTENT -->
{!! csrf_field() !!}
<div class="row">
    <div class="col-md-12">
        <!-- TITILE HEAD START -->
        <div class="title-dropdown_sec">
             @if (File::exists(base_path() . '/resources/views/powerpanel/partials/listbreadcrumbs.blade.php') != null)
            @include('powerpanel.partials.listbreadcrumbs',['ModuleName'=>'Manage Photo Gallery'])
            @endif
            
            <div id="cmspage_id" class="collapse in">
                <div class="collapse-inner">
                    <div class="portlet-title select_box filter-group">
                        <div class="row">
                            <div class="col-lg-6 col-md-12 col-xs-12">
                                <div class="portlet-lf-title">
                                    <div class="sub_select_filter" id="hidefilter">
                                        <span class="title">{!! trans('photogallery::template.common.filterby') !!}:</span>
                                        <span class="select_input">
                                            <select id="statusfilter" data-sort data-order class="bs-select select2">
                                                <option value=" ">{!! trans('photogallery::template.common.selectstatus') !!}</option>
                                                <option value="Y">{!! trans('photogallery::template.common.publish') !!}</option>
                                                <option value="N">{!! trans('photogallery::template.common.unpublish') !!}</option>
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
                                    @can('photo-gallery-create')
                                    <div class="add_category_button pull-right">
                                        <a class="add_category" href="{{ url('powerpanel/photo-gallery/add') }}" title="{{ trans('photogallery::template.photoGalleryModule.addphotoGallery') }}"><span>{{ trans('photogallery::template.photoGalleryModule.addphotoGallery') }}</span> <i class="la la-plus"></i></a>
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
        <!-- Begin: life time stats -->
        @if(Session::has('message'))
        <div class="alert alert-success">
            <button class="close" data-close="alert"></button>
            {{ Session::get('message') }}
        </div>
        @endif
        <div class="portlet light portlet-fit portlet-datatable">
            @if($iTotalRecords > 0)
            <div class="portlet-body">
                <div class="table-container">
                    <div class="col-md-12 col-sm-12 ">
                        <div class="row">
                            <div class="row col-md-6 col-sm-12 pull-right search_pages_div">
                                <div class="search_rh_div">
                                    <span>{{ trans('photogallery::template.common.search') }}:</span>
                                    <input type="search" class="form-control form-control-solid placeholder-no-fix" placeholder="Search by Title" id="searchfilter">
                                </div>
                            </div>
                        </div>
                    </div>
                         @php
                        $tablearray = [
                            'DataTableTab'=>[
                                'ColumnSetting'=>[
                                    ['Identity_Name'=>'title','TabIndex'=>'2','Name'=>'Title'],
                                    ['Identity_Name'=>'image','TabIndex'=>'3','Name'=>'Image'],
                                    ['Identity_Name'=>'cat','TabIndex'=>'4','Name'=>'Photo Album'],
                                    ['Identity_Name'=>'sdate','TabIndex'=>'5','Name'=>'Start Date'],
                                    ['Identity_Name'=>'edate','TabIndex'=>'6','Name'=>'End Date'],
                                    ['Identity_Name'=>'order','TabIndex'=>'7','Name'=>'Order'],
                                    ['Identity_Name'=>'publish','TabIndex'=>'8','Name'=>'Publish'],
                                    ['Identity_Name'=>'dactions','TabIndex'=>'9','Name'=>'Action']
                                ],
                                'DataTableHead'=>[
                                    ['Title'=>'Title','Align'=>'left'],
                                    ['Title'=>'Image','Align'=>'center'],
                                    ['Title'=>'Photo Album','Align'=>'center'],
                                    ['Title'=>'Start Date','Align'=>'center'],
                                    ['Title'=>'End Date','Align'=>'center'],
                                    ['Title'=>'Order','Align'=>'center'],
                                    ['Title'=>'Publish','Align'=>'center'],
                                    ['Title'=>'Action','Align'=>'right']
                                ]
                            ]
                        ];
                        @endphp
                        @include('powerpanel.partials.datatable-view',['ModuleName'=>'PhotoGallery','Permission_Delete'=>'photo-gallery-delete','tablearray'=>$tablearray,'userIsAdmin'=>$userIsAdmin,'Module_ID'=>Config::get('Constant.MODULE.ID')])
                </div>
            </div>
            <!-- Modal -->
             @if (File::exists(base_path() . '/resources/views/powerpanel/partials/quickeditpopup.blade.php') != null)
            @include('powerpanel.partials.quickeditpopup',['TableName'=>'photoGallery'])
            @endif
            @else
            @if (File::exists(base_path() . '/resources/views/powerpanel/partials/addrecordsection.blade.php') != null)
            @include('powerpanel.partials.addrecordsection',['type'=>Config::get('Constant.MODULE.TITLE'), 'adUrl' => url('powerpanel/photo-gallery/add')])
            @endif
            @endif
        </div>
    </div>
</div>
@if (File::exists(base_path() . '/resources/views/powerpanel/partials/deletePopup.blade.php') != null)
@include('powerpanel.partials.deletePopup')
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
    var DELETE_URL = '{!! url("/powerpanel/photo-gallery/DeleteRecord") !!}';
    var APPROVE_URL = '{!! url("/powerpanel/photo-gallery/ApprovedData_Listing") !!}';
    var getChildData = window.site_url + "/powerpanel/photo-gallery/getChildData";
    var getChildData_rollback = window.site_url + "/powerpanel/photo-gallery/getChildData_rollback";
    var ApprovedData_Listing = window.site_url + "/powerpanel/photo-gallery/ApprovedData_Listing";
    var Get_Comments = '{!! url("/powerpanel/photo-gallery/Get_Comments") !!}';
    var Quick_module_id = '<?php echo Config::get('Constant.MODULE.ID'); ?>';
      var settingarray = jQuery.parseJSON('{!!$settingarray!!}');
    var showChecker = true;
            @if (!$userIsAdmin)
            showChecker = false;
            @endif
</script>
<script src="{{ $CDN_PATH.'resources/global/plugins/jquery-cookie-master/src/jquery.cookie.js' }}" type="text/javascript"></script>
<script src="{{ $CDN_PATH.'resources/global/scripts/datatable.js' }}" type="text/javascript"></script>
<script src="{{ $CDN_PATH.'resources/global/plugins/datatables/datatables.min.js' }}" type="text/javascript"></script>
<script src="{{ $CDN_PATH.'resources/global/plugins/datatables/dataTables.editor.js' }}" type="text/javascript"></script>
<script src="{{ $CDN_PATH.'resources/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.js' }}" type="text/javascript"></script>
<script src="{{ $CDN_PATH.'resources/global/plugins/fancybox/source/helpers/jquery.fancybox-thumbs.js' }}" type="text/javascript"></script>
<script src="{{ $CDN_PATH.'resources/pages/scripts/packages/photogallery/photo-gallery-datatables-ajax.js' }}" type="text/javascript"></script>
<script src="{{ $CDN_PATH.'resources/pages/scripts/custom.js' }}" type="text/javascript"></script>
<script src="{{ $CDN_PATH.'resources/global/plugins/highslide/highslide-with-html.js' }}" type="text/javascript"></script>
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