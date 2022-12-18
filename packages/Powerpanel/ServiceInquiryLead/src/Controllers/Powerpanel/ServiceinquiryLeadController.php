<?php
namespace Powerpanel\ServiceInquiryLead\Controllers\Powerpanel;

use App\CommonModel;
use Powerpanel\ServiceInquiryLead\Models\ServiceinquiryLead;
use Powerpanel\ServiceInquiryLead\Models\ServiceinquiryLeadExport;
use App\Helpers\MyLibrary;
use App\Http\Controllers\PowerpanelController;
use Powerpanel\Services\Models\Services;
use Config;
use Excel;
use Request;

class ServiceinquiryLeadController extends PowerpanelController
{

    /**
     * Create a new Dashboard controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        if (isset($_COOKIE['locale'])) {
            app()->setLocale($_COOKIE['locale']);
        }
    }

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
        $servicesName = Services::getFrontServicesDropdownList();
        $iTotalRecords = CommonModel::getRecordCount(false,false,false, 'Powerpanel\ServiceInquiryLead\Models\ServiceinquiryLead');
        $this->breadcrumb['title'] = trans('serviceinquirylead::template.serviceinquiryleadModule.manageServiceinquiryLeads');
        $email = Request::segment(3);
        $emaildata = session(["email" => $email]);
        return view('serviceinquirylead::powerpanel.list', ['userIsAdmin' => $userIsAdmin, 'iTotalRecords' => $iTotalRecords, 'servicesName' => $servicesName, 'breadcrumb' => $this->breadcrumb]);
    }

    public function get_list()
    {
        $email = session("email");
        $filterArr = [];
        $records = [];
        $records["data"] = [];
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

        $arrResults = ServiceinquiryLead::getRecordList($filterArr);
        $iTotalRecords = CommonModel::getRecordCount($filterArr, true,false, 'Powerpanel\ServiceInquiryLead\Models\ServiceinquiryLead');

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
    public function DeleteRecord(Request $request)
    {
        $data = Request::all('ids');
        $update = MyLibrary::deleteMultipleRecords($data, false, false, 'Powerpanel\ServiceInquiryLead\Models\ServiceinquiryLead');
        echo json_encode($update);
        exit;
    }

    /**
     * This method handels export process of contact us leads
     * @return  xls file
     * @since   2016-10-18
     * @author  NetQuick
     */
    public function ExportRecord()
    {
        return Excel::download(new ServiceinquiryLeadExport, Config::get('Constant.SITE_NAME') . '-' . trans("serviceinquirylead::template.serviceinquiryleadModule.serviceInquiryLeads") . '-' . date("dmy-h:i") . '.xlsx');

    }

    public function tableData($value)
    {
        $details = '';
        $phoneNo = '';
        $service = '';
        if(isset($value->fkIntServiceId) && $value->fkIntServiceId == 0)
        {
            $service .= "General Enquiry";
        }else{
            if (!empty($value->fkIntServiceId)) {
                $serviceIDs = $value->fkIntServiceId;
                $selService = Services::getServiceNameById($serviceIDs);
                $service.= $selService['varTitle'];
                } else {
                $service .= 'N/A';
                }
        }
        
        if (!empty($value->txtUserMessage)) {
            $message = MyLibrary::getDecryptedString(nl2br($value->txtUserMessage));
            $details .= '<div class="pro-act-btn">';
            $details .= '<a href="javascript:void(0)" class="without_bg_icon" onclick="return hs.htmlExpand(this,{width:300,headingText:\'Message\',wrapperClassName:\'titlebar\',showCredits:false});"><span aria-hidden="true" class="icon-envelope"></span></a>';
            $details .= '<div class="highslide-maincontent">' . nl2br($message) . '</div>';
            $details .= '</div>';
        } else {
            $details .= 'N/A';
        } 

        if (!empty($value->varPhoneNo)) {
            $phoneNo = MyLibrary::getDecryptedString($value->varPhoneNo);
        } else {
            $phoneNo = 'N/A';
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
