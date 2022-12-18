<?php

namespace Powerpanel\DocumentReport\Controllers\Powerpanel;

use App\Http\Controllers\PowerpanelController;
use Request;
use Powerpanel\DocumentReport\Models\DocumentsReport;
use App\Helpers\Email_sender;
use Config;
use Illuminate\Support\Facades\Validator;

class DocumentReportController extends PowerpanelController {

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
        $hits_web_mobile = $this->getPageHitChart();
        $this->breadcrumb['title'] = trans('documentreport::template.dcumentreportModule.manageDocumentsReports');
        return view('documentreport::powerpanel.list', ['breadcrumb' => $this->breadcrumb, 'hits_web_mobile' => $hits_web_mobile]);
    }

    public function getPageHitChart() {
        $filter = Request::post();
        $year = isset($filter['year']) ? $filter['year'] : date("Y");
        $hitsChartArr = [['Month', 'Views in Mobile', 'Download in Mobile', 'Views in Desktop', 'Download in Desktop']];
        for ($i = 1; $i <= 12; $i++) {
            $View_Mob = DocumentsReport::getData($year, $i, 'intMobileViewCount');
            $Down_Mob = DocumentsReport::getData($year, $i, 'intMobileDownloadCount');
            $View_Des = DocumentsReport::getData($year, $i, 'intDesktopViewCount');
            $Down_Des = DocumentsReport::getData($year, $i, 'intDesktopDownloadCount');
            $month_name = date('F', mktime(0, 0, 0, $i, 1, 0));
            $hitsChartArr[] = [
                (string) $month_name,
                (isset($View_Mob['intMobileViewCount'])) ? (int) $View_Mob['intMobileViewCount'] : 0,
                (isset($Down_Mob['intMobileDownloadCount'])) ? (int) $Down_Mob['intMobileDownloadCount'] : 0,
                (isset($View_Des['intDesktopViewCount'])) ? (int) $View_Des['intDesktopViewCount'] : 0,
                (isset($Down_Des['intDesktopDownloadCount'])) ? (int) $Down_Des['intDesktopDownloadCount'] : 0,
            ];
        }
        $hits_web_mobile = json_encode($hitsChartArr);
        return $hits_web_mobile;
    }

    public function getSendChart(Request $request) {
        $returnArray = array("success" => "0", "msg" => "something Went Wrong");
        $data = Request::all();
        $messsages = array(
            'Report_Name.required' => 'Name is required',
            'Report_email.required' => 'Email is required',
        );
        $rules = array(
            'Report_Name' => 'required',
            'Report_email' => 'required',
        );

        $validator = Validator::make($data, $rules, $messsages);
        if ($validator->passes()) {
            $img = $data['chart_div'];
            $image_parts = explode(";base64,", $img);
            $image_type_aux = explode("image/", $image_parts[0]);
            $image_type = $image_type_aux[1];
            $image_base64 = base64_decode($image_parts[1]);
            $file = 'DOCUMENTS_REPORT_' . time() . '.' . $image_type;
            $path = public_path() . '/report_img/' . $file;
            file_put_contents($path, $image_base64);
//            --
            $moduleId = Config::get('Constant.MODULE.ID');
//            --
            $year = $data['year'];
            $table = "<table border='1' style='width:100%;border-color:#ddd;font-family:Arial,sans-serif;'>
                        <tr>
                            <th colspan='5'>$year</th>
                        </tr>
                        <tr>
                            <th align='left'>Month</th>
                            <th align='center'>Views in Mobile</th> 
                            <th align='center'>Download in Mobile</th>
                            <th align='center'>Views in Desktop</th>
                            <th align='center'>Download in Desktop</th>
                        </tr>";
            for ($i = 1; $i <= 12; $i++) {
                $View_Mob = DocumentsReport::getData($year, $i, 'intMobileViewCount');
                $Down_Mob = DocumentsReport::getData($year, $i, 'intMobileDownloadCount');
                $View_Des = DocumentsReport::getData($year, $i, 'intDesktopViewCount');
                $Down_Des = DocumentsReport::getData($year, $i, 'intDesktopDownloadCount');
//                ---
                $View_Mob = isset($View_Mob['intMobileViewCount']) ? $View_Mob['intMobileViewCount'] : 0;
                $Down_Mob = isset($Down_Mob['intMobileDownloadCount']) ? $Down_Mob['intMobileDownloadCount'] : 0;
                $View_Des = isset($View_Des['intDesktopViewCount']) ? $View_Des['intDesktopViewCount'] : 0;
                $Down_Des = isset($Down_Des['intDesktopDownloadCount']) ? $Down_Des['intDesktopDownloadCount'] : 0;
                $month_name = date('F', mktime(0, 0, 0, $i, 1, 0));
                $table .= "<tr>
                            <td>$month_name</td>
                            <td align='center'>$View_Mob</td>
                            <td align='center'>$Down_Mob</td>
                            <td align='center'>$View_Des</td>
                            <td align='center'>$Down_Des</td>
                            </tr>";
            }
            $View_Mob = DocumentsReport::DocumentsReport($year, 'intMobileViewCount');
            $Down_Mob = DocumentsReport::DocumentsReport($year, 'intMobileDownloadCount');
            $View_Des = DocumentsReport::DocumentsReport($year, 'intDesktopViewCount');
            $Down_Des = DocumentsReport::DocumentsReport($year, 'intDesktopDownloadCount');
            $table .= "<tr>
                            <th align='left'>Total:</th>
                            <th align='center'>$View_Mob->intMobileViewCount</th>
                            <th align='center'>$Down_Mob->intMobileDownloadCount</th>
                            <th align='center'>$View_Des->intDesktopViewCount</th>
                            <th align='center'>$Down_Des->intDesktopDownloadCount</th>
                        </tr>";
            $table .= "</table>";
            $mailReponse = Email_sender::DocsendReport($data, $file, $table, $moduleId);
            if ($mailReponse == true) {
                $returnArray = array("success" => "1", "msg" => "Report Mail Sent");
            } else {
                $returnArray = array("success" => "0", "msg" => "Mail Not Sent,Please Try again later");
            }
        } else {
            $returnArray = array("success" => "0", "msg" => "Please fill required fields");
        }
        echo json_encode($returnArray);
        exit;
    }

}
