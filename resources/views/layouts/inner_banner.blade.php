@if(isset($inner_banner_data) && count($inner_banner_data) > 0)
@php
$segment = Request::segment(1);
@endphp
<section>
	<div class="inner-banner">
		<div class="container">
			<div id="inner-banner" class="carousel slide" data-ride="carousel">
				<div class="carousel-inner i-b__radisu">
					@foreach($inner_banner_data as $key=>$inner_banner)
					<div class="item @if($key==0) active @endif">
						<div class="i-b_fill" style="background-image:url('{!! App\Helpers\resize_image::resize($inner_banner->fkIntImgId,1920,312) !!}'); background-size: cover;">
							<div class="i-b_caption">
								<div class="container p-0">
									<div class="row">
										<div class="col-sm-12">
											<div class="i-n_c_title">
												@if($segment == 'site-map')
												<h1 class="banner_h2_div">{{ strtoupper('Site Map') }}</h1>
												@elseif($segment == 'blogs')
												<h1 class="banner_h2_div">{{ strtoupper('Blog') }}</h1>
												@else
												<h1 class="banner_h2_div">{{ isset($detailPageTitle) ?$detailPageTitle:strtoupper($currentPageTitle) }}</h1>
												@endif
											</div>
											<div class="row">
												<div class="col-md-12 col-sm-12 col-xs-12">
													@if(isset($breadcrumb) && count($breadcrumb)>0)
													<ul class="ac-breadcrumb">
														<li><a href="{{url('/')}}" title="Home">Home</a></li>
														@if($segment == 'boat' || $segment == 'services')
														<li class=""><a href="{{url($breadcrumb[0]['url'])}}" title="{{ ucwords($currentPageTitle) }}">{{ ucwords($currentPageTitle) }}</a></li>
														<li class="active"><span href="{{url('/')}}">{{ isset($breadcrumb) ? $breadcrumb[1]['title']:ucfirst($currentPageTitle) }}</span></li>
														<li class="back-btn"><a href="{{url($breadcrumb[0]['url'])}}" title="Back to Listing"><i class="fa fa-angle-left" aria-hidden="true"></i> Back to Listing</a></li>
														@else
														<li class=""><a href="{{url($breadcrumb['url'])}}" title="{{ ucwords($currentPageTitle) }}">{{ ucwords($currentPageTitle) }}</a></li>
														<li class="active"><span href="{{url('/')}}">{{ isset($breadcrumb) ? $breadcrumb['title']:ucfirst($currentPageTitle) }}</span></li>
														<li class="back-btn"><a href="{{url($breadcrumb['url'])}}" title="Back to Listing"><i class="fa fa-angle-left" aria-hidden="true"></i> Back to Listing</a></li>
														@endif
													</ul>
													@else
													<ul class="ac-breadcrumb">
														<li><a href="{{url('/')}}" title="Home">Home</a></li>
														@if($segment == 'site-map')
														<li class="active">{{ ucwords('Site Map') }}</li>
														@else
														<li class="active">{{ ucwords($detailPageTitle) }}</li>
														@endif
													</ul>
													@endif
												</div>
												<div class="col-md-3 col-sm-12 col-xs-12">
													<ul class="ac-media clearfix">
														<li>
															<div class="dropdown show">
																<a class="media_link dropdown-toggle" title="Share" role="button" id="dropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
																	<i class="icon-share"></i>Share
																</a>
																<!-- AddToAny BEGIN -->
																<div class="a2a_kit a2a_kit_size_32 a2a_default_style dropdown-menu" aria-labelledby="dropdownMenuLink">
																	<a class="a2a_button_facebook dropdown-item"></a>
																	<a class="a2a_button_twitter dropdown-item"></a>
																	<a class="a2a_button_linkedin dropdown-item"></a>
																</div>
																<script>
																	var a2a_config = a2a_config || {};
																	a2a_config.onclick = 1;
																</script>
																<!-- AddToAny END -->
															</div>
														</li>
														@if(Config::get('Constant.DEFAULT_EMAILTOFRIENDOPTION') == "Y")
														<li>
															<div class="email">
																<a class="media_link" data-toggle="modal" href="#Modal_emailtofriend" title="Email"><i class="fi flaticon-mail"></i>Email</a>
															</div>
														</li>
														@endif
													</ul>
												</div>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
					@endforeach
				</div>
			</div>
		</div>
	</div>
</section>
@else
@php
$segment = Request::segment(1);
@endphp
<section>
	<div class="inner-banner">
		<div class="container">
			<div id="inner-banner" class="carousel slide" data-ride="carousel">
				<!-- Wrapper for slides -->
				<div class="carousel-inner i-b__radisu">
					<div class="item active">
						<div class="i-b_fill" style="background: #fff;">
							<div class="i-b_caption">
								<div class="container p-0">
									<div class="row">
										<div class="d-flex flex-wrap align-items-center w-100">
											<div class="col-md-8">
												@if(isset($breadcrumb) && count($breadcrumb)>0)
												<ul class="ac-breadcrumb flex-wrap justify-content-center justify-content-lg-start">
													<li><a href="{{url('/')}}" title="Home">Home</a></li>
													@if($segment == 'boat' || $segment == 'services')
													@if($segment == "boat")
													@php
													$currentPageTitle = "Boats";
													@endphp
													@endif
													<li class=""><a href="{{url($breadcrumb[0]['url'])}}" title="{{ ucwords($currentPageTitle) }}">{{ ucwords($currentPageTitle) }}</a></li>
													<li class="active">{{ isset($breadcrumb) ? $breadcrumb[1]['title']:ucfirst($currentPageTitle) }}</li>
													@else
													@if($segment == "blogs")
													@php
													$currentPageTitle = "Blog";
													@endphp
													@endif
													<li class=""><a href="{{url($breadcrumb['url'])}}" title="{{ ucwords($currentPageTitle) }}">{{ ucwords($currentPageTitle) }}</a></li>
													<li class="active">{{ isset($breadcrumb) ? $breadcrumb['title']:ucfirst($currentPageTitle) }}</li>
													@endif
												</ul>
												@else
												<ul class="ac-breadcrumb">
													@if($segment == "boat")
													@php
													$detailPageTitle = "Boats";
													@endphp
													@endif
													<li><a href="{{url('/')}}" title="Home">Home</a></li>
													@if($segment == 'site-map')
													<li class="active">{{ ucwords('Site Map') }}</li>
													@else
													<li class="active">{{ ucwords($detailPageTitle) }}</li>
													@endif
												</ul>
												@endif
											</div>
											<div class="col-md-4 d-none d-md-block">
												<ul class="ac-media clearfix d-flex  justify-content-lg-end ">
													<li>
														@if(isset($breadcrumb) && count($breadcrumb)>0)
														@if($segment == 'boat' || $segment == 'services')
													<li class="back-btn "><a href="{{url($breadcrumb[0]['url'])}}" title="Back to Listing"><i class="fa fa-angle-left" aria-hidden="true"></i> Back to Listing </a></li>
													@else
													<li class="back-btn "><a href="{{url($breadcrumb['url'])}}" title="Back to Listing"><i class="fa fa-angle-left" aria-hidden="true"></i> Back to Listing</a></li>
													@endif
													@endif
													</li>
													<li>
														<div class="dropdown show">
															<a class="media_link dropdown-toggle" title="Share" role="button" id="dropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
																<i class="icon-share"></i>Share
															</a>
															<!-- AddToAny BEGIN -->
															<div class="share">
																<div class="a2a_kit a2a_kit_size_32 a2a_default_style dropdown-menu" aria-labelledby="dropdownMenuLink">
																	<a class="a2a_button_facebook dropdown-item"></a>
																	<a class="a2a_button_twitter dropdown-item"></a>
																	<a class="a2a_button_linkedin dropdown-item"></a>
																	<a class="a2a_button_whatsapp dropdown-item"></a>
																</div>
															</div>
															<script>
																var a2a_config = a2a_config || {};
																a2a_config.onclick = 1;
															</script>
															<!-- AddToAny END -->
														</div>
													</li>
													@if(Config::get('Constant.DEFAULT_EMAILTOFRIENDOPTION') == "Y")
													<li>
														<div class="email">
															<a class="media_link" data-toggle="modal" href="#Modal_emailtofriend" title="Email"><i class="fi flaticon-mail"></i>Email</a>
														</div>
													</li>
													@endif
												</ul>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</section>
@endif