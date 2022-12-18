<?php

namespace Powerpanel\Gallery\Controllers\Powerpanel;

use Request;
use Illuminate\Support\Facades\Redirect;
use Powerpanel\Workflow\Models\Comments;
use Powerpanel\Gallery\Models\Gallery;
use Powerpanel\Workflow\Models\WorkflowLog;
use Powerpanel\Workflow\Models\Workflow;
use App\Log;
use App\RecentUpdates;
use App\Alias;
use Validator;
use DB;
use Request as AjaxRequest;
use Response;
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

class GalleryController extends PowerpanelController {

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
     * This method handels load gallery grid
     * @return  View
     * @since   2017-07-20
     * @author  NetQuick
     */
    public function index() {
//        echo "ASdas";
//        exit;
        $userIsAdmin = false;
        if (isset($this->currentUserRoleData) && !empty($this->currentUserRoleData)) {
            if ($this->currentUserRoleData->chrIsAdmin == 'Y') {
                $userIsAdmin = true;
            }
        } else {
            $userIsAdmin = true;
        }
        $total = Gallery::getRecordCount();
        $draftTotalRecords = Gallery::getRecordCountforListDarft(false, true, $userIsAdmin, array());
        $trashTotalRecords = Gallery::getRecordCountforListTrash();
        $favoriteTotalRecords = Gallery::getRecordCountforListFavorite();
        $NewRecordsCount = Gallery::getNewRecordsCount();
        $photoGallery = Gallery::getRecordList();
        $this->breadcrumb['title'] = trans('gallery::template.GalleryModule.manageGallery');
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
        if (AjaxRequest::ajax()) {
            return Response::json(view('gallery::powerpanel.gallery', ['iTotalRecords' => $total, 'breadcrumb' => $this->breadcrumb, 'MyLibrary' => $this->MyLibrary, 'photoGalleryObj' => $photoGallery, 'NewRecordsCount' => $NewRecordsCount, 'userIsAdmin' => $userIsAdmin, 'draftTotalRecords' => $draftTotalRecords, 'trashTotalRecords' => $trashTotalRecords, 'favoriteTotalRecords' => $favoriteTotalRecords, 'settingarray' => json_encode($settingarray)])->render());
        } else {
            return view('gallery::powerpanel.index', ['iTotalRecords' => $total, 'breadcrumb' => $this->breadcrumb, 'MyLibrary' => $this->MyLibrary, 'photoGalleryObj' => $photoGallery, 'NewRecordsCount' => $NewRecordsCount, 'userIsAdmin' => $userIsAdmin, 'draftTotalRecords' => $draftTotalRecords, 'trashTotalRecords' => $trashTotalRecords, 'favoriteTotalRecords' => $favoriteTotalRecords, 'settingarray' => json_encode($settingarray)]);
        }
    }

    /**
     * This method handels list of gallery with filters
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
        $arrResults = Gallery::getRecordList($filterArr, $isAdmin);
        $iTotalRecords = Gallery::getRecordCountforList($filterArr, true, $isAdmin);
        $end = $filterArr['iDisplayStart'] + $filterArr['iDisplayLength'];
        $end = $end > $iTotalRecords ? $iTotalRecords : $end;
        $tableSortedType = (isset($filterArr['orderTypeAscOrDesc']) && $filterArr['orderTypeAscOrDesc'] != "") ? $filterArr['orderTypeAscOrDesc'] : '';
        $totalRecords = Gallery::getRecordCount();
        if (count($arrResults) > 0 && !empty($arrResults)) {
            foreach ($arrResults as $key => $value) {
                $records["data"][] = $this->tableData($value, $totalRecords, $tableSortedType);
            }
        }
        $NewRecordsCount = Gallery::getNewRecordsCount();
        $records["newRecordCount"] = $NewRecordsCount;
        $records["customActionStatus"] = "OK";
        $records["draw"] = $sEcho;
        $records["recordsTotal"] = $iTotalRecords;
        $records["recordsFiltered"] = $iTotalRecords;
        echo json_encode($records);
        exit;
    }

    /**
     * This method handels list of gallery with filters
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
        $arrResults = Gallery::getRecordListFavorite($filterArr, $isAdmin);
        $iTotalRecords = Gallery::getRecordCountforListFavorite($filterArr, true, $isAdmin);
        $end = $filterArr['iDisplayStart'] + $filterArr['iDisplayLength'];
        $end = $end > $iTotalRecords ? $iTotalRecords : $end;
        $tableSortedType = (isset($filterArr['orderTypeAscOrDesc']) && $filterArr['orderTypeAscOrDesc'] != "") ? $filterArr['orderTypeAscOrDesc'] : '';
        $totalRecords = Gallery::getRecordCount();
        if (count($arrResults) > 0 && !empty($arrResults)) {
            foreach ($arrResults as $key => $value) {
                $records["data"][] = $this->tableDataFavorite($value, $totalRecords, $tableSortedType);
            }
        }
        $NewRecordsCount = Gallery::getNewRecordsCount();
        $records["newRecordCount"] = $NewRecordsCount;
        $records["customActionStatus"] = "OK";
        $records["draw"] = $sEcho;
        $records["recordsTotal"] = $iTotalRecords;
        $records["recordsFiltered"] = $iTotalRecords;
        echo json_encode($records);
        exit;
    }

    /**
     * This method handels list of gallery with filters
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
        $arrResults = Gallery::getRecordListDraft($filterArr, $isAdmin);
        $iTotalRecords = Gallery::getRecordCountforListDarft($filterArr, true, $isAdmin);
        $end = $filterArr['iDisplayStart'] + $filterArr['iDisplayLength'];
        $end = $end > $iTotalRecords ? $iTotalRecords : $end;
        $tableSortedType = (isset($filterArr['orderTypeAscOrDesc']) && $filterArr['orderTypeAscOrDesc'] != "") ? $filterArr['orderTypeAscOrDesc'] : '';
        $totalRecords = Gallery::getRecordCount();
        if (count($arrResults) > 0 && !empty($arrResults)) {
            foreach ($arrResults as $key => $value) {
                $records["data"][] = $this->tableDataDraft($value, $totalRecords, $tableSortedType);
            }
        }
        $NewRecordsCount = Gallery::getNewRecordsCount();
        $records["newRecordCount"] = $NewRecordsCount;
        $records["customActionStatus"] = "OK";
        $records["draw"] = $sEcho;
        $records["recordsTotal"] = $iTotalRecords;
        $records["recordsFiltered"] = $iTotalRecords;
        echo json_encode($records);
        exit;
    }

    /**
     * This method handels list of gallery with filters
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
        $arrResults = Gallery::getRecordListTrash($filterArr, $isAdmin);
        $iTotalRecords = Gallery::getRecordCountforListTrash($filterArr, true, $isAdmin);
        $end = $filterArr['iDisplayStart'] + $filterArr['iDisplayLength'];
        $end = $end > $iTotalRecords ? $iTotalRecords : $end;
        $tableSortedType = (isset($filterArr['orderTypeAscOrDesc']) && $filterArr['orderTypeAscOrDesc'] != "") ? $filterArr['orderTypeAscOrDesc'] : '';
        $totalRecords = Gallery::getRecordCount();
        if (count($arrResults) > 0 && !empty($arrResults)) {
            foreach ($arrResults as $key => $value) {
                $records["data"][] = $this->tableDataTrash($value, $totalRecords, $tableSortedType);
            }
        }
        $NewRecordsCount = Gallery::getNewRecordsCount();
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
        $arrResults = Gallery::getRecordList_tab1($filterArr);
        $iTotalRecords = Gallery::getRecordCountListApprovalTab($filterArr, true);
        $end = $filterArr['iDisplayStart'] + $filterArr['iDisplayLength'];
        $end = $end > $iTotalRecords ? $iTotalRecords : $end;
        if (count($arrResults) > 0 && !empty($arrResults)) {
            foreach ($arrResults as $key => $value) {
                $records["data"][] = $this->tableData_tab1($value);
            }
        }
        $NewRecordsCount = Gallery::getNewRecordsCount();
        $records["newRecordCount"] = $NewRecordsCount;
        $records["customActionStatus"] = "OK";
        $records["draw"] = $sEcho;
        $records["recordsTotal"] = $iTotalRecords;
        $records["recordsFiltered"] = $iTotalRecords;
        echo json_encode($records);
        exit;
    }

    /**
     * This method loads gallery edit view
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
        } else {
            $userIsAdmin = true;
        }
        $templateData = array();
        $avoidPhotoAlbumModuleIds = array();
        $photoAlbumsDropDownList = PhotoAlbum::getPhotoAlbumDropdwonList($avoidPhotoAlbumModuleIds);
        if (!is_numeric($alias)) {
            $total = Gallery::getRecordCount();
            $this->breadcrumb['title'] = trans('gallery::template.GalleryModule.addGallery');
            $this->breadcrumb['module'] = trans('gallery::template.GalleryModule.manageGallery');
            $this->breadcrumb['url'] = 'powerpanel/gallery';
            $this->breadcrumb['inner_title'] = trans('gallery::template.GalleryModule.addGallery');
            $templateData['total'] = $total;
            $templateData['breadcrumb'] = $this->breadcrumb;
            $templateData['imageManager'] = $imageManager;
            $templateData['photoAlbumsDropDownList'] = $photoAlbumsDropDownList;
        } else {
            $id = $alias;
            $Gallery = Gallery::getRecordById($id);
            if (empty($Gallery)) {
                return redirect()->route('powerpanel.gallery.add');
            }
            if ($Gallery->fkMainRecord != '0') {
                $Gallery_highLight = Gallery::getRecordById($Gallery->fkMainRecord);
                $templateData['Gallery_highLight'] = $Gallery_highLight;
            } else {
                $templateData['Gallery_highLight'] = "";
            }
            $this->breadcrumb['title'] = trans('gallery::template.GalleryModule.editGallery') . ' - ' . $Gallery->varTitle;
            $this->breadcrumb['module'] = trans('gallery::template.GalleryModule.manageGallery');
            $this->breadcrumb['url'] = 'powerpanel/gallery';
            $this->breadcrumb['inner_title'] = trans('gallery::template.GalleryModule.editGallery') . ' - ' . $Gallery->varTitle;
            $templateData['Gallery'] = $Gallery;
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
        return view('gallery::powerpanel.actions', $templateData);
    }

    /**
     * This method stores gallery modifications
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
//        echo '<pre>';print_r($postArr);exit;
        $messsages = [
            'order.required' => trans('gallery::template.GalleryModule.displayOrder'),
            'order.greater_than_zero' => trans('gallery::template.GalleryModule.displayGreaterThan'),
            'img_id.required' => 'Image field is required.',
        ];
        $rules = [
            'order' => 'required|greater_than_zero|handle_xss|no_url',
            'chrMenuDisplay' => 'required',
            'img_id' => 'required',
        ];
        $validator = Validator::make($postArr, $rules, $messsages);
        if ($validator->passes()) {
            $GalleryArr = [];
            $module = Modules::getModuleById(Config::get('Constant.MODULE.ID'));
            $id = Request::segment(3);
            $actionMessage = trans('gallery::template.GalleryModule.updateMessage');

            if (File::exists(app_path() . '/Workflow.php') != null || File::exists(base_path() . '/packages/Powerpanel/Workflow/src/Models/Workflow.php') != null) {
                $workFlowByCat = Workflow::getRecordByCategoryId($module->intFkGroupCode, $this->currentUserRoleData->id);
            }
            if (!empty($workFlowByCat->varUserId) && $workFlowByCat->chrNeedAddPermission == 'Y' && !$userIsAdmin) {
                $GalleryArr['chrPublish'] = 'N';
                $GalleryArr['chrDraft'] = 'N';
                $GalleryObj = $this->insertNewRecord($postArr, $GalleryArr);
                if ($postArr['chrMenuDisplay'] == 'D') {
                    $GalleryArr['chrDraft'] = 'D';
                }
                $GalleryArr['chrPublish'] = 'Y';
                $approvalObj = $this->insertApprovalRecord($GalleryObj, $postArr, $GalleryArr);
                $approval = $GalleryObj->id;
            } else {
                $GalleryObj = $this->insertNewRecord($postArr, $GalleryArr);
                $approval = $GalleryObj->id;
            }
            if (isset($postArr['saveandexit']) && $postArr['saveandexit'] == 'approvesaveandexit') {
                $actionMessage = trans('gallery::template.common.recordApprovalMessage');
            } else {
                $actionMessage = trans('gallery::template.GalleryModule.addMessage');
            }
            $id = $GalleryObj->id;

            AddImageModelRel::sync(explode(',', $postArr['img_id']), $id, $approval);
            if ((!empty($postArr['saveandexit']) && $postArr['saveandexit'] == 'saveandexit') || !$userIsAdmin) {
                if ($postArr['chrMenuDisplay'] == 'D') {
                    return redirect()->route('powerpanel.gallery.index', 'tab=D')->with('message', $actionMessage);
                } else {
                    return redirect()->route('powerpanel.gallery.index')->with('message', $actionMessage);
                }
            } else {
                return redirect()->route('powerpanel.gallery.edit', $id)->with('message', $actionMessage);
            }
        } else {
            return Redirect::back()->withErrors($validator)->withInput();
        }
    }

    public function update_store() {
        $data = Request::all();
        $moduleCode = Modules::getModule('gallery');
        $oldPhotoGalleryObj = Gallery::getRecordForLogById($data['id']);
        $whereConditions = ['id' => $data['id']];

        $updatePhotoGalleryFields = [
            'varTitle' => trim($data['title']) ? $data['title'] : null,
            'fkIntImgId' => !empty($data['imgId']) ? $data['imgId'] : null,
        ];

        $update = CommonModel::updateRecords($whereConditions, $updatePhotoGalleryFields, false, "Powerpanel\Gallery\Models\Gallery");
        if ($update) {
            self::swap_order_edit($data['order'], $data['id']);
        }
        $photoGalleryObj = Gallery::getRecordList();
        return view('gallery::powerpanel.gallery', ['photoGalleryObj' => $photoGalleryObj]);
    }

    public function update_status(Request $request) {
        $response = array();
        $data = Request::all();

        $moduleCode = Modules::getModule('gallery');
        if ($data['status'] == 'Y') {
            $photoGalleryObj = Gallery::getRecordForLogById($data['id']);
            $updatePhotoGalleryFields = ['chrPublish' => 'N'];
            $whereConditions = ['id' => $data['id']];
            $update = CommonModel::updateRecords($whereConditions, $updatePhotoGalleryFields, false, "Powerpanel\Gallery\Models\Gallery");
            if ($update) {
                $newPhotoGalleryObj = Gallery::getRecordForLogById($data['id']);

                $logArr = MyLibrary::logData($data['id'], $moduleCode->id);
                if (Auth::user()->can('log-advanced')) {
                    $oldRec = $this->recordHistory($photoGalleryObj);
                    $newRec = $this->recordHistory($newPhotoGalleryObj);
                    $logArr['old_val'] = $oldRec;
                    $logArr['new_val'] = $newRec;
                }

                $logArr['varTitle'] = $newPhotoGalleryObj->varTitle;
                $logArr['action'] = 'edit';
                Log::recordLog($logArr);

                if (Auth::user()->can('recent-updates-list')) {
                    $notificationArr = MyLibrary::notificationData($data['id'], $newPhotoGalleryObj, $moduleCode->id);
                    RecentUpdates::setNotification($notificationArr);
                }
                self::flushCache();
                $response['unpublish'] = trans('gallery::template.GalleryModule.unpublishedMessage');
            }
        }
        if ($data['status'] == 'N') {
            $photoGalleryObj = Gallery::getRecordForLogById($data['id']);
            $updatePhotoGalleryFields = ['chrPublish' => 'Y'];
            $whereConditions = ['id' => $data['id']];
            $update = CommonModel::updateRecords($whereConditions, $updatePhotoGalleryFields, false, "Powerpanel\Gallery\Models\Gallery");
            if ($update) {
                $newPhotoGalleryObj = Gallery::getRecordForLogById($data['id']);

                $logArr = MyLibrary::logData($data['id'], $moduleCode->id);
                if (Auth::user()->can('log-advanced')) {
                    $oldRec = $this->recordHistory($photoGalleryObj);
                    $newRec = $this->recordHistory($newPhotoGalleryObj);
                    $logArr['old_val'] = $oldRec;
                    $logArr['new_val'] = $newRec;
                }
                $logArr['action'] = 'edit';
                $logArr['varTitle'] = $newPhotoGalleryObj->varTitle;

                Log::recordLog($logArr);
                if (Auth::user()->can('recent-updates-list')) {
                    $notificationArr = MyLibrary::notificationData($data['id'], $newPhotoGalleryObj, $moduleCode->id);
                    RecentUpdates::setNotification($notificationArr);
                }
                self::flushCache();
                $response['publish'] = trans('gallery::template.GalleryModule.publishedMessage');
            }
        }
        echo json_encode($response);
        exit;
    }

//    public function destroy(Request $request) {
//        $response = array();
//        $data = Request::all();
//
//        $id = $data['id'];
//        if (!empty($id)) {
//            $newPhotoGalleryObj = Gallery::getRecordById($data['id']);
//
//            $moduleCode = Modules::getModule('gallery');
//            $logArr = MyLibrary::logData($data['id'], 'Powerpanel\Gallery\Models\Gallery');
//            $logArr['varTitle'] = $newPhotoGalleryObj->varTitle;
//            Log::recordLog($logArr);
//
//            if (Auth::user()->can('recent-updates-list')) {
//                $notificationArr = MyLibrary::notificationData($data['id'], $newPhotoGalleryObj, 'Powerpanel\Gallery\Models\Gallery');
//                RecentUpdates::setNotification($notificationArr);
//            }
//            $delete = Gallery::where('id', $id)->update(['chrPublish' => 'N', 'chrDelete' => 'Y']);
//            if ($delete) {
//                $objModule = CommonModel::getRecordsForDeleteById($id, 'Powerpanel\Gallery\Models\Gallery');
//                // print_r($objModule);die;
//                if (isset($objModule->intDisplayOrder)) {
//                    CommonModel::updateOrder($objModule);
//                    $updateFields['intDisplayOrder'] = 1;
//                }
//
//                $photoGalleryObj = Gallery::getRecordList();
//                $display_order = $photoGalleryObj->count() + 1;
//                self::flushCache();
//                return Response::json(view('powerpanel.gallery.gallery', array('photoGalleryObj' => $photoGalleryObj, 'display_order' => $display_order, 'imageManager' => true))->render());
//            }
//        }
//    }

    public function insertApprovedRecord($updateModuleFields, $postArr, $id) {
        $whereConditions = ['id' => $postArr['fkMainRecord']];
        $updateModuleFields['chrAddStar'] = 'N';
        $updateModuleFields['UserID'] = auth()->user()->id;
        $update = CommonModel::updateRecords($whereConditions, $updateModuleFields, false, 'Powerpanel\Gallery\Models\Gallery');
        if ($update) {
            self::swap_order_edit($postArr['order'], $postArr['fkMainRecord']);
        }
        $whereConditions_ApproveN = ['fkMainRecord' => $postArr['fkMainRecord']];
        $updateToApproveN = [
            'chrApproved' => 'N',
            'chrLetest' => 'N',
            'intApprovedBy' => '0',
        ];
        CommonModel::updateRecords($whereConditions_ApproveN, $updateToApproveN, false, 'Powerpanel\Gallery\Models\Gallery');
        $whereConditionsApprove = ['id' => $id, 'chrMain' => 'N'];
        $updateToApprove = [
            'chrApproved' => 'Y',
            'chrRollBack' => 'Y',
            'intApprovedBy' => auth()->user()->id
        ];
        CommonModel::updateRecords($whereConditionsApprove, $updateToApprove, false, 'Powerpanel\Gallery\Models\Gallery');
        if ($postArr['chrMenuDisplay'] == 'D') {
            $addlog = Config::get('Constant.DRAFT_RECORD_APPROVED');
        } else {
            $addlog = Config::get('Constant.RECORD_APPROVED');
        }
        $newBannerObj = Gallery::getRecordForLogById($id);
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
                $actionMessage = trans('gallery::template.GalleryModule.updateMessage');
            }
        }
    }

    public function insertApprovalRecord($moduleObj, $postArr, $GalleryArr) {
        $response = false;
        $GalleryArr['chrMain'] = 'N';
        $GalleryArr['chrLetest'] = 'Y';
        $GalleryArr['fkMainRecord'] = $moduleObj->id;
//        $GalleryArr['varTitle'] = stripslashes(trim($postArr['title']));
        if ($postArr['chrMenuDisplay'] == 'D') {
            $GalleryArr['chrDraft'] = 'D';
            $GalleryArr['chrPublish'] = 'N';
        } else {
            $GalleryArr['chrDraft'] = 'N';
            $GalleryArr['chrPublish'] = $postArr['chrMenuDisplay'];
        }
        $GalleryArr['intDisplayOrder'] = $postArr['order'];

        $GalleryArr['dtDateTime'] = !empty($postArr['start_date_time']) ? date('Y-m-d H:i:s', strtotime($postArr['start_date_time'])) : date('Y-m-d H:i:s');
        $GalleryArr['dtEndDateTime'] = !empty($postArr['end_date_time']) ? date('Y-m-d H:i:s', strtotime($postArr['end_date_time'])) : null;

        $GalleryArr['created_at'] = Carbon::now();
        $GalleryArr['UserID'] = auth()->user()->id;
        $GalleryArr['fkIntImgId'] = !empty($postArr['img_id']) ? $postArr['img_id'] : null;
        if ($postArr['chrMenuDisplay'] == 'D') {
            $addlog = Config::get('Constant.DRAFT_SENT_FOR_APPROVAL');
        } else {
            $addlog = Config::get('Constant.SENT_FOR_APPROVAL');
        }
        $GalleryID = CommonModel::addRecord($GalleryArr, 'Powerpanel\Gallery\Models\Gallery');
        if (!empty($GalleryID)) {
            $id = $GalleryID;
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
            $newGalleryObj = Gallery::getRecordForLogById($id);
            $logArr = MyLibrary::logData($id, false, $addlog);
            $logArr['varTitle'] = $newGalleryObj->varTitle;
            Log::recordLog($logArr);
            if (Auth::user()->can('recent-updates-list')) {
                $notificationArr = MyLibrary::notificationData($id, $newGalleryObj);
                RecentUpdates::setNotification($notificationArr);
            }
            $response = $newGalleryObj;
            self::flushCache();
            $actionMessage = trans('gallery::template.GalleryModule.addMessage');
        }
        $whereConditionsAddstar = ['id' => $moduleObj->id];
        $updateAddStar = [
            'chrAddStar' => 'Y',
        ];
        CommonModel::updateRecords($whereConditionsAddstar, $updateAddStar, false, 'Powerpanel\Gallery\Models\Gallery');
        return $response;
    }

    public function insertNewRecord($postArr, $GalleryArr) {
//        echo '<pre>';print_r($postArr);exit;
        $response = false;
        $GalleryArr['chrMain'] = 'Y';
        if ($postArr['chrMenuDisplay'] == 'Y') {
            $GalleryArr['chrDraft'] = 'D';
        } else {
            $GalleryArr['chrDraft'] = 'N';
        }
        $GalleryArr['UserID'] = auth()->user()->id;
        $GalleryArr['created_at'] = Carbon::now();
        $image = explode(",", $postArr['img_id']);

        foreach ($image as $value) {
            $Imagecount = DB::table('gallery')->where('chrDelete', '=', 'N')->count();
            $count = $Imagecount + 1;
            if ($postArr['chrMenuDisplay'] == 'Y') {
                $GalleryArr['chrPublish'] = 'Y';
            } else {
                $GalleryArr['chrPublish'] = 'N';
            }
            $Imagedata = DB::table('image')->select('txtImgOriginalName')->where('id', '=', $value)->where('chrPublish', '=', 'Y')->where('chrDelete', '=', 'N')->first();
            $GalleryArr['varTitle'] = stripslashes(trim($Imagedata->txtImgOriginalName));
            $GalleryArr['fkIntImgId'] = !empty($value) ? $value : null;
            $GalleryArr['intDisplayOrder'] = $count;
            $GalleryID = CommonModel::addRecord($GalleryArr, 'Powerpanel\Gallery\Models\Gallery');
        }

        if (!empty($GalleryID)) {
            $id = $GalleryID;
            $newGalleryObj = Gallery::getRecordForLogById($id);
            $logArr = MyLibrary::logData($id);
            $logArr['varTitle'] = $newGalleryObj->varTitle;
            Log::recordLog($logArr);
            if (Auth::user()->can('recent-updates-list')) {
                $notificationArr = MyLibrary::notificationData($id, $newGalleryObj);
                RecentUpdates::setNotification($notificationArr);
            }
            $response = $newGalleryObj;
            self::flushCache();
            $actionMessage = trans('gallery::template.GalleryModule.addMessage');
        }
        return $response;
    }

    /**
     * This method destroys Gallery in multiples
     * @return  Gallery index view
     * @since   2016-10-25
     * @author  NetQuick
     */
    public function DeleteRecord(Request $request) {
        $data = Request::all();
        $value = $data['ids'];
        $update = Gallery::where('id', $value)->update(['chrPublish' => 'N', 'chrDelete' => 'Y']);
        $moduleCode = Modules::getModule('gallery');
        if ($update) {
            $objModule = CommonModel::getRecordsForDeleteById($value, $moduleCode, 'Powerpanel\Gallery\Models\Gallery');
            if (isset($objModule->intDisplayOrder)) {
                CommonModel::updateOrder($objModule);
                $updateFields['intDisplayOrder'] = 1;
            }
        }
        $photoGalleryObj = Gallery::getRecordList();
        $display_order = $photoGalleryObj->count() + 1;
        self::flushCache();
//        return response()->json($photoGalleryObj);

        return view('gallery::powerpanel.gallery', ['photoGalleryObj' => $photoGalleryObj]);

//        return Response::json(view('powerpanel.gallery.gallery', array('photoGalleryObj' => $photoGalleryObj, 'display_order' => $display_order, 'imageManager' => true))->render());
//        return $update;
//        return Response::json(view('gallery::powerpanel.index', array('photoGalleryObj' => $photoGalleryObj,'display_order' => $display_order, 'settingarray' => json_encode('')))->render());
//        return view('gallery::powerpanel.index', ['photoGalleryObj' => $photoGalleryObj]);
    }

    /**
     * This method destroys Gallery in multiples
     * @return  Gallery index view
     * @since   2016-10-25
     * @author  NetQuick
     */
    public function publish(Request $request) {
        $requestArr = Request::all();
//        $request = (object) $requestArr;
        $val = Request::get('val');
        $alias = (int) Request::get('alias');
        $update = MyLibrary::setPublishUnpublish($alias, $val, 'Powerpanel\Gallery\Models\Gallery');
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
        MyLibrary::swapOrder($order, $exOrder, 'Powerpanel\Gallery\Models\Gallery');
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
            $response = MyLibrary::swapOrderAdd($order, $isCustomizeModule, $moduleHaveFields, 'Powerpanel\Gallery\Models\Gallery');
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
//    public static function swap_order_edit($order = null, $id = null) {
//        $isCustomizeModule = true;
//        $moduleHaveFields = ['chrMain'];
//        MyLibrary::swapOrderEdit($order, $id, $isCustomizeModule, $moduleHaveFields, 'Powerpanel\Gallery\Models\Gallery');
//        self::flushCache();
//    }

    public static function swap_order_edit($order = null, $id = null) {
        MyLibrary::swapOrderEdit($order, $id, false, false, 'Powerpanel\Gallery\Models\Gallery');
        self::flushCache();
    }

    public function tableData_tab1($value) {
        $actions = '';
        $publish_action = '';
        if (Auth::user()->can('gallery-edit')) {
            $actions .= '<a class="" title="' . trans("gallery::template.common.edit") . '" href="' . route('powerpanel.gallery.edit', array('alias' => $value->id)) . '">
				<i class="fa fa-pencil"></i></a>';
        }
        if (Auth::user()->can('gallery-delete') && $this->currentUserRoleData->chrIsAdmin == 'Y') {
            if (Config::get('Constant.DEFAULT_TRASH') == 'Y') {
                $actions .= '<a class = "delete-grid" title = "Trash" href = \'javascript:;\' onclick=\'Trashfun("' . $value->id . '")\' data-controller="gallery" data-alias = "' . $value->id . '" data-tab="A"><i class="fa fa-times"></i></a>';
            } else {
                $actions .= '<a class=" delete" title="' . trans("gallery::template.common.delete") . '" data-controller="gallery" data-alias = "' . $value->id . '" data-tab="A"><i class="fa fa-times"></i></a>';
            }
        }

        if (Auth::user()->can('gallery-edit')) {
            $title = '<a class="" title="' . trans("gallery::template.common.edit") . '" href="' . route('powerpanel.gallery.edit', array('alias' => $value->id)) . '">' . $value->varTitle . '</a>';
        } else {
            $title = stripslashes($value->varTitle);
        }
        $photoAlbum = isset($value->photoAlbum->varTitle) ? $value->photoAlbum->varTitle : '-';
        if (Auth::user()->can('gallery-reviewchanges')) {
            $update = "<a title=\"Click here to see all approval records.\" class=\"icon_title1\" style=\"margin-right: 5px;\" onclick=\"javascript:expandcollapsepanel(this ,'tasklisting" . $value->id . "', 'mainsingnimg" . $value->id . "'," . $value->id . ")\"><i id=\"mainsingnimg" . $value->id . "\" class=\"la la-plus-square\"></i></a>";
            $rollback = "<a title=\"Click here to see all approved records to rollback.\" class=\"icon_title2\" style=\"margin-right: 5px;\" onclick=\"javascript:expandcollapsepanel_rolback(this ,'tasklisting_rollback" . $value->id . "', 'mainsingnimg_rollback" . $value->id . "'," . $value->id . ")\"><i id=\"mainsingnimg_rollback" . $value->id . "\" class=\"la la-history\"></i></a>";
        } else {
            $update = '';
            $rollback = '';
        }
        if (Auth::user()->can('gallery-reviewchanges') && $value->chrAddStar == 'Y') {
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
        if (Auth::user()->can('gallery-edit')) {
            if ($value->chrLock != 'Y') {
                $title = '<div class="quick_edit"><a href = "' . route('powerpanel.gallery.edit', array('alias' => $value->id)) . '?tab=A">' . $value->varTitle . '</a> <div class="quick_edit_menu">
														<span><a href="' . route('powerpanel.gallery.edit', array('alias' => $value->id)) . '?tab=A" title="Edit">Edit</a></span>';
                if (Config::get('Constant.DEFAULT_TRASH') == 'Y') {
                    $title .= '<span><a title = "Trash" href = \'javascript:;\' onclick=\'Trashfun("' . $value->id . '")\' class="red" data-tab="A">Trash</a></span>';
                }
                $title .= '</div></div>';
            } else {
                if (auth()->user()->id != $value->LockUserID) {
                    if ($this->currentUserRoleData->chrIsAdmin == 'Y') {
                        $title = '<div class="quick_edit"><a href = "' . route('powerpanel.gallery.edit', array('alias' => $value->id)) . '?tab=A">' . $value->varTitle . '</a> <div class="quick_edit_menu">
	                            <span><a href="' . route('powerpanel.gallery.edit', array('alias' => $value->id)) . '?tab=A" title="Edit">Edit</a></span></div></div>';
                    } else {
                        $title = '<div class="quick_edit"><a href = "javascript:;">' . $value->varTitle . '</a></div>';
                    }
                } else {
                    $title = '<div class="quick_edit"><a href = "' . route('powerpanel.gallery.edit', array('alias' => $value->id)) . '?tab=A">' . $value->varTitle . '</a> <div class="quick_edit_menu">
	                            <span><a href="' . route('powerpanel.gallery.edit', array('alias' => $value->id)) . '?tab=A" title="Edit">Edit</a></span></div></div>';
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
        if (Auth::user()->can('gallery-edit')) {
            $actions .= '<a class="" title="' . trans("gallery::template.common.edit") . '" href="' . route('powerpanel.gallery.edit', array('alias' => $value->id)) . '">
				<i class="fa fa-pencil"></i></a>';
        }
        if (Auth::user()->can('gallery-delete') || (isset($this->currentUserRoleData->chrIsAdmin) && $this->currentUserRoleData->chrIsAdmin == 'Y')) {
            if (Config::get('Constant.DEFAULT_TRASH') == 'Y') {
                $actions .= '<a class="delete-grid" title="' . trans("gallery::template.common.delete") . '" onclick = \'Trashfun("' . $value->id . '")\' data-controller="gallery" data-alias = "' . $value->id . '" data-tab="P"><i class="fa fa-times"></i></a>';
            } else {
                $actions .= '<a class="delete" title="' . trans("gallery::template.common.delete") . '" data-controller="gallery" data-alias = "' . $value->id . '" data-tab="P"><i class="fa fa-times"></i></a>';
            }
        }
        if ($value->chrAddStar != 'Y') {
            if ($value->chrDraft != 'D') {
                if (Auth::user()->can('gallery-publish')) {
                    if ($value->chrPublish == 'Y') {
                        $publish_action .= '<input data-off-text="No" data-on-text="Yes" class="make-switch publish" class="make-switch publish" data-off-color="info" data-on-color="primary" type="checkbox" data-controller="powerpanel/gallery" title="' . trans("gallery::template.common.publishedRecord") . '" data-value="Unpublish" data-alias="' . $value->id . '">';
                    } else {
                        $publish_action .= '<input checked="" data-off-text="No" data-on-text="Yes" class="make-switch publish" class="make-switch publish" data-off-color="info" data-on-color="primary" type="checkbox" data-controller="powerpanel/gallery" title="' . trans("gallery::template.common.unpublishedRecord") . '" data-value="Publish" data-alias="' . $value->id . '">';
                    }
                }
            } else {
                $publish_action .= '<input checked="" data-off-text="No" data-on-text="Yes" class="make-switch pub" data-off-color="info" data-on-color="primary" type="checkbox" data-controller="powerpanel/gallery" title="' . trans("gallery::template.common.unpublishedRecord") . '" data-value="Publish" data-alias="' . $value->id . '">';
            }
        } else {
            $publish_action .= '---';
        }
        $startDate = date('' . Config::get('Constant.DEFAULT_DATE_FORMAT') . ' ' . Config::get('Constant.DEFAULT_TIME_FORMAT') . '', strtotime($value->dtDateTime));
        $endDate = !empty($value->dtEndDateTime) ? date('' . Config::get('Constant.DEFAULT_DATE_FORMAT') . ' ' . Config::get('Constant.DEFAULT_TIME_FORMAT') . '', strtotime($value->dtEndDateTime)) : 'No Expiry';
        $Quickedit_startDate = date('Y-m-d H:i', strtotime($value->dtDateTime));
        $Quickedit_endDate = !empty($value->dtEndDateTime) ? date('Y-m-d H:i', strtotime($value->dtEndDateTime)) : 'No Expiry';
        $title = $value->varTitle;
        if (Auth::user()->can('gallery-edit')) {
            if ($value->chrLock != 'Y') {
                if (isset($this->currentUserRoleData->chrIsAdmin) && $this->currentUserRoleData->chrIsAdmin == 'Y') {
                    $title = '<div class="quick_edit"><a href = "' . route('powerpanel.gallery.edit', array('alias' => $value->id)) . '?tab=P">' . $value->varTitle . '</a> <div class="quick_edit_menu">
														<span><a href="' . route('powerpanel.gallery.edit', array('alias' => $value->id)) . '?tab=P" title="Edit">Edit</a></span>';
                    if (Config::get('Constant.DEFAULT_QUICK') == 'Y') {
                        $title .= '<span><a title="Quick Edit" href=\'javascript:;\' data-toggle=\'modal\' data-target=\'#modalForm\' aria-label=\'Quick edit\' onclick=\'Quickeditfun("' . $value->id . '","' . $value->varTitle . '","' . $value->intSearchRank . '","' . $Quickedit_startDate . '","' . $Quickedit_endDate . '","P")\'>Quick Edit</a></span>';
                    }
                    if (Config::get('Constant.DEFAULT_TRASH') == 'Y') {
                        $title .= '<span><a title = "Trash" href = \'javascript:;\' onclick=\'Trashfun("' . $value->id . '")\' class="red" data-tab="P">Trash</a></span>';
                    }
                    $title .= '</div>    
											 </div>';
                } else {
                    $title = '<div class="quick_edit"><a href = "' . route('powerpanel.gallery.edit', array('alias' => $value->id)) . '?tab=P">' . $value->varTitle . '</a> <div class="quick_edit_menu">
														<span><a href="' . route('powerpanel.gallery.edit', array('alias' => $value->id)) . '?tab=P" title="Edit">Edit</a></span>
																</div>    
											 </div>';
                }
            } else {
                if (auth()->user()->id != $value->LockUserID) {
                    if (isset($this->currentUserRoleData->chrIsAdmin) && $this->currentUserRoleData->chrIsAdmin == 'Y') {
                        $title = '<div class="quick_edit"><a href = "' . route('powerpanel.gallery.edit', array('alias' => $value->id)) . '?tab=P">' . $value->varTitle . '</a> <div class="quick_edit_menu">
                            <span><a href="' . route('powerpanel.gallery.edit', array('alias' => $value->id)) . '?tab=P" title="Edit">Edit</a></span>
                                </div>    
                       </div>';
                    } else {
                        $title = '<div class="quick_edit"><a href = "javascript:;">' . $value->varTitle . '</a></div>';
                    }
                } else {
                    $title = '<div class="quick_edit"><a href = "' . route('powerpanel.gallery.edit', array('alias' => $value->id)) . '?tab=P">' . $value->varTitle . '</a> <div class="quick_edit_menu">
                            <span><a href="' . route('powerpanel.gallery.edit', array('alias' => $value->id)) . '?tab=P" title="Edit">Edit</a></span>
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
        if (Auth::user()->can('gallery-reviewchanges') && (File::exists(app_path() . '/Workflow.php') != null || File::exists(base_path() . '/packages/Powerpanel/Workflow/src/Models/Workflow.php') != null)) {
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
        if (Auth::user()->can('gallery-edit')) {
            $actions .= '<a class="" title="' . trans("gallery::template.common.edit") . '" href="' . route('powerpanel.gallery.edit', array('alias' => $value->id)) . '">
				<i class="fa fa-pencil"></i></a>';
        }
        if (Auth::user()->can('gallery-delete') && $this->currentUserRoleData->chrIsAdmin == 'Y') {
            if (Config::get('Constant.DEFAULT_TRASH') == 'Y') {
                $actions .= '<a class="delete-grid" title="' . trans("gallery::template.common.delete") . '" onclick = \'Trashfun("' . $value->id . '")\' data-controller="gallery" data-alias = "' . $value->id . '" data-tab="F"><i class="fa fa-times"></i></a>';
            } else {
                $actions .= '<a class="delete" title="' . trans("gallery::template.common.delete") . '" data-controller="gallery" data-alias = "' . $value->id . '" data-tab="F"><i class="fa fa-times"></i></a>';
            }
        }
        $startDate = date('' . Config::get('Constant.DEFAULT_DATE_FORMAT') . ' ' . Config::get('Constant.DEFAULT_TIME_FORMAT') . '', strtotime($value->dtDateTime));
        $endDate = !empty($value->dtEndDateTime) ? date('' . Config::get('Constant.DEFAULT_DATE_FORMAT') . ' ' . Config::get('Constant.DEFAULT_TIME_FORMAT') . '', strtotime($value->dtEndDateTime)) : 'No Expiry';
        $Quickedit_startDate = date('Y-m-d H:i', strtotime($value->dtDateTime));
        $Quickedit_endDate = !empty($value->dtEndDateTime) ? date('Y-m-d H:i', strtotime($value->dtEndDateTime)) : 'No Expiry';
        $title = $value->varTitle;
        if (Auth::user()->can('gallery-edit')) {
            if ($value->chrLock != 'Y') {
                if ($this->currentUserRoleData->chrIsAdmin == 'Y') {
                    $title = '<div class="quick_edit"><a href = "' . route('powerpanel.gallery.edit', array('alias' => $value->id)) . '?tab=F">' . $value->varTitle . '</a> <div class="quick_edit_menu">
														<span><a href="' . route('powerpanel.gallery.edit', array('alias' => $value->id)) . '?tab=F" title="Edit">Edit</a></span>';

                    if (Config::get('Constant.DEFAULT_TRASH') == 'Y') {
                        $title .= '<span><a title = "Trash" href = \'javascript:;\' onclick=\'Trashfun("' . $value->id . '")\' class="red" data-tab="F">Trash</a></span>';
                    }
                    $title .= '</div>    
											 </div>';
                } else {
                    $title = '<div class="quick_edit"><a href = "' . route('powerpanel.gallery.edit', array('alias' => $value->id)) . '?tab=F">' . $value->varTitle . '</a> <div class="quick_edit_menu">
														<span><a href="' . route('powerpanel.gallery.edit', array('alias' => $value->id)) . '?tab=F" title="Edit">Edit</a></span>
																</div>    
											 </div>';
                }
            } else {
                if (auth()->user()->id != $value->LockUserID) {
                    if ($this->currentUserRoleData->chrIsAdmin == 'Y') {
                        $title = '<div class="quick_edit"><a href = "' . route('powerpanel.gallery.edit', array('alias' => $value->id)) . '?tab=F">' . $value->varTitle . '</a> <div class="quick_edit_menu">
	                            <span><a href="' . route('powerpanel.gallery.edit', array('alias' => $value->id)) . '?tab=F" title="Edit">Edit</a></span>
	                                </div>    
	                        </div>';
                    } else {
                        $title = '<div class="quick_edit"><a href = "javascript:;">' . $value->varTitle . '</a></div>';
                    }
                } else {
                    $title = '<div class="quick_edit"><a href = "' . route('powerpanel.gallery.edit', array('alias' => $value->id)) . '?tab=F">' . $value->varTitle . '</a> <div class="quick_edit_menu">
	                            <span><a href="' . route('powerpanel.gallery.edit', array('alias' => $value->id)) . '?tab=F" title="Edit">Edit</a></span>
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
        if (Auth::user()->can('gallery-reviewchanges')) {
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
        if (Auth::user()->can('gallery-edit')) {
            $actions .= '<a class="" title="' . trans("gallery::template.common.edit") . '" href="' . route('powerpanel.gallery.edit', array('alias' => $value->id)) . '">
				<i class="fa fa-pencil"></i></a>';
        }
        if (Auth::user()->can('gallery-delete') && $this->currentUserRoleData->chrIsAdmin == 'Y') {
            if (Config::get('Constant.DEFAULT_TRASH') == 'Y') {
                $actions .= '<a lass = "delete-grid" title = "Trash" href = \'javascript:;\' onclick=\'Trashfun("' . $value->id . '")\' data-controller="gallery" data-alias = "' . $value->id . '" data-tab="D"><i class="fa fa-times"></i></a>';
            } else {
                $actions .= '<a class=" delete" title="' . trans("gallery::template.common.delete") . '" data-controller="gallery" data-alias = "' . $value->id . '" data-tab="D"><i class="fa fa-times"></i></a>';
            }
        }
        $publish_action .= '<input checked="" data-off-text="No" data-on-text="Yes" class="make-switch pub" data-off-color="info" data-on-color="primary" type="checkbox" data-controller="powerpanel/gallery" title="' . trans("gallery::template.common.publishedRecord") . '" data-value="Unpublish" data-alias="' . $value->id . '">';
        $startDate = date('' . Config::get('Constant.DEFAULT_DATE_FORMAT') . ' ' . Config::get('Constant.DEFAULT_TIME_FORMAT') . '', strtotime($value->dtDateTime));
        $endDate = !empty($value->dtEndDateTime) ? date('' . Config::get('Constant.DEFAULT_DATE_FORMAT') . ' ' . Config::get('Constant.DEFAULT_TIME_FORMAT') . '', strtotime($value->dtEndDateTime)) : 'No Expiry';
        $Quickedit_startDate = date('Y-m-d H:i', strtotime($value->dtDateTime));
        $Quickedit_endDate = !empty($value->dtEndDateTime) ? date('Y-m-d H:i', strtotime($value->dtEndDateTime)) : 'No Expiry';
        $title = $value->varTitle;
        if (Auth::user()->can('gallery-edit')) {
            if ($value->chrLock != 'Y') {
                if ($this->currentUserRoleData->chrIsAdmin == 'Y') {
                    $title = '<div class="quick_edit"><a href = "' . route('powerpanel.gallery.edit', array('alias' => $value->id)) . '?tab=D">' . $value->varTitle . '</a> <div class="quick_edit_menu">
														<span><a href="' . route('powerpanel.gallery.edit', array('alias' => $value->id)) . '?tab=D" title="Edit">Edit</a></span>';
                    if (Config::get('Constant.DEFAULT_TRASH') == 'Y') {
                        $title .= '<span><a title = "Trash" href = \'javascript:;\' onclick=\'Trashfun("' . $value->id . '")\' class="red" data-tab="D">Trash</a></span>';
                    }
                    $title .= '</div>    
											 </div>';
                } else {
                    $title = '<div class="quick_edit"><a href = "' . route('powerpanel.gallery.edit', array('alias' => $value->id)) . '?tab=D">' . $value->varTitle . '</a> <div class="quick_edit_menu">
														<span><a href="' . route('powerpanel.gallery.edit', array('alias' => $value->id)) . '?tab=D" title="Edit">Edit</a></span>
																</div>    
											 </div>';
                }
            } else {
                if (auth()->user()->id != $value->LockUserID) {
                    if ($this->currentUserRoleData->chrIsAdmin == 'Y') {
                        $title = '<div class="quick_edit"><a href = "' . route('powerpanel.gallery.edit', array('alias' => $value->id)) . '?tab=D">' . $value->varTitle . '</a> <div class="quick_edit_menu">
	                            <span><a href="' . route('powerpanel.gallery.edit', array('alias' => $value->id)) . '?tab=D" title="Edit">Edit</a></span></div></div>';
                    } else {
                        $title = '<div class="quick_edit"><a href = "javascript:;">' . $value->varTitle . '</a></div>';
                    }
                } else {
                    $title = '<div class="quick_edit"><a href = "' . route('powerpanel.gallery.edit', array('alias' => $value->id)) . '?tab=D">' . $value->varTitle . '</a> <div class="quick_edit_menu">
	                            <span><a href="' . route('powerpanel.gallery.edit', array('alias' => $value->id)) . '?tab=D" title="Edit">Edit</a></span>
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
        if (Auth::user()->can('gallery-reviewchanges')) {
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
        if (Auth::user()->can('gallery-delete') && $this->currentUserRoleData->chrIsAdmin == 'Y') {
            $actions .= '<a class=" delete" title="' . trans("gallery::template.common.delete") . '"  data-controller="gallery" data-alias = "' . $value->id . '" data-tab="T"><i class="fa fa-times"></i></a>';
        }
        $startDate = date('' . Config::get('Constant.DEFAULT_DATE_FORMAT') . ' ' . Config::get('Constant.DEFAULT_TIME_FORMAT') . '', strtotime($value->dtDateTime));
        $endDate = !empty($value->dtEndDateTime) ? date('' . Config::get('Constant.DEFAULT_DATE_FORMAT') . ' ' . Config::get('Constant.DEFAULT_TIME_FORMAT') . '', strtotime($value->dtEndDateTime)) : 'No Expiry';
        $title = $value->varTitle;
        if (Auth::user()->can('gallery-edit')) {
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
        if (Auth::user()->can('gallery-reviewchanges')) {
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
        $returnHtml .= '<table class="new_table_desing table table-striped table-bordered table-hover">
				<thead>
					<tr>
						<th align="center">' . trans('gallery::template.common.title') . '</th>	
						<th align="center">Category</th>	
						<th align="center">Image</th>	
						<th align="center">Start Date</th>	
						<th align="center">End Date</th>	
						<th align="center">' . trans('gallery::template.common.displayorder') . '</th>
						<th align="center">' . trans("gallery::template.common.publish") . '</th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td align="center">' . stripslashes($data->varTitle) . '</td>';
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
        $returnHtml .= '<table class="new_table_desing table table-striped table-bordered table-hover">
				<thead>
					<tr>
						<th align="center">' . trans('gallery::template.common.title') . '</th>	
						<th align="center">Category</th>	
						<th align="center">Image</th>	
						<th align="center">Start Date</th>	
						<th align="center">End Date</th>	
						<th align="center">' . trans('gallery::template.common.displayorder') . '</th>
						<th align="center">' . trans("gallery::template.common.publish") . '</th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td align="center" ' . $titlecolor . '>' . stripslashes($newdata->varTitle) . '</td>';
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
        Cache::tags('Gallery')->flush();
    }

    public function getChildData() {
        $childHtml = "";
        $Cmspage_childData = "";
        $Cmspage_childData = Gallery::getChildGrid();
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
                    $childHtml .= "<td class=\"text-center\"><span class='mob_show_title'>Edit: </span><a class='icon_round' title='" . trans("gallery::template.common.edit") . "' href='" . route('powerpanel.gallery.edit', array('alias' => $child_row->id)) . "'>
							<i class='fa fa-pencil'></i></a></td>";
                } else {
                    $childHtml .= "<td class=\"text-center\"><span class='mob_show_title'>Edit: </span>-</td>";
                }
                if ($child_row->chrApproved == 'N') {
                    $childHtml .= "<td class=\"text-center\"><span class='mob_show_title'>Status: </span><a title='" . trans("gallery::template.common.comments") . "'   href=\"javascript:;\" onclick=\"loadModelpopup('" . $child_row->id . "','" . $child_row->UserID . "','" . Config::get('Constant.MODULE.MODEL_NAME') . "','" . $child_row->fkMainRecord . "')\" class=\"approve_icon_btn\"><i class=\"fa fa-comments\"></i> <span>Comment</span></a>    <a  onclick=\"update_mainrecord('" . $child_row->id . "','" . $child_row->fkMainRecord . "','" . $child_row->UserID . "','A');\" title='" . trans("gallery::template.common.clickapprove") . "'  href=\"javascript:;\" class=\"approve_icon_btn\"><i class=\"fa fa-check-square-o\"></i> <span>Approve</span></a></td>";
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
        $Cmspage_rollbackchildData = Gallery::getChildrollbackGrid();
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
        $approvalData = Gallery::getOrderOfApproval($id);
        $flag = Request::post('flag');
        $message = Gallery::approved_data_Listing($request);
        if (!empty($approvalData)) {
            self::swap_order_edit($approvalData->intDisplayOrder, $main_id);
        }
        $newCmsPageObj = Gallery::getRecordForLogById($main_id);
        $approval_obj = Gallery::getRecordForLogById($approvalid);
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

    public function get_buider_list() {
        $filter = Request::post();
        $rows = '';
        $filterArr = [];
        $records = [];
        $filterArr['orderByFieldName'] = isset($filter['columns']) ? $filter['columns'] : '';
        $filterArr['orderTypeAscOrDesc'] = isset($filter['order']) ? $filter['order'] : '';
        $filterArr['catFilter'] = isset($filter['catValue']) ? $filter['catValue'] : '';
        $filterArr['critaria'] = isset($filter['critaria']) ? $filter['critaria'] : '';
        $filterArr['searchFilter'] = isset($filter['searchValue']) ? trim($filter['searchValue']) : '';
        $filterArr['iDisplayStart'] = isset($filter['start']) ? intval($filter['start']) : 1;
        $filterArr['iDisplayLength'] = isset($filter['length']) ? intval($filter['length']) : 5;
        $filterArr['ignore'] = !empty($filter['ignore']) ? $filter['ignore'] : [];
        $filterArr['selected'] = isset($filter['selected']) && !empty($filter['selected']) ? $filter['selected'] : [];
        $arrResults = Gallery::getBuilderRecordList($filterArr);

        $found = $arrResults->toArray();
        if (!empty($found)) {
            foreach ($arrResults as $key => $value) {
                $rows .= $this->tableDataBuilder($value, false, $filterArr['selected']);
            }
        } else {
            $rows .= '<tr id="not-found"><td colspan="4" align="center">No records found.</td></tr>';
        }
        $iTotalRecords = CommonModel::getTotalRecordCount('Powerpanel\Gallery\Models\Gallery',true, true);
        $records["data"] = $rows;
        $records["found"] = count($found);
        $records["recordsTotal"] = $iTotalRecords;
        return json_encode($records);
    }

    public function tableDataBuilder($value = false, $fcnt = false, $selected = []) {


        $publish_action = '';
        $dtFormat = Config::get('Constant.DEFAULT_DATE_FORMAT') . ' ' . Config::get('Constant.DEFAULT_TIME_FORMAT');
        $startDate = date('' . Config::get('Constant.DEFAULT_DATE_FORMAT') . ' ' . Config::get('Constant.DEFAULT_TIME_FORMAT') . '', strtotime($value->dtDateTime));
        $endDate = !empty($value->dtEndDateTime) ? date('' . Config::get('Constant.DEFAULT_DATE_FORMAT') . ' ' . Config::get('Constant.DEFAULT_TIME_FORMAT') . '', strtotime($value->dtEndDateTime)) : 'No Expiry';
        $record = '<tr ' . (in_array($value->id, $selected) ? 'class="selected-record"' : '') . '>';
        $record .= '<td width="1%" align="center">';
        $record .= '<label class="mt-checkbox mt-checkbox-outline">';
        $record .= '<input type="checkbox" data-title="' . $value->varTitle . '" name="delete[]" class="chkChoose" ' . (in_array($value->id, $selected) ? 'checked' : '') . ' value="' . $value->id . '">';
        $record .= '<span></span>';
        $record .= '</label>';
        $record .= '</td>';
        $record .= '<td width="30%" align="left">';
        $record .= $value->varTitle;
        $record .= '</td>';
        $record .= '<td width="30%" align="center">';
        $record .= '<img src="' . resize_image::resize($value->fkIntImgId, 50, 50) . '">';
        $record .= '</td>';
        //  $record .= '<td width="20%" align="center">';
        // $record .= $startDate;
        // $record .= '</td>';
        // $record .= '<td width="20%" align="center">';
        // $record .= $endDate;
        // $record .= '</td>';
        $record .= '<td width="30%" align="center">';
        $record .= date($dtFormat, strtotime($value->updated_at));
        $record .= '</td>';
        $record .= '</tr>';
        return $record;
    }

}
