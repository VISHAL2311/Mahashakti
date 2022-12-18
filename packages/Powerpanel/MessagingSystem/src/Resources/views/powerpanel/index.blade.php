@extends('powerpanel.layouts.app') @section('title') {{Config::get('Constant.SITE_NAME')}} - PowerPanel @stop @section('css')
<link href="{{ $CDN_PATH.'resources/css/packages/messagingsystem/contextMenu.css' }}" rel="stylesheet" type="text/css" />
<link href="{{ $CDN_PATH.'resources/css/packages/messagingsystem/messagingsystem.css' }}" rel="stylesheet" type="text/css" />
@endsection @section('content') {!! csrf_field() !!}
<div class="row">
    <div class="col-md-12">
        <!-- TITILE HEAD START -->
        <div class="title-dropdown_sec text-center">
            <div class="title_bar">
                <div class="page-head">
                    <div class="page-title">
                        <h1>Messaging System</h1>
                    </div>
                </div>
            </div>
        </div>
        <!-- TITILE HEAD End... -->
        <!-- Begin: life time stats -->
        @if(Session::has('message'))
        <div class="alert alert-success">
            <button class="close" data-close="alert"></button>
            {{ Session::get('message') }}
        </div>
        @endif
        <!-- Massage System S -->
        <div class="massage_system">
            <div class="row">
                <div class="col-lg-3 col-sm-4 col-xs-12 ac-nopadding">
                    <div class="portlet light portlet_msg_sidebar">
                        <div class="message-user-title">
                            <h2>Chats</h2>
                        </div>
                        <div class="message-user-search">
                                <!--<span class="overlay-srch-title">Search:</span>-->
                            <span class="overlay-input">
                                <i class="la la-search"></i>
                                <input placeholder="Search ..." type="search" id="search_msg" class="form-control form-control-solid placeholder-no-fix search_msg">
                            </span>
                        </div>
                        <div class='chat_sys_scroll mcscroll2'>
                            <ul class="chat_sys_list">
                                @php 
                                $usersData = \Powerpanel\MessagingSystem\Models\MessagingSystem::getUserList();
                                $i = 0; 
                                foreach($usersData as $userdata){ 
                                if($userdata->id != '1'){
                                $imagedata = \App\User::GetUserImage($userdata->id);
                                $username = \App\User::GetUserName($userdata->id);
                                if (!empty($imagedata)) {
                                $logo_url = \App\Helpers\resize_image::resize($imagedata);

                                } else { 
                                $logo_url = url('/resources/image/packages/messagingsystem/man.png'); 
                                } 
                                $logindata = \App\LoginLog::getLoginHistryData($userdata->id);

                                $loggedinuser = 'N';
                                if (!empty($logindata)) { 
                                $loggedinuser = 'Y'; 
                                } 
                                $CountUnRedata= \Powerpanel\MessagingSystem\Models\MessagingSystem::GetCountNewMessageidData($userdata->id,auth()->user()->id);
                                $lastData= \Powerpanel\MessagingSystem\Models\MessagingSystem::GetlastDate($userdata->id, auth()->user()->id);
                                if(isset($lastData->created_at) && !empty($lastData->created_at)){
                                $lastseen=date('' . Config::get('Constant.DEFAULT_DATE_FORMAT') . '', strtotime($lastData->created_at));
                                $lastseen= \Powerpanel\MessagingSystem\Models\MessagingSystem::relative_date(strtotime($lastData->created_at)); 
                                }else{
                                $lastseen='';
                                } if(isset($lastData->varShortDescription) && !empty($lastData->varShortDescription)){
                                $lastmsg=$lastData->varShortDescription; 
                                }elseif(isset($lastData->fkIntImgId)){
                                $lastmsg= "<i class='fa fa-picture-o' aria-hidden='true'></i>"; 
                                }
                                elseif(isset($lastData->fkIntDocId))
                                { 
                                $lastmsg= "<i class='fa fa-paperclip' aria-hidden='true'></i>"; 
                                }elseif(isset($lastData->varQuote) && $lastData->varQuote=='Y' && $lastData->varShortDescription=='')
                                { 
                                $lastmsg= "<i class='fa fa-quote-left'></i> quoted message"; 
                                }else{ 
                                $lastmsg= ""; 
                                } 
                                if ($userdata->id != auth()->user()->id) { 
                                @endphp
                                <li data-userid='{{ $userdata->id }}'>
                                    @if($CountUnRedata !=0) 
                                    @php $unread="unread-messages"; 
                                    @endphp 
                                    @else 
                                    @php 
                                    $unread=""; 
                                    @endphp 
                                    @endif
                                    <a href="#kt-chat__{{ $i }}" class="kt-widget__item {{ $unread }}" data-toggle="pill">
                                        <span class="kt-userpic">
                                            <img src="{{ $logo_url }}" alt="image"> 
                                            @if($loggedinuser == 'Y')
                                            <span class="kt-badge-dot"></span>
                                            @endif
                                        </span>
                                        <div class="kt-widget__info">
                                            <div class="kt-widget__section">
                                                <span href="javascript:void(0)" class="kt-widget__username">{{ $username }}</span>
                                            </div>
                                            <span class="kt-widget__desc newmsgdetail_{{ $userdata->id }}">
                                                {!!$lastmsg!!}
                                            </span>
                                        </div>
                                        <div id="newMSG_{{ $userdata->id }}" class="counter_wrapper">
                                            @if($CountUnRedata !=0)
                                            <div id="msg-number">{{$CountUnRedata}}</div>
                                            @endif
                                            <div class="chat_time_detail chat_time_{{ $userdata->id }}">{{$lastseen}}</div>
                                        </div>
                                    </a>
                                </li>
                                @php $i++; } } } @endphp
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="col-lg-9 col-sm-8 col-xs-12 ac-nopadding">
                    <div class="portlet light portlet_vh welcomesection" style="overflow:inherit;">
                        <div class="kt-chat">
                            <div class="select-main-msg" style="display:none;">
                                <div class="top-select">
                                    <span id="countmsg"><span>1</span> Message Selected</span>
                                    <div class="select-close">
                                        <a href=javascript:> <i class="fa fa-times"></i></a>
                                    </div>
                                </div>
                            </div>
                            <div id="htmldata">
                                <div class="message_signup">
                                    @php 
                                    $imagedata = \App\User::GetUserImage(auth()->user()->id); $username = \App\User::GetUserName(auth()->user()->id); 
                                    if(!empty($imagedata)) { 
                                    $logo_url = \App\Helpers\resize_image::resize($imagedata); 
                                    } else { 
                                    $logo_url = url('/resources/image/packages/messagingsystem/man.png');
                                    } 
                                    $logindata = \App\LoginLog::getLoginHistryData($userdata->id); $loggedinuser = 'N'; 
                                    if (!empty($logindata)) {
                                    $loggedinuser = 'Y'; 
                                    } 
                                    @endphp
                                    <div class="message_signup_div">
                                        <h2>Welcome, {{ $username }} </h2>
                                        <div class="kt_sign_avtar">
                                            <div class="avtar_holder" style="background-image:url('{{ $logo_url }}')"></div>
                                            @if($loggedinuser == 'Y')
                                            <span class="kt-badge-dot"></span>
                                            @endif
                                        </div>
                                        <div class="kt-start-conv">
                                            <a href="javascript:void(0)" class="btn element-btn add-element" onClick="startChat({{auth()->user()->id}})" title="Start a conversation">
                                                Start a conversation
                                            </a>
                                            <p>You are sign in as
                                                <strong>{{\App\Helpers\MyLibrary::getDecryptedString(auth()->user()->email)}}</strong>
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="kt-portlet__foot" id="replayform" style="display:none">
                                <form id="MsgSystem" name="MsgSystem">
                                    <div class="row">
                                        <div class="col-md-12 col-sm-12">
                                            <div class="form_wrapper">
                                                <div class="form-group  form-md-line-input message_typer">
                                                    <div class="input_relative">
                                                        <textarea maxlength="400" onkeypress="FilterInput('event')" id="varShortDescription" name="varShortDescription" placeholder="Write a message."
                                                                  class="form-control"  rows="2" cols="50"></textarea>
                                                    </div>
                                                </div>
                                                <div class="image_thumb_wrapper">
                                                    <div class="image_thumb multi_upload_images multi_file_upload" id="fileuploaddiv">
                                                        <a class="document_manager multiple-selection" data-multiple="true" onclick=MediaManager.openDocumentManager("publications");>
                                                            <i class="fa fa-paperclip" aria-hidden="true"></i>
                                                        </a>
                                                        <input class="form-control" type="hidden" id="publications" name="doc_id" value="">
                                                    </div>
                                                    <div class="image_thumb multi_upload_images multi_file_upload fileinput fileinput-new" id="imageuploaddiv">
                                                        <a class="media_manager multiple-selection" data-multiple="true" onclick=MediaManager.open('publications_image');>
                                                            <i class="fa fa-picture-o" aria-hidden="true"></i>
                                                        </a>
                                                        <input class="form-control" type="hidden" id="publications_image" name="img_id" value="" />
                                                    </div>
                                                    <div class="kt_chat__actions text-right multi_upload_images">
                                                        <button type="button" id="btnSubmit" class="btn btn-green-drake msgbutton multiple-selection" title="Send">
                                                            <i class="fa fa-paper-plane-o" aria-hidden="true"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="" id="publications_documents" class="documents_section"></div>
                                    <div class="" id="publications_image_img" class="images_section"></div>
                                    <input type="hidden" id="toid" name="toid" value="">
                                    <input type="hidden" id="editId" name="editId" value="">
                                    <input type="hidden" id="formtype" name="formtype" value="add">
                                    <span class="help-block errorclass"></span>
                                </form>
                                <div class="msg-copy" style="display: none;">Copied to clipboard</div>
                            </div>
                            <div class="romove_button" style="display:none;">
                                <div class="remove_down"> 
                                    <a href=javascript: title="Remove" id="MulRemoveMsg">
                                        <i class="fa fa-trash-o" aria-hidden="true"></i>
                                        Remove
                                    </a>                                    
                                </div>
                                <div class="clearfix"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Massage System E -->
    </div>
</div>

<div class="new_modal modal fade login-user-popup" id="UserListData" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog" >
        <div class="modal-vertical">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                    <h3 class="modal-title">Select User / Admin</h3>
                </div>
                <div class="modal-body">
                    <ul class="login_user">
                        @php 
                        $usersData = \Powerpanel\MessagingSystem\Models\MessagingSystem::getUserList();
                        $i = 0; 
                        foreach($usersData as $userdata){ 
                        if($userdata->id != '1'){
                        $imagedata = \App\User::GetUserImage($userdata->id);
                        $username = \App\User::GetUserName($userdata->id);
                        $useremail = \App\User::GetUserEmail($userdata->id);
                        if (!empty($imagedata)) {
                        $logo_url = \App\Helpers\resize_image::resize($imagedata);

                        } else { 
                        $logo_url = url('/resources/image/packages/messagingsystem/man.png'); 
                        } 
                        $logindata = \App\LoginLog::getLoginHistryData($userdata->id);
                        $loggedinuser = 'N';
                        if (!empty($logindata)) {
                        $loggedinuser = 'Y';
                        }
                        if ($userdata->id != auth()->user()->id) {
                        @endphp
                        <li>
                            <a href="javascript:void(0)" class="pop-widget__item" data-toggle="pill" onclick="JumpIntoUser({{$userdata->id}})">
                                <span class="pop-userpic">
                                    <img src="{{ $logo_url }}" alt="{{ $username }}"> 
                                    @if($loggedinuser == 'Y')
                                    <span class="kt-badge-dot"></span>
                                    @endif
                                </span>
                                <div class="pop-widget__info">
                                    <div class="pop-widget__section">
                                        <span href="javascript:void(0)" class="pop-widget__username">{{ $username }}</span>
                                        <span class="pop-email-data">({{ \App\Helpers\MyLibrary::getDecryptedString($useremail) }})</span>
                                    </div>
                                </div>
                            </a>
                        </li>
                        @php } 
                        } 
                        }
                        @endphp
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="new_modal modal fade login-user-popup" id="ForwordUserListData" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog" >
        <div class="modal-vertical">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                    <a href="javascript:void(0)" class="fwd-done disabled">Done</a>
                    <h3 class="modal-title">Forward Message</h3>
                </div>
                <div class="forward-type-message">
                    <span class="message-title">
                        <i class="fa fa-quote-left"></i> 
                        Test
                    </span>
                    <span class="overlay-input">
                        <input placeholder="Type a message here (optional)" type="search" id="new_forword_search_msg" name="new_forword_search_msg" class="form-control form-control-solid placeholder-no-fix search_msg">
                    </span>
                </div>
                <div class="forward-search">
                        <!--<span class="overlay-srch-title">Search:</span>-->
                    <span class="overlay-input">
                        <i class="la la-search"></i>
                        <input placeholder="Search people and groups" type="search" id="forword_search_msg" class="form-control form-control-solid placeholder-no-fix forword_search_msg">
                    </span>
                </div>
                <div class="modal-body">
                    <form id="formforword" name="formforword">
                        <input type="hidden" name="forwordRecId" id="forwordRecId">
                        <input type="hidden" name="varforquatnew" id="varforquatnew">
                    </form>

                    <ul class="login_user">
                        @php 
                        $usersData = \Powerpanel\MessagingSystem\Models\MessagingSystem::getUserList();
                        $i = 0; 
                        foreach($usersData as $userdata){ 
                        if($userdata->id != '1'){
                        $imagedata = \App\User::GetUserImage($userdata->id);
                        $username = \App\User::GetUserName($userdata->id);
                        $useremail = \App\User::GetUserEmail($userdata->id);
                        if (!empty($imagedata)) {
                        $logo_url = \App\Helpers\resize_image::resize($imagedata);

                        } else { 
                        $logo_url = url('/resources/image/packages/messagingsystem/man.png'); 
                        } 
                        $logindata = \App\LoginLog::getLoginHistryData($userdata->id);
                        $loggedinuser = 'N';
                        if (!empty($logindata)) {
                        $loggedinuser = 'Y';
                        }
                        if ($userdata->id != auth()->user()->id) {
                        @endphp
                        <li>
                            <a href="javascript:void(0)" class="pop-widget__item" id="userid_{{$userdata->id}}" data-toggle="pill">
                                <span class="pop-userpic">
                                    <img src="{{ $logo_url }}" alt="{{ $username }}"> 
                                    @if($loggedinuser == 'Y')
                                    <span class="kt-badge-dot"></span>
                                    @endif
                                </span>
                                <div class="pop-widget__info">
                                    <div class="pop-widget__section">
                                        <span class="pop-widget__username">{{ $username }}</span>
                                        <span class="pop-email-data">({{ \App\Helpers\MyLibrary::getDecryptedString($useremail) }})</span>
                                    </div>
                                </div>
                                <span class="f-send" id="{{$userdata->id}}" title="Send"><i class="fa fa-check" aria-hidden="true"></i>Send</span>
                            </a>
                        </li>
                        @php } 
                        } 
                        }
                        @endphp
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="new_modal modal fade" id="Sing_Remove_Msg" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog" >
        <div class="modal-vertical">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                    <h3 class="modal-title">Remove Message</h3>
                </div>
                <div class="modal-body">
                    <form id="singlemsgremove" name="singlemsgremove">
                        <p>Are you sure you want to remove this message?</P>
                        <a id="msg_cancel" class="btn red btn-green-drake">Cancel</a>
                        <a id="msg_remove" class="btn btn-green-drake">Remove</a>
                        <input type="hidden" id="removemsgid" name="removemsgid" value="">
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!--<a id="MulRemoveMsg">Remove</a>-->
@if (File::exists(base_path() . '/resources/views/powerpanel/partials/deletePopup.blade.php') != null)
@include('powerpanel.partials.deletePopup')
@endif
@if (File::exists(base_path() . '/resources/views/powerpanel/partials/approveRecord.blade.php') != null)
@include('powerpanel.partials.approveRecord')
@endif
@if (File::exists(base_path() . '/resources/views/powerpanel/partials/cmsPageComments.blade.php') != null)
@include('powerpanel.partials.cmsPageComments',['module'=>Config::get('Constant.MODULE.TITLE')])
@endif
@endsection @section('scripts')
<script src="{{ $CDN_PATH.'resources/pages/scripts/packages/messagingsystem/inputEmoji.js' }}" type="text/javascript"></script>
<script src="{{ $CDN_PATH.'resources/pages/scripts/packages/messagingsystem/contextMenu.js' }}" type="text/javascript"></script>
<script src="{{ $CDN_PATH.'resources/pages/scripts/packages/messagingsystem/jquery.ui.position.min.js' }}" type="text/javascript"></script>
<script src="{{ $CDN_PATH.'resources/pages/scripts/packages/messagingsystem/messagingsystem.js' }}" type="text/javascript"></script>
<script>
    var dataid = '<?php echo auth()->user()->id; ?>';
    </script>
@endsection