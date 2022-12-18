<?php

namespace Powerpanel\ContactInfo\Controllers\Powerpanel;

use App\CommonModel;
use App\Helpers\FrontPageContent_Shield;
use App\Helpers\MyLibrary;
use App\Helpers\resize_image;
use App\Http\Controllers\PowerpanelController;
use App\Log;
use App\RecentUpdates;
use Auth;
use Cache;
use Config;
use Illuminate\Support\Facades\Redirect;
use Powerpanel\ContactInfo\Models\ContactInfo;
use Request;
use Validator;

class ContactInfoController extends PowerpanelController
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
     * This method handels view of listing
     * @return  view
     * @since   2017-08-02
     * @author  NetQuick
     */
    public function index()
    {
        $total = CommonModel::getRecordCount(false, false, false, 'Powerpanel\ContactInfo\Models\ContactInfo');
        $this->breadcrumb['title'] = trans('contactinfo::template.contactModule.managecontacts');
        return view('contactinfo::powerpanel.list', ['total' => $total, 'breadcrumb' => $this->breadcrumb]);
    }

    /**
     * This method loads contactInfo edit view
     * @param   Alias of record
     * @return  View
     * @since   2017-11-10
     * @author  NetQuick
     */
    public function edit($id = false)
    {
        $imageManager = true;
        if (!is_numeric($id)) {
            $total = CommonModel::getRecordCount(false, false, false, 'Powerpanel\ContactInfo\Models\ContactInfo');
            $total = $total + 1;
            $this->breadcrumb['title'] = trans('contactinfo::template.contactModule.addnewcontact');
            $this->breadcrumb['module'] = trans('contactinfo::template.contactModule.managecontacts');
            $this->breadcrumb['url'] = 'powerpanel/contact-info';
            $this->breadcrumb['inner_title'] = trans('contactinfo::template.contactModule.addnewcontact');
            $breadcrumb = $this->breadcrumb;
            $data = ['total' => $total, 'breadcrumb' => $this->breadcrumb, 'imageManager' => $imageManager];
        } else {
            $contactInfo = ContactInfo::getRecordById($id);
            if (empty($contactInfo)) {
                return redirect()->route('powerpanel.contact-info.add');
            }
            $this->breadcrumb['title'] = trans('contactinfo::template.common.edit') . ' - ' . $contactInfo->varTitle;
            $this->breadcrumb['module'] = trans('contactinfo::template.contactModule.managecontacts');
            $this->breadcrumb['url'] = 'powerpanel/contact-info';
            $this->breadcrumb['inner_title'] = trans('contactinfo::template.common.edit') . ' - ' . $contactInfo->varTitle;
            $breadcrumb = $this->breadcrumb;
            $data = ['contactInfo' => $contactInfo, 'breadcrumb' => $this->breadcrumb, 'imageManager' => $imageManager];
        }
        return view('contactinfo::powerpanel.actions', $data);
    }

    /**
     * This method handels post of edit
     * @return  view
     * @since   2017-08-02
     * @author  NetQuick
     */
    public function handlePost(Request $request)
    {
        $postArr = Request::all();
        $postArr['order'] = 1;

        $rules = $this->serverSideValidationRules();
        $actionMessage = 'Opps... Something went wrong!';
        $validator = Validator::make($postArr, $rules);

        if ($validator->passes()) {
            foreach ($postArr['phone_no'] as $key => $value) {
                if (is_null($value) || $value == '') {
                    unset($postArr['phone_no'][$key]);
                }

            }
            $contactInfoArr['varTitle'] = trim($postArr['name']);
            $contactInfoArr['varEmail'] = serialize($postArr['email']);
            $contactInfoArr['varPhoneNo'] = serialize($postArr['phone_no']);
            $contactInfoArr['varMobileNo'] = $postArr['mobile_no'];
            $contactInfoArr['varFax'] = trim($postArr['fax']);
            $contactInfoArr['txtDescription'] = trim($postArr['description']);
            $contactInfoArr['txtAddress'] = trim($postArr['address']);
            $contactInfoArr['mailingaddress'] = (isset($postArr['mailingaddress'])) ? trim($postArr['mailingaddress']) : null;
            $contactInfoArr['chrIsPrimary'] = isset($postArr['primary']) ? $postArr['primary'] : 'Y';
            $contactInfoArr['chrPublish'] = isset($postArr['chrMenuDisplay']) ? $postArr['chrMenuDisplay'] : 'Y';
            $contactInfoArr['created_at'] = date('Y-m-d H:i:s');
            $contactInfoArr['updated_at'] = date('Y-m-d H:i:s');

            $id = Request::segment(3);
            if (is_numeric($id)) { #Edit post Handler=======
            $contactInfo = ContactInfo::getRecordForLogById($id);
                $whereConditions = ['id' => $id];
                $update = CommonModel::updateRecords($whereConditions, $contactInfoArr, false, 'Powerpanel\ContactInfo\Models\ContactInfo');
                if ($update) {
                    if (!empty($id)) {

                        self::swap_order_edit($postArr['order'], $id);

                        $logArr = MyLibrary::logData($id);
                        if (Auth::user()->can('log-advanced')) {
                            $newContactInfo = ContactInfo::getRecordForLogById($id);
                            $oldRec = $this->recordHistory($contactInfo);
                            $newRec = $this->newrecordHistory($contactInfo, $newContactInfo);
//                            $newRec = $this->recordHistory($newContactInfo);
                            $logArr['old_val'] = $oldRec;
                            $logArr['new_val'] = $newRec;
                        }
                        $logArr['varTitle'] = trim($postArr['name']);
                        Log::recordLog($logArr);
                        if (Auth::user()->can('recent-updates-list')) {
                            if (!isset($newContactInfo)) {
                                $newContactInfo = ContactInfo::getRecordForLogById($id);
                            }
                            $notificationArr = MyLibrary::notificationData($id, $newContactInfo);
                            RecentUpdates::setNotification($notificationArr);
                        }
                    }
                    self::flushCache();
                    $actionMessage = 'Contact has been successfully updated.';
                }
            } else { #Add post Handler=======
            $contactInfoArr['intDisplayOrder'] = self::swap_order_add($postArr['order']);
                $contactInfoID = CommonModel::addRecord($contactInfoArr, 'Powerpanel\ContactInfo\Models\ContactInfo');
                if (!empty($contactInfoID)) {
                    $id = $contactInfoID;
                    $newContactObj = ContactInfo::getRecordForLogById($id);

                    $logArr = MyLibrary::logData($id);
                    $logArr['varTitle'] = $newContactObj->varTitle;
                    Log::recordLog($logArr);
                    if (Auth::user()->can('recent-updates-list')) {
                        $notificationArr = MyLibrary::notificationData($id, $newContactObj);
                        RecentUpdates::setNotification($notificationArr);
                    }
                    self::flushCache();
                    $actionMessage = 'Contact has been successfully added.';
                }
            }

//                AddImageModelRel::sync(explode(',', $postArr['img_id']), $id);

            if ((!empty(Request::get('saveandexit')) && Request::get('saveandexit') == 'saveandexit')) {
                return redirect()->route('powerpanel.contact-info.edit',$id)->with('message', $actionMessage);
            } else {
                return redirect()->route('powerpanel.contact-info.edit', $id)->with('message', $actionMessage);
            }
        } else {
            return Redirect::back()->withErrors($validator)->withInput();
        }
    }

    /**
     * This method handels listing
     * @return  view
     * @since   2017-08-02
     * @author  NetQuick
     */
    public function get_list()
    {
        $filterArr = [];
        $records = [];
        $records["data"] = [];

        $filterArr['orderColumnNo'] = (!empty(Request::input('order')[0]['column']) ? Request::input('order')[0]['column'] : '');
        $filterArr['orderByFieldName'] = (!empty(Request::input('columns')[$filterArr['orderColumnNo']]['name']) ? Request::input('columns')[$filterArr['orderColumnNo']]['name'] : '');
        $filterArr['orderTypeAscOrDesc'] = (!empty(Request::input('order')[0]['dir']) ? Request::input('order')[0]['dir'] : '');
        $filterArr['statusFilter'] = !empty(Request::input('statusValue')) ? Request::input('statusValue') : '';
        $filterArr['searchFilter'] = !empty(Request::input('searchValue')) ? Request::input('searchValue') : '';
        $filterArr['iDisplayLength'] = intval(Request::input('length'));
        $filterArr['iDisplayStart'] = intval(Request::input('start'));

        $sEcho = intval(Request::input('draw'));
        $arrResults = ContactInfo::getRecordList($filterArr);
        $iTotalRecords = CommonModel::getRecordCount($filterArr, true, false, 'Powerpanel\ContactInfo\Models\ContactInfo');

        $end = $filterArr['iDisplayStart'] + $filterArr['iDisplayLength'];
        $end = $end > $iTotalRecords ? $iTotalRecords : $end;

        if (!empty($arrResults)) {
            foreach ($arrResults as $key => $value) {
                $records['data'][] = $this->tableData($value);
            }
        }

        if (!empty(Request::input("customActionType")) && Request::input("customActionType") == "group_action") {
            $records["customActionStatus"] = "OK"; // pass custom message(useful for getting status of group actions)
        }

        $records["draw"] = $sEcho;
        $records["recordsTotal"] = $iTotalRecords;
        $records["recordsFiltered"] = $iTotalRecords;
        echo json_encode($records);
        exit;
    }

    /**
     * This method handels Publish/Unpublish
     * @return  view
     * @since   2017-08-02
     * @author  NetQuick
     */
    public function publish(Request $request)
    {
        $id = (int) Request::input('alias');
        $val = Request::get('val');
        $update = MyLibrary::setPublishUnpublish($id, $val, 'Powerpanel\ContactInfo\Models\ContactInfo');
        self::flushCache();
        echo json_encode($update);
        exit;
    }

    /**
     * This method reorders position
     * @return  Banner index view data
     * @since   2016-10-26
     * @author  NetQuick
     */
    public function reorder()
    {
        $order = Request::input('order');
        $exOrder = Request::input('exOrder');
        MyLibrary::swapOrder($order, $exOrder);
        self::flushCache();
    }

    /**
     * This method destroys multiples records
     * @return  true/false
     * @since   03-08-2017
     * @author  NetQuick
     */
    public function DeleteRecord(Request $request)
    {
        $data = Request::all('ids');
        $update = MyLibrary::deleteMultipleRecords($data, false, false, 'Powerpanel\ContactInfo\Models\ContactInfo');
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
            $response = MyLibrary::swapOrderAdd($order, false, false, 'Powerpanel\ContactInfo\Models\ContactInfo');
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
        MyLibrary::swapOrderEdit($order, $id, false, false, 'Powerpanel\ContactInfo\Models\ContactInfo');
        self::flushCache();
    }

    /**
     * This method datatable grid data
     * @return  array
     * @since   03-08-2017
     * @author  NetQuick
     */
    public function tableData($value = false)
    {
        $actions = '';
        $publish_action = '';
        $image = '<div class="text-center">';
        if (!empty($value->fkIntImgId)) {
            $image .= '<a href="' . resize_image::resize($value->fkIntImgId) . '" class="fancybox-buttons" data-rel="fancybox-buttons">';
            $image .= '<img height="30" width="30" title="' . preg_replace('/[^A-Za-z0-9\-]/', '-', $value->varTitle) . '" src="' . resize_image::resize($value->fkIntImgId, 50, 50) . '"/>';
            $image .= '</a>';
        } else {
            $image .= '<span class="glyphicon glyphicon-minus"></span>';
        }
        $image .= '</div>';

        if (Auth::user()->can('contact-info-edit')) {
            $actions .= '<a class="" title="Edit" href="' . route('powerpanel.contact-info.edit', array('alias' => $value->id)) . '">
				<i class="fa fa-pencil"></i></a>';
        }

        if (Auth::user()->can('contact-info-delete')) {
            $actions .= '&nbsp;<a class=" delete" title="Delete" data-controller="contact-info" data-alias = "' . $value->id . '"><i class="fa fa-times"></i></a>'; 
        }

        if (Auth::user()->can('contact-info-publish')) {
            if ($value->chrPublish == 'Y') {
                $publish_action .= '<input data-off-text="No" data-on-text="Yes" class="make-switch publish" class="make-switch publish" data-off-color="info" data-on-color="primary" type="checkbox" data-controller="powerpanel/contact-info" title="' . trans("template.common.publishedRecord") . '" data-value="Unpublish" data-alias="' . $value->id . '">';
            } else {
                $publish_action .= '<input checked="" data-off-text="No" data-on-text="Yes" class="make-switch publish" class="make-switch publish" data-off-color="info" data-on-color="primary" type="checkbox" data-controller="powerpanel/contact-info" title="' . trans("template.common.unpublishedRecord") . '" data-value="Publish" data-alias="' . $value->id . '">';
            }
        }

        $details = '';
        $details .= '<a href="javascript:void(0)" class="highslide-active-anchor" onclick="return hs.htmlExpand(this,{width:300,headingText:\'Mailing Address\',wrapperClassName:\'titlebar\',showCredits:false});"><span aria-hidden="true" class="icon-envelope"></span></a>';
        $details .= '<div class="highslide-maincontent">' . nl2br($value->txtAddress) . '</div>';

        /* if ($value->chrIsPrimary == 'Y') {
        $primary = 'Yes';
        } else {
        $primary = 'No';
        } */

        if (Auth::user()->can('contact-info-edit')) {
            $title = '<a class="" title="Edit" href="' . route('powerpanel.contact-info.edit', array('alias' => $value->id)) . '">' . ucwords($value->varTitle) . '</a>';
        } else {
            $title = ucwords($value->varTitle);
        }
        $emails = unserialize($value->varEmail);
        $records = array(
            // '<input type = "checkbox" name = "delete" class = "chkDelete" value = "' . $value->id . '">',
            '',
            '<div class="pages_title_div_row">' . $title . '</div>',
            count($emails) > 1 ? implode('<br/>', $emails) : $emails[0],
            $details,
            date('' . Config::get('Constant.DEFAULT_DATE_FORMAT') . ' ' . Config::get('Constant.DEFAULT_TIME_FORMAT') . '', strtotime($value->created_at)),
            $actions,
            $value->varTitle,
        );
        return $records;
    }

    /**
     * This method handle severside validation rules
     * @return  array
     * @since   03-08-2017
     * @author  NetQuick
     */
    public function serverSideValidationRules()
    {
        $rules = array(
            'name' => 'required|max:255|handle_xss|no_url',
            'order' => 'required|greater_than_zero|handle_xss|no_url',
            'mobile_no' => 'required|handle_xss|no_url|max:20',
            'address' => 'required|handle_xss|no_url',
            'fax' => 'handle_xss|no_url',
            // 'mailingaddress' => 'required|handle_xss|no_url',
        );
        return $rules;
    }

    /**
     * This method handle notification old record
     * @return  array
     * @since   2016-10-25
     * @author  NetQuick
     */
    public function recordHistory($data = false)
    {
        $emails = implode('<br/>', unserialize($data->varEmail));
        $phones = implode('<br/>', unserialize($data->varPhoneNo));
        if (isset($data->varFax) && !empty($data->varFax)) {
            $varFax = $data->varFax;
        } else {
            $varFax = 'N/A';
        }
        if (isset($data->txtDescription) && $data->txtDescription != '') {
            $desc = FrontPageContent_Shield::renderBuilder($data->txtDescription);
            if (isset($desc['response']) && !empty($desc['response'])) {
                $desc = $desc['response'];
            } else {
                $desc = '---';
            }
        } else {
            $desc = '---';
        }
        $returnHtml = '';
        $returnHtml .= '<table class="new_table_desing table table-striped table-bordered table-hover">
			<thead>
				<tr>
					<th>' . trans("template.common.title") . '</th>
					<th>' . trans("template.common.email") . '</th>
					<th>' . trans("template.common.phoneno") . '</th>
					<th>Fax</th>
					<th>Working Hours</th>
					<th>' . trans("template.common.address") . '</th>
					<th> Mailing Address </th>
					<th>' . trans("template.common.publish") . '</th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td>' . $data->varTitle . '</td>
					<td>' . $emails . '</td>
					<td>' . $phones . '</td>
					<td>' . $varFax . '</td>
					<td>' . $desc . '</td>
					<td>' . $data->txtAddress . '</td>
					<td>' . $data->mailingaddress . '</td>
					<td>' . $data->chrPublish . '</td>
				</tr>
			</tbody>
		</table>';
        return $returnHtml;
    }

    public function newrecordHistory($data = false, $newdata = false)
    {
        if ($data->varTitle != $newdata->varTitle) {
            $titlecolor = 'style="background-color:#f5efb7"';
        } else {
            $titlecolor = '';
        }
        $newemails = implode('<br/>', unserialize($newdata->varEmail));
        $emails = implode('<br/>', unserialize($data->varEmail));
        if ($emails != $newemails) {
            $emailcolor = 'style="background-color:#f5efb7"';
        } else {
            $emailcolor = '';
        }
        $newphone = implode('<br/>', unserialize($newdata->varPhoneNo));
        $phone = implode('<br/>', unserialize($data->varPhoneNo));
        if ($phone != $newphone) {
            $phonecolor = 'style="background-color:#f5efb7"';
        } else {
            $phonecolor = '';
        }

        if ($data->varFax != $newdata->varFax) {
            $varFaxcolor = 'style="background-color:#f5efb7"';
        } else {
            $varFaxcolor = '';
        }

        if ($data->txtDescription != $newdata->txtDescription) {
            $txtDescriptioncolor = 'style="background-color:#f5efb7"';
        } else {
            $txtDescriptioncolor = '';
        }

        if ($data->txtAddress != $newdata->txtAddress) {
            $txtAddresscolor = 'style="background-color:#f5efb7"';
        } else {
            $txtAddresscolor = '';
        }
        if ($data->mailingaddress != $newdata->mailingaddress) {
            $mailingaddresscolor = 'style="background-color:#f5efb7"';
        } else {
            $mailingaddresscolor = '';
        }
        if ($data->chrPublish != $newdata->chrPublish) {
            $Publishcolor = 'style="background-color:#f5efb7"';
        } else {
            $Publishcolor = '';
        }
        if (isset($newdata->varFax) && !empty($newdata->varFax)) {
            $fax = $newdata->varFax;
        } else {
            $fax = "N/A";
        }
        if (isset($newdata->txtAddress) && !empty($newdata->txtAddress)) {
            $txtAddress = $newdata->txtAddress;
        } else {
            $txtAddress = "N/A";
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

        $returnHtml = '';
        $returnHtml .= '<table class = "new_table_desing table table-striped table-bordered table-hover">
				<thead>
				<tr>
				<th align="center">' . trans('contactinfo::template.common.title') . '</th>
				<th align="center">Email</th>
				<th align="center">Phone</th>
                                <th align="center">Fax</th>
                                <th align="center">Working Hours</th>
                                <th align="center">Address</th>
                                <th align="center">Mailing Address</th>
				<th align="center">' . trans('contactinfo::template.common.publish') . '</th>
				</tr>
				</thead>
				<tbody>
				<tr>
				<td align="center" ' . $titlecolor . '>' . stripslashes($newdata->varTitle) . '</td>
				<td align="center" ' . $emailcolor . '>' . $newemails . '</td>
				<td align="center" ' . $phonecolor . '>' . $newphone . '</td>
                                <td align="center" ' . $varFaxcolor . '>' . $fax . '</td>
                                <td align="center" ' . $txtDescriptioncolor . '>' . $desc . '</td>
                                <td align="center" ' . $txtAddresscolor . '>' . $txtAddress . '</td>
                                <td align="center" ' . $mailingaddresscolor . '>' . $newdata->mailingaddress . '</td>
				<td align="center" ' . $Publishcolor . '>' . $newdata->chrPublish . '</td>
				</tr>
				</tbody>
				</table>';
        return $returnHtml;
    }

    public static function flushCache()
    {
        Cache::tags('ContactInfo')->flush();
    }

}
