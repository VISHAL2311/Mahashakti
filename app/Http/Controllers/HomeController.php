<?php

namespace App\Http\Controllers;

use App\Helpers\FrontPageContent_Shield;
use App\Helpers\MyLibrary;
use File;
use Powerpanel\Banner\Models\Banner;
use Powerpanel\CmsPage\Models\CmsPage;
use Powerpanel\QuickLinks\Models\QuickLinks;
use Powerpanel\Services\Models\Services;
use Powerpanel\Boat\Models\Boat;
use Powerpanel\Work\Models\Work;
use Powerpanel\Testimonial\Models\Testimonial;
use Powerpanel\GetaEstimateLead\Models\GetaEstimateLead;
use Powerpanel\ServiceInquiryLead\Models\ServiceinquiryLead;
use Powerpanel\BoatInquiryLead\Models\BoatinquiryLead;
use App\Helpers\Email_sender;
use App\Rules\ValidateBadWord;
use App\Rules\ValidRecaptcha;
use Request;
use Validator;
use Redirect;
use DB;

class HomeController extends FrontController
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
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data = array();

        $services = Services::getFrontServicesDropdownList();
        if (!empty($services) && count($services) > 0) {
            $data['services'] = $services;
        }

        $bannerObj = Banner::getHomeBannerList();
        if (!empty($bannerObj) && count($bannerObj) > 0) {
            $data['bannerData'] = $bannerObj;
        }

        $homeServices = Services::getFrontListHome();
        if (!empty($homeServices)) {
            $data['homeServices'] = $homeServices;
        }

        $TestimonialHome = Testimonial::getLatestList();
        if (!empty($TestimonialHome)) {
            $data['TestimonialHome'] = $TestimonialHome;
        }

        $WorkHome = Work::getFrontListHome();
        if (!empty($WorkHome)) {
            $data['WorkHome'] = $WorkHome;
        }

        $homePageCmsPageSections = CmsPage::getHomePageDisplaySections();
        if (!empty($homePageCmsPageSections)) {
            $data['homePageCmsPageSections'] = FrontPageContent_Shield::renderBuilder($homePageCmsPageSections->txtDescription);
        }

        if (File::exists(base_path() . '/packages/Powerpanel/QuickLinks/src/Models/QuickLinks.php')) {
            $quickLinks = QuickLinks::getHomePageList(8);
            if (!empty($quickLinks)) {
                $data['quickLinks'] = array();
                $qlinkcounter = 0;
                foreach ($quickLinks as $link) {
                    if ($link->varLinkType == "internal") {
                        if ($link->modules->varModuleName) {
                            $qlink = MyLibrary::getUrlLinkForQlinks($link->modules->varModuleName, $link->fkIntPageId)['uri'];
                            if (!empty($qlink)) {
                                $data['quickLinks'][$qlinkcounter]['link'] = $qlink;
                                $data['quickLinks'][$qlinkcounter]['varTitle'] = $link->varTitle;
                                $data['quickLinks'][$qlinkcounter]['varLinkType'] = $link->varLinkType;
                                $qlinkcounter++;
                            }
                        }
                    } else {
                        $data['quickLinks'][$qlinkcounter]['link'] = $link->varExtLink;
                        $data['quickLinks'][$qlinkcounter]['varTitle'] = $link->varTitle;
                        $data['quickLinks'][$qlinkcounter]['varLinkType'] = $link->varLinkType;
                        $qlinkcounter++;
                    }
                }
            }
        }
        $site_monitor = DB::table('site_monitor')->select('varTitle')->where('chrDelete', 'N')->first();
        if (!empty($site_monitor)) {
            $data['site_monitor'] = $site_monitor;
        }
        return view('index', $data);
    }

    public function getPreviousAvailableRecordData($field)
    {
        $getPreviousAvailableRecordData = self::getPreviousAvailableRecordData_recursive($field);
        return $getPreviousAvailableRecordData;
    }

    public function getPreviousAvailableRecordData_recursive($field, $skip = 1)
    {
        $response = false;
        $getPreviousAvailableRecordData = InterestRates::getPreviousAvailableRecordData($field, $skip);

        if (!empty($getPreviousAvailableRecordData) && count($getPreviousAvailableRecordData) > 0) {

            $found = 0;
            foreach ($getPreviousAvailableRecordData as $data) {
                if ($data[$field] > 0) {
                    $found = 1;
                    break;
                }
            }
            if ($found == 1) {
                return $data;
            }

            if ($found == 0) {
                $skip = $skip + 10;
                $getPreviousAvailableRecordData = self::getPreviousAvailableRecordData_recursive($field, $skip);
            }
        } else {
            return false;
        }

        return $response;
    }

    //Get a free Estimate Form Store Code Start
    public function getaEstimate()
    {
        $data = Request::all();
        $messsages = array(
            'first_name.required' => 'Please enter name.',
            'first_name.handle_xss' => 'Please enter valid input.',
            'first_name.no_url' => 'URL is not allowed.',
            'last_name.handle_xss' => 'Please enter valid input.',
            'last_name.no_url' => 'URL is not allowed.',
            'user_message.handle_xss' => 'Please enter valid input.',
            'user_message.valid_input' => 'Please enter valid input.',
            'user_message.no_url' => 'URL is not allowed.',
            'contact_email.required' => 'Please enter email address.',
            'contact_email.email' => 'Please enter valid email address.',
            // 'phone_number.required' => 'Please enter phone.',
            // 'phone_number.min'=> 'Please enter at least 6 digits.',
            'phone_number.max' => 'You reach the maximum limit.',
            'phone_number.no_url' => 'URL is not allowed.',
            'phone_number.handle_xss' => 'Please enter valid input.',
            'services.required' => "Please select services.",
            'g-recaptcha-response.required' => "Please select I'm not a robot.",
        );

        $rules = array(
            'first_name' => ['required', 'handle_xss', 'no_url', new ValidateBadWord],
            'last_name' => ['handle_xss', 'no_url', new ValidateBadWord],
            'contact_email' => 'required|email',
            'user_message' => ['handle_xss', 'no_url', new ValidateBadWord],
            'phone_number' => 'max:20|handle_xss|no_url',
            'services' => ['required'],
        );

        $rules['g-recaptcha-response'] = ['required', new ValidRecaptcha];

        $validator = Validator::make($data, $rules, $messsages);
        if ($validator->passes()) {
            if ($data['g-recaptcha-response'] != '' && $data['last_name'] == '') {
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
                    Email_sender::getaEstimate($data, $requestaquote_lead->id);

                    if (Request::ajax()) {
                        return json_encode(['success' => 'Thank you for showing interest in our service. We have received your request and someone from our staff will contact you shortly.']);
                    } else {
                        return redirect()->route('thank-you')->with(['form_submit' => true, 'message' => 'Thank you for showing interest in our service. We have received your request and someone from our staff will contact you shortly.']);
                    }
                } else {
                    return redirect('/');
                }
            }else{
                return redirect('/');
            }
        } else {

            //return GetaEstimateLead form with errors
            return redirect('/')->withErrors($validator)->withInput();
            return json_encode($validator->errors());
            exit;
        }
    }
    //Get a free Estimate Form Store Code End

    //service inquiry start
    public function serviceinquiry()
    {
        $data = Request::all();

        $messsages = array(
            'first_name.required' => 'Please enter name.',
            'first_name.handle_xss' => 'Please enter valid input.',
            'first_name.no_url' => 'URL is not allowed.',
            'last_name.handle_xss' => 'Please enter valid input.',
            'last_name.no_url' => 'URL is not allowed.',
            'user_message.handle_xss' => 'Please enter valid input.',
            'user_message.valid_input' => 'Please enter valid input.',
            'user_message.no_url' => 'URL is not allowed.',
            'contact_email.required' => 'Please enter email address.',
            'contact_email.email' => 'Please enter valid email address.',
            //            'phone_number.required' => 'Please enter phone.',
            // 'phone_number.min'=> 'Please enter at least 6 digits.',
            'phone_number.max' => 'You reach the maximum limit.',
            'phone_number.no_url' => 'URL is not allowed.',
            'phone_number.handle_xss' => 'Please enter valid input.',
     
            'g-recaptcha-response.required' => "Please select I'm not a robot.",
        );

        $rules = array(
            'first_name' => ['required', 'handle_xss', 'no_url', new ValidateBadWord],
            'last_name' => ['handle_xss', 'no_url', new ValidateBadWord],
            'contact_email' => 'required|email',
            'user_message' => ['handle_xss', 'no_url', new ValidateBadWord],
            'phone_number' => 'max:20|handle_xss|no_url',
                      
        );

        // $rules['g-recaptcha-response'] = ['required', new ValidRecaptcha];
        $rules['g-recaptcha-response'] = ['required'];

        $validator = Validator::make($data, $rules, $messsages);

        if ($validator->passes()) {
            if ($data['g-recaptcha-response'] != ''  && $data['last_name'] == '') {
                $serviceinquiry_lead = new ServiceinquiryLead;
                $serviceinquiry_lead->varName = trim(strip_tags($data['first_name']));
                $serviceinquiry_lead->varEmail = MyLibrary::getEncryptedString(trim($data['contact_email']));
                if (isset($data['phone_number'])) {
                    $serviceinquiry_lead->varPhoneNo = MyLibrary::getEncryptedString(trim($data['phone_number']));
                } else {
                    $serviceinquiry_lead->varPhoneNo = '';
                }
                if (isset($data['user_message'])) {
                    $serviceinquiry_lead->txtUserMessage = MyLibrary::getEncryptedString(strip_tags(trim($data['user_message'])));
                } else {
                    $serviceinquiry_lead->txtUserMessage = '';
                }
                if (isset($data['services'])) {
                    $serviceinquiry_lead->fkIntServiceId = $data['services'];
                } else {
                    $serviceinquiry_lead->fkIntServiceId = null;
                }
                $serviceinquiry_lead->varIpAddress = MyLibrary::get_client_ip();
                $serviceinquiry_lead->save();
                /*Start this code for message*/
                if (!empty($serviceinquiry_lead->id)) {

                    $recordID = $serviceinquiry_lead->id;
                    // Email_sender::serviceInquiry($data, $serviceinquiry_lead->id);

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
                return Redirect::back()->withErrors($validator)->withInput();
            }
        }
    }
    //service inquiry end
    //boatinquiry start
    public function boatinquiry()
    {
        $data = Request::all();

        $messsages = array(
            'first_name.required' => 'Please enter name.',
            'first_name.handle_xss' => 'Please enter valid input.',
            'first_name.no_url' => 'URL is not allowed.',
            'last_name.handle_xss' => 'Please enter valid input.',
            'last_name.no_url' => 'URL is not allowed.',
            'user_message.handle_xss' => 'Please enter valid input.',
            'user_message.valid_input' => 'Please enter valid input.',
            'user_message.no_url' => 'URL is not allowed.',
            'contact_email.required' => 'Please enter email address.',
            'contact_email.email' => 'Please enter valid email address.',
            //            'phone_number.required' => 'Please enter phone.',
            // 'phone_number.min'=> 'Please enter at least 6 digits.',
            'phone_number.max' => 'You reach the maximum limit.',
            'phone_number.no_url' => 'URL is not allowed.',
            'phone_number.handle_xss' => 'Please enter valid input.',
           
            'g-recaptcha-response.required' => "Please select I'm not a robot.",
        );

        $rules = array(
            'first_name' => ['required', 'handle_xss', 'no_url', new ValidateBadWord],
            'last_name' => ['handle_xss', 'no_url', new ValidateBadWord],
            'contact_email' => 'required|email',
            'user_message' => ['handle_xss', 'no_url', new ValidateBadWord],
            'phone_number' => 'max:20|handle_xss|no_url',
          
        );

        // $rules['g-recaptcha-response'] = ['required', new ValidRecaptcha];
        $rules['g-recaptcha-response'] = ['required'];

        $validator = Validator::make($data, $rules, $messsages);

        if ($validator->passes()) {
            if ($data['g-recaptcha-response'] != ''  && $data['last_name'] == '') {
                $boatinquiry_lead = new BoatinquiryLead;
                $boatinquiry_lead->varName = trim(strip_tags($data['first_name']));
                $boatinquiry_lead->varEmail = MyLibrary::getEncryptedString(trim($data['contact_email']));
                if (isset($data['phone_number'])) {
                    $boatinquiry_lead->varPhoneNo = MyLibrary::getEncryptedString(trim($data['phone_number']));
                } else {
                    $boatinquiry_lead->varPhoneNo = '';
                }
                if (isset($data['user_message'])) {
                    $boatinquiry_lead->txtUserMessage = MyLibrary::getEncryptedString(strip_tags(trim($data['user_message'])));
                } else {
                    $boatinquiry_lead->txtUserMessage = '';
                }
                if (isset($data['boats'])) {
                    $boatinquiry_lead->fkIntBoatId = $data['boats'];
                } else {
                    $boatinquiry_lead->fkIntBoatId = null;
                }
                $boatinquiry_lead->varIpAddress = MyLibrary::get_client_ip();
                $boatinquiry_lead->save();
                /*Start this code for message*/
                if (!empty($boatinquiry_lead->id)) {

                    $recordID = $boatinquiry_lead->id;
                    // Email_sender::boatInquiry($data, $boatinquiry_lead->id);

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
                return Redirect::back()->withErrors($validator)->withInput();
            }
        }
    }
    //boatinquiry end
}
