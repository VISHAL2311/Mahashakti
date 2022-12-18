<?php
namespace Powerpanel\DataRemovalLead\Controllers\Powerpanel;

use App\CommonModel;
use Powerpanel\DataRemovalLead\Models\DataRemovalLead;
use Powerpanel\DataRemovalLead\Models\DataRemovalLeadExport;
use App\Helpers\MyLibrary;
use App\Http\Controllers\PowerpanelController;
use Powerpanel\Services\Models\Services;
use Config;
use Excel;
use Request;
use Schema;
use DB;
use Powerpanel\ContactUsLead\Models\ContactLead;

class DataRemovalLeadController extends PowerpanelController
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
        $iTotalRecords = CommonModel::getRecordCount(false,false,false, 'Powerpanel\DataRemovalLead\Models\DataRemovalLead');
        $this->breadcrumb['title'] = trans('dataremovallead::template.dataremovalleadModule.manageDataRemovalLeads');
        return view('dataremovallead::powerpanel.list', ['userIsAdmin' => $userIsAdmin, 'iTotalRecords' => $iTotalRecords, 'servicesName' => $servicesName, 'breadcrumb' => $this->breadcrumb]);
    }

    public function get_list()
    {
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

        $sEcho = intval(Request::get('draw'));

        $arrResults = DataRemovalLead::getRecordList($filterArr);
        $iTotalRecords = CommonModel::getRecordCount($filterArr, true,false, 'Powerpanel\DataRemovalLead\Models\DataRemovalLead');

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
    // public function DeleteRecord(Request $request)
    // {
    //     $data = Request::all('ids');
    //     $update = MyLibrary::deleteMultipleRecords($data, false, false, 'Powerpanel\DataRemovalLead\Models\DataRemovalLead');
    //     echo json_encode($update);
    //     exit;
    // }

    public function DeleteRecord(Request $request) {
        $data = Request::all('ids');

        $DataRemoveLeadePrivacy = DB::table('data_removal_leads')->select('varEmail')->where('id', $data['ids'][0])->get();
        
        if (Schema::hasTable('contact_lead')) {
            $DataRemoveLeadContactCount = DB::table('contact_lead')->select('varEmail')->where('varEmail', $DataRemoveLeadePrivacy[0]->varEmail)->count();
            $DataRemoveLeadContact = DB::table('contact_lead')->select('varEmail')->where('varEmail', $DataRemoveLeadePrivacy[0]->varEmail)->groupBy('varEmail')->get();
            if ($DataRemoveLeadContactCount > 0) {
                $delete = DB::table('contact_lead')->where('varEmail', $DataRemoveLeadContact[0]->varEmail)->delete();
            }
        }
        if (Schema::hasTable('serviceinquiry_lead')) {
            $DataRemoveLeadServiceCount = DB::table('serviceinquiry_lead')->select('varEmail')->where('varEmail', $DataRemoveLeadePrivacy[0]->varEmail)->count();
            $DataRemoveLeadService = DB::table('serviceinquiry_lead')->select('varEmail')->where('varEmail', $DataRemoveLeadePrivacy[0]->varEmail)->groupBy('varEmail')->get();
            if ($DataRemoveLeadServiceCount > 0) {
                $delete = DB::table('serviceinquiry_lead')->where('varEmail', $DataRemoveLeadService[0]->varEmail)->delete();
            }
        }
        if (Schema::hasTable('boatinquiry_lead')) {
            $DataRemoveLeadBoatCount = DB::table('boatinquiry_lead')->select('varEmail')->where('varEmail', $DataRemoveLeadePrivacy[0]->varEmail)->count();
            $DataRemoveLeadBoat = DB::table('boatinquiry_lead')->select('varEmail')->where('varEmail', $DataRemoveLeadePrivacy[0]->varEmail)->groupBy('varEmail')->get();
            if ($DataRemoveLeadBoatCount > 0) {
                $delete = DB::table('boatinquiry_lead')->where('varEmail', $DataRemoveLeadBoat[0]->varEmail)->delete();
            }
        }
        if (Schema::hasTable('getaestimate_lead')) {
            $DataRemoveLeadEstimateCount = DB::table('getaestimate_lead')->select('varEmail')->where('varEmail', $DataRemoveLeadePrivacy[0]->varEmail)->count();
            $DataRemoveLeadEstimate = DB::table('getaestimate_lead')->select('varEmail')->where('varEmail', $DataRemoveLeadePrivacy[0]->varEmail)->groupBy('varEmail')->get();
            if ($DataRemoveLeadEstimateCount > 0) {
                $delete = DB::table('getaestimate_lead')->where('varEmail', $DataRemoveLeadEstimate[0]->varEmail)->delete();
            }
        }
        if (Schema::hasTable('newsletter_lead')) {
            $DataRemoveLeadNewsCount = DB::table('newsletter_lead')->select('varEmail')->where('varEmail', $DataRemoveLeadePrivacy[0]->varEmail)->count();
            $DataRemoveLeadNews = DB::table('newsletter_lead')->select('varEmail')->where('varEmail', $DataRemoveLeadePrivacy[0]->varEmail)->groupBy('varEmail')->get();
            if ($DataRemoveLeadNewsCount > 0) {
                $delete = DB::table('newsletter_lead')->where('varEmail', $DataRemoveLeadNews[0]->varEmail)->delete();
            }
        }
        if (Schema::hasTable('privacy_removal_leads')) {
            $DataRemoveLeadePrivacyCount = DB::table('privacy_removal_leads')->select('varEmail')->where('varEmail', $DataRemoveLeadePrivacy[0]->varEmail)->count();
            $DataRemoveLeadPrivacy = DB::table('privacy_removal_leads')->select('varEmail')->where('varEmail', $DataRemoveLeadePrivacy[0]->varEmail)->groupBy('varEmail')->get();
            if ($DataRemoveLeadePrivacyCount > 0) {
                $delete = DB::table('privacy_removal_leads')->where('varEmail', $DataRemoveLeadPrivacy[0]->varEmail)->delete();
            }
            
        }

        $delete = MyLibrary::deleteMultipleRecords($data, false, false, 'Powerpanel\DataRemovalLead\Models\DataRemovalLead');
        echo json_encode($delete);
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
        return Excel::download(new DataRemovalLeadExport, Config::get('Constant.SITE_NAME') . '-' . trans("dataremovallead::template.dataremovalleadModule.dataRemovalLeads") . '-' . date("dmy-h:i") . '.xlsx');

    }

    public function tableData($value)
    {        
        $Reason = '';
        $Request = '';
        $countmessage = '';
     
        if (!empty($value->varReason)) {
            $Reason .= '<div class="pro-act-btn">';
            $Reason .= '<a href="javascript:void(0)" class="without_bg_icon" onclick="return hs.htmlExpand(this,{width:300,headingText:\'Reason for Removal\',wrapperClassName:\'titlebar\',showCredits:false});"><span aria-hidden="true" class="icon-magnifier-add"></span></a>';
            $Reason .= '<div class="highslide-maincontent">' . nl2br($value->varReason) . '</div>';
            $Reason .= '</div>';
        } else {
            $Reason .= 'N/A';
        }

        if ($value->varRequeststatus == "Y") {
            $Request .= 'Confirmed';
        } elseif ($value->varRequeststatus == "N") {
            $Request .= 'Not Confirmed';
        }

        $countRecord1 = '';
        $countRecord = DB::table('contact_lead')->where('varEmail', $value->varEmail)->where('chrPublish','=','Y')->where('chrDelete','=','N')->count();
        $countRecordServiceInquiry = DB::table('serviceinquiry_lead')->where('varEmail', $value->varEmail)->where('chrPublish','=','Y')->where('chrDelete','=','N')->count();
        $countRecordBoatInquiry = DB::table('boatinquiry_lead')->where('varEmail', $value->varEmail)->where('chrPublish','=','Y')->where('chrDelete','=','N')->count();
        $countRecordGetestimate = DB::table('getaestimate_lead')->where('varEmail', $value->varEmail)->where('chrPublish','=','Y')->where('chrDelete','=','N')->count();
        $countRecordNewsletter = DB::table('newsletter_lead')->where('varEmail', $value->varEmail)->where('chrPublish','=','Y')->where('chrDelete','=','N')->count();
        if ($countRecord > 0 || $countRecordGetestimate > 0 || $countRecordNewsletter > 0 || $countRecordServiceInquiry > 0 || $countRecordBoatInquiry > 0) {
            if($countRecord > 0){
                $countRecord1 .= '<lable> Contact Leads Count : </lable><a class="" title="' . trans("dataremovallead::template.dataremovalleadModule.viewmorerecord") . '" href="' . url('powerpanel/contact-us/' . Mylibrary::getDecryptedString($value->varEmail)) . '">' . trans("dataremovallead::template.dataremovalleadModule.viewmorerecord") . ' (' . $countRecord . ')</a></br></br>';
            }
            if($countRecordGetestimate > 0){
                $countRecord1 .= '<lable> Get a Free Estimate Leads Count : </lable><a class="" title="' . trans("dataremovallead::template.dataremovalleadModule.viewmorerecord") . '" href="' . url('powerpanel/get-a-estimate/' . Mylibrary::getDecryptedString($value->varEmail)) . '">' . trans("dataremovallead::template.dataremovalleadModule.viewmorerecord") . ' (' . $countRecordGetestimate . ')</a></br></br>';
            }
            if($countRecordNewsletter > 0){
                $countRecord1 .= '<lable> Newsletter Leads Count : </lable><a class="" title="' . trans("dataremovallead::template.dataremovalleadModule.viewmorerecord") . '" href="' . url('powerpanel/newsletter-lead/' . Mylibrary::getDecryptedString($value->varEmail)) . '">' . trans("dataremovallead::template.dataremovalleadModule.viewmorerecord") . ' (' . $countRecordNewsletter . ')</a></br></br>';
            }
            if($countRecordServiceInquiry > 0){
                $countRecord1 .= '<lable> Service Inquiry Leads Count : </lable><a class="" title="' . trans("dataremovallead::template.dataremovalleadModule.viewmorerecord") . '" href="' . url('powerpanel/service-inquiry/' . Mylibrary::getDecryptedString($value->varEmail)) . '">' . trans("dataremovallead::template.dataremovalleadModule.viewmorerecord") . ' (' . $countRecordServiceInquiry . ')</a></br></br>';
            }
            if($countRecordBoatInquiry > 0){
                $countRecord1 .= '<lable> Boat Inquiry Leads Count : </lable><a class="" title="' . trans("dataremovallead::template.dataremovalleadModule.viewmorerecord") . '" href="' . url('powerpanel/boat-inquiry/' . Mylibrary::getDecryptedString($value->varEmail)) . '">' . trans("dataremovallead::template.dataremovalleadModule.viewmorerecord") . ' (' . $countRecordBoatInquiry . ')</a></br></br>';
            }
        }else{
            $countRecord1 .= 'N/A';
        } 
        

        if($countRecord > 0 || $countRecordGetestimate > 0 || $countRecordNewsletter > 0 || $countRecordServiceInquiry > 0 || $countRecordBoatInquiry > 0){
            $countmessage .= '<div class="pro-act-btn">
                                <a href="javascript:void(0)" class="without_bg_icon" onclick="return hs.htmlExpand(this,{width:300,headingText:\'Record Location\',wrapperClassName:\'titlebar\',showCredits:false});"><span aria-hidden="true" class="icon-magnifier-add"></span></a>
                                <div class="highslide-maincontent">' . nl2br($countRecord1) . '</div>
                            </div>';
        }
        else{
            $countmessage .= 'N/A';
        }

        // if ($countRecord > 0 || $countRecordGetestimate > 0 || $countRecordNewsletter > 0) {
        //     $checkbox = '<div class="checker"><a href = "javascript:;" data-toggle = "tooltip" data-placement = "right" data-toggle = "tooltip" title = "This is module page so can&#39;t be deleted."><i style = "color:red" class = "fa fa-exclamation-triangle"></i></a></div>';
        // }else{
        //     $checkbox = '<input type="checkbox" name="delete[]" class="chkDelete" value="' . $value->id . '">';
        // } 

        if ($value->varRequeststatus == "Y") {
            $checkbox = '<input type="checkbox" name="delete[]" class="chkDelete" value="' . $value->id . '">';
        }else{
            $checkbox = '<div class="checker"><a href = "javascript:;" data-toggle = "tooltip" data-placement = "right" data-toggle = "tooltip" title = "This is module page so can&#39;t be deleted."><i style = "color:red" class = "fa fa-exclamation-triangle"></i></a></div>';
        }

        $records = array(
            //'<input type="checkbox" name="delete[]" class="chkDelete" value="' . $value->id . '">',
            $checkbox,
            $value->varName,
            MyLibrary::getDecryptedString($value->varEmail),
            $Reason,
            $Request,
            $countmessage,
            $value->varIpAddress,
            date('' . Config::get('Constant.DEFAULT_DATE_FORMAT') . ' ' . Config::get('Constant.DEFAULT_TIME_FORMAT') . '', strtotime($value->created_at)),
        );

        return $records;
    }
}
