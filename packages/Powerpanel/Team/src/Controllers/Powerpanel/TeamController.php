<?php
/**
 * The TeamController class handels dynamic menu configuration
 * configuration  process.
 * @package   Netquick powerpanel
 * @license   http://www.opensource.org/licenses/BSD-3-Clause
 * @version   1.1
 * @since     2017-07-07
 * @author    NetQuick
 */
namespace Powerpanel\Team\Controllers\Powerpanel;

use App\Alias;
use App\CommonModel;
use App\Helpers\AddImageModelRel;
use App\Helpers\MyLibrary;
use App\Helpers\resize_image;
use App\Http\Controllers\PowerpanelController;
use App\Log;
use App\RecentUpdates;
use Powerpanel\Team\Models\Team;
use Auth;
use Cache;
use Carbon\Carbon;
use Config;
use Illuminate\Support\Facades\Redirect;
use Request;
use Validator;

class TeamController extends PowerpanelController
{

    public function __construct()
    {
        parent::__construct();
        if (isset($_COOKIE['locale'])) {
            app()->setLocale($_COOKIE['locale']);
        }
        $this->MyLibrary = new MyLibrary();
    }
    /**
     * This method handels loading process of teams
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
        $iTotalRecords = CommonModel::getRecordCount(false,false,false, 'Powerpanel\Team\Models\Team');
        $this->breadcrumb['title'] = trans('team::template.teamModule.manageTeam');
        $breadcrumb = $this->breadcrumb;
        return view('team::powerpanel.index', compact('iTotalRecords', 'breadcrumb'));
    }

/**
 * This method loads team edit view
 * @param      Alias of record
 * @return  View
 * @since   2017-07-21
 * @author  NetQuick
 */
    public function edit($alias = false)
    {
        $imageManager = true;
        $availableSocialLinks = Config::get('Constant.AVAILABLE_SOCIAL_LINKS_FOR_TEAM_MEMBER');

        $teamSocialLinksOptions = array();
        if (!empty($availableSocialLinks)) {
            $socialLinks = unserialize($availableSocialLinks);
            $i = 0;
            foreach ($socialLinks as $links) {
                $teamSocialLinksOptions[$i]['key'] = $links['key'];
                $teamSocialLinksOptions[$i]['label'] = $links['title'];
                $teamSocialLinksOptions[$i]['placeholder'] = $links['placeholder'];
                $i++;
            }
        }

        if (!is_numeric($alias)) {
            $total = CommonModel::getRecordCount(false,false,false, 'Powerpanel\Team\Models\Team');
            $total = $total + 1;
            $this->breadcrumb['title'] = trans('team::template.teamModule.addTeamMember');
            $this->breadcrumb['module'] = trans('team::template.teamModule.manageTeam');
            $this->breadcrumb['url'] = 'powerpanel/team';
            $this->breadcrumb['inner_title'] = trans('team::template.teamModule.addTeamMember');
            $breadcrumb = $this->breadcrumb;
            $data = compact('total', 'breadcrumb', 'imageManager', 'teamSocialLinksOptions');
        } else {
            $id = $alias;
            $team = Team::getRecordById($id);
            if (empty($team)) {
                return redirect()->route('powerpanel.team.add');
            }
            $metaInfo = array(
                'varMetaTitle' => $team->varMetaTitle,
                'varMetaKeyword' => $team->varMetaKeyword,
                'varMetaDescription' => $team->varMetaDescription,
            );
            $this->breadcrumb['title'] = trans('team::template.teamModule.editTeamMember') . ' - ' . $team->varTitle;
            $this->breadcrumb['module'] = trans('team::template.teamModule.manageTeam');
            $this->breadcrumb['url'] = 'powerpanel/team';
            $this->breadcrumb['inner_title'] = trans('team::template.teamModule.editTeamMember') . ' - ' . $team->varTitle;
            $breadcrumb = $this->breadcrumb;
            $data = compact('team', 'metaInfo', 'breadcrumb', 'imageManager', 'teamSocialLinksOptions');
        }
        $data['MyLibrary'] = $this->MyLibrary;
        return view('team::powerpanel.actions', $data);
    }

    /**
     * This method stores team modifications
     * @return  View
     * @since   2017-07-21
     * @author  NetQuick
     */
    public function handlePost(Request $request)
    {
        $data = Request::all();

        $availableSocialLinks = Config::get('Constant.AVAILABLE_SOCIAL_LINKS_FOR_TEAM_MEMBER');
        $teamSocialLinksOptions = array();
        if (!empty($availableSocialLinks)) {
            $socialLinks = unserialize($availableSocialLinks);
            $i = 0;
            foreach ($socialLinks as $links) {
                $teamSocialLinksOptions[$i]['key'] = $links['key'];
                $teamSocialLinksOptions[$i]['label'] = $links['title'];
                $teamSocialLinksOptions[$i]['placeholder'] = $links['placeholder'];
                $i++;
            }
        }

        $messsages = array(
            'display_order.greater_than_zero' => trans('team::template.teamModule.displayGreaterThan'),
            'short_description' => 'required|max:' . (isset($settings) ? $settings->short_desc_length : 400),
            'phone_no.min' => 'Phone number must be at least 6 Digit long',
            'phone_no.max' => 'Phone number must be less then 20 Digit'
        );
        $rules = array(
            'name' => 'required|max:160',
            'display_order' => 'required|greater_than_zero',
            'chrMenuDisplay' => 'required',
            'alias' => 'required'
        );

        if (!empty($data['email'])) {
            $rules['email'] = 'email|max:100';
        }

        if (!empty($data['phone_no'])) {
            $rules['phone_no'] = 'min:6|max:20';
        }

        $socialLink = array();
        if (isset($teamSocialLinksOptions) && !empty($teamSocialLinksOptions)) {
            foreach ($teamSocialLinksOptions as $value) {
                if (isset($data[$value['key']]) && $data[$value['key']] != "") {
                    $socialLink[$value['key']] = $data[$value['key']];
                    $rules[$value['key']] = 'url';
                    $messsages[$value['key'] . '.url'] = 'Enter valid url';
                }
            }
        }
        $socialLink = serialize($socialLink);
        $validator = Validator::make($data, $rules, $messsages);
        if ($validator->passes()) {
            $id = Request::segment(3);
            $actionMessage = trans('team::template.common.oppsSomethingWrong');
            if (is_numeric($id)) {
                #Edit post Handler=======
                if ($data['oldAlias'] != $data['alias']) {
                    Alias::updateAlias($data['oldAlias'], $data['alias']);
                }
                $team = Team::getRecordForLogById($id);

                $updateTeamFields = [
                    'varTitle' => trim($data['name']),
                    'varDepartment' => !empty($data['department']) ? trim($data['department']) : null,
                    'varTagLine' => trim($data['tag_line']),
                    'fkIntImgId' => !empty($data['img_id']) ? $data['img_id'] : null,
                    'txtDescription' => !empty($data['description']) ? $data['description'] : null,
                    'txtShortDescription' => !empty($data['short_description']) ? trim($data['short_description']) : null ,
                    'varEmail' => (!empty($data['email']) ? trim($data['email']) : null),
                    'varPhoneNo' => (!empty($data['phone_no']) ? trim($data['phone_no']) : null),
                    'textAddress' => !empty($data['address']) ? trim($data['address']) : null,
                    'txtSocialLinks' => $socialLink,
                    'varMetaTitle' => trim($data['varMetaTitle']),
                    'varMetaDescription' => trim($data['varMetaDescription']),
                    'chrPublish' => $data['chrMenuDisplay'],
                ];
                $whereConditions = ['id' => $team->id];
                $update = CommonModel::updateRecords($whereConditions, $updateTeamFields,false, 'Powerpanel\Team\Models\Team');

                if ($update) {
                    if (!empty($id)) {
                        self::swap_order_edit($data['display_order'], $team->id);

                        $logArr = MyLibrary::logData($team->id);
                        if (Auth::user()->can('log-advanced')) {
                            $newTeamObj = Team::getRecordForLogById($team->id);
                            $oldRec = $this->recordHistory($team);
                            $newRec = $this->recordHistory($newTeamObj);
                            $logArr['old_val'] = $oldRec;
                            $logArr['new_val'] = $newRec;
                        }

                        $logArr['varTitle'] = trim($data['name']);
                        Log::recordLog($logArr);

                        if (Auth::user()->can('recent-updates-list')) {
                            if (!isset($newTeamObj)) {
                                $newTeamObj = Team::getRecordForLogById($team->id);
                            }
                            $notificationArr = MyLibrary::notificationData($team->id, $newTeamObj);
                            RecentUpdates::setNotification($notificationArr);
                        }
                        self::flushCache();
                        $actionMessage = trans('team::template.teamModule.updateMessage');
                    }
                }
            } else {
                #Add post Handler=======

                $teamArr = [];
                $teamArr['intAliasId'] = MyLibrary::insertAlias($data['alias']);
                $teamArr['varTitle'] = trim($data['name']);
                $teamArr['varDepartment'] = !empty($data['department']) ? trim($data['department']) : null;
                $teamArr['varTagLine'] = trim($data['tag_line']);
                $teamArr['fkIntImgId'] = !empty($data['img_id']) ? $data['img_id'] : null;
                $teamArr['intDisplayOrder'] = self::swap_order_add($data['display_order']);
                $teamArr['txtDescription'] = !empty($data['description']) ? $data['description'] : null;
                $teamArr['txtShortDescription'] = !empty($data['short_description']) ? trim($data['short_description']) : null ;
                $teamArr['varEmail'] = (!empty($data['email']) ? trim($data['email']) : null);
                $teamArr['varPhoneNo'] = (!empty($data['phone_no']) ? trim($data['phone_no']) : null);
                $teamArr['textAddress'] = !empty($data['address']) ? trim($data['address']) : null;
                $teamArr['txtSocialLinks'] = $socialLink;
                $teamArr['chrPublish'] = $data['chrMenuDisplay'];
                $teamArr['varMetaTitle'] = trim($data['varMetaTitle']);
                $teamArr['varMetaDescription'] = trim($data['varMetaDescription']);
                $teamArr['created_at'] = Carbon::now();

                $teamID = CommonModel::addRecord($teamArr, 'Powerpanel\Team\Models\Team');

                if (!empty($teamID)) {
                    $id = $teamID;
                    $newTeamObj = Team::getRecordForLogById($id);
                    $logArr = MyLibrary::logData($id);
                    $logArr['varTitle'] = $newTeamObj->varTitle;
                    Log::recordLog($logArr);
                    if (Auth::user()->can('recent-updates-list')) {
                        $notificationArr = MyLibrary::notificationData($id, $newTeamObj);
                        RecentUpdates::setNotification($notificationArr);
                    }
                    self::flushCache();
                    $actionMessage = trans('team::template.teamModule.addMessage');
                }
            }
            AddImageModelRel::sync(explode(',', $data['img_id']), $id);
            if (!empty($data['saveandexit']) && $data['saveandexit'] == 'saveandexit') {
                return redirect()->route('powerpanel.team.index')->with('message', $actionMessage);
            } else {
                return redirect()->route('powerpanel.team.edit', $id)->with('message', $actionMessage);
            }

        } else {
            return Redirect::back()->withErrors($validator)->withInput();
        }

    }
    /**
     * This method loads team table data on view
     * @return  View
     * @since   2017-07-20
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
        $filterArr['statusFilter'] = !empty(Request::get('customActionName')) ? Request::get('customActionName') : '';
        $filterArr['searchFilter'] = !empty(Request::get('searchValue')) ? Request::get('searchValue') : '';

        $filterArr['iDisplayLength'] = intval(Request::get('length'));
        $filterArr['iDisplayStart'] = intval(Request::get('start'));
        $sEcho = intval(Request::get('draw'));
        $arrResults = Team::getRecordList($filterArr);
        $iTotalRecords = CommonModel::getRecordCount($filterArr, true,false, 'Powerpanel\Team\Models\Team');
        $totalRecords = CommonModel::getTotalRecordCount('Powerpanel\Team\Models\Team');

        $end = $filterArr['iDisplayStart'] + $filterArr['iDisplayLength'];
        $end = $end > $iTotalRecords ? $iTotalRecords : $end;
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
        $arrResults = Team::getBuilderRecordList($filterArr);
        $found = $arrResults->toArray();

        if (!empty($found)) {
            foreach ($arrResults as $key => $value) {
                $rows .= $this->tableDataBuilder($value, false, $filterArr['selected']);
            }
        } else {
            $rows .= '<tr id="not-found"><td colspan="4" align="center">No records found.</td></tr>';
        }
        $iTotalRecords = CommonModel::getTotalRecordCount('Powerpanel\Team\Models\Team', true);
        $records["data"] = $rows;
        $records["found"] = count($found);
        $records["recordsTotal"] = $iTotalRecords;
        return json_encode($records);
    }

    public function tableDataBuilder($value = false, $fcnt = false, $selected = [])
    {
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
        $record .= $imgIcon;
        $record .= '</td>';
        $record .= '<td width="23.7%" align="left">';
        $record .= (!empty($value->varEmail)?$value->varEmail: '-');
        $record .= '</td>';
        $record .= '</tr>';

        return $record;
    }

    public function publish(Request $request)
    {
        $alias = Request::get('alias');
        $val = Request::get('val');
        $update = MyLibrary::setPublishUnpublish($alias, $val, 'Powerpanel\Team\Models\Team');
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
        MyLibrary::swapOrder($order, $exOrder,'Powerpanel\Team\Models\Team');
        self::flushCache();
    }
    /**
     * This method delete multiples Team
     * @return  true/false
     * @since   2017-07-22
     * @author  NetQuick
     */
    public function DeleteRecord(Request $request)
    {
        $data = Request::all('ids');
        $update = MyLibrary::deleteMultipleRecords($data,false,false, 'Powerpanel\Team\Models\Team');
        self::flushCache();
        echo json_encode($update);
        exit;
    }

    /**
     * This method handels swapping of available order record while adding
     * @param      order
     * @return  order
     * @since   2017-07-22
     * @author  NetQuick
     */
    public static function swap_order_add($order = null)
    {
        $response = false;
        if ($order != null) {
            $response = MyLibrary::swapOrderAdd($order,false,false,'Powerpanel\Team\Models\Team');
            self::flushCache();
        }
        return $response;
    }

    /**
     * This method handels swapping of available order record while editing
     * @param      order
     * @return  order
     * @since   2017-07-22
     * @author  NetQuick
     */
    public static function swap_order_edit($order = null, $id = null)
    {
        MyLibrary::swapOrderEdit($order, $id,false,false,'Powerpanel\Team\Models\Team');
        self::flushCache();
    }

    /**
     * This method handels logs History records
     * @param   $data
     * @return  HTML
     * @since   2017-07-27
     * @author  NetQuick
     */
    public function recordHistory($data = false)
    {
        $returnHtml = '';
        $returnHtml .= '<table class="new_table_desing table table-striped table-bordered table-hover">
									<thead>
										<tr>
												<th>' . trans("template.common.title") . '</th>
												<th>' . trans("template.teamModule.department") . '</th>
												<th>' . trans("template.teamModule.tagline") . '</th>
												<th>' . trans("template.common.order") . '</th>
												<th>' . trans("template.common.description") . '</th>
												<th>' . trans("template.common.image") . '</th>
												<th>' . trans("template.common.email") . '</th>
												<th>' . trans("template.teamModule.phone") . '</th>
												<th>' . trans("template.common.address") . '</th>
												<th>' . trans("template.teamModule.facebook") . '</th>
												<th>' . trans("template.teamModule.twitter") . '</th>
												<th>' . trans("template.teamModule.linkedin") . '</th>
												<th>' . trans("template.teamModule.googleplus") . '</th>
												<th>' . trans("template.common.metatitle") . '/th>
												<th>' . trans("template.common.metakeyword") . '</th>
												<th>' . trans("template.common.metadescription") . '</th>
												<th>' . trans("template.common.publish") . '</th>
										</tr>
									</thead>
									<tbody>
										<tr>
											<td>' . $data->varTitle . '</td>
											<td>' . $data->varDepartment . '</td>
											<td>' . $data->varTagLine . '</td>
											<td>' . ($data->intDisplayOrder) . '</td>
											<td>' . $data->txtDescription . '</td>';
        if ($data->fkIntImgId > 0) {
            $returnHtml .= '<td>' . '<img height="50" width="50" src="' . resize_image::resize($data->fkIntImgId) . '" />' . '</td>';
        } else {
            $returnHtml .= '<td>-</td>';
        }
        $returnHtml .= '<td>' . (!empty($data->varEmail) ? $data->varEmail : '-') . '</td>
											<td>' . (!empty($data->varPhoneNo) ? $data->varPhoneNo : '-') . '</td>
											<td>' . $data->textAddress . '</td>
											<td>' . $data->varFacebook . '</td>
											<td>' . $data->varTwitter . '</td>
											<td>' . $data->varLinkedin . '</td>
											<td>' . $data->varGooglePlus . '</td>
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
        $publish_action = '';
        $details = '';
        if (Auth::user()->can('team-edit')) {
            $details .= '<a class="without_bg_icon" title="' . trans("template.common.edit") . '"  href="' . route('powerpanel.team.edit', array('alias' => $value->id)) . '">
				<i class="fa fa-pencil"></i></a>';
        }
        if (Auth::user()->can('team-delete')) {
            $details .= '&nbsp;<a class="without_bg_icon delete" title="' . trans("template.common.delete") . '" data-controller="team" data-alias = "' . $value->id . '"><i class="fa fa-times"></i></a>';
        }

        if (Auth::user()->can('team-publish')) {
            if ($value->chrPublish == 'Y') {
                $publish_action .= '<input data-off-text="No" data-on-text="Yes" class="make-switch publish" class="make-switch publish" data-off-color="info" data-on-color="primary" type="checkbox" data-controller="powerpanel/team" title="' . trans("template.common.publishedRecord") . '" data-value="Unpublish" data-alias="' . $value->id . '">';
            } else {
                $publish_action .= '<input checked="" data-off-text="No" data-on-text="Yes" class="make-switch publish" class="make-switch publish" data-off-color="info" data-on-color="primary" type="checkbox" data-controller="powerpanel/team" title="' . trans("template.common.unpublishedRecord") . '" data-value="Publish" data-alias="' . $value->id . '">';
            }
        }

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
        
        // $orderArrow = '';
        // $orderArrow .= '<span class="pageorderlink">';
        // if ($totalRecords != $value->intDisplayOrder) {
        //     $orderArrow .= '<a href="javascript:;" data-order="' . $value->intDisplayOrder . '" class="moveUp"><i class="fa fa-plus" aria-hidden="true"></i></a> ';
        // }
        // $orderArrow .= $value->intDisplayOrder . ' ';
        // if ($value->intDisplayOrder != 1) {
        //     $orderArrow .= ' <a href="javascript:;" data-order="' . $value->intDisplayOrder . '" class="moveDwn"><i class="fa fa-minus" aria-hidden="true"></i></a>';
        // }
        // $orderArrow .= '</span>';

        $orderArrow = '';
        $dispOrder = $value->intDisplayOrder;
        if ($totalRecords != $value->intDisplayOrder) {
            $orderArrow .= '<a href="javascript:;" data-order="' . $value->intDisplayOrder . '" class="moveUp"><i class="fa fa-arrow-up" aria-hidden="true"></i></a> ';
        }
        $orderArrow .= $dispOrder;
        if ($value->intDisplayOrder != 1) {
            $orderArrow .= ' <a href="javascript:;" data-order="' . $value->intDisplayOrder . '" class="moveDwn"><i class="fa fa-arrow-down" aria-hidden="true"></i></a>';
        }

        $records = array(
            '<input type="checkbox" name="delete" class="chkDelete" value="' . $value->id . '">',
            '<div class="pages_title_div_row"><div class="quick_edit"><a class="without_bg_icon" title="' . trans("template.common.edit") . '" href="' . route('powerpanel.team.edit', array('alias' => $value->id)) . '">' . $value->varTitle . '</a></div></div>',
            $imgIcon,
            //(!empty($value->varDepartment) ? $value->varDepartment : '<span class="glyphicon glyphicon-minus"></span>'),
            (!empty($value->varTagLine) ? $value->varTagLine : '<span class="glyphicon glyphicon-minus"></span>'),
            $orderArrow,
            $publish_action,
            $details,
            $value->intDisplayOrder,
        );
        return $records;
    }

    public static function flushCache()
    {
        Cache::tags('Team')->flush();
    }
}
