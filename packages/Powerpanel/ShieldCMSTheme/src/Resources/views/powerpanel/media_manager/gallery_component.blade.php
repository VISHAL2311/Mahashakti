<script src="https://code.jquery.com/jquery-1.11.3.min.js"></script>
<div class="col-md-6">
    <div class="portlet gallary_manager light Design-preview Design-settings portlet-fullscreen media-manag" id="gallary_component" style="display:none;">
        <div class="portlet-title">
            <div class="caption">
                <i class="icon-picture font-grey-gallery"></i>
                <span class="caption-subject bold font-grey-gallery uppercase">Media Manager</span>
                <span class="caption-helper">All Media in one place</span>
            </div>
            <div class="tools">
                <input type="text" class="hide" name="imageName"  placeholder="Search by Image Name">
                <a href="javascript:void(0);" class="remove"> </a>
            </div>
        </div>
        <div class="portlet-body preview-bg">
            <div class="left-panel">
                <div class="info">
                    <div class="tab-content">
                        <div id="tab_6_3" class="tab-pane fade in active tab_6_3" >
                            <ul class="nav">
                                <li>
                                    <a class="active" id="upload_image" href="javascript:;" onclick="MediaManager.setImageUploadTab();" ><i class="icon-cloud-upload icons"></i>Upload Image</a>
                                </li>
                                <!-- <li>
                                                                        <a href="javascript:;" id="inser_url" onclick="MediaManager.setInsertImageFromUrlTab();"><i class="fa fa-external-link"></i>Insert image From Url</a>
                                </li> -->
                                <li>
                                    <a href="javascript:;" id="user_uploaded_image" onclick="MediaManager.setMyUploadTab({{ Auth::user()->id }});"><i class="fa fa-image"></i>My Uploads</a>
                                </li>
                                 <li>
                                    <a href="javascript:;" id="folder_uploaded_image" onclick="MediaManager.setFolderUploadTab({{ Auth::user()->id }});"><i class="fa fa-folder-open-o"></i>Folder Uploads</a>
                                </li>
                                <li>
                                    <a href="javascript:;" id="recent" onclick="MediaManager.setRecentUploadTab({{ Auth::user()->id }});"><i class="fa fa-hourglass-o"></i>Recent Uploads</a>
                                </li>
                                <li>
                                    <a href="javascript:;" id="trash" onclick="MediaManager.setTrashedImageTab({{ Auth::user()->id }});"><i class="icon-trash"></i>Trash</a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            <input type="hidden" id="data_id" value=""/>
            <input type="hidden" id="recordId" value=""/>
            <div class="loader" style="display:none;">
                <img src="{{ $CDN_PATH.'resources/images/media_loader.gif' }}">
            </div>
            <div class="right-panel image_html" style="display:none"></div>
            <div class="right-panel file_upload" style="display:none">	</div>
            <div class="right-panel user_uploaded" style="display:none">	</div>
            <div class="right-panel insert_from_url" style="display:none">	</div>
            <div class="right-panel trashed_images" style="display:none">	</div>
            <div class="right-panel recent_uploads" style="display:none">	</div>
            <div class="right-panel image_details" style="display:none">	</div>
            <div class="right-panel image_cropper" style="display:none"></div>
        </div>
    </div>
</div>
<div class="new_modal modal fade" id="imgInUse" tabindex="-1" role="basic" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-vertical">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                    {{ trans('template.common.alert') }}
                </div>
                <div class="modal-body text-center" id="imgInUseMessage"></div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-green-drake" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="new_modal modal fade" id="deleteMediaImage" tabindex="-1" role="basic" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-vertical">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                    {{ trans('template.common.alert') }}
                </div>
                <div class="modal-body text-center">Are you sure you want to delete selected image(s)? images are may be used in another records. </div>
                <div class="modal-footer">
                    <button type="button" class="btn red btn-outline remove_multiple_images" data-dismiss="modal">{{ trans('template.common.delete') }}</button>
                    <button type="button" class="btn btn-green-drake" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="new_modal modal fade" id="alertModalForImage" tabindex="-1" role="basic" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-vertical">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                    {{ trans('template.common.alert') }}
                </div>
                <div class="modal-body text-center alert_msg"></div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-green-drake" data-dismiss="modal">Ok</button>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="new_modal modal fade" id="permanentDeleteMediaImage" tabindex="-1" role="basic" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-vertical">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                    {{ trans('template.common.alert') }}
                </div>
                <div class="modal-body text-center">Are you sure you want to delete selected image(s) permanently?</div>
                <div class="modal-footer">
                    <button type="button" class="btn red btn-outline remove_multiple_images_permanently" data-dismiss="modal">{{ trans('template.common.delete') }}</button>
                    <button type="button" class="btn btn-green-drake" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="new_modal modal fade" id="emptyTrashMediaImage" tabindex="-1" role="basic" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-vertical">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                    {{ trans('template.common.alert') }}
                </div>
                <div class="modal-body text-center">Are you sure you want to empty trash?</div>
                <div class="modal-footer">
                    <button type="button" class="btn red btn-outline empty_trash_Image" data-dismiss="modal">{{ trans('template.common.yes') }}</button>
                    <button type="button" class="btn btn-green-drake" data-dismiss="modal">No</button>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="new_modal modal fade" id="restoreConfirmBox" tabindex="-1" role="basic" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-vertical">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                    {{ trans('template.common.alert') }}
                </div>
                <div class="modal-body text-center">Are you sure you want to restore selected image(s)?</div>
                <div class="modal-footer">
                    <button type="button" class="btn red btn-outline restore_multiple_images" data-dismiss="modal">Restore</button>
                    <button type="button" class="btn btn-green-drake" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
    window.user_id = '{{ Auth::user()->id }}';
    window.segment = '{{ Request::segment(2) }}';
    $(".media_manager").click(function() {
<?php if (Request::segment(4) == 'edit') { ?>
        $("#upload_image").addClass("active");
        $("#user_uploaded_image").removeClass("active");
        $("#upload_image").trigger("click");
<?php } else { ?>
        $("#upload_image").addClass("active");
        $("#user_uploaded_image").removeClass("active");
        $("#upload_image").trigger("click");
<?php } ?>
    });
</script>