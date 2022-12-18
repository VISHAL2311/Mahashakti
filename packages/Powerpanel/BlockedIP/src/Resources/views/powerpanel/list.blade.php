@extends('powerpanel.layouts.app')
@section('title')
{{Config::get('Constant.SITE_NAME')}} - PowerPanel
@stop
@section('css')
<link href="{{ $CDN_PATH.'resources/global/plugins/datatables/datatables.min.css' }}" rel="stylesheet" type="text/css" />
<link href="{{ $CDN_PATH.'resources/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.css' }}" rel="stylesheet" type="text/css" />
<link href="{{ $CDN_PATH.'resources/global/plugins/highslide/highslide.css' }}" rel="stylesheet" type="text/css" />
@endsection
@section('content')
<!--@include('powerpanel.partials.breadcrumbs')-->
{!! csrf_field() !!}
<div class="row">
    <div class="col-md-12">
        <!-- TITILE HEAD START -->        
        <div class="title_bar">
            <div class="page-head">
                <div class="page-title">
                    <h1>BLOCKED IPS</h1>                        
                </div>                        
            </div> 
            <ul class="page-breadcrumb breadcrumb">
                <li>
                    <span aria-hidden="true" class="icon-home"></span>
                    <a href="{{ url('powerpanel') }}">Home</a>
                    <i class="fa fa-circle"></i>
                </li>
                <li class="active">Blocked IPs</li>
            </ul>                    
            <div class="add_category_button pull-right">
                <a title="Help" class="add_category" target="_blank" href="{{ url('assets/videos/Shield_CMS_WorkFlow.mp4')}}">
                    <span title="Help">Help</span> <i class="la la-question-circle"></i>
                </a>
                @can('department-create')                                    
                <a class="add_category" href="{{ url('powerpanel/blocked-ips/add') }}"><span>Add IP</span> <i class="la la-plus"></i></a>                                    
                @endcan
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
        <div class="alert alert-success alert-success2" style="display: none;">
            <button class="close" data-close="alert"></button>
        </div>
        <div class="portlet light portlet-fit portlet-datatable bordered">
            @if($iTotalRecords > 0)
            <div class="portlet-body">
                <div class="table-container">
                    <div class="table-actions-wrapper">
                        <div class="search_rh_div pull-right">
                            <span>{{ trans('template.common.search') }}: </span>
                            <input type="search" class="form-control form-control-solid placeholder-no-fix" id="searchfilter" placeholder="Search by IP" name="searchfilter">
                        </div>
                    </div>
                    <table class="new_table_desing table table-striped table-bordered table-hover table-checkable dataTable hide-mobile" id="datatable_ajax">
                        <thead>
                            <tr role="row" class="heading">
                                <th width="2%" align="center"><input type="checkbox" class="group-checkable"></th>
                                <th width="4%" align="left"></th>
                                <th width="10%" align="left">Country</th>
                                <th width="10%" align="center">{{ trans('template.common.ip') }}</th>
                                <th width="5%" align="center">URLs</th>
                                <th width="5%" align="center">Access Information</th>
                                <!--<th width="5%" align="center">Action</th>-->
                                <th width="9%" align="center">Date Time</th>
                                <th width="5%" align="left">Action</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>

                    @if($iTotalRecords > 0)
                    <a href="javascript:;" class="btn-sm btn btn-outline green right_bottom_btn deleteMass"><i class="fa fa-unlock"></i>Un-block</a>
                    @endif

                </div>
            </div>
            @else
            @include('powerpanel.partials.addrecordsection')
            @endif
        </div>
    </div>
</div>

<div class="new_modal modal fade" id="noSelectedRecords" tabindex="-1" role="basic" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-vertical">	
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                    Alert
                </div>
                <div class="modal-body text-center">Please selecte at list one record.</div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-green-drake" data-dismiss="modal">OK</button>
                </div>
            </div>
            <!-- /.modal-content -->
        </div>
    </div>
    <!-- /.modal-dialog -->
</div>
<!-- /.modal -->
@include('powerpanel.partials.deletePopup',['module' => 'blocked-ips'])
@endsection
@section('scripts')
<script type="text/javascript">
    window.site_url = '{!! url("/") !!}';
    var MODULE_URL = '{!! url("/powerpanel/blocked-ips") !!}';
    var DELETE_URL = '{!! url("/powerpanel/blocked-ips/DeleteRecord") !!}';
</script>
<script src="{{ $CDN_PATH.'resources/global/plugins/jquery-cookie-master/src/jquery.cookie.js' }}" type="text/javascript"></script>	
<script src="{{ $CDN_PATH.'resources/global/plugins/datatables/datatables.min.js' }}" type="text/javascript"></script>
<script src="{{ $CDN_PATH.'resources/global/scripts/datatable.js' }}" type="text/javascript"></script>
<script src="{{ $CDN_PATH.'resources/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.js' }}" type="text/javascript"></script>
<script src="{{ $CDN_PATH.'resources/global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js' }}" type="text/javascript"></script>
<script src="{{ $CDN_PATH.'resources/pages/scripts/packages/blockip/blockedips-datatables-ajax.js' }}" type="text/javascript"></script>	
<script src="{{ $CDN_PATH.'resources/pages/scripts/custom.js' }}" type="text/javascript"></script>
<script src="{{ $CDN_PATH.'resources/global/plugins/highslide/highslide-with-html.js' }}" type="text/javascript"></script>
<?php
if (isset($arrResults)) {
    $icon = '';
    $blockid = '';
    foreach ($arrResults as $key => $value) {
        $icon .= '<div class="new_modal modal fade" id="blockidmodel_' . $value->id . '" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog" style="margin: 0 auto;display: table;width: 100%;height:100%;max-width: 800px;">
                        <div class="modal-vertical">
                        <div class="modal-content">
                    <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                    <h3 class="modal-title">Action</h3>
                    </div>
                    <div class="modal-body"><form name="formblockip' . $value->id . '" id="formblockip_' . $value->id . '" autocomplete="off">';
        $blockipurl = \Powerpanel\BlockedIP\Models\BlockedIps::getRecordIpList($value->varIpAddress);
        foreach ($blockipurl as $blockData) {
            if (isset($blockData->varNewUrl) && !empty($blockData->varNewUrl)) {
                $style = "";
                $stylebton = "display:none;";
                $value2 = "Y";
            } else {
                $style = "display:none;";
                $stylebton = "";
                $value2 = "N";
            }

            $blockid .= $blockData->id . ',';
            $icon .= '<div class="row">
                    <div class="col-sm-5">
                                            <div class="form-group  form-md-line-input">
                                            <input class="form-control seoField maxlength-handler" autocomplete="off" name="old_link_' . $blockData->id . '" type="text" value="' . $blockData->varUrl . '" aria-invalid="false">
                                           </div>  
                   </div>
                   <div class="col-sm-5">
                                            <div class="form-group  form-md-line-input ipblock_' . $blockData->id . '"  style="' . $style . '">
                                                <input class="form-control seoField maxlength-handler" autocomplete="off" id="new_link_' . $blockData->id . '" name="new_link_' . $blockData->id . '" type="text" value="' . $blockData->varNewUrl . '" aria-required="true" aria-invalid="false">
                                            </div>
                      </div>
                       <div class="col-sm-1" style="' . $stylebton . '">
                                            <div class="form-group  form-md-line-input">
                                            <button type="button" name="blockip_' . $blockData->id . '" value2="'.$value2.'" class="btn btn-green-drake submitaction" id="' . $blockData->id . '" value="saveandexit">Action</button>
                                                <span class="help-block"> </span>
                                            </div>
                      </div>
                    </div>';
        }
        $icon .= ' <input type="hidden" id="blockid" name="blockid" value="' . substr($blockid, 0, -1) . '"><div class="row"><div class="col-md-12 text-center">
                                           <br/><input type="submit" name="saveandexit" id="" class="btn btn-green-drake submitformblockip" value="Save &amp; Exit">
                                        </div></form></div></div>
                    </div>
                    </div>
                    </div>
                    </div>';
    }
    echo $icon;
}
?>

<script>
    $(document).on("click", "#Send_Report_Email", function () {
       var modelid = $(this).data("id");
       var selectedVal = $("#pageHitsChartFilter option:selected").val();
     $('#formblockip_' + modelid)[0].reset();
            $('#blockidmodel_' + modelid).modal('show');
    });
    $(document).ready(function () {
    @php    $blockid = ''; @endphp
    @if(isset($arrResults))
    @foreach ($arrResults as $key => $value)
@php $blockipurl = \Powerpanel\BlockedIP\Models\BlockedIps::getRecordIpList($value->varIpAddress); @endphp
       
        $("#formblockip_{{$value->id}}").validate({
            errorElement: 'span',
            errorClass: 'help-block',
            ignore: [],
            rules: {
                 @foreach ($blockipurl as $blockData) 
                new_link_{{$blockData->id}}: {
                   required:
                            {
                                depends: function () {
                                    if ($("#new_link_{{$blockData->id}}").val()=='' && $('button[name=blockip_{{$blockData->id}}]').attr('value2')=='Y') {
                                        return true;
                                    } else {
                                        return false;
                                    }
                                }
                            },
                             no_url: true,
                            
                },
                  old_link_{{$blockData->id}}: {
                      required: true,
                   no_url: true,
                  },
                @endforeach
            },
            messages: {
                 @foreach ($blockipurl as $blockData) 
                new_link_{{$blockData->id}}: {
                    required: "Please enter the link."
                },
                old_link_{{$blockData->id}}: {
                    required: "Please enter the link."
                },
                      @endforeach
              

            },
            errorPlacement: function (error, element) {
                if (element.parent('.input-group').length) {
                    error.insertAfter(element.parent());
                } else if (element.hasClass('select2')) {
                    error.insertAfter(element.next('span'));
                } else {
                    error.insertAfter(element);
                }
            },
             invalidHandler: function (event, validator) {
                var errors = validator.numberOfInvalids();
                if (errors) {
                    $.loader.close(true);
                }
                $('.alert-danger', $('#frmblockips')).show();
            },
            highlight: function (element) {
                $(element).closest('.form-group').addClass('has-error');
            },
            unhighlight: function (element) {
                $(element).closest('.form-group').removeClass('has-error');
            },
            
            submitHandler: function () {
               var ajaxurl = site_url + '/powerpanel/blocked-ips/updateblockid';
            var formData = $('form#formblockip_{{$value->id}}').serialize();
            $.ajax({
                url: ajaxurl,
                data: formData,
                type: "POST",
                dataType: "json",
                success: function (pollingdata) {

                }
            });
            $("button.close").click()
             location.reload(true);
           return false;
            },
        });
        
     @endforeach
       @endif
       $.validator.addMethod('no_url', function (value, element) {
    var re = /^[a-zA-Z0-9\-\.\:\\]+\.(com|org|net|mil|edu|COM|ORG|NET|MIL|EDU)$/;
    var re1 = /(ftp|http|https):\/\/(\w+:{0,1}\w*@)?(\S+)(:[0-9]+)?(\/|\/([\w#!:.?+=&%@!\-\/]))?/;
    var trimmed = $.trim(value);
    if (trimmed == '') {
        return true;
    }
    if (trimmed.match(re) == null && re1.test(trimmed) == true) {
        return true;
    }
}, "Only URL allowed");
    });
</script>
@endsection