@extends('powerpanel.layouts.app')
@section('title')
{{Config::get('Constant.SITE_NAME')}} - PowerPanel
@endsection
@section('content')
@php $settings = json_decode(Config::get("Constant.MODULE.SETTINGS")); @endphp
@include('powerpanel.partials.breadcrumbs')
<div class="row">
	<div class="col-md-12">
		@if(Session::has('message'))
		<div class="alert alert-success">
			<button class="close" data-close="alert"></button>
			{{ Session::get('message') }}
		</div>
		@endif
		<div class="portlet light bordered">
			<div class="portlet-body">
				<div class="tabbable tabbable-tabdrop">
					<div class="tab-content settings">
						<div class="tab-pane active form_pattern" id="general">
							{!! Form::open(['method' => 'post','id'=>'frmTeam']) !!}
							<div class="form-group @if($errors->first('name')) has-error @endif form-md-line-input">
								<label class="form_title" for="site_name">{{ trans('team::template.common.name') }} <span aria-required="true" class="required"> * </span></label>
								{!! Form::text('name', isset($team->varTitle)?$team->varTitle:old('name'), array('maxlength'=>150, 'class' => 'hasAlias form-control seoField maxlength-handler', 'placeholder' => trans("team::template.common.name"),'autocomplete'=>'off','data-url' => 'powerpanel/team')) !!}
								<span class="help-block">
									{{ $errors->first('name') }}
								</span>
							</div>
							<!-- code for alias -->
							{!! Form::hidden(null, null, array('class' => 'hasAlias','data-url' => 'powerpanel/team')) !!}
							{!! Form::hidden('alias', isset($team->alias->varAlias)?$team->alias->varAlias:old('alias') , array('class' => 'aliasField')) !!}
							{!! Form::hidden('oldAlias', isset($team->alias->varAlias)?$team->alias->varAlias:old('alias')) !!}
							<div class="form-group alias-group {{!isset($team->alias->varAlias)?'hide':''}}">
								<label class="form_title" for="Url">{{ trans('team::template.common.url') }} :</label>
								<a href="javascript:void;" class="alias">{!! url("/") !!}</a>
								<a href="javascript:void(0);" class="editAlias" title="{{ trans('team::template.common.edit') }}">
									<i class="fa fa-edit"></i>
								</a>
								&nbsp;
								 <a class="without_bg_icon openLink" title="{{ trans('team::template.common.openLink') }}" target="_blank" href="{{url('team/'.(isset($team->alias->varAlias) && isset($team)?$team->alias->varAlias:''))}}"><i class="fa fa-external-link" aria-hidden="true"></i></a>
							</div>
							<span class="help-block">
								{{ $errors->first('alias') }}
							</span>
							<!-- code for alias -->
							<div class="form-group {{ $errors->has('email') ? 'has-error' : '' }} form-md-line-input">
								<label class="form_title" for="email">{{ trans('team::template.common.email') }} <span aria-required="true" class="required"> * </span></label>
								{!! Form::email('email',isset($team->varEmail)?$team->varEmail:old('email'), array('class' => 'form-control input-sm', 'maxlength'=>'300','id' => 'email','placeholder' => trans('team::template.common.email'),'autocomplete'=>'off')) !!}
								<span class="help-block">
									{{ $errors->first('email') }}
								</span>
							</div>

							<div class="form-group {{ $errors->has('phone_no') ? 'has-error' : '' }} form-md-line-input">
								<label class="form_title" for="phone_no">{{ trans('team::template.common.phoneno') }} </label>
								{!! Form::tel('phone_no',isset($team->varPhoneNo)?$team->varPhoneNo:old('phone_no'), array('class' => 'form-control input-sm','id' => 'phone_no','minlength'=>'6','maxlength'=>'20','onpaste'=>'return false;', 'ondrop'=>'return false;','placeholder' => trans('team::template.common.phoneno'),'autocomplete'=>'off', 'onkeypress'=>'javascript: return KeycheckOnlyPhonenumber(event);')) !!}
								<span class="help-block">
									{{ $errors->first('phone_no') }}
								</span>
							</div>

							<!-- <div class="form-group @if($errors->first('department')) has-error @endif form-md-line-input">
								<label class="form_title" for="site_name">{{ trans('team::template.teamModule.department') }}</label>
								{!! Form::text('department', isset($team->varDepartment)?$team->varDepartment:old('department'), array('maxlength' => 100,'placeholder' => trans("team::template.teamModule.department"),'class' => 'form-control maxlength-handler','autocomplete'=>'off')) !!}
								<span class="help-block"> {{ $errors->first('department') }} </span>
							</div> -->
							
							<div class="form-group @if($errors->first('tag_line')) has-error @endif form-md-line-input">
								<label class="form_title" for="site_name">{{ trans('team::template.teamModule.designation') }}</label>
								{!! Form::text('tag_line', isset($team->varTagLine)?$team->varTagLine:old('tag_line'), array('maxlength'=>100,'placeholder' => trans("team::template.teamModule.designation"),'class' => 'form-control maxlength-handler','autocomplete'=>'off')) !!}
								<span class="help-block">
									{{ $errors->first('tag_line') }}
								</span>
							</div>

							<!-- <div class="form-group form-md-line-input">
								<label class="form_title" for="address">{{ trans('team::template.common.address') }}</label>
								{!! Form::textarea('address',isset($team->textAddress)?$team->textAddress:old('address'), array('class' => 'form-control maxlength-handler','maxlength'=>'400','id'=>'address','rows'=>'3','placeholder'=>trans('team::template.common.address'),'styel'=>'max-height:80px;')) !!}
							</div> -->

							<!-- @include('powerpanel.partials.imageControl',['type' => 'single','label' => trans('team::template.common.selectimage') ,'data'=> isset($team)?$team:null , 'id' => 'member_image', 'name' => 'img_id', 'settings' => $settings, 'width' => '500', 'height' => '500']) -->
							<div class="row">
                        <div class="col-md-12">
                          @if(isset($team->fkIntImgId) && ($team->fkIntImgId != $team->fkIntImgId))
                          @php $Class_fkIntImgId = " highlitetext"; @endphp
                          @else
                          @php $Class_fkIntImgId = ""; @endphp
                          @endif
                          <div class="image_thumb multi_upload_images">
                            <div class="form-group">
                              <label class="form_title {{ $Class_fkIntImgId }}" for="front_logo">{{ trans('team::template.common.selectimage') }} <span aria-required="true" class="required"> * </span></label>
                              <div class="clearfix"></div>
                              <div class="fileinput fileinput-new" data-provides="fileinput">
                                <div class="fileinput-preview thumbnail photo_album_image_img" data-trigger="fileinput" style="width:100%;float:left; height:120px;position: relative;">
                                  @if(old('image_url'))
                                  <img src="{{ old('image_url') }}" />
                                  @elseif(isset($team->fkIntImgId))
                                  <img src="{!! App\Helpers\resize_image::resize($team->fkIntImgId,120,120) !!}" />
                                  @else
                                  <img class="img_opacity" src="{{ $CDN_PATH.'resources/images/upload_file.gif' }}" />
                                  @endif
                                </div>

                                <div class="input-group">
                                  <a class="media_manager" data-multiple="false" onclick="MediaManager.open('photo_album_image');"><span class="fileinput-new"></span></a>
                                  
                                  <input class="form-control" type="hidden" id="photo_album_image" name="img_id" value="{{ isset($team->fkIntImgId)?$team->fkIntImgId:old('img_id') }}" />
                                  @php
                                    if (method_exists($MyLibrary, 'GetFolderID')) {
                                  if(isset($team->fkIntImgId)){
                                  $folderid = App\Helpers\MyLibrary::GetFolderID($team->fkIntImgId);
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
                              @php $height = isset($settings->height)?$settings->height:600; $width = isset($settings->width)?$settings->width:500; @endphp <span>{{ trans('team::template.common.imageSize',['height'=>$height, 'width'=>$width]) }}</span>
                            </div>
                            <span class="help-block">
                              {{ $errors->first('img_id') }}
                            </span>
                          </div>
                        </div>
                      </div>

							<div class="row">
								<div class="col-md-12">
									<div class="form-group @if($errors->first('short_description')) has-error @endif form-md-line-input">
									<label class="form_title">{{ trans('team::template.common.shortdescription') }}<span aria-required="true" class="required"> * </span></label>
									{!! Form::textarea('short_description', isset($team->txtShortDescription)?$team->txtShortDescription:old('short_description'), array('maxlength' => 200,'class' => 'form-control seoField maxlength-handler','id'=>'varShortDescription','rows'=>'3','placeholder' => 'Short Description')) !!}
									<span class="help-block">{{ $errors->first('short_description') }}</span>
									</div>
								</div>
							</div>
							
							<!-- Builder include -->
							<div id="body-roll" class="form-group" style="display:none;">
                            @if (Config::get('Constant.DEFAULT_VISUAL') == 'Y')
                            @php $sections = []; @endphp
                            @if(isset($team))
                            @php
                            $sections = json_decode($team->txtDescription);
                            @endphp
                            @endif
                            @php Powerpanel\VisualComposer\Controllers\VisualComposerController::page_section(['sections'=>$sections]) @endphp
                            @endif
                          	</div>
							<div class="form-group @if($errors->first('description')) has-error @endif">
								<label class="form_title">{{ trans('team::template.common.description') }}</label>
								{!! Form::textarea('description', isset($team->txtDescription)?$team->txtDescription:old('description'), array('placeholder' => trans('team::template.common.description'),'class' => 'form-control','id'=>'txtDescription')) !!}
								<span class="help-block">{{ $errors->first('description') }}</span>
							</div>
							
						
							@if(!empty($teamSocialLinksOptions))
							@if(isset($team->txtSocialLinks))
								@php	$socialLinks = unserialize($team->txtSocialLinks) @endphp
							@endif
									@foreach($teamSocialLinksOptions as $value)
											@php 
												$linkKey = $value['key'];
												$linkLabel = $value['label'];
												$linkPlaceholder = $value['placeholder'];
											@endphp
											@if(isset($team->txtSocialLinks))
														@php $selectedValue = isset($socialLinks[$linkKey])?$socialLinks[$linkKey]:''; @endphp
											@endif
											@if($linkKey!="" && $linkLabel!="")
											<div class="form-group @if($errors->first($linkKey)) has-error @endif form-md-line-input">
												{!! Form::text($linkKey, isset($selectedValue)?$selectedValue:old($linkKey), array('class' => 'form-control','autocomplete'=>'off','placeholder' => $linkPlaceholder,'id'=>$linkKey)) !!}
												<label class="form_title" for="site_name">{{ $linkLabel }}</label>
												<span class="help-block">{{ $errors->first($linkKey) }}</span>
											</div>
											@endif
									@endforeach
							@endif
														
							@include('powerpanel.partials.seoInfo',['form'=>'frmTeam','inf'=>isset($metaInfo)?$metaInfo:false])
							
							<h3>{{ trans('team::template.common.displayinformation') }}</h3>
							<div class="row">
								<div class="col-md-6">
									@php
										$display_order_attributes = array('class' => 'form-control','maxlength'=>10,'placeholder'=>trans('team::template.common.displayorder'),'autocomplete'=>'off');
									@endphp
									<div class="form-group @if($errors->first('display_order')) has-error @endif form-md-line-input">
										<label class="form_title" class="site_name">{{ trans('team::template.common.displayorder') }} <span aria-required="true" class="required"> * </span></label>
										{!! Form::text('display_order', isset($team->intDisplayOrder)?$team->intDisplayOrder:$total, $display_order_attributes) !!}
										<span class="help-block">
											{{ $errors->first('display_order') }}
										</span>
									</div>
								</div>
								<div class="col-md-6">
									@include('powerpanel.partials.displayInfo',['display' => isset($team->chrPublish)?$team->chrPublish:null])
								</div>
							</div>
							<button type="submit" name="saveandedit" class="btn btn-green-drake" value="saveandedit" title="{!! trans('team::template.common.saveandedit') !!}">{!! trans('team::template.common.saveandedit') !!}</button>
							<button type="submit" name="saveandexit" class="btn btn-green-drake" value="saveandexit" title="{!! trans('team::template.common.saveandexit') !!}">{!! trans('team::template.common.saveandexit') !!}</button>
							<a class="btn btn-outline red" href="{{ url('powerpanel/team') }}" title="{{ trans('team::template.common.cancel') }}">{{ trans('team::template.common.cancel') }}</a>
							{!! Form::close() !!}
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
<script src="{{ url('resources/global/plugins/jquery-inputmask/jquery.inputmask.bundle.min.js') }}" type="text/javascript"></script>
<script src="{{ url('resources/pages/scripts/form-input-mask.js') }}" type="text/javascript"></script>
@include('powerpanel.partials.ckeditor')
<script type="text/javascript">
	window.site_url =  '{!! url("/") !!}';	
	var seoFormId = 'frmTeam';
	var user_action = "{{ isset($team)?'edit':'add' }}";
	var moduleAlias = 'team';
</script>
<script src="{{ url('resources/pages/scripts/custom.js') }}" type="text/javascript"></script>
<script src="{{ url('resources/global/plugins/custom-alias/alias-generator.js') }}" type="text/javascript"></script>
<script src="{{ url('resources/global/plugins/bootstrap-maxlength/bootstrap-maxlength.min.js') }}" type="text/javascript"></script>
<script src="{{ url('resources/global/plugins/seo-generator/seo-info-generator-team.js') }}" type="text/javascript"></script>
<script src="{{ url('resources/pages/scripts/packages/team/team_validations.js') }}" type="text/javascript"></script>
@if (Config::get('Constant.DEFAULT_VISUAL') == 'Y')
    @php Powerpanel\VisualComposer\Controllers\VisualComposerController::get_builder_css_js()@endphp
@endif
@endsection