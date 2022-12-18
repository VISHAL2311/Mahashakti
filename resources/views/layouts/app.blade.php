@php
$CDN_PATH = Config::get('Constant.CDN_PATH');
$requestedFullUrl = Request::Url();
$homePageUrl = url('/');
@endphp
@if(!Request::ajax())
@if(Request::segment(1) == 'previewpage')  
@include('layouts.preview')
@else
@include('layouts.header')
@include('layouts.header_main')
@yield('content')
@include('layouts.footer_main')
@include('layouts.footer')
@endif
@endif