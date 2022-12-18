<?php

namespace Powerpanel\Brand\Controllers;

use App\Http\Controllers\FrontController;
use Powerpanel\Brand\Models\Brand;
use App\Document;
use Request;
use Response;
use DB;
use Powerpanel\CmsPage\Models\CmsPage;
use App\Helpers\FrontPageContent_Shield;
use App\Http\Traits\slug;
use App\Helpers\MyLibrary;
use Powerpanel\BrandCategory\Models\BrandCategory;
use App\Helpers\CustomPagination;

class BrandController extends FrontController {

    use slug;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct() {
        parent::__construct();
    }

    /**
     * This method loads Brand list view
     * @return  View
     * @since   2018-08-27
     * @author  NetQuick
     */
    public function index($alias = false) {
        
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

        $paginate = 30;
        $brands = Brand::getFrontList($paginate);
        $data['brands'] = $brands;

        if (isset($pageContent->varTitle) && !empty($pageContent->varTitle)) {
            view()->share('detailPageTitle', $pageContent->varTitle);
        }
        // End CMS PAGE Front Private, Password Prottected Code
            return view('brand::frontview.brands', $data);
    }

}
