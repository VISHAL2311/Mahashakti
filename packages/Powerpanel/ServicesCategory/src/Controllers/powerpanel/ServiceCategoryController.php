<?php
namespace Powerpanel\ServicesCategory\Controllers\Powerpanel;

use App\Alias;
use App\CommonModel;
use App\Helpers\AddCategoryAjax;
use App\Helpers\Category_builder;
use App\Helpers\MyLibrary;
use App\Http\Controllers\PowerpanelController;
use App\Log;
use App\RecentUpdates;
use Powerpanel\ServicesCategory\Models\ServiceCategory;
use Powerpanel\Services\Models\Services;
use Auth;
use Cache;
use Carbon\Carbon;
use Config;
use Illuminate\Support\Facades\Redirect;
use Request;
use Validator;

class ServiceCategoryController extends PowerpanelController
{
    public function __construct()
    {
        parent::__construct();
        if (isset($_COOKIE['locale'])) {
            app()->setLocale($_COOKIE['locale']);
        }
    }
/**
 * This method handels load process of serviceCategory
 * @return  View
 * @since   2017-11-10
 * @author  NetQuick
 */
    public function index()
    {
        $iTotalRecords = CommonModel::getRecordCount(false,false,false, 'Powerpanel\ServicesCategory\Models\ServiceCategory');
        $this->breadcrumb['title'] = trans('servicescategory::template.serviceCategoryModule.manageServiceCategory');
        $breadcrumb = $this->breadcrumb;
        return view('servicescategory::powerpanel.index', compact('iTotalRecords', 'breadcrumb'));
    }
/**
 * This method stores serviceCategory modifications
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
            'short_description' => 'required|max:' . (isset($settings) ? $settings->short_desc_length : 400),
            'alias' => 'required',
        );
        $messsages = array(
            'display_order.required' => trans('servicescategory::template.serviceCategoryModule.displayOrder'),
            'display_order.greater_than_zero' => trans('servicescategory::template.serviceCategoryModule.displayGreaterThan'),
            'short_description.required' => trans('servicescategory::template.serviceCategoryModule.shortDescription'),
        );
        $data['short_description'] = trim(preg_replace('/\s\s+/', ' ', $data['short_description']));
        $validator = Validator::make($data, $rules, $messsages);
        if ($validator->passes()) {
            $id = Request::segment(3);
            $actionMessage = trans('servicescategory::template.common.oppsSomethingWrong');
            if (is_numeric($id)) {
                #Edit post Handler=======
                if ($data['oldAlias'] != $data['alias']) {
                    Alias::updateAlias($data['oldAlias'], $data['alias']);
                }
                $serviceCategory = ServiceCategory::getRecordForLogById($id);
                $updateServiceCategoryFields = [
                    'varTitle' => trim($data['title']),
                    'intParentCategoryId' => $data['parent_category_id'],
                    'chrPublish' => isset($data['chrMenuDisplay']) ? $data['chrMenuDisplay'] : 'Y',
                    'txtDescription' => $data['description'],
                    'txtShortDescription' => trim($data['short_description']),
                    'varMetaTitle' => trim($data['varMetaTitle']),
                    //'varMetaKeyword' => trim($data['varMetaKeyword']),
                    'varMetaDescription' => trim($data['varMetaDescription']),
                ];
                $whereConditions = ['id' => $serviceCategory->id];
                $update = CommonModel::updateRecords($whereConditions, $updateServiceCategoryFields,false, 'Powerpanel\ServicesCategory\Models\ServiceCategory');
                if ($update) {
                    if (!empty($id)) {
                        MyLibrary::swapOrderEdit($data['display_order'], $serviceCategory->id,false,false, 'Powerpanel\ServicesCategory\Models\ServiceCategory');
                        $logArr = MyLibrary::logData($serviceCategory->id);
                        if (Auth::user()->can('log-advanced')) {
                            $newServiceObj = ServiceCategory::getRecordForLogById($serviceCategory->id);
                            $oldRec = $this->recordHistory($serviceCategory);
                            $newRec = $this->recordHistory($newServiceObj);
                            $logArr['old_val'] = $oldRec;
                            $logArr['new_val'] = $newRec;
                        }
                        $logArr['varTitle'] = trim($data['title']);
                        Log::recordLog($logArr);
                        if (Auth::user()->can('recent-updates-list')) {
                            if (!isset($newServiceObj)) {
                                $newServiceObj = ServiceCategory::getRecordForLogById($serviceCategory->id);
                            }
                            $notificationArr = MyLibrary::notificationData($serviceCategory->id, $newServiceObj);
                            RecentUpdates::setNotification($notificationArr);
                        }
                        $actionMessage = trans('servicescategory::template.serviceCategoryModule.successMessage');
                    }
                }
            } else {
                #Add post Handler=======
                $serviceCategoryArr = [];
                $serviceCategoryArr['intAliasId'] = MyLibrary::insertAlias($data['alias']);
                $serviceCategoryArr['varTitle'] = trim($data['title']);
                $serviceCategoryArr['intDisplayOrder'] = MyLibrary::swapOrderAdd($data['display_order'],false,false, 'Powerpanel\ServicesCategory\Models\ServiceCategory');
                $serviceCategoryArr['intParentCategoryId'] = $data['parent_category_id'];
                $serviceCategoryArr['txtDescription'] = $data['description'];
                $serviceCategoryArr['txtShortDescription'] = trim($data['short_description']);
                $serviceCategoryArr['varMetaTitle'] = trim($data['varMetaTitle']);
                // $serviceCategoryArr['varMetaKeyword'] = trim($data['varMetaKeyword']);
                $serviceCategoryArr['varMetaDescription'] = trim($data['varMetaDescription']);
                $serviceCategoryArr['chrPublish'] = $data['chrMenuDisplay'];
                $serviceCategoryArr['created_at'] = Carbon::now();
                $serviceCategoryID = CommonModel::addRecord($serviceCategoryArr,'Powerpanel\ServicesCategory\Models\ServiceCategory');
                if (!empty($serviceCategoryID)) {
                    $id = $serviceCategoryID;
                    $newServiceObj = ServiceCategory::getRecordForLogById($id);
                    $logArr = MyLibrary::logData($id);
                    $logArr['varTitle'] = $newServiceObj->varTitle;
                    Log::recordLog($logArr);
                    if (Auth::user()->can('recent-updates-list')) {
                        $notificationArr = MyLibrary::notificationData($id, $newServiceObj);
                        RecentUpdates::setNotification($notificationArr);
                    }
                    $actionMessage = trans('servicescategory::template.serviceCategoryModule.addedMessage');
                }
            }
            $this->flushCache();
            if (!empty($data['saveandexit']) && $data['saveandexit'] == 'saveandexit') {
                return redirect()->route('powerpanel.service-category.index')->with('message', $actionMessage);
            } else {
                return redirect()->route('powerpanel.service-category.edit', $id)->with('message', $actionMessage);
            }
        } else {
            return Redirect::back()->withErrors($validator)->withInput();
        }
    }
/**
 * This method loads serviceCategory table data on view
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
        $filterArr['serviceCategoryFilter'] = !empty(Request::get('serviceCategoryFilter')) ? Request::get('serviceCategoryFilter') : '';
        $filterArr['personalityFilter'] = !empty(Request::get('personalityFilter')) ? Request::get('personalityFilter') : '';
        $filterArr['paymentFilter'] = !empty(Request::get('paymentFilter')) ? Request::get('paymentFilter') : '';
        $filterArr['rangeFilter'] = !empty(Request::get('rangeFilter')) ? Request::get('rangeFilter') : '';
        $filterArr['iDisplayLength'] = intval(Request::get('length'));
        $filterArr['iDisplayStart'] = intval(Request::get('start'));
        $sEcho = intval(Request::get('draw'));
        $arrResults = ServiceCategory::getRecordList($filterArr);
        $iTotalRecords = CommonModel::getRecordCount($filterArr, true,false, 'Powerpanel\ServicesCategory\Models\ServiceCategory');
        $totalRecords = CommonModel::getTotalRecordCount('Powerpanel\ServicesCategory\Models\ServiceCategory');

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
        $records = ServiceCategory::getRecordList();
        $opt = '<option value="">All Category</option>';
        foreach ($records as $record) {
            $opt .= '<option value="' . $record->id . '">' . $record->varTitle . '</option>';
        }
        return $opt;
    }

/**
 * This method delete multiples serviceCategory
 * @return  true/false
 * @since   2017-07-15
 * @author  NetQuick
 */
    public function DeleteRecord(Request $request)
    {
        $data = Request::all('ids');
        $update = MyLibrary::deleteMultipleRecords($data,false,false, 'Powerpanel\ServicesCategory\Models\ServiceCategory');
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
        MyLibrary::swapOrder($order, $exOrder, 'Powerpanel\ServicesCategory\Models\ServiceCategory');
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
        $update = MyLibrary::setPublishUnpublish($alias, $val,'Powerpanel\ServicesCategory\Models\ServiceCategory');
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
            $parentCateName = ServiceCategory::getParentCategoryNameBycatId($catIDS);
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
        $hasRecords = Services::getCountById($value->id);
        $isParent = ServiceCategory::getCountById($value->id);
        $details = '';
        $parent_category_name = ' ';
        $publish_action = '';
        $titleData = "";
        $details = '<a class="without_bg_icon" href="' . url('powerpanel/services/add?category=' . $value->id) . '" title="' . trans("servicescategory::template.serviceCategoryModule.addService") . '"><i class="icon-notebook"></i></a>';
        if (Auth::user()->can('service-category-edit')) {
            $details .= '<a class="without_bg_icon" title="' . trans("template.common.edit") . '" href="' . route('powerpanel.service-category.edit', array('alias' => $value->id)) . '"><i class="fa fa-pencil"></i></a>';
        }
        if (Auth::user()->can('service-category-delete') && $hasRecords == 0 && $isParent == 0) {
            $details .= '&nbsp;<a class="without_bg_icon delete" title="' . trans("template.common.delete") . '" data-controller="service-category" data-alias = "' . $value->id . '"><i class="fa fa-times"></i></a>';
        }
        if (Auth::user()->can('service-category-publish')) {
            if ($hasRecords == 0 && $isParent == 0) {
                if ($value->chrPublish == 'Y') {
                    $publish_action .= '<input data-off-text="No" data-on-text="Yes" class="make-switch publish" class="make-switch publish" data-off-color="info" data-on-color="primary" type="checkbox" data-controller="powerpanel/service-category" title="' . trans("template.common.publishedRecord") . '" data-value="Unpublish" data-alias="' . $value->id . '">';
                } else {
                    $publish_action .= '<input checked="" data-off-text="No" data-on-text="Yes" class="make-switch publish" class="make-switch publish" data-off-color="info" data-on-color="primary" type="checkbox" data-controller="powerpanel/service-category" title="' . trans("template.common.unpublishedRecord") . '" data-value="Publish" data-alias="' . $value->id . '">';
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
            $parentCategoryName = ServiceCategory::getParentCategoryNameBycatId($catIDS);
            $parentCategoryTitle = $parentCategoryName[0]->varTitle;
        }

        $orderArrow = '';
        $orderArrow .= '<span class="pageorderlink">';
        if ($totalRecords != $value->intDisplayOrder) {
            $orderArrow .= '<a href="javascript:;" data-order="' . $value->intDisplayOrder . '" class="moveUp"><i class="fa fa-plus" aria-hidden="true"></i></a> ';
        }
        $orderArrow .= $value->intDisplayOrder . ' ';
        if ($value->intDisplayOrder != 1) {
            $orderArrow .= ' <a href="javascript:;" data-order="' . $value->intDisplayOrder . '" class="moveDwn"><i class="fa fa-minus" aria-hidden="true"></i></a>';
        }
        $orderArrow .= '</span>';

        $records = array(
            ($hasRecords == 0 && $isParent == 0) ? '<input type="checkbox" name="delete" class="chkDelete" value="' . $value->id . '">' : $checkbox,
            '<a class="without_bg_icon" title="Edit" href="' . route('powerpanel.service-category.edit', array('alias' => $value->id)) . '">' . $value->varTitle . '</a>',
            '<a href="javascript:void(0)" class="without_bg_icon" onclick="return hs.htmlExpand(this,{width:300,headingText:\'' . trans("template.common.shortdescription") . '\',wrapperClassName:\'titlebar\',showCredits:false});"><span aria-hidden="true" class="fa fa-file-text-o"></span></a>
          <div class="highslide-maincontent">' . nl2br($value->txtShortDescription) . '</div>
        </div>',
            $parentCategoryTitle,
            ($hasRecords > 0) ? '<a href="' . url('powerpanel/services?category=' . $value->id) . '">' . trans("template.common.view") . ' (' . $hasRecords . ')</a>' : '-',
            $orderArrow,
            $publish_action,
            $details,
            $value->intDisplayOrder,
        );
        return $records;
    }
/**
 * This method loads serviceCategory edit view
 * @param   Alias of record
 * @return  View
 * @since   2017-11-10
 * @author  NetQuick
 */
    public function edit($alias = false)
    {
        $isParent = 0;
        if (!is_numeric($alias)) {
            $categories = Category_builder::Parentcategoryhierarchy(false, false, 'Powerpanel\ServicesCategory\Models\ServiceCategory');
            $total = CommonModel::getRecordCount(false,false,false, 'Powerpanel\ServicesCategory\Models\ServiceCategory');
            $total = $total + 1;
            $this->breadcrumb['title'] = trans('servicescategory::template.serviceCategoryModule.addServiceCategory');
            $this->breadcrumb['module'] = trans('servicescategory::template.serviceCategoryModule.manageServiceCategory');
            $this->breadcrumb['url'] = 'powerpanel/service-category';
            $this->breadcrumb['inner_title'] = trans('servicescategory::template.serviceCategoryModule.addServiceCategory');
            $breadcrumb = $this->breadcrumb;
            $hasRecords = 0;
            $data = compact('total', 'breadcrumb', 'categories', 'hasRecords', 'isParent');
        } else {

            $id = $alias;
            $serviceCategory = ServiceCategory::getRecordById($id);
            if (empty($serviceCategory)) {
                return redirect()->route('powerpanel.service-category.add');
            }
            $hasRecords = Services::getCountById($serviceCategory->id);
            $isParent = ServiceCategory::getCountById($serviceCategory->id);
            $categories = Category_builder::Parentcategoryhierarchy($serviceCategory->intParentCategoryId, $serviceCategory->id,'Powerpanel\ServicesCategory\Models\ServiceCategory');

            $metaInfo = array('varMetaTitle' => $serviceCategory->varMetaTitle, 'varMetaKeyword' => $serviceCategory->varMetaKeyword, 'varMetaDescription' => $serviceCategory->varMetaDescription);
            $this->breadcrumb['title'] = trans('servicescategory::template.common.edit') . ' - ' . $serviceCategory->varTitle;
            $this->breadcrumb['module'] = trans('servicescategory::template.serviceCategoryModule.manageServiceCategory');
            $this->breadcrumb['url'] = 'powerpanel/service-category';
            $this->breadcrumb['inner_title'] = trans('servicescategory::template.common.edit') . ' - ' . $serviceCategory->varTitle;
            $breadcrumb = $this->breadcrumb;
            $data = compact('categories', 'hasRecords', 'isParent', 'serviceCategory', 'metaInfo', 'breadcrumb');
        }
        return view('servicescategory::powerpanel.actions', $data);
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
        $serCats = ServiceCategory::where('intParentCategoryId', $CatId)->get();
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
        return AddCategoryAjax::Add($data, 'ServiceCategory');
    }
    public static function flushCache()
    {
        Cache::tags('ServiceCategory')->flush();
    }
}
