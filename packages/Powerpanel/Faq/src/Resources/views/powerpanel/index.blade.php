@extends('powerpanel.layouts.app')
@section('title')
{{Config::get('Constant.SITE_NAME')}} - PowerPanel
@stop
@section('css')
<link href="{{ $CDN_PATH.'resources/global/plugins/datatables/datatables.min.css' }}" rel="stylesheet" type="text/css" />
<link href="{{ $CDN_PATH.'resources/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.css' }}" rel="stylesheet" type="text/css" />
<link href="{{ $CDN_PATH.'resources/global/plugins/highslide/highslide.css' }}" rel="stylesheet" type="text/css"/>
@endsection
@section('content')
<!-- BEGIN PAGE BASE CONTENT -->
{!! csrf_field() !!}
<div class="row">
    <div class="col-md-12">
        <!-- TITILE HEAD START -->
        <div class="title-dropdown_sec">
            <div class="title_bar">
                <div class="page-head">
                    <div class="page-title">
                        <h1>Manage FAQ'S</h1>                        
                    </div>     
                    <ul class="page-breadcrumb breadcrumb">
                        <li>
                            <span aria-hidden="true" class="icon-home"></span>
                            <a href="{{ url('powerpanel') }}">Home</a>
                            <i class="fa fa-circle"></i>
                        </li>
                        <li class="active">Manage FAQ's</li>
                    </ul>	
                    <a class="drop_toogle_arw" href="javascript:void(0);" data-toggle="collapse" data-target="#cmspage_id"><i class="la la-chevron-circle-up"></i></a>                                           
                </div>
            </div>
            <div id="cmspage_id" class="collapse in">
                <div class="collapse-inner">
                    <div class="portlet-title select_box filter-group">
                        <div class="row">
                            <div class="col-lg-6 col-md-12 col-xs-12">
                                <div class="portlet-lf-title">
                                    <div class="sub_select_filter" id="hidefilter">
                                        <span class="title">{!! trans('faq::template.common.filterby') !!}:</span>
                                        <span class="select_input">
                                            <select id="statusfilter" data-sort data-order class="bs-select select2">
                                                <option value=" ">{!! trans('faq::template.common.selectstatus') !!}</option>
                                                <option value="Y">{!! trans('faq::template.common.publish') !!}</option>
                                                <option value="N">{!! trans('faq::template.common.unpublish') !!}</option>
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
                                    @can('faq-create')
                                     @if( isset(App\Helpers\MyLibrary::getFront_Uri('faq')['uri']) )
                                    <div class="add_category_button pull-right">
                                        <a class="add_category" href="{{ url('powerpanel/faq/add') }}" title="{{ trans('faq::template.faqModule.addFaq') }}"><span>{{ trans('faq::template.faqModule.addFaq') }}</span> <i class="la la-plus"></i></a>
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
            @if( !isset(App\Helpers\MyLibrary::getFront_Uri('faq')['uri']) )
            @include('powerpanel.partials.pagenotavailable')
            @elseif($iTotalRecords > 0)
            <div class="portlet-body">
                <div class="table-container">
                    <div class="col-md-12 col-sm-12 ">
                        <div class="row">
                            <div class="row col-md-6 col-sm-12 pull-right search_pages_div">
                                <div class="search_rh_div">
                                    <span>{{ trans('faq::template.common.search') }}:</span>
                                    <input type="search" class="form-control form-control-solid placeholder-no-fix" placeholder="Search by Question" id="searchfilter">
                                </div>
                            </div>
                        </div>
                    </div>
                    {{--@if (File::exists(base_path() . '/resources/views/powerpanel/partials/tabpanel.blade.php') != null)
                    @include('powerpanel.partials.tabpanel',['tabarray'=>['favoriteTotalRecords','draftTotalRecords','trashTotalRecords']])
                    @endif--}}
                    @php
                    $tablearray = [
                        'DataTableTab'=>[
                            'ColumnSetting'=>[
                                ['Identity_Name'=>'Question','TabIndex'=>'2','Name'=>'Question'],
                                ['Identity_Name'=>'order','TabIndex'=>'3','Name'=>'Order'],
                                ['Identity_Name'=>'publish','TabIndex'=>'4','Name'=>'Publish'],
                                ['Identity_Name'=>'dactions','TabIndex'=>'5','Name'=>'Action']
                            ],
                            'DataTableHead'=>[
                                ['Title'=>'Question','Align'=>'left'],
                                ['Title'=>'Order','Align'=>'center'],
                                ['Title'=>'Publish','Align'=>'center'],
                                ['Title'=>'Action','Align'=>'right']
                            ]
                        ]
                    ];
                    @endphp
                    @include('powerpanel.partials.datatable-view',['ModuleName'=>'Faq','Permission_Delete'=>'faq-delete','tablearray'=>$tablearray,'userIsAdmin'=>$userIsAdmin,'Module_ID'=>Config::get('Constant.MODULE.ID')])
                </div>
            </div>
            <!-- Modal -->
            @if (File::exists(base_path() . '/resources/views/powerpanel/partials/quickeditpopup.blade.php') != null)
            @include('powerpanel.partials.quickeditpopup',['TableName'=>'faq'])
            @endif
            @else
            @if (File::exists(base_path() . '/resources/views/powerpanel/partials/addrecordsection.blade.php') != null)
            @include('powerpanel.partials.addrecordsection',['type'=>Config::get('Constant.MODULE.TITLE'), 'adUrl' => url('powerpanel/faq/add')])
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
    var DELETE_URL = '{!! url("/powerpanel/faq/DeleteRecord") !!}';
    var APPROVE_URL = '{!! url("/powerpanel/faq/ApprovedData_Listing") !!}';
    var getChildData = window.site_url + "/powerpanel/faq/getChildData";
    var getChildData_rollback = window.site_url + "/powerpanel/faq/getChildData_rollback";
    var ApprovedData_Listing = window.site_url + "/powerpanel/faq/ApprovedData_Listing";
    var rollbackRoute = window.site_url + "/powerpanel/faq/rollback-record";
    var Get_Comments = '{!! url("/powerpanel/faq/Get_Comments") !!}';
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
<script src="{{ $CDN_PATH.'resources/pages/scripts/packages/faq/faq-datatables-ajax.js' }}" type="text/javascript"></script>
<script src="{{ $CDN_PATH.'resources/pages/scripts/custom.js' }}" type="text/javascript"></script>
@if((File::exists(app_path() . '/Workflow.php') != null || File::exists(base_path() . '/packages/Powerpanel/Workflow/src/Models/Workflow.php') != null))
<script src="{{ $CDN_PATH.'resources/pages/scripts/user-updates-approval.js' }}" type="text/javascript"></script>
@endif
<script type="text/javascript">
    $(document).ready(function () {
        setInterval(function () {
            $('.addhiglight').closest("td").closest("tr").addClass('higlight');
        }, 800);
    $(document).on('click', '.share', function (e) {
        e.preventDefault();
        $('.new_share_popup').modal('show');
        $('#confirm_share').modal({backdrop: 'static', keyboard: false})
                .one('click', '#share', function () {
                    deleteItem(url, alias);
                });
    });
    });
</script>
@endsection