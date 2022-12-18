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
                <!-- <div class="tab-pane active" id="general"> -->
                  <div class="portlet-body form_pattern">
                    {!! Form::open(['method' => 'post','id'=>'frmService']) !!}
                    {!! Form::hidden('fkMainRecord', isset($services->fkMainRecord)?$services->fkMainRecord:old('fkMainRecord')) !!}
                                      <div class="form-group @if($errors->first('tag_line')) has-error @endif form-md-line-input">
                                          @if(isset($services))
                                        @if (File::exists(base_path() . '/resources/views/powerpanel/partials/lockedpage.blade.php') != null)
                                        @include('powerpanel.partials.lockedpage',['pagedata'=>$services])
                                        @endif
                                        @endif
                                        @php
                                        if(isset($services_highLight->intFKCategory) && ($services_highLight->intFKCategory != $services->intFKCategory)){
                                        $Class_title = " highlitetext";
                                        }else{
                                        $Class_title = "";
                                        }
                                        $currentCatAlias = '';
                                        @endphp
                                    </div>
                    <div class="form-body">
                      <div class="row">
                        <div class="col-md-12">
                          <div class="form-group @if($errors->first('title')) has-error @endif form-md-line-input">
                              @php if(isset($services_highLight->varTitle) && ($services_highLight->varTitle != $services->varTitle)){
                              $Class_title = " highlitetext";
                              }else{
                              $Class_title = "";
                              } @endphp
                            <label class="form_title" for="site_name">{{ trans('services::template.common.title') }} <span aria-required="true" class="required"> * </span></label>
                            {!! Form::text('title', isset($service->varTitle)?$service->varTitle:old('title'), array('maxlength' => 150, 'class' => 'form-control hasAlias seoField maxlength-handler','autocomplete'=>'off','data-url' => 'powerpanel/services')) !!}
                            <span class="help-block">
                              {{ $errors->first('title') }}
                            </span>
                          </div>
                        </div>
                      </div>
                      <!-- code for alias -->
                      {!! Form::hidden(null, null, array('class' => 'hasAlias','data-url' => 'powerpanel/services')) !!}
                      {!! Form::hidden('alias', isset($service->alias->varAlias)?$service->alias->varAlias:old('alias'), array('class' => 'aliasField')) !!}
                      {!! Form::hidden('oldAlias', isset($service->alias->varAlias)?$service->alias->varAlias:old('alias')) !!}
                      {!! Form::hidden('previewId') !!}
                      <div class="form-group alias-group {{!isset($service)?'hide':''}}">
                        <label class="form_title" for="{{ trans('services::template.url') }}">{{ trans('services::template.common.url') }} :</label>
                        <a href="javascript:void;" class="alias">{!! url("/") !!}</a>
                        <a href="javascript:void(0);" class="editAlias" title="Edit">
                          <i class="fa fa-edit"></i>
                          <a class="without_bg_icon openLink" title="Open Link" target="_blank" href="{{url('services/'.(isset($service->alias->varAlias) && isset($service)?$service->alias->varAlias:''))}}">
                            <i class="fa fa-external-link" aria-hidden="true"></i>
                          </a>
                        </a>
                      </div>
                      <span class="help-block">
                        {{ $errors->first('alias') }}
                      </span>
                      <!-- code for alias -->
                     @can('service-category-list') 
                      <div class="row">
                        <div class="col-md-12">
                          <div class="form-group">
                            <label class="form_title" for="category_id">{{ trans('services::template.common.selectcategory') }}</label>
                            <div id="categoryDropdown">
                              @php echo $categoryHeirarchyMain; @endphp
                            </div>
                          </div>
                        </div>
                       </div>
                      @endcan 
                      
                      <div class="row">
                        <div class="col-md-12 ">
                          <div class="form-group font_icons_file @if($errors->first('font_awesome_icon')) has-error @endif ">
                            <label class="form_title" for="font_awesome_icon">{{ trans('services::template.common.serviceIcon') }}</label>
                            {!! Form::text('font_awesome_icon', isset($service->varFontAwesomeIcon)?$service->varFontAwesomeIcon:old('font_awesome_icon'), array('id'=>"e4_element", 'data-placement'=>"bottomRight", 'class' => 'form-control icp icp-auto','placeholder' => trans('services::template.common.selectIcon'),'autocomplete'=>'off','readonly'=> 'readonly')) !!}
                            <span class="input-group-addon"></span>
                          </div>
                        </div>
                      </div>
                      
                      @include('powerpanel.partials.imageControl',['type' => 'multiple','label' => trans('services::template.common.selectimage') ,'data'=> isset($service)?$service:null , 'id' => 'service_image', 'name' => 'img_id', 'settings' => $settings, 'width' => '500', 'height' => '500'])

                      @include('powerpanel.partials.videoControl',['type' => 'multiple' ,'label' => trans('services::template.serviceModule.selectVideo') ,'data'=> isset($service)?$service:null,'videoData' => isset($videoData)?$videoData:null , 'id' => 'service_video', 'name' => 'video_id'])

                        <div class="row">
                          <div class="col-md-12">
                            <div class="form-group @if($errors->first('short_description')) has-error @endif form-md-line-input">
                              <label class="form_title">{{ trans('services::template.common.shortdescription') }}<span aria-required="true" class="required"> * </span></label>
                              {!! Form::textarea('short_description', isset($service->txtShortDescription)?$service->txtShortDescription:old('short_description'), array('maxlength' => isset($settings->short_desc_length)?$settings->short_desc_length:400,'class' => 'form-control seoField maxlength-handler','id'=>'varShortDescription','rows'=>'3')) !!}
                              <span class="help-block">{{ $errors->first('short_description') }}</span>
                            </div>
                          </div>
                        </div>
                        
                         <!-- Builder include -->
                          <div id="body-roll" class="form-group">
                            @if (Config::get('Constant.DEFAULT_VISUAL') == 'Y')
                              @php $sections = []; @endphp
                              @if(isset($service))
                                @php
                                $sections = json_decode($service->txtDescription);
                                @endphp
                              @endif
                              @php Powerpanel\VisualComposer\Controllers\VisualComposerController::page_section(['sections'=>$sections]) @endphp
                            @endif
			                    </div>

                        <div class="row">
                          <div class="col-md-8">
                            <div class="form-group">
                              @if ( (isset($service->chrFeaturedService) && $service->chrFeaturedService == 'N') || Request::old('featuredService')=='N' || (!isset($service->chrFeaturedService) && Request::old('featuredService')==null))
                              @php  $featured_checked_no = 'checked'  @endphp
                              @else
                              @php  $featured_checked_no = ''  @endphp
                              @endif
                              @if (isset($service->chrFeaturedService) && $service->chrFeaturedService == 'Y' || (Request::old('featuredService') == 'Y'))
                              @php  $featured_checked_yes = 'checked'  @endphp
                              @else
                              @php  $featured_checked_yes = ''  @endphp
                              @endif
                              <label class="control-label form_title">{{ trans('services::template.serviceModule.isFeaturedService') }}?</label>
                              <div class="md-radio-inline">
                                <div class="md-radio">
                                  <input class="md-radiobtn" type="radio" value="Y" name="featuredService" id="featuredServiceY" {{ $featured_checked_yes }}>
                                  <label for="featuredServiceY"> <span></span> <span class="check"></span> <span class="box"></span> {{ trans('services::template.common.yes') }} </label>
                                </div>
                                <div class="md-radio">
                                  <input class="md-radiobtn" type="radio" value="N" name="featuredService" id="featuredServiceN" {{ $featured_checked_no }}/>
                                  <label for="featuredServiceN"> <span></span> <span class="check"></span> <span class="box"></span> {{ trans('services::template.common.no') }} </label>
                                </div>
                              </div>
                              <div class="clearfix"></div>
                              <span><strong>{{ trans('services::template.common.note') }}: {{ trans('services::template.serviceModule.featuredServiceNote') }}*</strong></span>
                            </div>
                          </div>
                        </div>
                        <div class="row">
                          <div class="col-md-12">
                            <div class="nopadding">
                              @include('powerpanel.partials.seoInfo',['form'=>'frmService','inf'=>isset($metaInfo)?$metaInfo:false])
                            </div>
                          </div>
                        </div>
                        <h3 class="form-section">{{ trans('services::template.common.displayinformation') }}</h3>
                        <div class="row">
                          <div class="col-md-6">
                            @php
                            $display_order_attributes = array('class' => 'form-control','maxlength'=>10,'placeholder'=>trans('services::template.common.displayorder'),'autocomplete'=>'off');
                            @endphp
                            <div class="form-group @if($errors->first('display_order')) has-error @endif form-md-line-input">
                              <label class="form_title" for="site_name">{{ trans('services::template.common.displayorder') }}<span aria-required="true" class="required"> * </span></label>
                              {!! Form::text('display_order',  isset($service->intDisplayOrder)?$service->intDisplayOrder:$total, $display_order_attributes) !!}
                              <span class="help-block">
                                {{ $errors->first('display_order') }}
                              </span>
                            </div>
                          </div>
                          <div class="col-md-6">
                          @if(isset($services_highLight->chrPublish) && ($services_highLight->chrPublish != $services->chrPublish))
                                            @php $Class_chrPublish = " highlitetext"; @endphp
                                            @else
                                            @php $Class_chrPublish = ""; @endphp
                                            @endif
                            @include('powerpanel.partials.displayInfo',['Class_chrPublish'=>$Class_chrPublish,'display' => isset($service->chrPublish)?$service->chrPublish:null ])
                          </div>
                        </div>
                      </div>
                      <div class="form-actions">
                        <div class="row">
                          <div class="col-md-12">
                            @if(isset($services->fkMainRecord) && $services->fkMainRecord != 0)
                            <button type="submit" name="saveandexit" class="btn btn-green-drake" value="saveandexit">{!! trans('services::template.common.approve') !!}</button>
                            @else
                            @if($userIsAdmin)
                            <button type="submit" name="saveandedit" class="btn btn-green-drake" value="saveandedit">{!! trans('services::template.common.saveandedit') !!}</button>
                            <button type="submit" name="saveandexit" class="btn btn-green-drake" value="saveandexit">{!! trans('services::template.common.saveandexit') !!}</button>
                            @else
                            @if((isset($chrNeedAddPermission) && $chrNeedAddPermission == 'N') && (isset($charNeedApproval) && $charNeedApproval == 'N'))
                            <button type="submit" name="saveandexit" class="btn btn-green-drake" value="saveandexit">{!! trans('services::template.common.saveandexit') !!}</button>
                            @else
                            <button type="submit" name="saveandexit" class="btn btn-green-drake" value="approvesaveandexit">{!! trans('services::template.common.approvesaveandexit') !!}</button>
                            @endif
                            @endif
                            @endif
                            <a class="btn btn-outline red" href="{{ url('powerpanel/services') }}">{{ trans('services::template.common.cancel') }}</a>
                            @if(isset($services) && !empty($services) && $userIsAdmin)
                            &nbsp;<a class="btn btn-green-drake" title="Preview" onClick="generatePreview('{{url('/previewpage?url='.(App\Helpers\MyLibrary::getFrontUri('services')['uri']))}}');">Preview</a>
                            @endif
                          </div>
                        </div>
                      </div>
                      {!! Form::close() !!}
                    </div>
                  </div>
                <!-- </div> -->
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  @include('powerpanel.partials.addCat',['module' => 'service-category','categoryHeirarchy' => $categoryHeirarchy])
  @if (Config::get('Constant.DEFAULT_VISUAL') == 'Y')
    @php Powerpanel\VisualComposer\Controllers\VisualComposerController::get_dialog_maker()@endphp
    @php Powerpanel\VisualComposer\Controllers\VisualComposerController::get_visual_checkEditor()@endphp
  @endif
  @endsection
  @section('scripts')
  <script type="text/javascript">
  window.site_url =  '{!! url("/") !!}';
  var seoFormId = 'frmService';
  var user_action = "{{ isset($service)?'edit':'add' }}";
  var moduleAlias = "{{ App\Helpers\MyLibrary::getFrontUri('services')['moduleAlias'] }}";
  var categoryAllowed = false;
  var preview_add_route = '{!! route("powerpanel.services.addpreview") !!}';
  var previewForm = $('#frmServices');
  var isDetailPage = true;

  </script>
  <script src="{{ url('resources/global/plugins/bootstrap-maxlength/bootstrap-maxlength.min.js') }}" type="text/javascript"></script>
  <script src="{{ url('resources/global/plugins/fontawesome-iconpicker/dist/js/fontawesome-iconpicker.js')}}" type="text/javascript"></script>
  <script src="{{ url('resources/pages/scripts/custom.js') }}" type="text/javascript"></script>
  <script src="{{ url('resources/global/plugins/custom-alias/alias-generator.js') }}" type="text/javascript"></script>
  <script src="{{ url('resources/global/plugins/seo-generator/seo-info-generator.js') }}" type="text/javascript"></script>
  <script src="{{ url('resources/pages/scripts/packages/services/service_validations.js') }}" type="text/javascript"></script>
  <script type="text/javascript">
  $(function() {
    $('.icp-auto').iconpicker({
      hideOnSelect: true
    });
  });

  function OpenPassword(val) {
  if (val == 'PP') {
  $("#passid").show();
  } else {
  $("#passid").hide();
  }
  }
  </script>
  @if (Config::get('Constant.DEFAULT_VISUAL') == 'Y')
    @php Powerpanel\VisualComposer\Controllers\VisualComposerController::get_builder_css_js()@endphp
  @endif
  @endsection