<div class="modal fade bd-example-modal-lg composer-element-popup ckeditor-popup" id="sectionIframe" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      {!! Form::open(['method' => 'post','id'=>'frmSectionIframe']) !!}
      <input type="hidden" name="editing">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">
        <span>Ã—</span>
        </button>
        <h5 class="modal-title" id="exampleModalLabel"><b>Iframe</b></h5>
      </div>
      <div class="modal-body">
        <div class="form-group">
          <label class="control-label form_title">Iframe URL<span aria-required="true" class="required"> * </span></label>
          <input class="form-control item-data" name="content" />
        </div>
        <div class="form-group">
          <label class="control-label form_title">Extra Class</label>
          {!! Form::text('extra_class', old('extra_class'), array('maxlength'=>'160','class' => 'form-control extraClass','autocomplete'=>'off')) !!}
        </div>
        <div class="text-right">
          <button type="button" class="btn red btn-outline" data-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-green-drake addSection">Add</button>
        </div>
      </div>
      {!! Form::close() !!}
    </div>
  </div>
</div>