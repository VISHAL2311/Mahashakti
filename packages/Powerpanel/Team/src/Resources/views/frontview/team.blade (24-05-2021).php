@if(!Request::ajax())
@extends('layouts.app')
@section('content')
@include('layouts.inner_banner')
@endif

@if(!empty($teams) && count($teams)>0)
	<section>
		<div class="inner-page-container team-listing">
			<div class="container">
				<div class="row">
					@foreach($teams as $index => $team)
					<div class="col-lg-4 col-sm-6 col-12 team-main" data-aos="fade-up">	
						<div class="team_box">
							<div class="team_img img_hvr">								
								<div class="thumbnail-container">
									<div class="thumbnail">
										<a href="{{ url('team') }}/{{ $team->alias->varAlias }}" title="{{ ucwords($team->varTitle) }}">
											<img class="w-100 lazy" data-src="{{ App\Helpers\resize_image::resize($team->fkIntImgId,500,550)}}" src="{{ App\Helpers\resize_image::resize($team->fkIntImgId,500,550)}}" alt="{{ htmlspecialchars_decode($team->varTitle) }}">
										</a>
									</div>
								</div>	
							</div>
							<div class="team_info">								
								<h3 class="sub_title"><a href="{{ url('team') }}/{{ $team->alias->varAlias }}" title="{{ ucwords($team->varTitle) }}">{{ htmlspecialchars_decode($team->varTitle) }}</a></h3>								
								<!-- <div class="designation">{{ $team->varTagLine }}</div> -->
								<ul class="contact">
                                    @if(isset($team->varEmail) && !empty($team->varEmail))
									<li><a href="mailto:{{ $team->varEmail }}" title="Email Us On {{ $team->varEmail }}">{{ $team->varEmail }}</a></li>
                                    @endif
                                    @if(isset($team->varPhoneNo) && !empty($team->varPhoneNo))
									<li><a href="tel:{{ $team->varPhoneNo }}" title="Call Us On {{ $team->varPhoneNo }}">{{ $team->varPhoneNo }}</a></li>
                                    @endif
								</ul>
								<!--<ul class="social">
									<li><a href="#" title=""><i class="fa fa-facebook"></i></a></li>
									<li><a href="#" title=""><i class="fa fa-instagram"></i></a></li>
									<li><a href="#" title=""><i class="fa fa-twitter"></i></a></li>
									<li><a href="#" title=""><i class="fa fa-linkedin"></i></a></li>
								</ul>	-->
								<!-- @if(!empty($team->txtDescription))
								<div class="short_desc">
									{!! htmlspecialchars_decode(str_limit($team->txtDescription),100) !!}
								</div>
								@endif -->
								<a class="ac-border" href="{{ url('team') }}/{{ $team->alias->varAlias }}" title="Read More">
                                    <span class="text">Read More</span>
                                    <span class="line"></span>
                                </a>
							</div>
						</div>	
					</div>
					@endforeach
					@if(isset($teams) && count($teams)>0)
						<div class="col-12">
							<div class="pagination">
								{{ $teams->links() }}
							</div>
						</div>
					@endif
				</div>
			</div>	
		<div>
	</section>
@else 
	<section class="inner-page-container">
		<div class="container">
			<div class="row">
				<div class="col-12 text-center">
						<h2>Coming Soon...</h2>
				</div>	
			</div>
		</div>  
	</section>    
@endif

@if(!Request::ajax())
@section('footer_scripts')
@endsection
@endsection
@endif