<?php

/**
 * The MenuController class handels dynamic menu configuration
 * configuration  process.
 * @package   Netquick powerpanel
 * @license   http://www.opensource.org/licenses/BSD-3-Clause
 * @version   1.1
 * @since     04-08-2017
 * @author    NetQuick
 */

namespace Powerpanel\Menu\Controllers\powerpanel;


use App\Alias;
use Powerpanel\CmsPage\Models\CmsPage;
use App\CommonModel;
use App\Helpers\MyLibrary;
use App\Http\Controllers\PowerpanelController;
use App\Log;
use Powerpanel\Menu\Models\Menu;
use Powerpanel\Menu\Models\MenuType;
use App\Modules;
use Cache;
use Config;
use DB;
use Request;
use Validator;
use Auth;

class MenuController extends PowerpanelController
{
    public function __construct()
    {
        parent::__construct();
        if (isset($_COOKIE['locale'])) {
            app()->setLocale($_COOKIE['locale']);
        }
    }

    /**
     * This method handels loading process of dynamic menu
     * @return  View
     * @since   04-08-2017
     * @author  NetQuick
     */
    public function index()
    {
        $pageId = '';
        if (Request::has('pageId')) {
            $pageId = Request::get('pageId');
        }

        $menuType = MenuType::getList();
        $ignoreModules = ['contact-us', 'restaurant-reservations', 'sponsor', 'appointment-lead', 'gallery', 'advertise', 'banner', 'client', 'contact-info', 'faq', 'testimonial', 'privacy-removal-leads', 'data-removal-lead', 'get-a-estimate', 'boat-category', 'brand', 'service-inquiry', 'boat-inquiry'];

        $moduleList = Modules::getRecordAvaliModules($ignoreModules);
        $module = Modules::getModule('pages');
        //$cmsPages = CmsPage::getRecordsForMenu($module->id);
        $menu_array = array();

        if (isset($menuType[0])) {
            $menu_array = $this->buildMenu($menuType[0]->id);
        }

        $menu = $this->make_menu(0, "", $menu_array);
        $this->breadcrumb['title'] = trans('template.menuModule.managemenu');
        $client_role  = Auth::user()->hasRole('client_roles');

        return view('menu::powerpanel.index', ['menu' => $menu, 'moduleList' => $moduleList, 'cmsPageModuleID' => $module->id, 'menuTypes' => $menuType, 'breadcrumb' => $this->breadcrumb, 'pageId' => $pageId, 'client_role' => $client_role]);
    }

    /**
     * This method handels loading process of generating array from menu data
     * @return  Menu array
     * @since   04-08-2017
     * @author  NetQuick
     */
    public function buildMenu($position = null, $check_front = '')
    {
        if ($position == null) {
            $position = 1;
        }
        $response = false;
        $menu_array = array();
        $result = Menu::getFrontList($check_front);

        if (!empty($result[$position])) {
            foreach ($result[$position] as $menuItem) {
                $menu_array['items'][$menuItem->id] = array(
                    'id' => $menuItem->id,
                    'pid' => $menuItem->intParentMenuId,
                    'title' => $menuItem->varTitle,
                    'url' => $menuItem->txtPageUrl,
                    'active' => $menuItem->chrActive,
                    'publish' => $menuItem->chrPublish,
                    'position' => $menuItem->intPosition,
                    'menu_type' => $menuItem->menuType,
                    'mega_menu' => $menuItem->chrMegaMenu,
                    'chrInMobile' => $menuItem->chrInMobile,
                    'chrInWeb' => $menuItem->chrInWeb,
                    'chr_publish' => $menuItem->chrPublish,
                );
                $menu_array['parents'][$menuItem->intParentMenuId][] = $menuItem->id;
            }
        }
        $response = $menu_array;
        return $response;
    }
    /**
     * This method handels loading process of generating html menu from array data
     * @return  Html menu
     * @param     parentId, parentUrl, menu_array
     * @since   04-08-2017
     * @author  NetQuick
     */
    public function make_menu($parentId = false, $parentUrl = false, $menu_array = false)
    {
        $parent_order = 1;
        $response = false;
        $active = false;
        $html = '';
        if (isset($menu_array['parents'][$parentId])) {
            $homeCnt = 0;
            $child_order = 1;
            $html = '<ol class="dd-list menu_list_set">';
            foreach ($menu_array['parents'][$parentId] as $itemId) {
                $child = array_column($menu_array['items'], 'pid');
                $hasChild = (in_array($itemId, $child)) ? true : false;
                if ($hasChild) {
                    $order = $parent_order;
                } else {
                    $order = $child_order;
                }
                $active = $menu_array['items'][$itemId]['active'];
                $mega_menu = $menu_array['items'][$itemId]['mega_menu'];
                $position = $menu_array['items'][$itemId]['position'];
                $cur_url = $menu_array['items'][$itemId]['url'];
                $menuType = $menu_array['items'][$itemId]['menu_type'];

                $html .= '<li class="dd-item" data-order="' . $order . '" data-id="' . $menu_array['items'][$itemId]['id'] . '">';
                $html .= '<div class="dd-handle">
					<a href="' . $cur_url . '">' . $menu_array['items'][$itemId]['title'] . '</a>
					</div>
				<div class="actions">';

                #When menu has multiple home links
                $client_role  = Auth::user()->hasRole('client_roles');
                if (!$client_role) {
                    if ($homeCnt > 0 && $position == 1 && (strtolower($menu_array['items'][$itemId]['title']) == 'home')) {
                        $html .= '<a href="javascript:;" data-toggle="tooltip" data-placement="top" title="Delete" data-id="' . $menu_array['items'][$itemId]['id'] . '" class="deleteItem delect_icons_bg">
                        <i class="fa fa-trash"></i></a>';
                    }

                    if (!$hasChild && (strtolower($menu_array['items'][$itemId]['title']) != 'home')) {
                        $html .= '<a href="javascript:;" data-toggle="tooltip" data-placement="top" title="Delete" data-id="' . $menu_array['items'][$itemId]['id'] . '" class="deleteItem delect_icons_bg">
                        <i class="fa fa-trash"></i></a>';
                    } else {
                        $homeCnt++;
                    }

                    #When not header menu
                    if (!$hasChild && $position != 1 && (strtolower($menu_array['items'][$itemId]['title']) == 'home')) {
                        $html .= '<a href="javascript:;" data-toggle="tooltip" data-placement="top" title="Delete" data-id="' . $menu_array['items'][$itemId]['id'] . '" class="deleteItem delect_icons_bg">
                        <i class="fa fa-trash"></i></a>';
                    }
                }

                $html .= '<a data-toggle="tooltip" data-placement="top" title="Edit" data-id="' . $menu_array['items'][$itemId]['id'] . '" class="editItem edit_icon_bg">
				<i class="fa fa-pencil"></i></a>';
                if (strtolower($menu_array['items'][$itemId]['title']) != 'home') {
                    $html .= '<span class="md-checkbox menu_active">
					<input style="opacity:0" id="checkbox' . $menu_array['items'][$itemId]['id'] . '" ' . ($menuType->chrPublish == 'N' ? 'disabled=true' : '') . ' class="activeItem" type="checkbox" ' . ($active == 'Y' || (strtolower($menu_array['items'][$itemId]['title']) == 'home') ? 'checked' : '') . ' value="' . $menu_array['items'][$itemId]['id'] . '"/>
						<label title="' . ($active == 'Y' || (strtolower($menu_array['items'][$itemId]['title']) == 'home') ? 'Deactive Menu' : 'Activate Menu') . '" for="checkbox' . $menu_array['items'][$itemId]['id'] . '">
							<span></span>
							<span class="check tooltips" data-toggle="tooltip" data-placement="top" data-original-title="Deactive Menu & unpublish"></span>
							<span class="box tooltips" data-toggle="tooltip" data-placement="top" data-original-title="Active Menu & publish"></span>
						</label></span>';
                }
                if ($position != 1 && (strtolower($menu_array['items'][$itemId]['title']) == 'home')) {
                    $html .= '<span class="md-checkbox menu_active"><input style="opacity:0" id="checkbox' . $menu_array['items'][$itemId]['id'] . '" ' . ($menuType->chrPublish == 'N' ? 'disabled=true' : '') . ' class="activeItem md-check" type="checkbox" ' . ($active == 'Y' || (strtolower($menu_array['items'][$itemId]['title']) == 'home') ? 'checked' : '') . ' value="' . $menu_array['items'][$itemId]['id'] . '"/>
					<label title="' . ($active == 'Y' || (strtolower($menu_array['items'][$itemId]['title']) == 'home') ? 'Deactive Menu' : 'Activate Menu') . '" for="checkbox' . $menu_array['items'][$itemId]['id'] . '">
							<span></span>
							<span class="check tooltips" data-toggle="tooltip" data-placement="top" data-original-title="Deactive Menu"></span>
							<span class="box tooltips" data-toggle="tooltip" data-placement="top" data-original-title="Activate Menu"></span>
						</label></span>';
                }
                // $html.='<span class="md-checkbox mobile_view menu_active">
                // <input style="opacity:0" id="checkbox'.$menu_array['items'][$itemId]['id'].'-inmobile" '.($menuType->chrPublish=='N'?'disabled=true':'').' class="inMobileMenu" type="checkbox" '.($menu_array['items'][$itemId]['chrInMobile'] == 'Y'?'checked':'').' value="'.$menu_array['items'][$itemId]['id'].'"/>
                // <label title="'.($menu_array['items'][$itemId]['chrInMobile']=='Y' ?'Remove from mobile':'Add to mobile').'" for="checkbox'.$menu_array['items'][$itemId]['id'].'-inmobile">
                //         <span></span>
                //         <span class="check tooltips" data-toggle="tooltip" data-placement="top" data-original-title="Remove from mobile"></span>
                //         <span class="box tooltips" data-toggle="tooltip" data-placement="top" data-original-title="Add to mobile"></span>
                //     </label></span>';
                // $html.='<span class="md-checkbox decktop_view menu_active">
                // <input style="opacity:0" id="checkbox'.$menu_array['items'][$itemId]['id'].'-inweb" '.($menuType->chrPublish=='N'?'disabled=true':'').' class="inWebMenu" type="checkbox" '.($menu_array['items'][$itemId]['chrInWeb'] == 'Y'?'checked':'').' value="'.$menu_array['items'][$itemId]['id'].'"/>
                //         <label title="'.($menu_array['items'][$itemId]['chrInWeb']=='Y' ?'Remove from desktop':'Add to desktop').'" for="checkbox'.$menu_array['items'][$itemId]['id'].'-inweb">
                //         <span></span>
                //         <span class="check tooltips" data-toggle="tooltip" data-placement="top" data-original-title="Remove from desktop"></span>
                //         <span class="box tooltips" data-toggle="tooltip" data-placement="top" data-original-title="Add to desktop"></span>
                //     </label></span>';
                if ($hasChild && $parentId == 0 && $position == 1) {
                    $img = url('assets\images\mega-menu-preview.png'); //resize_image::resize(0);
                    //'<input class="megaMenu" type="checkbox"'.($mega_menu=='Y'?'checked':'').' value="'.$menu_array['items'][$itemId]['id'].'"/>';
                    $html .= '<span class="megamenu_info">
					<span class="md-checkbox megamenu_view menu_active">
							<input style="opacity:0" id="checkbox' . $menu_array['items'][$itemId]['id'] . '-megaMenu" ' . ($menuType->chrPublish == 'N' ? 'disabled=true' : '') . ' class="megaMenu" type="checkbox" ' . ($menu_array['items'][$itemId]['mega_menu'] == 'Y' ? 'checked' : '') . ' value="' . $menu_array['items'][$itemId]['id'] . '"/>
								<label title="' . ($menu_array['items'][$itemId]['mega_menu'] == 'Y' ? 'Remove mega menu' : 'Add mega menu') . '" for="checkbox' . $menu_array['items'][$itemId]['id'] . '-megaMenu">
								<span></span>
								<span class="check tooltips" data-toggle="tooltip" data-placement="top" data-original-title="Remove mega menu"></span>
								<span class="box tooltips" data-toggle="tooltip" data-placement="top" data-original-title="Add mega menu"></span>
							</label>
						</span>

						</span>
						';
                    //$html.='<a href="'.$img.'" title="Click here to view mega menu sample" class="fancybox-buttons" data-fancybox="fancybox-buttons"><i class="fa fa-info"></i></a>';
                }
                $html .= '</div>';
                $html .= $this->make_menu($itemId, $cur_url, $menu_array);
                $parent_order++;
                $html .= '</li>';
                $child_order++;
            }
            $html .= '</ol><input type="hidden" value="' . $menuType->chrPublish . '" id="menuActive">';
        }
        $response = $html;
        return $response;
    }
    /**
     * This method handels save function when menu item dragged (N level)
     * @return  Html menu
     * @since   04-08-2017
     * @author  NetQuick
     */
    public function updateDragItem($data, $root = false)
    {
        $parent_order = 1;
        foreach ($data as $key => $value) {
            if (array_key_exists("children", $data[$key])) {
                $child_order = 1;
                $parent = $data[$key]['id'];
                #Update parent who has child
                $whereConditions = ['id' => $parent];
                $updateMenuFields = [
                    'intParentMenuId' => 0,
                    'intItemOrder' => 0,
                    'intParentItemOrder' => $parent_order
                ];
                $update = CommonModel::updateRecords($whereConditions, $updateMenuFields, false, '\\Powerpanel\\Menu\\Models\\Menu');
                ###############################
                $parent_order++;
                foreach ($value['children'] as $skey => $svalue) {
                    if (isset($svalue['children'])) {
                        $this->updateDragItem([$svalue], false);
                    }
                    #Update children
                    $id = $svalue['id'];
                    $whereConditions = ['id' => $id];
                    $updateMenuFields = [
                        'intParentMenuId' => $parent,
                        'intItemOrder' => $child_order,
                        'intParentItemOrder' => 0
                    ];
                    $update = CommonModel::updateRecords($whereConditions, $updateMenuFields, false, '\\Powerpanel\\Menu\\Models\\Menu');
                    $child_order++;
                    ###############################
                }
            } else {
                #Update parent who don't have child
                $id = $value['id'];
                $whereConditions = ['id' => $id];
                $updateMenuFields = [
                    'intParentMenuId' => 0,
                    'intParentItemOrder' => $parent_order,
                    'intItemOrder' => 0
                ];
                $update = CommonModel::updateRecords($whereConditions, $updateMenuFields, false, '\\Powerpanel\\Menu\\Models\\Menu');
                $parent_order++;
                ###############################
            }
            $this->flushCache();
        }
    }
    /**
     * This method handels re-load process of dynamic menu
     * @return  Menu HTML content (raw)
     * @since   04-08-2017
     * @author  NetQuick
     */
    public function reload()
    {

        $position = Request::get('position');
        $check_front = Request::get('check_front');
        $menu_array = $this->buildMenu($position, $check_front);
        $menu = $this->make_menu(0, "", $menu_array);
        echo $menu;
    }
    /**
     * This method handels save function when menu content changes (ajax)
     * @return  Html menu
     * @since   04-08-2017
     * @author  NetQuick
     */
    public function saveMenu()
    {
        $position = Request::get('position');
        $active = Request::get('active');
        $data = json_decode(Request::get('menuList'), true);
        $activeItems = json_decode(Request::get('activeItem'));
        $inActiveItems = json_decode(Request::get('inActiveItem'));

        $inMobile = json_decode(Request::get('inMobile'));
        $inWeb = json_decode(Request::get('inWeb'));

        $notInMobile = json_decode(Request::get('notInMobile'));
        $notInWeb = json_decode(Request::get('notInWeb'));

        $this->updateDragItem($data, true);
        if ($active == 'Y') {
            foreach ($activeItems as $id) {
                $whereConditions = ['id' => $id];
                $orWhereConditions = ['intParentMenuId' => $id];
                $updateMenuFields = ['intPosition' => $position, 'chrPublish' => 'Y', 'chrActive' => 'Y', 'chrDisplayFront' => 'Y'];
                $update = CommonModel::updateRecords($whereConditions, $updateMenuFields, $orWhereConditions, '\\Powerpanel\\Menu\\Models\\Menu');
            }
            foreach ($inActiveItems as $id) {
                $whereConditions = ['id' => $id];
                $orWhereConditions = ['intParentMenuId' => $id];
                $updateMenuFields = ['intPosition' => $position, 'chrPublish' => 'N', 'chrActive' => 'N', 'chrDisplayFront' => 'Y'];
                $update = CommonModel::updateRecords($whereConditions, $updateMenuFields, $orWhereConditions, '\\Powerpanel\\Menu\\Models\\Menu');
            }
        } else {
            foreach ($inActiveItems as $id) {
                $whereConditions = ['id' => $id];
                $orWhereConditions = ['intParentMenuId' => $id];
                $updateMenuFields = ['intPosition' => $position, 'chrPublish' => 'N', 'chrActive' => 'N', 'chrDisplayFront' => 'Y'];
                $update = CommonModel::updateRecords($whereConditions, $updateMenuFields, $orWhereConditions, '\\Powerpanel\\Menu\\Models\\Menu');
            }
        }

        $whereConditions = ['id' => $position];
        $updateMenuFields = ['chrPublish' => $active];
        $update = CommonModel::updateRecords($whereConditions, $updateMenuFields, false, '\\Powerpanel\\Menu\\Models\\MenuType');

        foreach ($inMobile as $id) {
            $this->inMobile($id, 'Y');
        }
        foreach ($inWeb as $id) {
            $this->inWeb($id, 'Y');
        }

        foreach ($notInMobile as $id) {
            $this->inMobile($id, 'N');
        }
        foreach ($notInWeb as $id) {
            $this->inWeb($id, 'N');
        }
        $this->flushCache();
        reload();
    }
    /**
     * This method handels making a menu as mega menu (ajax)
     * @return  Html menu
     * @since   04-08-2017
     * @author  NetQuick
     */
    public function megaMenu()
    {
        $id = Request::get('id');
        $status = Request::get('status');
        $whereConditions = ['id' => $id];
        $updateMenuFields = ['chrMegaMenu' => $status];
        $update = CommonModel::updateRecords($whereConditions, $updateMenuFields, false, '\\Powerpanel\\Menu\\Models\\Menu');
        $this->flushCache();
    }



    public function getPageList()
    {
        $moduleId = Request::get('module');
        $pageId = Request::get('pageId');

        $moduleData = Modules::getModuleDataById($moduleId);

        $recordList = DB::table($moduleData->varTableName)->select($moduleData->varTableName . '.id', 'intAliasId', 'varTitle', 'alias.varAlias')
            ->leftJoin('alias', $moduleData->varTableName . '.intAliasId', '=', 'alias.id')
            ->where('chrPublish', 'Y')->get();

        $html = '';
        if ($recordList->count() > 0) {
            $html .= '<div class="form-group">
                        <ul class="checkbox-list menu_pages_list service-list-checks">';
            foreach ($recordList as $cmsPage) {
                if ($cmsPage->varTitle != "Data Removal Lead") {
                    $class = 'col-md-6';
                    if (strlen($cmsPage->varTitle) > 30) {
                        $class = 'col-md-12';
                    }

                    $html .= '<li class="' . $class . '" >';
                    $html .= '<label>';

                    $checked = '';
                    if ($cmsPage->id == $pageId) {
                        $checked = 'checked="checked"';
                    }

                    if ($moduleData->varModuleName == 'pages') {
                        $html .= '<input ' . $checked . ' class="frontPage" type="checkbox" data-title="' . $cmsPage->varTitle . '" name="pages[]" value="' . $cmsPage->varAlias . '_' . $cmsPage->id . '"> ' . $cmsPage->varTitle;
                    } else {
                        $html .= '<input class="frontPage" type="checkbox" data-title="' . $cmsPage->varTitle . '" name="pages[]" value="' . $moduleData->varModuleName . '/' . $cmsPage->varAlias . '_' . $cmsPage->id . '"> ' . $cmsPage->varTitle;
                    }

                    $html .= '</label>';
                    $html .= '</li>';
                }
            }
            $html .= '<li id="frontPageSelect" class="col-md-12" style="color: red;display: none;">' . trans("template.menuModule.frontPageSelect") . '</li>
                      <li id="frontPageExists" class="col-md-12" style="color: red;display: none;">' . trans('template.menuModule.frontPageExists') . '</li>';
            $html .= '</ul> </div>';
        } else {
            $html .= '<p class="text-center">' . trans('template.menuModule.noPageIsAvailable') . '</p>';
        }

        return $html;
    }

    /**
     * This method handels making a menu available in mobile (ajax)
     * @return  Html menu
     * @since   19-09-2017
     * @author  NetQuick
     */
    public function inMobile($id, $status)
    {
        $menuItem = Menu::getMenuItem($id);
        $whereConditions = ['id' => $id, 'chrActive' => 'Y'];
        $updateMenuFields = [
            'chrInMobile' => $status,
            'chrActive' => ($menuItem->chrInWeb == 'N' && $status == 'N' ? 'N' : 'Y'),
        ];
        $update = CommonModel::updateRecords($whereConditions, $updateMenuFields, false, '\\Powerpanel\\Menu\\Models\\Menu');
        $this->flushCache();
    }

    /**
     * This method handels making a menu available in web view (ajax)
     * @return  Html menu
     * @since   19-09-2017
     * @author  NetQuick
     */
    public function inWeb($id, $status)
    {
        $menuItem = Menu::getMenuItem($id);
        $whereConditions = ['id' => $id, 'chrActive' => 'Y'];
        $updateMenuFields = [
            'chrInWeb' => $status,
            'chrActive' => ($menuItem->chrInMobile == 'N' && $status == 'N' ? 'N' : 'Y'),
        ];
        $update = CommonModel::updateRecords($whereConditions, $updateMenuFields, false, '\\Powerpanel\\Menu\\Models\\Menu');
        $this->flushCache();
    }

    /**
     * This method handels save function when menu item added (ajax)
     * @return  Html menu
     * @since   04-08-2017
     * @author  NetQuick
     */
    public function addMenuItem()
    {
        $data = Request::all();
        $menu_count = Menu::getItemCount([
            ['intParentItemOrder', '>', 0],
            ['chrActive', '=', 'Y'],
        ]);

        $regex = '/^(https?:\/\/)?([\da-z\.-]+)\.([a-z\.]{2,6})([\/\w \.-]*)*\/?$/';
        //Validation rules
        $rules = array(
            'title' => "required|regex:/^['a-zA-Z0-9& ]+$/",
            // 'page_url' => 'required|regex:/^\S*$/',
            'page_url' => 'required|regex:' . $regex,
        );

        $messages = array(
            'title.required' => 'Title field is required.',
            'title.regex' => 'Title format is invalid.(Valid: a-z,A-Z,0-9)',
            'page_url.required' => 'URL field is required.',
            'page_url.regex' => 'Please enter valid URL.'
        );
        //Validate data
        $validator = Validator::make($data, $rules, $messages);
        if ($validator->passes()) {
            $exists = Menu::getItemCount([
                ['intPosition', '=', $data['position']],
                ['varTitle', '=', $data['title']],
                ['chrDelete', '=', 'N'],
            ]);
            if ($exists == 0) {
                $menuItemArr = [];
                $menuItemArr['varTitle'] = trim($data['title']);
                $menuItemArr['txtPageUrl'] = trim($data['page_url']);
                $menuItemArr['intPosition'] = $data['position'];
                $menuItemArr['chrActive'] = $data['active'];
                $menuItemArr['chrPublish'] = 'N';
                $menuItemArr['intParentItemOrder'] = $menu_count + 1;
                $menuItemID = CommonModel::addRecord($menuItemArr, '\\Powerpanel\\Menu\\Models\\Menu');
                $this->flushCache();
                $logArr = MyLibrary::logData($menuItemID);
                if ($logArr) {
                    $logArr['varTitle'] = trim($data['title']);
                    $logArr['action'] = "Add";
                    Log::recordLog($logArr);
                }
            } else {
                echo json_encode(['title' => "Item exists in menu."]);
            }
            /*End code for logs*/
        } else {
            $arrReturn = $validator->errors()->messages();
            if ($data['title']) {
                $arrReturn['titleI'] = $data['title'];
            }
            if ($data['page_url']) {
                $arrReturn['page_urlI'] = $data['page_url'];
            }
            echo json_encode($arrReturn);
        }
    }
    /**
     * This method handels save function when multiple menu items are added (ajax)
     * @return  Html menu
     * @since   04-08-2017
     * @author  NetQuick
     */
    public function addMenuItems()
    {
        $data = Request::all();
        $existsArr = [];
        $menu_count = Menu::getItemCount([
            ['intParentItemOrder', '>', 0],
            ['chrDelete', '=', 'N'],
        ]);
        $items = json_decode($data['items']);
        #Uncommennt all below line to preven duplicate entries in a menu
        foreach ($items as $key => $value) {
            /*$exists = Menu::getItemCount([
            ['intPosition', '=', $data['position']],
            ['varTitle', '=', $key],
            ['chrDelete','=','N']
            ]);

            if($exists == 0){*/
            $page = explode('_', trim($value));
            $menuItemArr = [];
            $menuItemArr['varTitle'] = trim($key);
            $menuItemArr['txtPageUrl'] = trim($page[0]);
            $menuItemArr['intPageId'] = trim($page[1]);
            $menuItemArr['intPosition'] = $data['position'];
            $menuItemArr['chrActive'] = $data['active'];
            $menuItemArr['chrPublish'] = 'N';
            $menuItemArr['chrDisplayFront'] = 'N';
            $menuItemArr['intParentItemOrder'] = $menu_count + 1;

            $menuItemID = CommonModel::addRecord($menuItemArr, '\\Powerpanel\\Menu\\Models\\Menu');
            $this->flushCache();
            $logArr = MyLibrary::logData($menuItemID);
            if ($logArr) {
                $logArr['varTitle'] = trim($key);
                $logArr['action'] = "Add";
                Log::recordLog($logArr);
            }
            /*} else {
        array_push($existsArr, $key);
        }*/
        }
        echo json_encode($existsArr);
    }
    /**
     * This method handels save function when main menu added (ajax)
     * @return  Html menu
     * @since   04-08-2017
     * @author  NetQuick
     */
    public function addMenuType()
    {
        $data = Request::all();
        //Validation rules
        $rules = array(
            'title' => 'required|regex:/^[a-zA-Z0-9& ]+$/',
        );
        $messages = array(
            'title.required' => 'Title field is required.',
            'title.regex' => 'Title format is invalid.(Valid: a-z,A-Z,0-9)',
        );
        //Validate data
        $validator = Validator::make($data, $rules, $messages);
        if ($validator->passes()) {
            $module = Modules::getModule('menu-type');
            $menuType = [];
            $menuType['varTitle'] = trim($data['title']);
            $menuType['intAliasId'] = MyLibrary::insertAlias($data['alias'], $module->id);
            $newMenuTypeID = CommonModel::addRecord($menuType, '\\Powerpanel\\Menu\\Models\\MenuType');
            $this->flushCache();
            echo json_encode(["menu_id" => $newMenuTypeID]);
        } else {
            echo json_encode($validator->errors()->messages());
        }
    }

    /**
     * This method handels get function when main menu added (ajax)
     * @return  Html menu
     * @since   04-08-2017
     * @author  NetQuick
     */
    public function getMenuType()
    {
        $data = MenuType::getList();
        $opt = '';
        foreach ($data as $value) {
            $opt .= '<option value="' . $value->id . '">' . $value->varTitle . '</option>';
        }
        return response()->json(array('success' => true, 'html' => $opt));
    }
    /**
     * This method handels delete function for menu item(ajax)
     * @return  Html menu
     * @since   04-08-2017
     * @author  NetQuick
     */
    public function deleteMenuItem()
    {
        if (null !== Request::all()) {
            $id = Request::get('id');
            $hasChild = Menu::checkHasChild($id);
            if (!$hasChild) {
                $menuObj = Menu::getRecordById($id);
                $whereConditions = ['id' => $id];
                $deleted = Menu::where($whereConditions)->delete();
                if ($deleted) {
                    Alias::deleteAlias($menuObj->intAliasId, Config::get('Constant.MODULE.ID'));
                }

                $this->flushCache();
                //$updateMenuFields=['chrPublish'=>'N', 'chrDelete'=>'Y'];
                //$update = CommonModel::updateRecords($whereConditions, $updateMenuFields);
            }
        }
    }
    /**
     * This method handels delete function for entire menu (ajax)
     * @return  Html menu
     * @since   04-08-2017
     * @author  NetQuick
     */
    public function deleteMenu()
    {
        if (null !== Request::all()) {
            $position = Request::get('position');

            $menuObj = Menu::getMenuItemByMenuType($position);
            $menuTypeObj = MenuType::getRecordById($position);

            $menuTypeWhereConditions = ['id' => $position];
            $deleted = MenuType::where($menuTypeWhereConditions)->delete();
            if ($deleted) {
                Alias::deleteAlias($menuTypeObj->intAliasId, 6);
            }

            $whereConditions = ['intPosition' => $position];
            $menuDeleted = Menu::where($whereConditions)->delete();
            if ($menuDeleted) {
                foreach ($menuObj as $key => $value) {
                    if (!empty($value->intAliasId)) {
                        Alias::deleteAlias($value->intAliasId, Config::get('Constant.MODULE.ID'));
                    }
                }
            }

            // $updateMenuFields = ['chrPublish'=>'N', 'chrDelete'=>'Y'];
            // $update=CommonModel::updateRecords($whereConditions, $updateMenuFields);

            // $updateMenuFields=['chrPublish'=>'N', 'chrDelete'=>'Y'];
            // $update=CommonModel::updateRecords($whereConditions, $updateMenuFields, false, '\\Powerpanel\\Menu\\Models\\MenuType');

            $this->flushCache();
        }
    }
    /**
     * This method handels get function for single menu item(ajax)
     * @return  Json menu item data
     * @since   04-08-2017
     * @author  NetQuick
     */
    public function getMenuItem()
    {
        $response = false;
        $id = Request::get('id');
        $response = Menu::getMenuItem($id);
        echo json_encode($response);
    }
    /**
     * This method handels update function for menu item(ajax)
     * @since   04-08-2017
     * @author  NetQuick
     */
    public function updateMenuItem()
    {
        $data = Request::all();
        //Validation rules
        $regex = '/^(https?:\/\/)?([\da-z\.-]+)\.([a-z\.]{2,6})([\/\w \.-]*)*\/?$/';
        $rules = array(
            'id' => 'required',
            'title' => "required|regex:/^['a-zA-Z0-9& ]+$/",
            'page_url' => 'required|regex:/^\S*$/',
            //            'page_url' => 'required|url',
            // 'page_url' => 'required|regex:'.$regex,
        );
        $messages = array(
            'title.required' => 'Title field is required.',
            'title.regex' => 'Title format is invalid.(Valid: a-z,A-Z,0-9)',
            'page_url.required' => 'Page url field is required.',
            'page_url.url' => 'Please enter valid URL.'
            //'page_url.regex' => 'Please enter valid URL.'
        );
        //Validate data
        $validator = Validator::make($data, $rules, $messages);
        if ($validator->passes()) {
            $id = $data['id'];
            $whereConditions = ['id' => $data['id']];
            $updateMenuFields = [
                'varTitle' => $data['title'],
                'txtPageUrl' => $data['page_url'],
                //'chrDisplayFront'=>'N'
            ];
            $update = CommonModel::updateRecords($whereConditions, $updateMenuFields, false, '\\Powerpanel\\Menu\\Models\\Menu');
            $this->flushCache();
        } else {
            echo json_encode($validator->errors()->messages());
        }
    }

    public function flushCache()
    {
        Cache::tags('Menu')->flush();
    }
}
