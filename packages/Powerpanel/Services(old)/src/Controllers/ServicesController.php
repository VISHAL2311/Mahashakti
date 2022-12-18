<?php

namespace Powerpanel\Services\Controllers;

use App\Http\Controllers\FrontController;
use App\Http\Traits\slug;
use Powerpanel\ServicesCategory\Models\ServiceCategory;
use Powerpanel\Services\Models\Services;
use App\Video;
use App\Helpers\MyLibrary;
use Powerpanel\CmsPage\Models\CmsPage;
use App\Helpers\FrontPageContent_Shield;
use Illuminate\Support\Facades\Request;
use File;

class ServicesController extends FrontController {

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct() {
        parent::__construct();
    }

    /**
     * This method loads services list view
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
        // End CMS PAGE Front Private, Password Prottected Code
        return view('services::frontview.services', $data);
    }

    /**
     * This method loads services detail view
     * @param   Alias of record
     * @return  View
     * @since   2020-01-17
     * @author  NetQuick
     */
    public function detail($alias) {
        $id = slug::resolve_alias($alias);
        // $service = Services::getFrontDetail($id);
        
        if (is_numeric($alias)) {
            $service = Services::getRecordById($alias);
        } else {
            $id = slug::resolve_alias($alias);
            $service = Services::getFrontDetail($id);
        }
        if (!empty($service)) {
            $PAGE_CONTENT = '';
            $description = \App\Helpers\FrontPageContent_Shield::renderBuilder($service->txtDescription);

            $serviceCategory = null;
            $categoryIds = unserialize($service->txtCategories);
            if (!empty($categoryIds)) {
                $serviceCategory = ServiceCategory::getRecordByIds($categoryIds);
            }
            $videoIDAray = explode(',', $service->fkIntVideoId);
            $videoObj = Video::getVideoData($videoIDAray);
            $metaInfo = array('varMetaTitle' => $service->varMetaTitle, 'varMetaKeyword' => $service->varMetaKeyword, 'varMetaDescription' => $service->varMetaDescription);
            $data = array();

            $breadcrumb = [];
            $segmentArr = Request::segments();
            $url = '';
            foreach ($segmentArr as $key => $value) {
                $url .= $value . '/';
                $breadcrumb[$key]['title'] = ucwords(str_replace('-', ' ', $value));
                $breadcrumb[$key]['url'] = rtrim($url, '/');
            }
            $moduelFrontPageUrl = MyLibrary::getFront_Uri('services')['uri'];
            $moduleFrontWithCatUrl = ($alias != false ) ? $moduelFrontPageUrl : $moduelFrontPageUrl;

            $similarServices = Services::getSimilarRecordList($service->id);
            $data['service'] = $service;
            $data['alias'] = $alias;
            $data['similarServices'] = $similarServices;
            $data['metaInfo'] = $metaInfo;
            $data['breadcrumb'] = $breadcrumb;
            $data['moduleFrontWithCatUrl'] = $moduleFrontWithCatUrl;
            $data['description'] = $description;
            $data['serviceCategory'] = $serviceCategory;
            $data['videoObj'] = $videoObj;
            $data['PAGE_CONTENT']['assets'] = $PAGE_CONTENT;
            $data['txtDescription'] = FrontPageContent_Shield::renderBuilder($service->txtDescription);


            if (isset($service->fkIntImgId) && !empty($service->fkIntImgId)) {
                $imageArr = explode(',', $service->fkIntImgId);
                view()->share('SHARE_IMG', $imageArr[0]);
            }

            if (isset($service->varMetaTitle) && !empty($service->varMetaTitle)) {
                view()->share('META_TITLE', $service->varMetaTitle);
            }

            if (isset($service->varMetaKeyword) && !empty($service->varMetaKeyword)) {
                view()->share('META_KEYWORD', $service->varMetaKeyword);
            }
            if (isset($service->varMetaDescription) && !empty($service->varMetaDescription)) {
                view()->share('META_DESCRIPTION', substr(trim($service->varMetaDescription), 0, 500));
            }
            return view('services::frontview.services-detail', $data);
        } else {
            abort(404);
        }
    }

}
