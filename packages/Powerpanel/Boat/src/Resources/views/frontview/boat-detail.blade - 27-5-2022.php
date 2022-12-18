@extends('layouts.app')
@section('content')
@include('layouts.inner_banner')

@if(!empty($boat))
<section>
    <div class="inner-page-container cms boat_detail">
        <div class="container">
            <!-- Main Section S -->
            <div class="row">
                <div class="col-3 d-none d-sm-block">
                    <div class="detail-panel">
                        <!-- <div class="item contact">
                            <img class="img-qc" src="{{ $CDN_PATH.'assets/images/form-q.svg' }}" alt="svg">
                            <h3 class="title-a">Get a Free Estimate</h3>
                            <p class="text-d">Are you ready to start building a relationship with Avalon Marine?</p>
                            <div class="text-center btn-qc">
                                <a class="ac-border" href="javascript:;" id="form-container2" title="Click Here">
                                    <span class="text">Click Here</span>
                                    <span class="line"></span>
                                </a>
                            </div>
                        </div> -->
                    </div>
                </div>
                @php
                $arrayforpictures = explode(",",$boat->fkIntImgId);
                @endphp
                <div class="col-md-12 col-xs-12" data-aos="fade-up">
                    <div class="cms boat-detail-content">
                        <a href="javascript:;" data-fancybox-trigger="preview" class="ac-btn">
                            <span class="icon">
                                <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 15 15">
                                    <g id="Group_6077" data-name="Group 6077" transform="translate(-9397 -5750)">
                                        <rect id="Rectangle_577" data-name="Rectangle 577" width="2.5" height="2.5" transform="translate(9397 5750)" fill="#fff"/>
                                        <rect id="Rectangle_582" data-name="Rectangle 582" width="2.5" height="2.5" transform="translate(9397 5756.25)" fill="#fff"/>
                                        <rect id="Rectangle_585" data-name="Rectangle 585" width="2.5" height="2.5" transform="translate(9397 5762.5)" fill="#fff"/>
                                        <rect id="Rectangle_578" data-name="Rectangle 578" width="2.5" height="2.5" transform="translate(9403.25 5750)" fill="#fff"/>
                                        <rect id="Rectangle_581" data-name="Rectangle 581" width="2.5" height="2.5" transform="translate(9403.25 5756.25)" fill="#fff"/>
                                        <rect id="Rectangle_584" data-name="Rectangle 584" width="2.5" height="2.5" transform="translate(9403.25 5762.5)" fill="#fff"/>
                                        <rect id="Rectangle_579" data-name="Rectangle 579" width="2.5" height="2.5" transform="translate(9409.5 5750)" fill="#fff"/>
                                        <rect id="Rectangle_580" data-name="Rectangle 580" width="2.5" height="2.5" transform="translate(9409.5 5756.25)" fill="#fff"/>
                                        <rect id="Rectangle_583" data-name="Rectangle 583" width="2.5" height="2.5" transform="translate(9409.5 5762.5)" fill="#fff"/>
                                    </g>
                                </svg>
                            </span> Show all Photo
                        </a>

                        <div class="image-box">
                        @foreach($arrayforpictures as $key=>$pictureid)
                            @if($key < 5) 
                            <div class="image">
                                <div class="thumbnail-container">
                                    <div class="thumbnail">
                                        <a href="{{ App\Helpers\resize_image::resize($pictureid,653,292)}}" data-fancybox="gallery">
                                            <picture>
                                                <source type="image/webp" data-srcset="{!! App\Helpers\LoadWebpImage::resize($pictureid,653,292) !!}" srcset="{!! App\Helpers\LoadWebpImage::resize($pictureid,653,292) !!}">
                                                <img class="lazy" data-src="{{ App\Helpers\resize_image::resize($pictureid,653,292)}}" src="{!! url('assets/images/loader.gif') !!}" alt="{{ htmlspecialchars_decode($boat->varTitle) }}" title="{{ htmlspecialchars_decode($boat->varTitle) }}">
                                            </picture>
                                        </a>
                                    </div>
                                </div>
                            </div>
                            @else
                            <div class="image" style="display:none;">
                                <div class="thumbnail-container">
                                    <div class="thumbnail">
                                        <a href="{{ App\Helpers\resize_image::resize($pictureid,653,292)}}" data-fancybox="gallery">
                                            <picture>
                                                <source type="image/webp" data-srcset="{!! App\Helpers\LoadWebpImage::resize($pictureid,653,292) !!}" srcset="{!! App\Helpers\LoadWebpImage::resize($pictureid,653,292) !!}">
                                                <img class="lazy" data-src="{{ App\Helpers\resize_image::resize($pictureid,653,292)}}" src="{!! url('assets/images/loader.gif') !!}" alt="{{ htmlspecialchars_decode($boat->varTitle) }}" title="{{ htmlspecialchars_decode($boat->varTitle) }}">
                                            </picture>
                                        </a>
                                    </div>
                                </div>
                            </div>                            
                            @endif
                        @endforeach
                    </div>
                </div>
            </div>
            <div class="row boat-content mx-0">                
                <div class="col-md-8">
                    @if(isset($txtDescription['response']) && !empty($txtDescription['response']) && $txtDescription['response'] != '[]')
                    {!!$txtDescription['response']!!}
                    @else
                    <div class="boat_title_price">
                        <div class="location"><i class="fa fa-map-marker" aria-hidden="true"></i> {!! htmlspecialchars_decode($boat->varBoatLocation) !!}</div>
                        <h3 class="cm-title">{!!$boat->varTitle!!}</h3>
                        @if(isset($boat->intPrice) && !empty($boat->intPrice))
                        <p class="price-tag">US${!! htmlspecialchars_decode($boat->intPrice) !!}</p>
                        @endif
                    </div>


                    <!-- @if(isset($boat->varBoatLocation) && !empty($boat->varBoatLocation))
                    <p>Location : {!! htmlspecialchars_decode($boat->varBoatLocation) !!}</p>
                    @endif -->

                    <!-- @if(isset($boat->intBoatconditionId) && !empty($boat->intBoatconditionId))
                    @php
                    $result = DB::table('boat_condition')->select('varTitle')->where('id',$boat->intBoatconditionId)->where('chrPublish','Y')->where('chrDelete','N')->first();
                    @endphp
                    <p>BOAT CONDITION : {!! $result->varTitle !!}</p>
                    @endif
                    @if(isset($boat->intBoatStockId) && !empty($boat->intBoatStockId))
                    @php
                    $result = DB::table('stock')->select('varTitle')->where('id',$boat->intBoatStockId)->where('chrPublish','Y')->where('chrDelete','N')->first();
                    @endphp
                    <p>SELECT STOCK : {!! $result->varTitle !!}</p>
                    @endif -->


                    @if(isset($boat->txtShortDescription) && !empty($boat->txtShortDescription))
                    <div id="accordion">
                        <div class="card">
                            <div class="card-header" id="headingOne">
                                <h5 class="mb-0">                                
                                    <button class="btn" data-toggle="collapse" data-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                                        Description
                                    </button>
                                </h5>
                            </div>

                            <div id="collapseOne" class="collapse show" aria-labelledby="headingOne" data-parent="#accordion">
                                <div class="card-body">
                                    {!! $boat->txtShortDescription !!}
                                </div>
                            </div>
                        </div>
                    <!-- </div> -->
                    @endif

                    <!-- <div id="accordion"> -->
                        <div class="card">
                            <div class="card-header" id="headingTwo">
                                <h5 class="mb-0">
                                    <button class="btn" data-toggle="collapse" data-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                                        Basics
                                    </button>
                                </h5>
                            </div>

                            <div id="collapseTwo" class="collapse " aria-labelledby="headingTwo" data-parent="#accordion">
                                <div class="card-body">
                                    @if(isset($boat->varHullMaterial) && !empty($boat->varHullMaterial))
                                    <p>HULL MATERIAL : {!! htmlspecialchars_decode($boat->varHullMaterial) !!}</p>
                                    @endif
                                    @if(isset($boat->varHullShape) && !empty($boat->varHullShape))
                                    <p>HULL MATERIAL : {!! htmlspecialchars_decode($boat->varHullShape) !!}</p>
                                    @endif
                                    @if(isset($boat->varHullWarranty) && !empty($boat->varHullWarranty))
                                    <p>HULL MATERIAL : {!! htmlspecialchars_decode($boat->varHullWarranty) !!}</p>
                                    @endif

                                    @if(isset($boat->yearYear) && !empty($boat->yearYear))
                                    <p>YEAR : {!! htmlspecialchars_decode($boat->yearYear) !!}</p>
                                    @endif

                                    @if(isset($boat->varModel) && !empty($boat->varModel))
                                    <p>MODEL : {!! htmlspecialchars_decode($boat->varModel) !!}</p>
                                    @endif

                                    @if(isset($boat->varLength) && !empty($boat->varLength))
                                    <p>LENGTH : {!! htmlspecialchars_decode($boat->varLength) !!}</p>
                                    @endif


                                    @if(isset($boat->intBoatBrandId) && !empty($boat->intBoatBrandId))
                                    @php
                                    $result = DB::table('brand')->select('varTitle')->where('id',$boat->intBoatBrandId)->where('chrPublish','Y')->where('chrDelete','N')->first();
                                    @endphp
                                    <p>BRAND : {!! $result->varTitle !!}</p>
                                    @endif

                                    @if(isset($boat->intBoatFuelId) && !empty($boat->intBoatFuelId))
                                    @php
                                    $result = DB::table('boat_fuel_type')->select('varTitle')->where('id',$boat->intBoatFuelId)->where('chrPublish','Y')->where('chrDelete','N')->first();
                                    @endphp
                                    <p>BOAT FUEL TYPE : {!! $result->varTitle !!}</p>
                                    @endif


                                    @if(isset($boat->intBoatCategoryId) && !empty($boat->intBoatCategoryId))
                                    @php
                                    $result = DB::table('boat_category')->select('varTitle')->where('id',$boat->intBoatCategoryId)->where('chrPublish','Y')->where('chrDelete','N')->first();
                                    @endphp
                                    <p>BOAT CATEGORY : {!! $result->varTitle !!}</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                    <!-- </div> -->

                    @if(isset($boat->txtOtherdetails) && !empty($boat->txtOtherdetails))
                    <!-- <div id="accordion"> -->
                        <div class="card">
                            <div class="card-header" id="headingThree">
                                <h5 class="mb-0">
                                    <button class="btn" data-toggle="collapse" data-target="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
                                        Other Details
                                    </button>
                                </h5>
                            </div>

                            <div id="collapseThree" class="collapse " aria-labelledby="headingThree" data-parent="#accordion">
                                <div class="card-body">
                                    {!! $boat->txtOtherdetails !!}
                                </div>
                            </div>
                        </div>
                    <!-- </div> -->
                    @endif

                    <!-- <div id="accordion"> -->
                        <div class="card">
                            <div class="card-header" id="headingFour">
                                <h5 class="mb-0">
                                    <button class="btn" data-toggle="collapse" data-target="#collapseFour" aria-expanded="false" aria-controls="collapseFour">
                                        Specification
                                    </button>
                                </h5>
                            </div>

                            <div id="collapseFour" class="collapse" aria-labelledby="headingFour" data-parent="#accordion">
                                <div class="card-body">
                                    @if( !empty($boat->varCruisingSpeed) || !empty($boat->varMaxSpeed))
                                    <div>
                                        Speed & Distance
                                        @if(isset($boat->varCruisingSpeed) && !empty($boat->varCruisingSpeed))
                                        <p>CRUISING SPEED : {!! htmlspecialchars_decode($boat->varCruisingSpeed) !!}</p>
                                        @endif
                                        @if(isset($boat->varMaxSpeed) && !empty($boat->varMaxSpeed))
                                        <p>MAX SPEED : {!! htmlspecialchars_decode($boat->varMaxSpeed) !!}</p>
                                        @endif
                                    </div>
                                    @endif


                                    @if( !empty($boat->varLengthOverall) || !empty($boat->varBridgeclearance)|| !empty($boat->varMaxDraft)|| !empty($boat->varBeam)|| !empty($boat->varLengthAtWaterline)|| !empty($boat->varCabinHeadroom))
                                    <div>
                                        Dimensions
                                        @if(isset($boat->varLengthOverall) && !empty($boat->varLengthOverall))
                                        <p>LENGTH OVERALL : {!! htmlspecialchars_decode($boat->varLengthOverall) !!}</p>
                                        @endif
                                        @if(isset($boat->varBridgeclearance) && !empty($boat->varBridgeclearance))
                                        <p>MAX BRIDGE CLEARANCE : {!! htmlspecialchars_decode($boat->varBridgeclearance) !!}</p>
                                        @endif
                                        @if(isset($boat->varMaxDraft) && !empty($boat->varMaxDraft))
                                        <p>MAX DRAFT : {!! htmlspecialchars_decode($boat->varMaxDraft) !!}</p>
                                        @endif
                                        @if(isset($boat->varBeam) && !empty($boat->varBeam))
                                        <p>BEAM : {!! htmlspecialchars_decode($boat->varBeam) !!}</p>
                                        @endif
                                        @if(isset($boat->varCabinHeadroom) && !empty($boat->varCabinHeadroom))
                                        <p>CABIN HEADROOM : {!! htmlspecialchars_decode($boat->varCabinHeadroom) !!}</p>
                                        @endif
                                        @if(isset($boat->varLengthAtWaterline) && !empty($boat->varLengthAtWaterline))
                                        <p>LENGTH AT WATERLINE : {!! htmlspecialchars_decode($boat->varLengthAtWaterline) !!}</p>
                                        @endif
                                    </div>
                                    @endif

                                    @if( !empty($boat->varDryWeight) )
                                    <div>
                                        Weights
                                        @if(isset($boat->varDryWeight) && !empty($boat->varDryWeight))
                                        <p>DRY WEIGHT : {!! htmlspecialchars_decode($boat->varDryWeight) !!}</p>
                                        @endif
                                    </div>
                                    @endif
                                    @if( !empty($boat->varWindlass) || !empty($boat->varDeadriseAtTransom) || !empty($boat->varElectricalCircuit) || !empty($boat->varSeatingCapacity))
                                    <div>
                                        Miscellaneous
                                        @if(isset($boat->varWindlass) && !empty($boat->varWindlass))
                                        <p>WINDLASS : {!! htmlspecialchars_decode($boat->varWindlass) !!}</p>
                                        @endif

                                        @if(isset($boat->varDeadriseAtTransom) && !empty($boat->varDeadriseAtTransom))
                                        <p>DEADRISE AT TRANSOM : {!! htmlspecialchars_decode($boat->varDeadriseAtTransom) !!}</p>
                                        @endif

                                        @if(isset($boat->varElectricalCircuit) && !empty($boat->varElectricalCircuit))
                                        <p>ELECTRICAL CIRCUIT : {!! htmlspecialchars_decode($boat->varElectricalCircuit) !!}</p>
                                        @endif
                                        @if(isset($boat->varSeatingCapacity) && !empty($boat->varSeatingCapacity))
                                        <p>Seating Capacity : {!! htmlspecialchars_decode($boat->varSeatingCapacity) !!}</p>
                                        @endif
                                    </div>
                                    @endif
                                    @if( !empty($boat->varFreshWaterTank) || !empty($boat->varFuelTank) || !empty($boat->varHoldingTank) )
                                    <div>
                                        Tanks
                                        @if(isset($boat->varFreshWaterTank) && !empty($boat->varFreshWaterTank))
                                        <p>FRESH WATER TANK : {!! htmlspecialchars_decode($boat->varFreshWaterTank) !!}</p>
                                        @endif

                                        @if(isset($boat->varFuelTank) && !empty($boat->varFuelTank))
                                        <p>FUEL TANK : {!! htmlspecialchars_decode($boat->varFuelTank) !!}</p>
                                        @endif

                                        @if(isset($boat->varHoldingTank) && !empty($boat->varHoldingTank))
                                        <p>HOLDING TANK : {!! htmlspecialchars_decode($boat->varHoldingTank) !!}</p>
                                        @endif
                                    </div>
                                    @endif
                                    @if( !empty($boat->varSingleBerths) || !empty($boat->varHeads) )
                                    <div>
                                        Accommodations
                                        @if(isset($boat->varSingleBerths) && !empty($boat->varSingleBerths))
                                        <p>SINGLE BERTHS : {!! htmlspecialchars_decode($boat->varSingleBerths) !!}</p>
                                        @endif

                                        @if(isset($boat->varHeads) && !empty($boat->varHeads))
                                        <p>HEADS : {!! htmlspecialchars_decode($boat->varHeads) !!}</p>
                                        @endif
                                    </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    @endif
                </div>
                <div class="d-flex row align-items-center col-md-4">
                    <div class="contact-list col-12" style="height: 100%;">
                        <!-- <h3 class="cm-title text-uppercase">Get in Touch</h3> -->

                        {!! Form::open(['method' => 'post','class'=>'ac-form row w-xl-100', 'id'=>'contact_page_form','url'=>'/boatinquiry', 'autocomplete' => 'off'] ) !!}
                        <h3>Intrested in this Boat?</h3>
                        <div class="col-md-12 text-right mb-4 mt-5">
                            <div class="required">* Denotes Required Inputs</div>
                        </div>
                        <div class="col-md-12 col-lg-12">
                            <div class="form-group">
                                <label class="nq-label" for="first_name">Name<span class="star">*</span></label>
                                {!! Form::text('first_name', old('first_name'), array('id'=>'first_name', 'class'=>'form-control ac-input', 'name'=>'first_name', 'maxlength'=>'60', 'onpaste'=>'return false;', 'ondrop'=>'return false;')) !!}
                                @if ($errors->has('first_name'))
                                <span class="error">{{ $errors->first('first_name') }}</span>
                                @endif
                            </div>
                        </div>
                        <div class="col-md-12 col-lg-12" style="display: none;">
                            <div class="form-group">
                                <label class="nq-label" for="last_name">Last Name<span class="star">*</span></label>
                                {!! Form::text('last_name', old('last_name'), array('id'=>'last_name', 'class'=>'form-control ac-input', 'name'=>'last_name', 'maxlength'=>'60', 'onpaste'=>'return false;', 'ondrop'=>'return false;')) !!}
                                @if ($errors->has('last_name'))
                                <span class="error">{{ $errors->first('last_name') }}</span>
                                @endif
                            </div>
                        </div>

            </div>
            <div class="row d-flex align-items-center col-md-4">
                <div class="contact-list col-12" style="height: 100%;">
                    <!-- <h3 class="cm-title text-uppercase">Get in Touch</h3> -->

                    {!! Form::open(['method' => 'post','class'=>'ac-form row w-xl-100', 'id'=>'contact_page_form','url'=>'/boatinquiry', 'autocomplete' => 'off'] ) !!}
                                @endif
                            </div>
                        </div>
                        <div class="col-md-12 col-lg-12">
                            <div class="form-group">
                                <label class="nq-label" for="contact_email">Interested In</label>
                                <select class="selectpicker ac-bootstrap-select form-control" name="boats">
                                    <option value=''>Select Interested In</option>
                                    <option value='0'>General Enquiry</option>
                                    @php
                                    $responseallboat = DB::table('boat')->select('id','varTitle')->where('chrPublish','Y')->where('chrDelete','N')->get();
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
                                    <option value='{{$boatdetail->id}}' {{$formisselected}}>{{$boatdetail->varTitle}} </option>

                                    @endforeach
                                </select>
                                @if ($errors->has('boats'))
                                <span class="error">{{ $errors->first('boats') }}</span>
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
    </div>

    @if(isset($similarBoat) && count($similarBoat) > 0)
    <section class="boat_sec" data-aos="fade-up">
        <div class="container-fluid">
            <h2 class="text-capitalize cm-title" data-aos="fade-up">Explore Other Boats</h2>
            <div class="boat-card-slider swiper">
                <div class="swiper-wrapper">
                    @foreach($similarBoat as $index => $boat)
                    @php
                    if(isset(App\Helpers\MyLibrary::getFront_Uri('boat')['uri'])){
                    $moduelFrontPageUrl = App\Helpers\MyLibrary::getFront_Uri('boat')['uri'];
                    $moduleFrontWithCatUrl = ($boat->varAlias != false ) ? $moduelFrontPageUrl . '/' . $boat->varAlias : $moduelFrontPageUrl;
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
                                        <source type="image/webp" data-srcset="{!! App\Helpers\LoadWebpImage::resize($boat->fkIntImgId,675,450) !!}" srcset="{!! App\Helpers\LoadWebpImage::resize($boat->fkIntImgId,675,450) !!}">
                                        <img class="lazy" data-src="{{ App\Helpers\resize_image::resize($boat->fkIntImgId,675,450)}}" src="{!! url('assets/images/loader.gif') !!}" alt="{{ htmlspecialchars_decode($boat->varTitle) }}" title="{{ htmlspecialchars_decode($boat->varTitle) }}">
                                    </picture>
                                </div>
                            </div>
                            <span class="line"></span>
                        </div>
                        <div class="boat-desc-wrap">
                            <div class="boat_title">
                                <h4 class="title text-capitalize main-title"><a href="{{ $recordLinkUrl }}" title="{!! $boat->varTitle !!}">{!! $boat->varTitle !!}</a></h4>
                            </div>
                            <div class="boat_desc">
                                <p>Price : ${!! $boat->intPrice !!}</p>
                                <p>Year :{!! $boat->yearYear !!}</p>
                                <p>Length :{!! $boat->varLength !!}</p>
                                @if(isset($boat->intBoatFuelId) && !empty($boat->intBoatFuelId))
                                @php
                                $result = DB::table('boat_fuel_type')->select('varTitle')->where('id',$boat->intBoatFuelId)->where('chrPublish','Y')->where('chrDelete','N')->first();
                                @endphp
                                <p>Fuel : {!! $result->varTitle !!}</p>
                                @endif
                                <a href="{{ $recordLinkUrl }}" class="boat-btn" title="MORE DETAILS">MORE DETAILS</a>
                            </div>
                        </div>
                    </div>
                    <!-- </div> -->
                    @endforeach
                </div>
                <div class="swiper-scrollbar"></div>
            </div>
            <div class="view-more text-md-right text-center">
                <a href="{{url('boat')}}" target="_blank" class="ac-btn" title="View All Boat">View All Boat</a>
            </div>
        </div>
    </section>























    @endif
    <div class="need_help_element elemt-2 mt-5">
        <div class="inner">
            <h3>
                Are you ready to start building a relationship with Avalon Marine?
            </h3>
            <Button class="btn ac-btn-primary" id="form-container1" title="Get a Free Estimate">Get a Free Estimate</button>
        </div>
        <div class="image-part">
        </div>
    </div>
    </div>
    </div>
</section>

@if(!Request::ajax())
@section('footer_scripts')
<script src="{{ url('assets/js/packages/boatinquirylead/boat-inquiry.js') }}?{{ Config::get('Constant.VERSION') }}"></script>

@endsection
@endsection
@endif