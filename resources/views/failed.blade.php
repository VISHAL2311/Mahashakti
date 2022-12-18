@php
  $frontLoad = new \App\Http\Controllers\FrontController;
@endphp
@if(!Request::ajax())
@extends('layouts.app')
@section('content')
{{--@include('layouts.inner_banner')--}}
@endif



<!-- 404_01 S -->
<div id="notfound-page"></div>
<section class="inner-page-container notfound_01">
    <div class="container-fluid">
        <div class="error_image">
            <div class="error_message">
                <h2 class="error-title">Access Denied</h2>
                <p class="error_para pb-1">{{ $message }}</p>
                <p class="error_para">Click on the links below to do something, Thanks!</p>
                <a class="ac-btn ac-wht" href="{{ url('/') }}" title="Back to Home"><span class="text">Back to Home</span><span class="line"></span></a>
            </div>
        </div>
    </div>
</section>
<!-- 404_01 E -->


@if(!Request::ajax())
@section('footer_scripts')
<script src="{{ url('assets/js/404.js') }}"></script>


@endsection

@endsection
@endif