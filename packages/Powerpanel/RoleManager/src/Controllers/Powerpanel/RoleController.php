<?php

namespace Powerpanel\RoleManager\Controllers\Powerpanel;

use App\Helpers\MyLibrary;
use App\Http\Controllers\PowerpanelController;
use App\Log;
use App\Modules;
use Auth;
use Config;
use Illuminate\Support\Facades\Redirect;
use Powerpanel\RoleManager\Models\Permission;
use Powerpanel\RoleManager\Models\Permission_role;
use Powerpanel\RoleManager\Models\Role;
use Spatie\Permission\Models\Role as SpatieRole;
use Spatie\Permission\Models\Permission as SpatiePermission;
use Powerpanel\RoleManager\Models\Role_user;
use Powerpanel\Workflow\Models\Workflow;
use Request;
use Session;
use Validator;

class RoleController extends PowerpanelController
{

    public $user;

    public function __construct()
    {
        parent::__construct();
        if (isset($_COOKIE['locale'])) {
            app()->setLocale($_COOKIE['locale']);
        }
        $this->user = Auth::user();
    }

    /**
     * This method handels load process of roles
     * @return  View
     * @since   2017-08-16
     * @author  NetQuick
     */
    public function index(Request $request)
    {
        $roles = Role::getRecordList();
        $iTotalRecords = count($roles->toArray());
        $this->breadcrumb['title'] = trans('rolemanager::template.roleModule.manageRoles');
        return view('rolemanager::powerpanel.index', ['roles' => $roles, 'iTotalRecords' => $iTotalRecords, 'breadcrumb' => $this->breadcrumb])
            ->with('i', (Request::input('page', 1) - 1) * 5);
    }

    /**
     * This method loads role edit view
     * @param   Alias of record
     * @return  View
     * @since   2017-10-28
     * @author  NetQuick
     */
    public function edit($id = false)
    {
        $isAdmin = false;
        $currentUserRoleData = Session::get('USERROLEDATA');
        if ($currentUserRoleData->chrIsAdmin == 'Y') {
            $isAdmin = true;
        }

        $grouppedPermission = Self::grouppedPermission();

        if (!is_numeric($id)) {
            $this->breadcrumb['title'] = trans('rolemanager::template.roleModule.addRole');
            $this->breadcrumb['module'] = trans('rolemanager::template.roleModule.manageRoles');
            $this->breadcrumb['url'] = 'powerpanel/roles';
            $this->breadcrumb['inner_title'] = trans('rolemanager::template.roleModule.addRole');
            $data = ['permission' => $grouppedPermission, 'breadcrumb' => $this->breadcrumb];
        } else {
            $role = Role::getRecordById($id);
            if (empty($role)) {
                return redirect()->route('powerpanel.roles.add');
            }
            $rolePermissions = Permission_role::getPermissionRole($id);
            $rolePermissions = array_column($rolePermissions, 'permission_role');
            $rolePermissions = array_column($rolePermissions, 'id');

            $this->breadcrumb['title'] = trans('rolemanager::template.roleModule.editRole') . ' - ' . $role->display_name;
            $this->breadcrumb['module'] = trans('rolemanager::template.roleModule.manageRoles');
            $this->breadcrumb['url'] = 'powerpanel/roles';
            $this->breadcrumb['inner_title'] = trans('rolemanager::template.roleModule.editRole') . ' - ' . $role->display_name;
            $data = ['role' => $role, 'permission' => $grouppedPermission, 'rolePermissions' => $rolePermissions, 'breadcrumb' => $this->breadcrumb];
        }
        $data['isAdmin'] = $isAdmin;
        return view('rolemanager::powerpanel.actions', $data);
    }

    public static function grouppedPermission($grpId = false)
    {
        $permissions = Permission::getPermissions($grpId);
        $grouppedPermission = array();

        foreach ($permissions as $data) {
            if ($data['modules'] != null) {
                $groupID = $data['modules']['group']['varTitle'];
                $groupOrder = $data['modules']['group']['intDisplayOrder'];
                $id = $data['modules']['varTitle'];
                $grouppedPermission[$id]['order'] = $groupOrder;
                if (isset($grouppedPermission[$id])) {
                    $grouppedPermission[$id]['group'] = $groupID;
                    $grouppedPermission[$id][] = $data;
                } else {
                    $grouppedPermission[$id]['group'] = $groupID;
                    $grouppedPermission[$id] = array($data);
                }
            }
        }

        $arr = array();
        foreach ($grouppedPermission as $key => $item) {
            if (isset($item['group'])) {
                $arr[$item['group']]['order'] = $item['order'];
                $arr[$item['group']]['group'] = $item['group'];
                $arr[$item['group']][$key] = $item;
            }
        }
        //ksort($arr);

        usort($arr, function ($a, $b) {
            return $a['order'] - $b['order'];
        });

        return $arr;
    }

    public static function GetCategorygroups($roleId = false)
    {
        $group = [];
        if ($roleId) {
            $roleCan = Permission_role::getPermissionRole($roleId);
            $permissonroleCan = array_column($roleCan, 'permission_role');
            $userroleassignedmodules = array_column($permissonroleCan, 'intFKModuleCode');
            $useraddeditModules = array();
            foreach ($permissonroleCan as $rolval) {
                if ($rolval['display_name'] == "per_add" || $rolval['display_name'] == "per_edit") {
                    array_push($useraddeditModules, $rolval['intFKModuleCode']);
                }
            }
            $modulecats = Modules::getModuleByWorkflowIds($useraddeditModules);
            if (!empty($modulecats)) {
                foreach ($modulecats as $module) {
                    if (in_array($module->id, $useraddeditModules)) {
                        $getResponse = Workflow::checkExists($module['group']['id'], $roleId, $module->id);
                        if (!$getResponse) {
                            $group[$module['group']['id']] = $module['group']['varTitle'];
                        }
                    }
                }
            }
        } else {
            $permissions = Permission::getPermissions();
            $permissions = array_column($permissions, 'modules');
            $permissions = array_column($permissions, 'group');
            foreach ($permissions as $key => $data) {
                $group[$data['id']] = $data['varTitle'];
            }
        }
        return $group;
    }

    public static function groups($roleId = false)
    {
        $group = [];
        if ($roleId) {

            $roleCan = Permission_role::getPermissionRole($roleId);
            $permissonroleCan = array_column($roleCan, 'permission_role');
            $userroleassignedmodules = array_column($permissonroleCan, 'intFKModuleCode');
            $useraddeditModules = array();
            foreach ($permissonroleCan as $rolval) {
                if ($rolval['display_name'] == "per_add" || $rolval['display_name'] == "per_edit") {
                    array_push($useraddeditModules, $rolval['intFKModuleCode']);
                }
            }
            $modulecats = Modules::getModuleByWorkflowIds($useraddeditModules);
            if (!empty($modulecats)) {
                foreach ($modulecats as $module) {
                    if (in_array($module->id, $useraddeditModules)) {
                        $group[$module['group']['id']] = $module['group']['varTitle'];
                    }
                }
            }
        } else {
            $ignoreID = ['5', '0', '4', '6'];
            $permissions = Permission::getPermissions();
            $permissions = array_column($permissions, 'modules');
            $permissions = array_column($permissions, 'group');
            foreach ($permissions as $key => $data) {
                if (!in_array($data['id'], $ignoreID)) {
                    $group[$data['id']] = $data['varTitle'];
                }
            }
        }
        return $group;
    }

    /**
     * This method stores blog modifications
     * @return  View
     * @since   2017-11-10
     * @author  NetQuick
     */
    public function handlePost(Request $request)
    {
        $data = Request::all();
        $roleDisplayName = trim($data['name']);
        $data['name'] = str_replace(" ", "_", strtolower(trim($data['name'])));

        $actionMessage = trans('rolemanager::template.common.oppsSomethingWrong');
        $id = Request::segment(3);
        $rules = array(
            'name' => 'required|handle_xss|no_url|unique:roles,name',
            'permission' => 'required',
        );

        $messages = array(
            'name.required' => 'Name field is required.',
            'name.unique' => 'Name has already been taken.',
            'permission.required' => 'Permission filed is required.',
        );

        if (is_numeric($id)) {
            unset($rules['name']);
        }

        $validator = Validator::make($data, $rules, $messages);
        if ($validator->passes()) {
            if (is_numeric($id)) { 
                
                #Edit post Handler=======
                if (isset($data['isadmin']) && $data['isadmin'] == 'on') {
                    if (isset($data['reviewPermissions']) && $data['reviewPermissions'] != '') {
                        $data['reviewPermissions'] = array_unique($data['reviewPermissions']);
                        $data['permission'] = array_merge($data['permission'], $data['reviewPermissions']);
                    }
                }

                $role = SpatieRole::findById($id);
                $role->display_name = $roleDisplayName;
                $role->name = str_replace(" ", "_", strtolower(trim($data['rolename'])));
//              $role->description = trim($data['description']);
                $role->chrIsAdmin = isset($data['isadmin']) && $data['isadmin'] == 'on' ? 'Y' : 'N';
                $role->save();

                //Permission_role::deletePermissionRole($id); 

                $finalPermissionArr = array();
                foreach ($data['permission'] as $value) {
                    $finalPermissionArr[] = $value;
                }

                //$permission = SpatiePermission::findById($value);
                $role->syncPermissions($finalPermissionArr);

                $newRoleObj = Role::getRecordById($id);
                $oldRec = $this->recordHistory($role);
                $newRec = $this->newrecordHistory($role, $newRoleObj);
                $logArr = MyLibrary::logData($newRoleObj->id);
                $logArr['old_val'] = $oldRec;
                $logArr['new_val'] = $newRec;
                $logArr['varTitle'] = $newRoleObj->display_name;
                $logArr['action'] = "edit";
                Log::recordLog($logArr);
                $actionMessage = trans('rolemanager::template.roleModule.updateMessage');

            } else { #Add post Handler=======

                if (isset($data['isadmin']) && $data['isadmin'] == 'on') {
                    if (isset($data['reviewPermissions']) && $data['reviewPermissions'] != '') {
                        $data['reviewPermissions'] = array_unique($data['reviewPermissions']);
                        $data['permission'] = array_merge($data['permission'], $data['reviewPermissions']);
                    }
                }

                $role = new SpatieRole();
                $role->display_name = $roleDisplayName;
                $role->name = str_replace(" ", "_", strtolower(trim($data['name'])));
//                $role->description = trim($data['description']);
                $role->chrIsAdmin = isset($data['isadmin']) && $data['isadmin'] == 'on' ? 'Y' : 'N';
                $role->save();


                $finalPermissionArr = array();
                foreach ($data['permission'] as $value) {
                    $finalPermissionArr[] = $value;
                }
                $role->syncPermissions($finalPermissionArr);
                
                // foreach ($data['permission'] as $value) {
                //     $permission = SpatiePermission::findById($value);
                //     $role->givePermissionTo($permission->name);
                // }
                
                
                if (!empty($role->id)) {
                    $id = $role->id;
                    $newRoleObj = Role::getRecordForLogById($id);
                    $logArr = MyLibrary::logData($newRoleObj->id);
                    $logArr['varTitle'] = $newRoleObj->display_name;
                    Log::recordLog($logArr);
                    $actionMessage = trans('rolemanager::template.roleModule.addMessage');
                }
            }

            if (!empty($data['saveandexit']) && $data['saveandexit'] == 'saveandexit') {
                return redirect()->route('powerpanel.roles.index')->with('message', $actionMessage);
            } else {
                return redirect()->route('powerpanel.roles.edit', $id)->with('message', $actionMessage);
            }
        } else {
            return Redirect::back()->withErrors($validator)->withInput();
        }
    }

    /**
     * This method loads events table data on view
     * @return  View
     * @since   2017-08-16
     * @author  NetQuick
     */
    public function get_list()
    {

        $userIsAdmin = false;
        if (isset($this->currentUserRoleData->chrIsAdmin) && $this->currentUserRoleData->chrIsAdmin == 'Y') {
            $userIsAdmin = true;
        }

        $filterArr = [];
        $records = [];
        $records["data"] = [];
        $filterArr['orderColumnNo'] = (!empty(Request::get('order')[0]['column']) ? Request::get('order')[0]['column'] : '');
        $filterArr['orderByFieldName'] = (!empty(Request::get('columns')[$filterArr['orderColumnNo']]['name']) ? Request::get('columns')[$filterArr['orderColumnNo']]['name'] : '');
        $filterArr['orderTypeAscOrDesc'] = (!empty(Request::get('order')[0]['dir']) ? Request::get('order')[0]['dir'] : '');
        $filterArr['searchFilter'] = !empty(Request::get('searchValue')) ? Request::get('searchValue') : '';
        $filterArr['iDisplayLength'] = intval(Request::get('length'));
        $filterArr['iDisplayStart'] = intval(Request::get('start'));
        $sEcho = intval(Request::get('draw'));
        $arrResults = Role::getRecordList($filterArr, $userIsAdmin, $this->currentUserRoleData);

        $iTotalRecords = Role::getRecordListCount($filterArr, true, $userIsAdmin, $this->currentUserRoleData);
        $end = $filterArr['iDisplayStart'] + $filterArr['iDisplayLength'];
        $end = $end > $iTotalRecords ? $iTotalRecords : $end;
        if (!empty($arrResults)) {
            foreach ($arrResults as $key => $value) {
                $allowed = false;
                $allowed = true;
                if ($allowed) {
                    $records["data"][] = $this->tableData($value);
                }
            }
        }
        $records["customActionStatus"] = "OK";
        $records["draw"] = $sEcho;
        $records["recordsTotal"] = $iTotalRecords;
        $records["recordsFiltered"] = $iTotalRecords;
        return json_encode($records);
    }

    public function tableData($role = false)
    {
        $titleData = "";
        $isRoleCount = Role_user::getCountById($role->id);
        if ($isRoleCount > 0) {
            $titleData = 'This role is assigned to user so can not be deleted.';
        }

        $checkbox = '<a href="javascript:;" data-toggle="tooltip" data-placement="right" data-toggle="tooltip" data-original-title="' . $titleData . '" title="' . $titleData . '"><i style="color:red" class="fa fa-exclamation-triangle"></i></a>';

        $actions = '';
//            $actions .= '<a class="without_bg_icon" title="'.trans("template.common.show").'" href="'.route('powerpanel.roles.show',$role->id).'">
        //            <i class="fa fa-search-plus" aria-hidden="true"></i></a>&nbsp;';
        if (Auth::user()->can('roles-edit')) {
            $actions .= '<a class="without_bg_icon" title="' . trans("rolemanager::template.common.edit") . '" href="' . route('powerpanel.roles.edit', $role->id) . '">
				<i class="fa fa-pencil"></i></a>';
        }
        if (Auth::user()->can('roles-delete') && $isRoleCount == 0) {
            if ($role->name != 'netquick_admin') {
                $actions .= '&nbsp;<a class="without_bg_icon delete" title="' . trans("rolemanager::template.common.delete") . '" data-controller="roles" data-alias = "' . $role->id . '"><i class="fa fa-times"></i></a>';
            }
        }
        if (Auth::user()->can('roles-edit')) {
            $display_name = '<a class="" title="' . trans("rolemanager::template.common.edit") . '" href="' . route('powerpanel.roles.edit', $role->id) . '">' . $role->display_name . '</a>';
        } else {
            $display_name = $role->display_name;
        }
        $chkDeleteBtn = '-';
        if (Auth::user()->can('roles-delete')) {
            if ($role->name != 'netquick_admin') {
                $chkDeleteBtn = ($isRoleCount == 0) ? '<input type="checkbox" name="delete" class="chkDelete" value="' . $role->id . '">' : $checkbox;
            }
        }
        if ($role->updated_at == '') {
            $udate = '---';
        } else {
            $udate = date('' . Config::get('Constant.DEFAULT_DATE_FORMAT') . ' ' . Config::get('Constant.DEFAULT_TIME_FORMAT') . '', strtotime($role->updated_at));
        }
        if ($role->chrIsAdmin == 'Y') {
            $Admin = 'Admin';
        } elseif ($role->chrIsAdmin == 'N') {
            $Admin = 'User';
        }
        $logurl = url('powerpanel/log?id=' . $role->id . '&mid=' . Config::get('Constant.MODULE.ID'));
        if (Auth::user()->can('log-list')) {
            $Copy = "" . $actions . "<a title=\"Log History\" class='log-grid' href=\"$logurl\"><i class=\"fa fa-clock-o\"></i></a>";
        } else {
            $Copy = $actions;
        }
        $records = array(
            $chkDeleteBtn,
            '<div class="pages_title_div_row">' . $display_name . '</div>',
            $Admin,
            $udate,
            $Copy,
        );
        return $records;
    }

    /**
     * This method loads a role data on view
     * @return  View
     * @since   2017-08-16
     * @author  NetQuick
     */
    public function show($id = false)
    {
        $role = Role::getRecordById($id);
        $rolePermissions = Permission_role::getPermissionRole($id);
        $this->breadcrumb['title'] = trans('rolemanager::template.roleModule.shows');
        $this->breadcrumb['module'] = trans('rolemanager::template.roleModule.manageRoles');
        $this->breadcrumb['url'] = 'powerpanel/roles';
        $this->breadcrumb['inner_title'] = trans('rolemanager::template.roleModule.shows') . ' - ' . $role->display_name;
        $breadcrumb = $this->breadcrumb;
        return view('powerpanel.roles.show', compact('role', 'rolePermissions', 'breadcrumb'));
    }

    /**
     * This method destroys roles in multiples
     * @return  Banner index view
     * @since   2016-11-10
     * @author  NetQuick
     */
    public function DeleteRecord(Request $request)
    {
        $data['ids'] = Request::input('ids');
        foreach ($data['ids'] as $key => $id) {
            $newRoleObj = Role::getRecordById($id);
            Permission_role::deletePermissionRole($id);
            $update = Role::updateRecord($id, ['chr_publish' => 'N', 'chr_delete' => 'Y']);
            if ($update) {
                $logArr = MyLibrary::logData($newRoleObj->id);
                $logArr['varTitle'] = $newRoleObj->display_name;
                Log::recordLog($logArr);
            }
            echo json_encode($update);
        }
        Role_user::deleteUserRoles($data['ids']);
        Workflow::deleteWorkflowForRoles($data['ids']);
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
        if ($data->chrIsAdmin == 'Y') {
            $admin = 'Admin';
        } else {
            $admin = 'User';
        }
        $returnHtml .= '
					<table class="new_table_desing table table-striped table-bordered table-hover">
						<thead>
								<tr>
										<th align="center">' . trans("rolemanager::template.common.title") . '</th>
										<th align="center">Admin/User</th>
								</tr>
						</thead>
						<tbody>
								<tr>
										<td align="center">' . $data->display_name . '</td>
										<td align="center">' . $admin . '</td>
								</tr>
						</tbody>
				</table>';
        return $returnHtml;
    }

    /**
     * This method handels logs History records
     * @param   $data
     * @return  HTML
     * @since   2017-07-21
     * @author  NetQuick
     */
    public function newrecordHistory($data = false, $newdata = false)
    {
        if ($data->display_name != $newdata->display_name) {
            $titlecolor = 'style="background-color:#f5efb7"';
        } else {
            $titlecolor = '';
        }
        if ($data->description != $newdata->description) {
            $deccolor = 'style="background-color:#f5efb7"';
        } else {
            $deccolor = '';
        }
        if ($newdata->chrIsAdmin == 'Y') {
            $admin = 'Admin';
        } else {
            $admin = 'User';
        }
        $returnHtml = '';
        $returnHtml .= '
					<table class="new_table_desing table table-striped table-bordered table-hover">
						<thead>
								<tr>
										<th align="center">' . trans("rolemanager::template.common.title") . '</th>
										<th align="center">Admin/User</th>
								</tr>
						</thead>
						<tbody>
								<tr>
										<td align="center" ' . $titlecolor . '>' . $data->display_name . '</td>
										<td align="center" ' . $deccolor . '>' . $admin . '</td>
								</tr>
						</tbody>
				</table>';
        return $returnHtml;
    }

}
