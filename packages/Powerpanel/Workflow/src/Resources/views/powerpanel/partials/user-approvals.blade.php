{!! Form::open(['method' => 'post','id'=>'frmWorkflowApproval']) !!}
{!! Form::hidden('workflow_type', 'approvals') !!}
{!! Form::hidden('activity','approvals') !!}
{!! Form::hidden('action','approvals') !!}
<div class="form-body">
    <div class="flow_form">
        @if(isset($workflow->varUserId))
        @php $user_selected = explode(',', $workflow->varUserId); @endphp
        @elseif(null !== old('user'))
        @php $user_selected = old('user'); @endphp
        @else
        @php $user_selected = []; @endphp
        @endif
        @php $needs_permissions = (count($user_selected) >= 2 && !in_array('', $user_selected));  @endphp
        <div class="row">
            <div class="col-md-12">
                <div class="form-group ">
                    <div class="user_fill">
                        <span>Start</span>
                    </div>
                </div>
                <div class="arrow_line"><span></span></div>
                <div class="form-group @if($errors->first('user_roles')) has-error @endif form-md-line-input">
                    <label class="form_title" class="site_name">Select role to create workflow <span aria-required="true" class="required"> * </span></label>
                    <div class="clearfix"></div>					
                    <div class="input_box">
                        @php $old_user_roles = old('user_roles') == null ? '' : old('user_roles'); @endphp
                        <select id="user_roles" name="user_roles" data-sort data-order class="form-control bs-select select2 status_select">
                            <option value="">Select Role</option>
                            @foreach($nonAdminRoles as $role)
                            <option @if( isset($workflow->varUserRoles) && $role->id == $workflow->varUserRoles)  selected @endif value="{{ $role->id }}">{{ $role->display_name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <span class="help-block">
                        {{ $errors->first('user_roles') }}
                    </span>
                </div>
                <!-- <div class="arrow_line"><span></span></div> -->
                <!-- <div class="category_id_msg clearfix">
                    <div class="left">
                        <div class="form-group @if($errors->first('category_id')) has-error @endif form-md-line-input">
                            <label class="form_title" class="site_name">Category <span aria-required="true" class="required"> * </span></label>
                            <div class="clearfix"></div>
                            @if(isset($workflow->intCategoryId))
                            @php $selected = $workflow->intCategoryId; @endphp
                            @elseif(null !== old('category_id'))
                            @php $selected = old('category_id'); @endphp
                            @else
                            @php $selected = ''; @endphp
                            @endif
                            <div class="input_box">								
                                <select data-selected="{{ $selected }}" id="category_id" name="category_id" data-sort data-order class="form-control bs-select select2 status_select">
                                    <option value="" @if($selected == "") selected @endif>Select Category</option>										
                                </select>
                            </div>
                            <span class="help-block">
                                {{ $errors->first('category_id') }}
                            </span>
                        </div>
                    </div>					
                </div> -->
                <div class="moduls_box_approval" id="modulehtml" style="display:none">
                    <div class="arrow_line"><span></span></div>
                    <div class="spacer15"></div>
                    @if(isset($workflow->varUserId))
                    @php $selected = explode(',', $workflow->varUserId); @endphp
                    @elseif(null !== old('user'))
                    @php $selected = old('user'); @endphp
                    @else
                    @php $selected = []; @endphp
                    @endif
                    @php $needs = (count($selected) >= 2 && !in_array('', $selected));  @endphp
                    <div class="row">                                                                
                        <div class="col-xs-5 col-xss-12">
                            <div class="side_moduls_box">
                                <div class="form-group form-md-line-input">
                                    <label class="form_title">Module List <span class="approval_div">(Approval Needed)</span> <a href="javascript:;" id="approvalid">Select All</a></label>
                                    <div class="select_moduls_box">
                                        <select name="catwise_modules[]" id="undo_redo" class="form-control" size="" multiple="multiple"></select>
                                    </div>    
                                </div>
                                <div id="errorToShow"></div>  
                                <div class="arrow_line"><span></span></div>
                                <img class="image-need" src="{{ url('resources/image/packages/workflow/workflow1.png') }}" name="Module List (Approval Needed)">
                                <div class="arrow_line"><span></span></div>
                                <div class="form-group form-md-line-input">	
                                    <div class="input_box @if($errors->first('user')) has-error @endif ">
                                        <label class="form_title site_name">Select Admins<span aria-required="true" class="required"> * </span></label>
                                        <select data-selected="{{ implode(',', $selected) }}" id="user" multiple name="user[]" data-sort data-order class="form-control bs-select select2 status_select">
                                            @php $selected_admin = ''; @endphp
                                            @if($selected == "") 
                                               @php  $selected_admin =  'selected'  @endphp
                                            @endif
                                            <option style="width:100% !important;" {{ $selected_admin }} value="">Select Admin</option>
                                        </select>
                                        <span class="help-block">
                                            {{ $errors->first('user') }}
                                        </span>
                                    </div>			
                                </div>
                            </div>                                                                  
                        </div>
                        <div class="col-xs-2 col-xss-12">                                                                
                            <div class="moduls_select_buttons clearfix">
                                <button type="button" id="undo_redo_rightAll" class="btn btn-green-drake"><i class="glyphicon glyphicon-forward"></i></button>
                                <button type="button" id="undo_redo_rightSelected" class="btn btn-green-drake"><i class="glyphicon glyphicon-chevron-right"></i></button>
                                <button type="button" id="undo_redo_leftSelected" class="btn btn-green-drake"><i class="glyphicon glyphicon-chevron-left"></i></button>
                                <button type="button" id="undo_redo_leftAll" class="btn btn-green-drake"><i class="glyphicon glyphicon-backward"></i></button>
                            </div>                                                                        
                        </div>
                        <div class="col-xs-5 col-xss-12"> 
                            <div class="side_moduls_box">
                                <div class="form-group form-md-line-input">    
                                    <label class="form_title">Module List <span class="noapproval_div">(No Approval Needed)</span> <a href="javascript:;" id="noapprovalid">Select All</a></label>
                                    <div class="select_moduls_box">
                                        <select name="directApproved[]" id="undo_redo_to" class="form-control" size="" multiple="multiple"></select>
                                    </div>   
                                </div>                                                                                                                                     
                                <div class="arrow_line"><span></span></div>
                                <img class="image-need" src="{{ url('resources/image/packages/workflow/workflow2.png') }}" name="Module List (Approval Needed)">
                                <div class="arrow_line"><span></span></div>
                                <div class="form-group form-md-line-input">
                                    <div class="input_box">                                                                        
                                        <div class="row_inp_rh">									
                                            <label class="form_title" >Direct Approved</label>									
                                        </div>
                                    </div>
                                </div>
                            </div>    
                        </div>
                    </div>
                </div>
            </div>
            <div class="clearfix"></div>
        </div>
    </div>
</div>
<div class="form-actions">
    <div class="row">
        <div class="col-md-12 text-center">
            <button type="submit" name="saveandexit" class="btn btn-green-drake" value="saveandexit">{!! trans('workflow::template.common.saveandexit') !!}</button>
            <a class="btn btn-outline red" href="{{ url('powerpanel/workflow') }}">{{ trans('workflow::template.common.cancel') }}</a>
        </div>
    </div>
</div>
{!! Form::close() !!}
