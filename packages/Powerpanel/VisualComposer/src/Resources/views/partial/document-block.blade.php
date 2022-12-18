
<div class="ac-modal modal fade bd-example-modal-lg composer-element-popup ckeditor-popup" id="sectionOnlyDocument" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
    <div class="ac-modal-table">
        <div class="ac-modal-center">
            <div class="modal-dialog">
                <div class="modal-content">
                    {!! Form::open(['method' => 'post','id'=>'frmSectionOnlyDocument']) !!}
                    <input type="hidden" name="editing">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal">
                            <span>×</span>
                        </button>
                        <h5 class="modal-title" id="exampleModalLabel"><b>Document</b></h5>
                    </div>
                    <div class="modal-body">
                        @php $imgkey = 1; @endphp
                        <div class=" img_1" id="img1">
                            <div class="team_box">
                                <div class="thumbnail_container">
                                    <a onclick="MediaManager.openDocumentManager('Composer_doc');" data-multiple='true' data-selected="1" class=" btn-green-drake document_manager pgbuilder-img image_gallery_change_1" title="" href="javascript:void(0);">
                                        <div class="thumbnail photo_gallery_1">
                                            <img src="{!! url('assets/images/packages/visualcomposer/plus-no-image.png') !!}">                  
                                        </div>

                                    </a>
                                    <div class="nqimg_mask">
                                        <div class="nqimg_inner">
                                            <input class="image_1 item-data imgip1" type="hidden" id="photo_gallery1" data-type="document" name="img1" value=""/>
                                            <input class="folder_1" type="hidden" id="vfolder_id" data-type="folder" name="vfolder_id" value=""/>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12" id="Composer_doc_documents">
                                <div class="builder_doc_list">
                                    <ul class="dochtml">
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <div class="text-right">
                            <button type="button" class="btn red btn-outline cancel-btn" data-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-green-drake" id="addSection">Add</button>
                        </div>

                    </div>
                    {!! Form::close() !!}
                </div>
            </div>
        </div>
    </div>
</div>