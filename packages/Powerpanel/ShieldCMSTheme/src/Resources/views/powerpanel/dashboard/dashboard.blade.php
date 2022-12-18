@extends('powerpanel.layouts.app')

@section('title')

{{Config::get('Constant.SITE_NAME')}} - PowerPanel

@endsection

@section('css')

<link href="{{ $CDN_PATH.'resources/global/plugins/datatables/datatables.min.css' }}" rel="stylesheet" type="text/css" />

<link href="{{ $CDN_PATH.'resources/global/plugins/highslide/highslide.css' }}" rel="stylesheet" type="text/css" />

<style type="text/css">

    .dataTables_filter, .dataTables_info { display: none; }

    .badge-danger{background: #D33600!important;}

</style>

@endsection

@section('content')

<div class="title_bar">

    <div class="page-head">

        <div class="page-title">

            <h1>Dashboard </h1>

        </div>

    </div>

    @if(!empty($dashboardWidgetSettings))

    <div class="dropdown pull-right gridsetting">

        <!-- <a class="dropdown-toggle" data-toggle="dropdown"><i class="icon-settings"></i></a> -->

        <ul class="dropdown-menu" id="grpChkBox">

            @foreach($dashboardWidgetSettings as $widget_key => $widget_value)

            @php

            $settingChecked ='';

            if($widget_value->widget_display=="Y"){

            $settingChecked ='checked="checked"';	

            }



            @endphp

            @if($widget_value->widget_id != 'widget_avl_workflow' && $widget_value->widget_id != 'widget_pending_workflow')

            <li>

                <div class="md-checkbox">

                    <input class="md-checkboxbtn dashboard_checkbox" value="{{ $widget_key }}" type="checkbox" name="{{ $widget_value->widget_id }}" id="{{ $widget_value->widget_id }}" {{ $settingChecked }}>

                    <label for="{{ $widget_value->widget_id }}">

                        <span></span>

                        <span class="check"></span>

                        <span class="box"></span>

                        {{ $widget_value->widget_name }}

                    </label>

                </div>

            </li>

            @endif

            @endforeach

        </ul>

    </div>

    @endif

</div>

<!-- END DASHBOARD STATS 1-->

<div class="row">

    @if(Session::has('message'))

    <div class="col-md-12">

        <div class="alert alert-success">

            <button class="close" data-close="alert"></button>

            {{ Session::get('message') }}

        </div>

    </div>

    @endif

    <div id="sortable1" class="connectedSortable">

        @for ($i = 0; $i <= 12; $i++)

        @if(isset($dashboardOrder[$i]) && $dashboardOrder[$i] == 1)

        @if(isset($dashboardWidgetSettings->widget_webhits) && $dashboardWidgetSettings->widget_webhits->widget_display=="Y")

        <div class="col-md-6 col-sm-6 ui-state-default ui-sortable-handle" data-id="1">

            <!-- BEGIN PORTLET-->

            <div class="portlet light">

                <div class="portlet-title dash-title">

                    <div class="caption">

                        <i class="icon-share font-green_drark hide"></i>

                        <span class="caption-subject font-green_drark bold uppercase" title="Website Hits">Website Hits</span>

                    </div>

                    <div class="caption_right pull-right">

                        <div class="caption_filter" style="width:150px">

                            <select class="bs-select select2" id="pageHitsChartFilter" placeholder="filter">

                                <option value="">Filter</option>

                                <option value="3" data-timeparam="year">Last Four Years</option>

                                <option value="2" data-timeparam="month">Current Year</option>

                                <option value="1" data-timeparam="year">Last Two Year</option>

                                <option value="0" data-timeparam="month">Current Month</option>

                                <option value="1" data-timeparam="month">Last Two Month</option>

                            </select>

                        </div>

                    </div>

                </div>

                <div class="portlet-body dash-table">

                    <div id="columnchart_material" style="width: 100%; height: 350px;"></div>

                </div>

            </div>

            <!-- END PORTLET-->

        </div>

        @endif

        @endif

        @if ($isAdmin)

        @if(isset($dashboardOrder[$i]) && $dashboardOrder[$i] == 3)

        @if(isset($dashboardWidgetSettings->widget_leadstatistics) && $dashboardWidgetSettings->widget_leadstatistics->widget_display=="Y")

        <div class="col-md-6 col-sm-6 ui-state-default ui-sortable-handle" data-id="3">

            <!-- BEGIN PORTLET-->

            <div class="portlet light">

                <div class="portlet-title dash-title">

                    <div class="caption">

                        <i class="icon-share font-green_drark hide"></i>

                        <span class="caption-subject font-green_drark bold uppercase" title="Leads Statistics">Leads Statistics</span>

                    </div>

                    <div class="caption_right pull-right">

                        <div class="caption_filter" style="width:150px">

                            <select class="bs-select select2" id="LeadFilter" placeholder="filter">

                                <option value="">Filter</option>

                                <option value="4" data-timeparam="year">Last Four Years</option>

                                <option value="0" data-timeparam="month">Current Year</option>

                                <option value="2" data-timeparam="year">Last Two Year</option>

                                <option value="1" data-timeparam="month">Current Month</option>

                                <option value="2" data-timeparam="month">Last Two Month</option>

                            </select>

                        </div>

                    </div>

                </div>

                <div class="portlet-body dash-table">

                    <div id="curve_chart" style="width: 100%; height: 350px"></div>

                </div>

            </div>

            <!-- END PORTLET-->

        </div>

        @endif

        @endif

        @endif

        @if(isset($dashboardOrder[$i]) && $dashboardOrder[$i] == 4)

        @if(isset($dashboardWidgetSettings->widget_download) && $dashboardWidgetSettings->widget_download->widget_display=="Y")

        <div class="col-md-6 col-sm-6 ui-state-default ui-sortable-handle" data-id="4">

            <!-- BEGIN PORTLET-->

            <div class="portlet light">

                <div class="portlet-title dash-title">

                    <div class="caption">

                        <i class="icon-share font-green_drark hide"></i>

                        <span class="caption-subject font-green_drark bold uppercase" title="Document Views & Downloads">Document Views & Downloads</span>

                    </div>

                    <div class="caption_right pull-right">

                        <div class="caption_filter" style="width:150px">

                            <select  class="bs-select select2" id="docChartFilter" placeholder="filter">

                                <option value="">Filter</option>

                                <option value="4" data-timeparam="year">Last Four Years</option>

                                <option value="0" data-timeparam="month">Current Year</option>

                                <option value="2" data-timeparam="year">Last Two Year</option>

                                <option value="1" data-timeparam="month">Current Month</option>

                                <option value="2" data-timeparam="month">Last Two Month</option>

                            </select>

                        </div>

                    </div>

                </div>

                <div class="portlet-body dash-table">

                    <div id="doc-chart" style="width: 100%; height: 350px;"></div>

                </div>

            </div>

            <!-- END PORTLET-->

        </div>

        @endif

        @endif

        @if ($isAdmin)

        @if(isset($dashboardOrder[$i]) && $dashboardOrder[$i] == 5)

        @can('feedback-leads-list')

        @if(isset($dashboardWidgetSettings->widget_feedbackleads) && $dashboardWidgetSettings->widget_feedbackleads->widget_display=="Y")

        <div class="col-md-6 col-sm-6 ui-state-default ui-sortable-handle" data-id="5">

            <!-- BEGIN PORTLET-->

            <div class="portlet light">

                <div class="portlet-title dash-title">

                    <div class="caption">

                        <i class="icon-share font-green_drark hide"></i>

                        <span class="caption-subject font-green_drark bold uppercase"

                              title="Feedback Leads">Feedback Leads</span>

                    </div>

                </div>

                <div class="portlet-body dash-table">

                    <div class="table-scrollable">

                        <table class="new_table_desing table table-condensed table-hover">

                            <thead>

                                <tr>

                                                                <!-- <th width="30%" align="left" title="Name"> Name </th> -->

                                    <th width="20%" align="left" title="{{  trans('shiledcmstheme::template.powerPanelDashboard.user') }}"> Name</th>

                                    <th width="20%" align="center" title="{{  trans('shiledcmstheme::template.powerPanelDashboard.email') }}">{{  trans('shiledcmstheme::template.powerPanelDashboard.email') }}</th>

                                    <th width="20%" align="center" title="{{  trans('shiledcmstheme::template.powerPanelDashboard.phone') }}">{{  trans('shiledcmstheme::template.powerPanelDashboard.phone') }}</th>

                                    <th width="30%" align="right" title="{{  trans('shiledcmstheme::template.powerPanelDashboard.receivedDateTime') }}"> {{  trans('shiledcmstheme::template.powerPanelDashboard.receivedDateTime') }}</th>

                                </tr>

                            </thead>

                            <tbody>

                                @if($feedBackleads->isEmpty())

                                <tr>

                                    <td align="center" colspan="4">No feedback leads found. </td>

                                </tr>

                                @else

                                @foreach ($feedBackleads as $key => $feedBackleads)

                                <tr>

                                    <td align="left">{{ $feedBackleads->varName }}</td>

                                    <td align="left">{{ App\Helpers\MyLibrary::getDecryptedString($feedBackleads->varEmail) }} </td>

                                    <td align="center">{{ App\Helpers\MyLibrary::getDecryptedString($feedBackleads->varPhoneNo) }}</td>

                                    <td align="right">{{ date(''.Config::get('Constant.DEFAULT_DATE_FORMAT').' '.Config::get('Constant.DEFAULT_TIME_FORMAT').'',strtotime($feedBackleads->created_at)) }}</td>

                                </tr>

                                @endforeach

                                @endif

                            </tbody>

                        </table>

                    </div>

                </div>

                @if(isset($feedBackleads) && !empty($feedBackleads))

                <div class="pull-right">

                    <a class="btn btn-green-drake" href="{{ url('powerpanel/feedback-leads') }}" title="{{  trans('shiledcmstheme::template.powerPanelDashboard.seeAllRecords') }}">{{  trans('shiledcmstheme::template.powerPanelDashboard.seeAllRecords') }}</a>

                </div>

                @endif

            </div>

            <!-- END PORTLET-->

        </div>

        @endif

        @endcan

        @endif

        @endif

        @if ($isAdmin)

        @if(isset($dashboardOrder[$i]) && $dashboardOrder[$i] == 6)

        @can('submit-tickets-list')

        @if(isset($dashboardWidgetSettings->widget_ticket) && $dashboardWidgetSettings->widget_ticket->widget_display=="Y")

        <div class="col-md-6 col-sm-6 ui-state-default ui-sortable-handle" data-id="6">

            <!-- BEGIN PORTLET-->

            <div class="portlet light">

                <div class="portlet-title dash-title">

                    <div class="caption">

                        <i class="icon-share font-green_drark hide"></i>

                        <span class="caption-subject font-green_drark bold uppercase"

                              title="Ticket List">Ticket List</span>

                    </div>

                </div>

                <div class="portlet-body dash-table">

                    <div class="table-scrollable">

                        <table class="new_table_desing table table-condensed table-hover">

                            <thead>

                                <tr>

                                    <th width="30%" align="left" title="{{  trans('shiledcmstheme::template.powerPanelDashboard.user') }}"> Name</th>

                                    <th width="20%" align="left" title="Type">Type</th>

                                    <th width="20%" align="center" title="Message">Message</th>

                                    <th width="30%" align="right" title="{{  trans('shiledcmstheme::template.powerPanelDashboard.receivedDateTime') }}"> {{  trans('shiledcmstheme::template.powerPanelDashboard.receivedDateTime') }}</th>

                                </tr>

                            </thead>

                            <tbody>

                                @if($submitTicketsleads->isEmpty())

                                <tr>

                                    <td align="center" colspan="4">No Tickets available</td>

                                </tr>

                                @else

                                @foreach ($submitTicketsleads as $key => $submitTicketsleads)

                                @php

                                if ($submitTicketsleads->intType == 1) {

                                $ticketType = 'Fixes / Issues';

                                } else if ($submitTicketsleads->intType == 2) {

                                $ticketType = 'Changes';

                                } else if ($submitTicketsleads->intType == 3) {

                                $ticketType = 'Suggestion';

                                } else if ($submitTicketsleads->intType == 4) {

                                $ticketType = 'New Features';

                                }

                                @endphp

                                <tr>

                                    <td align="left">{{ $submitTicketsleads->varTitle }} </td>

                                    <td align="left">{{ $ticketType }}</td>

                                    <td align="center">

                                        <div class="pro-act-btn">

                                            <a href="javascript:void(0)" onclick='return hs.htmlExpand(this, {width:300, headingText:"Message", wrapperClassName:"titlebar", showCredits:false, outlineType:false});'><span aria-hidden="true" class="icon-envelope"></span></a>

                                            <div class="highslide-maincontent">

                                                {{ $submitTicketsleads->txtShortDescription }}

                                            </div>

                                        </div>

                                    </td>

                                    <td align="right">{{ date(''.Config::get('Constant.DEFAULT_DATE_FORMAT').' '.Config::get('Constant.DEFAULT_TIME_FORMAT').'',strtotime($submitTicketsleads->created_at)) }}</td>

                                </tr>

                                @endforeach

                                @endif

                            </tbody>

                        </table>

                    </div>

                </div>

                @if(isset($submitTicketsleads) && !empty($submitTicketsleads))

                <div class="pull-right">

                    <a class="btn btn-green-drake" href="{{ url('powerpanel/submit-tickets') }}" title="{{  trans('shiledcmstheme::template.powerPanelDashboard.seeAllRecords') }}">{{  trans('shiledcmstheme::template.powerPanelDashboard.seeAllRecords') }}</a>

                </div>

                @endif

            </div>

            <!-- END PORTLET-->

        </div>

        @endif

        @endcan

        @endif

        @endif

        @if(isset($dashboardOrder[$i]) && $dashboardOrder[$i] == 7)

        @if($allowcomments)

        @if(isset($dashboardWidgetSettings->widget_commentuser) && $dashboardWidgetSettings->widget_commentuser->widget_display=="Y")

        <div class="col-md-6 col-sm-6 ui-state-default" data-id="7">

            <!-- BEGIN PORTLET-->

            <div class="portlet light">

                <div class="portlet-title dash-title">

                    <div class="caption">

                        <i class="icon-share font-green_drark hide"></i>

                        <span class="caption-subject font-green_drark bold uppercase" title="Comments For Approval">

                            Comments For Approval</span>

                    </div>

                </div>

                <div class="portlet-body dash-table">

                    <div class="table-scrollable mcscroll" style="height: 311px">

                        <table class="table table-condensed table-hover">

                            <thead>

                                <tr>

                                    <th width="30%" align="left" title="{{  trans('shiledcmstheme::template.common.module') }}"> {{  trans('shiledcmstheme::template.common.module') }} </th>

                                    <th width="10%" align="center" title="{{  trans('shiledcmstheme::template.common.title') }}"> {{  trans('shiledcmstheme::template.common.title') }} </th>

                                    <th width="40%" align="center" title="View Comments & Replay"> View Comments & Replay</th>

                                </tr>

                            </thead>

                            <tbody>

                                {!! $allowcomments !!}

                            </tbody>

                        </table>

                    </div>

                </div>

            </div>

            <!-- END PORTLET-->

        </div>

        @endif

        @endif

        @endif

        @if ($isAdmin)

        @if(isset($dashboardOrder[$i]) && $dashboardOrder[$i] == 8)

        @can('contact-us-list')

        @if(isset($dashboardWidgetSettings->widget_conatctleads) && $dashboardWidgetSettings->widget_conatctleads->widget_display=="Y")

        <div class="col-md-6 col-sm-6 ui-state-default" data-id="8">

            <!-- BEGIN PORTLET-->

            <div class="portlet light">

                <div class="portlet-title dash-title">

                    <div class="caption">

                        <i class="icon-share font-green_drark hide"></i>

                        <span class="caption-subject font-green_drark bold uppercase" title="{{  trans('shiledcmstheme::template.sidebar.contactuslead') }}">

                            {{  trans('shiledcmstheme::template.sidebar.contactuslead') }}</span>

                    </div>

                </div>

                <div class="portlet-body dash-table">

                    <div class="table-scrollable">

                        <table class="table table-condensed table-hover">

                            <thead>

                                <tr>

                                    <th width="30%" align="left" title="{{  trans('shiledcmstheme::template.common.name') }}"> {{  trans('shiledcmstheme::template.common.name') }} </th>

                                    <th width="30%" align="center" title="{{  trans('shiledcmstheme::template.common.interestedIn') }}"> {{  trans('shiledcmstheme::template.common.interestedIn') }} </th>

                                    <th width="10%" align="center" title="{{  trans('shiledcmstheme::template.common.details') }}"> {{  trans('shiledcmstheme::template.common.details') }} </th>

                                    <th width="40%" align="right" title="{{  trans('shiledcmstheme::template.powerPanelDashboard.receivedDateTime') }}"> {{  trans('shiledcmstheme::template.powerPanelDashboard.receivedDateTime') }}</th>

                                </tr>

                            </thead>

                            <tbody>

                                @if($leads->isEmpty())

                                <tr>

                                    <td align="center" colspan="4">{{  trans('shiledcmstheme::template.powerPanelDashboard.noContactLead') }} <a target="_blank" href="https://www.netclues.ky/it-services/digital-marketing/social-media-marketing-cayman-islands"> {{  trans('shiledcmstheme::template.powerPanelDashboard.here') }}</a> {{  trans('shiledcmstheme::template.powerPanelDashboard.findContactLead') }} </td>

                                </tr>

                                @else

                                @foreach ($leads as $key=>$lead)

                                @if($key<=4)

                                <tr>

                                    <td>{!! $lead->varName !!}</td>

                                    @php $service = ''; @endphp

                                    @if(isset($lead->fkIntServiceId) && $lead->fkIntServiceId == 0)

                                        @php $service .= "General Enquiry"; @endphp

                                    @else

                                        @if (!empty($lead->fkIntServiceId))

                                            @php $selService = \Powerpanel\Services\Models\Services::getServiceNameById($lead->fkIntServiceId); @endphp

                                            @php $service .= $selService['varTitle']; @endphp

                                            @php $class = ""; @endphp

                                        @else

                                            @php $service .= '-'; @endphp

                                            @php $class = "numeric text-center"; @endphp

                                        @endif

                                    @endif

                                    <td align="left" class="numeric text-center">{{  $service   }}</td>

                                    <td align="left" class='numeric text-center'>

                                        <a data-toggle='modal' class="contactUsLead" id="{!! $lead->id !!}" href='#DetailsLeads{!! $lead->id !!}' title="{{  trans('shiledcmstheme::template.powerPanelDashboard.clickDetails') }}">

                                            <span class='icon-magnifier-add' aria-hidden='true'></span>

                                        </a>

                                    </td>

                                    <td align="right">{{ date(''.Config::get('Constant.DEFAULT_DATE_FORMAT').'  '.Config::get('Constant.DEFAULT_TIME_FORMAT').'', strtotime($lead->created_at)) }}</td>

                                </tr>

                                @endif

                                @endforeach

                                @endif

                            </tbody>

                        </table>

                    </div>

                </div>

                @if(isset($leads) && !empty($leads) && count($leads) > 0 )

                <div class="pull-right">

                    <a class="btn btn-green-drake" href="{{ url('powerpanel/contact-us') }}" title="{{  trans('shiledcmstheme::template.powerPanelDashboard.seeAllRecords') }}">{{  trans('shiledcmstheme::template.powerPanelDashboard.seeAllRecords') }}</a>

                </div>

                @endif

            </div>

            <!-- END PORTLET-->

        </div>

        @endif

        @endcan

        @endif

        @endif



        @if ($isAdmin)

        @if(isset($dashboardOrder[$i]) && $dashboardOrder[$i] == 9)

        @can('get-a-estimate-list')

        @if(isset($dashboardWidgetSettings->widget_getaestimateleads) && $dashboardWidgetSettings->widget_getaestimateleads->widget_display=="Y")

        <div class="col-md-6 col-sm-6 ui-state-default" data-id="9">

            <!-- BEGIN PORTLET-->

            <div class="portlet light">

                <div class="portlet-title dash-title">

                    <div class="caption">

                        <i class="icon-share font-green_drark hide"></i>

                        <span class="caption-subject font-green_drark bold uppercase" title="{{  trans('shiledcmstheme::template.sidebar.getaestimatelead') }}">

                            {{  trans('shiledcmstheme::template.sidebar.getaestimatelead') }}</span>

                    </div>

                </div>

                <div class="portlet-body dash-table">

                    <div class="table-scrollable">

                        <table class="table table-condensed table-hover">

                            <thead>

                                <tr>

                                    <th width="30%" align="left" title="{{  trans('shiledcmstheme::template.common.name') }}"> {{  trans('shiledcmstheme::template.common.name') }} </th>

                                    <th width="20%" align="center" title="{{  trans('shiledcmstheme::template.common.interestedIn') }}"> {{  trans('shiledcmstheme::template.common.interestedIn') }} </th>

                                    <th width="10%" align="center" title="{{  trans('shiledcmstheme::template.common.details') }}"> {{  trans('shiledcmstheme::template.common.details') }} </th>

                                    <th width="40%" align="right" title="{{  trans('shiledcmstheme::template.powerPanelDashboard.receivedDateTime') }}"> {{  trans('shiledcmstheme::template.powerPanelDashboard.receivedDateTime') }}</th>

                                </tr>

                            </thead>

                            <tbody>

                                @if($getaestimateleads->isEmpty())

                                <tr>

                                    <td align="center" colspan="4">{{  trans('shiledcmstheme::template.powerPanelDashboard.noGetaesimateLead') }} <a target="_blank" href="https://www.netclues.ky/it-services/digital-marketing/social-media-marketing-cayman-islands"> {{  trans('shiledcmstheme::template.powerPanelDashboard.here') }}</a> {{  trans('shiledcmstheme::template.powerPanelDashboard.findGetaestimateLead') }} </td>

                                </tr>

                                @else

                                @foreach ($getaestimateleads as $key=>$lead)

                                @if($key<=4)

                                <tr>

                                    <td>{!! $lead->varName !!}</td>

                                    @if (!empty($lead->fkIntServiceId))

                                        @php $service = ''; @endphp

                                        @php $selService = \Powerpanel\Services\Models\Services::getServiceNameById($lead->fkIntServiceId); @endphp

                                        @php $service .= $selService['varTitle']; @endphp

                                    @endif

                                    <td align="left" class='numeric text-center'>{{ (!empty($lead->fkIntServiceId)? $service:'-')  }}</td>

                                    <td align="left" class='numeric text-center'>

                                        <a data-toggle='modal' class="getaEstimateLead" id="{!! $lead->id !!}" href='#DetailsLeads{!! $lead->id !!}' title="{{  trans('shiledcmstheme::template.powerPanelDashboard.clickDetails') }}">

                                            <span class='icon-magnifier-add' aria-hidden='true'></span>

                                        </a>

                                    </td>

                                    <td align="right">{{ date(''.Config::get('Constant.DEFAULT_DATE_FORMAT').'  '.Config::get('Constant.DEFAULT_TIME_FORMAT').'', strtotime($lead->created_at)) }}</td>

                                </tr>

                                @endif

                                @endforeach

                                @endif

                            </tbody>

                        </table>

                    </div>

                </div>

                @if(isset($getaestimateleads) && !empty($getaestimateleads) && count($getaestimateleads) > 0 )

                <div class="pull-right">

                    <a class="btn btn-green-drake" href="{{ url('powerpanel/get-a-estimate') }}" title="{{  trans('shiledcmstheme::template.powerPanelDashboard.seeAllRecords') }}">{{  trans('shiledcmstheme::template.powerPanelDashboard.seeAllRecords') }}</a>

                </div>

                @endif

            </div>

            <!-- END PORTLET-->

        </div>

        @endif

        @endcan

        @endif

        @endif



        @if ($isAdmin)

        @if(isset($dashboardOrder[$i]) && $dashboardOrder[$i] == 11)

        @can('newsletter-lead-list')

        @if(isset($dashboardWidgetSettings->widget_newsletterleads) && $dashboardWidgetSettings->widget_newsletterleads->widget_display=="Y")

        <div class="col-md-6 col-sm-6 ui-state-default" data-id="11">

            <!-- BEGIN PORTLET-->

            <div class="portlet light">

                <div class="portlet-title dash-title">

                    <div class="caption">

                        <i class="icon-share font-green_drark hide"></i>

                        <span class="caption-subject font-green_drark bold uppercase" title="Newsletter Lead">

                            {{  trans('Newsletter Lead') }}</span>

                    </div>

                </div>

                <div class="portlet-body dash-table">

                    <div class="table-scrollable">

                        <table class="table table-condensed table-hover">

                            <thead>

                                <tr>

                                    <th width="30%" align="left" title="{{  trans('shiledcmstheme::template.common.email') }}"> {{  trans('shiledcmstheme::template.common.email') }} </th>

                                    <th width="10%" align="center" title="{{  trans('Subscribed') }}"> {{  trans('Subscribed') }} </th>

                                    <th width="40%" align="right" title="{{  trans('shiledcmstheme::template.powerPanelDashboard.receivedDateTime') }}"> {{  trans('shiledcmstheme::template.powerPanelDashboard.receivedDateTime') }}</th>

                                </tr>

                            </thead>

                            <tbody>

                                @if($newsletterleads->isEmpty())

                                <tr>

                                    <td align="center" colspan="3">{{  trans('shiledcmstheme::template.powerPanelDashboard.noNewsLetter') }} <a target="_blank" href="https://www.netclues.ky/it-services/digital-marketing/social-media-marketing-cayman-islands"> {{  trans('shiledcmstheme::template.powerPanelDashboard.here') }}</a> {{  trans('shiledcmstheme::template.powerPanelDashboard.findNewsletterLead') }} </td>

                                </tr>

                                @else

                                @foreach ($newsletterleads as $key=>$newsletterlead)

                                @if($key<=4)

                                <tr>

                                    <td align="left">{!! App\Helpers\MyLibrary::getDecryptedString($newsletterlead->varEmail); !!}</td>

                                    <td align="center">{{ $newsletterlead->chrSubscribed }}</td>

                                    <td align="right">{{ date(''.Config::get('Constant.DEFAULT_DATE_FORMAT').'  '.Config::get('Constant.DEFAULT_TIME_FORMAT').'', strtotime($newsletterlead->created_at)) }}</td>

                                </tr>

                                @endif

                                @endforeach

                                @endif

                            </tbody>

                        </table>

                    </div>

                </div>

                @if(isset($newsletterleads) && !empty($newsletterleads) && count($newsletterleads) >  0)

                <div class="pull-right">

                    <a class="btn btn-green-drake" href="{{ url('powerpanel/newsletter-lead') }}" title="{{  trans('shiledcmstheme::template.powerPanelDashboard.seeAllRecords') }}">{{  trans('shiledcmstheme::template.powerPanelDashboard.seeAllRecords') }}</a>

                </div>

                @endif

            </div>

            <!-- END PORTLET-->

        </div>

        @endif

        @endcan

        @endif

        @endif

         

        @if ($isAdmin)

@if(isset($dashboardOrder[$i]) && $dashboardOrder[$i] == 12)

@can('service-inquiry-list')



@if(isset($dashboardWidgetSettings->widget_serviceinquiryleads) && $dashboardWidgetSettings->widget_serviceinquiryleads->widget_display=="Y")

<div class="col-md-6 col-sm-6 ui-state-default" data-id="12">

    <!-- BEGIN PORTLET-->

    <div class="portlet light">

        <div class="portlet-title dash-title">

            <div class="caption">

                <i class="icon-share font-green_drark hide"></i>

                <span class="caption-subject font-green_drark bold uppercase" title="{{  trans('shiledcmstheme::template.sidebar.serviceinquirylead') }}">

                    {{  trans('shiledcmstheme::template.sidebar.serviceinquirylead') }}</span>

            </div>

        </div>

        <div class="portlet-body dash-table">

            <div class="table-scrollable">

                <table class="table table-condensed table-hover">

                    <thead>

                        <tr>

                            <th width="30%" align="left" title="{{  trans('shiledcmstheme::template.common.name') }}"> {{  trans('shiledcmstheme::template.common.name') }} </th>

                            <th width="30%" align="center" title="{{  trans('shiledcmstheme::template.common.interestedIn') }}"> {{  trans('shiledcmstheme::template.common.interestedIn') }} </th>

                            <th width="10%" align="center" title="{{  trans('shiledcmstheme::template.common.details') }}"> {{  trans('shiledcmstheme::template.common.details') }} </th>

                            <th width="40%" align="right" title="{{  trans('shiledcmstheme::template.powerPanelDashboard.receivedDateTime') }}"> {{  trans('shiledcmstheme::template.powerPanelDashboard.receivedDateTime') }}</th>

                        </tr>

                    </thead>

                    <tbody>

                        @if($serviceinquiryleads->isEmpty())

                        <tr>

                            <td align="center" colspan="4">{{  trans('shiledcmstheme::template.powerPanelDashboard.noserviceinquirylead') }} <a target="_blank" href="https://www.netclues.ky/it-services/digital-marketing/social-media-marketing-cayman-islands"> {{  trans('shiledcmstheme::template.powerPanelDashboard.here') }}</a> {{  trans('shiledcmstheme::template.powerPanelDashboard.findserviceinquirylead') }} </td>

                        </tr>

                        @else

                        @foreach ($serviceinquiryleads as $key=>$lead)

                        @if($key<=4)

                        <tr>

                            <td>{!! $lead->varName !!}</td>

                            @php $service = ''; @endphp

                            @if(isset($lead->fkIntServiceId) && $lead->fkIntServiceId == 0)

                                @php $service .= "General Enquiry"; @endphp

                            @else

                                @if (!empty($lead->fkIntServiceId))

                                    @php $selService = \Powerpanel\Services\Models\Services::getServiceNameById($lead->fkIntServiceId); @endphp

                                    @php $service .= $selService['varTitle']; @endphp

                                    @php $class = ""; @endphp

                                @else

                                    @php $service .= '-'; @endphp

                                    @php $class = "numeric text-center"; @endphp

                                @endif

                            @endif

                            <td align="left" class="numeric text-center">{{  $service   }}</td>

                            <td align="left" class='numeric text-center'>

                                <a data-toggle='modal' class="serviceInquiryLead" id="{!! $lead->id !!}" href='#DetailsLeads{!! $lead->id !!}' title="{{  trans('shiledcmstheme::template.powerPanelDashboard.clickDetails') }}">

                                    <span class='icon-magnifier-add' aria-hidden='true'></span>

                                </a>

                            </td>

                            <td align="right">{{ date(''.Config::get('Constant.DEFAULT_DATE_FORMAT').'  '.Config::get('Constant.DEFAULT_TIME_FORMAT').'', strtotime($lead->created_at)) }}</td>

                        </tr>

                        @endif

                        @endforeach

                        @endif

                    </tbody>

                </table>

            </div>

        </div>

        @if(isset($serviceinquiryleads) && !empty($serviceinquiryleads) && count($serviceinquiryleads) > 0 )

        <div class="pull-right">

            <a class="btn btn-green-drake" href="{{ url('powerpanel/service-inquiry') }}" title="{{  trans('shiledcmstheme::template.powerPanelDashboard.seeAllRecords') }}">{{  trans('shiledcmstheme::template.powerPanelDashboard.seeAllRecords') }}</a>

        </div>

        @endif

    </div>

    <!-- END PORTLET-->

</div>

@endif

@endcan

@endif

@endif



@if ($isAdmin)

            @if(isset($dashboardOrder[$i]) && $dashboardOrder[$i] == 13)

            @can('boat-inquiry-list')

            @if(isset($dashboardWidgetSettings->widget_boatinquiryleads) && $dashboardWidgetSettings->widget_boatinquiryleads->widget_display=="Y")

            <div class="col-md-6 col-sm-6 ui-state-default" data-id="13">

                <!-- BEGIN PORTLET-->

                <div class="portlet light">

                    <div class="portlet-title dash-title">

                        <div class="caption">

                            <i class="icon-share font-green_drark hide"></i>

                            <span class="caption-subject font-green_drark bold uppercase" title="{{  trans('shiledcmstheme::template.sidebar.boatinquirylead') }}">

                                {{ trans('shiledcmstheme::template.sidebar.boatinquirylead') }}</span>

                        </div>

                    </div>

                    <div class="portlet-body dash-table">

                        <div class="table-scrollable">

                            <table class="table table-condensed table-hover">

                                <thead>

                                    <tr>

                                        <th width="30%" align="left" title="{{  trans('shiledcmstheme::template.common.name') }}"> {{ trans('shiledcmstheme::template.common.name') }} </th>

                                        <th width="30%" align="center" title="{{  trans('shiledcmstheme::template.common.interestedIn') }}"> {{ trans('shiledcmstheme::template.common.interestedIn') }} </th>

                                        <th width="10%" align="center" title="{{  trans('shiledcmstheme::template.common.details') }}"> {{ trans('shiledcmstheme::template.common.details') }} </th>

                                        <th width="40%" align="right" title="{{  trans('shiledcmstheme::template.powerPanelDashboard.receivedDateTime') }}"> {{ trans('shiledcmstheme::template.powerPanelDashboard.receivedDateTime') }}</th>

                                    </tr>

                                </thead>

                                <tbody>

                                    @if($boatinquiryleads->isEmpty())

                                    <tr>

                                        <td align="center" colspan="4">{{ trans('shiledcmstheme::template.powerPanelDashboard.noboatinquirylead') }} <a target="_blank" href="https://www.netclues.ky/it-services/digital-marketing/social-media-marketing-cayman-islands"> {{ trans('shiledcmstheme::template.powerPanelDashboard.here') }}</a> {{ trans('shiledcmstheme::template.powerPanelDashboard.findboatinquirylead') }} </td>

                                    </tr>

                                    @else

                                    @foreach ($boatinquiryleads as $key=>$lead)

                                    @if($key<=4) <tr>

                                        <td>{!! $lead->varName !!}</td>

                                        @php $boat = ''; @endphp

                                        @if(isset($lead->fkIntBoatId) && $lead->fkIntBoatId == 0)

                                        @php $boat .= "General Enquiry"; @endphp

                                        @else

                                        @if (!empty($lead->fkIntBoatId))

                                        @php $selBoat = \Powerpanel\Boat\Models\Boat::getBoatNameById($lead->fkIntBoatId); @endphp

                                        @php $boat .= $selBoat['varTitle']; @endphp

                                        @php $class = ""; @endphp

                                        @else

                                        @php $boat .= '-'; @endphp

                                        @php $class = "numeric text-center"; @endphp

                                        @endif

                                        @endif

                                        <td align="left" class="numeric text-center">{{ $boat   }}</td>

                                        <td align="left" class='numeric text-center'>

                                            <a data-toggle='modal' class="boatInquiryLead" id="{!! $lead->id !!}" href='#DetailsLeads{!! $lead->id !!}' title="{{  trans('shiledcmstheme::template.powerPanelDashboard.clickDetails') }}">

                                                <span class='icon-magnifier-add' aria-hidden='true'></span>

                                            </a>

                                        </td>

                                        <td align="right">{{ date(''.Config::get('Constant.DEFAULT_DATE_FORMAT').'  '.Config::get('Constant.DEFAULT_TIME_FORMAT').'', strtotime($lead->created_at)) }}</td>

                                        </tr>

                                        @endif

                                        @endforeach

                                        @endif

                                </tbody>

                            </table>

                        </div>

                    </div>

                    @if(isset($boatinquiryleads) && !empty($boatinquiryleads) && count($boatinquiryleads) > 0 )

                    <div class="pull-right">

                        <a class="btn btn-green-drake" href="{{ url('powerpanel/boat-inquiry') }}" title="{{  trans('shiledcmstheme::template.powerPanelDashboard.seeAllRecords') }}">{{ trans('shiledcmstheme::template.powerPanelDashboard.seeAllRecords') }}</a>

                    </div>

                    @endif

                </div>

                <!-- END PORTLET-->

            </div>

            @endif

            @endcan

            @endif

            @endif



            @if ($isAdmin)

        @if(isset($dashboardOrder[$i]) && $dashboardOrder[$i] == 14)

        @can('data-removal-lead-list')

        @if(isset($dashboardWidgetSettings->widget_dataremovalleads) && $dashboardWidgetSettings->widget_dataremovalleads->widget_display=="Y")

        <div class="col-md-6 col-sm-6 ui-state-default" data-id="14">

            <!-- BEGIN PORTLET-->

            <div class="portlet light">

                <div class="portlet-title dash-title">

                    <div class="caption">

                        <i class="icon-share font-green_drark hide"></i>

                        <span class="caption-subject font-green_drark bold uppercase" title="Data Removal Leads">

                            {{  trans('shiledcmstheme::template.sidebar.dataremovallead') }}</span>

                    </div>

                </div>

                <div class="portlet-body dash-table">

                    <div class="table-scrollable">

                        <table class="table table-condensed table-hover">

                            <thead>

                                <tr>

                                    <th width="30%" align="left" title="{{  trans('shiledcmstheme::template.common.name') }}"> {{  trans('shiledcmstheme::template.common.name') }} </th>

                                    <th width="30%" align="left" title="{{  trans('shiledcmstheme::template.common.emailaddress') }}"> {{  trans('shiledcmstheme::template.common.emailaddress') }} </th>

                                    <th width="10%" align="center" title="{{  trans('shiledcmstheme::template.common.details') }}"> {{  trans('shiledcmstheme::template.common.details') }} </th>

                                    <th width="40%" align="right" title="{{  trans('shiledcmstheme::template.powerPanelDashboard.receivedDateTime') }}"> {{  trans('shiledcmstheme::template.powerPanelDashboard.receivedDateTime') }}</th>

                                </tr>

                            </thead>

                            <tbody>

                                @if($dataremovalleads->isEmpty())

                                <tr>

                                    <td align="center" colspan="5">{{  trans('shiledcmstheme::template.powerPanelDashboard.noDataremovalLead') }} <a target="_blank" href="https://www.netclues.ky/it-services/digital-marketing/social-media-marketing-cayman-islands"> {{  trans('shiledcmstheme::template.powerPanelDashboard.here') }}</a> {{  trans('shiledcmstheme::template.powerPanelDashboard.findDataremovalLead') }} </td>

                                </tr>

                                @else

                                @foreach ($dataremovalleads as $key=>$lead)

                                @if($key<=4)

                                <tr>

                                    <td>{!! $lead->varName !!}</td>

                                    <td>{!! App\Helpers\MyLibrary::getDecryptedString($lead->varEmail) !!}</td>

                                    <td align="left" class='numeric text-center'>

                                        <a data-toggle='modal' class="dataRemovalLead" id="{!! $lead->id !!}" href='#DetailsLeads{!! $lead->id !!}' title="{{  trans('shiledcmstheme::template.powerPanelDashboard.clickDetails') }}">

                                            <span class='icon-magnifier-add' aria-hidden='true'></span>

                                        </a>

                                    </td>

                                    <td align="right">{{ date(''.Config::get('Constant.DEFAULT_DATE_FORMAT').'  '.Config::get('Constant.DEFAULT_TIME_FORMAT').'', strtotime($lead->created_at)) }}</td>

                                </tr>

                                @endif

                                @endforeach

                                @endif

                            </tbody>

                        </table>

                    </div>

                </div>

                @if(isset($dataremovalleads) && !empty($dataremovalleads) && count($dataremovalleads) > 0 )

                <div class="pull-right">

                    <a class="btn btn-green-drake" href="{{ url('powerpanel/data-removal-lead') }}" title="{{  trans('shiledcmstheme::template.powerPanelDashboard.seeAllRecords') }}">{{  trans('shiledcmstheme::template.powerPanelDashboard.seeAllRecords') }}</a>

                </div>

                @endif

            </div>

            <!-- END PORTLET-->

        </div>

        @endif

        @endcan

        @endif

        @endif





        {{--

        @if($isAdmin)

        @if(isset($dashboardOrder[$i]) && $dashboardOrder[$i] == 10)

        @if(isset($dashboardWidgetSettings->widget_avl_workflow) && $dashboardWidgetSettings->widget_avl_workflow->widget_display=="Y")

        <div class="col-md-6 col-sm-6 ui-state-default ui-sortable-handle" data-id="10">

            <!-- BEGIN PORTLET-->

            <div class="portlet light">

                <div class="portlet-title dash-title">

                    <div class="caption">

                        <i class="icon-share font-green_drark hide"></i>

                        <span class="caption-subject font-green_drark bold uppercase" title="Available Workflows">

                            Available Workflows

                        </span>

                    </div>

                    <div class="dash-approve-search pull-right">

                        <span>{{  trans('shiledcmstheme::template.common.search') }}:</span>

        <input type="search" class="form-control form-control-solid placeholder-no-fix" id="searchfilter-available-wf">

    </div>

</div>

<div class="portlet-body dash-table">

    <div class="table-scrollable  mcscroll" style="height: 311px">

        <table class="table table-condensed table-hover" id="availablewf-for-roles">

            <thead>

                <tr>

                    <th width="25%" align="left" title="Module Name"> Module Name </th>

                    <th width="20%" align="left" title="User Role"> User Role </th>

                    <th width="1%" align="center" title="Admin"> Admin </th>

                    <th width="10%" align="left" title="Category"> Category </th>

                    <th width="1%" align="center" title="Module"> View </th>

                </tr>

            </thead>

            <tbody>

                @if(!$availableWorkFlows->isEmpty())

                @foreach ($availableWorkFlows as $key=>$wf)

                @php

                $add = $wf->chrNeedAddPermission == 'Y'?'<span class="badge badge-light">Add</span>':'';

            $update = $wf->charNeedApproval == 'Y'?'<span class="badge badge-light">Update</span>':'';

            $admins = array_column($wf->adminusers, 'name');

            $admins = implode("<br/>", $admins);

            if($admins==null && $wf->chrNeedAddPermission=='N' && $wf->charNeedApproval=='N'){

            $add = '<span class="badge badge-light">Add</span>';

            $update = '<span class="badge badge-light">Update</span>';

            $admins = "Direct Approved";

            $adminlist = $admins;

            }else{

            $adminlist ='';

            $adminlist .= '

            <div class="pro-act-btn">

                <a href="javascript:void(0)" onclick="return hs.htmlExpand(this, {width:300, headingText:\'Admin\',wrapperClassName:\'titlebar\',showCredits:false, outlineType:false});"><span aria-hidden="true" class="icon-info"></span></a>';

                $adminlist .= '

                <div class="highslide-maincontent">';

                    $adminlist .=$admins;

                    $adminlist .= '

                </div>

                ';

                $adminlist .= '

            </div>

            ';

            }

            @endphp

            <tr>

                <td align="left"><label title="{!! $wf->moduleTitle !!}">{!! $wf->moduleTitle !!}</label></td>

                <td align="left"><label title="{!! $wf->roles->display_name !!}">{!! $wf->roles->display_name !!}</label></td>

                <td align="center"><label title="{!!	$admins !!}">{!!  $adminlist !!}</label></td>

                <td align="left"><label title="{!! $wf->varActivity !!}">{!! $wf->varActivity !!}</label></td>

                <td align="center"><a href="{{ url('powerpanel/workflow/') }}" title="View"><i class="fa fa-link"></i></a></td>

            </tr>

            @endforeach

            @endif

            </tbody>

        </table>

    </div>

</div>

</div>

<!-- END PORTLET-->

</div>

@endif

@endif

@endif

@if($isAdmin)

@if(isset($dashboardOrder[$i]) && $dashboardOrder[$i] == 11)

@if(isset($dashboardWidgetSettings->widget_pending_workflow) && $dashboardWidgetSettings->widget_pending_workflow->widget_display=="Y")

<div class="col-md-6 col-sm-6 ui-state-default ui-sortable-handle" data-id="11">

    <!-- BEGIN PORTLET-->

    <div class="portlet light">

        <div class="portlet-title dash-title">

            <div class="caption">

                <i class="icon-share font-green_drark hide"></i>

                <span class="caption-subject font-green_drark bold uppercase" title="Pending Workflows">

                    Pending Workflows

                </span>

            </div>

            <div class="dash-approve-search pull-right">

                <span>{{  trans('shiledcmstheme::template.common.search') }}:</span>

                <input type="search" class="form-control form-control-solid placeholder-no-fix" id="searchfilter-pending-wf">

            </div>

        </div>

        <div class="portlet-body dash-table">

            <div class="table-scrollable mcscroll" style="height: 311px">

                <table class="table table-condensed table-hover" id="pendingwf-for-roles">

                    <thead>

                        <tr>

                            <th width="25%" align="left" title="Module Name"> Module Name </th>

                            <th width="20%" align="left" title="User Role"> User Role </th>

                            <th width="25%" align="left" title="Title"> Category </th>

                            <th width="1%" align="center" title="Module"> Add </th>

                        </tr>

                    </thead>

                    <tbody>

                        @if(!empty($pendingRoleWorkFlows))

                        @foreach ($pendingRoleWorkFlows as $key=>$role)

                        @foreach($role['category'] as $cat=>$actions)

                        <tr>

                            <td align="left"><label title="{!! $actions[0]['modulename'] !!}">{!! $actions[0]['modulename'] !!}</label></td>

                            <td align="left"><label title="{!! $key !!}">{!! $key !!}</label></td>

                            <td align="left"><label title="{!! $cat !!}">{!! $cat !!}</label></td>

                            <td align="center">

                                @if( isset($actions[0]['action']) && isset($actions[1]['action']) )

                                <a href="{{ url('powerpanel/workflow/add') }}" title="Add Workflow">

                                    <i class="fa fa-plus-circle"></i>

                                </a>

                                @elseif(isset($actions[0]['id']))

                                <a href="{{ url('powerpanel/workflow/') }}" title="Add to Workflow"><i class="fa fa-plus-circle"></i></a>

                                @endif

                            </td>

                        </tr>

                        @endforeach

                        @endforeach

                        @endif

                    </tbody>

                </table>

            </div>

        </div>

    </div>

    <!-- END PORTLET-->

</div>

@endif

@endif

@endif

--}}

@if(isset($dashboardOrder[$i]) && $dashboardOrder[$i] == 12)

<!--@if($allowactivity)-->

@if(isset($dashboardWidgetSettings->widget_recentactivity) && $dashboardWidgetSettings->widget_recentactivity->widget_display=="Y")

<div class="col-md-6 col-sm-6 ui-state-default" data-id="12">

    <!-- BEGIN PORTLET-->

    <div class="portlet light">

        <div class="portlet-title dash-title">

            <div class="caption">

                <i class="icon-share font-green_drark hide"></i>

                <span class="caption-subject font-green_drark bold uppercase" title="Recent Activity">

                    Recent Activity</span>

            </div>

        </div>

        <div class="portlet-body dash-table">

            <div class="table-scrollable mcscroll" style="height: 311px">

                <table class="table table-condensed table-hover">

                    <thead>

                        <tr>

                            <th width="10%" align="center" title="Module"> Module </th>

                            <th width="10%" align="center" title="Title"> Title</th>

                            <th width="20%" align="center" title="OLD Value"> OLD Value</th>

                            <th width="20%" align="center" title="New Value"> New Value</th>

                            <th width="20%" align="center" title="Action"> Action</th>

                        </tr>

                    </thead>

                    <tbody>

                        {!! $allowactivity !!}

                    </tbody>

                </table>

            </div>

        </div>

    </div>

    <!-- END PORTLET-->

</div>

@endif

<!--@endif-->

@endif

@endfor

</div>

<!-- END CONTENT BODY -->

<div class="new_modal modal fade detailsCmsPage" tabindex="-1" aria-hidden="true"></div>

<div class="new_modal modal fade detailsContactUsLead" tabindex="-1" aria-hidden="true"></div>

<div class="new_modal modal fade detailsGetaEstimateLead" tabindex="-1" aria-hidden="true"></div>

<div class="new_modal modal fade detailsServiceInquiryLead" tabindex="-1" aria-hidden="true"></div>

<div class="new_modal modal fade detailsBoatInquiryLead" tabindex="-1" aria-hidden="true"></div>

<div class="new_modal modal fade detailsDataremovalLead" tabindex="-1" aria-hidden="true"></div>

<div class="new_modal modal fade BlogDetails" tabindex="-1" aria-hidden="true"></div>

@include('powerpanel.partials.cmsPageCommentsUser')

<script>

                    function loadModelpopup(id, intRecordID, fkMainRecord, varModuleNameSpace, intCommentBy, varModuleTitle) {

                    $('#CmsPageComments1User').show();

                            $('#CmsPageComments1User').modal({

                    backdrop: 'static',

                            keyboard: false

                    });

                            document.getElementById('id').value = id;

                            document.getElementById('intRecordID').value = intRecordID;

                            document.getElementById('fkMainRecord').value = fkMainRecord;

                            document.getElementById('varModuleNameSpace').value = varModuleNameSpace;

                            document.getElementById('intCommentBy').value = intCommentBy;

                            document.getElementById('varModuleTitle').value = varModuleTitle;

                            document.getElementById('CmsPageComments_user').value = '';

                            $.ajax({

                            type: "POST",

                                    url: window.site_url + "/powerpanel/dashboard/Get_Comments_user",

                                    data: {'id': id, 'intRecordID': intRecordID, 'fkMainRecord': fkMainRecord, 'varModuleNameSpace': varModuleNameSpace, 'intCommentBy': intCommentBy, 'varModuleTitle': varModuleTitle},

                                    async: false,

                                    success: function (data)

                                    {

                                    document.getElementById('test').innerHTML = data;

                                    }

                            });

                    }

</script>

@endsection

@section('scripts')

<script type="text/javascript">

            window.site_url = '{!! url("/") !!}';

                    $(document).on("click", ".dashboard_checkbox", function(){

            var widgetkey = $(this).val();

                    if ($(this).prop('checked') == true){

            var widget_disp = 'Y';

            } else{

            var widget_disp = 'N';

            }

            $.ajax({

            type: "POST",

                    url: site_url + "/powerpanel/dashboard/updatedashboardsettings",

                    data: {'widgetkey':widgetkey, 'widget_disp':widget_disp},

                    async: false,

                    beforeSend:function(){

                    $('body').loader(loaderConfig);

                    },

                    success: function (data)

                    {

                    window.location.reload();

                    },

                    error: function (xhr, ajaxOptions, thrownError) {

                    $.loader.close(true);

                            alert("Error:" + thrownError);

                            window.location.reload();

                    }

            });

            });</script>

<script src="{{ $CDN_PATH.'resources/global/plugins/highslide/highslide-with-html.js' }}" type="text/javascript"></script>

<script src="{{ $CDN_PATH.'resources/pages/scripts/packages/dashboard/dashboard-ajax.js' }}" type="text/javascript"></script>

<script src="{{ $CDN_PATH.'resources/global/plugins/datatables/datatables.min.js' }}" type="text/javascript"></script>

<script src="{{ $CDN_PATH.'resources/global/scripts/jquery-ui.js' }}" type="text/javascript"></script>

<script>

                    $(function () {

                    $("#sortable1").sortable({

                    connectWith: ".connectedSortable",

                            update: function () {

                            dashBoardUpdate('.ui-sortable-handle');

                            }

                    }).disableSelection();

                    });

                    function dashBoardUpdate(row) {

                    var rows = $(row);

                            var order = [];

                            $.each(rows, function (index) {

                            order.push($(this).data('id'));

                            });

                            $.ajax({

                            type: "POST",

                                    url: window.site_url + "/powerpanel/dashboard/updateorder",

                                    data: {'order':JSON.stringify(order)},

                                    async: false,

                                    success: function (data)

                                    {

                                    }

                            }

                            );

                    }

</script>

<script type="text/javascript">

            @if (Session::has('alert-success'))

                    toastr.options = {

                    "closeButton": true,

                            "debug": false,

                            "positionClass": "toast-top-right",

                            "onclick": null,

                            "showDuration": "1000",

                            "hideDuration": "1000",

                            "timeOut": "5000",

                            "extendedTimeOut": "1000",

                            "showEasing": "swing",

                            "hideEasing": "linear",

                            "showMethod": "fadeIn",

                            "hideMethod": "fadeOut"

                    }

            toastr.success("{{Session::get('alert-success')}} Welcome to {{Config::get('Constant.SITE_NAME')}}.");

                    @endif

                    @if (Session::has('alert-success'))

                    $("#topMsg").show().delay(5000).fadeOut();

                    $("#topMsg").fadeOut("slow", function () {

            $('.page-header').css('top', '0');

                    $('.page-container').css('top', '0');

            });

                    @endif

                    $(document).on('click', '#close_icn', function (e) {

            $("#topMsg").hide();

                    $('.page-header').css('top', '0');

                    $('.page-container').css('top', '0');

            });

                    $(".mcscroll").mCustomScrollbar({

            axis: "yx",

                    theme: "minimal-dark"

            });

                    var dataTable = $('#approvals').DataTable({

            "paging": false,

                    "ordering": false,

                    "info": false,

                    "oLanguage": {

                    "sEmptyTable": "No Approvals are pending"

                    }

            });

                    $("#searchfilter").keyup(function () {

            dataTable.search(this.value).draw();

            });</script>

<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>

<script type="text/javascript">

                    pageHitsChart({!! $hits_web_mobile !!});

                    $('#pageHitsChartFilter').change(function(){

            var timeslab = $(this).find('option:selected').data('timeparam');

                    var year = $('#pageHitsChartFilter').val();

                    $.ajax({

                    type: "POST",

                            url: window.site_url + "/powerpanel/dashboard/mobilehist",

                            data: {year:year, timeparam:timeslab},

                            async: false,

                            dataType:'JSON',

                            success: function (data){

                            pageHitsChart(data);

                            }

                    });

            });

                    function pageHitsChart(json){

                    google.charts.load('current', {'packages': ['bar']});

                            google.charts.setOnLoadCallback(drawChart);

                            function drawChart() {

                            var data = google.visualization.arrayToDataTable(json);

                                    var options = {

                                    chart: {

                                    title: '',

                                            subtitle: '',

                                    },

                                            colors: ['#4285F4', '#FFB006'],

                                    };

                                    var chart = new google.charts.Bar(document.getElementById('columnchart_material'));

                                    chart.draw(data, google.charts.Bar.convertOptions(options));

                            }

                    }

</script>

<script type="text/javascript">

            searchChart({!! $searchChart !!});

                    $('#searchChartFilter').change(function(){

            var timeslab = $(this).find('option:selected').data('timeparam');

                    var year = $('#searchChartFilter').val();

                    $.ajax({

                    type: "POST",

                            url: window.site_url + "/powerpanel/dashboard/search-chart",

                            data: {year:year, timeparam:timeslab},

                            async: false,

                            dataType:'JSON',

                            success: function (data){

                            searchChart(data);

                            }

                    });

            });

                    function searchChart(json){

                    google.charts.load('current', {'packages': ['corechart']});

                            google.charts.setOnLoadCallback(drawChart);

                            function drawChart() {

                            var data = google.visualization.arrayToDataTable(json);

                                    var options = {

                                    title: ''

                                    };

                                    /*var chart = new google.visualization.PieChart(document.getElementById('piechart'));

                                     chart.draw(data, options);*/

                            }

                    }

</script>

<script type="text/javascript">

            docChart({!! $docChartData !!});

                    $('#docChartFilter').change(function(){

            var timeslab = $(this).find('option:selected').data('timeparam');

                    var year = $('#docChartFilter').val();

                    $.ajax({

                    type: "POST",

                            url: window.site_url + "/powerpanel/dashboard/doc-chart",

                            data: {year:year, timeparam:timeslab},

                            async: false,

                            dataType:'JSON',

                            success: function (data){

                            docChart(data);

                            }

                    });

            });

                    function docChart(json){

                    google.charts.load('current', {'packages': ['corechart']});

                            google.charts.setOnLoadCallback(drawChart);

                            function drawChart() {

                            var data = google.visualization.arrayToDataTable(json);

                                    var options = {

                                    title: '',

                                            hAxis: {title: json[0][0], titleTextStyle: {color: '#333'}},

                                            vAxis: {minValue: 0}

                                    };

                                    var chart = new google.visualization.AreaChart(document.getElementById('doc-chart'));

                                    chart.draw(data, options);

                            }

                    }

</script>

@if($isAdmin)

<script type="text/javascript">

            LeadChart({!! $leadsChart !!});

                    $('#LeadFilter').change(function(){

            var timeslab = $(this).find('option:selected').data('timeparam');

                    var year = $('#LeadFilter').val();

                    $.ajax({

                    type: "POST",

                            url: window.site_url + "/powerpanel/dashboard/LeadChart",

                            data: {year:year, timeparam:timeslab},

                            async: false,

                            dataType:'JSON',

                            success: function (data){

                            LeadChart(data);

                            }

                    });

            });

                    function LeadChart(json){

                    google.charts.load('current', {'packages':['line']});

                            google.charts.setOnLoadCallback(drawChart);

                            function drawChart() {

                            var data = google.visualization.arrayToDataTable(json);

                                    var options = {'color': 'black', 'fontName': '<global-font-name>', 'fontSize': '<global-font-size>'};

                                    var chart = new google.charts.Line(document.getElementById('curve_chart'));

                                    chart.draw(data, google.charts.Line.convertOptions(options));

                            }

                    }

</script>

@endif

<script type="text/javascript">

            var dataTableAvailableWf = $('#availablewf-for-roles').DataTable({

            "paging":   false,

                    "ordering": false,

                    "info":     false,

                    "oLanguage": {

                    "sEmptyTable": "No Workflow available."

                    }

            });

                    $("#searchfilter-available-wf").keyup(function() {

            dataTableAvailableWf.search(this.value).draw();

            });

                    var dataTablePendingWf = $('#pendingwf-for-roles').DataTable({

            "paging":   false,

                    "ordering": false,

                    "info":     false,

                    "oLanguage": {

                    "sEmptyTable": "No Workflow pending."

                    }

            });

                    $("#searchfilter-pending-wf").keyup(function() {

            dataTablePendingWf.search(this.value).draw();

            });

                    $(window).on('load', function(){

            $('#pageHitsChartFilter').select2({

            placeholder: "Filter",

                    width: '100%',

                    minimumResultsForSearch: - 1

            });

                    $('#LeadChart').select2({

            placeholder: "Filter",

                    width: '100%',

                    minimumResultsForSearch: - 1

            });

                    $('#docChartFilter').select2({

            placeholder: "Filter",

                    width: '100%',

                    minimumResultsForSearch: - 1

            });

                    $('#searchChartFilter').select2({

            placeholder: "Filter",

                    width: '100%',

                    minimumResultsForSearch: - 1

            });

                    $('#LeadFilter').select2({

            placeholder: "Filter",

                    width: '100%',

                    minimumResultsForSearch: - 1

            });

            });

</script>

@endsection