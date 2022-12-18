@section('css')
@endsection
@extends('powerpanel.layouts.app')
@section('title')
{{Config::get('Constant.SITE_NAME')}} - PowerPanel
@endsection
@section('content')
<!--{{--@include('powerpanel.partials.builder-css') --}}  Builder include -->
@php $settings = json_decode(Config::get("Constant.MODULE.SETTINGS")); @endphp
@include('powerpanel.partials.breadcrumbs')
<div class="col-md-12 settings">
    @if(Session::has('message'))
    <div class="row">
        <div class="alert alert-success">
            <button class="close" data-close="alert"></button>
            {{ Session::get('message') }}
        </div>
    </div>
    @endif
    <div class="row">
        <div class="portlet light bordered">
            <div class="portlet-body">
                <div class="tabbable tabbable-tabdrop">
                    <div class="tab-content">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="portlet-body form_pattern">
                                    {!! Form::open(['method' => 'post','id'=>'frmCmsPage']) !!}

                                    <div class="form-body">
                                        @if(isset($Cmspage))
                                        @if (File::exists(base_path() . '/resources/views/powerpanel/partials/lockedpage.blade.php') != null)
                                        @include('powerpanel.partials.lockedpage',['pagedata'=>$Cmspage])
                                        @endif
                                        @endif
                                        <div class="form-group {{ $errors->has('title') ? ' has-error' : '' }} form-md-line-input">
                                            @php if(isset($Cmspage_highLight->varTitle) && ($Cmspage_highLight->varTitle != $Cmspage->varTitle)){
                                            $Class_title = " highlitetext";
                                            }else{
                                            $Class_title = "";
                                            } @endphp
                                            <label class="form_title {!! $Class_title !!}" for="title">{{ trans('cmspage::template.common.title') }} <span aria-required="true" class="required"> * </span></label>
                                            {!! Form::text('title', (isset($Cmspage->varTitle)?$Cmspage->varTitle:old('title')), array('maxlength'=>'150','class' => 'form-control input-sm hasAlias seoField maxlength-handler titlespellingcheck', 'data-url' => 'powerpanel/pages','id' => 'title','placeholder' => trans('cmspage::template.common.title'),'autocomplete'=>'off')) !!}
                                            <span style="color: red;">
                                                {{ $errors->first('title') }}
                                            </span>
                                        </div>
                                        <!-- code for alias -->
                                        {!! Form::hidden(null, null, array('class' => 'hasAlias','data-url' => 'powerpanel/pages')) !!}
                                        {!! Form::hidden('alias', isset($Cmspage->alias->varAlias)?$Cmspage->alias->varAlias:old('alias'), array('class' => 'aliasField')) !!}
                                        {!! Form::hidden('oldAlias', isset($Cmspage->alias->varAlias)?$Cmspage->alias->varAlias:old('alias')) !!}
                                        {!! Form::hidden('fkMainRecord', isset($Cmspage->fkMainRecord)?$Cmspage->fkMainRecord:old('fkMainRecord')) !!}
                                        {!! Form::hidden('previewId') !!}
                                        <div class="form-group alias-group {{!isset($Cmspage->alias)?'hide':''}}">
                                            <label for="Url" class="form_title">{{ trans('cmspage::template.common.url') }} :</label>
                                            @if(isset($Cmspage->alias->varAlias) && !$userIsAdmin)
                                            {{url('/'.$Cmspage->alias->varAlias)}}
                                            @else
                                            @if(auth()->user()->can('pages-create'))
                                            <a href="javascript:void;" class="alias">{!! url("/") !!}</a>
                                            @if(isset($Cmspage->alias) && $Cmspage->alias->varAlias!='home')
                                            <a href="javascript:void(0);" class="editAlias" title="{{ trans('cmspage::template.common.edit') }}">
                                                <i class="fa fa-edit"></i></a>
                                            {{--@if($Cmspage->intFKModuleCode == '4')--}}
                                            <!-- &nbsp;<a class="without_bg_icon openLink" title="{{ trans('cmspage::template.common.openLink') }}" href="{{url('/'.$Cmspage->alias->varAlias)}}" target="_blank"><i class="fa fa-external-link" aria-hidden="true"></i></a> -->
                                            &nbsp;<a class="without_bg_icon openLink" title="{{ trans('cmspage::template.common.openLink') }}" target="_blank" href="{{url($Cmspage->alias->varAlias)}}"><i class="fa fa-external-link" aria-hidden="true"></i></a>
                                            {{--@endif--}}
                                            @elseif(!isset($Cmspage->alias))
                                            <a href="javascript:void(0);" class="editAlias" title="{{ trans('cmspage::template.common.edit') }}">
                                                <i class="fa fa-edit"></i></a>
                                            &nbsp;<a class="without_bg_icon openLink" title="{{ trans('cmspage::template.common.openLink') }}" target="_blank" href="{{url('/')}}"><i class="fa fa-external-link" aria-hidden="true"></i></a>
                                            @endif
                                            @if(isset($Cmspage->alias) && $Cmspage->alias->varAlias=='home')
                                            &nbsp;<a class="without_bg_icon openLink" title="{{ trans('cmspage::template.common.openLink') }}" target="_blank" href="{{url('/')}}"><i class="fa fa-external-link" aria-hidden="true"></i></a>
                                            @endif
                                            @endif
                                            @endif
                                            <span class="help-block">
                                                {{ $errors->first('alias') }}
                                            </span>
                                        </div>
                                        <!-- code for alias -->
                                        @php if(isset($Cmspage_highLight->intFKModuleCode) && ($Cmspage_highLight->intFKModuleCode != $Cmspage->intFKModuleCode)){
                                        $Class_module = " highlitetext";
                                        }else{
                                        $Class_module = "";
                                        } @endphp
                                        @if(isset($Cmspage->alias->varAlias) && $Cmspage->alias->varAlias=='home')
                                        {!! Form::hidden('module','1') !!}
                                        @else
                                        <div @if(isset($Cmspage->alias->varAlias) && $Cmspage->alias->varAlias=='home') style="display: none;" @endif class="form-group @if($errors->first('module')) has-error @endif">
                                              <label class="form_title {!! $Class_module !!}" for="title">{{ trans('cmspage::template.pageModule.module') }}<span aria-required="true" class="required"> * </span></label>
                                            <select class="form-control bs-select select2" name="module">
                                                <option value=" ">--{{ trans('cmspage::template.common.selectmodule') }}--</option>
                                                @if($userIsClient)
                                                <option value="4" selected >Default Page (CMS)</option>
                                                @else
                                                @foreach ($modules as $module)
                                                @php $selected = ''; @endphp
                                                @if(isset($Cmspage->intFKModuleCode))
                                                @if($module['id'] == $Cmspage->intFKModuleCode)
                                                @php $selected = 'selected'; @endphp
                                                @endif
                                                @elseif($module['id'] == 4)
                                                @php $selected = 'selected'; @endphp
                                                @endif
                                                @php
                                                $avoidModules = array('sitemap');
                                                @endphp
                                                @if (!in_array($module['varModuleName'],$avoidModules))
                                                <option value="{{ $module['id'] }}" {{ $selected }} >{{ $module['varModuleName']== "pages"?'Default Page (CMS)':$module['varTitle'] }}</option>
                                                @endif
                                                @endforeach
                                                @endif
                                            </select>
                                            <span class="help-block">
                                                {{ $errors->first('module') }}
                                            </span>
                                        </div>
                                        @endif

                                        @if (Config::get('Constant.DEFAULT_VISUAL') == 'Y')

                                        <div id="body-roll">											
                                            @php
                                            $sections = [];
                                            @endphp
                                            @if(isset($Cmspage))
                                            @php
                                            $sections = json_decode($Cmspage->txtDescription);
                                            @endphp
                                            @endif
                                            <!-- Builder include -->
                                            @php Powerpanel\VisualComposer\Controllers\VisualComposerController::page_section(['sections'=>$sections])@endphp
                                            {{--@include('visualcomposer::page-sections',['sections'=>$sections])--}}
                                            <!--{{-- @include('powerpanel.partials.page-sections',['sections'=>$sections]) --}}-->
                                        </div>
                                        @else
                                        @php if(isset($Cmspage_highLight->txtDescription) && ($Cmspage_highLight->txtDescription != $Cmspage->txtDescription)){
                                        $Class_Description = " highlitetext";
                                        }else{
                                        $Class_Description = "";
                                        } @endphp
                                        <div  class="form-group {{ $errors->has('contents') ? ' has-error' : '' }}">
                                            <label for="default_page_size" class="form_title {!! $Class_Description !!}">{{ trans('cmspage::template.common.description') }}</label>
                                            {!! Form::textarea('contents',(isset($Cmspage->txtDescription)?$Cmspage->txtDescription:old('contents')) , array('class' => 'form-control cms','id'=>'txtDescription')) !!}
                                        </div>
                                        @endif
                                        @if(Config::get('Constant.CHRSearchRank') == 'Y')
                                        @if(isset($Cmspage->intSearchRank))
                                        @php $srank = $Cmspage->intSearchRank; @endphp
                                        @else
                                        @php
                                        $srank = null !== old('search_rank') ? old('search_rank') : 2 ;
                                        @endphp
                                        @endif
                                        @if(isset($Cmspage_highLight->intSearchRank) && ($Cmspage_highLight->intSearchRank != $Cmspage->intSearchRank))
                                        @php $Class_intSearchRank = " highlitetext"; @endphp
                                        @else
                                        @php $Class_intSearchRank = ""; @endphp
                                        @endif
                                        <div class="row" style="display:none;">
                                            <div class="col-md-12">
                                                <label class="{{ $Class_intSearchRank }} form_title">Search Ranking</label>
                                                <div class="wrapper search_rank">
                                                    <label for="yes_radio" id="yes-lbl">High</label><input type="radio" value="1" name="search_rank" @if($srank == 1) checked @endif id="yes_radio">
                                                                                                           <label for="maybe_radio" id="maybe-lbl">Medium</label><input type="radio" value="2" name="search_rank" @if($srank == 2) checked @endif id="maybe_radio">
                                                                                                           <label for="no_radio" id="no-lbl">Low</label><input type="radio" value="3" name="search_rank" @if($srank == 3) checked @endif id="no_radio">
                                                                                                           <div class="toggle"></div>
                                                </div>
                                            </div>
                                        </div>
                                        @endif
                                        @if(Config::get('Constant.CHRContentScheduling') == 'Y')
                                        <h3  style="display:none;" class="form-section">{{ trans('cmspage::template.common.ContentScheduling') }}</h3>
                                        @php $defaultDt = (null !== old('start_date_time'))?old('start_date_time'):date('Y-m-d H:i'); @endphp
                                        <div class="row" style="display:none;">
                                            <div  style="display:none;" class="col-md-6">
                                                <div class="form-group form-md-line-input">
                                                    @php if(isset($Cmspage_highLight->dtDateTime) && ($Cmspage_highLight->dtDateTime != $Cmspage->dtDateTime)){
                                                    $Class_date = " highlitetext";
                                                    }else{
                                                    $Class_date = "";
                                                    } @endphp
                                                    <label class="control-label form_title {!! $Class_date !!}">{{ trans('cmspage::template.common.startDateAndTime') }}<span aria-required="true" class="required"> * </span></label>
                                                    <div class="input-group date form_meridian_datetime @if($errors->first('start_date_time')) has-error @endif" data-date="{{ Carbon\Carbon::today()->format('Y-m-d') }}T15:25:00Z">
                                                        <span class="input-group-btn date_default">
                                                            <button class="btn date-set fromButton" type="button">
                                                                <i class="fa fa-calendar"></i>
                                                            </button>
                                                        </span>
                                                        {!! Form::text('start_date_time', date('Y-m-d H:i',strtotime(isset($Cmspage->dtDateTime)?$Cmspage->dtDateTime:$defaultDt)), array('class' => 'form-control','maxlength'=>160,'size'=>'16','id'=>'start_date_time','autocomplete'=>'off','onkeypress'=>"javascript: return KeycheckOnlyDate(event);",'onpaste'=>'return false')) !!}
                                                    </div>
                                                    <span class="help-block">
                                                        {{ $errors->first('start_date_time') }}
                                                    </span>
                                                </div>
                                            </div>
                                            @php $defaultDt = (null !== old('end_date_time'))?old('end_date_time'):null; @endphp
                                            @if ((isset($Cmspage->dtEndDateTime)==null))
                                            @php
                                            $expChecked_yes = 1;
                                            $expclass='';
                                            @endphp
                                            @else
                                            @php
                                            $expChecked_yes = 0;
                                            $expclass='no_expiry';
                                            @endphp
                                            @endif
                                            <div class="col-md-6">
                                                <div class="form-group form-md-line-input">
                                                    <div class="input-group date  form_meridian_datetime expirydate @if($errors->first('end_date_time')) has-error @endif" data-date="{{ Carbon\Carbon::today()->format('Y-m-d') }}T15:25:00Z" @if ($expChecked_yes==1) style="display:none;" @endif>
                                                         @php if(isset($Cmspage_highLight->varTitle) && ($Cmspage_highLight->dtEndDateTime != $Cmspage->dtEndDateTime)){
                                                         $Class_end_date = " highlitetext";
                                                         }else{
                                                         $Class_end_date = "";
                                                         } @endphp
                                                         <label class="control-label form_title {!! $Class_end_date !!}" >{{ trans('cmspage::template.common.endDateAndTime') }} <span aria-required="true" class="required"> * </span></label>
                                                        <div class="pos_cal">
                                                            <span class="input-group-btn date_default">
                                                                <button class="btn date-set toButton" type="button">
                                                                    <i class="fa fa-calendar"></i>
                                                                </button>
                                                            </span>
                                                            {!! Form::text('end_date_time', isset($Cmspage->dtEndDateTime)?date('Y-m-d H:i',strtotime($Cmspage->dtEndDateTime)):$defaultDt, array('class' => 'form-control','maxlength'=>160,'size'=>'16','id'=>'end_date_time','data-exp'=> $expChecked_yes,'data-newvalue','autocomplete'=>'off','onkeypress'=>"javascript: return KeycheckOnlyDate(event);",'onpaste'=>'return false')) !!}
                                                        </div>
                                                    </div>
                                                    <span class="help-block">
                                                        {{ $errors->first('end_date_time') }}
                                                    </span>
                                                    <label class="expdatelabel {{ $expclass }}">
                                                        <a id="noexpiry" name="noexpiry" href="javascript:void(0);">
                                                            <b class="expiry_lbl {!! $Class_end_date !!}"></b>
                                                        </a>
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                        @endif
                                        <div class="{{ $errors->has('display') ? ' has-error' : '' }} ">
                                            @php  $form = 'frmCmsPage';  @endphp
                                            @include('powerpanel.partials.seoInfo',['form'=> 'frmCmsPage','inf'=> isset($metaInfo)?$metaInfo:false,'inf_highLight'=> isset($metaInfo_highLight)?$metaInfo_highLight:false])
                                            @if(isset($Cmspage) && $Cmspage->alias->varAlias == 'home')
                                            {!! Form::hidden('chrMenuDisplay', 'Y') !!}
                                            {!! Form::hidden('chrPageActive', 'PU') !!}
                                            @endif

                                            @if(isset($Cmspage) && $Cmspage->intFKModuleCode !== 3)
                                                    @php $display = 'none'; @endphp
                                                @elseif(isset($menus) && $menus == 1)
                                                    @php $display = 'none'; @endphp
                                                @elseif(isset($Cmspage) && $Cmspage->id == 10)
                                                    @php $display = 'none'; @endphp
                                                @else
                                                    @php $display = ''; @endphp
                                                @endif

                                            @if(isset($publishActionDisplay))
                                            <div class="row">
                                                <div class="col-md-6" style="display:{{$display}}">
                                                    @if(isset($Cmspage_highLight->chrPublish) && ($Cmspage_highLight->chrPublish != $Cmspage->chrPublish))
                                                    @php $Class_chrPublish = " highlitetext"; @endphp
                                                    @else
                                                    @php $Class_chrPublish = ""; @endphp
                                                    @endif
                                                    @if((isset($Cmspage) && $Cmspage->chrDraft == 'D'))
                                                    @include('powerpanel.partials.displayInfo',['Class_chrPublish'=>$Class_chrPublish,'display' => (isset($Cmspage->chrDraft)?$Cmspage->chrDraft:'D')])
                                                    @else
                                                    @include('powerpanel.partials.displayInfo',['Class_chrPublish'=>$Class_chrPublish,'display' => (isset($Cmspage->chrPublish)?$Cmspage->chrPublish:'Y')])
                                                    @endif
                                                </div>

                                                @if (Config::get('Constant.DEFAULT_VISIBILITY') == 'Y')
                                                @if(isset($publishActionDisplay))
                                                @if(isset($Cmspage->chrPageActive))
                                                @php $srank1 = $Cmspage->chrPageActive; @endphp
                                                @else
                                                @php
                                                $srank1 = null !== old('chrPageActive') ? old('chrPageActive') : 'PU' ;
                                                @endphp
                                                @endif
                                                @if(isset($Cmspage_highLight->chrPageActive) && ($Cmspage_highLight->chrPageActive != $Cmspage->chrPageActive))
                                                @php $Class_chrPageActive = " highlitetext"; @endphp
                                                @else
                                                @php $Class_chrPageActive = ""; @endphp
                                                @endif
                                                <div class="col-md-6">
                                                    <label class="{{ $Class_chrPageActive }} form_title">Visibility</label>
                                                    <div class="md-radio-inline">
                                                        <div class="md-radio">
                                                            <input type="radio" value="PU" name="chrPageActive" id="chrPageActivePU" @if($srank1 == 'PU') checked @endif id="yes_radio_2"><label for="chrPageActivePU" onclick="OpenPassword('PU')"> <span></span> <span class="check"></span> <span class="box"></span> Public </label>
                                                        </div>
                                                        <div class="md-radio">
                                                            <input type="radio" value="PR" name="chrPageActive" id="chrPageActivePR" @if($srank1 == 'PR') checked @endif id="maybe_radio_2"><label for="chrPageActivePR" onclick="OpenPassword('PR')"> <span></span> <span class="check"></span> <span class="box"></span> Private </label>
                                                        </div>
                                                        <div class="md-radio">
                                                            <input type="radio" value="PP" name="chrPageActive" id="chrPageActivePP" @if($srank1 == 'PP') checked @endif id="no_radio_2"><label for="chrPageActivePP" onclick="OpenPassword('PP')"> <span></span> <span class="check"></span> <span class="box"></span> Password Protected </label>
                                                        </div>
                                                    </div>
                                                    <div class="toggle"></div>
                                                   
                                                    @if(!empty($Cmspage->varPassword) && $Cmspage->chrPageActive == 'PP')
                                                    @php  $password = ''; @endphp
                                                    @else
                                                    @php
                                                    
                                                    $password = "style=display:none" ;
                                                    @endphp
                                                    @endif
                                                    <div id='passid' {{ $password }}>
                                                        <div class="form-group"><label class="form_title" for="site_name">Url :</label> <a href="javascript:void;" class="alias">' + links + '</a></div>
                                                        <div class="form-group {{ $errors->has('varPassword') ? ' has-error' : '' }} form-md-line-input">
                                                            @php 
                                                            if(isset($Cmspage_highLight->varPassword) && ($Cmspage_highLight->varPassword != $Cmspage->varPassword)){
                                                            $Class_varPassword = " highlitetext";
                                                            }else{
                                                            $Class_varPassword = "";
                                                            } @endphp
                                                            <label class="form_varPassword {!! $Class_varPassword !!}" for="varPassword">Password <span aria-required="true" class="required"> * </span></label>
                                                            {!! Form::text('new_password', (isset($Cmspage->varPassword)?$Cmspage->varPassword:old('new_password')), array('autocomplete' => 'off','placeholder'=> 'Password', 'maxlength'=>20,'class' => 'form-control','id'=>'newpassword')) !!}
                                                            <span style="color: red;">
                                                                {{ $errors->first('new_password') }}
                                                            </span>
                                                            <div class="pswd_info" id="newpassword_info">
                                                                <h4>Password must meet the following requirements:</h4>
                                                                <ul>
                                                                    <li id="letter" class="letterinfo invalid">At least <strong>one letter</strong></li>
                                                                    <li id="capital" class="capitalletterinfo invalid">At least <strong>one capital letter</strong></li>
                                                                    <li id="number" class="numberinfo invalid">At least <strong>one number</strong></li>
                                                                    <li id="length" class="lengthInfo invalid">Password should be <strong>6 to 20 characters</strong></li>
                                                                    <li id="special" class="specialinfo invalid">At least <strong>one special character</strong></li>
                                                                </ul>


                                                            </div>
                                                            <br>
                                                            
                                                            <p><b>NOTE:</b> The page will be published as password protected.</p>
                                                        </div>
                                                    </div>
                                                    @endif
                                                    @if(isset($Cmspage->chrPageActive)&&($Cmspage->chrPageActive == 'PR'))
                                                    @php  $private = ''; @endphp
                                                    @else
                                                    @php
                                                    
                                                    $private =  "style=display:none" ;
                                                    @endphp
                                                    @endif
                                                    <div id="noteid"{{ $private }}>
                                                        <div class="form-group"><label class="form_title" for="site_name">Url :</label> <a href="javascript:void;" class="alias">' + links + '</a>
                                                        <p><b>NOTE:</b> The page will be published as private only you can view it. For view this page you have to remain login in PowerPanel.</p>
                                                        </div>
                                                    </div>

                                                    <!--</div>-->
                                                </div>
                                                @endif
                                            </div>
                                            <span style="color: red;">
                                                {{ $errors->first('display') }}
                                            </span>
                                            @endif
                                            <div class="form-actions">
                                                <div class="row">
                                                    <div class="col-md-12">
                                                        @if(isset($Cmspage->fkMainRecord) && $Cmspage->fkMainRecord != 0)
                                                        <button type="submit" name="saveandexit" class="btn btn-green-drake" value="saveandexit" title="{!! trans('cmspage::template.common.approve') !!}">{!! trans('cmspage::template.common.approve') !!}</button>
                                                        @else
                                                        @if($userIsAdmin)
                                                        <button type="submit" name="saveandedit" class="btn btn-green-drake" value="saveandedit" title="{!! trans('cmspage::template.common.saveandedit') !!}">{!! trans('cmspage::template.common.saveandedit') !!}</button>
                                                        <button type="submit" name="saveandexit" class="btn btn-green-drake" value="saveandexit" title="{!! trans('cmspage::template.common.saveandexit') !!}">{!! trans('cmspage::template.common.saveandexit') !!}</button>
                                                        @else
                                                        @if((isset($chrNeedAddPermission) && $chrNeedAddPermission == 'N') && (isset($charNeedApproval) && $charNeedApproval == 'N'))
                                                        <button type="submit" name="saveandexit" class="btn btn-green-drake" value="saveandexit" title="{!! trans('cmspage::template.common.saveandexit') !!}">{!! trans('cmspage::template.common.saveandexit') !!}</button>
                                                        @else
                                                        <button type="submit" name="saveandexit" class="btn btn-green-drake" value="approvesaveandexit" title="{!! trans('cmspage::template.common.approvesaveandexit') !!}">{!! trans('cmspage::template.common.approvesaveandexit') !!}</button>
                                                        @endif
                                                        @endif
                                                        @endif
                                                    @if(isset($menus) && $menus == 3)
                                                        @php $display1 = 'none'; @endphp
                                                    @elseif(isset($Cmspage) && $Cmspage->id == 10)
                                                        @php $display1 = 'none'; @endphp
                                                    @else
                                                        @php $display1 = ''; @endphp
                                                    @endif
                                                        <button style="display:{{$display1}}" type="submit" name="saveandmenu" class="btn btn-green-drake" title="Save & Assign to Menu" value="saveandmenu">Save & Assign to Menu</button>
                                                        <!--                                                        @if(!isset($Cmspage) || (isset($Cmspage) && $Cmspage->chrDraft == 'Y'))
                                                                                                                <button type="submit" name="Save as Draft" class="btn btn-outline btn-primary" value="saveasdraft">Save as Draft</button>
                                                                                                                @endif-->
                                                        @php
                                                        if(isset($_REQUEST['tab']) && $_REQUEST['tab'] == 'P'){
                                                        $tab = '?tab=P';
                                                        }else if(isset($_REQUEST['tab']) && $_REQUEST['tab'] == 'A'){
                                                        $tab = '?tab=A';
                                                        }else if(isset($_REQUEST['tab']) && $_REQUEST['tab'] == 'D'){
                                                        $tab = '?tab=D';
                                                        }else if(isset($_REQUEST['tab']) && $_REQUEST['tab'] == 'T'){
                                                        $tab = '?tab=T';
                                                        }else{
                                                        $tab = '';
                                                        }
                                                        @endphp
                                                        <a class="btn red btn-outline" href="{{ url('powerpanel/pages'.$tab) }}" title="{{ trans('cmspage::template.common.cancel') }}">{{ trans('cmspage::template.common.cancel') }}</a>
                                                    </div>
                                                </div>
                                            </div>
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
    @if (Config::get('Constant.DEFAULT_VISUAL') == 'Y')
    {{--@include('powerpanel.partials.dialog-maker') --}}
    @php Powerpanel\VisualComposer\Controllers\VisualComposerController::get_dialog_maker()@endphp
    @endif
    @endsection
    @section('scripts')
    @if (Config::get('Constant.DEFAULT_VISUAL') == 'Y')
    @php Powerpanel\VisualComposer\Controllers\VisualComposerController::get_visual_checkEditor()@endphp
    @else
    @include('powerpanel.partials.ckeditor',['config'=>'docsConfig'])
    @endif

    <script type="text/javascript">
                        window.site_url = '{!! url("/") !!}';
                        var seoFormId = 'frmCmsPage';
                        var user_action = "{{ isset($Cmspage)?'edit':'add' }}";
                        var moduleAlias = '';
                        var preview_add_route = '{!! route("powerpanel.pages.addpreview") !!}';
                        var previewForm = $('#frmCmsPage');
                        var isDetailPage = false;
                        function generate_seocontent1(formname) {
                        var Meta_Title = document.getElementById('title').value + "";
                                var abcd = $('textarea#txtDescription').val();
                                if (abcd != undefined){
                        var def = abcd.replace(/<a(\s[^>]*)?>.*?<\/a>/ig, "");
                                var abc = def.replace(/^(\s*)|(\s*)$/g, '').replace(/\s+/g, ' ');
                                var outString1 = abc.replace(/(<([^>]+)>)/ig, "");
                                var Meta_Description = outString1.substr(0, 200);
                        } else{
                        var Meta_Description = document.getElementById('title').value + "";
                        }

                        var Meta_Keyword = document.getElementById('title').value + "" + document.getElementById('title').value;
                                $('#varMetaTitle').val(Meta_Title);
//                        $('#varMetaKeyword').val(Meta_Keyword);
                                $('#varMetaDescription').val(Meta_Description);
                                $('#meta_title').html(Meta_Title);
                                $('#meta_description').html(Meta_Description);
                        }

    </script>
    <!-- <script src="{{ $CDN_PATH.'resources/pages/scripts/visual_composer-ajax.js' }}" type="text/javascript">
    </script> -->

    <script src="{{ $CDN_PATH.'messages.js' }}" type="text/javascript"></script>
    <script src="{{ $CDN_PATH.'resources/pages/scripts/packages/cmspage/cmspages_validations.js' }}" type="text/javascript"></script>
    <script src="{{ $CDN_PATH.'resources/global/plugins/bootstrap-maxlength/bootstrap-maxlength.min.js' }}" type="text/javascript"></script>
    <script type="text/javascript">
                        $('select[name=module]').select2({
                placeholder: "Select Module",
                        width: '100%'
                }).on("change", function (e) {
                $("select[name=module]").closest('.has-error').removeClass('has-error');
                        $("#module-error").remove();
                });</script>
    <script type="text/javascript">
                        function OpenPassword(val){
                        if (val == 'PP') {
                        $("#passid").show();
                        } else {
                        $("#passid").hide();
                        }
                        if (val == 'PR') {
                        $("#noteid").show();
                        } else {
                        $("#noteid").hide();
                        }
                        }


    </script>
    <script src="{{ $CDN_PATH.'resources/pages/scripts/custom.js' }}" type="text/javascript"></script>
    <script src="{{ $CDN_PATH.'resources/global/plugins/custom-alias/alias-generator.js' }}" type="text/javascript"></script>
    <!-- BEGIN CORE PLUGINS -->
    <script src="{{ $CDN_PATH.'resources/global/plugins/seo-generator/seo-info-generator.js' }}" type="text/javascript"></script>
    <script src="{{ Config::get('Constant.CDN_PATH').'resources/pages/scripts/pages_password_rules.js' }}" type="text/javascript"></script>
    <!-- END CORE PLUGINS -->
    <!--{{--@include('powerpanel.partials.builder-js') --}} Builder include -->
    @if (Config::get('Constant.DEFAULT_VISUAL') == 'Y')
    @php Powerpanel\VisualComposer\Controllers\VisualComposerController::get_builder_css_js()@endphp
    @endif
    @endsection