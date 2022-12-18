<div class="ac-modal modal fade bd-example-modal-lg composer-element-popup ckeditor-popup" id="sectionButton"  role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
    <div class="ac-modal-table">
        <div class="ac-modal-center">
            <div class="modal-dialog">
                <div class="modal-content">
                    {!! Form::open(['method' => 'post','id'=>'frmSectionButton']) !!}
                    <input type="hidden" name="editing">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal">
                            <span>×</span>
                        </button>
                        <h5 class="modal-title" id="exampleModalLabel"><b>Add Button</b></h5>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label class="control-label form_title">Title<span aria-required="true" class="required"> * </span></label>
                            {!! Form::text('section_title', old('section_title'), array('maxlength'=>'160','class' => 'form-control','id'=>'section_title','autocomplete'=>'off')) !!}
                        </div>
                        <div class="form-group">
                            <label class="control-label form_title">Link Target<span aria-required="true" class="required"> * </span></label>
                            <select name="section_button_target" class="form-control bootstrap-select bs-select buttonsec-class" id="section_button_target">
                                <option value="">Select Link Target</option>
                                <option value="_self">Same Window</option>
                                <option value="_blank">New Window</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label class="control-label form_title">Link<span aria-required="true" class="required"> * </span></label>
                            {!! Form::text('section_link', old('section_link'), array('maxlength'=>'255','class' => 'form-control','id'=>'section_link','autocomplete'=>'off')) !!}
                        </div>

                        <div class="form-group imagealign">
                            <label class="control-label form_title config-title">Button align options<span aria-required="true" class="required"> * </span></label>
                            <div class="row">
                                <div class="col-md-12">
                                    <ul class="imagealign">
                                        <li>
                                            <a href="javascript:;" title="Align Left">
                                                <input type="radio" id="button-left-image" name="selector" value="button-lft-txt">
                                                <label for="button-left-image"></label>
                                                <div class="check"><div class="inside"></div></div>
                                                <i class="icon"><img src="{{ url('assets/images/packages/visualcomposer/left-button.png') }}" alt=""></i>
                                            </a>
                                        </li>
                                        <li>
                                            <a href="javascript:;" title="Align Right">
                                                <input type="radio" id="button-right-image" name="selector" value="button-rt-txt">
                                                <label for="button-right-image"></label>
                                                <div class="check"><div class="inside"></div></div>
                                                <i class="icon"><img src="{{ url('assets/images/packages/visualcomposer/right-button.png') }}" alt=""></i>
                                            </a>
                                        </li>
                                        <li>
                                            <a href="javascript:;" title="Align Center">
                                                <input type="radio" id="button-center-image" name="selector" value="button-center-txt">
                                                <label for="button-center-image"></label>
                                                <div class="check"><div class="inside"></div></div>
                                                <i class="icon"><img src="{{ url('assets/images/packages/visualcomposer/center-button.png') }}" alt=""></i>
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