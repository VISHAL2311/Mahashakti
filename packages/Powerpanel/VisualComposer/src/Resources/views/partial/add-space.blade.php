<div class="ac-modal modal fade bd-example-modal-lg composer-element-popup ckeditor-popup ckbusiness-popup" id="sectionSpacerTemplate" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
    <div class="ac-modal-table">
        <div class="ac-modal-center">
            <div class="modal-dialog">
                <div class="modal-content">
                    {!! Form::open(['method' => 'post','id'=>'frmSectionSpacerTemplate']) !!}
                    <input type="hidden" name="editing">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal">
                            <span>×</span>
                        </button>
                        <h5 class="modal-title" id="exampleModalLabel"><b>Spacer Class</b></h5>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label class="control-label form_title">Spacer Class<span aria-required="true" class="required"> * </span></label>
                            <select name="section_spacer" class="form-control bootstrap-select bs-select layout-class" id="spacerid">
                                <option value="">Spacer Class</option>
                                <option value="9">ac-pt-xs-0</option>
                                <option value="10">ac-pt-xs-5</option>
                                <option value="11">ac-pt-xs-10</option>
                                <option value="12">ac-pt-xs-15</option>
                                <option value="13">ac-pt-xs-20</option>
                                <option value="14">ac-pt-xs-25</option>
                                <option value="15">ac-pt-xs-30</option>
                                <option value="16">ac-pt-xs-40</option>
                                <option value="17">ac-pt-xs-50</option>
                            </select>
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