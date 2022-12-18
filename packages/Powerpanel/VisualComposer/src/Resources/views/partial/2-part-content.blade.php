<div class="ac-modal modal fade bd-example-modal-lg composer-element-popup ckeditor-popup" id="sectiontwoContent"  role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
    <div class="ac-modal-table">
        <div class="ac-modal-center">
            <div class="modal-dialog">
                <div class="modal-content">
                    {!! Form::open(['method' => 'post','id'=>'frmSectionTwoContent']) !!}
                    <input type="hidden" name="editing">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal">
                            <span>×</span>
                        </button>
                        <h5 class="modal-title" id="exampleModalLabel"><b>2 Part Content</b></h5>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label class="control-label form_title">Left Side Content</label>
                            <textarea class="form-control item-data" name="leftcontent" id="leftck-area" column="40" rows="10"></textarea>
                        </div>
                        <div class="form-group">
                            <label class="control-label form_title">Right Side Content</label>
                            <textarea class="form-control item-data" name="rightcontent" id="rightck-area" column="40" rows="10"></textarea>
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