<?php

namespace Powerpanel\ShieldCMSTheme\Controllers\Powerpanel\dashboard;
use App\CommonModel;
use App\Dashboard;
use App\DashboardOrder;
use App\DocumentsReport;
use App\FeedbackLead;
use App\GlobalSearch;
use App\Helpers\MyLibrary;
use App\Http\Controllers\PowerpanelController;
use App\Log;
use App\Modules;
use App\Pagehit;
use App\SubmitTickets;
use App\User;
use App\UserNotification;
use Auth;
use Config;
use File;
use Powerpanel\ContactUsLead\Models\ContactLead;
use Powerpanel\GetaEstimateLead\Models\GetaEstimateLead;
use Powerpanel\ServiceInquiryLead\Models\ServiceinquiryLead;
use Powerpanel\DataRemovalLead\Models\DataRemovalLead;
use Powerpanel\BoatInquiryLead\Models\BoatinquiryLead;
use Powerpanel\Department\Models\Department;
use Powerpanel\NewsletterLead\Models\NewsletterLead;
use Powerpanel\RoleManager\Controllers\Powerpanel\RoleController;
use Powerpanel\RoleManager\Models\Permission_role;
use Powerpanel\RoleManager\Models\Role;
use Powerpanel\Workflow\Models\Comments;
use Powerpanel\Workflow\Models\Workflow;
use Powerpanel\Workflow\Models\WorkflowLog;
use Powerpanel\Services\Models\Services;
use Request;
use DB;

class DashboardController extends PowerpanelController
{
    /*
    |--------------------------------------------------------------------------
    | Dashboard Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling dashboard stats.
    |
    |
    |
     */

    /**
     * Create a new Dashboard controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        $this->middleware('auth');
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
        #Hits Chart======================================
        $hits_web_mobile = $this->getPageHitChart();
        #/.Hits Chart====================================
        #/.Lead Chart====================================
        $searchChart = $this->SearchChart();
        #/.Lead Chart====================================
        #Doc Chart======================================
        $docChartData = $this->getDocChartData();
        #/.Doc Chart====================================
        if ($userIsAdmin) {
            #Workflow Charts======================================
            //            $wf = Self::workflowFunctions();
            //            $availableWorkFlows = $wf['availableWorkFlows'];
            //            $pendingRoleWorkFlows = $wf['pendingRoleWF'];
            #/.Workflow Charts====================================
            #/.Lead Chart====================================
            $leadsChart = $this->LeadChart();
            #/.Lead Chart====================================
        } else {
//            $availableWorkFlows = "[]";
            //            $pendingRoleWorkFlows = "[]";
            $leadsChart = "[]";
        }
        $leads = ContactLead::getRecordList();
        $serviceinquiryleads = ServiceinquiryLead::getRecordList();
        $boatinquiryleads = BoatinquiryLead::getRecordList();
        $getaestimateleads = GetaEstimateLead::getRecordList();
        $newsletterleads = NewsletterLead::getRecordList();
        $dataremovalleads = DataRemovalLead::getRecordList();
        
        $approvals = null;
        if (File::exists(app_path() . '/Workflow.php') != null || File::exists(base_path() . '/packages/Powerpanel/Workflow/src/Models/Workflow.php') != null) {
            $approvals = WorkflowLog::getApprovalListDashbord();

            $moduleInformation = array();
            $recorduserinfo = array();
            $recordRepeatInfo = array();
            foreach ($approvals as $key => $approval) {
                if (!isset($moduleInformation[$approval->fkModuleId]) && empty($moduleInformation[$approval->fkModuleId])) {
                    $module = Modules::getModuleById($approval->fkModuleId);
                    $moduleInformation[$approval->fkModuleId] = $module;
                } else {
                    $module = $moduleInformation[$approval->fkModuleId];
                }

                $modelNameSpace = '---';
                if (isset($module->varModuleNameSpace) && $module->varModuleNameSpace != '') {
                    $modelNameSpace = $module->varModuleNameSpace . 'Models\\' . $module->varModelName;
                } else {
                    $modelNameSpace = '\\App\\' . $module->varModelName;
                }

//            $modelNameSpace = '\\App\\' . $module->varModelName;

                $approval->moduleName = $module->varTitle;
                $approval->varModuleName = $module->varModuleName;
                if (!isset($recordRepeatInfo[$module->varModuleName][$approval->fkRecordId]) && empty($recordRepeatInfo[$module->varModuleName][$approval->fkRecordId])) {
                    $record = CommonModel::getCronRecord($modelNameSpace, $approval->fkRecordId, 'approvals');
                    $recordRepeatInfo[$module->varModuleName][$approval->fkRecordId] = $record;
                } else {
                    $record = $recordRepeatInfo[$module->varModuleName][$approval->fkRecordId];
                }
            }
        }
        $allowcomments = false;
        if (!$userIsAdmin) {
            $allowcomments_all = Dashboard::get_user_comments();
            if (count($allowcomments_all) > 0) {
                foreach ($allowcomments_all as $key => $row_data) {
                    $get_letest_comments = Dashboard::get_letest_comments($row_data->fkMainRecord);
//                    $namespace = '\\App\\' . $row_data->varModuleNameSpace;
                    $namespace = $row_data->varNameSpace;

                    $moduleRecords = $namespace::getRecordById($row_data->fkMainRecord);

                    if (isset($moduleRecords->varTitle)) {
                        $moduleRecTitle = $moduleRecords->varTitle;
                    } else {
                        $moduleRecTitle = "";
                    }
                    if (!empty($moduleRecTitle)) {
                        $allowcomments .= "<tr>";
                        $allowcomments .= "<td align=\"left\">" . $row_data->varModuleTitle . "</td>";
                        $allowcomments .= "<td align=\"center\">" . $moduleRecTitle . "</td>";
                        $allowcomments .= "<td align=\"center\"><a href=\"javascript:;\" onclick=\"loadModelpopup('" . $get_letest_comments . "','" . $row_data->intRecordID . "','" . $row_data->fkMainRecord . "','" . $row_data->varModuleNameSpace . "','" . $row_data->intCommentBy . "','" . $row_data->varModuleTitle . "')\"><i class=\"fa fa-reply-all\"></i></a></td>";
                        $allowcomments .= "</tr>";
                    }
                }
            } else {
                $allowcomments .= "<tr><td colspan=\"4\" align=\"center\">No Comments available.  </td></tr>";
            }
        }
        $allowactivity = false;
//        if (!$userIsAdmin) {
        $allowactivity_all = Dashboard::get_recent_activity();
        if (count($allowactivity_all) > 0) {
            foreach ($allowactivity_all as $key => $row_data) {
                $old_val = '';
                $new_val = '';
                if (strlen($row_data->txtOldVal) > 0 && (strtolower($row_data->varAction) == 'edit' || $row_data->varAction == Config::get('Constant.UPDATE_DRAFT'))) {
                    $old_val .= '<a href="javascript:void(0)" class="without_bg_icon " onclick="return hs.htmlExpand(this,{width:1200,headingText:\'Old Value\',wrapperClassName:\'titlebar\',showCredits:false});"><span aria-hidden="true" class="icon-envelope"></span></a>';
                    $old_val .= '<div class="highslide-maincontent">' . $row_data->txtOldVal . '</div>';
                } else {
                    $old_val .= '-';
                }
                if (strlen($row_data->txtNewVal) > 0 && (strtolower($row_data->varAction) == 'edit' || $row_data->varAction == Config::get('Constant.UPDATE_DRAFT'))) {
                    $new_val .= '<a href="javascript:void(0)" class="without_bg_icon" onclick="return hs.htmlExpand(this,{width:1200,headingText:\'New Value\',wrapperClassName:\'titlebar\',showCredits:false});"><span aria-hidden="true" class="icon-envelope"></span></a>';
                    $new_val .= '<div class="highslide-maincontent">' . $row_data->txtNewVal . '</div>';
                } else {
                    $new_val .= '-';
                }
                if (!isset($moduleInformation[$row_data->fkIntModuleId]) && empty($moduleInformation[$row_data->fkIntModuleId])) {
                    $modulename = Modules::getModuleById($row_data->fkIntModuleId);
                    $moduleInformation[$row_data->fkIntModuleId] = $modulename;
                } else {
                    $modulename = $moduleInformation[$row_data->fkIntModuleId];
                }

                $allowactivity .= "<tr>";
                $allowactivity .= "<td align=\"center\">" . $modulename->varTitle . "</td>";
                $allowactivity .= "<td align=\"center\">" . $row_data->varTitle . "</td>";
                $allowactivity .= "<td align=\"center\">" . $old_val . "</td>";
                $allowactivity .= "<td align=\"center\">" . $new_val . "</td>";
                $allowactivity .= "<td align=\"center\">" . $row_data->varAction . "</td>";
                $allowactivity .= "</tr>";
            }
        } else {
            $allowactivity .= "<tr><td colspan=\"4\" align=\"center\">No records available.  </td></tr>";
        }
//        }
        $submitTicketsleads = null;
        $feedBackleads = null;
        $submitTicketsleads = SubmitTickets::getRecordForDashboardLeadList();
        $feedBackleads = FeedbackLead::getRecordForDashboardLeadList();
        $serviceinquiryleadsCount = count($serviceinquiryleads);
        $currentMonthserviceinquiryleadsCount = ServiceinquiryLead::getCurrentMonthCount();
        $currentYearserviceinquiryleadsCount = ServiceinquiryLead::getCurrentYearCount();
        $boatinquiryleadsCount = count($serviceinquiryleads);
        $currentMonthboatinquiryleadsCount = BoatinquiryLead::getCurrentMonthCount();
        $currentYearboatinquiryleadsCount = BoatinquiryLead::getCurrentYearCount();
        $contactLeadCount = count($leads);
        $currentMonthContactCount = ContactLead::getCurrentMonthCount();
        $currentYearContactCount = ContactLead::getCurrentYearCount();
        $currentMonthGetaEastimateCount = GetaEstimateLead::getCurrentMonthCount();
        $currentYearGetaEastimateCount = GetaEstimateLead::getCurrentYearCount();
        $currentMonthNewsletterCount = NewsletterLead::getCurrentMonthCount();
        $currentYearNewsletterCount = NewsletterLead::getCurrentYearCount();
        $dataremovalLeadCount = count($dataremovalleads);
        $currentMonthDataremovalCount = DataRemovalLead::getCurrentMonthCount();
        $currentYearDataremovalCount = DataRemovalLead::getCurrentYearCount();
        $dashboardOrder = DashboardOrder::getDashboardOrder(auth()->user()->id);
        $dashboardOrder = explode(',', $dashboardOrder);
        $isAdmin = $userIsAdmin;
        $dashboardWidgetArray = array(
            'widget_webhits' => array('widget_name' => 'Website Hits', 'widget_id' => "widget_webhits", 'widget_display' => 'Y'),
            'widget_leadstatistics' => array('widget_name' => 'Leads Statistics', 'widget_id' => "widget_leadstatistics", 'widget_display' => 'Y'),
            'widget_download' => array('widget_name' => 'Document Views & Downloads', 'widget_id' => "widget_download", 'widget_display' => 'Y'),
            'widget_feedbackleads' => array('widget_name' => 'Feedback Leads', 'widget_id' => "widget_feedbackleads", 'widget_display' => 'Y'),
            'widget_conatctleads' => array('widget_name' => 'Contact Leads', 'widget_id' => "widget_conatctleads", 'widget_display' => 'Y'),
            'widget_serviceinquiryleads' => array('widget_name' => 'Service Inquiry Leads', 'widget_id' => "widget_serviceinquiryleads", 'widget_display' => 'Y'),
            'widget_boatinquiryleads' => array('widget_name' => 'Boat Inquiry Leads', 'widget_id' => "widget_boatinquiryleads", 'widget_display' => 'Y'),
            'widget_newsletterleads' => array('widget_name' => 'News Letter Leads', 'widget_id' => "widget_newsletterleads", 'widget_display' => 'Y'),
            'widget_getaestimateleads' => array('widget_name' => 'Get Free Estimate Form Leads', 'widget_id' => "widget_getaestimateleads", 'widget_display' => 'Y'),
            'widget_dataremovalleads' => array('widget_name' => 'Data Removal Leads', 'widget_id' => "widget_dataremovalleads", 'widget_display' => 'Y'),
            'widget_inapporval' => array('widget_name' => 'In Approval', 'widget_id' => "widget_inapporval", 'widget_display' => 'Y'),
//            'widget_recentactivity' => array('widget_name' => 'Recent Activity', 'widget_id' => "widget_recentactivity", 'widget_display' => 'Y'),
        );
        $dashboardWidgetSettingsData = DashboardOrder::dashboardWidgetSettings(auth()->user()->id);
        $dashboardWidgetSettings = array();
        if (isset($dashboardWidgetSettingsData->txtWidgetSetting) && !empty($dashboardWidgetSettingsData->txtWidgetSetting)) {
            $dashboardWidgetSettings = json_decode($dashboardWidgetSettingsData->txtWidgetSetting);
        } else {
            if (!$userIsAdmin) {
                $nonadminWidgetArray = array(
                    'widget_webhits' => array('widget_name' => 'Website Hits', 'widget_id' => "widget_webhits", 'widget_display' => 'Y'),
                    'widget_download' => array('widget_name' => 'Document Views & Downloads', 'widget_id' => "widget_download", 'widget_display' => 'Y'),
                    'widget_commentuser' => array('widget_name' => 'Comments For user', 'widget_id' => "widget_commentuser", 'widget_display' => 'Y'),
                    'widget_recentactivity' => array('widget_name' => 'Recent Activity', 'widget_id' => "widget_recentactivity", 'widget_display' => 'Y'),
                );
                $whereConditions = ['UserID' => auth()->user()->id];
                $update = [
                    'txtWidgetSetting' => json_encode($nonadminWidgetArray),
                ];
                CommonModel::updateRecords($whereConditions, $update, false, 'App\DashboardOrder');
                $dashboardWidgetSettings = json_encode($nonadminWidgetArray);
                $dashboardWidgetSettings = json_decode($dashboardWidgetSettings);
            } else {
                $whereConditions = ['UserID' => auth()->user()->id];
                $update = ['txtWidgetSetting' => json_encode($dashboardWidgetArray)];
                CommonModel::updateRecords($whereConditions, $update, false, 'App\DashboardOrder');
            }
        }
        return view('shiledcmstheme::powerpanel.dashboard.dashboard', compact('isAdmin', 'hits_web_mobile', 'leads','serviceinquiryleads','boatinquiryleads', 'submitTicketsleads', 'feedBackleads', 'contactLeadCount','serviceinquiryleadsCount','boatinquiryleadsCount',  'currentMonthContactCount', 'currentMonthserviceinquiryleadsCount','currentMonthboatinquiryleadsCount','currentYearContactCount','currentYearserviceinquiryleadsCount','currentYearboatinquiryleadsCount', 'allowcomments', 'allowactivity', 'approvals', 'dashboardOrder', 'docChartData', 'leadsChart', 'searchChart', 'dashboardWidgetSettings','getaestimateleads','currentMonthGetaEastimateCount','currentYearGetaEastimateCount','currentYearNewsletterCount','currentMonthNewsletterCount','newsletterleads', 'dataremovalleads', 'dataremovalLeadCount', 'currentMonthDataremovalCount', 'currentYearDataremovalCount'));
    }

    public function ajaxcall()
    {
        $data = Request::all();
        
        switch ($data['type']) {
            case 'contactuslead':
                $contactusleadID = $data['id'];
                $contactusLeadRecord = ContactLead::getRecordById($contactusleadID);
                if (File::exists(base_path() . '/packages/Powerpanel/Department/src/Models/Department.php')) {
                    $contactusLeadRecord->DepartmentName = Department::getRecordforEmailById($contactusLeadRecord->fkIntDepartmentId)->varTitle;
                } else {
                    $contactusLeadRecord->DepartmentName = '';
                }
                $txtUserMessage = MyLibrary::getDecryptedString($contactusLeadRecord->txtUserMessage);
                $contactusLeadRecord->varEmail = MyLibrary::getDecryptedString($contactusLeadRecord->varEmail);
                $contactusLeadRecord->varPhoneNo = MyLibrary::getDecryptedString($contactusLeadRecord->varPhoneNo);
                $contactusLeadRecord->txtUserMessage = nl2br($txtUserMessage);
                
                echo json_encode($contactusLeadRecord);
                break;
            case 'serviceinquirylead':
                $serviceinquiryleadID = $data['id'];
                $serviceinquiryLeadRecord = ServiceinquiryLead::getRecordById($serviceinquiryleadID);
                if (File::exists(base_path() . '/packages/Powerpanel/Department/src/Models/Department.php')) {
                    $serviceinquiryLeadRecord->DepartmentName = Department::getRecordforEmailById($serviceinquiryLeadRecord->fkIntDepartmentId)->varTitle;
                } else {
                    $serviceinquiryLeadRecord->DepartmentName = '';
                }
                $txtUserMessage = MyLibrary::getDecryptedString($serviceinquiryLeadRecord->txtUserMessage);
                $serviceinquiryLeadRecord->varEmail = MyLibrary::getDecryptedString($serviceinquiryLeadRecord->varEmail);
                $serviceinquiryLeadRecord->varPhoneNo = MyLibrary::getDecryptedString($serviceinquiryLeadRecord->varPhoneNo);
                $serviceinquiryLeadRecord->txtUserMessage = nl2br($txtUserMessage);
                echo json_encode($serviceinquiryLeadRecord);
                break;
            case 'boatinquirylead':
                $boatinquiryleadID = $data['id'];
                $boatinquiryLeadRecord = BoatinquiryLead::getRecordById($boatinquiryleadID);
                if (File::exists(base_path() . '/packages/Powerpanel/Department/src/Models/Department.php')) {
                    $boatinquiryLeadRecord->DepartmentName = Department::getRecordforEmailById($boatinquiryLeadRecord->fkIntDepartmentId)->varTitle;
                } else {
                    $boatinquiryLeadRecord->DepartmentName = '';
                }
                $txtUserMessage = MyLibrary::getDecryptedString($boatinquiryLeadRecord->txtUserMessage);
                $boatinquiryLeadRecord->varEmail = MyLibrary::getDecryptedString($boatinquiryLeadRecord->varEmail);
                $boatinquiryLeadRecord->varPhoneNo = MyLibrary::getDecryptedString($boatinquiryLeadRecord->varPhoneNo);
                $boatinquiryLeadRecord->txtUserMessage = nl2br($txtUserMessage);
                echo json_encode($boatinquiryLeadRecord);
                break;
            case 'getaestimatelead':
                $getaestimateleadID = $data['id'];
                $getaestimateLeadRecord = GetaEstimateLead::getRecordById($getaestimateleadID);
                if (File::exists(base_path() . '/packages/Powerpanel/Department/src/Models/Department.php')) {
                    $getaestimateLeadRecord->DepartmentName = Department::getRecordforEmailById($getaestimateLeadRecord->fkIntDepartmentId)->varTitle;
                } else {
                    $getaestimateLeadRecord->DepartmentName = '';
                }
                $txtUserMessage = MyLibrary::getDecryptedString($getaestimateLeadRecord->txtUserMessage);
                $getaestimateLeadRecord->varEmail = MyLibrary::getDecryptedString($getaestimateLeadRecord->varEmail);
                $getaestimateLeadRecord->varPhoneNo = MyLibrary::getDecryptedString($getaestimateLeadRecord->varPhoneNo);
                $getaestimateLeadRecord->txtUserMessage = nl2br($txtUserMessage);
                echo json_encode($getaestimateLeadRecord);
                break;
            case 'dataremovallead':
                $dataremovalleadID = $data['id'];
                $dataremovalLeadRecord = DataRemovalLead::getRecordById($dataremovalleadID);
                $varEmail = $dataremovalLeadRecord->varEmail;
                $decryptvarEmail = MyLibrary::getDecryptedString($dataremovalLeadRecord->varEmail);
                $dataremovalLeadRecord->varReason = isset($dataremovalLeadRecord->varReason) && !empty($dataremovalLeadRecord->varReason) ? nl2br($dataremovalLeadRecord->varReason) : 'N/A' ;
                $dataremovalLeadRecord->varRequeststatus = isset($dataremovalLeadRecord->varRequeststatus) && !empty($dataremovalLeadRecord->varRequeststatus) && $dataremovalLeadRecord->varRequeststatus == "N"  ? 'Not Confirmed' : 'Confirmed' ;

                $countRecord1 = '';
                $countmessage = '';
                $countRecord = DB::table('contact_lead')->where('varEmail', $varEmail)->where('chrPublish', '=', 'Y')->where('chrDelete', '=', 'N')->count();
                $countRecordServiceInquiry = DB::table('serviceinquiry_lead')->where('varEmail', $varEmail)->where('chrPublish','=','Y')->where('chrDelete','=','N')->count();
                $countRecordBoatInquiry = DB::table('boatinquiry_lead')->where('varEmail', $varEmail)->where('chrPublish','=','Y')->where('chrDelete','=','N')->count();
                $countRecordGetestimate = DB::table('getaestimate_lead')->where('varEmail', $varEmail)->where('chrPublish','=','Y')->where('chrDelete','=','N')->count();
                $countRecordNewsletter = DB::table('newsletter_lead')->where('varEmail', $varEmail)->where('chrPublish','=','Y')->where('chrDelete','=','N')->count();
                if ($countRecord > 0 || $countRecordGetestimate > 0 || $countRecordNewsletter > 0 || $countRecordServiceInquiry > 0 || $countRecordBoatInquiry > 0) {
                    if ($countRecord > 0) {
                        $countRecord1 .= '<lable> Contact Leads Count : </lable><a class="" title="' . trans("View Record location") . '" href="' . url('powerpanel/contact-us/' . Mylibrary::getDecryptedString($decryptvarEmail)) . '">' . trans("dataremovallead::template.dataremovalleadModule.viewmorerecord") . ' (' . $countRecord . ')</a></br></br>';
                    }
                    if ($countRecordGetestimate > 0) {
                        $countRecord1 .= '<lable> Get a Free Estimate Leads Count : </lable><a class="" title="' . trans("View Record location") . '" href="' . url('powerpanel/get-a-estimate/' . Mylibrary::getDecryptedString($decryptvarEmail)) . '">' . trans("dataremovallead::template.dataremovalleadModule.viewmorerecord") . ' (' . $countRecordGetestimate . ')</a></br></br>';
                    }
                    if ($countRecordNewsletter > 0) {
                        $countRecord1 .= '<lable> Newsletter Leads Count : </lable><a class="" title="' . trans("View Record location") . '" href="' . url('powerpanel/newsletter-lead/' . Mylibrary::getDecryptedString($decryptvarEmail)) . '">' . trans("dataremovallead::template.dataremovalleadModule.viewmorerecord") . ' (' . $countRecordNewsletter . ')</a></br></br>';
                    }
                    if($countRecordServiceInquiry > 0){
                        $countRecord1 .= '<lable> Service Inquiry Leads Count : </lable><a class="" title="' . trans("View Record location") . '" href="' . url('powerpanel/service-inquiry/' . Mylibrary::getDecryptedString($decryptvarEmail)) . '">' . trans("dataremovallead::template.dataremovalleadModule.viewmorerecord") . ' (' . $countRecordServiceInquiry . ')</a></br></br>';
                    }
                    if($countRecordBoatInquiry > 0){
                        $countRecord1 .= '<lable> Boat Inquiry Leads Count : </lable><a class="" title="' . trans("View Record location") . '" href="' . url('powerpanel/boat-inquiry/' . Mylibrary::getDecryptedString($decryptvarEmail)) . '">' . trans("dataremovallead::template.dataremovalleadModule.viewmorerecord") . ' (' . $countRecordBoatInquiry . ')</a></br></br>';
                    }
                } else {
                    $countRecord1 .= 'N/A';
                }

                if ($countRecord > 0 || $countRecordGetestimate > 0 || $countRecordNewsletter > 0 || $countRecordServiceInquiry > 0 || $countRecordBoatInquiry > 0) {
                    $countmessage .=  '<br><br>'.nl2br($countRecord1);
                } else {
                    $countmessage .= 'N/A';
                }

                $dataremovalLeadRecord->countmessage = $countmessage;

                echo json_encode($dataremovalLeadRecord);
                break;
            default:
                echo "error";
                break;
        }
    }

    public function Get_Comments_user(Request $request)
    {
        $requestArr = Request::all();
        $request = (object) $requestArr;
        $templateData = Dashboard::get_comments_user($request);
        $Comments = "";
        if (count($templateData) > 0) {
            foreach ($templateData as $row_data) {
                if ($row_data->Fk_ParentCommentId == 0) {
                    $Comments .= '<li><p>' . nl2br($row_data->varCmsPageComments) . '</p><span class = "date">' . CommonModel::getUserName($row_data->intCommentBy) . ' ' . date('M d Y h:i A', strtotime($row_data->created_at)) . '</span></li>';
                    $UserComments = Dashboard::get_usercomments($row_data->id);
                    foreach ($UserComments as $row_comments) {
                        $Comments .= '<li class = "user-comments"><p>' . nl2br($row_comments->varCmsPageComments) . '</p><span class = "date">' . CommonModel::getUserName($row_comments->UserID) . ' ' . date('M d Y h:i A', strtotime($row_comments->created_at)) . '</span></li>';
                    }
                }
            }
        } else {
            $Comments .= '<li><p>No Comments yet.</p></li>';
        }
        echo $Comments;
        exit;
    }

    public function InsertComments_user(Request $request)
    {
        $requestArr = Request::all();
        $request = (object) $requestArr;
        $commentModuleData = Modules::getModuleByModelName(Request::post('varModuleNameSpace'));

        if ($commentModuleData['varModuleNameSpace'] != '') {
            $modelNameSpace = $commentModuleData['varModuleNameSpace'] . 'Models\\' . $commentModuleData['varModelName'];
        } else {
            $modelNameSpace = '\\App\\' . Request::post('varModuleNameSpace');
        }
        $Comments_data['Fk_ParentCommentId'] = Request::post('id');
        $Comments_data['intRecordID'] = Request::post('intRecordID');
        $Comments_data['fkMainRecord'] = Request::post('fkMainRecord');
        $Comments_data['varModuleNameSpace'] = Request::post('varModuleNameSpace');
        $Comments_data['varNameSpace'] = $modelNameSpace;
        $Comments_data['varCmsPageComments'] = Request::post('CmsPageComments_user');
        $Comments_data['UserID'] = auth()->user()->id;
        $Comments_data['intCommentBy'] = auth()->user()->id;
        $Comments_data['varModuleTitle'] = Request::post('varModuleTitle');
        Comments::insertComents($Comments_data);
        /* code for insert comment */
        $parentCommentId = Request::post('id');
        $parentCommentData = Comments::get_commentDetailForNotificationById($parentCommentId);
        $parentCommentedUserId = 0;
        if (!empty($parentCommentData)) {
            $parentCommentedUserId = $parentCommentData->intCommentBy;
        }

        $commentModuleId = 0;
        if (!empty($commentModuleData)) {
            $commentModuleId = $commentModuleData->id;
        }
        if ($commentModuleId > 0) {
            $userNotificationArr = MyLibrary::userNotificationData($commentModuleId);
            $userNotificationArr['fkRecordId'] = Request::post('intRecordID');
            $userNotificationArr['txtNotification'] = ucfirst(auth()->user()->name) . ' has replied on your comment ' . '(' . ucfirst(Request::post('varModuleTitle')) . ')';
            $userNotificationArr['fkIntUserId'] = auth()->user()->id;
            $userNotificationArr['chrNotificationType'] = 'C';
            $userNotificationArr['intOnlyForUserId'] = $parentCommentedUserId;
            UserNotification::addRecord($userNotificationArr);
//            $modelNameSpace = '\\App\\' . $commentModuleData['varModelName'];
            $commentdata = Config::get('Constant.REPLIED_COMMENT_ADDED');
            $newCmsPageObj = $modelNameSpace::getRecordForLogById(Request::post('intRecordID'));
            $logArr = MyLibrary::logData(Request::post('intRecordID'), $commentModuleId, $commentdata);
            $logArr['varTitle'] = stripslashes($newCmsPageObj->varTitle);
            Log::recordLog($logArr);
        }
        exit;
    }

    public function updateorder(Request $request)
    {
        $Allorder = Request::post('order');
        DashboardOrder::UpdateDisplayOrder($Allorder, auth()->user()->id);
    }

    public function updatedashboardsettings(Request $request)
    {
        $widget_key = Request::post('widgetkey');
        $widget_disp = Request::post('widget_disp');
        $UserId = auth()->user()->id;
        $dashboardWidgetSettingsData = DashboardOrder::dashboardWidgetSettings($UserId);
        $dashboardWidgetSettings = array();
        if (!empty($dashboardWidgetSettingsData)) {
            $dashboardWidgetSettings = json_decode($dashboardWidgetSettingsData->txtWidgetSetting, true);
            $dashboardWidgetSettings[$widget_key]['widget_display'] = $widget_disp;
            $updatedjson = json_encode($dashboardWidgetSettings);
            $whereConditions = ['UserID' => $UserId];
            $update = [
                'txtWidgetSetting' => $updatedjson,
            ];
            CommonModel::updateRecords($whereConditions, $update, false, 'App\DashboardOrder');
        }
        exit;
    }

    public static function workflowFunctions()
    {
        #Workflow funcions======================================
        $availableWorkFlows = Workflow::getApprovalWorkFlowsDashboard();
        $wf_moduleInformation = array();
        $wf_admusers = array();
        foreach ($availableWorkFlows as $wf) {
            $wf->varUserId = str_replace('1', '0', $wf->varUserId); //ignoring super admin

            if (!isset($wf_admusers[$wf->varUserId]) && empty($wf_admusers[$wf->varUserId])) {
                if (!empty($wf->varUserId)) {
                    $wf->adminusers = User::getRecordByIdIn(explode(', ', $wf->varUserId))->toArray();
                    if (!empty($wf->adminusers)) {
                        $wf_admusers[$wf->varUserId] = $wf->adminusers;
                    }
                } else {
                    $wf->adminusers = array();
                    $wf_admusers[$wf->varUserId] = $wf->adminusers;
                }
            } else {
                $wf->adminusers = $wf_admusers[$wf->varUserId];
            }

            if (!isset($wf_moduleInformation[$wf->intModuleId]) && empty($wf_moduleInformation[$wf->intModuleId])) {
                $moduledata = Modules::getModuleById($wf->intModuleId);
                $wf_moduleInformation[$wf->intModuleId] = $moduledata;
            } else {
                $moduledata = $wf_moduleInformation[$wf->intModuleId];
            }

            if (!empty($moduledata)) {
                $wf->moduleTitle = $moduledata->varTitle;
            } else {
                $wf->moduleTitle = ' ---';
            }
        }
        $nonAndminRoles = Role::getNonAdmins();
        $moduleCategory = RoleController::groups();
        $pendingRoleWF = [];
        foreach ($nonAndminRoles as $key => $nonAndminRole) {
            $roleCan = RoleController::groups($nonAndminRole->id);
            $roleCan1 = Permission_role::getPermissionRole($nonAndminRole->id);
            $roleCan1 = array_column($roleCan1, 'permission_role');
            $userroleassignedmodules = array_column($roleCan1, 'intFKModuleCode');
            foreach ($moduleCategory as $key => $category) {
                if ($category != 'Logs' && $category != 'User Management' && $category != 'Leads' && $category != 'Miscellaneous') {
                    $modules = Modules::getModulesBycategory($key);
                    $useraddeditModules = array();
                    foreach ($roleCan1 as $rolval) {
                        if ($rolval['display_name'] == "per_add" || $rolval['display_name'] == "per_edit") {
                            array_push($useraddeditModules, $rolval['intFKModuleCode']);
                        }
                    }
                    if (!empty($modules)) {
                        foreach ($modules as $module) {
                            if (in_array($module->id, $useraddeditModules)) {
                                if (in_array($category, $roleCan)) {
                                    $whereArray = [
                                        'varUserRoles' => $nonAndminRole->id,
                                        //'intCategoryId' => $key,
                                        'intModuleId' => $module->id,
                                    ];
                                    $add = Workflow::getPendingWorkFlows($whereArray);
                                    $whereArray = [
                                        'varUserRoles' => $nonAndminRole->id,
                                        //'intCategoryId' => $key,
                                        'intModuleId' => $module->id,
                                        'charNeedApproval' => 'N',
                                        'chrNeedAddPermission' => 'N',
                                    ];
                                    $directApproved = Workflow::getPendingWorkFlows($whereArray);
                                    if (empty($add)) {
                                        $addWfArr = [
                                            'category' => $category,
                                            'role' => $nonAndminRole->display_name,
                                            'modulename' => $module->varTitle,
                                            'action' => 'Add/Update',
                                        ];
                                        if (isset($add->id)) {
                                            $addWfArr['id'] = $add->id;
                                        }
                                        if (empty($directApproved)) {
                                            $pendingRoleWF[] = $addWfArr;
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
        $pendingRefine = [];
        foreach ($pendingRoleWF as $pwf) {
            $actionArr = [];
            $actionArr['action'] = $pwf['action'];
            $actionArr['modulename'] = $pwf['modulename'];
            if (isset($pwf['id'])) {
                $actionArr['id'] = $pwf['id'];
            }
            //$pendingRefine[$pwf['role']]['category'][$pwf['category']][] = $actionArr;
            $pendingRefine[$pwf['role']][] = $actionArr;

        }
        $response = [
            'availableWorkFlows' => $availableWorkFlows,
            'pendingRoleWF' => $pendingRefine,
        ];
        return $response;
        #./Workflow funcions====================================
    }

    public function getPageHitChart()
    {
        $filter = Request::post();
        $filter_year = isset($filter['year']) ? $filter['year'] : 3;
        $earliest_year = date("Y") - $filter_year;
        $latest_year = date('Y');
        $timeparam = isset($filter['timeparam']) ? $filter['timeparam'] : 'year';
        if ($filter_year != 0) {
            $hitsChartArr[] = [ucfirst($timeparam . 's'), 'Web', 'Mobile'];
        } else {
            $hitsChartArr[] = [ucfirst($timeparam), 'Web', 'Mobile'];
        }
        if ($timeparam != 'month') {
            foreach (range($latest_year, $earliest_year) as $i) {
                $hits_web = Pagehit::getHitsWebMobileHitsyears($i, 'Y', $timeparam);
                $mobile_web = Pagehit::getHitsWebMobileHitsyears($i, 'N', $timeparam);
                $hitsChartArr[] = [
                    (string) $i,
                    (int) $hits_web,
                    (int) $mobile_web,
                ];
            }
        } else {
            if ($filter['year'] != 2) {
                $current_month = date('m');
                $last_month = $current_month - $filter['year'];
            } else {
                $current_month = 1;
                $last_month = 12;
            }
            foreach (range($current_month, $last_month) as $i) {
                $year = date('Y');
                $hits_web = Pagehit::getHitsWebMobileHitsyears($year, 'Y', $timeparam, $i);
                $mobile_web = Pagehit::getHitsWebMobileHitsyears($year, 'N', $timeparam, $i);
                if ($filter['year'] != 2) {
                    $month_name = date('F', mktime(0, 0, 0, $i, 1, 0));
                } else {
                    $month_name = $i;
                }
                $hitsChartArr[] = [
                    (string) $month_name,
                    (int) $hits_web,
                    (int) $mobile_web,
                ];
            }
        }
        $hits_web_mobile = json_encode($hitsChartArr);
        return $hits_web_mobile;
    }

    public function getDocChartData()
    {
        $filter = Request::post();
        $filter_year = isset($filter['year']) ? $filter['year'] : 4;
        $earliest_year = date("Y") - $filter_year;
        $latest_year = date('Y');
        $timeparam = isset($filter['timeparam']) ? $filter['timeparam'] : 'year';
        $chartArr[] = [ucfirst($timeparam . 's'), 'Views in Mobile', 'Downloads in Mobile', 'Views in Desktop', 'Downloads in Desktop'];
        if ($timeparam != 'month') {
            foreach (range($latest_year, $earliest_year) as $i) {
                $View_Mob = DocumentsReport::DocumentsReport($i, 'intMobileViewCount');
                $Down_Mob = DocumentsReport::DocumentsReport($i, 'intMobileDownloadCount');
                $View_Des = DocumentsReport::DocumentsReport($i, 'intDesktopViewCount');
                $Down_Des = DocumentsReport::DocumentsReport($i, 'intDesktopDownloadCount');
                $chartArr[] = [
                    (string) $i,
                    (int) $View_Mob->intMobileViewCount,
                    (int) $Down_Mob->intMobileDownloadCount,
                    (int) $View_Des->intDesktopViewCount,
                    (int) $Down_Des->intDesktopDownloadCount,
                ];
            }
        } else {
            if ($filter['year'] != 0) {
                $current_month = date('m');
                $last_month = $current_month - $filter['year'];
            } else {
                $current_month = 1;
                $last_month = 12;
            }
            foreach (range($current_month, $last_month) as $i) {
                $year = date('Y');
                $View_Mob = DocumentsReport::getData($year, $i, 'intMobileViewCount');
                $Down_Mob = DocumentsReport::getData($year, $i, 'intMobileDownloadCount');
                $View_Des = DocumentsReport::getData($year, $i, 'intDesktopViewCount');
                $Down_Des = DocumentsReport::getData($year, $i, 'intDesktopDownloadCount');
                if ($filter['year'] != 0) {
                    $month_name = date('F', mktime(0, 0, 0, $i, 1, 0));
                } else {
                    $month_name = $i;
                }
                $chartArr[] = [
                    (string) $month_name,
                    (int) $View_Mob['intMobileViewCount'],
                    (int) $Down_Mob['intMobileDownloadCount'],
                    (int) $View_Des['intDesktopViewCount'],
                    (int) $Down_Des['intDesktopDownloadCount'],
                ];
            }
        }
        $docChartData = json_encode($chartArr);
        return $docChartData;
    }

    public function LeadChart()
    {
        $filter = Request::post();
        $filter_year = isset($filter['year']) ? $filter['year'] : 4;
        $earliest_year = date("Y") - $filter_year;
        $latest_year = date('Y');
        $timeparam = isset($filter['timeparam']) ? $filter['timeparam'] : 'year';
        $chartArr[] = [ucfirst($timeparam . 's'), 'Contact','Service Inquiry','Boat Inquiry','Data Removal'];
        if ($timeparam != 'month') {
            foreach (range($latest_year, $earliest_year) as $i) {
                $Contactleads = ContactLead::getRecordListDashboard($i, $timeparam);
                $Serviceinquiryleads = ServiceinquiryLead::getRecordListDashboard($i, $timeparam);
                $Boatinquiryleads = BoatinquiryLead::getRecordListDashboard($i, $timeparam);
                $Feedbackleads = FeedbackLead::getRecordListDashboard($i, $timeparam);
                $Getaestimateleads = GetaEstimateLead::getRecordListDashboard($i, $timeparam);
                $Dataremovalleads = 0;
                if (File::exists(base_path() . '/packages/Powerpanel/DataRemovalLead/src/Models/DataRemovalLead.php')) {
                    $Dataremovalleads = DataRemovalLead::getRecordListDashboard($i, $timeparam);
                }

                $Newsletterleads = 0;
                if (File::exists(base_path() . '/packages/Powerpanel/NewsletterLead/src/Models/NewsletterLead.php')) {
                    $Newsletterleads = NewsletterLead::getRecordListDashboard($i, $timeparam);
                }
                $chartArr[] = [
                    (string) $i,
                    (int) $Contactleads,
                    (int) $Serviceinquiryleads,
                    (int) $Boatinquiryleads,
                    (int) $Dataremovalleads,
                    // (int) $Feedbackleads,
                    // (int) $Newsletterleads,
                    // (int) $Getaestimateleads,
                ];
            }
        } else {
            if ($filter['year'] != 0) {
                $current_month = date('m');
                $last_month = $current_month - $filter['year'];
            } else {
                $current_month = 1;
                $last_month = 12;
            }
            foreach (range($current_month, $last_month) as $i) {
                $year = date('Y');
                $Contactleads = ContactLead::getRecordListDashboard($year, $timeparam, $i);
                $Serviceinquiryleads = ServiceinquiryLead::getRecordListDashboard($year, $timeparam, $i);
                $Boatinquiryleads = BoatinquiryLead::getRecordListDashboard($year, $timeparam, $i);
                $Feedbackleads = FeedbackLead::getRecordListDashboard($year, $timeparam, $i);
                $Newsletterleads = NewsletterLead::getRecordListDashboard($year, $timeparam, $i);
                $Getaestimateleads = GetaEstimateLead::getRecordListDashboard($year, $timeparam, $i);
                $Dataremovalleads = DataRemovalLead::getRecordListDashboard($year, $timeparam, $i);
                if ($filter['year'] != 0) {
                    $month_name = date('F', mktime(0, 0, 0, $i, 1, 0));
                } else {
                    $month_name = $i;
                }
                $chartArr[] = [
                    (string) $month_name,
                    (int) $Contactleads,
                    (int) $Serviceinquiryleads,
                    (int) $Boatinquiryleads,
                    (int) $Dataremovalleads,
                    // (int) $Feedbackleads,
                    // (int) $Newsletterleads,
                    // (int) $Getaestimateleads,
                ];
            }
        }
        $docChartData = json_encode($chartArr);
        return $docChartData;
    }

    public function SearchChart()
    {
        $final_array = array();
        $filter = Request::post();
        $year = isset($filter['year']) ? $filter['year'] : 4;
        $timeparam = isset($filter['timeparam']) ? $filter['timeparam'] : 'year';
        $dataArr = array();
        $searchDara = GlobalSearch::getRecordListDashboard($year, $timeparam);
        foreach ($searchDara as $key => $value) {
            $final_array[$value['Year']] = (!empty($value['SearchCount'])) ? $value['SearchCount'] : 0;
        }
        $chartArr[] = ['Year', 'Hits'];
        foreach ($final_array as $key => $value) {
            $chartArr[] = [
                (string) $key,
                (int) $value,
            ];
        }
        $searchChartData = $chartArr;
        $searchChartData = json_encode($searchChartData);
        return $searchChartData;
    }

}
