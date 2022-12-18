<div class="ac-modal modal fade bd-example-modal-lg composer-element-popup ckeditor-popup" id="sectionMap" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
    <div class="ac-modal-table">
        <div class="ac-modal-center">
            <div class="modal-dialog">
                <div class="modal-content">
                    {!! Form::open(['method' => 'post','id'=>'frmSectionMap']) !!}
                    <input type="hidden" name="editing">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal">
                            <span>×</span>
                        </button>
                        <h5 class="modal-title" id="exampleModalLabel"><b>Google Map</b></h5>
                    </div>
                    <div class="modal-body">
                        @php $imgkey = 1; @endphp
                        <!--                        <div class=" img_1" id="img1">
                                                    <div class="team_box">
                                                        <label class="control-label form_title">Marker<span aria-required="true" class="required"> * </span></label>
                                                        <div class="thumbnail_container">
                                                            <a onclick="MediaManager.open('photo_gallery', 1);" data-selected="1" class=" btn-green-drake media_manager pgbuilder-img image_gallery_change_1" title="" href="javascript:void(0);">
                                                                <div class="thumbnail photo_gallery_1">
                                                                    <img src="{!! url('assets/images/packages/visualcomposer/plus-no-image.png') !!}">                  
                                                                </div>
                                                            </a>
                                                            <div class="nqimg_mask">
                                                                <div class="nqimg_inner">
                                                                    <input class="image_1 item-data imgip" type="hidden" id="photo_gallery1" data-type="image" name="img1" value=""/>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>-->

                        <div class="form-group">
                            <div id="map" style="margin-left: 0px; margin-bottom: 10px; width:100%;height:300px;"></div>
                        </div>
                        <div style="padding-bottom: 20px"></div>
                        <div class="form-group">
                            <label class="control-label form_title">Latitude<span aria-required="true" class="required"> * </span></label>
                            {!! Form::text('img_latitude', old('img_latitude'), array('maxlength'=>'500','class' => 'form-control','id'=>'img_latitude','autocomplete'=>'off','readonly'=>'readonly')) !!}
                        </div>
                        <div class="form-group">
                            <label class="control-label form_title">Longitude<span aria-required="true" class="required"> * </span></label>
                            {!! Form::text('img_longitude', old('img_longitude'), array('maxlength'=>'500','class' => 'form-control','id'=>'img_longitude','autocomplete'=>'off','readonly'=>'readonly')) !!}
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