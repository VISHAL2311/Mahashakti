<?php
namespace Powerpanel\GetaEstimateLead\Controllers;

use App\Http\Controllers\FrontController;
use Powerpanel\ContactInfo\Models\ContactInfo;
use Powerpanel\GetaEstimateLead\Models\GetaEstimateLead;
use App\Helpers\Email_sender;
use App\Helpers\MyLibrary;
use App\NewsletterLead;
use App\Rules\ValidateBadWord;
use App\Rules\ValidRecaptcha;
use Config;
use Crypt;
use File;
use Illuminate\Support\Facades\Redirect;
use Powerpanel\Services\Models\Services;
use Powerpanel\Testimonial\Models\Testimonial;
use Powerpanel\StaticBlocks\Models\StaticBlocks;
use Request;
use Validator;

class GetaEstimateLeadController extends FrontController
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * This method loads Contactus list view
     * @return  View
     * @since   2020-01-17
     * @author  NetQuick
     */
    public function create()
    {
        $services = Services::getFrontServicesDropdownList();
        $deviceType = Config::get('Constant.DEVICE');
        $contacts = ContactInfo::getContactList();
        $data = ['contact_info' => $contacts,
            'breadcrumb' => $this->breadcrumb,
                 'services' => $services,
            'deviceType' => $deviceType];

        return view('getaestimatelead::frontview.get-a-estimate', $data);
    }

    /**
     * This method stores Contactus leads
     * @param   NA
     * @return  Redirection to Thank You page
     * @since   2020-01-17
     * @author  NetQuick
     */
    public function store()
    {
        $data = Request::all();
        $messsages = array(
            'first_name.required' => 'Please enter name.',
            'first_name.handle_xss' => 'Please enter valid input.',
            'first_name.no_url' => 'URL is not allowed.',
            'user_message.handle_xss' => 'Please enter valid input.',
            'user_message.valid_input' => 'Please enter valid input.',
            'user_message.no_url' => 'URL is not allowed.',
            'contact_email.required' => 'Please enter email address.',
            'contact_email.email' => 'Please enter valid email address.',
//            'phone_number.required' => 'Please enter phone.',
            'phone_number.min'=> 'Please enter at least 6 digits.',
            'phone_number.max'=> 'You reach the maximum limit.',
            'phone_number.no_url'=> 'URL is not allowed.',
            'phone_number.handle_xss' => 'Please enter valid input.',
            'services.required' => "Please select services.",
            // 'g-recaptcha-response.required' => "Please select I'm not a robot.",
        );

        $rules = array(
            'first_name' => ['required', 'handle_xss', 'no_url', new ValidateBadWord],
            'contact_email' => 'required|email',
            'user_message' => ['handle_xss', 'no_url', new ValidateBadWord],
            'phone_number' => 'min:6|max:20|handle_xss|no_url',
            'services' => ['required'],
        );

    //    $rules['g-recaptcha-response'] = ['required', new ValidRecaptcha];

        $validator = Validator::make($data, $rules, $messsages);
        if ($validator->passes()) {
            $requestaquote_lead = new GetaEstimateLead;
            $requestaquote_lead->varName = strip_tags($data['first_name']);
            $requestaquote_lead->fkIntServiceId = $data['services'];
            $requestaquote_lead->varEmail = MyLibrary::getEncryptedString($data['contact_email']);
            if (isset($data['phone_number'])) {
                $requestaquote_lead->varPhoneNo = MyLibrary::getEncryptedString($data['phone_number']);
            } else {
                $requestaquote_lead->varPhoneNo = '';
            }
            if (isset($data['user_message'])) {
                $requestaquote_lead->txtUserMessage = MyLibrary::getEncryptedString(strip_tags($data['user_message']));
            } else {
                $requestaquote_lead->txtUserMessage = '';
            }
            $requestaquote_lead->varIpAddress = MyLibrary::get_client_ip();
            $requestaquote_lead->save();
            
            /*Start this code for message*/
            if (!empty($requestaquote_lead->id)) {
                $recordID = $requestaquote_lead->id;
                // Email_sender::getaEstimate($data, $requestaquote_lead->id);

                if (Request::ajax()) {
                    return json_encode(['success' => 'Thank you for showing interest in our service. We have received your request and someone from our staff will contact you shortly.']);
                } else {
                    return redirect()->route('thank-you')->with(['form_submit' => true, 'message' => 'Thank you for showing interest in our service. We have received your request and someone from our staff will contact you shortly.']);
                }

            } else {
                return redirect('/');
            }

        } else {

            //return GetaEstimateLead form with errors
            if (!empty($data['back_url'])) {
                return redirect($data['back_url'] . '#contact_form')->withErrors($validator)->withInput();
            } else {
                return Redirect::route('get-a-estimate')->withErrors($validator)->withInput();
            }

        }
    }
}
