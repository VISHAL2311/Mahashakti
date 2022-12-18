<?php
namespace Powerpanel\Team\Controllers;

use App\Http\Controllers\FrontController;
use App\Helpers\FrontPageContent_Shield;
use App\Helpers\MyLibrary;
use App\Http\Traits\slug;
use Powerpanel\Team\Models\Team;
use Config;
use Powerpanel\CmsPage\Models\CmsPage;
use Illuminate\Support\Facades\Request;

class TeamController extends FrontController
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
     * This method loads team list view
     * @return  View
     * @since   2020-01-17
     * @author  NetQuick
     */
    public function index()
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

        $paginate = 12;
        $teams = Team::getFrontList($paginate);
        $data['teams'] = $teams;
        // End CMS PAGE Front Private, Password Prottected Code
        return view('team::frontview.team', $data);
    }

    /**
     * This method loads team detail view
     * @param   Alias of record
     * @return  View
     * @since   2020-01-17
     * @author  NetQuick
     */
    public function detail($alias)
    {
        $catid = false;
        $isCategoryList = false;
        $isCategoryTitle = '';
        $isCategoryAlias = '';

        if (is_numeric($alias)) {
            $team = Team::getRecordById($alias);
        } else {
            $id = slug::resolve_alias($alias);
            $team = Team::getFrontDetail($id);
        }
        $recordCategoryId = false;
        if (!empty($team)) {
            $recordCategoryId = $team->intFKCategory;
        }

        if (!empty($team)) {
            $metaInfo = array('varMetaTitle' => $team->varMetaTitle, 'varMetaKeyword' => $team->varMetaKeyword, 'varMetaDescription' => $team->varMetaDescription);
            if (isset($team->varMetaTitle) && !empty($team->varMetaTitle)) {
                view()->share('META_TITLE', $team->varMetaTitle);
            }
            if (isset($team->varMetaKeyword) && !empty($team->varMetaKeyword)) {
                view()->share('META_KEYWORD', $team->varMetaKeyword);
            }
            if (isset($team->varMetaDescription) && !empty($team->varMetaDescription)) {
                view()->share('META_DESCRIPTION', substr(trim($team->varMetaDescription), 0, 500));
            }
            $breadcrumb = [];
            $data = [];
            $moduelFrontPageUrl = MyLibrary::getFront_Uri('team')['uri'];
            $moduleFrontWithCatUrl = ($alias != false) ? $moduelFrontPageUrl : $moduelFrontPageUrl;
            $breadcrumb['title'] = (!empty($team->varTitle)) ? ucwords($team->varTitle) : '';
            $breadcrumb['url'] = MyLibrary::getFront_Uri('team')['uri'];
            $detailPageTitle = $breadcrumb['title'];
            $breadcrumb = $breadcrumb;
            $data['moduleTitle'] = 'team';
            $data['modulePageUrl'] = $moduelFrontPageUrl;
            $data['moduleFrontWithCatUrl'] = $moduleFrontWithCatUrl;
            $data['isCategoryList'] = $isCategoryList;
            $data['isCategoryTitle'] = $isCategoryTitle;
            $data['isCategoryAlias'] = $isCategoryAlias;
            $data['team'] = $team;
            $data['alias'] = $alias;
            $data['metaInfo'] = $metaInfo;
            $data['breadcrumb'] = $breadcrumb;
            $data['detailPageTitle'] = 'team';
            $data['txtDescription'] = FrontPageContent_Shield::renderBuilder($team->txtDescription);

            return view('team::frontview.team-detail', $data);
        } else {
            abort(404);
        }
    }
}
