<?php

namespace Powerpanel\BlogCategory\Controllers\Powerpanel;

use App\Alias;
use App\CommonModel;
use App\Helpers\AddCategoryAjax;
use App\Helpers\FrontPageContent_Shield;
use App\Helpers\MyLibrary;
use App\Http\Controllers\PowerpanelController;
use App\Log;
use App\Modules;
use App\Pagehit;
use App\RecentUpdates;
use App\User;
use App\UserNotification;
use Auth;
use Cache;
use Config;
use DB;
use File;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Request;
use Powerpanel\BlogCategory\Models\BlogCategory;
use Powerpanel\Blogs\Models\Blogs;
use Powerpanel\CmsPage\Models\CmsPage;
use Powerpanel\RoleManager\Models\Role_user;
use Powerpanel\Workflow\Models\Comments;
use Powerpanel\Workflow\Models\Workflow;
use Powerpanel\Workflow\Models\WorkflowLog;
use Validator;

class BlogCategoryController extends PowerpanelController
{

    public $catModule;

    public function __construct()
    {
        parent::__construct();
        if (isset($_COOKIE['locale'])) {
            app()->setLocale($_COOKIE['locale']);
        }
        $this->MyLibrary = new MyLibrary();
        $this->CommonModel = new CommonModel();
        $this->Alias = new Alias();
    }

    /**
     * This method handels load process of Blogcategory
     * @return  View
     * @since   2017-11-10
     * @author  NetQuick
     */
    public function index()
    {
        $userIsAdmin = false;
        if (isset($this->currentUserRoleData) && !empty($this->currentUserRoleData)) {
            if ($this->currentUserRoleData->chrIsAdmin == 'Y') {
                $userIsAdmin = true;
            }
        } else {
            $userIsAdmin = true;
        }
        $iTotalRecords = BlogCategory::getRecordCount();
        $draftTotalRecords = BlogCategory::getRecordCountforListDarft(false, true, $userIsAdmin, array());
        $trashTotalRecords = BlogCategory::getRecordCountforListTrash();
        $favoriteTotalRecords = BlogCategory::getRecordCountforListFavorite();
        $NewRecordsCount = BlogCategory::getNewRecordsCount();
        $this->breadcrumb['title'] = trans('blogcategory::template.blogCategoryModule.manageBlogCategory');
        $breadcrumb = $this->breadcrumb;
        if (method_exists($this->CommonModel, 'GridColumnData')) {
            $settingdata = CommonModel::GridColumnData(Config::get('Constant.MODULE.ID'));
            $settingarray = array();
            foreach ($settingdata as $sdata) {
                $settingarray[$sdata->chrtab][] = $sdata->columnid;
            }
        } else {
            $settingarray = '';
        }
        $settingarray = json_encode($settingarray);
        return view('blogcategory::powerpanel.index', compact('iTotalRecords', 'breadcrumb', 'NewRecordsCount', 'userIsAdmin', 'draftTotalRecords', 'trashTotalRecords', 'favoriteTotalRecords', 'settingarray'));
    }

    /**
     * This method loads Blogcategory edit view
     * @param   Alias of record
     * @return  View
     * @since   2017-11-10
     * @author  NetQuick
     */
    public function edit($id = false)
    {
        $hasRecords = 0;
        $documentManager = true;
        $userIsAdmin = false;
        if (isset($this->currentUserRoleData) && !empty($this->currentUserRoleData)) {
            if ($this->currentUserRoleData->chrIsAdmin == 'Y') {
                $userIsAdmin = true;
            }
        } else {
            $userIsAdmin = true;
        }
        if (!is_numeric($id)) {
            $this->breadcrumb['title'] = trans('blogcategory::template.blogCategoryModule.addBlogCategory');
            $this->breadcrumb['module'] = trans('blogcategory::template.blogCategoryModule.manageBlogCategory');
            $this->breadcrumb['url'] = 'powerpanel/blog-category';
            $this->breadcrumb['inner_title'] = trans('blogcategory::template.blogCategoryModule.addBlogCategory');
            $breadcrumb = $this->breadcrumb;
            $data = compact('documentManager', 'breadcrumb', 'userIsAdmin', 'hasRecords');
        } else {
            $documentManager = true;
            $blogCategory = BlogCategory::getRecordById($id);
            if (empty($blogCategory)) {
                return redirect()->route('powerpanel.blog-category.add');
            }
            if ($blogCategory->fkMainRecord != '0') {
                $blogCategory_highLight = BlogCategory::getRecordById($blogCategory->fkMainRecord);
                $templateData['blogCategory_highLight'] = $blogCategory_highLight;
                $metaInfo_highLight['varMetaTitle'] = $blogCategory_highLight['varMetaTitle'];
                $metaInfo_highLight['varMetaDescription'] = $blogCategory_highLight['varMetaDescription'];
                $display_publish = $blogCategory_highLight['chrPublish'];
                $hasRecords = Blogs::getCountById($blogCategory->fkMainRecord);
            } else {
                $templateData['blogCategory_highLight'] = "";
                $metaInfo_highLight['varMetaTitle'] = "";
                $metaInfo_highLight['varMetaDescription'] = "";
                $display_publish = '';
                $blogCategory_highLight = '';
                $hasRecords = Blogs::getCountById($blogCategory->id);
            }
            $metaInfo = array('varMetaTitle' => $blogCategory->varMetaTitle,
                'varMetaDescription' => $blogCategory->varMetaDescription);
            $this->breadcrumb['title'] = trans('blogcategory::template.blogCategoryModule.editBlogCategory') . ' - ' . $blogCategory->varTitle;
            $this->breadcrumb['module'] = trans('blogcategory::template.blogCategoryModule.manageBlogCategory');
            $this->breadcrumb['url'] = 'powerpanel/blog-category';
            $this->breadcrumb['inner_title'] = trans('blogcategory::template.blogCategoryModule.editBlogCategory') . ' - ' . $blogCategory->varTitle;
            $breadcrumb = $this->breadcrumb;
            if (method_exists($this->MyLibrary, 'getModulePageAliasByModuleName')) {
                $categorypagereocrdlink = MyLibrary::getModulePageAliasByModuleName('blog-category');
            }
            if (!empty($categorypagereocrdlink)) {
                $varURL = $categorypagereocrdlink . '/' . $blogCategory->alias->varAlias;
            } else {
                $varURL = $blogCategory->alias->varAlias;
            }
            $metaInfo['varURL'] = $varURL;
            $data = compact('blogCategory', 'documentManager', 'metaInfo', 'breadcrumb', 'blogCategory_highLight', 'metaInfo_highLight', 'display_publish', 'userIsAdmin', 'hasRecords');
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
        } else {
            $data['chrNeedAddPermission'] = 'N';
            $data['charNeedApproval'] = 'N';
        }
        //End Button Name Change For User Side
        $data['userIsAdmin'] = $userIsAdmin;
        return view('blogcategory::powerpanel.actions', $data);
    }

    /**
     * This method stores blogcategory modifications
     * @return  View
     * @since   2017-11-10
     * @author  NetQuick
     */
    public function handlePost(Request $request)
    {
        $approval = false;
        $userIsAdmin = false;
        if (isset($this->currentUserRoleData) && !empty($this->currentUserRoleData)) {
            if ($this->currentUserRoleData->chrIsAdmin == 'Y') {
                $userIsAdmin = true;
            }
        }
        $data = Request::input();
        $rules = array(
            'title' => 'required|max:160|handle_xss|no_url',
            'varMetaTitle' => 'required|max:500|handle_xss|no_url',
            'varMetaDescription' => 'required|max:500|handle_xss|no_url',
            'alias' => 'required',
            'order' => 'required|greater_than_zero|handle_xss|no_url',
        );
        $actionMessage = trans('blogcategory::template.common.oppsSomethingWrong');
        $messsages = array(
            'title.required' => 'Name field is required.',
            'order.required' => trans('blogcategory::template.blogCategoryModule.displayOrder'),
            'order.greater_than_zero' => trans('blogcategory::template.blogCategoryModule.displayGreaterThan'),
            'varMetaTitle.required' => trans('blogcategory::template.blogCategoryModule.metaTitle'),
            'varMetaDescription.required' => trans('blogcategory::template.blogCategoryModule.metaDescription'),
        );
        $validator = Validator::make($data, $rules, $messsages);
        if ($validator->passes()) {
            $module = Modules::getModuleById(Config::get('Constant.MODULE.ID'));
            if (Config::get('Constant.DEFAULT_VISUAL') == 'Y') {
                if ($data['section'] != '[]') {
                    $vsection = $data['section'];
                } else {
                    $vsection = '';
                }
            } else {
                $vsection = $data['description'];
            }
            $blogCategoryArr = [];
            $blogCategoryArr['varTitle'] = stripslashes(trim($data['title']));

            $blogCategoryArr['dtDateTime'] = !empty($data['start_date_time']) ? date('Y-m-d H:i:s', strtotime($data['start_date_time'])) : date('Y-m-d H:i:s');
            $blogCategoryArr['dtEndDateTime'] = !empty($data['end_date_time']) ? date('Y-m-d H:i:s', strtotime($data['end_date_time'])) : null;

            $blogCategoryArr['txtDescription'] = $vsection;
            $blogCategoryArr['varMetaTitle'] = stripslashes(trim($data['varMetaTitle']));
            $blogCategoryArr['varMetaDescription'] = stripslashes(trim($data['varMetaDescription']));
            $blogCategoryArr['chrPublish'] = isset($data['chrMenuDisplay']) ? $data['chrMenuDisplay'] : 'Y';
            $blogCategoryArr['UserID'] = auth()->user()->id;
            if ($data['chrMenuDisplay'] == 'D') {
                $blogCategoryArr['chrDraft'] = 'D';
                $blogCategoryArr['chrPublish'] = 'N';
            } else {
                $blogCategoryArr['chrDraft'] = 'N';
                $blogCategoryArr['chrPublish'] = $data['chrMenuDisplay'];
            }
            if (isset($data['chrPageActive']) && $data['chrPageActive'] != '') {
                $blogCategoryArr['chrPageActive'] = $data['chrPageActive'];
            }
            if (isset($data['chrPageActive']) && $data['chrPageActive'] == 'PP') {
                $blogCategoryArr['varPassword'] = $data['new_password'];
            } else {
                $blogCategoryArr['varPassword'] = '';
            }
            if ($data['chrMenuDisplay'] == 'D') {
                $addlog = Config::get('Constant.UPDATE_DRAFT');
            } else {
                $addlog = '';
            }
            if (Config::get('Constant.CHRSearchRank') == 'Y') {
                $blogCategoryArr['intSearchRank'] = $data['search_rank'];
            }
            $id = Request::segment(3);
            if (is_numeric($id)) { #Edit post Handler=======
            if ($data['oldAlias'] != $data['alias']) {
                Alias::updateAlias($data['oldAlias'], $data['alias']);
            }
                $blogCategory = BlogCategory::getRecordForLogById($id);
                $whereConditions = ['id' => $blogCategory->id];
                if ($blogCategory->chrLock == 'Y' && auth()->user()->id != $blogCategory->LockUserID) {
                    if ($this->currentUserRoleData->chrIsAdmin != 'Y') {
                        $lockedUserData = User::getRecordById($blogCategory->LockUserID, true);
                        $lockedUserName = 'someone';
                        if (!empty($lockedUserData)) {
                            $lockedUserName = $lockedUserData->name;
                        }
                        $actionMessage = "This record has been locked by " . $lockedUserName . ".";
                        return redirect()->route('powerpanel.blog-category.index')->with('message', $actionMessage);
                    }
                }
                if (File::exists(app_path() . '/Workflow.php') != null || File::exists(base_path() . '/packages/Powerpanel/Workflow/src/Models/Workflow.php') != null) {
                    if (!$userIsAdmin) {
                        $userRole = $this->currentUserRoleData->id;
                    } else {
                        $userRoleData = Role_user::getUserRoleByUserId($blogCategory->UserID);
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
                        if ($blogCategory->fkMainRecord == '0' || empty($workFlowByCat->varUserId)) {
                            $update = CommonModel::updateRecords($whereConditions, $blogCategoryArr, false, 'Powerpanel\BlogCategory\Models\BlogCategory');

                            if ($update) {
                                if (!empty($id)) {
                                    self::swap_order_edit($data['order'], $id);

                                    $logArr = MyLibrary::logData($blogCategory->id, false, $addlog);
                                    if (Auth::user()->can('log-advanced')) {
                                        $newBlogCategoryObj = BlogCategory::getRecordForLogById($blogCategory->id);
                                        $oldRec = $this->recordHistory($blogCategory);
                                        $newRec = $this->newrecordHistory($blogCategory, $newBlogCategoryObj);
                                        $logArr['old_val'] = $oldRec;
                                        $logArr['new_val'] = $newRec;
                                    }
                                    $logArr['varTitle'] = trim($data['title']);
                                    Log::recordLog($logArr);
                                    if (Auth::user()->can('recent-updates-list')) {
                                        if (!isset($newBlogCategoryObj)) {
                                            $newBlogCategoryObj = BlogCategory::getRecordForLogById($blogCategory->id);
                                        }
                                        $notificationArr = MyLibrary::notificationData($blogCategory->id, $newBlogCategoryObj);
                                        RecentUpdates::setNotification($notificationArr);
                                    }
                                    self::flushCache();
                                    if (isset($data['saveandexit']) && $data['saveandexit'] == 'approvesaveandexit') {
                                        $actionMessage = trans('blogcategory::template.common.recordApprovalMessage');
                                    } else {
                                        $actionMessage = trans('blogcategory::template.blogCategoryModule.updateMessage');
                                    }
                                }
                            }
                        } else {
                            $updateModuleFields = $blogCategoryArr;
                            $this->insertApprovedRecord($updateModuleFields, $data, $id);
                            if (isset($data['saveandexit']) && $data['saveandexit'] == 'approvesaveandexit') {
                                $actionMessage = trans('blogcategory::template.common.recordApprovalMessage');
                            } else {
                                $actionMessage = trans('blogcategory::template.blogCategoryModule.updateMessage');
                            }
                            $approval = $id;
                        }
                    } else {
                        if ($workFlowByCat->charNeedApproval == 'Y') {
                            $approvalObj = $this->insertApprovalRecord($blogCategory, $data, $blogCategoryArr);
                            if (isset($data['saveandexit']) && $data['saveandexit'] == 'approvesaveandexit') {
                                $actionMessage = trans('blogcategory::template.common.recordApprovalMessage');
                            } else {
                                $actionMessage = trans('blogcategory::template.blogCategoryModule.updateMessage');
                            }
                            $approval = $approvalObj->id;
                        }
                    }
                } else {
                    $update = CommonModel::updateRecords($whereConditions, $blogCategoryArr, false, 'Powerpanel\BlogCategory\Models\BlogCategory');
                    $actionMessage = trans('blogcategory::template.blogCategoryModule.updateMessage');
                }
            } else { #Add post Handler=======
            if (File::exists(app_path() . '/Workflow.php') != null || File::exists(base_path() . '/packages/Powerpanel/Workflow/src/Models/Workflow.php') != null) {
                $workFlowByCat = Workflow::getRecordByCategoryId($module->intFkGroupCode, $this->currentUserRoleData->id, Config::get('Constant.MODULE.ID'));
            }
                if (!empty($workFlowByCat->varUserId) && $workFlowByCat->chrNeedAddPermission == 'Y' && !$userIsAdmin) {

                    $blogCategoryArr['chrPublish'] = 'N';
                    $blogCategoryArr['chrDraft'] = 'N';
                    $blogCategoryObj = $this->insertNewRecord($data, $blogCategoryArr);
                    if ($data['chrMenuDisplay'] == 'D') {
                        $blogCategoryArr['chrDraft'] = 'D';
                    }
                    $blogCategoryArr['chrPublish'] = 'Y';
                    $approvalObj = $this->insertApprovalRecord($blogCategoryObj, $data, $blogCategoryArr);
                    $approval = $blogCategoryObj->id;
                } else {
                    $blogCategoryObj = $this->insertNewRecord($data, $blogCategoryArr);
                    $approval = $blogCategoryObj->id;
                }
                if (isset($data['saveandexit']) && $data['saveandexit'] == 'approvesaveandexit') {
                    $actionMessage = trans('blogcategory::template.common.recordApprovalMessage');
                } else {
                    $actionMessage = trans('blogcategory::template.blogCategoryModule.addedMessage');
                }
                $id = $blogCategoryObj->id;
            }
            if (method_exists($this->Alias, 'updatePreviewAlias')) {
                Alias::updatePreviewAlias($data['alias'], 'N');
            }
            if ((!empty(Request::get('saveandexit')) && Request::get('saveandexit') == 'saveandexit') || !$userIsAdmin) {
                if ($data['chrMenuDisplay'] == 'D') {
                    return redirect()->route('powerpanel.blog-category.index', 'tab=D')->with('message', $actionMessage);
                } else {
                    return redirect()->route('powerpanel.blog-category.index')->with('message', $actionMessage);
                }
            } else {
                return redirect()->route('powerpanel.blog-category.edit', $id)->with('message', $actionMessage);
            }
        } else {
            return Redirect::back()->withErrors($validator)->withInput();
        }
    }

    public function insertApprovedRecord($updateModuleFields, $postArr, $id)
    {
        $whereConditions = ['id' => $postArr['fkMainRecord']];
        $updateModuleFields['chrAddStar'] = 'N';
        $update = CommonModel::updateRecords($whereConditions, $updateModuleFields, false, 'Powerpanel\BlogCategory\Models\BlogCategory');
        if ($update) {
            self::swap_order_edit($postArr['order'], $postArr['fkMainRecord']);
        }
        $whereConditions_ApproveN = ['fkMainRecord' => $postArr['fkMainRecord']];
        $updateToApproveN = [
            'chrApproved' => 'N',
            'chrLetest' => 'N',
            'intApprovedBy' => '0',
        ];
        CommonModel::updateRecords($whereConditions_ApproveN, $updateToApproveN, false, 'Powerpanel\BlogCategory\Models\BlogCategory');
        $whereConditionsApprove = ['id' => $id, 'chrMain' => 'N'];
        $updateToApprove = [
            'chrApproved' => 'Y',
            'chrRollBack' => 'Y',
            'intApprovedBy' => auth()->user()->id,
        ];
        CommonModel::updateRecords($whereConditionsApprove, $updateToApprove, false, 'Powerpanel\BlogCategory\Models\BlogCategory');
        if ($postArr['chrMenuDisplay'] == 'D') {
            $addlog = Config::get('Constant.DRAFT_RECORD_APPROVED');
        } else {
            $addlog = Config::get('Constant.RECORD_APPROVED');
        }
        $newCmsPageObj = BlogCategory::getRecordForLogById($id);
        $logArr = MyLibrary::logData($id, false, $addlog);
        $logArr['varTitle'] = stripslashes($newCmsPageObj->varTitle);
        Log::recordLog($logArr);
        /* notification for user to record approved */
        $careers = BlogCategory::getRecordForLogById($id);
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

    public function insertApprovalRecord($moduleObj, $postArr, $blogCategoryArr)
    {
        $response = false;
        $blogCategoryArr['intAliasId'] = MyLibrary::insertAlias($postArr['alias']);
        $blogCategoryArr['intDisplayOrder'] = $postArr['order'];
        $blogCategoryArr['chrMain'] = 'N';
        $blogCategoryArr['chrLetest'] = 'Y';
        $blogCategoryArr['fkMainRecord'] = $moduleObj->id;
        if (Config::get('Constant.CHRSearchRank') == 'Y') {
            $blogCategoryArr['intSearchRank'] = $postArr['search_rank'];
        }
        if ($postArr['chrMenuDisplay'] == 'D') {
            $blogCategoryArr['chrDraft'] = 'D';
            $blogCategoryArr['chrPublish'] = 'N';
        } else {
            $blogCategoryArr['chrDraft'] = 'N';
            $blogCategoryArr['chrPublish'] = $postArr['chrMenuDisplay'];
        }
        if (isset($postArr['chrPageActive']) && $postArr['chrPageActive'] != '') {
            $blogCategoryArr['chrPageActive'] = $postArr['chrPageActive'];
        }
        if (isset($postArr['chrPageActive']) && $postArr['chrPageActive'] == 'PP') {
            $blogCategoryArr['varPassword'] = $postArr['new_password'];
        } else {
            $blogCategoryArr['varPassword'] = '';
        }
        if ($postArr['chrMenuDisplay'] == 'D') {
            $addlog = Config::get('Constant.DRAFT_SENT_FOR_APPROVAL');
        } else {
            $addlog = Config::get('Constant.SENT_FOR_APPROVAL');
        }
        $blogCategoryID = CommonModel::addRecord($blogCategoryArr, 'Powerpanel\BlogCategory\Models\BlogCategory');
        if (!empty($blogCategoryID)) {
            $id = $blogCategoryID;
            WorkflowLog::addRecord([
                'fkModuleId' => Config::get('Constant.MODULE.ID'),
                'fkRecordId' => $moduleObj->id,
                'charApproval' => 'Y',
            ]);
            if (method_exists($this->MyLibrary, 'userNotificationData')) {
                $userNotificationArr = MyLibrary::userNotificationData(Config::get('Constant.MODULE.ID'));
                $userNotificationArr['fkRecordId'] = $moduleObj->id;
                $userNotificationArr['txtNotification'] = 'New approval request from ' . ucfirst(auth()->user()->name) . ' (' . ucfirst(Config::get('Constant.MODULE.NAME')) . ')';
                $userNotificationArr['fkIntUserId'] = Auth::user()->id;
                $userNotificationArr['chrNotificationType'] = 'A';
                UserNotification::addRecord($userNotificationArr);
            }
            $newblogCategoryObj = BlogCategory::getRecordForLogById($id);
            $logArr = MyLibrary::logData($id, false, $addlog);
            $logArr['varTitle'] = $newblogCategoryObj->varTitle;
            Log::recordLog($logArr);
            if (Auth::user()->can('recent-updates-list')) {
                $notificationArr = MyLibrary::notificationData($id, $newblogCategoryObj);
                RecentUpdates::setNotification($notificationArr);
            }
            $response = $newblogCategoryObj;
            self::flushCache();
            $actionMessage = trans('blogcategory::template.blogCategoryModule.addedMessage');
        }
        $whereConditionsAddstar = ['id' => $moduleObj->id];
        $updateAddStar = [
            'chrAddStar' => 'Y',
        ];
        CommonModel::updateRecords($whereConditionsAddstar, $updateAddStar, false, 'Powerpanel\BlogCategory\Models\BlogCategory');
        return $response;
    }

    public function insertNewRecord($postArr, $blogCategoryArr)
    {
        $response = false;
        $blogCategoryArr['intAliasId'] = MyLibrary::insertAlias($postArr['alias']);
        $blogCategoryArr['intDisplayOrder'] = self::swap_order_add($postArr['order']);
        $blogCategoryArr['chrMain'] = 'Y';
        if (Config::get('Constant.CHRSearchRank') == 'Y') {
            $blogCategoryArr['intSearchRank'] = $postArr['search_rank'];
        }
        if ($postArr['chrMenuDisplay'] == 'D') {
            $blogCategoryArr['chrDraft'] = 'D';
            $blogCategoryArr['chrPublish'] = 'N';
        } else {
            $blogCategoryArr['chrDraft'] = 'N';
        }
        if (isset($postArr['chrPageActive']) && $postArr['chrPageActive'] != '') {
            $blogCategoryArr['chrPageActive'] = $postArr['chrPageActive'];
        }
        if (isset($postArr['chrPageActive']) && $postArr['chrPageActive'] == 'PP') {
            $blogCategoryArr['varPassword'] = $postArr['new_password'];
        } else {
            $blogCategoryArr['varPassword'] = '';
        }
        if ($postArr['chrMenuDisplay'] == 'D') {
            $addlog = Config::get('Constant.ADDED_DRAFT');
        } else {
            $addlog = '';
        }
        $blogCategoryID = CommonModel::addRecord($blogCategoryArr, 'Powerpanel\BlogCategory\Models\BlogCategory');
        if (!empty($blogCategoryID)) {
            $id = $blogCategoryID;
            $newBlogCategoryObj = BlogCategory::getRecordForLogById($id);
            $logArr = MyLibrary::logData($id, false, $addlog);
            $logArr['varTitle'] = stripslashes($newBlogCategoryObj->varTitle);
            Log::recordLog($logArr);
            if (Auth::user()->can('recent-updates-list')) {
                $notificationArr = MyLibrary::notificationData($id, $newBlogCategoryObj);
                RecentUpdates::setNotification($notificationArr);
            }
            $response = $newBlogCategoryObj;
            self::flushCache();
            $actionMessage = trans('blogcategory::template.blogCategoryModule.addedMessage');
        }
        return $response;
    }

    /**
     * This method loads blogcategory table data on view
     * @return  View
     * @since   2017-11-10
     * @author  NetQuick
     */
    public function get_list_New()
    {
        $filterArr = [];
        $records = [];
        $records["data"] = [];
        $filterArr['orderColumnNo'] = (!empty(Request::input('order')[0]['column']) ? Request::input('order')[0]['column'] : '');
        $filterArr['orderByFieldName'] = (!empty(Request::input('columns')[$filterArr['orderColumnNo']]['name']) ? Request::input('columns')[$filterArr['orderColumnNo']]['name'] : '');
        $filterArr['orderTypeAscOrDesc'] = (!empty(Request::input('order')[0]['dir']) ? Request::input('order')[0]['dir'] : '');
        $filterArr['statusFilter'] = !empty(Request::input('statusValue')) ? Request::input('statusValue') : '';
        $filterArr['catFilter'] = !empty(Request::input('catValue')) ? Request::input('catValue') : '';
        $filterArr['searchFilter'] = !empty(Request::input('searchValue')) ? Request::input('searchValue') : '';
        $filterArr['customFilterIdentity'] = !empty(Request::get('customFilterIdentity')) ? Request::get('customFilterIdentity') : '';
        $filterArr['iDisplayLength'] = intval(Request::input('length'));
        $filterArr['iDisplayStart'] = intval(Request::input('start'));
        $filterArr['rangeFilter'] = !empty(Request::input('rangeFilter')) ? Request::input('rangeFilter') : '';
        $sEcho = intval(Request::input('draw'));
        $arrResults = BlogCategory::getRecordList_tab1($filterArr);
        $iTotalRecords = BlogCategory::getRecordCountListApprovalTab($filterArr, true);
        $end = $filterArr['iDisplayStart'] + $filterArr['iDisplayLength'];
        $end = $end > $iTotalRecords ? $iTotalRecords : $end;
        if (!empty($arrResults)) {
            foreach ($arrResults as $key => $value) {
                $records["data"][] = $this->tableData_tab1($value);
            }
        }
        $NewRecordsCount = BlogCategory::getNewRecordsCount();
        $records["newRecordCount"] = $NewRecordsCount;
        $records["customActionStatus"] = "OK";
        $records["draw"] = $sEcho;
        $records["recordsTotal"] = $iTotalRecords;
        $records["recordsFiltered"] = $iTotalRecords;
        return json_encode($records);
    }

    public function get_list()
    {
        $userIsAdmin = false;
        if (!empty($this->currentUserRoleData)) {
            if ($this->currentUserRoleData->chrIsAdmin == 'Y') {
                $userIsAdmin = true;
            }
        }
        $filterArr = [];
        $records = [];
        $records["data"] = [];
        $filterArr['orderColumnNo'] = (!empty(Request::input('order')[0]['column']) ? Request::input('order')[0]['column'] : '');
        $filterArr['orderByFieldName'] = (!empty(Request::input('columns')[$filterArr['orderColumnNo']]['name']) ? Request::input('columns')[$filterArr['orderColumnNo']]['name'] : '');
        $filterArr['orderTypeAscOrDesc'] = (!empty(Request::input('order')[0]['dir']) ? Request::input('order')[0]['dir'] : '');
        $filterArr['statusFilter'] = !empty(Request::input('statusValue')) ? Request::input('statusValue') : '';
        $filterArr['customFilterIdentity'] = !empty(Request::get('customFilterIdentity')) ? Request::get('customFilterIdentity') : '';
        $filterArr['catFilter'] = !empty(Request::input('catValue')) ? Request::input('catValue') : '';
        $filterArr['searchFilter'] = !empty(Request::input('searchValue')) ? Request::input('searchValue') : '';
        $filterArr['iDisplayLength'] = intval(Request::input('length'));
        $filterArr['iDisplayStart'] = intval(Request::input('start'));
        $filterArr['rangeFilter'] = !empty(Request::input('rangeFilter')) ? Request::input('rangeFilter') : '';
        $sEcho = intval(Request::input('draw'));
        $isAdmin = false;
        if ($userIsAdmin) {
            $isAdmin = true;
        }

        $module = Modules::getModule('pages');
        $arrResults = BlogCategory::getRecordList($filterArr, $isAdmin);
        $iTotalRecords = BlogCategory::getRecordCountforList($filterArr, true, $isAdmin);
        $end = $filterArr['iDisplayStart'] + $filterArr['iDisplayLength'];
        $end = $end > $iTotalRecords ? $iTotalRecords : $end;
        $tableSortedType = (isset($filterArr['orderTypeAscOrDesc']) && $filterArr['orderTypeAscOrDesc'] != "") ? $filterArr['orderTypeAscOrDesc'] : '';
        $totalRecords = BlogCategory::getRecordCount();
        if (!empty($arrResults)) {
            foreach ($arrResults as $key => $value) {
                $records["data"][] = $this->tableData($value, $totalRecords, $tableSortedType);
            }
        }
        $NewRecordsCount = BlogCategory::getNewRecordsCount();
        $records["newRecordCount"] = $NewRecordsCount;
        $records["customActionStatus"] = "OK";
        $records["draw"] = $sEcho;
        $records["recordsTotal"] = $iTotalRecords;
        $records["recordsFiltered"] = $iTotalRecords;
        return json_encode($records);
    }

    public function get_list_favorite()
    {
        $userIsAdmin = false;
        if (!empty($this->currentUserRoleData)) {
            if ($this->currentUserRoleData->chrIsAdmin == 'Y') {
                $userIsAdmin = true;
            }
        }
        $filterArr = [];
        $records = [];
        $records["data"] = [];
        $filterArr['orderColumnNo'] = (!empty(Request::input('order')[0]['column']) ? Request::input('order')[0]['column'] : '');
        $filterArr['orderByFieldName'] = (!empty(Request::input('columns')[$filterArr['orderColumnNo']]['name']) ? Request::input('columns')[$filterArr['orderColumnNo']]['name'] : '');
        $filterArr['orderTypeAscOrDesc'] = (!empty(Request::input('order')[0]['dir']) ? Request::input('order')[0]['dir'] : '');
        $filterArr['statusFilter'] = !empty(Request::input('statusValue')) ? Request::input('statusValue') : '';
        $filterArr['customFilterIdentity'] = !empty(Request::get('customFilterIdentity')) ? Request::get('customFilterIdentity') : '';
        $filterArr['catFilter'] = !empty(Request::input('catValue')) ? Request::input('catValue') : '';
        $filterArr['searchFilter'] = !empty(Request::input('searchValue')) ? Request::input('searchValue') : '';
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
        $arrResults = BlogCategory::getRecordListFavorite($filterArr, $isAdmin);
        $iTotalRecords = BlogCategory::getRecordCountforListFavorite($filterArr, true, $isAdmin);
        $end = $filterArr['iDisplayStart'] + $filterArr['iDisplayLength'];
        $end = $end > $iTotalRecords ? $iTotalRecords : $end;
        $tableSortedType = (isset($filterArr['orderTypeAscOrDesc']) && $filterArr['orderTypeAscOrDesc'] != "") ? $filterArr['orderTypeAscOrDesc'] : '';
        $totalRecords = BlogCategory::getRecordCount();
        if (!empty($arrResults)) {
            foreach ($arrResults as $key => $value) {
                $records["data"][] = $this->tableDataFavorite($value, $totalRecords, $tableSortedType, $cmsPageForModule);
            }
        }
        $NewRecordsCount = BlogCategory::getNewRecordsCount();
        $records["newRecordCount"] = $NewRecordsCount;
        $records["customActionStatus"] = "OK";
        $records["draw"] = $sEcho;
        $records["recordsTotal"] = $iTotalRecords;
        $records["recordsFiltered"] = $iTotalRecords;
        return json_encode($records);
    }

    public function get_list_draft()
    {
        $userIsAdmin = false;
        if (!empty($this->currentUserRoleData)) {
            if ($this->currentUserRoleData->chrIsAdmin == 'Y') {
                $userIsAdmin = true;
            }
        }
        $filterArr = [];
        $records = [];
        $records["data"] = [];
        $filterArr['orderColumnNo'] = (!empty(Request::input('order')[0]['column']) ? Request::input('order')[0]['column'] : '');
        $filterArr['orderByFieldName'] = (!empty(Request::input('columns')[$filterArr['orderColumnNo']]['name']) ? Request::input('columns')[$filterArr['orderColumnNo']]['name'] : '');
        $filterArr['orderTypeAscOrDesc'] = (!empty(Request::input('order')[0]['dir']) ? Request::input('order')[0]['dir'] : '');
        $filterArr['statusFilter'] = !empty(Request::input('statusValue')) ? Request::input('statusValue') : '';
        $filterArr['customFilterIdentity'] = !empty(Request::get('customFilterIdentity')) ? Request::get('customFilterIdentity') : '';
        $filterArr['catFilter'] = !empty(Request::input('catValue')) ? Request::input('catValue') : '';
        $filterArr['searchFilter'] = !empty(Request::input('searchValue')) ? Request::input('searchValue') : '';
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
        $arrResults = BlogCategory::getRecordListDraft($filterArr, $isAdmin);
        $iTotalRecords = BlogCategory::getRecordCountforListDarft($filterArr, true, $isAdmin);
        $end = $filterArr['iDisplayStart'] + $filterArr['iDisplayLength'];
        $end = $end > $iTotalRecords ? $iTotalRecords : $end;
        $tableSortedType = (isset($filterArr['orderTypeAscOrDesc']) && $filterArr['orderTypeAscOrDesc'] != "") ? $filterArr['orderTypeAscOrDesc'] : '';
        $totalRecords = BlogCategory::getRecordCount();
        if (!empty($arrResults)) {
            foreach ($arrResults as $key => $value) {
                $records["data"][] = $this->tableDataDraft($value, $totalRecords, $tableSortedType, $cmsPageForModule);
            }
        }
        $NewRecordsCount = BlogCategory::getNewRecordsCount();
        $records["newRecordCount"] = $NewRecordsCount;
        $records["customActionStatus"] = "OK";
        $records["draw"] = $sEcho;
        $records["recordsTotal"] = $iTotalRecords;
        $records["recordsFiltered"] = $iTotalRecords;
        return json_encode($records);
    }

    public function get_list_trash()
    {
        $userIsAdmin = false;
        if (!empty($this->currentUserRoleData)) {
            if ($this->currentUserRoleData->chrIsAdmin == 'Y') {
                $userIsAdmin = true;
            }
        }
        $filterArr = [];
        $records = [];
        $records["data"] = [];
        $filterArr['orderColumnNo'] = (!empty(Request::input('order')[0]['column']) ? Request::input('order')[0]['column'] : '');
        $filterArr['orderByFieldName'] = (!empty(Request::input('columns')[$filterArr['orderColumnNo']]['name']) ? Request::input('columns')[$filterArr['orderColumnNo']]['name'] : '');
        $filterArr['orderTypeAscOrDesc'] = (!empty(Request::input('order')[0]['dir']) ? Request::input('order')[0]['dir'] : '');
        $filterArr['statusFilter'] = !empty(Request::input('statusValue')) ? Request::input('statusValue') : '';
        $filterArr['customFilterIdentity'] = !empty(Request::get('customFilterIdentity')) ? Request::get('customFilterIdentity') : '';
        $filterArr['catFilter'] = !empty(Request::input('catValue')) ? Request::input('catValue') : '';
        $filterArr['searchFilter'] = !empty(Request::input('searchValue')) ? Request::input('searchValue') : '';
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
        $arrResults = BlogCategory::getRecordListTrash($filterArr, $isAdmin);
        $iTotalRecords = BlogCategory::getRecordCountforListTrash($filterArr, true, $isAdmin);
        $end = $filterArr['iDisplayStart'] + $filterArr['iDisplayLength'];
        $end = $end > $iTotalRecords ? $iTotalRecords : $end;
        $tableSortedType = (isset($filterArr['orderTypeAscOrDesc']) && $filterArr['orderTypeAscOrDesc'] != "") ? $filterArr['orderTypeAscOrDesc'] : '';
        $totalRecords = BlogCategory::getRecordCount();
        if (!empty($arrResults)) {
            foreach ($arrResults as $key => $value) {
                $records["data"][] = $this->tableDataTrash($value, $totalRecords, $tableSortedType, $cmsPageForModule);
            }
        }
        $NewRecordsCount = BlogCategory::getNewRecordsCount();
        $records["newRecordCount"] = $NewRecordsCount;
        $records["customActionStatus"] = "OK";
        $records["draw"] = $sEcho;
        $records["recordsTotal"] = $iTotalRecords;
        $records["recordsFiltered"] = $iTotalRecords;
        return json_encode($records);
    }

    public function tableData($value = false, $totalRecord = false, $tableSortedType = 'asc')
    {
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
        $hasRecords = Blogs::getCountById($value->id);
        $details = '';
        $publish_action = '';
        $titleData_delete = "";
        $titleData_publish = "";
        if ($hasRecords > 0) {
            $titleData_delete .= 'This category is selected in ' . trans("blogcategory::template.sidebar.blogs") . ', so it can&#39;t be deleted.';
            $titleData_publish = 'This category is selected in ' . trans("blogcategory::template.sidebar.blogs") . ', so it can&#39;t be deleted.';
        }
        $checkbox = '<a href="javascript:;" data-toggle="tooltip" data-placement="right" data-toggle="tooltip" data-original-title="' . $titleData_delete . '" title="' . $titleData_delete . '"><i style="color:red" class="fa fa-exclamation-triangle"></i></a>';
        $checkbox_publish = '<a href="javascript:;" data-toggle="tooltip" data-placement="right" data-toggle="tooltip" data-original-title="' . $titleData_publish . '" title="' . $titleData_publish . '"><i style="color:red" class="fa fa-exclamation-triangle"></i></a>';
        if (Auth::user()->can('blog-category-edit')) {
            $details .= '<a class="" title="' . trans("blogcategory::template.common.edit") . '" href="' . route('powerpanel.blog-category.edit', array('alias' => $value->id)) . '"><i class="fa fa-pencil"></i></a>';
        }
        if ((Auth::user()->can('blog-category-delete') || (isset($this->currentUserRoleData->chrIsAdmin) && $this->currentUserRoleData->chrIsAdmin == 'Y')) && $hasRecords == 0) {
            if (Config::get('Constant.DEFAULT_TRASH') == 'Y') {
                $details .= '<a class="delete-grid" title="' . trans("blogcategory::template.common.delete") . '" data-controller="BlogCategory" onclick = \'Trashfun("' . $value->id . '")\' data-alias = "' . $value->id . '" data-tab="P"><i class="fa fa-times"></i></a>';
            } else {
                $details .= '<a class=" delete" title="' . trans("blogcategory::template.common.delete") . '" data-controller="BlogCategory" data-alias = "' . $value->id . '" data-tab="P"><i class="fa fa-times"></i></a>';
            }
        }
        if (isset($this->currentUserRoleData->chrIsAdmin) && $this->currentUserRoleData->chrIsAdmin == 'Y') {
            $details .= '<a class=" share" title="Share" data-modal="BlogCategory" data-alias="' . $value->id . '"  data-images="" data-link = "' . url('/' . $value->alias['varAlias']) . '" data-toggle="modal" data-target="#confirm_share">
					<i class="la la-share-alt"></i></a>';
        }
        if ($value->chrAddStar != 'Y') {
            if ($value->chrDraft != 'D') {
                if (Auth::user()->can('blog-category-publish')) {
                    if ($hasRecords == 0) {
                        if (!empty($value->chrPublish) && ($value->chrPublish == 'Y')) {
                            $publish_action .= '<input data-off-text="No" data-on-text="Yes" class="make-switch publish" class="make-switch publish" data-off-color="info" data-on-color="primary" type="checkbox" data-controller="powerpanel/blog-category" title="' . trans("blogcategory::template.common.publishedRecord") . '" data-value="Unpublish" data-alias="' . $value->id . '">';
                        } else {
                            $publish_action .= '<input checked="" data-off-text="No" data-on-text="Yes" class="make-switch publish" class="make-switch publish" data-off-color="info" data-on-color="primary" type="checkbox" data-controller="powerpanel/blog-category" title="' . trans("blogcategory::template.common.unpublishedRecord") . '" data-value="Publish" data-alias="' . $value->id . '">';
                        }
                    } else {
                        $publish_action = $checkbox_publish;
                    }
                }
            } else {
                $publish_action .= '<input checked="" data-off-text="No" data-on-text="Yes" class="make-switch pub" data-off-color="info" data-on-color="primary" type="checkbox" data-controller="powerpanel/blog-category" title="' . trans("blogcategory::template.common.unpublishedRecord") . '" data-value="Publish" data-alias="' . $value->id . '">';
            }
        } else {
            if ($hasRecords == 0) {
                $publish_action .= '---';
            } else {
                $publish_action = $checkbox_publish;
            }
        }
        if (Auth::user()->can('blog-category-reviewchanges') && (File::exists(app_path() . '/Workflow.php') != null || File::exists(base_path() . '/packages/Powerpanel/Workflow/src/Models/Workflow.php') != null)) {
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
        if (Auth::user()->can('blog-category-edit')) {
            if ($value->chrDraft == 'D' || $value->chrAddStar == 'Y') {
                $viewlink = url('/previewpage?url=' . MyLibrary::getFrontUri('blog-category')['uri'] . '/' . $value->id . '/preview');
                $linkviewLable = "Preview";
            } else {
                $viewlink = MyLibrary::getFrontUri('blog-category', $value->id)['uri'];
                $linkviewLable = "View";
            }
            //$frontViewLink = MyLibrary::getFrontUri('blog-category', $value->id)['uri'];
            if ($value->chrLock != 'Y') {
                if (isset($this->currentUserRoleData->chrIsAdmin) && $this->currentUserRoleData->chrIsAdmin == 'Y') {
                    $title = '<div class="quick_edit"><a href = "' . route('powerpanel.blog-category.edit', array('alias' => $value->id)) . '?tab=P">' . $value->varTitle . '</a> <div class="quick_edit_menu">
														<span><a href="' . route('powerpanel.blog-category.edit', array('alias' => $value->id)) . '?tab=P" title="Edit">Edit</a></span>';
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
                    $title = '<div class="quick_edit"><a href = "' . route('powerpanel.blog-category.edit', array('alias' => $value->id)) . '?tab=P">' . $value->varTitle . '</a> <div class="quick_edit_menu">
														<span><a href="' . route('powerpanel.blog-category.edit', array('alias' => $value->id)) . '?tab=P" title="Edit">Edit</a></span>
														<span><a href="' . $viewlink . '" target="_blank" title="' . $linkviewLable . '" >' . $linkviewLable . '</a></span>
																</div>
											 </div>';
                }
            } else {
                if (auth()->user()->id != $value->LockUserID) {
                    if (isset($this->currentUserRoleData->chrIsAdmin) && $this->currentUserRoleData->chrIsAdmin == 'Y') {
                        $title = '<div class="quick_edit"><a href = "' . route('powerpanel.blog-category.edit', array('alias' => $value->id)) . '?tab=P">' . $value->varTitle . '</a> <div class="quick_edit_menu">
                            <span><a href="' . route('powerpanel.blog-category.edit', array('alias' => $value->id)) . '?tab=P" title="Edit">Edit</a></span>
                            <span><a href="' . $viewlink . '" target="_blank" title="' . $linkviewLable . '" >' . $linkviewLable . '</a></span>
                                </div>
                       </div>';
                    } else {
                        $title = '<div class="quick_edit"><a href = "javascript:;">' . $value->varTitle . '</a></div>';
                    }
                } else {
                    $title = '<div class="quick_edit"><a href = "' . route('powerpanel.blog-category.edit', array('alias' => $value->id)) . '?tab=P">' . $value->varTitle . '</a> <div class="quick_edit_menu">
                            <span><a href="' . route('powerpanel.blog-category.edit', array('alias' => $value->id)) . '?tab=P" title="Edit">Edit</a></span>
                            <span><a href="' . $viewlink . '" target="_blank" title="' . $linkviewLable . '" >' . $linkviewLable . '</a></span>
                                </div>
                       </div>';
                }
            }
        }
        $orderArrow = '';
        $dispOrder = $value->intDisplayOrder;
        if (($value->intDisplayOrder == $totalRecord || $value->intDisplayOrder < $totalRecord) && $value->intDisplayOrder > 1) {
            $orderArrow .= '<a href="javascript:;" data-order="' . $value->intDisplayOrder . '" class="moveUp"><i class="fa fa-arrow-up" aria-hidden="true"></i></a>
								';
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

    public function tableDataFavorite($value = false, $totalRecord = false, $tableSortedType = 'asc', $moduleCmsPageData = false)
    {
        $Hits = Pagehit::where('fkIntAliasId', $value->intAliasId)->count();
        $webHits = '';
        if ($Hits > 0) {
            $webHits .= '<a data-toggle="modal" href="#" onclick=\'HitsPopup("' . $value->id . '","' . $value->intAliasId . '","' . $value->varTitle . '","F")\'>' . $Hits . '</a>
										<div class="new_modal modal fade" id="desc_' . $value->id . '_F" tabindex="-1" aria-hidden="true">
												<div class="modal-dialog" style="margin: 0 auto;display:table;width: 100%;height:100%;max-width: 1000px;">
												<div class="modal-vertical">
												<div class="modal-content">
										<div class="modal-header">
										<button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
										<h3 class="modal-title">Hits Report</h3>
										</div>
										<div class="modal-body">
										<div id="webdata_' . $value->id . '_F"></div>
										</div>
										</div>
										</div>
										</div>
										</div>';
        } else {
            $webHits .= '0';
        }
        $hasRecords = Blogs::getCountById($value->id);
        $details = '';
        $publish_action = '';
        $titleData_delete = "";
        $titleData_publish = "";
        if ($hasRecords > 0) {
            $titleData_delete .= 'This category is selected in ' . trans("blogcategory::template.sidebar.blogs") . ', so it can&#39;t be deleted.';
            $titleData_publish = 'This category is selected in ' . trans("blogcategory::template.sidebar.blogs") . ', so it can&#39;t be deleted.';
        }
        $checkbox = '<a href="javascript:;" data-toggle="tooltip" data-placement="right" data-toggle="tooltip" data-original-title="' . $titleData_delete . '" title="' . $titleData_delete . '"><i style="color:red" class="fa fa-exclamation-triangle"></i></a>';
        if (Auth::user()->can('blog-category-edit')) {
            $details .= '<a class="" title="' . trans("blogcategory::template.common.edit") . '" href="' . route('powerpanel.blog-category.edit', array('alias' => $value->id)) . '"><i class="fa fa-pencil"></i></a>';
        }
        if (Auth::user()->can('blog-category-delete') && $hasRecords == 0 && $this->currentUserRoleData->chrIsAdmin == 'Y') {
            if (Config::get('Constant.DEFAULT_TRASH') == 'Y') {
                $details .= '<a class="delete-grid" title="' . trans("blogcategory::template.common.delete") . '" data-controller="BlogCategory" onclick = \'Trashfun("' . $value->id . '")\' data-alias = "' . $value->id . '" data-tab="F"><i class="fa fa-times"></i></a>';
            } else {
                $details .= '<a class=" delete" title="' . trans("blogcategory::template.common.delete") . '" data-controller="BlogCategory" data-alias = "' . $value->id . '" data-tab="F"><i class="fa fa-times"></i></a>';
            }
        }
        $startDate = date('' . Config::get('Constant.DEFAULT_DATE_FORMAT') . ' ' . Config::get('Constant.DEFAULT_TIME_FORMAT') . '', strtotime($value->dtDateTime));
        $endDate = !empty($value->dtEndDateTime) ? date('' . Config::get('Constant.DEFAULT_DATE_FORMAT') . ' ' . Config::get('Constant.DEFAULT_TIME_FORMAT') . '', strtotime($value->dtEndDateTime)) : 'No Expiry';
        $Quickedit_startDate = date('Y-m-d H:i', strtotime($value->dtDateTime));
        $Quickedit_endDate = !empty($value->dtEndDateTime) ? date('Y-m-d H:i', strtotime($value->dtEndDateTime)) : 'No Expiry';
        $title = $value->varTitle;
        if (Auth::user()->can('blog-category-edit')) {
            if ($value->chrDraft == 'D' || $value->chrAddStar == 'Y') {
                $viewlink = url('/previewpage?url=' . MyLibrary::getFrontUri('blog-category')['uri'] . '/' . $value->id . '/preview');
                $linkviewLable = "Preview";
            } else {
                $viewlink = MyLibrary::getFrontUri('blog-category', $value->id)['uri'];
                $linkviewLable = "View";
            }
            //$frontViewLink = MyLibrary::getFrontUri('blog-category', $value->id)['uri'];
            if ($value->chrLock != 'Y') {
                if ($this->currentUserRoleData->chrIsAdmin == 'Y') {
                    $title = '<div class="quick_edit"><a href = "' . route('powerpanel.blog-category.edit', array('alias' => $value->id)) . '?tab=F">' . $value->varTitle . '</a> <div class="quick_edit_menu">
														<span><a href="' . route('powerpanel.blog-category.edit', array('alias' => $value->id)) . '?tab=F" title="Edit">Edit</a></span>';
                    if ($hasRecords == 0) {
                        if (Config::get('Constant.DEFAULT_TRASH') == 'Y') {
                            $title .= '<span><a title = "Trash" href = \'javascript:;\' onclick=\'Trashfun("' . $value->id . '")\' class="red" data-tab="F">Trash</a></span>';
                        }
                    }
                    $title .= '<span><a href="' . $viewlink . '" target="_blank" title="' . $linkviewLable . '" >' . $linkviewLable . '</a></span>
																</div>
											 </div>';
                } else {
                    $title = '<div class="quick_edit"><a href = "' . route('powerpanel.blog-category.edit', array('alias' => $value->id)) . '?tab=F">' . $value->varTitle . '</a> <div class="quick_edit_menu">
														<span><a href="' . route('powerpanel.blog-category.edit', array('alias' => $value->id)) . '?tab=F" title="Edit">Edit</a></span>
														<span><a href="' . $viewlink . '" target="_blank" title="' . $linkviewLable . '" >' . $linkviewLable . '</a></span>
																</div>
											 </div>';
                }
            } else {
                if (auth()->user()->id != $value->LockUserID) {
                    if ($this->currentUserRoleData->chrIsAdmin == 'Y') {
                        $title = '<div class="quick_edit"><a href = "' . route('powerpanel.blog-category.edit', array('alias' => $value->id)) . '?tab=F">' . $value->varTitle . '</a> <div class="quick_edit_menu">
	                            <span><a href="' . route('powerpanel.blog-category.edit', array('alias' => $value->id)) . '?tab=F" title="Edit">Edit</a></span>
	                            <span><a href="' . $viewlink . '" target="_blank" title="' . $linkviewLable . '" >' . $linkviewLable . '</a></span>
	                                </div>
	                        </div>';
                    } else {
                        $title = '<div class="quick_edit"><a href = "javascript:;">' . $value->varTitle . '</a></div>';
                    }
                } else {
                    $title = '<div class="quick_edit"><a href = "' . route('powerpanel.blog-category.edit', array('alias' => $value->id)) . '?tab=F">' . $value->varTitle . '</a> <div class="quick_edit_menu">
	                            <span><a href="' . route('powerpanel.blog-category.edit', array('alias' => $value->id)) . '?tab=F" title="Edit">Edit</a></span>
	                            <span><a href="' . $viewlink . '" target="_blank" title="' . $linkviewLable . '" >' . $linkviewLable . '</a></span>
	                                </div>
	                        </div>';
                }
            }
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
        if ($publish_action == "") {
            $publish_action = "---";
        } else {
            $publish_action = $publish_action;
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

    public function tableDataDraft($value = false, $totalRecord = false, $tableSortedType = 'asc', $moduleCmsPageData = false)
    {
        $Hits = Pagehit::where('fkIntAliasId', $value->intAliasId)->count();
        $webHits = '';
        if ($Hits > 0) {
            $webHits .= '<a data-toggle="modal" href="#" onclick=\'HitsPopup("' . $value->id . '","' . $value->intAliasId . '","' . $value->varTitle . '","D")\'>' . $Hits . '</a>
                    <div class="new_modal modal fade" id="desc_' . $value->id . '_D" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog" style="margin: 0 auto;display:table;width: 100%;height:100%;max-width: 1000px;">
                        <div class="modal-vertical">
                        <div class="modal-content">
                    <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                    <h3 class="modal-title">Hits Report</h3>
                    </div>
                    <div class="modal-body">
                    <div id="webdata_' . $value->id . '_D"></div>
                    </div>
                    </div>
                    </div>
                    </div>
                    </div>';
        } else {
            $webHits .= '0';
        }
        $hasRecords = Blogs::getCountById($value->id);
        $details = '';
        $publish_action = '';
        $titleData_delete = "";
        $titleData_publish = "";
        if ($hasRecords > 0) {
            $titleData_delete .= 'This category is selected in ' . trans("blogcategory::template.sidebar.blogs") . ', so it can&#39;t be deleted.';
            $titleData_publish = 'This category is selected in ' . trans("blogcategory::template.sidebar.blogs") . ', so it can&#39;t be deleted.';
        }
        $checkbox = '<a href="javascript:;" data-toggle="tooltip" data-placement="right" data-toggle="tooltip" data-original-title="' . $titleData_delete . '" title="' . $titleData_delete . '"><i style="color:red" class="fa fa-exclamation-triangle"></i></a>';
        $checkbox_publish = '<a href="javascript:;" data-toggle="tooltip" data-placement="right" data-toggle="tooltip" data-original-title="' . $titleData_publish . '" title="' . $titleData_publish . '"><i style="color:red" class="fa fa-exclamation-triangle"></i></a>';
        if (Auth::user()->can('blog-category-edit')) {
            $details .= '<a class="" title="' . trans("blogcategory::template.common.edit") . '" href="' . route('powerpanel.blog-category.edit', array('alias' => $value->id)) . '"><i class="fa fa-pencil"></i></a>';
        }
        if (Auth::user()->can('blog-category-delete') && $hasRecords == 0 && $this->currentUserRoleData->chrIsAdmin == 'Y') {
            if (Config::get('Constant.DEFAULT_TRASH') == 'Y') {
                $details .= '<a class="delete-grid" title="' . trans("blogcategory::template.common.delete") . '" data-controller="BlogCategory" onclick = \'Trashfun("' . $value->id . '")\' data-alias = "' . $value->id . '" data-tab="D"><i class="fa fa-times"></i></a>';
            } else {
                $details .= '<a class=" delete" title="' . trans("blogcategory::template.common.delete") . '" data-controller="BlogCategory" data-alias = "' . $value->id . '" data-tab="D"><i class="fa fa-times"></i></a>';
            }
        }
        if ($value->chrPublish == 'Y') {
            $publish_action .= '<input data-off-text="No" data-on-text="Yes" class="make-switch pub"  data-off-color="info" data-on-color="primary" type="checkbox" data-controller="powerpanel/blog-category" title="' . trans("blogcategory::template.common.publishedRecord") . '" data-value="Unpublish" data-alias="' . $value->id . '">';
        } else {
            $publish_action .= '<input checked="" data-off-text="No" data-on-text="Yes" class="make-switch pub"  data-off-color="info" data-on-color="primary" type="checkbox" data-controller="powerpanel/blog-category" title="' . trans("blogcategory::template.common.unpublishedRecord") . '" data-value="Publish" data-alias="' . $value->id . '">';
        }
        $startDate = date('' . Config::get('Constant.DEFAULT_DATE_FORMAT') . ' ' . Config::get('Constant.DEFAULT_TIME_FORMAT') . '', strtotime($value->dtDateTime));
        $endDate = !empty($value->dtEndDateTime) ? date('' . Config::get('Constant.DEFAULT_DATE_FORMAT') . ' ' . Config::get('Constant.DEFAULT_TIME_FORMAT') . '', strtotime($value->dtEndDateTime)) : 'No Expiry';
        $Quickedit_startDate = date('Y-m-d H:i', strtotime($value->dtDateTime));
        $Quickedit_endDate = !empty($value->dtEndDateTime) ? date('Y-m-d H:i', strtotime($value->dtEndDateTime)) : 'No Expiry';
        $title = $value->varTitle;
        if (Auth::user()->can('blog-category-edit')) {
            if ($value->chrDraft == 'D' || $value->chrAddStar == 'Y') {
                $viewlink = url('/previewpage?url=' . MyLibrary::getFrontUri('blog-category')['uri'] . '/' . $value->id . '/preview');
                $linkviewLable = "Preview";
            } else {
                $viewlink = MyLibrary::getFrontUri('blog-category', $value->id)['uri'];
                $linkviewLable = "View";
            }
            if ($value->chrLock != 'Y') {
                if ($this->currentUserRoleData->chrIsAdmin == 'Y') {
                    $title = '<div class="quick_edit"><a href = "' . route('powerpanel.blog-category.edit', array('alias' => $value->id)) . '?tab=D">' . $value->varTitle . '</a> <div class="quick_edit_menu">
                            <span><a href="' . route('powerpanel.blog-category.edit', array('alias' => $value->id)) . '?tab=D" title="Edit">Edit</a></span>';
                    if ($hasRecords == 0) {
                        if (Config::get('Constant.DEFAULT_TRASH') == 'Y') {
                            $title .= '<span><a title = "Trash" href = \'javascript:;\' onclick=\'Trashfun("' . $value->id . '")\' class="red" data-tab="D">Trash</a></span>';
                        }
                    }
                    $title .= '<span><a href="' . $viewlink . '" target="_blank" title="' . $linkviewLable . '" >' . $linkviewLable . '</a></span></div></div>';
                } else {
                    $title = '<div class="quick_edit"><a href = "' . route('powerpanel.blog-category.edit', array('alias' => $value->id)) . '?tab=D">' . $value->varTitle . '</a> <div class="quick_edit_menu">
                            <span><a href="' . route('powerpanel.blog-category.edit', array('alias' => $value->id)) . '?tab=D" title="Edit">Edit</a></span>
                            <span><a href="' . $viewlink . '" target="_blank" title="' . $linkviewLable . '" >' . $linkviewLable . '</a></span></div></div>';
                }
            } else {
                if (auth()->user()->id != $value->LockUserID) {
                    if ($this->currentUserRoleData->chrIsAdmin == 'Y') {
                        $title = '<div class="quick_edit"><a href = "' . route('powerpanel.blog-category.edit', array('alias' => $value->id)) . '?tab=D">' . $value->varTitle . '</a> <div class="quick_edit_menu">
	                            <span><a href="' . route('powerpanel.blog-category.edit', array('alias' => $value->id)) . '?tab=D" title="Edit">Edit</a></span>';

                        $title .= '<span><a href = "' . $viewlink . '" target = "_blank" title = "' . $linkviewLable . '" >' . $linkviewLable . '</a></span></div></div>';
                    } else {
                        $title = '<div class="quick_edit"><a href = "javascript:;">' . $value->varTitle . '</a></div>';
                    }
                } else {
                    $title = '<div class="quick_edit"><a href = "' . route('powerpanel.blog-category.edit', array('alias' => $value->id)) . '?tab=D">' . $value->varTitle . '</a> <div class="quick_edit_menu">
	                            <span><a href="' . route('powerpanel.blog-category.edit', array('alias' => $value->id)) . '?tab=D" title="Edit">Edit</a></span>
	                            <span><a href="' . $viewlink . '" target="_blank" title="' . $linkviewLable . '" >' . $linkviewLable . '</a></span>
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

    public function tableDataTrash($value = false, $totalRecord = false, $tableSortedType = 'asc', $moduleCmsPageData = false)
    {
        $Hits = Pagehit::where('fkIntAliasId', $value->intAliasId)->count();
        $webHits = '';
        if ($Hits > 0) {
            $webHits .= '<a data-toggle="modal" href="#" onclick=\'HitsPopup("' . $value->id . '","' . $value->intAliasId . '","' . $value->varTitle . '","T")\'>' . $Hits . '</a>
										<div class="new_modal modal fade" id="desc_' . $value->id . '_T" tabindex="-1" aria-hidden="true">
												<div class="modal-dialog" style="margin: 0 auto;display:table;width: 100%;height:100%;max-width: 1000px;">
												<div class="modal-vertical">
												<div class="modal-content">
										<div class="modal-header">
										<button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
										<h3 class="modal-title">Hits Report</h3>
										</div>
										<div class="modal-body">
										<div id="webdata_' . $value->id . '_T"></div>
										</div>
										</div>
										</div>
										</div>
										</div>';
        } else {
            $webHits .= '0';
        }
        $hasRecords = Blogs::getCountById($value->id);
        $details = '';
        $titleData_delete = "";
        $titleData_publish = "";
        if ($hasRecords > 0) {
            $titleData_delete .= 'This category is selected in ' . trans("blogcategory::template.sidebar.blogs") . ', so it can&#39;t be deleted.';
            $titleData_publish = 'This category is selected in ' . trans("blogcategory::template.sidebar.blogs") . ', so it can&#39;t be deleted.';
        }
        $checkbox = '<a href="javascript:;" data-toggle="tooltip" data-placement="right" data-toggle="tooltip" data-original-title="' . $titleData_delete . '" title="' . $titleData_delete . '"><i style="color:red" class="fa fa-exclamation-triangle"></i></a>';
        if (Auth::user()->can('blog-category-delete') && $hasRecords == 0 && $this->currentUserRoleData->chrIsAdmin == 'Y') {
            $details .= '<a class=" delete" title="' . trans("blogcategory::template.common.delete") . '" data-controller="BlogCategory" data-alias = "' . $value->id . '" data-tab="T"><i class="fa fa-times"></i></a>';
        }
        $startDate = date('' . Config::get('Constant.DEFAULT_DATE_FORMAT') . ' ' . Config::get('Constant.DEFAULT_TIME_FORMAT') . '', strtotime($value->dtDateTime));
        $endDate = !empty($value->dtEndDateTime) ? date('' . Config::get('Constant.DEFAULT_DATE_FORMAT') . ' ' . Config::get('Constant.DEFAULT_TIME_FORMAT') . '', strtotime($value->dtEndDateTime)) : 'No Expiry';
        $title = $value->varTitle;
        if (Auth::user()->can('blog-category-edit')) {
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
            ($hasRecords == 0) ? '<input type="checkbox" name="delete" class="chkDelete" value="' . $value->id . '">' : $checkbox,
            '<div class="pages_title_div_row"><input type="hidden" id="draftid" value="' . $value->id . '">' . $title . ' ' . $status . $statusdata . '</div>',
            $startDate,
            $endDate,
            $webHits,
            $log,
            $value->intDisplayOrder,
        );
        return $records;
    }

    /**
     * This method delete multiples blogcategory
     * @return  true/false
     * @since   2017-07-15
     * @author  NetQuick
     */
    public function DeleteRecord(Request $request)
    {
        $value = Request::input('value');
        $data['ids'] = Request::input('ids');
        if (File::exists(app_path() . '/Comments.php') != null || File::exists(base_path() . '/packages/Powerpanel/Workflow/src/Models/Comments.php') != null) {
            Comments::deleteComments($data['ids'], Config::get('Constant.MODULE.MODEL_NAME'));
        }
        $moduleHaveFields = ['chrMain', 'chrIsPreview'];
        $update = MyLibrary::deleteMultipleRecords($data, $moduleHaveFields, $value, 'Powerpanel\BlogCategory\Models\BlogCategory');
        foreach ($update as $ids) {
            $ignoreDeleteScope = true;
            $Deleted_Record = BlogCategory::getRecordById($ids, $ignoreDeleteScope);
            $Cnt_Letest = BlogCategory::getRecordCount_letest($Deleted_Record['fkMainRecord'], $Deleted_Record['id']);
            if ($Cnt_Letest <= 0) {
                $updateLetest = [
                    'chrAddStar' => 'N',
                ];
                $whereConditionsApprove = ['id' => $Deleted_Record['fkMainRecord']];
                CommonModel::updateRecords($whereConditionsApprove, $updateLetest, false, 'Powerpanel\BlogCategory\Models\BlogCategory');
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
                    'intfkModuleId' => Config::get('Constant.MODULE.ID'),
                ];
                $updateMenuFields = [
                    'chrPublish' => 'N',
                    'chrDelete' => 'Y',
                    'chrActive' => 'N',
                ];
                CommonModel::updateRecords($whereConditions, $updateMenuFields, false, '\\Powerpanel\\Menu\\Models\\Menu');
                if ($value != "P" && $value != "F" && $value != "A" && $value != "D") {
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
    public function reorder()
    {
        $order = Request::input('order');
        $exOrder = Request::input('exOrder');
        MyLibrary::swapOrder($order, $exOrder, 'Powerpanel\BlogCategory\Models\BlogCategory');
        self::flushCache();
    }

    /**
     * This method handels swapping of available order record while adding
     * @param   order
     * @return  order
     * @since   2016-10-21
     * @author  NetQuick
     */
    public static function swap_order_add($order = null)
    {
        $response = false;
        $isCustomizeModule = true;
        $moduleHaveFields = ['chrMain', 'chrIsPreview'];
        if ($order != null) {
            $response = MyLibrary::swapOrderAdd($order, $isCustomizeModule, $moduleHaveFields, 'Powerpanel\BlogCategory\Models\BlogCategory');
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
    public static function swap_order_edit($order = null, $id = null)
    {
        $isCustomizeModule = true;
        $moduleHaveFields = ['chrMain', 'chrIsPreview'];
        MyLibrary::swapOrderEdit($order, $id, $isCustomizeModule, $moduleHaveFields, 'Powerpanel\BlogCategory\Models\BlogCategory');
        self::flushCache();
    }

    /**
     * This method destroys Banner in multiples
     * @return  Banner index view
     * @since   2016-10-25
     * @author  NetQuick
     */
    public function publish(Request $request)
    {
        $requestArr = Request::all();
//        $request = (object) $requestArr;
        $val = Request::get('val');
        $alias = Request::input('alias');
        $update = MyLibrary::setPublishUnpublish($alias, $val, 'Powerpanel\BlogCategory\Models\BlogCategory');
        self::flushCache();
        echo json_encode($update);
        exit;
    }

    public function recordHistory($data = false)
    {
        $returnHtml = '';
        $startDate = date('' . Config::get('Constant.DEFAULT_DATE_FORMAT') . ' ' . Config::get('Constant.DEFAULT_TIME_FORMAT') . '', strtotime($data->dtDateTime));
        $endDate = !empty($data->dtEndDateTime) ? date('' . Config::get('Constant.DEFAULT_DATE_FORMAT') . ' ' . Config::get('Constant.DEFAULT_TIME_FORMAT') . '', strtotime($data->dtEndDateTime)) : 'No Expiry';

        if (isset($data->txtDescription) && $data->txtDescription != '') {

            $desc = FrontPageContent_Shield::renderBuilder($data->txtDescription);
            if (isset($desc['response']) && !empty($desc['response'])) {
                $desc = $desc['response'];
            } else {
                $desc = '---';
            }

        } else {
            $desc = '---';
        }

        $returnHtml .= '<table class="new_table_desing table table-striped table-bordered table-hover">
				<thead>
						<tr>
								<th align="center">' . trans("blogcategory::template.common.title") . '</th>
																																		 <th align="center">Description</th>
								<th align="center">Start Date</th>
								<th align="center">End Date</th>
																																<th align="center">Meta Title</th>
																																<th align="center">Meta Description</th>
								<th align="center">Display Order</th>
								<th align="center">' . trans("blogcategory::template.common.publish") . '</th>
						</tr>
				</thead>
				<tbody>
						<tr>
								<td align="center">' . stripslashes($data->varTitle) . '</td>
						        <td align="center">' . $desc . '</td>
																																<td align="center">' . $startDate . '</td>
								<td align="center">' . $endDate . '</td>
																																		<td align="center">' . stripslashes($data->varMetaTitle) . '</td>
																																				<td align="center">' . stripslashes($data->varMetaDescription) . '</td>
								<td align="center">' . $data->intDisplayOrder . '</td>
								<td align="center">' . $data->chrPublish . '</td>
						</tr>
				</tbody>
				</table>';
        return $returnHtml;
    }

    public function newrecordHistory($data = false, $newdata = false)
    {
        $returnHtml = '';
        if ($data->varTitle != $newdata->varTitle) {
            $titlecolor = 'style="background-color:#f5efb7"';
        } else {
            $titlecolor = '';
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
        if ($data->varMetaTitle != $newdata->varMetaTitle) {
            $metatitlecolor = 'style="background-color:#f5efb7"';
        } else {
            $metatitlecolor = '';
        }
        if ($data->varMetaDescription != $newdata->varMetaDescription) {
            $metadesccolor = 'style="background-color:#f5efb7"';
        } else {
            $metadesccolor = '';
        }
        if ($data->txtDescription != $newdata->txtDescription) {
            $desccolor = 'style="background-color:#f5efb7"';
        } else {
            $desccolor = '';
        }

        if (isset($newdata->txtDescription) && $newdata->txtDescription != '') {

            $desc = FrontPageContent_Shield::renderBuilder($newdata->txtDescription);
            if (isset($desc['response']) && !empty($desc['response'])) {
                $desc = $desc['response'];
            } else {
                $desc = '---';
            }

        } else {
            $desc = '---';
        }

        $startDate = date('' . Config::get('Constant.DEFAULT_DATE_FORMAT') . ' ' . Config::get('Constant.DEFAULT_TIME_FORMAT') . '', strtotime($newdata->dtDateTime));
        $endDate = !empty($newdata->dtEndDateTime) ? date('' . Config::get('Constant.DEFAULT_DATE_FORMAT') . ' ' . Config::get('Constant.DEFAULT_TIME_FORMAT') . '', strtotime($newdata->dtEndDateTime)) : 'No Expiry';
        $returnHtml .= '<table class="new_table_desing table table-striped table-bordered table-hover">
				<thead>
						<tr>
								<th align="center">' . trans("blogcategory::template.common.title") . '</th>
																																		<th align="center">Description</th>
								<th align="center">Start Date</th>
								<th align="center">End Date</th>
																																 <th align="center">Meta Title</th>
																																 <th align="center">Meta Description</th>
								<th align="center">Display Order</th>
								<th align="center">' . trans("blogcategory::template.common.publish") . '</th>
						</tr>
				</thead>
				<tbody>
						<tr>
								<td align="center" ' . $titlecolor . '>' . stripslashes($newdata->varTitle) . '</td>
					            <td align="center" ' . $desccolor . '>' . $desc . '</td>
																																<td align="center" ' . $DateTimecolor . '>' . $startDate . '</td>
								<td align="center" ' . $EndDateTimecolor . '>' . $endDate . '</td>
																																		<td align="center" ' . $metatitlecolor . '>' . stripslashes($newdata->varMetaTitle) . '</td>
																			 <td align="center" ' . $metadesccolor . '>' . stripslashes($newdata->varMetaDescription) . '</td>
								<td align="center" ' . $ordercolor . '>' . $newdata->intDisplayOrder . '</td>
								<td align="center" ' . $Publishcolor . '>' . $newdata->chrPublish . '</td>
						</tr>
				</tbody>
				</table>';
        return $returnHtml;
    }

    /**
     * This method stores blogcategory modifications
     * @return  View
     * @since   2017-11-10
     * @author  NetQuick
     */
    public function addPreview(Guard $auth)
    {
        $data = Request::input();
        $rules = array(
            'title' => 'required|max:160|handle_xss|no_url',
            'varMetaTitle' => 'required|max:500|handle_xss|no_url',
            'varMetaDescription' => 'required|max:500|handle_xss|no_url',
            'alias' => 'required',
            'order' => 'required|greater_than_zero|handle_xss|no_url',
        );
        $actionMessage = trans('blogcategory::template.common.oppsSomethingWrong');
        $messsages = array();
        $validator = Validator::make($data, $rules, $messsages);
        if (Config::get('Constant.DEFAULT_VISUAL') == 'Y') {
            if ($data['section'] != '[]') {
                $vsection = $data['section'];
            } else {
                $vsection = '';
            }
        } else {
            $vsection = $data['description'];
        }
        $blogCategoryArr = [];
        $blogCategoryArr['varTitle'] = stripslashes(trim($data['title']));
        $blogCategoryArr['dtDateTime'] = !empty($data['start_date_time']) ? date('Y-m-d H:i:s', strtotime($data['start_date_time'])) : date('Y-m-d H:i:s');
        $blogCategoryArr['dtEndDateTime'] = !empty($data['end_date_time']) ? date('Y-m-d H:i:s', strtotime($data['end_date_time'])) : null;
        $blogCategoryArr['txtDescription'] = $vsection;
        $blogCategoryArr['varMetaTitle'] = stripslashes(trim($data['varMetaTitle']));
        $blogCategoryArr['varMetaDescription'] = stripslashes(trim($data['varMetaDescription']));
        $blogCategoryArr['chrPublish'] = isset($data['chrMenuDisplay']) ? $data['chrMenuDisplay'] : 'Y';
        $blogCategoryArr['chrIsPreview'] = 'Y';
        $id = $data['previewId'];
        if (is_numeric($id)) { #Edit post Handler=======
        if ($data['oldAlias'] != $data['alias']) {
            Alias::updateAlias($data['oldAlias'], $data['alias']);
        }
            $blogCategory = BlogCategory::getRecordForLogById($id);
            $whereConditions = ['id' => $blogCategory->id];
            $update = CommonModel::updateRecords($whereConditions, $blogCategoryArr, false, 'Powerpanel\BlogCategory\Models\BlogCategory');
            if ($update) {
                if (!empty($id)) {
                    $logArr = MyLibrary::logData($blogCategory->id);
                    if (Auth::user()->can('log-advanced')) {
                        $newBlogCategoryObj = BlogCategory::getRecordForLogById($blogCategory->id);
                        $oldRec = $this->recordHistory($blogCategory);
                        $newRec = $this->recordHistory($newBlogCategoryObj);
                        $logArr['old_val'] = $oldRec;
                        $logArr['new_val'] = $newRec;
                    }
                    $logArr['varTitle'] = stripslashes(trim($data['title']));
                    Log::recordLog($logArr);
                    if (Auth::user()->can('recent-updates-list')) {
                        if (!isset($newBlogCategoryObj)) {
                            $newBlogCategoryObj = BlogCategory::getRecordForLogById($blogCategory->id);
                        }
                        $notificationArr = MyLibrary::notificationData($blogCategory->id, $newBlogCategoryObj);
                        RecentUpdates::setNotification($notificationArr);
                    }
                    self::flushCache();
                    $actionMessage = trans('blogcategory::template.blogCategoryModule.updateMessage');
                }
            }
        } else { #Add post Handler=======
        $blogCategoryArr['intAliasId'] = MyLibrary::insertAlias($data['alias'], false, 'Y');
            $id = CommonModel::addRecord($blogCategoryArr, 'Powerpanel\BlogCategory\Models\BlogCategory');
        }
        return json_encode(array('status' => $id, 'alias' => $data['alias'], 'message' => trans('template.pageModule.pageUpdate')));
    }

    public static function flushCache()
    {
        Cache::tags('BlogCategory')->flush();
    }

    public function tableData_tab1($value = false)
    {
        $Hits = Pagehit::where('fkIntAliasId', $value->intAliasId)->count();
        $webHits = '';
        if ($Hits > 0) {
            $webHits .= '<a data-toggle="modal" href="#" onclick=\'HitsPopup("' . $value->id . '","' . $value->intAliasId . '","' . $value->varTitle . '","A")\'>' . $Hits . '</a>
                    <div class="new_modal modal fade" id="desc_' . $value->id . '_A" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog" style="margin: 0 auto;display:table;width: 100%;height:100%;max-width: 1000px;">
                        <div class="modal-vertical">
                        <div class="modal-content">
                    <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                    <h3 class="modal-title">Hits Report</h3>
                    </div>
                    <div class="modal-body">
                    <div id="webdata_' . $value->id . '_A"></div>
                    </div>
                    </div>
                    </div>
                    </div>
                    </div>';
        } else {
            $webHits .= '0';
        }
        $hasRecords = Blogs::getCountById($value->id);
        $details = '';
        $publish_action = '';
        $titleData_delete = "";
        $titleData_publish = "";
        if ($hasRecords > 0) {
            $titleData_delete .= 'This category is selected in ' . trans("blogcategory::template.sidebar.blogs") . ', so it can&#39;t be deleted.';
            $titleData_publish = 'This category is selected in ' . trans("blogcategory::template.sidebar.blogs") . ', so it can&#39;t be deleted.';
        }
        $checkbox = '<a href="javascript:;" data-toggle="tooltip" data-placement="right" data-toggle="tooltip" data-original-title="' . $titleData_delete . '" title="' . $titleData_delete . '"><i style="color:red" class="fa fa-exclamation-triangle"></i></a>';
        $checkbox_publish = '<a href="javascript:;" data-toggle="tooltip" data-placement="right" data-toggle="tooltip" data-original-title="' . $titleData_publish . '" title="' . $titleData_publish . '"><i style="color:red" class="fa fa-exclamation-triangle"></i></a>';
        if (Auth::user()->can('blog-category-edit')) {
            $details .= '<a class="" title="' . trans("blogcategory::template.common.edit") . '" href="' . route('powerpanel.blog-category.edit', array('alias' => $value->id)) . '"><i class="fa fa-pencil"></i></a>';
        }
        if (Auth::user()->can('blog-category-delete') && $hasRecords == 0 && $this->currentUserRoleData->chrIsAdmin == 'Y') {
            if (Config::get('Constant.DEFAULT_TRASH') == 'Y') {
                $details .= '<a class="delete-grid" title="' . trans("blogcategory::template.common.delete") . '" data-controller="BlogCategory" onclick = \'Trashfun("' . $value->id . '")\' data-alias = "' . $value->id . '" data-tab="A"><i class="fa fa-times"></i></a>';
            } else {
                $details .= '<a class=" delete" title="' . trans("blogcategory::template.common.delete") . '" data-controller="BlogCategory" data-alias = "' . $value->id . '" data-tab="A"><i class="fa fa-times"></i></a>';
            }
        }
        $startDate = date('' . Config::get('Constant.DEFAULT_DATE_FORMAT') . ' ' . Config::get('Constant.DEFAULT_TIME_FORMAT') . '', strtotime($value->dtDateTime));
        $endDate = !empty($value->dtEndDateTime) ? date('' . Config::get('Constant.DEFAULT_DATE_FORMAT') . ' ' . Config::get('Constant.DEFAULT_TIME_FORMAT') . '', strtotime($value->dtEndDateTime)) : 'No Expiry';
        if (Auth::user()->can('blog-category-reviewchanges')) {
            $update = "<a title=\"Click here to see all approval records.\" class=\"icon_title1\" style=\"margin-right: 5px;\" onclick=\"javascript:expandcollapsepanel(this ,'tasklisting" . $value->id . "', 'mainsingnimg" . $value->id . "'," . $value->id . ")\"><i id=\"mainsingnimg" . $value->id . "\" class=\"la la-plus-square\"></i></a>";
            $rollback = "<a title=\"Click here to see all approved records to rollback.\"  class=\"icon_title2\" style=\"margin-right: 5px;\" onclick=\"javascript:expandcollapsepanel_rolback(this ,'tasklisting_rollback" . $value->id . "', 'mainsingnimg_rollback" . $value->id . "'," . $value->id . ")\"><i id=\"mainsingnimg_rollback" . $value->id . "\" class=\"la la-history\"></i></a>";
        } else {
            $update = '';
            $rollback = '';
        }
        if (Auth::user()->can('blog-category-reviewchanges') && $value->chrAddStar == 'Y') {
            $star = 'addhiglight';
        } else {
            $star = '';
        }
        $Quickedit_startDate = date('Y-m-d H:i', strtotime($value->dtDateTime));
        $Quickedit_endDate = !empty($value->dtEndDateTime) ? date('Y-m-d H:i', strtotime($value->dtEndDateTime)) : 'No Expiry';
        $title = $value->varTitle;
        if (Auth::user()->can('blog-category-edit')) {
            if ($value->chrDraft == 'D' || $value->chrAddStar == 'Y') {
                $viewlink = url('/previewpage?url=' . MyLibrary::getFrontUri('blog-category')['uri'] . '/' . $value->id . '/preview');
                $linkviewLable = "Preview";
            } else {
                $viewlink = MyLibrary::getFrontUri('blog-category', $value->id)['uri'];
                $linkviewLable = "View";
            }
            if ($value->chrLock != 'Y') {
                $title = '<div class="quick_edit"><a href = "' . route('powerpanel.blog-category.edit', array('alias' => $value->id)) . '?tab=A">' . $value->varTitle . '</a> <div class="quick_edit_menu">
                            <span><a href="' . route('powerpanel.blog-category.edit', array('alias' => $value->id)) . '?tab=A" title="Edit">Edit</a></span>';
                if ($hasRecords == 0) {
                    if (Config::get('Constant.DEFAULT_TRASH') == 'Y') {
                        $title .= '<span><a title = "Trash" href = \'javascript:;\' onclick=\'Trashfun("' . $value->id . '")\' class="red" data-tab="A">Trash</a></span>';
                    }
                }
                $title .= '<span><a href="' . $viewlink . '" target="_blank" title="' . $linkviewLable . '" >' . $linkviewLable . '</a></span></div></div>';
            } else {
                if (auth()->user()->id != $value->LockUserID) {
                    if ($this->currentUserRoleData->chrIsAdmin == 'Y') {
                        $title = '<div class="quick_edit"><a href = "' . route('powerpanel.blog-category.edit', array('alias' => $value->id)) . '?tab=A">' . $value->varTitle . '</a> <div class="quick_edit_menu">
	                            <span><a href="' . route('powerpanel.blog-category.edit', array('alias' => $value->id)) . '?tab=A" title="Edit">Edit</a></span>';

                        $title .= '<span><a href="' . $viewlink . '" target="_blank" title="' . $linkviewLable . '" >' . $linkviewLable . '</a></span></div></div>';
                    } else {
                        $title = '<div class="quick_edit"><a href = "javascript:;">' . $value->varTitle . '</a></div>';
                    }
                } else {
                    $title = '<div class="quick_edit"><a href = "' . route('powerpanel.blog-category.edit', array('alias' => $value->id)) . '?tab=A">' . $value->varTitle . '</a> <div class="quick_edit_menu">
	                            <span><a href="' . route('powerpanel.blog-category.edit', array('alias' => $value->id)) . '?tab=A" title="Edit">Edit</a></span>';

                    $title .= '<span><a href="' . $viewlink . '" target="_blank" title="' . $linkviewLable . '" >' . $linkviewLable . '</a></span></div></div>';
                }
            }
        }
        $pubbtn = $value->chrPageActive;
        $pbtn = '';
        if (Config::get('Constant.DEFAULT_VISIBILITY') == 'Y') {
            if ($pubbtn == 'PU') {
                $pbtn = '<div class="pub_status publicdiv" data-toggle="tooltip" title="Public"><span>Public</span></div>';
            } elseif ($pubbtn == 'PR') {
                $pbtn = '<div class="pub_status privatediv" data-toggle="tooltip" title="Private"><span>Private</span></div>';
            } elseif ($pubbtn == 'PP') {
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
            $status .= '<em>(' . Config::get('Constant.DRAFT_LIST') . ')</em> ';
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

    public function getChildData()
    {
        $childHtml = "";
        $Cmspage_childData = "";
        $Cmspage_childData = BlogCategory::getChildGrid();
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
                $previewlink = url('/previewpage?url=' . MyLibrary::getFrontUri('blog-category')['uri'] . '/' . $child_row->id . '/preview');
                $childHtml .= "<td class=\"text-center\"><span class='mob_show_title'>Preview: </span><a class='icon_round' href=" . $previewlink . " target='_blank'><i class=\"fa fa-desktop\"></i></a></td>";
                if ($child_row->chrApproved == 'N') {
                    $childHtml .= "<td class=\"text-center\"><span class='mob_show_title'>Edit: </span><a class='icon_round' title='" . trans("blogcategory::template.common.edit") . "' href='" . route('powerpanel.blog-category.edit', array('alias' => $child_row->id)) . "'>
							<i class='fa fa-pencil'></i></a></td>";
                } else {
                    $childHtml .= "<td class=\"text-center\"><span class='mob_show_title'>Edit: </span>-</td>";
                }
                if ($child_row->chrApproved == 'N') {
                    $childHtml .= "<td class=\"text-center\"><span class='mob_show_title'>Status: </span><a class=\"approve_icon_btn\" title='" . trans("blogcategory::template.common.comments") . "' href=\"javascript:;\" onclick=\"loadModelpopup('" . $child_row->id . "','" . $child_row->UserID . "','" . Config::get('Constant.MODULE.MODEL_NAME') . "','" . $child_row->fkMainRecord . "')\"><i class=\"fa fa-comments\"></i> <span>Comment</span></a>    <a class=\"approve_icon_btn\" onclick=\"update_mainrecord('" . $child_row->id . "','" . $child_row->fkMainRecord . "','" . $child_row->UserID . "','A');\" title='" . trans("blogcategory::template.common.clickapprove") . "' href=\"javascript:;\"><i class=\"fa fa-check-square-o\"></i> <span>Approve</span></a></td>";
                } else {
                    $childHtml .= "<td class=\"text-center\"><span class='mob_show_title'>Status: </span><span class='mob_show_overflow'><i class=\"la la-check-circle\" style=\"font-size:30px;\"></i><span style=\"display:block\"><strong>Approved On: </strong>" . date('M d Y h:i A', strtotime($child_row->dtApprovedDateTime)) . "</span><span style=\"display:block\"><strong>Approved By: </strong>" . CommonModel::getUserName($child_row->intApprovedBy) . "</span></span></td>";
                }
                $childHtml .= "</tr>";
            }
        } else {
            $childHtml .= "<tr><td colspan='7'>No Records</td></tr>";
        }
        $childHtml .= "</tr></td></tr>";
        $childHtml .= "</tr>
						</table>";
        echo $childHtml;
        exit;
    }

    public function getChildData_rollback()
    {
        $child_rollbackHtml = "";
        $Cmspage_rollbackchildData = "";
        $Cmspage_rollbackchildData = BlogCategory::getChildrollbackGrid();
        $child_rollbackHtml .= "<div class=\"producttbl producttb2\" style=\"\">";
        $child_rollbackHtml .= "<table class=\"new_table_desing table table-striped table-bordered table-hover table-checkable dataTable\" id=\"email_log_datatable_ajax\">
                                <tr role=\"row\">
                                    <th class=\"text-center\">Title</th>
                                    <th class=\"text-center\">Date</th>
                                    <th class=\"text-center\">User</th>
                                    <th class=\"text-center\">Preview</th>
                                    <th class=\"text-center\">Status</th>";
        $child_rollbackHtml .= "</tr>";
        if (count($Cmspage_rollbackchildData) > 0) {
            foreach ($Cmspage_rollbackchildData as $child_rollbacrow) {
                $child_rollbackHtml .= "<tr role=\"row\">";
                $child_rollbackHtml .= '<td class="text-center"><span class="mob_show_title">Title: </span>' . $child_rollbacrow->varTitle . '</td>';
                $child_rollbackHtml .= "<td class=\"text-center\"><span class='mob_show_title'>Date: </span>" . date('M d Y h:i A', strtotime($child_rollbacrow->created_at)) . "</td>";
                $child_rollbackHtml .= "<td class=\"text-center\"><span class='mob_show_title'>User: </span>" . CommonModel::getUserName($child_rollbacrow->UserID) . "</td>";
                $previewlink = url('/previewpage?url=' . MyLibrary::getFrontUri('blog-category')['uri'] . '/' . $child_rollbacrow->id . '/preview');
                $child_rollbackHtml .= "<td class=\"text-center\"><span class='mob_show_title'>Preview: </span><a class='icon_round' href=" . $previewlink . " target='_blank'><i class=\"fa fa-desktop\"></i></a></td>";
                if ($child_rollbacrow->chrApproved == 'Y') {
                    $child_rollbackHtml .= "<td class=\"text-center\"><span class='mob_show_title'>Status: </span><i class=\"la la-check-circle\" style=\"color: #1080F2;font-size:30px;\"></i></td>";
                } else {
                    // $child_rollbackHtml .= "<td class=\"text-center\"><span class='mob_show_title'>Status: </span><a onclick=\"update_mainrecord('" . $child_rollbacrow->id . "','" . $child_rollbacrow->fkMainRecord . "','" . $child_rollbacrow->UserID . "','R');\"  class=\"approve_icon_btn\">
                    // <i class=\"fa fa-history\"></i>  <span>RollBack</span>
                    // </a></td>";
                    $child_rollbackHtml .= "<td class=\"text-center\"><span class=\"glyphicon glyphicon-minus\"></span></td>";
                }
                $child_rollbackHtml .= "</tr>";
            }
        } else {
            $child_rollbackHtml .= "<tr><td colspan='7'>No Records</td></tr>";
        }
        echo $child_rollbackHtml;
        exit;
    }

    public function ApprovedData_Listing(Request $request)
    {
        $requestArr = Request::all();
        $request = (object) $requestArr;
        $id = Request::post('id');
        $approvalid = Request::post('id');
        $main_id = Request::post('main_id');
        $flag = Request::post('flag');
        $approvalData = BlogCategory::getOrderOfApproval($id);
        $message = BlogCategory::approved_data_Listing($request);
        if (!empty($approvalData)) {
            self::swap_order_edit($approvalData->intDisplayOrder, $main_id);
        }
        $newCmsPageObj = BlogCategory::getRecordForLogById($main_id);
        $approval_obj = BlogCategory::getRecordForLogById($approvalid);
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
        $careers = BlogCategory::getRecordForLogById($id);
        if (method_exists($this->MyLibrary, 'userNotificationData')) {
            $userNotificationArr = MyLibrary::userNotificationData(Config::get('Constant.MODULE.ID'));
            $userNotificationArr['fkRecordId'] = $id;
            $userNotificationArr['txtNotification'] = 'Your request has been approved by ' . ucfirst(auth()->user()->name) . ' (' . ucfirst(Config::get('Constant.MODULE.NAME')) . ')';
            $userNotificationArr['fkIntUserId'] = Auth::user()->id;
            $userNotificationArr['chrNotificationType'] = 'A';
            $userNotificationArr['intOnlyForUserId'] = $careers->UserID;
            UserNotification::addRecord($userNotificationArr);
        }
        /* notification for user to record approved */
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

    public function Get_Comments(Request $request)
    {
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

    public function get_builder_list()
    {
        $records = BlogCategory::getAllCategory();
        $opt = '<option value="">Category</option>';
        foreach ($records as $record) {
            $opt .= '<option value="' . $record->id . '">' . $record->varTitle . '</option>';
        }
        return $opt;
    }

    public function getAllCategory()
    {
        $records = BlogCategory::getAllCategory();
        $opt = '<option value="">Select Category</option>';
        foreach ($records as $record) {
            $opt .= '<option value="' . $record->id . '">' . $record->varTitle . '</option>';
        }
        return $opt;
    }

    public function addCatAjax()
    {
        $data = Request::input();
        return AddCategoryAjax::Add($data, 'BlogCategory');
    }

    public function rollBackRecord(Request $request)
    {

        $message = 'Oops! Something went wrong';
        $requestArr = Request::all();
        $request = (object) $requestArr;

        $previousRecord = BlogCategory::getPreviousRecordByMainId($request->id);
        if (!empty($previousRecord)) {

            $main_id = $previousRecord->fkMainRecord;
            $request->id = $previousRecord->id;
            $request->main_id = $main_id;

            $message = BlogCategory::approved_data_Listing($request);

            $newBlogObj = BlogCategory::getRecordForLogById($main_id);
            $restoredata = Config::get('Constant.ROLLBACK_RECORD');

            /* notification for user to record approved */
            $blogs = BlogCategory::getRecordForLogById($previousRecord->id);
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
