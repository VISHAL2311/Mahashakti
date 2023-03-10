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
use Vendor\Facades\Input;
use Illuminate\Support\Facades\Redirect;
use Request;
use Validator;
use Config;
use App\Helpers\MyLibrary;
use App\Helpers\time_zone;


class SubscriptionController extends FrontController
{
		public function __construct()
		{
				parent::__construct();
		}
		/**
		 * This method handels send subscribe email function
		 * @return  View
		 * @since   2017-11-10
		 * @author  NetQuick
		 */
		public function store()
		{
				time_zone::time_zone();
				$messsages = array(
						'email.unique' => 'This email is already subscribed. Please enter another email address.',
				);
				
				$rules = array(
					'email' => 'required|email|regex:[[_A-Za-z0-9-]+(\.[_A-Za-z0-9-]+)*@[A-Za-z0-9-]+(\.[A-Za-z0-9-]+)*(\.[A-Za-z]{2,4})]',
				);
				$data = Request::all();
				if (isset($data['email'])) {
						$data['email'] = trim($data['email']);
				}
				$validator = Validator::make($data, $rules, $messsages);

				if ($validator->passes()) 
				{
						$Email = trim($data['email']);
						$emailExistCheck = NewsletterLead::checkSubscriberExist(Mylibrary::getEncryptedString($Email));
						$emailUnSubscriberCheck = NewsletterLead::checkUnSubscriberExist(Mylibrary::getEncryptedString($Email));
						$num_str = sprintf("%06d", mt_rand(1, 999999));
						if(empty($emailExistCheck))
						{		
								$subscribeArr = [];
								if (!$emailUnSubscriberCheck) {
									$subscribeArr['varEmail'] = Mylibrary::getEncryptedString($Email,true);
									if (isset($data['name'])) {
											$subscribeArr['varName'] = $data['name'];
									}
									$subscribeArr['VarToken'] = $num_str;
									$subscribeArr['varIpAddress'] = MyLibrary::get_client_ip();
									$subscribeArr['created_at'] = date('Y-m-d h:i:s');
									$subscribe  = NewsletterLead::insertGetId($subscribeArr);
									$data = NewsletterLead::getRecords()->publish()->deleted()->checkRecordId($subscribe);
								}else{
									$subscribe = NewsletterLead::where('varEmail', '=', Mylibrary::getEncryptedString($Email, true))
									->publish()
									->deleted()
									->update(['VarToken' => $num_str, 'varIpAddress' => MyLibrary::get_client_ip(), 'updated_at' => date('Y-m-d h:i:s')]);
									$data = NewsletterLead::getRecords()->publish()->deleted()->where('varEmail', '=', Mylibrary::getEncryptedString($Email, true));
								}

								if ($data->count() > 0) 
								{
										$data = $data->first()->toArray();
										$id   = Crypt::encrypt($data['id']);
								// 		Email_sender::newsletter($data, $id);
										echo json_encode(['success' => 'Thank you, the confirmation request email sent to your entered address.']);
								}     
						}else{
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
		public function subscribe()
		{
				$linkID = Request::segment(4);
				$Token = Request::segment(5);
				if (strlen($linkID) > 0 && strlen($Token) > 0) {
						$reqId      = Crypt::decrypt($linkID);
						$Tokencheck = NewsletterLead::getRecords()->publish()->deleted()->checkToken($reqId, $Token);
						$subscriber = NewsletterLead::getRecords()->publish()->deleted()->checkRecordId($reqId);
						if ($Tokencheck->count() > 0) {
							if ($subscriber->count() > 0) {
									$num_str = sprintf("%06d", mt_rand(1, 999999));
									$id = Crypt::encrypt($reqId);
										 NewsletterLead::where('id', '=', $reqId)->update(['VarToken' => $num_str]);
										Email_sender::newsletterSubscribed($subscriber->first(), $id);
										Email_sender::newsletterSubscribed_admin($subscriber->first(), $id);
										NewsletterLead::where('id', '=', $reqId)->update(['chrSubscribed' => 'Y']);
										return redirect('/news-letter/thankyou')->with(['form_submit' => true, 'message' => 'Your subscription has been confirmed. We will keep you posted.']);
							}else{
								return redirect('/news-letter/thankyou')->with(['form_submit' => true, 'message' => 'This email is already subscribed from our newsletter subscription list.']);
							}
						}else {
								return redirect('/news-letter/failed')->with(['form_submit' => true, 'message' => 'The link you are trying to access is no longer exist.']);
						}
				}
		}
		/**
		 * This method handels un-subscribe function     * @return  View
		 * @since   2017-11-10
		 * @author  NetQuick
		 */
		public function unsubscribe()
		{
				$linkID = Request::segment(4);
				$Token = Request::segment(5);
				if (strlen($linkID) > 0 && strlen($Token) > 0) {
						$reqId      = Crypt::decrypt($linkID);
						$Tokencheck = NewsletterLead::getRecords()->publish()->deleted()->checkToken($reqId, $Token);
						$subscriber = NewsletterLead::getRecords()->publish()->deleted()->CheckRecordId_unsubscribe($reqId);
						if ($Tokencheck->count() > 0) {
							if ($subscriber->count() > 0) {
								$num_str = sprintf("%06d", mt_rand(1, 999999));
								$id = Crypt::encrypt($reqId);
								 Email_sender::newsletterUNSubscribed_admin($subscriber->first(), $id);
									NewsletterLead::where('id', '=', $reqId)->update(['chrSubscribed' => 'N', 'VarToken' => $num_str]);
									return redirect('/news-letter/thankyou')->with(['form_submit' => true, 'message' => 'You have been successfully unsubscribed from our newsletter subscription list.']);
							}else{
								return redirect('/news-letter/thankyou')->with(['form_submit' => true, 'message' => 'You have already unsubscribed from our newsletter subscription list.']);
							}
						}else{
							return redirect('/news-letter/failed')->with(['form_submit' => true, 'message' => 'The link you are trying to access is no longer exist.']);
						}
				}
		}
}
