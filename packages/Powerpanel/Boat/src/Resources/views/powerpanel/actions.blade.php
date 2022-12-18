@section('css')
<link href="{{ url('resources/global/plugins/fontawesome-iconpicker/dist/css/fontawesome-iconpicker.min.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ url('resources/global/plugins/bootstrap-datepicker/css/bootstrap-datepicker.min.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ $CDN_PATH.'resources/global/plugins/bootstrap-fileinput/bootstrap-fileinput.css' }}" rel="stylesheet" type="text/css" />

@endsection
@extends('powerpanel.layouts.app')
@section('title') {{ Config::get('Constant.SITE_NAME') }} - PowerPanel @endsection
@section('content')
@include('powerpanel.partials.breadcrumbs')
@php $settings = json_decode(Config::get("Constant.MODULE.SETTINGS")); @endphp
@if (count($errors) > 0)
<ul>
  @foreach ($errors->all() as $error)
  <li>{{ $error }}</li>
  @endforeach
</ul>
@endif
<div class="row">
  <div class="col-sm-12">
    @if(Session::has('message'))
    <div class="alert alert-success">
      <button class="close" data-close="alert"></button>
      {{ Session::get('message') }}
    </div>
    @endif
    <div class="portlet light bordered">
      <div class="portlet-body">
        <div class="tabbable tabbable-tabdrop">
          <div class="tab-content">
            <div class="row">
              <div class="col-md-12">
                <div class="tab-pane active" id="general">
                  <div class="portlet-body form_pattern">
                    {!! Form::open(['method' => 'post','id'=>'frmBoat']) !!}
                    <div class="form-body">
                      <div class="row">
                        <div class="col-md-12">
                          <div class="form-group @if($errors->first('title')) has-error @endif form-md-line-input">
                            <label class="form_title" for="site_name">{{ trans('boat::template.common.title') }} <span aria-required="true" class="required"> * </span></label>
                            {!! Form::text('title', isset($boat->varTitle)?$boat->varTitle:old('title'), array('maxlength' => 60, 'class' => 'form-control hasAlias seoField maxlength-handler','autocomplete'=>'off','data-url' => 'powerpanel/boat', 'placeholder' => 'Title')) !!}
                            <span class="help-block">
                              {{ $errors->first('title') }}
                            </span>
                          </div>
                        </div>
                      </div>

                      <!-- code for alias -->
                      {!! Form::hidden(null, null, array('class' => 'hasAlias','data-url' => 'powerpanel/boat')) !!}
                      {!! Form::hidden('alias', isset($boat->alias->varAlias)?$boat->alias->varAlias:old('alias'), array('class' => 'aliasField')) !!}
                      {!! Form::hidden('oldAlias', isset($boat->alias->varAlias)?$boat->alias->varAlias:old('alias')) !!}
                      <div class="form-group alias-group {{!isset($boat)?'hide':''}}">
                        <label class="form_title" for="{{ trans('boat::template.url') }}">{{ trans('boat::template.common.url') }} :</label>
                        <a href="javascript:void;" class="alias">{!! url("/") !!}</a>
                        <a href="javascript:void(0);" class="editAlias" title="Edit">
                          <i class="fa fa-edit"></i>
                          <a class="without_bg_icon openLink" title="Open Link" target="_blank" href="{{url('boat/'.(isset($boat->alias->varAlias) && isset($boat)?$boat->alias->varAlias:''))}}">
                            <i class="fa fa-external-link" aria-hidden="true"></i>
                          </a>
                        </a>
                      </div>
                      <span class="help-block">
                        {{ $errors->first('alias') }}
                      </span>
                      <!-- code for alias -->

                      <input type="hidden" name="category_id" value="">
                      <div class="row" style="display: none;">
                        <div class="col-md-12 ">
                          <div class="form-group font_icons_file @if($errors->first('font_awesome_icon')) has-error @endif ">
                            <label class="form_title" for="font_awesome_icon">{{ trans('boat::template.common.boatIcon') }}</label>
                            {!! Form::text('font_awesome_icon', isset($boat->varFontAwesomeIcon)?$boat->varFontAwesomeIcon:old('font_awesome_icon'), array('id'=>"e4_element", 'data-placement'=>"bottomRight", 'class' => 'form-control icp icp-auto','placeholder' => trans('boat::template.common.selectIcon'),'autocomplete'=>'off','readonly'=> 'readonly')) !!}
                            <span class="input-group-addon"></span>
                          </div>
                        </div>
                      </div>
                      <div class="row">
                        <div class="col-md-4">
                          <div class="form-group @if($errors->first('tag_line')) has-error @endif form-md-line-input">
                            @if(isset($boat))
                            @if (File::exists(base_path() . '/resources/views/powerpanel/partials/lockedpage.blade.php') != null)
                            @include('powerpanel.partials.lockedpage',['pagedata'=>$boat])
                            @endif
                            @endif
                            @php
                            if(isset($boat_highLight->intFKCategory) && ($boat_highLight->intFKCategory != $boat->intFKCategory)){
                            $Class_title = " highlitetext";
                            }else{
                            $Class_title = "";
                            }
                            $currentCatAlias = '';
                            @endphp
                            <label class="form_title {{ $Class_title }}" for="site_name">Select Boat Category <span aria-required="true" class="required"> * </span></label>
                            <select class="form-control bs-select select2" name="boat_category_id">
                              <option value="">-- Select Boat Category --</option>
                              @foreach ($boatcategory as $cat)
                              @php $permissionName = 'boat-list' @endphp
                              @php $selected = ''; @endphp
                              @if(isset($boat->intBoatCategoryId))
                              @if($cat['id'] == $boat->intBoatCategoryId)
                              @php $selected = 'selected'; $currentCatAlias = $cat['alias']['varAlias']; @endphp
                              @endif
                              @endif
                              <option value="{{ $cat['id'] }}" data-categryalias="{{ $cat['alias']['varAlias'] }}" {{ $selected }}>{{ $cat['varModuleName']== "boat"?'Select Category':$cat['varTitle'] }}</option>
                              @endforeach
                            </select>
                            <span class="help-block">
                              {{ $errors->first('category') }}
                            </span>
                          </div>
                        </div>
                        <div class="col-md-4">
                          <div class="form-group @if($errors->first('tag_line')) has-error @endif form-md-line-input">
                            @if(isset($boat))
                            @if (File::exists(base_path() . '/resources/views/powerpanel/partials/lockedpage.blade.php') != null)
                            @include('powerpanel.partials.lockedpage',['pagedata'=>$boat])
                            @endif
                            @endif
                            @php
                            if(isset($boat_highLight->intFKCategory) && ($boat_highLight->intFKCategory != $boat->intFKCategory)){
                            $Class_title = " highlitetext";
                            }else{
                            $Class_title = "";
                            }
                            $currentCatAlias = '';
                            @endphp
                            <label class="form_title {{ $Class_title }}" for="site_name">Select Brand <span aria-required="true" class="required"> * </span></label>
                            <select class="form-control bs-select select2" name="boat_brand_id">
                              <option value="">-- Select Brand --</option>
                              @foreach ($brandcategory as $cat)
                              @php $permissionName = 'boat-list' @endphp
                              @php $selected = ''; @endphp
                              @if(isset($boat->intBoatBrandId))
                              @if($cat['id'] == $boat->intBoatBrandId)
                              @php $selected = 'selected'; $currentCatAlias = $cat['alias']['varAlias']; @endphp
                              @endif
                              @endif
                              <option value="{{ $cat['id'] }}" data-categryalias="{{ $cat['alias']['varAlias'] }}" {{ $selected }}>{{ $cat['varModuleName']== "boat"?'Select Category':$cat['varTitle'] }}</option>
                              @endforeach
                            </select>
                            <span class="help-block">
                              {{ $errors->first('category') }}
                            </span>
                          </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group @if($errors->first('boat_location')) has-error @endif form-md-line-input">
                              <label class="form_title">{{ trans('boat::template.common.boat_location') }}<span aria-required="true" class="required"> * </span></label>
                              {!! Form::text('boat_location', isset($boat->varBoatLocation)?$boat->varBoatLocation:old('boat_location'), array('maxlength' =>150,'class' => 'form-control maxlength-handler','placeholder' => 'Boat Location')) !!}
                              <span class="help-block">{{ $errors->first('boat_location') }}</span>
                            </div>
                          </div>
                      </div>
                      <div class="row">
                        <div class="col-md-12">
                          <div class="form-group @if($errors->first('short_description')) has-error @endif form-md-line-input">
                            <label class="form_title">{{ trans('boat::template.common.shortdescription') }}<span aria-required="true" class="required"> * </span></label>
                            {!! Form::textarea('short_description', isset($boat->txtShortDescription)?$boat->txtShortDescription:old('short_description'), array('maxlength' => isset($settings->short_desc_length)?$settings->short_desc_length:500,'class' => 'form-control seoField maxlength-handler','id'=>'varShortDescription','rows'=>'3','placeholder' => 'Short Description')) !!}
                            <span class="help-block">{{ $errors->first('short_description') }}</span>
                          </div>
                        </div>
                      </div>
                      @include('powerpanel.partials.imageControl',['type' => 'multiple','label' =>'Select Image <span aria-required="true" class="required"> * </span>' ,'data'=> isset($boat)?$boat:null , 'id' => 'boat_image', 'name' => 'img_id', 'settings' => $settings, 'width' => '1920', 'height' => '1080'])

                      <div style="display: none;">
                        @include('powerpanel.partials.videoControl',['type' => 'multiple' ,'label' => trans('boat::template.boatModule.selectVideo') ,'data'=> isset($boat)?$boat:null,'videoData' => isset($videoData)?$videoData:null , 'id' => 'boat_video', 'name' => 'video_id'])
                      </div>


                      <div class="form-group @if($errors->first('tag_line')) has-error @endif form-md-line-input">
                        <label class="form_title {{ $Class_title }}" for="site_name">Select Stock <span aria-required="true" class="required"> * </span></label>
                        <div class="md-radio-inline">
                          @foreach($stock as $value)
                          @php $selected = ''; @endphp
                          @if(isset($boat->intBoatStockId))
                          @if($value->id == $boat->intBoatStockId)
                          @php $selected = 'checked'; @endphp
                          @endif
                          @endif
                          <div class="md-radio">
                            <input type="radio" value="{{ $value->id }}" id="{{ $value->id }}" name="boat_stock_id" class="md-radiobtn banner" {{ $selected }}>
                            <label for="{{ $value->id }}">
                              <span class="inc"></span>
                              <span class="check"></span>
                              <span class="box"></span> {!! $value->varTitle !!}
                            </label>
                          </div>
                          @endforeach
                        </div>
                        <span class="help-block">
                          {{ $errors->first('stock') }}
                        </span>
                      </div>
                      <div class="row">
                        <div class="col-md-4">
                          <div class="form-group @if($errors->first('tag_line')) has-error @endif form-md-line-input">
                            <label class="form_title {{ $Class_title }}" for="site_name">Select Boat Condition <span aria-required="true" class="required"> * </span></label>
                            <select class="form-control bs-select select2" name="boat_condition_id">
                              <option value="">-- Select Boat Condition --</option>
                              @foreach ($boatcondition as $cat)
                              @php $selected = ''; @endphp
                              @if(isset($boat->intBoatconditionId))
                              @if($cat->id == $boat->intBoatconditionId)
                              @php $selected = 'selected'; @endphp
                              @endif
                              @endif
                              <option value="{{ $cat->id }}" data-categryalias="" {{$selected}}>{!! $cat->varTitle !!}</option>
                              @endforeach
                            </select>
                            <span class="help-block">
                              {{ $errors->first('category') }}
                            </span>
                          </div>
                        </div>
                        <div class="col-md-4">
                          <div class="form-group @if($errors->first('tag_line')) has-error @endif form-md-line-input">
                            <label class="form_title {{ $Class_title }}" for="site_name">Select Boat Fuel Type <span aria-required="true" class="required"> * </span></label>
                            <select class="form-control bs-select select2" name="boat_fuel_type_id">
                              <option value="">-- Select Boat Fuel Type --</option>
                              @foreach ($boatfueltype as $cat)
                              @php $selected = ''; @endphp
                              @if(isset($boat->intBoatFuelId))
                              @if($cat->id == $boat->intBoatFuelId)
                              @php $selected = 'selected'; @endphp
                              @endif
                              @endif
                              <option value="{{ $cat->id }}" {{$selected}} data-categryalias="">{!! $cat->varTitle !!}</option>
                              @endforeach
                            </select>
                            <span class="help-block">
                              {{ $errors->first('category') }}
                            </span>
                          </div>
                        </div>

                        <div class="col-md-4">
                          <div class="form-group @if($errors->first('price')) has-error @endif form-md-line-input">
                            <label class="form_title">{{ trans('boat::template.common.price') }}<span aria-required="true" class="required"> * </span></label>
                            {!! Form::text('price', isset($boat->intPrice)?$boat->intPrice:old('price'), array('maxlength' =>9, 'id' => 'price','class' => 'form-control maxlength-handler','placeholder' => 'Price', 'onpaste'=>'return false;', 'ondrop'=>'return false;', 'onkeypress'=>'javascript: return KeycheckOnlyPhonenumber(event);')) !!}
                            <span class="help-block">{{ $errors->first('price') }}</span>
                          </div>
                        </div>
                      </div>
                      <div class="row">
                        <div class="col-md-4">
                          <div class="form-group @if($errors->first('hull_material')) has-error @endif form-md-line-input">
                            <label class="form_title">{{ trans('boat::template.common.hull_material') }}<span aria-required="true" class="required"> * </span></label>
                            {!! Form::text('hull_material', isset($boat->varHullMaterial)?$boat->varHullMaterial:old('hull_material'), array('maxlength' =>150,'class' => 'form-control maxlength-handler','placeholder' => 'Hull Material')) !!}
                            <span class="help-block">{{ $errors->first('hull_material') }}</span>
                          </div>
                        </div>
                        <div class="col-md-4">
                          <div class="form-group @if($errors->first('hull_shape')) has-error @endif form-md-line-input">
                            <label class="form_title">{{ trans('boat::template.common.hull_shape') }}</label>
                            {!! Form::text('hull_shape', isset($boat->varHullShape)?$boat->varHullShape:old('hull_shape'), array('maxlength' =>150,'class' => 'form-control maxlength-handler','placeholder' => 'Hull Shape')) !!}
                            <span class="help-block">{{ $errors->first('hull_shape') }}</span>
                          </div>
                        </div>
                        <div class="col-md-4">
                          <div class="form-group @if($errors->first('hull_warranty')) has-error @endif form-md-line-input">
                            <label class="form_title">{{ trans('boat::template.common.hull_warranty') }}</label>
                            {!! Form::text('hull_warranty', isset($boat->varHullWarranty)?$boat->varHullWarranty:old('hull_warranty'), array('maxlength' =>150,'class' => 'form-control maxlength-handler','placeholder' => 'Hull Warranty')) !!}
                            <span class="help-block">{{ $errors->first('hull_warranty') }}</span>
                          </div>
                        </div>
                      </div>
                      <div class="row">
                        <div class="col-md-4">
                          <div class="form-group @if($errors->first('year')) has-error @endif form-md-line-input">
                            <label class="form_title">{{ trans('boat::template.common.year') }}<span aria-required="true" class="required"> * </span></label>
                            {!! Form::text('year', isset($boat->yearYear)?$boat->yearYear:old('year'), array('maxlength' =>4,'class' => 'form-control ','id'=>'yearpicker','placeholder' => 'Year','onpaste'=>'return false;','autocomplete'=>'off','onkeypress'=>'javascript: return KeycheckOnlyPhonenumber(event);')) !!}
                            <span class="help-block">{{ $errors->first('year') }}</span>
                          </div>
                        </div>

                        <div class="col-md-4">
                          <div class="form-group @if($errors->first('model')) has-error @endif form-md-line-input">
                            <label class="form_title">{{ trans('boat::template.common.model') }}<span aria-required="true" class="required"> * </span></label>
                            {!! Form::text('model', isset($boat->varModel)?$boat->varModel:old('model'), array('maxlength' =>150,'class' => 'form-control maxlength-handler','placeholder' => 'Model')) !!}
                            <span class="help-block">{{ $errors->first('model') }}</span>
                          </div>
                        </div>

                        <div class="col-md-4">
                          <div class="form-group @if($errors->first('length')) has-error @endif form-md-line-input">
                            <label class="form_title">{{ trans('boat::template.common.length') }}<span aria-required="true" class="required"> * </span></label>
                            {!! Form::text('length', isset($boat->varLength)?$boat->varLength:old('length'), array('maxlength' =>10,'class' => 'form-control maxlength-handler','id'=>'lengthft',  'placeholder' => 'Length')) !!}
                            <span class="help-block">{{ $errors->first('length') }}</span>
                          </div>
                        </div>
                      </div>



                      <div class="row">
                        <div class="col-md-12">
                          <div class="form-group @if($errors->first('description')) has-error @endif form-md-line-input">
                            <label class="form_title">{{ trans('boat::template.common.description') }}<span aria-required="true" class="required"> * </span></label>
                            {!! Form::textarea('description', isset($boat->txtDescriptionnew)?$boat->txtDescriptionnew:old('description'), array('class' => 'form-control ','placeholder' => 'Description','id'=>'txtDescription')) !!}
                            <span class="help-block">{{ $errors->first('description') }}</span>
                          </div>
                        </div>
                      </div>
                      <div class="row">
                        <div class="col-md-12">
                          <div class="form-group @if($errors->first('other_details')) has-error @endif form-md-line-input">
                            <label class="form_title">{{ trans('boat::template.common.otherdetails') }}</label>
                            {!! Form::textarea('other_details', isset($boat->txtOtherdetails)?$boat->txtOtherdetails:old('other_details'), array('class' => 'form-control maxlength-handler ','id'=>'txtOtherDetails','placeholder' => 'Other Deatils')) !!}
                            <span class="help-block">{{ $errors->first('other_details') }}</span>
                          </div>
                        </div>
                      </div>

                      <h3 class="form-section">SPECIFICATIONS</h3>
                      <div class="row">
                        <div class="col-md-4">
                          <div class="form-group @if($errors->first('cruising_speed')) has-error @endif form-md-line-input">
                            <label class="form_title">{{ trans('boat::template.common.cruising_speed') }}</label>
                            {!! Form::text('cruising_speed', isset($boat->varCruisingSpeed)?$boat->varCruisingSpeed:old('cruising_speed'), array('maxlength' =>150,'class' => 'form-control maxcruising_speed maxlength-handler','placeholder' => trans('boat::template.common.cruising_speed') )) !!}
                            <span class="help-block">{{ $errors->first('cruising_speed') }}</span>
                          </div>
                        </div>
                        <div class="col-md-4">
                          <div class="form-group @if($errors->first('max_speed')) has-error @endif form-md-line-input">
                            <label class="form_title">{{ trans('boat::template.common.max_speed') }}</label>
                            {!! Form::text('max_speed', isset($boat->varMaxSpeed)?$boat->varMaxSpeed:old('max_speed'), array('maxlength' =>150,'class' => 'form-control maxmax_speed maxlength-handler','placeholder' => trans('boat::template.common.max_speed') )) !!}
                            <span class="help-block">{{ $errors->first('max_speed') }}</span>
                          </div>
                        </div>
                        <div class="col-md-4">
                          <div class="form-group @if($errors->first('dry_weight')) has-error @endif form-md-line-input">
                            <label class="form_title">{{ trans('boat::template.common.dry_weight') }}</label>
                            {!! Form::text('dry_weight', isset($boat->varDryWeight)?$boat->varDryWeight:old('dry_weight'), array('maxlength' =>150,'class' => 'form-control maxdry_weight maxlength-handler','placeholder' => trans('boat::template.common.dry_weight') )) !!}
                            <span class="help-block">{{ $errors->first('dry_weight') }}</span>
                          </div>
                        </div>
                      </div>
                      <div class="row">
                        <div class="col-md-4">
                          <div class="form-group @if($errors->first('max_bridge_clearance')) has-error @endif form-md-line-input">
                            <label class="form_title">{{ trans('boat::template.common.max_bridge_clearance') }}</label>
                            {!! Form::text('max_bridge_clearance', isset($boat->varBridgeclearance)?$boat->varBridgeclearance:old('max_bridge_clearance'), array('maxlength' =>150,'class' => 'form-control maxmax_bridge_clearance maxlength-handler','placeholder' => trans('boat::template.common.max_bridge_clearance') )) !!}
                            <span class="help-block">{{ $errors->first('max_bridge_clearance') }}</span>
                          </div>
                        </div>
                        <div class="col-md-4">
                          <div class="form-group @if($errors->first('max_draft')) has-error @endif form-md-line-input">
                            <label class="form_title">{{ trans('boat::template.common.max_draft') }}</label>
                            {!! Form::text('max_draft', isset($boat->varMaxDraft)?$boat->varMaxDraft:old('max_draft'), array('maxlength' =>150,'class' => 'form-control maxmax_draft maxlength-handler','placeholder' => trans('boat::template.common.max_draft') )) !!}
                            <span class="help-block">{{ $errors->first('max_draft') }}</span>
                          </div>
                        </div>
                        <div class="col-md-4">
                          <div class="form-group @if($errors->first('beam')) has-error @endif form-md-line-input">
                            <label class="form_title">{{ trans('boat::template.common.beam') }}<span aria-required="true" class="required"> * </span></label>
                            {!! Form::text('beam', isset($boat->varBeam)?$boat->varBeam:old('beam'), array('maxlength' =>150,'class' => 'form-control maxbeam maxlength-handler','placeholder' => trans('boat::template.common.beam') )) !!}
                            <span class="help-block">{{ $errors->first('beam') }}</span>
                          </div>
                        </div>
                      </div>
                      <div class="row">
                        <div class="col-md-4">
                          <div class="form-group @if($errors->first('cabin_headroom')) has-error @endif form-md-line-input">
                            <label class="form_title">{{ trans('boat::template.common.cabin_headroom') }}</label>
                            {!! Form::text('cabin_headroom', isset($boat->varCabinHeadroom)?$boat->varCabinHeadroom:old('cabin_headroom'), array('maxlength' =>150,'class' => 'form-control maxcabin_headroom maxlength-handler','placeholder' => trans('boat::template.common.cabin_headroom'))) !!}
                            <span class="help-block">{{ $errors->first('cabin_headroom') }}</span>
                          </div>
                        </div>
                        <div class="col-md-4">
                          <div class="form-group @if($errors->first('length_at_waterline')) has-error @endif form-md-line-input">
                            <label class="form_title">{{ trans('boat::template.common.length_at_waterline') }}</label>
                            {!! Form::text('length_at_waterline', isset($boat->varLengthAtWaterline)?$boat->varLengthAtWaterline:old('length_at_waterline'), array('maxlength' =>150,'class' => 'form-control maxlength_at_waterline maxlength-handler','placeholder' => trans('boat::template.common.length_at_waterline') )) !!}
                            <span class="help-block">{{ $errors->first('length_at_waterline') }}</span>
                          </div>
                        </div>
                        <div class="col-md-4">
                          <div class="form-group @if($errors->first('length_overall')) has-error @endif form-md-line-input">
                            <label class="form_title">{{ trans('boat::template.common.length_overall') }}<span aria-required="true" class="required"> * </span></label>
                            {!! Form::text('length_overall', isset($boat->varLengthOverall)?$boat->varLengthOverall:old('length_overall'), array('maxlength' =>150,'class' => 'form-control maxlength_overall maxlength-handler','placeholder' => trans('boat::template.common.length_overall') )) !!}
                            <span class="help-block">{{ $errors->first('length_overall') }}</span>
                          </div>
                        </div>
                      </div>
                      <div class="row">
                        <div class="col-md-4">
                          <div class="form-group @if($errors->first('windlass')) has-error @endif form-md-line-input">
                            <label class="form_title">{{ trans('boat::template.common.windlass') }}</label>
                            {!! Form::text('windlass', isset($boat->varWindlass)?$boat->varWindlass:old('windlass'), array('maxlength' =>150,'class' => 'form-control maxwindlass maxlength-handler','placeholder' => trans('boat::template.common.windlass') )) !!}
                            <span class="help-block">{{ $errors->first('windlass') }}</span>
                          </div>
                        </div>
                        <div class="col-md-4">
                          <div class="form-group @if($errors->first('deadrise_at_transom')) has-error @endif form-md-line-input">
                            <label class="form_title">{{ trans('boat::template.common.deadrise_at_transom') }}</label>
                            {!! Form::text('deadrise_at_transom', isset($boat->varDeadriseAtTransom)?$boat->varDeadriseAtTransom:old('deadrise_at_transom'), array('maxlength' =>150,'class' => 'form-control maxdeadrise_at_transom maxlength-handler','placeholder' => trans('boat::template.common.deadrise_at_transom') )) !!}
                            <span class="help-block">{{ $errors->first('deadrise_at_transom') }}</span>
                          </div>
                        </div>
                        <div class="col-md-4">
                          <div class="form-group @if($errors->first('electrical_circuit')) has-error @endif form-md-line-input">
                            <label class="form_title">{{ trans('boat::template.common.electrical_circuit') }}</label>
                            {!! Form::text('electrical_circuit', isset($boat->varElectricalCircuit)?$boat->varElectricalCircuit:old('electrical_circuit'), array('maxlength' =>150,'class' => 'form-control maxelectrical_circuit maxlength-handler','placeholder' => trans('boat::template.common.electrical_circuit') )) !!}
                            <span class="help-block">{{ $errors->first('electrical_circuit') }}</span>
                          </div>
                        </div>
                      </div>

                      <div class="row">
                        <div class="col-md-4">
                          <div class="form-group @if($errors->first('seating_capacity')) has-error @endif form-md-line-input">
                            <label class="form_title">{{ trans('boat::template.common.seating_capacity') }}</label>
                            {!! Form::text('seating_capacity', isset($boat->varSeatingCapacity)?$boat->varSeatingCapacity:old('seating_capacity'), array('maxlength' =>150,'class' => 'form-control maxseating_capacity maxlength-handler','placeholder' => trans('boat::template.common.seating_capacity') )) !!}
                            <span class="help-block">{{ $errors->first('seating_capacity') }}</span>
                          </div>
                        </div>
                        <div class="col-md-4">
                          <div class="form-group @if($errors->first('single_berths')) has-error @endif form-md-line-input">
                            <label class="form_title">{{ trans('boat::template.common.single_berths') }}</label>
                            {!! Form::text('single_berths', isset($boat->varSingleBerths)?$boat->varSingleBerths:old('single_berths'), array('maxlength' =>150,'class' => 'form-control maxsingle_berths maxlength-handler','placeholder' => trans('boat::template.common.single_berths') )) !!}
                            <span class="help-block">{{ $errors->first('single_berths') }}</span>
                          </div>
                        </div>
                        <div class="col-md-4">
                          <div class="form-group @if($errors->first('heads')) has-error @endif form-md-line-input">
                            <label class="form_title">{{ trans('boat::template.common.heads') }}</label>
                            {!! Form::text('heads', isset($boat->varHeads)?$boat->varHeads:old('heads'), array('maxlength' =>150,'class' => 'form-control maxheads maxlength-handler','placeholder' => trans('boat::template.common.heads') )) !!}
                            <span class="help-block">{{ $errors->first('heads') }}</span>
                          </div>
                        </div>
                      </div>
                      <div class="row">
                        <div class="col-md-4">
                          <div class="form-group @if($errors->first('fresh_water_tank')) has-error @endif form-md-line-input">
                            <label class="form_title">{{ trans('boat::template.common.fresh_water_tank') }}</label>
                            {!! Form::text('fresh_water_tank', isset($boat->varFreshWaterTank)?$boat->varFreshWaterTank:old('fresh_water_tank'), array('maxlength' =>150,'class' => 'form-control maxfresh_water_tank maxlength-handler','placeholder' => trans('boat::template.common.fresh_water_tank') )) !!}
                            <span class="help-block">{{ $errors->first('fresh_water_tank') }}</span>
                          </div>
                        </div>
                        <div class="col-md-4">
                          <div class="form-group @if($errors->first('fuel_tank')) has-error @endif form-md-line-input">
                            <label class="form_title">{{ trans('boat::template.common.fuel_tank') }}</label>
                            {!! Form::text('fuel_tank', isset($boat->varFuelTank)?$boat->varFuelTank:old('fuel_tank'), array('maxlength' =>150,'class' => 'form-control maxfuel_tank maxlength-handler','placeholder' => trans('boat::template.common.fuel_tank') )) !!}
                            <span class="help-block">{{ $errors->first('fuel_tank') }}</span>
                          </div>
                        </div>
                        <div class="col-md-4">
                          <div class="form-group @if($errors->first('holding_tank')) has-error @endif form-md-line-input">
                            <label class="form_title">{{ trans('boat::template.common.holding_tank') }}</label>
                            {!! Form::text('holding_tank', isset($boat->varHoldingTank)?$boat->varHoldingTank:old('holding_tank'), array('maxlength' =>150,'class' => 'form-control maxholding_tank maxlength-handler','placeholder' => trans('boat::template.common.holding_tank') )) !!}
                            <span class="help-block">{{ $errors->first('holding_tank') }}</span>
                          </div>
                        </div>
                      </div>

                      <!-- Builder include -->
                      <div id="body-roll" style="display:none" class="form-group">
                        @if (Config::get('Constant.DEFAULT_VISUAL') == 'Y')
                        @php $sections = []; @endphp
                        @if(isset($boat))
                        @php
                        $sections = json_decode($boat->txtDescription);
                        @endphp
                        @endif
                        @php Powerpanel\VisualComposer\Controllers\VisualComposerController::page_section(['sections'=>$sections]) @endphp
                        @endif
                      </div>

                      <div class="row" style="display:none;">
                        <div class="col-md-8">
                          <div class="form-group">
                            @if ( (isset($boat->chrFeaturedBoat) && $boat->chrFeaturedBoat == 'N') || Request::old('featuredBoat')=='N' || (!isset($boat->chrFeaturedBoat) && Request::old('featuredBoat')==null))
                            @php $featured_checked_no = 'checked' @endphp
                            @else
                            @php $featured_checked_no = '' @endphp
                            @endif
                            @if (isset($boat->chrFeaturedBoat) && $boat->chrFeaturedBoat == 'Y' || (Request::old('featuredBoat') == 'Y'))
                            @php $featured_checked_yes = 'checked' @endphp
                            @else
                            @php $featured_checked_yes = '' @endphp
                            @endif
                            <label class="control-label form_title">{{ trans('boat::template.boatModule.isFeaturedBoat') }}?</label>
                            <div class="md-radio-inline">
                              <div class="md-radio">
                                <input class="md-radiobtn" type="radio" value="Y" name="featuredBoat" id="featuredBoatY" {{ $featured_checked_yes }}>
                                <label for="featuredBoatY"> <span></span> <span class="check"></span> <span class="box"></span> {{ trans('boat::template.common.yes') }} </label>
                              </div>
                              <div class="md-radio">
                                <input class="md-radiobtn" type="radio" value="N" name="featuredBoat" id="featuredBoatN" {{ $featured_checked_no }} />
                                <label for="featuredBoatN"> <span></span> <span class="check"></span> <span class="box"></span> {{ trans('boat::template.common.no') }} </label>
                              </div>
                            </div>
                            <div class="clearfix"></div>
                            <span><strong>{{ trans('boat::template.common.note') }}: {{ trans('boat::template.boatModule.featuredBoatNote') }}*</strong></span>
                          </div>
                        </div>
                      </div>
                      <div class="row">
                        <div class="col-md-12">
                          <div class="nopadding">
                            @include('powerpanel.partials.seoInfo',['form'=>'frmBoat','inf'=>isset($metaInfo)?$metaInfo:false])
                          </div>
                        </div>
                      </div>
                      <h3 class="form-section">{{ trans('boat::template.common.displayinformation') }}</h3>
                      <div class="row">
                        <div class="col-md-6">
                          @php
                          $display_order_attributes = array('class' => 'form-control','maxlength'=>10,'placeholder'=>trans('boat::template.common.displayorder'),'autocomplete'=>'off');
                          @endphp
                          <div class="form-group @if($errors->first('display_order')) has-error @endif form-md-line-input">
                            <label class="form_title" for="site_name">{{ trans('boat::template.common.displayorder') }}<span aria-required="true" class="required"> * </span></label>
                            {!! Form::text('display_order', isset($boat->intDisplayOrder)?$boat->intDisplayOrder:$total, $display_order_attributes) !!}
                            <span class="help-block">
                              {{ $errors->first('display_order') }}
                            </span>
                          </div>
                        </div>
                        <div class="col-md-6">
                          @include('powerpanel.partials.displayInfo',['display' => isset($boat->chrPublish)?$boat->chrPublish:null ])
                        </div>
                      </div>
                    </div>
                    <div class="form-actions">
                      <div class="row">
                        <div class="col-md-12">
                          <button type="submit" name="saveandedit" class="btn btn-green-drake" value="saveandedit" title="{!! trans('boat::template.common.saveandedit') !!}">{!! trans('boat::template.common.saveandedit') !!}</button>
                          <button type="submit" name="saveandexit" class="btn btn-green-drake" value="saveandexit" title="{!! trans('boat::template.common.saveandexit') !!}">{!! trans('boat::template.common.saveandexit') !!}</button>
                          <a class="btn btn-outline red" href="{{ url('powerpanel/boat') }}" title="{{ trans('boat::template.common.cancel') }}">{{ trans('boat::template.common.cancel') }}</a>
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
  </div>
</div>
@if (Config::get('Constant.DEFAULT_VISUAL') == 'Y')
@php Powerpanel\VisualComposer\Controllers\VisualComposerController::get_dialog_maker()@endphp

@endif
@endsection
@section('scripts')
<script type="text/javascript">
  window.site_url = '{!! url("/") !!}';
  var seoFormId = 'frmBoat';
  var user_action = "{{ isset($boat)?'edit':'add' }}";
  var moduleAlias = 'boat';
</script>
<script src="{{ url('resources/global/plugins/bootstrap-maxlength/bootstrap-maxlength.min.js') }}" type="text/javascript"></script>
<script src="{{ url('resources/global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js') }}" type="text/javascript"></script>
<script src="{{ url('resources/global/plugins/fontawesome-iconpicker/dist/js/fontawesome-iconpicker.js')}}" type="text/javascript"></script>
<script src="{{ url('resources/pages/scripts/custom.js') }}" type="text/javascript"></script>
<script src="{{ url('resources/global/plugins/custom-alias/alias-generator.js') }}" type="text/javascript"></script>
<script src="{{ url('resources/global/plugins/seo-generator/seo-info-generator-boat.js') }}" type="text/javascript"></script>
<script src="{{ url('resources/pages/scripts/packages/boat/boat_validations.js') }}" type="text/javascript"></script>
<script src="{{ $CDN_PATH.'resources/global/plugins/bootstrap-fileinput/bootstrap-fileinput.js' }}" type="text/javascript"></script>
@include('powerpanel.partials.ckeditorTestimonial',['config'=>'docsConfig'])
<script type="text/javascript">
  $(function() {
    $('.icp-auto').iconpicker({
      hideOnSelect: true
    });
    $("#yearpicker").datepicker({
      format: 'yyyy',
      minViewMode: "years",
      endDate: '+0d',
      autoclose: true
    });
  });
  $('#lengthft').on('input', function() {
  this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*?)\..*/g, '$1');
});
</script>
@if (Config::get('Constant.DEFAULT_VISUAL') == 'Y')
@php Powerpanel\VisualComposer\Controllers\VisualComposerController::get_builder_css_js()@endphp
@endif
@endsection