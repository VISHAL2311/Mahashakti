<?php

namespace Powerpanel\Boat\Controllers\Powerpanel;

use App\Alias;
use App\CommonModel;
use App\Helpers\AddImageModelRel;
use App\Helpers\AddVideoModelRel;
use Powerpanel\Boat\Models\Categories;
use App\Helpers\CategoryArrayBuilder;
use App\Helpers\Category_builder;
use App\Helpers\MyLibrary;
use App\Helpers\resize_image;
use App\Http\Controllers\PowerpanelController;
use App\Log;
use App\Pagehit;
use App\Modules;
use App\RecentUpdates;
use Powerpanel\Boat\Models\Boat;
use Powerpanel\Brand\Models\Brand;
use Powerpanel\BoatCategory\Models\BoatCategory;
use App\video;
use Auth;
use Cache;
use Carbon\Carbon;
use Config;
use Illuminate\Support\Facades\Redirect;
use Request;
use Validator;
use DB;

class BoatController extends PowerpanelController
{

    public $catModule;
    public function __construct()
    {
        parent::__construct();
        if (isset($_COOKIE['locale'])) {
            app()->setLocale($_COOKIE['locale']);
        }
        $this->MyLibrary = new MyLibrary();
    }
    /**
     * This method handels load process of boat
     * @return  View
     * @since   2017-11-10
     * @author  NetQuick
     */
    public function index()
    {
        $iTotalRecords = CommonModel::getRecordCount(false, false, false, 'Powerpanel\Boat\Models\Boat');
        $this->breadcrumb['title'] = trans('boat::template.boatModule.manageBoat');
        $breadcrumb = $this->breadcrumb;
        return view('boat::powerpanel.index', compact('iTotalRecords',  'breadcrumb'));
    }
    /**
     * This method loads boat edit view
     * @param   Alias of record
     * @return  View
     * @since   2017-11-10
     * @author  NetQuick
     */
    public function edit($id = false)
    {
        $imageManager = true;
        $videoManager = true;



        #icon code======================================

        if (!is_numeric($id)) {
            $boatcategory = BoatCategory::getCatWithParent($id);
            $brandcategory = Brand::getCatWithParent($id);
            $stock = DB::table('stock')->where('chrPublish', 'Y')->where('chrDelete', 'N')->get();
            $boatcondition = DB::table('boat_condition')->where('chrPublish', 'Y')->where('chrDelete', 'N')->get();
            $boatfueltype = DB::table('boat_fuel_type')->where('chrPublish', 'Y')->where('chrDelete', 'N')->get();

            $total = CommonModel::getRecordCount(false, false, false, 'Powerpanel\Boat\Models\Boat');
            $total = $total + 1;
            $this->breadcrumb['title'] = trans('boat::template.boatModule.addBoat');
            $this->breadcrumb['module'] = trans('boat::template.boatModule.manageBoat');
            $this->breadcrumb['url'] = 'powerpanel/boat';
            $this->breadcrumb['inner_title'] = trans('boat::template.boatModule.addBoat');
            $breadcrumb = $this->breadcrumb;
            $data = compact('total', 'breadcrumb', 'stock', 'boatfueltype', 'boatcondition', 'brandcategory', 'boatcategory', 'imageManager', 'imageManager', 'videoManager');
        } else {

            //Edit Record
            $boat = Boat::getRecordById($id);
            $boatcategory = BoatCategory::getCatWithParent($id);
            $brandcategory = Brand::getCatWithParent($id);
            $stock = DB::table('stock')->where('chrPublish', 'Y')->where('chrDelete', 'N')->get();
            $boatcondition = DB::table('boat_condition')->where('chrPublish', 'Y')->where('chrDelete', 'N')->get();
            $boatfueltype = DB::table('boat_fuel_type')->where('chrPublish', 'Y')->where('chrDelete', 'N')->get();

            $videoIDAray = explode(',', $boat->fkIntVideoId);
            $videoData = video::getVideoData($videoIDAray);

            if (empty($boat)) {
                return redirect()->route('powerpanel.boat.add');
            }

            $metaInfo = array('varMetaTitle' => $boat->varMetaTitle, 'varMetaKeyword' => $boat->varMetaKeyword, 'varMetaDescription' => $boat->varMetaDescription);
            $this->breadcrumb['title'] = trans('boat::template.boatModule.editBoat') . ' - ' . $boat->varTitle;
            $this->breadcrumb['module'] = trans('boat::template.boatModule.manageBoat');
            $this->breadcrumb['url'] = 'powerpanel/boat';
            $this->breadcrumb['inner_title'] = trans('boat::template.boatModule.editBoat') . ' - ' . $boat->varTitle;
            $breadcrumb = $this->breadcrumb;
            $data = compact('boat', 'metaInfo', 'brandcategory', 'stock', 'boatcategory', 'boatcondition', 'boatfueltype', 'breadcrumb', 'imageManager', 'videoManager',  'videoData');
        }
        $data['MyLibrary'] = $this->MyLibrary;
        return view('boat::powerpanel.actions', $data);
    }

    /**
     * This method stores boat modifications
     * @return  View
     * @since   2017-11-10
     * @author  NetQuick
     */
    public function handlePost(Request $request)
    {

        $data = Request::all();


        $actionMessage = trans('boat::template.common.oppsSomethingWrong');
        $settings = json_decode(Config::get("Constant.MODULE.SETTINGS"));
        $rules = array(
            'title' => 'required|max:160',
            'display_order' => 'required|greater_than_zero',
            'chrMenuDisplay' => 'required',
            // 'short_description' => 'required|max:400',
            'alias' => 'required',
        );
        //        $rules['varMetaTitle'] = 'required|max:' . $this->metaLength;
        //        $rules['varMetaKeyword'] = 'required|max:' . $this->metaLength;
        //        $rules['varMetaDescription'] = 'required|max:' . $this->metaDescriptionLength;

        $messsages = array(
            'display_order.greater_than_zero' => trans('boat::template.boatModule.displayGreaterThan'),
            // 'short_description.required' => trans('boat::template.boatModule.shortDescription'),
            'varMetaTitle.required' => trans('boat::template.boatModule.metaTitle'),
            //            'varMetaKeyword.required' => trans('boat::template.boatModule.metaKeyword'),
            'varMetaDescription.required' => trans('boat::template.boatModule.metaDescription'),
        );

        $validator = Validator::make($data, $rules, $messsages);
        if ($validator->passes()) {
            $boatArr = [];
            $boatArr['varTitle'] = trim($data['title']);
            $boatArr['fkIntImgId'] = !empty($data['img_id']) ? $data['img_id'] : null;
            $boatArr['fkIntVideoId'] = !empty($data['video_id']) ? $data['video_id'] : null;
            $boatArr['intBoatCategoryId'] = !empty($data['boat_category_id']) ? $data['boat_category_id'] : null;
            $boatArr['intBoatBrandId'] = !empty($data['boat_brand_id']) ? $data['boat_brand_id'] : null;
            $boatArr['intBoatStockId'] = !empty($data['boat_stock_id']) ? $data['boat_stock_id'] : null;
            $boatArr['yearYear'] = !empty($data['year']) ? $data['year'] : null;
            $boatArr['intPrice'] = !empty($data['price']) ? $data['price'] : null;
            $boatArr['varModel'] = !empty($data['model']) ? $data['model'] : null;
            $boatArr['varLength'] = !empty($data['length']) ? $data['length'] : null;
            $boatArr['txtOtherdetails'] = !empty($data['other_details']) ? $data['other_details'] : null;
            $boatArr['intBoatFuelId'] = !empty($data['boat_fuel_type_id']) ? $data['boat_fuel_type_id'] : null;
            $boatArr['varHullMaterial'] = !empty($data['hull_material']) ? $data['hull_material'] : null;
            $boatArr['varBoatLocation'] = !empty($data['boat_location']) ? $data['boat_location'] : null;
            $boatArr['varHullShape'] = !empty($data['hull_shape']) ? $data['hull_shape'] : null;
            $boatArr['varHullWarranty'] = !empty($data['hull_warranty']) ? $data['hull_warranty'] : null;
            $boatArr['txtOtherdetail'] = !empty($data['other_details']) ? $data['other_details'] : null;
            $boatArr['txtDescriptionnew'] = !empty($data['description']) ? $data['description'] : null;
            $boatArr['varCruisingSpeed'] = !empty($data['cruising_speed']) ? $data['cruising_speed'] : null;
            $boatArr['varMaxSpeed'] = !empty($data['max_speed']) ? $data['max_speed'] : null;
            $boatArr['varLengthOverall'] = !empty($data['length_overall']) ? $data['length_overall'] : null;
            $boatArr['varBridgeclearance'] = !empty($data['max_bridge_clearance']) ? $data['max_bridge_clearance'] : null;
            $boatArr['varMaxDraft'] = !empty($data['max_draft']) ? $data['max_draft'] : null;
            $boatArr['varBeam'] = !empty($data['beam']) ? $data['beam'] : null;
            $boatArr['varCabinHeadroom'] = !empty($data['cabin_headroom']) ? $data['cabin_headroom'] : null;
            $boatArr['varLengthAtWaterline'] = !empty($data['length_at_waterline']) ? $data['length_at_waterline'] : null;
            $boatArr['varDryWeight'] = !empty($data['dry_weight']) ? $data['dry_weight'] : null;
            $boatArr['varWindlass'] = !empty($data['windlass']) ? $data['windlass'] : null;
            $boatArr['varDeadriseAtTransom'] = !empty($data['deadrise_at_transom']) ? $data['deadrise_at_transom'] : null;
            $boatArr['varElectricalCircuit'] = !empty($data['electrical_circuit']) ? $data['electrical_circuit'] : null;
            $boatArr['varSeatingCapacity'] = !empty($data['seating_capacity']) ? $data['seating_capacity'] : null;
            $boatArr['varFreshWaterTank'] = !empty($data['fresh_water_tank']) ? $data['fresh_water_tank'] : null;
            $boatArr['varFuelTank'] = !empty($data['fuel_tank']) ? $data['fuel_tank'] : null;
            $boatArr['varHoldingTank'] = !empty($data['holding_tank']) ? $data['holding_tank'] : null;
            $boatArr['varSingleBerths'] = !empty($data['single_berths']) ? $data['single_berths'] : null;
            $boatArr['varHeads'] = !empty($data['heads']) ? $data['heads'] : null;
            $boatArr['intBoatconditionId'] = !empty($data['boat_condition_id']) ? $data['boat_condition_id'] : null;
            $boatArr['varExternalLink'] = '';
            $boatArr['varFontAwesomeIcon'] = $data['font_awesome_icon'];
            $boatArr['txtDescription'] = $data['section'];
            $boatArr['txtShortDescription'] = trim($data['short_description']);
            $boatArr['varPreferences'] = '';
            $boatArr['chrFeaturedBoat'] = $data['featuredBoat'];
            $boatArr['chrPublish'] = $data['chrMenuDisplay'];
            $boatArr['created_at'] = Carbon::now();

            $boatArr['varMetaTitle'] = trim($data['varMetaTitle']);
            //            $boatArr['varMetaKeyword'] = trim($data['varMetaKeyword']);
            $boatArr['varMetaDescription'] = trim($data['varMetaDescription']);

            $id = Request::segment(3);
            if (is_numeric($id)) {
                #Edit post Handler=======
                if ($data['oldAlias'] != $data['alias']) {
                    Alias::updateAlias($data['oldAlias'], $data['alias']);
                }
                $boat = Boat::getRecordForLogById($id);
                $whereConditions = ['id' => $boat->id];
                $update = CommonModel::updateRecords($whereConditions, $boatArr, false, 'Powerpanel\Boat\Models\Boat');
                if ($update) {
                    if (!empty($id)) {
                        self::swap_order_edit($data['display_order'], $boat->id);

                        $logArr = MyLibrary::logData($boat->id);
                        if (Auth::user()->can('log-advanced')) {
                            $newBoatObj = Boat::getRecordForLogById($boat->id);
                            $oldRec = $this->recordHistory($boat);
                            $newRec = $this->recordHistory($newBoatObj);
                            $logArr['old_val'] = $oldRec;
                            $logArr['new_val'] = $newRec;
                        }
                        $logArr['varTitle'] = trim($data['title']);
                        Log::recordLog($logArr);
                        if (Auth::user()->can('recent-updates-list')) {
                            if (!isset($newBoatObj)) {
                                $newBoatObj = Boat::getRecordForLogById($boat->id);
                            }
                            $notificationArr = MyLibrary::notificationData($boat->id, $newBoatObj);
                            RecentUpdates::setNotification($notificationArr);
                        }
                    }
                    self::flushCache();
                    $actionMessage = trans('boat::template.boatModule.updateMessage');
                }
            } else {
                #Add post Handler=======
                $boatArr['intAliasId'] = MyLibrary::insertAlias($data['alias']);
                $boatArr['intDisplayOrder'] = self::swap_order_add($data['display_order']);
                $boatID = CommonModel::addRecord($boatArr, 'Powerpanel\Boat\Models\Boat');
                if (!empty($boatID)) {
                    $id = $boatID;
                    $newBoatObj = Boat::getRecordForLogById($id);

                    $logArr = MyLibrary::logData($id);
                    $logArr['varTitle'] = $newBoatObj->varTitle;
                    Log::recordLog($logArr);
                    if (Auth::user()->can('recent-updates-list')) {
                        $notificationArr = MyLibrary::notificationData($id, $newBoatObj);
                        RecentUpdates::setNotification($notificationArr);
                    }
                    self::flushCache();
                    $actionMessage = trans('boat::template.boatModule.addedMessage');
                }
            }
            AddImageModelRel::sync(explode(',', $data['img_id']), $id);
            AddVideoModelRel::sync(explode(',', $data['video_id']), $id);
            if (!empty($data['saveandexit']) && $data['saveandexit'] == 'saveandexit') {
                return redirect()->route('powerpanel.boat.index')->with('message', $actionMessage);
            } else {
                return redirect()->route('powerpanel.boat.edit', $id)->with('message', $actionMessage);
            }
        } else {
            return Redirect::back()->withErrors($validator)->withInput();
        }
    }
    /**
     * This method loads boat table data on view
     * @return  View
     * @since   2017-11-10
     * @author  NetQuick
     */
    public function get_list()
    {
        $filterArr = [];
        $records = [];
        $records["data"] = [];
        $filterArr['orderColumnNo'] = (!empty(Request::get('order')[0]['column']) ? Request::get('order')[0]['column'] : '');
        $filterArr['orderByFieldName'] = (!empty(Request::get('columns')[$filterArr['orderColumnNo']]['name']) ? Request::get('columns')[$filterArr['orderColumnNo']]['name'] : '');
        $filterArr['orderTypeAscOrDesc'] = (!empty(Request::get('order')[0]['dir']) ? Request::get('order')[0]['dir'] : '');
        $filterArr['statusFilter'] = !empty(Request::get('statusValue')) ? Request::get('statusValue') : '';
        $filterArr['catFilter'] = !empty(Request::get('catValue')) ? Request::get('catValue') : '';
        $filterArr['searchFilter'] = !empty(Request::get('searchValue')) ? Request::get('searchValue') : '';
        $filterArr['iDisplayLength'] = intval(Request::get('length'));
        $filterArr['iDisplayStart'] = intval(Request::get('start'));
        /**** Delete record then redirect to approriate pagination **/
        $currentrecordcountstart = intval(Request::get('start'));
        $currentpaging = intval(Request::get('length'));

        $totalRecords_old = CommonModel::getTotalRecordCount('Powerpanel\Boat\Models\Boat');
        if ($totalRecords_old > $currentrecordcountstart) {
            $filterArr['iDisplayStart'] = intval(Request::get('start'));
        } else {
            $filterArr['iDisplayStart'] = intval(0);
        }
        /**** Delete record then redirect to approriate pagination **/
        $sEcho = intval(Request::get('draw'));
        $arrResults = Boat::getRecordList($filterArr);
        $iTotalRecords = CommonModel::getRecordCount($filterArr, true, false, 'Powerpanel\Boat\Models\Boat');
        $totalRecords = CommonModel::getTotalRecordCount('Powerpanel\Boat\Models\Boat');
        $end = $filterArr['iDisplayStart'] + $filterArr['iDisplayLength'];
        $end = $end > $iTotalRecords ? $iTotalRecords : $end;

        if (!empty($arrResults)) {
            foreach ($arrResults as $key => $value) {
                $records["data"][] = $this->tableData($value, $this->catModule, $totalRecords);
            }
        }

        $records["customActionStatus"] = "OK";
        $records["draw"] = $sEcho;
        $records["recordsTotal"] = $iTotalRecords;
        $records["recordsFiltered"] = $iTotalRecords;
        return json_encode($records);
    }

    /**
     * This method loads boat builder data on view
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
        $filterArr['catFilter'] = isset($filter['catValue']) ? $filter['catValue'] : '';
        $filterArr['critaria'] = isset($filter['critaria']) ? $filter['critaria'] : '';
        $filterArr['searchFilter'] = isset($filter['searchValue']) ? trim($filter['searchValue']) : '';
        $filterArr['iDisplayStart'] = isset($filter['start']) ? intval($filter['start']) : 1;
        $filterArr['iDisplayLength'] = isset($filter['length']) ? intval($filter['length']) : 5;
        $filterArr['ignore'] = !empty($filter['ignore']) ? $filter['ignore'] : [];
        $filterArr['selected'] = isset($filter['selected']) && !empty($filter['selected']) ? $filter['selected'] : [];
        $arrResults = Boat::getBuilderRecordList($filterArr);
        $found = $arrResults->toArray();

        if (!empty($found)) {
            foreach ($arrResults as $key => $value) {
                $rows .= $this->tableDataBuilder($value, false, $filterArr['selected']);
            }
        } else {
            $rows .= '<tr id="not-found"><td colspan="4" align="center">No records found.</td></tr>';
        }
        $iTotalRecords = CommonModel::getTotalRecordCount('Powerpanel\Boat\Models\Boat', false, true);
        $records["data"] = $rows;
        $records["found"] = count($found);
        $records["recordsTotal"] = $iTotalRecords;
        return json_encode($records);
    }

    public function tableDataBuilder($value = false, $fcnt = false, $selected = [])
    {


        if (isset($value->fkIntImgId) && $value->fkIntImgId != '') {
            $image = '<img src="' . resize_image::resize($value->fkIntImgId, 50, 50) . '">';
        } else {
            $image = '---';
        }

        $dtFormat = Config::get('Constant.DEFAULT_DATE_FORMAT');
        $record = '<tr ' . (in_array($value->id, $selected) ? 'class="selected-record"' : '') . '>';
        $record .= '<td width="1%" align="center">';
        $record .= '<label class="mt-checkbox mt-checkbox-outline">';
        $record .= '<input type="checkbox" data-title="' . $value->varTitle . '" name="delete[]" class="chkChoose" ' . (in_array($value->id, $selected) ? 'checked' : '') . ' value="' . $value->id . '">';
        $record .= '<span></span>';
        $record .= '</label>';
        $record .= '</td>';
        $record .= '<td width="40%" align="left">';
        $record .= $value->varTitle;
        $record .= '</td>';
        $record .= '<td width="40%" align="center">';
        $record .= $image;
        $record .= '</td>';
        $record .= '</tr>';

        return $record;
    }

    /**
     * This method delete multiples boat
     * @return  true/false
     * @since   2017-07-15
     * @author  NetQuick
     */
    public function DeleteRecord(Request $request)
    {
        $data = Request::all('ids');
        $update = MyLibrary::deleteMultipleRecords($data, false, false, 'Powerpanel\Boat\Models\Boat');
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
    public function reorder()
    {
        $order = Request::get('order');
        $exOrder = Request::get('exOrder');

        MyLibrary::swapOrder($order, $exOrder, 'Powerpanel\Boat\Models\Boat');
        self::flushCache();
    }
    /**
     * This method handels swapping of available order record while adding
     * @param   order
     * @return  order
     * @since   2016-10-21
     * @author  NetQuick
     */
    public static function swap_order_add($order = null)
    {
        $response = false;
        if ($order != null) {
            $response = MyLibrary::swapOrderAdd($order, false, false, 'Powerpanel\Boat\Models\Boat');
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
    public static function swap_order_edit($order = null, $id = null)
    {
        MyLibrary::swapOrderEdit($order, $id, false, false, 'Powerpanel\Boat\Models\Boat');
        self::flushCache();
    }

    public function makeFeatured()
    {
        $id = Request::get('id');
        $featured = Request::get('featured');
        $whereConditions = ['id' => $id];
        $update = CommonModel::updateRecords($whereConditions, ['chrFeaturedBoat' => $featured], false, 'Powerpanel\Boat\Models\Boat');
        self::flushCache();
        echo json_encode($update);
    }

    /**
     * This method destroys Boat in multiples
     * @return  Boat index view
     * @since   2016-10-25
     * @author  NetQuick
     */
    public function publish(Request $request)
    {
        $alias = Request::get('alias');
        $val = Request::get('val');
        $update = MyLibrary::setPublishUnpublish($alias, $val, 'Powerpanel\Boat\Models\Boat');
        self::flushCache();
        echo json_encode($update);
        exit;
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
														<th>' . trans("template.common.title") . '</th>
														<th>' . trans("template.common.image") . '</th>
														<th>' . trans("template.common.displayorder") . '</th>
														<th>' . trans("template.common.boatIcon") . '</th>
														<th>' . trans("template.common.shortDescription") . '</th>
														<th>' . trans("template.common.description") . '</th>
														<th>' . trans("template.boatModule.featuredBoat") . '</th>
														<th>' . trans("template.common.metatitle") . '</th>
														<th>' . trans("template.common.metakeyword") . '</th>
														<th>' . trans("template.common.metadescription") . '</th>
														<th>' . trans("template.common.publish") . '</th>
													</tr>
												</thead>
												<tbody>
														<tr>
																<td>' . $data->varTitle . '</td>';

        if ($data->fkIntImgId > 0) {
            $returnHtml .= '<td>' . '<img height="50" width="50" src="' . resize_image::resize($data->fkIntImgId) . '" />' . '</td>';
        } else {
            $returnHtml .= '<td>-</td>';
        }
        $returnHtml .= '<td>' . ($data->intDisplayOrder) . '</td>
																<td>' . $data->varFontAwesomeIcon . '</td>
																<td>' . $data->txtShortDescription . '</td>
																<td>' . $data->txtDescription . '</td>
																<td>' . $data->chrFeaturedBoat . '</td>
																<td>' . $data->varMetaTitle . '</td>
																<td>' . $data->varMetaKeyword . '</td>
																<td>' . $data->varMetaDescription . '</td>
																<td>' . $data->chrPublish . '</td>
														</tr>
												</tbody>
										</table>';
        return $returnHtml;
    }

    public function tableData($value = false, $catModuleID = false, $totalRecord = false)
    {
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
        if (Auth::user()->can('boat-edit')) {
            $details = '<a class="without_bg_icon" title="' . trans("template.common.edit") . '" href="' . route('powerpanel.boat.edit', array('alias' => $value->id)) . '"><i class="fa fa-pencil"></i></a>';
        }
        if (Auth::user()->can('boat-delete')) {
            $details .= '&nbsp;<a class="without_bg_icon delete" title="' . trans("template.common.delete") . '" data-controller="boat" data-alias = "' . $value->id . '"><i class="fa fa-times"></i></a>';
        }

        if (Auth::user()->can('boat-publish')) {
            if (!empty($value->chrPublish) && ($value->chrPublish == 'Y')) {
                $publish_action .= '<input data-off-text="No" data-on-text="Yes" class="make-switch publish" class="make-switch publish" data-off-color="info" data-on-color="primary" type="checkbox" data-controller="powerpanel/boat" title="' . trans("template.common.publishedRecord") . '" data-value="Unpublish" data-alias="' . $value->id . '">';
            } else {
                $publish_action .= '<input checked="" data-off-text="No" data-on-text="Yes" class="make-switch publish" class="make-switch publish" data-off-color="info" data-on-color="primary" type="checkbox" data-controller="powerpanel/boat" title="' . trans("template.common.unpublishedRecord") . '" data-value="Publish" data-alias="' . $value->id . '">';
            }
        }

        /* $details .='<a class="without_bg_icon share" title="Share" data-modal="Boat" data-alias="'.$value->id.'"  data-images="'.$value->fkIntImgId.'" data-link = "'.url('/boat/'.$value->alias['varAlias']).'" data-toggle="modal" data-target="#confirm_share">
        <i class="fa fa-share-alt"></i></a>';*/

        if (Auth::user()->can('boat-edit')) {
            $title = '<a class="" title="Edit" href="' . route('powerpanel.boat.edit', array('alias' => $value->id)) . '">' . $value->varTitle . '</a>';
        } else {
            $title = $value->varTitle;
        }
        $imgIcon = '';
        if (isset($value->fkIntImgId) && !empty($value->fkIntImgId)) {
            $imageArr = explode(',', $value->fkIntImgId);
            if (count($imageArr) > 1) {
                $imgIcon .= '<div class="multi_image_thumb">';
                foreach ($imageArr as $key => $image) {
                    $imgIcon .= '<a href="' . resize_image::resize($image) . '" class="fancybox-thumb" rel="fancybox-thumb-' . $value->id . '" data-fancybox="fancybox-thumb">';
                    $imgIcon .= '<img height="30" width="30" title="' . preg_replace('/[^A-Za-z0-9\-]/', '-', $value->varTitle) . '" src="' . resize_image::resize($image, 50, 50) . '"/>';
                    $imgIcon .= '</a>';
                }
                $imgIcon .= '</div>';
            } else {
                $imgIcon .= '<div class="multi_image_thumb">';
                $imgIcon .= '<a href="' . resize_image::resize($value->fkIntImgId) . '" class="fancybox-buttons"  data-fancybox="fancybox-buttons">';
                $imgIcon .= '<img height="30" width="30" title="' . preg_replace('/[^A-Za-z0-9\-]/', '-', $value->varTitle) . '" src="' . resize_image::resize($value->fkIntImgId, 50, 50) . '"/>';
                $imgIcon .= '</a>';
                $imgIcon .= '</div>';
            }
        } else {
            $imgIcon .= '<span class="glyphicon glyphicon-minus"></span>';
        }


        $fontAwesomeIcon = '';
        if (!empty($value->varFontAwesomeIcon)) {
            //$fontAwesomeIcon .= ucfirst($value->varFontAwesomeIcon);
            $fontAwesomeIcon .= '<i class="fa ' . strtolower($value->varFontAwesomeIcon) . '"></i>';
        } else {
            $fontAwesomeIcon .= '<span class="glyphicon glyphicon-minus"></span>';
        }

        $featuredBoat = '';
        if (!empty($value->chrFeaturedBoat)) {
            if ($value->chrFeaturedBoat == 'Y') {
                $featuredBoat .= '<a href="javascript:makeFeatured(' . $value->id . ',\'N\');"><i class="fa fa-star" aria-hidden="true"></i></a>';
            } else {
                $featuredBoat .= '<a href="javascript:makeFeatured(' . $value->id . ',\'Y\');"><i class="fa fa-star-o" aria-hidden="true"></i></a>';
            }
        } else {
            $featuredBoat .= '<a href="javascript:makeFeatured(' . $value->id . ',\'Y\');"><i class="fa fa-star-o" aria-hidden="true"></i></a>';
        }

        if (Auth::user()->can('boat-edit')) {
            $title = '<div class="pages_title_div_row"><div class="quick_edit"><a class="" title="' . trans("template.common.edit") . '" href="' . route('powerpanel.boat.edit', array('alias' => $value->id)) . '">' . $value->varTitle . '</a></div></div>';
        } else {
            $title = $value->varTitle;
        }

        // $orderArrow = '';
        // $orderArrow .= '<span class="pageorderlink">';
        // if ($totalRecord != $value->intDisplayOrder) {
        //     $orderArrow .= '<a href="javascript:;" data-order="' . $value->intDisplayOrder . '" class="moveUp"> <i class="fa fa-plus " aria-hidden="true"></i></a>';
        // }
        // $orderArrow .= $value->intDisplayOrder . ' ';
        // if ($value->intDisplayOrder != 1) {
        //     $orderArrow .= '<a href="javascript:;"  data-order="' . $value->intDisplayOrder . '" class="moveDwn"><i class="fa fa-minus" aria-hidden="true"></i></a>';
        // }
        // $orderArrow .= '</span>';

        $orderArrow = '';
        $dispOrder = $value->intDisplayOrder;
       
    
        if ($value->intDisplayOrder != 1) {
            $orderArrow .= ' <a href="javascript:;" data-order="' . $value->intDisplayOrder . '" class="moveDwn"><i class="fa fa-arrow-up" aria-hidden="true"></i></a>';
        }
        $orderArrow .= " ".$dispOrder." ";
        if ($totalRecord != $value->intDisplayOrder) {
            $orderArrow .= '<a href="javascript:;" data-order="' . $value->intDisplayOrder . '" class="moveUp"><i class="fa fa-arrow-down" aria-hidden="true"></i></a> ';
        }

        $records = array(
            '<input type="checkbox" name="delete" class="chkDelete" value="' . $value->id . '">',
            $title,
            '<div class="pro-act-btn">
					<a href="javascript:void(0)" class="without_bg_icon" onclick="return hs.htmlExpand(this,{width:300,headingText:\'' . trans("template.common.shortdescription") . '\',wrapperClassName:\'titlebar\',showCredits:false});"><span aria-hidden="true" class="fa fa-file-text-o"></span></a>
						<div class="highslide-maincontent">' . htmlspecialchars_decode($value->txtShortDescription) . '</div>
					</div>',
            $imgIcon,
             $webHits,
            $orderArrow,
           
            // $featuredBoat,
            $publish_action,
            $details,
            $value->intDisplayOrder,
        );
        return $records;
    }

    public static function flushCache()
    {
        Cache::tags('Boat')->flush();
    }
}
