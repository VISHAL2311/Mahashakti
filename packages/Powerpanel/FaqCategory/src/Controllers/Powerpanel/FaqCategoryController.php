<?php

namespace Powerpanel\FaqCategory\Controllers\Powerpanel;

use App\Http\Controllers\PowerpanelController;
use Illuminate\Contracts\Auth\Guard;
use Carbon\Carbon;
use Powerpanel\FaqCategory\Models\FaqCategory;
use App\Modules;
use App\Alias;
use App\User;
use Request;
use Illuminate\Support\Facades\Redirect;
use Validator;
use Powerpanel\Workflow\Models\Comments;
use DB;
use File;
use Powerpanel\Workflow\Models\WorkflowLog;
use Powerpanel\Workflow\Models\Workflow;
use App\Log;
use App\RecentUpdates;
use App\CommonModel;
use App\Helpers\MyLibrary;
use Auth;
use Config;
use Cache;
use App\Helpers\Category_builder;
use App\Helpers\CategoryArrayBuilder;
use App\Pagehit;
use Powerpanel\RoleManager\Models\Role_user;
use Powerpanel\CmsPage\Models\CmsPage;
use App\Helpers\ParentRecordHierarchy_builder;
use Powerpanel\Faq\Models\Faq;
use App\UserNotification;
use App\Helpers\PageHitsReport;

class FaqCategoryController extends PowerpanelController {

    public $catModule;

    public function __construct() {
        parent::__construct();
        if (isset($_COOKIE['locale'])) {
            app()->setLocale($_COOKIE['locale']);
        }
        $this->MyLibrary = new MyLibrary();
        $this->CommonModel = new CommonModel();
        $this->Alias = new Alias();
    }

    /**
     * This method handels load process of faqcategory
     * @return  View
     * @since   2017-11-10
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
        $iTotalRecords = FaqCategory::getRecordCount();
        $draftTotalRecords = FaqCategory::getRecordCountforListDarft(false, true, $userIsAdmin, array());
        $trashTotalRecords = FaqCategory::getRecordCountforListTrash();
        $favoriteTotalRecords = FaqCategory::getRecordCountforListFavorite();
        $NewRecordsCount = FaqCategory::getNewRecordsCount();
        $this->breadcrumb['title'] = trans('faq-category::template.faqCategoryModule.manageFaqCategory');
        if (method_exists($this->CommonModel, 'GridColumnData')) {
            $settingdata = CommonModel::GridColumnData(Config::get('Constant.MODULE.ID'));
            $settingarray = array();
            foreach ($settingdata as $sdata) {
                $settingarray[$sdata->chrtab][] = $sdata->columnid;
            }
        } else {
            $settingarray = '';
        }
        return view('faq-category::powerpanel.index', ['userIsAdmin' => $userIsAdmin, 'iTotalRecords' => $iTotalRecords, 'breadcrumb' => $this->breadcrumb, 'NewRecordsCount' => $NewRecordsCount, 'draftTotalRecords' => $draftTotalRecords, 'trashTotalRecords' => $trashTotalRecords, 'favoriteTotalRecords' => $favoriteTotalRecords, 'settingarray' => json_encode($settingarray)]);
    }

    /**
     * This method loads faqcategory edit view
     * @param   Alias of record
     * @return  View
     * @since   2017-11-10
     * @author  NetQuick
     */
    public function edit($id = false) {
        $hasRecords = 0;
        $documentManager = true;
        $userIsAdmin = false;
        $imageManager = false;
        $videoManager = false;
        if (isset($this->currentUserRoleData) && !empty($this->currentUserRoleData)) {
            if ($this->currentUserRoleData->chrIsAdmin == 'Y') {
                $userIsAdmin = true;
            }
        }else{
            $userIsAdmin = true;
        }
        if (!is_numeric($id)) {
            $this->breadcrumb['title'] = trans('faq-category::template.faqCategoryModule.addFaqCategory');
            $this->breadcrumb['module'] = trans('faq-category::template.faqCategoryModule.manageFaqCategory');
            $this->breadcrumb['url'] = 'powerpanel/faq-category';
            $this->breadcrumb['inner_title'] = trans('faq-category::template.faqCategoryModule.addFaqCategory');
            $breadcrumb = $this->breadcrumb;
            $data = compact('documentManager', 'breadcrumb', 'imageManager', 'videoManager', 'userIsAdmin', 'hasRecords');
        } else {
            $documentManager = true;
            $faqCategory = FaqCategory::getRecordById($id);
            if (empty($faqCategory)) {
                return redirect()->route('powerpanel.faq-category.add');
            }
            if ($faqCategory->fkMainRecord != '0') {
                $faqCategory_highLight = FaqCategory::getRecordById($faqCategory->fkMainRecord);
                $metaInfo_highLight['varMetaTitle'] = $faqCategory_highLight['varMetaTitle'];
                $metaInfo_highLight['varMetaDescription'] = $faqCategory_highLight['varMetaDescription'];
                $display_publish = $faqCategory_highLight['chrPublish'];
                $hasRecords = Faq::getCountById($faqCategory->fkMainRecord);
            } else {
                $faqCategory_highLight = "";
                $metaInfo_highLight['varMetaTitle'] = "";
                $metaInfo_highLight['varMetaDescription'] = "";
                $display_publish = '';
                $hasRecords = Faq::getCountById($faqCategory->id);
            }
            $metaInfo = array(
                'varMetaTitle' => $faqCategory->varMetaTitle,
                'varMetaDescription' => $faqCategory->varMetaDescription
            );
            if (method_exists($this->MyLibrary, 'getModulePageAliasByModuleName')) {
                $categorypagereocrdlink = MyLibrary::getModulePageAliasByModuleName('faq-category');
            }
            if (!empty($categorypagereocrdlink)) {
                $varURL = $categorypagereocrdlink . '/' . $faqCategory->alias->varAlias;
            } else {
                $varURL = $faqCategory->alias->varAlias;
            }
            $metaInfo['varURL'] = $varURL;
            $this->breadcrumb['title'] = trans('faq-category::template.faqCategoryModule.editFaqCategory') . ' - ' . $faqCategory->varTitle;
            $this->breadcrumb['module'] = trans('faq-category::template.faqCategoryModule.manageFaqCategory');
            $this->breadcrumb['url'] = 'powerpanel/faq-category';
            $this->breadcrumb['inner_title'] = trans('faq-category::template.faqCategoryModule.editFaqCategory') . ' - ' . $faqCategory->varTitle;
            $breadcrumb = $this->breadcrumb;
            $data = compact('faqCategory', 'documentManager', 'metaInfo', 'breadcrumb', 'imageManager', 'videoManager', 'faqCategory_highLight', 'metaInfo_highLight', 'display_publish', 'userIsAdmin', 'hasRecords');
        }
        //Start Button Name Change For User Side
        if (isset($this->currentUserRoleData->chrIsAdmin) && $this->currentUserRoleData->chrIsAdmin != 'Y') {
            $module = Modules::getModuleById(Config::get('Constant.MODULE.ID'));
            if (!$userIsAdmin) {
                $userRole = $this->currentUserRoleData->id;
            } else {
                $userRoleData = Role_user::getUserRoleByUserId(auth()->user()->id);
                $userRole = $userRoleData->role_id;
            }
            $workFlowByCat = Workflow::getRecordByCategoryId($module->intFkGroupCode, $userRole, Config::get('Constant.MODULE.ID'));
            if (!empty($workFlowByCat)) {
                $data['chrNeedAddPermission'] = $workFlowByCat->chrNeedAddPermission;
                $data['charNeedApproval'] = $workFlowByCat->charNeedApproval;
            } else {
                $data['chrNeedAddPermission'] = 'N';
                $data['charNeedApproval'] = 'N';
            }
        }else {
            $data['chrNeedAddPermission'] = 'N';
            $data['charNeedApproval'] = 'N';
        }
        //End Button Name Change For User Side
        return view('faq-category::powerpanel.actions', $data);
    }

    /**
     * This method stores faqcategory modifications
     * @return  View
     * @since   2017-11-10
     * @author  NetQuick
     */
    public function handlePost(Request $request) {
        $requestArr = Request::all();
        $request = (object) $requestArr;
        $approval = false;
        $userIsAdmin = false;
        if (!empty($this->currentUserRoleData)) {
            if ($this->currentUserRoleData->chrIsAdmin == 'Y') {
                $userIsAdmin = true;
            }
        }
        $data = Request::all();
        $rules = array(
            'title' => 'required|max:160|handle_xss|no_url',
            'varMetaTitle' => 'required|max:500|handle_xss|no_url',
            'varMetaDescription' => 'required|max:500|handle_xss|no_url',
            'alias' => 'required',
            'chrMenuDisplay' => 'required',
            'order' => 'required|greater_than_zero|handle_xss|no_url',
        );
        $actionMessage = trans('faq-category::template.common.oppsSomethingWrong');
        $messsages = array(
            'title.required' => 'Name field is required.',
            'varMetaTitle.required' => trans('faq-category::template.faqCategoryModule.metaTitle'),
            'varMetaDescription.required' => trans('faq-category::template.faqCategoryModule.metaDescription'),
            'order.required' => trans('faq-category::template.faqCategoryModule.displayOrder'),
            'order.greater_than_zero' => trans('faq-category::template.faqCategoryModule.displayGreaterThan')
        );
        $validator = Validator::make($data, $rules, $messsages);
        if ($validator->passes()) {
            $module = Modules::getModuleById(Config::get('Constant.MODULE.ID'));
            $faqCategoryArr = [];
            if (Config::get('Constant.DEFAULT_VISUAL') == 'Y') {
                if ($data['section'] != '[]') {
                    $vsection = $data['section'];
                } else {
                    $vsection = '';
                }
            } else {
                $vsection = $data['description'];
            }
            $faqCategoryArr['varTitle'] = stripslashes(trim($data['title']));
            $faqCategoryArr['dtDateTime'] = !empty($data['start_date_time']) ? date('Y-m-d H:i:s', strtotime($data['start_date_time'])) : date('Y-m-d H:i:s');
            $faqCategoryArr['dtEndDateTime'] = !empty($data['end_date_time']) ? date('Y-m-d H:i:s', strtotime($data['end_date_time'])) : null;
           
            $faqCategoryArr['txtDescription'] = $vsection;
            $faqCategoryArr['varMetaTitle'] = stripslashes(trim($data['varMetaTitle']));
            $faqCategoryArr['varMetaDescription'] = stripslashes(trim($data['varMetaDescription']));
            $faqCategoryArr['chrPublish'] = isset($data['chrMenuDisplay']) ? $data['chrMenuDisplay'] : 'Y';
            if(Config::get('Constant.CHRSearchRank') == 'Y'){
            $faqCategoryArr['intSearchRank'] = $data['search_rank'];
            }
            $faqCategoryArr['UserID'] = auth()->user()->id;
            if ($data['chrMenuDisplay'] == 'D') {
                $faqCategoryArr['chrDraft'] = 'D';
                $faqCategoryArr['chrPublish'] = 'N';
            } else {
                $faqCategoryArr['chrDraft'] = 'N';
                $faqCategoryArr['chrPublish'] = $data['chrMenuDisplay'];
            }
            if (isset($data['chrPageActive']) && $data['chrPageActive'] != '') {
                $faqCategoryArr['chrPageActive'] = $data['chrPageActive'];
            }
            if (isset($data['chrPageActive']) && $data['chrPageActive'] == 'PP') {
                $faqCategoryArr['varPassword'] = $data['new_password'];
            } else {
                $faqCategoryArr['varPassword'] = '';
            }
            if ($data['chrMenuDisplay'] == 'D') {
                $addlog = Config::get('Constant.UPDATE_DRAFT');
            } else {
                $addlog = '';
            }
            $id = Request::segment(3);
            if (is_numeric($id)) { #Edit post Handler=======
                if ($data['oldAlias'] != $data['alias']) {
                    Alias::updateAlias($data['oldAlias'], $data['alias']);
                }
                $faqCategory = FaqCategory::getRecordForLogById($id);
                $whereConditions = ['id' => $faqCategory->id];
                if ($faqCategory->chrLock == 'Y' && auth()->user()->id != $faqCategory->LockUserID) {
                    if ($this->currentUserRoleData->chrIsAdmin != 'Y') {
                        $lockedUserData = User::getRecordById($faqCategory->LockUserID, true);
                        $lockedUserName = 'someone';
                        if (!empty($lockedUserData)) {
                            $lockedUserName = $lockedUserData->name;
                        }
                        $actionMessage = "This record has been locked by " . $lockedUserName . ".";
                        return redirect()->route('powerpanel.faq-category.index')->with('message', $actionMessage);
                    }
                }
                if (File::exists(app_path() . '/Workflow.php') != null || File::exists(base_path() . '/packages/Powerpanel/Workflow/src/Models/Workflow.php') != null) {
                    if (!$userIsAdmin) {
                        $userRole = $this->currentUserRoleData->id;
                    } else {
                        $userRoleData = Role_user::getUserRoleByUserId($faqCategory->UserID);
                        if (isset($userRoleData->role_id)) {
                            $userRole = $userRoleData->role_id;
                        } else {
                            $userRole = $this->currentUserRoleData->id;
                        }
                    }
                    if ($data['chrMenuDisplay'] == 'D') {
                        DB::table('menu')->where('intPageId', $id)->where('intfkModuleId', Config::get('Constant.MODULE.ID'))->delete();
                    }
                    $workFlowByCat = Workflow::getRecordByCategoryId($module->intFkGroupCode, $userRole, Config::get('Constant.MODULE.ID'));
                    if (empty($workFlowByCat->varUserId) || $userIsAdmin || $workFlowByCat->charNeedApproval == 'N') {
                        if ($faqCategory->fkMainRecord == '0' || empty($workFlowByCat->varUserId)) {
                            $update = CommonModel::updateRecords($whereConditions, $faqCategoryArr, false, 'Powerpanel\FaqCategory\Models\FaqCategory');
                            if ($update) {
                                if (!empty($id)) {
                                    self::swap_order_edit($data['order'], $id);
                                    $logArr = MyLibrary::logData($faqCategory->id, false, $addlog);
                                    if (Auth::user()->can('log-advanced')) {
                                        $newFaqCategoryObj = FaqCategory::getRecordForLogById($faqCategory->id);
                                        $oldRec = $this->recordHistory($faqCategory);
                                        $newRec = $this->newrecordHistory($faqCategory, $newFaqCategoryObj);
                                        $logArr['old_val'] = $oldRec;
                                        $logArr['new_val'] = $newRec;
                                    }
                                    $logArr['varTitle'] = trim($data['title']);
                                    Log::recordLog($logArr);
                                    if (Auth::user()->can('recent-updates-list')) {
                                        if (!isset($newFaqCategoryObj)) {
                                            $newFaqCategoryObj = FaqCategory::getRecordForLogById($faqCategory->id);
                                        }
                                        $notificationArr = MyLibrary::notificationData($faqCategory->id, $newFaqCategoryObj);
                                        RecentUpdates::setNotification($notificationArr);
                                    }
                                    self::flushCache();
                                    if (isset($data['saveandexit']) && $data['saveandexit'] == 'approvesaveandexit') {
                                        $actionMessage = trans('faq-category::template.common.recordApprovalMessage');
                                    } else {
                                        $actionMessage = trans('faq-category::template.faqCategoryModule.updateMessage');
                                    }
                                }
                            }
                        } else {
                            $updateModuleFields = $faqCategoryArr;
                            $this->insertApprovedRecord($updateModuleFields, $data, $id);
                            if (isset($data['saveandexit']) && $data['saveandexit'] == 'approvesaveandexit') {
                                $actionMessage = trans('faq-category::template.common.recordApprovalMessage');
                            } else {
                                $actionMessage = trans('faq-category::template.faqCategoryModule.updateMessage');
                            }
                            $approval = $id;
                        }
                    } else {
                        if ($workFlowByCat->charNeedApproval == 'Y') {
                            $approvalObj = $this->insertApprovalRecord($faqCategory, $data, $faqCategoryArr);
                            if (isset($data['saveandexit']) && $data['saveandexit'] == 'approvesaveandexit') {
                                $actionMessage = trans('faq-category::template.common.recordApprovalMessage');
                            } else {
                                $actionMessage = trans('faq-category::template.faqCategoryModule.updateMessage');
                            }
                            $approval = $approvalObj->id;
                        }
                    }
                } else {
                    $update = CommonModel::updateRecords($whereConditions, $faqCategoryArr, false, 'Powerpanel\FaqCategory\Models\FaqCategory');
                    $actionMessage = trans('faq-category::template.faqCategoryModule.updateMessage');
                }
            } else { #Add post Handler=======
                if (File::exists(app_path() . '/Workflow.php') != null || File::exists(base_path() . '/packages/Powerpanel/Workflow/src/Models/Workflow.php') != null) {
                    $workFlowByCat = Workflow::getRecordByCategoryId($module->intFkGroupCode, $this->currentUserRoleData->id, Config::get('Constant.MODULE.ID'));
                }
                if (!empty($workFlowByCat->varUserId) && $workFlowByCat->chrNeedAddPermission == 'Y' && !$userIsAdmin) {

                    $faqCategoryArr['chrPublish'] = 'N';
                    $faqCategoryArr['chrDraft'] = 'N';
                    $faqCategoryObj = $this->insertNewRecord($data, $faqCategoryArr);
                    if ($data['chrMenuDisplay'] == 'D') {
                        $faqCategoryArr['chrDraft'] = 'D';
                    }
                    $faqCategoryArr['chrPublish'] = 'Y';
                    $approvalObj = $this->insertApprovalRecord($faqCategoryObj, $data, $faqCategoryArr);
                    $approval = $faqCategoryObj->id;
                } else {
                    $faqCategoryObj = $this->insertNewRecord($data, $faqCategoryArr);
                    $approval = $faqCategoryObj->id;
                }
                if (isset($data['saveandexit']) && $data['saveandexit'] == 'approvesaveandexit') {
                    $actionMessage = trans('faq-category::template.common.recordApprovalMessage');
                } else {
                    $actionMessage = trans('faq-category::template.faqCategoryModule.addMessage');
                }
                $id = $faqCategoryObj->id;
            }
            if (method_exists($this->Alias, 'updatePreviewAlias')) {
                Alias::updatePreviewAlias($data['alias'], 'N');
            }
            if ((!empty($request->saveandexit) && $request->saveandexit == 'saveandexit') || !$userIsAdmin) {
                if ($data['chrMenuDisplay'] == 'D') {
                    return redirect()->route('powerpanel.faq-category.index', 'tab=D')->with('message', $actionMessage);
                } else {
                    return redirect()->route('powerpanel.faq-category.index')->with('message', $actionMessage);
                }
            } else {
                return redirect()->route('powerpanel.faq-category.edit', $id)->with('message', $actionMessage);
            }
        } else {
            return Redirect::back()->withErrors($validator)->withInput();
        }
    }

    public function insertApprovedRecord($updateModuleFields, $postArr, $id) {
        $whereConditions = ['id' => $postArr['fkMainRecord']];
        $updateModuleFields['chrAddStar'] = 'N';
        $update = CommonModel::updateRecords($whereConditions, $updateModuleFields, false, 'Powerpanel\FaqCategory\Models\FaqCategory');
        if ($update) {
            self::swap_order_edit($postArr['order'], $postArr['fkMainRecord']);
        }
        $whereConditions_ApproveN = ['fkMainRecord' => $postArr['fkMainRecord']];
        $updateToApproveN = [
            'chrApproved' => 'N',
            'chrLetest' => 'N',
            'intApprovedBy' => '0',
        ];
        CommonModel::updateRecords($whereConditions_ApproveN, $updateToApproveN, false, 'Powerpanel\FaqCategory\Models\FaqCategory');
        $whereConditionsApprove = ['id' => $id, 'chrMain' => 'N'];
        $updateToApprove = [
            'chrApproved' => 'Y',
            'chrRollBack' => 'Y',
            'intApprovedBy' => auth()->user()->id
        ];
        CommonModel::updateRecords($whereConditionsApprove, $updateToApprove, false, 'Powerpanel\FaqCategory\Models\FaqCategory');
        if ($postArr['chrMenuDisplay'] == 'D') {
            $addlog = Config::get('Constant.DRAFT_RECORD_APPROVED');
        } else {
            $addlog = Config::get('Constant.RECORD_APPROVED');
        }
        $newCmsPageObj = FaqCategory::getRecordForLogById($id);
        $logArr = MyLibrary::logData($id, false, $addlog);
        $logArr['varTitle'] = stripslashes($newCmsPageObj->varTitle);
        Log::recordLog($logArr);
        /* notification for user to record approved */
        $careers = FaqCategory::getRecordForLogById($id);
        if (method_exists($this->MyLibrary, 'userNotificationData')) {
            $userNotificationArr = MyLibrary::userNotificationData(Config::get('Constant.MODULE.ID'));
            $userNotificationArr['fkRecordId'] = $id;
            $userNotificationArr['txtNotification'] = 'Your request has been approved by ' . ucfirst(auth()->user()->name) . ' (' . ucfirst(Config::get('Constant.MODULE.NAME')) . ')';
            $userNotificationArr['fkIntUserId'] = Auth::user()->id;
            $userNotificationArr['chrNotificationType'] = 'A';
            $userNotificationArr['intOnlyForUserId'] = $careers->UserID;
            UserNotification::addRecord($userNotificationArr);
            /* notification for user to record approved */
        }
        if ($update) {
            if ($id > 0 && !empty($id)) {
                $where = [];
                $flowData = [];
                $flowData['dtYes'] = Config::get('Constant.SQLTIMESTAMP');
                $where['fkModuleId'] = Config::get('Constant.MODULE.ID');
                $where['fkRecordId'] = (isset($postArr['fkMainRecord']) && (int) $postArr['fkMainRecord'] != 0) ? $postArr['fkMainRecord'] : $id;
                $where['dtYes'] = 'null';
                WorkflowLog::updateRecord($flowData, $where);
            }
        }
    }

    public function insertApprovalRecord($moduleObj, $postArr, $faqCategoryArr) {
        $response = false;
        $faqCategoryArr['intAliasId'] = MyLibrary::insertAlias($postArr['alias']);
        $faqCategoryArr['intDisplayOrder'] = $postArr['order'];
        $faqCategoryArr['chrMain'] = 'N';
        $faqCategoryArr['chrLetest'] = 'Y';
        $faqCategoryArr['fkMainRecord'] = $moduleObj->id;
        if(Config::get('Constant.CHRSearchRank') == 'Y'){
        $faqCategoryArr['intSearchRank'] = $postArr['search_rank'];
        }
        if ($postArr['chrMenuDisplay'] == 'D') {
            $faqCategoryArr['chrDraft'] = 'D';
            $faqCategoryArr['chrPublish'] = 'N';
        } else {
            $faqCategoryArr['chrDraft'] = 'N';
            $faqCategoryArr['chrPublish'] = $postArr['chrMenuDisplay'];
        }
        if (isset($postArr['chrPageActive']) && $postArr['chrPageActive'] != '') {
            $faqCategoryArr['chrPageActive'] = $postArr['chrPageActive'];
        }
        if (isset($postArr['chrPageActive']) && $postArr['chrPageActive'] == 'PP') {
            $faqCategoryArr['varPassword'] = $postArr['new_password'];
        } else {
            $faqCategoryArr['varPassword'] = '';
        }
        if ($postArr['chrMenuDisplay'] == 'D') {
            $addlog = Config::get('Constant.DRAFT_SENT_FOR_APPROVAL');
        } else {
            $addlog = Config::get('Constant.SENT_FOR_APPROVAL');
        }
        $faqCategoryID = CommonModel::addRecord($faqCategoryArr, 'Powerpanel\FaqCategory\Models\FaqCategory');
        if (!empty($faqCategoryID)) {
            $id = $faqCategoryID;
            WorkflowLog::addRecord([
                'fkModuleId' => Config::get('Constant.MODULE.ID'),
                'fkRecordId' => $moduleObj->id,
                'charApproval' => 'Y'
            ]);
            if (method_exists($this->MyLibrary, 'userNotificationData')) {
                $userNotificationArr = MyLibrary::userNotificationData(Config::get('Constant.MODULE.ID'));
                $userNotificationArr['fkRecordId'] = $moduleObj->id;
                $userNotificationArr['txtNotification'] = 'New approval request from ' . ucfirst(auth()->user()->name) . ' (' . ucfirst(Config::get('Constant.MODULE.NAME')) . ')';
                $userNotificationArr['fkIntUserId'] = Auth::user()->id;
                $userNotificationArr['chrNotificationType'] = 'A';
                UserNotification::addRecord($userNotificationArr);
            }
            $newFaqCategoryObj = FaqCategory::getRecordForLogById($id);
            $logArr = MyLibrary::logData($id, false, $addlog);
            $logArr['varTitle'] = $newFaqCategoryObj->varTitle;
            Log::recordLog($logArr);
            if (Auth::user()->can('recent-updates-list')) {
                $notificationArr = MyLibrary::notificationData($id, $newFaqCategoryObj);
                RecentUpdates::setNotification($notificationArr);
            }
            $response = $newFaqCategoryObj;
            self::flushCache();
            $actionMessage = trans('faq-category::template.faqCategoryModule.addMessage');
        }
        $whereConditionsAddstar = ['id' => $moduleObj->id];
        $updateAddStar = [
            'chrAddStar' => 'Y',
        ];
        CommonModel::updateRecords($whereConditionsAddstar, $updateAddStar, false, 'Powerpanel\FaqCategory\Models\FaqCategory');
        return $response;
    }

    public function insertNewRecord($postArr, $faqCategoryArr) {
        $response = false;
        $faqCategoryArr['intAliasId'] = MyLibrary::insertAlias($postArr['alias'], Config::get('Constant.MODULE.ID'));
        $faqCategoryArr['intDisplayOrder'] = self::swap_order_add($postArr['order']);
        $faqCategoryArr['chrMain'] = 'Y';
        if(Config::get('Constant.CHRSearchRank') == 'Y'){
        $faqCategoryArr['intSearchRank'] = $postArr['search_rank'];
        }
        if ($postArr['chrMenuDisplay'] == 'D') {
            $faqCategoryArr['chrDraft'] = 'D';
            $faqCategoryArr['chrPublish'] = 'N';
        } else {
            $faqCategoryArr['chrDraft'] = 'N';
        }
        if (isset($postArr['chrPageActive']) && $postArr['chrPageActive'] != '') {
            $faqCategoryArr['chrPageActive'] = $postArr['chrPageActive'];
        }
        if (isset($postArr['chrPageActive']) && $postArr['chrPageActive'] == 'PP') {
            $faqCategoryArr['varPassword'] = $postArr['new_password'];
        } else {
            $faqCategoryArr['varPassword'] = '';
        }
        if ($postArr['chrMenuDisplay'] == 'D') {
            $addlog = Config::get('Constant.ADDED_DRAFT');
        } else {
            $addlog = '';
        }
        $faqCategoryID = CommonModel::addRecord($faqCategoryArr, 'Powerpanel\FaqCategory\Models\FaqCategory');
        if (!empty($faqCategoryID)) {
            $id = $faqCategoryID;
            $newFaqCategoryObj = FaqCategory::getRecordForLogById($id);
            $logArr = MyLibrary::logData($id, false, $addlog);
            $logArr['varTitle'] = stripslashes($newFaqCategoryObj->varTitle);
            Log::recordLog($logArr);
            if (Auth::user()->can('recent-updates-list')) {
                $notificationArr = MyLibrary::notificationData($id, $newFaqCategoryObj);
                RecentUpdates::setNotification($notificationArr);
            }
            $response = $newFaqCategoryObj;
            self::flushCache();
            $actionMessage = trans('faq-category::template.faqCategoryModule.addMessage');
        }
        return $response;
    }

    /**
     * This method loads faqcategory table data on view
     * @return  View
     * @since   2017-11-10
     * @author  NetQuick
     */
    public function get_list_New() {
        $filterArr = [];
        $records = [];
        $records["data"] = [];
        $filterArr['orderColumnNo'] = (!empty(Request::input('order') [0]['column']) ? Request::input('order') [0]['column'] : '');
        $filterArr['orderByFieldName'] = (!empty(Request::input('columns') [$filterArr['orderColumnNo']]['name']) ? Request::input('columns') [$filterArr['orderColumnNo']]['name'] : '');
        $filterArr['orderTypeAscOrDesc'] = (!empty(Request::input('order') [0]['dir']) ? Request::input('order') [0]['dir'] : '');
        $filterArr['statusFilter'] = !empty(Request::input('statusValue')) ? Request::input('statusValue') : '';
        $filterArr['catFilter'] = !empty(Request::input('catValue')) ? Request::input('catValue') : '';
        $filterArr['searchFilter'] = !empty(Request::input('searchValue')) ? Request::input('searchValue') : '';
        $filterArr['customFilterIdentity'] = !empty(Request::get('customFilterIdentity')) ? Request::get('customFilterIdentity') : '';
        $filterArr['iDisplayLength'] = intval(Request::input('length'));
        $filterArr['iDisplayStart'] = intval(Request::input('start'));
        $filterArr['rangeFilter'] = !empty(Request::input('rangeFilter')) ? Request::input('rangeFilter') : '';
        $sEcho = intval(Request::input('draw'));
        $arrResults = FaqCategory::getRecordList_tab1($filterArr);
        $iTotalRecords = FaqCategory::getRecordCountListApprovalTab($filterArr, true);
        $end = $filterArr['iDisplayStart'] + $filterArr['iDisplayLength'];
        $end = $end > $iTotalRecords ? $iTotalRecords : $end;
        if (!empty($arrResults)) {
            foreach ($arrResults as $key => $value) {
                $records["data"][] = $this->tableData_tab1($value);
            }
        }
        $NewRecordsCount = FaqCategory::getNewRecordsCount();
        $records["newRecordCount"] = $NewRecordsCount;
        $records["customActionStatus"] = "OK";
        $records["draw"] = $sEcho;
        $records["recordsTotal"] = $iTotalRecords;
        $records["recordsFiltered"] = $iTotalRecords;
        return json_encode($records);
    }

    public function get_list() {
        $userIsAdmin = false;
        if (!empty($this->currentUserRoleData)) {
            if ($this->currentUserRoleData->chrIsAdmin == 'Y') {
                $userIsAdmin = true;
            }
        }
        $filterArr = [];
        $records = [];
        $records["data"] = [];
        $filterArr['orderColumnNo'] = (!empty(Request::input('order') [0]['column']) ? Request::input('order') [0]['column'] : '');
        $filterArr['orderByFieldName'] = (!empty(Request::input('columns') [$filterArr['orderColumnNo']]['name']) ? Request::input('columns') [$filterArr['orderColumnNo']]['name'] : '');
        $filterArr['orderTypeAscOrDesc'] = (!empty(Request::input('order') [0]['dir']) ? Request::input('order') [0]['dir'] : '');
        $filterArr['statusFilter'] = !empty(Request::input('statusValue')) ? Request::input('statusValue') : '';
        $filterArr['catFilter'] = !empty(Request::input('catValue')) ? Request::input('catValue') : '';
        $filterArr['searchFilter'] = !empty(Request::input('searchValue')) ? Request::input('searchValue') : '';
        $filterArr['customFilterIdentity'] = !empty(Request::get('customFilterIdentity')) ? Request::get('customFilterIdentity') : '';
        $filterArr['iDisplayLength'] = intval(Request::input('length'));
        $filterArr['iDisplayStart'] = intval(Request::input('start'));
        $filterArr['rangeFilter'] = !empty(Request::input('rangeFilter')) ? Request::input('rangeFilter') : '';
        $sEcho = intval(Request::input('draw'));
        $isAdmin = false;
        if ($userIsAdmin) {
            $isAdmin = true;
        }
        $module = Modules::getModule('pages');
        $arrResults = FaqCategory::getRecordList($filterArr, $isAdmin);
        $iTotalRecords = FaqCategory::getRecordCountforList($filterArr, true, $isAdmin);
        $end = $filterArr['iDisplayStart'] + $filterArr['iDisplayLength'];
        $end = $end > $iTotalRecords ? $iTotalRecords : $end;
        $tableSortedType = (isset($filterArr['orderTypeAscOrDesc']) && $filterArr['orderTypeAscOrDesc'] != "") ? $filterArr['orderTypeAscOrDesc'] : '';
        $totalRecords = FaqCategory::getRecordCount();
        if (!empty($arrResults)) {
            foreach ($arrResults as $key => $value) {
                $records["data"][] = $this->tableData($value, $totalRecords, $tableSortedType);
            }
        }
        $NewRecordsCount = FaqCategory::getNewRecordsCount();
        $records["newRecordCount"] = $NewRecordsCount;
        $records["customActionStatus"] = "OK";
        $records["draw"] = $sEcho;
        $records["recordsTotal"] = $iTotalRecords;
        $records["recordsFiltered"] = $iTotalRecords;
        return json_encode($records);
    }

    public function get_list_favorite() {
        $userIsAdmin = false;
        if (!empty($this->currentUserRoleData)) {
            if ($this->currentUserRoleData->chrIsAdmin == 'Y') {
                $userIsAdmin = true;
            }
        }
        $filterArr = [];
        $records = [];
        $records["data"] = [];
        $filterArr['orderColumnNo'] = (!empty(Request::input('order') [0]['column']) ? Request::input('order') [0]['column'] : '');
        $filterArr['orderByFieldName'] = (!empty(Request::input('columns') [$filterArr['orderColumnNo']]['name']) ? Request::input('columns') [$filterArr['orderColumnNo']]['name'] : '');
        $filterArr['orderTypeAscOrDesc'] = (!empty(Request::input('order') [0]['dir']) ? Request::input('order') [0]['dir'] : '');
        $filterArr['statusFilter'] = !empty(Request::input('statusValue')) ? Request::input('statusValue') : '';
        $filterArr['catFilter'] = !empty(Request::input('catValue')) ? Request::input('catValue') : '';
        $filterArr['searchFilter'] = !empty(Request::input('searchValue')) ? Request::input('searchValue') : '';
        $filterArr['customFilterIdentity'] = !empty(Request::get('customFilterIdentity')) ? Request::get('customFilterIdentity') : '';
        $filterArr['iDisplayLength'] = intval(Request::input('length'));
        $filterArr['iDisplayStart'] = intval(Request::input('start'));
        $filterArr['rangeFilter'] = !empty(Request::input('rangeFilter')) ? Request::input('rangeFilter') : '';
        $sEcho = intval(Request::input('draw'));
        $isAdmin = false;
        if ($userIsAdmin) {
            $isAdmin = true;
        }
        $module = Modules::getModule('pages');
        $cmsPageForModule = CmsPage::getRecordForPowerpanelShareByModuleId(Config::get('Constant.MODULE.ID'), $module->id);
        $arrResults = FaqCategory::getRecordListFavorite($filterArr, $isAdmin);
        $iTotalRecords = FaqCategory::getRecordCountforListFavorite($filterArr, true, $isAdmin);
        $end = $filterArr['iDisplayStart'] + $filterArr['iDisplayLength'];
        $end = $end > $iTotalRecords ? $iTotalRecords : $end;
        $tableSortedType = (isset($filterArr['orderTypeAscOrDesc']) && $filterArr['orderTypeAscOrDesc'] != "") ? $filterArr['orderTypeAscOrDesc'] : '';
        $totalRecords = FaqCategory::getRecordCount();
        if (!empty($arrResults)) {
            foreach ($arrResults as $key => $value) {
                $records["data"][] = $this->tableDataFavorite($value, $totalRecords, $tableSortedType, $cmsPageForModule);
            }
        }
        $NewRecordsCount = FaqCategory::getNewRecordsCount();
        $records["newRecordCount"] = $NewRecordsCount;
        $records["customActionStatus"] = "OK";
        $records["draw"] = $sEcho;
        $records["recordsTotal"] = $iTotalRecords;
        $records["recordsFiltered"] = $iTotalRecords;
        return json_encode($records);
    }

    public function get_list_draft() {
        $userIsAdmin = false;
        if (!empty($this->currentUserRoleData)) {
            if ($this->currentUserRoleData->chrIsAdmin == 'Y') {
                $userIsAdmin = true;
            }
        }
        $filterArr = [];
        $records = [];
        $records["data"] = [];
        $filterArr['orderColumnNo'] = (!empty(Request::input('order') [0]['column']) ? Request::input('order') [0]['column'] : '');
        $filterArr['orderByFieldName'] = (!empty(Request::input('columns') [$filterArr['orderColumnNo']]['name']) ? Request::input('columns') [$filterArr['orderColumnNo']]['name'] : '');
        $filterArr['orderTypeAscOrDesc'] = (!empty(Request::input('order') [0]['dir']) ? Request::input('order') [0]['dir'] : '');
        $filterArr['statusFilter'] = !empty(Request::input('statusValue')) ? Request::input('statusValue') : '';
        $filterArr['catFilter'] = !empty(Request::input('catValue')) ? Request::input('catValue') : '';
        $filterArr['searchFilter'] = !empty(Request::input('searchValue')) ? Request::input('searchValue') : '';
        $filterArr['customFilterIdentity'] = !empty(Request::get('customFilterIdentity')) ? Request::get('customFilterIdentity') : '';
        $filterArr['iDisplayLength'] = intval(Request::input('length'));
        $filterArr['iDisplayStart'] = intval(Request::input('start'));
        $filterArr['rangeFilter'] = !empty(Request::input('rangeFilter')) ? Request::input('rangeFilter') : '';
        $sEcho = intval(Request::input('draw'));
        $isAdmin = false;
        if ($userIsAdmin) {
            $isAdmin = true;
        }
        $module = Modules::getModule('pages');
        $cmsPageForModule = CmsPage::getRecordForPowerpanelShareByModuleId(Config::get('Constant.MODULE.ID'), $module->id);
        $arrResults = FaqCategory::getRecordListDraft($filterArr, $isAdmin);
        $iTotalRecords = FaqCategory::getRecordCountforListDarft($filterArr, true, $isAdmin);
        $end = $filterArr['iDisplayStart'] + $filterArr['iDisplayLength'];
        $end = $end > $iTotalRecords ? $iTotalRecords : $end;
        $tableSortedType = (isset($filterArr['orderTypeAscOrDesc']) && $filterArr['orderTypeAscOrDesc'] != "") ? $filterArr['orderTypeAscOrDesc'] : '';
        $totalRecords = FaqCategory::getRecordCount();
        if (!empty($arrResults)) {
            foreach ($arrResults as $key => $value) {
                $records["data"][] = $this->tableDataDraft($value, $totalRecords, $tableSortedType, $cmsPageForModule);
            }
        }
        $NewRecordsCount = FaqCategory::getNewRecordsCount();
        $records["newRecordCount"] = $NewRecordsCount;
        $records["customActionStatus"] = "OK";
        $records["draw"] = $sEcho;
        $records["recordsTotal"] = $iTotalRecords;
        $records["recordsFiltered"] = $iTotalRecords;
        return json_encode($records);
    }

    public function get_list_trash() {
        $userIsAdmin = false;
        if (!empty($this->currentUserRoleData)) {
            if ($this->currentUserRoleData->chrIsAdmin == 'Y') {
                $userIsAdmin = true;
            }
        }
        $filterArr = [];
        $records = [];
        $records["data"] = [];
        $filterArr['orderColumnNo'] = (!empty(Request::input('order') [0]['column']) ? Request::input('order') [0]['column'] : '');
        $filterArr['orderByFieldName'] = (!empty(Request::input('columns') [$filterArr['orderColumnNo']]['name']) ? Request::input('columns') [$filterArr['orderColumnNo']]['name'] : '');
        $filterArr['orderTypeAscOrDesc'] = (!empty(Request::input('order') [0]['dir']) ? Request::input('order') [0]['dir'] : '');
        $filterArr['statusFilter'] = !empty(Request::input('statusValue')) ? Request::input('statusValue') : '';
        $filterArr['catFilter'] = !empty(Request::input('catValue')) ? Request::input('catValue') : '';
        $filterArr['searchFilter'] = !empty(Request::input('searchValue')) ? Request::input('searchValue') : '';
        $filterArr['customFilterIdentity'] = !empty(Request::get('customFilterIdentity')) ? Request::get('customFilterIdentity') : '';
        $filterArr['iDisplayLength'] = intval(Request::input('length'));
        $filterArr['iDisplayStart'] = intval(Request::input('start'));
        $filterArr['rangeFilter'] = !empty(Request::input('rangeFilter')) ? Request::input('rangeFilter') : '';
        $sEcho = intval(Request::input('draw'));
        $isAdmin = false;
        if ($userIsAdmin) {
            $isAdmin = true;
        }
        $module = Modules::getModule('pages');
        $cmsPageForModule = CmsPage::getRecordForPowerpanelShareByModuleId(Config::get('Constant.MODULE.ID'), $module->id);
        $arrResults = FaqCategory::getRecordListTrash($filterArr, $isAdmin);
        $iTotalRecords = FaqCategory::getRecordCountforListTrash($filterArr, true, $isAdmin);
        $end = $filterArr['iDisplayStart'] + $filterArr['iDisplayLength'];
        $end = $end > $iTotalRecords ? $iTotalRecords : $end;
        $tableSortedType = (isset($filterArr['orderTypeAscOrDesc']) && $filterArr['orderTypeAscOrDesc'] != "") ? $filterArr['orderTypeAscOrDesc'] : '';
        $totalRecords = FaqCategory::getRecordCount();
        if (!empty($arrResults)) {
            foreach ($arrResults as $key => $value) {
                $records["data"][] = $this->tableDataTrash($value, $totalRecords, $tableSortedType, $cmsPageForModule);
            }
        }
        $NewRecordsCount = FaqCategory::getNewRecordsCount();
        $records["newRecordCount"] = $NewRecordsCount;
        $records["customActionStatus"] = "OK";
        $records["draw"] = $sEcho;
        $records["recordsTotal"] = $iTotalRecords;
        $records["recordsFiltered"] = $iTotalRecords;
        return json_encode($records);
    }

    public function tableData($value = false, $totalRecord = false, $tableSortedType = 'asc') {
        $Hits = Pagehit::where('fkIntAliasId', $value->intAliasId)->count();
        $webHits = '';
        if ($Hits > 0) {
            $webHits .= '<a data-toggle="modal" href="#" onclick=\'HitsPopup("' . $value->id . '","' . $value->intAliasId . '","' . $value->varTitle . '","P")\'>' . $Hits . '</a>
                    <div class="new_modal modal fade" id="desc_' . $value->id . '_P" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog" style="margin: 0 auto;display:table;width: 100%;height:100%;max-width: 1000px;">
                        <div class="modal-vertical">
                        <div class="modal-content">
                    <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                    <h3 class="modal-title">Hits Report</h3>
                    </div>
                    <div class="modal-body">
                    <div id="webdata_' . $value->id . '_P"></div>
                    </div>
                    </div>
                    </div>
                    </div>
                    </div>';
        } else {
            $webHits .= '0';
        }
        $hasRecords = Faq::getCountById($value->id);
        $details = '';
        $publish_action = '';
        $titleData_delete = "";
        $titleData_publish = "";
        if ($hasRecords > 0) {
            $titleData_delete .= 'This category is selected in ' . trans("faq-category::template.sidebar.faq") . ', so it can&#39;t be deleted.';
            $titleData_publish = 'This category is selected in ' . trans("faq-category::template.sidebar.faq") . ', so it can&#39;t be deleted.';
        }
        $checkbox = '<a href="javascript:;" data-toggle="tooltip" data-placement="right" data-toggle="tooltip" data-original-title="' . $titleData_delete . '" title="' . $titleData_delete . '"><i style="color:red" class="fa fa-exclamation-triangle"></i></a>';
        $checkbox_publish = '<a href="javascript:;" data-toggle="tooltip" data-placement="right" data-toggle="tooltip" data-original-title="' . $titleData_publish . '" title="' . $titleData_publish . '"><i style="color:red" class="fa fa-exclamation-triangle"></i></a>';
        if (Auth::user()->can('faq-category-edit')) {
            $details .= '<a class="" title="' . trans("faq-category::template.common.edit") . '" href="' . route('powerpanel.faq-category.edit', array('alias' => $value->id)) . '"><i class="fa fa-pencil"></i></a>';
        }
        if (Auth::user()->can('faq-category-delete') || (isset($this->currentUserRoleData->chrIsAdmin) && $this->currentUserRoleData->chrIsAdmin == 'Y')) {
            if ($hasRecords == 0) {
                if (Config::get('Constant.DEFAULT_TRASH') == 'Y') {
                    $details .= '<a title = "' . trans('faq-category::template.common.delete') . '" class="delete-grid" onclick = \'Trashfun("' . $value->id . '")\' data-controller = "FaqCategory" data-alias = "' . $value->id . '" data-tab = "P"><i class = "fa fa-times"></i></a>';
                } else {
                    $details .= '<a class = "delete" title = "' . trans('faq-category::template.common.delete') . '" data-controller = "FaqCategory" data-alias = "' . $value->id . '" data-tab = "P"><i class = "fa fa-times"></i></a>';
                }
            }
        }
        if (isset($this->currentUserRoleData->chrIsAdmin) && $this->currentUserRoleData->chrIsAdmin == 'Y') {
            $details .= '<a class=" share" title="Share" data-modal="FaqCategory" data-alias="' . $value->id . '"  data-images="" data-link = "' . url('/' . $value->alias['varAlias']) . '" data-toggle="modal" data-target="#confirm_share">
					<i class="la la-share-alt"></i></a>';
        }
        if ($value->chrAddStar != 'Y') {
            if ($value->chrDraft != 'D') {
                if (Auth::user()->can('faq-category-publish')) {
                    if ($hasRecords == 0) {
                        if (!empty($value->chrPublish) && ($value->chrPublish == 'Y')) {
                            $publish_action .= '<input data-off-text="No" data-on-text="Yes" class="make-switch publish" class="make-switch publish" data-off-color="info" data-on-color="primary" type="checkbox" data-controller="powerpanel/faq-category" title="' . trans("faq-category::template.common.publishedRecord") . '" data-value="Unpublish" data-alias="' . $value->id . '">';
                        } else {
                            $publish_action .= '<input checked="" data-off-text="No" data-on-text="Yes" class="make-switch publish" class="make-switch publish" data-off-color="info" data-on-color="primary" type="checkbox" data-controller="powerpanel/faq-category" title="' . trans("faq-category::template.common.unpublishedRecord") . '" data-value="Publish" data-alias="' . $value->id . '">';
                        }
                    } else {
                        $publish_action = $checkbox_publish;
                    }
                }
            } else {
                $publish_action .= '<input checked="" data-off-text="No" data-on-text="Yes" class="make-switch pub" data-off-color="info" data-on-color="primary" type="checkbox" data-controller="powerpanel/faq-category" title="' . trans("faq-category::template.common.unpublishedRecord") . '" data-value="Publish" data-alias="' . $value->id . '">';
            }
        } else {
            if ($hasRecords == 0) {
                $publish_action .= '---';
            } else {
                $publish_action = $checkbox_publish;
            }
        }
        $minus = '<span class="glyphicon glyphicon-minus"></span>';
        if (Auth::user()->can('faq-category-reviewchanges') && (File::exists(app_path() . '/Workflow.php') != null || File::exists(base_path() . '/packages/Powerpanel/Workflow/src/Models/Workflow.php') != null)) {
            $update = "<a title=\"Click here to see all approval records.\" class=\"icon_title1\" style=\"margin-right: 5px;\" onclick=\"javascript:expandcollapsepanel(this ,'tasklisting" . $value->id . "', 'mainsingnimg" . $value->id . "'," . $value->id . ")\"><i id=\"mainsingnimg" . $value->id . "\" class=\"la la-plus-square\"></i></a>";
            $rollback = "<a title=\"Click here to see all approved records to rollback.\"  class=\"icon_title2\" style=\"margin-right: 5px;\" onclick=\"javascript:expandcollapsepanel_rolback(this ,'tasklisting_rollback" . $value->id . "', 'mainsingnimg_rollback" . $value->id . "'," . $value->id . ")\"><i id=\"mainsingnimg_rollback" . $value->id . "\" class=\"la la-history\"></i></a>";
        } else {
            $update = '';
            $rollback = '';
        }
        $startDate = date('' . Config::get('Constant.DEFAULT_DATE_FORMAT') . ' ' . Config::get('Constant.DEFAULT_TIME_FORMAT') . '', strtotime($value->dtDateTime));
        $endDate = !empty($value->dtEndDateTime) ? date('' . Config::get('Constant.DEFAULT_DATE_FORMAT') . ' ' . Config::get('Constant.DEFAULT_TIME_FORMAT') . '', strtotime($value->dtEndDateTime)) : 'No Expiry';
        $Quickedit_startDate = date('Y-m-d H:i', strtotime($value->dtDateTime));
        $Quickedit_endDate = !empty($value->dtEndDateTime) ? date('Y-m-d H:i', strtotime($value->dtEndDateTime)) : 'No Expiry';
        $title = $value->varTitle;
        if (Auth::user()->can('faq-category-edit')) {
            if ($value->chrDraft == 'D' || $value->chrAddStar == 'Y') {
                $viewlink = url('/previewpage?url=' . MyLibrary::getFrontUri('faq-category')['uri'] . '/' . $value->id . '/preview/detail');
                $linkviewLable = "Preview";
            } else {
                $viewlink = MyLibrary::getFrontUri('faq-category', $value->id)['uri'];
                $linkviewLable = "View";
            }
            //$frontViewLink = MyLibrary::getFrontUri('faq-category', $value->id)['uri'];
            if ($value->chrLock != 'Y') {
                if (isset($this->currentUserRoleData->chrIsAdmin) && $this->currentUserRoleData->chrIsAdmin == 'Y') {
                    $title = '<div class="quick_edit"><a href = "' . route('powerpanel.faq-category.edit', array('alias' => $value->id)) . '?tab=P">' . $value->varTitle . '</a> <div class="quick_edit_menu">
                            <span><a href="' . route('powerpanel.faq-category.edit', array('alias' => $value->id)) . '?tab=P" title="Edit">Edit</a></span>';
                    if (Config::get('Constant.DEFAULT_QUICK') == 'Y') {
                        $title .= '<span><a title="Quick Edit" href=\'javascript:;\' data-toggle=\'modal\' data-target=\'#modalForm\' aria-label=\'Quick edit\' onclick=\'Quickeditfun("' . $value->id . '","' . $value->varTitle . '","' . $value->intSearchRank . '","' . $Quickedit_startDate . '","' . $Quickedit_endDate . '","P")\'>Quick Edit</a></span>';
                    }
                    if ($hasRecords == 0) {
                        if (Config::get('Constant.DEFAULT_TRASH') == 'Y') {
                            $title .= '<span><a title = "Trash" href = \'javascript:;\' onclick=\'Trashfun("' . $value->id . '")\' class="red" data-tab="P">Trash</a></span>';
                        }
                    }
                    $title .= '<span><a href="' . $viewlink . '" target="_blank" title="' . $linkviewLable . '" >' . $linkviewLable . '</a></span>
                                </div>    
                       </div>';
                } else {
                    $title = '<div class="quick_edit"><a href = "' . route('powerpanel.faq-category.edit', array('alias' => $value->id)) . '?tab=P">' . $value->varTitle . '</a> <div class="quick_edit_menu">
                            <span><a href="' . route('powerpanel.faq-category.edit', array('alias' => $value->id)) . '?tab=P" title="Edit">Edit</a></span>
                            <span><a href="' . $viewlink . '" target="_blank" title="' . $linkviewLable . '" >' . $linkviewLable . '</a></span>
                                </div>    
                       </div>';
                }
            } else {
                if (auth()->user()->id != $value->LockUserID) {
                    if (isset($this->currentUserRoleData->chrIsAdmin) && $this->currentUserRoleData->chrIsAdmin == 'Y') {
                        $title = '<div class="quick_edit"><a href = "' . route('powerpanel.faq-category.edit', array('alias' => $value->id)) . '?tab=P">' . $value->varTitle . '</a> <div class="quick_edit_menu">
                            <span><a href="' . route('powerpanel.faq-category.edit', array('alias' => $value->id)) . '?tab=P" title="Edit">Edit</a></span>
                                </div>    
                       </div>';
                    } else {
                        $title = '<div class="quick_edit"><a href = "javascript:;">' . $value->varTitle . '</a></div>';
                    }
                } else {
                    $title = '<div class="quick_edit"><a href = "' . route('powerpanel.faq-category.edit', array('alias' => $value->id)) . '?tab=P">' . $value->varTitle . '</a> <div class="quick_edit_menu">
                            <span><a href="' . route('powerpanel.faq-category.edit', array('alias' => $value->id)) . '?tab=P" title="Edit">Edit</a></span>
                                </div>    
                       </div>';
                }
            }
        }
        $orderArrow = '';
        $dispOrder = $value->intDisplayOrder;
        if (($value->intDisplayOrder == $totalRecord || $value->intDisplayOrder < $totalRecord) && $value->intDisplayOrder > 1) {
            $orderArrow .= '<a href="javascript:;" data-order="' . $value->intDisplayOrder . '" class="moveUp"><i class="fa fa-arrow-up" aria-hidden="true"></i></a> ';
        }
        $orderArrow .= $dispOrder;
        if (($value->intDisplayOrder != $totalRecord || $value->intDisplayOrder < $totalRecord)) {
            $orderArrow .= ' <a href="javascript:;" data-order="' . $value->intDisplayOrder . '" class="moveDwn"><i class="fa fa-arrow-down" aria-hidden="true"></i></a>';
        }

        if (Config::get('Constant.DEFAULT_FAVORITE') == 'Y') {
            $Favorite_array = explode(",", $value->FavoriteID);
            if (in_array(auth()->user()->id, $Favorite_array)) {
                $Class = 'la la-star';
                $Favorite = '<a class="star_icon_div" href="javascript:;" onclick="GetFavorite(' . $value->id . ',\'N\',\'P\')"><i class="' . $Class . '"></i></a>';
            } else {
                $Class = 'la la-star-o';
                $Favorite = '<a class="star_icon_div" href="javascript:;" onclick="GetFavorite(' . $value->id . ',\'Y\',\'P\')"><i class="' . $Class . '"></i></a>';
            }
        } else {
            $Favorite = '';
        }
        $First_td = '<div class="star_box">' . $Favorite . '</div>';
        $logurl = url('powerpanel/log?id=' . $value->id . '&mid=' . Config::get('Constant.MODULE.ID'));
        $log = '';
        if ($value->chrLock != 'Y') {
            if (isset($this->currentUserRoleData->chrIsAdmin) && $this->currentUserRoleData->chrIsAdmin == 'Y') {
                if (Config::get('Constant.DEFAULT_DUPLICATE') == 'Y') {
                    $log .= "<a title=\"Duplicate\" class='copy-grid' href=\"javascript:;\" onclick=\"GetCopyPage('" . $value->id . "');\"><i class=\"fa fa-clone\"></i></a>";
                }
                $log .= $details;
                if (Auth::user()->can('log-list')) {
                    $log .= "<a title=\"Log History\" class='log-grid' href=\"$logurl\"><i class=\"fa fa-clock-o\"></i></a>";
                }
            } else {
                if ($details == "") {
                    $details = "---";
                } else {
                    $details = $details;
                }
                $log .= $details;
                if (Auth::user()->can('log-list')) {
                    $log .= "<a title=\"Log History\" href=\"$logurl\"><i class=\"fa fa-clock-o\"></i></a>";
                }
            }
        } else {
            if (auth()->user()->id != $value->LockUserID) {
                $lockedUserData = User::getRecordById($value->LockUserID, true);
                $lockedUserName = 'someone';
                if (!empty($lockedUserData)) {
                    $lockedUserName = $lockedUserData->name;
                }
                if (isset($this->currentUserRoleData->chrIsAdmin) && $this->currentUserRoleData->chrIsAdmin == 'Y') {
                    $log .= '<a class="star_lock" onclick="GetUnLockData(' . $value->id . ',' . auth()->user()->id . ',' . Config::get('Constant.MODULE.ID') . ',1)" title="This record has been locked by ' . $lockedUserName . ', Click here to unlock."><i class="fa fa-lock"></i></a>';
                } else {
                    $log .= '<a class="star_lock" title="This record has been locked by ' . $lockedUserName . '."><i class="fa fa-lock"></i></a>';
                }
            } else {
                $log .= '<a class="star_lock" onclick="GetUnLockData(' . $value->id . ',' . auth()->user()->id . ',' . Config::get('Constant.MODULE.ID') . ',1)" title="Click here to unlock."><i class="fa fa-lock"></i></a>';
            }
        }
        $statusdata = '';
        if (method_exists($this->MyLibrary, 'count_days')) {
            $days = MyLibrary::count_days($value->created_at);
            $days_modified = MyLibrary::count_days($value->updated_at);
        } else {
            $days = '';
            $days_modified = '';
        }
        if ($days_modified < Config::get('Constant.DEFAULT_DAYS') && $days < Config::get('Constant.DEFAULT_DAYS')) {
            if ($days < Config::get('Constant.DEFAULT_DAYS')) {
                $statusdata = '<img border="0" title="There was new action on this menu." alt="New" src="' . url('assets/images/new.png') . '">';
            }
        } else {
            if ($days_modified < Config::get('Constant.DEFAULT_DAYS')) {
                $statusdata = '<img border="0" title="There was edit/update action on this menu." alt="Updated" src="' . url('assets/images/updated.png') . '">';
            }
            if ($days < Config::get('Constant.DEFAULT_DAYS')) {
                $statusdata = '<img border="0" title="There was new action on this menu." alt="New" src="' . url('assets/images/new.png') . '">';
            }
        }
        $status = '';
        if ($value->chrDraft == 'D') {
            $status .= Config::get('Constant.DRAFT_LIST') . ' ';
        }
        if ($value->chrAddStar == 'Y') {
            $status .= Config::get('Constant.APPROVAL_LIST') . ' ';
        }
        if ($publish_action == "") {
            $publish_action = "---";
        } else {
            if ($value->chrLock != 'Y') {
                $publish_action = $publish_action;
            } else {
                if ((auth()->user()->id == $value->LockUserID) || (isset($this->currentUserRoleData->chrIsAdmin) && $this->currentUserRoleData->chrIsAdmin == 'Y')) {
                    $publish_action = $publish_action;
                } else {
                    $publish_action = "---";
                }
            }
        }
        if (Auth::user()->can('banners-reviewchanges')) {
            $log .= "<a title='Rollback to previous version'  onclick=\"rollbackToPreviousVersion('" . $value->id . "');\"  class=\"log-grid\"><i class=\"fa fa-history\"></i></a>";
        }
        $records = array(
            ($hasRecords == 0) ? '<input type="checkbox" name="delete" class="chkDelete" value="' . $value->id . '">' : $checkbox,
            $First_td,
            '<div class="pages_title_div_row">' . $update . $rollback . $title . ' ' . $status . $statusdata . '</div>',
            $startDate,
            $endDate,
            $orderArrow,
            $webHits,
            $publish_action,
            $log,
            $value->intDisplayOrder,
        );
        return $records;
    }

    public function tableDataFavorite($value = false, $totalRecord = false, $tableSortedType = 'asc', $moduleCmsPageData = false) {
        $Hits = Pagehit::where('fkIntAliasId', $value->intAliasId)->count();
        $webHits = '';
        if ($Hits > 0) {
            $webHits .= '<a data-toggle="modal" href="#" onclick=\'HitsPopup("' . $value->id . '","' . $value->intAliasId . '","' . $value->varTitle . '","P")\'>' . $Hits . '</a>
                    <div class="new_modal modal fade" id="desc_' . $value->id . '_P" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog" style="margin: 0 auto;display:table;width: 100%;height:100%;max-width: 1000px;">
                        <div class="modal-vertical">
                        <div class="modal-content">
                    <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                    <h3 class="modal-title">Hits Report</h3>
                    </div>
                    <div class="modal-body">
                    <div id="webdata_' . $value->id . '_P"></div>
                    </div>
                    </div>
                    </div>
                    </div>
                    </div>';
        } else {
            $webHits .= '0';
        }
        $hasRecords = Faq::getCountById($value->id);
        $details = '';
        $publish_action = '';
        $titleData_delete = "";
        $titleData_publish = "";
        if ($hasRecords > 0) {
            $titleData_delete .= 'This category is selected in ' . trans("faq-category::template.sidebar.faq") . ', so it can&#39;t be deleted.';
            $titleData_publish = 'This category is selected in ' . trans("faq-category::template.sidebar.faq") . ', so it can&#39;t be deleted.';
        }
        $checkbox = '<a href="javascript:;" data-toggle="tooltip" data-placement="right" data-toggle="tooltip" data-original-title="' . $titleData_delete . '" title="' . $titleData_delete . '"><i style="color:red" class="fa fa-exclamation-triangle"></i></a>';
        $checkbox_publish = '<a href="javascript:;" data-toggle="tooltip" data-placement="right" data-toggle="tooltip" data-original-title="' . $titleData_publish . '" title="' . $titleData_publish . '"><i style="color:red" class="fa fa-exclamation-triangle"></i></a>';
        if (Auth::user()->can('faq-category-edit')) {
            $details .= '<a class="" title="' . trans("faq-category::template.common.edit") . '" href="' . route('powerpanel.faq-category.edit', array('alias' => $value->id)) . '"><i class="fa fa-pencil"></i></a>';
        }
        if (Auth::user()->can('faq-category-delete') && $this->currentUserRoleData->chrIsAdmin == 'Y') {
            if ($hasRecords == 0) {
                if (Config::get('Constant.DEFAULT_TRASH') == 'Y') {
                    $details .= '<a title = "' . trans('faq-category::template.common.delete') . '" class="delete-grid" onclick = \'Trashfun("' . $value->id . '")\' data-controller = "FaqCategory" data-alias = "' . $value->id . '" data-tab = "F"><i class = "fa fa-times"></i></a>';
                } else {
                    $details .= '<a class = "delete" title = "' . trans('faq-category::template.common.delete') . '" data-controller = "FaqCategory" data-alias = "' . $value->id . '" data-tab = "F"><i class = "fa fa-times"></i></a>';
                }
            }
        }
        if (Auth::user()->can('faq-category-publish')) {
            if ($hasRecords == 0) {
                if (!empty($value->chrPublish) && ($value->chrPublish == 'Y')) {
                    $publish_action .= '<input data-off-text="No" data-on-text="Yes" class="make-switch publish" class="make-switch publish" data-off-color="info" data-on-color="primary" type="checkbox" data-controller="powerpanel/faq-category" title="' . trans("faq-category::template.common.publishedRecord") . '" data-value="Unpublish" data-alias="' . $value->id . '">';
                } else {
                    $publish_action .= '<input checked="" data-off-text="No" data-on-text="Yes" class="make-switch publish" class="make-switch publish" data-off-color="info" data-on-color="primary" type="checkbox" data-controller="powerpanel/faq-category" title="' . trans("faq-category::template.common.unpublishedRecord") . '" data-value="Publish" data-alias="' . $value->id . '">';
                }
            } else {
                $publish_action = $checkbox_publish;
            }
        }
        $startDate = date('' . Config::get('Constant.DEFAULT_DATE_FORMAT') . ' ' . Config::get('Constant.DEFAULT_TIME_FORMAT') . '', strtotime($value->dtDateTime));
        $endDate = !empty($value->dtEndDateTime) ? date('' . Config::get('Constant.DEFAULT_DATE_FORMAT') . ' ' . Config::get('Constant.DEFAULT_TIME_FORMAT') . '', strtotime($value->dtEndDateTime)) : 'No Expiry';
        $Quickedit_startDate = date('Y-m-d H:i', strtotime($value->dtDateTime));
        $Quickedit_endDate = !empty($value->dtEndDateTime) ? date('Y-m-d H:i', strtotime($value->dtEndDateTime)) : 'No Expiry';
        $title = $value->varTitle;
        if (Auth::user()->can('faq-category-edit')) {
            if ($value->chrDraft == 'D' || $value->chrAddStar == 'Y') {
                $viewlink = url('/previewpage?url=' . MyLibrary::getFrontUri('faq-category')['uri'] . '/' . $value->id . '/preview/detail');
                $linkviewLable = "Preview";
            } else {
                $viewlink = MyLibrary::getFrontUri('faq-category', $value->id)['uri'];
                $linkviewLable = "View";
            }
            //$frontViewLink = MyLibrary::getFrontUri('faq-category', $value->id)['uri'];
            if ($value->chrLock != 'Y') {
                if ($this->currentUserRoleData->chrIsAdmin == 'Y') {
                    $title = '<div class="quick_edit"><a href = "' . route('powerpanel.faq-category.edit', array('alias' => $value->id)) . '?tab=F">' . $value->varTitle . '</a> <div class="quick_edit_menu">
                            <span><a href="' . route('powerpanel.faq-category.edit', array('alias' => $value->id)) . '?tab=F" title="Edit">Edit</a></span>';
                    if ($hasRecords == 0) {
                        if (Config::get('Constant.DEFAULT_TRASH') == 'Y') {
                            $title .= '<span><a title = "Trash" href = \'javascript:;\' onclick=\'Trashfun("' . $value->id . '")\' class="red" data-tab="F">Trash</a></span>';
                        }
                    }
                    $title .= '<span><a href="' . $viewlink . '" target="_blank" title="' . $linkviewLable . '" >' . $linkviewLable . '</a></span>
                                </div>    
                       </div>';
                } else {
                    $title = '<div class="quick_edit"><a href = "' . route('powerpanel.faq-category.edit', array('alias' => $value->id)) . '?tab=F">' . $value->varTitle . '</a> <div class="quick_edit_menu">
                            <span><a href="' . route('powerpanel.faq-category.edit', array('alias' => $value->id)) . '?tab=F" title="Edit">Edit</a></span>
                            <span><a href="' . $viewlink . '" target="_blank" title="' . $linkviewLable . '" >' . $linkviewLable . '</a></span>
                                </div>    
                       </div>';
                }
            } else {
                if (auth()->user()->id != $value->LockUserID) {
                    if ($this->currentUserRoleData->chrIsAdmin == 'Y') {
                        $title = '<div class="quick_edit"><a href = "' . route('powerpanel.faq-category.edit', array('alias' => $value->id)) . '?tab=F">' . $value->varTitle . '</a> <div class="quick_edit_menu">
	                            <span><a href="' . route('powerpanel.faq-category.edit', array('alias' => $value->id)) . '?tab=F" title="Edit">Edit</a></span>
	                                </div>    
	                        </div>';
                    } else {
                        $title = '<div class="quick_edit"><a href = "javascript:;">' . $value->varTitle . '</a></div>';
                    }
                } else {
                    $title = '<div class="quick_edit"><a href = "' . route('powerpanel.faq-category.edit', array('alias' => $value->id)) . '?tab=F">' . $value->varTitle . '</a> <div class="quick_edit_menu">
	                            <span><a href="' . route('powerpanel.faq-category.edit', array('alias' => $value->id)) . '?tab=F" title="Edit">Edit</a></span>
	                                </div>    
	                        </div>';
                }
            }
        }
        $orderArrow = '';
        $dispOrder = $value->intDisplayOrder;
        if (($value->intDisplayOrder == $totalRecord || $value->intDisplayOrder < $totalRecord) && $value->intDisplayOrder > 1) {
            $orderArrow .= '<a href="javascript:;" data-order="' . $value->intDisplayOrder . '" class="moveUp"><i class="fa fa-arrow-up" aria-hidden="true"></i></a>';
        }
        $orderArrow .= $dispOrder;
        if (($value->intDisplayOrder != $totalRecord || $value->intDisplayOrder < $totalRecord)) {
            $orderArrow .= ' <a href="javascript:;" data-order="' . $value->intDisplayOrder . '" class="moveDwn"><i class="fa fa-arrow-down" aria-hidden="true"></i></a>';
        }

        if (Config::get('Constant.DEFAULT_FAVORITE') == 'Y') {
            $Favorite_array = explode(",", $value->FavoriteID);
            if (in_array(auth()->user()->id, $Favorite_array)) {
                $Class = 'la la-star';
                $Favorite = '<a class="star_icon_div" href="javascript:;" onclick="GetFavorite(' . $value->id . ',\'N\',\'F\')"><i class="' . $Class . '"></i></a>';
            } else {
                $Class = 'la la-star-o';
                $Favorite = '<a class="star_icon_div" href="javascript:;" onclick="GetFavorite(' . $value->id . ',\'Y\',\'F\')"><i class="' . $Class . '"></i></a>';
            }
        } else {
            $Favorite = '';
        }
        $First_td = '<div class="star_box">' . $Favorite . '</div>';
        $logurl = url('powerpanel/log?id=' . $value->id . '&mid=' . Config::get('Constant.MODULE.ID'));
        $log = '';
        if ($value->chrLock != 'Y') {
            if ($this->currentUserRoleData->chrIsAdmin == 'Y') {
                $log .= $details;
                if (Auth::user()->can('log-list')) {
                    $log .= "<a title=\"Log History\" href=\"$logurl\"><i class=\"fa fa-clock-o\"></i></a>";
                }
            } else {
                if ($details == "") {
                    $details = "---";
                } else {
                    $details = $details;
                }
                $log .= $details;
                if (Auth::user()->can('log-list')) {
                    $log .= "<a title=\"Log History\" href=\"$logurl\"><i class=\"fa fa-clock-o\"></i></a>";
                }
            }
        } else {
            if (auth()->user()->id != $value->LockUserID) {
                if ($this->currentUserRoleData->chrIsAdmin == 'Y') {
                    $lockedUserData = User::getRecordById($value->LockUserID, true);
                    $lockedUserName = 'someone';
                    if (!empty($lockedUserData)) {
                        $lockedUserName = $lockedUserData->name;
                    }
                    $log .= '<a class="star_lock" onclick="GetUnLockData(' . $value->id . ',' . auth()->user()->id . ',' . Config::get('Constant.MODULE.ID') . ',1)" title="This record has been locked by ' . $lockedUserName . ', Click here to unlock."><i class="fa fa-lock"></i></a>';
                } else {

                    $log .= '<a class="star_lock" title="This record has been locked by ' . $lockedUserName . '."><i class="fa fa-lock"></i></a>';
                }
            } else {
                $log .= '<a class="star_lock" onclick="GetUnLockData(' . $value->id . ',' . auth()->user()->id . ',' . Config::get('Constant.MODULE.ID') . ',1)" title="Click here to unlock."><i class="fa fa-lock"></i></a>';
            }
        }
        $statusdata = '';
        $days = MyLibrary::count_days($value->created_at);
        $days_modified = MyLibrary::count_days($value->updated_at);
        if ($days_modified < Config::get('Constant.DEFAULT_DAYS') && $days < Config::get('Constant.DEFAULT_DAYS')) {
            if ($days < Config::get('Constant.DEFAULT_DAYS')) {
                $statusdata = '<img border="0" title="There was new action on this menu." alt="New" src="' . url('assets/images/new.png') . '">';
            }
        } else {
            if ($days_modified < Config::get('Constant.DEFAULT_DAYS')) {
                $statusdata = '<img border="0" title="There was edit/update action on this menu." alt="Updated" src="' . url('assets/images/updated.png') . '">';
            }
            if ($days < Config::get('Constant.DEFAULT_DAYS')) {
                $statusdata = '<img border="0" title="There was new action on this menu." alt="New" src="' . url('assets/images/new.png') . '">';
            }
        }
        $status = '';
        if ($value->chrDraft == 'D') {
            $status .= Config::get('Constant.DRAFT_LIST') . ' ';
        }
        if ($value->chrAddStar == 'Y') {
            $status .= Config::get('Constant.APPROVAL_LIST') . ' ';
        }

        if (Auth::user()->can('banners-reviewchanges')) {
            $log .= "<a title='Rollback to previous version'  onclick=\"rollbackToPreviousVersion('" . $value->id . "');\"  class=\"log-grid\"><i class=\"fa fa-history\"></i></a>";
        }

        $records = array(
            ($hasRecords == 0) ? '<input type="checkbox" name="delete" class="chkDelete" value="' . $value->id . '">' : $checkbox,
            $First_td,
            '<div class="pages_title_div_row">' . $title . ' ' . $status . $statusdata . '</div>',
            $startDate,
            $endDate,
            $webHits,
            $log,
            $value->intDisplayOrder,
        );
        return $records;
    }

    public function tableDataDraft($value = false, $totalRecord = false, $tableSortedType = 'asc', $moduleCmsPageData = false) {
        $Hits = Pagehit::where('fkIntAliasId', $value->intAliasId)->count();
        $webHits = '';
        if ($Hits > 0) {
            $webHits .= '<a data-toggle="modal" href="#" onclick=\'HitsPopup("' . $value->id . '","' . $value->intAliasId . '","' . $value->varTitle . '","P")\'>' . $Hits . '</a>
                    <div class="new_modal modal fade" id="desc_' . $value->id . '_P" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog" style="margin: 0 auto;display:table;width: 100%;height:100%;max-width: 1000px;">
                        <div class="modal-vertical">
                        <div class="modal-content">
                    <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                    <h3 class="modal-title">Hits Report</h3>
                    </div>
                    <div class="modal-body">
                    <div id="webdata_' . $value->id . '_P"></div>
                    </div>
                    </div>
                    </div>
                    </div>
                    </div>';
        } else {
            $webHits .= '0';
        }
        $hasRecords = Faq::getCountById($value->id);
        $details = '';
        $publish_action = '';
        $titleData_delete = "";
        $titleData_publish = "";
        if ($hasRecords > 0) {
            $titleData_delete .= 'This category is selected in ' . trans("faq-category::template.sidebar.faq") . ', so it can&#39;t be deleted.';
            $titleData_publish = 'This category is selected in ' . trans("faq-category::template.sidebar.faq") . ', so it can&#39;t be deleted.';
        }
        $checkbox = '<a href="javascript:;" data-toggle="tooltip" data-placement="right" data-toggle="tooltip" data-original-title="' . $titleData_delete . '" title="' . $titleData_delete . '"><i style="color:red" class="fa fa-exclamation-triangle"></i></a>';
        $checkbox_publish = '<a href="javascript:;" data-toggle="tooltip" data-placement="right" data-toggle="tooltip" data-original-title="' . $titleData_publish . '" title="' . $titleData_publish . '"><i style="color:red" class="fa fa-exclamation-triangle"></i></a>';
        if (Auth::user()->can('faq-category-edit')) {
            $details .= '<a class="" title="' . trans("faq-category::template.common.edit") . '" href="' . route('powerpanel.faq-category.edit', array('alias' => $value->id)) . '"><i class="fa fa-pencil"></i></a>';
        }
        if (Auth::user()->can('faq-category-delete') && $this->currentUserRoleData->chrIsAdmin == 'Y') {
            if ($hasRecords == 0) {
                if (Config::get('Constant.DEFAULT_TRASH') == 'Y') {
                    $details .= '<a title = "' . trans('faq-category::template.common.delete') . '" class="delete-grid" onclick = \'Trashfun("' . $value->id . '")\' data-controller = "FaqCategory" data-alias = "' . $value->id . '" data-tab = "D"><i class = "fa fa-times"></i></a>';
                } else {
                    $details .= '<a class = "delete" title = "' . trans('faq-category::template.common.delete') . '" data-controller = "FaqCategory" data-alias = "' . $value->id . '" data-tab = "D"><i class = "fa fa-times"></i></a>';
                }
            }
        }
        if ($value->chrPublish == 'Y') {
            $publish_action .= '<input data-off-text="No" data-on-text="Yes" class="make-switch pub"  data-off-color="info" data-on-color="primary" type="checkbox" data-controller="powerpanel/faq-category" title="' . trans("faq-category::template.common.publishedRecord") . '" data-value="Unpublish" data-alias="' . $value->id . '">';
        } else {
            $publish_action .= '<input checked="" data-off-text="No" data-on-text="Yes" class="make-switch pub"  data-off-color="info" data-on-color="primary" type="checkbox" data-controller="powerpanel/faq-category" title="' . trans("faq-category::template.common.unpublishedRecord") . '" data-value="Publish" data-alias="' . $value->id . '">';
        }
        $startDate = date('' . Config::get('Constant.DEFAULT_DATE_FORMAT') . ' ' . Config::get('Constant.DEFAULT_TIME_FORMAT') . '', strtotime($value->dtDateTime));
        $endDate = !empty($value->dtEndDateTime) ? date('' . Config::get('Constant.DEFAULT_DATE_FORMAT') . ' ' . Config::get('Constant.DEFAULT_TIME_FORMAT') . '', strtotime($value->dtEndDateTime)) : 'No Expiry';
        $Quickedit_startDate = date('Y-m-d H:i', strtotime($value->dtDateTime));
        $Quickedit_endDate = !empty($value->dtEndDateTime) ? date('Y-m-d H:i', strtotime($value->dtEndDateTime)) : 'No Expiry';
        $title = $value->varTitle;
        if (Auth::user()->can('faq-category-edit')) {
            if ($value->chrDraft == 'D' || $value->chrAddStar == 'Y') {
                $viewlink = url('/previewpage?url=' . MyLibrary::getFrontUri('faq-category')['uri'] . '/' . $value->id . '/preview/detail');
                $linkviewLable = "Preview";
            } else {
                $viewlink = MyLibrary::getFrontUri('faq-category', $value->id)['uri'];
                $linkviewLable = "View";
            }
            //$previewlink = url('/previewpage?url=' . MyLibrary::getFrontUri('faq-category')['uri'] . '/' . $value->id . '/preview/detail');
            if ($value->chrLock != 'Y') {
                if ($this->currentUserRoleData->chrIsAdmin == 'Y') {
                    $title = '<div class="quick_edit"><a href = "' . route('powerpanel.faq-category.edit', array('alias' => $value->id)) . '?tab=D">' . $value->varTitle . '</a> <div class="quick_edit_menu">
                            <span><a href="' . route('powerpanel.faq-category.edit', array('alias' => $value->id)) . '?tab=D" title="Edit">Edit</a></span>';
                    if ($hasRecords == 0) {
                        if (Config::get('Constant.DEFAULT_TRASH') == 'Y') {
                            $title .= '<span><a title = "Trash" href = \'javascript:;\' onclick=\'Trashfun("' . $value->id . '")\' class="red" data-tab="D">Trash</a></span>';
                        }
                    }
                    $title .= '<span><a href="' . $viewlink . '" target="_blank" title="' . $linkviewLable . '" >' . $linkviewLable . '</a></span>
                                </div>    
                       </div>';
                } else {
                    $title = '<div class="quick_edit"><a href = "' . route('powerpanel.faq-category.edit', array('alias' => $value->id)) . '?tab=D">' . $value->varTitle . '</a> <div class="quick_edit_menu">
                            <span><a href="' . route('powerpanel.faq-category.edit', array('alias' => $value->id)) . '?tab=D" title="Edit">Edit</a></span>
                            <span><a href="' . $viewlink . '" target="_blank" title="' . $linkviewLable . '" >' . $linkviewLable . '</a></span>
                                </div>    
                       </div>';
                }
            } else {
                if (auth()->user()->id != $value->LockUserID) {
                    if ($this->currentUserRoleData->chrIsAdmin == 'Y') {
                        $title = '<div class="quick_edit"><a href = "' . route('powerpanel.faq-category.edit', array('alias' => $value->id)) . '?tab=D">' . $value->varTitle . '</a> <div class="quick_edit_menu">
	                            <span><a href="' . route('powerpanel.faq-category.edit', array('alias' => $value->id)) . '?tab=D" title="Edit">Edit</a></span></div></div>';
                    } else {
                        $title = '<div class="quick_edit"><a href = "javascript:;">' . $value->varTitle . '</a></div>';
                    }
                } else {
                    $title = '<div class="quick_edit"><a href = "' . route('powerpanel.faq-category.edit', array('alias' => $value->id)) . '?tab=D">' . $value->varTitle . '</a> <div class="quick_edit_menu">
	                            <span><a href="' . route('powerpanel.faq-category.edit', array('alias' => $value->id)) . '?tab=D" title="Edit">Edit</a></span>
	                                </div>    
	                        </div>';
                }
            }
        }

        $logurl = url('powerpanel/log?id=' . $value->id . '&mid=' . Config::get('Constant.MODULE.ID'));
        $log = '';
        if ($value->chrLock != 'Y') {
            if ($this->currentUserRoleData->chrIsAdmin == 'Y') {
                $log .= $details;
                if (Auth::user()->can('log-list')) {
                    $log .= "<a title=\"Log History\" href=\"$logurl\"><i class=\"fa fa-clock-o\"></i></a>";
                }
            } else {
                if ($details == "") {
                    $details = "---";
                } else {
                    $details = $details;
                }
                $log .= $details;
                if (Auth::user()->can('log-list')) {
                    $log .= "<a title=\"Log History\" href=\"$logurl\"><i class=\"fa fa-clock-o\"></i></a>";
                }
            }
        } else {
            if (auth()->user()->id != $value->LockUserID) {
                $lockedUserData = User::getRecordById($value->LockUserID, true);
                $lockedUserName = 'someone';
                if (!empty($lockedUserData)) {
                    $lockedUserName = $lockedUserData->name;
                }
                if ($this->currentUserRoleData->chrIsAdmin == 'Y') {
                    $log .= '<a class="star_lock" onclick="GetUnLockData(' . $value->id . ',' . auth()->user()->id . ',' . Config::get('Constant.MODULE.ID') . ',1)" title="This record has been locked by ' . $lockedUserName . ', Click here to unlock."><i class="fa fa-lock"></i></a>';
                } else {

                    $log .= '<a class="star_lock" title="This record has been locked by ' . $lockedUserName . '."><i class="fa fa-lock"></i></a>';
                }
            } else {
                $log .= '<a class="star_lock" onclick="GetUnLockData(' . $value->id . ',' . auth()->user()->id . ',' . Config::get('Constant.MODULE.ID') . ',1)" title="Click here to unlock."><i class="fa fa-lock"></i></a>';
            }
        }
        $statusdata = '';
        $days = MyLibrary::count_days($value->created_at);
        $days_modified = MyLibrary::count_days($value->updated_at);
        if ($days_modified < Config::get('Constant.DEFAULT_DAYS') && $days < Config::get('Constant.DEFAULT_DAYS')) {
            if ($days < Config::get('Constant.DEFAULT_DAYS')) {
                $statusdata = '<img border="0" title="There was new action on this menu." alt="New" src="' . url('assets/images/new.png') . '">';
            }
        } else {
            if ($days_modified < Config::get('Constant.DEFAULT_DAYS')) {
                $statusdata = '<img border="0" title="There was edit/update action on this menu." alt="Updated" src="' . url('assets/images/updated.png') . '">';
            }
            if ($days < Config::get('Constant.DEFAULT_DAYS')) {
                $statusdata = '<img border="0" title="There was new action on this menu." alt="New" src="' . url('assets/images/new.png') . '">';
            }
        }
        $status = '';

        if ($value->chrAddStar == 'Y') {
            $status .= Config::get('Constant.APPROVAL_LIST') . ' ';
        }
        if ($publish_action == "") {
            $publish_action = "---";
        } else {
            if ($value->chrLock != 'Y') {
                $publish_action = $publish_action;
            } else {
                if ((auth()->user()->id == $value->LockUserID) || $this->currentUserRoleData->chrIsAdmin == 'Y') {
                    $publish_action = $publish_action;
                } else {
                    $publish_action = "---";
                }
            }
        }
        $records = array(
            ($hasRecords == 0) ? '<input type="checkbox" name="delete" class="chkDelete" value="' . $value->id . '">' : $checkbox,
            '<div class="pages_title_div_row"><input type="hidden" id="draftid" value="' . $value->id . '">' . $title . ' ' . $status . $statusdata . '</div>',
            $startDate,
            $endDate,
            $webHits,
            $publish_action,
            $log,
            $value->intDisplayOrder,
        );
        return $records;
    }

    public function tableDataTrash($value = false, $totalRecord = false, $tableSortedType = 'asc', $moduleCmsPageData = false) {
        $Hits = Pagehit::where('fkIntAliasId', $value->intAliasId)->count();
        $webHits = '';
        if ($Hits > 0) {
            $webHits .= '<a data-toggle="modal" href="#" onclick=\'HitsPopup("' . $value->id . '","' . $value->intAliasId . '","' . $value->varTitle . '","P")\'>' . $Hits . '</a>
                    <div class="new_modal modal fade" id="desc_' . $value->id . '_P" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog" style="margin: 0 auto;display:table;width: 100%;height:100%;max-width: 1000px;">
                        <div class="modal-vertical">
                        <div class="modal-content">
                    <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                    <h3 class="modal-title">Hits Report</h3>
                    </div>
                    <div class="modal-body">
                    <div id="webdata_' . $value->id . '_P"></div>
                    </div>
                    </div>
                    </div>
                    </div>
                    </div>';
        } else {
            $webHits .= '0';
        }
        $hasRecords = Faq::getCountById($value->id);
        $details = '';
        $publish_action = '';
        $titleData_delete = "";
        $titleData_publish = "";
        if ($hasRecords > 0) {
            $titleData_delete .= 'This category is selected in ' . trans("faq-category::template.sidebar.faq") . ', so it can&#39;t be deleted.';
            $titleData_publish = 'This category is selected in ' . trans("faq-category::template.sidebar.faq") . ', so it can&#39;t be deleted.';
        }
        $checkbox = '<a href="javascript:;" data-toggle="tooltip" data-placement="right" data-toggle="tooltip" data-original-title="' . $titleData_delete . '" title="' . $titleData_delete . '"><i style="color:red" class="fa fa-exclamation-triangle"></i></a>';
        if (Auth::user()->can('faq-category-delete') && $this->currentUserRoleData->chrIsAdmin == 'Y') {
            if ($hasRecords == 0) {
                $details .= '<a class = "delete" title = "' . trans('faq-category::template.common.delete') . '" data-controller = "FaqCategory" data-alias = "' . $value->id . '" data-tab = "T"><i class = "fa fa-times"></i></a>';
            }
        }
        $startDate = date('' . Config::get('Constant.DEFAULT_DATE_FORMAT') . ' ' . Config::get('Constant.DEFAULT_TIME_FORMAT') . '', strtotime($value->dtDateTime));
        $endDate = !empty($value->dtEndDateTime) ? date('' . Config::get('Constant.DEFAULT_DATE_FORMAT') . ' ' . Config::get('Constant.DEFAULT_TIME_FORMAT') . '', strtotime($value->dtEndDateTime)) : 'No Expiry';
        $title = $value->varTitle;
        if (Auth::user()->can('faq-category-edit')) {
            $title = '<div class="quick_edit text-uppercase">' . $value->varTitle . '    
                        </div>';
        }


        $logurl = url('powerpanel/log?id=' . $value->id . '&mid=' . Config::get('Constant.MODULE.ID'));
        if ($details == "") {
            $details = "---";
        } else {
            $details = $details;
        }
        $log = '';
        if ($value->chrLock != 'Y') {
            if ($this->currentUserRoleData->chrIsAdmin == 'Y') {
                if (Config::get('Constant.DEFAULT_TRASH') == 'Y') {
                    $log .= "<a title=\"Restore\" href='javascript:;' onclick='Restorefun(\"$value->id\",\"T\")'><i class=\"fa fa-repeat\"></i></a>";
                }
                $log .= $details;
                if (Auth::user()->can('log-list')) {
                    $log .= "<a title=\"Log History\" href=\"$logurl\"><i class=\"fa fa-clock-o\"></i></a>";
                }
            }
        } else {
            if (auth()->user()->id != $value->LockUserID) {
                $lockedUserData = User::getRecordById($value->LockUserID, true);
                $lockedUserName = 'someone';
                if (!empty($lockedUserData)) {
                    $lockedUserName = $lockedUserData->name;
                }
                if ($this->currentUserRoleData->chrIsAdmin == 'Y') {
                    $log .= '<a class="star_lock" onclick="GetUnLockData(' . $value->id . ',' . auth()->user()->id . ',' . Config::get('Constant.MODULE.ID') . ',1)" title="This record has been locked by ' . $lockedUserName . ', Click here to unlock."><i class="fa fa-lock"></i></a>';
                } else {

                    $log .= '<a class="star_lock" title="This record has been locked by ' . $lockedUserName . '."><i class="fa fa-lock"></i></a>';
                }
            } else {
                $log .= '<a class="star_lock" onclick="GetUnLockData(' . $value->id . ',' . auth()->user()->id . ',' . Config::get('Constant.MODULE.ID') . ',1)" title="Click here to unlock."><i class="fa fa-lock"></i></a>';
            }
        }
        $statusdata = '';
        $days = MyLibrary::count_days($value->created_at);
        $days_modified = MyLibrary::count_days($value->updated_at);
        if ($days_modified < Config::get('Constant.DEFAULT_DAYS') && $days < Config::get('Constant.DEFAULT_DAYS')) {
            if ($days < Config::get('Constant.DEFAULT_DAYS')) {
                $statusdata = '<img border="0" title="There was new action on this menu." alt="New" src="' . url('assets/images/new.png') . '">';
            }
        } else {
            if ($days_modified < Config::get('Constant.DEFAULT_DAYS')) {
                $statusdata = '<img border="0" title="There was edit/update action on this menu." alt="Updated" src="' . url('assets/images/updated.png') . '">';
            }
            if ($days < Config::get('Constant.DEFAULT_DAYS')) {
                $statusdata = '<img border="0" title="There was new action on this menu." alt="New" src="' . url('assets/images/new.png') . '">';
            }
        }
        $status = '';
        if ($value->chrDraft == 'D') {
            $status .= Config::get('Constant.DRAFT_LIST') . ' ';
        }
        if ($value->chrAddStar == 'Y') {
            $status .= Config::get('Constant.APPROVAL_LIST') . ' ';
        }
        $records = array(
            ($hasRecords == 0) ? '<input type="checkbox" name="delete" class="chkDelete" value="' . $value->id . '"> ' : $checkbox,
            '<div class="pages_title_div_row">' . $title . ' ' . $status . $statusdata . '</div>',
            $startDate,
            $endDate,
            $webHits,
            $log,
            $value->intDisplayOrder,
        );
        return $records;
    }

    /**
     * This method delete multiples faqcategory
     * @return  true/false
     * @since   2017-07-15
     * @author  NetQuick
     */
    public function DeleteRecord(Request $request) {
        $value = Request::input('value');
        $data['ids'] = Request::input('ids');
        if (File::exists(app_path() . '/Comments.php') != null || File::exists(base_path() . '/packages/Powerpanel/Workflow/src/Models/Comments.php') != null) {
            Comments::deleteComments($data['ids'], Config::get('Constant.MODULE.MODEL_NAME'));
        }
        $moduleHaveFields = ['chrMain', 'chrIsPreview'];
        $update = MyLibrary::deleteMultipleRecords($data, $moduleHaveFields, $value, 'Powerpanel\FaqCategory\Models\FaqCategory');
        foreach ($update as $ids) {
            $ignoreDeleteScope = true;
            $Deleted_Record = FaqCategory::getRecordById($ids, $ignoreDeleteScope);
            $Cnt_Letest = FaqCategory::getRecordCount_letest($Deleted_Record['fkMainRecord'], $Deleted_Record['id']);
            if ($Cnt_Letest <= 0) {
                $updateLetest = [
                    'chrAddStar' => 'N',
                ];
                $whereConditionsApprove = ['id' => $Deleted_Record['fkMainRecord']];
                CommonModel::updateRecords($whereConditionsApprove, $updateLetest, false, 'Powerpanel\FaqCategory\Models\FaqCategory');
                if (File::exists(app_path() . '/Workflow.php') != null || File::exists(base_path() . '/packages/Powerpanel/Workflow/src/Models/Workflow.php') != null) {
                    $where = [];
                    $flowData = [];
                    $flowData['dtNo'] = Config::get('Constant.SQLTIMESTAMP');
                    $where['fkModuleId'] = Config::get('Constant.MODULE.ID');
                    $where['fkRecordId'] = $Deleted_Record['fkMainRecord'];
                    $where['dtNo'] = 'null';
                    WorkflowLog::updateRecord($flowData, $where);
                }
            }
            if ($Deleted_Record['chrMain'] == "Y") {
                $whereConditions = [
                    'intRecordId' => $ids,
                    'intfkModuleId' => Config::get('Constant.MODULE.ID')
                ];
                $updateMenuFields = [
                    'chrPublish' => 'N',
                    'chrDelete' => 'Y',
                    'chrActive' => 'N',
                ];
                CommonModel::updateRecords($whereConditions, $updateMenuFields, false, '\\Powerpanel\\Menu\\Models\\Menu');
                if ($value != "P" && $value != "F" && $value != "D" && $value != "A") {
                    Alias::where('id', $Deleted_Record['intAliasId'])
                            ->where('intFkModuleCode', Config::get('Constant.MODULE.ID'))
                            ->delete();
                }
            }
        }
        self::flushCache();
        echo json_encode($update);
        exit;
    }

    /**
     * This method reorders banner position
     * @return  Banner index view data
     * @since   2016-10-26
     * @author  NetQuick
     */
    public function reorder() {
        $order = Request::input('order');
        $exOrder = Request::input('exOrder');
        MyLibrary::swapOrder($order, $exOrder, 'Powerpanel\FaqCategory\Models\FaqCategory');
        self::flushCache();
    }

    /**
     * This method handels swapping of available order record while adding
     * @param   order
     * @return  order
     * @since   2016-10-21
     * @author  NetQuick
     */
    public static function swap_order_add($order = null) {
        $response = false;
        $isCustomizeModule = true;
        $moduleHaveFields = ['chrMain', 'chrIsPreview'];
        if ($order != null) {
            $response = MyLibrary::swapOrderAdd($order, $isCustomizeModule, $moduleHaveFields, 'Powerpanel\FaqCategory\Models\FaqCategory');
            self::flushCache();
        }
        return $response;
    }

    /**
     * This method handels swapping of available order record while editing
     * @param   order
     * @return  order
     * @since   2016-12-23
     * @author  NetQuick
     */
    public static function swap_order_edit($order = null, $id = null) {
        $isCustomizeModule = true;
        $moduleHaveFields = ['chrMain', 'chrIsPreview'];
        MyLibrary::swapOrderEdit($order, $id, $isCustomizeModule, $moduleHaveFields, 'Powerpanel\FaqCategory\Models\FaqCategory');
        self::flushCache();
    }

    /**
     * This method destroys Banner in multiples
     * @return  Banner index view
     * @since   2016-10-25
     * @author  NetQuick
     */
    public function publish(Request $request) {
        $requestArr = Request::all();
//        $request = (object) $requestArr;
        $val = Request::get('val');
        $alias = Request::input('alias');
        $update = MyLibrary::setPublishUnpublish($alias, $val, 'Powerpanel\FaqCategory\Models\FaqCategory');
        self::flushCache();
        echo json_encode($update);
        exit;
    }

    public function recordHistory($data = false) {
        $returnHtml = '';
        $startDate = date('' . Config::get('Constant.DEFAULT_DATE_FORMAT') . ' ' . Config::get('Constant.DEFAULT_TIME_FORMAT') . '', strtotime($data->dtDateTime));
        $endDate = !empty($data->dtEndDateTime) ? date('' . Config::get('Constant.DEFAULT_DATE_FORMAT') . ' ' . Config::get('Constant.DEFAULT_TIME_FORMAT') . '', strtotime($data->dtEndDateTime)) : 'No Expiry';
        $returnHtml .= '<table class="new_table_desing table table-striped table-bordered table-hover">
				<thead>
						<tr>
								<th align="center">' . trans("faq-category::template.common.title") . '</th>
								<th align="center">' . trans("faq-category::template.common.description") . '</th>
                                                                <th align="center">Start date</th>
                                                                <th align="center">End date</th>
                                                                 <th align="center">Meta Title</th>
                                                                 <th align="center">Meta Description</th>
								<th align="center">' . trans("faq-category::template.common.order") . '</th>
								<th align="center">' . trans("faq-category::template.common.publish") . '</th>
						</tr>
				</thead>
				<tbody>
						<tr>
								<td align="center">' . stripslashes($data->varTitle) . '</td>
                                                                <td align="center">' . $data->txtDescription . '</td>
                                                                <td align="center">' . $startDate . '</td>
                                                                <td align="center">' . $endDate . '</td>
                                                                    <td align="center">' . $data->varMetaTitle . '</td>
                                                                <td align="center">' . $data->varMetaDescription . '</td>
								<td align="center">' . ($data->intDisplayOrder) . '</td>
								<td align="center">' . $data->chrPublish . '</td>
						</tr>
				</tbody>
				</table>';
        return $returnHtml;
    }

    public function newrecordHistory($data = false, $newdata = false) {
        if ($data->varTitle != $newdata->varTitle) {
            $titlecolor = 'style="background-color:#f5efb7"';
        } else {
            $titlecolor = '';
        }
        if ($data->txtDescription != $newdata->txtDescription) {
            $desccolor = 'style="background-color:#f5efb7"';
        } else {
            $desccolor = '';
        }
        if ($data->chrPublish != $newdata->chrPublish) {
            $Publishcolor = 'style="background-color:#f5efb7"';
        } else {
            $Publishcolor = '';
        }
        if ($data->dtDateTime != $newdata->dtDateTime) {
            $DateTimecolor = 'style="background-color:#f5efb7"';
        } else {
            $DateTimecolor = '';
        }
        if ($data->dtEndDateTime != $newdata->dtEndDateTime) {
            $EndDateTimecolor = 'style="background-color:#f5efb7"';
        } else {
            $EndDateTimecolor = '';
        }
        if ($data->intDisplayOrder != $newdata->intDisplayOrder) {
            $ordercolor = 'style="background-color:#f5efb7"';
        } else {
            $ordercolor = '';
        }
        if ($data->varMetaDescription != $newdata->varMetaDescription) {
            $varMetaDescriptioncolor = 'style="background-color:#f5efb7"';
        } else {
            $varMetaDescriptioncolor = '';
        }
        if ($data->varMetaTitle != $newdata->varMetaTitle) {
            $varMetaTitlecolor = 'style="background-color:#f5efb7"';
        } else {
            $varMetaTitlecolor = '';
        }
        $returnHtml = '';
        $startDate = date('' . Config::get('Constant.DEFAULT_DATE_FORMAT') . ' ' . Config::get('Constant.DEFAULT_TIME_FORMAT') . '', strtotime($newdata->dtDateTime));
        $endDate = !empty($newdata->dtEndDateTime) ? date('' . Config::get('Constant.DEFAULT_DATE_FORMAT') . ' ' . Config::get('Constant.DEFAULT_TIME_FORMAT') . '', strtotime($newdata->dtEndDateTime)) : 'No Expiry';
        $returnHtml .= '<table class="new_table_desing table table-striped table-bordered table-hover">
				<thead>
						<tr>
								<th align="center">' . trans("faq-category::template.common.title") . '</th>
								<th align="center">' . trans("faq-category::template.common.description") . '</th>
                                                                <th align="center">Start date</th>
                                                                <th align="center">End date</th>
                                                                <th align="center">Meta Title</th>
                                                                <th align="center">Meta Description</th>
								<th align="center">' . trans("faq-category::template.common.order") . '</th>
								<th align="center">' . trans("faq-category::template.common.publish") . '</th>
						</tr>
				</thead>
				<tbody>
						<tr>
								<td align="center" ' . $titlecolor . '>' . stripslashes($newdata->varTitle) . '</td>
                                                                <td align="center" ' . $desccolor . '>' . $newdata->txtDescription . '</td>
                                                                <td align="center" ' . $DateTimecolor . '>' . $startDate . '</td>
                                                                <td align="center" ' . $EndDateTimecolor . '>' . $endDate . '</td>
                                                                <td align="center" ' . $varMetaTitlecolor . '>' . $newdata->varMetaTitle . '</td>
                                                                <td align="center" ' . $varMetaDescriptioncolor . '>' . $newdata->varMetaDescription . '</td>
								<td align="center" ' . $ordercolor . '>' . ($newdata->intDisplayOrder) . '</td>
								<td align="center" ' . $Publishcolor . '>' . $newdata->chrPublish . '</td>
						</tr>
				</tbody>
				</table>';
        return $returnHtml;
    }

    /**
     * This method stores faqcategory modifications
     * @return  View
     * @since   2017-11-10
     * @author  NetQuick
     */
    public function addPreview(Guard $auth) {
        $data = Request::all();
        $rules = array(
            'title' => 'required|max:160|handle_xss|no_url',
            'varMetaTitle' => 'required|max:500|handle_xss|no_url',
            'varMetaDescription' => 'required|max:500|handle_xss|no_url',
            'alias' => 'required',
            'chrMenuDisplay' => 'required',
            'order' => 'required|greater_than_zero|handle_xss|no_url',
        );
        $actionMessage = trans('faq-category::template.common.oppsSomethingWrong');
        $messsages = array(
        );
        $validator = Validator::make($data, $rules, $messsages);
        $faqCategoryArr = [];
        if (Config::get('Constant.DEFAULT_VISUAL') == 'Y') {
            if ($data['section'] != '[]') {
                $vsection = $data['section'];
            } else {
                $vsection = '';
            }
        } else {
            $vsection = $data['description'];
        }
        $faqCategoryArr['varTitle'] = stripslashes(trim($data['title']));
        $faqCategoryArr['dtDateTime'] = !empty($data['start_date_time']) ? date('Y-m-d H:i:s', strtotime($data['start_date_time'])) : date('Y-m-d H:i:s');
        $faqCategoryArr['dtEndDateTime'] = !empty($data['end_date_time']) ? date('Y-m-d H:i:s', strtotime($data['end_date_time'])) : null;
        $faqCategoryArr['txtDescription'] = $vsection;
        $faqCategoryArr['varMetaTitle'] = stripslashes(trim($data['varMetaTitle']));
        $faqCategoryArr['varMetaDescription'] = stripslashes(trim($data['varMetaDescription']));
        $faqCategoryArr['chrPublish'] = isset($data['chrMenuDisplay']) ? $data['chrMenuDisplay'] : 'Y';
        $faqCategoryArr['chrIsPreview'] = 'Y';
        $id = $data['previewId'];
        if (is_numeric($id)) { #Edit post Handler=======
            if ($data['oldAlias'] != $data['alias']) {
                Alias::updateAlias($data['oldAlias'], $data['alias']);
            }
            $faqCategory = FaqCategory::getRecordForLogById($id);
            $whereConditions = ['id' => $faqCategory->id];
            $update = CommonModel::updateRecords($whereConditions, $faqCategoryArr, false, 'Powerpanel\FaqCategory\Models\FaqCategory');
            if ($update) {
                if (!empty($id)) {
                    $logArr = MyLibrary::logData($faqCategory->id);
                    if (Auth::user()->can('log-advanced')) {
                        $newFaqCategoryObj = FaqCategory::getRecordForLogById($faqCategory->id);
                        $oldRec = $this->recordHistory($faqCategory);
                        $newRec = $this->recordHistory($newFaqCategoryObj);
                        $logArr['old_val'] = $oldRec;
                        $logArr['new_val'] = $newRec;
                    }
                    $logArr['varTitle'] = stripslashes(trim($data['title']));
                    Log::recordLog($logArr);
                    if (Auth::user()->can('recent-updates-list')) {
                        if (!isset($newFaqCategoryObj)) {
                            $newFaqCategoryObj = FaqCategory::getRecordForLogById($faqCategory->id);
                        }
                        $notificationArr = MyLibrary::notificationData($faqCategory->id, $newFaqCategoryObj);
                        RecentUpdates::setNotification($notificationArr);
                    }
                    self::flushCache();
                    $actionMessage = trans('faq-category::template.faqCategoryModule.updateMessage');
                }
            }
        } else { #Add post Handler=======
            $faqCategoryArr['intAliasId'] = MyLibrary::insertAlias($data['alias'], false, 'Y');
            $id = CommonModel::addRecord($faqCategoryArr, 'Powerpanel\FaqCategory\Models\FaqCategory');
        }
        return json_encode(array('status' => $id, 'alias' => $data['alias'], 'message' => trans('faq-category::template.pageModule.pageUpdate')));
    }

    public static function flushCache() {
        Cache::tags('FaqCategory')->flush();
    }

    public function tableData_tab1($value = false) {
        $Hits = Pagehit::where('fkIntAliasId', $value->intAliasId)->count();
        $webHits = '';
        if ($Hits > 0) {
            $webHits .= '<a data-toggle="modal" href="#" onclick=\'HitsPopup("' . $value->id . '","' . $value->intAliasId . '","' . $value->varTitle . '","P")\'>' . $Hits . '</a>
                    <div class="new_modal modal fade" id="desc_' . $value->id . '_P" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog" style="margin: 0 auto;display:table;width: 100%;height:100%;max-width: 1000px;">
                        <div class="modal-vertical">
                        <div class="modal-content">
                    <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                    <h3 class="modal-title">Hits Report</h3>
                    </div>
                    <div class="modal-body">
                    <div id="webdata_' . $value->id . '_P"></div>
                    </div>
                    </div>
                    </div>
                    </div>
                    </div>';
        } else {
            $webHits .= '0';
        }
        $hasRecords = Faq::getCountById($value->id);
        $details = '';
        $publish_action = '';
        $titleData_delete = "";
        $titleData_publish = "";
        if ($hasRecords > 0) {
            $titleData_delete .= 'This category is selected in ' . trans("faq-category::template.sidebar.faq") . ', so it can&#39;t be deleted.';
            $titleData_publish = 'This category is selected in ' . trans("faq-category::template.sidebar.faq") . ', so it can&#39;t be deleted.';
        }
        $checkbox = '<a href="javascript:;" data-toggle="tooltip" data-placement="right" data-toggle="tooltip" data-original-title="' . $titleData_delete . '" title="' . $titleData_delete . '"><i style="color:red" class="fa fa-exclamation-triangle"></i></a>';
        $checkbox_publish = '<a href="javascript:;" data-toggle="tooltip" data-placement="right" data-toggle="tooltip" data-original-title="' . $titleData_publish . '" title="' . $titleData_publish . '"><i style="color:red" class="fa fa-exclamation-triangle"></i></a>';
        if (Auth::user()->can('faq-category-edit')) {
            $details .= '<a class="" title="' . trans("faq-category::template.common.edit") . '" href="' . route('powerpanel.faq-category.edit', array('alias' => $value->id)) . '"><i class="fa fa-pencil"></i></a>';
        }
        if (Auth::user()->can('faq-category-delete') && $hasRecords == 0 || (isset($this->currentUserRoleData->chrIsAdmin) && $this->currentUserRoleData->chrIsAdmin == 'Y')) {
            if (Config::get('Constant.DEFAULT_TRASH') == 'Y') {
                $details .= '<a title = "' . trans('faq-category::template.common.delete') . '" class="delete-grid" onclick = \'Trashfun("' . $value->id . '")\' data-controller = "FaqCategory" data-alias = "' . $value->id . '" data-tab = "A"><i class = "fa fa-times"></i></a>';
            } else {
                $details .= '<a class = "delete" title = "' . trans('faq-category::template.common.delete') . '" data-controller = "FaqCategory" data-alias = "' . $value->id . '" data-tab = "A"><i class = "fa fa-times"></i></a>';
            }
        }

        $startDate = date('' . Config::get('Constant.DEFAULT_DATE_FORMAT') . ' ' . Config::get('Constant.DEFAULT_TIME_FORMAT') . '', strtotime($value->dtDateTime));
        $endDate = !empty($value->dtEndDateTime) ? date('' . Config::get('Constant.DEFAULT_DATE_FORMAT') . ' ' . Config::get('Constant.DEFAULT_TIME_FORMAT') . '', strtotime($value->dtEndDateTime)) : 'No Expiry';
        $Quickedit_startDate = date('Y-m-d H:i', strtotime($value->dtDateTime));
        $Quickedit_endDate = !empty($value->dtEndDateTime) ? date('Y-m-d H:i', strtotime($value->dtEndDateTime)) : 'No Expiry';
        if (Auth::user()->can('faq-category-reviewchanges')) {
            $update = "<a title=\"Click here to see all approval records.\" class=\"icon_title1\" style=\"margin-right: 5px;\" onclick=\"javascript:expandcollapsepanel(this ,'tasklisting" . $value->id . "', 'mainsingnimg" . $value->id . "'," . $value->id . ")\"><i id=\"mainsingnimg" . $value->id . "\" class=\"la la-plus-square\"></i></a>";
            $rollback = "<a title=\"Click here to see all approved records to rollback.\"  class=\"icon_title2\" style=\"margin-right: 5px;\" onclick=\"javascript:expandcollapsepanel_rolback(this ,'tasklisting_rollback" . $value->id . "', 'mainsingnimg_rollback" . $value->id . "'," . $value->id . ")\"><i id=\"mainsingnimg_rollback" . $value->id . "\" class=\"la la-history\"></i></a>";
        } else {
            $update = '';
            $rollback = '';
        }
        if (Auth::user()->can('faq-category-reviewchanges') && $value->chrAddStar == 'Y') {
            $star = 'addhiglight';
        } else {
            $star = '';
        }
        $title = $value->varTitle;
        if (Auth::user()->can('faq-category-edit')) {
            if ($value->chrDraft == 'D' || $value->chrAddStar == 'Y') {
                $viewlink = url('/previewpage?url=' . MyLibrary::getFrontUri('faq-category')['uri'] . '/' . $value->id . '/preview/detail');
                $linkviewLable = "Preview";
            } else {
                $viewlink = MyLibrary::getFrontUri('faq-category', $value->id)['uri'];
                $linkviewLable = "View";
            }
            //$frontViewLink = MyLibrary::getFrontUri('faq-category', $value->id)['uri'];
            if ($value->chrLock != 'Y') {
                $title = '<div class="quick_edit"><a href = "' . route('powerpanel.faq-category.edit', array('alias' => $value->id)) . '?tab=A">' . $value->varTitle . '</a> <div class="quick_edit_menu">
                            <span><a href="' . route('powerpanel.faq-category.edit', array('alias' => $value->id)) . '?tab=A" title="Edit">Edit</a></span>';
                if ($hasRecords == 0) {
                    if (Config::get('Constant.DEFAULT_TRASH') == 'Y') {
                        $title .= '<span><a title = "Trash" href = \'javascript:;\' onclick=\'Trashfun("' . $value->id . '")\' class="red" data-tab="A">Trash</a></span>';
                    }
                }
                $title .= '<span><a href="' . $viewlink . '" target="_blank" title="' . $linkviewLable . '" >' . $linkviewLable . '</a></span></div></div>';
            } else {
                if (auth()->user()->id != $value->LockUserID) {
                    if (isset($this->currentUserRoleData->chrIsAdmin) && $this->currentUserRoleData->chrIsAdmin == 'Y') {
                        $title = '<div class="quick_edit"><a href = "' . route('powerpanel.faq-category.edit', array('alias' => $value->id)) . '?tab=A">' . $value->varTitle . '</a> <div class="quick_edit_menu">
	                            <span><a href="' . route('powerpanel.faq-category.edit', array('alias' => $value->id)) . '?tab=A" title="Edit">Edit</a></span></div></div>';
                    } else {
                        $title = '<div class="quick_edit"><a href = "javascript:;">' . $value->varTitle . '</a></div>';
                    }
                } else {
                    $title = '<div class="quick_edit"><a href = "' . route('powerpanel.faq-category.edit', array('alias' => $value->id)) . '?tab=A">' . $value->varTitle . '</a> <div class="quick_edit_menu">
	                            <span><a href="' . route('powerpanel.faq-category.edit', array('alias' => $value->id)) . '?tab=A" title="Edit">Edit</a></span></div></div>';
                }
            }
        }
        $pubbtn = $value->chrPageActive;
        $pbtn = '';
        if (Config::get('Constant.DEFAULT_VISIBILITY') == 'Y') {
            if ($pubbtn == 'PU') {
                $pbtn = '<div class="pub_status publicdiv" data-toggle="tooltip" title="Public"><span>Public</span></div>';
            } else if ($pubbtn == 'PR') {
                $pbtn = '<div class="pub_status privatediv" data-toggle="tooltip" title="Private"><span>Private</span></div>';
            } else if ($pubbtn == 'PP') {
                $pbtn = '<div class="pub_status passworddiv" data-toggle="tooltip" title="Password Protected"><span>Password Protected</span></div>';
            } else {
                $pbtn = '<div class="pub_status publicdiv" data-toggle="tooltip" title="Public"><span>Public</span></div>';
            }
        }
        if (Config::get('Constant.DEFAULT_FAVORITE') == 'Y') {
            $Favorite_array = explode(",", $value->FavoriteID);
            if (in_array(auth()->user()->id, $Favorite_array)) {
                $Class = 'la la-star';
                $Favorite = '<a class="star_icon_div" href="javascript:;" onclick="GetFavorite(' . $value->id . ',\'N\',\'A\')"><i class="' . $Class . '"></i></a>';
            } else {
                $Class = 'la la-star-o';
                $Favorite = '<a class="star_icon_div" href="javascript:;" onclick="GetFavorite(' . $value->id . ',\'Y\',\'A\')"><i class="' . $Class . '"></i></a>';
            }
        } else {
            $Favorite = '';
        }
        $First_td = '<div class="star_box">' . $Favorite . $pbtn . '</div>';
        $logurl = url('powerpanel/log?id=' . $value->id . '&mid=' . Config::get('Constant.MODULE.ID'));
        if ($details == "") {
            $details = "---";
        } else {
            $details = $details;
        }
        $log = '';
        if ($value->chrLock != 'Y') {
            $log .= $details;
            if (Auth::user()->can('log-list')) {
                $log .= "<a title=\"Log History\" href=\"$logurl\"><i class=\"fa fa-clock-o\"></i></a>";
            }
        } else {
            if (auth()->user()->id != $value->LockUserID) {
                $lockedUserData = User::getRecordById($value->LockUserID, true);
                $lockedUserName = 'someone';
                if (!empty($lockedUserData)) {
                    $lockedUserName = $lockedUserData->name;
                }
                if (isset($this->currentUserRoleData->chrIsAdmin) && $this->currentUserRoleData->chrIsAdmin == 'Y') {
                    $log .= '<a class="star_lock" onclick="GetUnLockData(' . $value->id . ',' . auth()->user()->id . ',' . Config::get('Constant.MODULE.ID') . ',1)" title="This record has been locked by ' . $lockedUserName . ', Click here to unlock."><i class="fa fa-lock"></i></a>';
                } else {

                    $log .= '<a class="star_lock" title="This record has been locked by ' . $lockedUserName . '."><i class="fa fa-lock"></i></a>';
                }
            } else {
                $log .= '<a class="star_lock" onclick="GetUnLockData(' . $value->id . ',' . auth()->user()->id . ',' . Config::get('Constant.MODULE.ID') . ',1)" title="Click here to unlock."><i class="fa fa-lock"></i></a>';
            }
        }
        $statusdata = '';
        if (method_exists($this->MyLibrary, 'count_days')) {
            $days = MyLibrary::count_days($value->created_at);
            $days_modified = MyLibrary::count_days($value->updated_at);
        } else {
            $days = '';
            $days_modified = '';
        }
        if ($days_modified < Config::get('Constant.DEFAULT_DAYS') && $days < Config::get('Constant.DEFAULT_DAYS')) {
            if ($days < Config::get('Constant.DEFAULT_DAYS')) {
                $statusdata = '<img border="0" title="There was new action on this menu." alt="New" src="' . url('assets/images/new.png') . '">';
            }
        } else {
            if ($days_modified < Config::get('Constant.DEFAULT_DAYS')) {
                $statusdata = '<img border="0" title="There was edit/update action on this menu." alt="Updated" src="' . url('assets/images/updated.png') . '">';
            }
            if ($days < Config::get('Constant.DEFAULT_DAYS')) {
                $statusdata = '<img border="0" title="There was new action on this menu." alt="New" src="' . url('assets/images/new.png') . '">';
            }
        }
        $status = '';
        if ($value->chrDraft == 'D') {
            $status .= Config::get('Constant.DRAFT_LIST') . ' ';
        }

        if (Auth::user()->can('banners-reviewchanges')) {
            $log .= "<a title='Rollback to previous version'  onclick=\"rollbackToPreviousVersion('" . $value->id . "');\"  class=\"log-grid\"><i class=\"fa fa-history\"></i></a>";
        }
        $records = array(
            $First_td,
            '<div class="pages_title_div_row">' . $update . $rollback . $title . $status . $statusdata . '</div>',
            $startDate,
            $endDate,
            $webHits,
            $log,
            $value->intDisplayOrder,
        );
        return $records;
    }

    public function getChildData() {
        $childHtml = "";
        $Cmspage_childData = "";
        $Cmspage_childData = FaqCategory::getChildGrid();
        $childHtml .= "<div class=\"producttbl\" style=\"\">";
        $childHtml .= "<table class=\"new_table_desing table table-striped table-bordered table-hover table-checkable dataTable\" id=\"email_log_datatable_ajax\">
			<tr role=\"row\">
			<th class=\"text-center\"></th>
                        <th class=\"text-center\">Title</th>
			<th class=\"text-center\">Date Submitted</th>
			<th class=\"text-center\">User</th>
			<th class=\"text-center\">Preview</th>
			<th class=\"text-center\">Edit</th>
			<th class=\"text-center\">Status</th>";
        $childHtml .= "         </tr>";
        if (count($Cmspage_childData) > 0) {
            foreach ($Cmspage_childData as $child_row) {
                $parentAlias = $child_row->alias->varAlias;
                $childHtml .= "<tr role=\"row\">";
                if ($child_row->chrApproved == 'N') {
                    $childHtml .= "<td><span class='mob_show_title'>&nbsp</span><input type=\"checkbox\" name=\"delete\" class=\"chkDelete\" value='" . $child_row->id . "'></td>";
                } else {
                    $childHtml .= "<td><span class='mob_show_title'>&nbsp</span><div class=\"checker\"><a href=\"javascript:;\" data-toggle=\"tooltip\" data-placement=\"right\" title=\"This is approved record, so can't be deleted.\"><i style=\"color:red\" class=\"fa fa-exclamation-triangle\"></i></a></div></td>";
                }
                $childHtml .= '<td class="text-center"><span class="mob_show_title">Title: </span>' . $child_row->varTitle . '</td>';
                $childHtml .= "<td class=\"text-center\"><span class='mob_show_title'>Date Submitted: </span>" . date('M d Y h:i A', strtotime($child_row->created_at)) . "</td>";
                $childHtml .= "<td class=\"text-center\"><span class='mob_show_title'>User: </span>" . CommonModel::getUserName($child_row->UserID) . "</td>";
                $previewlink = url('/previewpage?url=' . MyLibrary::getFrontUri('faq-category')['uri'] . '/' . $child_row->id . '/preview/detail');
                $childHtml .= "<td class=\"text-center\"><span class='mob_show_title'>Preview: </span><a class='icon_round' href=" . $previewlink . " target='_blank'><i class=\"fa fa-desktop\"></i></a></td>";
                if ($child_row->chrApproved == 'N') {
                    $childHtml .= "<td class=\"text-center\"><span class='mob_show_title'>Edit: </span><a class='icon_round' title='" . trans("faq-category::template.common.edit") . "' href='" . route('powerpanel.faq-category.edit', array('alias' => $child_row->id)) . "'>
							<i class='fa fa-pencil'></i></a></td>";
                } else {
                    $childHtml .= "<td class=\"text-center\"><span class='mob_show_title'>Edit: </span>-</td>";
                }
                if ($child_row->chrApproved == 'N') {
                    $childHtml .= "<td class=\"text-center\"><span class='mob_show_title'>Status: </span><a class=\"approve_icon_btn\" title='" . trans("faq-category::template.common.comments") . "' href=\"javascript:;\" onclick=\"loadModelpopup('" . $child_row->id . "','" . $child_row->UserID . "','" . Config::get('Constant.MODULE.MODEL_NAME') . "','" . $child_row->fkMainRecord . "')\"><i class=\"fa fa-comments\"></i> <span>Comment</span></a>    <a  class=\"approve_icon_btn\" onclick=\"update_mainrecord('" . $child_row->id . "','" . $child_row->fkMainRecord . "','" . $child_row->UserID . "','A');\" title='" . trans("faq-category::template.common.clickapprove") . "' href=\"javascript:;\"><i class=\"fa fa-check-square-o\"></i> <span>Approve</span></a></td>";
                } else {
                    $childHtml .= "<td class=\"text-center\"><span class='mob_show_title'>Status: </span><span class='mob_show_overflow'><i class=\"la la-check-circle\" style=\"font-size:30px;\"></i><span style=\"display:block\"><strong>Approved On: </strong>" . date('M d Y h:i A', strtotime($child_row->dtApprovedDateTime)) . "</span><span style=\"display:block\"><strong>Approved By: </strong>" . CommonModel::getUserName($child_row->intApprovedBy) . "</span></span></td>";
                }
                $childHtml .= "</tr>";
            }
        } else {
            $childHtml .= "<tr><td colspan='6'>No Records</td></tr>";
        }
        $childHtml .= "</tr></td></tr>";
        $childHtml .= "</tr>
						</table>";
        echo $childHtml;
        exit;
    }

    public function getChildData_rollback() {
        $child_rollbackHtml = "";
        $Cmspage_rollbackchildData = "";
        $Cmspage_rollbackchildData = FaqCategory::getChildrollbackGrid();
        $child_rollbackHtml .= "<div class=\"producttbl producttb2\" style=\"\">";
        $child_rollbackHtml .= "<table class=\"new_table_desing table table-striped table-bordered table-hover table-checkable dataTable\" id=\"email_log_datatable_ajax\">
																<tr role=\"row\">        
                                                                                                                                                <th class=\"text-center\">Title</th>
																		<th class=\"text-center\">Date</th>
																		<th class=\"text-center\">User</th>
																		<th class=\"text-center\">Preview</th>                                     
																		<th class=\"text-center\">Status</th>";
        $child_rollbackHtml .= "         </tr>";
        if (count($Cmspage_rollbackchildData) > 0) {
            foreach ($Cmspage_rollbackchildData as $child_rollbacrow) {
                $child_rollbackHtml .= "<tr role=\"row\">";
                $child_rollbackHtml .= '<td class="text-center"><span class="mob_show_title">Title: </span>' . $child_rollbacrow->varTitle . '</td>';
                $child_rollbackHtml .= "<td class=\"text-center\"><span class='mob_show_title'>Date: </span>" . date('M d Y h:i A', strtotime($child_rollbacrow->created_at)) . "</td>";
                $child_rollbackHtml .= "<td class=\"text-center\"><span class='mob_show_title'>User: </span>" . CommonModel::getUserName($child_rollbacrow->UserID) . "</td>";
                $previewlink = url('/previewpage?url=' . MyLibrary::getFrontUri('faq-category')['uri'] . '/' . $child_rollbacrow->id . '/preview/detail');
                $child_rollbackHtml .= "<td class=\"text-center\"><span class='mob_show_title'>Preview: </span><a class='icon_round' href=" . $previewlink . " target='_blank'><i class=\"fa fa-desktop\"></i></a></td>";
                if ($child_rollbacrow->chrApproved == 'Y') {
                    $child_rollbackHtml .= "<td class=\"text-center\"><span class='mob_show_title'>Status: </span><i class=\"la la-check-circle\" style=\"color: #1080F2;font-size:30px;\"></i></td>";
                } else {
                    // $child_rollbackHtml .= "<td class=\"text-center\"><span class='mob_show_title'>Status: </span><a onclick=\"update_mainrecord('" . $child_rollbacrow->id . "','" . $child_rollbacrow->fkMainRecord . "','" . $child_rollbacrow->UserID . "','R');\"  class=\"approve_icon_btn\">
					// 						<i class=\"fa fa-history\"></i>  <span>RollBack</span>
                    // 					</a></td>";
                    $child_rollbackHtml .= "<td class=\"text-center\"><span class=\"glyphicon glyphicon-minus\"></span></td>";
                }
                $child_rollbackHtml .= "</tr>";
            }
        } else {
            $child_rollbackHtml .= "<tr><td colspan='6'>No Records</td></tr>";
        }
        echo $child_rollbackHtml;
        exit;
    }

    public function ApprovedData_Listing(Request $request) {
        $requestArr = Request::all();
        $request = (object) $requestArr;
        $id = Request::post('id');
        $approvalid = Request::post('id');
        $main_id = Request::post('main_id');
        $flag = Request::post('flag');
        $approvalData = FaqCategory::getOrderOfApproval($id);
        $message = FaqCategory::approved_data_Listing($request);
        if (!empty($approvalData)) {
            self::swap_order_edit($approvalData->intDisplayOrder, $main_id);
        }
        $newCmsPageObj = FaqCategory::getRecordForLogById($main_id);
        $approval_obj = FaqCategory::getRecordForLogById($approvalid);
        if ($flag == 'R') {
            $restoredata = Config::get('Constant.ROLLBACK_RECORD');
        } else {
            if ($approval_obj->chrDraft == 'D') {
                $restoredata = Config::get('Constant.DRAFT_RECORD_APPROVED');
            } else {
                $restoredata = Config::get('Constant.RECORD_APPROVED');
            }
        }
        /* notification for user to record approved */
        $careers = FaqCategory::getRecordForLogById($id);
        if (method_exists($this->MyLibrary, 'userNotificationData')) {
            $userNotificationArr = MyLibrary::userNotificationData(Config::get('Constant.MODULE.ID'));
            $userNotificationArr['fkRecordId'] = $id;
            $userNotificationArr['txtNotification'] = 'Your request has been approved by ' . ucfirst(auth()->user()->name) . ' (' . ucfirst(Config::get('Constant.MODULE.NAME')) . ')';
            $userNotificationArr['fkIntUserId'] = Auth::user()->id;
            $userNotificationArr['chrNotificationType'] = 'A';
            $userNotificationArr['intOnlyForUserId'] = $careers->UserID;
            UserNotification::addRecord($userNotificationArr);
            /* notification for user to record approved */
        }
        $logArr = MyLibrary::logData($main_id, false, $restoredata);
        $logArr['varTitle'] = stripslashes($newCmsPageObj->varTitle);
        Log::recordLog($logArr);
        $where = [];
        $flowData = [];
        $flowData['dtYes'] = Config::get('Constant.SQLTIMESTAMP');
        $where['fkModuleId'] = Config::get('Constant.MODULE.ID');
        $where['fkRecordId'] = $main_id;
        $where['dtYes'] = 'null';
        WorkflowLog::updateRecord($flowData, $where);
        echo $message;
    }

    public function Get_Comments(Request $request) {
        $requestArr = Request::all();
        $request = (object) $requestArr;
        $templateData = Comments::get_comments($request);
        $Comments = "";
        if (count($templateData) > 0) {
            foreach ($templateData as $row_data) {
                if ($row_data->Fk_ParentCommentId == 0) {
                    $Comments .= '<li><p>' . nl2br($row_data->varCmsPageComments) . '</p><span class = "date">' . CommonModel::getUserName($row_data->intCommentBy) . ' ' . date('M d Y h:i A', strtotime($row_data->created_at)) . '</span></li>';
                    $UserComments = Comments::get_usercomments($row_data->id);
                    foreach ($UserComments as $row_comments) {
                        $Comments .= '<li class="user-comments"><p>' . nl2br($row_comments->varCmsPageComments) . '</p><span class = "date">' . CommonModel::getUserName($row_comments->UserID) . ' ' . date('M d Y h:i A', strtotime($row_comments->created_at)) . '</span></li>';
                    }
                }
            }
        } else {
            $Comments .= '<li><p>No Comments yet.</p></li>';
        }
        echo $Comments;
        exit;
    }

    public function getAllCategory() {
        $records = FaqCategory::getAllCategory();
        $opt = '<option value="">Select Category</option>';
        foreach ($records as $record) {
            $opt .= '<option value="' . $record->id . '">' . $record->varTitle . '</option>';
        }
        return $opt;
    }

    public function get_builder_list() {
        $records = FaqCategory::getAllCategory();
        $opt = '<option value="">Category</option>';
        foreach ($records as $record) {
            $opt .= '<option value="' . $record->id . '">' . $record->varTitle . '</option>';
        }
        return $opt;
    }

    public function rollBackRecord(Request $request) {

        $message = 'Oops! Something went wrong';
        $requestArr = Request::all();
        $request = (object) $requestArr;
        
        $previousRecord = FaqCategory::getPreviousRecordByMainId($request->id);
        if(!empty($previousRecord)) {

            $main_id = $previousRecord->fkMainRecord;
            $request->id = $previousRecord->id;
            $request->main_id = $main_id;

            $message = FaqCategory::approved_data_Listing($request);
            
            $newBlogObj = FaqCategory::getRecordForLogById($main_id);
            $restoredata = Config::get('Constant.ROLLBACK_RECORD');

            /* notification for user to record approved */
            $blogs = FaqCategory::getRecordForLogById($previousRecord->id);
            if (method_exists($this->MyLibrary, 'userNotificationData')) {
                $userNotificationArr = MyLibrary::userNotificationData(Config::get('Constant.MODULE.ID'));
                $userNotificationArr['fkRecordId'] = $previousRecord->id;
                $userNotificationArr['txtNotification'] = 'Your request has been approved by ' . ucfirst(auth()->user()->name) . ' (' . ucfirst(Config::get('Constant.MODULE.NAME')) . ')';
                $userNotificationArr['fkIntUserId'] = Auth::user()->id;
                $userNotificationArr['chrNotificationType'] = 'A';
                $userNotificationArr['intOnlyForUserId'] = $blogs->UserID;
                UserNotification::addRecord($userNotificationArr);
            }
            /* notification for user to record approved */

            $logArr = MyLibrary::logData($main_id, false, $restoredata);
            $logArr['varTitle'] = stripslashes($newBlogObj->varTitle);
            Log::recordLog($logArr);
            $where = [];
            $flowData = [];
            $flowData['dtYes'] = Config::get('Constant.SQLTIMESTAMP');
            $where['fkModuleId'] = Config::get('Constant.MODULE.ID');
            $where['fkRecordId'] = $main_id;
            $where['dtYes'] = 'null';
            WorkflowLog::updateRecord($flowData, $where);
        }
        echo $message;
    }

}
