<?php

namespace Powerpanel\PhotoGallery\Controllers\Powerpanel;

use Request;
use Illuminate\Support\Facades\Redirect;
use Powerpanel\Workflow\Models\Comments;
use Powerpanel\PhotoGallery\Models\PhotoGallery;
use Powerpanel\Workflow\Models\WorkflowLog;
use Powerpanel\Workflow\Models\Workflow;
use App\Log;
use App\RecentUpdates;
use App\Alias;
use Validator;
use DB;
use Config;
use App\Http\Controllers\PowerpanelController;
use Crypt;
use Auth;
use File;
use App\Helpers\MyLibrary;
use App\CommonModel;
use App\Helpers\AddImageModelRel;
use Carbon\Carbon;
use Cache;
use App\Helpers\Category_builder;
use App\Helpers\CategoryArrayBuilder;
use App\Modules;
use Powerpanel\RoleManager\Models\Role_user;
use App\Helpers\resize_image;
use Powerpanel\PhotoAlbum\Models\PhotoAlbum;
use App\UserNotification;
use App\User;

class PhotoGalleryController extends PowerpanelController {

    /**
     * Create a new controller instance.
     * @return void
     */
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
     * This method handels load photo-gallery grid
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
        $total = PhotoGallery::getRecordCount();
        $draftTotalRecords = PhotoGallery::getRecordCountforListDarft(false, true, $userIsAdmin, array());
        $trashTotalRecords = PhotoGallery::getRecordCountforListTrash();
        $favoriteTotalRecords = PhotoGallery::getRecordCountforListFavorite();
        $NewRecordsCount = PhotoGallery::getNewRecordsCount();
        // \\$photoAlbumsDropDownFilterList = PhotoAlbum::getPhotoAlbumDropdwonFilterList();
        $this->breadcrumb['title'] = trans('photogallery::template.photoGalleryModule.managePhotoGallery');
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
        return view('photo-gallery::powerpanel.index', ['iTotalRecords' => $total, 'breadcrumb' => $this->breadcrumb, 'NewRecordsCount' => $NewRecordsCount, 'userIsAdmin' => $userIsAdmin, 'draftTotalRecords' => $draftTotalRecords, 'trashTotalRecords' => $trashTotalRecords, 'favoriteTotalRecords' => $favoriteTotalRecords, 'settingarray' => json_encode($settingarray)]);
    }

    /**
     * This method handels list of photo-gallery with filters
     * @return  View
     * @since   2017-07-20
     * @author  NetQuick
     */
    public function get_list() {
        /* Start code for sorting */
        $filterArr = [];
        $records = array();
        $records["data"] = array();
        $filterArr['orderColumnNo'] = (!empty(Request::get('order') [0]['column']) ? Request::get('order') [0]['column'] : '');
        $filterArr['orderByFieldName'] = (!empty(Request::get('columns') [$filterArr['orderColumnNo']]['name']) ? Request::get('columns') [$filterArr['orderColumnNo']]['name'] : '');
        $filterArr['orderTypeAscOrDesc'] = (!empty(Request::get('order') [0]['dir']) ? Request::get('order') [0]['dir'] : '');
        $filterArr['statusFilter'] = !empty(Request::get('statusValue')) ? Request::get('statusValue') : '';
        $filterArr['albumFilter'] = !empty(Request::get('albumValue')) ? Request::get('albumValue') : '';
        $filterArr['searchFilter'] = !empty(Request::get('searchValue')) ? Request::get('searchValue') : '';
        $filterArr['iDisplayLength'] = intval(Request::get('length'));
        $filterArr['iDisplayStart'] = intval(Request::get('start'));
        $sEcho = intval(Request::get('draw'));
        $isAdmin = false;
        if (isset($this->currentUserRoleData) && !empty($this->currentUserRoleData)) {
            if ($this->currentUserRoleData->chrIsAdmin == 'Y') {
                $isAdmin = true;
            }
        }
        $arrResults = PhotoGallery::getRecordList($filterArr, $isAdmin);
        $iTotalRecords = PhotoGallery::getRecordCountforList($filterArr, true, $isAdmin);
        $end = $filterArr['iDisplayStart'] + $filterArr['iDisplayLength'];
        $end = $end > $iTotalRecords ? $iTotalRecords : $end;
        $tableSortedType = (isset($filterArr['orderTypeAscOrDesc']) && $filterArr['orderTypeAscOrDesc'] != "") ? $filterArr['orderTypeAscOrDesc'] : '';
        $totalRecords = PhotoGallery::getRecordCount();
        if (count($arrResults) > 0 && !empty($arrResults)) {
            foreach ($arrResults as $key => $value) {
                $records["data"][] = $this->tableData($value, $totalRecords, $tableSortedType);
            }
        }
        $NewRecordsCount = PhotoGallery::getNewRecordsCount();
        $records["newRecordCount"] = $NewRecordsCount;
        $records["customActionStatus"] = "OK";
        $records["draw"] = $sEcho;
        $records["recordsTotal"] = $iTotalRecords;
        $records["recordsFiltered"] = $iTotalRecords;
        echo json_encode($records);
        exit;
    }

    /**
     * This method handels list of photo-gallery with filters
     * @return  View
     * @since   2017-07-20
     * @author  NetQuick
     */
    public function get_list_favorite() {
        /* Start code for sorting */
        $filterArr = [];
        $records = array();
        $records["data"] = array();
        $filterArr['orderColumnNo'] = (!empty(Request::get('order') [0]['column']) ? Request::get('order') [0]['column'] : '');
        $filterArr['orderByFieldName'] = (!empty(Request::get('columns') [$filterArr['orderColumnNo']]['name']) ? Request::get('columns') [$filterArr['orderColumnNo']]['name'] : '');
        $filterArr['orderTypeAscOrDesc'] = (!empty(Request::get('order') [0]['dir']) ? Request::get('order') [0]['dir'] : '');
        $filterArr['statusFilter'] = !empty(Request::get('statusValue')) ? Request::get('statusValue') : '';
        $filterArr['albumFilter'] = !empty(Request::get('albumValue')) ? Request::get('albumValue') : '';
        $filterArr['searchFilter'] = !empty(Request::get('searchValue')) ? Request::get('searchValue') : '';
        $filterArr['iDisplayLength'] = intval(Request::get('length'));
        $filterArr['iDisplayStart'] = intval(Request::get('start'));
        $sEcho = intval(Request::get('draw'));
        $isAdmin = false;
        if ($this->currentUserRoleData->chrIsAdmin == 'Y') {
            $isAdmin = true;
        }
        $arrResults = PhotoGallery::getRecordListFavorite($filterArr, $isAdmin);
        $iTotalRecords = PhotoGallery::getRecordCountforListFavorite($filterArr, true, $isAdmin);
        $end = $filterArr['iDisplayStart'] + $filterArr['iDisplayLength'];
        $end = $end > $iTotalRecords ? $iTotalRecords : $end;
        $tableSortedType = (isset($filterArr['orderTypeAscOrDesc']) && $filterArr['orderTypeAscOrDesc'] != "") ? $filterArr['orderTypeAscOrDesc'] : '';
        $totalRecords = PhotoGallery::getRecordCount();
        if (count($arrResults) > 0 && !empty($arrResults)) {
            foreach ($arrResults as $key => $value) {
                $records["data"][] = $this->tableDataFavorite($value, $totalRecords, $tableSortedType);
            }
        }
        $NewRecordsCount = PhotoGallery::getNewRecordsCount();
        $records["newRecordCount"] = $NewRecordsCount;
        $records["customActionStatus"] = "OK";
        $records["draw"] = $sEcho;
        $records["recordsTotal"] = $iTotalRecords;
        $records["recordsFiltered"] = $iTotalRecords;
        echo json_encode($records);
        exit;
    }

    /**
     * This method handels list of photo-gallery with filters
     * @return  View
     * @since   2017-07-20
     * @author  NetQuick
     */
    public function get_list_draft() {
        /* Start code for sorting */
        $filterArr = [];
        $records = array();
        $records["data"] = array();
        $filterArr['orderColumnNo'] = (!empty(Request::get('order') [0]['column']) ? Request::get('order') [0]['column'] : '');
        $filterArr['orderByFieldName'] = (!empty(Request::get('columns') [$filterArr['orderColumnNo']]['name']) ? Request::get('columns') [$filterArr['orderColumnNo']]['name'] : '');
        $filterArr['orderTypeAscOrDesc'] = (!empty(Request::get('order') [0]['dir']) ? Request::get('order') [0]['dir'] : '');
        $filterArr['statusFilter'] = !empty(Request::get('statusValue')) ? Request::get('statusValue') : '';
        $filterArr['albumFilter'] = !empty(Request::get('albumValue')) ? Request::get('albumValue') : '';
        $filterArr['searchFilter'] = !empty(Request::get('searchValue')) ? Request::get('searchValue') : '';
        $filterArr['iDisplayLength'] = intval(Request::get('length'));
        $filterArr['iDisplayStart'] = intval(Request::get('start'));
        $sEcho = intval(Request::get('draw'));
        $isAdmin = false;
        if ($this->currentUserRoleData->chrIsAdmin == 'Y') {
            $isAdmin = true;
        }
        $arrResults = PhotoGallery::getRecordListDraft($filterArr, $isAdmin);
        $iTotalRecords = PhotoGallery::getRecordCountforListDarft($filterArr, true, $isAdmin);
        $end = $filterArr['iDisplayStart'] + $filterArr['iDisplayLength'];
        $end = $end > $iTotalRecords ? $iTotalRecords : $end;
        $tableSortedType = (isset($filterArr['orderTypeAscOrDesc']) && $filterArr['orderTypeAscOrDesc'] != "") ? $filterArr['orderTypeAscOrDesc'] : '';
        $totalRecords = PhotoGallery::getRecordCount();
        if (count($arrResults) > 0 && !empty($arrResults)) {
            foreach ($arrResults as $key => $value) {
                $records["data"][] = $this->tableDataDraft($value, $totalRecords, $tableSortedType);
            }
        }
        $NewRecordsCount = PhotoGallery::getNewRecordsCount();
        $records["newRecordCount"] = $NewRecordsCount;
        $records["customActionStatus"] = "OK";
        $records["draw"] = $sEcho;
        $records["recordsTotal"] = $iTotalRecords;
        $records["recordsFiltered"] = $iTotalRecords;
        echo json_encode($records);
        exit;
    }

    /**
     * This method handels list of photo-gallery with filters
     * @return  View
     * @since   2017-07-20
     * @author  NetQuick
     */
    public function get_list_trash() {
        /* Start code for sorting */
        $filterArr = [];
        $records = array();
        $records["data"] = array();
        $filterArr['orderColumnNo'] = (!empty(Request::get('order') [0]['column']) ? Request::get('order') [0]['column'] : '');
        $filterArr['orderByFieldName'] = (!empty(Request::get('columns') [$filterArr['orderColumnNo']]['name']) ? Request::get('columns') [$filterArr['orderColumnNo']]['name'] : '');
        $filterArr['orderTypeAscOrDesc'] = (!empty(Request::get('order') [0]['dir']) ? Request::get('order') [0]['dir'] : '');
        $filterArr['statusFilter'] = !empty(Request::get('statusValue')) ? Request::get('statusValue') : '';
        $filterArr['albumFilter'] = !empty(Request::get('albumValue')) ? Request::get('albumValue') : '';
        $filterArr['searchFilter'] = !empty(Request::get('searchValue')) ? Request::get('searchValue') : '';
        $filterArr['iDisplayLength'] = intval(Request::get('length'));
        $filterArr['iDisplayStart'] = intval(Request::get('start'));
        $sEcho = intval(Request::get('draw'));
        $isAdmin = false;
        if ($this->currentUserRoleData->chrIsAdmin == 'Y') {
            $isAdmin = true;
        }
        $arrResults = PhotoGallery::getRecordListTrash($filterArr, $isAdmin);
        $iTotalRecords = PhotoGallery::getRecordCountforListTrash($filterArr, true, $isAdmin);
        $end = $filterArr['iDisplayStart'] + $filterArr['iDisplayLength'];
        $end = $end > $iTotalRecords ? $iTotalRecords : $end;
        $tableSortedType = (isset($filterArr['orderTypeAscOrDesc']) && $filterArr['orderTypeAscOrDesc'] != "") ? $filterArr['orderTypeAscOrDesc'] : '';
        $totalRecords = PhotoGallery::getRecordCount();
        if (count($arrResults) > 0 && !empty($arrResults)) {
            foreach ($arrResults as $key => $value) {
                $records["data"][] = $this->tableDataTrash($value, $totalRecords, $tableSortedType);
            }
        }
        $NewRecordsCount = PhotoGallery::getNewRecordsCount();
        $records["newRecordCount"] = $NewRecordsCount;
        $records["customActionStatus"] = "OK";
        $records["draw"] = $sEcho;
        $records["recordsTotal"] = $iTotalRecords;
        $records["recordsFiltered"] = $iTotalRecords;
        echo json_encode($records);
        exit;
    }

    public function get_list_New() {
        /* Start code for sorting */
        $filterArr = [];
        $records = array();
        $records["data"] = array();
        $filterArr['orderColumnNo'] = (!empty(Request::get('order') [0]['column']) ? Request::get('order') [0]['column'] : '');
        $filterArr['orderByFieldName'] = (!empty(Request::get('columns') [$filterArr['orderColumnNo']]['name']) ? Request::get('columns') [$filterArr['orderColumnNo']]['name'] : '');
        $filterArr['orderTypeAscOrDesc'] = (!empty(Request::get('order') [0]['dir']) ? Request::get('order') [0]['dir'] : '');
        $filterArr['statusFilter'] = !empty(Request::get('statusValue')) ? Request::get('statusValue') : '';
        $filterArr['albumFilter'] = !empty(Request::get('albumValue')) ? Request::get('albumValue') : '';
        $filterArr['searchFilter'] = !empty(Request::get('searchValue')) ? Request::get('searchValue') : '';
        $filterArr['iDisplayLength'] = intval(Request::get('length'));
        $filterArr['iDisplayStart'] = intval(Request::get('start'));
        $sEcho = intval(Request::get('draw'));
        $arrResults = PhotoGallery::getRecordList_tab1($filterArr);
        $iTotalRecords = PhotoGallery::getRecordCountListApprovalTab($filterArr, true);
        $end = $filterArr['iDisplayStart'] + $filterArr['iDisplayLength'];
        $end = $end > $iTotalRecords ? $iTotalRecords : $end;
        if (count($arrResults) > 0 && !empty($arrResults)) {
            foreach ($arrResults as $key => $value) {
                $records["data"][] = $this->tableData_tab1($value);
            }
        }
        $NewRecordsCount = PhotoGallery::getNewRecordsCount();
        $records["newRecordCount"] = $NewRecordsCount;
        $records["customActionStatus"] = "OK";
        $records["draw"] = $sEcho;
        $records["recordsTotal"] = $iTotalRecords;
        $records["recordsFiltered"] = $iTotalRecords;
        echo json_encode($records);
        exit;
    }

    /**
     * This method loads photo-gallery edit view
     * @param  	Alias of record
     * @return  View
     * @since   2017-07-21
     * @author  NetQuick
     */
    public function edit($alias = false) {
        $imageManager = true;
        $userIsAdmin = false;
        if (isset($this->currentUserRoleData) && !empty($this->currentUserRoleData)) {
            if ($this->currentUserRoleData->chrIsAdmin == 'Y') {
                $userIsAdmin = true;
            }
        }else{
            $userIsAdmin = true;
        }
        $templateData = array();
        $avoidPhotoAlbumModuleIds = array();
        // $photoAlbumsDropDownList = PhotoAlbum::getPhotoAlbumDropdwonList($avoidPhotoAlbumModuleIds);
        if (!is_numeric($alias)) {
            $total = PhotoGallery::getRecordCount();
            $this->breadcrumb['title'] = trans('photogallery::template.photoGalleryModule.addphotoGallery');
            $this->breadcrumb['module'] = trans('photogallery::template.photoGalleryModule.managePhotoGallery');
            $this->breadcrumb['url'] = 'powerpanel/photo-gallery';
            $this->breadcrumb['inner_title'] = trans('photogallery::template.photoGalleryModule.addphotoGallery');
            $templateData['total'] = $total;
            $templateData['breadcrumb'] = $this->breadcrumb;
            $templateData['imageManager'] = $imageManager;
            // $templateData['photoAlbumsDropDownList'] = $photoAlbumsDropDownList;
        } else {
            $id = $alias;
            $photoGallery = PhotoGallery::getRecordById($id);
            if (empty($photoGallery)) {
                return redirect()->route('powerpanel.photo-gallery.add');
            }
            if ($photoGallery->fkMainRecord != '0') {
                $photoGallery_highLight = PhotoGallery::getRecordById($photoGallery->fkMainRecord);
                $templateData['photoGallery_highLight'] = $photoGallery_highLight;
            } else {
                $templateData['photoGallery_highLight'] = "";
            }
            $this->breadcrumb['title'] = trans('photogallery::template.photoGalleryModule.editphotoGallery') . ' - ' . $photoGallery->varTitle;
            $this->breadcrumb['module'] = trans('photogallery::template.photoGalleryModule.managePhotoGallery');
            $this->breadcrumb['url'] = 'powerpanel/photo-gallery';
            $this->breadcrumb['inner_title'] = trans('photogallery::template.photoGalleryModule.editphotoGallery') . ' - ' . $photoGallery->varTitle;
            $templateData['photoGallery'] = $photoGallery;
            $templateData['id'] = $id;
            $templateData['breadcrumb'] = $this->breadcrumb;
            $templateData['imageManager'] = $imageManager;
            $templateData['photoAlbumsDropDownList'] = $photoAlbumsDropDownList;
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
                $templateData['chrNeedAddPermission'] = $workFlowByCat->chrNeedAddPermission;
                $templateData['charNeedApproval'] = $workFlowByCat->charNeedApproval;
            } else {
                $templateData['chrNeedAddPermission'] = 'N';
                $templateData['charNeedApproval'] = 'N';
            }
        } else {
            $templateData['chrNeedAddPermission'] = 'N';
            $templateData['charNeedApproval'] = 'N';
        }
        //End Button Name Change For User Side
        $templateData['userIsAdmin'] = $userIsAdmin;
        $templateData['MyLibrary'] = $this->MyLibrary;
        return view('photo-gallery::powerpanel.actions', $templateData);
    }

    /**
     * This method stores photo-gallery modifications
     * @return  View
     * @since   2017-07-21
     * @author  NetQuick
     */
    public function handlePost(Request $request) {
        $approval = false;
        $userIsAdmin = false;
        if (isset($this->currentUserRoleData) && !empty($this->currentUserRoleData)) {
            if ($this->currentUserRoleData->chrIsAdmin == 'Y') {
                $userIsAdmin = true;
            }
        }
        $postArr = Request::all();
        $messsages = [
            'title.required' => 'Title field is required.',
            'order.required' => trans('photogallery::template.photoGalleryModule.displayOrder'),
            'order.greater_than_zero' => trans('photogallery::template.photoGalleryModule.displayGreaterThan'),
            'img_id.required' => 'Image field is required.',
            // 'photoAlbumId.required' => 'Photo album field is required.'
        ];
        $rules = [
            'title' => 'required|max:160|handle_xss|no_url',
            'order' => 'required|greater_than_zero|handle_xss|no_url',
            'chrMenuDisplay' => 'required',
            'img_id' => 'required',
            // 'photoAlbumId' => 'required',
        ];
        $validator = Validator::make($postArr, $rules, $messsages);
        if ($validator->passes()) {
            $photoGalleryArr = [];
            $module = Modules::getModuleById(Config::get('Constant.MODULE.ID'));
            $id = Request::segment(3);
            $actionMessage = trans('photogallery::template.photoGalleryModule.updateMessage');
            if (is_numeric($id)) { #Edit post Handler=======
                $photoGallery = PhotoGallery::getRecordForLogById($id);
                $updatePhotoGalleryFields = [];
                $updatePhotoGalleryFields['varTitle'] = stripslashes(trim($postArr['title']));
                $updatePhotoGalleryFields['chrPublish'] = $postArr['chrMenuDisplay'];
                
                $updatePhotoGalleryFields['dtDateTime'] = !empty($postArr['start_date_time']) ? date('Y-m-d H:i:s', strtotime($postArr['start_date_time'])) : date('Y-m-d H:i:s');
                $updatePhotoGalleryFields['dtEndDateTime'] = !empty($postArr['end_date_time']) ? date('Y-m-d H:i:s', strtotime($postArr['end_date_time'])) : null;
               
                $updatePhotoGalleryFields['UserID'] = auth()->user()->id;
                $updatePhotoGalleryFields['fkIntImgId'] = !empty($postArr['img_id']) ? $postArr['img_id'] : null;
                $updatePhotoGalleryFields['intPhotoAlbumId'] = !empty($postArr['photoAlbumId']) ? $postArr['photoAlbumId'] : null;
                if ($postArr['chrMenuDisplay'] == 'D') {
                    $updatePhotoGalleryFields['chrDraft'] = 'D';
                    $updatePhotoGalleryFields['chrPublish'] = 'N';
                } else {
                    $updatePhotoGalleryFields['chrDraft'] = 'N';
                    $updatePhotoGalleryFields['chrPublish'] = $postArr['chrMenuDisplay'];
                }
                $whereConditions = ['id' => $id];
                if ($postArr['chrMenuDisplay'] == 'D') {
                    $addlog = Config::get('Constant.UPDATE_DRAFT');
                } else {
                    $addlog = '';
                }
                if ($photoGallery->chrLock == 'Y' && auth()->user()->id != $photoGallery->LockUserID) {
                    if ($this->currentUserRoleData->chrIsAdmin != 'Y') {
                        $lockedUserData = User::getRecordById($photoGallery->LockUserID, true);
                        $lockedUserName = 'someone';
                        if (!empty($lockedUserData)) {
                            $lockedUserName = $lockedUserData->name;
                        }
                        $actionMessage = "This record has been locked by " . $lockedUserName . ".";
                        return redirect()->route('powerpanel.photo-gallery.index')->with('message', $actionMessage);
                    }
                }
                if (File::exists(app_path() . '/Workflow.php') != null || File::exists(base_path() . '/packages/Powerpanel/Workflow/src/Models/Workflow.php') != null) {
                    if (!$userIsAdmin) {
                        $userRole = $this->currentUserRoleData->id;
                    } else {
                        $userRoleData = Role_user::getUserRoleByUserId($photoGallery->UserID);
                        if (isset($userRoleData->role_id)) {
                            $userRole = $userRoleData->role_id;
                        } else {
                            $userRole = $this->currentUserRoleData->id;
                        }
                    }
                    $workFlowByCat = Workflow::getRecordByCategoryId($module->intFkGroupCode, $userRole);
                    if (empty($workFlowByCat->varUserId) || $userIsAdmin || $workFlowByCat->charNeedApproval == 'N') {
                        if ((int) $photoGallery->fkMainRecord === 0 || empty($workFlowByCat->varUserId)) {
                            $update = CommonModel::updateRecords($whereConditions, $updatePhotoGalleryFields,false,'Powerpanel\PhotoGallery\Models\PhotoGallery');
                            if ($update) {
                                if ($id > 0 && !empty($id)) {
                                    self::swap_order_edit($postArr['order'], $id);
                                    $logArr = MyLibrary::logData($id, false, $addlog);
                                    if (Auth::user()->can('log-advanced')) {
                                        $newPhotoGalleryObj = PhotoGallery::getRecordForLogById($id);
                                        $oldRec = $this->recordHistory($photoGallery);
                                        $newRec = $this->newrecordHistory($photoGallery, $newPhotoGalleryObj);
                                        $logArr['old_val'] = $oldRec;
                                        $logArr['new_val'] = $newRec;
                                    }
                                    $logArr['varTitle'] = trim($postArr['title']);
                                    Log::recordLog($logArr);
                                    if (Auth::user()->can('recent-updates-list')) {
                                        if (!isset($newPhotoGalleryObj)) {
                                            $newPhotoGalleryObj = PhotoGallery::getRecordForLogById($id);
                                        }
                                        $notificationArr = MyLibrary::notificationData($id, $newPhotoGalleryObj);
                                        RecentUpdates::setNotification($notificationArr);
                                    }
                                    self::flushCache();
                                    if (isset($postArr['saveandexit']) && $postArr['saveandexit'] == 'approvesaveandexit') {
                                        $actionMessage = trans('photogallery::template.common.recordApprovalMessage');
                                    } else {
                                        $actionMessage = trans('photogallery::template.photoGalleryModule.updateMessage');
                                    }
                                }
                            }
                        } else {
                            $updateModuleFields = $updatePhotoGalleryFields;
                            $this->insertApprovedRecord($updateModuleFields, $postArr, $id);
                            if (isset($postArr['saveandexit']) && $postArr['saveandexit'] == 'approvesaveandexit') {
                                $actionMessage = trans('photogallery::template.common.recordApprovalMessage');
                            } else {
                                $actionMessage = trans('photogallery::template.photoGalleryModule.updateMessage');
                            }
                            $approval = $id;
                        }
                    } else {
                        if ($workFlowByCat->charNeedApproval == 'Y') {
                            $approvalObj = $this->insertApprovalRecord($photoGallery, $postArr, $updatePhotoGalleryFields);
                            if (isset($postArr['saveandexit']) && $postArr['saveandexit'] == 'approvesaveandexit') {
                                $actionMessage = trans('photogallery::template.common.recordApprovalMessage');
                            } else {
                                $actionMessage = trans('photogallery::template.photoGalleryModule.updateMessage');
                            }
                            $approval = $approvalObj->id;
                        }
                    }
                } else {
                    $update = CommonModel::updateRecords($whereConditions, $updatePhotoGalleryFields,false,'Powerpanel\PhotoGallery\Models\PhotoGallery');
                    $actionMessage = trans('photogallery::template.photoGalleryModule.updateMessage');
                }
            } else { #Add post Handler=======
                if (File::exists(app_path() . '/Workflow.php') != null || File::exists(base_path() . '/packages/Powerpanel/Workflow/src/Models/Workflow.php') != null) {
                    $workFlowByCat = Workflow::getRecordByCategoryId($module->intFkGroupCode, $this->currentUserRoleData->id);
                }
                if (!empty($workFlowByCat->varUserId) && $workFlowByCat->chrNeedAddPermission == 'Y' && !$userIsAdmin) {
                    $photoGalleryArr['chrPublish'] = 'N';
                    $photoGalleryArr['chrDraft'] = 'N';
                    $photoGalleryObj = $this->insertNewRecord($postArr, $photoGalleryArr);
                    if ($postArr['chrMenuDisplay'] == 'D') {
                        $photoGalleryArr['chrDraft'] = 'D';
                    }
                    $photoGalleryArr['chrPublish'] = 'Y';
                    $approvalObj = $this->insertApprovalRecord($photoGalleryObj, $postArr, $photoGalleryArr);
                    $approval = $photoGalleryObj->id;
                } else {
                    $photoGalleryObj = $this->insertNewRecord($postArr, $photoGalleryArr);
                    $approval = $photoGalleryObj->id;
                }
                if (isset($postArr['saveandexit']) && $postArr['saveandexit'] == 'approvesaveandexit') {
                    $actionMessage = trans('photogallery::template.common.recordApprovalMessage');
                } else {
                    $actionMessage = trans('photogallery::template.photoGalleryModule.addMessage');
                }
                $id = $photoGalleryObj->id;
            }
            AddImageModelRel::sync(explode(',', $postArr['img_id']), $id, $approval);
            if ((!empty($postArr['saveandexit']) && $postArr['saveandexit'] == 'saveandexit') || !$userIsAdmin) {
                if ($postArr['chrMenuDisplay'] == 'D') {
                    return redirect()->route('powerpanel.photo-gallery.index', 'tab=D')->with('message', $actionMessage);
                } else {
                    return redirect()->route('powerpanel.photo-gallery.index')->with('message', $actionMessage);
                }
            } else {
                return redirect()->route('powerpanel.photo-gallery.edit', $id)->with('message', $actionMessage);
            }
        } else {
            return Redirect::back()->withErrors($validator)->withInput();
        }
    }

    public function insertApprovedRecord($updateModuleFields, $postArr, $id) {
        $whereConditions = ['id' => $postArr['fkMainRecord']];
        $updateModuleFields['chrAddStar'] = 'N';
        $updateModuleFields['UserID'] = auth()->user()->id;
        $update = CommonModel::updateRecords($whereConditions, $updateModuleFields,false,'Powerpanel\PhotoGallery\Models\PhotoGallery');
        if ($update) {
            self::swap_order_edit($postArr['order'], $postArr['fkMainRecord']);
        }
        $whereConditions_ApproveN = ['fkMainRecord' => $postArr['fkMainRecord']];
        $updateToApproveN = [
            'chrApproved' => 'N',
            'chrLetest' => 'N',
            'intApprovedBy' => '0',
        ];
        CommonModel::updateRecords($whereConditions_ApproveN, $updateToApproveN,false,'Powerpanel\PhotoGallery\Models\PhotoGallery');
        $whereConditionsApprove = ['id' => $id, 'chrMain' => 'N'];
        $updateToApprove = [
            'chrApproved' => 'Y',
            'chrRollBack' => 'Y',
            'intApprovedBy' => auth()->user()->id
        ];
        CommonModel::updateRecords($whereConditionsApprove, $updateToApprove,false,'Powerpanel\PhotoGallery\Models\PhotoGallery');
        if ($postArr['chrMenuDisplay'] == 'D') {
            $addlog = Config::get('Constant.DRAFT_RECORD_APPROVED');
        } else {
            $addlog = Config::get('Constant.RECORD_APPROVED');
        }
        $newBannerObj = PhotoGallery::getRecordForLogById($id);
        $logArr = MyLibrary::logData($id, false, $addlog);
        $logArr['varTitle'] = stripslashes($newBannerObj->varTitle);
        Log::recordLog($logArr);
        /* notification for user to record approved */
        if (method_exists($this->MyLibrary, 'userNotificationData')) {
            $userNotificationArr = MyLibrary::userNotificationData(Config::get('Constant.MODULE.ID'));
            $userNotificationArr['fkRecordId'] = $id;
            $userNotificationArr['txtNotification'] = 'Your request has been approved by ' . ucfirst(auth()->user()->name) . ' (' . ucfirst(Config::get('Constant.MODULE.NAME')) . ')';
            $userNotificationArr['fkIntUserId'] = Auth::user()->id;
            $userNotificationArr['chrNotificationType'] = 'A';
            $userNotificationArr['intOnlyForUserId'] = $newBannerObj->UserID;
            UserNotification::addRecord($userNotificationArr);
        }
        /* notification for user to record approved */
        if ($update) {
            if ($id > 0 && !empty($id)) {
                $where = [];
                $flowData = [];
                $flowData['dtYes'] = Config::get('Constant.SQLTIMESTAMP');
                $where['fkModuleId'] = Config::get('Constant.MODULE.ID');
                $where['fkRecordId'] = (isset($postArr['fkMainRecord']) && (int) $postArr['fkMainRecord'] != 0) ? $postArr['fkMainRecord'] : $id;
                $where['dtYes'] = 'null';
                WorkflowLog::updateRecord($flowData, $where);
                self::flushCache();
                $actionMessage = trans('photogallery::template.photoGalleryModule.updateMessage');
            }
        }
    }

    public function insertApprovalRecord($moduleObj, $postArr, $photoGalleryArr) {
        $response = false;
        $photoGalleryArr['chrMain'] = 'N';
        $photoGalleryArr['chrLetest'] = 'Y';
        $photoGalleryArr['fkMainRecord'] = $moduleObj->id;
        $photoGalleryArr['varTitle'] = stripslashes(trim($postArr['title']));
        if ($postArr['chrMenuDisplay'] == 'D') {
            $photoGalleryArr['chrDraft'] = 'D';
            $photoGalleryArr['chrPublish'] = 'N';
        } else {
            $photoGalleryArr['chrDraft'] = 'N';
            $photoGalleryArr['chrPublish'] = $postArr['chrMenuDisplay'];
        }
        $photoGalleryArr['intDisplayOrder'] = $postArr['order'];
       
        $photoGalleryArr['dtDateTime'] = !empty($postArr['start_date_time']) ? date('Y-m-d H:i:s', strtotime($postArr['start_date_time'])) : date('Y-m-d H:i:s');
        $photoGalleryArr['dtEndDateTime'] = !empty($postArr['end_date_time']) ? date('Y-m-d H:i:s', strtotime($postArr['end_date_time'])) : null;
        
        $photoGalleryArr['created_at'] = Carbon::now();
        $photoGalleryArr['UserID'] = auth()->user()->id;
        $photoGalleryArr['fkIntImgId'] = !empty($postArr['img_id']) ? $postArr['img_id'] : null;
        $photoGalleryArr['intPhotoAlbumId'] = !empty($postArr['photoAlbumId']) ? $postArr['photoAlbumId'] : null;
        if ($postArr['chrMenuDisplay'] == 'D') {
            $addlog = Config::get('Constant.DRAFT_SENT_FOR_APPROVAL');
        } else {
            $addlog = Config::get('Constant.SENT_FOR_APPROVAL');
        }
        $photoGalleryID = CommonModel::addRecord($photoGalleryArr,'Powerpanel\PhotoGallery\Models\PhotoGallery');
        if (!empty($photoGalleryID)) {
            $id = $photoGalleryID;
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
            $newPhotoGalleryObj = PhotoGallery::getRecordForLogById($id);
            $logArr = MyLibrary::logData($id, false, $addlog);
            $logArr['varTitle'] = $newPhotoGalleryObj->varTitle;
            Log::recordLog($logArr);
            if (Auth::user()->can('recent-updates-list')) {
                $notificationArr = MyLibrary::notificationData($id, $newPhotoGalleryObj);
                RecentUpdates::setNotification($notificationArr);
            }
            $response = $newPhotoGalleryObj;
            self::flushCache();
            $actionMessage = trans('photogallery::template.photoGalleryModule.addMessage');
        }
        $whereConditionsAddstar = ['id' => $moduleObj->id];
        $updateAddStar = [
            'chrAddStar' => 'Y',
        ];
        CommonModel::updateRecords($whereConditionsAddstar, $updateAddStar,false,'Powerpanel\PhotoGallery\Models\PhotoGallery');
        return $response;
    }

    public function insertNewRecord($postArr, $photoGalleryArr) {
        $response = false;
        $photoGalleryArr['chrMain'] = 'Y';
        $photoGalleryArr['varTitle'] = stripslashes(trim($postArr['title']));
        $photoGalleryArr['intDisplayOrder'] = self::swap_order_add($postArr['order']);
        if ($postArr['chrMenuDisplay'] == 'D') {
            $photoGalleryArr['chrDraft'] = 'D';
            $photoGalleryArr['chrPublish'] = 'N';
        } else {
            $photoGalleryArr['chrDraft'] = 'N';
        }
        
        $photoGalleryArr['dtDateTime'] = !empty($postArr['start_date_time']) ? date('Y-m-d H:i:s', strtotime($postArr['start_date_time'])) : date('Y-m-d H:i:s');
        $photoGalleryArr['dtEndDateTime'] = !empty($postArr['end_date_time']) ? date('Y-m-d H:i:s', strtotime($postArr['end_date_time'])) : null;
        
        $photoGalleryArr['UserID'] = auth()->user()->id;
        $photoGalleryArr['created_at'] = Carbon::now();
        $photoGalleryArr['fkIntImgId'] = !empty($postArr['img_id']) ? $postArr['img_id'] : null;
        $photoGalleryArr['intPhotoAlbumId'] = !empty($postArr['photoAlbumId']) ? $postArr['photoAlbumId'] : null;
        $photoGalleryID = CommonModel::addRecord($photoGalleryArr,'Powerpanel\PhotoGallery\Models\PhotoGallery');
        if (!empty($photoGalleryID)) {
            $id = $photoGalleryID;
            $newPhotoGalleryObj = PhotoGallery::getRecordForLogById($id);
            $logArr = MyLibrary::logData($id);
            $logArr['varTitle'] = $newPhotoGalleryObj->varTitle;
            Log::recordLog($logArr);
            if (Auth::user()->can('recent-updates-list')) {
                $notificationArr = MyLibrary::notificationData($id, $newPhotoGalleryObj);
                RecentUpdates::setNotification($notificationArr);
            }
            $response = $newPhotoGalleryObj;
            self::flushCache();
            $actionMessage = trans('photogallery::template.photoGalleryModule.addMessage');
        }
        return $response;
    }

    /**
     * This method destroys PhotoGallery in multiples
     * @return  PhotoGallery index view
     * @since   2016-10-25
     * @author  NetQuick
     */
    public function DeleteRecord(Request $request) {
        $value = Request::input('value');
        $data['ids'] = Request::input('ids');
        $moduleHaveFields = ['chrMain'];
        $update = MyLibrary::deleteMultipleRecords($data, $moduleHaveFields, $value,'Powerpanel\PhotoGallery\Models\PhotoGallery');
        if (File::exists(app_path() . '/Comments.php') != null || File::exists(base_path() . '/packages/Powerpanel/Workflow/src/Models/Comments.php') != null) {
            Comments::deleteComments($data['ids'], Config::get('Constant.MODULE.MODEL_NAME'));
        }
        foreach ($update as $ids) {
            $ignoreDeleteScope = true;
            $Deleted_Record = PhotoGallery::getRecordById($ids, $ignoreDeleteScope);
            $Cnt_Letest = PhotoGallery::getRecordCount_letest($Deleted_Record['fkMainRecord'], $Deleted_Record['id']);
            if ($Cnt_Letest <= 0) {
                $updateLetest = [
                    'chrAddStar' => 'N',
                ];
                $whereConditionsApprove = ['id' => $Deleted_Record['fkMainRecord']];
                CommonModel::updateRecords($whereConditionsApprove, $updateLetest,false,'Powerpanel\PhotoGallery\Models\PhotoGallery');
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
        }
        self::flushCache();
        echo json_encode($update);
        exit;
    }

    /**
     * This method destroys PhotoGallery in multiples
     * @return  PhotoGallery index view
     * @since   2016-10-25
     * @author  NetQuick
     */
    public function publish(Request $request) {
        $requestArr = Request::all();
//        $request = (object) $requestArr;
        $val = Request::get('val');
        $alias = (int) Request::get('alias');
        $update = MyLibrary::setPublishUnpublish($alias, $val,'Powerpanel\PhotoGallery\Models\PhotoGallery');
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
        $order = Request::get('order');
        $exOrder = Request::get('exOrder');
        MyLibrary::swapOrder($order, $exOrder,'Powerpanel\PhotoGallery\Models\PhotoGallery');
        self::flushCache();
    }

    /**
     * This method handels swapping of available order record while adding
     * @param  	order
     * @return  order
     * @since   2016-10-21
     * @author  NetQuick
     */
    public static function swap_order_add($order = null) {
        $response = false;
        $isCustomizeModule = true;
        $moduleHaveFields = ['chrMain'];
        if ($order != null) {
            $response = MyLibrary::swapOrderAdd($order, $isCustomizeModule, $moduleHaveFields,'Powerpanel\PhotoGallery\Models\PhotoGallery');
            self::flushCache();
        }
        return $response;
    }

    /**
     * This method handels swapping of available order record while editing
     * @param  	order
     * @return  order
     * @since   2016-12-23
     * @author  NetQuick
     */
    public static function swap_order_edit($order = null, $id = null) {
        $isCustomizeModule = true;
        $moduleHaveFields = ['chrMain'];
        MyLibrary::swapOrderEdit($order, $id, $isCustomizeModule, $moduleHaveFields,'Powerpanel\PhotoGallery\Models\PhotoGallery');
        self::flushCache();
    }

    public function tableData_tab1($value) {
        $actions = '';
        $publish_action = '';
        if (Auth::user()->can('photo-gallery-edit')) {
            $actions .= '<a class="" title="' . trans("photogallery::template.common.edit") . '" href="' . route('powerpanel.photo-gallery.edit', array('alias' => $value->id)) . '">
				<i class="fa fa-pencil"></i></a>';
        }
        if (Auth::user()->can('photo-gallery-delete') && $this->currentUserRoleData->chrIsAdmin == 'Y') {
            if (Config::get('Constant.DEFAULT_TRASH') == 'Y') {
                $actions .= '<a class = "delete-grid" title = "Trash" href = \'javascript:;\' onclick=\'Trashfun("' . $value->id . '")\' data-controller="photo-gallery" data-alias = "' . $value->id . '" data-tab="A"><i class="fa fa-times"></i></a>';
            } else {
                $actions .= '<a class=" delete" title="' . trans("photogallery::template.common.delete") . '" data-controller="photo-gallery" data-alias = "' . $value->id . '" data-tab="A"><i class="fa fa-times"></i></a>';
            }
        }

        if (Auth::user()->can('photo-gallery-edit')) {
            $title = '<a class="" title="' . trans("photogallery::template.common.edit") . '" href="' . route('powerpanel.photo-gallery.edit', array('alias' => $value->id)) . '">' . $value->varTitle . '</a>';
        } else {
            $title = stripslashes($value->varTitle);
        }
        $photoAlbum = isset($value->photoAlbum->varTitle) ? $value->photoAlbum->varTitle : '-';
        if (Auth::user()->can('photo-gallery-reviewchanges')) {
            $update = "<a title=\"Click here to see all approval records.\" class=\"icon_title1\" style=\"margin-right: 5px;\" onclick=\"javascript:expandcollapsepanel(this ,'tasklisting" . $value->id . "', 'mainsingnimg" . $value->id . "'," . $value->id . ")\"><i id=\"mainsingnimg" . $value->id . "\" class=\"la la-plus-square\"></i></a>";
            $rollback = "<a title=\"Click here to see all approved records to rollback.\" class=\"icon_title2\" style=\"margin-right: 5px;\" onclick=\"javascript:expandcollapsepanel_rolback(this ,'tasklisting_rollback" . $value->id . "', 'mainsingnimg_rollback" . $value->id . "'," . $value->id . ")\"><i id=\"mainsingnimg_rollback" . $value->id . "\" class=\"la la-history\"></i></a>";
        } else {
            $update = '';
            $rollback = '';
        }
        if (Auth::user()->can('photo-gallery-reviewchanges') && $value->chrAddStar == 'Y') {
            $star = 'addhiglight';
        } else {
            $star = '';
        }
        $imgIcon = '';
        $imgIcon .= '<div class="multi_image_thumb">';
        $imgIcon .= '<a href="' . resize_image::resize($value->fkIntImgId) . '" class="fancybox-buttons"  data-rel="fancybox-buttons">';
        $imgIcon .= '<img height="30" width="30" title="' . preg_replace('/[^A-Za-z0-9\-]/', '-', $value->varTitle) . '" src="' . resize_image::resize($value->fkIntImgId, 50, 50) . '"/>';
        $imgIcon .= '</a>';
        $imgIcon .= '</div>';
        $startDate = date('' . Config::get('Constant.DEFAULT_DATE_FORMAT') . ' ' . Config::get('Constant.DEFAULT_TIME_FORMAT') . '', strtotime($value->dtDateTime));
        $endDate = !empty($value->dtEndDateTime) ? date('' . Config::get('Constant.DEFAULT_DATE_FORMAT') . ' ' . Config::get('Constant.DEFAULT_TIME_FORMAT') . '', strtotime($value->dtEndDateTime)) : 'No Expiry';
        $Quickedit_startDate = date('Y-m-d H:i', strtotime($value->dtDateTime));
        $Quickedit_endDate = !empty($value->dtEndDateTime) ? date('Y-m-d H:i', strtotime($value->dtEndDateTime)) : 'No Expiry';
        $title = $value->varTitle;
        if (Auth::user()->can('photo-gallery-edit')) {
            if ($value->chrLock != 'Y') {
                $title = '<div class="quick_edit"><a href = "' . route('powerpanel.photo-gallery.edit', array('alias' => $value->id)) . '?tab=A">' . $value->varTitle . '</a> <div class="quick_edit_menu">
														<span><a href="' . route('powerpanel.photo-gallery.edit', array('alias' => $value->id)) . '?tab=A" title="Edit">Edit</a></span>';
                if (Config::get('Constant.DEFAULT_TRASH') == 'Y') {
                    $title .= '<span><a title = "Trash" href = \'javascript:;\' onclick=\'Trashfun("' . $value->id . '")\' class="red" data-tab="A">Trash</a></span>';
                }
                $title .= '</div></div>';
            } else {
                if (auth()->user()->id != $value->LockUserID) {
                    if ($this->currentUserRoleData->chrIsAdmin == 'Y') {
                        $title = '<div class="quick_edit"><a href = "' . route('powerpanel.photo-gallery.edit', array('alias' => $value->id)) . '?tab=A">' . $value->varTitle . '</a> <div class="quick_edit_menu">
	                            <span><a href="' . route('powerpanel.photo-gallery.edit', array('alias' => $value->id)) . '?tab=A" title="Edit">Edit</a></span></div></div>';
                    } else {
                        $title = '<div class="quick_edit"><a href = "javascript:;">' . $value->varTitle . '</a></div>';
                    }
                } else {
                    $title = '<div class="quick_edit"><a href = "' . route('powerpanel.photo-gallery.edit', array('alias' => $value->id)) . '?tab=A">' . $value->varTitle . '</a> <div class="quick_edit_menu">
	                            <span><a href="' . route('powerpanel.photo-gallery.edit', array('alias' => $value->id)) . '?tab=A" title="Edit">Edit</a></span></div></div>';
                }
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

        $First_td = '<div class="star_box star_box_auto">' . $Favorite . '</div>';
        $logurl = url('powerpanel/log?id=' . $value->id . '&mid=' . Config::get('Constant.MODULE.ID'));
        if ($actions == "") {
            $actions = "---";
        } else {
            $actions = $actions;
        }
        $log = '';
        if ($value->chrLock != 'Y') {
            $log .= $actions;
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

        $records = array(
            $First_td,
            '<div class="pages_title_div_row">' . $update . $rollback . $title . $status . $statusdata . '</div>',
            $imgIcon,
            $photoAlbum,
            $startDate,
            $endDate,
            $log,
            $value->intDisplayOrder,
        );
        return $records;
    }

    public function tableData($value, $totalRecord = false, $tableSortedType = 'asc') {
        $actions = '';
        $publish_action = '';
        if (Auth::user()->can('photo-gallery-edit')) {
            $actions .= '<a class="" title="' . trans("photogallery::template.common.edit") . '" href="' . route('powerpanel.photo-gallery.edit', array('alias' => $value->id)) . '">
				<i class="fa fa-pencil"></i></a>';
        }
        if (Auth::user()->can('photo-gallery-delete') || (isset($this->currentUserRoleData->chrIsAdmin) && $this->currentUserRoleData->chrIsAdmin == 'Y')) {
            if (Config::get('Constant.DEFAULT_TRASH') == 'Y') {
                $actions .= '<a class="delete-grid" title="' . trans("photogallery::template.common.delete") . '" onclick = \'Trashfun("' . $value->id . '")\' data-controller="photo-gallery" data-alias = "' . $value->id . '" data-tab="P"><i class="fa fa-times"></i></a>';
            } else {
                $actions .= '<a class="delete" title="' . trans("photogallery::template.common.delete") . '" data-controller="photo-gallery" data-alias = "' . $value->id . '" data-tab="P"><i class="fa fa-times"></i></a>';
            }
        }
        if ($value->chrAddStar != 'Y') {
            if ($value->chrDraft != 'D') {
                if (Auth::user()->can('photo-gallery-publish')) {
                    if ($value->chrPublish == 'Y') {
                        $publish_action .= '<input data-off-text="No" data-on-text="Yes" class="make-switch publish" class="make-switch publish" data-off-color="info" data-on-color="primary" type="checkbox" data-controller="powerpanel/photo-gallery" title="' . trans("photogallery::template.common.publishedRecord") . '" data-value="Unpublish" data-alias="' . $value->id . '">';
                    } else {
                        $publish_action .= '<input checked="" data-off-text="No" data-on-text="Yes" class="make-switch publish" class="make-switch publish" data-off-color="info" data-on-color="primary" type="checkbox" data-controller="powerpanel/photo-gallery" title="' . trans("photogallery::template.common.unpublishedRecord") . '" data-value="Publish" data-alias="' . $value->id . '">';
                    }
                }
            } else {
                $publish_action .= '<input checked="" data-off-text="No" data-on-text="Yes" class="make-switch pub" data-off-color="info" data-on-color="primary" type="checkbox" data-controller="powerpanel/photo-gallery" title="' . trans("photogallery::template.common.unpublishedRecord") . '" data-value="Publish" data-alias="' . $value->id . '">';
            }
        } else {
            $publish_action .= '---';
        }
        $startDate = date('' . Config::get('Constant.DEFAULT_DATE_FORMAT') . ' ' . Config::get('Constant.DEFAULT_TIME_FORMAT') . '', strtotime($value->dtDateTime));
        $endDate = !empty($value->dtEndDateTime) ? date('' . Config::get('Constant.DEFAULT_DATE_FORMAT') . ' ' . Config::get('Constant.DEFAULT_TIME_FORMAT') . '', strtotime($value->dtEndDateTime)) : 'No Expiry';
        $Quickedit_startDate = date('Y-m-d H:i', strtotime($value->dtDateTime));
        $Quickedit_endDate = !empty($value->dtEndDateTime) ? date('Y-m-d H:i', strtotime($value->dtEndDateTime)) : 'No Expiry';
        $title = $value->varTitle;
        if (Auth::user()->can('photo-gallery-edit')) {
            if ($value->chrLock != 'Y') {
                if (isset($this->currentUserRoleData->chrIsAdmin) && $this->currentUserRoleData->chrIsAdmin == 'Y') {
                    $title = '<div class="quick_edit"><a href = "' . route('powerpanel.photo-gallery.edit', array('alias' => $value->id)) . '?tab=P">' . $value->varTitle . '</a> <div class="quick_edit_menu">
														<span><a href="' . route('powerpanel.photo-gallery.edit', array('alias' => $value->id)) . '?tab=P" title="Edit">Edit</a></span>';
                    if (Config::get('Constant.DEFAULT_QUICK') == 'Y') {
                        $title .= '<span><a title="Quick Edit" href=\'javascript:;\' data-toggle=\'modal\' data-target=\'#modalForm\' aria-label=\'Quick edit\' onclick=\'Quickeditfun("' . $value->id . '","' . $value->varTitle . '","' . $value->intSearchRank . '","' . $Quickedit_startDate . '","' . $Quickedit_endDate . '","P")\'>Quick Edit</a></span>';
                    }
                    if (Config::get('Constant.DEFAULT_TRASH') == 'Y') {
                        $title .= '<span><a title = "Trash" href = \'javascript:;\' onclick=\'Trashfun("' . $value->id . '")\' class="red" data-tab="P">Trash</a></span>';
                    }
                    $title .= '</div>    
											 </div>';
                } else {
                    $title = '<div class="quick_edit"><a href = "' . route('powerpanel.photo-gallery.edit', array('alias' => $value->id)) . '?tab=P">' . $value->varTitle . '</a> <div class="quick_edit_menu">
														<span><a href="' . route('powerpanel.photo-gallery.edit', array('alias' => $value->id)) . '?tab=P" title="Edit">Edit</a></span>
																</div>    
											 </div>';
                }
            } else {
                if (auth()->user()->id != $value->LockUserID) {
                    if (isset($this->currentUserRoleData->chrIsAdmin) && $this->currentUserRoleData->chrIsAdmin == 'Y') {
                        $title = '<div class="quick_edit"><a href = "' . route('powerpanel.photo-gallery.edit', array('alias' => $value->id)) . '?tab=P">' . $value->varTitle . '</a> <div class="quick_edit_menu">
                            <span><a href="' . route('powerpanel.photo-gallery.edit', array('alias' => $value->id)) . '?tab=P" title="Edit">Edit</a></span>
                                </div>    
                       </div>';
                    } else {
                        $title = '<div class="quick_edit"><a href = "javascript:;">' . $value->varTitle . '</a></div>';
                    }
                } else {
                    $title = '<div class="quick_edit"><a href = "' . route('powerpanel.photo-gallery.edit', array('alias' => $value->id)) . '?tab=P">' . $value->varTitle . '</a> <div class="quick_edit_menu">
                            <span><a href="' . route('powerpanel.photo-gallery.edit', array('alias' => $value->id)) . '?tab=P" title="Edit">Edit</a></span>
                                </div>    
                       </div>';
                }
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
        if (Auth::user()->can('photo-gallery-reviewchanges') && (File::exists(app_path() . '/Workflow.php') != null || File::exists(base_path() . '/packages/Powerpanel/Workflow/src/Models/Workflow.php') != null)) {
            $update = "<a title=\"Click here to see all approval records.\" class=\"icon_title1\" style=\"margin-right: 5px;\" onclick=\"javascript:expandcollapsepanel(this ,'tasklisting" . $value->id . "', 'mainsingnimg" . $value->id . "'," . $value->id . ")\"><i id=\"mainsingnimg" . $value->id . "\" class=\"la la-plus-square\"></i></a>";
            $rollback = "<a title=\"Click here to see all approved records to rollback.\" class=\"icon_title2\" style=\"margin-right: 5px;\" onclick=\"javascript:expandcollapsepanel_rolback(this ,'tasklisting_rollback" . $value->id . "', 'mainsingnimg_rollback" . $value->id . "'," . $value->id . ")\"><i id=\"mainsingnimg_rollback" . $value->id . "\" class=\"la la-history\"></i></a>";
        } else {
            $update = '';
            $rollback = '';
        }
        $photoAlbum = isset($value->photoAlbum->varTitle) ? $value->photoAlbum->varTitle : '-';
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
        $imgIcon = '';
        $imgIcon .= '<div class="multi_image_thumb">';
        $imgIcon .= '<a href="' . resize_image::resize($value->fkIntImgId) . '" class="fancybox-buttons"  data-rel="fancybox-buttons">';
        $imgIcon .= '<img height="30" width="30" title="' . preg_replace('/[^A-Za-z0-9\-]/', '-', $value->varTitle) . '" src="' . resize_image::resize($value->fkIntImgId, 50, 50) . '"/>';
        $imgIcon .= '</a>';
        $imgIcon .= '</div>';
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
        $First_td = '<div class="star_box star_box_auto">' . $Favorite . '</div>';
        $logurl = url('powerpanel/log?id=' . $value->id . '&mid=' . Config::get('Constant.MODULE.ID'));
        $log = '';
        if ($value->chrLock != 'Y') {
            if (isset($this->currentUserRoleData->chrIsAdmin) && $this->currentUserRoleData->chrIsAdmin == 'Y') {
                if (Config::get('Constant.DEFAULT_DUPLICATE') == 'Y') {
                    $log .= "<a title=\"Duplicate\" class='copy-grid' href=\"javascript:;\" onclick=\"GetCopyPage('" . $value->id . "');\"><i class=\"fa fa-clone\"></i></a>";
                }
                $log .= $actions;
                if (Auth::user()->can('log-list')) {
                    $log .= "<a title=\"Log History\" class='log-grid' href=\"$logurl\"><i class=\"fa fa-clock-o\"></i></a>";
                }
            } else {
                if ($actions == "") {
                    $actions = "---";
                } else {
                    $actions = $actions;
                }
                $log .= $actions;
                if (Auth::user()->can('log-list')) {
                    $log .= "<a title=\"Log History\" class='log-grid' href=\"$logurl\"><i class=\"fa fa-clock-o\"></i></a>";
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
        if ($publish_action == "") {
            $publish_action = "---";
        } else {
            if ($value->chrLock != 'Y') {
                $publish_action = $publish_action;
            } else {
                if (isset($this->currentUserRoleData->chrIsAdmin) && (auth()->user()->id == $value->LockUserID) || $this->currentUserRoleData->chrIsAdmin == 'Y') {
                    $publish_action = $publish_action;
                } else {
                    $publish_action = "---";
                }
            }
        }
        $records = array(
            '<input type="checkbox" name="delete" class="chkDelete" value="' . $value->id . '">',
            $First_td,
            '<div class="pages_title_div_row">' . $update . $rollback . $title . $status . $statusdata . '</div>',
            $imgIcon,
            $photoAlbum,
            $startDate,
            $endDate,
            $orderArrow,
            $publish_action,
            $log,
            $value->intDisplayOrder,
        );
        return $records;
    }

    public function tableDataFavorite($value, $totalRecord = false, $tableSortedType = 'asc') {
        $actions = '';
        $publish_action = '';
        if (Auth::user()->can('photo-gallery-edit')) {
            $actions .= '<a class="" title="' . trans("photogallery::template.common.edit") . '" href="' . route('powerpanel.photo-gallery.edit', array('alias' => $value->id)) . '">
				<i class="fa fa-pencil"></i></a>';
        }
        if (Auth::user()->can('photo-gallery-delete') && $this->currentUserRoleData->chrIsAdmin == 'Y') {
            if (Config::get('Constant.DEFAULT_TRASH') == 'Y') {
                $actions .= '<a class="delete-grid" title="' . trans("photogallery::template.common.delete") . '" onclick = \'Trashfun("' . $value->id . '")\' data-controller="photo-gallery" data-alias = "' . $value->id . '" data-tab="F"><i class="fa fa-times"></i></a>';
            } else {
                $actions .= '<a class="delete" title="' . trans("photogallery::template.common.delete") . '" data-controller="photo-gallery" data-alias = "' . $value->id . '" data-tab="F"><i class="fa fa-times"></i></a>';
            }
        }
        $startDate = date('' . Config::get('Constant.DEFAULT_DATE_FORMAT') . ' ' . Config::get('Constant.DEFAULT_TIME_FORMAT') . '', strtotime($value->dtDateTime));
        $endDate = !empty($value->dtEndDateTime) ? date('' . Config::get('Constant.DEFAULT_DATE_FORMAT') . ' ' . Config::get('Constant.DEFAULT_TIME_FORMAT') . '', strtotime($value->dtEndDateTime)) : 'No Expiry';
        $Quickedit_startDate = date('Y-m-d H:i', strtotime($value->dtDateTime));
        $Quickedit_endDate = !empty($value->dtEndDateTime) ? date('Y-m-d H:i', strtotime($value->dtEndDateTime)) : 'No Expiry';
        $title = $value->varTitle;
        if (Auth::user()->can('photo-gallery-edit')) {
            if ($value->chrLock != 'Y') {
                if ($this->currentUserRoleData->chrIsAdmin == 'Y') {
                    $title = '<div class="quick_edit"><a href = "' . route('powerpanel.photo-gallery.edit', array('alias' => $value->id)) . '?tab=F">' . $value->varTitle . '</a> <div class="quick_edit_menu">
														<span><a href="' . route('powerpanel.photo-gallery.edit', array('alias' => $value->id)) . '?tab=F" title="Edit">Edit</a></span>';

                    if (Config::get('Constant.DEFAULT_TRASH') == 'Y') {
                        $title .= '<span><a title = "Trash" href = \'javascript:;\' onclick=\'Trashfun("' . $value->id . '")\' class="red" data-tab="F">Trash</a></span>';
                    }
                    $title .= '</div>    
											 </div>';
                } else {
                    $title = '<div class="quick_edit"><a href = "' . route('powerpanel.photo-gallery.edit', array('alias' => $value->id)) . '?tab=F">' . $value->varTitle . '</a> <div class="quick_edit_menu">
														<span><a href="' . route('powerpanel.photo-gallery.edit', array('alias' => $value->id)) . '?tab=F" title="Edit">Edit</a></span>
																</div>    
											 </div>';
                }
            } else {
                if (auth()->user()->id != $value->LockUserID) {
                    if ($this->currentUserRoleData->chrIsAdmin == 'Y') {
                        $title = '<div class="quick_edit"><a href = "' . route('powerpanel.photo-gallery.edit', array('alias' => $value->id)) . '?tab=F">' . $value->varTitle . '</a> <div class="quick_edit_menu">
	                            <span><a href="' . route('powerpanel.photo-gallery.edit', array('alias' => $value->id)) . '?tab=F" title="Edit">Edit</a></span>
	                                </div>    
	                        </div>';
                    } else {
                        $title = '<div class="quick_edit"><a href = "javascript:;">' . $value->varTitle . '</a></div>';
                    }
                } else {
                    $title = '<div class="quick_edit"><a href = "' . route('powerpanel.photo-gallery.edit', array('alias' => $value->id)) . '?tab=F">' . $value->varTitle . '</a> <div class="quick_edit_menu">
	                            <span><a href="' . route('powerpanel.photo-gallery.edit', array('alias' => $value->id)) . '?tab=F" title="Edit">Edit</a></span>
	                                </div>    
	                        </div>';
                }
            }
        }
        $statusdata = '';
        $days = Mylibrary::count_days($value->created_at);
        $days_modified = Mylibrary::count_days($value->updated_at);
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
        if (Auth::user()->can('photo-gallery-reviewchanges')) {
            $update = "<a title=\"Click here to see all approval records.\" class=\"icon_title1\" style=\"margin-right: 5px;\" onclick=\"javascript:expandcollapsepanel(this ,'tasklisting" . $value->id . "', 'mainsingnimg" . $value->id . "'," . $value->id . ")\"><i id=\"mainsingnimg" . $value->id . "\" class=\"la la-plus-square\"></i></a>";
            $rollback = "<a title=\"Click here to see all approved records to rollback.\" class=\"icon_title2\" style=\"margin-right: 5px;\" onclick=\"javascript:expandcollapsepanel_rolback(this ,'tasklisting_rollback" . $value->id . "', 'mainsingnimg_rollback" . $value->id . "'," . $value->id . ")\"><i id=\"mainsingnimg_rollback" . $value->id . "\" class=\"la la-history\"></i></a>";
        } else {
            $update = '';
            $rollback = '';
        }
        $photoAlbum = isset($value->photoAlbum->varTitle) ? $value->photoAlbum->varTitle : '-';
        $imgIcon = '';
        $imgIcon .= '<div class="multi_image_thumb">';
        $imgIcon .= '<a href="' . resize_image::resize($value->fkIntImgId) . '" class="fancybox-buttons"  data-rel="fancybox-buttons">';
        $imgIcon .= '<img height="30" width="30" title="' . preg_replace('/[^A-Za-z0-9\-]/', '-', $value->varTitle) . '" src="' . resize_image::resize($value->fkIntImgId, 50, 50) . '"/>';
        $imgIcon .= '</a>';
        $imgIcon .= '</div>';
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
        $First_td = '<div class="star_box star_box_auto">' . $Favorite . '</div>';
        $logurl = url('powerpanel/log?id=' . $value->id . '&mid=' . Config::get('Constant.MODULE.ID'));

        if ($actions == "") {
            $actions = "---";
        } else {
            $actions = $actions;
        }
        $log = '';
        if ($value->chrLock != 'Y') {
            $log .= $actions;
            if (Auth::user()->can('log-list')) {
                $log .= "<a title=\"Log History\" href=\"$logurl\"><i class=\"fa fa-clock-o\"></i></a>";
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

        $records = array(
            '<input type="checkbox" name="delete" class="chkDelete" value="' . $value->id . '">',
            $First_td,
            '<div class="pages_title_div_row">' . $title . $status . $statusdata . '</div>',
            $imgIcon,
            $photoAlbum,
            $startDate,
            $endDate,
            $log,
            $value->intDisplayOrder,
        );
        return $records;
    }

    public function tableDataDraft($value, $totalRecord = false, $tableSortedType = 'asc') {
        $actions = '';
        $publish_action = '';
        if (Auth::user()->can('photo-gallery-edit')) {
            $actions .= '<a class="" title="' . trans("photogallery::template.common.edit") . '" href="' . route('powerpanel.photo-gallery.edit', array('alias' => $value->id)) . '">
				<i class="fa fa-pencil"></i></a>';
        }
        if (Auth::user()->can('photo-gallery-delete') && $this->currentUserRoleData->chrIsAdmin == 'Y') {
            if (Config::get('Constant.DEFAULT_TRASH') == 'Y') {
                $actions .= '<a lass = "delete-grid" title = "Trash" href = \'javascript:;\' onclick=\'Trashfun("' . $value->id . '")\' data-controller="photo-gallery" data-alias = "' . $value->id . '" data-tab="D"><i class="fa fa-times"></i></a>';
            } else {
                $actions .= '<a class=" delete" title="' . trans("photogallery::template.common.delete") . '" data-controller="photo-gallery" data-alias = "' . $value->id . '" data-tab="D"><i class="fa fa-times"></i></a>';
            }
        }
        $publish_action .= '<input checked="" data-off-text="No" data-on-text="Yes" class="make-switch pub" data-off-color="info" data-on-color="primary" type="checkbox" data-controller="powerpanel/photo-gallery" title="' . trans("photogallery::template.common.publishedRecord") . '" data-value="Unpublish" data-alias="' . $value->id . '">';
        $startDate = date('' . Config::get('Constant.DEFAULT_DATE_FORMAT') . ' ' . Config::get('Constant.DEFAULT_TIME_FORMAT') . '', strtotime($value->dtDateTime));
        $endDate = !empty($value->dtEndDateTime) ? date('' . Config::get('Constant.DEFAULT_DATE_FORMAT') . ' ' . Config::get('Constant.DEFAULT_TIME_FORMAT') . '', strtotime($value->dtEndDateTime)) : 'No Expiry';
        $Quickedit_startDate = date('Y-m-d H:i', strtotime($value->dtDateTime));
        $Quickedit_endDate = !empty($value->dtEndDateTime) ? date('Y-m-d H:i', strtotime($value->dtEndDateTime)) : 'No Expiry';
        $title = $value->varTitle;
        if (Auth::user()->can('photo-gallery-edit')) {
            if ($value->chrLock != 'Y') {
                if ($this->currentUserRoleData->chrIsAdmin == 'Y') {
                    $title = '<div class="quick_edit"><a href = "' . route('powerpanel.photo-gallery.edit', array('alias' => $value->id)) . '?tab=D">' . $value->varTitle . '</a> <div class="quick_edit_menu">
														<span><a href="' . route('powerpanel.photo-gallery.edit', array('alias' => $value->id)) . '?tab=D" title="Edit">Edit</a></span>';
                    if (Config::get('Constant.DEFAULT_TRASH') == 'Y') {
                        $title .= '<span><a title = "Trash" href = \'javascript:;\' onclick=\'Trashfun("' . $value->id . '")\' class="red" data-tab="D">Trash</a></span>';
                    }
                    $title .= '</div>    
											 </div>';
                } else {
                    $title = '<div class="quick_edit"><a href = "' . route('powerpanel.photo-gallery.edit', array('alias' => $value->id)) . '?tab=D">' . $value->varTitle . '</a> <div class="quick_edit_menu">
														<span><a href="' . route('powerpanel.photo-gallery.edit', array('alias' => $value->id)) . '?tab=D" title="Edit">Edit</a></span>
																</div>    
											 </div>';
                }
            } else {
                if (auth()->user()->id != $value->LockUserID) {
                    if ($this->currentUserRoleData->chrIsAdmin == 'Y') {
                        $title = '<div class="quick_edit"><a href = "' . route('powerpanel.photo-gallery.edit', array('alias' => $value->id)) . '?tab=D">' . $value->varTitle . '</a> <div class="quick_edit_menu">
	                            <span><a href="' . route('powerpanel.photo-gallery.edit', array('alias' => $value->id)) . '?tab=D" title="Edit">Edit</a></span></div></div>';
                    } else {
                        $title = '<div class="quick_edit"><a href = "javascript:;">' . $value->varTitle . '</a></div>';
                    }
                } else {
                    $title = '<div class="quick_edit"><a href = "' . route('powerpanel.photo-gallery.edit', array('alias' => $value->id)) . '?tab=D">' . $value->varTitle . '</a> <div class="quick_edit_menu">
	                            <span><a href="' . route('powerpanel.photo-gallery.edit', array('alias' => $value->id)) . '?tab=D" title="Edit">Edit</a></span>
	                                </div>    
	                        </div>';
                }
            }
        }
        $statusdata = '';
        $days = Mylibrary::count_days($value->created_at);
        $days_modified = Mylibrary::count_days($value->updated_at);
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
        if (Auth::user()->can('photo-gallery-reviewchanges')) {
            $update = "<a title=\"Click here to see all approval records.\" class=\"icon_title1\" style=\"margin-right: 5px;\" onclick=\"javascript:expandcollapsepanel(this ,'tasklisting" . $value->id . "', 'mainsingnimg" . $value->id . "'," . $value->id . ")\"><i id=\"mainsingnimg" . $value->id . "\" class=\"la la-plus-square\"></i></a>";
            $rollback = "<a title=\"Click here to see all approved records to rollback.\" class=\"icon_title2\" style=\"margin-right: 5px;\" onclick=\"javascript:expandcollapsepanel_rolback(this ,'tasklisting_rollback" . $value->id . "', 'mainsingnimg_rollback" . $value->id . "'," . $value->id . ")\"><i id=\"mainsingnimg_rollback" . $value->id . "\" class=\"la la-history\"></i></a>";
        } else {
            $update = '';
            $rollback = '';
        }
        $photoAlbum = isset($value->photoAlbum->varTitle) ? $value->photoAlbum->varTitle : '-';
        $imgIcon = '';
        $imgIcon .= '<div class="multi_image_thumb">';
        $imgIcon .= '<a href="' . resize_image::resize($value->fkIntImgId) . '" class="fancybox-buttons"  data-rel="fancybox-buttons">';
        $imgIcon .= '<img height="30" width="30" title="' . preg_replace('/[^A-Za-z0-9\-]/', '-', $value->varTitle) . '" src="' . resize_image::resize($value->fkIntImgId, 50, 50) . '"/>';
        $imgIcon .= '</a>';
        $imgIcon .= '</div>';
        $logurl = url('powerpanel/log?id=' . $value->id . '&mid=' . Config::get('Constant.MODULE.ID'));
        if ($actions == "") {
            $actions = "---";
        } else {
            $actions = $actions;
        }
        $log = '';
        if ($value->chrLock != 'Y') {
            $log .= $actions;
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
            '<input type="checkbox" name="delete" class="chkDelete" value="' . $value->id . '">',
            '<div class="pages_title_div_row"><input type="hidden" id="draftid" value="' . $value->id . '">' . $title . $status . $statusdata . '</div>',
            $imgIcon,
            $photoAlbum,
            $startDate,
            $endDate,
            $publish_action,
            $log,
            $value->intDisplayOrder,
        );
        return $records;
    }

    public function tableDataTrash($value, $totalRecord = false, $tableSortedType = 'asc') {
        $actions = '';
        $publish_action = '';
        if (Auth::user()->can('photo-gallery-delete') && $this->currentUserRoleData->chrIsAdmin == 'Y') {
            $actions .= '<a class=" delete" title="' . trans("photogallery::template.common.delete") . '"  data-controller="photo-gallery" data-alias = "' . $value->id . '" data-tab="T"><i class="fa fa-times"></i></a>';
        }
        $startDate = date('' . Config::get('Constant.DEFAULT_DATE_FORMAT') . ' ' . Config::get('Constant.DEFAULT_TIME_FORMAT') . '', strtotime($value->dtDateTime));
        $endDate = !empty($value->dtEndDateTime) ? date('' . Config::get('Constant.DEFAULT_DATE_FORMAT') . ' ' . Config::get('Constant.DEFAULT_TIME_FORMAT') . '', strtotime($value->dtEndDateTime)) : 'No Expiry';
        $title = $value->varTitle;
        if (Auth::user()->can('photo-gallery-edit')) {
            $title = '<div class="quick_edit text-uppercase">' . $value->varTitle . '</div>';
        }
        $statusdata = '';
        $days = Mylibrary::count_days($value->created_at);
        $days_modified = Mylibrary::count_days($value->updated_at);
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
        $status = ' ';
        if ($value->chrDraft == 'D') {
            $status .= Config::get('Constant.DRAFT_LIST') . ' ';
        }
        if ($value->chrAddStar == 'Y') {
            $status .= Config::get('Constant.APPROVAL_LIST') . ' ';
        }
        if (Auth::user()->can('photo-gallery-reviewchanges')) {
            $update = "<a title=\"Click here to see all approval records.\" class=\"icon_title1\" style=\"margin-right: 5px;\" onclick=\"javascript:expandcollapsepanel(this ,'tasklisting" . $value->id . "', 'mainsingnimg" . $value->id . "'," . $value->id . ")\"><i id=\"mainsingnimg" . $value->id . "\" class=\"la la-plus-square\"></i></a>";
            $rollback = "<a title=\"Click here to see all approved records to rollback.\" class=\"icon_title2\" style=\"margin-right: 5px;\" onclick=\"javascript:expandcollapsepanel_rolback(this ,'tasklisting_rollback" . $value->id . "', 'mainsingnimg_rollback" . $value->id . "'," . $value->id . ")\"><i id=\"mainsingnimg_rollback" . $value->id . "\" class=\"la la-history\"></i></a>";
        } else {
            $update = '';
            $rollback = '';
        }
        $photoAlbum = isset($value->photoAlbum->varTitle) ? $value->photoAlbum->varTitle : '-';
        $imgIcon = '';
        $imgIcon .= '<div class="multi_image_thumb">';
        $imgIcon .= '<a href="' . resize_image::resize($value->fkIntImgId) . '" class="fancybox-buttons"  data-rel="fancybox-buttons">';
        $imgIcon .= '<img height="30" width="30" title="' . preg_replace('/[^A-Za-z0-9\-]/', '-', $value->varTitle) . '" src="' . resize_image::resize($value->fkIntImgId, 50, 50) . '"/>';
        $imgIcon .= '</a>';
        $imgIcon .= '</div>';
        $logurl = url('powerpanel/log?id=' . $value->id . '&mid=' . Config::get('Constant.MODULE.ID'));
        if ($actions == "") {
            $actions = "---";
        } else {
            $actions = $actions;
        }
        $log = '';
        if ($value->chrLock != 'Y') {
            if ($this->currentUserRoleData->chrIsAdmin == 'Y') {
                if (Config::get('Constant.DEFAULT_TRASH') == 'Y') {
                    $log .= "<a title=\"Restore\" href='javascript:;' onclick='Restorefun(\"$value->id\",\"T\")'><i class=\"fa fa-repeat\"></i></a>";
                }
                $log .= $actions;
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
        $records = array(
            '<input type="checkbox" name="delete" class="chkDelete" value="' . $value->id . '">',
            '<div class="pages_title_div_row"><input type="hidden" id="draftid" value="' . $value->id . '">' . $title . $status . $statusdata . '</div>',
            $imgIcon,
            $photoAlbum,
            $startDate,
            $endDate,
            $log,
            $value->intDisplayOrder,
        );
        return $records;
    }

    /**
     * This method handels logs History records
     * @param   $data
     * @return  HTML
     * @since   2017-07-21
     * @author  NetQuick
     */
    public function recordHistory($data = false) {
        $returnHtml = '';
        $startDate = date('' . Config::get('Constant.DEFAULT_DATE_FORMAT') . ' ' . Config::get('Constant.DEFAULT_TIME_FORMAT') . '', strtotime($data->dtDateTime));
        $endDate = !empty($data->dtEndDateTime) ? date('' . Config::get('Constant.DEFAULT_DATE_FORMAT') . ' ' . Config::get('Constant.DEFAULT_TIME_FORMAT') . '', strtotime($data->dtEndDateTime)) : 'No Expiry';
        // $teamCategory = PhotoAlbum::getCatData($data->intPhotoAlbumId);
        $returnHtml .= '<table class="new_table_desing table table-striped table-bordered table-hover">
				<thead>
					<tr>
						<th align="center">' . trans('photogallery::template.common.title') . '</th>	
						<th align="center">Category</th>	
						<th align="center">Image</th>	
						<th align="center">Start Date</th>	
						<th align="center">End Date</th>	
						<th align="center">' . trans('photogallery::template.common.displayorder') . '</th>
						<th align="center">' . trans("photogallery::template.common.publish") . '</th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td align="center">' . stripslashes($data->varTitle) . '</td>	
						<td align="center">' . $teamCategory->varTitle . '</td>';
        if ($data->fkIntImgId > 0) {
            $returnHtml .= '<td align="center">' . '<img height="50" width="50" src="' . resize_image::resize($data->fkIntImgId) . '" />' . '</td>';
        } else {
            $returnHtml .= '<td align="center">-</td>';
        }
        $returnHtml .= '<td align="center">' . $startDate . '</td>	
						<td align="center">' . $endDate . '</td>
						<td align="center">' . ($data->intDisplayOrder) . '</td>
						<td align="center">' . $data->chrPublish . '</td>
					</tr>
				</tbody>
			</table>';
        return $returnHtml;
    }

    /**
     * This method handels logs History records
     * @param   $data
     * @return  HTML
     * @since   2017-07-21
     * @author  NetQuick
     */
    public function newrecordHistory($data = false, $newdata = false) {
        if ($data->varTitle != $newdata->varTitle) {
            $titlecolor = 'style="background-color:#f5efb7"';
        } else {
            $titlecolor = '';
        }
        if ($data->intFKCategory != $newdata->intFKCategory) {
            $catcolor = 'style="background-color:#f5efb7"';
        } else {
            $catcolor = '';
        }
        if ($data->fkIntImgId != $newdata->fkIntImgId) {
            $imgcolor = 'style="background-color:#f5efb7"';
        } else {
            $imgcolor = '';
        }
        if ($data->dtDateTime != $newdata->dtDateTime) {
            $sdatecolor = 'style="background-color:#f5efb7"';
        } else {
            $sdatecolor = '';
        }
        if ($data->dtEndDateTime != $newdata->dtEndDateTime) {
            $edatecolor = 'style="background-color:#f5efb7"';
        } else {
            $edatecolor = '';
        }
        if ($data->intDisplayOrder != $newdata->intDisplayOrder) {
            $ordercolor = 'style="background-color:#f5efb7"';
        } else {
            $ordercolor = '';
        }
        if ($data->chrPublish != $newdata->chrPublish) {
            $Publishcolor = 'style="background-color:#f5efb7"';
        } else {
            $Publishcolor = '';
        }
        $returnHtml = '';
        $startDate = date('' . Config::get('Constant.DEFAULT_DATE_FORMAT') . ' ' . Config::get('Constant.DEFAULT_TIME_FORMAT') . '', strtotime($newdata->dtDateTime));
        $endDate = !empty($newdata->dtEndDateTime) ? date('' . Config::get('Constant.DEFAULT_DATE_FORMAT') . ' ' . Config::get('Constant.DEFAULT_TIME_FORMAT') . '', strtotime($newdata->dtEndDateTime)) : 'No Expiry';
        // $teamCategory = PhotoAlbum::getCatData($newdata->intPhotoAlbumId);
        $returnHtml .= '<table class="new_table_desing table table-striped table-bordered table-hover">
				<thead>
					<tr>
						<th align="center">' . trans('photogallery::template.common.title') . '</th>	
						<th align="center">Category</th>	
						<th align="center">Image</th>	
						<th align="center">Start Date</th>	
						<th align="center">End Date</th>	
						<th align="center">' . trans('photogallery::template.common.displayorder') . '</th>
						<th align="center">' . trans("photogallery::template.common.publish") . '</th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td align="center" ' . $titlecolor . '>' . stripslashes($newdata->varTitle) . '</td>	
						<td align="center" ' . $catcolor . '>' . $teamCategory->varTitle . '</td>';
        if ($newdata->fkIntImgId > 0) {
            $returnHtml .= '<td align="center" ' . $imgcolor . '>' . '<img height="50" width="50" src="' . resize_image::resize($newdata->fkIntImgId) . '" />' . '</td>';
        } else {
            $returnHtml .= '<td align="center">-</td>';
        }
        $returnHtml .= '<td align="center" ' . $sdatecolor . '>' . $startDate . '</td>	
						<td align="center" ' . $edatecolor . '>' . $endDate . '</td>
						<td align="center" ' . $ordercolor . '>' . ($newdata->intDisplayOrder) . '</td>
						<td align="center" ' . $Publishcolor . '>' . $newdata->chrPublish . '</td>
					</tr>
				</tbody>
			</table>';
        return $returnHtml;
    }

    public static function flushCache() {
        Cache::tags('PhotoGallery')->flush();
    }

    public function getChildData() {
        $childHtml = "";
        $Cmspage_childData = "";
        $Cmspage_childData = PhotoGallery::getChildGrid();
        $childHtml .= "<div class=\"producttbl\" style=\"\">";
        $childHtml .= "<table class=\"new_table_desing table table-striped table-bordered table-hover table-checkable dataTable\" id=\"email_log_datatable_ajax\">
																<tr role=\"row\">
																		<th class=\"text-center\"></th>
																																																																								<th class=\"text-center\">Title</th>
																		<th class=\"text-center\">Date Submitted</th>
																		<th class=\"text-center\">User</th>
																		<th class=\"text-center\">Edit</th>
																		<th class=\"text-center\">Status</th>";
        $childHtml .= "         </tr>";
        if (count($Cmspage_childData) > 0) {
            foreach ($Cmspage_childData as $child_row) {
                $parentAlias = '';
                $childHtml .= "<tr role=\"row\">";
                if ($child_row->chrApproved == 'N') {
                    $childHtml .= "<td><span class='mob_show_title'>&nbsp</span><input type=\"checkbox\" name=\"delete\" class=\"chkDelete\" value=\"$child_row->id\"></td>";
                } else {
                    $childHtml .= "<td><span class='mob_show_title'>&nbsp</span><div class=\"checker\"><a href=\"javascript:;\" data-toggle=\"tooltip\" data-placement=\"right\" title=\"This is approved record, so can't be deleted.\"><i style=\"color:red\" class=\"fa fa-exclamation-triangle\"></i></a></div></td>";
                }
                $childHtml .= '<td class="text-center"><span class="mob_show_title">Title: </span>' . $child_row->varTitle . '</td>';
                $childHtml .= "<td class=\"text-center\"><span class='mob_show_title'>Date Submitted: </span>" . date('M d Y h:i A', strtotime($child_row->created_at)) . "</td>";
                $childHtml .= "<td class=\"text-center\"><span class='mob_show_title'>User: </span>" . CommonModel::getUserName($child_row->UserID) . "</td>";
                if ($child_row->chrApproved == 'N') {
                    $childHtml .= "<td class=\"text-center\"><span class='mob_show_title'>Edit: </span><a class='icon_round' title='" . trans("photogallery::template.common.edit") . "' href='" . route('powerpanel.photo-gallery.edit', array('alias' => $child_row->id)) . "'>
							<i class='fa fa-pencil'></i></a></td>";
                } else {
                    $childHtml .= "<td class=\"text-center\"><span class='mob_show_title'>Edit: </span>-</td>";
                }
                if ($child_row->chrApproved == 'N') {
                    $childHtml .= "<td class=\"text-center\"><span class='mob_show_title'>Status: </span><a title='" . trans("photogallery::template.common.comments") . "'   href=\"javascript:;\" onclick=\"loadModelpopup('" . $child_row->id . "','" . $child_row->UserID . "','" . Config::get('Constant.MODULE.MODEL_NAME') . "','" . $child_row->fkMainRecord . "')\" class=\"approve_icon_btn\"><i class=\"fa fa-comments\"></i> <span>Comment</span></a>    <a  onclick=\"update_mainrecord('" . $child_row->id . "','" . $child_row->fkMainRecord . "','" . $child_row->UserID . "','A');\" title='" . trans("photogallery::template.common.clickapprove") . "'  href=\"javascript:;\" class=\"approve_icon_btn\"><i class=\"fa fa-check-square-o\"></i> <span>Approve</span></a></td>";
                } else {
                    $childHtml .= "<td class=\"text-center\"><span class='mob_show_title'>Status: </span><span class='mob_show_overflow'><i class=\"la la-check-circle\" style=\"font-size:30px;\"></i><span style=\"display:block\"><strong>Approved On: </strong>" . date('M d Y h:i A', strtotime($child_row->updated_at)) . "</span><span style=\"display:block\"><strong>Approved By: </strong>" . CommonModel::getUserName($child_row->intApprovedBy) . "</span></span></td>";
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
        $Cmspage_rollbackchildData = PhotoGallery::getChildrollbackGrid();
        $child_rollbackHtml .= "<div class=\"producttbl producttb2\" style=\"\">";
        $child_rollbackHtml .= "<table class=\"new_table_desing table table-striped table-bordered table-hover table-checkable dataTable\" id=\"email_log_datatable_ajax\">
																<tr role=\"row\">      
																																																																<th class=\"text-center\">Title</th>
																		<th class=\"text-center\">Date</th>
																		<th class=\"text-center\">User</th>                                     
																		<th class=\"text-center\">Status</th>";
        $child_rollbackHtml .= "         </tr>";
        if (count($Cmspage_rollbackchildData) > 0) {
            foreach ($Cmspage_rollbackchildData as $child_rollbacrow) {
                $child_rollbackHtml .= "<tr role=\"row\">";
                $child_rollbackHtml .= '<td class="text-center"><span class="mob_show_title">Title: </span>' . $child_rollbacrow->varTitle . '</td>';
                $child_rollbackHtml .= "<td class=\"text-center\"><span class='mob_show_title'>Date: </span>" . date('M d Y h:i A', strtotime($child_rollbacrow->created_at)) . "</td>";
                $child_rollbackHtml .= "<td class=\"text-center\"><span class='mob_show_title'>User: </span>" . CommonModel::getUserName($child_rollbacrow->UserID) . "</td>";
                if ($child_rollbacrow->chrApproved == 'Y') {
                    $child_rollbackHtml .= "<td class=\"text-center\"><span class='mob_show_title'>Status: </span><i class=\"la la-check-circle\" style=\"color: #1080F2;font-size:30px;\"></i></td>";
                } else {
                    $child_rollbackHtml .= "<td class=\"text-center\"><span class='mob_show_title'>Status: </span><a onclick=\"update_mainrecord('" . $child_rollbacrow->id . "','" . $child_rollbacrow->fkMainRecord . "','" . $child_rollbacrow->UserID . "','R');\"  class=\"approve_icon_btn\">
											<i class=\"fa fa-history\"></i>  <span>RollBack</span>
										</a></td>";
                }
                $child_rollbackHtml .= "</tr>";
            }
        } else {
            $child_rollbackHtml .= "<tr><td colspan='6'>No Records</td></tr>";
        }
        echo $child_rollbackHtml;
        exit;
    }

    public function insertComents(Request $request) {
        $Comments_data['intRecordID'] = Request::post('id');
        $Comments_data['varModuleNameSpace'] = Request::post('namespace');
        $Comments_data['varCmsPageComments'] = stripslashes(Request::post('CmsPageComments'));
        $Comments_data['UserID'] = Request::post('UserID');
        $Comments_data['intCommentBy'] = auth()->user()->id;
        $Comments_data['varModuleTitle'] = Config::get('Constant.MODULE.TITLE');
        $Comments_data['fkMainRecord'] = Request::post('fkMainRecord');
        Comments::insertComents($Comments_data);
        exit;
    }

    public function ApprovedData_Listing(Request $request) {
        $requestArr = Request::all();
        $request = (object) $requestArr;
        $id = Request::post('id');
        $main_id = Request::post('main_id');
        $approvalid = Request::post('id');
        $approvalData = PhotoGallery::getOrderOfApproval($id);
        $flag = Request::post('flag');
        $message = PhotoGallery::approved_data_Listing($request);
        if (!empty($approvalData)) {
            self::swap_order_edit($approvalData->intDisplayOrder, $main_id);
        }
        $newCmsPageObj = PhotoGallery::getRecordForLogById($main_id);
        $approval_obj = PhotoGallery::getRecordForLogById($approvalid);
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
        if (method_exists($this->MyLibrary, 'userNotificationData')) {
            $userNotificationArr = MyLibrary::userNotificationData(Config::get('Constant.MODULE.ID'));
            $userNotificationArr['fkRecordId'] = $approvalid;
            $userNotificationArr['txtNotification'] = 'Your request has been approved by ' . ucfirst(auth()->user()->name) . ' (' . ucfirst(Config::get('Constant.MODULE.NAME')) . ')';
            $userNotificationArr['fkIntUserId'] = Auth::user()->id;
            $userNotificationArr['chrNotificationType'] = 'A';
            $userNotificationArr['intOnlyForUserId'] = $approval_obj->UserID;
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

}
