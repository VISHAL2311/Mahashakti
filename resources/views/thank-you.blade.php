@if(!Request::ajax())
@extends('layouts.app')
@section('content')
@endif
<div id="thankyou-page"></div> 
<div id="notfound-page thank_you_page"></div>
<section class="thankyou-01">
    <div class="container-fluid p-0">
        <div class="thankyou_image">
            <div class="thankyou_message">
                <h2 class="thankyou_title"> Thank You! </h2>                
                <p class="thankyou_para"> 
                  {!! $message !!}
                </p>
                <h5 class="thankyou_singup">Have a great day!</h5>
                <a class="ac-btn ac-wht" href="{{ url('/') }}" title="Back to Home"><span class="text">Back to Home</span><span class="line"></span></a>
            </div>
        </div>
    </div>
</section>

@if(!Request::ajax())
@section('footer_scripts')
<script src="{{ url('') }}"></script>

<script src="{{ url('assets/js/thank-you.js') }}?{{ Config::get('Constant.VERSION') }}"></script>


@endsection

@endsection
@endif