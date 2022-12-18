<?php

namespace Powerpanel\GetaEstimateLead\Controllers\Powerpanel;

use App\CommonModel;
use Powerpanel\GetaEstimateLead\Models\GetaEstimateLead;
use Powerpanel\GetaEstimateLead\Models\GetaEstimateLeadExport;
use App\Helpers\MyLibrary;
use App\Http\Controllers\PowerpanelController;
use Powerpanel\Services\Models\Services;
use Config;
use Excel;
use Request;

class GetaEstimateLeadController extends PowerpanelController {

    /**
     * Create a new Dashboard controller instance.
     *
     * @return void
     */
    public function __construct() {
        parent::__construct();
        if (isset($_COOKIE['locale'])) {
            app()->setLocale($_COOKIE['locale']);
        }
    }

    public function index() {
        $servicesName = Services::getFrontServicesDropdownList();
        $iTotalRecords = CommonModel::getRecordCount(false, false, false, 'Powerpanel\GetaEstimateLead\Models\GetaEstimateLead');
        $this->breadcrumb['title'] = trans('getaestimatelead::template.getaestimateModule.manageGetaEstimateLeads');
        $email = Request::segment(3);
        $emaildata = session(["email" => $email]);
        return view('getaestimatelead::powerpanel.list', ['iTotalRecords' => $iTotalRecords, 'servicesName' => $servicesName, 'breadcrumb' => $this->breadcrumb]);
    }

    public function get_list() {
        $filterArr = [];
        $records = [];
        $records["data"] = [];
        $email = session("email");
        $filterArr['orderColumnNo'] = (!empty(Request::get('order')[0]['column']) ? Request::get('order')[0]['column'] : '');
        $filterArr['orderByFieldName'] = (!empty(Request::get('columns')[$filterArr['orderColumnNo']]['name']) ? Request::get('columns')[$filterArr['orderColumnNo']]['name'] : '');
        $filterArr['orderTypeAscOrDesc'] = (!empty(Request::get('order')[0]['dir']) ? Request::get('order')[0]['dir'] : '');
        $filterArr['searchFilter'] = !empty(Request::get('searchValue')) ? Request::get('searchValue') : '';
        $filterArr['start'] = !empty(Request::get('rangeFilter')['from']) ? Request::get('rangeFilter')['from'] : '';
        $filterArr['end'] = !empty(Request::get('rangeFilter')['to']) ? Request::get('rangeFilter')['to'] : '';
        $filterArr['iDisplayLength'] = intval(Request::get('length'));
        $filterArr['iDisplayStart'] = intval(Request::get('start'));
        $filterArr['servicefilter'] = !empty(Request::get('servicefilter')) ? Request::get('servicefilter') : '';
        $filterArr['catValue'] = !empty($email) ? $email : '';

        $sEcho = intval(Request::get('draw'));

        $arrResults = GetaEstimateLead::getRecordList($filterArr);
        $iTotalRecords = CommonModel::getRecordCount($filterArr, true, false, 'Powerpanel\GetaEstimateLead\Models\GetaEstimateLead');

        $end = $filterArr['iDisplayStart'] + $filterArr['iDisplayLength'];
        $end = $end > $iTotalRecords ? $iTotalRecords : $end;

        if (!empty($arrResults)) {
            foreach ($arrResults as $key => $value) {
                $records["data"][] = $this->tableData($value);
            }
        }

        if (isset($_REQUEST["customActionType"]) && $_REQUEST["customActionType"] == "group_action") {
            $records["customActionStatus"] = "OK"; // pass custom message(useful for getting status of group actions)
        }

        $records["draw"] = $sEcho;
        $records["recordsTotal"] = $iTotalRecords;
        $records["recordsFiltered"] = $iTotalRecords;
        echo json_encode($records);
        exit;
    }

    /**
     * This method handels delete leads operation
     * @return  xls file
     * @since   2016-10-18
     * @author  NetQuick
     */
    public function DeleteRecord(Request $request) {
        $data = Request::all('ids');
        $update = MyLibrary::deleteMultipleRecords($data, false, false, 'Powerpanel\GetaEstimateLead\Models\GetaEstimateLead');
        echo json_encode($update);
        exit;
    }

    /**
     * This method handels export process of Request a Quote leads
     * @return  xls file
     * @since   2016-10-18
     * @author  NetQuick
     */
    public function ExportRecord() {
        return Excel::download(new GetaEstimateLeadExport, Config::get('Constant.SITE_NAME') . '-' . trans("Get Free Estimate Form Leads") . '-' . date("dmy-h:i") . '.xlsx');
    }

    public function tableData($value) {
        $details = '';
        $phoneNo = '';
        $service = '';
        if (!empty($value->fkIntServiceId)) {
        $serviceIDs = $value->fkIntServiceId;
        $selService = Services::getServiceNameById($serviceIDs);
        $service.= $selService['varTitle'];
        } else {
        $service .= '-';
        }
        if (!empty($value->txtUserMessage)) {
            $message = MyLibrary::getDecryptedString(nl2br($value->txtUserMessage));
            $details .= '<div class="pro-act-btn">';
            $details .= '<a href="javascript:void(0)" class="without_bg_icon" onclick="return hs.htmlExpand(this,{width:300,headingText:\'Message\',wrapperClassName:\'titlebar\',showCredits:false});"><span aria-hidden="true" class="icon-envelope"></span></a>';
            $details .= '<div class="highslide-maincontent">' . nl2br($message) . '</div>';
            $details .= '</div>';
        } else {
            $details .= '-';
        }

        if (!empty($value->varPhoneNo)) {
            $phoneNo = MyLibrary::getDecryptedString($value->varPhoneNo);
        } else {
            $phoneNo = '-';
        }

        $records = array(
            '<input type="checkbox" name="delete[]" class="chkDelete" value="' . $value->id . '">',
            $value->varName,
            MyLibrary::getDecryptedString($value->varEmail),
            $phoneNo,
            $service,
            $details,
            $value->varIpAddress,
            date('' . Config::get('Constant.DEFAULT_DATE_FORMAT') . ' ' . Config::get('Constant.DEFAULT_TIME_FORMAT') . '', strtotime($value->created_at)),
        );

        return $records;
    }

}
