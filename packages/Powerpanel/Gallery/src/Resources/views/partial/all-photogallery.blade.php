<div class="ac-modal modal fade bd-example-modal-lg composer-element-popup ckeditor-popup ckbusiness-popup" id="sectionPhotoAlbumModuleTemplate" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
    <div class="ac-modal-table">
        <div class="ac-modal-center">
            <div class="modal-dialog">
                <div class="modal-content">
                    {!! Form::open(['method' => 'post','id'=>'frmSectionPhotoAlbumModuleTemplate']) !!}
                    <input type="hidden" name="editing">
                    <input type="hidden" name="template">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal">
                            <span>×</span>
                        </button>
                        <h5 class="modal-title" id="exampleModalLabel"><b>Photo Gallery</b></h5>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label class="control-label form_title">Caption <span aria-required="true" class="required"> * </span></label>
                            {!! Form::text('section_title', old('section_title'), array('maxlength'=>'160','class' => 'form-control','id'=>'section_title','autocomplete'=>'off')) !!}
                        </div>
                        <div class="form-group" style="display:none;">
                            <label class="control-label form_title">Description</label>
                            {!! Form::textarea('section_description', old('section_description'), array('class' => 'form-control','rows'=>'3','id'=>'section_description','autocomplete'=>'off')) !!}
                        </div>
                        <div class="form-group" style="display:none;">
                            <label class="control-label form_title">Configurations<span aria-required="true" class="required"> * </span></label>
                            <select name="section_config" class="form-control bootstrap-select bs-select config-class" id="config">
                                <option value="">Configurations</option>
                                <option value="1">Image &amp; Title</option>
                                <option value="3">Title, Start Date</option>
                                <option value="4">Image, Title, Start Date</option>
                            </select>
                        </div> 
                        <div class="form-group" style="display:none;">
                            <label class="control-label form_title">Layout<span aria-required="true" class="required"> * </span></label>
                            <select name="layoutType" class="form-control bootstrap-select bs-select layout-class" id="photoalbum-template-layout">
                                <option value="">Select Layout</option>      
                                <option class="list" value="list">List</option>
                                <option class="grid" value="grid_2_col">Grid 2 column</option>
                                <option class="grid" value="grid_3_col">Grid 3 column</option>
                                <option class="grid" value="grid_4_col">Grid 4 column</option>
                            </select>
                        </div>
                         <div class="form-group" style="display:none;">
                            <label class="control-label form_title">Start Date</label>
                            {!! Form::text('photo_start_date_time', old('photo_start_date_time'), array('maxlength'=>'160','class' => 'form-control','id'=>'photo_start_date_time','autocomplete'=>'off')) !!}
                        </div>
                        <div class="form-group" style="display:none;">
                            <label class="control-label form_title">End Date</label>
                            {!! Form::text('photo_end_date_time', old('photo_end_date_time'), array('maxlength'=>'160','class' => 'form-control','id'=>'photo_end_date_time','autocomplete'=>'off')) !!}
                        </div>
                         <div class="form-group" style="display:none;">
                            <label class="control-label form_title">Limit (Note: All the blogs will be shown if Limit not entered)</label>
                            {!! Form::number('section_limit', old('section_limit'), array('maxlength'=>'2','class' => 'form-control','id'=>'section_limit','autocomplete'=>'off','min'=>'1')) !!}
                        </div>
                         <div class="form-group" style="display:none;">
                            <label class="control-label form_title">Extra Class</label>
                            {!! Form::text('extra_class', old('extra_class'), array('maxlength'=>'160','class' => 'form-control','id'=>'extra_class','autocomplete'=>'off')) !!}
                        </div>
                        <div class="clearfix"></div>
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