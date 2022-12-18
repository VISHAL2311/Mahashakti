<div class="ac-modal modal fade bd-example-modal-lg composer-element-popup ckeditor-popup" id="sectionContactInfo"  role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
    <div class="ac-modal-table">
        <div class="ac-modal-center">
            <div class="modal-dialog">
                <div class="modal-content">
                    {!! Form::open(['method' => 'post','id'=>'frmSectionContactInfo']) !!}
                    <input type="hidden" name="editing">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal">
                            <span>×</span>
                        </button>
                        <h5 class="modal-title" id="exampleModalLabel"><b>Add Contact Info</b></h5>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label class="control-label form_title">Address <span aria-required="true" class="required"> * </span></label>
                            {!! Form::textarea('section_address', old('section_address'), array('class' => 'form-control','rows'=>'3','id'=>'section_address','autocomplete'=>'off')) !!}
                        </div>
                        <div class="form-group">
                            <label class="control-label form_title">Email <span aria-required="true" class="required"> * </span></label>
                            {!! Form::email('section_email', old('section_email'), array('maxlength'=>'160','class' => 'form-control','id'=>'section_email','autocomplete'=>'off')) !!}
                        </div>
                        <div class="form-group">
                            <label class="control-label form_title">Phone # <span aria-required="true" class="required"> * </span></label>
                            {!! Form::text('section_phone', old('section_phone'), array('maxlength'=>'20','class' => 'form-control','id'=>'section_phone','onkeypress'=>"javascript: return KeycheckOnlyPhonenumber(event);",'onpaste'=>'return false','autocomplete'=>'off')) !!}
                        </div>
                        <div class="form-group">
                            <label class="control-label form_title">Other Information</label>
                            <textarea name="title" class="form-control item-data" id="ck-area" column="40" rows="1"></textarea>
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