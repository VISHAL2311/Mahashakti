<?php

namespace Powerpanel\Work\Controllers;

use App\Http\Controllers\FrontController;
use App\Http\Traits\slug;
use Powerpanel\Work\Models\Work;
use App\Video;
use App\Helpers\MyLibrary;
use Powerpanel\CmsPage\Models\CmsPage;
use App\Helpers\FrontPageContent_Shield;
use Illuminate\Support\Facades\Request;
use Powerpanel\Blogs\Models\Blogs;
use Powerpanel\Testimonial\Models\Testimonial;
use File;

class WorkController extends FrontController {

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct() {
        parent::__construct();
    }

    /**
     * This method loads Work list view
     * @return  View
     * @since   2020-01-17
     * @author  NetQuick
     */
    public function index() {
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
        $work = Work::getFrontList($paginate);
        $data['work'] = $work;
        
        $TestimonialHome = Testimonial::getLatestList();
        $data['TestimonialHome'] = $TestimonialHome;

        if (isset($pageContent->varTitle) && !empty($pageContent->varTitle)) {
            view()->share('detailPageTitle', $pageContent->varTitle);
        }
        // End CMS PAGE Front Private, Password Prottected Code 
        return view('work::frontview.work',$data);    
    }

    /**
     * This method loads Work detail view
     * @param   Alias of record
     * @return  View
     * @since   2020-01-17
     * @author  NetQuick
     */
    public function detail($alias) {
        $id = slug::resolve_alias($alias);
        $work = Work::getFrontDetail($id);
        if (!empty($work)) {

            $PAGE_CONTENT = '';
            $description = \App\Helpers\FrontPageContent_Shield::renderBuilder($work->txtDescription);
            // dd($description['response']);

           
            $videoIDAray = explode(',', $work->fkIntVideoId);
            $videoObj = Video::getVideoData($videoIDAray);
            $metaInfo = array('varMetaTitle' => $work->varMetaTitle, 'varMetaKeyword' => $work->varMetaKeyword, 'varMetaDescription' => $work->varMetaDescription);
            $data = array();

            $breadcrumb = [];
            $segmentArr = Request::segments();
            $url = '';
            foreach ($segmentArr as $key => $value) {
                $url .= $value . '/';
                $breadcrumb[$key]['title'] = ucwords(str_replace('-', ' ', $value));
                $breadcrumb[$key]['url'] = rtrim($url, '/');
            }
            $moduelFrontPageUrl = MyLibrary::getFront_Uri('work')['uri'];
            $moduleFrontWithCatUrl = ($alias != false ) ? $moduelFrontPageUrl : $moduelFrontPageUrl;

            $similarWork = Work::getSimilarRecordList($work->id);
            $data['work'] = $work;
            $data['alias'] = $alias;
            $data['similarWork'] = $similarWork;
            $data['metaInfo'] = $metaInfo;
            $data['breadcrumb'] = $breadcrumb;
            $data['moduleFrontWithCatUrl'] = $moduleFrontWithCatUrl;
            $data['description'] = $description;
            $data['videoObj'] = $videoObj;
            $data['PAGE_CONTENT']['assets'] = $PAGE_CONTENT;
            $data['txtDescription'] = FrontPageContent_Shield::renderBuilder($work->txtDescription,$work->id);

            $blogsSlidebar = Blogs::getSidebarRecordList();
            $data['blogsSlidebar'] = $blogsSlidebar;


            if (isset($work->fkIntImgId) && !empty($work->fkIntImgId)) {
                $imageArr = explode(',', $work->fkIntImgId);
                view()->share('SHARE_IMG', $imageArr[0]);
            }

            if (isset($work->varMetaTitle) && !empty($work->varMetaTitle)) {
                view()->share('META_TITLE', $work->varMetaTitle);
            }

            if (isset($work->varMetaKeyword) && !empty($work->varMetaKeyword)) {
                view()->share('META_KEYWORD', $work->varMetaKeyword);
            }
            if (isset($work->varMetaDescription) && !empty($work->varMetaDescription)) {
                view()->share('META_DESCRIPTION', substr(trim($work->varMetaDescription), 0, 500));
            }
            return view('work::frontview.work-detail', $data);
        } else {
            abort(404);
        }
    }

}
