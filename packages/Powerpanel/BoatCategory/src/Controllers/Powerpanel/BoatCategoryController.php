<?php
namespace Powerpanel\BoatCategory\Controllers\Powerpanel;

use App\Alias;
use App\CommonModel;
use App\Helpers\AddCategoryAjax;
use App\Helpers\Category_builder;
use App\Helpers\MyLibrary;
use App\Http\Controllers\PowerpanelController;
use App\Log;
use App\RecentUpdates;
use Powerpanel\BoatCategory\Models\BoatCategory;

use Powerpanel\Boat\Models\Boat;
use Auth;
use Cache;
use Carbon\Carbon;
use Config;
use Illuminate\Support\Facades\Redirect;
use Request;
use Validator;

class BoatCategoryController extends PowerpanelController
{
    public function __construct()
    {
        parent::__construct();
        if (isset($_COOKIE['locale'])) {
            app()->setLocale($_COOKIE['locale']);
        }
    }
/**
 * This method handels load process of boatCategory
 * @return  View
 * @since   2017-11-10
 * @author  NetQuick
 */
    public function index()
    {
        $iTotalRecords = CommonModel::getRecordCount(false,false,false, 'Powerpanel\BoatCategory\Models\BoatCategory');
        $this->breadcrumb['title'] = trans('boatcategory::template.boatCategoryModule.manageBoatCategory');
        $breadcrumb = $this->breadcrumb;
        return view('boatcategory::powerpanel.index', compact('iTotalRecords', 'breadcrumb'));
    }
/**
 * This method stores boatCategory modifications
 * @return  View
 * @since   2017-11-10
 * @author  NetQuick
 */
    public function handlePost(Request $request)
    {
        $data = Request::all();
        $settings = json_decode(Config::get("Constant.MODULE.SETTINGS"));
        $rules = array(
            'title' => 'required|max:160',
            'display_order' => 'required|greater_than_zero',
        
          
        );
        $messsages = array(
            'display_order.required' => trans('boatcategory::template.boatCategoryModule.displayOrder'),
            'display_order.greater_than_zero' => trans('boatcategory::template.boatCategoryModule.displayGreaterThan'),
      
        );
        
        $validator = Validator::make($data, $rules, $messsages);
        if ($validator->passes()) {
            $id = Request::segment(3);
            $actionMessage = trans('boatcategory::template.common.oppsSomethingWrong');
            if (is_numeric($id)) {
                #Edit post Handler=======
              
                $boatCategory = BoatCategory::getRecordForLogById($id);
                $updateBoatCategoryFields = [
                    'varTitle' => trim($data['title']),
                 
                    'chrPublish' => isset($data['chrMenuDisplay']) ? $data['chrMenuDisplay'] : 'Y',
           
                ];
                $whereConditions = ['id' => $boatCategory->id];
                $update = CommonModel::updateRecords($whereConditions, $updateBoatCategoryFields,false, 'Powerpanel\BoatCategory\Models\BoatCategory');
                if ($update) {
                    if (!empty($id)) {
                        MyLibrary::swapOrderEdit($data['display_order'], $boatCategory->id,false,false, 'Powerpanel\BoatCategory\Models\BoatCategory');
                        $logArr = MyLibrary::logData($boatCategory->id);
                        if (Auth::user()->can('log-advanced')) {
                            $newBoatObj = BoatCategory::getRecordForLogById($boatCategory->id);
                            $oldRec = $this->recordHistory($boatCategory);
                            $newRec = $this->recordHistory($newBoatObj);
                            $logArr['old_val'] = $oldRec;
                            $logArr['new_val'] = $newRec;
                        }
                        $logArr['varTitle'] = trim($data['title']);
                        Log::recordLog($logArr);
                        if (Auth::user()->can('recent-updates-list')) {
                            if (!isset($newBoatObj)) {
                                $newBoatObj = BoatCategory::getRecordForLogById($boatCategory->id);
                            }
                            $notificationArr = MyLibrary::notificationData($boatCategory->id, $newBoatObj);
                            RecentUpdates::setNotification($notificationArr);
                        }
                        $actionMessage = trans('boatcategory::template.boatCategoryModule.successMessage');
                    }
                }
            } else {
                #Add post Handler=======
                $boatCategoryArr = [];
        
                $boatCategoryArr['varTitle'] = trim($data['title']);
                $boatCategoryArr['intDisplayOrder'] = MyLibrary::swapOrderAdd($data['display_order'],false,false, 'Powerpanel\BoatCategory\Models\BoatCategory');
             
                $boatCategoryArr['chrPublish'] = $data['chrMenuDisplay'];
                $boatCategoryArr['created_at'] = Carbon::now();
                $boatCategoryID = CommonModel::addRecord($boatCategoryArr,'Powerpanel\BoatCategory\Models\BoatCategory');
                if (!empty($boatCategoryID)) {
                    $id = $boatCategoryID;
                    $newBoatObj = BoatCategory::getRecordForLogById($id);
                    $logArr = MyLibrary::logData($id);
                    $logArr['varTitle'] = $newBoatObj->varTitle;
                    Log::recordLog($logArr);
                    if (Auth::user()->can('recent-updates-list')) {
                        $notificationArr = MyLibrary::notificationData($id, $newBoatObj);
                        RecentUpdates::setNotification($notificationArr);
                    }
                    $actionMessage = trans('boatcategory::template.boatCategoryModule.addedMessage');
                }
            }
            $this->flushCache();
            if (!empty($data['saveandexit']) && $data['saveandexit'] == 'saveandexit') {
                return redirect()->route('powerpanel.boat-category.index')->with('message', $actionMessage);
            } else {
                return redirect()->route('powerpanel.boat-category.edit', $id)->with('message', $actionMessage);
            }
        } else {
            return Redirect::back()->withErrors($validator)->withInput();
        }
    }
/**
 * This method loads boatCategory table data on view
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
        $filterArr['searchFilter'] = !empty(Request::get('searchValue')) ? Request::get('searchValue') : '';
        $filterArr['boatCategoryFilter'] = !empty(Request::get('boatCategoryFilter')) ? Request::get('boatCategoryFilter') : '';
        $filterArr['personalityFilter'] = !empty(Request::get('personalityFilter')) ? Request::get('personalityFilter') : '';
        $filterArr['paymentFilter'] = !empty(Request::get('paymentFilter')) ? Request::get('paymentFilter') : '';
        $filterArr['rangeFilter'] = !empty(Request::get('rangeFilter')) ? Request::get('rangeFilter') : '';
        $filterArr['iDisplayLength'] = intval(Request::get('length'));
        $filterArr['iDisplayStart'] = intval(Request::get('start'));
        $sEcho = intval(Request::get('draw'));
        $arrResults = BoatCategory::getRecordList($filterArr);
        $iTotalRecords = CommonModel::getRecordCount($filterArr, true,false, 'Powerpanel\BoatCategory\Models\BoatCategory');
        $totalRecords = CommonModel::getTotalRecordCount('Powerpanel\BoatCategory\Models\BoatCategory');

        if (!empty($arrResults)) {
            foreach ($arrResults as $key => $value) {
                $records["data"][] = $this->tableData($value, $totalRecords);
            }
        }

        $records["customActionStatus"] = "OK";
        $records["draw"] = $sEcho;
        $records["recordsTotal"] = $iTotalRecords;
        $records["recordsFiltered"] = $iTotalRecords;

        return json_encode($records);
    }

    public function get_builder_list()
    {
        $records = BoatCategory::getRecordList();
        $opt = '<option value="">All Category</option>';
        foreach ($records as $record) {
            $opt .= '<option value="' . $record->id . '">' . $record->varTitle . '</option>';
        }
        return $opt;
    }

/**
 * This method delete multiples boatCategory
 * @return  true/false
 * @since   2017-07-15
 * @author  NetQuick
 */
    public function DeleteRecord(Request $request)
    {
        $data = Request::all('ids');
        $update = MyLibrary::deleteMultipleRecords($data,false,false, 'Powerpanel\BoatCategory\Models\BoatCategory');
        $this->flushCache();
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
        MyLibrary::swapOrder($order, $exOrder, 'Powerpanel\BoatCategory\Models\BoatCategory');
        $this->flushCache();
    }
/**
 * This method destroys Banner in multiples
 * @return  Banner index view
 * @since   2016-10-25
 * @author  NetQuick
 */
    public function publish(Request $request)
    {
        $alias = Request::get('alias');
        $val = Request::get('val');
        $update = MyLibrary::setPublishUnpublish($alias, $val,'Powerpanel\BoatCategory\Models\BoatCategory');
        $this->flushCache();
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
                                                        <th>' . trans("template.common.parentCategory") . '</th>
                                                        <th>' . trans("template.common.shortDescription") . '</th>
                                                        <th>' . trans("template.common.description") . '</th>
                                                        <th>' . trans("template.common.displayorder") . '</th>
                                                        <th>' . trans("template.common.metatitle") . '</th>
                                                        <th>' . trans("template.common.metakeyword") . '</th>
                                                        <th>' . trans("template.common.metadescription") . '</th>
                                                        <th>' . trans("template.common.publish") . '</th>
                                                    </tr>
                                              </thead>
                                              <tbody>
                                                    <tr>
                                                          <td>' . $data->varTitle . '</td>';
        if ($data->intParentCategoryId > 0) {
            $catIDS[] = $data->intParentCategoryId;
            $parentCateName = BoatCategory::getParentCategoryNameBycatId($catIDS);
            $parentCateName = $parentCateName[0]->varTitle;
            $returnHtml .= '<td>' . $parentCateName . '</td>';
        } else {
            $returnHtml .= '<td>-</td>';
        }
        $returnHtml .= '<td>' . $data->txtShortDescription . '</td>
                                                          <td>' . $data->txtDescription . '</td>
                                                          <td>' . ($data->intDisplayOrder) . '</td>
                                                          <td>' . $data->varMetaTitle . '</td>
                                                          <td>' . $data->varMetaKeyword . '</td>
                                                          <td>' . $data->varMetaDescription . '</td>
                                                          <td>' . $data->chrPublish . '</td>
                                                    </tr>
                                              </tbody>
                                        </table>';
        return $returnHtml;
    }

    public function tableData($value = false, $totalRecords)
    {
        $hasRecords = Boat::getBoatCategoryCountById($value->id);
        $isParent = BoatCategory::getCountById($value->id);
        $details = '';
        $parent_category_name = ' ';
        $publish_action = '';
        $titleData = "";
        $details = '';
        if (Auth::user()->can('boat-category-edit')) {
            $details .= '<a class="without_bg_icon" title="' . trans("template.common.edit") . '" href="' . route('powerpanel.boat-category.edit', array('alias' => $value->id)) . '"><i class="fa fa-pencil"></i></a>';
        }
        if (Auth::user()->can('boat-category-delete') && $hasRecords == 0 && $isParent == 0) {
            $details .= '&nbsp;<a class="without_bg_icon delete" title="' . trans("template.common.delete") . '" data-controller="boat-category" data-alias = "' . $value->id . '"><i class="fa fa-times"></i></a>';
        }
        if (Auth::user()->can('boat-category-publish')) {
            if ($hasRecords == 0 && $isParent == 0) {
                if ($value->chrPublish == 'Y') {
                    $publish_action .= '<input data-off-text="No" data-on-text="Yes" class="make-switch publish" class="make-switch publish" data-off-color="info" data-on-color="primary" type="checkbox" data-controller="powerpanel/boat-category" title="' . trans("template.common.publishedRecord") . '" data-value="Unpublish" data-alias="' . $value->id . '">';
                } else {
                    $publish_action .= '<input checked="" data-off-text="No" data-on-text="Yes" class="make-switch publish" class="make-switch publish" data-off-color="info" data-on-color="primary" type="checkbox" data-controller="powerpanel/boat-category" title="' . trans("template.common.unpublishedRecord") . '" data-value="Publish" data-alias="' . $value->id . '">';
                }
            } else {
                $publish_action = '-';
            }
        }
        if ($hasRecords > 0) {
            $titleData = 'This category is selected in ' . $hasRecords . ' record(s) so it can&#39;t be deleted or unpublished.';
        }
        if ($isParent > 0) {
            $titleData = 'This category is selected as Parent Category in ' . $isParent . ' record(s) so it can&#39;t be deleted or unpublished.';
        }
        $checkbox = '<a href="javascript:;" data-toggle="tooltip" data-placement="right" data-toggle="tooltip" data-original-title="' . $titleData . '" title="' . $titleData . '"><i style="color:red" class="fa fa-exclamation-triangle"></i></a>';
        $parentCategoryTitle = '-';
        if (!empty($value->intParentCategoryId) && $value->intParentCategoryId > 0) {
            $catIDS[] = $value->intParentCategoryId;
            $parentCategoryName = BoatCategory::getParentCategoryNameBycatId($catIDS);
            $parentCategoryTitle = $parentCategoryName[0]->varTitle;
        }

        
        $orderArrow = '';
        $dispOrder = $value->intDisplayOrder;
       
      
        if ($value->intDisplayOrder != 1) {
            $orderArrow .= '<a href="javascript:;" data-order="' . $value->intDisplayOrder . '" class="moveDwn"><i class="fa fa-arrow-up" aria-hidden="true"></i></a>';
        }
        $orderArrow .= " ".$dispOrder." ";
        if ($totalRecords != $value->intDisplayOrder) {
            $orderArrow .= '<a href="javascript:;" data-order="' . $value->intDisplayOrder . '" class="moveUp"><i class="fa fa-arrow-down" aria-hidden="true"></i></a>';
        }
        $ordervalue=$value->intDisplayOrder;
        if (Auth::user()->can('boat-category-edit')) {
            $title = '<div class="pages_title_div_row"><div class="quick_edit"><a class="" title="' . trans("template.common.edit") . '" href="' . route('powerpanel.boat-category.edit', array('alias' => $value->id)) . '">' . $value->varTitle . '</a></div></div>';
        } else {
            $title = $value->varTitle;
        }

        $records = array(
            ($hasRecords == 0 && $isParent == 0) ? '<input type="checkbox" name="delete" class="chkDelete" value="' . $value->id . '">' : $checkbox,
            $title,
            $orderArrow,
            $publish_action,
            $details,
            $ordervalue,
        );
        return $records;
    }
/**
 * This method loads boatCategory edit view
 * @param   Alias of record
 * @return  View
 * @since   2017-11-10
 * @author  NetQuick
 */
    public function edit($alias = false)
    {
        $isParent = 0;
        if (!is_numeric($alias)) {
            $categories = Category_builder::Parentcategoryhierarchy(false, false, 'Powerpanel\BoatCategory\Models\BoatCategory');
            $total = CommonModel::getRecordCount(false,false,false, 'Powerpanel\BoatCategory\Models\BoatCategory');
            $total = $total + 1;
            $this->breadcrumb['title'] = trans('boatcategory::template.boatCategoryModule.addBoatCategory');
            $this->breadcrumb['module'] = trans('boatcategory::template.boatCategoryModule.manageBoatCategory');
            $this->breadcrumb['url'] = 'powerpanel/boat-category';
            $this->breadcrumb['inner_title'] = trans('boatcategory::template.boatCategoryModule.addBoatCategory');
            $breadcrumb = $this->breadcrumb;
            $hasRecords = 0;
            $data = compact('total', 'breadcrumb', 'categories', 'hasRecords', 'isParent');
        } else {

            $id = $alias;
            $boatCategory = BoatCategory::getRecordById($id);
            if (empty($boatCategory)) {
                return redirect()->route('powerpanel.boat-category.add');
            }
            $hasRecords = Boat::getBoatCategoryCountById($boatCategory->id);
      

            $isParent = BoatCategory::getCountById($boatCategory->id);
            $categories = Category_builder::Parentcategoryhierarchy($boatCategory->intParentCategoryId, $boatCategory->id,'Powerpanel\BoatCategory\Models\BoatCategory');

            $metaInfo = array('varMetaTitle' => $boatCategory->varMetaTitle, 'varMetaKeyword' => $boatCategory->varMetaKeyword, 'varMetaDescription' => $boatCategory->varMetaDescription);
            $this->breadcrumb['title'] = trans('boatcategory::template.common.edit') . ' - ' . $boatCategory->varTitle;
            $this->breadcrumb['module'] = trans('boatcategory::template.boatCategoryModule.manageBoatCategory');
            $this->breadcrumb['url'] = 'powerpanel/boat-category';
            $this->breadcrumb['inner_title'] = trans('boatcategory::template.common.edit') . ' - ' . $boatCategory->varTitle;
            $breadcrumb = $this->breadcrumb;
            //add hasrecords for boat:: modle-
            $data = compact('categories','hasRecords', 'isParent', 'boatCategory', 'metaInfo', 'breadcrumb');
        }
        return view('boatcategory::powerpanel.actions', $data);
    }
/**
 * This method handels loading process of generating html menu from array data
 * @return  Html menu
 * @param  parentId, parentUrl, menu_array
 * @since   04-08-2017
 * @author  NetQuick
 */
    public function getChildren($CatId = false)
    {
        $serCats = BoatCategory::where('intParentCategoryId', $CatId)->get();
        $response = false;
        $html = '';
        foreach ($serCats as $serCat) {
            if (isset($serCat->intParentCategoryId)) {
                $html = '<ul class="dd-list menu_list_set">';
                $html .= '<li class="dd-item">';
                $html .= $serCat->varTitle;
                $html .= $this->getChildren($serCat->id);
                $html .= '</li>';
                $html .= '</ul>';
            }
        }
        $response = $html;
        return $response;
    }
    public function addCatAjax()
    {
        $data = Request::all();
        return AddCategoryAjax::Add($data, 'BoatCategory');
    }
    public static function flushCache()
    {
        Cache::tags('BoatCategory')->flush();
    }
}
