@extends('powerpanel.layouts.app')
@section('title')
{{Config::get('Constant.SITE_NAME')}} - PowerPanel
@stop
@section('content')
@php $settings = json_decode(Config::get("Constant.MODULE.SETTINGS")); @endphp
@include('powerpanel.partials.breadcrumbs')
<div class="col-md-12 settings">
    <div class="row">
        @if(Session::has('message'))
        <div class="alert alert-success">
            <button class="close" data-close="alert"></button>
            {{ Session::get('message') }}
        </div>
        @endif
        <div class="portlet light bordered">
            <div class="portlet-body">
                <div class="tabbable tabbable-tabdrop">
                    <div class="tab-content">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="portlet-body form_pattern">
                                    {!! Form::open(['method' => 'post','id'=>'frmblockips']) !!}
                                    <div class="form-group @if($errors->first('tag_line')) has-error @endif form-md-line-input">
                                        <label class="form_title" for="site_name">IP Address <span aria-required="true" class="required"> * </span></label>
                                        {!! Form::text('ip_address', isset($department->varTitle) ? $department->varTitle:old('ip_address'), array('maxlength'=>'20','placeholder' => 'IP Address','class' => 'form-control maxlength-handler','autocomplete'=>'off','onkeypress'=>'javascript: return KeycheckOnlyAmount(event);')) !!}
                                        <span class="help-block">
                                            {{ $errors->first('ip_address') }}
                                        </span>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12">
                                            @if($userIsAdmin)
                                            <button type="submit" name="saveandexit" class="btn btn-green-drake" value="saveandexit">{!! trans('template.common.saveandexit') !!}</button>
                                            @endif
                                            <a class="btn red btn-outline" href="{{ url('powerpanel/blocked-ips') }}">{{ trans('template.common.cancel') }}</a>
                                        </div>
                                    </div>
                                    {!! Form::close() !!}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="clearfix"></div>
@endsection
@section('scripts')
<script type="text/javascript">
    window.site_url = '{!! url("/") !!}';
    var user_action = "{{ isset($department)?'edit':'add' }}";
    var moduleAlias = 'department';
</script>
<script src="{{ $CDN_PATH.'resources/pages/scripts/packages/blockip/blocked_ips_validations.js' }}" type="text/javascript"></script>
<script src="{{ $CDN_PATH.'resources/pages/scripts/numbervalidation.js' }}" type="text/javascript"></script>
<script src="{{ $CDN_PATH.'resources/pages/scripts/custom.js' }}" type="text/javascript"></script>
<script src="{{ $CDN_PATH.'resources/global/plugins/bootstrap-maxlength/bootstrap-maxlength.min.js' }}" type="text/javascript"></script>
@endsection