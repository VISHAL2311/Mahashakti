<?php

namespace Powerpanel\CmsPage\Controllers;

use App\CommonModel;
use App\Helpers\Aws_File_helper;
use App\Helpers\FrontPageContent_Shield;
use App\Helpers\MyLibrary;
use App\Http\Controllers\FrontController;
use App\Http\Traits\slug;
use DB;
use Powerpanel\CmsPage\Models\CmsPage;
use Request;
use Response;

class CmsPagesController extends FrontController
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
     * This method loads CMS Termsconditions list view
     * @return  View
     * @since   2018-08-27
     * @author  NetQuick
     */
    public function index()
    {
        $data = array();
        $pagename = Request::segment(1);
        if (is_numeric($pagename) && (int)$pagename > 0) {
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

        $CONTENT = '<h2 class="no_record coming_soon_rcd"> Coming Soon</h2>';
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
        if (isset($pageContentcms) && $pageContentcms->chrPageActive == 'PR') {
            if ($pageContentcms->UserID == $user_id) {
                if (isset($pageContent) && $pageContent != '') {
                    $data['PageData'] = FrontPageContent_Shield::renderBuilder($pageContent);
                }
                
            } else {
                return redirect(url('/'));
            }
        } elseif (isset($pageContentcms) && $pageContentcms->chrPageActive == 'PP') {
            $data['PassPropage'] = 'PP';
            $data['Pageid'] = $pageContentcms->id;
        } else {
            if (isset($pageContent) && $pageContent != '') {
                $data['PageData'] = FrontPageContent_Shield::renderBuilder($CONTENT);
            } else {
                $data['PageData'] = '';
            }
            $data['pageContent'] = $pageContent;
        }

        if (isset($pageContent->varTitle) && !empty($pageContent->varTitle)) {
            view()->share('detailPageTitle', $pageContent->varTitle);
        }
        // End CMS PAGE Front Private, Password Prottected Code

        $data['CONTENT'] = $CONTENT;
        $data['breadcrumb'] = $this->breadcrumb;

        return view('cmspage::frontview.pages', $data);
    }

    public function viewPDF($dir, $filename)
    {
        $AWSContants = MyLibrary::getAWSconstants();
        if ($AWSContants['BUCKET_ENABLED'] == true) {
            $file_path = $dir . "/" . $filename;
            echo $data['modulePageUrl'] = Aws_File_helper::getObject_new($file_path);
            exit;
        } else {
            $pathToFile = public_path($dir . "/" . $filename);
            $headers = [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'inline; filename="' . $filename . '"',
            ];
            return response()->file($pathToFile, $headers);
        }
    }

    public function store()
    {
        $updateMenuFields = [
            'txtDescription' => $_REQUEST['txtDescription'],
        ];
        $whereConditions = ['id' => $_REQUEST['cms_id']];
        $update = CommonModel::updateRecords($whereConditions, $updateMenuFields, false, 'Powerpanel\CmsPage\Models\CmsPage');
        return json_encode(['success' => 'Cms Content Updated.']);
    }

    public function PagePassURLListing()
    {
        $record = Request::input();
        $pagedata = DB::table('cms_page')
            ->select('*')
            ->where('id', '=', $record['id'])
            ->first();
        if ($pagedata->varPassword == $record['passwordprotect']) {
            $html = FrontPageContent_Shield::renderBuilder($pagedata->txtDescription);
            echo json_encode($html);
        } else {
            $response = array("error" => 1, 'validatorErrors' => 'Password Does Not Match');
            echo json_encode($response);
        }
    }

    public function previewpage()
    {
        return view('layouts.preview');
    }

    public function detail(){
        abort('404');
    }

}