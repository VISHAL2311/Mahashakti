@section('css')
@extends('powerpanel.layouts.app')
@section('title')
{{Config::get('Constant.SITE_NAME')}} - PowerPanel
@stop
@section('css')
<link href="{{ $CDN_PATH.'resources/global/plugins/datatables/datatables.min.css' }}" rel="stylesheet" type="text/css" />
<link href="{{ $CDN_PATH.'resources/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.css' }}" rel="stylesheet" type="text/css" />
<link href="{{ $CDN_PATH.'resources/global/plugins/fancybox/source/helpers/jquery.fancybox-thumbs.css' }}" rel="stylesheet" type="text/css"/>
<link href="{{ $CDN_PATH.'resources/global/plugins/highslide/highslide.css' }}" rel="stylesheet" type="text/css"/>
<link href="{{ $CDN_PATH.'resources/layouts/layout4/css/multi-gallery.css' }}" rel="stylesheet" type="text/css"/>
@endsection
@section('content')
<!-- BEGIN PAGE BASE CONTENT -->
{!! csrf_field() !!}
<div class="row">
    <div class="col-md-12">
        <div class="title-dropdown_sec">
            @if (File::exists(base_path() . '/resources/views/powerpanel/partials/listbreadcrumbs.blade.php') != null)
            @include('powerpanel.partials.listbreadcrumbs',['ModuleName'=>'Gallery'])
            @php $settings = json_decode(Config::get("Constant.MODULE.SETTINGS")); @endphp
            @endif 
        </div>

        <div class="col-md-12 settings">
            <div class="row">
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
                                        <div class="portlet-body form_pattern">
                                            {!! Form::open(['method' => 'post','id'=>'frmGallery']) !!}
                                            {!! Form::hidden('fkMainRecord', isset($Gallery->fkMainRecord)?$Gallery->fkMainRecord:old('fkMainRecord')) !!}
                                            @if(isset($Gallery))
                                            @if (File::exists(base_path() . '/resources/views/powerpanel/partials/lockedpage.blade.php') != null)
                                            @include('powerpanel.partials.lockedpage',['pagedata'=>$Gallery])
                                            @endif
                                            @endif

                                            @include('powerpanel.partials.imageControl',['type' => 'multiple','label' => trans('gallery::template.common.selectimage').' <span aria-required="true" class="required"> * </span>' ,'data'=> isset($Gallery)?$Gallery:null , 'id' => 'bankcoin_image', 'name' => 'img_id', 'settings' => $settings, 'width' => '1920', 'height' => '1080'])


                                            <h3 class="form-section">{{ trans('gallery::template.common.displayinformation') }}</h3>
                                            <div class="row">
                                                <div class="col-md-6" style="display: none;">
                                                    <div class="form-group @if($errors->first('order')) has-error @endif form-md-line-input">
                                                        @php
                                                        $display_order_attributes = array('class' => 'form-control','maxlength'=>5,'placeholder'=>trans('gallery::template.common.displayorder'),'autocomplete'=>'off');
                                                        @endphp
                                                        @if(isset($Gallery_highLight->intDisplayOrder) && ($Gallery_highLight->intDisplayOrder != $Gallery->intDisplayOrder))
                                                        @php $Class_intDisplayOrder = " highlitetext"; @endphp
                                                        @else
                                                        @php $Class_intDisplayOrder = ""; @endphp
                                                        @endif
                                                        <label class="form_title {{ $Class_intDisplayOrder }}" for="site_name">{{ trans('gallery::template.common.displayorder') }} <span aria-required="true" class="required"> * </span></label>
                                                        {!! Form::text('order', isset($Gallery->intDisplayOrder)?$Gallery->intDisplayOrder:'1', $display_order_attributes) !!}
                                                        <span style="color: red;">
                                                            {{ $errors->first('order') }}
                                                        </span>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    @if(isset($Gallery_highLight->chrPublish) && ($Gallery_highLight->chrPublish != $Gallery->chrPublish))
                                                    @php $Class_chrPublish = " highlitetext"; @endphp
                                                    @else
                                                    @php $Class_chrPublish = ""; @endphp
                                                    @endif
                                                    @if((isset($Gallery) && $Gallery->chrDraft == 'D'))
                                                    @include('powerpanel.partials.displayInfo',['Class_chrPublish'=>$Class_chrPublish,'display' => (isset($Gallery->chrDraft)?$Gallery->chrDraft:'D')])
                                                    @else
                                                    @include('powerpanel.partials.displayInfo',['Class_chrPublish'=>$Class_chrPublish,'display' => (isset($Gallery->chrPublish)?$Gallery->chrPublish:'Y')])
                                                    @endif
                                                </div>
                                            </div>
                                            <div class="form-actions">
                                                <div class="row">
                                                    <div class="col-md-12">
                                                        @if(isset($Gallery->fkMainRecord) && $Gallery->fkMainRecord != 0)
                                                        <button type="submit" name="saveandexit" class="btn btn-green-drake" value="saveandexit">{!! trans('gallery::template.common.approve') !!}</button>
                                                        @else
                                                        @if($userIsAdmin)
                                                        <button type="submit" name="saveandexit" class="btn btn-green-drake" value="saveandexit">{!! trans('gallery::template.common.saveandexit') !!}</button>
                                                        @else
                                                        @if((isset($chrNeedAddPermission) && $chrNeedAddPermission == 'N') && (isset($charNeedApproval) && $charNeedApproval == 'N'))
                                                        <button type="submit" name="saveandexit" class="btn btn-green-drake" value="saveandexit">{!! trans('gallery::template.common.saveandexit') !!}</button>
                                                        @else
                                                        <button type="submit" name="saveandexit" class="btn btn-green-drake" value="approvesaveandexit">{!! trans('gallery::template.common.approvesaveandexit') !!}</button>
                                                        @endif
                                                        @endif  
                                                        @endif
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
        <div class="clearfix"></div>
    </div>
    <div class="col-md-12">
        <div class="portlet light bordered posts multi_gallery">
            <div class="portlet-title">
                <h3>Gallery</h3>
                <div class="clearfix"></div>
                <div class="portlet-body">
                   
                    <div class="row text-center pg_main_border">
                        <div class="posts_my">
                            @if($photoGalleryObj->count() > 0)
                            @foreach ($photoGalleryObj as $key => $value)
                            @if(isset($value->video))
                            <div class="col-md-3 col-sm-4 col-xs-6 col-xs-small img_{{ $value->id  }}">
                                <div class="multi_gal_box">			                        
                                    <div class="thumbnail_container">
                                        <div class="thumbnail video_gallery_{{ $value->id  }}">
                                            @if(!empty($value->video->youtubeId))	
                                            <img src='http://img.youtube.com/vi/{{ $value->video->youtubeId }}/default.jpg' />
                                            @else
                                            <img class="img_opacity" src="{{ url('resources\images\video_upload_file.gif') }}" />
                                            @endif	
                                        </div>
                                        <div class="nqimg_mask">
                                            <div class="nqimg_inner">
                                                @if(!empty($value->video->youtubeId))	
                                                <a class="btn btn-green-drake fancybox-buttons fancybox fancybox.iframe video_iframe_{{ $value->id  }}" data-rel="fancybox-buttons" data-fancybox-group="gallery" title="{{ $value->video->varVideoName }}" href="http://www.youtube.com/embed/{{ $value->video->youtubeId  }}?autoplay=1"><i class="fa fa-link"></i></a>
                                                <a onclick="MediaManager.openVideoManager('video_gallery',{{ $value->id  }});" class="btn btn-green-drake video_manager video_gallery_change_{{ $value->id  }}" title="{{ $value->video->varVideoName }}" href="javascript:void(0);"><i class="fa fa-edit"></i></a>
                                                <input class="video_{{ $value->id }}" type="hidden" id="video_gallery" name="video_id" value="{{ $value->fkIntVideoId }}" />
                                                @else 
                                                <a class="btn btn-green-drake fancybox-buttons  fancybox fancybox.iframe video_iframe_{{ $value->id  }}" data-rel="fancybox-buttons" data-fancybox-group="gallery" title="{{ $value->video->varVideoName }}.{{ $value->video->varVideoExtension }}" href="{{ url('/') }}/assets/videos/{{ $value->video->varVideoName }}.{{ $value->video->varVideoExtension }}"><i class="fa fa-link"></i></a>
                                                <a onclick="MediaManager.openVideoManager('video_gallery',{{ $value->id  }});" class="btn btn-green-drake video_manager video_gallery_change_{{ $value->id  }}" title="{{ $value->video->varVideoName }}.{{ $value->video->varVideoExtension }}" href="javascript:void(0);"><i class="fa fa-edit"></i></a>
                                                <input class="video_{{ $value->id }}" type="hidden" id="video_gallery" name="video_id" value="{{ $value->fkIntVideoId }}" />
                                                @endif			
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endif
                            @if($value->video == '')	
                            <div class="col-md-3 col-sm-4 col-xs-6 col-xs-small img_{{ $value->id  }}">
                                <div class="multi_gal_box">
                                    <div class="thumbnail_container">
                                        <div class="thumbnail photo_gallery_{{ $value->id }}">
                                            <img src="{!! App\Helpers\resize_image::resize($value->fkIntImgId,230,150) !!}" />
                                        </div>
                                        <div class="nqimg_mask" id="photoDiv">
                                            <div class="nqimg_inner">
                                                <a class="btn btn-green-drake fancybox-buttons image_iframe_{{ $value->id  }}" data-rel="fancybox-buttons" title="View image" href="{!! App\Helpers\resize_image::resize($value->fkIntImgId,800,800) !!}"><i class="fa fa-link"></i></a>
                                                <a onclick="MediaManager.open('photo_gallery',{{ $value->id  }}); doSomethingElse();" class="btn btn-green-drake media_manager image_gallery_change_{{ $value->id  }}" data-multiple="false" title="Change Image" href="javascript:void(0);"><i class="fa fa-edit"></i></a>
                                                <input class="image_{{ $value->id }}" type="hidden" id="photo_gallery" name="img_id" value="{{ $value->fkIntImgId }}" />
                                            </div>
                                        </div>
                                    </div>
                                    <div class="team_desc">
                                        <table width="100%" border="0" align="center" cellpadding="3" cellspacing="3">
                                            <tr>
                                                <td colspan="2" align="left" valign="middle">
                                                    <div class="form-group form-md-line-input">
                                                        <textarea id="title_{{ $value->id }}"  name="title" class="form-control edited" rows="1">{{ $value->varTitle }}</textarea>
                                                        <label class="site_name form_title text-left">{{ trans('template.common.title') }}</label>
                                                        <input class="tt1_{{ $value->id }}" type="hidden"  name="title" value="{{ $value->varTitle }}" />
                                                    </div>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td colspan="2" align="left" valign="middle">
                                                    <div class="form-group  form-md-line-input">
                                                        <input id="display_order_{{ $value->id }}" class="form-control edited" value="{{ $value->intDisplayOrder }}" name="display_order" type="text">
                                                        <input type="hidden" id="display_order_hidden_{{ $value->id }}" class="form-control edited" value="{{ $value->intDisplayOrder }}" name="display_order_hidden">
                                                        <label class="form_title site_name text-left">{{ trans('template.common.displayorder') }}</label>
                                                    </div>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td colspan="2" width="100%" align="left" valign="middle">
                                                    <ul class="nqsocia">
                                                        @can('gallery-edit')
                                                        <li><a href="javascript:void(0);" onclick="update_data('{{ $value->id }}')" title="Save" class="sn"><i class="fa fa-save"></i></a>
                                                        </li>
                                                        @endcan
                                                        @can('gallery-publish')
                                                        @if($value->chrPublish == 'Y')
                                                        <li>
                                                            <a href="javascript:void(0);" data-status = "{{ $value->chrPublish  }}" onclick="update_status('{{ $value->id }}')"  title="Publish" class="sn status_{{ $value->id }}"><i class="fa fa-eye"></i></a>
                                                        </li>
                                                        @else
                                                        <li>
                                                            <a href="javascript:void(0);" data-status = "{{ $value->chrPublish  }}" onclick="update_status('{{ $value->id }}')" title="Unpublish" class="sn status_{{ $value->id }}"><i class="fa fa-eye-slash"></i></a>
                                                        </li>
                                                        @endif
                                                        @endcan
                                                        @can('gallery-delete')
                                                        <li>
                                                            <a href="javascript:void(0);" onclick="remove('{{ $value->id }}')" title="Delete" class="sn">
                                                                <i class="fa fa-remove"></i>
                                                            </a>
                                                        </li>
                                                        @endcan
                                                    </ul>
                                                </td>
                                            </tr>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            @endif
                            @endforeach
                            @else
                            <div class="col-md-12">
                                <h2>No Images</h2>
                            </div>
                            @endif
                        </div>
                    </div>

                    @if($photoGalleryObj->links())
                    @if($photoGalleryObj->count() > 0)
                    <div class="row">
                        <div align="center">
                            {{ $photoGalleryObj->links() }}
                        </div>
                    </div>
                    @endif
                    @endif

                </div>
            </div>
        </div>
    </div>
</div>
@if (File::exists(base_path() . '/resources/views/powerpanel/partials/deletePopup.blade.php') != null)
@include('powerpanel.partials.deletePopup')
@endif
@if (File::exists(base_path() . '/resources/views/powerpanel/partials/approveRecord.blade.php') != null)
@include('powerpanel.partials.approveRecord')
@endif
@if (File::exists(base_path() . '/resources/views/powerpanel/partials/cmsPageComments.blade.php') != null)
@include('powerpanel.partials.cmsPageComments',['module'=>Config::get('Constant.MODULE.TITLE')])
@endif
@endsection
@section('scripts')
<script type="text/javascript">
                                            window.site_url = '{!! url("/") !!}';
                                            var user_action = "{{ isset($Gallery)?'edit':'add' }}";
                                            var moduleAlias = 'gallery';</script>
<script type="text/javascript">
                                            window.site_url = '{!! url("/") !!}';
                                            var DELETE_URL = '{!! url("/powerpanel/gallery/DeleteRecord") !!}';
                                            var APPROVE_URL = '{!! url("/powerpanel/gallery/ApprovedData_Listing") !!}';
                                            var getChildData = window.site_url + "/powerpanel/gallery/getChildData";
                                            var getChildData_rollback = window.site_url + "/powerpanel/gallery/getChildData_rollback";
                                            var ApprovedData_Listing = window.site_url + "/powerpanel/gallery/ApprovedData_Listing";
                                            var Get_Comments = '{!! url("/powerpanel/gallery/Get_Comments") !!}';
                                            var Quick_module_id = '<?php echo Config::get('Constant.MODULE.ID'); ?>';
                                            var settingarray = jQuery.parseJSON('{!!$settingarray!!}');
                                            var showChecker = true;
                                            @if (!$userIsAdmin)
                                            showChecker = false;
                                            @endif
</script>
<script src="{{ $CDN_PATH.'resources/pages/scripts/packages/gallery/gallery_validations.js' }}" type="text/javascript"></script>
<script src="{{ $CDN_PATH.'resources/global/plugins/bootstrap-maxlength/bootstrap-maxlength.min.js' }}" type="text/javascript"></script>
<script src="{{ $CDN_PATH.'resources/global/plugins/jquery-cookie-master/src/jquery.cookie.js' }}" type="text/javascript"></script>
<script src="{{ $CDN_PATH.'resources/global/scripts/datatable.js' }}" type="text/javascript"></script>
<script src="{{ $CDN_PATH.'resources/pages/scripts/custom.js' }}" type="text/javascript"></script>
<script src="{{ $CDN_PATH.'resources/global/plugins/highslide/highslide-with-html.js' }}" type="text/javascript"></script>
@if((File::exists(app_path() . '/Workflow.php') != null || File::exists(base_path() . '/packages/Powerpanel/Workflow/src/Models/Workflow.php') != null))
<script src="{{ $CDN_PATH.'resources/pages/scripts/user-updates-approval.js' }}" type="text/javascript"></script>
@endif
<script type="text/javascript">
                                            $('.fancybox-buttons').fancybox({
                                    autoWidth: true,
                                            autoHeight: true,
                                            autoResize: true,
                                            autoCenter: true,
                                            closeBtn: true,
                                            openEffect: 'elastic',
                                            closeEffect: 'elastic',
                                            helpers: {
                                            title: {
                                            type: 'inside',
                                                    position: 'top'
                                            }
                                            },
                                            beforeShow: function () {
                                            this.title = $(this.element).data("title");
                                            }
                                    });
                                            $(".fancybox-thumb").fancybox({
                                    prevEffect: 'none',
                                            nextEffect: 'none',
                                            helpers:
                                    {
                                    title: {
                                    type: 'outside'
                                    },
                                            thumbs: {
                                            width: 60,
                                                    height: 50
                                            }
                                    }
                                    });
                                            $(document).ready(function () {
                                    setInterval(function () {
                                    $('.addhiglight').closest("td").closest("tr").addClass('higlight');
                                    }, 800);
                                    });
</script>
@endsection