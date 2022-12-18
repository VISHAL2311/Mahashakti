<?php

namespace Powerpanel\MessagingSystem\Controllers\Powerpanel;

use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Redirect;
use Powerpanel\Workflow\Models\Comments;
use Powerpanel\MessagingSystem\Models\MessagingSystem;
use Powerpanel\Workflow\Models\WorkflowLog;
use Powerpanel\Workflow\Models\Workflow;
use App\Log;
use App\User;
use Powerpanel\RoleManager\Models\Role;
use App\RecentUpdates;
use Powerpanel\MessagingSystem\Models\MessagingDeleted;
use App\Alias;
use Validator;
use Config;
use DB;
use App\Http\Controllers\PowerpanelController;
use Crypt;
use Auth;
use App\Helpers\MyLibrary;
use App\CommonModel;
use Carbon\Carbon;
use Cache;
use App\Modules;
use Powerpanel\RoleManager\Models\Role_user;
use App\UserNotification;

class MessagingSystemController extends PowerpanelController {

    /**
     * Create a new controller instance.
     * @return void
     */
    public $moduleHaveFields = [];

    public function __construct() {

        parent::__construct();
        if (isset($_COOKIE['locale'])) {
            app()->setLocale($_COOKIE['locale']);
        }
        $this->moduleHaveFields = ['chrMain'];
    }

    /**
     * This method handels load MessagingSystem grid
     * @return  View
     * @since   2017-07-20
     * @author  NetQuick
     */
    public function index() {
       $userIsAdmin = false;
        if (isset($this->currentUserRoleData) && !empty($this->currentUserRoleData)) {
            if ($this->currentUserRoleData->chrIsAdmin == 'Y') {
                $userIsAdmin = true;
            }
        }else{
            $userIsAdmin = true;
        }
        $total = MessagingSystem::getRecordCount();
        $roles = Role::getRecordListing('display_name', 'id');
        $NewRecordsCount = MessagingSystem::getNewRecordsCount();
        $this->breadcrumb['title'] = trans('messagingsystem::template.messagingsystemModule.managemessagingsystem');
        return view('messagingsystem::powerpanel.index', ['roles' => $roles, 'iTotalRecords' => $total, 'breadcrumb' => $this->breadcrumb, 'NewRecordsCount' => $NewRecordsCount, 'userIsAdmin' => $userIsAdmin]);
    }

    public function publish(Request $request) {

        $alias = (int) Input::get('alias');
        $update = MyLibrary::setPublishUnpublish($alias, $request);
        self::flushCache();
        echo json_encode($update);
        exit;
    }

    public function RemoveSingMsg(Request $request) {
        $RemoveId = Request::post('removemsgidvalue');
        $fromid = Request::post('fromid');
        $toid = Request::post('toid');
        $IdArray = explode(",", $RemoveId);
        MessagingSystem::destroy($IdArray);
        MessagingDeleted::DeletedRecordMsg($toid, $fromid);
        return $RemoveId;
    }

    public function ClearChat(Request $request) {
        $toid = Request::post('toid');
        $fromid = Request::post('fromid');
        MessagingSystem::where("FromID", '=', $fromid)
                ->where('ToID', '=', $toid)
                ->orWhere('FromID', '=', $toid)->where("ToID", '=', $fromid)->delete();
        MessagingDeleted::DeletedRecordMsg($toid, $fromid);
        return $toid;
    }

    public static function flushCache() {
        Cache::tags('messagingsystem')->flush();
    }

    public function InserMessageData(Request $request) {
        $postArr = Request::all();
      
        $messagingsystemArr = [];
        $updatemessagingsystemArr = [];
        $listuser = self::GetuserData();
        if ($postArr['formtype'] == 'edit') {
            $whereConditions = ['id' => $postArr['editId']];
            $updatemessagingsystemArr['varShortDescription'] = $postArr['varShortDescription'];
            $updatemessagingsystemArr['varEdit'] = 'Y';
            $updatemessagingsystemArr['varread'] = 'N';
            $updatemessagingsystemArr['fkIntImgId'] = !empty($postArr['img_id']) ? $postArr['img_id'] : null;
            $updatemessagingsystemArr['fkIntDocId'] = !empty($postArr['doc_id']) ? $postArr['doc_id'] : null;
            $update = CommonModel::updateRecords($whereConditions, $updatemessagingsystemArr,false, 'Powerpanel\MessagingSystem\Models\MessagingSystem');
            return $postArr['editId'] . '@@' . $postArr['varShortDescription'];
        } else {
            $messagingsystemArr['chrMain'] = 'Y';
            $messagingsystemArr['varTitle'] = '';
            $messagingsystemArr['varShortDescription'] = $postArr['varShortDescription'];
            $messagingsystemArr['fkIntDocId'] = !empty($postArr['doc_id']) ? $postArr['doc_id'] : null;
            $messagingsystemArr['fkIntImgId'] = !empty($postArr['img_id']) ? $postArr['img_id'] : null;

            $messagingsystemArr['chrPublish'] = isset($postArr['chrMenuDisplay']) ? $postArr['chrMenuDisplay'] : 'Y';
            $messagingsystemArr['FromID'] = auth()->user()->id;
            $fromusername = User::getRecordById(auth()->user()->id);
            $tousername = User::getRecordById($postArr['toid']);
            $messagingsystemArr['FromName'] = $fromusername->name;
            $messagingsystemArr['ToName'] = $tousername->name;
            $messagingsystemArr['FromEmail'] = MyLibrary::getDecryptedString(auth()->user()->email);
            $messagingsystemArr['ToID'] = $postArr['toid'];
            $user = User::getRecordById($postArr['toid']);
            $messagingsystemArr['ToEmail'] = MyLibrary::getDecryptedString($user->email);
            $messagingsystemArr['UserID'] = auth()->user()->id;
            $messagingsystemArr['created_at'] = Carbon::now();
            if ($postArr['formtype'] == 'quote') {
                $messagingsystemArr['varQuote'] = 'Y';
                $messagingsystemArr['varQuoteId'] = $postArr['editId'];
            }
            $messagingsystemID = CommonModel::addRecord($messagingsystemArr,'Powerpanel\MessagingSystem\Models\MessagingSystem');
            self::flushCache();

            return $postArr['toid'] . '@@' . $listuser;
        }
    }

    public function ForwordMsg(Request $request) {
        $toid = Request::post('toid');
        $fromid = Request::post('fromid');
        $recordid = Request::post('recordid');
        $newmsg = Request::post('newmsg');
        $varforquatnew = Request::post('varforquatnew');
        $forworddata = MessagingSystem::getRecordById($recordid, '');
//        echo $forworddata['varShortDescription'];
//        exit;
        if ($varforquatnew == 'Y' && $newmsg != '') {
            $messagingsystemArrNew = [];
            $messagingsystemArrNew['chrMain'] = 'Y';
            $messagingsystemArrNew['varTitle'] = '';
            $messagingsystemArrNew['varShortDescription'] = isset($newmsg) ? $newmsg : null;
            $messagingsystemArrNew['fkIntDocId'] = isset($forworddata['fkIntDocId']) ? $forworddata['fkIntDocId'] : null;
            $messagingsystemArrNew['fkIntImgId'] = !empty($forworddata['fkIntImgId']) ? $forworddata['fkIntImgId'] : null;

            $messagingsystemArrNew['chrPublish'] = isset($forworddata['chrPublish']) ? $forworddata['chrPublish'] : 'Y';
            $messagingsystemArrNew['FromID'] = auth()->user()->id;
            $fromusername = User::getRecordById(auth()->user()->id);
            $tousername = User::getRecordById($toid);
            $messagingsystemArrNew['FromName'] = $fromusername->name;
//        echo $tousername->name;exit;
            $messagingsystemArrNew['ToName'] = $tousername->name;
            $messagingsystemArrNew['FromEmail'] = MyLibrary::getDecryptedString(auth()->user()->email);
            $messagingsystemArrNew['ToID'] = $toid;
            $user = User::getRecordById($toid);
            $messagingsystemArrNew['ToEmail'] = MyLibrary::getDecryptedString($user->email);
            $messagingsystemArrNew['UserID'] = auth()->user()->id;
            $messagingsystemArrNew['created_at'] = Carbon::now();
            $messagingsystemArrNew['varQuoteId'] = $recordid;

            $messagingsystemID = CommonModel::addRecord($messagingsystemArrNew,'Powerpanel\MessagingSystem\Models\MessagingSystem');
        }
//         *******Insert Forword**********
        $messagingsystemArr = [];
        $messagingsystemArr['chrMain'] = 'Y';
        $messagingsystemArr['varTitle'] = '';
        if ($varforquatnew == 'Y' && $newmsg != '') {
            $messagingsystemArr['varShortDescription'] = null;
        } else {
            $messagingsystemArr['varShortDescription'] = isset($newmsg) ? $newmsg : null;
        }
        $messagingsystemArr['fkIntDocId'] = isset($forworddata['fkIntDocId']) ? $forworddata['fkIntDocId'] : null;
        $messagingsystemArr['fkIntImgId'] = !empty($forworddata['fkIntImgId']) ? $forworddata['fkIntImgId'] : null;

        $messagingsystemArr['chrPublish'] = isset($forworddata['chrPublish']) ? $forworddata['chrPublish'] : 'Y';
        $messagingsystemArr['FromID'] = auth()->user()->id;
        $fromusername = User::getRecordById(auth()->user()->id);
        $tousername = User::getRecordById($toid);
        $messagingsystemArr['FromName'] = $fromusername->name;
//        echo $tousername->name;exit;
        $messagingsystemArr['ToName'] = $tousername->name;
        $messagingsystemArr['FromEmail'] = MyLibrary::getDecryptedString(auth()->user()->email);
        $messagingsystemArr['ToID'] = $toid;
        $user = User::getRecordById($toid);
        $messagingsystemArr['ToEmail'] = MyLibrary::getDecryptedString($user->email);
        $messagingsystemArr['UserID'] = auth()->user()->id;
        $messagingsystemArr['created_at'] = Carbon::now();

        $messagingsystemArr['varQuote'] = 'Y';
        $messagingsystemArr['varQuoteId'] = $recordid;

        $messagingsystemID = CommonModel::addRecord($messagingsystemArr,'Powerpanel\MessagingSystem\Models\MessagingSystem');

        echo "<pre/>";
        print_r('sucess');
        exit;
    }

    public function GetNewMessage(Request $request) {
        $toid = Request::post('toid');
        $fromid = Request::post('fromid');
        $CountUnRedata = MessagingSystem::GetCountNewMessageidData($toid, $fromid);
        $GetUnReadData = MessagingSystem::GetNewMessageidData($toid, $fromid);

        if ($CountUnRedata > 0) {
            foreach ($GetUnReadData as $UnRedata) {
                $updatemessagingsystemFields['varread'] = 'Y';
                $whereConditions = ['id' => $UnRedata->id];
                $update = CommonModel::updateRecords($whereConditions, $updatemessagingsystemFields,false,'Powerpanel\MessagingSystem\Models\MessagingSystem');
                self::flushCache();
            }
            return $CountUnRedata;
        } else {
            return $CountUnRedata;
        }
    }

    public function GetNewMessageCounter(Request $request) {

        $toidArray = Request::post('toid');
        $activeuserid = Request::post('activeuserid');
        $CountUnRedata = '';
        $toidplus = '';
        $lastmsg = '';
        $fromid = Request::post('fromid');
        foreach ($toidArray as $toid) {
            $CountUnRedataorg = MessagingSystem::GetCountNewMessageidData($toid, $fromid);
            if ($CountUnRedataorg > 0) {
                $lastData = MessagingSystem::GetlastDate($toid, auth()->user()->id);
                if (isset($lastData->varShortDescription) && !empty($lastData->varShortDescription)) {
                    $lastmsg = $lastData->varShortDescription;
                } elseif (isset($lastData->fkIntImgId)) {
                    $lastmsg = "<i class='fa fa-picture-o' aria-hidden='true'></i>";
                } elseif (isset($lastData->fkIntDocId)) {
                    $lastmsg = "<i class='fa fa-paperclip' aria-hidden='true'></i>";
                } else {
                    $lastmsg = "";
                }
            }
            $CountUnRedata .= $CountUnRedataorg . '@@';
            $toidplus .= $toid . '-';
        }
        $DeletedMsg = '';
        if ($activeuserid != '') {
            $DeletedMsg = MessagingDeleted::GetCountDeteled($activeuserid, $fromid);
        }
        $userlist = self::GetuserData();
        return $CountUnRedata . '!!' . $lastmsg . '!!' . $userlist . '!!' . $DeletedMsg;
    }

    public function GetuserData() {
        $usersData = MessagingSystem::getUserList();
        $i = 0;
        $listhtml = '';
        foreach ($usersData as $userdata) {
            if($userdata->id != 1){
            $imagedata = User::GetUserImage($userdata->id);
            $username = User::GetUserName($userdata->id);
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
            $CountUnRedata = MessagingSystem::GetCountNewMessageidData($userdata->id, auth()->user()->id);
            $lastData = MessagingSystem::GetlastDate($userdata->id, auth()->user()->id);
            if (isset($lastData->created_at) && !empty($lastData->created_at)) {
                $lastseen = date('' . Config::get('Constant.DEFAULT_DATE_FORMAT') . '', strtotime($lastData->created_at));
                $lastseen = MessagingSystem::relative_date(strtotime($lastData->created_at));
            } else {
                $lastseen = '';
            }
            if (isset($lastData->varShortDescription) && !empty($lastData->varShortDescription)) {
                $lastmsg = $lastData->varShortDescription;
            } elseif (isset($lastData->fkIntImgId)) {
                $lastmsg = "<i class='fa fa-picture-o' aria-hidden='true'></i>";
            } elseif (isset($lastData->fkIntDocId)) {
                $lastmsg = "<i class='fa fa-paperclip' aria-hidden='true'></i>";
            } elseif (isset($lastData->varQuote) && $lastData->varQuote == 'Y' && $lastData->varShortDescription == '') {
                $lastmsg = "<i class='fa fa-quote-left'></i> quoted message";
            } else {
                $lastmsg = "";
            }
            if ($userdata->id != auth()->user()->id) {
                $listhtml .= "<li data-userid='$userdata->id'>";
                if ($CountUnRedata != 0) {
                    $unread = "unread-messages";
                } else {
                    $unread = "";
                }
                $listhtml .= '<a href="#kt-chat__' . $i . '" class="kt-widget__item ' . $unread . '" data-toggle="pill">';
                $listhtml .= '<span class="kt-userpic">';
                $listhtml .= '<img src="' . $logo_url . '" alt="image">';
                if ($loggedinuser == 'Y') {
                    $listhtml .= '<span class="kt-badge-dot"></span>';
                }
                $listhtml .= '</span>';
                $listhtml .= '<div class="kt-widget__info">';
                $listhtml .= '<div class="kt-widget__section"><span href="javascript:;" class="kt-widget__username">' . $username . '</span></div>';
                $listhtml .= '<span class="kt-widget__desc newmsgdetail_' . $userdata->id . '">' . $lastmsg . '</span>';
                $listhtml .= ' </div>';
                $listhtml .= '<div id="newMSG_' . $userdata->id . '" class="counter_wrapper">';
                if ($CountUnRedata != 0) {
                    $listhtml .= '<div id="msg-number">' . $CountUnRedata . '</div>';
                }
                $listhtml .= ' <div class="chat_time_detail chat_time_' . $userdata->id . '">' . $lastseen . '</div>';
                $listhtml .= ' </div>   </a>      </li>  ';
                $i++;
            }
            }
        }
        return $listhtml;
    }

    public function GetRecentid(Request $request) {
        $fromid = Request::post('fromid');
        $RecentData = MessagingSystem::GetRecentid($fromid);
        if (isset($RecentData->ToID) && !empty($RecentData->ToID)) {
            $RecentData = $RecentData->ToID;
        } else {
            $RecentData = '0';
        }
        return $RecentData;
    }

    public function GetMessageidData(Request $request) {
        $toid = Request::post('toid');
        $fromid = Request::post('fromid');
        $data = MessagingSystem::GetMessageidData($toid, $fromid);
        $username = "Unknown";
        if (!empty($data)) {
            $username = User::GetUserName($toid);
            $useremail = User::GetUserEmail($toid);
            $username1 = $username;
        }
        $logindata = \App\LoginLog::getLoginHistryData($toid);
        $loggedinuser = 'N';
        if (!empty($logindata)) {
            $loggedinuser = 'Y';
        }
        $html = '';
        $imagedata = User::GetUserImage($toid);
        if (!empty($imagedata)) {
            $logo_url = \App\Helpers\resize_image::resize($imagedata);
        } else {
            $logo_url = url('/resources/image/packages/messagingsystem/man.png');
        }
        $html .= '<div class="kt-chat__head">
        				<div class="kt-chat__lf">
                 <span class="kt-userpic message-img">
                    <img src="' . $logo_url . '"  alt="image">
                 </span>
                 <a href="javascript:;" class="kt-chat__title">' . $username1 . ' <span class="email-data">(' . MyLibrary::getDecryptedString($useremail) . ')</span><span class="kt-chat__status">';
        if ($loggedinuser == 'Y') {
            $html .= '<span class="kt-badge-dot"></span> Active';
        }
        $html .= '</span></a>';
        $html .= '</div>';
//        <a href="javascript:;"  class="msg_head_chat_icon" aria-expanded="true"><i class="la la-archive"></i> Archive</a>
        $html .= '';
        $html .= '</div>';
        if (count($data) > 0) {
            $j = 0;
            $html .= '<div class="kt-portlet__scroll mcscroll"><div class="kt-portlet__body tab-content">';
            foreach ($data as $userdata) {
                if($userdata->id != 1){
                if ($userdata->FromID == auth()->user()->id) {
                    $position = "class='kt-chat__message kt-chat__message--right'";
                    $removeposition = "Y";
                    $colorclass = 'kt-chat__text kt-bg-light-brand';
                    $colorclassforpan = 'pencil_right';
                    $right = "style='display:none'";
                } else {
                    $removeposition = "N";
                    $colorclassforpan = 'pencil_left';
                    $position = "class='kt-chat__message'";
                    $colorclass = 'kt-chat__text kt-bg-light-success';
                    $right = '';
                }
                if ($userdata->FromID == auth()->user()->id || $userdata->ToID == auth()->user()->id) {
                    $class = 'in active';
                } else {
                    $class = '';
                }
                $imagedata = User::GetUserImage($userdata->FromID);
                $username = User::GetUserName($userdata->FromID);
                if (!empty($imagedata)) {
                    $logo_url = \App\Helpers\resize_image::resize($imagedata);
                } else {
                    $logo_url = url('/resources/image/packages/messagingsystem/man.png');
                }
                $docsAray = explode(',', $userdata->fkIntDocId);
                $docObj = \App\Helpers\DocumentHelper::getDocsByIds($docsAray);

                $imageAray = explode(',', $userdata->fkIntImgId);
                $imagObj = array();
                foreach ($imageAray as $imagesId) {
                    if (isset($imagesId) && !empty($imagesId)) {
                        $imagObj[] = \App\Helpers\resize_image::resize($imagesId);
                    }
                }
                $html .= '<div class="tab-pane fade ' . $class . ' kt_remove_msg_' . $userdata->id . '" id="kt-chat__' . $j . '">
                                       <div class="kt-chat__messages">';
                if ($removeposition == 'Y') {
                    $onclick = "opencontextMenuRight(event);";
                } else {
                    $onclick = "opencontextMenuLeft(event);";
                }
                $html .= '<div class="message-option" id="triple_message_option" onclick="' . $onclick . '"><span></span><span></span><span></span></div>';
                $html .= ' <div ' . $position . '>';
                if ($removeposition == 'Y') {
                    $html .= '<div class="sel-message" style="display:none">
                    <label class="message-check">
                    <input type="checkbox" name="delete[]" class="chkDelete" onclick="checkckeckbox()" value="' . $userdata->id . '">
                        <span class="checkmark"></span>
                    </label>
                </div>';
                }
                $html .= '<div class="kt-chat__user">
                                                    <span class="kt-userpic kt-userpic--circle kt-userpic--sm" ' . $right . '>
                                                        <img src="' . $logo_url . '" alt="image">
                                                    </span>
                                                </div>
                                                <div class="kt-chat__text_wrap ' . $colorclassforpan . '">
                                                <a href="javascript:;" class="kt-chat__username" ' . $right . '>' . $userdata->FromName . '</a>';
                if ($userdata->varEdit == 'Y') {
                    if ($userdata->fkIntImgId != '') {
                        $html .= '<i class="fa fa-pencil" style="" title="This image has been edited."></i>';
                    } else if ($userdata->fkIntDocId != '') {
                        $html .= '<i class="fa fa-pencil" style="" title="This file has been edited."></i>';
                    } else {
                        $html .= '<i class="fa fa-pencil" style="" title="This message has been edited."></i>';
                    }
                }
                $html .= '<span class="kt-chat__datetime">' . date('' . Config::get('Constant.DEFAULT_TIME_FORMAT') . '', strtotime($userdata->created_at)) . '</span>';
                if ($userdata->varQuote == 'Y' && $userdata->varShortDescription == '') {
                    $dataid = "data-id='$userdata->varQuoteId'";
                } else {
                    $dataid = "";
                }
                $dataidimage = '';
                if ($userdata->fkIntImgId != '') {
                    $dataidimage = "data-image='$userdata->fkIntImgId'";
                }
                $dataidfile = '';
                if ($userdata->fkIntDocId != '') {
                    $dataidfile = "data-file='$userdata->fkIntDocId'";
                }

                if ($userdata->FromID == auth()->user()->id) {
                    $html .= '<span ' . $dataid . ' ' . $dataidimage . ' '.$dataidfile.' class="context-menu-admin"  id="' . $userdata->id . '">';
                } else {
                    $html .= '<span ' . $dataid . ' ' . $dataidimage . ' class="context-menu-user"  id="' . $userdata->id . '">';
                }

                $html .= '<div style="display:none;" id="html_chat_' . $userdata->id . '">' . nl2br($userdata->varShortDescription) . '</div>';
                $html .= '<div class="' . $colorclass . '" id="html_chat_dis_' . $userdata->id . '">';
                $varQuotecls = '';


                if ($userdata->varQuote == 'Y') {
                    $quoteText = MessagingSystem::getRecordById($userdata->varQuoteId);
                    if (isset($quoteText->FromID)) {
                        $html .= '<i class="fa fa-quote-left"></i>';
                        $userdatabyid = User::getRecordById($quoteText->FromID);
                        $lastseen = MessagingSystem::relative_date(strtotime($quoteText->created_at));
                        $htmlmage = '';
                        $filemage = '';
                        if ($quoteText->fkIntDocId != '') {
                            $htmlmage .= '<br/>';
                            $docsArayQ = explode(',', $quoteText->fkIntDocId);
                            $docObjQ = \App\Helpers\DocumentHelper::getDocsByIds($docsArayQ);
                            $htmlmage .= self::getimagefile($docObjQ);
                        }
                        if ($quoteText->fkIntImgId != '') {
                            $imageArayQ = explode(',', $quoteText->fkIntImgId);
                            $imagObjQ = array();
                            foreach ($imageArayQ as $imagesIdQ) {
                                if (isset($imagesIdQ) && !empty($imagesIdQ)) {
                                    $imagObjQ[] = \App\Helpers\resize_image::resize($imagesIdQ);
                                }
                            }
                            $htmlmage .= '<br/>';
                            foreach ($imagObjQ as $imagdataQ) {
                                if (isset($imagdataQ) && !empty($imagdataQ)) {
                                    $htmlmage .= '<a  href="' . $imagdata . '" title="Download" class="image_download" download><img  src="' . $imagdataQ . '" style="width: 50px;height: 50px;padding: 4px;"></a>';
                                }
                            }
                        }
                        $html .= '<span class="quote-top-msg">' . nl2br($quoteText->varShortDescription) . $htmlmage . '</span><div class="my-quote">' . $userdatabyid['name'] . ', ' . $lastseen . '</div>';
                        if (isset($userdata->varShortDescription) && !empty($userdata->varShortDescription)) {
                            $html .= '<span class="quote-bottom-msg">' . nl2br($userdata->varShortDescription) . '</span>';
                        }
                    } else {
                        $html .= nl2br($userdata->varShortDescription);
                    }
                } else {
                    $html .= nl2br($userdata->varShortDescription);
                }

                $html .= '<div class="btn_group">';
                foreach ($docObj as $docObj) {
                    if (!empty($docObj) && isset($docObj->varDocumentExtension)) {
                        $PDF_Path = url('documents/' . $docObj->txtSrcDocumentName . '.' . $docObj->varDocumentExtension);
                        $doclink = url('documents/' . $docObj->txtSrcDocumentName . '.' . $docObj->varDocumentExtension);
                        if ($docObj->varDocumentExtension == 'pdf' || $docObj->varDocumentExtension == 'PDF') {
                            $blank = 'target="_blank"';
                            $title = $docObj->txtSrcDocumentName;
                            $anchorLinkHitType = "view";
                            $icon = "pdf_icon.png";
                        } elseif ($docObj->varDocumentExtension == 'txt' || $docObj->varDocumentExtension == 'TXT') {
                            $blank = '';
                            $title = 'Download (' . $docObj->txtSrcDocumentName . ')';
                            $anchorLinkHitType = "download";
                            $icon = "txt_icon.png";
                        } elseif ($docObj->varDocumentExtension == 'doc' || $docObj->varDocumentExtension == 'DOC') {
                            $blank = '';
                            $title = 'Download (' . $docObj->txtSrcDocumentName . ')';
                            $anchorLinkHitType = "download";
                            $icon = "doc_icon.png";
                        } elseif ($docObj->varDocumentExtension == 'ppt' || $docObj->varDocumentExtension == 'PPT') {
                            $blank = '';
                            $title = 'Download (' . $docObj->txtSrcDocumentName . ')';
                            $anchorLinkHitType = "download";
                            $icon = "ppt_icon.png";
                        } elseif ($docObj->varDocumentExtension == 'xls' || $docObj->varDocumentExtension == 'XLS' || $docObj->varDocumentExtension == 'xlsx' || $docObj->varDocumentExtension == 'XLSX' || $docObj->varDocumentExtension == 'xlsm' || $docObj->varDocumentExtension == 'XLSM') {
                            $blank = '';
                            $title = 'Download (' . $docObj->txtSrcDocumentName . ')';
                            $anchorLinkHitType = "download";
                            $icon = "xls_icon.png";
                        } else {
                            $blank = '';
                            $anchorLinkHitType = "download";
                            $anchorLinkIsdownload = "download";
                            $title = 'Download (' . $docObj->txtSrcDocumentName . ')';
                            $icon = "document_icon.png";
                        }
                    }
                    $html .= '<a ' . $blank . ' href="' . url($PDF_Path) . '" data-viewid="' . $docObj->id . '" data-viewtype="' . $anchorLinkHitType . '" title="' . $title . '" class="lnk_view docHitClick" ' . $anchorLinkHitType . '><img  src="' . url('assets/images/documents_logo/' . $icon . '') . '"></a>';
                }
                foreach ($imagObj as $imagdata) {
                    if (isset($imagdata) && !empty($imagdata)) {
                        $html .= '<a  href="' . $imagdata . '" title="Download" class="image_download" download><img  src="' . $imagdata . '"></a>';
                    }
                }
                $html .= '</div>';
                $html .= '</div>';

                $html .= '</div></div></div></div>';
                $j++;
            }
        }
            $html .= '</div></div>';
        } else {
            $html .= '<div class="kt-portlet__scroll kt-chat_start_conv text-center"><h3>Start a Conversation</h3></div>';
        }
        $CountUnRedata = MessagingSystem::GetCountNewMessageidData($toid, $fromid);
        $GetUnReadData = MessagingSystem::GetNewMessageidData($toid, $fromid);
        if ($CountUnRedata > 0) {
            foreach ($GetUnReadData as $UnRedata) {
                $updatemessagingsystemFields['varread'] = 'Y';
                $whereConditions = ['id' => $UnRedata->id];
                $update = CommonModel::updateRecords($whereConditions, $updatemessagingsystemFields,false,'Powerpanel\MessagingSystem\Models\MessagingSystem');
                self::flushCache();
            }
        }
        MessagingDeleted::where("FromID", '=', $toid)->where('ToID', '=', $fromid)->delete();
        echo $html;
        exit;
    }

    public function getimagefile($docObj) {
        $html = '';
        foreach ($docObj as $docObj) {
            if (!empty($docObj) && isset($docObj->varDocumentExtension)) {
                $PDF_Path = url('documents/' . $docObj->txtSrcDocumentName . '.' . $docObj->varDocumentExtension);
                $doclink = url('documents/' . $docObj->txtSrcDocumentName . '.' . $docObj->varDocumentExtension);
                if ($docObj->varDocumentExtension == 'pdf' || $docObj->varDocumentExtension == 'PDF') {
                    $blank = 'target="_blank"';
                    $title = $docObj->txtSrcDocumentName;
                    $anchorLinkHitType = "view";
                    $icon = "pdf_icon.png";
                } elseif ($docObj->varDocumentExtension == 'txt' || $docObj->varDocumentExtension == 'TXT') {
                    $blank = '';
                    $title = 'Download (' . $docObj->txtSrcDocumentName . ')';
                    $anchorLinkHitType = "download";
                    $icon = "txt_icon.png";
                } elseif ($docObj->varDocumentExtension == 'doc' || $docObj->varDocumentExtension == 'DOC') {
                    $blank = '';
                    $title = 'Download (' . $docObj->txtSrcDocumentName . ')';
                    $anchorLinkHitType = "download";
                    $icon = "doc_icon.png";
                } elseif ($docObj->varDocumentExtension == 'ppt' || $docObj->varDocumentExtension == 'PPT') {
                    $blank = '';
                    $title = 'Download (' . $docObj->txtSrcDocumentName . ')';
                    $anchorLinkHitType = "download";
                    $icon = "ppt_icon.png";
                } elseif ($docObj->varDocumentExtension == 'xls' || $docObj->varDocumentExtension == 'XLS' || $docObj->varDocumentExtension == 'xlsx' || $docObj->varDocumentExtension == 'XLSX' || $docObj->varDocumentExtension == 'xlsm' || $docObj->varDocumentExtension == 'XLSM') {
                    $blank = '';
                    $title = 'Download (' . $docObj->txtSrcDocumentName . ')';
                    $anchorLinkHitType = "download";
                    $icon = "xls_icon.png";
                } else {
                    $blank = '';
                    $anchorLinkHitType = "download";
                    $anchorLinkIsdownload = "download";
                    $title = 'Download (' . $docObj->txtSrcDocumentName . ')';
                    $icon = "document_icon.png";
                }
            }
            $html .= '<a ' . $blank . ' href="' . url($PDF_Path) . '" data-viewid="' . $docObj->id . '" data-viewtype="' . $anchorLinkHitType . '" title="' . $title . '" class="lnk_view docHitClick" style="width: 32px;margin: 5px 3px;display: inline-block;vertical-align: middle;" ' . $anchorLinkHitType . '><img  src="' . url('assets/images/documents_logo/' . $icon . '') . '"></a>';
        }
        return $html;
    }

}
