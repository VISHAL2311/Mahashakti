@extends('powerpanel.layouts.app')
@section('title')
{{Config::get('Constant.SITE_NAME')}} - PowerPanel
@stop
@section('css')
<link href="{{ $CDN_PATH.'resources/global/plugins/datatables/datatables.min.css' }}" rel="stylesheet" type="text/css"/>
<link href="{{ $CDN_PATH.'resources/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.css' }}" rel="stylesheet" type="text/css"/>
<style>
    .fancybox-wrap{width:50% !important;text-align:center}
    .fancybox-inner{width:100% !important;vertical-align:middle ;height:auto !important}
</style>
@endsection
@section('content')
<!--@include('powerpanel.partials.breadcrumbs')-->
<div class="row">
    <div class="col-md-12">
        <!-- TITILE HEAD START -->
        <div class="title-dropdown_sec">
            @if (File::exists(base_path() . '/resources/views/powerpanel/partials/listbreadcrumbs.blade.php') != null)
            @include('powerpanel.partials.listbreadcrumbs',['ModuleName'=>'Manage Banners'])
            @endif
            
            <div id="cmspage_id" class="collapse in">
                <div class="collapse-inner">  
                    <div class="portlet-title select_box filter-group">
                        <div class="row">
                            <div class="col-lg-6 col-md-12 col-xs-12">
                                <div class="portlet-lf-title">
                                    <div class="sub_select_filter" id="hidefilter">
                                        <span class="title">{!! trans('banner::template.common.filterby') !!}:</span>
                                        <span class="select_input">
                                            <select id="statusfilter" data-sort data-order class="bs-select select2">
                                                <option value=" ">{!! trans('banner::template.common.selectstatus') !!}</option>
                                                <option value="Y">{!! trans('banner::template.common.publish') !!}</option>
                                                <option value="N">{!! trans('banner::template.common.unpublish') !!}</option>
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
                                    @can('banners-create')
                                    <div class="add_category_button pull-right">
                                        <a class="add_category" href="{{ url('powerpanel/banners/add') }}" title="{{ trans('banner::template.bannerModule.add') }}"><span>{{ trans('banner::template.bannerModule.add') }}</span> <i class="la la-plus"></i></a>
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
        <div class="portlet light portlet-fit portlet-datatable bordered">
            @if($total > 0)
            <div class="portlet-body">
                <div class="table-container">
                    <div class="col-md-12 col-sm-12 ">
                        <div class="row">
                            <div class="row col-md-6 col-sm-12 pull-right search_pages_div ">
                                <div class="search_rh_div">
                                    <span>{{ trans('banner::template.common.search') }}:</span>
                                    <input type="search" class="form-control form-control-solid placeholder-no-fix" placeholder="Search by Title" id="searchfilter">
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
                                    ['Identity_Name'=>'title','TabIndex'=>'2','Name'=>'Title'],
                                
                                    ['Identity_Name'=>'bannertype','TabIndex'=>'3','Name'=>'Banner Type'],
                                    ['Identity_Name'=>'page','TabIndex'=>'4','Name'=>'Page'],
                                    ['Identity_Name'=>'order','TabIndex'=>'5','Name'=>'Order'],
                                    ['Identity_Name'=>'publish','TabIndex'=>'6','Name'=>'Publish'],
                                    ['Identity_Name'=>'actions','TabIndex'=>'7','Name'=>'Action']
                                ],
                                'DataTableHead'=>[
                                    ['Title'=>'Title','Align'=>'left'],
                                 
                                    ['Title'=>'Banner Type','Align'=>'center'],
                                    ['Title'=>'Page','Align'=>'center'],
                                    ['Title'=>'Order','Align'=>'center'],
                                    ['Title'=>'Publish','Align'=>'center'],
                                    ['Title'=>'Action','Align'=>'right']
                                ]
                            ]
                        ];
                        @endphp
                        @include('powerpanel.partials.datatable-view',['ModuleName'=>'Banners','Permission_Delete'=>'banners-delete','tablearray'=>$tablearray,'userIsAdmin'=>$userIsAdmin,'Module_ID'=>Config::get('Constant.MODULE.ID')])
                    
                    
                </div>
            </div>
            <!-- Modal -->
            @if (File::exists(base_path() . '/resources/views/powerpanel/partials/quickeditpopup.blade.php') != null)
            @include('powerpanel.partials.quickeditpopup',['TableName'=>'banners'])
            @endif
            @else
             @if (File::exists(base_path() . '/resources/views/powerpanel/partials/addrecordsection.blade.php') != null)
            @include('powerpanel.partials.addrecordsection',['type'=>Config::get('Constant.MODULE.TITLE'), 'adUrl' => url('powerpanel/banners/add')])
            @endif
            @endif
            
        </div>
    </div>
</div>
@include('powerpanel.partials.deletePopup')
<div class="new_modal new_share_popup modal fade bs-modal-md" id="confirm_share" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-md">
        <div class="modal-vertical">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                </div>
                <div class="modal-body delMsg text-center">
                    <form role="form" id='frmshareoption'>
                        <div class="form-body">
                            <div class="form-group">
                                <input name="varTitle" class="form-control spinner" placeholder="{!! trans('banner::template.bannerModule.processSomething') !!}" type="text">
                            </div>
                            <div class="form-group">
                                <textarea name="txtDescription" class="form-control" placeholder="{!! trans('banner::template.common.shortdescription') !!}" rows="3"></textarea>
                            </div>
                            <div class="form-group">
                                <div class="checkbox-list">
                                    <label class="checkbox-inline">
                                        <input name="socialmedia[]" type="checkbox" value="facebook">
                                        <i class="fa fa-facebook"></i>&nbsp; {!! trans('banner::template.bannerModule.facebook') !!}
                                    </label>
                                    <label class="checkbox-inline">
                                        <input name="socialmedia[]" type="checkbox" value="twitter">
                                        <i class="fa fa-twitter"></i>&nbsp; {!! trans('banner::template.bannerModule.twitter') !!}
                                    </label>
                                    <label class="checkbox-inline">
                                        <input name="socialmedia[]" type="checkbox" value="linkedin">
                                        <i class="fa fa-linkedin"></i>&nbsp; {!! trans('banner::template.bannerModule.linkedin') !!}
                                    </label>
                                </div>
                            </div>
                            <button type="submit" class="btn btn-green-drake">{!! trans('banner::template.common.submit') !!}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@if (File::exists(base_path() . '/resources/views/powerpanel/partials/onepushmodal.blade.php') != null)
@include('powerpanel.partials.onepushmodal')
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
    var DELETE_URL = '{!! url("/powerpanel/banners/DeleteRecord") !!}';
    var onePushShare = '{!! url("/powerpanel/banners/share") !!}';
    var APPROVE_URL = '{!! url("/powerpanel/banners/ApprovedData_Listing") !!}';
    var getChildData = window.site_url + "/powerpanel/banners/getChildData";
    var getChildData_rollback = window.site_url + "/powerpanel/banners/getChildData_rollback";
    var ApprovedData_Listing = window.site_url + "/powerpanel/banners/ApprovedData_Listing";
    var rollbackRoute = window.site_url + "/powerpanel/banners/rollback-record";
    var Get_Comments = window.site_url + "/powerpanel/banners/Get_Comments";
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
<script src="{{ $CDN_PATH.'resources/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.js' }}" type="text/javascript"></script>
<script src="{{ $CDN_PATH.'resources/global/plugins/fancybox/source/helpers/jquery.fancybox-thumbs.js' }}" type="text/javascript"></script>
<script src="{{ $CDN_PATH.'resources/pages/scripts/packages/banner/table-banners-ajax.js' }}" type="text/javascript"></script>
<script src="{{ $CDN_PATH.'resources/pages/scripts/custom.js' }}" type="text/javascript"></script>
 @if((File::exists(app_path() . '/Workflow.php') != null || File::exists(base_path() . '/packages/Powerpanel/Workflow/src/Models/Workflow.php') != null))
<script src="{{ $CDN_PATH.'resources/pages/scripts/user-updates-approval.js' }}" type="text/javascript"></script>
@endif
<script src="{{ $CDN_PATH.'resources/pages/scripts/packages/banner/banners-index-validations.js' }}" type="text/javascript"></script>
<script type="text/javascript">
    $('.fancybox-buttons').fancybox({
        autoWidth: true,
        autoHeight: true,
        autoResize: true,
        autoCenter: true,
        autoDimensions: false,
        closeBtn: true,
        'maxHeight': 380,
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
            this.width = $('.fancybox-iframe').contents().find('html').width();
            this.height = $('.fancybox-iframe').contents().find('html').height();
        }
    });
    $(document).on('click', '.share', function (e) {
        e.preventDefault();
        $('.new_share_popup').modal('show');
        $('#confirm_share').modal({backdrop: 'static', keyboard: false})
                .one('click', '#share', function () {
                    deleteItem(url, alias);
                });
    });
</script>
@endsection