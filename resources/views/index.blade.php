@if(!Request::ajax())
@extends('layouts.app')
@section('content')
@include('layouts.home_banner')
@endif


@if(isset($PAGE_CONTENT['response']) && $PAGE_CONTENT['response'] != '[]')
    {!!  $PAGE_CONTENT['response'] !!}
@elseif(isset($PAGE_CONTENT) && $PAGE_CONTENT != '[]')
    {!!  $PAGE_CONTENT !!}
@else
<section class="page_section">
  <div class="container">
		<div class="row">
			<div class="col-12 text-center">
				<h2>Coming Soon...</h2>
			</div>	
		</div>
	</div>
</section>
@endif      
@endsection
@section('footer_scripts')
@endsection