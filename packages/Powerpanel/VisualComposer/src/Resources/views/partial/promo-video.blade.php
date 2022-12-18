<div class="ac-modal modal fade bd-example-modal-lg composer-element-popup ckeditor-popup" id="sectionVideo" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
    <div class="ac-modal-table">
        <div class="ac-modal-center">
            <div class="modal-dialog">
                <div class="modal-content">
                    {!! Form::open(['method' => 'post','id'=>'frmSectionVideo']) !!}
                    <input type="hidden" name="editing">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal">
                            <span>×</span>
                        </button>
                        <h5 class="modal-title" id="exampleModalLabel"><b>Video</b></h5>
                    </div>
                    <div class="modal-body">

                        <div class="form-group">
                            <label class="control-label form_title">Caption <span aria-required="true" class="required"> * </span></label>          
                            {!! Form::text('title', old('title'), array('maxlength'=>'160','class' => 'form-control','id'=>'videoCaption','autocomplete'=>'off')) !!}
                        </div>

                        @php $unid = uniqid().'builder'; @endphp
                        <div class="form-group">          
                            <label class="form_title" for="site_name">Video Source</label>
                            <div class="md-radio-inline">
                                <div class="md-radio">
                                    <input class="md-radiobtn" checked type="radio" value="YouTube" name="chrVideoType" id="{{ $unid.'1' }}"> 
                                    <label for="{{ $unid.'1' }}"> <span></span> <span class="check"></span> <span class="box"></span> YouTube </label>         
                                </div>
                                <div class="md-radio">
                                    <input class="md-radiobtn" type="radio" value="Vimeo" name="chrVideoType" id="{{ $unid.'2' }}">
                                    <label for="{{ $unid.'2' }}"> <span></span> <span class="check"></span> <span class="box"></span> Vimeo </label>        
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="control-label form_title">Video Embed URL<span aria-required="true" class="required"> * </span>(eg. https://www.youtube.com/embed/9MoKICpeBb8)</label>
                            {!! Form::text('video_id', old('video_id'), array('maxlength'=>'160','class' => 'form-control','id'=>'videoId','autocomplete'=>'off')) !!}
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