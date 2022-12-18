@extends('layouts.app')
@section('content')
@include('layouts.inner_banner')
<section>
	<div class="inner-page-container cms team_detail">
		<div class="container">
			<!-- Main Section S -->
			<div class="row">
				<!-- <div class="right_content">
					<div class="back_div">
						@php
						$start_date_time = '';
						if(isset($_REQUEST['start_date_time']) && strip_tags($_REQUEST['start_date_time']) != ''){
							$start_date_time = '&start_date_time='.strip_tags($_REQUEST['start_date_time']);
						}
						$end_date_time = '';
						if(isset($_REQUEST['end_date_time']) && strip_tags($_REQUEST['end_date_time']) != ''){
							$end_date_time = '&end_date_time='.strip_tags($_REQUEST['end_date_time']);
						}
						$page = '';
						if(isset($_REQUEST['page']) && strip_tags($_REQUEST['page']) != ''){
							$page = '&page='.strip_tags($_REQUEST['page']);
						}
						$catid = '';
						if(isset($_REQUEST['catid']) && intval($_REQUEST['catid']) != ''){
							$catid = '&catid='.intval($_REQUEST['catid']);
						}
						if(isset($_REQUEST['N']) && intval($_REQUEST['N']) == 'C'){
							$Nurl = '?N='.strip_tags($_REQUEST['N']);
						}else{
							$Nurl = '?N=N';
						}

						if($Nurl == "?N=N"){
							$backpageUrl = $moduleFrontWithCatUrl;
						}else{
							$backpageUrl = App\Helpers\MyLibrary::getFront_Uri('teams')['uri'];
						}

						@endphp
						<a href="{{ $backpageUrl }}" title="Back"><i class="fi flaticon-right"></i> Back</a>
					</div>
				</div> -->
				<div class="col-12 animated fadeInUp">
					<div class="cms">
						@if(isset($team->fkIntImgId) && $team->fkIntImgId != '')
							@php $itemImg = App\Helpers\resize_image::resize($team->fkIntImgId,500,600) @endphp
							<img class="lazy" data-src="{{ $itemImg }}" src="{{ $itemImg }}" alt="{{ $team->varTitle }}">
						@endif
						<h2 class="cms_detail_h2 ac-mb-xs-15">{{ $team->varTitle }}</h2>
						<ul class="contact-info">
						    @if(isset($team->varTagLine) && !empty($team->varTagLine))
                            <li><a><i class="fa fa-suitcase"></i> {{ $team->varTagLine }}</a></li>
                            @endif
							@if(isset($team->varPhoneNo) && !empty($team->varPhoneNo))
							<li><a href="tel:{{ $team->varPhoneNo }}" title="Call Us On : {{ $team->varPhoneNo }}"><i class="icon-phone"></i> {{ $team->varPhoneNo }}</a></li>
							@endif
							@if(isset($team->varEmail) && !empty($team->varEmail))
							<li><a href="mailto:{{ $team->varEmail }}" title="Email Us On : {{ $team->varEmail }}"><i class="icon-email"></i> {{ $team->varEmail }}</a></li>
							@endif
							
						</ul>
						@if(isset($team) && !empty($team->txtDescription))
							<!-- <h6>Description</h6> -->
							{!! $team->txtDescription !!}
						@else
						<!-- <h6>Short Description</h6> -->
							<p>{!! $team->txtShortDescription !!}</p>
						@endif

						@if(!empty($team->fkIntDocId))
						@php
						$docsAray = explode(',', $team->fkIntDocId);
						$docObj   = App\Document::getDocDataByIds($docsAray);
						@endphp
						@if(count($docObj) > 0)
						<div class="download_files clearfix">
							<h6>Download(s)</h6>
							<ul>
								@foreach($docObj as $key => $val)
								@php
								if($val->varDocumentExtension == 'pdf' || $val->varDocumentExtension == 'PDF'){
								$blank = 'target="_blank"';
								}else{
								$blank = '';
								}
								if($val->varDocumentExtension == 'pdf' || $val->varDocumentExtension == 'PDF'){
								$icon = "flaticon-pdf-file";
								}elseif($val->varDocumentExtension == 'doc' || $val->varDocumentExtension == 'docx'){
								$icon = "flaticon-doc-file";
								}elseif($val->varDocumentExtension == 'xls' || $val->varDocumentExtension == 'xlsx'){
								$icon = "flaticon-xls-file";
								}else{
								$icon = "flaticon-doc-file";
								}
								@endphp
								<li><a {!! $blank !!} href="{{ $CDN_PATH.'documents/'.$val->txtSrcDocumentName.'.'.$val->varDocumentExtension }}" data-viewid="{{ $val->id }}" data-viewtype="download" class="docHitClick" title="{{ $val->txtDocumentName }}" ><i class="fi {{ $icon }}"></i>{{ $val->txtDocumentName }}</a></li>
								@endforeach
							</ul>
						</div>
						@endif
						@endif
					</div>
				</div>
			</div>
		</div>
		<!-- Main Section E -->
	</div>
</section>
@if(!Request::ajax())
@section('footer_scripts')
@endsection
@endsection
@endif
