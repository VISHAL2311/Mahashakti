<?php
namespace Powerpanel\Services\Controllers\Powerpanel;

use App\Alias;
use App\CommonModel;
use App\Helpers\AddImageModelRel;
use App\Helpers\AddVideoModelRel;
use Powerpanel\Services\Models\Categories;
use App\Helpers\CategoryArrayBuilder;
use App\Helpers\Category_builder;
use App\Helpers\MyLibrary;
use App\Helpers\resize_image;
use App\Http\Controllers\PowerpanelController;
use App\Log;
use App\Modules;
use App\RecentUpdates;
use Powerpanel\ServicesCategory\Models\ServiceCategory;
use Powerpanel\Services\Models\Services;
use App\video;
use Auth;
use Cache;
use File;
use App\Helpers\FrontPageContent_Shield;
use Powerpanel\Workflow\Models\Workflow;
use Powerpanel\Workflow\Models\WorkflowLog;
use App\UserNotification;
use Carbon\Carbon;
use Config;
use Illuminate\Support\Facades\Redirect;
use Request;
use Validator;

class ServicesController extends PowerpanelController
{

    public $catModule;
    public function __construct()
    {
        parent::__construct();
        if (isset($_COOKIE['locale'])) {
            app()->setLocale($_COOKIE['locale']);
        }
         $this->MyLibrary = new MyLibrary();
        $this->CommonModel = new CommonModel();
        $this->Alias = new Alias();
    }
    /**
     * This method handels load process of services
     * @return  View
     * @since   2017-11-10
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
        $total = Services::getRecordCount();
        $NewRecordsCount = Services::getNewRecordsCount();

        $iTotalRecords = CommonModel::getRecordCount(false,false,false, 'Powerpanel\Services\Models\Services');
        $ServicesCategory = $iTotalRecords > 0 ? ServiceCategory::getCatWithParent() : null;
        $this->breadcrumb['title'] = trans('services::template.serviceModule.manageServices');
        $breadcrumb = $this->breadcrumb;
        if (method_exists($this->CommonModel, 'GridColumnData')) {
            $settingdata = CommonModel::GridColumnData(Config::get('Constant.MODULE.ID'));
            $settingarray = array();
            foreach ($settingdata as $sdata) {
                $settingarray[$sdata->chrtab][] = $sdata->columnid;
            }
        } else {
            $settingarray = '';
        }
        $settingarray = json_encode($settingarray);
        return view('services::powerpanel.index', compact('iTotalRecords', 'ServicesCategory', 'breadcrumb', 'total', 'NewRecordsCount','settingarray'));
    }

    public function insertNewRecord($data, $servicesArr, $preview = 'N') {
        $response = false;
        $servicesArr['chrMain'] = 'Y';
        $servicesArr['varTitle'] = stripslashes(trim($data['title']));
        $servicesArr['intFkCategory'] = $data['category_id'];
        $servicesArr['intAliasId'] = MyLibrary::insertAlias($data['alias'], false, $preview);
        $servicesArr['txtShortDescription'] = stripslashes(trim($data['short_description']));
        if (Config::get('Constant.DEFAULT_VISUAL') == 'Y') {
            if ($data['section'] != '[]') {
                $vsection = $data['section'];
            } else {
                $vsection = '';
            }
        } else {
            $vsection = $data['description'];
        }
        $servicesArr['txtDescription'] = $vsection;
        $servicesArr['varMetaTitle'] = stripslashes(trim($data['varMetaTitle']));
        $servicesArr['varMetaDescription'] = stripslashes(trim($data['varMetaDescription']));
        if (isset($data['chrPageActive']) && $data['chrPageActive'] != '') {
            $servicesArr['chrPageActive'] = $data['chrPageActive'];
        }
        $servicesArr['UserID'] = auth()->user()->id;
        $servicesArr['created_at'] = Carbon::now();
        $servicesArr['fkIntImgId'] = !empty($data['img_id']) ? $data['img_id'] : null;
        $servicesID = CommonModel::addRecord($servicesArr, 'Powerpanel\Services\Models\Services');
        if (!empty($servicesID)) {
            $id = $servicesID;
            $newServiceObj = Services::getRecordForLogById($id);
            $logArr = MyLibrary::logData($id, false, $addlog);
            $logArr['varTitle'] = $newServiceObj->varTitle;
            Log::recordLog($logArr);
            if (Auth::user()->can('recent-updates-list')) {
                $notificationArr = MyLibrary::notificationData($id, $newServiceObj);
                RecentUpdates::setNotification($notificationArr);
            }
            $response = $newServiceObj;
            self::flushCache();
            $actionMessage = trans('blogs::template.servicesModule.addMessage');
        }
        return $response;
    }

    /**
     * This method loads service edit view
     * @param   Alias of record
     * @return  View
     * @since   2017-11-10
     * @author  NetQuick
     */
    public function edit($id = false)
    {
        $module = Modules::getModule('service-category');
        $category = ServiceCategory::getCatWithParent($module->id);
        $templateData = array();
        $documentManager = true;
        $imageManager = true;
        $videoManager = true;
        $userIsAdmin = false;
        if (isset($this->currentUserRoleData) && !empty($this->currentUserRoleData)) {
            if ($this->currentUserRoleData->chrIsAdmin == 'Y') {
                $userIsAdmin = true;
            }
        } else {
            $userIsAdmin = true;
        }

        #icon code======================================
        $categoryHeirarchy = Category_builder::Parentcategoryhierarchy(false, false, 'Powerpanel\ServicesCategory\Models\ServiceCategory');
        if (!is_numeric($id)) {
            //Add Record
            $total = Services::getRecordCount();
            if (auth()->user()->can('services-create') || $userIsAdmin) {
                $total = $total + 1;
            }

            $catIDs = Request::get('category');
            $getID = !empty($catIDs)?[$catIDs]:false;
            $categoryHeirarchyMain = Categories::Parentcategoryhierarchy($getID, false, 'Powerpanel\ServicesCategory\Models\ServiceCategory');
            $total = CommonModel::getRecordCount(false,false,false, 'Powerpanel\Services\Models\Services');
            $total = $total + 1;
            $this->breadcrumb['title'] = trans('services::template.serviceModule.addService');
            $this->breadcrumb['module'] = trans('services::template.serviceModule.manageServices');
            $this->breadcrumb['url'] = 'powerpanel/services';
            $this->breadcrumb['inner_title'] = trans('services::template.serviceModule.addService');
            $breadcrumb = $this->breadcrumb;
            $services = Services::getRecordById($id);
            
            $data = compact('total', 'breadcrumb', 'imageManager', 'imageManager', 'videoManager', 'categoryHeirarchy', 'categoryHeirarchyMain');
        } else {
            //Edit Record
            $total = Services::getRecordCount();
            if (auth()->user()->can('services-create') || $userIsAdmin) {
                $total = $total + 1;
            }
            $service = Services::getRecordById($id);

            $videoIDAray = explode(',', $service->fkIntVideoId);
            $videoData = video::getVideoData($videoIDAray);

            $cateID = unserialize($service->txtCategories);
            $categoryHeirarchyMain = Categories::Parentcategoryhierarchy($cateID, $service->id, 'Powerpanel\ServicesCategory\Models\ServiceCategory');

            if (empty($service)) {
                return redirect()->route('powerpanel.services.add');
            }

            $metaInfo = array('varMetaTitle' => $service->varMetaTitle, 'varMetaKeyword' => $service->varMetaKeyword, 'varMetaDescription' => $service->varMetaDescription);
            $this->breadcrumb['title'] = trans('services::template.serviceModule.editService') . ' - ' . $service->varTitle;
            $this->breadcrumb['module'] = trans('services::template.serviceModule.manageServices');
            $this->breadcrumb['url'] = 'powerpanel/services';
            $this->breadcrumb['inner_title'] = trans('services::template.serviceModule.editService') . ' - ' . $service->varTitle;
            $templateData['total'] = $total;
            $templateData['breadcrumb'] = $this->breadcrumb;
            $templateData['serviceCategory'] = $category;
            $templateData['imageManager'] = $imageManager;
            $templateData['documentManager'] = $documentManager;
            $breadcrumb = $this->breadcrumb;
            $data = compact('service', 'metaInfo', 'breadcrumb', 'imageManager', 'videoManager', 'categoryHeirarchy', 'videoData', 'categoryHeirarchyMain');
        }

        if (isset($this->currentUserRoleData->chrIsAdmin) && $this->currentUserRoleData->chrIsAdmin != 'Y') {
            $module = Modules::getModuleById(Config::get('Constant.MODULE.ID'));
            if (!$userIsAdmin) {
                $userRole = $this->currentUserRoleData->id;
            } else {
                $userRoleData = Role_user::getUserRoleByUserId(auth()->user()->id);
                $userRole = $userRoleData->role_id;
            }
            $workFlowByCat = Workflow::getRecordByCategoryId($module->intFkGroupCode, $userRole, Config::get('Constant.MODULE.ID'));
            if (!empty($workFlowByCat)) {
                $templateData['chrNeedAddPermission'] = $workFlowByCat->chrNeedAddPermission;
                $templateData['charNeedApproval'] = $workFlowByCat->charNeedApproval;
            } else {
                $templateData['chrNeedAddPermission'] = 'N';
                $templateData['charNeedApproval'] = 'N';
            }
        } else {
            $templateData['chrNeedAddPermission'] = 'N';
            $templateData['charNeedApproval'] = 'N';
        }
        //End Button Name Change For User Side
        $templateData['userIsAdmin'] = $userIsAdmin;
        $templateData['MyLibrary'] = $this->MyLibrary;
        return view('services::powerpanel.actions', $data);
    }

    /**
     * This method stores service modifications
     * @return  View
     * @since   2017-11-10
     * @author  NetQuick
     */
    public function handlePost(Request $request)
    {

        $data = Request::all();
        $approval = false;
        $userIsAdmin = false;
        $actionMessage = trans('services::template.common.oppsSomethingWrong');
        $settings = json_decode(Config::get("Constant.MODULE.SETTINGS"));
        $rules = array(
            'title' => 'required|max:160',
            'display_order' => 'required|greater_than_zero',
            // 'chrMenuDisplay' => 'required',
            'short_description' => 'required|max:' . (isset($settings) ? $settings->short_desc_length : 400),
            'alias' => 'required',
        );
        // $rules['varMetaTitle'] = 'required|max:' . $this->metaLength;
        // $rules['varMetaKeyword'] = 'required|max:' . $this->metaLength;
        // $rules['varMetaDescription'] = 'required|max:' . $this->metaDescriptionLength;

        $messsages = array(
            'display_order.greater_than_zero' => trans('services::template.serviceModule.displayGreaterThan'),
            'short_description.required' => trans('services::template.serviceModule.shortDescription'),
            'varMetaTitle.required' => trans('services::template.serviceModule.metaTitle'),
            // 'varMetaKeyword.required' => trans('services::template.serviceModule.metaKeyword'),
            'varMetaDescription.required' => trans('services::template.serviceModule.metaDescription'),
        );

        $data['short_description'] = trim(preg_replace('/\s\s+/', ' ', $data['short_description']));

        $validator = Validator::make($data, $rules, $messsages);
        if ($validator->passes()) {
            $module = Modules::getModuleById(Config::get('Constant.MODULE.ID'));
            $servicesArr = [];
            $servicesArr['varTitle'] = trim($data['title']);
            $servicesArr['fkIntImgId'] = !empty($data['img_id']) ? $data['img_id'] : null;
            $servicesArr['fkIntVideoId'] = !empty($data['video_id']) ? $data['video_id'] : null;
            $servicesArr['varExternalLink'] = '';
            $servicesArr['varFontAwesomeIcon'] = $data['font_awesome_icon'];
            $servicesArr['txtDescription'] = $data['section'];
            $servicesArr['txtShortDescription'] = trim($data['short_description']);
            $servicesArr['txtCategories'] = isset($data['category_id']) ? serialize($data['category_id']) : null;
            $servicesArr['varPreferences'] = '';
            $servicesArr['chrFeaturedService'] = $data['featuredService'];
            // $servicesArr['chrPublish'] = $data['chrMenuDisplay'];
            $servicesArr['created_at'] = Carbon::now();

            $servicesArr['varMetaTitle'] = trim($data['varMetaTitle']);
            //$servicesArr['varMetaKeyword'] = trim($data['varMetaKeyword']);
            $servicesArr['varMetaDescription'] = trim($data['varMetaDescription']);
            if (isset($this->currentUserRoleData)) {
                $currentUserRoleData = $this->currentUserRoleData;
            }
            $id = Request::segment(3);
            $actionMessage = trans('blogs::template.servicesModule.updateMessage');
            
            if (is_numeric($id)) {   #Edit post Handler=======
                $service = Services::getRecordForLogById($id);
                $updateServicesFields = [];
                $updateServicesFields['varTitle'] = stripslashes(trim($data['title']));
                //  $updateServicesFields['intFKCategory'] = $data['category_id'];
                $updateServicesFields['txtDescription'] = $data['section'];
                $updateServicesFields['varMetaTitle'] = stripslashes(trim($data['varMetaTitle']));
                $updateServicesFields['varMetaDescription'] = stripslashes(trim($data['varMetaDescription']));
                if (Config::get('Constant.CHRSearchRank') == 'Y') {
                    // $updateServicesFields['intSearchRank'] = $data['search_rank'];
                }
                // $updateServicesFields['dtDateTime'] = !empty($data['start_date_time']) ? date('Y-m-d H:i:s', strtotime($data['start_date_time'])) : date('Y-m-d H:i:s');
                // $updateServicesFields['dtEndDateTime'] = !empty($data['end_date_time']) ? date('Y-m-d H:i:s', strtotime($data['end_date_time'])) : null;
                $updateServicesFields['UserID'] = auth()->user()->id;
                if ($data['chrMenuDisplay'] == 'D') {
                    // $updateServicesFields['chrDraft'] = 'D';
                    $updateServicesFields['chrPublish'] = 'N';
                } else {
                    // $updateServicesFields['chrDraft'] = 'N';
                    $updateServicesFields['chrPublish'] = $data['chrMenuDisplay'];
                }
                if (isset($data['chrPageActive']) && $data['chrPageActive'] != '') {
                    $updateServicesFields['chrPageActive'] = $data['chrPageActive'];
                }
                if (isset($data['chrPageActive']) && $data['chrPageActive'] == 'PP') {
                    // $updateServicesFields['varPassword'] = $data['new_password'];
                } else {
                    // $updateServicesFields['varPassword'] = '';
                }
                if ($data['chrMenuDisplay'] == 'D') {
                    $addlog = Config::get('Constant.UPDATE_DRAFT');
                } else {
                    $addlog = '';
                }
                $updateServicesFields['fkIntImgId'] = !empty($data['img_id']) ? $data['img_id'] : null;
                $whereConditions = ['id' => $id];
                if ($service->chrLock == 'Y' && auth()->user()->id != $service->LockUserID) {
                    if ($this->currentUserRoleData->chrIsAdmin != 'Y') {
                        $lockedUserData = User::getRecordById($services->LockUserID, true);
                        $lockedUserName = 'someone';
                        if (!empty($lockedUserData)) {
                            $lockedUserName = $lockedUserData->name;
                        }
                        $actionMessage = "This record has been locked by " . $lockedUserName . ".";
                        return redirect()->route('powerpanel.services.index')->with('message', $actionMessage);
                    }
                }
                if (File::exists(app_path() . '/Workflow.php') != null || File::exists(base_path() . '/packages/Powerpanel/Workflow/src/Models/Workflow.php') != null) {
                    if (!$userIsAdmin) {
                        $userRole = $currentUserRoleData->id;
                    } else {
                        $userRoleData = Role_user::getUserRoleByUserId($services->UserID);
                        if (isset($userRoleData->role_id)) {
                            $userRole = $userRoleData->role_id;
                        } else {
                            $userRole = $this->currentUserRoleData->id;
                        }
                    }

                    if ($data['chrMenuDisplay'] == 'D') {
                        DB::table('menu')->where('intPageId', $id)->where('intfkModuleId', Config::get('Constant.MODULE.ID'))->delete();
                    }

                    $workFlowByCat = Workflow::getRecordByCategoryId($module->intFkGroupCode, $userRole, Config::get('Constant.MODULE.ID'));

                    if (empty($workFlowByCat->varUserId) || $userIsAdmin || $workFlowByCat->charNeedApproval == 'N') {
                        if ($data['oldAlias'] != $data['alias']) {
                            Alias::updateAlias($data['oldAlias'], $data['alias']);
                        }
                        if ((int) $service->fkMainRecord === 0 || empty($workFlowByCat->varUserId)) {
                            $update = CommonModel::updateRecords($whereConditions, $updateServicesFields, false, 'Powerpanel\Services\Models\Services');
                            if ($update) {

                                if ($id > 0 && !empty($id)) {
                                    $logArr = MyLibrary::logData($id);
                                    if (Auth::user()->can('log-advanced')) {
                                        $newServiceObj = Services::getRecordForLogById($id);
                                        $oldRec = $this->recordHistory($service);
                                        
                                        $newRec = $this->newrecordHistory($service, $newServiceObj);
                                        $logArr['old_val'] = $oldRec;
                                        $logArr['new_val'] = $newRec;
                                    }
                                    $logArr['varTitle'] = trim($data['title']);
                                    Log::recordLog($logArr);
                                    if (Auth::user()->can('recent-updates-list')) {
                                        if (!isset($newServiceObj)) {
                                            $newServiceObj = Services::getRecordForLogById($id);
                                        }
                                        $notificationArr = MyLibrary::notificationData($id, $newServiceObj);
                                        RecentUpdates::setNotification($notificationArr);
                                    }
                                    self::flushCache();
                                    if (isset($data['saveandexit']) && $data['saveandexit'] == 'approvesaveandexit') {
                                        $actionMessage = trans('blogs::template.common.recordApprovalMessage');
                                    } else {
                                        $actionMessage = trans('blogs::template.servicesModule.updateMessage');
                                    }
                                }
                            }
                        } else {
                            $updateModuleFields = $updateServicesFields;
                            $this->insertApprovedRecord($updateModuleFields, $data, $id);
                            if (isset($data['saveandexit']) && $data['saveandexit'] == 'approvesaveandexit') {
                                $actionMessage = trans('blogs::template.common.recordApprovalMessage');
                            } else {
                                $actionMessage = trans('blogs::template.servicesModule.updateMessage');
                            }
                            $approval = $id;
                        }
                    } else {

                        if ($workFlowByCat->charNeedApproval == 'Y') {
                            $approvalObj = $this->insertApprovalRecord($service, $data, $updateServicesFields);

                            if (isset($data['saveandexit']) && $data['saveandexit'] == 'approvesaveandexit') {
                                $actionMessage = trans('blogs::template.common.recordApprovalMessage');
                            } else {
                                $actionMessage = trans('blogs::template.servicesModule.updateMessage');
                            }
                            $approval = $approvalObj->id;
                        }

                    }
                } else {
                    $update = CommonModel::updateRecords($whereConditions, $updateServicesFields, false, 'Powerpanel\Services\Models\Services');
                    $actionMessage = trans('blogs::template.blogsModule.updateMessage');
                }
            } else {
                #Add post Handler =======
                $servicesArr['intAliasId'] = MyLibrary::insertAlias($data['alias']);
                $servicesArr['intDisplayOrder'] = self::swap_order_add($data['display_order']);
                $serviceID = CommonModel::addRecord($servicesArr, 'Powerpanel\Services\Models\Services');
                if (File::exists(app_path() . '/Workflow.php') != null || File::exists(base_path() . '/packages/Powerpanel/Workflow/src/Models/Workflow.php') != null) {
                    $workFlowByCat = Workflow::getRecordByCategoryId($module->intFkGroupCode, $currentUserRoleData->id, Config::get('Constant.MODULE.ID'));
                }
                if (!empty($workFlowByCat->varUserId) && $workFlowByCat->chrNeedAddPermission == 'Y' && !$userIsAdmin) {
                    $servicesArr['chrPublish'] = 'N';
                    $servicesObj = $this->insertNewRecord($data, $servicesArr);
                    $servicesArr['chrPublish'] = 'Y';
                    $approvalObj = $this->insertApprovalRecord($servicesObj, $data, $servicesArr);
                    $approval = $servicesObj->id;
                } else {
                    $servicesObj = $this->insertNewRecord($data, $servicesArr);
                    $approval = $servicesObj->id;
                }
                if (isset($data['saveandexit']) && $data['saveandexit'] == 'approvesaveandexit') {
                    $actionMessage = trans('services::template.common.recordApprovalMessage');
                } else {
                    $actionMessage = trans('services::template.servicesModule.addMessage');
                }
                $actionMessage = trans('services::template.servicesModule.addMessage');
                $id = $servicesObj->id;
            }

            AddImageModelRel::sync(explode(',', $data['img_id']), $id);
            AddVideoModelRel::sync(explode(',', $data['video_id']), $id);
            if (!empty($data['saveandexit']) && $data['saveandexit'] == 'saveandexit') {
                return redirect()->route('powerpanel.services.index')->with('message', $actionMessage);
            } else {
                return redirect()->route('powerpanel.services.edit', $id)->with('message', $actionMessage);
            }
        } else {
            return Redirect::back()->withErrors($validator)->withInput();
        }
    }
    /**
     * This method loads services table data on view
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

        $totalRecords_old = CommonModel::getTotalRecordCount('Powerpanel\Services\Models\Services');
        if ($totalRecords_old > $currentrecordcountstart) {
            $filterArr['iDisplayStart'] = intval(Request::get('start'));
        } else {
            $filterArr['iDisplayStart'] = intval(0);
        }
        /**** Delete record then redirect to approriate pagination **/
        $sEcho = intval(Request::get('draw'));
        $arrResults = Services::getRecordList($filterArr);
        $iTotalRecords = CommonModel::getRecordCount($filterArr, true,false, 'Powerpanel\Services\Models\Services');
        $totalRecords = CommonModel::getTotalRecordCount('Powerpanel\Services\Models\Services');
        $end = $filterArr['iDisplayStart'] + $filterArr['iDisplayLength'];
        $end = $end > $iTotalRecords ? $iTotalRecords : $end;

        if (!empty($arrResults)) {
            $this->catModule = Modules::getModule('service-category')->id;
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
     * This method loads service builder data on view
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
        $arrResults = Services::getBuilderRecordList($filterArr);
        $found = $arrResults->toArray();

        if (!empty($found)) {
            foreach ($arrResults as $key => $value) {
                $rows .= $this->tableDataBuilder($value, false, $filterArr['selected']);
            }
        } else {
            $rows .= '<tr id="not-found"><td colspan="4" align="center">No records found.</td></tr>';
        }
        $iTotalRecords = CommonModel::getTotalRecordCount('Powerpanel\Services\Models\Services',false, true);
        $records["data"] = $rows;
        $records["found"] = count($found);
        $records["recordsTotal"] = $iTotalRecords;
        return json_encode($records);
    }

    public function tableDataBuilder($value = false, $fcnt = false, $selected = [])
    {
        $categories = "-";
        if(!empty($value->txtCategories))
        {
            $categoryArr = ServiceCategory::getRecordByIds(unserialize($value->txtCategories))->toArray();
            if(!empty($categoryArr)){
                $categories = array_column($categoryArr, 'varTitle');
                $categories = implode(', ', $categories);
            }
        }
        
        if(isset($value->fkIntImgId) && $value->fkIntImgId != ''){
            $image = '<img src="' . resize_image::resize($value->fkIntImgId, 50, 50) . '">';
        }else{
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
        $record .= '<td width="20%" align="left">';
        $record .= $value->varTitle;
        $record .= '</td>';
        $record .= '<td width="20%" align="center">';
        $record .= $categories;
        $record .= '</td>';
         $record .= '<td width="20%" align="center">';
        $record .= $image;
        $record .= '</td>';
        $record .= '</tr>';

        return $record;
    }
    
    /**
     * This method delete multiples services
     * @return  true/false
     * @since   2017-07-15
     * @author  NetQuick
     */
    public function DeleteRecord(Request $request)
    {
        $data = Request::all('ids');
        $update = MyLibrary::deleteMultipleRecords($data,false,false,'Powerpanel\Services\Models\Services');
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

        MyLibrary::swapOrder($order, $exOrder,'Powerpanel\Services\Models\Services');
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
            $response = MyLibrary::swapOrderAdd($order,false,false,'Powerpanel\Services\Models\Services');
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
        MyLibrary::swapOrderEdit($order, $id,false,false,'Powerpanel\Services\Models\Services');
        self::flushCache();
    }

    public function makeFeatured()
    {
        $id = Request::get('id');
        $featured = Request::get('featured');
        $whereConditions = ['id' => $id];
        $update = CommonModel::updateRecords($whereConditions, ['chrFeaturedService' => $featured],false,'Powerpanel\Services\Models\Services');
        self::flushCache();
        echo json_encode($update);
    }

    /**
     * This method destroys Service in multiples
     * @return  Service index view
     * @since   2016-10-25
     * @author  NetQuick
     */
    public function publish(Request $request)
    {
        $alias = Request::get('alias');
        $val = Request::get('val');
        $update = MyLibrary::setPublishUnpublish($alias, $val,'Powerpanel\Services\Models\Services');
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
														<th>' . trans("template.common.serviceIcon") . '</th>
														<th>' . trans("template.common.shortDescription") . '</th>
														<th>' . trans("template.common.description") . '</th>
														<th>' . trans("template.serviceModule.featuredService") . '</th>
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
																<td>' . $data->chrFeaturedService . '</td>
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
        $publish_action = '';
        if (Auth::user()->can('services-edit')) {
            $details = '<a class="without_bg_icon" title="' . trans("template.common.edit") . '" href="' . route('powerpanel.services.edit', array('alias' => $value->id)) . '"><i class="fa fa-pencil"></i></a>';
        }
        if (Auth::user()->can('services-delete')) {
            $details .= '&nbsp;<a class="without_bg_icon delete" title="' . trans("template.common.delete") . '" data-controller="services" data-alias = "' . $value->id . '"><i class="fa fa-times"></i></a>';
        }

        if (Auth::user()->can('services-publish')) {
            if (!empty($value->chrPublish) && ($value->chrPublish == 'Y')) {
                $publish_action .= '<input data-off-text="No" data-on-text="Yes" class="make-switch publish" class="make-switch publish" data-off-color="info" data-on-color="primary" type="checkbox" data-controller="powerpanel/services" title="' . trans("template.common.publishedRecord") . '" data-value="Unpublish" data-alias="' . $value->id . '">';
            } else {
                $publish_action .= '<input checked="" data-off-text="No" data-on-text="Yes" class="make-switch publish" class="make-switch publish" data-off-color="info" data-on-color="primary" type="checkbox" data-controller="powerpanel/services" title="' . trans("template.common.unpublishedRecord") . '" data-value="Publish" data-alias="' . $value->id . '">';
            }
        }

        /* $details .='<a class="without_bg_icon share" title="Share" data-modal="Services" data-alias="'.$value->id.'"  data-images="'.$value->fkIntImgId.'" data-link = "'.url('/services/'.$value->alias['varAlias']).'" data-toggle="modal" data-target="#confirm_share">
        <i class="fa fa-share-alt"></i></a>';*/

        if (Auth::user()->can('services-edit')) {
            $title = '<a class="" title="Edit" href="' . route('powerpanel.services.edit', array('alias' => $value->id)) . '">' . $value->varTitle . '</a>';
        } else {
            $title = $value->varTitle;
        }
        if (Auth::user()->can('services-reviewchanges') && (File::exists(app_path() . '/Workflow.php') != null || File::exists(base_path() . '/packages/Powerpanel/Workflow/src/Models/Workflow.php') != null)) {
            $update = "<a title=\"Click here to see all approval records.\" class=\"icon_title1\" style=\"margin-right: 5px;\" onclick=\"javascript:expandcollapsepanel(this ,'tasklisting" . $value->id . "', 'mainsingnimg" . $value->id . "'," . $value->id . ")\"><i id=\"mainsingnimg" . $value->id . "\" class=\"la la-plus-square\"></i></a>";
            $rollback = "<a title=\"Click here to see all approved records to rollback.\" class=\"icon_title2\" style=\"margin-right: 5px;\" onclick=\"javascript:expandcollapsepanel_rolback(this ,'tasklisting_rollback" . $value->id . "', 'mainsingnimg_rollback" . $value->id . "'," . $value->id . ")\"><i id=\"mainsingnimg_rollback" . $value->id . "\" class=\"la la-history\"></i></a>";
        } else {
            $update = '';
            $rollback = '';
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

        $category = '';
        if (isset($value->txtCategories) && $value->txtCategories != '') {
            $categoryIDs = unserialize($value->txtCategories);
            $selCategory = ServiceCategory::getParentCategoryNameBycatId($categoryIDs);

            $category .= '<div class="pro-act-btn"><a href="javascript:void(0)" class="without_bg_icon" onclick="return hs.htmlExpand(this,{width:300,headingText:\'' . trans("template.common.category") . '\',wrapperClassName:\'titlebar\',showCredits:false});"><span aria-hidden="true" class="icon-info"></span></a>';
            $category .= '<div class="highslide-maincontent">';
            $category .= '<ul>';
            foreach ($selCategory as $selCat) {
                if (strlen(trim($selCat)) > 0) {
                    $category .= '<li>';
                    $category .= $selCat->varTitle;
                    $category .= '</li>';
                }
            }
            $category .= '<ul>';
            $category .= '</div>';
            $category .= '</div>';
        } else {
            $category .= '<span class="glyphicon glyphicon-minus"></span>';
        }

        $fontAwesomeIcon = '';
        if (!empty($value->varFontAwesomeIcon)) {
            //$fontAwesomeIcon .= ucfirst($value->varFontAwesomeIcon);
            $fontAwesomeIcon .= '<i class="fa ' . strtolower($value->varFontAwesomeIcon) . '"></i>';
        } else {
            $fontAwesomeIcon .= '<span class="glyphicon glyphicon-minus"></span>';
        }

        $featuredService = '';
        if (!empty($value->chrFeaturedService)) {
            if ($value->chrFeaturedService == 'Y') {
                $featuredService .= '<a href="javascript:makeFeatured(' . $value->id . ',\'N\');"><i class="fa fa-star" aria-hidden="true"></i></a>';
            } else {
                $featuredService .= '<a href="javascript:makeFeatured(' . $value->id . ',\'Y\');"><i class="fa fa-star-o" aria-hidden="true"></i></a>';

            }
        } else {
            $featuredService .= '<a href="javascript:makeFeatured(' . $value->id . ',\'Y\');"><i class="fa fa-star-o" aria-hidden="true"></i></a>';

        }

        if (Auth::user()->can('services-edit')) {
            $title = '<a class="" title="' . trans("template.common.edit") . '" href="' . route('powerpanel.services.edit', array('alias' => $value->id)) . '">' . $value->varTitle . '</a>';
        } else {
            $title = $value->varTitle;
        }

        $orderArrow = '';
        $orderArrow .= '<span class="pageorderlink">';
        if ($totalRecord != $value->intDisplayOrder) {
            $orderArrow .= '<a href="javascript:;" data-order="' . $value->intDisplayOrder . '" class="moveUp"> <i class="fa fa-plus " aria-hidden="true"></i></a>';
        }
        $orderArrow .= $value->intDisplayOrder . ' ';
        if ($value->intDisplayOrder != 1) {
            $orderArrow .= '<a href="javascript:;"  data-order="' . $value->intDisplayOrder . '" class="moveDwn"><i class="fa fa-minus" aria-hidden="true"></i></a>';
        }
        $orderArrow .= '</span>';

        $statusdata = '';
        if (method_exists($this->MyLibrary, 'count_days')) {
            $days = MyLibrary::count_days($value->created_at);
            $days_modified = MyLibrary::count_days($value->updated_at);
        } else {
            $days = '';
            $days_modified = '';
        }
        if ($days_modified < Config::get('Constant.DEFAULT_DAYS') && $days < Config::get('Constant.DEFAULT_DAYS')) {
            if ($days < Config::get('Constant.DEFAULT_DAYS')) {
                $statusdata = '<img border="0" title="There was new action on this menu." alt="New" src="' . url('assets/images/new.png') . '">';
            }
        } else {
            if ($days_modified < Config::get('Constant.DEFAULT_DAYS')) {
                $statusdata = '<img border="0" title="There was edit/update action on this menu." alt="Updated" src="' . url('assets/images/updated.png') . '">';
            }
            if ($days < Config::get('Constant.DEFAULT_DAYS')) {
                $statusdata = '<img border="0" title="There was new action on this menu." alt="New" src="' . url('assets/images/new.png') . '">';
            }
        }
        $status = '';
        if ($value->chrDraft == 'D') {
            $status .= Config::get('Constant.DRAFT_LIST') . ' ';
        }
        if ($value->chrAddStar == 'Y') {
            $status .= Config::get('Constant.APPROVAL_LIST') . ' ';
        }

        $records = array(
            '<input type="checkbox" name="delete" class="chkDelete" value="' . $value->id . '">',
            $featuredService,
            '<div class="pages_title_div_row">' . $update . $rollback . $title .' ' . $status . $statusdata . '</div>',
            '<div class="pro-act-btn">
					<a href="javascript:void(0)" class="without_bg_icon" onclick="return hs.htmlExpand(this,{width:300,headingText:\'' . trans("template.common.shortdescription") . '\',wrapperClassName:\'titlebar\',showCredits:false});"><span aria-hidden="true" class="fa fa-file-text-o"></span></a>
						<div class="highslide-maincontent">' . htmlspecialchars_decode($value->txtShortDescription) . '</div>
					</div>',
            $imgIcon,
            $category,
            $fontAwesomeIcon,
            $orderArrow,
            $publish_action,
            $details
        );
        return $records;
    }

    public function tableData_tab1($value) {
        $Hits = Pagehit::where('fkIntAliasId', $value->intAliasId)->count();
        $webHits = '';
        if ($Hits > 0) {
            $webHits .= '<a data-toggle="modal" href="#" onclick=\'HitsPopup("' . $value->id . '","' . $value->intAliasId . '","' . $value->varTitle . '","A")\'>' . $Hits . '</a>
										<div class="new_modal modal fade" id="desc_' . $value->id . '_A" tabindex="-1" aria-hidden="true">
												<div class="modal-dialog" style="margin: 0 auto;display:table;width: 100%;height:100%;max-width: 1000px;">
												<div class="modal-vertical">
												<div class="modal-content">
										<div class="modal-header">
										<button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
										<h3 class="modal-title">Hits Report</h3>
										</div>
										<div class="modal-body">
										<div id="webdata_' . $value->id . '_A"></div>
										</div>
										</div>
										</div>
										</div>
										</div>';
        } else {
            $webHits .= '0';
        }
        $actions = '';
        $publish_action = '';
        if (Auth::user()->can('services-edit')) {
            $actions .= '<a class="" title="' . trans("services::template.common.edit") . '" href="' . route('powerpanel.services.edit', array('alias' => $value->id)) . '">
				<i class="fa fa-pencil"></i></a>';
        }
        if (Auth::user()->can('services-delete') && $this->currentUserRoleData->chrIsAdmin == 'Y') {
            if (Config::get('Constant.DEFAULT_TRASH') == 'Y') {
                $actions .= '<a class="delete-grid" title="' . trans("services::template.common.delete") . '" onclick = \'Trashfun("' . $value->id . '")\' data-controller="NetServiceController" data-alias = "' . $value->id . '" data-tab="A"><i class="fa fa-times"></i></a>';
            } else {
                $actions .= '<a class=" delete" title="' . trans("services::template.common.delete") . '" data-controller="NetServiceController" data-alias = "' . $value->id . '" data-tab="A"><i class="fa fa-times"></i></a>';
            }
        }
        $startDate = date('' . Config::get('Constant.DEFAULT_DATE_FORMAT') . ' ' . Config::get('Constant.DEFAULT_TIME_FORMAT') . '', strtotime($value->dtDateTime));
        // $endDate = !empty($value->dtEndDateTime) ? date('' . Config::get('Constant.DEFAULT_DATE_FORMAT') . ' ' . Config::get('Constant.DEFAULT_TIME_FORMAT') . '', strtotime($value->dtEndDateTime)) : 'No Expiry';
        $Quickedit_startDate = date('Y-m-d H:i', strtotime($value->dtDateTime));
        // $Quickedit_endDate = !empty($value->dtEndDateTime) ? date('Y-m-d H:i', strtotime($value->dtEndDateTime)) : 'No Expiry';

        $title = $value->varTitle;
        if (Auth::user()->can('services-edit')) {
            $categoryRecordAlias = MyLibrary::getRecordAliasByModuleNameRecordId("service-category", $value->intFKCategory);
            if ($value->chrDraft == 'D' || $value->chrAddStar == 'Y') {
                $viewlink = url('/previewpage?url=' . MyLibrary::getFrontUri('services')['uri'] . '/' . $value->id . '/preview/detail');
                $linkviewLable = "Preview";
            } else {
                $viewlink = MyLibrary::getFrontUri('services')['uri'] . '/' . $value->alias->varAlias;
                $linkviewLable = "View";
            }
            //$frontViewLink = MyLibrary::getFrontUri('Services')['uri'] . '/' . $value->alias->varAlias;
            if ($value->chrLock != 'Y') {
                $title = '<div class="quick_edit"><a href = "' . route('powerpanel.services.edit', array('alias' => $value->id)) . '?tab=A">' . $value->varTitle . '</a> <div class="quick_edit_menu">
														<span><a href="' . route('powerpanel.services.edit', array('alias' => $value->id)) . '?tab=A" title="Edit">Edit</a></span>';
                if (Config::get('Constant.DEFAULT_TRASH') == 'Y') {
                    $title .= '<span><a title = "Trash" href = \'javascript:;\' onclick=\'Trashfun("' . $value->id . '")\' class="red" data-tab="A">Trash</a></span>';
                }
                $title .= '<span><a href="' . $viewlink . '" target="_blank" title="' . $linkviewLable . '" >' . $linkviewLable . '</a></span></div></div>';
            } else {
                if (auth()->user()->id != $value->LockUserID) {
                    if ($this->currentUserRoleData->chrIsAdmin == 'Y') {
                        $title = '<div class="quick_edit"><a href = "' . route('powerpanel.services.edit', array('alias' => $value->id)) . '?tab=A">' . $value->varTitle . '</a> <div class="quick_edit_menu">
	                            <span><a href="' . route('powerpanel.services.edit', array('alias' => $value->id)) . '?tab=A" title="Edit">Edit</a></span>';

                        $title .= '<span><a href="' . $viewlink . '" target="_blank" title="' . $linkviewLable . '" >' . $linkviewLable . '</a></span></div></div>';
                    } else {
                        $title = '<div class="quick_edit"><a href = "javascript:;">' . $value->varTitle . '</a></div>';
                    }
                } else {
                    $title = '<div class="quick_edit"><a href = "' . route('powerpanel.services.edit', array('alias' => $value->id)) . '?tab=A">' . $value->varTitle . '</a> <div class="quick_edit_menu">
	                            <span><a href="' . route('powerpanel.services.edit', array('alias' => $value->id)) . '?tab=A" title="Edit">Edit</a></span>';

                    $title .= '<span><a href="' . $viewlink . '" target="_blank" title="' . $linkviewLable . '" >' . $linkviewLable . '</a></span></div></div>';
                }
            }
        }

        if (Config::get('Constant.DEFAULT_FAVORITE') == 'Y') {
            $Favorite_array = explode(",", $value->FavoriteID);
            if (in_array(auth()->user()->id, $Favorite_array)) {
                $Class = 'la la-star';
                $Favorite = '<a class="star_icon_div" href="javascript:;" onclick="GetFavorite(' . $value->id . ',\'N\',\'A\')"><i class="' . $Class . '"></i></a>';
            } else {
                $Class = 'la la-star-o';
                $Favorite = '<a class="star_icon_div" href="javascript:;" onclick="GetFavorite(' . $value->id . ',\'Y\',\'A\')"><i class="' . $Class . '"></i></a>';
            }
        } else {
            $Favorite = '';
        }
        $First_td = '<div class="star_box">' . $Favorite . '</div>';
        $servicescatarray = $value->servicescat->toArray();
        $servicescatdata = $servicescatarray['varTitle'];
        if (Auth::user()->can('services-reviewchanges')) {
            $update = "<a title=\"Click here to see all approval records.\" class=\"icon_title1\" style=\"margin-right: 5px;\" onclick=\"javascript:expandcollapsepanel(this ,'tasklisting" . $value->id . "', 'mainsingnimg" . $value->id . "'," . $value->id . ")\"><i id=\"mainsingnimg" . $value->id . "\" class=\"la la-plus-square\"></i></a>";
            $rollback = "<a title=\"Click here to see all approved records to rollback.\" class=\"icon_title2\" style=\"margin-right: 5px;\" onclick=\"javascript:expandcollapsepanel_rolback(this ,'tasklisting_rollback" . $value->id . "', 'mainsingnimg_rollback" . $value->id . "'," . $value->id . ")\"><i id=\"mainsingnimg_rollback" . $value->id . "\" class=\"la la-history\"></i></a>";
        } else {
            $update = '';
            $rollback = '';
        }
        if (Auth::user()->can('services-reviewchanges') && $value->chrAddStar == 'Y') {
            $star = 'addhiglight';
        } else {
            $star = '';
        }
        $imgIcon = '';
        $imgIcon .= '<div class="multi_image_thumb">';
        $imgIcon .= '<a href="' . resize_image::resize($value->fkIntImgId) . '" class="fancybox-buttons"  data-rel="fancybox-buttons">';
        $imgIcon .= '<img height="30" width="30" title="' . preg_replace('/[^A-Za-z0-9\-]/', '-', $value->varTitle) . '" src="' . resize_image::resize($value->fkIntImgId, 50, 50) . '"/>';
        $imgIcon .= '</a>';
        $imgIcon .= '</div>';
        $logurl = url('powerpanel/log?id=' . $value->id . '&mid=' . Config::get('Constant.MODULE.ID'));
        if ($actions == "") {
            $actions = "---";
        } else {
            $actions = $actions;
        }
        $log = '';
        if ($value->chrLock != 'Y') {
            $log .= $actions;
            if (Auth::user()->can('log-list')) {
                $log .= "<a title=\"Log History\" href=\"$logurl\"><i class=\"fa fa-clock-o\"></i></a>";
            }
        } else {
            if (auth()->user()->id != $value->LockUserID) {
                $lockedUserData = User::getRecordById($value->LockUserID, true);
                $lockedUserName = 'someone';
                if (!empty($lockedUserData)) {
                    $lockedUserName = $lockedUserData->name;
                }
                if ($this->currentUserRoleData->chrIsAdmin == 'Y') {
                    $log .= '<a class="star_lock" onclick="GetUnLockData(' . $value->id . ',' . auth()->user()->id . ',' . Config::get('Constant.MODULE.ID') . ',1)" title="This record has been locked by ' . $lockedUserName . ', Click here to unlock."><i class="fa fa-lock"></i></a>';
                } else {

                    $log .= '<a class="star_lock" title="This record has been locked by ' . $lockedUserName . '."><i class="fa fa-lock"></i></a>';
                }
            } else {
                $log .= '<a class="star_lock" onclick="GetUnLockData(' . $value->id . ',' . auth()->user()->id . ',' . Config::get('Constant.MODULE.ID') . ',1)" title="Click here to unlock."><i class="fa fa-lock"></i></a>';
            }
        }
        $statusdata = '';
        if (method_exists($this->MyLibrary, 'count_days')) {
            $days = MyLibrary::count_days($value->created_at);
            $days_modified = MyLibrary::count_days($value->updated_at);
        } else {
            $days = '';
            $days_modified = '';
        }
        if ($days_modified < Config::get('Constant.DEFAULT_DAYS') && $days < Config::get('Constant.DEFAULT_DAYS')) {
            if ($days < Config::get('Constant.DEFAULT_DAYS')) {
                $statusdata = '<img border="0" title="There was new action on this menu." alt="New" src="' . url('assets/images/new.png') . '">';
            }
        } else {
            if ($days_modified < Config::get('Constant.DEFAULT_DAYS')) {
                $statusdata = '<img border="0" title="There was edit/update action on this menu." alt="Updated" src="' . url('assets/images/updated.png') . '">';
            }
            if ($days < Config::get('Constant.DEFAULT_DAYS')) {
                $statusdata = '<img border="0" title="There was new action on this menu." alt="New" src="' . url('assets/images/new.png') . '">';
            }
        }
        $status = '';
        if ($value->chrDraft == 'D') {
            $status .= Config::get('Constant.DRAFT_LIST') . ' ';
        }
        if (Auth::user()->can('banners-reviewchanges')) {
            $log .= "<a title='Rollback to previous version'  onclick=\"rollbackToPreviousVersion('" . $value->id . "');\"  class=\"log-grid\"><i class=\"fa fa-history\"></i></a>";
        }
        $records = array(
            $First_td,
            '<div class="pages_title_div_row">' . $update . $rollback . $title . $status . $statusdata . '</div>',
            $servicescatdata,
            $imgIcon,
            $startDate,
            $endDate,
            $webHits,
            $log,
        );
        return $records;
    }

    public static function flushCache()
    {
        Cache::tags('Services')->flush();
        Cache::tags('ServiceCategory')->flush();
    }

    public function ApprovedData_Listing(Request $request) {
        $requestArr = Request::all();
        $request = (object) $requestArr;
        $id = Request::post('id');
        $main_id = Request::post('main_id');
        $approvalid = Request::post('id');
        $flag = Request::post('flag');
        $approvalData = Services::getOrderOfApproval($id);
        $message = Services::approved_data_Listing($request);
        $newCmsPageObj = Services::getRecordForLogById($main_id);
        $approval_obj = Services::getRecordForLogById($approvalid);
        if ($flag == 'R') {
            $restoredata = Config::get('Constant.ROLLBACK_RECORD');
        } else {
            if ($approval_obj->chrDraft == 'D') {
                $restoredata = Config::get('Constant.DRAFT_RECORD_APPROVED');
            } else {
                $restoredata = Config::get('Constant.RECORD_APPROVED');
            }
        }
        /* notification for user to record approved */
        $careers = Services::getRecordForLogById($id);
        if (method_exists($this->MyLibrary, 'userNotificationData')) {
            $userNotificationArr = MyLibrary::userNotificationData(Config::get('Constant.MODULE.ID'));
            $userNotificationArr['fkRecordId'] = $id;
            $userNotificationArr['txtNotification'] = 'Your request has been approved by ' . ucfirst(auth()->user()->name) . ' (' . ucfirst(Config::get('Constant.MODULE.NAME')) . ')';
            $userNotificationArr['fkIntUserId'] = Auth::user()->id;
            $userNotificationArr['chrNotificationType'] = 'A';
            $userNotificationArr['intOnlyForUserId'] = $careers->UserID;
            UserNotification::addRecord($userNotificationArr);
        }
        /* notification for user to record approved */
        $logArr = MyLibrary::logData($main_id, false, $restoredata);
        $logArr['varTitle'] = stripslashes($newCmsPageObj->varTitle);
        Log::recordLog($logArr);
        $where = [];
        $flowData = [];
        $flowData['dtYes'] = Config::get('Constant.SQLTIMESTAMP');
        $where['fkModuleId'] = Config::get('Constant.MODULE.ID');
        $where['fkRecordId'] = $main_id;
        $where['dtYes'] = 'null';
        WorkflowLog::updateRecord($flowData, $where);
        echo $message;
    }

    public function rollBackRecord(Request $request) {

        $message = 'Oops! Something went wrong';
        $requestArr = Request::all();
        $request = (object) $requestArr;

        $previousRecord = Services::getPreviousRecordByMainId($request->id);
        if (!empty($previousRecord)) {

            $main_id = $previousRecord->fkMainRecord;
            $request->id = $previousRecord->id;
            $request->main_id = $main_id;

            $message = Services::approved_data_Listing($request);

            $newServiceObj = Services::getRecordForLogById($main_id);
            $restoredata = Config::get('Constant.ROLLBACK_RECORD');

            /* notification for user to record approved */
            $Services = Services::getRecordForLogById($previousRecord->id);
            if (method_exists($this->MyLibrary, 'userNotificationData')) {
                $userNotificationArr = MyLibrary::userNotificationData(Config::get('Constant.MODULE.ID'));
                $userNotificationArr['fkRecordId'] = $previousRecord->id;
                $userNotificationArr['txtNotification'] = 'Your request has been approved by ' . ucfirst(auth()->user()->name) . ' (' . ucfirst(Config::get('Constant.MODULE.NAME')) . ')';
                $userNotificationArr['fkIntUserId'] = Auth::user()->id;
                $userNotificationArr['chrNotificationType'] = 'A';
                $userNotificationArr['intOnlyForUserId'] = $Services->UserID;
                UserNotification::addRecord($userNotificationArr);
            }
            /* notification for user to record approved */

            $logArr = MyLibrary::logData($main_id, false, $restoredata);
            $logArr['varTitle'] = stripslashes($newServiceObj->varTitle);
            Log::recordLog($logArr);
            $where = [];
            $flowData = [];
            $flowData['dtYes'] = Config::get('Constant.SQLTIMESTAMP');
            $where['fkModuleId'] = Config::get('Constant.MODULE.ID');
            $where['fkRecordId'] = $main_id;
            $where['dtYes'] = 'null';
            WorkflowLog::updateRecord($flowData, $where);
        }
        echo $message;
    }

    public function getChildData() {
        $childHtml = "";
        $Cmspage_childData = "";
        $Cmspage_childData = Services::getChildGrid();
        $childHtml .= "<div class=\"producttbl\" style=\"\">";
        $childHtml .= "<table class=\"new_table_desing table table-striped table-bordered table-hover table-checkable dataTable\" id=\"email_log_datatable_ajax\">
																<tr role=\"row\">
																		<th class=\"text-center\"></th>
																		<th class=\"text-center\">Title</th>
																		<th class=\"text-center\">Date Submitted</th>
																		<th class=\"text-center\">User</th>
																		<th class=\"text-center\">Preview</th>
																		<th class=\"text-center\">Edit</th>
																		<th class=\"text-center\">Status</th>";
        $childHtml .= "</tr>";
        if (count($Cmspage_childData) > 0) {
            foreach ($Cmspage_childData as $child_row) {
                $childHtml .= "<tr role=\"row\">";
                if ($child_row->chrApproved == 'N') {
                    $childHtml .= "<td><span class='mob_show_title'>&nbsp</span><input type=\"checkbox\" name=\"delete\" class=\"chkDelete\" value=\"$child_row->id\"></td>";
                } else {
                    $childHtml .= "<td><span class='mob_show_title'>&nbsp</span><div class=\"checker\"><a href=\"javascript:;\" data-toggle=\"tooltip\" data-placement=\"right\" title=\"This is approved record, so can't be deleted.\"><i style=\"color:red\" class=\"fa fa-exclamation-triangle\"></i></a></div></td>";
                }
                $childHtml .= '<td class="text-center"><span class="mob_show_title">Title: </span>' . $child_row->varTitle . '</td>';
                $childHtml .= "<td class=\"text-center\"><span class='mob_show_title'>Date Submitted: </span>" . date('M d Y h:i A', strtotime($child_row->created_at)) . "</td>";
                $childHtml .= "<td class=\"text-center\"><span class='mob_show_title'>User: </span>" . CommonModel::getUserName($child_row->UserID) . "</td>";
                $categoryRecordAlias = MyLibrary::getRecordAliasByModuleNameRecordId("service-category", $child_row->intFKCategory);
                $previewlink = url('/previewpage?url=' . MyLibrary::getFrontUri('services')['uri'] . '/' . $child_row->id . '/preview/detail');
                $childHtml .= "<td class=\"text-center\"><span class='mob_show_title'>Preview: </span><a class='icon_round' href=" . $previewlink . " target='_blank'><i class=\"fa fa-desktop\"></i></a></td>";
                if ($child_row->chrApproved == 'N') {
                    $childHtml .= "<td class=\"text-center\"><span class='mob_show_title'>Edit: </span><a class='icon_round' title='" . trans("services::template.common.edit") . "' href='" . route('powerpanel.services.edit', array('alias' => $child_row->id)) . "'>
							<i class='fa fa-pencil'></i></a></td>";
                } else {
                    $childHtml .= "<td class=\"text-center\"><span class='mob_show_title'>Edit: </span>-</td>";
                }
                if ($child_row->chrApproved == 'N') {
                    $childHtml .= "<td class=\"text-center\"><span class='mob_show_title'>Status: </span><a class=\"approve_icon_btn\" title='" . trans("services::template.common.comments") . "'   href=\"javascript:;\" onclick=\"loadModelpopup('" . $child_row->id . "','" . $child_row->UserID . "','" . Config::get('Constant.MODULE.MODEL_NAME') . "','" . $child_row->fkMainRecord . "')\"><i class=\"fa fa-comments\"></i> <span>Comment</span></a>    <a  onclick=\"update_mainrecord('" . $child_row->id . "','" . $child_row->fkMainRecord . "','" . $child_row->UserID . "','A');\" title='" . trans("services::template.common.clickapprove") . "' class=\"approve_icon_btn\" href=\"javascript:;\"><i class=\"fa fa-check-square-o\"></i> <span>Approve</span></a></td>";
                } else {
                    $childHtml .= "<td class=\"text-center\"><span class='mob_show_title'>Status: </span><span class='mob_show_overflow'><i class=\"la la-check-circle\" style=\"font-size:30px;\"></i><span style=\"display:block\"><strong>Approved On: </strong>" . date('M d Y h:i A', strtotime($child_row->dtApprovedDateTime)) . "</span><span style=\"display:block\"><strong>Approved By: </strong>" . CommonModel::getUserName($child_row->intApprovedBy) . "</span></span></td>";
                }
                $childHtml .= "</tr>";
            }
        } else {
            $childHtml .= "<tr><td colspan='7'>No Records</td></tr>";
        }
        $childHtml .= "</tr></td></tr>";
        $childHtml .= "</tr>
                        </table>";
        echo $childHtml;
        exit;
    }

    public function get_list_New() {
        /* Start code for sorting */
        $filterArr = [];
        $records = [];
        $records["data"] = array();
        $filterArr['orderColumnNo'] = (!empty(Request::input('order')[0]['column']) ? Request::input('order')[0]['column'] : '');
        $filterArr['orderByFieldName'] = (!empty(Request::input('columns')[$filterArr['orderColumnNo']]['name']) ? Request::input('columns')[$filterArr['orderColumnNo']]['name'] : '');
        $filterArr['orderTypeAscOrDesc'] = (!empty(Request::input('order')[0]['dir']) ? Request::input('order')[0]['dir'] : '');
        $filterArr['customFilterIdentity'] = !empty(Request::get('customFilterIdentity')) ? Request::get('customFilterIdentity') : '';
        $filterArr['statusFilter'] = !empty(Request::input('statusValue')) ? Request::input('statusValue') : '';
        $filterArr['catFilter'] = !empty(Request::input('catValue')) ? Request::input('catValue') : '';
        $filterArr['searchFilter'] = !empty(Request::input('searchValue')) ? Request::input('searchValue') : '';
        $filterArr['iDisplayLength'] = intval(Request::input('length'));
        $filterArr['iDisplayStart'] = intval(Request::input('start'));
        $sEcho = intval(Request::input('draw'));
        $arrResults = Services::getRecordList_tab1($filterArr);

        $iTotalRecords = Services::getRecordCountListApprovalTab($filterArr, true);
        $end = $filterArr['iDisplayStart'] + $filterArr['iDisplayLength'];
        $end = $end > $iTotalRecords ? $iTotalRecords : $end;
        if (count($arrResults) > 0 && !empty($arrResults)) {
            foreach ($arrResults as $key => $value) {
                $records["data"][] = $this->tableData_tab1($value);
            }
        }
        $NewRecordsCount = Services::getNewRecordsCount();
        $records["newRecordCount"] = $NewRecordsCount;
        $records["customActionStatus"] = "OK";
        $records["draw"] = $sEcho;
        $records["recordsTotal"] = $iTotalRecords;
        $records["recordsFiltered"] = $iTotalRecords;
        echo json_encode($records);
        exit;
    }

    public function getChildData_rollback() {
        $child_rollbackHtml = "";
        $Cmspage_rollbackchildData = "";
        $Cmspage_rollbackchildData = Services::getChildrollbackGrid();
        $child_rollbackHtml .= "<div class=\"producttbl producttb2\" style=\"\">";
        $child_rollbackHtml .= "<table class=\"new_table_desing table table-striped table-bordered table-hover table-checkable dataTable\" id=\"email_log_datatable_ajax\">
							    <tr role=\"row\">																																																																<th class=\"text-center\">Title</th>
                                    <th class=\"text-center\">Date</th>
                                    <th class=\"text-center\">User</th>
                                    <th class=\"text-center\">Preview</th>
                                    <th class=\"text-center\">Status</th>";
        $child_rollbackHtml .= "         </tr>";
        if (count($Cmspage_rollbackchildData) > 0) {
            foreach ($Cmspage_rollbackchildData as $child_rollbacrow) {
                $child_rollbackHtml .= "<tr role=\"row\">";
                $child_rollbackHtml .= '<td class="text-center"><span class="mob_show_title">Title: </span>' . $child_rollbacrow->varTitle . '</td>';
                $child_rollbackHtml .= "<td class=\"text-center\"><span class='mob_show_title'>Date: </span>" . date('M d Y h:i A', strtotime($child_rollbacrow->created_at)) . "</td>";
                $child_rollbackHtml .= "<td class=\"text-center\"><span class='mob_show_title'>User: </span>" . CommonModel::getUserName($child_rollbacrow->UserID) . "</td>";
                $categoryRecordAlias = MyLibrary::getRecordAliasByModuleNameRecordId("service-category", $child_rollbacrow->intFKCategory);
                $previewlink = url('/previewpage?url=' . MyLibrary::getFrontUri('services')['uri'] . '/' . $child_rollbacrow->id . '/preview/detail');
                $child_rollbackHtml .= "<td class=\"text-center\"><span class='mob_show_title'>Preview: </span><a class='icon_round' href=" . $previewlink . " target='_blank'><i class=\"fa fa-desktop\"></i></a></td>";
                if ($child_rollbacrow->chrApproved == 'Y') {
                    $child_rollbackHtml .= "<td class=\"text-center\"><span class='mob_show_title'>Status: </span><i class=\"la la-check-circle\" style=\"color: #1080F2;font-size:30px;\"></i></td>";
                } else {

                    // $child_rollbackHtml .= "<td class=\"text-center\">
                    //<span class='mob_show_title'>Status: </span><a onclick=\"update_mainrecord('" . $child_rollbacrow->id . "','" . $child_rollbacrow->fkMainRecord . "','" . $child_rollbacrow->UserID . "','R');\"  class=\"approve_icon_btn\">
                    //                         <i class=\"fa fa-history\"></i>  <span>RollBack</span>
                    //                     </a></td>";

                    $child_rollbackHtml .= "<td class=\"text-center\"><span class=\"glyphicon glyphicon-minus\"></span></td>";
                }

                $child_rollbackHtml .= "</tr>";
            }
        } else {
            $child_rollbackHtml .= "<tr><td colspan='6'>No Records</td></tr>";
        }
        echo $child_rollbackHtml;
        exit;
    }

    public function get_buider_list() {
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
        $arrResults = Services::getBuilderRecordList($filterArr);
        $found = $arrResults->toArray();
        if (!empty($found)) {
            foreach ($arrResults as $key => $value) {
                $rows .= $this->tableDataBuilder($value, false, $filterArr['selected']);
            }
        } else {
            $rows .= '<tr id="not-found"><td colspan="4" align="center">No records found.</td></tr>';
        }
        $iTotalRecords = CommonModel::getTotalRecordCount('Powerpanel\Services\Models\Services', true, true);
        $records["data"] = $rows;
        $records["found"] = count($found);
        $records["recordsTotal"] = $iTotalRecords;
        return json_encode($records);
    }

    public function insertApprovedRecord($updateModuleFields, $data, $id) {

        $whereConditions = ['id' => $data['fkMainRecord']];
        $updateModuleFields['chrAddStar'] = 'N';
        $updateModuleFields['chrPublish'] = trim($data['chrMenuDisplay']);
        $updateModuleFields['UserID'] = auth()->user()->id;
        $update = CommonModel::updateRecords($whereConditions, $updateModuleFields, false, 'Powerpanel\Services\Models\Services');
        $whereConditions_ApproveN = ['fkMainRecord' => $data['fkMainRecord']];
        $updateToApproveN = [
            'chrApproved' => 'N',
            'chrLetest' => 'N',
            'intApprovedBy' => '0',
        ];
        CommonModel::updateRecords($whereConditions_ApproveN, $updateToApproveN, false, 'Powerpanel\Services\Models\Services');
        $whereConditionsApprove = ['id' => $id, 'chrMain' => 'N'];
        $updateToApprove = [
            'chrApproved' => 'Y',
            'chrRollBack' => 'Y',
            'intApprovedBy' => auth()->user()->id,
        ];
        CommonModel::updateRecords($whereConditionsApprove, $updateToApprove, false, 'Powerpanel\Services\Models\Services');
        if ($data['chrMenuDisplay'] == 'D') {
            $addlog = Config::get('Constant.DRAFT_RECORD_APPROVED');
        } else {
            $addlog = Config::get('Constant.RECORD_APPROVED');
        }
        $newCmsPageObj = Services::getRecordForLogById($id);
        $logArr = MyLibrary::logData($id, false, $addlog);
        $logArr['varTitle'] = stripslashes($newCmsPageObj->varTitle);
        Log::recordLog($logArr);
        /* notification for user to record approved */
        $careers = Services::getRecordForLogById($id);
        if (method_exists($this->MyLibrary, 'userNotificationData')) {
            $userNotificationArr = MyLibrary::userNotificationData(Config::get('Constant.MODULE.ID'));
            $userNotificationArr['fkRecordId'] = $id;
            $userNotificationArr['txtNotification'] = 'Your request has been approved by ' . ucfirst(auth()->user()->name) . ' (' . ucfirst(Config::get('Constant.MODULE.NAME')) . ')';
            $userNotificationArr['fkIntUserId'] = Auth::user()->id;
            $userNotificationArr['chrNotificationType'] = 'A';
            $userNotificationArr['intOnlyForUserId'] = $careers->UserID;
            UserNotification::addRecord($userNotificationArr);
            /* notification for user to record approved */
        }
        if ($update) {
            if ($id > 0 && !empty($id)) {
                $where = [];
                $flowData = [];
                $flowData['dtYes'] = Config::get('Constant.SQLTIMESTAMP');
                $where['fkModuleId'] = Config::get('Constant.MODULE.ID');
                $where['fkRecordId'] = (isset($data['fkMainRecord']) && (int) $data['fkMainRecord'] != 0) ? $data['fkMainRecord'] : $id;
                $where['dtYes'] = 'null';
                WorkflowLog::updateRecord($flowData, $where);
                self::flushCache();
                $actionMessage = trans('blogs::template.blogsModule.updateMessage');
            }
        }
    }

    public function insertApprovalRecord($moduleObj, $data, $servicesArr) {
        $response = false;
        $servicesArr['chrMain'] = 'N';
        $servicesArr['chrLetest'] = 'Y';
        $servicesArr['fkMainRecord'] = $moduleObj->id;
        $servicesArr['varTitle'] = stripslashes(trim($data['title']));
        // $servicesArr['intFKCategory'] = $data['category_id'];
        $servicesArr['intAliasId'] = MyLibrary::insertAlias($data['alias'], false, 'N');
        $servicesArr['varMetaTitle'] = stripslashes(trim($data['varMetaTitle']));
        if (Config::get('Constant.CHRSearchRank') == 'Y') {
            // $servicesArr['intSearchRank'] = $data['search_rank'];
        }
        $servicesArr['varMetaDescription'] = stripslashes(trim($data['varMetaDescription']));
        // $servicesArr['varShortDescription'] = stripslashes(trim($data['short_description']));
        if ($data['chrMenuDisplay'] == 'D') {
            $servicesArr['chrDraft'] = 'D';
            $servicesArr['chrPublish'] = 'N';
        } else {
            // $servicesArr['chrDraft'] = 'N';
            $servicesArr['chrPublish'] = $data['chrMenuDisplay'];
        }
        if (isset($data['chrPageActive']) && $data['chrPageActive'] != '') {
            $servicesArr['chrPageActive'] = $data['chrPageActive'];
        }
        if (isset($data['chrPageActive']) && $data['chrPageActive'] == 'PP') {
            // $servicesArr['varPassword'] = $data['new_password'];
        } else {
            // $servicesArr['varPassword'] = '';
        }
        if (Config::get('Constant.CHRSearchRank') == 'Y') {
            // $servicesArr['intSearchRank'] = $data['search_rank'];
        }
        if ($data['chrMenuDisplay'] == 'D') {
            $addlog = Config::get('Constant.DRAFT_SENT_FOR_APPROVAL');
        } else {
            $addlog = Config::get('Constant.SENT_FOR_APPROVAL');
        }
        $servicesArr['created_at'] = Carbon::now();
        $servicesArr['UserID'] = auth()->user()->id;
        $servicesArr['fkIntImgId'] = !empty($data['img_id']) ? $data['img_id'] : null;
        $servicesArrID = CommonModel::addRecord($servicesArr, 'Powerpanel\Services\Models\Services');
        if (!empty($servicesArrID)) {
            $id = $servicesArrID;
            WorkflowLog::addRecord([
                'fkModuleId' => Config::get('Constant.MODULE.ID'),
                'fkRecordId' => $moduleObj->id,
                'charApproval' => 'Y',
            ]);
            if (method_exists($this->MyLibrary, 'userNotificationData')) {
                $userNotificationArr = MyLibrary::userNotificationData(Config::get('Constant.MODULE.ID'));
                $userNotificationArr['fkRecordId'] = $moduleObj->id;
                $userNotificationArr['txtNotification'] = 'New approval request from ' . ucfirst(auth()->user()->name) . ' (Powerpanel\Services\Models\\' . ucfirst(Config::get('Constant.MODULE.NAME')) . ')';
                $userNotificationArr['fkIntUserId'] = Auth::user()->id;
                $userNotificationArr['chrNotificationType'] = 'A';
                UserNotification::addRecord($userNotificationArr);
            }
            $newServiceObj = Services::getRecordForLogById($id);
            $logArr = MyLibrary::logData($id, false, $addlog);
            $logArr['varTitle'] = $newServiceObj->varTitle;
            Log::recordLog($logArr);
            if (Auth::user()->can('recent-updates-list')) {
                $notificationArr = MyLibrary::notificationData($id, $newServiceObj);
                RecentUpdates::setNotification($notificationArr);
            }
            $response = $newServiceObj;
            self::flushCache();
            $actionMessage = trans('blogs::template.blogsModule.addMessage');
        }

        $whereConditionsAddstar = ['id' => $moduleObj->id];
        $updateAddStar = [
            // 'chrAddStar' => 'Y',
        ];
        
        CommonModel::updateRecords($whereConditionsAddstar, $updateAddStar, false, 'Powerpanel\Services\Models\Services');
        return $response;
    }

    public function addPreview() {
        $data = Request::post();
        $id = $data['previewId'];
        if (is_numeric($id)) { #Edit post Handler=======
            $services = Services::getRecordForLogById($id);
            $updateServicesFields = [];
            if (Config::get('Constant.DEFAULT_VISUAL') == 'Y') {
                if ($data['section'] != '[]') {
                    $vsection = $data['section'];
                } else {
                    $vsection = '';
                }
            } else {
                $vsection = $data['description'];
            }
            $updateServicesFields['varTitle'] = stripslashes(trim($data['title']));
            $updateServicesFields['intFKCategory'] = trim($data['category_id']);
            $updateServicesFields['varShortDescription'] = stripslashes(trim($data['short_description']));
            $updateServicesFields['txtDescription'] = $vsection;
            $updateServicesFields['varMetaTitle'] = stripslashes(trim($data['varMetaTitle']));
            $updateServicesFields['varMetaDescription'] = stripslashes(trim($data['varMetaDescription']));
            if (Config::get('Constant.CHRSearchRank') == 'Y') {
                $updateServicesFields['intSearchRank'] = $data['search_rank'];
            }
            $updateServicesFields['chrPublish'] = $data['chrMenuDisplay'];
            $updateServicesFields['dtDateTime'] = !empty($data['start_date_time']) ? date('Y-m-d H:i:s', strtotime($data['start_date_time'])) : date('Y-m-d H:i:s');
            // $updateServicesFields['dtEndDateTime'] = !empty($data['end_date_time']) ? date('Y-m-d H:i:s', strtotime($data['end_date_time'])) : null;
            $updateServicesFields['fkIntImgId'] = !empty($data['img_id']) ? $data['img_id'] : null;
            $updateServicesFields['UserID'] = auth()->user()->id;
            $updateServicesFields['chrIsPreview'] = 'Y';
            $whereConditions = ['id' => $id];
            if ($data['oldAlias'] != $data['alias']) {
                Alias::updateAlias($data['oldAlias'], $data['alias']);
            }
            $update = CommonModel::updateRecords($whereConditions, $updateServicesFields, false, 'Powerpanel\Services\Models\Services');
        } else {
            $servicesArr['dtDateTime'] = !empty($data['start_date_time']) ? date('Y-m-d H:i:s', strtotime($data['start_date_time'])) : date('Y-m-d H:i:s');
            // $servicesArr['dtEndDateTime'] = !empty($data['end_date_time']) ? date('Y-m-d H:i:s', strtotime($data['end_date_time'])) : null;
            $servicesArr['chrIsPreview'] = 'Y';
            $id = $this->insertNewRecord($data, $servicesArr, 'Y')->id;
        }
        return json_encode(array('status' => $id, 'alias' => $data['alias'], 'message' => trans('template.pageModule.pageUpdate')));
    }

    public function newrecordHistory($data = false, $newdata = false) {
        $returnHtml = '';
        $startDate = date('' . Config::get('Constant.DEFAULT_DATE_FORMAT') . ' ' . Config::get('Constant.DEFAULT_TIME_FORMAT') . '', strtotime($newdata->dtDateTime));
        // $endDate = !empty($newdata->dtEndDateTime) ? date('' . Config::get('Constant.DEFAULT_DATE_FORMAT') . ' ' . Config::get('Constant.DEFAULT_TIME_FORMAT') . '', strtotime($newdata->dtEndDateTime)) : 'No Expiry';
        $aaa = 
        $ServiceCategory = ServiceCategory::getCatData(unserialize($newdata->txtCategories));
        // dd(unserialize($newdata->txtCategories));
        // dd($ServiceCategory->varTitle);
        if ($data->varTitle != $newdata->varTitle) {
            $titlecolor = 'style="background-color:#f5efb7"';
        } else {
            $titlecolor = '';
        }
        if ($data->intFKCategory != $newdata->intFKCategory) {
            $catcolor = 'style="background-color:#f5efb7"';
        } else {
            $catcolor = '';
        }
        if ($data->fkIntImgId != $newdata->fkIntImgId) {
            $imgcolor = 'style="background-color:#f5efb7"';
        } else {
            $imgcolor = '';
        }
        if ($data->dtDateTime != $newdata->dtDateTime) {
            $sdatecolor = 'style="background-color:#f5efb7"';
        } else {
            $sdatecolor = '';
        }
        if ($data->dtEndDateTime != $newdata->dtEndDateTime) {
            $edatecolor = 'style="background-color:#f5efb7"';
        } else {
            $edatecolor = '';
        }
        if ($data->chrPublish != $newdata->chrPublish) {
            $Publishcolor = 'style="background-color:#f5efb7"';
        } else {
            $Publishcolor = '';
        }
        if ($data->varShortDescription != $newdata->varShortDescription) {
            $ShortDescriptioncolor = 'style="background-color:#f5efb7"';
        } else {
            $ShortDescriptioncolor = '';
        }
        if ($data->txtDescription != $newdata->txtDescription) {
            $desccolor = 'style="background-color:#f5efb7"';
        } else {
            $desccolor = '';
        }
        if ($data->varMetaTitle != $newdata->varMetaTitle) {
            $metatitlecolor = 'style="background-color:#f5efb7"';
        } else {
            $metatitlecolor = '';
        }
        if ($data->varMetaDescription != $newdata->varMetaDescription) {
            $metadesccolor = 'style="background-color:#f5efb7"';
        } else {
            $metadesccolor = '';
        }

        if (isset($newdata->txtDescription) && $newdata->txtDescription != '') {

            $desc = FrontPageContent_Shield::renderBuilder($newdata->txtDescription);
            if (isset($desc['response']) && !empty($desc['response'])) {
                $desc = $desc['response'];
            } else {
                $desc = '---';
            }
        } else {
            $desc = '---';
        }

        $returnHtml .= '<table class="new_table_desing table table-striped table-bordered table-hover">
                <thead>
                    <tr>
                        <th align="center">' . trans('services::template.common.title') . '</th>
                        <th align="center">Category</th>
                                                                                                <th align="center">Short Description</th>
                                <th align="center">Description</th>
                                                                                                <th align="center">' . trans("services::template.common.image") . '</th>
                        <th align="center">Start Date</th>
                        <th align="center">End Date</th>
                                                                                                <th align="center">Meta Title</th>
                                                                                                                                <th align="center">Meta Description</th>
                                                                                                <th align="center">' . trans("services::template.common.publish") . '</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td align="center" ' . $titlecolor . '>' . stripslashes($newdata->varTitle) . '</td>
                        <td align="center" ' . $catcolor . '>' . $ServiceCategory->varTitle . '</td><td align="center" ' . $ShortDescriptioncolor . '>' . stripslashes($newdata->varShortDescription) . '</td>
                        <td align="center" ' . $desccolor . '>' . $desc . '</td>';
        if ($data->fkIntImgId > 0) {
            $returnHtml .= '<td align="center" ' . $imgcolor . '>' . '<img height="50" width="50" src="' . resize_image::resize($newdata->fkIntImgId) . '" />' . '</td>';
        } else {
            $returnHtml .= '<td align="center">-</td>';
        }
        $returnHtml .= '<td align="center" ' . $sdatecolor . '>' . $startDate . '</td>
                        <td align="center" ' . $edatecolor . '>' . $endDate . '</td>
                                                                                                        <td align="center" ' . $metatitlecolor . '>' . stripslashes($newdata->varMetaTitle) . '</td>
                                                                            <td align="center" ' . $metadesccolor . '>' . stripslashes($newdata->varMetaDescription) . '</td>
                        <td align="center" ' . $Publishcolor . '>' . $newdata->chrPublish . '</td>
                    </tr>
                </tbody>
            </table>';
        return $returnHtml;
    }
}
