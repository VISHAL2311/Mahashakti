<?php

namespace Powerpanel\ContactUsLead\Controllers;

use App\Http\Controllers\FrontController;
use Powerpanel\ContactInfo\Models\ContactInfo;
use Powerpanel\ContactUsLead\Models\ContactLead;
use App\Helpers\Email_sender;
use App\Helpers\MyLibrary;
use App\NewsletterLead;
use App\Http\Traits\slug;
use Powerpanel\CmsPage\Models\CmsPage;
use App\Rules\ValidateBadWord;
use App\Rules\ValidRecaptcha;
use Config;
use Crypt;
use App\Helpers\FrontPageContent_Shield;
use File;
use Illuminate\Support\Facades\Redirect;
use Request;
use Validator;
use Powerpanel\Services\Models\Services;
use Cookie;
use Session;

class ContactleadController extends FrontController
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
        $data = array();
        $pagename = Request::segment(1);
        if (is_numeric($pagename) && (int) $pagename > 0) {
            $aliasId = $pagename;
        } else {
            $aliasId = slug::resolve_alias($pagename);
        }

        if (null !== Request::segment(2) && Request::segment(2) != 'preview') {
            if (is_numeric(Request::segment(2))) {
                $cmsPageId = Request::segment(2);
                $pageContent = CmsPage::getPageByPageId($cmsPageId, false);
            } elseif (Request::segment(2) == 'print') {
                $pageContent = CmsPage::getPageContentByPageAlias($aliasId);
            }
        } elseif (is_numeric($aliasId)) {
            $pageContent = CmsPage::getPageContentByPageAlias($aliasId);
            if (!isset($pageContent->id)) {
                $pageContent = CmsPage::getPageByPageId($aliasId, false);
            }
        }
        if (!isset($pageContent->id)) {
            abort('404');
        }

        $CONTENT = ' <h2 class="no_record coming_soon_rcd"> Coming Soon</h2>';
        if (!empty($pageContent->txtDescription)) {
            $CONTENT = $pageContent->txtDescription;
        }

        // Start CMS PAGE Front Private, Password Prottected Code

        $pageContentcms = CmsPage::getPageContentByPageAlias($aliasId);
        if (isset(auth()->user()->id)) {
            $user_id = auth()->user()->id;
        } else {
            $user_id = '';
        }

        $data['PageData'] = '';
        if (isset($pageContentcms) && $pageContentcms->chrPageActive == 'PR') {
            if ($pageContentcms->UserID == $user_id) {
                if (isset($pageContent->txtDescription) && !empty($pageContent->txtDescription)) {
                    $data['PageData'] = FrontPageContent_Shield::renderBuilder($pageContent);
                }
            } else {
                return redirect(url('/'));
            }
        } else if (isset($pageContentcms) && $pageContentcms->chrPageActive == 'PP') {
            $data['PassPropage'] = 'PP';
            $data['Pageid'] = $pageContentcms->id;
        } else {
            if (isset($pageContent->txtDescription) && !empty($pageContent->txtDescription)) {
                $data['PageData'] = FrontPageContent_Shield::renderBuilder($pageContent);
            }
            $data['pageContent'] = $pageContent;
        }

        if (isset($pageContent->varTitle) && !empty($pageContent->varTitle)) {
            view()->share('detailPageTitle', $pageContent->varTitle);
        }
        // End CMS PAGE Front Private, Password Prottected Code
        $services = Services::getFrontServicesDropdownList();
        $deviceType = Config::get('Constant.DEVICE');
        $contacts = ContactInfo::getContactList();
        Session::put('ContactUS_Form', uniqid());
        $data = [
            'contact_info' => $contacts,
            'breadcrumb' => $this->breadcrumb,
            'data' => $data,
            'services' => $services,
            'deviceType' => $deviceType
        ];
        return view('contactuslead::frontview.contact-us', $data);
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
            'first_name.valid_input' => 'Please enter valid input.',
            'first_name.regex' => 'Please enter valid input.',
            'last_name.handle_xss' => 'Please enter valid input.',
            'last_name.no_url' => 'URL is not allowed.',
            'user_message.handle_xss' => 'Please enter valid input.',
            'user_message.valid_input' => 'Please enter valid input.',
            'user_message.no_url' => 'URL is not allowed.',
            'contact_email.required' => 'Please enter email.',
            'contact_email.email' => 'Please enter valid email.',
            'contact_email.regex' => 'Please enter valid email.',
            //            'phone_number.required' => 'Please enter phone.',
            // 'phone_number.min'=> 'Please enter at least 6 digits.',
            'phone_number.max' => 'You reach the maximum limit.',
            'phone_number.no_url' => 'URL is not allowed.',
            'phone_number.handle_xss' => 'Please enter valid input.',
                       'services.required' => "Please select interested in.",
            'g-recaptcha-response.required' => "Please select I'm not a robot.",
        );

        $rules = array(
            'first_name' => ['required', 'handle_xss', 'no_url', 'valid_input', 'regex:/^[a-zA-Z\s]*$/', new ValidateBadWord],
            // 'last_name' => ['handle_xss', 'no_url', new ValidateBadWord],
            'contact_email' => 'required|email|regex:/(.+)@(.+)\.(.+)/i',
            'user_message' => ['handle_xss','valid_input', 'no_url', new ValidateBadWord],
            'phone_number' => 'max:20|handle_xss|no_url',
                       'services' => ['required'],
        );

        // $rules['g-recaptcha-response'] = ['required', new ValidRecaptcha];
        $rules['g-recaptcha-response'] = ['required'];

        $validator = Validator::make($data, $rules, $messsages);

        if ($validator->passes()) {
            if ($data['g-recaptcha-response'] != '' && Session::get('ContactUS_Form') != '' && $data['last_name'] == '') {
                $contactus_lead = new ContactLead;
                $contactus_lead->varName = trim(strip_tags($data['first_name']));
                $contactus_lead->varEmail = MyLibrary::getEncryptedString(trim($data['contact_email']));
                if (isset($data['phone_number'])) {
                    $contactus_lead->varPhoneNo = MyLibrary::getEncryptedString(trim($data['phone_number']));
                } else {
                    $contactus_lead->varPhoneNo = '';
                }
                if (isset($data['user_message'])) {
                    $contactus_lead->txtUserMessage = MyLibrary::getEncryptedString(strip_tags(trim($data['user_message'])));
                } else {
                    $contactus_lead->txtUserMessage = '';
                }
                if (isset($data['services'])) {
                    $contactus_lead->fkIntServiceId = $data['services'];
                } else {
                    $contactus_lead->fkIntServiceId = null;
                }
                $contactus_lead->varIpAddress = MyLibrary::get_client_ip();
                $contactus_lead->save();
                /*Start this code for message*/
                if (!empty($contactus_lead->id)) {

                    $recordID = $contactus_lead->id;
                    Email_sender::contactUs($data, $contactus_lead->id);

                    if (Request::ajax()) {
                        return json_encode(['success' => 'We have received your request. Someone from our staff will contact you shortly.']);
                    } else {
                        return redirect()->route('thank-you')->with(['form_submit' => true, 'message' => 'We have received your request. Someone from our staff will contact you shortly.']);
                        //return redirect()->back()->with(['form_submit' => true, 'message' => 'Thank you for contacting us, We will get back to you shortly.']);
                    }
                } else {
                    return redirect('/');
                }
            } else {
                return redirect('/');
            }
        } else {

            //return contact form with errors
            if (!empty($data['back_url'])) {
                return redirect($data['back_url'] . '#contact_page_form')->withErrors($validator)->withInput();
            } else {
                return Redirect::route('contact-us')->withErrors($validator)->withInput();
            }
        }
    }
}
