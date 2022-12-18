<?php

namespace Powerpanel\Boat\Controllers;

use App\Http\Controllers\FrontController;
use App\Http\Traits\slug;
use Powerpanel\Boat\Models\Boat;
use App\Video;
use App\Helpers\MyLibrary;
use Powerpanel\CmsPage\Models\CmsPage;
use App\Helpers\FrontPageContent_Shield;
use Illuminate\Support\Facades\Request;
use App\Helpers\resize_image;
use App\Helpers\LoadWebpImage;

use Powerpanel\Work\Models\Work;
use File;
use DB;

class BoatController extends FrontController
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

    public function boat_search()
    {
        $response = array();
        $filterdataAll = Request::all();


        $fields = array('');
        $fields['moduleFields'] = array(
            'id',
            'intAliasId',
            'fkIntImgId',
            'fkIntVideoId',
            'varTitle', 'intBoatCategoryId', 'intBoatBrandId', 'intBoatStockId', 'yearYear', 'intPrice', 'varModel', 'varLength', 'intBoatFuelId', 'varHullMaterial', 'varBoatLocation', 'varHullShape', 'varHullWarranty', 'txtOtherdetail', 'txtDescriptionnew', 'varCruisingSpeed', 'varMaxSpeed', 'varLengthOverall', 'varBridgeclearance', 'varMaxDraft', 'varBeam', 'varCabinHeadroom', 'varLengthAtWaterline', 'varDryWeight', 'varWindlass', 'varDeadriseAtTransom', 'varElectricalCircuit', 'varSeatingCapacity', 'varFreshWaterTank', 'varFuelTank', 'varHoldingTank', 'varSingleBerths', 'varHeads', 'intBoatconditionId', 'txtOtherdetails',
            'varExternalLink',
            'varFontAwesomeIcon',
            'txtShortDescription',
            'txtDescription',
            'txtCategories',
            'varPreferences',
            'intDisplayOrder',
            'chrFeaturedBoat',
            'chrPublish',
            'chrDelete',
            'varMetaTitle',
            'varMetaKeyword',
            'varMetaDescription',
            'created_at',
            'updated_at'
        );


        $response = Boat::getBoatFilterList($fields, $filterdataAll);
        $responsecount = Boat::getBoatFilterCount($fields, $filterdataAll);
        $fill = $response['data'];
        $html = "";
        $html .= ' <div class="row" >';

        foreach ($fill as $index => $boat) {
            if (isset(MyLibrary::getFront_Uri('boat')['uri'])) {
                $moduelFrontPageUrl = MyLibrary::getFront_Uri('boat')['uri'];
                $moduleFrontWithCatUrl = ($boat->varAlias != false) ? $moduelFrontPageUrl . '/' . $boat->varAlias : $moduelFrontPageUrl;
                $recordLinkUrl = $moduleFrontWithCatUrl . '/' . $boat->alias->varAlias;
            } else {
                $recordLinkUrl = '';
            }
            $boat_stock = DB::table('stock')->select('varTitle')->where('chrPublish', 'Y')->where('chrDelete', 'N')->where('id', $boat->intBoatStockId)->first();
            $boat_stock = $boat_stock->varTitle;
            $boat_category = DB::table('boat_category')->select('varTitle')->where('chrPublish', 'Y')->where('chrDelete', 'N')->where('id', $boat->intBoatCategoryId)->first();
            $boat_category = $boat_category->varTitle;
            $boat_condition = DB::table('boat_condition')->select('varTitle')->where('chrPublish', 'Y')->where('chrDelete', 'N')->where('id', $boat->intBoatconditionId)->first();
            $boat_condition = $boat_condition->varTitle;
            $brand = DB::table('brand')->select('varTitle')->where('chrPublish', 'Y')->where('chrDelete', 'N')->where('id', $boat->intBoatBrandId)->first();
            $brand = $brand->varTitle;
            if (!empty($boat_stock) && $boat_stock == "Available to Order") {
                $boat_stock_class = "available";
            } elseif (!empty($boat_stock) && $boat_stock == "Sold") {
                $boat_stock_class = "sold";
            } elseif (!empty($boat_stock) && $boat_stock == "Available") {
                $boat_stock_class = "in-stock";
            } elseif (!empty($boat_stock) && $boat_stock == "Coming Soon") {
                $boat_stock_class = "comingsoon";
            } elseif (!empty($boat_stock) && $boat_stock == "Sale Pending") {
                $boat_stock_class = "salepending";
            }else{
                $boat_stock_class = "available";
            }

            $html .= ' <div class="col-xl-4 col-sm-6 col-12 boat-card">';
            $html .= '     <div class="boat-card-inner">';
            $html .= '         <div class="boat-card-img">';
            $html .= '             <div class="thumbnail-container">';
            $html .= '                 <div class="thumbnail" style="background: #f5f5f5;">';
            $html .= '                 <a href="' . $recordLinkUrl . '">';
            $html .= '                     <picture class="img-hvr_hvr">';
            $html .= '                         <source type="image/webp" data-srcset="' . LoadWebpImage::resize($boat->fkIntImgId, 337, 225) . '" srcset="' . LoadWebpImage::resize($boat->fkIntImgId, 337, 225) . '">';
            $html .= '                         <img  data-src="' . resize_image::resize($boat->fkIntImgId, 337, 225) . '" src="' . url(" assets/images/loader.gif") . '" alt="' . htmlspecialchars_decode($boat->varTitle) . '" title="' . htmlspecialchars_decode($boat->varTitle) . '">';
            $html .= '                     </picture>';
            $html .= '                 </a>';
            $html .= '                 </div>';
            $html .= '             </div>';

            //$html .= '             <div class="'.$boat_stock_class.'">'. $boat_stock .'</div>';
            $html .= '             <div class="boat-price  d-flex justify-content-center align-items-end"><strong>$' . number_format($boat->intPrice) . '</strong></div>';
            $html .= '         </div>';
            $html .= '         <div class="boat-info">';
            $html .= '             <div class="boat-title">';
            $html .= '                 <h2 class="main-title">';
            $html .= '                     <a href=" ' . $recordLinkUrl . '" title="' . ucwords($boat->varTitle) . '">' . htmlspecialchars_decode(str_limit($boat->varTitle, 48))  . '</a>';
            $html .= '                 </h2>';
            $html .= '             </div>';
            $html .= '             <ul class="info_wrap">';
            $html .= '             <li class="sub-title">
            <svg xmlns="http://www.w3.org/2000/svg" version="1.1" xmlns:xlink="http://www.w3.org/1999/xlink" xmlns:svgjs="http://svgjs.com/svgjs" width="512" height="512" x="0" y="0" viewBox="0 0 60.123 60.123" style="enable-background:new 0 0 512 512" xml:space="preserve" class=""><g transform="matrix(0.64,0,0,0.64,10.822140197753903,10.822319583892831)">
<g xmlns="http://www.w3.org/2000/svg">
    <path d="M57.124,51.893H16.92c-1.657,0-3-1.343-3-3s1.343-3,3-3h40.203c1.657,0,3,1.343,3,3S58.781,51.893,57.124,51.893z" fill="#000000" data-original="#000000" class=""></path>
    <path d="M57.124,33.062H16.92c-1.657,0-3-1.343-3-3s1.343-3,3-3h40.203c1.657,0,3,1.343,3,3   C60.124,31.719,58.781,33.062,57.124,33.062z" fill="#000000" data-original="#000000" class=""></path>
    <path d="M57.124,14.231H16.92c-1.657,0-3-1.343-3-3s1.343-3,3-3h40.203c1.657,0,3,1.343,3,3S58.781,14.231,57.124,14.231z" fill="#000000" data-original="#000000" class=""></path>
    <circle cx="4.029" cy="11.463" r="4.029" fill="#000000" data-original="#000000" class=""></circle>
    <circle cx="4.029" cy="30.062" r="4.029" fill="#000000" data-original="#000000" class=""></circle>
    <circle cx="4.029" cy="48.661" r="4.029" fill="#000000" data-original="#000000" class=""></circle>
</g>
<g xmlns="http://www.w3.org/2000/svg">
</g>
<g xmlns="http://www.w3.org/2000/svg">
</g>
<g xmlns="http://www.w3.org/2000/svg">
</g>
<g xmlns="http://www.w3.org/2000/svg">
</g>
<g xmlns="http://www.w3.org/2000/svg">
</g>
<g xmlns="http://www.w3.org/2000/svg">
</g>
<g xmlns="http://www.w3.org/2000/svg">
</g>
<g xmlns="http://www.w3.org/2000/svg">
</g>
<g xmlns="http://www.w3.org/2000/svg">
</g>
<g xmlns="http://www.w3.org/2000/svg">
</g>
<g xmlns="http://www.w3.org/2000/svg">
</g>
<g xmlns="http://www.w3.org/2000/svg">
</g>
<g xmlns="http://www.w3.org/2000/svg">
</g>
<g xmlns="http://www.w3.org/2000/svg">
</g>
<g xmlns="http://www.w3.org/2000/svg">
</g>
</g></svg>' . $boat_category . '</li>';
            $html .= '             <li class="condition"><svg xmlns="http://www.w3.org/2000/svg" version="1.1" xmlns:xlink="http://www.w3.org/1999/xlink" xmlns:svgjs="http://svgjs.com/svgjs" width="512" height="512" x="0" y="0" viewBox="0 0 64 64" style="enable-background:new 0 0 512 512" xml:space="preserve" class=""><g><g xmlns="http://www.w3.org/2000/svg"><path d="m32.016 58.003c-9.888.001-19.006-5.665-23.373-14.583-4.419-9.027-3.081-20.285 3.331-28.014 6.405-7.721 16.818-11.09 26.526-8.583 1.069.276 1.712 1.367 1.437 2.437-.276 1.07-1.368 1.712-2.437 1.437-8.212-2.121-17.026.729-22.447 7.264-5.424 6.539-6.556 16.064-2.817 23.702 3.725 7.608 11.942 12.564 20.376 12.334 8.433-.23 16.086-5.359 19.497-13.066 2.003-4.526 2.427-9.651 1.192-14.432-.276-1.069.367-2.16 1.437-2.437 1.067-.277 2.16.367 2.437 1.437 1.458 5.649.959 11.705-1.407 17.051-4.032 9.11-13.079 15.173-23.046 15.445-.236.005-.472.008-.706.008z" fill="#000000" data-original="#000000" class=""></path></g><g xmlns="http://www.w3.org/2000/svg"><path d="m32 38.24c-.512 0-1.024-.195-1.414-.586-.781-.781-.781-2.047 0-2.828l24-24c.78-.781 2.048-.781 2.828 0 .781.781.781 2.047 0 2.828l-24 24c-.39.39-.902.586-1.414.586z" fill="#000000" data-original="#000000" class=""></path></g><g xmlns="http://www.w3.org/2000/svg"><path d="m32 38.24c-.512 0-1.024-.195-1.414-.586l-8.485-8.485c-.781-.781-.781-2.047 0-2.828.78-.781 2.048-.781 2.828 0l8.485 8.485c.781.781.781 2.047 0 2.828-.39.39-.902.586-1.414.586z" fill="#000000" data-original="#000000" class=""></path></g></g></svg> ' . $boat_condition . '</li>';
            $html .= '             <li class="i-beam"><i class="fa fa-arrows-v" aria-hidden="true"></i>' . $boat->varBeam . '</li>';
            $html .= '             <li class="i-length"><i class="fa fa-arrows-h" aria-hidden="true"></i>' . $boat->varLengthOverall . '</li>';
            $html .= '             <li class="i-length"><i class="fa fa-calendar-check-o" aria-hidden="true"></i>' . $boat->yearYear . '</li>';
            $html .= '             <li class="i-length"><i class="fa fa-tag" aria-hidden="true"></i>' . $brand . '</li>';
            $html .= '             <li class="' . $boat_stock_class . '"><svg xmlns="http://www.w3.org/2000/svg" version="1.1" xmlns:xlink="http://www.w3.org/1999/xlink" xmlns:svgjs="http://svgjs.com/svgjs" width="512" height="512" x="0" y="0" viewBox="0 0 456.212 456" style="enable-background:new 0 0 512 512" xml:space="preserve" class=""><g transform="matrix(0.81,0,0,0.8099999999999999,43.340039062499926,39.920039107799596)"><path xmlns="http://www.w3.org/2000/svg" d="m415.007812 1.589844c-1.570312-.96875-3.378906-1.484375-5.222656-1.484375h-297.789062c-25.601563 0-46.429688 20.625-46.429688 45.902343l-1.75 256.441407h-53.816406c-5.523438 0-10 4.476562-10 10v46.199219c.0351562 33.925781 27.527344 61.417968 61.453125 61.457031.6875 0 1.378906-.070313 2.054687-.210938h248.457032c1.480468.105469 2.972656.175781 4.476562.175781 34.453125-.0625 62.351563-28 62.363282-62.453124v-149.90625h67.40625c5.523437 0 10-4.476563 10-10v-148.445313c-.121094-23.871094-17.601563-44.101563-41.203126-47.675781zm-395.007812 357.0625v-36.199219h234.078125v35.167969c-.011719 15.667968 5.882813 30.761718 16.511719 42.273437h-210.503906c-.472657.003907-.941407.039063-1.40625.109375-21.753907-1.480468-38.652344-19.546875-38.679688-41.351562zm338.804688-309.386719v308.351563c-.019532 11.179687-4.460938 21.890624-12.355469 29.800781-7.558594 7.753906-17.828125 12.28125-28.648438 12.625-.539062-.09375-1.085937-.140625-1.636719-.144531h-3.515624c-21.78125-2.144532-38.425782-20.386719-38.570313-42.273438v-45.175781c0-5.519531-4.476563-10-10-10h-180.261719l1.75-256.371094c.144532-14.460937 11.964844-26.082031 26.429688-25.972656h256.742187c-6.441406 8.355469-9.9375 18.609375-9.933593 29.160156zm77.40625 138.445313h-57.40625v-138.445313c.261718-15.667969 13.035156-28.226563 28.703124-28.226563 15.667969 0 28.441407 12.558594 28.703126 28.226563zm0 0" fill="#000000" data-original="#000000"></path><path xmlns="http://www.w3.org/2000/svg" d="m128.386719 108.441406h94.710937c5.523438 0 10-4.476562 10-10 0-5.523437-4.476562-10-10-10h-94.710937c-5.523438 0-10 4.476563-10 10 0 5.523438 4.476562 10 10 10zm0 0" fill="#000000" data-original="#000000"></path><path xmlns="http://www.w3.org/2000/svg" d="m305.058594 159.941406h-176.671875c-5.523438 0-10 4.476563-10 10 0 5.519532 4.476562 10 10 10h176.671875c5.523437 0 10-4.480468 10-10 0-5.523437-4.476563-10-10-10zm0 0" fill="#000000" data-original="#000000"></path><path xmlns="http://www.w3.org/2000/svg" d="m305.058594 231.4375h-176.671875c-5.523438 0-10 4.480469-10 10 0 5.523438 4.476562 10 10 10h176.671875c5.523437 0 10-4.476562 10-10 0-5.519531-4.476563-10-10-10zm0 0" fill="#000000" data-original="#000000"></path></g></svg>' . $boat_stock . '</li>';
            $html .= '             </ul>';
            // $html .= '             <div class="overlay-btn">';
            // $html .= '                  <a href="' . $recordLinkUrl . '" class="ac-btn"> <span class="text" title="View Details">View Details</span><span class="line"></span></a>';
            // $html .= '             </div>';
            $html .= '         </div>';


            $html .= '     </div>';
            $html .= ' </div>';
        }
        $html .= ' </div>';
        $html .= ' <div id="pagination">';
        $html .= $fill->links();
        $html .= ' </div>';

        if ($responsecount == 0) {
            $html = '<p>Your search did not match with any records.</p>';
        }

        $response['response_html'] = $html;
        $response['total_count'] = $responsecount . " Result(s)";




        return $response;
    }

    /**
     * This method loads boat list view
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
        $paginate = 8;
        $boat = Boat::getFrontList($paginate);
        $data['boat'] = $boat;

        if (isset($pageContent->varTitle) && !empty($pageContent->varTitle)) {
            view()->share('detailPageTitle', $pageContent->varTitle);
        }
        // End CMS PAGE Front Private, Password Prottected Code 
        return view('boat::frontview.boat', $data);
    }

    /**
     * This method loads boat detail view
     * @param   Alias of record
     * @return  View
     * @since   2020-01-17
     * @author  NetQuick
     */
    public function detail($alias)
    {
        $id = slug::resolve_alias($alias);
        $boat = Boat::getFrontDetail($id);
        $works = Work::getFrontListService();
        if (!empty($boat) || !empty($works)) {

            $PAGE_CONTENT = '';
            $description = \App\Helpers\FrontPageContent_Shield::renderBuilder($boat->txtDescription);
            // dd($description['response']);


            $videoIDAray = explode(',', $boat->fkIntVideoId);
            $videoObj = Video::getVideoData($videoIDAray);
            $metaInfo = array('varMetaTitle' => $boat->varMetaTitle, 'varMetaKeyword' => $boat->varMetaKeyword, 'varMetaDescription' => $boat->varMetaDescription);
            $data = array();

            $breadcrumb = [];
            $segmentArr = Request::segments();
            $url = '';
            foreach ($segmentArr as $key => $value) {
                $url .= $value . '/';
                $breadcrumb[$key]['title'] = ucwords(str_replace('-', ' ', $value));
                $breadcrumb[$key]['url'] = rtrim($url, '/');
            }
            $moduelFrontPageUrl = MyLibrary::getFront_Uri('boat')['uri'];
            $moduleFrontWithCatUrl = ($alias != false) ? $moduelFrontPageUrl : $moduelFrontPageUrl;

            $similarBoat = Boat::getSimilarRecordList($boat->id);

            $data['boat'] = $boat;
            $data['alias'] = $alias;
            $data['similarBoat'] = $similarBoat;
            $data['metaInfo'] = $metaInfo;
            $data['breadcrumb'] = $breadcrumb;
            $data['moduleFrontWithCatUrl'] = $moduleFrontWithCatUrl;
            $data['description'] = $description;
            $data['videoObj'] = $videoObj;
            $data['PAGE_CONTENT']['assets'] = $PAGE_CONTENT;
            $data['txtDescription'] = FrontPageContent_Shield::renderBuilder($boat->txtDescription, $boat->id);
            $data['works'] = $works;


            if (isset($boat->fkIntImgId) && !empty($boat->fkIntImgId)) {
                $imageArr = explode(',', $boat->fkIntImgId);
                view()->share('SHARE_IMG', $imageArr[0]);
            }

            if (isset($boat->varMetaTitle) && !empty($boat->varMetaTitle)) {
                view()->share('META_TITLE', $boat->varMetaTitle);
            }

            if (isset($boat->varMetaKeyword) && !empty($boat->varMetaKeyword)) {
                view()->share('META_KEYWORD', $boat->varMetaKeyword);
            }
            if (isset($boat->varMetaDescription) && !empty($boat->varMetaDescription)) {
                view()->share('META_DESCRIPTION', substr(trim($boat->varMetaDescription), 0, 500));
            }

            return view('boat::frontview.boat-detail', $data);
        } else {
            abort(404);
        }
    }
}
