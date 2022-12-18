@if(!Request::ajax())
@extends('layouts.app')
@section('content')
@include('layouts.inner_banner')
@endif
<!-- contact_01 S -->
<section class="inner-page-container contact_01">
    <div class="container">
        <div class="row bg-color_contact">
            <div class="col-md-6 col-lg-5 col-xl-5 l-1">
                @if(!empty($contact_info))
                @foreach($contact_info as $key => $value) 
                <div class="left-section">
                    <h3 class="cm-title text-uppercase">Contact Information</h3> 
                    <div class="cms">
                        <p>Fill up the form and our Team will get back to you within 24 hours.</p>
                    </div>   
                    <!-- <h3 class="cm-title text-uppercase">{{ $value->varTitle }}</h3> -->                        
                    @if(!empty($value->varPhoneNo))
                    <div class="info">
                        <!-- <div class="title">Phone :</div> -->
                        <div class="des">
                            @php $phone = unserialize($value->varPhoneNo); @endphp
                            @foreach($phone as $p)
                            <a href="tel:{{ $p }}" target="_blank" title="Call Us On {{ $p }}"><i class="icon-phone"></i>{{ $p }}</a>
                            @endforeach
                        </div>
                    </div>
                    @endif
                    @if(!empty($value->varEmail))
                    <div class="info">
                        <!-- <div class="title">Email :</div> -->
                        <div class="des">
                            @php  $email = unserialize($value->varEmail); @endphp
                            @foreach($email as $e)
                            <a href="mailto:{{ $e }}" target="_blank" title="Mail Us On {{ $e }}"><i class="icon-email"></i>{{ $e }}</a>
                            @endforeach
                        </div>
                    </div>
                    @endif
                    @if(!empty($value->txtAddress))
                    <div class="info">
                        <!-- <div class="title">Address :</div> -->
                        <div class="des"><p>{!! nl2br($value->txtAddress) !!}</p></div>
                    </div>
                    @endif
                    <!--<div class="info">
                        <div class="title">Opening Hours : </div>
                        <div class="des">
                            <div style="padding-bottom: 2px;">Monday to Friday : 8:30am-5:30pm</div>
                            <div style="padding-bottom: 2px;">Saturday : 10am-2pm</div>
                            <div style="padding-bottom: 2px;">Sunday : Closed</div>
                        </div>
                    </div>-->
                    @endforeach
                    @endif
                    @if(
                    !empty(Config::get('Constant.SOCIAL_FB_LINK')) || 
                    !empty(Config::get('Constant.SOCIAL_TWITTER_LINK')) || 
                    !empty(Config::get('Constant.SOCIAL_INSTAGRAM_LINK')) || 
                    !empty(Config::get('Constant.SOCIAL_PINTEREST_LINK')) || 
                    !empty(Config::get('Constant.SOCIAL_YELP_LINK'))
                    )
                    <div class="info socail">
                        <!-- <div class="cm-title text-uppercase">Follow Us</div> -->
                        <div class="des">
                            @if(!empty(Config::get('Constant.SOCIAL_FB_LINK')))
                            <a href="{{ Config::get('Constant.SOCIAL_FB_LINK') }}" class="icon-soc" title="Follow Us On Facebook" target="_blank"><i class="fa fa-facebook"></i></a>
                            @endif
                            @if(!empty(Config::get('Constant.SOCIAL_TWITTER_LINK')))
                            <a href="{{ Config::get('Constant.SOCIAL_TWITTER_LINK') }}" class="icon-soc" title="Follow Us On Twitter" target="_blank"><i class="fa fa-twitter"></i></a>
                            @endif
                            @if(!empty(Config::get('Constant.SOCIAL_INSTAGRAM_LINK')))
                            <a href="{{ Config::get('Constant.SOCIAL_INSTAGRAM_LINK') }}" class="icon-soc" title="Follow Us On Instagram" target="_blank"><i class="fa fa-instagram"></i></a>
                            @endif
                            @if(!empty(Config::get('Constant.SOCIAL_PINTEREST_LINK')))
                            <a href="{{ Config::get('Constant.SOCIAL_PINTEREST_LINK') }}" class="icon-soc" title="Follow Us On Pinterest" target="_blank"><i class="fa fa-pinterest"></i></a>
                            @endif
                            @if(!empty(Config::get('Constant.SOCIAL_YELP_LINK')))
                            <a href="{{ Config::get('Constant.SOCIAL_YELP_LINK') }}" class="icon-soc" title="Follow Us On Yelp" target="_blank"><i class="fa fa-yelp"></i></a>
                            @endif
                        </div>
                    </div>
                </div>
                @endif
            </div>
            <div class="col-md-6 col-lg-7 col-xl-7 r-1">
                <div class="row d-flex align-items-center">
                    <div class="contact-list col-12" style="height: 100%;">
                        <!-- <h3 class="cm-title text-uppercase">Get in Touch</h3> -->

                        {!! Form::open(['method' => 'post','class'=>'ac-form row w-xl-100', 'id'=>'contact_page_form', 'autocomplete' => 'off']   ) !!}

                        <div class="col-md-12 text-right mb-4 mt-5">
                            <div class="required">* Denotes Required Inputs</div>
                        </div>
                        <div class="col-md-12 col-lg-6">
                            <div class="form-group">
                                <label class="nq-label" for="first_name">Name<span class="star">*</span></label>
                                {!! Form::text('first_name', old('first_name'), array('id'=>'first_name', 'class'=>'form-control ac-input', 'name'=>'first_name', 'maxlength'=>'60', 'onpaste'=>'return false;', 'ondrop'=>'return false;')) !!}
                                @if ($errors->has('first_name'))
                                <span class="error">{{ $errors->first('first_name') }}</span>
                                @endif
                            </div>
                        </div>
                        <div class="col-md-12 col-lg-6" style="display: none;">
                            <div class="form-group">
                                <label class="nq-label" for="last_name">Last Name<span class="star">*</span></label>
                                {!! Form::text('last_name', old('last_name'), array('id'=>'last_name', 'class'=>'form-control ac-input', 'name'=>'last_name', 'maxlength'=>'60', 'onpaste'=>'return false;', 'ondrop'=>'return false;')) !!}
                                @if ($errors->has('last_name'))
                                <span class="error">{{ $errors->first('last_name') }}</span>
                                @endif
                            </div>
                        </div>
                        <div class="col-md-12 col-lg-6">
                            <div class="form-group">
                                <label class="nq-label" for="phone_number">Phone</label>
                                {!! Form::text('phone_number', old('phone_number'), array('id'=>'phone_number', 'class'=>'form-control ac-input', 'name'=>'phone_number', 'maxlength'=>"20", 'onpaste'=>'return false;', 'ondrop'=>'return false;', 'onkeypress'=>'javascript: return KeycheckOnlyPhonenumber(event);')) !!}
                                @if ($errors->has('phone_number'))
                                <span class="error">{{ $errors->first('phone_number') }}</span>
                                @endif
                            </div>
                        </div>
                        <div class="col-md-12 col-lg-6">
                            <div class="form-group">
                                <label class="nq-label" for="contact_email">Email<span class="star">*</span></label>
                                {!! Form::email('contact_email', old('contact_email'), array('id'=>'contact_email', 'class'=>'form-control ac-input', 'name'=>'contact_email', 'maxlength'=>'60', 'onpaste'=>'return false;', 'ondrop'=>'return false;')) !!}
                                @if ($errors->has('contact_email'))
                                <span class="error">{{ $errors->first('contact_email') }}</span>
                                @endif
                            </div>
                        </div>
                        <div class="col-md-12 col-lg-6">
                            <div class="form-group">
                                <label class="nq-label" for="contact_email">Interested In</label>
                                <select class="selectpicker ac-bootstrap-select form-control" name="services">
                                    <option value=''>Select Interested In</option>
                                    <option value='0'>General Enquiry</option>  
                                    @foreach($services as $service)
                                    <option value='{{$service->id}}'>{{$service->varTitle}}</option>
                                    @endforeach
                                </select>
                                @if ($errors->has('services'))
                                <span class="error">{{ $errors->first('services') }}</span>
                                @endif
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label class="nq-label" for="user_message">Message</label>
                                {!! Form::textarea('user_message', old('user_message'), array('class'=>'form-control ac-textarea', 'name'=>'user_message', 'rows'=>'3', 'id'=>'user_message', 'maxlength'=>'400', 'spellcheck'=>'true', 'onpaste'=>'return true;', 'ondrop'=>'return true;' )) !!}
                                @if ($errors->has('user_message'))
                                <span class="error">{{ $errors->first('user_message') }}</span>
                                @endif
                            </div>
                        </div>
                        <!-- @if(File::exists(app_path().'/NewsletterLead.php'))
                        <div class="col-md-12">
                            <div class="form-group">
                                <div class="nq-checkbox-list">
                                    <label class="nq-checkbox pt-xs-0">
                                        <input name="subscribe" type="checkbox"> Subscribe me to your newsletter as well<span></span>
                                    </label>
                                </div>
                            </div>
                        </div>
                        @endif -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <div class="captcha">
                                    <div id="recaptcha2"></div>
                                    <div class="capphitcha" data-sitekey="{{Config::get('Constant.GOOGLE_CAPCHA_KEY')}}">
                                        @if ($errors->has('g-recaptcha-response'))
                                        <label class="error help-block">{{ $errors->first('g-recaptcha-response') }}</label>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <button type="submit" class="ac-btn" title="Submit">Submit</button>
                            </div>
                        </div>

                        {!! Form::close() !!}
                    </div>
                </div>
            </div>			
        </div>
    </div>
</section>
<section class="conactus_mao">
    <div class="contaienr">
        <!--<iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d60252.146737536976!2d-81.35595428697765!3d19.29283643746882!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x8f2586f64c1f9699%3A0x8c51ee3c8bb6bfdd!2sGeorge%20Town%2C%20Cayman%20Islands!5e0!3m2!1sen!2sin!4v1620292882936!5m2!1sen!2sin" width="100%" height="350" style="border:0;" allowfullscreen="" loading="lazy"></iframe>-->
        <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3764.971916600912!2d-81.38359498509502!3d19.327024886944386!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x8f2587a8f667f48b%3A0x8d5157e795ad3d46!2sBuckingham%20Square!5e0!3m2!1sen!2sjm!4v1621517688713!5m2!1sen!2sjm" width="100%" height="350" style="border:0;" allowfullscreen="" loading="lazy"></iframe>
    </div>
</section>
<!-- @php
    
            $current_adress = !empty($contact_info->txtAddress)?$contact_info->txtAddress:'';
            $pinaddress = explode("*", trim(preg_replace('/\s\s+/', '*', $current_adress)));
            $pinaddress = implode('<br/>', $pinaddress);
    @endphp

    var address = "{{ trim(preg_replace('/\s\s+/', ' ',  $current_adress)) }}";
    var pinaddress = "{!! $pinaddress !!}";  -->
<script type="text/javascript">
    var sitekey = '{{Config::get("Constant.GOOGLE_CAPCHA_KEY")}}';
    var onContactloadCallback = function () {
        grecaptcha.render('contact_html_element', {
            'sitekey': sitekey
        });
    };
</script>

<!-- <script src="https://maps.googleapis.com/maps/api/js?key={{Config::get('Constant.GOOGLE_MAP_KEY')}}&callback=initMap" async defer></script> -->
<!-- <script src="https://www.google.com/recaptcha/api.js?onload=onContactloadCallback&render=explicit" async defer></script> -->
<!-- contact_01 E -->
@if(!Request::ajax())
@section('footer_scripts')
<script src="{{ url('assets/libraries/phone/jquery.caret.js') }}?{{ Config::get('Constant.VERSION') }}"></script>
<script src="{{ url('assets/libraries/phone/jquery.mobilePhoneNumber.js') }}?{{ Config::get('Constant.VERSION') }}"></script>
<script src="{{ url('assets/js/packages/serviceinquirylead/service-inquiry.js') }}?{{ Config::get('Constant.VERSION') }}"></script>
<!-- <script src="https://maps.googleapis.com/maps/api/js?key={{Config::get('Constant.GOOGLE_MAP_KEY')}}&callback=initMap" async defer></script> -->
@endsection
<style>
    .password_form {
        padding: 40px;
        background: #fff;
        box-shadow: 0 0 25px rgba(0,0,0,.5);
        max-width: 600px;
        margin: auto;
    }
    .password_form .label-title {    
        font-weight: 400;
        margin-bottom: 5px;
        font-size: 14px;
        color: gray;
    }
    .ac-border {   
        max-width: 200px;
        width: 100%;
        margin-top:10px;
    }
</style>
@endsection
@endif