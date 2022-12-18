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
    <div class="container">
        <div class="row">
            <div class="col-md-12 text-center">
                <div class="icon"><i class="fa fa-frown-o" aria-hidden="true"></i></div>
                <!-- <div class="cm-title">ERROR 404 <br/>NOT FOUND</div> -->
                <div class="cm-title"> Access Denied </div>
                <div class="desc">{{ $message }}</div>
                <div class="great_day">Have a great day!</div>
                <a class="ac-border" href="{{ url('/') }}" title="Back to Home"><span class="text">Back to Home</span><span class="line"></span></a>
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