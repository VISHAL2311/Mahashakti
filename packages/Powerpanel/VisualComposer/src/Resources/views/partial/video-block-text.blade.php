<div class="ac-modal modal fade bd-example-modal-lg composer-element-popup ckeditor-popup" id="sectionVideoContent"  role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
    <div class="ac-modal-table">
        <div class="ac-modal-center">
            <div class="modal-dialog">
                <div class="modal-content">
                    {!! Form::open(['method' => 'post','id'=>'frmsectionVideoContent']) !!}
                    <input type="hidden" name="editing">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal">
                            <span>×</span>
                        </button>
                        <h5 class="modal-title" id="exampleModalLabel"><b>Video with Information</b></h5>
                    </div>
                    <div class="modal-body">
                        @php $imgkey = 1; @endphp
                        <div class="form-group">
                            <label class="control-label form_title">Caption</label>          
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
                                                <input type="radio" id="home-left-video" name="selector" value="lft-txt">
                                                <label for="home-left-video"></label>
                                                <div class="check"><div class="inside"></div></div>
                                                <i class="icon"><img src="{{ url('assets/images/packages/visualcomposer/left-video.png') }}" alt=""></i>
                                            </a>
                                        </li>
                                        <li>
                                            <a href="javascript:;" title="Align Right">
                                                <input type="radio" id="home-right-video" name="selector" value="rt-txt">
                                                <label for="home-right-video"></label>
                                                <div class="check"><div class="inside"></div></div>
                                                <i class="icon"><img src="{{ url('assets/images/packages/visualcomposer/right-video.png') }}" alt=""></i>
                                            </a>
                                        </li>
                                        <li>
                                            <a href="javascript:;" title="Align Top">
                                                <input type="radio" id="home-top-video" name="selector" value="top-txt">
                                                <label for="home-top-video"></label>
                                                <div class="check"><div class="inside"></div></div>
                                                <i class="icon"><img src="{{ url('assets/images/packages/visualcomposer/top-video.png') }}" alt=""></i>
                                            </a>
                                        </li>
                                        <li>
                                            <a href="javascript:;" title="Align Center">
                                                <input type="radio" id="home-center-video" name="selector" value="center-txt">
                                                <label for="home-center-video"></label>
                                                <div class="check"><div class="inside"></div></div>
                                                <i class="icon"><img src="{{ url('assets/images/packages/visualcomposer/center-video.png') }}" alt=""></i>
                                            </a>
                                        </li>
                                        <li>
                                            <a href="javascript:;" title="Align Bottom">
                                                <input type="radio" id="bottom-video" name="selector" value="bot-txt">
                                                <label for="bottom-video"></label>
                                                <div class="check"><div class="inside"></div></div>
                                                <i class="icon"><img src="{{ url('assets/images/packages/visualcomposer/bottom-video.png') }}" alt=""></i>
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