<?php
namespace Powerpanel\Testimonial\Controllers\Powerpanel;

use App\Alias;
use App\CommonModel;
use App\Helpers\AddImageModelRel;
use App\Helpers\MyLibrary;
use App\Helpers\resize_image;
use App\Http\Controllers\PowerpanelController;
use App\Log;
use App\RecentUpdates;
use Powerpanel\Testimonial\Models\Testimonial;
use Auth;
use Cache;
use Carbon\Carbon;
use Config;
use Illuminate\Support\Facades\Redirect;
use Request;
use Validator;

class TestimonialController extends PowerpanelController
{
    /**
     * Create a new controller instance.
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        if (isset($_COOKIE['locale'])) {
            app()->setLocale($_COOKIE['locale']);
        }
    }

    /**
     * This method handels load testimonial grid
     * @return  View
     * @since   2017-07-20
     * @author  NetQuick
     */
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
        $total = CommonModel::getRecordCount(false,false,false, 'Powerpanel\Testimonial\Models\Testimonial');
        $this->breadcrumb['title'] = trans('testimonial::template.testimonialModule.manageTestimonials');
        return view('testimonial::powerpanel.list_testimonial', ['total' => $total, 'breadcrumb' => $this->breadcrumb,'userIsAdmin' => $userIsAdmin]);
    }

    /**
     * This method handels list of testimonial with filters
     * @return  View
     * @since   2017-07-20
     * @author  NetQuick
     */
    public function get_list()
    {

        /*Start code for sorting*/
        $filterArr = [];
        $records = array();

        $records["data"] = array();
        $filterArr['orderColumnNo'] = (!empty(Request::get('order')[0]['column']) ? Request::get('order')[0]['column'] : '');
        $filterArr['orderByFieldName'] = (!empty(Request::get('columns')[$filterArr['orderColumnNo']]['name']) ? Request::get('columns')[$filterArr['orderColumnNo']]['name'] : '');
        $filterArr['orderTypeAscOrDesc'] = (!empty(Request::get('order')[0]['dir']) ? Request::get('order')[0]['dir'] : '');
        $filterArr['searchFilter'] = !empty(Request::get('searchValue')) ? Request::get('searchValue') : '';
        $filterArr['statusFilter'] = !empty(Request::get('statusFilter')) ? Request::get('statusFilter') : '';
        $filterArr['dateFilter'] = !empty(Request::get('dateValue')) ? Request::get('dateValue') : '';
        $filterArr['iDisplayLength'] = intval(Request::get('length'));
        $filterArr['iDisplayStart'] = intval(Request::get('start'));

        $sEcho = intval(Request::get('draw'));

        $arrResults = Testimonial::getRecordList($filterArr);
        $iTotalRecords = CommonModel::getRecordCount($filterArr, true,false, 'Powerpanel\Testimonial\Models\Testimonial');
        $end = $filterArr['iDisplayStart'] + $filterArr['iDisplayLength'];
        $end = $end > $iTotalRecords ? $iTotalRecords : $end;

        if ($arrResults->count() > 0 && !empty($arrResults)) {
            foreach ($arrResults as $key => $value) {
                $records["data"][] = $this->tableData($value);
            }
        }
        $records["customActionStatus"] = "OK";
        $records["draw"] = $sEcho;
        $records["recordsTotal"] = $iTotalRecords;
        $records["recordsFiltered"] = $iTotalRecords;
        echo json_encode($records);
        exit;

    }


    /**
     * This method loads product builder data on view
     * @return  View
     * @since   2020-02-05
     * @author  NetQuick
     */
    public function get_builder_list()
    {
        $filter = Request::post();
        $rows = '';
        $filterArr = [];
        $records = [];
        $filterArr['orderByFieldName'] = isset($filter['columns']) ? $filter['columns'] : '';
        $filterArr['orderTypeAscOrDesc'] = isset($filter['order']) ? $filter['order'] : '';
        $filterArr['critaria'] = isset($filter['critaria']) ? $filter['critaria'] : '';
        $filterArr['searchFilter'] = isset($filter['searchValue']) ? trim($filter['searchValue']) : '';
        $filterArr['iDisplayStart'] = isset($filter['start']) ? intval($filter['start']) : 1;
        $filterArr['iDisplayLength'] = isset($filter['length']) ? intval($filter['length']) : 5;
        $filterArr['ignore'] = !empty($filter['ignore']) ? $filter['ignore'] : [];
        $filterArr['selected'] = isset($filter['selected']) && !empty($filter['selected']) ? $filter['selected'] : [];
        $arrResults = Testimonial::getBuilderRecordList($filterArr);
        $found = $arrResults->toArray();

        if (!empty($found)) {
            foreach ($arrResults as $key => $value) {
                $rows .= $this->tableDataBuilder($value, false, $filterArr['selected']);
            }
        } else {
            $rows .= '<tr id="not-found"><td colspan="4" align="center">No records found.</td></tr>';
        }
        $iTotalRecords = CommonModel::getTotalRecordCount('Powerpanel\Testimonial\Models\Testimonial', true);
        $records["data"] = $rows;
        $records["found"] = count($found);
        $records["recordsTotal"] = $iTotalRecords;
        return json_encode($records);
    }

    public function tableDataBuilder($value = false, $fcnt = false, $selected = [])
    {
        
        $dtFormat = Config::get('Constant.DEFAULT_DATE_FORMAT');
        $record = '<tr ' . (in_array($value->id, $selected) ? 'class="selected-record"' : '') . '>';
        $record .= '<td width="5%" align="center">';
        $record .= '<label class="mt-checkbox mt-checkbox-outline">';
        $record .= '<input type="checkbox" data-title="' . $value->varTitle . '" name="delete[]" class="chkChoose" ' . (in_array($value->id, $selected) ? 'checked' : '') . ' value="' . $value->id . '">';
        $record .= '<span></span>';
        $record .= '</label>';
        $record .= '</td>';
        $record .= '<td width="23.7%" align="left">';
        $record .= $value->varTitle;
        $record .= '</td>';
        $record .= '<td width="23.7%" align="center">';
        $record .=  str_limit(htmlspecialchars_decode($value->txtDescription), 100);
        $record .= '</td>';
        $record .= '<td width="23.7%" align="center">';
        $record .= (!empty($value->dtStartDateTime)?date($dtFormat, strtotime($value->dtStartDateTime)):'-');
        $record .= '</td>';
        $record .= '</tr>';

        return $record;
    }


    /**
     * This method loads testimonial edit view
     * @param      Alias of record
     * @return  View
     * @since   2017-07-21
     * @author  NetQuick
     */

    public function edit($alias = false)
    {
        $imageManager = true;
        if (!is_numeric($alias)) {
            $total = CommonModel::getRecordCount(false,false,false, 'Powerpanel\Testimonial\Models\Testimonial');
            $total = $total + 1;
            $this->breadcrumb['title'] = trans('testimonial::template.testimonialModule.addTestimonial');
            $this->breadcrumb['module'] = trans('testimonial::template.testimonialModule.manageTestimonials');
            $this->breadcrumb['url'] = 'powerpanel/testimonial';
            $this->breadcrumb['inner_title'] = trans('testimonial::template.testimonialModule.addTestimonial');
            $data = [
                'total' => $total,
                'breadcrumb' => $this->breadcrumb,
                'imageManager' => 'imageManager',
            ];
        } else {
            $id = $alias;
            $testimonial = Testimonial::getRecordById($id);
            if (empty($testimonial)) {
                return redirect()->route('powerpanel.testimonial.add');
            }
            $this->breadcrumb['title'] = trans('testimonial::template.testimonialModule.editTestimonial') . ' - ' . $testimonial->varTitle;
            $this->breadcrumb['module'] = trans('testimonial::template.testimonialModule.manageTestimonials');
            $this->breadcrumb['url'] = 'powerpanel/testimonial';
            $this->breadcrumb['inner_title'] = trans('testimonial::template.testimonialModule.editTestimonial') . ' - ' . $testimonial->varTitle;
            $data = [
                'testimonials' => $testimonial,
                'id' => $id,
                'breadcrumb' => $this->breadcrumb,
                'imageManager' => 'imageManager',
            ];
        }
        return view('testimonial::powerpanel.actions', $data);
    }

    /**
     * This method stores testimonial modifications
     * @return  View
     * @since   2017-07-21
     * @author  NetQuick
     */

    public function handlePost(Request $request)
    {
        $postArr = Request::all();

        $rules = array(
            'testimonialby' => 'required|max:160',
            'testimonial' => 'required',
            // 'city' => 'required',
            'chrMenuDisplay' => 'required',
        );
        $messsages = array(
            'testimonialby.required' => 'Testimonial by field is required.',
            // 'city.required' => 'City field is required.',
            'testimonial.required' => 'Testimonial field is required.',
        );
        $validator = Validator::make($postArr, $rules, $messsages);
        if ($validator->passes()) {
            $id = Request::segment(3);
            $actionMessage = trans('testimonial::template.common.oppsSomethingWrong');
            if (is_numeric($id)) { #Edit post Handler=======
            $testimonial = Testimonial::getRecordForLogById($id);
                $updateTestimonialFields = [];
                $updateTestimonialFields['varTitle'] = trim($postArr['testimonialby']);
                $updateTestimonialFields['varCity'] = !empty($postArr['city']) ? trim($postArr['city']) : null;
                $updateTestimonialFields['fkIntImgId'] = !empty($postArr['img_id']) ? $postArr['img_id'] : null;
                $updateTestimonialFields['txtDescription'] = $postArr['testimonial'];
                $updateTestimonialFields['varStarRating'] = isset($postArr['starrating']) && !empty($postArr['starrating']) ? $postArr['starrating'] : null;
                $updateTestimonialFields['dtStartDateTime'] = date('Y-m-d', strtotime(str_replace('/', '-', $postArr['testimonialdate'])));
                $updateTestimonialFields['chrPublish'] = $postArr['chrMenuDisplay'];

                $whereConditions = ['id' => $id];
                $update = CommonModel::updateRecords($whereConditions, $updateTestimonialFields,false, 'Powerpanel\Testimonial\Models\Testimonial');
                if ($update) {
                    if ($id > 0 && !empty($id)) {
                        $logArr = MyLibrary::logData($id);
                        if (Auth::user()->can('log-advanced')) {
                            $newTestimonialObj = Testimonial::getRecordForLogById($id);
                            $oldRec = $this->recordHistory($testimonial);
                            $newRec = $this->recordHistory($newTestimonialObj);
                            $logArr['old_val'] = $oldRec;
                            $logArr['new_val'] = $newRec;
                        }

                        $logArr['varTitle'] = trim($postArr['testimonialby']);
                        Log::recordLog($logArr);
                        if (Auth::user()->can('recent-updates-list')) {
                            if (!isset($newTestimonialObj)) {
                                $newTestimonialObj = Testimonial::getRecordForLogById($id);
                            }
                            $notificationArr = MyLibrary::notificationData($id, $newTestimonialObj);
                            RecentUpdates::setNotification($notificationArr);
                        }
                        self::flushCache();
                        $actionMessage = trans('testimonial::template.testimonialModule.updateMessage');
                    }
                }
            } else { #Add post Handler=======
            $testimonialArr['varTitle'] = trim($postArr['testimonialby']);
            $testimonialArr['varCity'] = !empty($postArr['city']) ? trim($postArr['city']) : null;
                $testimonialArr['fkIntImgId'] = !empty($postArr['img_id']) ? $postArr['img_id'] : null;
                $testimonialArr['txtDescription'] = $postArr['testimonial'];
                $testimonialArr['varStarRating'] = isset($postArr['starrating']) && !empty($postArr['starrating']) ? $postArr['starrating'] : null;
                $testimonialArr['dtStartDateTime'] = date('Y-m-d', strtotime(str_replace('/', '-', $postArr['testimonialdate'])));
                $testimonialArr['chrPublish'] = $postArr['chrMenuDisplay'];
                $testimonialArr['created_at'] = Carbon::now();

                $testimonialID = CommonModel::addRecord($testimonialArr, 'Powerpanel\Testimonial\Models\Testimonial');
                if (!empty($testimonialID)) {
                    $id = $testimonialID;
                    $newTestimonialObj = Testimonial::getRecordForLogById($id);

                    $logArr = MyLibrary::logData($id);
                    $logArr['varTitle'] = $newTestimonialObj->varTitle;
                    Log::recordLog($logArr);
                    if (Auth::user()->can('recent-updates-list')) {
                        $notificationArr = MyLibrary::notificationData($id, $newTestimonialObj);
                        RecentUpdates::setNotification($notificationArr);
                    }
                    self::flushCache();
                    $actionMessage = trans('testimonial::template.testimonialModule.addMessage');
                }

            }
            AddImageModelRel::sync(explode(',', $postArr['img_id']), $id);
            if (!empty($postArr['saveandexit']) && $postArr['saveandexit'] == 'saveandexit') {
                return redirect()->route('powerpanel.testimonial.index')->with('message', $actionMessage);
            } else {
                return redirect()->route('powerpanel.testimonial.edit', $id)->with('message', $actionMessage);
            }

        } else {
            return Redirect::back()->withErrors($validator)->withInput();
        }
    }

    /**
     * This method destroys Testimonial in multiples
     * @return  Testimonial index view
     * @since   2016-10-25
     * @author  NetQuick
     */
    public function DeleteRecord(Request $request)
    {
        $data = Request::all('ids');
        $update = MyLibrary::deleteMultipleRecords($data,false,false, 'Powerpanel\Testimonial\Models\Testimonial');
        self::flushCache();
        echo json_encode($update);
        exit;
    }

    /**
     * This method destroys Testimonial in multiples
     * @return  Testimonial index view
     * @since   2016-10-25
     * @author  NetQuick
     */
    public function publish(Request $request)
    {
        $alias = Request::get('alias');
        $val = Request::get('val');
        $update = MyLibrary::setPublishUnpublish($alias, $val, 'Powerpanel\Testimonial\Models\Testimonial');
        self::flushCache();
        echo json_encode($update);
        exit;
    }

    public function tableData($value)
    {
        $details = '';
        $actions = '';
        $publish_action = '';

         $imgIcon = '';
        if (isset($value->fkIntImgId) && !empty($value->fkIntImgId)) {
            $imageArr = explode(',', $value->fkIntImgId);
            if (count($imageArr) > 1) {
                
                $imgIcon .= '<div class="multi_image_thumb">';
                foreach ($imageArr as $key => $image) {
                    $imgIcon .= '<a href="' . resize_image::resize($image) . '" class="fancybox-thumb" rel="fancybox-thumb-' . $value->id . '" data-rel="fancybox-thumb">';
                    $imgIcon .= '<img height="30" width="30" title="' . preg_replace('/[^A-Za-z0-9\-]/', '-', $value->varTitle) . '" src="' . resize_image::resize($image, 50, 50) . '"/>';
                    $imgIcon .= '</a>';
                }
                $imgIcon .= '</div>';
            } else {
               
                $imgIcon .= '<div class="multi_image_thumb">';
                $imgIcon .= '<a href="' . resize_image::resize($value->fkIntImgId) . '" class="fancybox-buttons"  data-rel="fancybox-buttons">';
                $imgIcon .= '<img height="30" width="30" title="' . preg_replace('/[^A-Za-z0-9\-]/', '-', $value->varTitle) . '" src="' . resize_image::resize($value->fkIntImgId, 50, 50) . '"/>';
                $imgIcon .= '</a>';
                $imgIcon .= '</div>';
            }
        } else {
            $imgIcon .= '<span class="glyphicon glyphicon-minus"></span>';
        }

        if (Auth::user()->can('testimonial-edit')) {
            $actions .= '<a class="without_bg_icon" title="' . trans("testimonial::template.common.edit") . '" href="' . route('powerpanel.testimonial.edit', array('alias' => $value->id)) . '">
					<i class="fa fa-pencil"></i></a>';
        }
        if (Auth::user()->can('testimonial-delete')) {
            $actions .= '&nbsp;<a class="without_bg_icon delete" title="' . trans("testimonial::template.common.delete") . '" data-controller="testimonial" data-alias = "' . $value->id . '"><i class="fa fa-times"></i></a>';
        }

        if (Auth::user()->can('testimonial-publish')) {
            if ($value->chrPublish == 'Y') {
                $publish_action .= '<input data-off-text="No" data-on-text="Yes" class="make-switch publish" class="make-switch publish" data-off-color="info" data-on-color="primary" type="checkbox" data-controller="powerpanel/testimonial" title="' . trans("testimonial::template.common.publishedRecord") . '" data-value="Unpublish" data-alias="' . $value->id . '">';
            } else {
                $publish_action .= '<input checked="" data-off-text="No" data-on-text="Yes" class="make-switch publish" class="make-switch publish" data-off-color="info" data-on-color="primary" type="checkbox" data-controller="powerpanel/testimonial" title="' . trans("testimonial::template.common.unpublishedRecord") . '" data-value="Publish" data-alias="' . $value->id . '">';
            }
        }

        if (Auth::user()->can('testimonial-edit')) {
            $title = '<div class="pages_title_div_row"><div class="quick_edit"><a class="" title="' . trans("testimonial::template.common.edit") . '" href="' . route('powerpanel.testimonial.edit', array('alias' => $value->id)) . '">' . $value->varTitle . '</a></div></div>';
        } else {
            $title = $value->varTitle;
        }
        $dateTimeTestimonial = '';
        if(!empty($value->dtStartDateTime)){
          $dateTimeTestimonial = date(Config::get('Constant.DEFAULT_DATE_FORMAT'), strtotime($value->dtStartDateTime));    
        }else{
          $dateTimeTestimonial = '-';   
        }        

        $rate = '';
        $i = 1;
        if(isset($value->varStarRating) && !empty($value->varStarRating)){
            $rate .= '<div class="wrapper-star">';
            while($i <= 5){
                $check = $i <= $value->varStarRating ? 'fa fa-star' : 'fa fa-star-o';
                $rate .= '<i class="'.$check.'" aria-hidden="true"></i>&nbsp;';
                $i++;
            }
            $rate .= '</div>'; 
        }else {
            $rate .= 'N/A'; 
        }

        $records = array(
            '<input type="checkbox" name="delete" class="chkDelete" value="' . $value->id . '">',
            $title,
            '<div class="pro-act-btn">
					<a href="javascript:void(0)" class="without_bg_icon highslide-active-anchor" onclick="return hs.htmlExpand(this,{width:300,headingText:\'' . trans("testimonial::template.common.testimonial") . '\',wrapperClassName:\'titlebar\',showCredits:false});"><span aria-hidden="true" class="fa fa-file-text-o"></span></a>
					<div class="highslide-maincontent">' . htmlspecialchars_decode($value->txtDescription) . '</div>
					</div>',
            // $imgIcon,
            $rate,
            $dateTimeTestimonial,
            $publish_action,
            $actions,
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
    public function recordHistory($data = false)
    {

        $returnHtml = '';
        $returnHtml .= '<table class="new_table_desing table table-striped table-bordered table-hover">
				<thead>
					<tr>
						<th>' . trans("testimonial::template.testimonialModule.testimonialBy") . '</th>
						<th>' . trans("testimonial::template.testimonialModule.testimonialDate") . '</th>
						<th>' . trans("testimonial::template.common.description") . '</th>
						<th>' . trans("testimonial::template.common.publish") . '</th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td>' . $data->varTitle . '</td>
						<td>' . date(Config::get('Constant.DEFAULT_DATE_FORMAT'), strtotime($data->dtStartDateTime)) . '</td>
						<td>' . $data->txtDescription . '</td>
						<td>' . $data->chrPublish . '</td>
					</tr>
				</tbody>
			</table>';

        return $returnHtml;
    }
    public static function flushCache()
    {
        Cache::tags('Testimonial')->flush();
    }
}
