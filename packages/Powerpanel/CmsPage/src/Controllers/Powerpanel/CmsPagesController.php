<?php

namespace Powerpanel\CmsPage\Controllers\Powerpanel;

use App\Alias;
use Powerpanel\CmsPage\Models\CmsPage;
use Powerpanel\Workflow\Models\Comments;
use App\CommonModel;
use App\Helpers\MyLibrary;
use App\Http\Controllers\PowerpanelController;
use App\Helpers\FrontPageContent_Shield;
use App\Helpers\PageHitsReport;
use App\Log;
use Powerpanel\Workflow\Models\WorkflowLog;
use Powerpanel\Workflow\Models\Workflow;
use App\Modules;
use Config;
use App\Pagehit;
use App\RecentUpdates;
use Auth;
use File;
use Carbon\Carbon;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Redirect;
use Validator;
use Cache;
use DB;
use Powerpanel\RoleManager\Models\Role_user;
use App\Helpers\AddImageModelRel;
use App\Http\Traits\slug;
use App\UserNotification;
use Powerpanel\Menu\Models\Menu;
use App\User;

class CmsPagesController extends PowerpanelController {

    /**
     * Create a new controller instance.
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
     * This method handles list view.
     *
     * @return View
     *
     * @since   2017-07-24
     *
     * @author  NetQuick
     */
    public function index() {
        $userIsAdmin = false;
        if (isset($this->currentUserRoleData) && !empty($this->currentUserRoleData)) {
            if ($this->currentUserRoleData->chrIsAdmin == 'Y') {
                $userIsAdmin = true;
            }
        } else {
            $userIsAdmin = true;
        }
        $iTotalRecords = CmsPage::getRecordCount();
        $draftTotalRecords = CmsPage::getRecordCountforListDarft(false, true, $userIsAdmin, array());
        $trashTotalRecords = CmsPage::getRecordCountforListTrash();
        $favoriteTotalRecords = CmsPage::getRecordCountforListFavorite();
        $archiveTotalRecords = CmsPage::getRecordCountforListArchive();
        $NewRecordsCount = CmsPage::getNewRecordsCount();
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
        $this->breadcrumb['title'] = trans('cmspage::template.pageModule.manage');
        return view('cmspage::powerpanel.list', ['iTotalRecords' => $iTotalRecords, 'breadcrumb' => $this->breadcrumb, 'NewRecordsCount' => $NewRecordsCount, 'userIsAdmin' => $userIsAdmin, 'draftTotalRecords' => $draftTotalRecords, 'trashTotalRecords' => $trashTotalRecords, 'favoriteTotalRecords' => $favoriteTotalRecords, 'archiveTotalRecords' => $archiveTotalRecords,'settingarray'=>$settingarray]);
    }

    /**
     * This method fetch list of pages.
     *
     * @return json
     *
     * @since   2017-07-24
     *
     * @author  NetQuick
     */
    public function get_list_New() {
        $filterArr = [];
        $records = [];
        $records['data'] = [];
        $filterArr['orderColumnNo'] = (!empty(Request::input('order')[0]['column']) ? Request::input('order')[0]['column'] : '');
        $filterArr['orderByFieldName'] = (!empty(Request::input('columns')[$filterArr['orderColumnNo']]['name']) ? Request::input('columns')[$filterArr['orderColumnNo']]['name'] : '');
        $filterArr['orderTypeAscOrDesc'] = (!empty(Request::input('order')[0]['dir']) ? Request::input('order')[0]['dir'] : '');
        $filterArr['statusFilter'] = !empty(Request::input('customActionName')) ? Request::input('customActionName') : '';
        $filterArr['searchFilter'] = !empty(Request::input('searchValue')) ? Request::input('searchValue') : '';
        $filterArr['customFilterIdentity'] = !empty(Request::get('customFilterIdentity')) ? Request::get('customFilterIdentity') : '';
        $filterArr['iDisplayLength'] = intval(Request::input('length'));
        $filterArr['iDisplayStart'] = intval(Request::input('start'));
        $iDisplayLength = intval(Request::input('length'));
        $iDisplayStart = intval(Request::input('start'));
        $sEcho = intval(Request::input('draw'));
        $arrResults = CmsPage::getRecordList_tab1($filterArr);
        $iTotalRecords = CmsPage::getRecordCountListApprovalTab($filterArr, true);
        $end = $iDisplayStart + $iDisplayLength;
        $end = $end > $iTotalRecords ? $iTotalRecords : $end;
        if (!empty($arrResults)) {
            foreach ($arrResults as $key => $value) {
                $records['data'][] = $this->tableData_tab1($value);
            }
        }
        if (!empty(Request::input('customActionType')) && Request::input('customActionType') == 'group_action') {
            $records['customActionStatus'] = 'OK';
        }
        $NewRecordsCount = Cmspage::getNewRecordsCount();
        $records['newRecordCount'] = $NewRecordsCount;
        $records['draw'] = $sEcho;
        $records['recordsTotal'] = $iTotalRecords;
        $records['recordsFiltered'] = $iTotalRecords;
        echo json_encode($records);
        exit;
    }

    public function get_list() {
        $filterArr = [];
        $records = [];
        $records['data'] = [];
        $filterArr['orderColumnNo'] = (!empty(Request::input('order')[0]['column']) ? Request::input('order')[0]['column'] : '');
        $filterArr['orderByFieldName'] = (!empty(Request::input('columns')[$filterArr['orderColumnNo']]['name']) ? Request::input('columns')[$filterArr['orderColumnNo']]['name'] : '');
        $filterArr['orderTypeAscOrDesc'] = (!empty(Request::input('order')[0]['dir']) ? Request::input('order')[0]['dir'] : '');
        $filterArr['statusFilter'] = !empty(Request::input('customActionName')) ? Request::input('customActionName') : '';
        $filterArr['searchFilter'] = !empty(Request::input('searchValue')) ? Request::input('searchValue') : '';
        $filterArr['customFilterIdentity'] = !empty(Request::get('customFilterIdentity')) ? Request::get('customFilterIdentity') : '';
        $filterArr['iDisplayLength'] = intval(Request::input('length'));
        $filterArr['iDisplayStart'] = intval(Request::input('start'));
        $iDisplayLength = intval(Request::input('length'));
        $iDisplayStart = intval(Request::input('start'));
        $sEcho = intval(Request::input('draw'));
        $isAdmin = false;
        if (isset($this->currentUserRoleData) && !empty($this->currentUserRoleData)) {
            if ($this->currentUserRoleData->chrIsAdmin == 'Y') {
                $isAdmin = true;
            }
        }
        $igonresModulesforShare = Modules::getModuleDataByNames(['']);
        $igonresModulesIds = array();
        if (!empty($igonresModulesforShare)) {
            foreach ($igonresModulesforShare as $ignoreModule) {
                $igonresModulesIds[] = $ignoreModule->id;
            }
        }
        $ignoreId = [38];
        $arrResults = CmsPage::getRecordList($filterArr, $isAdmin, $ignoreId);
        $iTotalRecords = CmsPage::getRecordCountforList($filterArr, true, $isAdmin, $ignoreId);
        $end = $iDisplayStart + $iDisplayLength;
        $end = $end > $iTotalRecords ? $iTotalRecords : $end;
        if (!empty($arrResults)) {
            foreach ($arrResults as $key => $value) {
                if (!in_array($value->id, $ignoreId)) {
                    $records['data'][] = $this->tableData($value, $igonresModulesIds);
                }
            }
        }
        if (!empty(Request::input('customActionType')) && Request::input('customActionType') == 'group_action') {
            $records['customActionStatus'] = 'OK';
        }
        $NewRecordsCount = Cmspage::getNewRecordsCount();
        $records['newRecordCount'] = $NewRecordsCount;
        $records['draw'] = $sEcho;
        $records['recordsTotal'] = $iTotalRecords;
        $records['recordsFiltered'] = $iTotalRecords;
        echo json_encode($records);
        exit;
    }

    //Start Draft LIST of Records
    public function get_list_draft() {
        $filterArr = [];
        $records = [];
        $records['data'] = [];
        $filterArr['orderColumnNo'] = (!empty(Request::input('order')[0]['column']) ? Request::input('order')[0]['column'] : '');
        $filterArr['orderByFieldName'] = (!empty(Request::input('columns')[$filterArr['orderColumnNo']]['name']) ? Request::input('columns')[$filterArr['orderColumnNo']]['name'] : '');
        $filterArr['orderTypeAscOrDesc'] = (!empty(Request::input('order')[0]['dir']) ? Request::input('order')[0]['dir'] : '');
        $filterArr['statusFilter'] = !empty(Request::input('customActionName')) ? Request::input('customActionName') : '';
        $filterArr['searchFilter'] = !empty(Request::input('searchValue')) ? Request::input('searchValue') : '';
        $filterArr['customFilterIdentity'] = !empty(Request::get('customFilterIdentity')) ? Request::get('customFilterIdentity') : '';
        $filterArr['iDisplayLength'] = intval(Request::input('length'));
        $filterArr['iDisplayStart'] = intval(Request::input('start'));
        $iDisplayLength = intval(Request::input('length'));
        $iDisplayStart = intval(Request::input('start'));
        $sEcho = intval(Request::input('draw'));
        $isAdmin = false;
        if ($this->currentUserRoleData->chrIsAdmin == 'Y') {
            $isAdmin = true;
        }
        $igonresModulesforShare = Modules::getModuleDataByNames(['publications-category']);
        $igonresModulesIds = array();
        if (!empty($igonresModulesforShare)) {
            foreach ($igonresModulesforShare as $ignoreModule) {
                $igonresModulesIds[] = $ignoreModule->id;
            }
        }
        $ignoreId = [];
        $arrResults = CmsPage::getRecordListDraft($filterArr, $isAdmin, $ignoreId);
        $iTotalRecords = CmsPage::getRecordCountforListDarft($filterArr, true, $isAdmin, $ignoreId);
        $end = $iDisplayStart + $iDisplayLength;
        $end = $end > $iTotalRecords ? $iTotalRecords : $end;
        if (!empty($arrResults)) {
            foreach ($arrResults as $key => $value) {
                if (!in_array($value->id, $ignoreId)) {
                    $records['data'][] = $this->tableDataDraft($value, $igonresModulesIds);
                }
            }
        }
        if (!empty(Request::input('customActionType')) && Request::input('customActionType') == 'group_action') {
            $records['customActionStatus'] = 'OK';
        }
        $NewRecordsCount = Cmspage::getNewRecordsCount();
        $records['newRecordCount'] = $NewRecordsCount;
        $records['draw'] = $sEcho;
        $records['recordsTotal'] = $iTotalRecords;
        $records['recordsFiltered'] = $iTotalRecords;
        echo json_encode($records);
        exit;
    }

    public function get_list_trash() {
        $filterArr = [];
        $records = [];
        $records['data'] = [];
        $filterArr['orderColumnNo'] = (!empty(Request::input('order')[0]['column']) ? Request::input('order')[0]['column'] : '');
        $filterArr['orderByFieldName'] = (!empty(Request::input('columns')[$filterArr['orderColumnNo']]['name']) ? Request::input('columns')[$filterArr['orderColumnNo']]['name'] : '');
        $filterArr['orderTypeAscOrDesc'] = (!empty(Request::input('order')[0]['dir']) ? Request::input('order')[0]['dir'] : '');
        $filterArr['statusFilter'] = !empty(Request::input('customActionName')) ? Request::input('customActionName') : '';
        $filterArr['searchFilter'] = !empty(Request::input('searchValue')) ? Request::input('searchValue') : '';
        $filterArr['customFilterIdentity'] = !empty(Request::get('customFilterIdentity')) ? Request::get('customFilterIdentity') : '';
        $filterArr['iDisplayLength'] = intval(Request::input('length'));
        $filterArr['iDisplayStart'] = intval(Request::input('start'));
        $iDisplayLength = intval(Request::input('length'));
        $iDisplayStart = intval(Request::input('start'));
        $sEcho = intval(Request::input('draw'));
        $isAdmin = false;
        if ($this->currentUserRoleData->chrIsAdmin == 'Y') {
            $isAdmin = true;
        }
        $igonresModulesforShare = Modules::getModuleDataByNames(['publications-category']);
        $igonresModulesIds = array();
        if (!empty($igonresModulesforShare)) {
            foreach ($igonresModulesforShare as $ignoreModule) {
                $igonresModulesIds[] = $ignoreModule->id;
            }
        }
        $ignoreId = [];
        $arrResults = CmsPage::getRecordListTrash($filterArr, $isAdmin, $ignoreId);
        $iTotalRecords = CmsPage::getRecordCountforListTrash($filterArr, true, $isAdmin, $ignoreId);
        $end = $iDisplayStart + $iDisplayLength;
        $end = $end > $iTotalRecords ? $iTotalRecords : $end;
        if (!empty($arrResults)) {
            foreach ($arrResults as $key => $value) {
                if (!in_array($value->id, $ignoreId)) {
                    $records['data'][] = $this->tableDataTrash($value, $igonresModulesIds);
                }
            }
        }
        if (!empty(Request::input('customActionType')) && Request::input('customActionType') == 'group_action') {
            $records['customActionStatus'] = 'OK';
        }
        $NewRecordsCount = Cmspage::getNewRecordsCount();
        $records['newRecordCount'] = $NewRecordsCount;
        $records['draw'] = $sEcho;
        $records['recordsTotal'] = $iTotalRecords;
        $records['recordsFiltered'] = $iTotalRecords;
        echo json_encode($records);
        exit;
    }

    public function get_list_favorite() {
        $filterArr = [];
        $records = [];
        $records['data'] = [];
        $filterArr['orderColumnNo'] = (!empty(Request::input('order')[0]['column']) ? Request::input('order')[0]['column'] : '');
        $filterArr['orderByFieldName'] = (!empty(Request::input('columns')[$filterArr['orderColumnNo']]['name']) ? Request::input('columns')[$filterArr['orderColumnNo']]['name'] : '');
        $filterArr['orderTypeAscOrDesc'] = (!empty(Request::input('order')[0]['dir']) ? Request::input('order')[0]['dir'] : '');
        $filterArr['statusFilter'] = !empty(Request::input('customActionName')) ? Request::input('customActionName') : '';
        $filterArr['searchFilter'] = !empty(Request::input('searchValue')) ? Request::input('searchValue') : '';
        $filterArr['customFilterIdentity'] = !empty(Request::get('customFilterIdentity')) ? Request::get('customFilterIdentity') : '';
        $filterArr['iDisplayLength'] = intval(Request::input('length'));
        $filterArr['iDisplayStart'] = intval(Request::input('start'));
        $iDisplayLength = intval(Request::input('length'));
        $iDisplayStart = intval(Request::input('start'));
        $sEcho = intval(Request::input('draw'));
        $isAdmin = false;
        if ($this->currentUserRoleData->chrIsAdmin == 'Y') {
            $isAdmin = true;
        }
        $igonresModulesforShare = Modules::getModuleDataByNames(['publications-category']);
        $igonresModulesIds = array();
        if (!empty($igonresModulesforShare)) {
            foreach ($igonresModulesforShare as $ignoreModule) {
                $igonresModulesIds[] = $ignoreModule->id;
            }
        }
        $ignoreId = [];
        $arrResults = CmsPage::getRecordListFavorite($filterArr, $isAdmin, $ignoreId);
        $iTotalRecords = CmsPage::getRecordCountforListFavorite($filterArr, true, $isAdmin, $ignoreId);
        $end = $iDisplayStart + $iDisplayLength;
        $end = $end > $iTotalRecords ? $iTotalRecords : $end;
        if (!empty($arrResults)) {
            foreach ($arrResults as $key => $value) {
                if (!in_array($value->id, $ignoreId)) {
                    $records['data'][] = $this->tableDataFavorite($value, $igonresModulesIds);
                }
            }
        }
        if (!empty(Request::input('customActionType')) && Request::input('customActionType') == 'group_action') {
            $records['customActionStatus'] = 'OK';
        }
        $NewRecordsCount = Cmspage::getNewRecordsCount();
        $records['newRecordCount'] = $NewRecordsCount;
        $records['draw'] = $sEcho;
        $records['recordsTotal'] = $iTotalRecords;
        $records['recordsFiltered'] = $iTotalRecords;
        echo json_encode($records);
        exit;
    }

    public function get_list_archive() {
        $filterArr = [];
        $records = [];
        $records['data'] = [];
        $filterArr['orderColumnNo'] = (!empty(Request::input('order')[0]['column']) ? Request::input('order')[0]['column'] : '');
        $filterArr['orderByFieldName'] = (!empty(Request::input('columns')[$filterArr['orderColumnNo']]['name']) ? Request::input('columns')[$filterArr['orderColumnNo']]['name'] : '');
        $filterArr['orderTypeAscOrDesc'] = (!empty(Request::input('order')[0]['dir']) ? Request::input('order')[0]['dir'] : '');
        $filterArr['statusFilter'] = !empty(Request::input('customActionName')) ? Request::input('customActionName') : '';
        $filterArr['searchFilter'] = !empty(Request::input('searchValue')) ? Request::input('searchValue') : '';
        $filterArr['customFilterIdentity'] = !empty(Request::get('customFilterIdentity')) ? Request::get('customFilterIdentity') : '';
        $filterArr['iDisplayLength'] = intval(Request::input('length'));
        $filterArr['iDisplayStart'] = intval(Request::input('start'));
        $iDisplayLength = intval(Request::input('length'));
        $iDisplayStart = intval(Request::input('start'));
        $sEcho = intval(Request::input('draw'));
        $isAdmin = false;
        if ($this->currentUserRoleData->chrIsAdmin == 'Y') {
            $isAdmin = true;
        }
        $igonresModulesforShare = Modules::getModuleDataByNames(['publications-category']);
        $igonresModulesIds = array();
        if (!empty($igonresModulesforShare)) {
            foreach ($igonresModulesforShare as $ignoreModule) {
                $igonresModulesIds[] = $ignoreModule->id;
            }
        }
        $ignoreId = [];
        $arrResults = CmsPage::getRecordListArchive($filterArr, $isAdmin, $ignoreId);
        $iTotalRecords = CmsPage::getRecordCountforListArchive($filterArr, true, $isAdmin, $ignoreId);
        $end = $iDisplayStart + $iDisplayLength;
        $end = $end > $iTotalRecords ? $iTotalRecords : $end;
        if (!empty($arrResults)) {
            foreach ($arrResults as $key => $value) {
                if (!in_array($value->id, $ignoreId)) {
                    $records['data'][] = $this->tableDataArchive($value, $igonresModulesIds);
                }
            }
        }
        if (!empty(Request::input('customActionType')) && Request::input('customActionType') == 'group_action') {
            $records['customActionStatus'] = 'OK';
        }
        $NewRecordsCount = Cmspage::getNewRecordsCount();
        $records['newRecordCount'] = $NewRecordsCount;
        $records['draw'] = $sEcho;
        $records['recordsTotal'] = $iTotalRecords;
        $records['recordsFiltered'] = $iTotalRecords;
        echo json_encode($records);
        exit;
    }

//END Draft LIST of Records
    public function edit($id = false) {
        $imageManager = true;
        $userIsAdmin = false;
        $userIsClient = false;
        if (isset($this->currentUserRoleData) && !empty($this->currentUserRoleData)) {
            if ($this->currentUserRoleData->chrIsAdmin == 'Y') {
                $userIsAdmin = true;
            }
        } else {
            $userIsAdmin = true;
        }
        if (isset($this->currentUserRoleData) && !empty($this->currentUserRoleData)) {
            if ($this->currentUserRoleData->name == 'client_roles') {
                $userIsClient = true;
            }
        } else {
            $userIsClient = true;
        }
        $ignoreModulesNames = ['workflow', 'submit-tickets', 'feedback-leads','blog-category','faq-category','service-category','faq','team','video-gallery','work','boat-category','boat-inquiry','service-inquiry','brand'];
        $ignoreModules = Modules::getModuleIdsByNames($ignoreModulesNames);
        $ignoreModulesIds = array();
        if (!empty($ignoreModules)) {
            $ignoreModulesIds = array_column($ignoreModules, 'id');
        }
        $modules = Modules::getFrontModulesList($ignoreModulesIds);
        $menus = DB::table('menu')->where('intPageId',$id)->where('chrDelete','N')->where('chrPublish', 'Y')->count();
        $templateData = array();
        if (is_numeric($id) && !empty($id)) {
            $Cmspage = CmsPage::getRecordById($id);
            if (empty($Cmspage)) {
                return redirect()->route('powerpanel.pages.add');
            }
            if ($Cmspage->fkMainRecord != '0') {
                $Cmspage_highLight = CmsPage::getRecordById($Cmspage->fkMainRecord);
                $templateData['Cmspage_highLight'] = $Cmspage_highLight;
                $metaInfo_highLight['varMetaTitle'] = $Cmspage_highLight['varMetaTitle'];
                $metaInfo_highLight['varMetaDescription'] = $Cmspage_highLight['varMetaDescription'];
            } else {
                $templateData['Cmspage_highLight'] = '';
                $metaInfo_highLight['varMetaTitle'] = '';
                $metaInfo_highLight['varMetaDescription'] = '';
            }
            $templateData['Cmspage'] = $Cmspage;
            $metaInfo['varURL'] = $Cmspage['alias']['varAlias'];
            $metaInfo['varMetaTitle'] = $Cmspage['varMetaTitle'];
            $metaInfo['varMetaDescription'] = $Cmspage['varMetaDescription'];
            $this->breadcrumb['title'] = trans('cmspage::template.common.edit') . ' - ' . $Cmspage->varTitle;
            $this->breadcrumb['inner_title'] = trans('cmspage::template.common.edit') . ' - ' . $Cmspage->varTitle;
            if ($Cmspage->alias->varAlias != 'home') {
                $templateData['publishActionDisplay'] = true;
            }
        } else {
            $this->breadcrumb['title'] = trans('cmspage::template.pageModule.add');
            $this->breadcrumb['inner_title'] = trans('cmspage::template.pageModule.add');
            $templateData['publishActionDisplay'] = true;
        }
        $templateData['userIsAdmin'] = $userIsAdmin;
        $templateData['userIsClient'] = $userIsClient;
        $this->breadcrumb['module'] = trans('cmspage::template.pageModule.manage');
        $this->breadcrumb['url'] = 'powerpanel/pages';
        $templateData['modules'] = $modules;
        $templateData['breadcrumb'] = $this->breadcrumb;
        $templateData['metaInfo'] = (!empty($metaInfo) ? $metaInfo : '');
        $templateData['metaInfo_highLight'] = (!empty($metaInfo_highLight) ? $metaInfo_highLight : '');
        $templateData['imageManager'] = $imageManager;
        $templateData['menus'] = $menus;

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
        $templateData['MyLibrary'] = $this->MyLibrary;
        return view('cmspage::powerpanel.actions', $templateData);
    }

    public function handlePost(Request $request, Guard $auth) {
        $approval = false;
        $userIsAdmin = false;
        if (isset($this->currentUserRoleData) && !empty($this->currentUserRoleData)) {
            if ($this->currentUserRoleData->chrIsAdmin == 'Y') {
                $userIsAdmin = true;
            }
        }
        $data = Request::input();
        $actionMessage = trans('cmspage::template.common.oppsSomethingWrong');
        $rules = array(
            'title' => 'required|max:160|handle_xss|no_url',
            'module' => 'required',
            'varMetaTitle' => 'required|max:500|handle_xss|no_url',
            'varMetaDescription' => 'required|max:500|handle_xss|no_url',
            'chrMenuDisplay' => 'required',
            'alias' => 'required',
        );
        $messsages = array(
            'varMetaTitle.required' => trans('cmspage::template.pageModule.metaTitle'),
            'title.required' => trans('cmspage::template.pageModule.title'),
            'varMetaDescription.required' => trans('cmspage::template.pageModule.metaDescription'),
        );
        $validator = Validator::make($data, $rules, $messsages);
        if ($validator->passes()) {
            $module = Modules::getModuleById(Config::get('Constant.MODULE.ID'));
            $moduleCode = $data['module'];
            $cmsPageArr = [];
            if (Config::get('Constant.DEFAULT_VISUAL') == 'Y') {
                if ($data['section'] != '[]') {
                    $vsection = $data['section'];
                } else {
                    $vsection = '';
                }
            } else {
                $vsection = $data['contents'];
            }
            $cmsPageArr['varTitle'] = stripslashes(trim($data['title']));
            $cmsPageArr['intFKModuleCode'] = $moduleCode;
            $cmsPageArr['txtDescription'] = $vsection;
            $cmsPageArr['varMetaTitle'] = stripslashes(trim($data['varMetaTitle']));
            $cmsPageArr['varMetaDescription'] = stripslashes(trim($data['varMetaDescription']));
            if(Config::get('Constant.CHRContentScheduling') == 'Y'){
            $cmsPageArr['dtDateTime'] = date('Y-m-d H:i:s', strtotime($data['start_date_time']));
            $cmsPageArr['dtEndDateTime'] = (isset($data['end_date_time']) && $data['end_date_time'] != "") ? date('Y-m-d H:i:s', strtotime($data['end_date_time'])) : null;
            }
            $cmsPageArr['UserID'] = auth()->user()->id;
            if ($data['chrMenuDisplay'] == 'D') {
                $cmsPageArr['chrDraft'] = 'D';
                $cmsPageArr['chrPublish'] = 'N';
            } else {
                $cmsPageArr['chrDraft'] = 'N';
                $cmsPageArr['chrPublish'] = $data['chrMenuDisplay'];
            }
            if(Config::get('Constant.CHRSearchRank') == 'Y'){
            $cmsPageArr['intSearchRank'] = $data['search_rank'];
            }
            if (isset($data['chrPageActive']) && $data['chrPageActive'] != '') {
                $cmsPageArr['chrPageActive'] = $data['chrPageActive'];
            }
            if (isset($data['chrPageActive']) && $data['chrPageActive'] == 'PP') {
                $cmsPageArr['varPassword'] = $data['new_password'];
            } else {
                $cmsPageArr['varPassword'] = '';
            }
            if ($data['chrMenuDisplay'] == 'D') {
                $addlog = Config::get('Constant.UPDATE_DRAFT');
            } else {
                $addlog = '';
            }
            $id = Request::segment(3);
            if (is_numeric($id) && !empty($id)) {
                //Edit post Handler=======
                $cmsPage = CmsPage::getRecordForLogById($id);
                if ($cmsPage->chrLock == 'Y' && auth()->user()->id != $cmsPage->LockUserID) {
                    if ($this->currentUserRoleData->chrIsAdmin != 'Y') {
                        $lockedUserData = User::getRecordById($cmsPage->LockUserID, true);
                        $lockedUserName = 'someone';
                        if (!empty($lockedUserData)) {
                            $lockedUserName = $lockedUserData->name;
                        }
                        $actionMessage = "This record has been locked by " . $lockedUserName . ".";
                        return redirect()->route('powerpanel.pages.index')->with('message', $actionMessage);
                    }
                }
                if (File::exists(app_path() . '/Workflow.php') != null || File::exists(base_path() . '/packages/Powerpanel/Workflow/src/Models/Workflow.php') != null) {
                    if (!$userIsAdmin) {
                        $userRole = $this->currentUserRoleData->id;
                    } else {
                        $userRoleData = Role_user::getUserRoleByUserId($cmsPage->UserID);
                        if (isset($userRoleData->role_id)) {
                            $userRole = $userRoleData->role_id;
                        } else {
                            $userRole = $this->currentUserRoleData->id;
                        }
                    }
                    $workFlowByCat = Workflow::getRecordByCategoryId($module->intFkGroupCode, $userRole, Config::get('Constant.MODULE.ID'));
                    if ($data['oldAlias'] != $data['alias']) {
                        Alias::updateAlias($data['oldAlias'], $data['alias']);
                        if ($moduleCode != 4) {
                            Mylibrary::updateAliasForPageInMenu($data['alias'], 'before', $moduleCode);
                        }
                    }
                    if ($data['chrMenuDisplay'] == 'D') {
                        Menu::deletePermenentMenuRecord($id, Config::get('Constant.MODULE.ID'));
                        //DB::table('menu')->where('intPageId', $id)->where('intfkModuleId', Config::get('Constant.MODULE.ID'))->delete();
                    }
                    $whereConditions = ['id' => $cmsPage->id];
                    if (empty($workFlowByCat->varUserId) || $userIsAdmin || $workFlowByCat->charNeedApproval == 'N') {
                        if ($cmsPage->fkMainRecord == '0' || empty($workFlowByCat->varUserId)) {
                            $update = CommonModel::updateRecords($whereConditions, $cmsPageArr,false, 'Powerpanel\CmsPage\Models\CmsPage');
                            if ($update) {
                                $newCmsPageObj = CmsPage::getRecordForLogById($cmsPage->id);
                                //Update record in menu
                                $whereConditions = ['txtPageUrl' => $data['oldAlias']];
                                $updateMenuFields = [
                                    'varTitle' => $newCmsPageObj->varTitle,
                                    'txtPageUrl' => $newCmsPageObj->alias->varAlias,
                                    'chrPublish' => $data['chrMenuDisplay'],
                                    'chrActive' => $data['chrMenuDisplay'],
                                ];
                                //Update record in menu
                                $logArr = MyLibrary::logData($cmsPage->id, false, $addlog);
                                if (Auth::user()->can('log-advanced')) {
                                    $oldRec = $this->recordHistory($cmsPage);
                                    $newRec = $this->newrecordHistory($cmsPage, $newCmsPageObj);
                                    $logArr['old_val'] = $oldRec;
                                    $logArr['new_val'] = $newRec;
                                }
                                $logArr['varTitle'] = $newCmsPageObj->varTitle;
                                Log::recordLog($logArr);
                                if (Auth::user()->can('recent-updates-list')) {
                                    $notificationArr = MyLibrary::notificationData($cmsPage->id, $newCmsPageObj);
                                    RecentUpdates::setNotification($notificationArr);
                                }
                                self::flushCache();
                                if (isset($data['saveandexit']) && $data['saveandexit'] == 'approvesaveandexit') {
                                    $actionMessage = trans('cmspage::template.pageModule.pageApprovalUpdate');
                                } else {
                                    $actionMessage = trans('cmspage::template.pageModule.pageUpdate');
                                }
                            }
                        } else {
                            $newCmsPageObj = CmsPage::getRecordForLogById($cmsPage->id);
                            //Update record in menu
                            $whereConditions = ['txtPageUrl' => $data['oldAlias']];
                            $updateMenuFields = [
                                'varTitle' => $newCmsPageObj->varTitle,
                                'txtPageUrl' => $newCmsPageObj->alias->varAlias,
                                'chrPublish' => $data['chrMenuDisplay'],
                                'chrActive' => $data['chrMenuDisplay'],
                            ];
                            //Update record in menu
                            $updateModuleFields = $cmsPageArr;
                            $this->insertApprovedRecord($updateModuleFields, $data, $id);
                            if (isset($data['saveandexit']) && $data['saveandexit'] == 'approvesaveandexit') {
                                $actionMessage = trans('cmspage::template.pageModule.pageApprovalUpdate');
                            } else {
                                $actionMessage = trans('cmspage::template.pageModule.pageUpdate');
                            }
                            $approval = $id;
                        }
                    } else {
                        if ($workFlowByCat->charNeedApproval == 'Y') {
                            $postArr = $data;
                            $approvalObj = $this->insertApprovalRecord($cmsPage, $postArr, $cmsPageArr);
                            if (isset($data['saveandexit']) && $data['saveandexit'] == 'approvesaveandexit') {
                                $actionMessage = trans('cmspage::template.pageModule.pageApprovalUpdate');
                            } else {
                                $actionMessage = trans('cmspage::template.pageModule.pageUpdate');
                            }
                            $approval = $approvalObj->id;
                        }
                    }
                } else {
                     $whereConditions = ['id' => $cmsPage->id];
                    $update = CommonModel::updateRecords($whereConditions, $cmsPageArr, false, 'Powerpanel\CmsPage\Models\CmsPage');
                    $actionMessage = trans('cmspage::template.pageModule.pageUpdate');
                }
            } else {
                $postArr = $data;
                if (File::exists(app_path() . '/Workflow.php') != null || File::exists(base_path() . '/packages/Powerpanel/Workflow/src/Models/Workflow.php') != null) {
                    $workFlowByCat = Workflow::getRecordByCategoryId($module->intFkGroupCode, $this->currentUserRoleData->id, Config::get('Constant.MODULE.ID'));
                }
                if (!empty($workFlowByCat->varUserId) && $workFlowByCat->chrNeedAddPermission == 'Y' && !$userIsAdmin) {
                    if ($data['chrPageActive'] == 'PR') {
                        $cmsPageArr['chrPublish'] = 'Y';
                    } else {
                        $cmsPageArr['chrPublish'] = 'N';
                    }
                    $cmsPageArr['chrDraft'] = 'N';
                    $cmsPage = $this->insertNewRecord($postArr, $cmsPageArr);
                    if ($data['chrMenuDisplay'] == 'D') {
                        $cmsPageArr['chrDraft'] = 'D';
                    }
                    $cmsPageArr['chrPublish'] = 'Y';
                    $approvalObj = $this->insertApprovalRecord($cmsPage, $postArr, $cmsPageArr);
                    $approval = $cmsPage->id;
                } else {
                    $cmsPage = $this->insertNewRecord($postArr, $cmsPageArr);
                    $approval = $cmsPage->id;
                }
                if (isset($data['saveandexit']) && $data['saveandexit'] == 'approvesaveandexit') {
                    $actionMessage = trans('cmspage::template.pageModule.addapprovalMessage');
                } else {
                    $actionMessage = trans('cmspage::template.pageModule.addMessage');
                }
                $id = $cmsPage->id;
            }
            if (method_exists($this->Alias, 'updatePreviewAlias')) {
                Alias::updatePreviewAlias($data['alias'], 'N');
            }
            if ((!empty(Request::get('saveandexit')) && Request::get('saveandexit') == 'saveandexit') || !$userIsAdmin) {
                if ($data['chrMenuDisplay'] == 'D') {
                    return redirect()->route('powerpanel.pages.index', 'tab=D')->with('message', $actionMessage);
                } else {
                    return redirect()->route('powerpanel.pages.index')->with('message', $actionMessage);
                }
            }elseif (!empty(Request::get('saveandmenu')) && Request::get('saveandmenu') == 'saveandmenu') {
                return redirect('powerpanel/menu?pageId=' . $id);
            } else {
                return redirect()->route('powerpanel.pages.edit', $id)->with('message', $actionMessage);
            }
        } else {
            return Redirect::back()->withErrors($validator)->withInput();
        }
    }

    public function insertApprovedRecord($updateModuleFields, $postArr, $id) {
        $whereConditions = ['id' => $postArr['fkMainRecord']];
        $updateModuleFields['chrAddStar'] = 'N';
        $update = CommonModel::updateRecords($whereConditions, $updateModuleFields,false, 'Powerpanel\CmsPage\Models\CmsPage');
        $whereConditions_ApproveN = ['fkMainRecord' => $postArr['fkMainRecord']];
        $updateToApproveN = [
            'chrApproved' => 'N',
            'chrLetest' => 'N',
            'intApprovedBy' => '0',
        ];
        CommonModel::updateRecords($whereConditions_ApproveN, $updateToApproveN,false, 'Powerpanel\CmsPage\Models\CmsPage');
        $whereConditionsApprove = ['id' => $id, 'chrMain' => 'N'];
        $updateToApprove = [
            'chrApproved' => 'Y',
            'chrRollBack' => 'Y',
            'intApprovedBy' => auth()->user()->id,
        ];
        CommonModel::updateRecords($whereConditionsApprove, $updateToApprove,false, 'Powerpanel\CmsPage\Models\CmsPage');
        if ($postArr['chrMenuDisplay'] == 'D') {
            $addlog = Config::get('Constant.DRAFT_RECORD_APPROVED');
        } else {
            $addlog = Config::get('Constant.RECORD_APPROVED');
        }
        $newCmsPageObj = CmsPage::getRecordForLogById($id);
        $logArr = MyLibrary::logData($id, false, $addlog);
        $logArr['varTitle'] = stripslashes($newCmsPageObj->varTitle);
        Log::recordLog($logArr);
        if (method_exists($this->MyLibrary, 'userNotificationData')) {
            /* notification for user to record approved */
            $userNotificationArr = MyLibrary::userNotificationData(Config::get('Constant.MODULE.ID'));
            $userNotificationArr['fkRecordId'] = $id;
            $userNotificationArr['txtNotification'] = 'Your request has been approved by ' . ucfirst(auth()->user()->name) . ' (' . ucfirst(Config::get('Constant.MODULE.NAME')) . ')';
            $userNotificationArr['fkIntUserId'] = Auth::user()->id;
            $userNotificationArr['chrNotificationType'] = 'A';
            $userNotificationArr['intOnlyForUserId'] = $newCmsPageObj->UserID;
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
                self::flushCache();
                $actionMessage = trans('cmspage::template.pageModule.pageUpdate');
            }
        }
    }

// Start Insert Approval Records
    public function insertApprovalRecord($moduleObj, $postArr, $cmsPageArr) {
        $response = false;
        $cmsPageArr['intAliasId'] = MyLibrary::insertAlias($postArr['alias'], false, 'N');
        $cmsPageArr['chrMain'] = 'N';
        $cmsPageArr['chrLetest'] = 'Y';
        if ($postArr['chrMenuDisplay'] == 'D') {
            $cmsPageArr['chrDraft'] = 'D';
            $cmsPageArr['chrPublish'] = 'N';
        } else {
            $cmsPageArr['chrDraft'] = 'N';
            $cmsPageArr['chrPublish'] = $postArr['chrMenuDisplay'];
        }
        $cmsPageArr['fkMainRecord'] = $moduleObj->id;
        if(Config::get('Constant.CHRSearchRank') == 'Y'){
        $cmsPageArr['intSearchRank'] = $postArr['search_rank'];
        }
        if (isset($postArr['chrPageActive']) && $postArr['chrPageActive'] != '') {
            $cmsPageArr['chrPageActive'] = $postArr['chrPageActive'];
        }
        if (isset($postArr['chrPageActive']) && $postArr['chrPageActive'] == 'PP') {
            $cmsPageArr['varPassword'] = $postArr['new_password'];
        } else {
            $cmsPageArr['varPassword'] = '';
        }
        if ($postArr['chrMenuDisplay'] == 'D') {
            $addlog = Config::get('Constant.DRAFT_SENT_FOR_APPROVAL');
        } else {
            $addlog = Config::get('Constant.SENT_FOR_APPROVAL');
        }
        $id = CommonModel::addRecord($cmsPageArr,'Powerpanel\CmsPage\Models\CmsPage');
        if (isset($id) && !empty($id)) {
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
            $newCmsPageObj = CmsPage::getRecordForLogById($id);
            $logArr = MyLibrary::logData($id, false, $addlog);
            $logArr['varTitle'] = stripslashes($newCmsPageObj->varTitle);
            Log::recordLog($logArr);
            if (Auth::user()->can('recent-updates-list')) {
                $notificationArr = MyLibrary::notificationData($id, $newCmsPageObj);
                RecentUpdates::setNotification($notificationArr);
            }
            self::flushCache();
            $response = $newCmsPageObj;
        }
        $whereConditionsAddstar = ['id' => $moduleObj->id];
        $updateAddStar = [
            'chrAddStar' => 'Y',
        ];
        CommonModel::updateRecords($whereConditionsAddstar, $updateAddStar,false, 'Powerpanel\CmsPage\Models\CmsPage');
        return $response;
    }

// End Insert Approval Records
    public function insertNewRecord($postArr, $cmsPageArr) {
        $response = false;
//Add post Handler=======
        $cmsPageArr['chrMain'] = 'Y';
        $cmsPageArr['intAliasId'] = MyLibrary::insertAlias($postArr['alias'], false, 'N');
        $cmsPageArr['created_at'] = Carbon::now();
        $cmsPageArr['updated_at'] = Carbon::now();
        if(Config::get('Constant.CHRSearchRank') == 'Y'){
        $cmsPageArr['intSearchRank'] = $postArr['search_rank'];
        }
        if (isset($postArr['chrPageActive']) && $postArr['chrPageActive'] != '') {
            $cmsPageArr['chrPageActive'] = $postArr['chrPageActive'];
        }
        if (isset($postArr['chrPageActive']) && $postArr['chrPageActive'] == 'PP') {
            $cmsPageArr['varPassword'] = $postArr['new_password'];
        } else {
            $cmsPageArr['varPassword'] = '';
        }

        if ($postArr['chrMenuDisplay'] == 'D') {
            $cmsPageArr['chrDraft'] = 'D';
            $cmsPageArr['chrPublish'] = 'N';
        } else {
            $cmsPageArr['chrDraft'] = 'N';
        }

        if ($postArr['chrMenuDisplay'] == 'D') {
            $addlog = Config::get('Constant.ADDED_DRAFT');
        } else {
            $addlog = '';
        }
        $id = CommonModel::addRecord($cmsPageArr, 'Powerpanel\CmsPage\Models\CmsPage');
        if (isset($id) && !empty($id)) {
            $newCmsPageObj = CmsPage::getRecordForLogById($id);
            $logArr = MyLibrary::logData($id, false, $addlog);
            $logArr['varTitle'] = $newCmsPageObj->varTitle;
            Log::recordLog($logArr);
            if (Auth::user()->can('recent-updates-list')) {
                $notificationArr = MyLibrary::notificationData($id, $newCmsPageObj);
                RecentUpdates::setNotification($notificationArr);
            }
            $response = $newCmsPageObj;
            self::flushCache();
        }
        return $response;
    }

    /**
     * This method destroys Banner in multiples.
     *
     * @return Banner index view
     *
     * @since   2016-10-25
     *
     * @author  NetQuick
     */
    public function DeleteRecord(Request $request) {
        $value = Request::get('value');
        $data['ids'] = Request::get('ids');
        $moduleHaveFields = ['chrMain', 'chrIsPreview'];
        $update = MyLibrary::deleteMultipleRecords($data, $moduleHaveFields, $value, 'Powerpanel\CmsPage\Models\CmsPage');
        if (File::exists(app_path() . '/Comments.php') != null || File::exists(base_path() . '/packages/Powerpanel/Workflow/src/Models/Comments.php') != null) {
            Comments::deleteComments($data['ids'], Config::get('Constant.MODULE.MODEL_NAME'));
        }
        foreach ($update as $update) {
            $ignoreDeleteScope = true;
            $Cmspage = CmsPage::getRecordById($update, $ignoreDeleteScope);
            $Cnt_Letest = CmsPage::getRecordCount_letest($Cmspage['fkMainRecord'], $Cmspage['id']);
            if ($Cnt_Letest <= 0) {
                $updateLetest = [
                    'chrAddStar' => 'N',
                ];
                $whereConditionsApprove = ['id' => $Cmspage['fkMainRecord']];
                CommonModel::updateRecords($whereConditionsApprove, $updateLetest,false, 'Powerpanel\CmsPage\Models\CmsPage');
                if (File::exists(app_path() . '/Workflow.php') != null || File::exists(base_path() . '/packages/Powerpanel/Workflow/src/Models/Workflow.php') != null) {
                    $where = [];
                    $flowData = [];
                    $flowData['dtNo'] = Config::get('Constant.SQLTIMESTAMP');
                    $where['fkModuleId'] = Config::get('Constant.MODULE.ID');
                    $where['fkRecordId'] = $Cmspage['fkMainRecord'];
                    $where['dtNo'] = 'null';
                    WorkflowLog::updateRecord($flowData, $where);
                }
            }
            if ($Cmspage['chrMain'] == "Y") {
                $whereConditions = [
                    'intPageId' => $update,
                    'intfkModuleId' => Config::get('Constant.MODULE.ID')
                ];
                $updateMenuFields = [
                    'chrPublish' => 'N',
                    'chrDelete' => 'Y',
                    'chrActive' => 'N',
                ];
                CommonModel::updateRecords($whereConditions, $updateMenuFields, false, '\\Powerpanel\\Menu\\Models\\Menu');
                //code for delete alias from database
                if ($value != 'P' && $value != 'F' && $value != 'A' && $value != 'D' && $value != 'R') {
                    Alias::where('id', $Cmspage['intAliasId'])
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
     * This method handle publish-unpublish features.
     *
     * @return true/false
     *
     * @since   2017-07-24
     *
     * @author  NetQuick
     */
    public function publish(Request $request) {
        $alias = Request::input('alias');
         $val = Request::get('val');
        $update = MyLibrary::setPublishUnpublish($alias, $val, 'Powerpanel\CmsPage\Models\CmsPage');
        $pageId = $alias;
        $state = Request::input('val') == 'Unpublish' ? 'N' : 'Y';
        $whereConditions = ['intPageId' => $pageId];
        $updateMenuFields = ['chrPublish' => $state, 'chrActive' => $state];
//CommonModel::updateRecords($whereConditions, $updateMenuFields, false, '\\Powerpanel\\Menu\\Models\\Menu');
        self::flushCache();
        echo json_encode($update);
        exit;
    }

    /**
     * This method handle to get child record.
     *
     * @author  Snehal
     */
    public function getChildData() {
        $childHtml = '';
        $Cmspage_childData = '';
        $Cmspage_childData = CmsPage::getChildGrid();
        $childHtml .= '<div class="producttbl" style="">';
        $childHtml .= '<table class="new_table_desing table table-striped table-bordered table-hover table-checkable dataTable" id="email_log_datatable_ajax">
																<tr role="row">
																		<th class="text-center"></th>
																		<th class="text-center">Title</th>
																		<th class="text-center">Date Submitted</th>
																		<th class="text-center">User</th>
																		<th class="text-center">Preview</th>
																		 <th class="text-center">Edit</th>
																		<th class="text-center">Status</th>';
        $childHtml .= '         </tr>';
        if (count($Cmspage_childData) > 0) {
            foreach ($Cmspage_childData as $child_row) {
                $parentAlias = $child_row->alias->varAlias;
                $childHtml .= '<tr role="row">';
                if ($child_row->chrApproved == 'N') {
                    $childHtml .= "<td><span class='mob_show_title'>&nbsp</span><input type=\"checkbox\" name=\"delete\" class=\"chkDelete\" value=\"$child_row->id\"></td>";
                } else {
                    $childHtml .= "<td><span class='mob_show_title'>&nbsp</span><div class=\"checker\"><a href=\"javascript:;\" data-toggle=\"tooltip\" data-placement=\"right\" title=\"This is approved record, so can't be deleted.\"><i style=\"color:red\" class=\"fa fa-exclamation-triangle\"></i></a></div></td>";
                }
                $url = url('/previewpage?url=' . MyLibrary::getFrontUri('pages')['uri'] . '/' . $parentAlias . '/' . $child_row->id . '/preview');
                $childHtml .= '<td class="text-center"><span class="mob_show_title">Title: </span>' . $child_row->varTitle . '</td>';
                $childHtml .= '<td class="text-center"><span class="mob_show_title">Date Submitted: </span>' . date('M d Y h:i A', strtotime($child_row->created_at)) . '</td>';
                $childHtml .= '<td class="text-center"><span class="mob_show_title">User: </span>' . CommonModel::getUserName($child_row->UserID) . '</td>';
                $childHtml .= '<td class="text-center"><span class="mob_show_title">Preview: </span><a class="icon_round" href=' . $url . " target='_blank'><i class=\"fa fa-desktop\"></i></a></td>";
                if ($child_row->chrApproved == 'N') {
                    $childHtml .= "<td class=\"text-center\"><span class='mob_show_title'>Edit: </span><a class='icon_round' title='" . trans('cmspage::template.common.edit') . "' href='" . route('powerpanel.pages.edit', array('alias' => $child_row->id)) . "?tab=A'><i class='fa fa-pencil'></i></a></td>";
                } else {
                    $childHtml .= '<td class="text-center"><span class="mob_show_title">Edit: </span>-</td>';
                }
                if ($child_row->chrApproved == 'N') {
                    $childHtml .= "<td class=\"text-center\"><span class='mob_show_title'>Status: </span><a class=\"approve_icon_btn\" title='" . trans('cmspage::template.common.comments') . "' href=\"javascript:;\" onclick=\"loadModelpopup('" . $child_row->id . "','" . $child_row->UserID . "','" . Config::get('Constant.MODULE.MODEL_NAME') . "','" . $child_row->fkMainRecord . "')\"><i class=\"fa fa-comments\"></i> <span>Comment</span></a><a class=\"approve_icon_btn\" onclick=\"update_mainrecord('" . $child_row->id . "','" . $child_row->fkMainRecord . "','" . $child_row->UserID . "','A');\" title='" . trans('cmspage::template.common.clickapprove') . "'  href=\"javascript:;\"><i class=\"fa fa-check-square-o\"></i> <span>Approve</span></a></td>";
                } else {
                    $childHtml .= '<td class="text-center"><span class="mob_show_title">Status: </span><span class="mob_show_overflow"><i class="la la-check-circle" style="font-size:30px;"></i><span style="display:block"><strong>Approved On: </strong>' . date('M d Y h:i A', strtotime($child_row->dtApprovedDateTime)) . '</span><span style="display:block"><strong>Approved By: </strong>' . CommonModel::getUserName($child_row->intApprovedBy) . '</span></span></td>';
                }
                $childHtml .= '</tr>';
            }
        } else {
            $childHtml .= "<tr><td colspan='7'>No Records</td></tr>";
        }
        $childHtml .= '</tr></td></tr>';
        $childHtml .= '</tr>
						</table>';
        echo $childHtml;
        exit;
    }

    /**
     * This method handle to Approve record updated by user.
     *
     * @author  Snehal
     */
    public function ApprovedData_Listing(Request $request) {
        $requestArr = Request::all();
        $request = (object) $requestArr;
        $main_id = Request::post('main_id');
        $approvalid = Request::post('id');
        $flag = Request::post('flag');
        $message = CmsPage::approved_data_Listing($request);
        $newCmsPageObj = CmsPage::getRecordForLogById($main_id);
        $approval_obj = CmsPage::getRecordForLogById($approvalid);
        if ($flag == 'R') {
            $restoredata = Config::get('Constant.ROLLBACK_RECORD');
        } else {
            if ($approval_obj->chrDraft == 'D') {
                $restoredata = Config::get('Constant.DRAFT_RECORD_APPROVED');
            } else {
                $restoredata = Config::get('Constant.RECORD_APPROVED');
            }
        }
        if (method_exists($this->MyLibrary, 'userNotificationData')) {
            /* notification for user to record approved */
            $userNotificationArr = MyLibrary::userNotificationData(Config::get('Constant.MODULE.ID'));
            $userNotificationArr['fkRecordId'] = $approvalid;
            $userNotificationArr['txtNotification'] = 'Your request has been approved by ' . ucfirst(auth()->user()->name) . ' (' . ucfirst(Config::get('Constant.MODULE.NAME')) . ')';
            $userNotificationArr['fkIntUserId'] = Auth::user()->id;
            $userNotificationArr['chrNotificationType'] = 'A';
            $userNotificationArr['intOnlyForUserId'] = $approval_obj->UserID;
            UserNotification::addRecord($userNotificationArr);
            /* notification for user to record approved */
        }
        $logArr = MyLibrary::logData($main_id, false, $restoredata);
        $logArr['varTitle'] = stripslashes($newCmsPageObj->varTitle);
        Log::recordLog($logArr);
//Update record in menu
        $whereConditions = ['intPageId' => $main_id, 'intfkModuleId' => 4];
        $updateMenuFields = [
            'varTitle' => $newCmsPageObj->varTitle,
        ];
//Update record in menu
        $where = [];
        $flowData = [];
        $flowData['dtYes'] = Config::get('Constant.SQLTIMESTAMP');
        $where['fkModuleId'] = Config::get('Constant.MODULE.ID');
        $where['fkRecordId'] = $main_id;
        $where['dtYes'] = 'null';
        WorkflowLog::updateRecord($flowData, $where);
        echo $message;
    }

    /**
     * This method handle to Comments record updated by user.
     *
     * @author  Snehal
     */
    public function Get_Comments(Request $request) {
        $requestArr = Request::all();
        $request = (object) $requestArr;
        $templateData = Comments::get_comments($request);
        $Comments = '';
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

    /**
     * This method rollback approved record.
     *
     * @author  Snehal
     */
    public function getChildData_rollback() {
        $child_rollbackHtml = '';
        $Cmspage_rollbackchildData = '';
        $Cmspage_rollbackchildData = CmsPage::getChildrollbackGrid();
        $child_rollbackHtml .= '<div class="producttbl producttb2" style="">';
        $child_rollbackHtml .= '<table class="new_table_desing table table-striped table-bordered table-hover table-checkable dataTable" id="email_log_datatable_ajax">
																<tr role="row">                                   
																		<th class="text-center">Title</th>
																		<th class="text-center">Date</th>
																		<th class="text-center">User</th>
																		<th class="text-center">Preview</th>                                     
																		<th class="text-center">Status</th>';
        $child_rollbackHtml .= '         </tr>';
        if (count($Cmspage_rollbackchildData) > 0) {
            foreach ($Cmspage_rollbackchildData as $child_rollbacrow) {
                $parentAlias = $child_rollbacrow->alias->varAlias;
                $url = url('/previewpage?url=' . MyLibrary::getFrontUri('pages')['uri'] . '/' . $parentAlias . '/' . $child_rollbacrow->id . '/preview');
                $child_rollbackHtml .= '<tr role="row">';
                $child_rollbackHtml .= '<td class="text-center"><span class="mob_show_title">Title: </span>' . $child_rollbacrow->varTitle . '</td>';
                $child_rollbackHtml .= '<td class="text-center"><span class="mob_show_title">Date: </span>' . date('M d Y h:i A', strtotime($child_rollbacrow->created_at)) . '</td>';
                $child_rollbackHtml .= '<td class="text-center"><span class="mob_show_title">User: </span>' . CommonModel::getUserName($child_rollbacrow->UserID) . '</td>';
                $child_rollbackHtml .= '<td class="text-center"><span class="mob_show_title">Preview: </span><a class="icon_round" href=' . $url . " target='_blank'><i class=\"fa fa-desktop\"></i></a></td>";
                if ($child_rollbacrow->chrApproved == 'Y') {
                    $child_rollbackHtml .= '<td class="text-center"><span class="mob_show_title">Status: </span><i class="la la-check-circle" style="color: #1080F2;font-size:30px;"></i></td>';
                } else {
                    // $child_rollbackHtml .= "<td class=\"text-center\"><span class='mob_show_title'>Status: </span><a onclick=\"update_mainrecord('" . $child_rollbacrow->id . "','" . $child_rollbacrow->fkMainRecord . "','" . $child_rollbacrow->UserID . "','R');\"  class=\"approve_icon_btn\">
					// 						<i class=\"fa fa-history\"></i>  <span>RollBack</span>
                    //                     </a></td>";
                    $child_rollbackHtml .= "<td class=\"text-center\"><span class=\"glyphicon glyphicon-minus\"></span></td>";
                }
                $child_rollbackHtml .= '</tr>';
            }
        } else {
            $child_rollbackHtml .= "<tr><td colspan='5'>No Records</td></tr>";
        }
        echo $child_rollbackHtml;
        exit;
    }

    /**
     * This method Comment Insert.
     *
     * @author  Snehal
     */
    public function insertComents(Request $request) {
        $modiledata = Modules::getModuleById(Request::post('varModuleId'));
        if ($modiledata['varModuleNameSpace'] != '') {
            $modelNameSpace = $modiledata['varModuleNameSpace'] . 'Models\\' . $modiledata['varModelName'];
        } else {
            $modelNameSpace = '\\App\\' . Request::post('namespace');
        }
        $Comments_data['intRecordID'] = Request::post('id');
        $Comments_data['varNameSpace'] = $modelNameSpace;
        $Comments_data['varModuleNameSpace'] = Request::post('namespace');
        $Comments_data['varCmsPageComments'] = stripslashes(trim(Request::post('CmsPageComments')));
        $Comments_data['UserID'] = Request::post('UserID');
        $Comments_data['intCommentBy'] = auth()->user()->id;
        $Comments_data['varModuleTitle'] = Request::post('varModuleTitle');
        $Comments_data['fkMainRecord'] = Request::post('fkMainRecord');
        Comments::insertComents($Comments_data);

        $commentdata = Config::get('Constant.COMMENT_ADDED');
        $newCmsPageObj = $modelNameSpace::getRecordForLogById(Request::post('id'));
        $logArr = MyLibrary::logData(Request::post('id'), Request::post('varModuleId'), $commentdata);
        $logArr['varTitle'] = stripslashes($newCmsPageObj->varTitle);
        Log::recordLog($logArr);
        /* code for insert comment */
        $userNotificationArr = MyLibrary::userNotificationData(Request::post('varModuleId'));
        $userNotificationArr['fkRecordId'] = Request::post('id');
        $userNotificationArr['txtNotification'] = 'New comment from ' . ucfirst(auth()->user()->name) . ' (' . ucfirst(Request::post('varModuleTitle')) . ')';
        $userNotificationArr['fkIntUserId'] = auth()->user()->id;
        $userNotificationArr['chrNotificationType'] = 'C';
        $userNotificationArr['intOnlyForUserId'] = Request::post('UserID');
        UserNotification::addRecord($userNotificationArr);
        exit;
    }

    public function tableData($value = false, $ignoreModuleIds = false) {
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
        $publish_action = '';
        $actions = '';
        $hasrecord = DB::table('menu')->where('intPageId',$value->id)->where('chrDelete','N')->where('chrPublish', 'Y')->count();
        if ($value->alias->varAlias != 'home') {
            if ($value->modules->varModuleName == 'pages' || $value->modules->varModuleName == 'sitemap') {
                $manageRecordsLink = $value->modules->varTitle;
            } else {
                if (isset($this->currentUserRoleData->chrIsAdmin) && $this->currentUserRoleData->chrIsAdmin == 'Y') {
                    $manageRecordsLink = '<a class = "" title = "Manage Records" href = "' . url('powerpanel/' . $value->modules->varModuleName) . '">Manage Records</a>';
                } else {
                    $manageRecordsLink = $value->modules->varTitle;
                }
            }
        } else {
            $manageRecordsLink = '-';
        }

        if (Auth::user()->can('pages-edit')) {
            $actions .= '<a  title = "' . trans('cmspage::template.common.edit') . '" class="" href = "' . route('powerpanel.pages.edit', array('alias' => $value->id)) . '?tab=P"><i class = "fa fa-pencil"></i></a>';
        }
        if ($value->id != 1) {
            $startDate = $value->dtDateTime;
            $endDate = $value->dtEndDateTime;
            $startDate = date('' . Config::get('Constant.DEFAULT_DATE_FORMAT') . ' ' . Config::get('Constant.DEFAULT_TIME_FORMAT') . '', strtotime($startDate));
            $endDate = !empty($value->dtEndDateTime) ? date('' . Config::get('Constant.DEFAULT_DATE_FORMAT') . ' ' . Config::get('Constant.DEFAULT_TIME_FORMAT') . '', strtotime($value->dtEndDateTime)) : 'No Expiry';
        } else {
            $startDate = '-';
            $endDate = '-';
        }
        // if ($value->chrAddStar != 'Y') {
        //     if ($value->chrDraft != 'D') {
        //         if (Auth::user()->can('pages-publish')) {
        //             if ($value->alias->varAlias != 'home') {
        //                 if ($value->chrPublish == 'Y') {
        //                     $publish_action .= '<input data-off-text = "No" data-on-text = "Yes" class = "make-switch publish" data-off-color = "info" data-on-color = "primary" type = "checkbox" data-controller = "powerpanel/pages" title = "' . trans('cmspage::template.common.publishedRecord') . '" data-value = "Unpublish" data-alias = "' . $value->id . '">';
        //                 } else {
        //                     $publish_action .= '<input checked = "" data-off-text = "No" data-on-text = "Yes" class = "make-switch publish" class = "make-switch publish" data-off-color = "info" data-on-color = "primary" type = "checkbox" data-controller = "powerpanel/pages" title = "' . trans('cmspage::template.common.unpublishedRecord') . '" data-value = "Publish" data-alias = "' . $value->id . '">';
        //                 }
        //             }
        //         }
        //     } else {
        //         if ($value->alias->varAlias != 'home') {
        //             if ($value->chrPublish == 'Y') {
        //                 $publish_action .= '<input data-off-text = "No" data-on-text = "Yes" class = "make-switch pub" data-off-color = "info" data-on-color = "primary" type = "checkbox" data-controller = "powerpanel/pages" title = "' . trans('cmspage::template.common.publishedRecord') . '" data-value = "Unpublish" data-alias = "' . $value->id . '">';
        //             } else {
        //                 $publish_action .= '<input checked = "" data-off-text = "No" data-on-text = "Yes" class = "make-switch pub" class = "make-switch publish" data-off-color = "info" data-on-color = "primary" type = "checkbox" data-controller = "powerpanel/pages" title = "' . trans('cmspage::template.common.unpublishedRecord') . '" data-value = "Publish" data-alias = "' . $value->id . '">';
        //             }
        //         }
        //     }
        // } else {
        //     $publish_action .= '---';
        // }
        if ($value->chrAddStar != 'Y') {
            if ($value->chrDraft != 'D') {
                if (Auth::user()->can('pages-publish')) {
                    if ($value->alias->varAlias != 'home' && isset($hasrecord) && $hasrecord == 0) {
                        if ($value->modules->varModuleName == 'pages') {
                            if($value->id != 13 && $value->id != 15){
                                if ($value->chrPublish == 'Y') {
                                    $publish_action .= '<input data-off-text = "No" data-on-text = "Yes" class = "make-switch publish" data-off-color = "info" data-on-color = "primary" type = "checkbox" data-controller = "powerpanel/pages" title = "' . trans('cmspage::template.common.publishedRecord') . '" data-value = "Unpublish" data-alias = "' . $value->id . '">';
                                } else {
                                    $publish_action .= '<input checked = "" data-off-text = "No" data-on-text = "Yes" class = "make-switch publish" class = "make-switch publish" data-off-color = "info" data-on-color = "primary" type = "checkbox" data-controller = "powerpanel/pages" title = "' . trans('cmspage::template.common.unpublishedRecord') . '" data-value = "Publish" data-alias = "' . $value->id . '">';
                                }
                            }
                        }
                    }
                }
            } else {
                if ($value->alias->varAlias != 'home') {
                    if ($value->chrPublish == 'Y') {
                        $publish_action .= '<input data-off-text = "No" data-on-text = "Yes" class = "make-switch pub" data-off-color = "info" data-on-color = "primary" type = "checkbox" data-controller = "powerpanel/pages" title = "' . trans('cmspage::template.common.publishedRecord') . '" data-value = "Unpublish" data-alias = "' . $value->id . '">';
                    } else {
                        $publish_action .= '<input checked = "" data-off-text = "No" data-on-text = "Yes" class = "make-switch pub" class = "make-switch publish" data-off-color = "info" data-on-color = "primary" type = "checkbox" data-controller = "powerpanel/pages" title = "' . trans('cmspage::template.common.unpublishedRecord') . '" data-value = "Publish" data-alias = "' . $value->id . '">';
                    }
                }
            }
        } else {
            $publish_action .= '---';
        }
        if (Auth::user()->can('pages-delete') || (isset($this->currentUserRoleData->chrIsAdmin) && $this->currentUserRoleData->chrIsAdmin == 'Y')) {
            if ($value->alias->varAlias != 'home' && isset($hasrecord) && $hasrecord == 0) {
                if ($value->modules->varModuleName == 'pages') {
                    if($value->id != 15){
                        if (Config::get('Constant.DEFAULT_TRASH') == 'Y') {
                            $actions .= '<a title = "Trash" class="delete-grid" href = "javascript:;" onclick = \'Trashfun("' . $value->id . '")\' data-controller = "pages" data-alias = "' . $value->id . '" data-tab = "P"><i class = "fa fa-times"></i></a>';
                        } else {
                            $actions .= '<a class = "delete" title = "' . trans('cmspage::template.common.delete') . '" data-controller = "pages" data-alias = "' . $value->id . '" data-tab = "P"><i class = "fa fa-times"></i></a>';
                        }
                    }
                }
            }
        }
        if(isset($hasrecord) && $hasrecord != 0 || $value->modules->varModuleName != 'pages'){
            $checkbox = '<div class="checker"><a href = "javascript:;" data-toggle = "tooltip" data-placement = "right" data-toggle = "tooltip" title = "This is module page so can&#39;t be deleted."><i style = "color:red" class = "fa fa-exclamation-triangle"></i></a></div>';
        }
        elseif($value->id == 15){
            $checkbox = '<div class="checker"><a href = "javascript:;" data-toggle = "tooltip" data-placement = "right" data-toggle = "tooltip" title = "This is module page so can&#39;t be deleted."><i style = "color:red" class = "fa fa-exclamation-triangle"></i></a></div>';
        }
        else{
            $checkbox = '<input type = "checkbox" name = "delete" class = "chkDelete" value = "' . $value->id . '">';
        }
        // if ($value->modules->varModuleName == 'pages') {
        //     if ($value->alias->varAlias != 'home') {
        //         $checkbox = '<input type = "checkbox" name = "delete" class = "chkDelete" value = "' . $value->id . '">';
        //     }
        // }
        if (Auth::user()->can('pages-reviewchanges')) {
            $update = "<a class=\"icon_title1\" title=\"Click here to see all approval records.\" style=\"margin-right: 5px;\" onclick=\"javascript:expandcollapsepanel(this ,'tasklisting" . $value->id . "', 'mainsingnimg" . $value->id . "'," . $value->id . ')"><i id="mainsingnimg' . $value->id . '" class="la la-plus-square"></i></a>';
            $rollback = "<a class=\"icon_title2\" title=\"Click here to see all approved records to rollback.\" style=\"margin-right: 5px;\" onclick=\"javascript:expandcollapsepanel_rolback(this ,'tasklisting_rollback" . $value->id . "', 'mainsingnimg_rollback" . $value->id . "'," . $value->id . ')"><i id="mainsingnimg_rollback' . $value->id . '" class="la la-history"></i></a>';
        } else {
            $update = '';
            $rollback = '';
        }
        $Quickedit_startDate = date('Y-m-d H:i', strtotime($value->dtDateTime));
        $Quickedit_endDate = !empty($value->dtEndDateTime) ? date('Y-m-d H:i', strtotime($value->dtEndDateTime)) : 'No Expiry';
        $title = $value->varTitle;
        if (Auth::user()->can('pages-edit')) {

            if ($value->chrDraft == 'D' || $value->chrAddStar == 'Y') {
                $viewlink = url('/previewpage?url=' . url($value->alias->varAlias) . '/' . $value->id . '/preview');
                $linkviewLable = "Preview";
            } else {
                $viewlink = url($value->alias->varAlias);
                $linkviewLable = "View";
            }
            if ($value->chrLock != 'Y') {
                if (isset($this->currentUserRoleData->chrIsAdmin) && $this->currentUserRoleData->chrIsAdmin == 'Y') {
                    $title = '<div class="quick_edit"><a href = "' . route('powerpanel.pages.edit', array('alias' => $value->id)) . '?tab=P">' . $value->varTitle . '</a>';
                    if ($value->alias->varAlias != "home") {
                        if (Config::get('Constant.DEFAULT_QUICK') == 'Y') {
                            $title .= '<span><a title="Quick Edit" href=\'javascript:;\' data-toggle=\'modal\' data-target=\'#modalForm\' aria-label=\'Quick edit\' onclick=\'Quickeditfun("' . $value->id . '","' . $value->varTitle . '","' . $value->intSearchRank . '","' . $Quickedit_startDate . '","' . $Quickedit_endDate . '","P")\'>Quick Edit</a></span>';
                        }
                    }
                    if ($value->alias->varAlias != 'home') {
                        if ($value->modules->varModuleName == 'pages') {
                            if (Config::get('Constant.DEFAULT_TRASH') == 'Y') {
                                $title .= '<span><a title = "Trash" href = \'javascript:;\' onclick=\'Trashfun("' . $value->id . '")\' class="red" data-tab="P">Trash</a></span>';
                            }
                        }
                    }
                    if ($value->alias->varAlias != 'home') {
                        if (Config::get('Constant.DEFAULT_ARCHIVE') == 'Y') {
                            $title .= '<span><a title = "Archive" href = \'javascript:;\' onclick="Archivefun(' . $value->id . ',\'N\',\'P\')"   data-tab="P">Archive</a></span>';
                        }
                    }
                    $title .= '</div>';
                } else {
                    $title = '<div class="quick_edit"><a href = "' . route('powerpanel.pages.edit', array('alias' => $value->id)) . '?tab=P">' . $value->varTitle . '</a> <div class="quick_edit_menu">
                            <span><a href="' . route('powerpanel.pages.edit', array('alias' => $value->id)) . '?tab=P" title="Edit">Edit</a></span>
                            <span><a href="' . $viewlink . '" target="_blank" title="' . $linkviewLable . '" >' . $linkviewLable . '</a></span>
                                </div>    
                       </div>';
                }
            } else {
                if (auth()->user()->id != $value->LockUserID) {
                    if (isset($this->currentUserRoleData->chrIsAdmin) && $this->currentUserRoleData->chrIsAdmin == 'Y') {
                        $title = '<div class="quick_edit"><a href = "' . route('powerpanel.pages.edit', array('alias' => $value->id)) . '?tab=P">' . $value->varTitle . '</a> <div class="quick_edit_menu">
                            <span><a href="' . route('powerpanel.pages.edit', array('alias' => $value->id)) . '?tab=P" title="Edit">Edit</a></span>
                            <span><a href="' . $viewlink . '" target="_blank" title="' . $linkviewLable . '" >' . $linkviewLable . '</a></span>
                                </div>    
                       </div>';
                    } else {
                        $title = '<div class="quick_edit"><a href = "javascript:;">' . $value->varTitle . '</a></div>';
                    }
                } else {
                    $title = '<div class="quick_edit"><a href = "' . route('powerpanel.pages.edit', array('alias' => $value->id)) . '?tab=P">' . $value->varTitle . '</a> <div class="quick_edit_menu">
                            <span><a href="' . route('powerpanel.pages.edit', array('alias' => $value->id)) . '?tab=P" title="Edit">Edit</a></span>
                            <span><a href="' . $viewlink . '" target="_blank" title="' . $linkviewLable . '" >' . $linkviewLable . '</a></span>
                                </div>    
                       </div>';
                }
            }
        }
        if (isset($this->currentUserRoleData->chrIsAdmin) && $this->currentUserRoleData->chrIsAdmin == 'Y') {
            if (!in_array($value->intFKModuleCode, $ignoreModuleIds)) {
                if ($value->alias->varAlias === "home") {
                    //$actions .= '<a class="share" title="Click to share on social media pages" data-modal="CmsPage" data-alias="' . $value->id . '" data-title="' . $value->varTitle . '" data-images="" data-namespace="Powerpanel\CmsPage\" data-backdrop="static" data-keyboard="false" data-link = "' . url('/') . '" data-toggle="modal" data-target="#confirm_share"><i class="la la-share-alt"></i></a>';
                } else {
                    // $actions .= '<a class="share" title="Click to share on social media pages" data-modal="CmsPage" data-alias="' . $value->id . '" data-title="' . $value->varTitle . '" data-images="" data-namespace="Powerpanel\CmsPage\" data-backdrop="static" data-keyboard="false" data-link = "' . url('/' . $value->alias['varAlias']) . '" data-toggle="modal" data-target="#confirm_share">
					// <i class="la la-share-alt"></i></a>';
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
                $Favorite = '<a class="star_icon_div" href="javascript:;" onclick="GetFavorite(' . $value->id . ',\'N\',\'P\')"><i class="' . $Class . '"></i></a>';
            } else {
                $Class = 'la la-star-o';
                $Favorite = '<a class="star_icon_div" href="javascript:;" onclick="GetFavorite(' . $value->id . ',\'Y\',\'P\')"><i class="' . $Class . '"></i></a>';
            }
        } else {
            $Favorite = '';
        }

        $First_td = '<div class="star_box">' . $Favorite . $pbtn . '</div>';
        if ($value->updated_at == '-0001-11-30 00:00:00') {
            $udate = '---';
        } else {
            $udate = date('' . Config::get('Constant.DEFAULT_DATE_FORMAT') . ' ' . Config::get('Constant.DEFAULT_TIME_FORMAT') . '', strtotime($value->updated_at));
        }
        $logurl = url('powerpanel/log?id=' . $value->id . '&mid=' . Config::get('Constant.MODULE.ID'));
        $log = '';
        if ($value->chrLock != 'Y') {
            if (isset($this->currentUserRoleData->chrIsAdmin) && $this->currentUserRoleData->chrIsAdmin == 'Y') {
                if (Config::get('Constant.DEFAULT_DUPLICATE') == 'Y') {
                    $log .= "<a title=\"Duplicate\" class='copy-grid' href=\"javascript:;\" onclick=\"GetCopyPage('" . $value->id . "');\"><i class=\"fa fa-clone\"></i></a>";
                }
                $log .= $actions;
                if (Auth::user()->can('log-list')) {
                    //$log .= "<a title=\"Log History\" class='log-grid' href=\"$logurl\"><i class=\"fa fa-clock-o\"></i></a>";
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
                if ((auth()->user()->id == $value->LockUserID) || (isset($this->currentUserRoleData->chrIsAdmin) && $this->currentUserRoleData->chrIsAdmin == 'Y')) {
                    $publish_action = $publish_action;
                } else {
                    $publish_action = "---";
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
        if ($value->chrArchive == 'Y') {
            $status .= Config::get('Constant.ARCHIVE_LIST') . ' ';
        }
        if (Auth::user()->can('banners-reviewchanges')) {
            //$log .= "<a title='Rollback to previous version'  onclick=\"rollbackToPreviousVersion('" . $value->id . "');\"  class=\"log-grid\"><i class=\"fa fa-history\"></i></a>";
        }
        $frontMenu =  Menu::select('intAliasId')->where('intAliasId',$value->intAliasId)->first();
        $records = array(
            $checkbox,
            $First_td,
            '<div class="pages_title_div_row">' . $title .'</div>',
            $manageRecordsLink,
            // $startDate,
            // $endDate,
            $webHits,
            // ($frontMenu == null) ? $publish_action : "---",
            $publish_action,
            $udate,
            $log,
        );
        return $records;
    }

    public function tableDataTrash($value = false, $ignoreModuleIds = false) {
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
        $publish_action = '';
        $actions = '';
        if ($value->modules->varModuleName == 'pages' || $value->modules->varModuleName == 'sitemap') {
            $manageRecordsLink = $value->modules->varTitle;
        } else {
            if ($this->currentUserRoleData->chrIsAdmin == 'Y') {
                $manageRecordsLink = '<a class = "" title = "Manage Records" href = "' . url('powerpanel/' . $value->modules->varModuleName) . '">Manage Records</a>';
            } else {
                $manageRecordsLink = $value->modules->varTitle;
            }
        }
        if ($value->id != 1) {
            $startDate = $value->dtDateTime;
            $endDate = $value->dtEndDateTime;
            $startDate = date('' . Config::get('Constant.DEFAULT_DATE_FORMAT') . ' ' . Config::get('Constant.DEFAULT_TIME_FORMAT') . '', strtotime($startDate));
            $endDate = !empty($value->dtEndDateTime) ? date('' . Config::get('Constant.DEFAULT_DATE_FORMAT') . ' ' . Config::get('Constant.DEFAULT_TIME_FORMAT') . '', strtotime($value->dtEndDateTime)) : 'No Expiry';
        } else {
            $startDate = '-';
            $endDate = '-';
        }
        if (Auth::user()->can('pages-publish')) {
            if ($value->alias->varAlias != 'home') {
                if ($value->chrPublish == 'Y') {
                    $publish_action .= '<input data-off-text = "No" data-on-text = "Yes" class = "make-switch publish" data-off-color = "info" data-on-color = "primary" type = "checkbox" data-controller = "powerpanel/pages" title = "' . trans('cmspage::template.common.publishedRecord') . '" data-value = "Unpublish" data-alias = "' . $value->id . '">';
                } else {
                    $publish_action .= '<input checked = "" data-off-text = "No" data-on-text = "Yes" class = "make-switch publish" class = "make-switch publish" data-off-color = "info" data-on-color = "primary" type = "checkbox" data-controller = "powerpanel/pages" title = "' . trans('cmspage::template.common.unpublishedRecord') . '" data-value = "Publish" data-alias = "' . $value->id . '">';
                }
            }
        }
        if (Auth::user()->can('pages-delete') && $this->currentUserRoleData->chrIsAdmin == 'Y') {
            if ($value->alias->varAlias != 'home') {
                if ($value->modules->varModuleName == 'pages') {
                    $actions .= '<a class = " delete" title = "' . trans('cmspage::template.common.delete') . '" data-controller = "pages" data-alias = "' . $value->id . '" data-tab = "T"><i class = "fa fa-times"></i></a>';
                }
            }
        }
        $checkbox = '<div class="checker"><a href = "javascript:;" data-toggle = "tooltip" data-placement = "right" data-toggle = "tooltip" title = "This is module page so can&#39;t be deleted."><i style = "color:red" class = "fa fa-exclamation-triangle"></i></a></div>';
        if ($value->modules->varModuleName == 'pages') {
            if ($value->alias->varAlias != 'home') {
                $checkbox = '<input type = "checkbox" name = "delete" class = "chkDelete" value = "' . $value->id . '">';
            }
        }
        if (Auth::user()->can('pages-reviewchanges')) {
            $update = "<a class=\"icon_title1\" title=\"Click here to see all approval records.\" style=\"margin-right: 5px;\" onclick=\"javascript:expandcollapsepanel(this ,'tasklisting" . $value->id . "', 'mainsingnimg" . $value->id . "'," . $value->id . ')"><i id="mainsingnimg' . $value->id . '" class="la la-plus-square"></i></a>';
            $rollback = "<a class=\"icon_title2\" title=\"Click here to see all approved records to rollback.\" style=\"margin-right: 5px;\" onclick=\"javascript:expandcollapsepanel_rolback(this ,'tasklisting_rollback" . $value->id . "', 'mainsingnimg_rollback" . $value->id . "'," . $value->id . ')"><i id="mainsingnimg_rollback' . $value->id . '" class="la la-history"></i></a>';
        } else {
            $update = '';
            $rollback = '';
        }
        $title = $value->varTitle;
        if (Auth::user()->can('pages-edit')) {
            $title = '<div class="quick_edit text-uppercase">' . $value->varTitle . '    
                        </div>';
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
        $First_td = '<div class="star_box">' . $pbtn . '</div>';
        if ($value->updated_at == '-0001-11-30 00:00:00') {
            $udate = '---';
        } else {
            $udate = date('' . Config::get('Constant.DEFAULT_DATE_FORMAT') . ' ' . Config::get('Constant.DEFAULT_TIME_FORMAT') . '', strtotime($value->updated_at));
        }
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
        if ($value->chrArchive == 'Y') {
            $status .= Config::get('Constant.ARCHIVE_LIST') . ' ';
        }
        $records = array(
            $checkbox,
            '<div class="pages_title_div_row">'.$First_td . $title . ' ' . $status . $statusdata . '</div>',
            $manageRecordsLink,
            $startDate,
            $endDate,
            $webHits,
            $udate,
            $log,
        );
        return $records;
    }

    public function tableDataFavorite($value = false, $ignoreModuleIds = false) {
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
        $publish_action = '';
        $actions = '';
        if ($value->alias->varAlias != 'home') {
            if ($value->modules->varModuleName == 'pages' || $value->modules->varModuleName == 'sitemap') {
                $manageRecordsLink = $value->modules->varTitle;
            } else {
                if ($this->currentUserRoleData->chrIsAdmin == 'Y') {
                    $manageRecordsLink = '<a class = "" title = "Manage Records" href = "' . url('powerpanel/' . $value->modules->varModuleName) . '">Manage Records</a>';
                } else {
                    $manageRecordsLink = $value->modules->varTitle;
                }
            }
        } else {
            $manageRecordsLink = '-';
        }
        if (Auth::user()->can('pages-edit')) {
            $actions .= '<a class = "" title = "' . trans('cmspage::template.common.edit') . '" href = "' . route('powerpanel.pages.edit', array('alias' => $value->id)) . '?tab=P"><i class = "fa fa-pencil"></i></a>';
        }
        if ($value->id != 1) {
            $startDate = $value->dtDateTime;
            $endDate = $value->dtEndDateTime;
            $startDate = date('' . Config::get('Constant.DEFAULT_DATE_FORMAT') . ' ' . Config::get('Constant.DEFAULT_TIME_FORMAT') . '', strtotime($startDate));
            $endDate = !empty($value->dtEndDateTime) ? date('' . Config::get('Constant.DEFAULT_DATE_FORMAT') . ' ' . Config::get('Constant.DEFAULT_TIME_FORMAT') . '', strtotime($value->dtEndDateTime)) : 'No Expiry';
        } else {
            $startDate = '-';
            $endDate = '-';
        }
        if (Auth::user()->can('pages-publish')) {
            if ($value->alias->varAlias != 'home') {
                if ($value->chrPublish == 'Y') {
                    $publish_action .= '<input data-off-text = "No" data-on-text = "Yes" class = "make-switch publish" data-off-color = "info" data-on-color = "primary" type = "checkbox" data-controller = "powerpanel/pages" title = "' . trans('cmspage::template.common.publishedRecord') . '" data-value = "Unpublish" data-alias = "' . $value->id . '">';
                } else {
                    $publish_action .= '<input checked = "" data-off-text = "No" data-on-text = "Yes" class = "make-switch publish" class = "make-switch publish" data-off-color = "info" data-on-color = "primary" type = "checkbox" data-controller = "powerpanel/pages" title = "' . trans('cmspage::template.common.unpublishedRecord') . '" data-value = "Publish" data-alias = "' . $value->id . '">';
                }
            }
        }
        if (Auth::user()->can('pages-delete') && $this->currentUserRoleData->chrIsAdmin == 'Y') {
            if ($value->alias->varAlias != 'home') {
                if ($value->modules->varModuleName == 'pages') {
                    if (Config::get('Constant.DEFAULT_TRASH') == 'Y') {
                        $actions .= '<a class = "delete-grid" title = "Trash" href = "javascript:;" onclick = \'Trashfun("' . $value->id . '")\' data-controller = "pages" data-alias = "' . $value->id . '" data-tab = "F"><i class = "fa fa-times"></i></a>';
                    } else {
                        $actions .= '<a class = " delete" title = "' . trans('cmspage::template.common.delete') . '"  data-controller = "pages" data-alias = "' . $value->id . '" data-tab = "F"><i class = "fa fa-times"></i></a>';
                    }
                }
            }
        }
        $checkbox = '<div class="checker"><a href = "javascript:;" data-toggle = "tooltip" data-placement = "right" data-toggle = "tooltip" title = "This is module page so can&#39;t be deleted."><i style = "color:red" class = "fa fa-exclamation-triangle"></i></a></div>';
        if ($value->modules->varModuleName == 'pages') {
            if ($value->alias->varAlias != 'home') {
                $checkbox = '<input type = "checkbox" name = "delete" class = "chkDelete" value = "' . $value->id . '">';
            }
        }
        if (Auth::user()->can('pages-reviewchanges')) {
            $update = "<a class=\"icon_title1\" title=\"Click here to see all approval records.\" style=\"margin-right: 5px;\" onclick=\"javascript:expandcollapsepanel(this ,'tasklisting" . $value->id . "', 'mainsingnimg" . $value->id . "'," . $value->id . ')"><i id="mainsingnimg' . $value->id . '" class="la la-plus-square"></i></a>';
            $rollback = "<a class=\"icon_title2\" title=\"Click here to see all approved records to rollback.\" style=\"margin-right: 5px;\" onclick=\"javascript:expandcollapsepanel_rolback(this ,'tasklisting_rollback" . $value->id . "', 'mainsingnimg_rollback" . $value->id . "'," . $value->id . ')"><i id="mainsingnimg_rollback' . $value->id . '" class="la la-history"></i></a>';
        } else {
            $update = '';
            $rollback = '';
        }

        $Quickedit_startDate = date('Y-m-d H:i', strtotime($value->dtDateTime));
        $Quickedit_endDate = !empty($value->dtEndDateTime) ? date('Y-m-d H:i', strtotime($value->dtEndDateTime)) : 'No Expiry';
        $title = $value->varTitle;
        if (Auth::user()->can('pages-edit')) {
            if ($value->chrDraft == 'D' || $value->chrAddStar == 'Y') {
                $viewlink = url('/previewpage?url=' . url($value->alias->varAlias) . '/' . $value->id . '/preview');
                $linkviewLable = "Preview";
            } else {
                $viewlink = url($value->alias->varAlias);
                $linkviewLable = "View";
            }
            if ($value->chrLock != 'Y') {
                if ($this->currentUserRoleData->chrIsAdmin == 'Y') {
                    $title = '<div class="quick_edit"><a href = "' . route('powerpanel.pages.edit', array('alias' => $value->id)) . '?tab=F">' . $value->varTitle . '</a> <div class="quick_edit_menu">
	                            <span><a href="' . route('powerpanel.pages.edit', array('alias' => $value->id)) . '?tab=F" title="Edit">Edit</a></span>';
                    if ($value->alias->varAlias != 'home') {
                        if ($value->modules->varModuleName == 'pages') {
                            if (Config::get('Constant.DEFAULT_TRASH') == 'Y') {
                                $title .= '<span><a title = "Trash" href = \'javascript:;\' onclick=\'Trashfun("' . $value->id . '")\' class="red" data-tab="F">Trash</a></span>';
                            }
                        }
                    }
                    $title .= '<span><a href = "' . $viewlink . '" target = "_blank" title = "' . $linkviewLable . '" >' . $linkviewLable . '</a></span></div></div>';
                } else {
                    $title = '<div class="quick_edit"><a href = "' . route('powerpanel.pages.edit', array('alias' => $value->id)) . '?tab=F">' . $value->varTitle . '</a> <div class="quick_edit_menu">
	                            <span><a href="' . route('powerpanel.pages.edit', array('alias' => $value->id)) . '?tab=F" title="Edit">Edit</a></span>
	                            <span><a href="' . $viewlink . '" target="_blank" title="' . $linkviewLable . '" >' . $linkviewLable . '</a></span>
	                                </div>    
	                        </div>';
                }
            } else {
                if (auth()->user()->id != $value->LockUserID) {
                    if ($this->currentUserRoleData->chrIsAdmin == 'Y') {
                        $title = '<div class="quick_edit"><a href = "' . route('powerpanel.pages.edit', array('alias' => $value->id)) . '?tab=F">' . $value->varTitle . '</a> <div class="quick_edit_menu">
	                            <span><a href="' . route('powerpanel.pages.edit', array('alias' => $value->id)) . '?tab=F" title="Edit">Edit</a></span>
	                            <span><a href="' . $viewlink . '" target="_blank" title="' . $linkviewLable . '" >' . $linkviewLable . '</a></span>
	                                </div>    
	                        </div>';
                    } else {
                        $title = '<div class="quick_edit"><a href = "javascript:;">' . $value->varTitle . '</a></div>';
                    }
                } else {
                    $title = '<div class="quick_edit"><a href = "' . route('powerpanel.pages.edit', array('alias' => $value->id)) . '?tab=F">' . $value->varTitle . '</a> <div class="quick_edit_menu">
	                            <span><a href="' . route('powerpanel.pages.edit', array('alias' => $value->id)) . '?tab=F" title="Edit">Edit</a></span>
	                            <span><a href="' . $viewlink . '" target="_blank" title="' . $linkviewLable . '" >' . $linkviewLable . '</a></span>
	                                </div>    
	                        </div>';
                }
            }
        }
        if ($this->currentUserRoleData->chrIsAdmin == 'Y') {
            if (!in_array($value->intFKModuleCode, $ignoreModuleIds)) {
                
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
                $Favorite = '<a class="star_icon_div" href="javascript:;" onclick="GetFavorite(' . $value->id . ',\'N\',\'F\')"><i class="' . $Class . '"></i></a>';
            } else {
                $Class = 'la la-star-o';
                $Favorite = '<a class="star_icon_div" href="javascript:;" onclick="GetFavorite(' . $value->id . ',\'Y\',\'F\')"><i class="' . $Class . '"></i></a>';
            }
        } else {
            $Favorite = '';
        }
        $First_td = '<div class="star_box">' . $Favorite . $pbtn . '</div>';
        if ($value->updated_at == '-0001-11-30 00:00:00') {
            $udate = '---';
        } else {
            $udate = date('' . Config::get('Constant.DEFAULT_DATE_FORMAT') . ' ' . Config::get('Constant.DEFAULT_TIME_FORMAT') . '', strtotime($value->updated_at));
        }
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
        if ($value->chrArchive == 'Y') {
            $status .= Config::get('Constant.ARCHIVE_LIST') . ' ';
        }
        $records = array(
            $checkbox,
            $First_td,
            '<div class="pages_title_div_row">' . $title . ' ' . $status . $statusdata . '</div>',
            $manageRecordsLink,
            $startDate,
            $endDate,
            $webHits,
            $udate,
            $log,
        );
        return $records;
    }

    public function tableDataArchive($value = false, $ignoreModuleIds = false) {
        $Hits = Pagehit::where('fkIntAliasId', $value->intAliasId)->count();
        $webHits = '';
        if ($Hits > 0) {
            $webHits .= '<a data-toggle="modal" href="#" onclick=\'HitsPopup("' . $value->id . '","' . $value->intAliasId . '","' . $value->varTitle . '","R")\'>' . $Hits . '</a>
                    <div class="new_modal modal fade" id="desc_' . $value->id . '_R" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog" style="margin: 0 auto;display:table;width: 100%;height:100%;max-width: 1000px;">
                        <div class="modal-vertical">
                        <div class="modal-content">
                    <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                    <h3 class="modal-title">Hits Report</h3>
                    </div>
                    <div class="modal-body">
                    <div id="webdata_' . $value->id . '_R"></div>
                    </div>
                    </div>
                    </div>
                    </div>
                    </div>';
        } else {
            $webHits .= '0';
        }
        $publish_action = '';
        $actions = '';
        if ($value->alias->varAlias != 'home') {
            if ($value->modules->varModuleName == 'pages' || $value->modules->varModuleName == 'sitemap') {
                $manageRecordsLink = $value->modules->varTitle;
            } else {
                if ($this->currentUserRoleData->chrIsAdmin == 'Y') {
                    $manageRecordsLink = '<a class = "" title = "Manage Records" href = "' . url('powerpanel/' . $value->modules->varModuleName) . '">Manage Records</a>';
                } else {
                    $manageRecordsLink = $value->modules->varTitle;
                }
            }
        } else {
            $manageRecordsLink = '-';
        }
        if (Auth::user()->can('pages-edit')) {
            $actions .= '<a class = "" title = "' . trans('cmspage::template.common.edit') . '" href = "' . route('powerpanel.pages.edit', array('alias' => $value->id)) . '?tab=R"><i class = "fa fa-pencil"></i></a>';
        }
        if ($value->id != 1) {
            $startDate = $value->dtDateTime;
            $endDate = $value->dtEndDateTime;
            $startDate = date('' . Config::get('Constant.DEFAULT_DATE_FORMAT') . ' ' . Config::get('Constant.DEFAULT_TIME_FORMAT') . '', strtotime($startDate));
            $endDate = !empty($value->dtEndDateTime) ? date('' . Config::get('Constant.DEFAULT_DATE_FORMAT') . ' ' . Config::get('Constant.DEFAULT_TIME_FORMAT') . '', strtotime($value->dtEndDateTime)) : 'No Expiry';
        } else {
            $startDate = '-';
            $endDate = '-';
        }
        if (Auth::user()->can('pages-publish')) {
            if ($value->alias->varAlias != 'home') {
                if ($value->chrPublish == 'Y') {
                    $publish_action .= '<input data-off-text = "No" data-on-text = "Yes" class = "make-switch publish" data-off-color = "info" data-on-color = "primary" type = "checkbox" data-controller = "powerpanel/pages" title = "' . trans('cmspage::template.common.publishedRecord') . '" data-value = "Unpublish" data-alias = "' . $value->id . '">';
                } else {
                    $publish_action .= '<input checked = "" data-off-text = "No" data-on-text = "Yes" class = "make-switch publish" class = "make-switch publish" data-off-color = "info" data-on-color = "primary" type = "checkbox" data-controller = "powerpanel/pages" title = "' . trans('cmspage::template.common.unpublishedRecord') . '" data-value = "Publish" data-alias = "' . $value->id . '">';
                }
            }
        }
        if (Auth::user()->can('pages-delete') && $this->currentUserRoleData->chrIsAdmin == 'Y') {
            if ($value->alias->varAlias != 'home') {
                if ($value->modules->varModuleName == 'pages') {
                    if (Config::get('Constant.DEFAULT_TRASH') == 'Y') {
                        $actions .= '<a class = "delete-grid" title = "Trash" href = "javascript:;" onclick = \'Trashfun("' . $value->id . '")\' data-controller = "pages" data-alias = "' . $value->id . '" data-tab = "R"><i class = "fa fa-times"></i></a>';
                    } else {
                        $actions .= '<a class = " delete" title = "' . trans('cmspage::template.common.delete') . '"  data-controller = "pages" data-alias = "' . $value->id . '" data-tab = "R"><i class = "fa fa-times"></i></a>';
                    }
                }
            }
        }
        $checkbox = '<div class="checker"><a href = "javascript:;" data-toggle = "tooltip" data-placement = "right" data-toggle = "tooltip" title = "This is module page so can&#39;t be deleted."><i style = "color:red" class = "fa fa-exclamation-triangle"></i></a></div>';
        if ($value->modules->varModuleName == 'pages') {
            if ($value->alias->varAlias != 'home') {
                $checkbox = '<input type = "checkbox" name = "delete" class = "chkDelete" value = "' . $value->id . '">';
            }
        }
        if (Auth::user()->can('pages-reviewchanges')) {
            $update = "<a class=\"icon_title1\" title=\"Click here to see all approval records.\" style=\"margin-right: 5px;\" onclick=\"javascript:expandcollapsepanel(this ,'tasklisting" . $value->id . "', 'mainsingnimg" . $value->id . "'," . $value->id . ')"><i id="mainsingnimg' . $value->id . '" class="la la-plus-square"></i></a>';
            $rollback = "<a class=\"icon_title2\" title=\"Click here to see all approved records to rollback.\" style=\"margin-right: 5px;\" onclick=\"javascript:expandcollapsepanel_rolback(this ,'tasklisting_rollback" . $value->id . "', 'mainsingnimg_rollback" . $value->id . "'," . $value->id . ')"><i id="mainsingnimg_rollback' . $value->id . '" class="la la-history"></i></a>';
        } else {
            $update = '';
            $rollback = '';
        }

        $Quickedit_startDate = date('Y-m-d H:i', strtotime($value->dtDateTime));
        $Quickedit_endDate = !empty($value->dtEndDateTime) ? date('Y-m-d H:i', strtotime($value->dtEndDateTime)) : 'No Expiry';
        $title = $value->varTitle;
        if (Auth::user()->can('pages-edit')) {
            if ($value->chrDraft == 'D' || $value->chrAddStar == 'Y') {
                $viewlink = url('/previewpage?url=' . url($value->alias->varAlias) . '/' . $value->id . '/preview');
                $linkviewLable = "Preview";
            } else {
                $viewlink = url($value->alias->varAlias);
                $linkviewLable = "View";
            }
            if ($value->chrLock != 'Y') {
                if ($this->currentUserRoleData->chrIsAdmin == 'Y') {
                    $title = '<div class="quick_edit"><a href = "' . route('powerpanel.pages.edit', array('alias' => $value->id)) . '?tab=R">' . $value->varTitle . '</a> <div class="quick_edit_menu">
	                            <span><a href="' . route('powerpanel.pages.edit', array('alias' => $value->id)) . '?tab=R" title="Edit">Edit</a></span>';
                    if ($value->alias->varAlias != 'home') {
                        if ($value->modules->varModuleName == 'pages') {
                            if (Config::get('Constant.DEFAULT_TRASH') == 'Y') {
                                $title .= '<span><a title = "Trash" href = \'javascript:;\' onclick=\'Trashfun("' . $value->id . '")\' class="red" data-tab="R">Trash</a></span>';
                            }
                        }
                    }
                    $title .= '<span><a href = "' . $viewlink . '" target = "_blank" title = "' . $linkviewLable . '" >' . $linkviewLable . '</a></span></div></div>';
                } else {
                    $title = '<div class="quick_edit"><a href = "' . route('powerpanel.pages.edit', array('alias' => $value->id)) . '?tab=R">' . $value->varTitle . '</a> <div class="quick_edit_menu">
	                            <span><a href="' . route('powerpanel.pages.edit', array('alias' => $value->id)) . '?tab=R" title="Edit">Edit</a></span>
	                            <span><a href="' . $viewlink . '" target="_blank" title="' . $linkviewLable . '" >' . $linkviewLable . '</a></span>
	                                </div>    
	                        </div>';
                }
            } else {
                if (auth()->user()->id != $value->LockUserID) {
                    if ($this->currentUserRoleData->chrIsAdmin == 'Y') {
                        $title = '<div class="quick_edit"><a href = "' . route('powerpanel.pages.edit', array('alias' => $value->id)) . '?tab=R">' . $value->varTitle . '</a> <div class="quick_edit_menu">
	                            <span><a href="' . route('powerpanel.pages.edit', array('alias' => $value->id)) . '?tab=R" title="Edit">Edit</a></span>
	                            <span><a href="' . $viewlink . '" target="_blank" title="' . $linkviewLable . '" >' . $linkviewLable . '</a></span>
	                                </div>    
	                        </div>';
                    } else {
                        $title = '<div class="quick_edit"><a href = "javascript:;">' . $value->varTitle . '</a></div>';
                    }
                } else {
                    $title = '<div class="quick_edit"><a href = "' . route('powerpanel.pages.edit', array('alias' => $value->id)) . '?tab=R">' . $value->varTitle . '</a> <div class="quick_edit_menu">
	                            <span><a href="' . route('powerpanel.pages.edit', array('alias' => $value->id)) . '?tab=R" title="Edit">Edit</a></span>
	                            <span><a href="' . $viewlink . '" target="_blank" title="' . $linkviewLable . '" >' . $linkviewLable . '</a></span>
	                                </div>    
	                        </div>';
                }
            }
        }
        if ($this->currentUserRoleData->chrIsAdmin == 'Y') {
            if (!in_array($value->intFKModuleCode, $ignoreModuleIds)) {
                
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

        $First_td = '<div class="star_box">' . $pbtn . '</div>';
        if ($value->updated_at == '-0001-11-30 00:00:00') {
            $udate = '---';
        } else {
            $udate = date('' . Config::get('Constant.DEFAULT_DATE_FORMAT') . ' ' . Config::get('Constant.DEFAULT_TIME_FORMAT') . '', strtotime($value->updated_at));
        }
        $logurl = url('powerpanel/log?id=' . $value->id . '&mid=' . Config::get('Constant.MODULE.ID'));
        if ($actions == "") {
            $actions = "---";
        } else {
            $actions = $actions;
        }
        $log = '';

        if ($value->chrLock != 'Y') {
            if (Config::get('Constant.DEFAULT_ARCHIVE') == 'Y') {
                $log .= "<a title=\"UnArchive\" href='javascript:;' onclick='UnArchivefun(\"$value->id\",\"R\")'><i class=\"fa fa-archive\"></i></a>";
            }
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

        $records = array(
            $checkbox,
            $First_td,
            '<div class="pages_title_div_row">' . $title . ' ' . $status . $statusdata . '</div>',
            $manageRecordsLink,
            $startDate,
            $endDate,
            $webHits,
            $udate,
            $log,
        );
        return $records;
    }

    public function tableDataDraft($value = false, $ignoreModuleIds = false) {
        $Hits = Pagehit::where('fkIntAliasId', $value->intAliasId)->count();
        $webHits = '';
        if ($Hits > 0) {
            $webHits .= '<a data-toggle="modal" href="#" onclick=\'HitsPopup("' . $value->id . '","' . $value->intAliasId . '","' . $value->varTitle . '","R")\'>' . $Hits . '</a>
                    <div class="new_modal modal fade" id="desc_' . $value->id . '_R" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog" style="margin: 0 auto;display:table;width: 100%;height:100%;max-width: 1000px;">
                        <div class="modal-vertical">
                        <div class="modal-content">
                    <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                    <h3 class="modal-title">Hits Report</h3>
                    </div>
                    <div class="modal-body">
                    <div id="webdata_' . $value->id . '_R"></div>
                    </div>
                    </div>
                    </div>
                    </div>
                    </div>';
        } else {
            $webHits .= '0';
        }
        $publish_action = '';
        $actions = '';
        if ($value->modules->varModuleName == 'pages' || $value->modules->varModuleName == 'sitemap') {
            $manageRecordsLink = $value->modules->varTitle;
        } else {
            if ($this->currentUserRoleData->chrIsAdmin == 'Y') {
                $manageRecordsLink = '<a class = "" title = "Manage Records" href = "' . url('powerpanel/' . $value->modules->varModuleName) . '">Manage Records</a>';
            } else {
                $manageRecordsLink = $value->modules->varTitle;
            }
        }
        if (Auth::user()->can('pages-edit')) {
            $actions .= '<a class = "" title = "' . trans('cmspage::template.common.edit') . '" href = "' . route('powerpanel.pages.edit', array('alias' => $value->id)) . '?tab=D"><i class = "fa fa-pencil"></i></a>';
        }
        if ($value->id != 1) {
            $startDate = $value->dtDateTime;
            $endDate = $value->dtEndDateTime;
            $startDate = date('' . Config::get('Constant.DEFAULT_DATE_FORMAT') . ' ' . Config::get('Constant.DEFAULT_TIME_FORMAT') . '', strtotime($startDate));
            $endDate = !empty($value->dtEndDateTime) ? date('' . Config::get('Constant.DEFAULT_DATE_FORMAT') . ' ' . Config::get('Constant.DEFAULT_TIME_FORMAT') . '', strtotime($value->dtEndDateTime)) : 'No Expiry';
        } else {
            $startDate = '-';
            $endDate = '-';
        }
        if ($value->chrAddStar != 'Y') {
            if ($value->alias->varAlias != 'home') {
                if ($value->chrPublish == 'Y') {
                    $publish_action .= '<input data-off-text = "No" data-on-text = "Yes" class = "make-switch pub" data-off-color = "info" data-on-color = "primary" type = "checkbox" data-controller = "powerpanel/pages" title = "' . trans('cmspage::template.common.publishedRecord') . '" data-value = "Unpublish" data-alias = "' . $value->id . '">';
                } else {
                    $publish_action .= '<input checked = "" data-off-text = "No" data-on-text = "Yes" class = "make-switch pub" class = "make-switch publish" data-off-color = "info" data-on-color = "primary" type = "checkbox" data-controller = "powerpanel/pages" title = "' . trans('cmspage::template.common.unpublishedRecord') . '" data-value = "Publish" data-alias = "' . $value->id . '">';
                }
            }
        } else {
            $publish_action .= '---';
        }
        if (Auth::user()->can('pages-delete') && $this->currentUserRoleData->chrIsAdmin == 'Y') {
            if ($value->alias->varAlias != 'home') {
                if ($value->modules->varModuleName == 'pages') {
                    if (Config::get('Constant.DEFAULT_TRASH') == 'Y') {
                        $actions .= '<a class = "delete-grid" title = "Trash" href = \'javascript:;\' onclick=\'Trashfun("' . $value->id . '")\' data-controller = "pages" data-alias = "' . $value->id . '" data-tab = "D"><i class = "fa fa-times"></i></a>';
                    } else {
                        $actions .= '<a class = " delete" title = "' . trans('cmspage::template.common.delete') . '" data-controller = "pages" data-alias = "' . $value->id . '" data-tab = "D"><i class = "fa fa-times"></i></a>';
                    }
                }
            }
        }
        $checkbox = '<div class="checker"><a href = "javascript:;" data-toggle = "tooltip" data-placement = "right" data-toggle = "tooltip" title = "This is module page so can&#39;t be deleted."><i style = "color:red" class = "fa fa-exclamation-triangle"></i></a></div>';
        if ($value->modules->varModuleName == 'pages') {
            if ($value->alias->varAlias != 'home') {
                $checkbox = '<input type = "checkbox" name = "delete" class = "chkDelete" value = "' . $value->id . '">';
            }
        }
        if (Auth::user()->can('pages-reviewchanges')) {
            $update = "<a class=\"icon_title1\" title=\"Click here to see all approval records.\" style=\"margin-right: 5px;\" onclick=\"javascript:expandcollapsepanel(this ,'tasklisting" . $value->id . "', 'mainsingnimg" . $value->id . "'," . $value->id . ')"><i id="mainsingnimg' . $value->id . '" class="la la-plus-square"></i></a>';
            $rollback = "<a class=\"icon_title2\" title=\"Click here to see all approved records to rollback.\" style=\"margin-right: 5px;\" onclick=\"javascript:expandcollapsepanel_rolback(this ,'tasklisting_rollback" . $value->id . "', 'mainsingnimg_rollback" . $value->id . "'," . $value->id . ')"><i id="mainsingnimg_rollback' . $value->id . '" class="la la-history"></i></a>';
        } else {
            $update = '';
            $rollback = '';
        }
        $Quickedit_startDate = date('Y-m-d H:i', strtotime($value->dtDateTime));
        $Quickedit_endDate = !empty($value->dtEndDateTime) ? date('Y-m-d H:i', strtotime($value->dtEndDateTime)) : 'No Expiry';
        $title = $value->varTitle;
        if (Auth::user()->can('pages-edit')) {
            if ($value->chrDraft == 'D' || $value->chrAddStar == 'Y') {
                $viewlink = url('/previewpage?url=' . url($value->alias->varAlias) . '/' . $value->id . '/preview');
                $linkviewLable = "Preview";
            } else {
                $viewlink = url($value->alias->varAlias);
                $linkviewLable = "View";
            }

            if ($value->chrLock != 'Y') {
                if ($this->currentUserRoleData->chrIsAdmin == 'Y') {
                    $title = '<div class="quick_edit"><a href = "' . route('powerpanel.pages.edit', array('alias' => $value->id)) . '?tab=D">' . $value->varTitle . '</a> <div class="quick_edit_menu">
	                            <span><a href="' . route('powerpanel.pages.edit', array('alias' => $value->id)) . '?tab=D" title="Edit">Edit</a></span>';
                    if ($value->alias->varAlias != 'home') {
                        if ($value->modules->varModuleName == 'pages') {
                            if (Config::get('Constant.DEFAULT_TRASH') == 'Y') {
                                $title .= '<span><a title = "Trash" href = \'javascript:;\' onclick=\'Trashfun("' . $value->id . '")\' class="red" data-tab="D">Trash</a></span>';
                            }
                        }
                    }

                    $title .= '<span><a href = "' . $viewlink . '" target = "_blank" title = "' . $linkviewLable . '" >' . $linkviewLable . '</a></span></div></div>';
                } else {
                    $title = '<div class="quick_edit"><a href = "' . route('powerpanel.pages.edit', array('alias' => $value->id)) . '?tab=D">' . $value->varTitle . '</a> <div class="quick_edit_menu">
	                            <span><a href="' . route('powerpanel.pages.edit', array('alias' => $value->id)) . '?tab=D" title="Edit">Edit</a></span>
	                            <span><a href="' . $viewlink . '" target="_blank" title="' . $linkviewLable . '" >' . $linkviewLable . '</a></span>
	                                </div>    
	                        </div>';
                }
            } else {
                if (auth()->user()->id != $value->LockUserID) {
                    if ($this->currentUserRoleData->chrIsAdmin == 'Y') {
                        $title = '<div class="quick_edit"><a href = "' . route('powerpanel.pages.edit', array('alias' => $value->id)) . '?tab=D">' . $value->varTitle . '</a> <div class="quick_edit_menu">
	                            <span><a href="' . route('powerpanel.pages.edit', array('alias' => $value->id)) . '?tab=D" title="Edit">Edit</a></span>';

                        $title .= '<span><a href = "' . $viewlink . '" target = "_blank" title = "' . $linkviewLable . '" >' . $linkviewLable . '</a></span></div></div>';
                    } else {
                        $title = '<div class="quick_edit"><a href = "javascript:;">' . $value->varTitle . '</a></div>';
                    }
                } else {
                    $title = '<div class="quick_edit"><a href = "' . route('powerpanel.pages.edit', array('alias' => $value->id)) . '?tab=D">' . $value->varTitle . '</a> <div class="quick_edit_menu">
	                            <span><a href="' . route('powerpanel.pages.edit', array('alias' => $value->id)) . '?tab=D" title="Edit">Edit</a></span>
	                            <span><a href="' . $viewlink . '" target="_blank" title="' . $linkviewLable . '" >' . $linkviewLable . '</a></span>
	                                </div>    
	                        </div>';
                }
            }
        }
        if ($this->currentUserRoleData->chrIsAdmin == 'Y') {
            if (!in_array($value->intFKModuleCode, $ignoreModuleIds)) {
                
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
                $Favorite = '<a class="star_icon_div" href="javascript:;" onclick="GetFavorite(' . $value->id . ',\'N\',\'D\')"><i class="' . $Class . '"></i></a>';
            } else {
                $Class = 'la la-star-o';
                $Favorite = '<a class="star_icon_div" href="javascript:;" onclick="GetFavorite(' . $value->id . ',\'Y\',\'D\')"><i class="' . $Class . '"></i></a>';
            }
        } else {
            $Favorite = '';
        }
        $First_td = '<div class="star_box">' . $pbtn . '</div>';
        if ($value->updated_at == '-0001-11-30 00:00:00') {
            $udate = '---';
        } else {
            $udate = date('' . Config::get('Constant.DEFAULT_DATE_FORMAT') . ' ' . Config::get('Constant.DEFAULT_TIME_FORMAT') . '', strtotime($value->updated_at));
        }
        $logurl = url('powerpanel/log?id=' . $value->id . '&mid=' . Config::get('Constant.MODULE.ID'));

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
        if ($value->chrArchive == 'Y') {
            $status .= Config::get('Constant.ARCHIVE_LIST') . ' ';
        }
        $frontMenu =  Menu::select('intAliasId')->where('intAliasId',$value->intAliasId)->first();
        $records = array(
            $checkbox,
            '<div class="pages_title_div_row">'.$First_td . '<input type="hidden" id="draftid" value="' . $value->id . '">' . $title . ' ' . $status . $statusdata . '</div>',
            $manageRecordsLink,
            $startDate,
            $endDate,
            $webHits,
            ($frontMenu == null) ? $publish_action : "---",
            $udate,
            $log,
        );
        return $records;
    }

    public function tableData_tab1($value = false) {
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
        $publish_action = '';
        $actions = '';
        if ($value->modules->varModuleName == 'pages' || $value->modules->varModuleName == 'sitemap') {
            $manageRecordsLink = $value->modules->varTitle;
        } else {
            if ($this->currentUserRoleData->chrIsAdmin == 'Y') {
                $manageRecordsLink = '<a class = "" title = "Manage Records" href = "' . url('powerpanel/' . $value->modules->varModuleName) . '">Manage Records</a>';
            } else {
                $manageRecordsLink = $value->modules->varTitle;
            }
        }
        if (Auth::user()->can('pages-edit')) {
            $actions .= '<a class = "" title = "' . trans('cmspage::template.common.edit') . '" href = "' . route('powerpanel.pages.edit', array('alias' => $value->id)) . '?tab=A"><i class = "fa fa-pencil"></i></a>';
        }
        if (Auth::user()->can('pages-delete') && $this->currentUserRoleData->chrIsAdmin == 'Y') {
            if ($value->alias->varAlias != 'home') {
                if ($value->modules->varModuleName == 'pages') {
                    if (Config::get('Constant.DEFAULT_TRASH') == 'Y') {
                        $actions .= '<a class = "delete-grid" title = "Trash" href = \'javascript:;\' onclick=\'Trashfun("' . $value->id . '")\' data-controller = "pages" data-alias = "' . $value->id . '" data-tab = "A"><i class = "fa fa-times"></i></a>';
                    } else {
                        $actions .= '<a class = "delete" title = "' . trans('cmspage::template.common.delete') . '" data-controller = "pages" data-alias = "' . $value->id . '" data-tab = "A"><i class = "fa fa-times"></i></a>';
                    }
                }
            }
        }
        if ($value->id != 1) {
            $startDate = $value->dtDateTime;
            $endDate = $value->dtEndDateTime;
            $startDate = date('' . Config::get('Constant.DEFAULT_DATE_FORMAT') . ' ' . Config::get('Constant.DEFAULT_TIME_FORMAT') . '', strtotime($startDate));
            $endDate = !empty($value->dtEndDateTime) ? date('' . Config::get('Constant.DEFAULT_DATE_FORMAT') . ' ' . Config::get('Constant.DEFAULT_TIME_FORMAT') . '', strtotime($value->dtEndDateTime)) : 'No Expiry';
        } else {
            $startDate = '-';
            $endDate = '-';
        }
        $checkbox = '<a href = "javascript:;" data-toggle = "tooltip" data-placement = "right" data-toggle = "tooltip" title = "This is module page so can&#39;t be deleted."><i style = "color:red" class = "fa fa-exclamation-triangle"></i></a>';
        if ($value->modules->varModuleName == 'pages') {
            if ($value->alias->varAlias != 'home') {
                $checkbox = '<input type = "checkbox" name = "delete" class = "chkDelete" value = "' . $value->id . '">';
            }
        }
        if (Auth::user()->can('pages-reviewchanges')) {
            $update = "<a class=\"icon_title1\" title=\"Click here to see all approval records.\" style=\"margin-right: 5px;\" onclick=\"javascript:expandcollapsepanel(this ,'tasklisting" . $value->id . "', 'mainsingnimg" . $value->id . "'," . $value->id . ')"><i id="mainsingnimg' . $value->id . '" class="la la-plus-square"></i></a>';
            $rollback = "<a class=\"icon_title2\" title=\"Click here to see all approved records to rollback.\"  style=\"margin-right: 5px;\" onclick=\"javascript:expandcollapsepanel_rolback(this ,'tasklisting_rollback" . $value->id . "', 'mainsingnimg_rollback" . $value->id . "'," . $value->id . ')"><i id="mainsingnimg_rollback' . $value->id . '" class="la la-history"></i></a>';
        } else {
            $update = '';
            $rollback = '';
        }
        if (Auth::user()->can('pages-reviewchanges') && $value->chrAddStar == 'Y') {
            $star = 'addhiglight';
        } else {
            $star = '';
        }
        $Quickedit_startDate = date('Y-m-d H:i', strtotime($value->dtDateTime));
        $Quickedit_endDate = !empty($value->dtEndDateTime) ? date('Y-m-d H:i', strtotime($value->dtEndDateTime)) : 'No Expiry';
        $title = $value->varTitle;
        if (Auth::user()->can('pages-edit')) {
            if ($value->chrDraft == 'D' || $value->chrAddStar == 'Y') {
                $viewlink = url('/previewpage?url=' . url($value->alias->varAlias) . '/' . $value->id . '/preview');
                $linkviewLable = "Preview";
            } else {
                $viewlink = url($value->alias->varAlias);
                $linkviewLable = "View";
            }
            if ($value->chrLock != 'Y') {
                $title = '<div class="quick_edit"><a href = "' . route('powerpanel.pages.edit', array('alias' => $value->id)) . '?tab=A">' . $value->varTitle . '</a> <div class="quick_edit_menu">
	                            <span><a href="' . route('powerpanel.pages.edit', array('alias' => $value->id)) . '?tab=A" title="Edit">Edit</a></span>';
                if ($value->alias->varAlias != 'home') {
                    if ($value->modules->varModuleName == 'pages') {
                        if (Config::get('Constant.DEFAULT_TRASH') == 'Y') {
                            $title .= '<span><a title = "Trash" href = \'javascript:;\' onclick=\'Trashfun("' . $value->id . '")\' class="red" data-tab="A">Trash</a></span>';
                        }
                    }
                }
                $title .= '<span><a href="' . $viewlink . '" target="_blank" title="' . $linkviewLable . '" >' . $linkviewLable . '</a></span></div></div>';
            } else {
                if (auth()->user()->id != $value->LockUserID) {
                    if ($this->currentUserRoleData->chrIsAdmin == 'Y') {
                        $title = '<div class="quick_edit"><a href = "' . route('powerpanel.pages.edit', array('alias' => $value->id)) . '?tab=A">' . $value->varTitle . '</a> <div class="quick_edit_menu">
	                            <span><a href="' . route('powerpanel.pages.edit', array('alias' => $value->id)) . '?tab=A" title="Edit">Edit</a></span>';

                        $title .= '<span><a href="' . $viewlink . '" target="_blank" title="' . $linkviewLable . '" >' . $linkviewLable . '</a></span></div></div>';
                    } else {
                        $title = '<div class="quick_edit"><a href = "javascript:;">' . $value->varTitle . '</a></div>';
                    }
                } else {
                    $title = '<div class="quick_edit"><a href = "' . route('powerpanel.pages.edit', array('alias' => $value->id)) . '?tab=A">' . $value->varTitle . '</a> <div class="quick_edit_menu">
	                            <span><a href="' . route('powerpanel.pages.edit', array('alias' => $value->id)) . '?tab=A" title="Edit">Edit</a></span>';

                    $title .= '<span><a href="' . $viewlink . '" target="_blank" title="' . $linkviewLable . '" >' . $linkviewLable . '</a></span></div></div>';
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
        if ($value->updated_at == '-0001-11-30 00:00:00') {
            $udate = '---';
        } else {
            $udate = date('' . Config::get('Constant.DEFAULT_DATE_FORMAT') . ' ' . Config::get('Constant.DEFAULT_TIME_FORMAT') . '', strtotime($value->updated_at));
        }
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
        if ($value->chrArchive == 'Y') {
            $status .= Config::get('Constant.ARCHIVE_LIST') . ' ';
        }

        $records = array(
            $First_td,
            '<div class="pages_title_div_row">' . $update . $rollback . $title . ' ' . $status . $statusdata . '</div>',
            $manageRecordsLink,
            $startDate,
            $endDate,
            $webHits,
            $udate,
            $log,
        );
//}
        return $records;
    }

    /**
     * This method handels logs History records.
     *
     * @param $data
     *
     * @return HTML
     *
     * @since   2017-07-27
     *
     * @author  NetQuick
     */
    public function recordHistory($data = false) {
        $startDate = date('' . Config::get('Constant.DEFAULT_DATE_FORMAT') . ' ' . Config::get('Constant.DEFAULT_TIME_FORMAT') . '', strtotime($data->dtDateTime));
        $endDate = !empty($data->dtEndDateTime) ? date('' . Config::get('Constant.DEFAULT_DATE_FORMAT') . ' ' . Config::get('Constant.DEFAULT_TIME_FORMAT') . '', strtotime($data->dtEndDateTime)) : 'No Expiry';
        
        if(isset($data->txtDescription) && $data->txtDescription != ''){
            $desc = FrontPageContent_Shield::renderBuilder($data->txtDescription);
            if(isset($desc['response']) && !empty($desc['response'])) {  
                $desc = $desc['response'];
            }else{
                $desc = '---';    
            } 
        }else{
            $desc = '---';
        }
       
        $returnHtml = '';
        $returnHtml .= '<table class = "new_table_desing table table-striped table-bordered table-hover">
				<thead>
				<tr>
				<th align="center">' . trans('cmspage::template.common.title') . '</th>
				<th align="center">' . trans('cmspage::template.common.modulename') . '</th>
				<th align="center">' . trans('cmspage::template.common.content') . '</th>
                                <th align="center">Start date</th>
                                <th align="center">End date</th>
                                <th align="center">Meta Title</th>
                                <th align="center">Meta Description</th>
				<th align="center">' . trans('cmspage::template.common.publish') . '</th>
				</tr>
				</thead>
				<tbody>
				<tr>
				<td align="center">' . stripslashes($data->varTitle) . '</td>
				<td align="center">' . $data->modules->varModuleName . '</td>
				<td align="center">' . $desc . '</td>
                <td align="center">' . $startDate . '</td>
				<td align="center">' . $endDate . '</td>
				<td align="center">' . $data->varMetaTitle . '</td>
				<td align="center">' . $data->varMetaDescription . '</td>
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
        if ($data->modules->varModuleName != $newdata->modules->varModuleName) {
            $modulecolor = 'style="background-color:#f5efb7"';
        } else {
            $modulecolor = '';
        }
        if ($data->txtDescription != $newdata->txtDescription) {
            $desccolor = 'style="background-color:#f5efb7"';
        } else {
            $desccolor = '';
        }
        if ($data->varMetaTitle != $newdata->varMetaTitle) {
            $metatitlecolor = 'style=background-color:#f5efb7"';
        } else {
            $metatitlecolor = '';
        }
        if ($data->varMetaDescription != $newdata->varMetaDescription) {
            $metadesccolor = 'style="background-color:#f5efb7"';
        } else {
            $metadesccolor = '';
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
        if(isset($newdata->txtDescription) && $newdata->txtDescription != ''){
            $desc = FrontPageContent_Shield::renderBuilder($newdata->txtDescription);
            if(isset($desc['response']) && !empty($desc['response'])) {  
                $desc = $desc['response'];
            }else{
                $desc = '---';    
            } 
        }else{
            $desc = '---';
        }
        $startDate = date('' . Config::get('Constant.DEFAULT_DATE_FORMAT') . ' ' . Config::get('Constant.DEFAULT_TIME_FORMAT') . '', strtotime($newdata->dtDateTime));
        $endDate = !empty($newdata->dtEndDateTime) ? date('' . Config::get('Constant.DEFAULT_DATE_FORMAT') . ' ' . Config::get('Constant.DEFAULT_TIME_FORMAT') . '', strtotime($newdata->dtEndDateTime)) : 'No Expiry';
        $returnHtml = '';
        $returnHtml .= '<table class = "new_table_desing table table-striped table-bordered table-hover">
				<thead>
				<tr>
				<th align="center">' . trans('cmspage::template.common.title') . '</th>
				<th align="center">' . trans('cmspage::template.common.modulename') . '</th>
				<th align="center">' . trans('cmspage::template.common.content') . '</th>
                                <th align="center">Start date</th>
                                <th align="center">End date</th>
                                <th align="center">Meta Title</th>
                                <th align="center">Meta Description</th>
				<th align="center">' . trans('cmspage::template.common.publish') . '</th>
				</tr>
				</thead>
				<tbody>
				<tr>
				<td align="center" ' . $titlecolor . '>' . stripslashes($newdata->varTitle) . '</td>
				<td align="center" ' . $modulecolor . '>' . $newdata->modules->varModuleName . '</td>
				<td align="center" ' . $desccolor . '>' . $desc . '</td>
                                <td align="center" ' . $DateTimecolor . '>' . $startDate . '</td>
                                <td align="center" ' . $EndDateTimecolor . '>' . $endDate . '</td>
                                <td align="center" ' . $varMetaTitlecolor . '>' . $newdata->varMetaTitle . '</td>
                                <td align="center" ' . $varMetaDescriptioncolor . '>' . $newdata->varMetaDescription . '</td>
				<td align="center" ' . $Publishcolor . '>' . $newdata->chrPublish . '</td>
				</tr>
				</tbody>
				</table>';
        return $returnHtml;
    }

    public function flushCache() {
        Cache::forget('getPageByPageId');
    }

    public function addPreview(Request $request, Guard $auth) {
        $data = Request::input();
        $rules = array(
            'title' => 'required|max:160',
            'module' => 'required',
            'varMetaTitle' => 'max:500',
            'varMetaDescription' => 'max:500',
            'chrMenuDisplay' => 'required',
            'alias' => 'required',
        );
        $validator = Validator::make($data, $rules);
        if ($validator->passes()) {
            if (Config::get('Constant.DEFAULT_VISUAL') == 'Y') {
                if ($data['section'] != '[]') {
                    $vsection = $data['section'];
                } else {
                    $vsection = '';
                }
            } else {
                $vsection = $data['contents'];
            }
            $moduleCode = $data['module'];
            $cmsPageArr = [];
            $cmsPageArr['varTitle'] = stripslashes(trim($data['title']));
            $cmsPageArr['intFKModuleCode'] = $moduleCode;
            $cmsPageArr['txtDescription'] = $vsection;
            $cmsPageArr['chrPublish'] = $data['chrMenuDisplay'];
            $cmsPageArr['varMetaTitle'] = stripslashes(trim($data['varMetaTitle']));
            $cmsPageArr['varMetaDescription'] = stripslashes(trim($data['varMetaDescription']));
            $cmsPageArr['UserID'] = auth()->user()->id;
            $id = $data['previewId'];
            if (is_numeric($id) && !empty($id)) {
//Edit post Handler=======
                $cmsPage = CmsPage::getRecordById($id);
                if ($data['oldAlias'] != $data['alias']) {
                    Alias::updateAlias($data['oldAlias'], $data['alias']);
                }
                $whereConditions = ['id' => $cmsPage->id];
                if ($this->currentUserRoleData->chrIsAdmin == 'Y') {
                    if ($cmsPage->fkMainRecord == '0') {
                        $cmsPageArr['chrIsPreview'] = 'Y';
                        $update = CommonModel::updateRecords($whereConditions, $cmsPageArr,false, 'Powerpanel\CmsPage\Models\CmsPage');
                        if ($update) {
                            $newCmsPageObj = CmsPage::getRecordById($cmsPage->id);
//Update record in menu
                            $whereConditions = ['txtPageUrl' => $data['oldAlias']];
                            $updateMenuFields = [
                                'varTitle' => stripslashes($newCmsPageObj->varTitle),
                                'txtPageUrl' => $newCmsPageObj->alias->varAlias,
                                'chrPublish' => $data['chrMenuDisplay'],
                                'chrActive' => $data['chrMenuDisplay'],
                            ];
//Update record in menu
                            if (Auth::user()->can('recent-updates-list')) {
                                $notificationArr = MyLibrary::notificationData($cmsPage->id, $newCmsPageObj);
                                RecentUpdates::setNotification($notificationArr);
                            }
                            self::flushCache();
                        }
                    } else {
                        $cmsPage = '';
                        $data_child_record = Request::input();
                        if (Config::get('Constant.DEFAULT_VISUAL') == 'Y') {
                            if ($data_child_record['section'] != '[]') {
                                $vsection = $data_child_record['section'];
                            } else {
                                $vsection = '';
                            }
                        } else {
                            $vsection = $data_child_record['contents'];
                        }
                        $id = $data['previewId'];
                        $cmsPage = CmsPage::getRecordById($id);
                        $whereConditions = ['id' => $data_child_record['fkMainRecord']];
                        $cmsPageArr_child['varTitle'] = stripslashes(trim($data_child_record['title']));
                        $cmsPageArr_child['intFKModuleCode'] = trim($data_child_record['module']);
                        $cmsPageArr_child['txtDescription'] = trim($vsection);
                        $cmsPageArr_child['varMetaTitle'] = stripslashes(trim($data_child_record['varMetaTitle']));
                        $cmsPageArr_child['varMetaDescription'] = stripslashes(trim($data_child_record['varMetaDescription']));
                        $cmsPageArr_child['chrAddStar'] = 'N';
                        $cmsPageArr_child['chrPublish'] = trim($data_child_record['chrMenuDisplay']);
                        $cmsPageArr['chrIsPreview'] = 'Y';
                        $update = CommonModel::updateRecords($whereConditions, $cmsPageArr_child,false, 'Powerpanel\CmsPage\Models\CmsPage');
                        $whereConditions_ApproveN = ['fkMainRecord' => $data_child_record['fkMainRecord']];
                        $updateToApproveN = [
                            'chrApproved' => 'N',
                            'intApprovedBy' => '0',
                        ];
                        CommonModel::updateRecords($whereConditions_ApproveN, $updateToApproveN,false, 'Powerpanel\CmsPage\Models\CmsPage');
                        $whereConditionsApprove = ['id' => $id, 'chrMain' => 'N'];
                        $updateToApprove = [
                            'chrApproved' => 'Y',
                            'chrRollBack' => 'Y',
                            'intApprovedBy' => auth()->user()->id,
                        ];
                        CommonModel::updateRecords($whereConditionsApprove, $updateToApprove,false, 'Powerpanel\CmsPage\Models\CmsPage');
                    }
                } else {
                    $cmsPageArr['intAliasId'] = MyLibrary::insertAlias($data['alias'], false, 'Y');
                    $cmsPageArr['chrMain'] = 'N';
                    $cmsPageArr['chrIsPreview'] = 'Y';
                    $cmsPageArr['fkMainRecord'] = $cmsPage->id;
                    $id = CommonModel::addRecord($cmsPageArr, 'Powerpanel\CmsPage\Models\CmsPage');
                    $whereConditionsAddstar = ['id' => $cmsPage->id];
                    $updateAddStar = [
                        'chrAddStar' => 'Y',
                    ];
                    CommonModel::updateRecords($whereConditionsAddstar, $updateAddStar,false, 'Powerpanel\CmsPage\Models\CmsPage');
                }
            } else {
//Add post Handler=======
                $cmsPageArr['chrMain'] = 'Y';
                $cmsPageArr['intAliasId'] = MyLibrary::insertAlias($data['alias'], false, 'Y');
                $cmsPageArr['created_at'] = Carbon::now();
                $cmsPageArr['updated_at'] = Carbon::now();
                $cmsPageArr['chrIsPreview'] = 'Y';
                $id = CommonModel::addRecord($cmsPageArr,'Powerpanel\CmsPage\Models\CmsPage');
            }
            return json_encode(array('status' => $id, 'alias' => $data['alias'], 'message' => trans('cmspage::template.pageModule.pageUpdate')));
        } else {
            return json_encode(array('status' => 'error', 'message' => $validator->errors()));
        }
    }

    function Template_Listing() {
        $record = Request::input();
        $pagedata = DB::table('visultemplate')
                ->select('*')
                ->where('id', '=', $record['id'])
                ->first();
        if ($record['temp'] == 'Y') {
            $temp = 'Y';
        } else {
            $temp = 'N';
        }
        $response = view('powerpanel.partials.pagetemplatesections', ['sections' => json_decode($pagedata->txtDesc), 'contentavalibale' => $temp])->render();
        return $response;
    }

    function FormBuilder_Listing() {
        $record = Request::input();
        $pagedata = DB::table('form_builder')
                ->select('*')
                ->where('id', '=', $record['id'])
                ->first();
        if ($record['temp'] == 'Y') {
            $temp = 'Y';
        } else {
            $temp = 'N';
        }
        if ($record['temp'] == 'F') {
            $temp = 'F';
            $response = view('powerpanel.partials.pageformbuilderPartitionsections', ['sections' => [$pagedata->id, $pagedata->varName], 'contentavalibale' => $temp])->render();
        } else {
            $response = view('powerpanel.partials.pageformbuildersections', ['sections' => [$pagedata->id, $pagedata->varName], 'contentavalibale' => $temp])->render();
        }
        return $response;
    }

    public function rollBackRecord(Request $request)
    {

        $message = 'Oops! Something went wrong';
        $requestArr = Request::all();
        $request = (object)$requestArr;

        $previousRecord = CmsPage::getPreviousRecordByMainId($request->id);
        if (!empty($previousRecord)) {

            $main_id = $previousRecord->fkMainRecord;
            $request->id = $previousRecord->id;
            $request->main_id = $main_id;

            $message = CmsPage::approved_data_Listing($request);

            $newBlogObj = CmsPage::getRecordForLogById($main_id);
            $restoredata = Config::get('Constant.ROLLBACK_RECORD');

            /* notification for user to record approved */
            $blogs = CmsPage::getRecordForLogById($previousRecord->id);
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