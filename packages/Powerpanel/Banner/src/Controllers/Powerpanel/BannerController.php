<?php

namespace Powerpanel\Banner\Controllers\Powerpanel;

use Request;
use Illuminate\Support\Facades\Redirect;
use Powerpanel\CmsPage\Models\CmsPage;
use App\Modules;
use Powerpanel\Banner\Models\Banner;
use App\Video;
use App\User;
use App\Log;
use App\Blogs;
use App\RecentUpdates;
use App\Alias;
use Powerpanel\Workflow\Models\Comments;
use Powerpanel\Workflow\Models\WorkflowLog;
use Powerpanel\Workflow\Models\Workflow;
use Validator;
use Config;
use DB;
use App\Http\Controllers\PowerpanelController;
use Crypt;
use Auth;
use File;
use App\Helpers\SocialShare;
use App\Helpers\resize_image;
use App\Helpers\AddImageModelRel;
use App\Helpers\MyLibrary;
use App\CommonModel;
use Carbon\Carbon;
use Cache;
use Powerpanel\RoleManager\Models\Role_user;
use App\UserNotification;

class BannerController extends PowerpanelController {

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
    }

    /**
     * This method handels load banner grid
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
        } else {
            $userIsAdmin = true;
        }
        $total = Banner::getRecordCount();
        $draftTotalRecords = Banner::getRecordCountforListDarft(false, true, $userIsAdmin, array());
        $trashTotalRecords = Banner::getRecordCountforListTrash();
        $favoriteTotalRecords = Banner::getRecordCountforListFavorite();
        $archiveTotalRecords = Banner::getRecordCountforListArchive();
        $NewRecordsCount = Banner::getNewRecordsCount();
        $cms_pages = $total > 0 ? CmsPage::getPagesWithModule() : null;
        $this->breadcrumb['title'] = trans('banner::template.bannerModule.manage');
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
        return view('banner::powerpanel.list', ['total' => $total, 'cms_pages' => $cms_pages, 'breadcrumb' => $this->breadcrumb, 'NewRecordsCount' => $NewRecordsCount, 'userIsAdmin' => $userIsAdmin, 'draftTotalRecords' => $draftTotalRecords, 'trashTotalRecords' => $trashTotalRecords, 'favoriteTotalRecords' => $favoriteTotalRecords, 'archiveTotalRecords' => $archiveTotalRecords, 'settingarray' => $settingarray]);
    }

    /**
     * This method handels list of banner with filters
     * @return  View
     * @since   2017-07-20
     * @author  NetQuick
     */
    public function get_list() {
        /* Start code for sorting */
        $filterArr = [];
        $records = array();
        $records["data"] = array();
        $filterArr['orderColumnNo'] = (!empty(Request::input('order') [0]['column']) ? Request::input('order') [0]['column'] : '');
        $filterArr['orderByFieldName'] = (!empty(Request::input('columns') [$filterArr['orderColumnNo']]['name']) ? Request::input('columns') [$filterArr['orderColumnNo']]['name'] : '');
        $filterArr['orderTypeAscOrDesc'] = (!empty(Request::input('order') [0]['dir']) ? Request::input('order') [0]['dir'] : '');
        $filterArr['searchFilter'] = !empty(Request::input('searchValue')) ? Request::input('searchValue') : '';
        $filterArr['statusFilter'] = !empty(Request::input('customActionName')) ? Request::input('customActionName') : '';
        $filterArr['bannerFilter'] = !empty(Request::input('bannerFilter')) ? Request::input('bannerFilter') : '';
        $filterArr['bannerFilterType'] = !empty(Request::input('bannerFilterType')) ? Request::input('bannerFilterType') : '';
        $filterArr['pageFilter'] = !empty(Request::input('pageFilter')) ? Request::input('pageFilter') : '';
        $filterArr['iDisplayLength'] = intval(Request::input('length'));
        $filterArr['iDisplayStart'] = intval(Request::input('start'));
        $sEcho = intval(Request::input('draw'));
        $isAdmin = false;
        if (isset($this->currentUserRoleData) && !empty($this->currentUserRoleData)) {
            if ($this->currentUserRoleData->chrIsAdmin == 'Y') {
                $isAdmin = true;
            }
        }
        $arrResults = Banner::getRecordList($filterArr, $isAdmin);
        $iTotalRecords = Banner::getRecordCountforList($filterArr, true, $isAdmin);
        $end = $filterArr['iDisplayStart'] + $filterArr['iDisplayLength'];
        $end = $end > $iTotalRecords ? $iTotalRecords : $end;
        $totalRecords = Banner::getRecordCount();
        $tableSortedType = (isset($filterArr['orderTypeAscOrDesc']) && $filterArr['orderTypeAscOrDesc'] != "") ? $filterArr['orderTypeAscOrDesc'] : '';
        if (count($arrResults) > 0 && !empty($arrResults)) {
            $homeBannerCount = Banner::homeBannerCount();
            $innerBannerCount = Banner::innerBannerCount();
            foreach ($arrResults as $key => $value) {
                $records["data"][] = $this->tableData($value, $homeBannerCount, $innerBannerCount, $totalRecords, $tableSortedType);
            }
        }
        $NewRecordsCount = Banner::getNewRecordsCount();
        $records["newRecordCount"] = $NewRecordsCount;
        $records["customActionStatus"] = "OK";
        $records["draw"] = $sEcho;
        $records["recordsTotal"] = $iTotalRecords;
        $records["recordsFiltered"] = $iTotalRecords;
        echo json_encode($records);
        exit;
    }

    /**
     * This method handels list of banner with filters
     * @return  View
     * @since   2017-07-20
     * @author  NetQuick
     */
    public function get_list_favorite() {
        /* Start code for sorting */
        $filterArr = [];
        $records = array();
        $records["data"] = array();
        $filterArr['orderColumnNo'] = (!empty(Request::input('order') [0]['column']) ? Request::input('order') [0]['column'] : '');
        $filterArr['orderByFieldName'] = (!empty(Request::input('columns') [$filterArr['orderColumnNo']]['name']) ? Request::input('columns') [$filterArr['orderColumnNo']]['name'] : '');
        $filterArr['orderTypeAscOrDesc'] = (!empty(Request::input('order') [0]['dir']) ? Request::input('order') [0]['dir'] : '');
        $filterArr['searchFilter'] = !empty(Request::input('searchValue')) ? Request::input('searchValue') : '';
        $filterArr['statusFilter'] = !empty(Request::input('customActionName')) ? Request::input('customActionName') : '';
        $filterArr['bannerFilter'] = !empty(Request::input('bannerFilter')) ? Request::input('bannerFilter') : '';
        $filterArr['bannerFilterType'] = !empty(Request::input('bannerFilterType')) ? Request::input('bannerFilterType') : '';
        $filterArr['pageFilter'] = !empty(Request::input('pageFilter')) ? Request::input('pageFilter') : '';
        $filterArr['iDisplayLength'] = intval(Request::input('length'));
        $filterArr['iDisplayStart'] = intval(Request::input('start'));
        $sEcho = intval(Request::input('draw'));
        $isAdmin = false;
        if ($this->currentUserRoleData->chrIsAdmin == 'Y') {
            $isAdmin = true;
        }
        $arrResults = Banner::getRecordListFavorite($filterArr, $isAdmin);
        $iTotalRecords = Banner::getRecordCountforListFavorite($filterArr, true, $isAdmin);
        $end = $filterArr['iDisplayStart'] + $filterArr['iDisplayLength'];
        $end = $end > $iTotalRecords ? $iTotalRecords : $end;
        $totalRecords = Banner::getRecordCount();
        $tableSortedType = (isset($filterArr['orderTypeAscOrDesc']) && $filterArr['orderTypeAscOrDesc'] != "") ? $filterArr['orderTypeAscOrDesc'] : '';
        if (count($arrResults) > 0 && !empty($arrResults)) {
            $homeBannerCount = Banner::homeBannerCount();
            $innerBannerCount = Banner::innerBannerCount();
            foreach ($arrResults as $key => $value) {
                $records["data"][] = $this->tableDataFavorite($value, $homeBannerCount, $innerBannerCount, $totalRecords, $tableSortedType);
            }
        }
        $NewRecordsCount = Banner::getNewRecordsCount();
        $records["newRecordCount"] = $NewRecordsCount;
        $records["customActionStatus"] = "OK";
        $records["draw"] = $sEcho;
        $records["recordsTotal"] = $iTotalRecords;
        $records["recordsFiltered"] = $iTotalRecords;
        echo json_encode($records);
        exit;
    }

    public function get_list_archive() {
        /* Start code for sorting */
        $filterArr = [];
        $records = array();
        $records["data"] = array();
        $filterArr['orderColumnNo'] = (!empty(Request::input('order') [0]['column']) ? Request::input('order') [0]['column'] : '');
        $filterArr['orderByFieldName'] = (!empty(Request::input('columns') [$filterArr['orderColumnNo']]['name']) ? Request::input('columns') [$filterArr['orderColumnNo']]['name'] : '');
        $filterArr['orderTypeAscOrDesc'] = (!empty(Request::input('order') [0]['dir']) ? Request::input('order') [0]['dir'] : '');
        $filterArr['searchFilter'] = !empty(Request::input('searchValue')) ? Request::input('searchValue') : '';
        $filterArr['statusFilter'] = !empty(Request::input('customActionName')) ? Request::input('customActionName') : '';
        $filterArr['bannerFilter'] = !empty(Request::input('bannerFilter')) ? Request::input('bannerFilter') : '';
        $filterArr['bannerFilterType'] = !empty(Request::input('bannerFilterType')) ? Request::input('bannerFilterType') : '';
        $filterArr['pageFilter'] = !empty(Request::input('pageFilter')) ? Request::input('pageFilter') : '';
        $filterArr['iDisplayLength'] = intval(Request::input('length'));
        $filterArr['iDisplayStart'] = intval(Request::input('start'));
        $sEcho = intval(Request::input('draw'));
        $isAdmin = false;
        if ($this->currentUserRoleData->chrIsAdmin == 'Y') {
            $isAdmin = true;
        }
        $arrResults = Banner::getRecordListArchive($filterArr, $isAdmin);
        $iTotalRecords = Banner::getRecordCountforListFavorite($filterArr, true, $isAdmin);
        $end = $filterArr['iDisplayStart'] + $filterArr['iDisplayLength'];
        $end = $end > $iTotalRecords ? $iTotalRecords : $end;
        $totalRecords = Banner::getRecordCount();
        $tableSortedType = (isset($filterArr['orderTypeAscOrDesc']) && $filterArr['orderTypeAscOrDesc'] != "") ? $filterArr['orderTypeAscOrDesc'] : '';
        if (count($arrResults) > 0 && !empty($arrResults)) {
            $homeBannerCount = Banner::homeBannerCount();
            $innerBannerCount = Banner::innerBannerCount();
            foreach ($arrResults as $key => $value) {
                $records["data"][] = $this->tableDataArchive($value, $homeBannerCount, $innerBannerCount, $totalRecords, $tableSortedType);
            }
        }
        $NewRecordsCount = Banner::getNewRecordsCount();
        $records["newRecordCount"] = $NewRecordsCount;
        $records["customActionStatus"] = "OK";
        $records["draw"] = $sEcho;
        $records["recordsTotal"] = $iTotalRecords;
        $records["recordsFiltered"] = $iTotalRecords;
        echo json_encode($records);
        exit;
    }

    /**
     * This method handels list of banner with filters
     * @return  View
     * @since   2017-07-20
     * @author  NetQuick
     */
    public function get_list_draft() {
        /* Start code for sorting */
        $filterArr = [];
        $records = array();
        $records["data"] = array();
        $filterArr['orderColumnNo'] = (!empty(Request::input('order') [0]['column']) ? Request::input('order') [0]['column'] : '');
        $filterArr['orderByFieldName'] = (!empty(Request::input('columns') [$filterArr['orderColumnNo']]['name']) ? Request::input('columns') [$filterArr['orderColumnNo']]['name'] : '');
        $filterArr['orderTypeAscOrDesc'] = (!empty(Request::input('order') [0]['dir']) ? Request::input('order') [0]['dir'] : '');
        $filterArr['searchFilter'] = !empty(Request::input('searchValue')) ? Request::input('searchValue') : '';
        $filterArr['statusFilter'] = !empty(Request::input('customActionName')) ? Request::input('customActionName') : '';
        $filterArr['bannerFilter'] = !empty(Request::input('bannerFilter')) ? Request::input('bannerFilter') : '';
        $filterArr['bannerFilterType'] = !empty(Request::input('bannerFilterType')) ? Request::input('bannerFilterType') : '';
        $filterArr['pageFilter'] = !empty(Request::input('pageFilter')) ? Request::input('pageFilter') : '';
        $filterArr['iDisplayLength'] = intval(Request::input('length'));
        $filterArr['iDisplayStart'] = intval(Request::input('start'));
        $sEcho = intval(Request::input('draw'));
        $isAdmin = false;
        if ($this->currentUserRoleData->chrIsAdmin == 'Y') {
            $isAdmin = true;
        }
        $arrResults = Banner::getRecordListDraft($filterArr, $isAdmin);
        $iTotalRecords = Banner::getRecordCountforListDarft($filterArr, true, $isAdmin);
        $end = $filterArr['iDisplayStart'] + $filterArr['iDisplayLength'];
        $end = $end > $iTotalRecords ? $iTotalRecords : $end;
        $totalRecords = Banner::getRecordCount();
        $tableSortedType = (isset($filterArr['orderTypeAscOrDesc']) && $filterArr['orderTypeAscOrDesc'] != "") ? $filterArr['orderTypeAscOrDesc'] : '';
        if (count($arrResults) > 0 && !empty($arrResults)) {
            $homeBannerCount = Banner::homeBannerCount();
            $innerBannerCount = Banner::innerBannerCount();
            foreach ($arrResults as $key => $value) {
                $records["data"][] = $this->tableDataDraft($value, $homeBannerCount, $innerBannerCount, $totalRecords, $tableSortedType);
            }
        }
        $NewRecordsCount = Banner::getNewRecordsCount();
        $records["newRecordCount"] = $NewRecordsCount;
        $records["customActionStatus"] = "OK";
        $records["draw"] = $sEcho;
        $records["recordsTotal"] = $iTotalRecords;
        $records["recordsFiltered"] = $iTotalRecords;
        echo json_encode($records);
        exit;
    }

    /**
     * This method handels list of banner with filters
     * @return  View
     * @since   2017-07-20
     * @author  NetQuick
     */
    public function get_list_trash() {
        /* Start code for sorting */
        $filterArr = [];
        $records = array();
        $records["data"] = array();
        $filterArr['orderColumnNo'] = (!empty(Request::input('order') [0]['column']) ? Request::input('order') [0]['column'] : '');
        $filterArr['orderByFieldName'] = (!empty(Request::input('columns') [$filterArr['orderColumnNo']]['name']) ? Request::input('columns') [$filterArr['orderColumnNo']]['name'] : '');
        $filterArr['orderTypeAscOrDesc'] = (!empty(Request::input('order') [0]['dir']) ? Request::input('order') [0]['dir'] : '');
        $filterArr['searchFilter'] = !empty(Request::input('searchValue')) ? Request::input('searchValue') : '';
        $filterArr['statusFilter'] = !empty(Request::input('customActionName')) ? Request::input('customActionName') : '';
        $filterArr['bannerFilter'] = !empty(Request::input('bannerFilter')) ? Request::input('bannerFilter') : '';
        $filterArr['bannerFilterType'] = !empty(Request::input('bannerFilterType')) ? Request::input('bannerFilterType') : '';
        $filterArr['pageFilter'] = !empty(Request::input('pageFilter')) ? Request::input('pageFilter') : '';
        $filterArr['iDisplayLength'] = intval(Request::input('length'));
        $filterArr['iDisplayStart'] = intval(Request::input('start'));
        $sEcho = intval(Request::input('draw'));
        $isAdmin = false;
        if ($this->currentUserRoleData->chrIsAdmin == 'Y') {
            $isAdmin = true;
        }
        $arrResults = Banner::getRecordListTrash($filterArr, $isAdmin);
        $iTotalRecords = Banner::getRecordCountforListTrash($filterArr, true, $isAdmin);
        $end = $filterArr['iDisplayStart'] + $filterArr['iDisplayLength'];
        $end = $end > $iTotalRecords ? $iTotalRecords : $end;
        $totalRecords = Banner::getRecordCount();
        $tableSortedType = (isset($filterArr['orderTypeAscOrDesc']) && $filterArr['orderTypeAscOrDesc'] != "") ? $filterArr['orderTypeAscOrDesc'] : '';
        if (count($arrResults) > 0 && !empty($arrResults)) {
            $homeBannerCount = Banner::homeBannerCount();
            $innerBannerCount = Banner::innerBannerCount();
            foreach ($arrResults as $key => $value) {
                $records["data"][] = $this->tableDataTrash($value, $homeBannerCount, $innerBannerCount, $totalRecords, $tableSortedType);
            }
        }
        $NewRecordsCount = Banner::getNewRecordsCount();
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
        $filterArr['orderColumnNo'] = (!empty(Request::input('order') [0]['column']) ? Request::input('order') [0]['column'] : '');
        $filterArr['orderByFieldName'] = (!empty(Request::input('columns') [$filterArr['orderColumnNo']]['name']) ? Request::input('columns') [$filterArr['orderColumnNo']]['name'] : '');
        $filterArr['orderTypeAscOrDesc'] = (!empty(Request::input('order') [0]['dir']) ? Request::input('order') [0]['dir'] : '');
        $filterArr['searchFilter'] = !empty(Request::input('searchValue')) ? Request::input('searchValue') : '';
        $filterArr['statusFilter'] = !empty(Request::input('customActionName')) ? Request::input('customActionName') : '';
        $filterArr['bannerFilter'] = !empty(Request::input('bannerFilter')) ? Request::input('bannerFilter') : '';
        $filterArr['bannerFilterType'] = !empty(Request::input('bannerFilterType')) ? Request::input('bannerFilterType') : '';
        $filterArr['pageFilter'] = !empty(Request::input('pageFilter')) ? Request::input('pageFilter') : '';
        $filterArr['iDisplayLength'] = intval(Request::input('length'));
        $filterArr['iDisplayStart'] = intval(Request::input('start'));
        $sEcho = intval(Request::input('draw'));
        $arrResults = Banner::getRecordList_tab1($filterArr);
        $iTotalRecords = Banner::getRecordCountListApprovalTab($filterArr, true);
        $end = $filterArr['iDisplayStart'] + $filterArr['iDisplayLength'];
        $end = $end > $iTotalRecords ? $iTotalRecords : $end;
        if (count($arrResults) > 0 && !empty($arrResults)) {
            $homeBannerCount = Banner::homeBannerCount();
            $innerBannerCount = Banner::innerBannerCount();
            foreach ($arrResults as $key => $value) {
                $records["data"][] = $this->tableData_tab1($value, $homeBannerCount, $innerBannerCount);
            }
        }
        $NewRecordsCount = Banner::getNewRecordsCount();
        $records["newRecordCount"] = $NewRecordsCount;
        $records["customActionStatus"] = "OK";
        $records["draw"] = $sEcho;
        $records["recordsTotal"] = $iTotalRecords;
        $records["recordsFiltered"] = $iTotalRecords;
        echo json_encode($records);
        exit;
    }

    public function getChildData() {
        $childHtml = "";
        $Cmspage_childData = "";
        $Cmspage_childData = Banner::getChildGrid();
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
                    $childHtml .= "<td class=\"text-center\"><span class='mob_show_title'>Edit: </span><a class='icon_round' title='" . trans("banner::template.common.edit") . "' href='" . route('powerpanel.banners.edit', array('alias' => $child_row->id)) . "'>
														<i class='fa fa-pencil'></i></a></td>";
                } else {
                    $childHtml .= "<td class=\"text-center\"><span class='mob_show_title'>Edit: </span>-</td>";
                }
                if ($child_row->chrApproved == 'N') {
                    $childHtml .= "<td class=\"text-center\"><span class='mob_show_title'>Status: </span> <a class=\"approve_icon_btn\" title='" . trans("banner::template.common.comments") . "' href=\"javascript:;\" onclick=\"loadModelpopup('" . $child_row->id . "','" . $child_row->UserID . "','" . Config::get('Constant.MODULE.MODEL_NAME') . "','" . $child_row->fkMainRecord . "')\"><i class=\"fa fa-comments\"></i> <span>Comment</span></a>    <a  class=\"approve_icon_btn\" onclick=\"update_mainrecord('" . $child_row->id . "','" . $child_row->fkMainRecord . "','" . $child_row->UserID . "','A');\" title='" . trans("banner::template.common.clickapprove") . "' href=\"javascript:;\"><i class=\"fa fa-check-square-o\"></i> <span>Approve</span></a></td>";
                } else {
                    $childHtml .= "<td class=\"text-center\"><span class='mob_show_title'>Status: </span> <span class='mob_show_overflow'><i class=\"la la-check-circle\" style=\"font-size:30px;\"></i><span style=\"display:block\"><strong>Approved On: </strong>" . date('M d Y h:i A', strtotime($child_row->dtApprovedDateTime)) . "</span><span style=\"display:block\"><strong>Approved By: </strong>" . CommonModel::getUserName($child_row->intApprovedBy) . "</span></span></td>";
                }
                $childHtml .= "</tr>";
            }
        } else {
            $childHtml .= "<tr><td colspan='6'>No Records</td></tr>";
        }
        $childHtml .= "</tr></td></tr>";
        $childHtml .= "</tr></table>";
        echo $childHtml;
        exit;
    }

    public function ApprovedData_Listing(Request $request) {
        $requestArr = Request::all();
        $request = (object) $requestArr;
        $id = Request::post('id');
        $main_id = Request::post('main_id');
        $approvalid = Request::post('id');
        $flag = Request::post('flag');
        $approvalData = Banner::getOrderOfApproval($id);
        $message = Banner::approved_data_Listing($request);
        if (!empty($approvalData)) {
            self::swap_order_edit($approvalData->intDisplayOrder, $main_id);
        }
        $newCmsPageObj = Banner::getRecordForLogById($main_id);
        $approval_obj = Banner::getRecordForLogById($approvalid);
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
     * This method loads banner edit view
     * @param   Alias of record
     * @return  View
     * @since   2017-11-10
     * @author  NetQuick
     */
    public function edit($id = false) {
        $ignoreModuleIds = [26,31,34,35,37,42,43,44,45,46,47,48,46,50];
        $module = Modules::getFrontModulesList($ignoreModuleIds);
        $userIsAdmin = false;
        if (isset($this->currentUserRoleData) && !empty($this->currentUserRoleData)) {
            if ($this->currentUserRoleData->chrIsAdmin == 'Y') {
                $userIsAdmin = true;
            }
        } else {
            $userIsAdmin = true;
        }
        if (!is_numeric($id)) {
            $total = Banner::getRecordCount();
            if (isset($this->currentUserRoleData->chrIsAdmin) && $this->currentUserRoleData->chrIsAdmin == 'Y') {
                $total = $total + 1;
            }
            $this->breadcrumb['title'] = trans('banner::template.bannerModule.add');
            $this->breadcrumb['module'] = trans('banner::template.bannerModule.manage');
            $this->breadcrumb['url'] = 'powerpanel/banners';
            $this->breadcrumb['inner_title'] = trans('banner::template.bannerModule.add');
            $breadcrumb = $this->breadcrumb;
            $data = ['modules' => $module, 'total_banner' => $total, 'breadcrumb' => $this->breadcrumb, 'imageManager' => true, 'videoManager' => false, 'userIsAdmin' => $userIsAdmin];
        } else {
            $banners = Banner::getRecordById($id);
            if (empty($banners)) {
                return redirect()->route('powerpanel.banners.add');
            }
            if ($banners->fkMainRecord != '0') {
                $banners_highLight = Banner::getRecordById($banners->fkMainRecord);
                $templateData['banners_highLight'] = $banners_highLight;
                $display_publish = $banners_highLight['chrPublish'];
            } else {
                $banners_highLight = "";
                $templateData['banners_highLight'] = "";
                $display_publish = '';
            }
            $this->breadcrumb['title'] = trans('banner::template.common.edit') . ' - ' . $banners->varTitle;
            $this->breadcrumb['module'] = trans('banner::template.bannerModule.manage');
            $this->breadcrumb['url'] = 'powerpanel/banners';
            $this->breadcrumb['inner_title'] = trans('banner::template.common.edit') . ' - ' . $banners->varTitle;
            $breadcrumb = $this->breadcrumb;
            $data = ['banners' => $banners, 'modules' => $module, 'breadcrumb' => $this->breadcrumb, 'imageManager' => true, 'videoManager' => false, 'banners_highLight' => $banners_highLight, 'display_publish' => $display_publish, 'userIsAdmin' => $userIsAdmin];
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
                if (isset($workFlowByCat->chrNeedAddPermission)) {
                    $data['chrNeedAddPermission'] = $workFlowByCat->chrNeedAddPermission;
                }
                if (isset($workFlowByCat->charNeedApproval)) {
                    $data['charNeedApproval'] = $workFlowByCat->charNeedApproval;
                }
            } else {
                $data['chrNeedAddPermission'] = 'N';
                $data['charNeedApproval'] = 'N';
            }
        } else {
            $data['chrNeedAddPermission'] = 'N';
            $data['charNeedApproval'] = 'N';
        }
        //End Button Name Change For User Side
        $data['MyLibrary'] = $this->MyLibrary;
        return view('banner::powerpanel.actions', $data);
    }

    /**
     * This method stores banner modifications
     * @return  View
     * @since   2017-11-10
     * @author  NetQuick
     */
    public function handlePost(Request $request) {
        $requestArr = Request::all();
        $request = (object) $requestArr;
        $approval = false;
        $userIsAdmin = false;
        if (isset($this->currentUserRoleData) && !empty($this->currentUserRoleData)) {
            if ($this->currentUserRoleData->chrIsAdmin == 'Y') {
                $userIsAdmin = true;
            }
        }
        $postArr = Request::all();
        $bannerFields = [];
        $actionMessage = trans('banner::template.common.oppsSomethingWrong');
        $rules = array(
            'title' => 'required|max:160|handle_xss|no_url',
            'banner_type' => 'required|handle_xss|no_url',
            'display_order' => 'required|greater_than_zero|handle_xss|no_url',
            'chrMenuDisplay' => 'required',
            'link' => 'handle_xss',
            'short_description' => 'handle_xss|no_url'
        );
        if (isset($postArr['banner_type']) && $postArr['banner_type'] != 'home_banner') {
            $rules['modules'] = 'required';
            $rules['foritem'] = 'required';
        }
        if ($postArr['banner_type'] == 'home_banner') {
            $rules['bannerversion'] = 'required';
            if ($postArr['bannerversion'] == 'vid_banner') {
                $rules['video_id'] = 'required';
            } 
        } 
        $messsages = array(
            'title.required' => "Title field is required.",
            // 'img_id.required' => trans('banner::template.bannerModule.bannerValidation'),
            'display_order.required' => trans('banner::template.bannerModule.displayOrder'),
            'display_order.greater_than_zero' => trans('banner::template.bannerModule.displayGreaterThan'),
            'modules.required' => trans('banner::template.bannerModule.moduleValidationMessage'),
            'foritem.required' => trans('banner::template.bannerModule.pageValidationMessage')
        );
        $validator = Validator::make($postArr, $rules, $messsages);
        if ($validator->passes()) {
            $module = Modules::getModuleById(Config::get('Constant.MODULE.ID'));
            $pageId = 0;
            if ($postArr['banner_type'] == 'inner_banner') {
                $moduleId = $postArr['modules'];
                $pageId = $postArr['foritem'];
                $bannerFields['fkIntImgId'] = $postArr['img_id'];
                $bannerFields['varVideoTitle'] = !empty($postArr['videotitle']) ? $postArr['videotitle'] : null;
                $bannerFields['varLink'] = !empty($postArr['link']) ? $postArr['link'] : null;
                $bannerFields['fkIntVideoId'] = null;
                $bannerFields['varTagLine'] = !empty($postArr['tag_line']) ? $postArr['tag_line'] : null;
                $bannerFields['varButtonName'] = !empty($postArr['button_name']) ? $postArr['button_name'] : null;
                $bannerFields['varLink'] = !empty($postArr['link']) ? $postArr['link'] : null;
            } else {
                $homePage = CmsPage::getHomePage();
                if (!empty($homePage->id)) {
                    $moduleId = $homePage->modules->id;
                    $pageId = $homePage->id;
                }
                if ($postArr['bannerversion'] == 'vid_banner') {
                    $bannerFields['fkIntVideoId'] = $postArr['video_id'];
                    $bannerFields['fkIntImgId'] = null;
                    $moduleId = null;
                } else {
                    $bannerFields['varTagLine'] = !empty($postArr['tag_line']) ? $postArr['tag_line'] : null;
                    $bannerFields['fkIntImgId'] = $postArr['img_id'];
                    $bannerFields['varVideoTitle'] = !empty($postArr['videotitle']) ? $postArr['videotitle'] : null;
                    $bannerFields['varButtonName'] = !empty($postArr['button_name']) ? $postArr['button_name'] : null;
                    $bannerFields['varLink'] = !empty($postArr['link']) ? $postArr['link'] : null;
                    $bannerFields['fkIntVideoId'] = null;
                    $moduleId = null;
                }
            }
            if (isset($postArr['chrDisplayVideo']) && $postArr['chrDisplayVideo'] == 'on') {
                $chrDisplayVideo = 'Y';
            } else {
                $chrDisplayVideo = 'N';
            }
            if (isset($postArr['chrDisplayLink']) && $postArr['chrDisplayLink'] == 'on') {
                $chrDisplayLink = 'Y';
            } else {
                $chrDisplayLink = 'N';
            }
            $bannerFields['varTitle'] = trim($postArr['title']);
            $bannerFields['varTagLine'] = !empty($postArr['tag_line']) ? $postArr['tag_line'] : null;
            $bannerFields['varButtonName'] = !empty($postArr['button_name']) ? $postArr['button_name'] : null;
            $bannerFields['varLink'] = !empty($postArr['link']) ? $postArr['link'] : null;
            $bannerFields['varBannerType'] = $postArr['banner_type'];
            $bannerFields['varBannerVersion'] = $postArr['bannerversion'];
            $bannerFields['chrDisplayVideo'] = $chrDisplayVideo;
            $bannerFields['chrDisplayLink'] = $chrDisplayLink;
            $bannerFields['fkIntPageId'] = $pageId;
            $bannerFields['fkModuleId'] = $moduleId;
            $bannerFields['chrPublish'] = $postArr['chrMenuDisplay'];
            $bannerFields['chrDefaultBanner'] = !empty($postArr['defaultBanner']) ? $postArr['defaultBanner'] : 'N';
            $bannerFields['varShortDescription'] = !empty($postArr['short_description']) ? $postArr['short_description'] : '';
            $bannerFields['varVideoLink'] = $postArr['videolink'];
            if (Config::get('Constant.CHRContentScheduling') == 'Y') {
                $bannerFields['dtDateTime'] = !empty($postArr['start_date_time']) ? date('Y-m-d H:i:s', strtotime($postArr['start_date_time'])) : date('Y-m-d H:i:s');
                $bannerFields['dtEndDateTime'] = !empty($postArr['end_date_time']) ? date('Y-m-d H:i:s', strtotime($postArr['end_date_time'])) : null;
            }
            $bannerFields['UserID'] = auth()->user()->id;
            if ($postArr['chrMenuDisplay'] == 'D') {
                $bannerFields['chrDraft'] = 'D';
                $bannerFields['chrPublish'] = 'N';
            } else {
                $bannerFields['chrDraft'] = 'N';
                $bannerFields['chrPublish'] = $postArr['chrMenuDisplay'];
            }
            if ($postArr['chrMenuDisplay'] == 'D') {
                $addlog = Config::get('Constant.UPDATE_DRAFT');
            } else {
                $addlog = '';
            }
            $id = Request::segment(3);
            if (is_numeric($id)) { #Edit post Handler=======
                $banner = Banner::getRecordForLogById($id);
                $whereConditions = ['id' => $id];
                if ($banner->chrLock == 'Y' && auth()->user()->id != $banner->LockUserID) {
                    if ($this->currentUserRoleData->chrIsAdmin != 'Y') {
                        $lockedUserData = User::getRecordById($banner->LockUserID, true);
                        $lockedUserName = 'someone';
                        if (!empty($lockedUserData)) {
                            $lockedUserName = $lockedUserData->name;
                        }
                        $actionMessage = "This record has been locked by " . $lockedUserName . ".";
                        return redirect()->route('powerpanel.banners.index')->with('message', $actionMessage);
                    }
                }
                if (File::exists(app_path() . '/Workflow.php') != null || File::exists(base_path() . '/packages/Powerpanel/Workflow/src/Models/Workflow.php') != null) {
                    if (!$userIsAdmin) {
                        $userRole = $this->currentUserRoleData->id;
                    } else {
                        $userRoleData = Role_user::getUserRoleByUserId($banner->UserID);
                        if (isset($userRoleData->role_id)) {
                            $userRole = $userRoleData->role_id;
                        } else {
                            $userRole = $this->currentUserRoleData->id;
                        }
                    }
                    $workFlowByCat = Workflow::getRecordByCategoryId($module->intFkGroupCode, $userRole, Config::get('Constant.MODULE.ID'));
                    if (empty($workFlowByCat->varUserId) || $userIsAdmin || $workFlowByCat->charNeedApproval == 'N') {
                        if ((int) $banner->fkMainRecord === 0 || empty($workFlowByCat->varUserId)) {
                            $update = CommonModel::updateRecords($whereConditions, $bannerFields, false, 'Powerpanel\Banner\Models\Banner');
                            if ($update) {
                                if (!empty($id)) {
                                    self::swap_order_edit($postArr['display_order'], $id);
                                    $logArr = MyLibrary::logData($id, false, $addlog);
                                    if (Auth::user()->can('log-advanced')) {
                                        $newBannerObj = Banner::getRecordForLogById($id);
                                        $oldRec = $this->recordHistory($banner);
                                        $newRec = $this->newrecordHistory($banner, $newBannerObj);
                                        $logArr['old_val'] = $oldRec;
                                        $logArr['new_val'] = $newRec;
                                    }
                                    $logArr['varTitle'] = trim($postArr['title']);
                                    Log::recordLog($logArr);
                                    if (Auth::user()->can('recent-updates-list')) {
                                        if (!isset($newBannerObj)) {
                                            $newBannerObj = Banner::getRecordForLogById($id);
                                        }
                                        $notificationArr = MyLibrary::notificationData($id, $newBannerObj);
                                        RecentUpdates::setNotification($notificationArr);
                                    }
                                }
                                self::flushCache();
                                if (isset($postArr['saveandexit']) && $postArr['saveandexit'] == 'approvesaveandexit') {
                                    $actionMessage = trans('banner::template.common.recordApprovalMessage');
                                } else {
                                    $actionMessage = trans('banner::template.bannerModule.updateMessage');
                                }
                            }
                        } else {
                            $updateModuleFields = $bannerFields;
                            $this->insertApprovedRecord($updateModuleFields, $postArr, $id);
                            if (isset($postArr['saveandexit']) && $postArr['saveandexit'] == 'approvesaveandexit') {
                                $actionMessage = trans('banner::template.common.recordApprovalMessage');
                            } else {
                                $actionMessage = trans('banner::template.bannerModule.updateMessage');
                            }
                            $approval = $id;
                        }
                    } else {
                        if ($workFlowByCat->charNeedApproval == 'Y') {
                            $approvalObj = $this->insertApprovalRecord($banner, $postArr, $bannerFields);
                            if (isset($postArr['saveandexit']) && $postArr['saveandexit'] == 'approvesaveandexit') {
                                $actionMessage = trans('banner::template.common.recordApprovalMessage');
                            } else {
                                $actionMessage = trans('banner::template.bannerModule.updateMessage');
                            }
                            $approval = $approvalObj->id;
                        }
                    }
                } else {
                    $update = CommonModel::updateRecords($whereConditions, $bannerFields, false, 'Powerpanel\Banner\Models\Banner');
                    $actionMessage = trans('banner::template.bannerModule.updateMessage');
                }
            } else { #Add post Handler=======
                if (File::exists(app_path() . '/Workflow.php') != null || File::exists(base_path() . '/packages/Powerpanel/Workflow/src/Models/Workflow.php') != null) {
                    $workFlowByCat = Workflow::getRecordByCategoryId($module->intFkGroupCode, $this->currentUserRoleData->id, Config::get('Constant.MODULE.ID'));
                }
                if (!empty($workFlowByCat->varUserId) && $workFlowByCat->chrNeedAddPermission == 'Y' && !$userIsAdmin) {
                    $bannerFields['chrPublish'] = 'N';
                    $bannerFields['chrDraft'] = 'N';
                    $bannerObj = $this->insertNewRecord($postArr, $bannerFields);
                    if ($postArr['chrMenuDisplay'] == 'D') {
                        $bannerFields['chrDraft'] = 'D';
                    }
                    $bannerFields['chrPublish'] = 'Y';
                    $approvalObj = $this->insertApprovalRecord($bannerObj, $postArr, $bannerFields);
                    $approval = $bannerObj->id;
                } else {
                    $bannerObj = $this->insertNewRecord($postArr, $bannerFields);
                    $approval = $bannerObj->id;
                }
                if (isset($postArr['saveandexit']) && $postArr['saveandexit'] == 'approvesaveandexit') {
                    $actionMessage = trans('banner::template.common.recordApprovalMessage');
                } else {
                    $actionMessage = trans('banner::template.bannerModule.addedMessage');
                }
                $id = $bannerObj->id;
            }
            AddImageModelRel::sync(explode(',', $postArr['img_id']), $id, $approval);
            if ((!empty($request->saveandexit) && $request->saveandexit == 'saveandexit') || !$userIsAdmin) {
                if ($postArr['chrMenuDisplay'] == 'D') {
                    return redirect()->route('powerpanel.banners.index', 'tab=D')->with('message', $actionMessage);
                } else {
                    return redirect()->route('powerpanel.banners.index')->with('message', $actionMessage);
                }
            } else {
                return redirect()->route('powerpanel.banners.edit', $id)->with('message', $actionMessage);
            }
        } else {
            return Redirect::back()->withErrors($validator)->withInput();
        }
    }

    public function insertApprovedRecord($updateModuleFields, $postArr, $id) {
        $whereConditions = ['id' => $postArr['fkMainRecord']];
        $updateModuleFields['chrAddStar'] = 'N';
        $update = CommonModel::updateRecords($whereConditions, $updateModuleFields, false, 'Powerpanel\Banner\Models\Banner');
        if ($update) {
            self::swap_order_edit($postArr['display_order'], $postArr['fkMainRecord']);
        }
        $whereConditions_ApproveN = ['fkMainRecord' => $postArr['fkMainRecord']];
        $updateToApproveN = [
            'chrApproved' => 'N',
            'chrLetest' => 'N',
            'intApprovedBy' => '0',
        ];
        CommonModel::updateRecords($whereConditions_ApproveN, $updateToApproveN, false, 'Powerpanel\Banner\Models\Banner');
        $whereConditionsApprove = ['id' => $id, 'chrMain' => 'N'];
        $updateToApprove = [
            'chrApproved' => 'Y',
            'chrRollBack' => 'Y',
            'intApprovedBy' => auth()->user()->id
        ];
        CommonModel::updateRecords($whereConditionsApprove, $updateToApprove, false, 'Powerpanel\Banner\Models\Banner');
        if ($postArr['chrMenuDisplay'] == 'D') {
            $addlog = Config::get('Constant.DRAFT_RECORD_APPROVED');
        } else {
            $addlog = Config::get('Constant.RECORD_APPROVED');
        }
        $newBannerObj = Banner::getRecordForLogById($id);
        $logArr = MyLibrary::logData($id, false, $addlog);
        $logArr['varTitle'] = stripslashes($newBannerObj->varTitle);
        Log::recordLog($logArr);
        if (method_exists($this->MyLibrary, 'userNotificationData')) {
            /* notification for user to record approved */
            $userNotificationArr = MyLibrary::userNotificationData(Config::get('Constant.MODULE.ID'));
            $userNotificationArr['fkRecordId'] = $id;
            $userNotificationArr['txtNotification'] = 'Your request has been approved by ' . ucfirst(auth()->user()->name) . ' (' . ucfirst(Config::get('Constant.MODULE.NAME')) . ')';
            $userNotificationArr['fkIntUserId'] = Auth::user()->id;
            $userNotificationArr['chrNotificationType'] = 'A';
            $userNotificationArr['intOnlyForUserId'] = $newBannerObj->UserID;
            UserNotification::addRecord($userNotificationArr);
            /* notification for user to record approved */
        }
        if ($update) {
            $where = [];
            $flowData = [];
            $flowData['dtYes'] = Config::get('Constant.SQLTIMESTAMP');
            $where['fkModuleId'] = Config::get('Constant.MODULE.ID');
            $where['fkRecordId'] = (isset($postArr['fkMainRecord']) && (int) $postArr['fkMainRecord'] != 0) ? $postArr['fkMainRecord'] : $id;
            $where['dtYes'] = 'null';
            WorkflowLog::updateRecord($flowData, $where);
            self::flushCache();
            $actionMessage = trans('banner::template.bannerModule.updateMessage');
        }
    }

    public function insertApprovalRecord($moduleObj, $postArr, $bannerFields) {
        $response = false;
        $bannerFields['chrMain'] = 'N';
        $bannerFields['chrLetest'] = 'Y';
        $bannerFields['fkMainRecord'] = $moduleObj->id;
        $bannerFields['intDisplayOrder'] = $postArr['display_order'];
        if ($postArr['chrMenuDisplay'] == 'D') {
            $bannerFields['chrDraft'] = 'D';
            $bannerFields['chrPublish'] = 'N';
        } else {
            $bannerFields['chrDraft'] = 'N';
            $bannerFields['chrPublish'] = $postArr['chrMenuDisplay'];
        }
        if (Config::get('Constant.CHRContentScheduling') == 'Y') {
            $bannerFields['dtDateTime'] = !empty($postArr['start_date_time']) ? date('Y-m-d H:i:s', strtotime($postArr['start_date_time'])) : date('Y-m-d H:i:s');
            $bannerFields['dtEndDateTime'] = !empty($postArr['end_date_time']) ? date('Y-m-d H:i:s', strtotime($postArr['end_date_time'])) : null;
        }
        if ($postArr['chrMenuDisplay'] == 'D') {
            $addlog = Config::get('Constant.DRAFT_SENT_FOR_APPROVAL');
        } else {
            $addlog = Config::get('Constant.SENT_FOR_APPROVAL');
        }
        $bannerID = CommonModel::addRecord($bannerFields, 'Powerpanel\Banner\Models\Banner');
        if (!empty($bannerID)) {
            $id = $bannerID;
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
            $newBannerObj = Banner::getRecordForLogById($id);
            $logArr = MyLibrary::logData($id, false, $addlog);
            $logArr['varTitle'] = $newBannerObj->varTitle;
            Log::recordLog($logArr);
            if (Auth::user()->can('recent-updates-list')) {
                $notificationArr = MyLibrary::notificationData($id, $newBannerObj);
                RecentUpdates::setNotification($notificationArr);
            }
            $response = $newBannerObj;
            self::flushCache();
            $actionMessage = trans('banner::template.bannerModule.addedMessage');
        }
        $whereConditionsAddstar = ['id' => $moduleObj->id];
        $updateAddStar = [
            'chrAddStar' => 'Y',
        ];
        CommonModel::updateRecords($whereConditionsAddstar, $updateAddStar, false, 'Powerpanel\Banner\Models\Banner');
        return $response;
    }

    public function insertNewRecord($postArr, $bannerFields) {
        $response = false;
        $bannerFields['intDisplayOrder'] = self::swap_order_add($postArr['display_order']);
        $bannerFields['chrMain'] = 'Y';
        if (Config::get('Constant.CHRContentScheduling') == 'Y') {
            $bannerFields['dtDateTime'] = !empty($postArr['start_date_time']) ? date('Y-m-d H:i:s', strtotime($postArr['start_date_time'])) : date('Y-m-d H:i:s');
            $bannerFields['dtEndDateTime'] = !empty($postArr['end_date_time']) ? date('Y-m-d H:i:s', strtotime($postArr['end_date_time'])) : null;
        }
        if ($postArr['chrMenuDisplay'] == 'D') {
            $bannerFields['chrDraft'] = 'D';
            $bannerFields['chrPublish'] = 'N';
        } else {
            $bannerFields['chrDraft'] = 'N';
        }
        $bannerID = CommonModel::addRecord($bannerFields, 'Powerpanel\Banner\Models\Banner');
        if (!empty($bannerID)) {
            $id = $bannerID;
            $newBannerObj = Banner::getRecordForLogById($id);
            if ($postArr['chrMenuDisplay'] == 'D') {
                $addlog = Config::get('Constant.ADDED_DRAFT');
            } else {
                $addlog = '';
            }
            $logArr = MyLibrary::logData($id, false, $addlog);
            $logArr['varTitle'] = $newBannerObj->varTitle;
            Log::recordLog($logArr);
            if (Auth::user()->can('recent-updates-list')) {
                $notificationArr = MyLibrary::notificationData($id, $newBannerObj);
                RecentUpdates::setNotification($notificationArr);
            }
            $response = $newBannerObj;
            self::flushCache();
            $actionMessage = trans('banner::template.bannerModule.addedMessage');
        }
        return $response;
    }

    public function getChildData_rollback() {
        $child_rollbackHtml = "";
        $Cmspage_rollbackchildData = "";
        $Cmspage_rollbackchildData = Banner::getChildrollbackGrid();
        $child_rollbackHtml .= "<div class=\"producttbl producttb2\" style=\"\">";
        $child_rollbackHtml .= "<table class=\"new_table_desing table table-striped table-bordered table-hover table-checkable dataTable\" id=\"email_log_datatable_ajax\">
				<tr role=\"row\">      
                                <th class=\"text-center\">Title</th>
				<th class=\"text-center\">Date</th>
				<th class=\"text-center\">User</th>				
				<th class=\"text-center\">Status</th>";
        $child_rollbackHtml .= "</tr>";
        if (count($Cmspage_rollbackchildData) > 0) {
            foreach ($Cmspage_rollbackchildData as $child_rollbacrow) {
                $child_rollbackHtml .= "<tr role=\"row\">";
                $child_rollbackHtml .= '<td class="text-center"><span class="mob_show_title">Title: </span>' . $child_rollbacrow->varTitle . '</td>';
                $child_rollbackHtml .= "<td class=\"text-center\"><span class='mob_show_title'>Date: </span>" . date('M d Y h:i A', strtotime($child_rollbacrow->created_at)) . "</td>";
                $child_rollbackHtml .= "<td class=\"text-center\"><span class='mob_show_title'>User: </span>" . CommonModel::getUserName($child_rollbacrow->UserID) . "</td>";
                if ($child_rollbacrow->chrApproved == 'Y') {
                    $child_rollbackHtml .= "<td class=\"text-center\"><span class='mob_show_title'>Status: </span><i class=\"la la-check-circle\" style=\"color: #1080F2;font-size:30px;\"></i></td>";
                } else {
                //     $child_rollbackHtml .= "<td class=\"text-center\"><span class='mob_show_title'>Status: </span><a onclick=\"update_mainrecord('" . $child_rollbacrow->id . "','" . $child_rollbacrow->fkMainRecord . "','" . $child_rollbacrow->UserID . "','R');\"  class=\"approve_icon_btn\">
				// 																		<i class=\"fa fa-history\"></i>  <span>RollBack</span>
                // 																</a></td>";
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

    public function Get_Comments(Request $request) {
        $requestArr = Request::all();
        $request = (object) $requestArr;
        $templateData = Comments::get_comments($request);
        $Comments = "";
        if (!empty($templateData)) {
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
     * This method destroys Banner in multiples
     * @return  Banner index view
     * @since   2016-10-25
     * @author  NetQuick
     */
    public function DeleteRecord(Request $request) {
        $value = Request::input('value');
        $data['ids'] = Request::input('ids');
        if (File::exists(app_path() . '/Comments.php') != null || File::exists(base_path() . '/packages/Powerpanel/Workflow/src/Models/Comments.php') != null) {
            Comments::deleteComments($data['ids'], Config::get('Constant.MODULE.MODEL_NAME'));
        }
        $moduleHaveFields = ['chrMain', 'chrIsPreview'];
        $update = MyLibrary::deleteMultipleRecords($data, $moduleHaveFields, $value, 'Powerpanel\Banner\Models\Banner');
        foreach ($data['ids'] as $ids) {
            $ignoreDeleteScope = true;
            $Deleted_Record = Banner::getRecordById($ids, $ignoreDeleteScope);
            $Cnt_Letest = Banner::getRecordCount_letest($Deleted_Record['fkMainRecord'], $Deleted_Record['id']);
            if ($Cnt_Letest <= 0) {
                $updateLetest = [
                    'chrAddStar' => 'N',
                ];
                $whereConditionsApprove = ['id' => $Deleted_Record['fkMainRecord']];
                CommonModel::updateRecords($whereConditionsApprove, $updateLetest, false, 'Powerpanel\Banner\Models\Banner');
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
     * This method destroys Banner in multiples
     * @return  Banner index view
     * @since   2016-10-25
     * @author  NetQuick
     */
    public function publish(Request $request) {
        $alias = (int) Request::input('alias');
        $val = Request::get('val');
        $update = MyLibrary::setPublishUnpublish($alias, $val, 'Powerpanel\Banner\Models\Banner');
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
        MyLibrary::swapOrder($order, $exOrder, 'Powerpanel\Banner\Models\Banner');
        self::flushCache();
    }

    /**
     * This method assigns default banner flag
     * @return  Banner index view data
     * @since   2016-12-10
     * @author  NetQuick
     */
    public function makeDefault() {
        $id = Request::input('alias');
        $val = Request::input('val');
        if (!empty($val) && ($val == 'rm-default')) {
            $update = Banner::setDefault($id, ['chrDefaultBanner' => 'N']);
            if ($update) {
                $logArr = MyLibrary::logData($id);
                $logArr['action'] = 'remove-default-banner';
                Log::recordLog($logArr);
                self::flushCache();
            }
        }
        if (!empty($val) && ($val == 'default')) {
            $update = Banner::setDefault($id, ['chrDefaultBanner' => 'Y']);
            if ($update) {
                $logArr = MyLibrary::logData($id);
                $logArr['action'] = 'made-default-banner';
                Log::recordLog($logArr);
                self::flushCache();
            }
        }
        echo json_encode($update);
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
        if ($order != null) {
            $response = MyLibrary::swapOrderAdd($order, $isCustomizeModule, false, 'Powerpanel\Banner\Models\Banner');
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
        MyLibrary::swapOrderEdit($order, $id, false, false, 'Powerpanel\Banner\Models\Banner');
        self::flushCache();
    }

    /**
     * This method handels getting category and it's records (ajax)  
     * @return      JSON object
     * @since       2016-12-23
     * @author      NetQuick
     */
    public static function selectRecords() {
        $data = Request::input();
        $module = (isset($data['module'])) ? $data['module'] : '';
        $selected = (isset($data['selected']) && $data['selected'] != "") ? $data['selected'] : '';
        $recordSelect = '<option value=" ">--' . trans('banner::template.bannerModule.selectPage') . '--</option>';
        if ($module != "") {
            $module = Modules::getModule($module);
            if ($module->varModuleNameSpace != '') {
                $model = $module->varModuleNameSpace . 'Models\\' . $data['model'];
            } else {
                $model = '\\App\\' . $data['model'];
            }
//            $model = '\\App\\' . $data['model'];
//            $module = Modules::getModule($module);
//            if ($module->varModuleName == "pages") {
//                $moduleRec = $model::getPagesWithModule();
//                foreach ($moduleRec as $record) {
//                    if (strtolower($record->varTitle) != 'home') {
//                        if (Auth::user()->can($record->modules->varModuleName . '-list')) {
//                            $recordSelect .= '<option data-moduleid="' . $module->id . '" value="' . $record->id . '" ' . ($record->id == $selected ? 'selected' : '') . '>' . ucwords($record->varTitle) . '</option>';
//                        }
//                    }
//                }
//            } else {
            if (isset($module->id)) {
                $moduleRec = $model::getRecordList();
                foreach ($moduleRec as $record) {
                    if (isset($record->id)) {
                        if (strtolower($record->varTitle) != 'home') {
                            $recordSelect .= '<option data-moduleid="' . $module->id . '" value="' . $record->id . '" ' . ($record->id == $selected ? 'selected' : '') . '>' . ucwords($record->varTitle) . '</option>';
                        }
                    }
                }
            }
        }
        return $recordSelect;
    }

    public function tableData_tab1($value, $iTotalHomeBannerRecords, $iTotalInnerBannerRecords) {
        $image = '';
        $actions = '';
        $banner_type = '';
        $checkbox = '';
        $publish_action = '';
        if (Auth::user()->can('banners-edit')) {
            $actions .= '<a class="" title="' . trans("template.common.edit") . '" href="' . route('powerpanel.banners.edit', array('alias' => $value->id)) . '">
								<span><i class="fa fa-pencil"></i></a>';
        }
        if ($value->varBannerType == 'home_banner') {
            if (($iTotalHomeBannerRecords > 1)) {
                if (Auth::user()->can('banners-delete') && $this->currentUserRoleData->chrIsAdmin == 'Y') {
                    if (Config::get('Constant.DEFAULT_TRASH') == 'Y') {
                        $actions .= '<a title = "Trash" class="delete-grid" onclick = \'Trashfun("' . $value->id . '")\' data-controller = "banners" data-alias = "' . $value->id . '" data-tab = "A"><i class = "fa fa-times"></i></a>';
                    } else {
                        $actions .= '<a class = "delete" title = "' . trans('banner::template.common.delete') . '" data-controller = "banners" data-alias = "' . $value->id . '" data-tab = "A"><i class = "fa fa-times"></i></a>';
                    }
                }
                $checkbox = '<input type="checkbox" name="delete" class="chkDelete" value="' . $value->id . '">';
            } else {
                $checkbox = '<a href="javascript:;" data-toggle="tooltip" data-placement="right" data-toggle="tooltip" title="This is default banner so can&#39;t be deleted."><i style="color:red" class="fa fa-exclamation-triangle"></i></a>';
            }
        }
        if ($value->varBannerType == 'inner_banner') {
            if ($value->chrDefaultBanner == 'Y') {
                if (($iTotalInnerBannerRecords > 1)) {
                    if (Auth::user()->can('banners-delete') && $this->currentUserRoleData->chrIsAdmin == 'Y') {
                        if (Config::get('Constant.DEFAULT_TRASH') == 'Y') {
                            $actions .= '<a title = "Trash" class="delete-grid" onclick = \'Trashfun("' . $value->id . '")\' data-controller = "banners" data-alias = "' . $value->id . '" data-tab = "A"><i class = "fa fa-times"></i></a>';
                        } else {
                            $actions .= '<a class = "delete" title = "' . trans('banner::template.common.delete') . '" data-controller = "banners" data-alias = "' . $value->id . '" data-tab = "A"><i class = "fa fa-times"></i></a>';
                        }
                    }
                    $checkbox = '<input type="checkbox" name="delete" class="chkDelete" value="' . $value->id . '">';
                } else {
                    $checkbox = '<a href="javascript:;" data-toggle="tooltip" data-placement="right" data-toggle="tooltip" title="This is default banner so can&#39;t be deleted."><i style="color:red" class="fa fa-exclamation-triangle"></i></a>';
                }
            } else {
                if (Auth::user()->can('banners-delete') && $this->currentUserRoleData->chrIsAdmin == 'Y') {
                    if (Config::get('Constant.DEFAULT_TRASH') == 'Y') {
                        $actions .= '<a title = "Trash" class="delete-grid" onclick = \'Trashfun("' . $value->id . '")\' data-controller = "banners" data-alias = "' . $value->id . '" data-tab = "A"><i class = "fa fa-times"></i></a>';
                    } else {
                        $actions .= '<a class = "delete" title = "' . trans('banner::template.common.delete') . '" data-controller = "banners" data-alias = "' . $value->id . '" data-tab = "A"><i class = "fa fa-times"></i></a>';
                    }
                }
                $checkbox = '<input type="checkbox" name="delete" class="chkDelete" value="' . $value->id . '">';
            }
        }

        $pageName = '';
        if ($value->varBannerType != "home_banner") {
            if ($value->modules->varTitle != 'Pages') {
                $pageName = isset($value->modules->varTitle) && strlen($value->modules->varTitle) > 0 ? $value->modules->varTitle : 'Default';
            } else {
                $pageName = isset($value->pages->varTitle) && strlen($value->pages->varTitle) > 0 ? $value->pages->varTitle : 'Default';
            }
        } else {
            $pageName = 'Home';
        }
        if (isset($value->pages->varTitle) && strtolower($value->pages->varTitle) != 'home') {
            if ($value->chrDefaultBanner == 'Y') {
                $actions .= '<a class=" defaultBanner" data-controller="powerpanel/banners" title="' . trans("template.common.removeDefault") . '" data-value="rm-default" data-alias="' . $value->id . '"><i class="fa fa-ban" aria-hidden="true"></i></a>';
            } else {
                $actions .= '<a class=" defaultBanner" data-controller="powerpanel/banners" title="' . trans("template.common.makeDefault") . '" data-value="default" data-alias="' . $value->id . '"><i class="fa fa-check" aria-hidden="true"></i></a>';
            }
        }
        $image .= '<div class="text-center">';
        if (!empty($value->image)) {
            $image .= '<a href="' . resize_image::resize($value->fkIntImgId) . '" class="fancybox-buttons" data-rel="fancybox-buttons">';
            $image .= '<img height="30" width="30" title="' . preg_replace('/[^A-Za-z0-9\-]/', '-', $value->varTitle) . '" src="' . resize_image::resize($value->fkIntImgId, 50, 50) . '"/>';
            $image .= '</a>';
        } else {
            $image .= '<span class="glyphicon glyphicon-minus"></span>';
        }
        $image .= '</div>';
        if ($value->varBannerType == 'home_banner') {
            $banner_type = 'Home Banner';
        } else {
            $banner_type = 'Inner Banner';
        }
        if (Auth::user()->can('banners-reviewchanges')) {
            $update = "<a title=\"Click here to see all approval records.\" class=\"icon_title1\" style=\"margin-right: 5px;\" onclick=\"javascript:expandcollapsepanel(this ,'tasklisting" . $value->id . "', 'mainsingnimg" . $value->id . "'," . $value->id . ")\"><i id=\"mainsingnimg" . $value->id . "\" class=\"la la-plus-square\"></i></a>";
            $rollback = "<a title=\"Click here to see all approved records to rollback.\" class=\"icon_title2\" style=\"margin-right: 5px;\" onclick=\"javascript:expandcollapsepanel_rolback(this ,'tasklisting_rollback" . $value->id . "', 'mainsingnimg_rollback" . $value->id . "'," . $value->id . ")\"><i id=\"mainsingnimg_rollback" . $value->id . "\" class=\"la la-history\"></i></a>";
        } else {
            $update = '';
            $rollback = '';
        }
        if (Auth::user()->can('banners-reviewchanges') && $value->chrAddStar == 'Y') {
            $star = 'addhiglight';
        } else {
            $star = '';
        }
        $startDate = date('' . Config::get('Constant.DEFAULT_DATE_FORMAT') . ' ' . Config::get('Constant.DEFAULT_TIME_FORMAT') . '', strtotime($value->dtDateTime));
        $endDate = !empty($value->dtEndDateTime) ? date('' . Config::get('Constant.DEFAULT_DATE_FORMAT') . ' ' . Config::get('Constant.DEFAULT_TIME_FORMAT') . '', strtotime($value->dtEndDateTime)) : 'No Expiry';
        $Quickedit_startDate = date('Y-m-d H:i', strtotime($value->dtDateTime));
        $Quickedit_endDate = !empty($value->dtEndDateTime) ? date('Y-m-d H:i', strtotime($value->dtEndDateTime)) : 'No Expiry';
        $title = $value->varTitle;
        if (Auth::user()->can('banners-edit')) {
            if ($value->chrLock != 'Y') {
                $title = '<div class="quick_edit"><a href = "' . route('powerpanel.banners.edit', array('alias' => $value->id)) . '?tab=A">' . $value->varTitle . '</a> <div class="quick_edit_menu">
                            <span><a href="' . route('powerpanel.banners.edit', array('alias' => $value->id)) . '?tab=A" title="Edit">Edit</a></span>';
                if (Config::get('Constant.DEFAULT_TRASH') == 'Y') {
                    $title .= '<span><a title = "Trash" href = \'javascript:;\' onclick=\'Trashfun("' . $value->id . '")\' class="red" data-tab="A">Trash</a></span>';
                }
                $title .= '</div></div>';
            } else {
                if (auth()->user()->id != $value->LockUserID) {
                    if ($this->currentUserRoleData->chrIsAdmin == 'Y') {
                        $title = '<div class="quick_edit"><a href = "' . route('powerpanel.banners.edit', array('alias' => $value->id)) . '?tab=A">' . $value->varTitle . '</a> <div class="quick_edit_menu">
	                            <span><a href="' . route('powerpanel.banners.edit', array('alias' => $value->id)) . '?tab=A" title="Edit">Edit</a></span></div></div>';
                    } else {
                        $title = '<div class="quick_edit"><a href = "javascript:;">' . $value->varTitle . '</a></div>';
                    }
                } else {
                    $title = '<div class="quick_edit"><a href = "' . route('powerpanel.banners.edit', array('alias' => $value->id)) . '?tab=A">' . $value->varTitle . '</a> <div class="quick_edit_menu">
	                            <span><a href="' . route('powerpanel.banners.edit', array('alias' => $value->id)) . '?tab=A" title="Edit">Edit</a></span></div></div>';
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
        if (Auth::user()->can('banners-reviewchanges')) {
            $log .= "<a title='Rollback to previous version'  onclick=\"rollbackToPreviousVersion('" . $value->id . "');\"  class=\"log-grid\"><i class=\"fa fa-history\"></i></a>";
        }
        $records = array(
            $First_td,
            $update . $rollback . $title . ' ' . $status . $statusdata,
            $image,
            $banner_type,
            $pageName,
            $startDate,
            $log
        );
        return $records;
    }

    public function tableData($value, $iTotalHomeBannerRecords, $iTotalInnerBannerRecords, $totalRecord = false, $tableSortedType = 'asc') {
        $image = '';
        $actions = '';
        $banner_type = '';
        $checkbox = '';
        $publish_action = '';
        if (Auth::user()->can('banners-edit')) {
            $actions .= '<a class="" title="' . trans("template.common.edit") . '" href="' . route('powerpanel.banners.edit', array('alias' => $value->id)) . '">
								<span><i class="fa fa-pencil"></i></a>';
        }
        if ($value->varBannerType == 'home_banner') {
            if (($iTotalHomeBannerRecords > 1)) {
                if (Auth::user()->can('banners-delete') || (isset($this->currentUserRoleData->chrIsAdmin) && $this->currentUserRoleData->chrIsAdmin == 'Y')) {
                    if (Config::get('Constant.DEFAULT_TRASH') == 'Y') {
                        $actions .= '<a title = "Trash" class="delete-grid" onclick = \'Trashfun("' . $value->id . '")\' data-controller = "banners" data-alias = "' . $value->id . '" data-tab = "P"><i class = "fa fa-times"></i></a>';
                    } else {
                        $actions .= '<a class = "delete" title = "' . trans('banner::template.common.delete') . '" data-controller = "banners" data-alias = "' . $value->id . '" data-tab = "P"><i class = "fa fa-times"></i></a>';
                    }
                }
                $checkbox = '<input type="checkbox" name="delete" class="chkDelete" value="' . $value->id . '">';
            } else {
                $checkbox = '<a href="javascript:;" data-toggle="tooltip" data-placement="right" data-toggle="tooltip" title="This is default banner so can&#39;t be deleted."><i style="color:red" class="fa fa-exclamation-triangle"></i></a>';
            }
        }
        if ($value->varBannerType == 'inner_banner') {
            if ($value->chrDefaultBanner == 'Y') {
                if (($iTotalInnerBannerRecords > 1)) {
                    if (Auth::user()->can('banners-delete') || (isset($this->currentUserRoleData->chrIsAdmin) && $this->currentUserRoleData->chrIsAdmin == 'Y')) {
                        if (Config::get('Constant.DEFAULT_TRASH') == 'Y') {
                            $actions .= '<a title = "' . trans('banner::template.common.delete') . '" class="delete-grid" onclick = \'Trashfun("' . $value->id . '")\' data-controller = "banners" data-alias = "' . $value->id . '" data-tab = "P"><i class = "fa fa-times"></i></a>';
                        } else {
                            $actions .= '<a class = "delete" title = "' . trans('banner::template.common.delete') . '" data-controller = "banners" data-alias = "' . $value->id . '" data-tab = "P"><i class = "fa fa-times"></i></a>';
                        }
                    }
                    $checkbox = '<input type="checkbox" name="delete" class="chkDelete" value="' . $value->id . '">';
                } else {
                    $checkbox = '<a href="javascript:;" data-toggle="tooltip" data-placement="right" data-toggle="tooltip" title="This is default banner so can&#39;t be deleted."><i style="color:red" class="fa fa-exclamation-triangle"></i></a>';
                }
            } else {
                if (Auth::user()->can('banners-delete') || (isset($this->currentUserRoleData->chrIsAdmin) && $this->currentUserRoleData->chrIsAdmin == 'Y')) {
                    if (Config::get('Constant.DEFAULT_TRASH') == 'Y') {
                        $actions .= '<a title = "' . trans('banner::template.common.delete') . '" class="delete-grid" onclick = \'Trashfun("' . $value->id . '")\' data-controller = "banners" data-alias = "' . $value->id . '" data-tab = "P"><i class = "fa fa-times"></i></a>';
                    } else {
                        $actions .= '<a class = "delete" title = "' . trans('banner::template.common.delete') . '" data-controller = "banners" data-alias = "' . $value->id . '" data-tab = "P"><i class = "fa fa-times"></i></a>';
                    }
                }
                $checkbox = '<input type="checkbox" name="delete" class="chkDelete" value="' . $value->id . '">';
            }
        }
        if ($value->chrAddStar != 'Y') {
            if ($value->chrDraft != 'D') {
                if (Auth::user()->can('banners-publish')) {
                    if ($value->chrPublish == 'Y') {
                        $publish_action .= '<input  data-off-text="No" data-on-text="Yes" class="make-switch publish" class="make-switch publish" data-off-color="info" data-on-color="primary" type="checkbox" data-controller="powerpanel/banners" title="' . trans("template.common.publishedRecord") . '" data-value="Unpublish" data-alias="' . $value->id . '">';
                    } else {
                        $publish_action .= '<input checked="" data-off-text="No" data-on-text="Yes" class="make-switch publish" class="make-switch publish" data-off-color="info" data-on-color="primary" type="checkbox" data-controller="powerpanel/banners" title="' . trans("template.common.unpublishedRecord") . '" data-value="Publish" data-alias="' . $value->id . '">';
                    }
                }
            } else {
                $publish_action .= '<input checked="" data-off-text="No" data-on-text="Yes" class="make-switch pub" data-off-color="info" data-on-color="primary" type="checkbox" data-controller="powerpanel/banners" title="' . trans("template.common.unpublishedRecord") . '" data-value="Publish" data-alias="' . $value->id . '">';
            }
        } else {
            $publish_action .= '---';
        }
        $pageName = '';
        if ($value->varBannerType != "home_banner") {
            if (isset($value->modules->varTitle)) {
                if ($value->modules->varTitle != 'Pages') {
                    $pageName = isset($value->modules->varTitle) && strlen($value->modules->varTitle) > 0 ? $value->modules->varTitle : 'Default';
                } else {
                    $pageName = isset($value->pages->varTitle) && strlen($value->pages->varTitle) > 0 ? $value->pages->varTitle : 'Default';
                }
            }
        } else {
            $pageName = 'Home';
        }
        if (isset($value->pages->varTitle) && strtolower($value->pages->varTitle) != 'home') {
            if ($value->chrDefaultBanner == 'Y') {
                $actions .= '<a class=" defaultBanner" data-controller="powerpanel/banners" title="' . trans("template.common.removeDefault") . '" data-value="rm-default" data-alias="' . $value->id . '"><i class="fa fa-ban" aria-hidden="true"></i></a>';
            } else {
                //$actions .= '<a class=" defaultBanner" data-controller="powerpanel/banners" title="' . trans("template.common.makeDefault") . '" data-value="default" data-alias="' . $value->id . '"><i class="fa fa-check" aria-hidden="true"></i></a>';
            }
        }
        $image .= '<div class="text-center">';
        if (!empty($value->image)) {
            $image .= '<a href="' . resize_image::resize($value->fkIntImgId) . '" class="fancybox-buttons" data-rel="fancybox-buttons">';
            $image .= '<img height="30" width="30" title="' . preg_replace('/[^A-Za-z0-9\-]/', '-', $value->varTitle) . '" src="' . resize_image::resize($value->fkIntImgId, 50, 50) . '"/>';
            $image .= '</a>';
        } else {
            $image .= '<span class="glyphicon glyphicon-minus"></span>';
        }
        $image .= '</div>';
        if ($value->varBannerType == 'home_banner') {
            $banner_type = 'Home Banner';
        } else {
            $banner_type = 'Inner Banner';
        }
        if (Auth::user()->can('banners-reviewchanges')) {
            $update = "<a title=\"Click here to see all approval records.\" class=\"icon_title1\" style=\"margin-right: 5px;\" onclick=\"javascript:expandcollapsepanel(this ,'tasklisting" . $value->id . "', 'mainsingnimg" . $value->id . "'," . $value->id . ")\"><i id=\"mainsingnimg" . $value->id . "\" class=\"la la-plus-square\"></i></a>";
            $rollback = "<a title=\"Click here to see all approved records to rollback.\" class=\"icon_title2\" style=\"margin-right: 5px;\" onclick=\"javascript:expandcollapsepanel_rolback(this ,'tasklisting_rollback" . $value->id . "', 'mainsingnimg_rollback" . $value->id . "'," . $value->id . ")\"><i id=\"mainsingnimg_rollback" . $value->id . "\" class=\"la la-history\"></i></a>";
        } else {
            $update = '';
            $rollback = '';
        }
        $startDate = date('' . Config::get('Constant.DEFAULT_DATE_FORMAT') . ' ' . Config::get('Constant.DEFAULT_TIME_FORMAT') . '', strtotime($value->dtDateTime));
        $endDate = !empty($value->dtEndDateTime) ? date('' . Config::get('Constant.DEFAULT_DATE_FORMAT') . ' ' . Config::get('Constant.DEFAULT_TIME_FORMAT') . '', strtotime($value->dtEndDateTime)) : 'No Expiry';
        $Quickedit_startDate = date('Y-m-d H:i', strtotime($value->dtDateTime));
        $Quickedit_endDate = !empty($value->dtEndDateTime) ? date('Y-m-d H:i', strtotime($value->dtEndDateTime)) : 'No Expiry';
        if (isset($value->varTitle)) {
            $title = $value->varTitle;
        }
        if (Auth::user()->can('banners-edit')) {
            if ($value->chrLock != 'Y') {
                if (isset($this->currentUserRoleData->chrIsAdmin) && $this->currentUserRoleData->chrIsAdmin == 'Y') {
                    $title = '<div class="quick_edit"><a href = "' . route('powerpanel.banners.edit', array('alias' => $value->id)) . '?tab=P">' . $value->varTitle . '</a>';
                    if (Config::get('Constant.DEFAULT_QUICK') == 'Y') {
                        $title .= '<span><a title="Quick Edit" href=\'javascript:;\' data-toggle=\'modal\' data-target=\'#modalForm\' aria-label=\'Quick edit\' onclick=\'Quickeditfun("' . $value->id . '","' . $value->varTitle . '","' . $value->intSearchRank . '","' . $Quickedit_startDate . '","' . $Quickedit_endDate . '","P")\'>Quick Edit</a></span>';
                    }
                    if (Config::get('Constant.DEFAULT_TRASH') == 'Y') {
                        $title .= '<span><a title = "Trash" href = \'javascript:;\' onclick=\'Trashfun("' . $value->id . '")\' class="red" data-tab="P">Trash</a></span>';
                    }
                    if (Config::get('Constant.DEFAULT_ARCHIVE') == 'Y') {
                        $title .= '<span><a title = "Archive" href = \'javascript:;\' onclick="Archivefun(' . $value->id . ',\'N\',\'P\')"   data-tab="P">Archive</a></span>';
                    }
                    $title .= '</div>    
                       </div>';
                } else {
                    $title = '<div class="quick_edit"><a href = "' . route('powerpanel.banners.edit', array('alias' => $value->id)) . '?tab=P">' . $value->varTitle . '</a> <div class="quick_edit_menu">
                            <span><a href="' . route('powerpanel.banners.edit', array('alias' => $value->id)) . '?tab=P" title="Edit">Edit</a></span>
                                </div>';
                }
            } else {
                if (auth()->user()->id != $value->LockUserID) {
                    if (isset($this->currentUserRoleData->chrIsAdmin) && $this->currentUserRoleData->chrIsAdmin == 'Y') {
                        $title = '<div class="quick_edit"><a href = "' . route('powerpanel.banners.edit', array('alias' => $value->id)) . '?tab=P">' . $value->varTitle . '</a> <div class="quick_edit_menu">
                            <span><a href="' . route('powerpanel.banners.edit', array('alias' => $value->id)) . '?tab=P" title="Edit">Edit</a></span>
                                </div>    
                       </div>';
                    } else {
                        $title = '<div class="quick_edit"><a href = "javascript:;">' . $value->varTitle . '</a></div>';
                    }
                } else {
                    $title = '<div class="quick_edit"><a href = "' . route('powerpanel.banners.edit', array('alias' => $value->id)) . '?tab=P">' . $value->varTitle . '</a> <div class="quick_edit_menu">
                            <span><a href="' . route('powerpanel.banners.edit', array('alias' => $value->id)) . '?tab=P" title="Edit">Edit</a></span>
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
                   // $log .= "<a title=\"Log History\" class='log-grid' href=\"$logurl\"><i class=\"fa fa-clock-o\"></i></a>";
                }
            } else {
                if ($actions == "") {
                    $actions = "---";
                } else {
                    $actions = $actions;
                }
                $log .= $actions;
                if (Auth::user()->can('log-list')) {
                    //$log .= "<a title=\"Log History\" class='log-grid' href=\"$logurl\"><i class=\"fa fa-clock-o\"></i></a>";
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
            if (Auth::user()->can('banners-reviewchanges')) {
            //$log .= "<a title='Rollback to previous version'  onclick=\"rollbackToPreviousVersion('" . $value->id . "');\"  class=\"log-grid\"><i class=\"fa fa-history\"></i></a>";
        }
        }

        $records = array(
            $checkbox,
            $First_td,
            '<div class="pages_title_div_row">'  . $title . '</div>',
           
            $banner_type,
            $pageName,
            // $startDate,
            $orderArrow,
            $publish_action,
            $log,
        );
        return $records;
    }

    public function tableDataFavorite($value, $iTotalHomeBannerRecords, $iTotalInnerBannerRecords, $totalRecord = false, $tableSortedType = 'asc') {
        $image = '';
        $actions = '';
        $banner_type = '';
        $checkbox = '';
        $publish_action = '';
        if (Auth::user()->can('banners-edit')) {
            $actions .= '<a class="" title="' . trans("template.common.edit") . '" href="' . route('powerpanel.banners.edit', array('alias' => $value->id)) . '">
								<span><i class="fa fa-pencil"></i></a>';
        }
        if ($value->varBannerType == 'home_banner') {
            if (($iTotalHomeBannerRecords > 1)) {
                if (Auth::user()->can('banners-delete') && $this->currentUserRoleData->chrIsAdmin == 'Y') {
                    if (Config::get('Constant.DEFAULT_TRASH') == 'Y') {
                        $actions .= '<a title = "' . trans('banner::template.common.delete') . '" class="delete-grid" onclick = \'Trashfun("' . $value->id . '")\' data-controller = "banners" data-alias = "' . $value->id . '" data-tab = "F"><i class = "fa fa-times"></i></a>';
                    } else {
                        $actions .= '<a class = "delete" title = "' . trans('banner::template.common.delete') . '" data-controller = "banners" data-alias = "' . $value->id . '" data-tab = "F"><i class = "fa fa-times"></i></a>';
                    }
                }
                $checkbox = '<input type="checkbox" name="delete" class="chkDelete" value="' . $value->id . '">';
            } else {
                $checkbox = '<a href="javascript:;" data-toggle="tooltip" data-placement="right" data-toggle="tooltip" title="This is default banner so can&#39;t be deleted."><i style="color:red" class="fa fa-exclamation-triangle"></i></a>';
            }
        }
        if ($value->varBannerType == 'inner_banner') {
            if ($value->chrDefaultBanner == 'Y') {
                if (($iTotalInnerBannerRecords > 1)) {
                    if (Auth::user()->can('banners-delete') && $this->currentUserRoleData->chrIsAdmin == 'Y') {
                        if (Config::get('Constant.DEFAULT_TRASH') == 'Y') {
                            $actions .= '<a title = "' . trans('banner::template.common.delete') . '" class="delete-grid" onclick = \'Trashfun("' . $value->id . '")\' data-controller = "banners" data-alias = "' . $value->id . '" data-tab = "F"><i class = "fa fa-times"></i></a>';
                        } else {
                            $actions .= '<a class = "delete" title = "' . trans('banner::template.common.delete') . '" data-controller = "banners" data-alias = "' . $value->id . '" data-tab = "F"><i class = "fa fa-times"></i></a>';
                        }
                    }
                    $checkbox = '<input type="checkbox" name="delete" class="chkDelete" value="' . $value->id . '">';
                } else {
                    $checkbox = '<a href="javascript:;" data-toggle="tooltip" data-placement="right" data-toggle="tooltip" title="This is default banner so can&#39;t be deleted."><i style="color:red" class="fa fa-exclamation-triangle"></i></a>';
                }
            } else {
                if (Auth::user()->can('banners-delete') && $this->currentUserRoleData->chrIsAdmin == 'Y') {
                    if (Config::get('Constant.DEFAULT_TRASH') == 'Y') {
                        $actions .= '<a title = "' . trans('banner::template.common.delete') . '" class="delete-grid" onclick = \'Trashfun("' . $value->id . '")\' data-controller = "banners" data-alias = "' . $value->id . '" data-tab = "F"><i class = "fa fa-times"></i></a>';
                    } else {
                        $actions .= '<a class = "delete delete-grid" title = "' . trans('banner::template.common.delete') . '" data-controller = "banners" data-alias = "' . $value->id . '" data-tab = "F"><i class = "fa fa-times"></i></a>';
                    }
                }
                $checkbox = '<input type="checkbox" name="delete" class="chkDelete" value="' . $value->id . '">';
            }
        }
        if (Auth::user()->can('banners-publish')) {
            if ($value->chrPublish == 'Y') {
                $publish_action .= '<input  data-off-text="No" data-on-text="Yes" class="make-switch publish" class="make-switch publish" data-off-color="info" data-on-color="primary" type="checkbox" data-controller="powerpanel/banners" title="' . trans("template.common.publishedRecord") . '" data-value="Unpublish" data-alias="' . $value->id . '">';
            } else {
                $publish_action .= '<input checked="" data-off-text="No" data-on-text="Yes" class="make-switch publish" class="make-switch publish" data-off-color="info" data-on-color="primary" type="checkbox" data-controller="powerpanel/banners" title="' . trans("template.common.unpublishedRecord") . '" data-value="Publish" data-alias="' . $value->id . '">';
            }
        }
        $pageName = '';
        if ($value->varBannerType != "home_banner") {
            if ($value->modules->varTitle != 'Pages') {
                $pageName = isset($value->modules->varTitle) && strlen($value->modules->varTitle) > 0 ? $value->modules->varTitle : 'Default';
            } else {
                $pageName = isset($value->pages->varTitle) && strlen($value->pages->varTitle) > 0 ? $value->pages->varTitle : 'Default';
            }
        } else {
            $pageName = 'Home';
        }

        $image .= '<div class="text-center">';
        if (!empty($value->image)) {
            $image .= '<a href="' . resize_image::resize($value->fkIntImgId) . '" class="fancybox-buttons" data-rel="fancybox-buttons">';
            $image .= '<img height="30" width="30" title="' . preg_replace('/[^A-Za-z0-9\-]/', '-', $value->varTitle) . '" src="' . resize_image::resize($value->fkIntImgId, 50, 50) . '"/>';
            $image .= '</a>';
        } else {
            $image .= '<span class="glyphicon glyphicon-minus"></span>';
        }
        $image .= '</div>';
        if ($value->varBannerType == 'home_banner') {
            $banner_type = 'Home Banner';
        } else {
            $banner_type = 'Inner Banner';
        }
        if (Auth::user()->can('banners-reviewchanges')) {
            $update = "<a title=\"Click here to see all approval records.\" class=\"icon_title1\" style=\"margin-right: 5px;\" onclick=\"javascript:expandcollapsepanel(this ,'tasklisting" . $value->id . "', 'mainsingnimg" . $value->id . "'," . $value->id . ")\"><i id=\"mainsingnimg" . $value->id . "\" class=\"la la-plus-square\"></i></a>";
            $rollback = "<a title=\"Click here to see all approved records to rollback.\" class=\"icon_title2\" style=\"margin-right: 5px;\" onclick=\"javascript:expandcollapsepanel_rolback(this ,'tasklisting_rollback" . $value->id . "', 'mainsingnimg_rollback" . $value->id . "'," . $value->id . ")\"><i id=\"mainsingnimg_rollback" . $value->id . "\" class=\"la la-history\"></i></a>";
        } else {
            $update = '';
            $rollback = '';
        }
        $startDate = date('' . Config::get('Constant.DEFAULT_DATE_FORMAT') . ' ' . Config::get('Constant.DEFAULT_TIME_FORMAT') . '', strtotime($value->dtDateTime));
        $endDate = !empty($value->dtEndDateTime) ? date('' . Config::get('Constant.DEFAULT_DATE_FORMAT') . ' ' . Config::get('Constant.DEFAULT_TIME_FORMAT') . '', strtotime($value->dtEndDateTime)) : 'No Expiry';
        $Quickedit_startDate = date('Y-m-d H:i', strtotime($value->dtDateTime));
        $Quickedit_endDate = !empty($value->dtEndDateTime) ? date('Y-m-d H:i', strtotime($value->dtEndDateTime)) : 'No Expiry';
        $title = $value->varTitle;
        if (Auth::user()->can('banners-edit')) {
            if ($value->chrLock != 'Y') {
                if ($this->currentUserRoleData->chrIsAdmin == 'Y') {
                    $title = '<div class="quick_edit"><a href = "' . route('powerpanel.banners.edit', array('alias' => $value->id)) . '?tab=F">' . $value->varTitle . '</a> <div class="quick_edit_menu">
                            <span><a href="' . route('powerpanel.banners.edit', array('alias' => $value->id)) . '?tab=F" title="Edit">Edit</a></span>';
                    if (Config::get('Constant.DEFAULT_TRASH') == 'Y') {
                        $title .= '<span><a title = "Trash" href = \'javascript:;\' onclick=\'Trashfun("' . $value->id . '")\' class="red" data-tab="F">Trash</a></span>';
                    }
                    $title .= '</div>    
                       </div>';
                } else {
                    $title = '<div class="quick_edit"><a href = "' . route('powerpanel.banners.edit', array('alias' => $value->id)) . '?tab=F">' . $value->varTitle . '</a> <div class="quick_edit_menu">
                            <span><a href="' . route('powerpanel.banners.edit', array('alias' => $value->id)) . '?tab=F" title="Edit">Edit</a></span>
                                </div>    
                       </div>';
                }
            } else {
                if (auth()->user()->id != $value->LockUserID) {
                    if ($this->currentUserRoleData->chrIsAdmin == 'Y') {
                        $title = '<div class="quick_edit"><a href = "' . route('powerpanel.banners.edit', array('alias' => $value->id)) . '?tab=F">' . $value->varTitle . '</a> <div class="quick_edit_menu">
	                            <span><a href="' . route('powerpanel.banners.edit', array('alias' => $value->id)) . '?tab=F" title="Edit">Edit</a></span>
	                                </div>    
	                        </div>';
                    } else {
                        $title = '<div class="quick_edit"><a href = "javascript:;">' . $value->varTitle . '</a></div>';
                    }
                } else {
                    $title = '<div class="quick_edit"><a href = "' . route('powerpanel.banners.edit', array('alias' => $value->id)) . '?tab=F">' . $value->varTitle . '</a> <div class="quick_edit_menu">
	                            <span><a href="' . route('powerpanel.banners.edit', array('alias' => $value->id)) . '?tab=F" title="Edit">Edit</a></span>
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
                //$log .= "<a title=\"Log History\" href=\"$logurl\"><i class=\"fa fa-clock-o\"></i></a>";
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
        if (Auth::user()->can('banners-reviewchanges')) {
            $log .= "<a title='Rollback to previous version'  onclick=\"rollbackToPreviousVersion('" . $value->id . "');\"  class=\"log-grid\"><i class=\"fa fa-history\"></i></a>";
        }
        $records = array(
            $checkbox,
            $First_td,
            '<div class="pages_title_div_row">' . $title . ' ' . $status . $statusdata . '</div>',
            $image,
            $banner_type,
            $pageName,
            $startDate,
            $log,
            $value->intDisplayOrder
        );
        return $records;
    }

    public function tableDataArchive($value, $iTotalHomeBannerRecords, $iTotalInnerBannerRecords, $totalRecord = false, $tableSortedType = 'asc') {
        $image = '';
        $actions = '';
        $banner_type = '';
        $checkbox = '';
        $publish_action = '';
        if (Auth::user()->can('banners-edit')) {
            $actions .= '<a class="" title="' . trans("template.common.edit") . '" href="' . route('powerpanel.banners.edit', array('alias' => $value->id)) . '">
								<span><i class="fa fa-pencil"></i></a>';
        }
        if ($value->varBannerType == 'home_banner') {
            if (($iTotalHomeBannerRecords > 1)) {
                if (Auth::user()->can('banners-delete') && $this->currentUserRoleData->chrIsAdmin == 'Y') {
                    if (Config::get('Constant.DEFAULT_TRASH') == 'Y') {
                        $actions .= '<a title = "' . trans('banner::template.common.delete') . '" class="delete-grid" onclick = \'Trashfun("' . $value->id . '")\' data-controller = "banners" data-alias = "' . $value->id . '" data-tab = "R"><i class = "fa fa-times"></i></a>';
                    } else {
                        $actions .= '<a class = "delete" title = "' . trans('banner::template.common.delete') . '" data-controller = "banners" data-alias = "' . $value->id . '" data-tab = "R"><i class = "fa fa-times"></i></a>';
                    }
                }
                $checkbox = '<input type="checkbox" name="delete" class="chkDelete" value="' . $value->id . '">';
            } else {
                $checkbox = '<a href="javascript:;" data-toggle="tooltip" data-placement="right" data-toggle="tooltip" title="This is default banner so can&#39;t be deleted."><i style="color:red" class="fa fa-exclamation-triangle"></i></a>';
            }
        }
        if ($value->varBannerType == 'inner_banner') {
            if ($value->chrDefaultBanner == 'Y') {
                if (($iTotalInnerBannerRecords > 1)) {
                    if (Auth::user()->can('banners-delete') && $this->currentUserRoleData->chrIsAdmin == 'Y') {
                        if (Config::get('Constant.DEFAULT_TRASH') == 'Y') {
                            $actions .= '<a title = "' . trans('banner::template.common.delete') . '" class="delete-grid" onclick = \'Trashfun("' . $value->id . '")\' data-controller = "banners" data-alias = "' . $value->id . '" data-tab = "R"><i class = "fa fa-times"></i></a>';
                        } else {
                            $actions .= '<a class = "delete" title = "' . trans('banner::template.common.delete') . '" data-controller = "banners" data-alias = "' . $value->id . '" data-tab = "R"><i class = "fa fa-times"></i></a>';
                        }
                    }
                    $checkbox = '<input type="checkbox" name="delete" class="chkDelete" value="' . $value->id . '">';
                } else {
                    $checkbox = '<a href="javascript:;" data-toggle="tooltip" data-placement="right" data-toggle="tooltip" title="This is default banner so can&#39;t be deleted."><i style="color:red" class="fa fa-exclamation-triangle"></i></a>';
                }
            } else {
                if (Auth::user()->can('banners-delete') && $this->currentUserRoleData->chrIsAdmin == 'Y') {
                    if (Config::get('Constant.DEFAULT_TRASH') == 'Y') {
                        $actions .= '<a title = "' . trans('banner::template.common.delete') . '" class="delete-grid" onclick = \'Trashfun("' . $value->id . '")\' data-controller = "banners" data-alias = "' . $value->id . '" data-tab = "R"><i class = "fa fa-times"></i></a>';
                    } else {
                        $actions .= '<a class = "delete delete-grid" title = "' . trans('banner::template.common.delete') . '" data-controller = "banners" data-alias = "' . $value->id . '" data-tab = "R"><i class = "fa fa-times"></i></a>';
                    }
                }
                $checkbox = '<input type="checkbox" name="delete" class="chkDelete" value="' . $value->id . '">';
            }
        }
        if (Auth::user()->can('banners-publish')) {
            if ($value->chrPublish == 'Y') {
                $publish_action .= '<input  data-off-text="No" data-on-text="Yes" class="make-switch publish" class="make-switch publish" data-off-color="info" data-on-color="primary" type="checkbox" data-controller="powerpanel/banners" title="' . trans("template.common.publishedRecord") . '" data-value="Unpublish" data-alias="' . $value->id . '">';
            } else {
                $publish_action .= '<input checked="" data-off-text="No" data-on-text="Yes" class="make-switch publish" class="make-switch publish" data-off-color="info" data-on-color="primary" type="checkbox" data-controller="powerpanel/banners" title="' . trans("template.common.unpublishedRecord") . '" data-value="Publish" data-alias="' . $value->id . '">';
            }
        }
        $pageName = '';
        // if ($value->varBannerType != "home_banner") {
            if ($value->modules->varTitle != 'Pages') {
                $pageName = isset($value->modules->varTitle) && strlen($value->modules->varTitle) > 0 ? $value->modules->varTitle : 'Default';
            } else {
                $pageName = isset($value->pages->varTitle) && strlen($value->pages->varTitle) > 0 ? $value->pages->varTitle : 'Default';
            }
        // } else {
        //     $pageName = 'Home';
        // }

        $image .= '<div class="text-center">';
        if (!empty($value->image)) {
            $image .= '<a href="' . resize_image::resize($value->fkIntImgId) . '" class="fancybox-buttons" data-rel="fancybox-buttons">';
            $image .= '<img height="30" width="30" title="' . preg_replace('/[^A-Za-z0-9\-]/', '-', $value->varTitle) . '" src="' . resize_image::resize($value->fkIntImgId, 50, 50) . '"/>';
            $image .= '</a>';
        } else {
            $image .= '<span class="glyphicon glyphicon-minus"></span>';
        }
        $image .= '</div>';
        if ($value->varBannerType == 'home_banner') {
            $banner_type = 'Home Banner';
        } else {
            $banner_type = 'Inner Banner';
        }
        if (Auth::user()->can('banners-reviewchanges')) {
            $update = "<a title=\"Click here to see all approval records.\" class=\"icon_title1\" style=\"margin-right: 5px;\" onclick=\"javascript:expandcollapsepanel(this ,'tasklisting" . $value->id . "', 'mainsingnimg" . $value->id . "'," . $value->id . ")\"><i id=\"mainsingnimg" . $value->id . "\" class=\"la la-plus-square\"></i></a>";
            $rollback = "<a title=\"Click here to see all approved records to rollback.\" class=\"icon_title2\" style=\"margin-right: 5px;\" onclick=\"javascript:expandcollapsepanel_rolback(this ,'tasklisting_rollback" . $value->id . "', 'mainsingnimg_rollback" . $value->id . "'," . $value->id . ")\"><i id=\"mainsingnimg_rollback" . $value->id . "\" class=\"la la-history\"></i></a>";
        } else {
            $update = '';
            $rollback = '';
        }
        $startDate = date('' . Config::get('Constant.DEFAULT_DATE_FORMAT') . ' ' . Config::get('Constant.DEFAULT_TIME_FORMAT') . '', strtotime($value->dtDateTime));
        $endDate = !empty($value->dtEndDateTime) ? date('' . Config::get('Constant.DEFAULT_DATE_FORMAT') . ' ' . Config::get('Constant.DEFAULT_TIME_FORMAT') . '', strtotime($value->dtEndDateTime)) : 'No Expiry';
        $Quickedit_startDate = date('Y-m-d H:i', strtotime($value->dtDateTime));
        $Quickedit_endDate = !empty($value->dtEndDateTime) ? date('Y-m-d H:i', strtotime($value->dtEndDateTime)) : 'No Expiry';
        $title = $value->varTitle;
        if (Auth::user()->can('banners-edit')) {
            if ($value->chrLock != 'Y') {
                if ($this->currentUserRoleData->chrIsAdmin == 'Y') {
                    $title = '<div class="quick_edit"><a href = "' . route('powerpanel.banners.edit', array('alias' => $value->id)) . '?tab=R">' . $value->varTitle . '</a> <div class="quick_edit_menu">
                            <span><a href="' . route('powerpanel.banners.edit', array('alias' => $value->id)) . '?tab=R" title="Edit">Edit</a></span>';
                    if (Config::get('Constant.DEFAULT_TRASH') == 'Y') {
                        $title .= '<span><a title = "Trash" href = \'javascript:;\' onclick=\'Trashfun("' . $value->id . '")\' class="red" data-tab="F">Trash</a></span>';
                    }
                    $title .= '</div>    
                       </div>';
                } else {
                    $title = '<div class="quick_edit"><a href = "' . route('powerpanel.banners.edit', array('alias' => $value->id)) . '?tab=R">' . $value->varTitle . '</a> <div class="quick_edit_menu">
                            <span><a href="' . route('powerpanel.banners.edit', array('alias' => $value->id)) . '?tab=R" title="Edit">Edit</a></span>
                                </div>    
                       </div>';
                }
            } else {
                if (auth()->user()->id != $value->LockUserID) {
                    if ($this->currentUserRoleData->chrIsAdmin == 'Y') {
                        $title = '<div class="quick_edit"><a href = "' . route('powerpanel.banners.edit', array('alias' => $value->id)) . '?tab=R">' . $value->varTitle . '</a> <div class="quick_edit_menu">
	                            <span><a href="' . route('powerpanel.banners.edit', array('alias' => $value->id)) . '?tab=R" title="Edit">Edit</a></span>
	                                </div>    
	                        </div>';
                    } else {
                        $title = '<div class="quick_edit"><a href = "javascript:;">' . $value->varTitle . '</a></div>';
                    }
                } else {
                    $title = '<div class="quick_edit"><a href = "' . route('powerpanel.banners.edit', array('alias' => $value->id)) . '?tab=R">' . $value->varTitle . '</a> <div class="quick_edit_menu">
	                            <span><a href="' . route('powerpanel.banners.edit', array('alias' => $value->id)) . '?tab=R" title="Edit">Edit</a></span>
	                                </div>    
	                        </div>';
                }
            }
        }

        $First_td = '';
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
                //$log .= "<a title=\"Log History\" href=\"$logurl\"><i class=\"fa fa-clock-o\"></i></a>";
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
        $records = array(
            $checkbox,
            $First_td,
            '<div class="pages_title_div_row">' . $title . ' ' . $status . $statusdata . '</div>',
            $image,
            $banner_type,
            $pageName,
            $startDate,
            $log,
            $value->intDisplayOrder
        );
        return $records;
    }

    public function tableDataDraft($value, $iTotalHomeBannerRecords, $iTotalInnerBannerRecords, $totalRecord = false, $tableSortedType = 'asc') {
        $image = '';
        $actions = '';
        $banner_type = '';
        $checkbox = '';
        $publish_action = '';
        if (Auth::user()->can('banners-edit')) {
            $actions .= '<a class="" title="' . trans("template.common.edit") . '" href="' . route('powerpanel.banners.edit', array('alias' => $value->id)) . '">
								<span><i class="fa fa-pencil"></i></a>';
        }
        if ($value->varBannerType == 'home_banner') {
            if (($iTotalHomeBannerRecords > 1)) {
                if (Auth::user()->can('banners-delete') && $this->currentUserRoleData->chrIsAdmin == 'Y') {
                    if (Config::get('Constant.DEFAULT_TRASH') == 'Y') {
                        $actions .= '<a title = "Trash" class="delete-grid" onclick = \'Trashfun("' . $value->id . '")\' data-controller = "banners" data-alias = "' . $value->id . '" data-tab = "D"><i class = "fa fa-times"></i></a>';
                    } else {
                        $actions .= '<a class = "delete" title = "' . trans('banner::template.common.delete') . '" data-controller = "banners" data-alias = "' . $value->id . '" data-tab = "D"><i class = "fa fa-times"></i></a>';
                    }
                }
                $checkbox = '<input type="checkbox" name="delete" class="chkDelete" value="' . $value->id . '">';
            } else {
                $checkbox = '<a href="javascript:;" data-toggle="tooltip" data-placement="right" data-toggle="tooltip" title="This is default banner so can&#39;t be deleted."><i style="color:red" class="fa fa-exclamation-triangle"></i></a>';
            }
        }
        if ($value->varBannerType == 'inner_banner') {
            if ($value->chrDefaultBanner == 'Y') {
                if (($iTotalInnerBannerRecords > 1)) {
                    if (Auth::user()->can('banners-delete') && $this->currentUserRoleData->chrIsAdmin == 'Y') {
                        if (Config::get('Constant.DEFAULT_TRASH') == 'Y') {
                            $actions .= '<a title = "Trash" class="delete-grid" onclick = \'Trashfun("' . $value->id . '")\' data-controller = "banners" data-alias = "' . $value->id . '" data-tab = "D"><i class = "fa fa-times"></i></a>';
                        } else {
                            $actions .= '<a class = "delete" title = "' . trans('banner::template.common.delete') . '" data-controller = "banners" data-alias = "' . $value->id . '" data-tab = "D"><i class = "fa fa-times"></i></a>';
                        }
                    }
                    $checkbox = '<input type="checkbox" name="delete" class="chkDelete" value="' . $value->id . '">';
                } else {
                    $checkbox = '<a href="javascript:;" data-toggle="tooltip" data-placement="right" data-toggle="tooltip" title="This is default banner so can&#39;t be deleted."><i style="color:red" class="fa fa-exclamation-triangle"></i></a>';
                }
            } else {
                if (Auth::user()->can('banners-delete') && $this->currentUserRoleData->chrIsAdmin == 'Y') {
                    if (Config::get('Constant.DEFAULT_TRASH') == 'Y') {
                        $actions .= '<a title = "Trash" class="delete-grid" onclick = \'Trashfun("' . $value->id . '")\' data-controller = "banners" data-alias = "' . $value->id . '" data-tab = "D"><i class = "fa fa-times"></i></a>';
                    } else {
                        $actions .= '<a class = "delete" title = "' . trans('banner::template.common.delete') . '" data-controller = "banners" data-alias = "' . $value->id . '" data-tab = "D"><i class = "fa fa-times"></i></a>';
                    }
                }
                $checkbox = '<input type="checkbox" name="delete" class="chkDelete" value="' . $value->id . '">';
            }
        }
        $publish_action .= '<input checked="" data-off-text="No" data-on-text="Yes" class="make-switch pub" data-off-color="info" data-on-color="primary" type="checkbox" data-controller="powerpanel/banners" title="' . trans("template.common.unpublishedRecord") . '" data-value="Publish" data-alias="' . $value->id . '">';
        $pageName = '';
        if ($value->varBannerType != "home_banner") {
            if ($value->modules->varTitle != 'Pages') {
                $pageName = isset($value->modules->varTitle) && strlen($value->modules->varTitle) > 0 ? $value->modules->varTitle : 'Default';
            } else {
                $pageName = isset($value->pages->varTitle) && strlen($value->pages->varTitle) > 0 ? $value->pages->varTitle : 'Default';
            }
        } else {
            $pageName = 'Home';
        }

        $image .= '<div class="text-center">';
        if (!empty($value->image)) {
            $image .= '<a href="' . resize_image::resize($value->fkIntImgId) . '" class="fancybox-buttons" data-rel="fancybox-buttons">';
            $image .= '<img height="30" width="30" title="' . preg_replace('/[^A-Za-z0-9\-]/', '-', $value->varTitle) . '" src="' . resize_image::resize($value->fkIntImgId, 50, 50) . '"/>';
            $image .= '</a>';
        } else {
            $image .= '<span class="glyphicon glyphicon-minus"></span>';
        }
        $image .= '</div>';
        if ($value->varBannerType == 'home_banner') {
            $banner_type = 'Home Banner';
        } else {
            $banner_type = 'Inner Banner';
        }
        if (Auth::user()->can('banners-reviewchanges')) {
            $update = "<a title=\"Click here to see all approval records.\" class=\"icon_title1\" style=\"margin-right: 5px;\" onclick=\"javascript:expandcollapsepanel(this ,'tasklisting" . $value->id . "', 'mainsingnimg" . $value->id . "'," . $value->id . ")\"><i id=\"mainsingnimg" . $value->id . "\" class=\"la la-plus-square\"></i></a>";
            $rollback = "<a title=\"Click here to see all approved records to rollback.\" class=\"icon_title2\" style=\"margin-right: 5px;\" onclick=\"javascript:expandcollapsepanel_rolback(this ,'tasklisting_rollback" . $value->id . "', 'mainsingnimg_rollback" . $value->id . "'," . $value->id . ")\"><i id=\"mainsingnimg_rollback" . $value->id . "\" class=\"la la-history\"></i></a>";
        } else {
            $update = '';
            $rollback = '';
        }
        $startDate = date('' . Config::get('Constant.DEFAULT_DATE_FORMAT') . ' ' . Config::get('Constant.DEFAULT_TIME_FORMAT') . '', strtotime($value->dtDateTime));
        $endDate = !empty($value->dtEndDateTime) ? date('' . Config::get('Constant.DEFAULT_DATE_FORMAT') . ' ' . Config::get('Constant.DEFAULT_TIME_FORMAT') . '', strtotime($value->dtEndDateTime)) : 'No Expiry';
        $Quickedit_startDate = date('Y-m-d H:i', strtotime($value->dtDateTime));
        $Quickedit_endDate = !empty($value->dtEndDateTime) ? date('Y-m-d H:i', strtotime($value->dtEndDateTime)) : 'No Expiry';
        $title = $value->varTitle;
        if (Auth::user()->can('banners-edit')) {
            if ($value->chrLock != 'Y') {
                if ($this->currentUserRoleData->chrIsAdmin == 'Y') {
                    $title = '<div class="quick_edit"><a href = "' . route('powerpanel.banners.edit', array('alias' => $value->id)) . '?tab=D">' . $value->varTitle . '</a> <div class="quick_edit_menu">
                            <span><a href="' . route('powerpanel.banners.edit', array('alias' => $value->id)) . '?tab=D" title="Edit">Edit</a></span>';
                    if (Config::get('Constant.DEFAULT_TRASH') == 'Y') {
                        $title .= '<span><a title = "Trash" href = \'javascript:;\' onclick=\'Trashfun("' . $value->id . '")\' class="red" data-tab="D">Trash</a></span>';
                    }
                    $title .= '</div>    
                       </div>';
                } else {
                    $title = '<div class="quick_edit"><a href = "' . route('powerpanel.banners.edit', array('alias' => $value->id)) . '?tab=D">' . $value->varTitle . '</a> <div class="quick_edit_menu">
                            <span><a href="' . route('powerpanel.banners.edit', array('alias' => $value->id)) . '?tab=D" title="Edit">Edit</a></span>
                                </div>    
                       </div>';
                }
            } else {
                if (auth()->user()->id != $value->LockUserID) {
                    if ($this->currentUserRoleData->chrIsAdmin == 'Y') {
                        $title = '<div class="quick_edit"><a href = "' . route('powerpanel.banners.edit', array('alias' => $value->id)) . '?tab=D">' . $value->varTitle . '</a> <div class="quick_edit_menu">
	                            <span><a href="' . route('powerpanel.banners.edit', array('alias' => $value->id)) . '?tab=D" title="Edit">Edit</a></span>';
                        $title .= '</div>    
                       </div>';
                    } else {
                        $title = '<div class="quick_edit"><a href = "javascript:;">' . $value->varTitle . '</a></div>';
                    }
                } else {
                    $title = '<div class="quick_edit"><a href = "' . route('powerpanel.banners.edit', array('alias' => $value->id)) . '?tab=D">' . $value->varTitle . '</a> <div class="quick_edit_menu">
	                            <span><a href="' . route('powerpanel.banners.edit', array('alias' => $value->id)) . '?tab=D" title="Edit">Edit</a></span>
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
                $Favorite = '<a class="star_icon_div" href="javascript:;" onclick="GetFavorite(' . $value->id . ',\'N\',\'D\')"><i class="' . $Class . '"></i></a>';
            } else {
                $Class = 'la la-star-o';
                $Favorite = '<a class="star_icon_div" href="javascript:;" onclick="GetFavorite(' . $value->id . ',\'Y\',\'D\')"><i class="' . $Class . '"></i></a>';
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
                //$log .= "<a title=\"Log History\" href=\"$logurl\"><i class=\"fa fa-clock-o\"></i></a>";
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
            $checkbox,
            '<div class="pages_title_div_row"><input type="hidden" id="draftid" value="' . $value->id . '">' . $title . ' ' . $status . $statusdata . '</div>',
            $image,
            $banner_type,
            $pageName,
            $startDate,
            $publish_action,
            $log
        );
        return $records;
    }

    public function tableDataTrash($value, $iTotalHomeBannerRecords, $iTotalInnerBannerRecords, $totalRecord = false, $tableSortedType = 'asc') {
        $image = '';
        $actions = '';
        $banner_type = '';
        $checkbox = '';
        $publish_action = '';
        if ($value->varBannerType == 'home_banner') {
            if (($iTotalHomeBannerRecords > 1)) {
                if (Auth::user()->can('banners-delete') && $this->currentUserRoleData->chrIsAdmin == 'Y') {
                    $actions .= '<a class=" delete" title="' . trans("template.common.delete") . '" data-controller="banners" data-alias="' . $value->id . '" data-tab="T"><i class="fa fa-times"></i></a>';
                }
                $checkbox = '<input type="checkbox" name="delete" class="chkDelete" value="' . $value->id . '">';
            } else {
                $checkbox = '<a href="javascript:;" data-toggle="tooltip" data-placement="right" data-toggle="tooltip" title="This is default banner so can&#39;t be deleted."><i style="color:red" class="fa fa-exclamation-triangle"></i></a>';
            }
        }
        if ($value->varBannerType == 'inner_banner') {
            if ($value->chrDefaultBanner == 'Y') {
                if (($iTotalInnerBannerRecords > 1)) {
                    if (Auth::user()->can('banners-delete') && $this->currentUserRoleData->chrIsAdmin == 'Y') {
                        $actions .= '<a class=" delete" title="' . trans("template.common.delete") . '" data-controller="banners" data-alias = "' . $value->id . '" data-tab="T"><i class="fa fa-times"></i></a>';
                    }
                    $checkbox = '<input type="checkbox" name="delete" class="chkDelete" value="' . $value->id . '">';
                } else {
                    $checkbox = '<a href="javascript:;" data-toggle="tooltip" data-placement="right" data-toggle="tooltip" title="This is default banner so can&#39;t be deleted."><i style="color:red" class="fa fa-exclamation-triangle"></i></a>';
                }
            } else {
                if (Auth::user()->can('banners-delete') && $this->currentUserRoleData->chrIsAdmin == 'Y') {
                    $actions .= '<a class=" delete" title="Delete" data-controller="banners" data-alias = "' . $value->id . '" data-tab="T"><i class="fa fa-times"></i></a>';
                }
                $checkbox = '<input type="checkbox" name="delete" class="chkDelete" value="' . $value->id . '">';
            }
        }
        $pageName = '';
        if ($value->varBannerType != "home_banner") {
            if ($value->modules->varTitle != 'Pages') {
                $pageName = isset($value->modules->varTitle) && strlen($value->modules->varTitle) > 0 ? $value->modules->varTitle : 'Default';
            } else {
                $pageName = isset($value->pages->varTitle) && strlen($value->pages->varTitle) > 0 ? $value->pages->varTitle : 'Default';
            }
        } else {
            $pageName = 'Home';
        }

        $image .= '<div class="text-center">';
        if (!empty($value->image)) {
            $image .= '<a href="' . resize_image::resize($value->fkIntImgId) . '" class="fancybox-buttons" data-rel="fancybox-buttons">';
            $image .= '<img height="30" width="30" title="' . preg_replace('/[^A-Za-z0-9\-]/', '-', $value->varTitle) . '" src="' . resize_image::resize($value->fkIntImgId, 50, 50) . '"/>';
            $image .= '</a>';
        } else {
            $image .= '<span class="glyphicon glyphicon-minus"></span>';
        }
        $image .= '</div>';
        if ($value->varBannerType == 'home_banner') {
            $banner_type = 'Home Banner';
        } else {
            $banner_type = 'Inner Banner';
        }
        if (Auth::user()->can('banners-reviewchanges')) {
            $update = "<a title=\"Click here to see all approval records.\" class=\"icon_title1\" style=\"margin-right: 5px;\" onclick=\"javascript:expandcollapsepanel(this ,'tasklisting" . $value->id . "', 'mainsingnimg" . $value->id . "'," . $value->id . ")\"><i id=\"mainsingnimg" . $value->id . "\" class=\"la la-plus-square\"></i></a>";
            $rollback = "<a title=\"Click here to see all approved records to rollback.\" class=\"icon_title2\" style=\"margin-right: 5px;\" onclick=\"javascript:expandcollapsepanel_rolback(this ,'tasklisting_rollback" . $value->id . "', 'mainsingnimg_rollback" . $value->id . "'," . $value->id . ")\"><i id=\"mainsingnimg_rollback" . $value->id . "\" class=\"la la-history\"></i></a>";
        } else {
            $update = '';
            $rollback = '';
        }
        $startDate = date('' . Config::get('Constant.DEFAULT_DATE_FORMAT') . ' ' . Config::get('Constant.DEFAULT_TIME_FORMAT') . '', strtotime($value->dtDateTime));
        $endDate = !empty($value->dtEndDateTime) ? date('' . Config::get('Constant.DEFAULT_DATE_FORMAT') . ' ' . Config::get('Constant.DEFAULT_TIME_FORMAT') . '', strtotime($value->dtEndDateTime)) : 'No Expiry';
        $title = $value->varTitle;
        if (Auth::user()->can('banners-edit')) {
            $title = '<div class="quick_edit text-uppercase">' . $value->varTitle . '    
                        </div>';
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
            '<div class="pages_title_div_row">' . $title . ' ' . $status . $statusdata . '</div>',
            $image,
            $banner_type,
            $pageName,
            $startDate,
            $log
        );
        return $records;
    }

    /**
     * This method handels logs old records
     * @param   $data
     * @return  order
     * @since   2017-07-21
     * @author  NetQuick
     */
    public function recordHistory($data = false) {
        $startDate = date('' . Config::get('Constant.DEFAULT_DATE_FORMAT') . ' ' . Config::get('Constant.DEFAULT_TIME_FORMAT') . '', strtotime($data->dtDateTime));
        $endDate = !empty($data->dtEndDateTime) ? date('' . Config::get('Constant.DEFAULT_DATE_FORMAT') . ' ' . Config::get('Constant.DEFAULT_TIME_FORMAT') . '', strtotime($data->dtEndDateTime)) : 'No Expiry';
        $banner_type = NULL;
        if ($data->varBannerType == 'home_banner') {
            $banner_type = 'Home Banner';
        } else {
            $banner_type = 'Inner Banner';
        }
        $bannerVersion = ($data->varBannerVersion == "vid_banner") ? "Video Banner" : "Image Banner";
        if ($data->varBannerVersion == "vid_banner" && $data->fkIntVideoId != null) {
            $videoDetail = Video::getVideoTitleById($data->fkIntVideoId);
            $videoTitle = ($videoDetail->varVideoName != "") ? $videoDetail->varVideoName . "." . $videoDetail->varVideoExtension : '-';
        } else {
            $videoTitle = "-";
        }

        $moduledata = Modules::getModuleById($data->fkModuleId);
        if (isset($moduledata->varModelName) && !empty($moduledata->varModelName)) {
            $model = '\\Powerpanel\\'. $moduledata->varModelName.'\\Models\\' . $moduledata->varModelName;
            $moduleRec = $model::getRecordById($data->fkIntPageId);
        } else {
            $moduleRec = '';
        }

        if (isset($moduleRec->varTitle) && !empty($moduleRec->varTitle)) {
            $pageTitle = $moduleRec->varTitle;
        } else {
            $pageTitle = '-';
        }
        $returnHtml = '';
        $returnHtml .= '<table class="new_table_desing table table-striped table-bordered table-hover">
			<thead>
			<tr>
				<th align="center">' . trans("template.common.title") . '</th>
                                <th align="center">Start date</th>
                                <th align="center">End date</th>
				<th align="center">' . trans("template.bannerModule.bannerType") . '</th>
				<th align="center">' . trans("template.bannerModule.page") . '</th>
				<th align="center">' . trans("template.common.image") . '</th>
				<th align="center">' . trans("template.bannerModule.version") . '</th>
				<th align="center">' . trans("template.common.video") . '</th>
				<th align="center">' . trans("template.common.displayorder") . '</th>
				<th align="center">' . trans("template.common.defaultbanner") . '</th>
				<th align="center">' . trans("template.common.publish") . '</th>
			</tr>
			</thead>
			<tbody>
			<tr>
				<td align="center">' . $data->varTitle . '</td>
                                <td align="center">' . $startDate . '</td>
				<td align="center">' . $endDate . '</td>
				<td align="center">' . $banner_type . '</td>
				<td align="center">' . $pageTitle . '</td>';
        if ($data->fkIntImgId > 0) {
            $returnHtml .= '<td align="center">' . '<img height="50" width="50" src="' . resize_image::resize($data->fkIntImgId) . '" />' . '</td>';
        } else {
            $returnHtml .= '<td align="center">-</td>';
        }
        $returnHtml .= '<td align="center">' . $bannerVersion . '</td>
			<td align="center">' . $videoTitle . '</td> 
			<td align="center">' . ($data->intDisplayOrder) . '</td>
			<td align="center">' . $data->chrDefaultBanner . '</td>
			<td align="center">' . $data->chrPublish . '</td>
			</tr>
			</tbody>
			</table>';
        return $returnHtml;
    }

    public function newrecordHistory($data = false, $newdata = false) {
        $startDate = date('' . Config::get('Constant.DEFAULT_DATE_FORMAT') . ' ' . Config::get('Constant.DEFAULT_TIME_FORMAT') . '', strtotime($newdata->dtDateTime));
        $endDate = !empty($newdata->dtEndDateTime) ? date('' . Config::get('Constant.DEFAULT_DATE_FORMAT') . ' ' . Config::get('Constant.DEFAULT_TIME_FORMAT') . '', strtotime($newdata->dtEndDateTime)) : 'No Expiry';
        if ($data->varTitle != $newdata->varTitle) {
            $titlecolor = 'style="background-color:#f5efb7"';
        } else {
            $titlecolor = '';
        }
        if ($data->varBannerType != $newdata->varBannerType) {
            $varBannerType = 'style="background-color:#f5efb7"';
        } else {
            $varBannerType = '';
        }
        if ($data->fkIntPageId != $newdata->fkIntPageId) {
            $fkIntPageId = 'style="background-color:#f5efb7"';
        } else {
            $fkIntPageId = '';
        }
        if ($data->fkIntImgId != $newdata->fkIntImgId) {
            $fkIntImgId = 'style="background-color:#f5efb7"';
        } else {
            $fkIntImgId = '';
        }
        if ($data->varBannerVersion != $newdata->varBannerVersion) {
            $varBannerVersion = 'style="background-color:#f5efb7"';
        } else {
            $varBannerVersion = '';
        }
        if ($data->intDisplayOrder != $newdata->intDisplayOrder) {
            $intDisplayOrder = 'style=background-color:#f5efb7"';
        } else {
            $intDisplayOrder = '';
        }
        if ($data->chrDefaultBanner != $newdata->chrDefaultBanner) {
            $chrDefaultBanner = 'style="background-color:#f5efb7"';
        } else {
            $chrDefaultBanner = '';
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
        $banner_type = NULL;
        if ($newdata->varBannerType == 'home_banner') {
            $banner_type = 'Home Banner';
        } else {
            $banner_type = 'Inner Banner';
        }
        $bannerVersion = ($data->varBannerVersion == "vid_banner") ? "Video Banner" : "Image Banner";
        if ($newdata->varBannerVersion == "vid_banner" && $newdata->fkIntVideoId != null) {
            $videoDetail = Video::getVideoTitleById($newdata->fkIntVideoId);
            $videoTitle = ($videoDetail->varVideoName != "") ? $videoDetail->varVideoName . "." . $videoDetail->varVideoExtension : '-';
        } else {
            $videoTitle = "-";
        }
        $moduledata = Modules::getModuleById($newdata->fkModuleId);
        if (isset($moduledata->varModelName) && !empty($moduledata->varModelName)) {
            $model = '\\Powerpanel\\'. $moduledata->varModelName.'\\Models\\' . $moduledata->varModelName;
            $moduleRec = $model::getRecordById($newdata->fkIntPageId);
        } else {
            $moduleRec = '';
        }

        if (isset($moduleRec->varTitle) && !empty($moduleRec->varTitle)) {
            $pageTitle = $moduleRec->varTitle;
        } else {
            $pageTitle = '-';
        }
        $returnHtml = '';
        $returnHtml .= '<table class="new_table_desing table table-striped table-bordered table-hover">
			<thead>
			<tr>
				<th align="center">' . trans("template.common.title") . '</th>
                                <th align="center">Start date</th>
                                <th align="center">End date</th>
				<th align="center">' . trans("template.bannerModule.bannerType") . '</th>
				<th align="center">' . trans("template.bannerModule.page") . '</th>
				<th align="center">' . trans("template.common.image") . '</th>
				<th align="center">' . trans("template.bannerModule.version") . '</th>
				<th align="center">' . trans("template.common.video") . '</th>
				<th align="center">' . trans("template.common.displayorder") . '</th>
				<th align="center">' . trans("template.common.defaultbanner") . '</th>
				<th align="center">' . trans("template.common.publish") . '</th>
			</tr>
			</thead>
			<tbody>
			<tr>
				<td align="center" ' . $titlecolor . '>' . $newdata->varTitle . '</td>
                                <td align="center" ' . $DateTimecolor . '>' . $startDate . '</td>
                                <td align="center" ' . $EndDateTimecolor . '>' . $endDate . '</td>
				<td align="center" ' . $varBannerType . '>' . $banner_type . '</td>
				<td align="center" ' . $fkIntPageId . '>' . $pageTitle . '</td>';
        if ($newdata->fkIntImgId > 0) {
            $returnHtml .= '<td align="center" ' . $fkIntImgId . '>' . '<img height="50" width="50" src="' . resize_image::resize($newdata->fkIntImgId) . '" />' . '</td>';
        } else {
            $returnHtml .= '<td align="center">-</td>';
        }
        $returnHtml .= '<td align="center" ' . $varBannerVersion . '>' . $bannerVersion . '</td>
			<td align="center">' . $videoTitle . '</td> 
			<td align="center" ' . $intDisplayOrder . '>' . ($newdata->intDisplayOrder) . '</td>
			<td align="center" ' . $chrDefaultBanner . '>' . $newdata->chrDefaultBanner . '</td>
			<td align="center" ' . $Publishcolor . '>' . $newdata->chrPublish . '</td>
			</tr>
			</tbody>
			</table>';
        return $returnHtml;
    }

    public static function flushCache() {
        Cache::tags('Banner')->flush();
    }

    public function rollBackRecord(Request $request) {

        $message = 'Oops! Something went wrong';
        $requestArr = Request::all();
        $request = (object) $requestArr;
        
        $previousRecord = Banner::getPreviousRecordByMainId($request->id);
        if(!empty($previousRecord)) {

            $main_id = $previousRecord->fkMainRecord;
            $request->id = $previousRecord->id;
            $request->main_id = $main_id;

            $message = Banner::approved_data_Listing($request);
            
            $newBlogObj = Banner::getRecordForLogById($main_id);
            $restoredata = Config::get('Constant.ROLLBACK_RECORD');

            /* notification for user to record approved */
            $blogs = Banner::getRecordForLogById($previousRecord->id);
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
