@section('css')
<style>
	.star-rating {
		/* border: solid 1px #ccc; */
		display: flex;
		flex-direction: row-reverse;
		font-size: 1.5em;
		justify-content: space-around;
		padding: 0 .2em;
		text-align: center;
		width: 5em;
	}

	.star-rating input {
		display: none;
	}

	.star-rating label {
		color: #ccc;
		cursor: pointer;
	}

	.star-rating :checked~label {
		color: #f90;
	}

	.star-rating label:hover,
	.star-rating label:hover~label {
		color: #fc0;
	}

	/* explanation */

	article {
		background-color: #ffe;
		box-shadow: 0 0 1em 1px rgba(0, 0, 0, .25);
		color: #006;
		font-family: cursive;
		font-style: italic;
		margin: 4em;
		max-width: 30em;
		padding: 2em;
	}
</style>
<link href="{{ url('resources/global/plugins/bootstrap-datepicker/css/bootstrap-datepicker.css') }}" rel="stylesheet" type="text/css"/>
<link href="{{ url('resources/global/plugins/bootstrap-fileinput/bootstrap-fileinput.css') }}" rel="stylesheet" type="text/css" />
@endsection
@extends('powerpanel.layouts.app')
@section('title')
{{Config::get('Constant.SITE_NAME')}} - PowerPanel
@endsection
@section('content')
@php $settings = json_decode(Config::get("Constant.MODULE.SETTINGS")); @endphp
@include('powerpanel.partials.breadcrumbs')
<div class="col-md-12 settings">
    @if(Session::has('message'))
    <div class="row">
        <div class="alert alert-success">
            <button class="close" data-close="alert"></button>
            {{ Session::get('message') }}
        </div>
    </div>
    @endif
    <div class="row">
        <div class="portlet light bordered">
            <div class="portlet-body">
                <div class="tabbable tabbable-tabdrop">
                    <div class="tab-content row form_pattern">
                        <div class="col-md-12">
                            <div class="portlet-body">
                                {!! Form::open(['method' => 'post','id'=>'frmTestimonial']) !!}
                                <div class="form-body">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group {{ $errors->has('testimonialby') ? 'has-error' : '' }} form-md-line-input">
                                                <label class="form_title" for="testimonialby">{{ trans('testimonial::template.testimonialModule.testimonialBy') }} <span aria-required="true" class="required"> * </span></label>
                                                {!! Form::text('testimonialby',isset($testimonials->varTitle) ? $testimonials->varTitle:old('testimonialby') ,array('class' => 'form-control input-sm seoField maxlength-handler','maxlength'=>'150','id' => 'testimonialby','placeholder' => trans('testimonial::template.testimonialModule.testimonialBy'),'autocomplete'=>'off')) !!}
                                                <span style="color: red;">
                                                    {{ $errors->first('testimonialby') }}
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row" style="display:none;">
                                        <div class="col-md-12">
                                            <div class="form-group {{ $errors->has('city') ? 'has-error' : '' }} form-md-line-input">
                                                <label class="form_title" for="city">{{ trans('testimonial::template.testimonialModule.city') }} <span aria-required="true" class="required"> * </span></label>
                                                {!! Form::text('city',isset($testimonials->varCity) ? $testimonials->varCity:old('city') ,array('class' => 'form-control input-sm seoField maxlength-handler','maxlength'=>'30','id' => 'city','placeholder' => trans('testimonial::template.testimonialModule.city'),'autocomplete'=>'off')) !!}
                                                <span style="color: red;">
                                                    {{ $errors->first('city') }}
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row" style="display:none;">
                                        <div class="col-md-12">
                                            <div class="form-group form-md-line-input">
                                                <label class="form_title" for="testimonial">{{ trans('testimonial::template.testimonialModule.testimonialDate') }}</label>
                                                <div class="input-group date-picker" data-date-format="{{Config::get('Constant.DEFAULT_DATE_FORMAT')}}">
                                                    <span class="input-group-btn date_default">
                                                        <span class="btn date-set">
                                                            <i class="fa fa-calendar"></i>
                                                        </span>
                                                    </span>
                                                    <input type="text" class="form-control datepicker" id="testimonialdate" name="testimonialdate" value="{{isset($testimonials->dtStartDateTime)?date(Config::get('Constant.DEFAULT_DATE_FORMAT'),strtotime(str_replace('/','-',$testimonials->dtStartDateTime))):date(Config::get('Constant.DEFAULT_DATE_FORMAT'))}}" readonly>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row" style="display:none;">
                                    @include('powerpanel.partials.imageControl',['type' => 'multiple','label' => 'Select Photo' ,'data'=> isset($testimonials)?$testimonials:null , 'id' => 'testimonial_image', 'name' => 'img_id', 'settings' => $settings, 'width' => '500', 'height' => '500'])
                                    </div>
                                    
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group @if($errors->first('testimonial')) has-error @endif">
                                                <label class="form_title" for="testimonial">{{ trans('testimonial::template.common.testimonial') }} <span aria-required="true" class="required"> * </span></label>
                                                <span class="help-block">
                                                    <strong>{{ $errors->first('testimonial') }}</strong>
                                                </span>
                                                {!! Form::textarea('testimonial',isset($testimonials->txtDescription)?$testimonials->txtDescription:old('testimonial'),array('class' => 'form-control','id'=>'txtDescription','palceholder'=>trans('testimonial::template.testimonialModule.testimonial'),'style'=>'max-height:80px;')) !!}
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="row">
                                        <div class="col-md-12">
											<div class="form-group form-md-line-input">
												<label class="form_title" for="site_name">Rating</label>

												@php
												if(isset($testimonials->varStarRating)){
												$rate = '';
												$i = 5;
												$rate .= '<div class="star-rating">';
													while($i >= 1){
													$check = $i == $testimonials->varStarRating ? 'checked' : '';
													$rate .= '<input type="radio" id="'.$i.'-stars" name="starrating" value="'.$i.'" '.$check.' /><label for="'.$i.'-stars" class="star">&#9733;</label>';
													$i--;
													}
													$rate .= '</div>';
												echo '<input type="hidden" name="starrating" value="'.$testimonials->varStarRating.'" />';
												echo $rate;
												} else {
												echo '<div class="star-rating">	
													<input type="radio" id="5-stars" name="starrating" value="5" />
													<label for="5-stars" class="star">&#9733;</label>
													<input type="radio" id="4-stars" name="starrating" value="4" />
													<label for="4-stars" class="star">&#9733;</label>
													<input type="radio" id="3-stars" name="starrating" value="3" />
													<label for="3-stars" class="star">&#9733;</label>
													<input type="radio" id="2-stars" name="starrating" value="2" />
													<label for="2-stars" class="star">&#9733;</label>
													<input type="radio" id="1-star" name="starrating" value="1" />
													<label for="1-star" class="star">&#9733;</label>
												</div>';
												}
												@endphp


											</div>
										</div>
                                    </div>
                                    <h3>{{ trans('testimonial::template.common.displayinformation') }}</h3>
                                    <div class="row">
                                        <div class="col-md-6">
                                            @include('powerpanel.partials.displayInfo',['display' => isset($testimonials->chrPublish)?$testimonials->chrPublish:null])
                                        </div>
                                    </div>
                                </div>
                                <div class="form-actions">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <button type="submit" name="saveandedit" class="btn btn-green-drake" value="saveandedit" title="{!! trans('testimonial::template.common.saveandedit') !!}">{!! trans('testimonial::template.common.saveandedit') !!}</button>
                                            <button type="submit" name="saveandexit" class="btn btn-green-drake" value="saveandexit" title="{!! trans('testimonial::template.common.saveandexit') !!}">{!! trans('testimonial::template.common.saveandexit') !!}</button>
                                            <a class="btn btn-outline red" href="{{ url('powerpanel/testimonial') }}" title="{{ trans('testimonial::template.common.cancel') }}">{{ trans('testimonial::template.common.cancel') }}</a>
                                        </div>
                                    </div>
                                </div>
                                {!! Form::close() !!}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@section('scripts')
@include('powerpanel.partials.ckeditorTestimonial')
<script src="{{ url('resources/global/plugins/bootstrap-maxlength/bootstrap-maxlength.min.js') }}" type="text/javascript"></script>
<script src="{{ url('resources/global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.js') }}" type="text/javascript"></script>
<script src="{{ url('resources/global/plugins/bootstrap-fileinput/bootstrap-fileinput.js') }}" type="text/javascript"></script>
<script src="{{ url('resources/pages/scripts/packages/testimonial/testimonial_validations.js') }}" type="text/javascript"></script>
<script type="text/javascript">
window.site_url = '{!! url("/") !!}';
var moduleAlias = 'testimonials';
$(document).ready(function () {
    $('.datepicker').datepicker({
        autoclose: true,
        endDate: new Date(),
        format: DEFAULT_DT_FMT_FOR_DATEPICKER
    });
});
$('.date-set,.datepicker').on('click', function () {
    $('.datepicker').datepicker('show');
});
$('.maxlength-handler').maxlength({
    limitReachedClass: "label label-danger",
    alwaysShow: true,
    threshold: 5,
    twoCharLinebreak: false,
    appendToParent: true
});
</script>
<script src="{{ url('resources/pages/scripts/custom.js') }}" type="text/javascript"></script>
@endsection