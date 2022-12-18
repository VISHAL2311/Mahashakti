@extends('powerpanel.layouts.app')
@section('title')
{{Config::get('Constant.SITE_NAME')}} - PowerPanel
@endsection
@section('css')
<link href="{{ $CDN_PATH.'resources/global/plugins/datatables/datatables.min.css' }}" rel="stylesheet" type="text/css"/>
<link href="{{ $CDN_PATH.'resources/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.css' }}" rel="stylesheet" type="text/css"/>
<link href="{{ $CDN_PATH.'resources/global/plugins/tooltips/tooltip.css' }}" rel="stylesheet" type="text/css"/>
<link href="{{ $CDN_PATH.'resources/global/plugins/highslide/highslide.css' }}" rel="stylesheet" type="text/css"/>
@endsection
@section('content')
<!--@include('powerpanel.partials.breadcrumbs')-->
<div class="row">
    <div class="col-md-12">

        <!-- TITILE HEAD START -->

        <div class="title-dropdown_sec">
            @if (File::exists(base_path() . '/resources/views/powerpanel/partials/listbreadcrumbs.blade.php') != null)
            @include('powerpanel.partials.listbreadcrumbs',['ModuleName'=>'Manage Pages'])
            @endif
            
            <div id="cmspage_id" class="collapse in">
                <div class="collapse-inner">
                    <div class="portlet-title select_box filter-group">
                        <div class="row">
                            <div class="col-lg-6 col-md-12 col-xs-12">
                                <div class="portlet-lf-title">
                                    <div class="sub_select_filter" id="hidefilter">
                                        <span class="title">{{ trans('cmspage::template.common.filterby') }}:</span>
                                        <span class="select_input">
                                            <select id="statusfilter" data-sort data-order class="bs-select select2">
                                                <option value=" ">{{ trans('cmspage::template.common.selectstatus') }}</option>
                                                <option value="Y">{{ trans('cmspage::template.common.publish') }}</option>
                                                <option value="N">{{ trans('cmspage::template.common.unpublish') }}</option>
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
                                    @can('pages-create')
                                    <div class="add_category_button pull-right">
                                        <a class="add_category" href="{{ url('powerpanel/pages/add') }}" title="{{ trans('cmspage::template.pageModule.add') }}"><span>{{ trans('cmspage::template.pageModule.add') }}</span> <i class="la la-plus"></i></a>
                                    </div>
                                    @endcan
                                    @if (Config::get('Constant.DEFAULT_VISIBILITY') == 'Y')
                                    <div class="public-status">
                                        <ul>
                                            <li class="pub_status publicdiv"><a class="list_head_filter" href="javascript:;" data-filterIdentity="PU"><span>Public</span></a></li>
                                            <li class="pub_status privatediv"><a class="list_head_filter" href="javascript:;" data-filterIdentity="PR"><span>Private</span></a></li>
                                            <li class="pub_status passworddiv"><a class="list_head_filter" href="javascript:;" data-filterIdentity="PP"><span>Password Protected</span></a></li>
                                        </ul>
                                    </div>
                                    @endif
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
        <div class="portlet light portlet-fit portlet-datatable bordered">
            @if($iTotalRecords > 0)

            <div class="portlet-body">
                <div class="table-container">
                    <div class="col-md-12 col-sm-12 ">
                        <div class="row">
                            <div class="row col-md-6 col-sm-12 pull-right search_pages_div">
                                <div class="search_rh_div">
                                    <span>{{ trans('cmspage::template.common.search') }}:</span>
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
                                ['Identity_Name'=>'module','TabIndex'=>'3','Name'=>'Module'],
                                ['Identity_Name'=>'hits','TabIndex'=>'4','Name'=>'Hits'],
                                ['Identity_Name'=>'publish','TabIndex'=>'5','Name'=>'Publish'],
                                ['Identity_Name'=>'mdate','TabIndex'=>'6','Name'=>'Modify Date'],
                                ['Identity_Name'=>'dactions','TabIndex'=>'7','Name'=>'Action']
                            ],
                            'DataTableHead'=>[
                                ['Title'=>'Title','Align'=>'left'],
                                ['Title'=>'Module','Align'=>'left'],
                                ['Title'=>'Hits','Align'=>'center'],
                                ['Title'=>'Publish','Align'=>'center'],
                                ['Title'=>'Modify Date','Align'=>'center'],
                                ['Title'=>'Action','Align'=>'right']
                            ]
                        ]
                    ];
                    @endphp
                    @include('powerpanel.partials.datatable-view',['ModuleName'=>'Pages','Permission_Delete'=>'pages-delete','tablearray'=>$tablearray,'userIsAdmin'=>$userIsAdmin,'Module_ID'=>Config::get('Constant.MODULE.ID')])
                    
                </div>
            </div>
            <!-- Modal -->
            @if (File::exists(base_path() . '/resources/views/powerpanel/partials/quickeditpopup.blade.php') != null)
            @include('powerpanel.partials.quickeditpopup',['TableName'=>'Cmspage'])
            @endif
            @else
            @if (File::exists(base_path() . '/resources/views/powerpanel/partials/addrecordsection.blade.php') != null)
            @include('powerpanel.partials.addrecordsection',['type'=>Config::get('Constant.MODULE.TITLE'), 'adUrl' => url('powerpanel/pages/add')])
            @endif
            @endif

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
    var DELETE_URL = '{!! url("/powerpanel/pages/DeleteRecord") !!}';
    var APPROVE_URL = '{!! url("/powerpanel/pages/ApprovedData_Listing") !!}';
    $(document).ready(function () {
        $('[data-toggle="tooltip"]').tooltip();
    });
    var APPROVE_URL = '{!! url("/powerpanel/pages/ApprovedData_Listing") !!}';
    var getChildData = window.site_url + "/powerpanel/pages/getChildData";
    var rollbackRoute = window.site_url + "/powerpanel/pages/rollback-record";
    var getChildData_rollback = window.site_url + "/powerpanel/pages/getChildData_rollback";
    var ApprovedData_Listing = window.site_url + "/powerpanel/pages/ApprovedData_Listing";
    var Get_Comments = window.site_url + "/powerpanel/pages/Get_Comments";

    var Quick_module_id = '<?php echo Config::get('Constant.MODULE.ID'); ?>';
      var settingarray = jQuery.parseJSON('{!!$settingarray!!}');
    var showChecker = true;
            @if (!$userIsAdmin)
            showChecker = false;
            @endif

            var onePushShare = '{!! url("/powerpanel/share") !!}';
    var onePushGetRec = '{!! url("/powerpanel/share/getrec") !!}';</script>
<script src="{{ $CDN_PATH.'resources/global/plugins/jquery-cookie-master/src/jquery.cookie.js' }}" type="text/javascript"></script>
<script src="{{ $CDN_PATH.'resources/global/scripts/datatable.js' }}" type="text/javascript"></script>
<script src="{{ $CDN_PATH.'resources/global/plugins/datatables/datatables.min.js' }}" type="text/javascript"></script>
<script src="{{ $CDN_PATH.'resources/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.js' }}" type="text/javascript"></script>
<script src="{{ $CDN_PATH.'resources/global/plugins/tooltips/tooltip.js' }}" type="text/javascript"></script>
<script src="{{ $CDN_PATH.'resources/pages/scripts/packages/cmspage/table-cms-pages-ajax.js' }}" type="text/javascript"></script>
@if((File::exists(app_path() . '/Workflow.php') != null || File::exists(base_path() . '/packages/Powerpanel/Workflow/src/Models/Workflow.php') != null))
<script src="{{ $CDN_PATH.'resources/pages/scripts/user-updates-approval.js' }}" type="text/javascript"></script>
@endif
<script src="{{ $CDN_PATH.'resources/pages/scripts/custom.js' }}" type="text/javascript"></script>
<script src="{{ $CDN_PATH.'resources/pages/scripts/sharer-validations.js' }}" type="text/javascript"></script>

@if(Auth::user()->hasRole('user_account'))
<script type="text/javascript">
    $(document).ready(function () {
        setInterval(function () {
            $('.checker').closest("td").hide();
            $('.checker').closest("th").hide();
        }, 800);
    });
    var moduleName = 'pages';</script>
@endif
@endsection