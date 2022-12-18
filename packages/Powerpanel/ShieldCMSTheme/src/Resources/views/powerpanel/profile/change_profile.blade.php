@section('css')
<link href="{{ $CDN_PATH.'resources/global/plugins/bootstrap-fileinput/bootstrap-fileinput.css' }}" rel="stylesheet" type="text/css" />
<link href="{{ $CDN_PATH.'resources/global/plugins/select2/css/select2.min.css' }}" rel="stylesheet" type="text/css"/>
<link href="{{ $CDN_PATH.'resources/global/plugins/select2/css/select2-bootstrap.min.css' }}" rel="stylesheet" type="text/css"/>
@endsection
@extends('powerpanel.layouts.app')
@section('title')
{{Config::get('Constant.SITE_NAME')}} - PowerPanel
@endsection
@section('content')
@include('powerpanel.partials.breadcrumbs')
<div class="col-md-12">
    @if(Session::has('message'))
    <div class="row">
        <div class="alert alert-success">
            <button class="close" data-close="alert"></button>
            {{ Session::get('message') }}
        </div>
    </div>
    @endif
    <div class="row">
        <div class="portlet light bdisplay_ordered">
            <div class="portlet-body form_pattern">
                <div class="tabbable tabbable-tabdrop">
                    <div class="tab-content">
                        {!! Form::open(['method' => 'post','id'=>'changeProfile']) !!}
                        <div class="form-body">
                            <div class="form-group {{ $errors->has('name') ? 'has-error' : '' }} form-md-line-input">
                                <label class="form_title" for="name">{{  trans('shiledcmstheme::template.common.name') }} <span aria-required="true" class="required"> * </span></label>
                                {!! Form::text('name',$user_data->name,array('class' => 'form-control input-sm', 'maxlength'=>'150','id' => 'name','autocomplete'=>'off','placeholder'=> trans('shiledcmstheme::template.common.name'))) !!}
                                <span class="help-block">
                                    {{ $errors->first('name') }}
                                </span>
                            </div>
                            <div class="form-group {{ $errors->has('email') ? 'has-error' : '' }} form-md-line-input">
                                <label class="form_title" for="email">{{  trans('shiledcmstheme::template.common.email') }} <span aria-required="true" class="required"> * </span></label>
                                {!! Form::text('email',$user_data->email,array('class' => 'form-control input-sm', 'maxlength'=>'100','id' => 'email','autocomplete'=>'off','placeholder'=> trans('shiledcmstheme::template.common.email'))) !!}
                                <span class="help-block">
                                    {{ $errors->first('email') }}
                                </span>
                            </div>

                            <div class="form-group {{ $errors->has('personalId') ? 'has-error' : '' }} form-md-line-input">
                                <label class="form_title" for="email">Personal Email <span aria-required="true" class="required"> * </span></label>
                                {!! Form::text('personalId',$user_data->personalId,array('class' => 'form-control input-sm', 'maxlength'=>'100','id' => 'personalId','autocomplete'=>'off','placeholder'=>'Personal Email')) !!}
                                <span class="help-block">
                                    {{ $errors->first('personalId') }}
                                </span>
                                <div class="note_text"><strong>Note:</strong> Forgot password email will land on mentioned email id.</div>
                            </div> 

                            <div class="form-group {{ $errors->has('user_photo') ? ' has-error' : '' }}">
                                <div class="image_thumb">
                                    <label class="form_title" for="front_logo">{{  trans('shiledcmstheme::template.myProfile.profilephoto') }}</label>
                                    <div class="fileinput fileinput-new" data-provides="fileinput">
                                        <div class="fileinput-preview thumbnail user_photo_img" data-trigger="fileinput" style="width:100%; height:120px;position: relative;">
                                            @if(old('image_url'))
                                            <img src="{{ old('image_url') }}" />
                                            @elseif ($user_photo != '')
                                            <img src="{{ $user_photo }}" />
                                            @else
                                            <img  src="{{ url('resources/images/man.png') }}"/>
                                            @endif
                                        </div>
                                        <div class="input-group">
                                            <a class="media_manager" onclick="MediaManager.open('user_photo')"><span class="fileinput-new"></a>
                                            <input class="form-control" type="hidden" id="user_photo" name="user_photo" value="{{$user_data->fkIntImgId}}"/>
                                            <input class="form-control" type="hidden" id="image_url" name="image_url" value="{{ old('image_url') }}" />
                                        </div>
                                        <div class="overflow_layer">
                                            <a onclick="MediaManager.open('user_photo');" class="media_manager remove_img"><i class="fa fa-pencil"></i></a>
                                            <a href="javascript:;" class="fileinput-exists remove_img removeimg" data-dismiss="fileinput"><i class="fa fa-trash-o"></i></a>
                                        </div>
                                    </div>
                                    <div class="clearfix"></div>
                                    @php $height = isset($settings->height)?$settings->height:500; $width = isset($settings->width)?$settings->width:500; @endphp <span>{{  trans('shiledcmstheme::template.common.imageSize',['height'=>$height, 'width'=>$width]) }}</span>
                                    <span class="help-block">
                                        {{ $errors->first('user_photo') }} {{ $errors->first('user_photo') }}
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="form-actions">
                            <div class="row">
                                <div class="col-md-12">
                                    <button type="submit" name="save-settings" class="btn btn-green-drake" value="Update Profile">{{  trans('shiledcmstheme::template.myProfile.updateprofile') }}</button>
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
<div class="clearfix"></div>
@endsection
@section('scripts')
<script src="{{ $CDN_PATH.'resources/pages/scripts/packages/profile/change_profile.js' }}" type="text/javascript"></script>
<script src="{{ $CDN_PATH.'resources/global/plugins/select2/js/select2.full.min.js' }}" type="text/javascript"></script>
<script src="{{ $CDN_PATH.'resources/global/plugins/select2/js/components-select2.min.js' }}" type="text/javascript"></script>
@endsection