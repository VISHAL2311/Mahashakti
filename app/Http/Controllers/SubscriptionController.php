<?php

/**
 * The SubscriptionController class handels subscription functions for front end
 * configuration  process.
 * @package   Netquick powerpanel
 * @license   http://www.opensource.org/licenses/BSD-3-Clause
 * @version   1.00
 * @since     2017-11-10
 * @author    NetQuick
 */

namespace App\Http\Controllers;

use App\Helpers\Email_sender;
use App\Http\Controllers\Controller;
use Powerpanel\NewsletterLead\Models\NewsletterLead;
use Auth;
use Crypt;
use Request;
use Illuminate\Support\Facades\Redirect;
use Validator;
use Config;
use App\Helpers\MyLibrary;
use App\Helpers\time_zone;

class SubscriptionController extends FrontController {

    public function __construct() {
        parent::__construct();
    }

    /**
     * This method handels send subscribe email function
     * @return  View
     * @since   2017-11-10
     * @author  NetQuick
     */
    public function store() {

        time_zone::time_zone();
        $messsages = array(
            'email.unique' => 'This email is already subscribed. Please enter another email address.',
        );

        $data = Request::all();
        if (isset($data['email'])) {
            $data['email'] = trim($data['email']);
        }

        $rules = array(
            'email' => 'required|email|regex:[[_A-Za-z0-9-]+(\.[_A-Za-z0-9-]+)*@[A-Za-z0-9-]+(\.[A-Za-z0-9-]+)*(\.[A-Za-z]{2,4})]',
        );
        $validator = Validator::make($data, $rules, $messsages);

        if ($validator->passes()) {
            $email = (strtolower($data['email']));
            $emailExistCheck = NewsletterLead::checkSubscriberExist(Mylibrary::getEncryptedString($email));
            if (!$emailExistCheck) {
                $subscribeArr = [];
                $subscribeArr['varEmail'] = Mylibrary::getEncryptedString($data['email']);
                $subscribeArr['varIpAddress'] = MyLibrary::get_client_ip();
                $subscribeArr['created_at'] = date('Y-m-d h:i:s');
                
                $subscribe = NewsletterLead::insertGetId($subscribeArr);
                $data = NewsletterLead::getRecords()->publish()->deleted()->checkRecordId($subscribe);
                if ($data->count() > 0) {
                    $data = $data->first()->toArray();
                    $id = Crypt::encrypt($data['id']);
                    Email_sender::newsletter($data, $id);
                    echo json_encode(['success' => 'Thank you, the confirmation request email sent to your entered address.']);
                }
            } else {
                echo json_encode(['error' => ['This email is already subscribed. Please enter another email address.']]);
            }
        } else {
            echo json_encode(['error' => $validator->errors()->all()]);
        }
    }

    /**
     * This method handels subscribe function
     * @return  View
     * @since   2017-11-10
     * @author  NetQuick
     */
    public function subscribe() {
        $linkID = Request::segment(4);
        if (strlen($linkID) > 0) {
            $reqId = Crypt::decrypt($linkID);
            $subscriber = NewsletterLead::getRecords()->publish()->deleted()->checkRecordId($reqId)->where('chrSubscribed', 'N')->first();
            if (!empty($subscriber)) {
                NewsletterLead::where('id', '=', $reqId)->update(['chrSubscribed' => 'Y']);
                $id = Crypt::encrypt($reqId);
                Email_sender::newsletterSubscribed($subscriber, $id);
                Email_sender::newsletterSubscribed_admin($subscriber, $id);
                return redirect('thank-you')->with(['form_submit' => true, 'message' => 'Your subscription has been confirmed. We will keep you posted.']);
            } else {
                return redirect('failed')->with(['form_submit' => true, 'message' => 'The link you are trying to access is no longer exist.']);
            }
        }
    }

    /**
     * This method handels un-subscribe function     * @return  View
     * @since   2017-11-10
     * @author  NetQuick
     */
    public function unsubscribe() {
        $linkID = Request::segment(4);
        if (strlen($linkID) > 0) {
            $reqId = Crypt::decrypt($linkID);
            $subscriber = NewsletterLead::getRecords()->publish()->deleted()->checkRecordId($reqId)->count();
            if ($subscriber > 0) {
                NewsletterLead::where('id', '=', $reqId)->delete();
                return redirect('thank-you')->with(['form_submit' => true, 'message' => 'You have been successfully unsubscribed from our newsletter subscription list.']);
            } else {
                return redirect('failed')->with(['form_submit' => true, 'message' => 'The link you are trying to access is no longer exist.']);
            }
        }
    }

}