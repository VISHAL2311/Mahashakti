@if(!Request::ajax())

@extends('layouts.app')

@section('content')

@include('layouts.inner_banner')

@endif

<!-- contact_01 S -->

<section class="inner-page-container contact_01">

    <div class="container">

        <div class="row bg-color_contact">

            <div class="col-md-6 col-lg-5 col-xl-5 l-1 p-0">

                @if(!empty($contact_info))

                @foreach($contact_info as $key => $value)

                <div class="left-section">

                    <h3 class="text-uppercase contact__head">Contact Information</h3>

                    <div class="cms">

                        <p>Fill up the form and our Team will get back to you within 24 hours.</p>

                    </div>

                    @if(!empty($value->varPhoneNo))

                    <div class="info">

                        <div class="des">

                            @php $phone = unserialize($value->varPhoneNo); @endphp

                            @foreach($phone as $p)

                            

                            @php  $ph = str_replace([' ','(',')','-'],['','','',''],$p);  @endphp

                            <a href="tel:{{ $ph }}" title="Call Us On: {{ $p }}"><i class="icon-call"></i>{{ $p }}</a>

                            @endforeach

                        </div>

                    </div>

                    @endif

                    @if(!empty($value->varMobileNo))

                    <div class="info">

                        <div class="des">

                        @php  $ph1 = str_replace([' ','(',')','-'],['','','',''],$value->varMobileNo);  @endphp

                            <a href="tel:{{ $ph1 }}" title="Call Us On: {{ $value->varMobileNo }}"><i class="icon-call"></i>{{ $value->varMobileNo }}</a>

                        </div>

                    </div>

                    @endif

                    @if(!empty($value->varEmail))

                    <div class="info">

                        <div class="des">

                            @php $email = unserialize($value->varEmail); @endphp

                            @foreach($email as $e)

                            <a href="mailto:{{ $e }}" title="Mail Us On: {{ $e }}"><i class="fa fa-envelope-o" aria-hidden="true"></i>{{ $e }}</a>

                            @endforeach

                        </div>

                    </div>

                    @endif

                    @if(!empty($value->txtAddress))

                    <div class="info">

                        <div class="des">

                            <p><i class="fa fa-address-book-o contact_us_addr" aria-hidden="true"></i> {!! nl2br($value->txtAddress) !!}</p>

                        </div>

                    </div>

                    @endif

                    @endforeach

                    @endif

                    @if(

                    !empty(Config::get('Constant.SOCIAL_FB_LINK')) ||

                    !empty(Config::get('Constant.SOCIAL_TWITTER_LINK')) ||

                    !empty(Config::get('Constant.SOCIAL_INSTAGRAM_LINK')) ||

                    !empty(Config::get('Constant.SOCIAL_PINTEREST_LINK')) ||

                    !empty(Config::get('Constant.SOCIAL_ECAYONLINE_LINK')) ||

                    !empty(Config::get('Constant.SOCIAL_YACHTWORLD_LINK')) ||

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

                            @if(!empty(Config::get('Constant.SOCIAL_ECAYONLINE_LINK')))

                            <a href="{{ Config::get('Constant.SOCIAL_ECAYONLINE_LINK') }}" class="icon-soc" title="Follow Us On Ecay Online" target="_blank"><i class="fa fa-etsy"></i></a>

                            @endif

                            @if(!empty(Config::get('Constant.SOCIAL_YACHTWORLD_LINK')))

                            <a href="{{ Config::get('Constant.SOCIAL_YACHTWORLD_LINK') }}" class="icon-soc" title="Follow Us On Yacht World" target="_blank"><i class="fa fa-ship"></i></a>

                            @endif

                        </div>

                    </div>

                </div>

                @endif

            </div>

            <div class="col-md-6 col-lg-7 col-xl-7 r-1">

                <div class="row d-flex align-items-center">

                    <div class="contact-list col-12 contact-us_list" style="height: 100%;">



                        {!! Form::open(['method' => 'post','class'=>'ac-form row w-xl-100', 'id'=>'contact_page_form', 'autocomplete' => 'off'] ) !!}



                        <div class="col-md-12 text-right mb-4 mt-4 mt-lg-5">

                            <div class="required">* Denotes Required Inputs</div>

                        </div>

                        <div class="col-md-12 col-lg-6">

                            <div class="form-group">

                                <!-- <label class="nq-label" for="first_name">Name<span class="star">*</span></label> -->

                                {!! Form::text('first_name', old('first_name'), array('id'=>'first_name', 'class'=>'form-control ac-input', 'name'=>'first_name', 'maxlength'=>'60', 'placeholder'=>"Name*", 'onpaste'=>'return false;', 'ondrop'=>'return false;')) !!}

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

                                <!-- <label class="nq-label" for="phone_number">Phone</label> -->

                                {!! Form::text('phone_number', old('phone_number'), array('id'=>'phone_number', 'class'=>'form-control ac-input maxlength-handler', 'name'=>'phone_number', 'maxlength'=>"14", 'placeholder'=>"Phone", 'onpaste'=>'return false;', 'ondrop'=>'return false;', 'onkeypress'=>'javascript: return KeycheckOnlyPhonenumber(event);')) !!}

                                @if ($errors->has('phone_number'))

                                <span class="error">{{ $errors->first('phone_number') }}</span>

                                @endif

                            </div>

                        </div>

                        <div class="col-md-12 col-lg-6">

                            <div class="form-group">

                                <!-- <label class="nq-label" for="contact_email">Email<span class="star">*</span></label> -->

                                {!! Form::email('contact_email', old('contact_email'), array('id'=>'contact_email', 'class'=>'form-control ac-input', 'name'=>'contact_email', 'maxlength'=>'60', 'placeholder'=>"Email*", 'onpaste'=>'return false;', 'ondrop'=>'return false;')) !!}

                                @if ($errors->has('contact_email'))

                                <span class="error">{{ $errors->first('contact_email') }}</span>

                                @endif

                            </div>

                        </div>

                        <div class="col-md-12 col-lg-6">

                            <div class="form-group">

                                <!-- <label class="nq-label" for="contact_email">Interested In*</label> -->

                                <select class="selectpicker ac-bootstrap-select form-control" name="services">

                                    <option value=''>Select Interested In*</option>

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

                                <!-- <label class="nq-label" for="user_message">Message</label> -->

                                {!! Form::textarea('user_message', old('user_message'), array('class'=>'form-control ac-textarea', 'name'=>'user_message', 'rows'=>'3', 'id'=>'user_message', 'maxlength'=>'400', 'placeholder'=>"Message", 'spellcheck'=>'true', 'onpaste'=>'return true;', 'ondrop'=>'return true;' )) !!}

                                @if ($errors->has('user_message'))

                                <span class="error">{{ $errors->first('user_message') }}</span>

                                @endif

                            </div>

                        </div>

                        <div class="col-md-6">

                            <div class="form-group">

                                <div class="captcha" >

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

                            <div class="form-group form__last">

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



<!--<section class="conactus_map">-->

<!--    <div class="contaienr">-->

<!--        <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3764.765860238963!2d-81.38152968515918!3d19.3359637869393!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x8f2587a121466493%3A0x89b5382033ec2311!2sThe%20Ritz-Carlton%2C%20Grand%20Cayman!5e0!3m2!1sen!2sin!4v1653953780590!5m2!1sen!2sin" width="100%" height="450" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>-->

<!--    </div>-->

<!--</section>-->

<script type="text/javascript">

    var sitekey = '{{Config::get("Constant.GOOGLE_CAPCHA_KEY")}}';

    var onContactloadCallback = function() {

        grecaptcha.render('recaptcha2', {

            'sitekey': sitekey

        });

    };

</script>

<script src="https://www.google.com/recaptcha/api.js?onload=onContactloadCallback&render=explicit" async defer></script>



<!-- contact_01 E -->

@if(!Request::ajax())

@section('footer_scripts')

<script src="{{ url('assets/libraries/phone/jquery.caret.js') }}?{{ Config::get('Constant.VERSION') }}"></script>

<script src="{{ url('resources/global/plugins/bootstrap-maxlength/bootstrap-maxlength.min.js') }}" type="text/javascript"></script>

<script src="{{ url('assets/libraries/phone/jquery.mobilePhoneNumber.js') }}?{{ Config::get('Constant.VERSION') }}"></script>

<script src="{{ url('assets/js/packages/contactuslead/contact-us.js') }}?{{ Config::get('Constant.VERSION') }}"></script>

@endsection

<style>

    .password_form {

        padding: 40px;

        background: #fff;

        box-shadow: 0 0 25px rgba(0, 0, 0, .5);

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

        margin-top: 10px;

    }

</style>

@endsection

@endif