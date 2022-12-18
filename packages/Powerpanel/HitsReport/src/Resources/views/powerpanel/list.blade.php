@extends('powerpanel.layouts.app')
@section('title')
{{Config::get('Constant.SITE_NAME')}} - PowerPanel
@stop
@section('content')
<!-- @include('powerpanel.partials.breadcrumbs') -->
<div class="row">
    <div class="col-md-12">
        <!-- TITILE HEAD START -->
		<div class="title-dropdown_sec">
			@if (File::exists(base_path() . '/resources/views/powerpanel/partials/listbreadcrumbs.blade.php') != null)
			@include('powerpanel.partials.listbreadcrumbs',['ModuleName'=>'Manage Hits Report'])
			@endif
		</div>
        <div class="clearfix">
            <div class="portlet light">
                <div class="portlet-title dash-title hits_doc_page-title">
                    <div class="caption">
                        <i class="icon-share font-green_drark hide"></i>
                        <span class="caption-subject font-green_drark bold uppercase" title="Page Hits">Page Hits</span>
                    </div>
                    
                    <div class="caption_right pull-right">
                        <div class="add_category_button pull-right">
                            <a href="JavaScript:Void(0);" id="Send_Report_Email" title="Send Report" class="add_category"><span>Send Report</span> <i class="la la-envelope-o"></i></a>
                        </div>
                        <div class="caption_filter pull-left" >
                        <label class="yearfilter">Year: </label>
                            <span class="select_input">
                                <select class="bs-select select2" id="pageHitsChartFilter" placeholder="filter" style="width:100px;">
                                    @php 
                                    $currentYear = date('Y');
                                    $pastTenYears = $currentYear - 9;
                                    $futureTenYears = $currentYear; 
                                    $yearOptions = range($pastTenYears,$futureTenYears);
                                    arsort($yearOptions);
                                    $i = 0;
                                    @endphp
                                    @foreach($yearOptions as $year)
                                    <option value="{{ $year }}">{{ $year }}</option>
                                    @php
                                    $i++;
                                    @endphp
                                    @endforeach
                                </select>
                            </span>
                        </div>
                    </div>
                </div>
                <div class="portlet-body dash-table">
                    <div id="columnchart_material" style="width: 100%; height: 350px;"></div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="new_modal modal fade bs-modal-md" id="ReportModel" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-md">
        <div class="modal-vertical">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                    Send Report Email
                </div>
                <div class="modal-body replybody">
                    {!! Form::open(['method' => 'post','class'=>'HitsReportForm','id'=>'HitsReportForm']) !!}
                    {!! Form::hidden('chart_div','',array('id' => 'chart_div')) !!}
                    {!! Form::hidden('year','',array('id' => 'year')) !!}
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="to">Name: <span aria-required="true" class="required"> * </span></label>
                                {!! Form::text('Report_Name',  old('Report_Name') , array('id' => 'Report_Name', 'class' => 'form-control', 'placeholder'=>'Name')) !!}
                            </div>
                            <div class="form-group">
                                <label for="to">Email: <span aria-required="true" class="required"> * </span></label>
                                {!! Form::text('Report_email',  old('Report_email') , array('id' => 'Report_email', 'class' => 'form-control', 'placeholder'=>'Email')) !!}
                            </div>
                            <div class="success"></div>
                            <label class="error"></label>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <button type="submit" class="btn btn-green-drake" id="report_for_submit" value="saveandexit">Submit</button>
                            </div>
                        </div>
                    </div>
                    {!! Form::close() !!}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@section('scripts')
<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
<script type="text/javascript">
pageHitsChart({!! $hits_web_mobile !!});
        $('#pageHitsChartFilter').change(function () {
    var year = $('#pageHitsChartFilter').val();
    $.ajax({
        type: "POST",
        url: window.site_url + "/powerpanel/hits-report/mobilehist",
        data: {year: year},
        async: false,
        dataType: 'JSON',
        success: function (data) {
            pageHitsChart(data);
        }
    });
});

function pageHitsChart(json) {
    google.charts.load('current', {'packages': ['corechart']});
    google.charts.setOnLoadCallback(drawChart1);
    function drawChart1() {
        var data = google.visualization.arrayToDataTable(json);
        var options = {
            width: 1400,
            chartArea: {
                width: 1100,
            },
            colors: ['#4285F4', '#FFB006'],
        };
        var chart1 = new google.visualization.ColumnChart(document.getElementById('columnchart_material'));
        var chart_div = document.getElementById('chart_div');
        google.visualization.events.addListener(chart1, 'ready', function () {
            chart_div.value = chart1.getImageURI();
        });
        chart1.draw(data, options);
    }
    google.charts.load('current', {'packages': ['bar']});
    google.charts.setOnLoadCallback(drawChart);
    function drawChart() {
        var data = google.visualization.arrayToDataTable(json);
        var options = {
            colors: ['#4285F4', '#FFB006'],
        };
        var chart = new google.charts.Bar(document.getElementById('columnchart_material'));
        chart.draw(data, google.charts.Bar.convertOptions(options));
    }
}
</script>
<script>
    var Email_Send_Report_URL = '{!! url("/powerpanel/hits-report/sendreport") !!}';
</script>
<script src="{{ $CDN_PATH.'resources/pages/scripts/packages/hitsreport/hits-report-datatables-ajax.js' }}" type="text/javascript"></script>
@endsection