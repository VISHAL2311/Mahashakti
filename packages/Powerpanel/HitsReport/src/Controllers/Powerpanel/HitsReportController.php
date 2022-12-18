<?php

namespace Powerpanel\HitsReport\Controllers\Powerpanel;

use App\Http\Controllers\PowerpanelController;
use Request;
use Powerpanel\HitsReport\Models\HitsReport;
use App\Helpers\Email_sender;
use Config;
use Illuminate\Support\Facades\Validator;

class HitsReportController extends PowerpanelController {

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
        $this->breadcrumb['title'] = trans('hitsreport::template.hitsreportModule.manageHitsReports');
        return view('hitsreport::powerpanel.list', ['breadcrumb' => $this->breadcrumb, 'hits_web_mobile' => $hits_web_mobile]);
    }

    public function getPageHitChart() {
        $filter = Request::post();
        $year = isset($filter['year']) ? $filter['year'] : date("Y");
        $hitsChartArr = [['Month', 'Web', 'Mobile']];
        for ($i = 1; $i <= 12; $i++) {
            $hits_web = HitsReport::getHitsWebHitsyears($year, $i, 'Y');
            $Mobile_web = HitsReport::getHitsWebHitsyears($year, $i, 'N');
            $month_name = date('F', mktime(0, 0, 0, $i, 1, 0));
            $hitsChartArr[] = [
                (string) $month_name,
                (int) $hits_web,
                (int) $Mobile_web,
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
            $file = 'PAGE_HITS_' . time() . '.' . $image_type;
            $path = public_path() . '/report_img/' . $file;
            file_put_contents($path, $image_base64);
//            --
            $moduleId = Config::get('Constant.MODULE.ID');
//            --
            $year = $data['year'];
            $table = "<table border='1' style='width:100%;border-color:#ddd;font-family:Arial,sans-serif;'>
                        <tr>
                            <th colspan='3'>$year</th>
                        </tr>
                        <tr>
                            <th align='left'>Month</th>
                            <th align='center'>Web</th> 
                            <th align='center'>Mobile</th>
                        </tr>";
            for ($i = 1; $i <= 12; $i++) {
                $hits_web = HitsReport::getHitsWebHitsyears($year, $i, 'Y');
                $Mobile_web = HitsReport::getHitsWebHitsyears($year, $i, 'N');
                $month_name = date('F', mktime(0, 0, 0, $i, 1, 0));
                $table .= "<tr>
                            <td>$month_name</td>
                            <td align='center'>$hits_web</td>
                            <td align='center'>$Mobile_web</td>
                        </tr>";
            }

            $hits_web_sum = HitsReport::getSumWebHitsyears($year, 'Y');
            $Mobile_web_sum = HitsReport::getSumWebHitsyears($year, 'N');
            $table .= "<tr>
                            <th align='left'>Total:</th>
                            <th align='center'>$hits_web_sum</th>
                            <th align='center'>$Mobile_web_sum</th>
                        </tr>";
            $table .= "</table>";
            $mailReponse = Email_sender::sendReport($data, $file, $table, $moduleId);
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
