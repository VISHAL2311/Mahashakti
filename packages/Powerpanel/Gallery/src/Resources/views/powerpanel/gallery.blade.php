<div class="col-md-12">
   
    <div class="row text-center pg_main_border">
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