<div class="modal fade bd-example-modal-lg composer-element-popup ckeditor-popup ckbusiness-popup" id="sectionWorkModuleTemplate" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            {!! Form::open(['method' => 'post','id'=>'frmSectionWorkModuleTemplate']) !!}
            <input type="hidden" name="editing">
            <input type="hidden" name="template">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">
                    <span>×</span>
                </button>
                <h5 class="modal-title" id="exampleModalLabel"><b>Add Work</b></h5>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label class="control-label form_title">Caption<span aria-required="true" class="required"> * </span></label>
                    {!! Form::text('section_title', old('section_title'), array('maxlength'=>'160','class' => 'form-control','autocomplete'=>'off')) !!}
                </div> 
                <div class="form-group">
                            <textarea class="form-control item-data" name="content" id="ck-area" column="40" rows="10"></textarea>
                        </div>
              
                <div class="form-group" style="display:none;">
                    <label class="control-label form_title">Configurations<span aria-required="true" class="required"> * </span></label>
                    <select name="section_config" class="form-control bootstrap-select bs-select config">
                        <option value="">Configurations</option>
                        <option value="1">Image &amp; Title</option>
                        <option value="2">Image &amp;,Title, Short Description</option>
                        <option value="3">Title, Start Date</option>
                        <option value="4">Image, Title, Start Date</option>
                        <option value="5" selected>Image, Title, Short Description, Start Date</option>
                    </select>
                </div>
                <div class="form-group" style="display:none;">
                    <label class="control-label form_title">Extra Class</label>
                    {!! Form::text('extra_class', old('extra_class'), array('maxlength'=>'160','class' => 'form-control extraClass','autocomplete'=>'off')) !!}
                </div>
                <div class="form-group" style="display:none;">
                    <label class="control-label form_title">Layout<span aria-required="true" class="required"> * </span></label>
                    <select name="layoutType" class="form-control bootstrap-select bs-select" id="work-template-layout">
                        <option class="grid" value="grid_2_col">Grid 2 column</option>
                        <option class="grid" value="grid_3_col" selected>Grid 3 column</option>
                        <option class="grid" value="grid_4_col">Grid 4 column</option> 
                    </select>
                </div>
                <div class="clearfix"></div>
                <div class="text-right">
                    <button type="button" class="btn red btn-outline" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-green-drake addSection">Add</button>
                </div>
            </div>
            {!! Form::close() !!}
        </div>
    </div>
</div>