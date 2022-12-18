@extends('layouts.app')
@section('content')
@include('layouts.inner_banner')
@if(!empty($boat))
<section>
    <div class="inner-page-container cms boat_detail">
        <div class="container">
            <!-- Main Section S -->
            <div class="row">
                @php
                $arrayforpictures = explode(",",$boat->fkIntImgId);
                @endphp
                <div class="col-md-12 col-xs-12 px-lg-0" data-aos="fade-up">
                    <div class="cms boat-detail-content">
                        <a href="javascript:;" data-fancybox-trigger="preview" class="ac-btn" title="Show All Photo">
                            <span class="icon" >
                                <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 15 15">
                                    <g id="Group_6077" data-name="Group 6077" transform="translate(-9397 -5750)">
                                        <rect id="Rectangle_577" data-name="Rectangle 577" width="2.5" height="2.5" transform="translate(9397 5750)" fill="#fff" />
                                        <rect id="Rectangle_582" data-name="Rectangle 582" width="2.5" height="2.5" transform="translate(9397 5756.25)" fill="#fff" />
                                        <rect id="Rectangle_585" data-name="Rectangle 585" width="2.5" height="2.5" transform="translate(9397 5762.5)" fill="#fff" />
                                        <rect id="Rectangle_578" data-name="Rectangle 578" width="2.5" height="2.5" transform="translate(9403.25 5750)" fill="#fff" />
                                        <rect id="Rectangle_581" data-name="Rectangle 581" width="2.5" height="2.5" transform="translate(9403.25 5756.25)" fill="#fff" />
                                        <rect id="Rectangle_584" data-name="Rectangle 584" width="2.5" height="2.5" transform="translate(9403.25 5762.5)" fill="#fff" />
                                        <rect id="Rectangle_579" data-name="Rectangle 579" width="2.5" height="2.5" transform="translate(9409.5 5750)" fill="#fff" />
                                        <rect id="Rectangle_580" data-name="Rectangle 580" width="2.5" height="2.5" transform="translate(9409.5 5756.25)" fill="#fff" />
                                        <rect id="Rectangle_583" data-name="Rectangle 583" width="2.5" height="2.5" transform="translate(9409.5 5762.5)" fill="#fff" />
                                    </g>
                                </svg>
                            </span> Show All Photo
                        </a>
                        <div class="image-box">
                            @foreach($arrayforpictures as $key=>$pictureid)
                            @if($key < 5) @php 
                            $width=(isset($key) && $key < 2 ? 700 : 344); 
                            $height=(isset($key) && $key < 2 ? 473 : 230); @endphp 
                            <div class="image">
                                <div class="thumbnail-container">
                                    <div class="thumbnail">
                                        <a data-fancybox-index="{{$key}}" title="Click here to zoom">
                                            <picture>
                                                <source type="image/webp"
                                                    data-srcset="{!! App\Helpers\LoadWebpImage::resize($pictureid,$width,$height) !!}"
                                                    srcset="{!! App\Helpers\LoadWebpImage::resize($pictureid,$width,$height) !!}">
                                                <img class="lazy"
                                                    data-src="{{ App\Helpers\resize_image::resize($pictureid,$width,$height)}}"
                                                    src="{{ App\Helpers\resize_image::resize($pictureid,97,75)}}"
                                                    alt="{{ htmlspecialchars_decode($boat->varTitle) }}"
                                                    title="{{ htmlspecialchars_decode($boat->varTitle) }}">
                                            </picture>
                                            <span class="mask">
                                                <img src="{{ url('/') }}/{{ ('assets/images/plus.svg') }}" alt="plus">
                                            </span>
                                        </a>
                                    </div>
                                </div>
                        </div>
                        @endif
                        @endforeach
                        @foreach($arrayforpictures as $key=>$pictureid)
                        <div style="display:none;">
                            <a href="{{ App\Helpers\resize_image::resize($pictureid)}}"
                                data-fancybox="gallery">
                                <picture>
                                    <source type="image/webp"
                                        data-srcset="{!! App\Helpers\LoadWebpImage::resize($pictureid) !!}"
                                        srcset="{!! App\Helpers\LoadWebpImage::resize($pictureid) !!}">
                                    <img class="lazy"
                                        data-src="{{ App\Helpers\resize_image::resize($pictureid)}}"
                                        src="{{ App\Helpers\resize_image::resize($pictureid,97,75)}}"
                                        alt="{{ htmlspecialchars_decode($boat->varTitle) }}"
                                        title="{{ htmlspecialchars_decode($boat->varTitle) }}">
                                </picture>
                            </a>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
            <div class="row w-100 boat-content mx-0 px-0">
                <div class="col-lg-8 col-sm-12  p-lg-0 p-sm-3 pr-lg-4">
                    @if(isset($txtDescription['response']) && !empty($txtDescription['response']) &&
                    $txtDescription['response'] != '[]')
                    {!!$txtDescription['response']!!}
                    @else
                    <div class="boat_title_price">
                        <div class="location d-flex align-items-center"><img src="{{ url('/') }}/{{ ('assets/images/location.svg') }}" alt="Location"> {!! htmlspecialchars_decode($boat->varBoatLocation) !!}
                         
                        </div>
                           @php 
                                $boat_stock = DB::table('stock')->select('varTitle')->where('chrPublish', 'Y')->where('chrDelete', 'N')->where('id', $boat->intBoatStockId)->first();
                                $boat_stock = $boat_stock->varTitle;
                                $boat_stock_class = "";
                            @endphp
                            @if(!empty($boat_stock) && $boat_stock == "Available to Order")
                                @php $boat_stock_class = "available"; @endphp
                            @elseif (!empty($boat_stock) && $boat_stock == "Sold")
                                @php $boat_stock_class = "sold"; @endphp
                            @elseif (!empty($boat_stock) && $boat_stock == "Available")
                                @php $boat_stock_class = "in-stock"; @endphp
                            @elseif (!empty($boat_stock) && $boat_stock == "Coming Soon")
                                @php $boat_stock_class = "comingsoon"; @endphp
                            @elseif (!empty($boat_stock) && $boat_stock == "Sale Pending")
                                @php $boat_stock_class = "salepending"; @endphp
                            @else
                                @php $boat_stock_class = "available"; @endphp
                            @endif
                            
                        <h3 class="cm-title">{!!$boat->varTitle!!} <span class="{{ $boat_stock_class }} boat-detail-status">{{ (!empty($boat_stock) ? $boat_stock : '') }}</span></h3>
                        @if(isset($boat->intPrice) && !empty($boat->intPrice))


                        <p class="price-tag">US${!! htmlspecialchars_decode(number_format($boat->intPrice)) !!}</p>
                        @endif
                    </div>


                    @if(isset($boat->txtDescriptionnew) && !empty($boat->txtDescriptionnew))
                    <div id="accordion">
                        <div class="card">
                            <div class="card-header" id="headingOne">
                                <h5 class="mb-0">
                                    <button class="btn" data-toggle="collapse" data-target="#collapseOne"
                                        aria-expanded="true" aria-controls="collapseOne">
                                        Description
                                    </button>
                                </h5>
                            </div>
                            <div id="collapseOne" class="collapse show" aria-labelledby="headingOne"
                                data-parent="#accordion">
                                <div class="card-body">
                                    {!! $boat->txtDescriptionnew !!}
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif

                    <div id="accordion">
                        <div class="card">
                            <div class="card-header" id="headingTwo">
                                <h5 class="mb-0">
                                    <button class="btn" data-toggle="collapse" data-target="#collapseTwo"
                                        aria-expanded="false" aria-controls="collapseTwo">
                                        Basics
                                    </button>
                                </h5>
                            </div>

                            <div id="collapseTwo" class="collapse " aria-labelledby="headingTwo"
                                data-parent="#accordion">
                                <div class="card-body list">
                                    @if(isset($boat->varHullMaterial) && !empty($boat->varHullMaterial))
                                    <div class="list__item">Hull Material : <span>{!! htmlspecialchars_decode($boat->varHullMaterial)
                                            !!}</span></div>
                                    @endif
                                    @if(isset($boat->varHullShape) && !empty($boat->varHullShape))
                                    <div class="list__item">Hull Shape : <span>{!! htmlspecialchars_decode($boat->varHullShape) !!}</span>
                                    </div>
                                    @endif
                                    @if(isset($boat->varHullWarranty) && !empty($boat->varHullWarranty))
                                    <div class="list__item">Hull Warranty : <span>{!! htmlspecialchars_decode($boat->varHullWarranty)
                                            !!}</span></div>
                                    @endif

                                    @if(isset($boat->yearYear) && !empty($boat->yearYear))
                                    <div class="list__item">Year : <span>{!! htmlspecialchars_decode($boat->yearYear) !!}</span></div>
                                    @endif

                                    @if(isset($boat->varModel) && !empty($boat->varModel))
                                    <div class="list__item">Model : <span>{!! htmlspecialchars_decode($boat->varModel) !!}</span></div>
                                    @endif

                                    @if(isset($boat->varLength) && !empty($boat->varLength))
                                    <div class="list__item">Length : <span>{!! htmlspecialchars_decode($boat->varLength) !!} ft</span></div>
                                    @endif


                                    @if(isset($boat->intBoatBrandId) && !empty($boat->intBoatBrandId))
                                    @php
                                    $result =
                                    DB::table('brand')->select('varTitle')->where('id',$boat->intBoatBrandId)->where('chrPublish','Y')->where('chrDelete','N')->first();
                                    @endphp
                                    <div class="list__item">Brand : <span>{!! $result->varTitle !!}</span></div>
                                    @endif

                                    @if(isset($boat->intBoatFuelId) && !empty($boat->intBoatFuelId))
                                    @php
                                    $result =
                                    DB::table('boat_fuel_type')->select('varTitle')->where('id',$boat->intBoatFuelId)->where('chrPublish','Y')->where('chrDelete','N')->first();
                                    @endphp
                                    <div class="list__item">Boat Fuel Type : <span>{!! $result->varTitle !!}</span></div>
                                    @endif


                                    @if(isset($boat->intBoatCategoryId) && !empty($boat->intBoatCategoryId))
                                    @php
                                    $result =
                                    DB::table('boat_category')->select('varTitle')->where('id',$boat->intBoatCategoryId)->where('chrPublish','Y')->where('chrDelete','N')->first();
                                    @endphp
                                    <div class="list__item">Boat Category : <span>{!! $result->varTitle !!}</span></div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    @if(isset($boat->txtOtherdetails) && !empty($boat->txtOtherdetails))
                    <div id="accordion">
                        <div class="card">
                            <div class="card-header" id="headingThree">
                                <h5 class="mb-0">
                                    <button class="btn" data-toggle="collapse" data-target="#collapseThree"
                                        aria-expanded="false" aria-controls="collapseThree">
                                        Other Details
                                    </button>
                                </h5>
                            </div>

                            <div id="collapseThree" class="collapse " aria-labelledby="headingThree"
                                data-parent="#accordion">
                                <div class="card-body">
                                    {!! $boat->txtOtherdetails !!}
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif
                    @if( !empty($boat->varLengthOverall) || !empty($boat->varBridgeclearance)||
                    !empty($boat->varMaxDraft)|| !empty($boat->varBeam)|| !empty($boat->varLengthAtWaterline)||
                    !empty($boat->varCabinHeadroom) || !empty($boat->varCruisingSpeed) || !empty($boat->varMaxSpeed) ||
                    !empty($boat->varDryWeight) || !empty($boat->varWindlass) || !empty($boat->varDeadriseAtTransom) ||
                    !empty($boat->varElectricalCircuit) || !empty($boat->varSeatingCapacity) ||
                    !empty($boat->varFreshWaterTank) || !empty($boat->varFuelTank) || !empty($boat->varHoldingTank) ||
                    !empty($boat->varSingleBerths) || !empty($boat->varHeads) )
                    <div id="accordion">
                        <div class="card">
                            <div class="card-header" id="headingFour">
                                <h5 class="mb-0">
                                    <button class="btn" data-toggle="collapse" data-target="#collapseFour"
                                        aria-expanded="false" aria-controls="collapseFour">
                                        Specifications
                                    </button>
                                </h5>
                            </div>
                            <div id="collapseFour" class="collapse" aria-labelledby="headingFour"
                                data-parent="#accordion">
                                <div class="card-body list-block">
                                    @if( !empty($boat->varCruisingSpeed) || !empty($boat->varMaxSpeed))
                                    <div class="list-content">
                                        <h5>Speed & Distance</h5>
                                        <ul>
                                            @if(isset($boat->varCruisingSpeed) && !empty($boat->varCruisingSpeed))
                                            <li>Cruising Speed : <span>{!!
                                                    htmlspecialchars_decode($boat->varCruisingSpeed) !!}</span></li>
                                            @endif
                                            @if(isset($boat->varMaxSpeed) && !empty($boat->varMaxSpeed))
                                            <li>Max Speed : <span>{!! htmlspecialchars_decode($boat->varMaxSpeed) !!}</span>
                                            </li>
                                            @endif
                                        </ul>
                                    </div>
                                    @endif
                                    @if( !empty($boat->varLengthOverall) || !empty($boat->varBridgeclearance)||
                                    !empty($boat->varMaxDraft)|| !empty($boat->varBeam)||
                                    !empty($boat->varLengthAtWaterline)|| !empty($boat->varCabinHeadroom))
                                    <div class="list-content">
                                        <h5>Dimensions</h5>
                                        <ul>
                                            @if(isset($boat->varLengthOverall) && !empty($boat->varLengthOverall))
                                            <li>Length Overall : <span>{!! htmlspecialchars_decode($boat->varLengthOverall)
                                                    !!}</span></li>
                                            @endif
                                            @if(isset($boat->varBridgeclearance) && !empty($boat->varBridgeclearance))
                                            <li>Max Bridge Clearance : <span>{!!
                                                    htmlspecialchars_decode($boat->varBridgeclearance) !!}</span></li>
                                            @endif
                                            @if(isset($boat->varMaxDraft) && !empty($boat->varMaxDraft))
                                            <li>Max Draft : <span>{!! htmlspecialchars_decode($boat->varMaxDraft) !!}</span>
                                            </li>
                                            @endif
                                            @if(isset($boat->varBeam) && !empty($boat->varBeam))
                                            <li>Beam : <span>{!! htmlspecialchars_decode($boat->varBeam) !!}</span></li>
                                            @endif
                                            @if(isset($boat->varCabinHeadroom) && !empty($boat->varCabinHeadroom))
                                            <li>Cabin Headroom : <span>{!! htmlspecialchars_decode($boat->varCabinHeadroom)
                                                    !!}</span></li>
                                            @endif
                                            @if(isset($boat->varLengthAtWaterline) && !empty($boat->varLengthAtWaterline))
                                            <li>Length at Waterline : <span>{!!
                                                    htmlspecialchars_decode($boat->varLengthAtWaterline) !!}</span></li>
                                            @endif
                                        </ul>   
                                    </div>
                                    @endif
                                    @if( !empty($boat->varDryWeight) )
                                    <div class="list-content">
                                        <h5>Weights</h5>
                                        <ul>
                                            @if(isset($boat->varDryWeight) && !empty($boat->varDryWeight))
                                            <li>Dry Weight : <span>{!! htmlspecialchars_decode($boat->varDryWeight)
                                                    !!}</span></li>
                                            @endif
                                        </ul>   
                                    </div>
                                    @endif
                                    @if( !empty($boat->varWindlass) || !empty($boat->varDeadriseAtTransom) ||
                                    !empty($boat->varElectricalCircuit) || !empty($boat->varSeatingCapacity))
                                    <div class="list-content">
                                        <h5>Miscellaneous</h5>
                                        <ul>
                                            @if(isset($boat->varWindlass) && !empty($boat->varWindlass))
                                            <li>Windlass : <span>{!! htmlspecialchars_decode($boat->varWindlass) !!}</span>
                                            </li>
                                            @endif

                                            @if(isset($boat->varDeadriseAtTransom) && !empty($boat->varDeadriseAtTransom))
                                            <li>Deadrise at Transom : <span>{!!
                                                    htmlspecialchars_decode($boat->varDeadriseAtTransom) !!}</span></li>
                                            @endif

                                            @if(isset($boat->varElectricalCircuit) && !empty($boat->varElectricalCircuit))
                                            <li>Electrical Circuit : <span>{!!
                                                    htmlspecialchars_decode($boat->varElectricalCircuit) !!}</span></li>
                                            @endif
                                            @if(isset($boat->varSeatingCapacity) && !empty($boat->varSeatingCapacity))
                                            <li>Seating Capacity : <span>{!!
                                                    htmlspecialchars_decode($boat->varSeatingCapacity) !!}</span></li>
                                            @endif
                                        </ul>   
                                    </div>
                                    @endif
                                    @if( !empty($boat->varFreshWaterTank) || !empty($boat->varFuelTank) ||
                                    !empty($boat->varHoldingTank) )
                                    <div class="list-content">
                                        <h5>Tanks</h5>
                                        <ul>
                                            @if(isset($boat->varFreshWaterTank) && !empty($boat->varFreshWaterTank))
                                            <li>Fresh Water Tank : <span>{!!
                                                    htmlspecialchars_decode($boat->varFreshWaterTank) !!}</span></li>
                                            @endif

                                            @if(isset($boat->varFuelTank) && !empty($boat->varFuelTank))
                                            <li>Fuel Tank : <span>{!! htmlspecialchars_decode($boat->varFuelTank) !!}</span>
                                            </li>
                                            @endif

                                            @if(isset($boat->varHoldingTank) && !empty($boat->varHoldingTank))
                                            <li>Holding Tank : <span>{!! htmlspecialchars_decode($boat->varHoldingTank)
                                                    !!}</span></li>
                                            @endif
                                        </ul>   
                                    </div>
                                    @endif
                                    @if( !empty($boat->varSingleBerths) || !empty($boat->varHeads) )
                                    <div class="list-content">
                                        <h5>Accommodations</h5>
                                        <ul>
                                            @if(isset($boat->varSingleBerths) && !empty($boat->varSingleBerths))
                                            <li>Single Berths : <span>{!! htmlspecialchars_decode($boat->varSingleBerths)
                                                    !!}</span></li>
                                            @endif

                                            @if(isset($boat->varHeads) && !empty($boat->varHeads))
                                            <li>Heads : <span>{!! htmlspecialchars_decode($boat->varHeads) !!}</span></li>
                                            @endif
                                        </ul>   
                                    </div>
                                    @endif
                                </div>
                            </div>

                        </div>
                    </div>
                    @endif
                    @endif
                </div>
                <div class="col-lg-4 pl-xl-5 pr-lg-0">
                    <div class="boat-panel col-12">
                        
                        {!! Form::open(['method' => 'post','class'=>'ac-form mx-0 w-xl-100',
                        'id'=>'contact_page_form','url'=>'/boatinquiry', 'autocomplete' => 'off'] ) !!}
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
                            
                            <select class="selectpicker ac-bootstrap-select form-control" name="boats">
                                <option value=''>Choose the option*</option>
                                @php
                                $responseallboat =
                                DB::table('boat')->select('id','varTitle')->where('chrPublish','Y')->where('chrDelete','N')->get();
                                $formisselected= "";
                                @endphp
                                @foreach($responseallboat as $boatdetail)
                                @if($boat->id == $boatdetail->id)
                                @php
                                $formisselected = "selected";
                                @endphp
                                @else
                                @php
                                $formisselected = "";
                                @endphp
                                @endif
                                <option value='{{$boatdetail->id}}' {{$formisselected}}>{{$boatdetail->varTitle}}
                                </option>
                                @endforeach
                            </select>
                            @if ($errors->has('boats'))
                            <span class="error">{{ $errors->first('boats') }}</span>
                            @endif
                        </div>
                        <div class="form-group">
                            
                            {!! Form::textarea('user_message', old('user_message'), array('class'=>'form-control mt-2
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

        <!-- </div> -->
    </div>

    @if(isset($similarBoat) && count($similarBoat) > 0)
    <section class="boat_sec" data-aos="fade-up">
        <div class="container-fluid">
            <h2 class="text-capitalize cm-title mt-3 mb-4 mb-lg-5" data-aos="fade-up">Explore Other Boats</h2>
            <div class="boat-card-slider swiper">
                <div class="swiper-wrapper">
                    @foreach($similarBoat as $index => $boat)
                    @php
                    if(isset(App\Helpers\MyLibrary::getFront_Uri('boat')['uri'])){
                    $moduelFrontPageUrl = App\Helpers\MyLibrary::getFront_Uri('boat')['uri'];
                    $moduleFrontWithCatUrl = ($boat->varAlias != false ) ? $moduelFrontPageUrl . '/' . $boat->varAlias :
                    $moduelFrontPageUrl;
                    $recordLinkUrl = $moduleFrontWithCatUrl.'/'.$boat->alias->varAlias;
                    }else{
                    $recordLinkUrl = '';
                    }
                    @endphp
                    <!-- <div class="col-lg-4 col-md-4 col-sm-12 col-12"> -->
                    <div class="swiper-slide">
                        <div class="boat_img">
                            <div class="thumbnail-container">
                                <div class="thumbnail">
                                    <picture>
                                        <source type="image/webp"
                                            data-srcset="{!! App\Helpers\LoadWebpImage::resize($boat->fkIntImgId,606,404) !!}"
                                            srcset="{!! App\Helpers\LoadWebpImage::resize($boat->fkIntImgId,606,404) !!}">
                                        <img class="lazy"
                                            data-src="{{ App\Helpers\resize_image::resize($boat->fkIntImgId,606,404)}}"
                                            src="{{ App\Helpers\resize_image::resize($boat->fkIntImgId,97,75)}}"
                                            alt="{{ htmlspecialchars_decode($boat->varTitle) }}"
                                            title="{{ htmlspecialchars_decode($boat->varTitle) }}">
                                    </picture>
                                </div>
                                @php 
                                    $boat_stock = DB::table('stock')->select('varTitle')->where('chrPublish', 'Y')->where('chrDelete', 'N')->where('id', $boat->intBoatStockId)->first();
                                    $boat_stock = $boat_stock->varTitle;
                                    $boat_stock_class = "";
                                @endphp
                                @if(!empty($boat_stock) && $boat_stock == "Available to Order")
                                    @php $boat_stock_class = "available"; @endphp
                                @elseif (!empty($boat_stock) && $boat_stock == "Sold")
                                    @php $boat_stock_class = "sold"; @endphp
                                @elseif (!empty($boat_stock) && $boat_stock == "Available")
                                    @php $boat_stock_class = "in-stock"; @endphp
                                @elseif (!empty($boat_stock) && $boat_stock == "Coming Soon")
                                    @php $boat_stock_class = "comingsoon"; @endphp
                                @elseif (!empty($boat_stock) && $boat_stock == "Sale Pending")
                                    @php $boat_stock_class = "salepending"; @endphp
                                @else
                                    @php $boat_stock_class = "available"; @endphp
                                @endif
                                <div class="{{ $boat_stock_class }} status-tag-line">{{ (!empty($boat_stock) ? $boat_stock : '') }}</div>
                            </div>
                            <span class="line"></span>
                        </div>
                        <div class="boat-desc-wrap">
                            <div class="boat_title">
                                <h4 class="title text-capitalize main-title"><a href="{{ $recordLinkUrl }}"
                                        title="{!! $boat->varTitle !!}">{!! $boat->varTitle !!}</a></h4>
                            </div>
                            <div class="boat_desc">
                                <p>US${!! number_format($boat->intPrice) !!}</p>
                                <p>Year: {!! $boat->yearYear !!}</p>
                                <p>Length: {!! $boat->varLength !!}ft</p>
                                @if(isset($boat->intBoatFuelId) && !empty($boat->intBoatFuelId))
                                @php
                                $result =
                                DB::table('boat_fuel_type')->select('varTitle')->where('id',$boat->intBoatFuelId)->where('chrPublish','Y')->where('chrDelete','N')->first();
                                @endphp
                                <p>Fuel: {!! $result->varTitle !!}</p>
                                @endif
                            </div>
                            <a href="{{ $recordLinkUrl }}" class="boat-btn" title="More Details">MORE DETAILS</a>
                        </div>
                    </div>
                    <!-- </div> -->
                    @endforeach
                </div>
                <div class="swiper-scrollbar"></div>
            </div>
            <div class="view-more text-md-right text-center">
                <a href="{{$moduelFrontPageUrl}}"  class="ac-btn" title="View All Boats">View All Boats</a>
            </div>
        </div>
    </section>
    @endif
    <!-- </div> -->
    </div>
</section>
@endif

@if(!Request::ajax())
@section('footer_scripts')
<script src="{{ url('assets/js/packages/boatinquirylead/boat-inquiry.js') }}?{{ Config::get('Constant.VERSION') }}"></script>
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