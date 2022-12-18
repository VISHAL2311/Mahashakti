<?php
namespace Powerpanel\DataRemovalLead\Controllers;

use App\Http\Controllers\FrontController;
use Powerpanel\ContactInfo\Models\ContactInfo;
use Powerpanel\DataRemovalLead\Models\DataRemovalLead;
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
use App\CommonModel;
use Powerpanel\Services\Models\Services;

class DataRemovalLeadController extends FrontController
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
     * This method loads Data Removal list view
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
        $data = ['contact_info' => $contacts,
            'breadcrumb' => $this->breadcrumb,
            'data' => $data,
            'services' => $services,
            'deviceType' => $deviceType];
        return view('dataremovallead::frontview.data-removal', $data);
    }

    /**
     * This method stores Data Removal leads
     * @param   NA
     * @return  Redirection to Thank You page
     * @since   2020-01-17
     * @author  NetQuick
     */
    public function store()
    {
        $data = Request::all();

        $contacts = ContactInfo::getContactList();
        // dd($contacts);
            foreach($contacts as $contact){
                $generalContactInfo = $contact;
            }
            $objContactInfo = (!empty($generalContactInfo)) ? $generalContactInfo : '';
            $genEmail = '';
            if (isset($objContactInfo->varEmail)) {
                $genEmails = unserialize($objContactInfo->varEmail);
                $genEmail = count($genEmails) > 0 ? $genEmails[0] : $genEmails;
            }

        $messsages = array(
            'varFirstName.required' => 'Please enter your first name.',
            'varFirstName.valid_input' => 'Please enter valid input.',
            'varFirstName.alpha' => 'Please enter valid input.',
            'varFirstName.handle_xss' => 'Please enter valid input.',
            'varFirstName.no_url' => 'URL is not allowed.',
            'varEmail.required' => 'Please enter your email address.',
            'varEmail.email' => 'Please enter a valid email.',
            'varEmail.regex' => 'Please enter a valid email.',
            'varReason.required' => 'Please enter reason for removal.',
            'varReason.handle_xss' => 'Please enter valid input.',
            'varReason.valid_input' => 'Please enter valid input.',
            'varReason.no_url' => 'URL is not allowed.',
            'chrInfo.accepted' => 'Please confirm your authorization.',
            'g-recaptcha-response.required' => "Please select I'm not a robot.",
        );
        
        $rules = array(
            'varFirstName' => ['required', 'handle_xss', 'no_url', 'valid_input', new ValidateBadWord],
            'varEmail' => 'required|email|regex:/(.+)@(.+)\.(.+)/i',
            'varReason' => ['required', 'handle_xss', 'no_url', 'valid_input', new ValidateBadWord],
            'chrInfo'=> 'accepted'
        );

        // $rules['g-recaptcha-response'] = ['required', new ValidRecaptcha];
        $rules['g-recaptcha-response'] = ['required'];

        $validator = Validator::make($data, $rules, $messsages);

        if ($validator->passes()) {
            $dataremoval_lead = new DataRemovalLead;
            $dataremoval_lead->varName = trim(strip_tags($data['varFirstName']));
            $dataremoval_lead->varEmail = MyLibrary::getEncryptedString(trim($data['varEmail']));
            $dataremoval_lead->varReason = trim(strip_tags($data['varReason']));
            $dataremoval_lead->varIpAddress = MyLibrary::get_client_ip();
            $dataremoval_lead->save();

            // $message = 'Thank you for your confirmation. We have received your request to remove your stored information permanently. We will remove all your information within two weeks. If you have any concern on this please contact on this email: <a href="mailto:' . $genEmail . '" title="' . $genEmail . '">' . $genEmail . '</a>';
            $message = 'We have sent confirmation link to the entered email address. Please check your inbox and confirm you are the original requester.';
            /*Start this code for message*/
            if (!empty($dataremoval_lead->id)) {

                $recordID = $dataremoval_lead->id;
                Email_sender::DataRemoval($data, $dataremoval_lead->id);

                if (Request::ajax()) {
                    return json_encode(['success' => $message]);
                } else {
                    return redirect()->route('thank-you')->with(['form_submit' => true, 'message' => $message]);
                    //return redirect()->back()->with(['form_submit' => true, 'message' => 'Thank you for contacting us, We will get back to you shortly.']);
                }

            } else {
                return redirect('/');
            }

        } else {

            //return contact form with errors
            if (!empty($data['back_url'])) {
                return redirect($data['back_url'] . '#data_removal_form')->withErrors($validator)->withInput();
            } else {
                return Redirect::route('data-removal-lead')->withErrors($validator)->withInput();
            }

        }
    }

    public function removalConfirmation() {
        $data = Request::all();
        $id = Request::segment(3);
        // $encodedId = $data['e'];
        // $id = base64_decode($encodedId);
        $userConfirmation = DataRemovalLead::getRecordByEmail($id);
//        echo "<pre/>";print_r($userConfirmation->varRequeststatus);exit;
        if (isset($userConfirmation->varRequeststatus) && $userConfirmation->varRequeststatus == 'N') {
            $contacts = ContactInfo::getContactList();
//            echo "<pre/>";print_r($contacts);exit;
            foreach ($contacts as $contact) {
                $generalContactInfo = $contact;
            }
            $objContactInfo = (!empty($generalContactInfo)) ? $generalContactInfo : '';
            $genEmail = '';
            if (isset($objContactInfo->varEmail)) {
                $genEmails = unserialize($objContactInfo->varEmail);
                $genEmail = count($genEmails) > 0 ? $genEmails[0] : $genEmails;
            }

            $encryptedEmail = Mylibrary::getEncryptedString($id);
//            echo $genEmails; exit;
            $updateDataRemovalFields = [
                'varRequeststatus' => 'Y',
                'updated_at' => date('Y-m-d H:i:s')
            ];
            $whereConditions = ['id' => $id];
            $update = CommonModel::updateRecords($whereConditions, $updateDataRemovalFields, false, 'Powerpanel\DataRemovalLead\Models\DataRemovalLead');
            Email_sender::DataRemovalConfirmation($userConfirmation, $contacts);
            return redirect()->route('thank-you')->with(['form_submit' => true,
                        'message' => 'Thank you for your confirmation. We have received your request to remove your stored information permanently. We will remove all your information within two weeks. If you have any concern on this please contact on this email: <a href="mailto:' . $genEmail . '" title="' . $genEmail . '">' . $genEmail . '</a>.']);
        } else {
            return redirect('failed')->with(['form_submit' => true, 'message' => 'The link you are trying to access is no longer exist.']);
            // return redirect('aceessdenied')->with(['form_submit' => true,
            //             'message' => 'The link you are trying to access is no longer exist.']);
        }
    }

    public function powerpanel() {
        $data = Request::all();
        $id = Request::segment(3);
        // $encodedId = $data['e'];
        // $id = base64_decode($encodedId);
        $userConfirmation = DataRemovalLead::getRecordByEmail($id);
        if (isset($userConfirmation->varRequeststatus) && $userConfirmation->varRequeststatus == 'N') {
            $contacts = ContactInfo::getContactDetails();
            foreach ($contacts as $contact) {
                if (isset($contact->varRequeststatus) && $contact->varRequeststatus == 'Y') {
                    $generalContactInfo = $contact;
                }
            }
            $objContactInfo = (!empty($generalContactInfo)) ? $generalContactInfo : '';
            $genEmail = '';
            if (isset($objContactInfo->varEmail)) {
                $genEmails = unserialize($objContactInfo->varEmail);
                $genEmail = count($genEmails) > 0 ? $genEmails[0] : $genEmails;
            }
            $encryptedEmail = Mylibrary::getEncryptedString($id);
            $updateDataRemovalFields = [
                'varRequeststatus' => 'Y',
                'updated_at' => date('Y-m-d H:i:s')
            ];
            $whereConditions = ['varEmail' => $encryptedEmail];
            $update = CommonModel::updateRecords($whereConditions, $updateDataRemovalFields, false, 'Powerpanel\DataRemovalLead\Models\DataRemovalLead');
            Email_sender::DataRemovalConfirmation($userConfirmation, $genEmail);
            return redirect()->route('data-removal-leads/thankyou')->with(['form_submit' => true,
                        'message' => 'Thank you for your confirmation. We have received your request to remove your stored information permanently. We will remove all your information within two weeks. If you have any concern on this please contact on this email: <a href="mailto:' . $genEmail . '" title="' . $genEmail . '">' . $genEmail . '</a>.']);
        } else {
            abort(404);
        }
    }

    public function getEmail(){
        $email = Mylibrary::getEncryptedString(Request::get('varEmail'));
        $status = DataRemovalLead::where('chrPublish', 'Y')->where('chrDelete', 'N')->where('varEmail', $email)->first();
        if(isset($status) && !empty($status))
        {
            echo json_encode(1);
            exit;
        } else {
            echo json_encode(0);
            exit;
        }
    }

}
