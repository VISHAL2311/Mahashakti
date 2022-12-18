<?php

namespace Powerpanel\ShieldCMSTheme\Controllers\Powerpanel\users;

use App\CommonModel;
use App\Helpers\Email_sender;
use App\Helpers\MyLibrary;
use App\Http\Controllers\PowerpanelController;
use App\Log;
use App\RecentUpdates;
use App\Role;
use App\Role_user;
use App\User;
use Auth;
use DB;
use Hash;
use Illuminate\Support\Facades\Redirect;
use Request;
use Session;
use Validator;

class UserController extends PowerpanelController
{

    public $user;

    public function __construct()
    {
        parent::__construct();
        if (isset($_COOKIE['locale'])) {
            app()->setLocale($_COOKIE['locale']);
        }
    }

    /**
     * Display a listing of the resource.
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $data = User::getRecordList();
        $iTotalRecords = count($data->toArray()) - 1 - 1;
        $this->breadcrumb['title'] = trans('shiledcmstheme::template.userModule.manageUser');
        $breadcrumb = $this->breadcrumb;
        return view('shiledcmstheme::powerpanel.users.list', compact('data', 'iTotalRecords', 'breadcrumb'))
            ->with('i', (Request::input('page', 1) - 1) * 5);
    }

    /**
     * This method loads use add/edit view
     * @param   Alias of record
     * @return  View
     * @since   2017-11-03
     * @author  NetQuick
     */
    public function edit($id = false)
    {
        $roles = Role::getRecordListing('display_name', 'id');
        if (!is_numeric($id)) {
            $this->breadcrumb['title'] = trans('shiledcmstheme::template.userModule.addUser');
            $this->breadcrumb['module'] = trans('shiledcmstheme::template.userModule.manageUser');
            $this->breadcrumb['url'] = 'powerpanel/users';
            $this->breadcrumb['inner_title'] = trans('shiledcmstheme::template.userModule.addUser');
            $breadcrumb = $this->breadcrumb;
            $data = compact('roles', 'breadcrumb');
        } else {
            $user = User::getRecordById($id);
            $user->email = MyLibrary::getDecryptedString($user->email);
            if (empty($user)) {
                return redirect()->route('powerpanel.users.add');
            }
            $userRole = Role::where('id', $user->roles[0]->id)->pluck('id', 'id')->toArray();
            $this->breadcrumb['title'] = trans('shiledcmstheme::template.userModule.editUser') . " - " . $user->name;
            $this->breadcrumb['module'] = trans('shiledcmstheme::template.userModule.manageUser');
            $this->breadcrumb['url'] = 'powerpanel/users';
            $this->breadcrumb['inner_title'] = trans('shiledcmstheme::template.userModule.editUser') . " - " . $user->name;
            $breadcrumb = $this->breadcrumb;
            $data = compact('user', 'roles', 'userRole', 'breadcrumb');

        }
        return view('shiledcmstheme::powerpanel.users.actions', $data);
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
        $id = Request::segment(3);
        $data['email'] = MyLibrary::getEncryptedString($data['email']);
        $actionMessage = trans('shiledcmstheme::template.common.oppsSomethingWrong');
        $rules = [
            'name' => 'required|max:160|handle_xss|no_url',
            'email' => 'required|max:160|handle_xss|no_url|unique:users,email,' . $id,
            'roles' => 'required',
        ];
        $messages = [
            'name.required' => 'Name field is required.',
            'email.required' => 'Email is required.',
            'email.unique' => 'Email id already exists.',
            'roles' => 'Role field is required.',
        ];
        if (isset($data['password']) || !is_numeric($id)) {
            $rules['password'] = 'same:confirm-password|min:6|max:20|check_passwordrules';
            $messages['password.same'] = 'Password and confirm password should match';
        }
        $validator = Validator::make($data, $rules, $messages);
        if ($validator->passes()) {

            $userArr = [];
            $userArr['name'] = trim($data['name']);
            $userArr['email'] = $data['email'];
            $userArr['personalId'] = $data['email'];
            $userArr['pass_change_dt'] = date('Y-m-d');
            $userArr['chrPublish'] = $data['chrMenuDisplay'];

            if (is_numeric($id)) {

                #Edit post Handler=======
                $user = User::getRecordById($id);
                $userArr['password'] = (!empty($data['password'])) ? Hash::make($data['password']) : $user->password;
                $whereConditions = ['id' => $user->id];
                
                $update = CommonModel::updateRecords($whereConditions, $userArr);
                $user->removeRole($user->roles[0]->name);
                if (!empty($data['roles'])) {
                    foreach ($data['roles'] as $key => $value) {

                        $attachedRole = Role::getRecordById($value);
                        $user->assignRole($attachedRole->name);
                        $this->addChartOrder($id, $value);
                    }
                }

                if ($update) {    
                    if (!empty($id)) {
                        $logArr = MyLibrary::logData($user['id']);
                        if (Auth::user()->can('log-advanced')) {
                            $newUserObj = User::getRecordById($id);
                            $oldRec = $this->recordHistory($user);
                            $newRec = $this->recordHistory($newUserObj);
                            $logArr['old_val'] = $oldRec;
                            $logArr['new_val'] = $newRec;
                        }
                        $logArr['varTitle'] = trim($data['name']);
                        if (!empty($logArr)) {
                            Log::recordLog($logArr);
                        }
                        if (Auth::user()->can('recent-updates-list')) {
                            if (!isset($newUserObj)) {
                                $newUserObj = User::getRecordById($id);
                            }
                            $notificationArr = MyLibrary::notificationData($user->id, $newUserObj);
                            if (!empty($notificationArr)) {
                                RecentUpdates::setNotification($notificationArr);
                            }
                        }
                    }
                    $actionMessage = trans('shiledcmstheme::template.userModule.updateMessage');
                }
            } else { 

            #Add post Handler=======
            $userArr['password'] = Hash::make($data['password']);

                $createdUser = User::create($userArr);
                $id  = $createdUser->id;

                //$id = CommonModel::addRecord($userArr);
                $user = User::getRecordById($id);
                
                if (!empty($data['roles'])) {
                    foreach ($data['roles'] as $key => $value) {
                        $attachedRole = Role::getRecordById($value);
                        $createdUser->assignRole($attachedRole->name);
                        $this->addChartOrder($id, $value);
                    }
                }

                if (isset($id)) {
                    $newUserObj = User::getRecordById($id);
                    $logArr = MyLibrary::logData($id);
                    $logArr['varTitle'] = $newUserObj->name;
                    if (!empty($logArr)) {
                        Log::recordLog($logArr);
                    }
                    if (Auth::user()->can('recent-updates-list')) {
                        $notificationArr = MyLibrary::notificationData($id, $newUserObj);
                        if (!empty($notificationArr)) {
                            RecentUpdates::setNotification($notificationArr);
                        }
                    }
                    $actionMessage = trans('shiledcmstheme::template.userModule.addMessage');
                }
            }

            if (!empty($data['saveandexit']) && $data['saveandexit'] == 'saveandexit') {
                return redirect()->route('powerpanel.users.index')->with('message', $actionMessage);
            } else {
                return redirect()->route('powerpanel.users.edit', $id)->with('message', $actionMessage);
            }

        } else {
            //dd($validator->errors());
            return Redirect::back()->withErrors($validator)->withInput();
        }
    }

    public function addChartOrder($userid, $roleid)
    {
        $attachedRole = Role::getRecordById($roleid);
        if ($attachedRole->chrIsAdmin == 'Y') {
            $chartData = [
                'intDisplayOrder' => '[1,3,4,5,8,9,10,11,12]',
                'UserID' => $userid,
                'updated_at' => date('Y-m-d H:i:s'),
            ];
        } else {
            $chartData = [
                'intDisplayOrder' => '[1,4,7,12]',
                'UserID' => $userid,
                'updated_at' => date('Y-m-d H:i:s'),
            ];
        }
        User::addChartData($userid, $chartData);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $user = User::getRecordById($id);
        $this->breadcrumb['title'] = trans('shiledcmstheme::template.common.shows') . " - " . $user->name;
        $this->breadcrumb['module'] = trans('shiledcmstheme::template.userModule.manageUser');
        $this->breadcrumb['url'] = 'powerpanel/users';
        $this->breadcrumb['inner_title'] = trans('shiledcmstheme::template.common.shows') . " - " . $user->name;
        $breadcrumb = $this->breadcrumb;
        return view('powerpanel.users.show', compact('user', 'breadcrumb'));
    }

    /**
     * This method destroys Log in multiples
     * @return  Log index view
     * @since   2016-10-25
     * @author  NetQuick
     */
    public function DeleteRecord()
    {
        $data = Request::get('ids');
        foreach ($data as $key => $id) {
            $user = User::getRecordById($id);
            $user->removeRole($user->roles[0]->name);
        }
        $update = User::deleteRecordsPermanent($data);
        exit;
    }

    public function publish(Request $request)
    {
        $alias = (int) Request::get('alias');
        $update = MyLibrary::setPublishUnpublish($alias, $request);
        echo json_encode($update);
        exit;
    }

    public function userLocking()
    {
        $data = Request::post();
        $intAttempts = $data['action'] == 'unlock' ? 0 : 5;
        $whereConditions = ['id' => $data['id']];
        $userArr = ['intAttempts' => $intAttempts];
        CommonModel::updateRecords($whereConditions, $userArr);
    }

    public function Security_Remove()
    {
        $userid = Auth::user()->id;
        DB::table('users')
            ->where('id', $userid)
            ->update(['chrSecurityQuestions' => 'N', 'intSearchRank' => null, 'varQuestion1' => 0, 'varQuestion2' => 0, 'varQuestion3' => 0, 'varAnswer1' => null, 'varAnswer2' => null, 'varAnswer3' => null]);
        $pass_change_dt = User::getRecordById($userid);
        $name = $pass_change_dt['name'];
        $personalemail = MyLibrary::getDecryptedString($pass_change_dt['personalId']);
        Email_sender::Security_Questions_Enable($name, $personalemail);
        exit;
    }

    public function Security_Add()
    {
        $data = Request::post();
        if (array_key_exists('search_rank', $data)) {
            $rank = $data['search_rank'];
        } else {
            $rank = '2';
        }
        $userid = Auth::user()->id;
        DB::table('users')
            ->where('id', $userid)
            ->update(['chrSecurityQuestions' => 'Y', 'intSearchRank' => $rank, 'varQuestion1' => $data['Question1'], 'varQuestion2' => $data['Question2'], 'varQuestion3' => $data['Question3'], 'varAnswer1' => $data['Answer1'], 'varAnswer2' => $data['Answer2'], 'varAnswer3' => $data['Answer3']]);
//        ----------------
        $body = '';
        $SecurityQuestion1 = User::GetSecurityQuestion_byId($data['Question1']);
        $input1 = $data['Answer1'];
        $cnt1 = strlen($input1);
        if ($cnt1 >= 5) {
            $len1 = strlen($input1) - 4;
            $output1 = substr($input1, 0, 2) . str_repeat('*', $len1) . substr($input1, -2);
        } else if ($cnt1 == 4) {
            $output1 = substr($input1, 0, 1) . str_repeat('*', 2) . substr($input1, -1);
        } elseif ($cnt1 == 3) {
            $output1 = substr($input1, 0, 1) . str_repeat('*', 1) . substr($input1, -1);
        } elseif ($cnt1 == 2) {
            $output1 = substr($input1, 0, 1) . str_repeat('*', 1);
        } elseif ($cnt1 == 1) {
            $output1 = substr($input1, 0, 1);
        }
//        ---
        $SecurityQuestion2 = User::GetSecurityQuestion_byId($data['Question2']);
        $input2 = $data['Answer2'];
        $cnt2 = strlen($input2);
        if ($cnt2 >= 5) {
            $len2 = strlen($input2) - 4;
            $output2 = substr($input2, 0, 2) . str_repeat('*', $len2) . substr($input2, -2);
        } else if ($cnt2 == 4) {
            $output2 = substr($input2, 0, 1) . str_repeat('*', 2) . substr($input2, -1);
        } elseif ($cnt2 == 3) {
            $output2 = substr($input2, 0, 1) . str_repeat('*', 1) . substr($input2, -1);
        } elseif ($cnt2 == 2) {
            $output2 = substr($input2, 0, 1) . str_repeat('*', 1);
        } elseif ($cnt2 == 1) {
            $output2 = substr($input2, 0, 1);
        }
//        ---
        $SecurityQuestion3 = User::GetSecurityQuestion_byId($data['Question3']);
        $input3 = $data['Answer3'];
        $cnt3 = strlen($input3);
        if ($cnt3 >= 5) {
            $len3 = strlen($input3) - 4;
            $output3 = substr($input3, 0, 2) . str_repeat('*', $len3) . substr($input3, -2);
        } else if ($cnt3 == 4) {
            $output3 = substr($input3, 0, 1) . str_repeat('*', 2) . substr($input3, -1);
        } elseif ($cnt3 == 3) {
            $output3 = substr($input3, 0, 1) . str_repeat('*', 1) . substr($input3, -1);
        } elseif ($cnt3 == 2) {
            $output3 = substr($input3, 0, 1) . str_repeat('*', 1);
        } elseif ($cnt3 == 1) {
            $output3 = substr($input3, 0, 1);
        }
        $body .= '<tr>
                    <td style="font-family:Arial, Helvetica, sans-serif; font-size:15px; line-height:24px;"><strong>' . $SecurityQuestion1 . ': </strong>' . $output1 . '</td>
                </tr>';
        $body .= '<tr>
                    <td style="font-family:Arial, Helvetica, sans-serif; font-size:15px; line-height:24px;"><strong>' . $SecurityQuestion2 . ': </strong>' . $output2 . '</td>
                </tr>';
        $body .= '<tr>
                    <td style="font-family:Arial, Helvetica, sans-serif; font-size:15px; line-height:24px;"><strong>' . $SecurityQuestion3 . ': </strong>' . $output3 . '</td>
                </tr>';
//        ----------------
        $pass_change_dt = User::getRecordById($userid);
        $name = $pass_change_dt['name'];
        $personalemail = MyLibrary::getDecryptedString($pass_change_dt['personalId']);
        Email_sender::Security_Questions_Enable($name, $personalemail, $body);
        $returnArray = array("success" => "1", "msg" => "Security Questions Updated");
        echo json_encode($returnArray);
        exit;
    }

    public function step_Email_Otp()
    {
        $data = Request::post();
        $userid = Auth::user()->id;
        if (array_key_exists('verifyEmail', $data)) {
            $personalId = MyLibrary::getEncryptedString($data['verifyEmail']);
            DB::table('users')
                ->where('id', $userid)
                ->update(['personalId' => $personalId]);
        }
        $rand1 = (mt_rand(10, 62));
        $time = substr(time(), -2);
        $rand2 = (mt_rand(63, 99));
        $random = $rand1 . $time . $rand2;
        DB::table('users')
            ->where('id', $userid)
            ->update(['Int_Authentication_Otp' => $random]);

        $pass_change_dt = User::getRecordById($userid);
        $name = $pass_change_dt['name'];
        $chrAuthentication = $pass_change_dt['chrAuthentication'];
        $OTP = $pass_change_dt['Int_Authentication_Otp'];
        $personalemail = MyLibrary::getDecryptedString($pass_change_dt['personalId']);
        Email_sender::Authentication_Otp($name, $OTP, $personalemail, $chrAuthentication);
        $returnArray = array("success" => "1", "msg" => "");
        echo json_encode($returnArray);
        exit;
    }

    public function step_Otp_verify()
    {
        $data = Request::post();
        $userid = Auth::user()->id;

        $pass_change_dt = User::getRecordById($userid);
        $OTP = $pass_change_dt['Int_Authentication_Otp'];
        $otp_Insert = $data['otp'];

        if ($OTP != $otp_Insert) {
            $response = array("error" => 1, 'validatorErrors' => 'Please enter correct access code as you received in your personal email id.');
            echo json_encode($response);
            exit;
        } else {
            $chrAuthentication = $pass_change_dt['chrAuthentication'];
            if ($chrAuthentication != 'Y') {
                $settings = "Y";
            } else {
                $settings = "N";
            }
            DB::table('users')
                ->where('id', $userid)
                ->update(['Int_Authentication_Otp' => '0', 'chrAuthentication' => $settings]);
//------------------------
            $name = $pass_change_dt['name'];
            $personalemail = MyLibrary::getDecryptedString($pass_change_dt['personalId']);
            Email_sender::Authentication_Enable_Disable($name, $personalemail, $settings);
//-----------------------
            $returnArray = array("success" => $settings, "msg" => "");
            echo json_encode($returnArray);
            exit;
        }
    }

    public function get_list()
    {
        $userIsAdmin = false;
        if ($this->currentUserRoleData->chrIsAdmin == 'Y') {
            $userIsAdmin = true;
        }
        $filterArr = [];
        $records = [];
        $records["data"] = [];
        $filterArr['orderColumnNo'] = (!empty(Request::get('order')[0]['column']) ? Request::get('order')[0]['column'] : '');
        $filterArr['orderByFieldName'] = (!empty(Request::get('columns')[$filterArr['orderColumnNo']]['name']) ? Request::get('columns')[$filterArr['orderColumnNo']]['name'] : '');
        $filterArr['orderTypeAscOrDesc'] = (!empty(Request::get('order')[0]['dir']) ? Request::get('order')[0]['dir'] : '');
        $filterArr['statusFilter'] = !empty(Request::input('customActionName')) ? Request::input('customActionName') : '';
        $filterArr['searchFilter'] = !empty(Request::get('searchValue')) ? Request::get('searchValue') : '';
        $filterArr['iDisplayLength'] = intval(Request::get('length'));
        $filterArr['iDisplayStart'] = intval(Request::get('start'));
        $sEcho = intval(Request::get('draw'));

        $Users = User::getRecordList($filterArr);
        $iTotalRecords = count($Users->toArray()) - 1;

        if (!empty($Users)) {

            foreach ($Users as $key => $user) {
                $userRole = $user->roles[0]->name;
                $allowed = false;

                if (($userIsAdmin && $userRole != 'netquick_admin' && $user->id != 2 && $user->id != Auth::user()->id) || $this->currentUserRoleData->name == 'netquick_admin') {
                    $allowed = true;
                } else {
                    $iTotalRecords--;
                }
                if ($allowed) {
                    $records["data"][] = $this->tableData($user, $this->currentUserRoleData->name);
                }
            }

        }

        if (isset($_REQUEST["customActionType"]) && $_REQUEST["customActionType"] == "group_action") {
            $records["customActionStatus"] = "OK"; // pass custom message(useful for getting status of group actions)
        }

        $iTotalRecords = count($records['data']);
        $records["draw"] = $sEcho;
        $records["recordsTotal"] = $iTotalRecords;
        $records["recordsFiltered"] = $iTotalRecords;
        echo json_encode($records);
        exit;
    }

    public function tableData($user = false, $currentRole = false)
    {
        $publish_action = '';
        $roles = '<label class = "label label-success">' . $user->roles[0]->display_name . '</label>';

        $actions = '';
        if (Auth::user()->can('users-edit') || $currentRole == 'netquick_admin') {
            $actions .= '<a class = "" title = "' . trans("shiledcmstheme::template.common.edit") . '" href = "' . route('powerpanel.users.edit', $user->id) . '"><i class = "fa fa-pencil"></i></a>';
        }

        if ((Auth::user()->can('users-delete') || $currentRole == 'netquick_admin') && Auth::user()->id != $user->id) {
            $actions .= '<a class = " delete" title = "' . trans("shiledcmstheme::template.common.delete") . '" data-controller = "users" data-alias = "' . $user->id . '"><i class = "fa fa-times"></i></a>';
        }

        if ((Auth::user()->can('users-publish') || $currentRole == 'netquick_admin') && Auth::user()->id != $user->id) {
            if ($user->chrPublish == 'Y') {
                $publish_action .= '<input data-off-text = "No" data-on-text = "Yes" class = "make-switch publish" class = "make-switch publish" data-off-color = "info" data-on-color = "primary" type = "checkbox" data-controller = "powerpanel/users" title = "' . trans("shiledcmstheme::template.common.publishedRecord") . '" data-value = "Unpublish" data-alias = "' . $user->id . '">';
            } else {
                $publish_action .= '<input checked = "" data-off-text = "No" data-on-text = "Yes" class = "make-switch publish" class = "make-switch publish" data-off-color = "info" data-on-color = "primary" type = "checkbox" data-controller = "powerpanel/users" title = "' . trans("shiledcmstheme::template.common.unpublishedRecord") . '" data-value = "Publish" data-alias = "' . $user->id . '">';
            }
        }

        if (Auth::user()->can('users-edit') || $currentRole == 'netquick_admin') {
            $title = '<a class = "" title = "' . trans("shiledcmstheme::template.common.edit") . '" href = "' . route('powerpanel.users.edit', $user->id) . '">' . $user->name . '</a>';
        } else {
            $title = $user->name;
        }
        $chkDeleteBtn = '-';
        if (Auth::user()->can('users-delete')) {
            if ($user->id != 1) {
                $chkDeleteBtn = '<input type = "checkbox" name = "delete" class = "chkDelete" value = "' . $user->id . '">';
            }
        }

        if ($currentRole == 'netquick_admin' || $currentRole == 'netclues_admin') {
            if ($user->intAttempts >= 5) {
                $actions .= '&nbsp;<a class = " lock-unlock" data-action = "unlock" data-id = "' . $user->id . '" title = "Unlock this user" href = "javascript:void(0);"><i class = "fa fa-lock"></i></a > ';
            }
            // else
            // {
            //     $actions .= ' & nbsp;
            //     <a class=" lock-unlock" data-action="lock" data-id="'.$user->id.'" title="Lock this user" href="javascript:void(0);"><i class="fa fa-unlock"></i></a>';
            // }
        }
        if ($user->chrAuthentication == 'Y') {
            $step = "On";
        } else {
            $step = "Off";
        }
        $records = array(
            $chkDeleteBtn,
            $title,
            MyLibrary::getDecryptedString($user->email),
            '<a href="javascript:;" class="reset-link" data-email="' . MyLibrary::getDecryptedString($user->email) . '">Send Reset link</a>',
            $roles,
            $step,
            $publish_action,
            $actions,
        );

        return $records;
    }

    public function recordHistory($data = false)
    {

        $userRole = Role::where('id', $data->roles[0]->id)->pluck('id', 'id')->toArray();
        $roles = '';
        if (!empty($userRole)) {
            foreach ($userRole as $v) {
                $roleDetail = Role::getRecordById($v);
                $roleName = (isset($roleDetail->display_name) && $roleDetail->display_name != "") ? $roleDetail->display_name : '-';
                $roles .= ' <label class="label label-success">' . $roleName . '</label>';
            }
        }
        $oldRec = '';
        $oldRec .= '<table class="new_table_desing table table-striped table-bordered table-hover">
					<thead>
						<tr>
							<th>' . trans("shiledcmstheme::template.common.name") . '</th>
							<th>' . trans("shiledcmstheme::template.common.email") . '</th>
							<th>' . trans("shiledcmstheme::template.common.roles") . '</th>
						</tr>
					</thead>
					<tbody>
						<tr>
							<td>' . $data->name . '</td>
							<td>' . $data->email . '</td>
							<td>' . $roles . '</td>
						</tr>
					</tbody>
			</table>';
        return $oldRec;
    }

    public function checkanswer(Request $request)
    {
        $response = array("success" => 1);
        $postArr = Request::all();
        $user_data = User::checkAns(auth()->user()->id, $postArr['QuestionId'], $postArr['SecurityAnswer']);
        if ($user_data != 0) {
            DB::table('users')
                ->where('id', auth()->user()->id)
                ->update(['intAttempts' => '0', 'First_Attempts_Time' => null, 'Last_Attempts_Time' => null]);
            Session::put('Security_history', $postArr['SecurityAnswer']);
//            Request::session()->flash('alert-success', 'You are successfully logged in.');
        }
        $response = array("success" => $user_data);
        echo json_encode($response);
        //SitemapGenerator::create(url('/'))->writeToFile(public_path().'/sitemap.xml');
    }

}
