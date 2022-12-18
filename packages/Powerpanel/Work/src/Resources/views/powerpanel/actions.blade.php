@section('css')
<link href="{{ url('resources/global/plugins/fontawesome-iconpicker/dist/css/fontawesome-iconpicker.min.css') }}" rel="stylesheet" type="text/css"/>
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
                    {!! Form::open(['method' => 'post','id'=>'frmWork']) !!}
                    <div class="form-body">
                      <div class="row">
                        <div class="col-md-12">
                          <div class="form-group @if($errors->first('title')) has-error @endif form-md-line-input">
                            <label class="form_title" for="site_name">{{ trans('work::template.common.title') }} <span aria-required="true" class="required"> * </span></label>
                            {!! Form::text('title', isset($work->varTitle)?$work->varTitle:old('title'), array('maxlength' => 50, 'class' => 'form-control hasAlias seoField maxlength-handler','autocomplete'=>'off','data-url' => 'powerpanel/work', 'placeholder' => 'Title')) !!}
                            <span class="help-block">
                              {{ $errors->first('title') }}
                            </span>
                          </div>
                        </div>
                      </div>
                      <!-- code for alias -->
                      {!! Form::hidden(null, null, array('class' => 'hasAlias','data-url' => 'powerpanel/work')) !!}
                      {!! Form::hidden('alias', isset($work->alias->varAlias)?$work->alias->varAlias:old('alias'), array('class' => 'aliasField')) !!}
                      {!! Form::hidden('oldAlias', isset($work->alias->varAlias)?$work->alias->varAlias:old('alias')) !!}
                      <div class="form-group alias-group {{!isset($work)?'hide':''}}">
                        <label class="form_title" for="{{ trans('work::template.url') }}">{{ trans('work::template.common.url') }} :</label>
                        <a href="javascript:void;" class="alias">{!! url("/") !!}</a>
                        <a href="javascript:void(0);" class="editAlias" title="Edit">
                          <i class="fa fa-edit"></i>
                          <a class="without_bg_icon openLink" title="Open Link" target="_blank" href="{{url('work/'.(isset($work->alias->varAlias) && isset($work)?$work->alias->varAlias:''))}}">
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
                            <label class="form_title" for="font_awesome_icon">{{ trans('work::template.common.workIcon') }}</label>
                            {!! Form::text('font_awesome_icon', isset($work->varFontAwesomeIcon)?$work->varFontAwesomeIcon:old('font_awesome_icon'), array('id'=>"e4_element", 'data-placement'=>"bottomRight", 'class' => 'form-control icp icp-auto','placeholder' => trans('work::template.common.selectIcon'),'autocomplete'=>'off','readonly'=> 'readonly')) !!}
                            <span class="input-group-addon"></span>
                          </div>
                        </div>
                      </div>
                      
                      @include('powerpanel.partials.imageControl',['type' => 'multiple','label' => trans('work::template.common.selectimage') ,'data'=> isset($work)?$work:null , 'id' => 'work_image', 'name' => 'img_id', 'settings' => $settings, 'width' => '1349', 'height' => '604'])
                      <!-- <div class="row">
                          <div class="col-md-12">
                            @if(isset($work->fkIntImgId) && ($work->fkIntImgId != $work->fkIntImgId))
                            @php $Class_fkIntImgId = " highlitetext"; @endphp
                            @else
                            @php $Class_fkIntImgId = ""; @endphp
                            @endif
                            <div class="image_thumb multi_upload_images">
                              <div class="form-group">
                                <label class="form_title {{ $Class_fkIntImgId }}" for="front_logo">{{ trans('work::template.common.selectimage') }} <span aria-required="true" class="required"> * </span></label>
                                <div class="clearfix"></div>
                                <div class="fileinput fileinput-new" data-provides="fileinput">
                                  <div class="fileinput-preview thumbnail photo_album_image_img" data-trigger="fileinput" style="width:100%;float:left; height:120px;position: relative;">
                                    @if(old('image_url'))
                                    <img src="{{ old('image_url') }}" />
                                    @elseif(isset($work->fkIntImgId))
                                    <img src="{!! App\Helpers\resize_image::resize($work->fkIntImgId,120,120) !!}" />
                                    @else
                                    <img class="img_opacity" src="{{ $CDN_PATH.'resources/images/upload_file.gif' }}" />
                                    @endif
                                  </div>

                                  <div class="input-group">
                                    <a class="media_manager" data-multiple="false" onclick="MediaManager.open('photo_album_image');"><span class="fileinput-new"></span></a>
                                    
                                    <input class="form-control" type="hidden" id="photo_album_image" name="img_id" value="{{ isset($work->fkIntImgId)?$work->fkIntImgId:old('img_id') }}" />
                                    @php
                                      if (method_exists($MyLibrary, 'GetFolderID')) {
                                    if(isset($work->fkIntImgId)){
                                    $folderid = App\Helpers\MyLibrary::GetFolderID($work->fkIntImgId);
                                    @endphp
                                    @if(isset($folderid->fk_folder) && $folderid->fk_folder != '0')
                                    <input class="form-control" type="hidden" id="folder_id" name="folder_id" value="{{ $folderid->fk_folder }}" />
                                    @endif
                                    @php
                                    }
                                    }
                                    @endphp
                                    <input class="form-control" type="hidden" id="image_url" name="image_url" value="{{ old('image_url') }}" />
                                  </div>
                                  <div class="overflow_layer">
                                    <a onclick="MediaManager.open('photo_album_image');" class="media_manager remove_img"><i class="fa fa-pencil"></i></a>
                                    <a href="javascript:;" class="fileinput-exists remove_img removeimg" data-dismiss="fileinput"><i class="fa fa-trash-o"></i></a>
                                  </div>
                                </div>
                                <div class="clearfix"></div>
                                @php $height = isset($settings->height)?$settings->height:433; $width = isset($settings->width)?$settings->width:650; @endphp <span>{{ trans('work::template.common.imageSize',['height'=>$height, 'width'=>$width]) }}</span>
                              </div>
                              <span class="help-block">
                                {{ $errors->first('img_id') }}
                              </span>
                            </div>
                          </div>
                      </div> -->
                      <div style="display: none;">
                      @include('powerpanel.partials.videoControl',['type' => 'multiple' ,'label' => trans('work::template.workModule.selectVideo') ,'data'=> isset($work)?$work:null,'videoData' => isset($videoData)?$videoData:null , 'id' => 'work_video', 'name' => 'video_id'])
                      </div>
                        <div class="row">
                          <div class="col-md-12">
                            <div class="form-group @if($errors->first('short_description')) has-error @endif form-md-line-input">
                              <label class="form_title">{{ trans('work::template.common.shortdescription') }}<span aria-required="true" class="required"> * </span></label>
                              {!! Form::textarea('short_description', isset($work->txtShortDescription)?$work->txtShortDescription:old('short_description'), array('maxlength' => isset($settings->short_desc_length)?$settings->short_desc_length:400,'class' => 'form-control seoField maxlength-handler','id'=>'varShortDescription','rows'=>'3','placeholder' => 'Short Description')) !!}
                              <span class="help-block">{{ $errors->first('short_description') }}</span>
                            </div>
                          </div>
                        </div>
                        
                         <!-- Builder include -->
                         <div id="body-roll" class="form-group">
                            @if (Config::get('Constant.DEFAULT_VISUAL') == 'Y')
                              @php $sections = []; @endphp
                              @if(isset($work))
                                @php
                                $sections = json_decode($work->txtDescription);
                                @endphp
                              @endif
                              @php Powerpanel\VisualComposer\Controllers\VisualComposerController::page_section(['sections'=>$sections]) @endphp
                            @endif
			</div>

                        <div class="row" style="display:none;">
                          <div class="col-md-8">
                            <div class="form-group">
                              @if ( (isset($work->chrFeaturedWork) && $work->chrFeaturedWork == 'N') || Request::old('featuredWork')=='N' || (!isset($work->chrFeaturedWork) && Request::old('featuredWork')==null))
                              @php  $featured_checked_no = 'checked'  @endphp
                              @else
                              @php  $featured_checked_no = ''  @endphp
                              @endif
                              @if (isset($work->chrFeaturedWork) && $work->chrFeaturedWork == 'Y' || (Request::old('featuredWork') == 'Y'))
                              @php  $featured_checked_yes = 'checked'  @endphp
                              @else
                              @php  $featured_checked_yes = ''  @endphp
                              @endif
                              <label class="control-label form_title">{{ trans('work::template.workModule.isFeaturedWork') }}?</label>
                              <div class="md-radio-inline">
                                <div class="md-radio">
                                  <input class="md-radiobtn" type="radio" value="Y" name="featuredWork" id="featuredWorkY" {{ $featured_checked_yes }}>
                                  <label for="featuredWorkY"> <span></span> <span class="check"></span> <span class="box"></span> {{ trans('work::template.common.yes') }} </label>
                                </div>
                                <div class="md-radio">
                                  <input class="md-radiobtn" type="radio" value="N" name="featuredWork" id="featuredWorkN" {{ $featured_checked_no }}/>
                                  <label for="featuredWorkN"> <span></span> <span class="check"></span> <span class="box"></span> {{ trans('work::template.common.no') }} </label>
                                </div>
                              </div>
                              <div class="clearfix"></div>
                              <span><strong>{{ trans('work::template.common.note') }}: {{ trans('work::template.workModule.featuredWorkNote') }}*</strong></span>
                            </div>
                          </div>
                        </div>
                        <div class="row">
                          <div class="col-md-12">
                            <div class="nopadding">
                              @include('powerpanel.partials.seoInfo',['form'=>'frmWork','inf'=>isset($metaInfo)?$metaInfo:false])
                            </div>
                          </div>
                        </div>
                        <h3 class="form-section">{{ trans('work::template.common.displayinformation') }}</h3>
                        <div class="row">
                          <div class="col-md-6">
                            @php
                            $display_order_attributes = array('class' => 'form-control','maxlength'=>10,'placeholder'=>trans('work::template.common.displayorder'),'autocomplete'=>'off');
                            @endphp
                            <div class="form-group @if($errors->first('display_order')) has-error @endif form-md-line-input">
                              <label class="form_title" for="site_name">{{ trans('work::template.common.displayorder') }}<span aria-required="true" class="required"> * </span></label>
                              {!! Form::text('display_order',  isset($work->intDisplayOrder)?$work->intDisplayOrder:$total, $display_order_attributes) !!}
                              <span class="help-block">
                                {{ $errors->first('display_order') }}
                              </span>
                            </div>
                          </div>
                          <div class="col-md-6">
                            @include('powerpanel.partials.displayInfo',['display' => isset($work->chrPublish)?$work->chrPublish:null ])
                          </div>
                        </div>
                      </div>
                      <div class="form-actions">
                        <div class="row">
                          <div class="col-md-12">
                            <button type="submit" name="saveandedit" class="btn btn-green-drake" value="saveandedit" title="{!! trans('work::template.common.saveandedit') !!}">{!! trans('work::template.common.saveandedit') !!}</button>
                            <button type="submit" name="saveandexit" class="btn btn-green-drake" value="saveandexit" title="{!! trans('work::template.common.saveandexit') !!}">{!! trans('work::template.common.saveandexit') !!}</button>
                            <a class="btn btn-outline red" href="{{ url('powerpanel/work') }}" title="{{ trans('work::template.common.cancel') }}">{{ trans('work::template.common.cancel') }}</a>
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
    @php Powerpanel\VisualComposer\Controllers\VisualComposerController::get_visual_checkEditor()@endphp
  @endif
  @endsection
  @section('scripts')
  <script type="text/javascript">
  window.site_url =  '{!! url("/") !!}';
  var seoFormId = 'frmWork';
  var user_action = "{{ isset($work)?'edit':'add' }}";
  var moduleAlias = 'work';

  </script>
  <script src="{{ url('resources/global/plugins/bootstrap-maxlength/bootstrap-maxlength.min.js') }}" type="text/javascript"></script>
  <script src="{{ url('resources/global/plugins/fontawesome-iconpicker/dist/js/fontawesome-iconpicker.js')}}" type="text/javascript"></script>
  <script src="{{ url('resources/pages/scripts/custom.js') }}" type="text/javascript"></script>
  <script src="{{ url('resources/global/plugins/custom-alias/alias-generator.js') }}" type="text/javascript"></script>
  <script src="{{ url('resources/global/plugins/seo-generator/seo-info-generator.js') }}" type="text/javascript"></script>
  <script src="{{ url('resources/pages/scripts/packages/work/work_validations.js') }}" type="text/javascript"></script>
  <script type="text/javascript">
  $(function() {
    $('.icp-auto').iconpicker({
      hideOnSelect: true
    });
  });
  </script>
  @if (Config::get('Constant.DEFAULT_VISUAL') == 'Y')
    @php Powerpanel\VisualComposer\Controllers\VisualComposerController::get_builder_css_js()@endphp
  @endif
  @endsection