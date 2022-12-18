@if(!Request::ajax())
@extends('layouts.app')
@section('content')
@include('layouts.inner_banner')
@endif

<!-- contact_01 S -->
<?php
if (isset($PAGE_CONTENT['response']) && !empty($PAGE_CONTENT['response']) && $PAGE_CONTENT['response'] != '[]') {
    echo $PAGE_CONTENT['response'];
}
?>
<section class="inner-page-container request-quote-page">
    <div class="container">
        <div class="row">
            
            
            <div class="col-lg-12 d-flex flex-wrap">
                <div class="request-content">
                    <div class="common-title">
                        <h2 class="title">Questions? Write Us Here!</h2>
                    </div>
                    <div class="cms">
                        <p>We are available by e-mail or by phone. You can also use the quick Get Free Estimate form to ask a question about our services and projects we are working on. We would be pleased to answer your questions.</p>
                    </div>
                </div>
                <div class="contact-form">
                    {!! Form::open(['method' => 'post','class'=>'nqform mt-xs-30', 'id'=>'getaestimate_page_form']) !!}
                    <div class="row align-items-start">
                        <div class="col-md-12 text-right">
                            <div class="required">* Denotes Required Inputs</div>
                        </div>
                        <div class="col-lg-6">
                            <div class="form-group">
                                    <!-- <label class="nq-label" for="first_name">First Name<span class="star">*</span></label> -->
                                {!! Form::text('first_name', old('first_name'), array('id'=>'first_name', 'class'=>'form-control nq-input', 'name'=>'first_name', 'maxlength'=>'60','placeholder'=>'Name *', 'onpaste'=>'return false;', 'ondrop'=>'return false;')) !!}
                                @if ($errors->has('first_name'))
                                <span class="error">{{ $errors->first('first_name') }}</span>
                                @endif
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="form-group">
                                    <!-- <label class="nq-label" for="contact_email">Email<span class="star">*</span></label> -->
                                {!! Form::email('contact_email', old('contact_email'), array('id'=>'contact_email', 'class'=>'form-control nq-input', 'name'=>'contact_email', 'maxlength'=>'60','placeholder'=>'Email Address *', 'onpaste'=>'return false;', 'ondrop'=>'return false;')) !!}
                                @if ($errors->has('contact_email'))
                                <span class="error">{{ $errors->first('contact_email') }}</span>
                                @endif
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="form-group">
                                    <!-- <label class="nq-label" for="phone_number">Phone<span class="star">*</span></label> -->
                                {!! Form::text('phone_number', old('phone_number'), array('id'=>'phone_number', 'class'=>'form-control nq-input', 'name'=>'phone_number', 'maxlength'=>"20", 'placeholder'=>'Phone *','onpaste'=>'return false;', 'ondrop'=>'return false;', 'onkeypress'=>'javascript: return KeycheckOnlyPhonenumber(event);')) !!}
                                @if ($errors->has('phone_number'))
                                <span class="error">{{ $errors->first('phone_number') }}</span>
                                @endif
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="form-group">
                                    <!-- <label class="nq-label" for="phone_number">Phone<span class="star">*</span></label> -->
                                <select class="form-control" name="services">
                                    <option value=''>Select Service *</option>
                                    @foreach($services as $service)
                                    <option value='{{$service->id}}'>{{$service->varTitle}}</option>
                                    @endforeach
                                </select>
                                @if ($errors->has('services'))
                                <span class="error">{{ $errors->first('services') }}</span>
                                @endif
                            </div>
                        </div>
                        <div class="col-lg-12">
                            <div class="form-group">
                                <!-- <label class="nq-label" for="user_message">Comments</label> -->
                                {!! Form::textarea('user_message', old('user_message'), array('class'=>'form-control nq-textarea', 'name'=>'user_message', 'maxlength'=>'400', 'rows'=>'6', 'id'=>'user_message', 'spellcheck'=>'true', 'placeholder'=>'Description' )) !!}
                                @if ($errors->has('user_message'))
                                <span class="error">{{ $errors->first('user_message') }}</span>
                                @endif
                            </div>
                        </div>

                        <div class="col-lg-6">
                            <div class="form-group">
                                <div class="captcha">
                                    <div id="recaptcha1"></div>
                                    <div class="capphitcha" data-sitekey="{{Config::get('Constant.GOOGLE_CAPCHA_KEY')}}">
                                        @if ($errors->has('g-recaptcha-response'))
                                        <label class="error help-block">{{ $errors->first('g-recaptcha-response') }}</label>
                                    @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-6 text-center text-md-right">
                            <div class="form-group">
                                <button type="submit" class="btn btn-primary" id="submitform" title="Submit">Submit</button>
                            </div>
                        </div>
                    </div>
                    {!! Form::close() !!}
                </div>
            </div>
            
        </div>
    </div>
</section>

<!--<script src="https://maps.googleapis.com/maps/api/js?key={{Config::get('Constant.GOOGLE_MAP_KEY')}}&callback=initMap" async defer></script>-->
<!-- <script src="https://www.google.com/recaptcha/api.js?onload=onContactloadCallback&render=explicit" async defer></script> -->
<!-- contact_01 E -->
@if(!Request::ajax())
@section('footer_scripts')
<script src="{{ url('assets/libraries/phone/jquery.caret.js') }}"></script>
<script src="{{ url('assets/libraries/phone/jquery.mobilePhoneNumber.js') }}"></script>
<script src="{{ url('assets/js/packages/getaestimatelead/get-a-estimate.js') }}"></script>
<!-- <script src="https://maps.googleapis.com/maps/api/js?key={{Config::get("Constant.GOOGLE_MAP_KEY")}}&callback=initMap" async defer></script> -->
@endsection
@endsection
@endif