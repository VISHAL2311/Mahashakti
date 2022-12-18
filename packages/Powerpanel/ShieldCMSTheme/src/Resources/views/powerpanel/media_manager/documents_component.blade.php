<div class="col-md-6">
    <div class="portlet gallary_manager light Design-preview Design-settings portlet-fullscreen media-manag" id="document_component" style="display:none;">
        <div class="portlet-title">
            <div class="caption">
                <i class="icon-picture font-grey-gallery"></i>
                <span class="caption-subject bold font-grey-gallery uppercase">Media Manager</span>
                <span class="caption-helper">All Media in one place</span>
            </div>
            <div class="tools">
                <input type="text" class="hide media_input" name="docName"  placeholder="Search by Document Name">
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
                                    <a class="active" id="upload_document" href="javascript:;" onclick="MediaManager.setDocumentUploadTab();" ><i class="icon-cloud-upload icons"></i>Upload Document</a>
                                </li>
                                <li>
                                    <a href="javascript:;" id="user_uploaded_docs" onclick="MediaManager.setDocumentListTab({{ Auth::user()->id }});"><i class="fa fa-file-text-o"></i>My Documents</a>
                                </li>
                                <li>
                                    <a href="javascript:;" id="folder_uploaded_docs" onclick="MediaManager.setFolderDocumentListTab({{ Auth::user()->id }});"><i class="fa fa-folder-open-o"></i>Folder Uploads</a>
                                </li>
                                <li>
                                    <a href="javascript:;" id="trash_docs" onclick="MediaManager.setTrashedDocumentTab({{ Auth::user()->id }});"><i class="icon-trash"></i>Trash</a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            <input type="hidden" id="control_id" value=""/>
            <div class="loader" style="display:none;">
                <img src="{{ $CDN_PATH.'resources/images/media_loader.gif' }}">
            </div>
            <div class="right-panel docs_html" style="display:none"></div>
            <div class="right-panel docs_upload" style="display:none">	</div>
            <div class="right-panel user_uploaded_docs" style="display:none">	</div>
            <div class="right-panel trashed_docs" style="display:none">	</div>
        </div>
    </div>
</div>
<div class="new_modal modal fade" id="deleteMediaDocument" tabindex="-1" role="basic" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-vertical">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                    {{ trans('template.common.alert') }}
                </div>
                <div class="modal-body text-center">Are you sure you want to delete selected documents? </div>
                <div class="modal-footer">
                    <button type="button" class="btn red btn-outline remove_multiple_document" data-dismiss="modal">{{ trans('template.common.delete') }}</button>
                    <button type="button" class="btn btn-green-drake" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="new_modal modal fade" id="alertModalForDocument" tabindex="-1" role="basic" aria-hidden="true">
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
<div class="new_modal modal fade" id="permanentDeleteMediaDocument" tabindex="-1" role="basic" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-vertical">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                    {{ trans('template.common.alert') }}
                </div>
                <div class="modal-body text-center">Are you sure you want to delete selected documents permanently?</div>
                <div class="modal-footer">
                    <button type="button" class="btn red btn-outline remove_multiple_document_permanently" data-dismiss="modal">{{ trans('template.common.delete') }}</button>
                    <button type="button" class="btn btn-green-drake" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="new_modal modal fade" id="emptyTrashMediaDocument" tabindex="-1" role="basic" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-vertical">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                    {{ trans('template.common.alert') }}
                </div>
                <div class="modal-body text-center">Are you sure you want to empty trash?</div>
                <div class="modal-footer">
                    <button type="button" class="btn red btn-outline empty_trash_Document" data-dismiss="modal">{{ trans('template.common.yes') }}</button>
                    <button type="button" class="btn btn-green-drake" data-dismiss="modal">No</button>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="new_modal modal fade" id="restoreDocumentConfirmBox" tabindex="-1" role="basic" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-vertical">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                    {{ trans('template.common.alert') }}
                </div>
                <div class="modal-body text-center">Are you sure you want to restore selected document(s)?</div>
                <div class="modal-footer">
                    <button type="button" class="btn red btn-outline restore_multiple_documents" data-dismiss="modal">Restore</button>
                    <button type="button" class="btn btn-green-drake" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
            window.user_id = '{{ Auth::user()->id }}';
            window.segment = '{{ Request::segment(2) }}';
</script>