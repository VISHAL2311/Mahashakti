@extends('powerpanel.layouts.app')
@section('title')
{{Config::get('Constant.SITE_NAME')}} - PowerPanel
@endsection
@section('css')
<link href="{{ $CDN_PATH.'resources/global/plugins/datatables/datatables.min.css' }}" rel="stylesheet" type="text/css" />
<link href="{{ $CDN_PATH.'resources/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.css' }}" rel="stylesheet" type="text/css" />
<link href="{{ $CDN_PATH.'resources/global/plugins/bootstrap-datepicker/css/bootstrap-datepicker3.min.css' }}" rel="stylesheet" type="text/css" />
<link href="{{ $CDN_PATH.'resources/global/plugins/highslide/highslide.css' }}" rel="stylesheet" type="text/css"/>
@endsection
@section('content')
<!--@include('powerpanel.partials.breadcrumbs')-->
<!-- BEGIN PAGE BASE CONTENT -->
<div class="row">
    <div class="col-md-12">
        <!-- TITILE HEAD START -->

        <div class="title-dropdown_sec">
            <div class="title_bar">
                <div class="page-head">
                    <div class="page-title">
                        <h1>Manage AUDIT LOG</h1>                        
                    </div>   
                    <ul class="page-breadcrumb breadcrumb">
                        <li>
                            <span aria-hidden="true" class="icon-home"></span>
                            <a href="{{ url('powerpanel') }}">Home</a>
                            <i class="fa fa-circle"></i>
                        </li>
                        <li class="active">Manage Audit Log</li>
                    </ul>
                    <a class="drop_toogle_arw" href="javascript:void(0);" data-toggle="collapse" data-target="#cmspage_id"><i class="la la-chevron-circle-up"></i></a>                                           
                </div>    
                <div class="add_category_button pull-right" style="display:none;">
                    <a title="Help" class="add_category" target="_blank" href="{{ url('assets/videos/Shield_CMS_WorkFlow.mp4')}}">
                        <span title="Help">Help</span> <i class="la la-question-circle"></i>
                    </a>
                </div>
            </div>
            <div id="cmspage_id" class="collapse in">
                <div class="collapse-inner">
                    <div class="portlet-title select_box filter-group">
                        <div class="row">
                            <div class="col-lg-8 col-md-12 col-xs-12">
                                <div class="portlet-lf-title">
                                    <?php if (!isset($_REQUEST['id']) && !isset($_REQUEST['mid'])) { ?>
                                        <div class="sub_select_filter" id="hidefilter">
                                            <span class="title">{{  trans('shiledcmstheme::template.common.filterby') }}:</span>
                                            <span class="select_input">
                                                <select id="modulefilter" name="modulefilter" data-sort data-order class="bs-select select2">
                                                    <option value=" ">-{!!  trans('shiledcmstheme::template.common.selectmodule') !!}-</option>
                                                    @if(count($modules) > 0)
                                                    @foreach ($modules as $pagedata)
                                                    @php
                                                    $avoidModules = array('testimonial');
                                                    @endphp
                                                    @if (ucfirst($pagedata->varTitle)!='Home' && !in_array($pagedata->varModuleName,$avoidModules))
                                                    <option data-model="{{ $pagedata->varModelName }}" data-module="{{ $pagedata->varModuleName }}" data-namespace="{{ $pagedata->varModuleNameSpace }}" data-id="{{ $pagedata->id }}" value="{{ $pagedata->id }}" {{ (isset($banners->fkModuleId) && $pagedata->id == $banners->fkModuleId) || $pagedata->id == old('modules')? 'selected' : '' }} >{{ $pagedata->varTitle }}</option>
                                                    @endif
                                                    @endforeach
                                                    @endif
                                                </select>
                                            </span>
                                            <span class="select_input" style="display:none;">    
                                                <select id="foritem" name="foritem" data-sort data-order class="bs-select select2 category_filter">
                                                    <option value=" ">--{!!  trans('shiledcmstheme::template.bannerModule.selectPage') !!}--</option>
                                                </select>
                                            </span>
                                            <span class="select_input">
                                                <select id="userfilter" name="userfilter" data-sort data-order class="bs-select select2 category_filter">
                                                    <option value=" ">--{!!  trans('shiledcmstheme::template.common.selectuser') !!}--</option>
                                                    @if(!empty($userslist))
                                                    @foreach ($userslist as $users)
                                                    @php
                                                    $avoidUsers = array();
                                                    @endphp
                                                    @if (!in_array($users->id,$avoidUsers))
                                                    	<option value="{{ $users->id }}" {{ $users->id == old('userfilter')? 'selected' : '' }} >{{ $users->name }}</option>
                                                    @endif
                                                    @endforeach
                                                    @endif
                                                </select>
                                            </span>
                                            <span class="btn btn-icon-only btn-green-drake green-new" type="button" id="refresh" title="Reset">
                                                <i class="fa fa-refresh" aria-hidden="true"></i>
                                            </span>
                                        </div>
                                    <?php } ?>
                                </div>    
                            </div>
                            <div class="col-lg-4 col-md-12 col-xs-12">
                                <div class="portlet-rh-title">    
                                    <div class="public-status pub-log-status">
                                        <ul>
                                            <li class="pub_status adddiv"><a class="list_head_filter" href="javascript:;" data-filterIdentity="add"><span>Add</span></a></li>
                                            <li class="pub_status updatediv"><a class="list_head_filter" href="javascript:;" data-filterIdentity="edit"><span>Update</span></a></li>
                                            <li class="pub_status deletediv"><a class="list_head_filter" href="javascript:;" data-filterIdentity="delete"><span>Delete</span></a></li>
                                            <!-- <li class="pub_status transhdiv"><a class="list_head_filter" href="javascript:;" data-filterIdentity="trash"><span>Trash</span></a></li>
                                            <li class="pub_status commentdiv"><a class="list_head_filter" href="javascript:;" data-filterIdentity="comment"><span>Comment</span></a></li>
                                            <li class="pub_status approveddiv"><a class="list_head_filter" href="javascript:;" data-filterIdentity="approved"><span>Approved</span></a></li>
                                            <li class="pub_status copydiv"><a class="list_head_filter" href="javascript:;" data-filterIdentity="copy"><span>Copy</span></a></li> -->
                                            <li class="pub_status otherdiv"><a class="list_head_filter" href="javascript:;" data-filterIdentity="other"><span>Other</span></a></li>
                                        </ul>
                                    </div>
                                </div>            
                            </div>
                        </div>  
                    </div>
                </div>
            </div> 
        </div>
        <!-- TITILE HEAD End... --> 
        <!-- Begin: life time stats -->
        <div class="portlet light portlet-fit portlet-datatable">
            @if($total > 0)
            
            <div class="portlet-body">
                <div class="table-container">
                    <div class="table-actions-wrapper">
                        <div class="search_rh_div pull-right">
                            <span>Search:</span>
                            <input type="search" class="form-control form-control-solid placeholder-no-fix" placeholder="Search by User" id="searchfilter">
                        </div>
                    </div>

                    <table class="new_table_desing table table-striped table-bordered table-hover table-checkable hide-mobile" id="datatable_ajax">
                        <thead>
                            <tr role="row" class="heading">
                                <th width="2%" align="center">
                                    <input type="checkbox" class="group-checkable">
                                </th>
                                <th width="2%" align="center"></th>
                                <th width="15%" align="left">{{  trans('shiledcmstheme::template.common.user') }}</th>
                                <th width="20%" align="left">Name</th>
                                <th width="15%" align="left">{{  trans('shiledcmstheme::template.common.action') }}</th>
                                <th width="10%" align="center">{{  trans('shiledcmstheme::template.common.ipAddress') }}</th>
                                <th width="20%" align="center">{{  trans('shiledcmstheme::template.common.dateandtime') }}</th>
                            </tr>
                        </thead>
                        <tbody> </tbody>
                    </table>
                    @can('log-delete')
                    <a href="javascript:;" class="btn-sm btn btn-outline red right_bottom_btn deleteMass">{{  trans("shiledcmstheme::template.common.delete") }}
                    </a>
                    @endcan
                    @if ($userIsAdmin)
                    <a href="#selectedRecords" class="btn-sm btn btn-green-drake right_bottom_btn ExportRecord" data-toggle="modal">Export</a>
                    @endif
                </div>
            </div>
            @else
            @include('powerpanel.partials.addrecordsection')
            @endif
        </div>
    </div>
</div>
<div class="new_modal modal fade bs-modal-md" id="confirmForAll" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-md">
        <div class="modal-vertical">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                    Confirm
                </div>
                <div class="modal-body text-center delMsg"></div>
                <div class="modal-footer">
                    <button type="button" id="deleteAll" class="btn red btn-outline">{{  trans("shiledcmstheme::template.common.delete") }}</button>
                    <button type="button" class="btn btn-green-drake" data-dismiss="modal">{{  trans("shiledcmstheme::template.common.close") }}</button>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="new_modal modal fade" id="noRecords" tabindex="-1" role="basic" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-vertical">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                    {{  trans('shiledcmstheme::template.common.alert') }}
                </div>
                <div class="modal-body text-center">{{  trans('shiledcmstheme::template.contactleadModule.noExport') }} </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-green-drake" data-dismiss="modal">{{  trans('shiledcmstheme::template.common.ok') }}</button>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="new_modal modal fade" id="selectedRecords" tabindex="-1" role="basic" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-vertical">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                    {{  trans('shiledcmstheme::template.common.alert') }}
                </div>
                <div class="modal-body text-center">{{  trans('shiledcmstheme::template.contactleadModule.recordsExport') }}</div>
                <div class="modal-footer">
                    <div align="center">
                        <div class="md-radio-inline">
                            <div class="md-radio">
                                <input type="radio" value="selected_records" id="selected_records" name="export_type" class="md-radiobtn" checked="checked">
                                <label for="selected_records">
                                    <span class="inc"></span>
                                    <span class="check"></span>
                                    <span class="box"></span> {{  trans('shiledcmstheme::template.contactleadModule.selectedRecords') }}
                                </label>
                            </div>
                            <div class="md-radio">
                                <input type="radio" value="all_records" id="all_records" name="export_type" class="md-radiobtn">
                                <label for="all_records">
                                    <span class="inc"></span>
                                    <span class="check"></span>
                                    <span class="box"></span>{{  trans('shiledcmstheme::template.contactleadModule.allRecords') }}
                                </label>
                            </div>
                        </div>
                    </div>
                    <button type="button" class="btn btn-green-drake" id="ExportRecord" data-dismiss="modal">{{  trans('shiledcmstheme::template.common.ok') }} </button>
                </div>
            </div>
            <!-- /.modal-content -->
        </div>
    </div>
    <!-- /.modal-dialog -->
</div>
<div class="new_modal modal fade" id="noSelectedRecords" tabindex="-1" role="basic" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-vertical">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                    {{  trans('shiledcmstheme::template.common.alert') }}
                </div>
                <div class="modal-body text-center">{{  trans('shiledcmstheme::template.contactleadModule.leastRecord') }} </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-green-drake" data-dismiss="modal">{{  trans('shiledcmstheme::template.common.ok') }} </button>
                </div>
            </div>
            <!-- /.modal-content -->
        </div>
    </div>
    <!-- /.modal-dialog -->
</div>
<!-- /.modal -->
@endsection
@section('scripts')
<script type="text/javascript">
    window.site_url = '{!! url("/") !!}';
    var DELETE_URL = '{!! url("/powerpanel/log/DeleteRecord") !!}';
    var QuerySringParams = {};
</script>
<?php if (isset($_REQUEST['id']) && $_REQUEST['id'] != '') { ?>
    <script type="text/javascript">
        var rid = '?rid=<?php echo $_REQUEST['id'] ?>';
        QuerySringParams.rid = '<?php echo $_REQUEST['id'] ?>';
    </script>
<?php } else { ?>
    <script type="text/javascript">
        var rid = '';
    </script>
<?php } ?>
<?php if (isset($_REQUEST['mid']) && $_REQUEST['mid'] != '') { ?>
    <script type="text/javascript">
        var mid = '&mid=<?php echo $_REQUEST['mid'] ?>';
        QuerySringParams.mid = '<?php echo $_REQUEST['mid'] ?>';
    </script>
<?php } else { ?>
    <script type="text/javascript">
        var mid = '';
    </script>
<?php } ?>
<script type="text/javascript">
    var showChecker = true;
            @if (!$userIsAdmin)
    showChecker = false;
    @endif
            var selectedRecord = '';
</script>
<script src="{{ $CDN_PATH.'resources/global/plugins/jquery-cookie-master/src/jquery.cookie.js' }}" type="text/javascript"></script>
<script src="{{ $CDN_PATH.'resources/global/scripts/datatable.js' }}" type="text/javascript"></script>
<script src="{{ $CDN_PATH.'resources/global/plugins/datatables/datatables.min.js' }}" type="text/javascript"></script>
<script src="{{ $CDN_PATH.'resources/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.js' }}" type="text/javascript"></script>
<script src="{{ $CDN_PATH.'resources/global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js' }}" type="text/javascript"></script>
<script src="{{ $CDN_PATH.'resources/pages/scripts/packages/logmanager/log-datatables-ajax.js' }}" type="text/javascript"></script>
<script src="{{ $CDN_PATH.'resources/pages/scripts/custom.js' }}" type="text/javascript"></script>
<script src="{{ $CDN_PATH.'resources/global/plugins/highslide/highslide-with-html-log.js' }}" type="text/javascript"></script>
@endsection