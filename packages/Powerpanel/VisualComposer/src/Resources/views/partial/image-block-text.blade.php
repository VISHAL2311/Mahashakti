<div class="ac-modal modal fade bd-example-modal-lg composer-element-popup ckeditor-popup" id="sectionImage"  role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
    <div class="ac-modal-table">
        <div class="ac-modal-center">
            <div class="modal-dialog">
                <div class="modal-content">
                    {!! Form::open(['method' => 'post','id'=>'frmSectionImage']) !!}
                    <input type="hidden" name="editing">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal">
                            <span>×</span>
                        </button>
                        <h5 class="modal-title" id="exampleModalLabel"><b>Image with Information</b></h5>
                    </div>
                    <div class="modal-body">
                        @php $imgkey = 1; @endphp
                        <div class=" img_1" id="img1">
                            <div class="team_box">
                                <div class="thumbnail_container">
                                    <a data-multiple="false" onclick="MediaManager.open('photo_gallery', 1);" data-selected="1" class=" btn-green-drake media_manager pgbuilder-img image_gallery_change_1" title="" href="javascript:void(0);">
                                        <div class="thumbnail photo_gallery_1">
                                            <img src="{!! url('assets/images/packages/visualcomposer/plus-no-image.png') !!}">                  
                                        </div>
                                    </a>
                                    <div class="nqimg_mask">
                                        <div class="nqimg_inner">
                                            <input class="image_1 item-data imgip" type="hidden" id="photo_gallery1" data-type="image" name="img1" value="1"/>
                                             <input class="folder_1" type="hidden" id="vfolder_id" data-type="folder" name="vfolder_id" value=""/>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label form_title">Caption</label>
                            {!! Form::text('img_title', old('img_title'), array('maxlength'=>'160','class' => 'form-control','id'=>'img_title','autocomplete'=>'off')) !!}
                        </div>
                        <div class="form-group">
                            <textarea class="form-control item-data" name="content" id="ck-area" column="40" rows="10"></textarea>
                        </div>
                        <div class="form-group imagealign">
                            <label class="control-label form_title config-title">Image align options<span aria-required="true" class="required"> * </span></label>
                            <div class="row">
                                <div class="col-md-12">
                                    <ul class="imagealign">
                                        <li>
                                            <a href="javascript:;" title="Align Left">

                                                <input type="radio" id="home-left-image" name="selector" value="lft-txt">
                                                <label for="home-left-image"></label>
                                                <div class="check"><div class="inside"></div></div>

                                                <i class="icon"><img src="{{ url('assets/images/packages/visualcomposer/left-image.png') }}" alt=""></i>
                                            </a>

                                        </li>
                                        <li>
                                            <a href="javascript:;" title="Align Right">
                                                <input type="radio" id="home-right-image" name="selector" value="rt-txt">
                                                <label for="home-right-image"></label>
                                                <div class="check"><div class="inside"></div></div>
                                                <i class="icon"><img src="{{ url('assets/images/packages/visualcomposer/right-image.png') }}" alt=""></i>
                                            </a>

                                        </li>
                                        <li>
                                            <a href="javascript:;" title="Align Top">
                                                <input type="radio" id="home-top-image" name="selector" value="top-txt">
                                                <label for="home-top-image"></label>
                                                <div class="check"><div class="inside"></div></div>
                                                <i class="icon"><img src="{{ url('assets/images/packages/visualcomposer/top-image.png') }}" alt=""></i>
                                            </a>

                                        </li>
                                        <li>
                                            <a href="javascript:;" title="Align Cneter">
                                                <input type="radio" id="home-center-image" name="selector" value="center-txt">
                                                <label for="home-center-image"></label>
                                                <div class="check"><div class="inside"></div></div>
                                                <i class="icon"><img src="{{ url('assets/images/packages/visualcomposer/center-image.png') }}" alt=""></i>
                                            </a>

                                        </li>
                                        <li>
                                            <a href="javascript:;" title="Align Bottom">
                                                <input type="radio" id="bottom-image" name="selector" value="bot-txt">
                                                <label for="bottom-image"></label>
                                                <div class="check"><div class="inside"></div></div>
                                                <i class="icon"><img src="{{ url('assets/images/packages/visualcomposer/bottom-image.png') }}" alt=""></i>
                                            </a>

                                        </li>

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