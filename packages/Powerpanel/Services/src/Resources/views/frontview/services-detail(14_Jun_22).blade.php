@extends('layouts.app')
@section('content')
@include('layouts.inner_banner')

@if(!empty($service))
<section>
    <div class="inner-page-container cms services_detail">
        <div class="container">
            <!-- Main Section S -->
            <div class="row">
                <div class="col-lg-9 col-xs-12 pr-5" data-aos="fade-up">
                    <div class="cms serv-detail-content">
                        <h3 class="cm-title">{!!$service->varTitle!!}</h3>
                        <div class="image">
                            <div class="thumbnail-container">
                                <div class="thumbnail">
                                    <picture>
                                        <source type="image/webp" data-srcset="{!! App\Helpers\LoadWebpImage::resize($service->fkIntImgId,996,446) !!}" srcset="{!! App\Helpers\LoadWebpImage::resize($service->fkIntImgId,996,446) !!}">
                                        <img class="lazy" data-src="{{ App\Helpers\resize_image::resize($service->fkIntImgId,996,446)}}" src="{!! url('assets/images/loader.gif') !!}" alt="{{ htmlspecialchars_decode($service->varTitle) }}" title="{{ htmlspecialchars_decode($service->varTitle) }}">
                                    </picture>
                                </div>
                            </div>
                        </div>
                        <div class="right_content">
                            @if(isset($txtDescription['response']) && !empty($txtDescription['response']) && $txtDescription['response'] != '[]')
                            {!!$txtDescription['response']!!}
                            @else
                            <p>{!! htmlspecialchars_decode($service->txtShortDescription) !!}</p>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 d-none d-lg-block">
                    <div class="detail-panel">
                        <div class="sticky-panel">
                            @if(isset($similarServices) && count($similarServices) > 0)
                            <div class="item rec-blog">
                                <h3 class="title-a">Our Other Services</h3>
                                <ul>
                                    @foreach($similarServices as $index => $similarServices)
                                    @php
                                    if(isset(App\Helpers\MyLibrary::getFront_Uri('Services')['uri'])){
                                    $moduelFrontPageUrl = App\Helpers\MyLibrary::getFront_Uri('Services')['uri'];
                                    $moduleFrontWithCatUrl = ($similarServices->varAlias != false ) ? $moduelFrontPageUrl . '/' . $similarServices->varAlias : $moduelFrontPageUrl;
                                    $recordLinkUrl = $moduleFrontWithCatUrl.'/'.$similarServices->alias->varAlias;
                                    }else{
                                    $recordLinkUrl = '';
                                    }
                                    @endphp
                                    <li class="active">
                                        <a href="{{ $recordLinkUrl }}" title="{{ucwords($similarServices->varTitle)}}">
                                            <div class="thumbnail-container">
                                                <div class="thumbnail">
                                                    <picture>
                                                        <source type="image/webp" data-srcset="{!! App\Helpers\LoadWebpImage::resize($similarServices->fkIntImgId,70,46) !!}" srcset="{!! App\Helpers\LoadWebpImage::resize($similarServices->fkIntImgId,70,46) !!}">
                                                        <img class="lazy" data-src="{{ App\Helpers\resize_image::resize($similarServices->fkIntImgId,70,46)}}" src="{!! url('assets/images/loader.gif') !!}" alt="{{ htmlspecialchars_decode($similarServices->varTitle) }}" title="{{ htmlspecialchars_decode($similarServices->varTitle) }}">
                                                    </picture>
                                                </div>
                                            </div>
                                            {{$similarServices->varTitle}}
                                        </a>
                                        <div class="clearfix"></div>
                                    </li>
                                    @endforeach
                                </ul>
                            </div>
                            @endif
                            <div class="item contact">
                                <img class="img-qc" src="{{ $CDN_PATH.'assets/images/form-q.svg' }}" alt="svg">
                                <h3 class="title-a">Request a Service</h3>
                                <p class="text-d">Are you ready to start building a relationship with Avalon Marine?</p>
                                <div class="text-center btn-qc">
                                    <a class="ac-btn" href="" id="service_form" title="Click Here" data-toggle="modal" data-target="#exampleModalCenter">Click Here</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>


<!-- Modal -->
<div class="modal fade" id="exampleModalCenter" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLongTitle">Request Service</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">

                <section class="inner-page-container_">
                    <div class="container">
                        <div class="row bg-color_contact">
                            <div class="col-md-12 col-lg-12 col-xl-12 r-1">
                                <div class="row d-flex align-items-center">
                                    <div class="contact-list col-12" style="height: 100%;">
                                        <!-- <h3 class="cm-title text-uppercase">Get in Touch</h3> -->

                                        {!! Form::open(['method' => 'post','class'=>'ac-form row w-xl-100','url'=>'/serviceinquiry', 'id'=>'contact_page_form', 'autocomplete' => 'off'] ) !!}

                                        <div class="col-md-12 text-right mb-md-4 mt-md-2">
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
                                                <label class="nq-label" for="contact_email">Interested In* </label>
                                                <select class="selectpicker ac-bootstrap-select form-control" name="services">
                                                    <option value=''>Choose the option</option>
                                                    <option value='0'>General Enquiry</option>
                                                    @foreach($services as $servicee)
                                                    @php
                                                    if($service->id == $servicee->id){
                                                        $sel = "selected";
                                                    }else{
                                                        $sel = "";
                                                    }
                                                    @endphp
                                                    <option value='{{$servicee->id}}' {{$sel}} >{{$servicee->varTitle}}</option>
                                                    @endforeach
                                                </select>
                                                @if ($errors->has('services'))
                                                <span class="error">{{ $errors->first('services') }}</span>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="form-group service_message">
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
                                            <div class="form-group bottom_group ">
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
            </div>

        </div>
    </div>
</div>
















@endif

@if(!Request::ajax())
@section('footer_scripts')
<script src="{{ url('assets/js/packages/serviceinquirylead/service-inquiry.js') }}?{{ Config::get('Constant.VERSION') }}"></script>
<script type="text/javascript">
    var sitekey = '{{Config::get("Constant.GOOGLE_CAPCHA_KEY")}}';
    var onContactloadCallback = function() {
        grecaptcha.render('recaptcha2', {
            'sitekey': sitekey
        });
    };
</script>
<script src="https://www.google.com/recaptcha/api.js?onload=onContactloadCallback&render=explicit" async defer></script>
@endsection
@endsection
@endif