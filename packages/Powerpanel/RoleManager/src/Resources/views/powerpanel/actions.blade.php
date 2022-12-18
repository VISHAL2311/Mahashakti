@extends('powerpanel.layouts.app')
@section('title')
{{Config::get('Constant.SITE_NAME')}} - PowerPanel
@endsection
@section('css')
<link href="{{ $CDN_PATH.'assets/global/plugins/menu-loader/style.css' }}" rel="stylesheet" type="text/css"/>
<style type="text/css">
    .loading {
        height:20px;
        padding:0 0 0 0;
        position:relative;
        top:-5px;
        left:-15px;
    }
</style>
@endsection
@section('content')
@php
$settings = json_decode(Config::get("Constant.MODULE.SETTINGS"));
$ignoreList = ['Front Home','Sitemap'];
$ignorePermission = ['settings-module-setting','settings-recent-activities','workflow-publish'];
@endphp
@include('powerpanel.partials.breadcrumbs')
{{-- @if (count($errors) > 0)
<div class="alert alert-danger">
	<strong>Whoops!</strong> There were some problems with your input.<br><br>
	<ul>
		@foreach ($errors->all() as $error)
		<li>{{ $error }}</li>
@endforeach
</ul>
</div>
@endif --}}
<div class="col-md-12 settings">
    @if(isset($role))
    {!! Form::model($role, ['id'=>'frmRole','method' => 'PATCH','route' => ['powerpanel.roles.update', $role->id]]) !!}
    @else
    {!! Form::open(array('route' => 'powerpanel.roles.add','method'=>'POST','id'=>'frmRole')) !!}
    @endif
    <div class="row">
        @if(Session::has('message'))
        <div class="alert alert-success">
            <button class="close" data-close="alert"></button>
            {{ Session::get('message') }}
        </div>
        @endif
        <div class="portlet light bordered">
            <div class="portlet-body form_pattern">
                <div class="tabbable tabbable-tabdrop">
                    <div class="tab-content settings">
                        <div class="form-body">
                            <div class="form-group {{ $errors->has('name') ? ' has-error' : '' }} form-md-line-input">
                                <label class="form_title focus-none" for="name">{{ trans('rolemanager::template.common.name') }}  <span aria-required="true" class="required"> * </span></label>    
                                {!! Form::text('name', isset($role->display_name) ? $role->display_name : old('name'), array('maxlength'=>'150','class' => 'form-control input-sm titlespellingcheck','placeholder' => trans('rolemanager::template.common.name'),'autocomplete'=>'off')) !!}
                                
                                <span style="color: red;">
                                    {{ $errors->first('name') }}
                                </span>
                            </div>
                            {{ Form::hidden('rolename', isset($role->name) ? $role->name :'') }}
                            {{-- <div class="form-group {{ $errors->has('description') ? ' has-error' : '' }} form-md-line-input">
                                {!! Form::textarea('description', null, array('maxlength'=>'400','placeholder' => 'Description','class' => 'form-control','style'=>'height:100px','placeholder'=>trans('rolemanager::template.common.description'))) !!}
                                <span style="color: red;">
                                    {{ $errors->first('description') }}
                                </span>
                                <label class="form_title focus-none" for="description"> {{ trans('rolemanager::template.common.description') }} </label>
                            </div> --}}
                            @if($isAdmin)
                            <div class="form-group">
                                <label class="form_title">
                                    Is Admin Role?
                                    <span class="checked_off_on title_checked">
                                        <input @if(isset($role->chrIsAdmin) && $role->chrIsAdmin == 'Y') checked @endif type="checkbox" name="isadmin" id="isadmin" class="make-switch switch-large" data-label-icon="fa fa-fullscreen"  data-on-text="Yes" data-off-text="No"/>
                                    </span>
                                </label>
                            </div>
                            @endif
                            @if(isset($role))
                            <div class="form-group {{ $errors->has('permission') ? ' has-error' : '' }} ">
                                <label class="form_title focus-none" for="permission">{{ trans('rolemanager::template.common.permission') }}  <span aria-required="true" class="required"> * </span></label>
                                <div class="clearfix" style="height:5px;"></div>
                                <div class="row">
                                    <div class="col-md-12">
                                        @foreach($permission as $grp => $group)
                                        @if(strtolower($group['group']) != "logs" || (auth()->user()->hasRole('netquick_admin')))
                                        <div class="grp-sec">
                                            {{-- <div class="permissions_list"> --}}
                                            @php
                                            $grpIdentity =  preg_replace('/[^a-zA-Z0-9\']/', '-', strtolower($group['group']));
                                            @endphp
                                            <label class="form_title border_bottom">
                                                {{$group['group'] }}
                                                {{-- <span class="checked_off_on title_checked activation">
														<input type="checkbox" name="group-active" id="{{ $grpIdentity }}" class="make-switch switch-large group-activation" data-label-icon="fa fa-fullscreen"  data-on-text="Active" data-off-text="In active"/>
                                                </span> --}}
                                            </label>
                                            {{--  </div> --}}
                                            <div class="row {{ $grpIdentity }}">
                                                @foreach($group as $key => $permissions)
                                                @if(is_array($permissions))
                                                @php $permit=[]; $moduleOn=[]; @endphp
                                                @foreach($permissions as $index=>$pval)
                                                @if(isset($pval['name']))
                                                @if(auth()->user()->can($pval['name']) || auth()->user()->hasRole('netquick_admin'))
                                                @php
                                                array_push($permit, $pval['name']);
                                                if(in_array($pval['id'], $rolePermissions)){
                                                array_push($moduleOn, $pval['name']);
                                                }
                                                if(count($moduleOn) == 1){
                                                if (strpos($moduleOn[0], '-reviewchanges') !== false) {
                                                $moduleOn = [];
                                                }
                                                }
                                                @endphp
                                                @endif
                                                @endif
                                                @endforeach
                                                @if(count($permit)>0)
                                                @if(!in_array($key, $ignoreList))
                                                @php
                                                $moduleIdentity =  preg_replace('/[^a-zA-Z0-9\']/', '-', strtolower($key));
                                                @endphp
                                                <div class="col-md-4">
                                                    <div class="permissions_list">
                                                        <label class="form_title">
                                                            {{$key}}
                                                            <span class="checked_off_on activation">
                                                                <input type="checkbox" name="active" id="{{ $moduleIdentity }}" class="make-switch switch-large module-activation" data-label-icon="fa fa-fullscreen"  data-on-text="Active" data-off-text="In active" {{ (count($moduleOn) > 0)?'checked' : '' }}>
                                                            </span>
                                                        </label>
                                                        <span class="right_permis {{ $moduleIdentity }}">
                                                            @foreach($permissions as $index=>$value)
                                                            @if(isset($value['name']))
                                                            @if($value['display_name']=='per_reviewchanges')
                                                            <input type="hidden"  name="reviewPermissions[]" value="{{$value['id']}}">
                                                            @endif
                                                            @if((auth()->user()->can($value['name']) && $value['display_name']!='per_reviewchanges' && !in_array($value['name'], $ignorePermission) ) || ( $value['display_name']!='per_reviewchanges' && !in_array($value['name'], $ignorePermission) && auth()->user()->hasRole('netquick_admin') ))
                                                            {{-- @if(auth()->user()->can($value['name'])) --}}
                                                            <span class="md-checkbox {{$value['display_name']}} menu_active">
                                                                <input id="per-{{$value['id']}}" style="opacity:0" value="{{$value['id']}}" name="permission[{{$value['id']}}]" class="md-check" type="checkbox" {{in_array($value['id'], $rolePermissions) ? 'checked' : ''}}>
                                                                <label for="per-{{$value['id']}}">
                                                                    <span class="inc"></span>
                                                                    <span class="check tooltips" data-toggle="tooltip" data-placement="top" data-original-title="Revoke {{ucwords(str_replace('-',' ', $value['description']))}}"></span>
                                                                    <span class="box tooltips" data-toggle="tooltip" data-placement="top" data-original-title="Grant {{ucwords(str_replace('-',' ', $value['description']))}}"></span>
                                                                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>
                                                            @endif
                                                            @endif
                                                            @endforeach
                                                        </span>
                                                    </div>
                                                </div>
                                                @endif
                                                @endif
                                                @endif
                                                @endforeach
                                            </div>
                                        </div>
                                        @endif
                                        @endforeach
                                    </div>
                                </div>
                                <span style="color: red;">
                                    {{ $errors->first('permission') }}
                                </span>
                            </div>
                            @else
                            <div class="form-group {{ $errors->has('permission') ? ' has-error' : '' }} ">
                                <label class="form_title focus-none" for="permission">{{ trans('rolemanager::template.common.permission') }}  <span aria-required="true" class="required"> * </span></label>
                                <div class="clearfix" style="height:5px;"></div>
                                <div class="row">
                                    <div class="col-md-12">
                                        @foreach($permission as $grp => $group)
                                        @if(strtolower($group['group']) != "logs" || (auth()->user()->hasRole('netquick_admin')))
                                        <div class="grp-sec">
                                            {{-- <div class="permissions_list"> --}}
                                            @php
                                            $grpIdentity =  preg_replace('/[^a-zA-Z0-9\']/', '-', strtolower($group['group']));
                                            @endphp
                                            <label class="form_title border_bottom">
                                                {{$group['group'] }}
                                                {{-- <span class="checked_off_on title_checked activation">
															<input type="checkbox" name="group-active" id="{{ $grpIdentity }}" class="make-switch switch-large group-activation" data-label-icon="fa fa-fullscreen"  data-on-text="Active" data-off-text="In active"/>
                                                </span> --}}
                                            </label>
                                            {{--  </div> --}}
                                            <div class="row {{ $grpIdentity }}">
                                                @foreach($group as $key => $permissions)
                                                @if(is_array($permissions))
                                                @php $permit=[]; @endphp
                                                @foreach($permissions as $index=>$pval)
                                                @if(isset($pval['name']))
                                                @if(auth()->user()->can($pval['name']) || auth()->user()->hasRole('netquick_admin'))
                                                @php
                                                array_push($permit, $pval['name']);
                                                @endphp
                                                @endif
                                                @endif
                                                @endforeach
                                                @if(count($permit)>0)
                                                @if(!in_array($key, $ignoreList) || (auth()->user()->hasRole('netquick_admin') && strtolower($key) == "logs"))
                                                @php
                                                $moduleIdentity =  preg_replace('/[^a-zA-Z0-9\']/', '-', strtolower($key));
                                                @endphp
                                                <div class="col-md-4">
                                                    <div class="permissions_list">
                                                        <label class="form_title">
                                                            <span class="checked_off_on activation">
                                                                <input type="checkbox" name="active" id="{{ $moduleIdentity }}" class="make-switch switch-large module-activation" data-label-icon="fa fa-fullscreen"  data-on-text="Active" data-off-text="In active">
                                                            </span>
                                                            {{$key}}</label>
                                                        <span class="right_permis {{ $moduleIdentity }}">
                                                            @foreach($permissions as $index=>$value)
                                                            @if(isset($value['display_name']) && $value['display_name']=='per_reviewchanges')
                                                            <input type="hidden"  name="reviewPermissions[]" value="{{$value['id']}}">
                                                            @endif
                                                            @if(isset($value['name']) && $value['display_name']!='per_reviewchanges' && !in_array($value['name'], $ignorePermission) )
                                                            {{-- @if(isset($value['name'])) --}}
                                                            @if(auth()->user()->can($value['name']) || auth()->user()->hasRole('netquick_admin'))
                                                            <span class="md-checkbox {{$value['display_name']}} menu_active">
                                                                <input id="per-{{$value['id']}}" style="opacity:0" value="{{$value['id']}}" name="permission[{{$value['id']}}]" class="md-check" type="checkbox">
                                                                <label for="per-{{$value['id']}}">
                                                                    <span class="inc"></span>
                                                                    <span class="check tooltips" data-toggle="tooltip" data-placement="top" data-original-title="Revoke {{ucwords(str_replace('-',' ', $value['description']))}}"></span>
                                                                    <span class="box tooltips" data-toggle="tooltip" data-placement="top" data-original-title="Grant {{ucwords(str_replace('-',' ', $value['description']))}}"></span>
                                                                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>
                                                            @endif
                                                            @endif
                                                            @endforeach
                                                        </span>
                                                    </div>
                                                </div>
                                                @endif
                                                @endif
                                                @endif
                                                @endforeach
                                            </div>
                                        </div>
                                        @endif
                                        @endforeach
                                    </div>
                                </div>
                                <span style="color: red;">
                                    {{ $errors->first('permission') }}
                                </span>
                            </div>
                            @endif
                        </div>
                        <div class="form-actions">
                            <div class="row">
                                <div class="col-md-12">
                                    <button type="submit" name="saveandedit" class="btn btn-green-drake" value="saveandedit" title="{!! trans('rolemanager::template.common.saveandedit') !!}">{!! trans('rolemanager::template.common.saveandedit') !!}</button>
                                    <button type="submit" name="saveandexit" class="btn btn-green-drake" value="saveandexit" title="{!! trans('rolemanager::template.common.saveandexit') !!}"> {!! trans('rolemanager::template.common.saveandexit') !!}</button>
                                    <a class="btn btn-outline red" href="{{url('powerpanel/roles')}}" title="{{ trans('rolemanager::template.common.cancel') }}">{{ trans('rolemanager::template.common.cancel') }}</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    {!! Form::close() !!}
</div>
<div class="clearfix"></div>
@endsection
@section('scripts')
<script type="text/javascript">
    var rootUrl = "{{ URL::to('/') }}";
    var moduleAlias = "";
    var editing = false;
            @if (isset($role -> chrIsAdmin))
            editing = true;
            @endif
</script>
<script src="{{ $CDN_PATH.'resources/global/plugins/bootstrap-maxlength/bootstrap-maxlength.min.js' }}" type="text/javascript"></script>
<script src="{{ $CDN_PATH.'assets/global/plugins/menu-loader/jquery-loader.js' }}" type="text/javascript"></script>
<script src="{{ $CDN_PATH.'resources/pages/scripts/packages/rolemanager/role_validations.js' }}" type="text/javascript"></script>
@endsection