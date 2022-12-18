@if(!Request::ajax())
@extends('layouts.app')
@section('content')
@include('layouts.inner_banner')
@endif

@if(isset($PageData['response']) && !empty($PageData['response']) && gettype($PageData['response']) == "string")

{!! $PageData['response'] !!}
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
<script type="text/javascript" src="{{ $CDN_PATH.'assets/libraries/masonry/js/masonry.pkgd.min.js'}}?{{ Config::get('Constant.VERSION') }}"></script>
<script type="text/javascript" src="{{ $CDN_PATH.'assets/libraries/masonry/js/masonry-function.js'}}?{{ Config::get('Constant.VERSION') }}"></script>
@endsection
@endsection
@endif