@extends('layouts.app')
@section('content')
@include('layouts.inner_banner')

@if(!empty($service))
<section>
    <div class="inner-page-container cms services_detail">
        <div class="container">
            <!-- Main Section S -->
            <div class="row">
                <div class="col-xl-8 col-lg-8 col-xs-12 pr-xl-5 pr-lg-4" data-aos="fade-up">
                    <div class="cms serv-detail-content">
                        <h3 class="cm-title">{!!$service->varTitle!!}</h3>
                        <div class="image">
                            <div class="thumbnail-container">
                                <div class="thumbnail">
                                    <picture>
                                        <source type="image/webp" data-srcset="{!! App\Helpers\LoadWebpImage::resize($service->fkIntImgId,879,596) !!}" srcset="{!! App\Helpers\LoadWebpImage::resize($service->fkIntImgId,879,596) !!}">
                                        <img class="lazy" data-src="{{ App\Helpers\resize_image::resize($service->fkIntImgId,879,596)}}" src="{!! url('assets/images/loader.gif') !!}" alt="{{ htmlspecialchars_decode($service->varTitle) }}" title="{{ htmlspecialchars_decode($service->varTitle) }}">
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
                <div class="col-xl-4 col-lg-4 col-xs-12 mt-4 mt-lg-0">
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
                                                        <source type="image/webp" data-srcset="{!! App\Helpers\LoadWebpImage::resize($similarServices->fkIntImgId,70,58) !!}" srcset="{!! App\Helpers\LoadWebpImage::resize($similarServices->fkIntImgId,70,58) !!}">
                                                        <img class="lazy" data-src="{{ App\Helpers\resize_image::resize($similarServices->fkIntImgId,70,58)}}" src="{!! url('assets/images/loader.gif') !!}" alt="{{ htmlspecialchars_decode($similarServices->varTitle) }}" title="{{ htmlspecialchars_decode($similarServices->varTitle) }}">
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
                            <div class="boat-panel"> 
                            {!! Form::open(['method' => 'post','class'=>'ac-form mx-0 w-xl-100',
                            'id'=>'contact_page_form','url'=>'/serviceinquiry', 'autocomplete' => 'off'] ) !!}
                            <h3 class="content-title">Interested in this Boat?</h3>
                        
                            <div class="user-info d-flex align-items-center">

                                <div class="user-details">
                                    <h6 class="user-name">Avalon Marine</h6>
                                    @if(isset($objContactInfo) && !empty($objContactInfo))
                                    @php
                                    $phone = unserialize($objContactInfo[0]->varPhoneNo);
                                    $phone = count($phone)>0?$phone[0]:$phone;
                                    $phone1 = str_replace(' ','',$phone);
                                    @endphp
                                    {{--<!-- <a class="number" href="tel:{{ $phone1 }}" title="Call Us On : {{ $phone1 }}">
                                    {{ $phone }}</a><br> -->--}}
                                    <a class="number" href="tel:{{str_replace(' ','',$objContactInfo[0]->varMobileNo)}}"
                                        title="Call Us On : {{str_replace(' ','',$objContactInfo[0]->varMobileNo)}}">{{$objContactInfo[0]->varMobileNo}}</a>
                                    @endif

                                </div>
                            </div>
                            <div class="form-group">
                                
                                {!! Form::text('first_name', old('first_name'), array('id'=>'first_name',
                                'placeholder'=>'Name*', 'class'=>'form-control ac-input', 'name'=>'first_name',
                                'maxlength'=>'60', 'onpaste'=>'return false;', 'ondrop'=>'return false;')) !!}
                                @if ($errors->has('first_name'))
                                <span class="error">{{ $errors->first('first_name') }}</span>
                                @endif
                            </div>
                            <div class="form-group">
                                
                                {!! Form::email('contact_email', old('contact_email'), array('id'=>'contact_email',
                                'placeholder'=>'Email*', 'class'=>'form-control ac-input', 'name'=>'contact_email',
                                'maxlength'=>'60', 'onpaste'=>'return false;', 'ondrop'=>'return false;')) !!}
                                @if ($errors->has('contact_email'))
                                <span class="error">{{ $errors->first('contact_email') }}</span>
                                @endif
                            </div>
                            <div class="form-group">
                            
                                {!! Form::text('phone_number', old('phone_number'), array('id'=>'phone_number',
                                'placeholder'=>'Phone', 'class'=>'form-control sidePanelForm ac-input',
                                'name'=>'phone_number', 'maxlength'=>"20", 'onpaste'=>'return false;', 'ondrop'=>'return
                                false;', 'onkeypress'=>'javascript: return KeycheckOnlyPhonenumber(event);')) !!}
                                @if ($errors->has('phone_number'))
                                <span class="error">{{ $errors->first('phone_number') }}</span>
                                @endif
                            </div>
                            <div class="form-group">
                            
                            <select class="selectpicker ac-bootstrap-select form-control" name="services">
                                    <option value=''>Choose the option*</option>
                                    <option value="0">General Enquiry</option>
                                    @php
                                    $responseallboat =
                                    DB::table('services')->select('id','varTitle')->where('chrPublish','Y')->where('chrDelete','N')->get();
                                    $formisselected= "";
                                    @endphp
                                    @foreach($responseallboat as $serviceselect)
                                    @if($service->id == $serviceselect->id)
                                    @php
                                    $formisselected = "selected";
                                    @endphp
                                    @else
                                    @php
                                    $formisselected = "";
                                    @endphp
                                    @endif
                                    <option value='{{$serviceselect->id}}' {{$formisselected}}>{{$serviceselect->varTitle}}
                                    </option>
                                    @endforeach
                                </select>
                                @if ($errors->has('services'))
                                <span class="error">{{ $errors->first('services') }}</span>
                                @endif
                                
                            
                            </div>
                            <div class="form-group">
                                
                                {!! Form::textarea('user_message', old('user_message'), array('class'=>'form-control mt-4
                                ac-textarea','placeholder'=>'Comment', 'name'=>'user_message', 'rows'=>'3',
                                'id'=>'user_message', 'maxlength'=>'400', 'spellcheck'=>'true', 'onpaste'=>'return true;',
                                'ondrop'=>'return true;' )) !!}
                                @if ($errors->has('user_message'))
                                <span class="error">{{ $errors->first('user_message') }}</span>
                                @endif
                            </div>
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
                            <div class="form-group m-auto d-flex">
                                <button type="submit" class="ac-btn" title="Submit">Submit</button>
                            </div>
                            {!! Form::close() !!}
                        </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

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