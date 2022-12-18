@php
$div1 = "";
$div2 = "";
@endphp
@if(Request::segment(1) == '')
@php
$div1 = "display:none;";
$div2 = "display:none;";
@endphp
<script>
$(document).scroll(function () {
    if ($(this).scrollTop() > 500) {
        $('#footer_div').css("display", "block");
        $('.instagram-section').css("display", "block");
    }
});
</script>
@else
@php
$div1 = "display:block;";
$div2 = "display:block;";
@endphp
@endif
<section class="instagram-section" style="{{$div1}}">
    <div class="container">
        <div class="row">
            <div class="col-sm-12 text-center" data-aos="fade-in">
                <div class="cm-title text-uppercase">Instagram</div>
            </div>
        </div>
         <div class="row">
            <?php
            $token = DB::table('insta_token')->select('varToken')->where('id', '=', 1)->first();
            $feed_url = 'https://graph.instagram.com/17841447585035231/media?fields=id,caption,media_type,media_url,permalink,thumbnail_url,username,timestamp&access_token='.$token->varToken.'&limit=4';
            $curl = curl_init();
            curl_setopt($curl, CURLOPT_URL, $feed_url);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
            $response = curl_exec($curl);
            $responseArray = json_decode($response, true);
            curl_close($curl);
            if (isset($responseArray['data'])) {
                $assign['Insta'] = $responseArray['data'];
                foreach ($assign['Insta'] as $instadata) {
                    ?>
                    <div class="col-sm-3 col-xs-6 items" data-aos="fade-in">
                        <a href="<?php echo $instadata['permalink']; ?>" title="<?php echo $instadata['username']; ?>" target="_blank">
                            <div class="thumbnail-container">
                                <div class="thumbnail">
                                    <img src="<?php echo $instadata['media_url']; ?>" alt="<?php echo $instadata['username']; ?>" loading="lazy">
                                </div>
                                <div class="icon"><i class="icon-instagram-o"></i></div>
                            </div>
                        </a>
                    </div>
                    <?php
                }
            } else {
                ?>
                <div class="col-sm-3 col-xs-6 items" data-aos="fade-in">
                    <a href="https://www.instagram.com/douglasconstructiongroup/" title="" target="_blank">
                        <div class="thumbnail-container">
                            <div class="thumbnail">
                                <img src="{{ $CDN_PATH.'assets/images/instagram-01.jpg' }}"  data-src="{{ $CDN_PATH.'assets/images/instagram-01.jpg' }}" class="lazy" alt="Douglas Construction">
                            </div>
                            <div class="icon"><i class="icon-instagram-o"></i></div>
                        </div>
                    </a>
                </div>
                <div class="col-sm-3 col-xs-6 items" data-aos="fade-in">
                    <a href="https://www.instagram.com/douglasconstructiongroup/" title="" target="_blank">
                        <div class="thumbnail-container">
                            <div class="thumbnail">
                                <img src="{{ $CDN_PATH.'assets/images/instagram-02.jpg' }}" data-src="{{ $CDN_PATH.'assets/images/instagram-02.jpg' }}" class="lazy" alt="Douglas Construction">
                            </div>
                            <div class="icon"><i class="icon-instagram-o"></i></div>
                        </div>
                    </a>
                </div>
                <div class="col-sm-3 col-xs-6 items" data-aos="fade-in">
                    <a href="https://www.instagram.com/douglasconstructiongroup/" title="" target="_blank">
                        <div class="thumbnail-container">
                            <div class="thumbnail">
                                <img src="{{ $CDN_PATH.'assets/images/instagram-03.jpg' }}" data-src="{{ $CDN_PATH.'assets/images/instagram-03.jpg' }}" class="lazy" alt="Douglas Construction">
                            </div>
                            <div class="icon"><i class="icon-instagram-o"></i></div>
                        </div>
                    </a>
                </div>
                <div class="col-sm-3 col-xs-6 items" data-aos="fade-in">
                    <a href="https://www.instagram.com/douglasconstructiongroup/" title="" target="_blank">
                        <div class="thumbnail-container">
                            <div class="thumbnail">
                                <img src="{{ $CDN_PATH.'assets/images/instagram-04.jpg' }}" data-src="{{ $CDN_PATH.'assets/images/instagram-04.jpg' }}" class="lazy" alt="Douglas Construction">
                            </div>
                            <div class="icon"><i class="icon-instagram-o"></i></div>
                        </div>
                    </a>
                </div>
            <?php } ?>
        </div> 
    </div>
</section>
<footer id="footer_div" style="{{$div2}}">
    <div class="cookie-note" id="js-gdpr-consent-banner" style="display: none;" data-aos="fade-up"> 
        <img src="{{ $CDN_PATH.'assets/images/cookie.svg'}}" loading="lazy" class="cookie-img" alt="Cookie">
        <h6>Improve Site Experience</h6>
        @php
        $var_privacy_policy=\App\Helpers\static_block::get_page_title('10');
        $class='';
        @endphp 
        @if(Request::segment(1) == 'privacy-policy')
        @php $class='active';@endphp
        @endif
        @if(isset($var_privacy_policy) && count($var_privacy_policy) > 0)
        <!-- <p>This website uses cookies in order to improve the user experience <a herf="{{ url('privacy-policy') }}" title="Cookies" target="_blank">Cookies</a></p>         -->
        <p>This website uses cookies in order to improve the user experience Cookies</p>        
        @endif
        <div class="d-flex justify-content-around">
            <button type="submit" class="btn close-btn" title="Accept" id="cookie_policy" onclick="GetGDPRCLOSE()">Accept</button>
        </div>        
    </div>
    <div class="right-form">    
        <span class="text-g">Get a Free Estimate</span>            
        <div class="" id="form-container">
            <div id="form-close"><i class="icon-pencil"></i></div>                  
            <div id="form-content">
                {!! Form::open(['method' => 'post','class'=>'ac-form', 'id'=>'getaestimate_page_form','url'=>'/getaestimate']) !!}
                <div class="row">
                    <div class="col-12">
                        <h3 class="cm-title text-uppercase">Get a Free Estimate</h3>
                        <p class="cm-sub-t">Fill up the form and our team will get back to you within 24 hours.</p>
                    </div>
                    <div class="col-12">
                        <div class="form-group ac-form-group">
                            {!! Form::text('first_name', old('first_name'), array('id'=>'first_name', 'class'=>'form-control ac-input', 'name'=>'first_name', 'maxlength'=>'60','placeholder'=>'Name *', 'onpaste'=>'return false;', 'ondrop'=>'return false;')) !!}
                            @if (isset($errors) && $errors->has('first_name'))
                            <span class="error">{{ $errors->first('first_name') }}</span>
                            @endif
                        </div>                      
                    </div>
                    <div class="col-12" style="display:none;">
                        <div class="form-group ac-form-group">
                            {!! Form::text('last_name', old('last_name'), array('id'=>'last_name', 'class'=>'form-control ac-input', 'name'=>'last_name', 'maxlength'=>'60','placeholder'=>'Last Name', 'onpaste'=>'return false;', 'ondrop'=>'return false;')) !!}
                            @if (isset($errors) && $errors->has('last_name'))
                            <span class="error">{{ $errors->first('last_name') }}</span>
                            @endif
                        </div>                      
                    </div>
                    <div class="col-12">
                        <div class="form-group ac-form-group">
                            {!! Form::email('contact_email', old('contact_email'), array('id'=>'contact_email', 'class'=>'form-control ac-input', 'name'=>'contact_email', 'maxlength'=>'60','placeholder'=>'Email Address *', 'onpaste'=>'return false;', 'ondrop'=>'return false;')) !!}  
                            @if (isset($errors) && $errors->has('contact_email'))
                            <span class="error">{{ $errors->first('contact_email') }}</span>
                            @endif
                        </div>                      
                    </div>
                    <div class="col-12">
                        <div class="form-group ac-form-group">
                            {!! Form::text('phone_number', old('phone_number'), array('id'=>'phone_number_estimate', 'class'=>'form-control ac-input', 'name'=>'phone_number', 'maxlength'=>"20", 'placeholder'=>'Phone','onpaste'=>'return false;', 'ondrop'=>'return false;', 'onkeypress'=>'javascript: return KeycheckOnlyPhonenumber(event);')) !!}
                            @if (isset($errors) && $errors->has('phone_number'))
                            <span class="error">{{ $errors->first('phone_number') }}</span>
                            @endif
                        </div>                      
                    </div>
                    {{-- <div class="col-12">
                        <div class="form-group ac-form-group">
                            <select class="selectpicker ac-bootstrap-select" data-width="100%" title="Interested In *" name="services">
                                <option value=''>--Select Interested In--</option>
                                @foreach($services as $service)
                                <option value='{{$service->id}}' >{{$service->varTitle}}</option>
                                @endforeach
                            </select>
                            @if (isset($errors) && $errors->has('services'))
                            <span class="error">{{ $errors->first('services') }}</span>
                            @endif
                        </div>                      
                    </div> --}}
                    <div class="col-12">
                        <div class="form-group ac-form-group">
                            {!! Form::textarea('user_message', old('user_message'), array('class'=>'form-control ac-textarea', 'name'=>'user_message', 'maxlength'=>'400', 'rows'=>'2', 'id'=>'user_message', 'spellcheck'=>'true', 'placeholder'=>'Message' )) !!}
                            @if (isset($errors) && $errors->has('user_message'))
                            <span class="error">{{ $errors->first('user_message') }}</span>
                            @endif
                        </div>                      
                    </div>
                    <div class="col-12">
                        <div class="form-group ac-form-group">
                            <div class="captcha">
                                <div id="recaptcha1"></div>
                                <div class="capphitcha" data-sitekey="{{Config::get('Constant.GOOGLE_CAPCHA_KEY')}}">
                                    @if (isset($errors) && $errors->has('g-recaptcha-response'))
                                    <label class="error help-block">{{ $errors->first('g-recaptcha-response') }}</label>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-12">
                        <button type="submit" class="ac-btn" value="Submit" title="Submit">Submit</button>
                    </div>
                </div>
                {!! Form::close() !!}
            </div>
        </div>
    </div>
    <div class="footer-main">
        <div class="container">
            <div class="row">
                <div class="col-lg-3 col-sm-6 col-xs-6 col-xss-12 items" data-aos="fade-in">
                    <div class="footer_box mail_box">
                        <div class="footer_logo">
                            @php $segment = Request::segment(2); @endphp
                            @if($segment == 'aceessdenied')
                            <a href="{{ url('/') }}" title="Douglas Construction}">
                                <img src="{{ App\Helpers\resize_image::resize(Config::get('Constant.FRONT_LOGO_ID')) }}" alt="{{ Config::get("Constant.SITE_NAME") }}" loading="lazy">
                            </a>
                            @else
                            <a href="{{ url('/') }}" title="{{ Config::get("Constant.SITE_NAME") }}">
                                <img src="{{ App\Helpers\resize_image::resize(Config::get('Constant.FRONT_LOGO_ID')) }}" loading="lazy" alt="{{ Config::get("Constant.SITE_NAME") }}">
                            </a>
                            @endif
                        </div>
                        @if(isset($objContactInfo) && !empty($objContactInfo))
                        @php $address = $objContactInfo[0]->txtAddress; @endphp
                        @endif
                        @if(isset($address) && !empty($address))
                        <div class="about_foot">
                            <p>{!! nl2br($address) !!}</p>
                        </div>  
                        @else
                        <div class="about_foot">
                            <p>10 Market st, P.O 507<br /> KY1-9006 Camana Bay, Grand Cayman<br /> Cayman Island</p>
                        </div>  
                        @endif                      
                    </div>
                </div>
                <div class="col-lg-3 col-sm-6 col-xs-6 col-xss-12 items" data-aos="fade-in">
                    <div class="footer_box quick_box">
                        <div class="foot_title">Contact Us</div>
                        @if(isset($objContactInfo) && !empty($objContactInfo))
                        @php
                        $email = unserialize($objContactInfo[0]->varEmail);
                        $email = count($email)>0?$email[0]:$email;
                        $phone = unserialize($objContactInfo[0]->varPhoneNo);
                        $phone = count($phone)>0?$phone[0]:$phone;
                        $phone1 = str_replace(' ','',$phone);
                        @endphp
                        @endif
                        <ul class="contact-info">
                            @if(isset($phone) && !empty($phone))
                            <li><a href="tel:{{ $phone1 }}" title="Call Us On : {{ $phone1 }}"><i class="icon-phone"></i> {{ $phone }}</a></li>
                            @endif
                            @if(isset($objContactInfo) && !empty($objContactInfo[0]->varMobileNo))
                            <li><a href="tel:{{str_replace(' ','',$objContactInfo[0]->varMobileNo)}}" title="Call Us On : {{str_replace(' ','',$objContactInfo[0]->varMobileNo)}}"><i class="icon-phone"></i> {{$objContactInfo[0]->varMobileNo}}</a></li>
                            @endif
                            @if(isset($email) && !empty($email))
                            <li><a href="mailto:{{ $email }}" title="Email Us On : {{ $email }}"><i class="icon-email"></i> {{ $email }}</a></li>
                            @endif
                        </ul>
                    </div>
                </div>              
                <div class="col-lg-3 col-sm-6 col-xs-6 col-xss-12 items" data-aos="fade-in">
                    <div class="footer_box quick_box">
                        <div class="foot_title">Follow us</div>
                        @php $socialAvailable = false; @endphp
                        @if((null!==Config::get('Constant.SOCIAL_FB_LINK') && strlen(Config::get('Constant.SOCIAL_FB_LINK')) > 0) || (null!==Config::get('Constant.SOCIAL_TWITTER_LINK') && strlen(Config::get('Constant.SOCIAL_TWITTER_LINK')) > 0) || (null!==Config::get('Constant.SOCIAL_YOUTUBE_LINK') && strlen(Config::get('Constant.SOCIAL_YOUTUBE_LINK')) > 0))
                        @php $socialAvailable = true; @endphp
                        <ul class="social">
                            @if(null!==(Config::get('Constant.SOCIAL_FB_LINK')) && strlen(Config::get('Constant.SOCIAL_FB_LINK')) > 0)
                            <li><a href="{{ Config::get('Constant.SOCIAL_FB_LINK') }}" title="Follow Us On Facebook" target="_blank"><i class="icon-facebook"></i></a></li>
                            @endif
                            @if(null!==(Config::get('Constant.SOCIAL_TWITTER_LINK')) && strlen(Config::get('Constant.SOCIAL_TWITTER_LINK')) > 0)
                            <li><a href="{{ Config::get('Constant.SOCIAL_TWITTER_LINK') }}" title="Follow Us On Twitter" target="_blank"><i class="icon-twitter"></i></a></li>
                            @endif
                            @if(null!==(Config::get('Constant.SOCIAL_INSTAGRAM_LINK')) && strlen(Config::get('Constant.SOCIAL_INSTAGRAM_LINK')) > 0)
                            <li><a href="{{ Config::get('Constant.SOCIAL_INSTAGRAM_LINK') }}" title="Follow Us On Instagram" target="_blank"><i class="icon-instagram"></i></a></li>
                            @endif
                            @if(null!==(Config::get('Constant.SOCIAL_PINTEREST_LINK')) && strlen(Config::get('Constant.SOCIAL_PINTEREST_LINK')) > 0)
                            <li><a href="{{ Config::get('Constant.SOCIAL_PINTEREST_LINK') }}" title="Follow Us On Pinterest" target="_blank"><i class="icon-pinterest"></i></a></li>
                            @endif
                            @if(null!==(Config::get('Constant.SOCIAL_YELP_LINK')) && strlen(Config::get('Constant.SOCIAL_YELP_LINK')) > 0)
                            <li><a href="{{ Config::get('Constant.SOCIAL_YELP_LINK') }}" title="Follow Us On Yelp" target="_blank"><i class="icon-yelp"></i></a></li>
                            @endif
                        </ul>
                        @endif
                    </div>
                </div>
                <div class="col-lg-3 col-sm-6 col-xs-6 col-xss-12 items" data-aos="fade-in">
                    <div class="footer_box newslatter_sign">
                        <h4 class="foot_title">SIGN UP FOR NEWSLETTER</h4>
                        <div class="subscribe-sec">
                            <div class="mailling_box">    
                                {!! Form::open(['method' => 'post','class'=>'newslatter subscription_form','id'=>'subscription_form']) !!}
                                <div class="form-group">
                                    {!! Form::email('email',  old('email') , array('id' => 'email', 'class' => 'form-control', 'placeholder'=>'Email Address*')) !!}
                                    <div class="success"></div>
                                    <div class="error"></div>
                                    <!-- <label class="error"></label> -->
                                    <button class="ac-btn" title="Subscribe" >Subscribe</button>
                                </div>
                                {!! Form::close() !!}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="footer-bottom">
        <div class="container">
            <div class="row">
                <div class="col-lg-6 col-md-12 col-sm-12 col-xs-12" data-aos="fade-right" data-aos-offset="0">
                    <div class="f-m_copyright">Copyright &copy; <?php echo date("Y"); ?> {{ Config::get("Constant.SITE_NAME") }}. All Rights Reserved.</div>
                </div>
                <div class="col-lg-3 col-md-12 col-sm-12 col-xs-12" data-aos="fade-in" data-aos-offset="0">
                    <ul class="f-m_link">
                        @php
                        $var_privacy_policy=\App\Helpers\static_block::get_page_title('10');
                        $class='';
                        @endphp 
                        @if(Request::segment(1) == 'privacy-policy')
                        @php $class='active';@endphp
                        @endif
                        @if(isset($var_privacy_policy) && count($var_privacy_policy) > 0)
                        <li><a class="{{$class}}" href="{{ url($var_privacy_policy['data']->varAlias) }}" title="Privacy Policy">Privacy Policy</a></li>
                        @endif
                        @php
                        $var_sitemap=\App\Helpers\static_block::get_page_title('15');
                        $class1='';
                        @endphp 
                        @if(Request::segment(1) == 'site-map')
                        @php $class1='active';@endphp
                        @endif
                        {{-- @if(isset($var_sitemap) && count($var_sitemap) > 0)
                        <li><a class="{{$class1}}" href="{{ url('/site-map') }}" title="Site Map">Site Map</a></li>
                        @endif --}}
                        <li><a @if(Request::segment(1) == 'site-map') class="active" @endif title="Site Map" href="{{ url('/site-map') }}">Site Map</a></li>
                    </ul>
                </div>
                <div class="col-lg-3 col-md-12 col-sm-12 col-xs-12" data-aos="fade-left" data-aos-offset="0">
                    <div class="f-m_designed">Crafted By: <a class="f-m_d_logo" href="https://www.netclues.ky" target="_blank" rel="nofollow" title="Netclues!"></a></div>
                </div>
            </div>

        </div>
    </div>
</footer>
@if(Config::get('Constant.DEFAULT_FEEDBACKFORM') == "Y")
<div class="feedback_form" >
    <div style="top:calc(50% - 62px);"  class="feedback_icon" id='feedback_form_model' title="Feedback" data-toggle="modal" data-target="#exampleModal"><i class="fa fa-comments"></i></div>
</div>
@endif