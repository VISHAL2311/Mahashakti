<?php

namespace Powerpanel\Blogs\Controllers;

use App\Helpers\FrontPageContent_Shield;
use App\Helpers\MyLibrary;
use App\Http\Controllers\FrontController;
use App\Http\Traits\slug;
use Powerpanel\BlogCategory\Models\BlogCategory;
use Powerpanel\Services\Models\Services;
use Powerpanel\Blogs\Models\Blogs;
use Powerpanel\Boat\Models\Boat;
use Powerpanel\CmsPage\Models\CmsPage;
use Powerpanel\Work\Models\Work;
use Request;

class BlogsController extends FrontController
{

    use slug;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

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

        $paginate = 8;
        $blogs = Blogs::getFrontList($paginate);
        $data['blogs'] = $blogs;

        $blogsSlidebar = Blogs::getSidebarRecordList();
        $data['blogsSlidebar'] = $blogsSlidebar;

        $workSlidebar = Work::getSlidebarRecordList();
        $data['workSlidebar'] = $workSlidebar;

        if (isset($pageContent->varTitle) && !empty($pageContent->varTitle)) {
            view()->share('detailPageTitle', $pageContent->varTitle);
        }
        // End CMS PAGE Front Private, Password Prottected Code
        return view('blogs::frontview.blogs', $data);

    }

    public function detail($alias)
    {
        $catid = false;
        $isCategoryList = false;
        $isCategoryTitle = '';
        $isCategoryAlias = '';

        if (is_numeric($alias)) {
            $blogs = Blogs::getRecordById($alias);
        } 
        $id = slug::resolve_alias($alias);
        $blogs = Blogs::getFrontDetail($id);
        $recordCategoryId = false;
        if (!empty($blogs)) {
            $recordCategoryId = $blogs->intFKCategory;
        }

        if (!empty($blogs)) {
            $metaInfo = array('varMetaTitle' => $blogs->varMetaTitle, 'varMetaKeyword' => $blogs->varMetaKeyword, 'varMetaDescription' => $blogs->varMetaDescription);
            if (isset($blogs->varMetaTitle) && !empty($blogs->varMetaTitle)) {
                view()->share('META_TITLE', $blogs->varMetaTitle);
            }
            if (isset($blogs->varMetaKeyword) && !empty($blogs->varMetaKeyword)) {
                view()->share('META_KEYWORD', $blogs->varMetaKeyword);
            }
            if (isset($blogs->varMetaDescription) && !empty($blogs->varMetaDescription)) {
                view()->share('META_DESCRIPTION', substr(trim($blogs->varMetaDescription), 0, 500));
            }
            $breadcrumb = [];
            $data = [];
            $moduelFrontPageUrl = MyLibrary::getFront_Uri('blogs')['uri'];
            $moduleFrontWithCatUrl = ($alias != false) ? $moduelFrontPageUrl : $moduelFrontPageUrl;
            $breadcrumb['title'] = (!empty($blogs->varTitle)) ? ucwords($blogs->varTitle) : '';
            $breadcrumb['url'] = MyLibrary::getFront_Uri('blogs')['uri'];
            $detailPageTitle = $breadcrumb['title'];
            $breadcrumb = $breadcrumb;
            $data['moduleTitle'] = 'Blogs';
            $blogsAllCategoriesArr = BlogCategory::getAllCategoriesFrontSidebarList();
            $data['blogsAllCategoriesArr'] = $blogsAllCategoriesArr;
            $data['modulePageUrl'] = $moduelFrontPageUrl;
            $data['moduleFrontWithCatUrl'] = $moduleFrontWithCatUrl;
            $data['isCategoryList'] = $isCategoryList;
            $data['isCategoryTitle'] = $isCategoryTitle;
            $data['isCategoryAlias'] = $isCategoryAlias;
            $data['blogs'] = $blogs;
            $data['alias'] = $alias;
            $data['metaInfo'] = $metaInfo;
            $data['breadcrumb'] = $breadcrumb;
            $data['detailPageTitle'] = 'Blogs';
            $data['txtDescription'] = FrontPageContent_Shield::renderBuilder($blogs->txtDescription);

            $similarBlogs = Blogs::getSimilarRecordList($blogs->id);
            $data['similarBlogs'] = $similarBlogs;

            $workSlidebar = Work::getSidebarRecordList();
            $data['workSlidebar'] = $workSlidebar;
            
            $similarServices = Services::getSidebarRecordList();
            $data['similarServices'] = $similarServices;

            $similarBoats = Boat::getSidebarRecordList();
            $data['similarBoats'] = $similarBoats;

            return view('blogs::frontview.blogs-detail', $data);
        } else {
            abort(404);
        }
    }

}
