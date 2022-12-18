<?php

/**
 * The FrontController class handels Preloaded data for front side
 * configuration  process (ORM code Updates).
 * @package   Netquick powerpanel
 * @license   http://www.opensource.org/licenses/BSD-3-Clause
 * @version   1.1
 * @since     2017-08-09
 * @author    NetQuick
 */

namespace App\Http\Controllers;

use App\Alias;
use App\Helpers\Page_hits;
use App\Helpers\Document_hits;
use App\Helpers\FrontPageContent_Shield;
use App\Helpers\MenuBuilder;
use App\Helpers\MyLibrary;
use App\Helpers\resize_image;
use App\Helpers\time_zone;
use App\Http\Controllers\Controller;
use App\Http\Traits\slug;
use App\LiveUsers;
use App\LoginLog;
use App\User;
use Config;
use DB;
use File;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Request;
use Jenssegers\Agent\Agent;
use Powerpanel\Alerts\Models\Alerts;
use Powerpanel\Banner\Models\Banner;
use Powerpanel\CmsPage\Models\CmsPage;
use Powerpanel\Department\Models\Department;
use Powerpanel\Menu\Models\Menu;
use Powerpanel\QuickLinks\Models\QuickLinks;

class FrontController extends Controller
{

    use slug;

    protected $breadcrumb = [];
    protected $sitemap_content;
    private $ip = '';

    public function __construct()
    {
        view()->share('CDN_PATH', Config::get('Constant.CDN_PATH'));
        $device = Config::get('Constant.DEVICE');
        $isMobile = false;
        if ($device == 'mobile') {
            $isMobile = true;
        }
        if (!Request::ajax()) {
            time_zone::time_zone();
            $menu_content = Menu::getHerderMenuItem();

            $menu_content1 = Menu::getFrontList();
            $requestedFullUrl = Request::Url();
            $homePageUrl = url('/');
            $homeActive = "";
            if ($requestedFullUrl == $homePageUrl) {
                $homeActive = "active";
            }
            // Menu Panel
            $html = '';
            $html1 = '';
            $html .= '<ul class="brand-nav brand-navbar">';
            foreach ($menu_content as $element) {
                $parentactiveclass = '';
                if (Request::segment(1) != '') {
                    $menu_childalias = Menu::getHerderChildMenuAliasItem(Request::segment(1));
                    if (isset($menu_childalias[0]->intParentMenuId) && $menu_childalias[0]->intParentMenuId != '') {
                        $menu_childalias1 = Menu::getHerderChildMenuAliasItem2($menu_childalias[0]->intParentMenuId);
                        if (isset($menu_childalias[0]->intParentMenuId) && $menu_childalias[0]->intParentMenuId != '') {
                            if ($element->id == $menu_childalias[0]->intParentMenuId) {
                                $parentactiveclass = "active";
                            } else if (isset($menu_childalias1[0]) && $element->id == $menu_childalias1[0]->intParentMenuId) {
                                $parentactiveclass = "active";
                            } else {
                                $parentactiveclass = "";
                            }
                        }
                    } else {
                        if (Request::segment(1) == $element->txtPageUrl) {
                            $parentactiveclass = "active";
                        } else if (Request::segment(1) == 'events-calender' && $element->id == 73) {
                            $parentactiveclass = "active";
                        }
                    }
                }
                $menu_childcontent = Menu::getHerderChildMenuItem($element->id);
                if ($element->txtPageUrl == 'javascript:;') {
                    $menuurl4 = 'javascript:;';
                } else {
                    $menuurl4 = url($element->txtPageUrl);
                }
                $html .= '<li class="first ' . $parentactiveclass . '"><a href="' . $menuurl4 . '" title="' . ucfirst($element->varTitle) . '">' . ucfirst($element->varTitle) . '</a>';
                if (count($menu_childcontent) > 0) {
                    $html .= '<ul class="sub-menu">';
                    $activeclass = '';
                    foreach ($menu_childcontent as $row) {

                        $menu_childalias1 = Menu::getHerderChildMenuAliasItem2($row->intParentMenuId);
                        $activeclass = '';
                        if (Request::segment(2) != '') {
                            $url = Request::segment(1) . '/' . Request::segment(2);
                        } else {
                            $url = Request::segment(1);
                        }
                        if ($url != '') {
                            if ($url == $row->txtPageUrl) {
                                $activeclass = "active";
                            } else if ($url == 'events-calender' && $row->id == 187) {
                                $activeclass = "active";
                            } else {
                                $activeclass = '';
                            }
                        }
//                            echo Request::segment(1);exit;
                        if ($row->txtPageUrl == 'javascript:;') {
                            $menuurl = 'javascript:;';
                        } else {
                            $menuurl = url($row->txtPageUrl);
                        }
                        if (isset($row->id) && $row->id != '') {

                            $html .= '<li class="first ' . $activeclass . '"><a href="' . $menuurl . '" title="' . ucfirst($row->varTitle) . '">' . ucfirst($row->varTitle) . '</a>';
                            $html .= self::getChildMenu($row->id);
                            $html .= '</li>';
                        } else {
                            $html .= '<li ' . $activeclass . '><a href="' . url($row->txtPageUrl) . '" title="' . ucfirst($row->varTitle) . '">' . ucfirst($row->varTitle) . '</a>';
                            $html .= '</li>';
                        }
                    }
                    $html .= '</ul>';
                }
                $html .= '</li>';
            }
            $html .= '</ul>';

            view()->share('HeadreMenuhtml', $html);
            
            $FooterMenu = Menu::getHomeFooterFrontRecords()->all();
            // dd($FooterMenu);
            view()->share('FooterMenu', $FooterMenu);

            if (Request::segment(1) == 'career') {
                $dataurl = 'careers';
            } else if (Request::segment(2) != '') {
                $dataurl = Request::segment(1) . '/' . Request::segment(2);
            } else {
                $dataurl = Request::segment(1);
            }

            if (Request::segment(2) != '') {
                $url = Request::segment(1) . '/' . Request::segment(2);
            } else {
                $url = Request::segment(1);
            }

            //Left Panel
            if ($url != '') {
                $left_content = Menu::getLeftPanelItem($dataurl);
//                echo "<pre/>";
                //                print_r($left_content);
                //                exit;
                $lefthtml = '';
                if (count($left_content) > 0) {

                    $lefthtml .= '<div class="col-md-3 col-sm-12 col-xs-12 animated fadeInUp load">
                <aside class="side_bar">
                <div class="sidebar_listing clearfix">
		<ul>';

                    foreach ($left_content as $element) {

                        $title = '';
                        if ($element->id == $element->intParentMenuId) {
                            $menu_childcontent = Menu::getHerderChildMenuItem($element->intParentMenuId);
                            $title = $menu_childcontent[0]->varTitle;
                            $url = $menu_childcontent[0]->txtPageUrl;
                        } else {
                            $menu_childcontent1 = Menu::getHerderChildMenuItem1($element->intParentMenuId);
                            if (isset($menu_childcontent1[0]->intParentMenuId) && $menu_childcontent1[0]->intParentMenuId == '0') {
                                $menu_childcontent = Menu::getHerderChildMenuItem($menu_childcontent1[0]->id);
                                if (isset($menu_childcontent[0]->intParentMenuId) && $menu_childcontent[0]->intParentMenuId != '') {
                                    $menu_childtitle = Menu::getHerderChildMenuTitle($menu_childcontent[0]->intParentMenuId);
                                    $title = $menu_childtitle->varTitle;
                                    $url = $menu_childtitle->txtPageUrl;
                                }
                            } else {
                                if (isset($menu_childcontent1[0]->intParentMenuId) && $menu_childcontent1[0]->intParentMenuId != '') {
                                    $menu_childcontent = Menu::getHerderChildMenuItem($menu_childcontent1[0]->intParentMenuId);
                                    if (isset($menu_childcontent[0]->intParentMenuId) && $menu_childcontent[0]->intParentMenuId != '') {
                                        $menu_childtitle = Menu::getHerderChildMenuTitle($menu_childcontent[0]->intParentMenuId);
                                        $title = $menu_childtitle->varTitle;
                                        $url = $menu_childtitle->txtPageUrl;
                                    }
                                } else {

                                    $menu_childcontent = Menu::getHerderChildMenuItem($element->id);
                                    if (isset($menu_childcontent[0]->intParentMenuId) && $menu_childcontent[0]->intParentMenuId != '') {
                                        $menu_childtitle = Menu::getHerderChildMenuTitle($menu_childcontent[0]->intParentMenuId);

                                        $title = $menu_childtitle->varTitle;
                                        $url = $menu_childtitle->txtPageUrl;
                                    }
                                }
                            }
                        }
                        if (isset($url) && $url != '') {
                            $url1 = url($url);
                        } else {
                            $url1 = url('/');
                        }
                        if ($url == 'javascript:;') {
                            $menuurl6 = 'javascript:;';
                        } else {
                            $menuurl6 = $url1;
                        }
                        $lefthtml .= '<li>
                                    <a  href="' . $menuurl6 . '" class="sidebar_title" title="' . ucfirst($title) . '">
                                       ' . ucfirst($title) . '
                                    </a>
                                    <div>
                                        <ul>';
                        $activeclass = '';
                        foreach ($menu_childcontent as $row) {
                            $activeclass = '';
                            if (Request::segment(2) != '') {
                                $url = Request::segment(1) . '/' . Request::segment(2);
                            } else {
                                $url = Request::segment(1);
                            }
                            if ($url != '') {
                                if ($url == $row->txtPageUrl) {
                                    $activeclass = 'class="active"';
                                } else {
                                    $activeclass = '';
                                }
                            }
                            $activeclass = '';

                            if ($url != '') {
                                if ($url == $row->txtPageUrl) {
                                    $activeclass = 'class="active"';
                                    $activeclass1 = 'active';
                                } else {
                                    $activeclass = '';
                                    $activeclass1 = '';
                                }
                            }
                            if (isset($row->id) && $row->id != '') {
                                $menu_childcontent1 = Menu::getHerderChildMenuItem($row->id);
                                if (count($menu_childcontent1) == '') {
                                    if ($row->txtPageUrl == 'javascript:;') {
                                        $menuurl = 'javascript:;';
                                    } else {
                                        $menuurl = url($row->txtPageUrl);
                                    }
                                    $lefthtml .= '<li ' . $activeclass . '><a href="' . $menuurl . '" title="' . ucfirst($row->varTitle) . '">' . ucfirst($row->varTitle) . '</a> ';
                                    $lefthtml .= '</li>';
                                } else {
                                    if ($row->txtPageUrl == 'javascript:;') {
                                        $menuurl = 'javascript:;';
                                    } else {
                                        $menuurl = url($row->txtPageUrl);
                                    }
                                    $lefthtml .= '<li class="dropdown first ' . $activeclass1 . '">
                                    <a class="dropdown_toggle" href="' . $menuurl . '" title="' . $row->varTitle . '">
                                       ' . $row->varTitle . '
                                    </a>
                                    <span class="caret-icon"><i class="fa fa-angle-down"></i></span>
                                    <div class="dropdown-menu">';
                                    $lefthtml .= self::getLeftChildMenu($row->id, ucfirst($row->varTitle), Request::segment(1));
                                    $lefthtml .= '</div></li>';
                                }
                            } else {
                                $lefthtml .= '<li ' . $activeclass . '><a href="' . url($row->txtPageUrl) . '" title="' . ucfirst($row->varTitle) . '">' . ucfirst($row->varTitle) . '</a>';
                                $lefthtml .= '</li>';
                            }
                        }
                    }
                    $lefthtml .= '</ul></div></aside></div>';
                    view()->share('LeftPanelhtml', $lefthtml);
                } else {
                    $lefthtml .= '';
                    view()->share('LeftPanelhtml', $lefthtml);
                }
            }
            if (isset($menu_content1[2]) && $menu_content1[2][0]['menuType']['chrPublish'] == 'Y') {
                MenuBuilder::loadMenu($menu_content1[2], 'footerMenu', $isMobile);
            }
            $this->sitemap_content = $menu_content1;
            if (Request::segment(1) != 'download' && Request::segment(1) != 'viewPDF') {
                $this->shareData();
            }
        }
        
    }

    public function getChildMenu($id)
    {

        $menu_childcontent = Menu::getHerderChildMenuItem($id);

        $html = '';
        if (count($menu_childcontent) > 0) {
            $html .= '<ul class="sub-menu">';
            foreach ($menu_childcontent as $row) {
                $activeclass = '';
                $currenturl = Request::segment(1) . '/' . Request::segment(2);
                if (Request::segment(1) != '') {
                    if (Request::segment(1) == $row->txtPageUrl) {
                        $activeclass = "active";
                    } else if ($currenturl == $row->txtPageUrl) {
                        $activeclass = "active";
                    } else {
                        $activeclass = '';
                    }
                }
                if ($row->txtPageUrl == 'javascript:;') {
                    $menuurl = 'javascript:;';
                } else {
                    $menuurl = url($row->txtPageUrl);
                }
                $html .= '<li class="first ' . $activeclass . '"><a href="' . $menuurl . '" title="' . ucfirst($row->varTitle) . '">' . ucfirst($row->varTitle) . '</a>';
                $html .= self::getChildMenu($row->id);
                $html .= '</li>';
            }
            $html .= '</ul>';
        }
        return $html;
    }

    public function getMegaChildMenu($id)
    {
        $menu_childcontent = Menu::getHerderChildMenuItem($id);

        $html = '';
        if (count($menu_childcontent) > 0) {
            $html .= '<ul>';
            foreach ($menu_childcontent as $row) {
                $activeclass = '';
                $currenturl = Request::segment(1) . '/' . Request::segment(2);
                if (Request::segment(1) != '') {
                    if (Request::segment(1) == $row->txtPageUrl) {
                        $activeclass = "active";
                    } else if ($currenturl == $row->txtPageUrl) {
                        $activeclass = "active";
                    } else {
                        $activeclass = '';
                    }
                }
                if ($row->txtPageUrl == 'javascript:;') {
                    $menuurl = 'javascript:;';
                } else {
                    $menuurl = url($row->txtPageUrl);
                }
                $html .= '<li class="first ' . $activeclass . '"><a href="' . $menuurl . '" title="' . ucfirst($row->varTitle) . '">' . ucfirst($row->varTitle) . '</a>';
                $html .= self::getMegaChildMenu($row->id);
                $html .= '</li>';
            }
            $html .= '</ul>';
        }
        return $html;
    }

    public function getLeftChildMenu($id, $title, $url)
    {
        $menu_childcontent = Menu::getHerderChildMenuItem($id);
        $lefthtml = '';
        if (count($menu_childcontent) > 0) {

            $lefthtml .= '<ul>';
            foreach ($menu_childcontent as $row) {
                $activeclass = '';
                $currenturl = Request::segment(1) . '/' . Request::segment(2);
                if (Request::segment(1) != '') {
                    if (Request::segment(1) == $row->txtPageUrl) {
                        $activeclass = 'class="active"';
                    } else if ($currenturl == $row->txtPageUrl) {
                        $activeclass = 'class="active"';
                    } else {
                        $activeclass = '';
                    }
                }
                if ($row->txtPageUrl == 'javascript:;') {
                    $menuurl = 'javascript:;';
                } else {
                    $menuurl = url($row->txtPageUrl);
                }
                $lefthtml .= ' <li ' . $activeclass . '><a href="' . $menuurl . '" title="' . ucfirst($row->varTitle) . '">' . ucfirst($row->varTitle) . '</a> ';
                $lefthtml .= self::getLeftChildMenu($row->id, ucfirst($row->varTitle), url($row->txtPageUrl));
                $lefthtml .= '</li>';
            }
            $lefthtml .= '</ul>';
        }
        return $lefthtml;
    }

    public function setInnerBanner($pageObj = false)
    {

        $innerBannerArr = [];
        $innerBannerArr['currentPageTitle'] = (isset($pageObj->varTitle) ? $pageObj->varTitle : Request::segment(1));

        $defaultBanner = Banner::getDefaultBannerList();
        $innerBanner = $defaultBanner;
        if (isset($pageObj->id)) {
            if (null !== Request::segment(1) && null == Request::segment(2)) {

                $AliasId = slug::resolve_alias_for_routes(Request::segment(1));
                $moduleID = Alias::getModuleByAliasId($AliasId);
                if (isset($pageObj->id)) {
                    $innerBanner = Banner::getInnerBannerListingPage($pageObj->id, $moduleID->intFkModuleCode);
                    if (count($innerBanner) < 1) {
                        $innerBanner = $defaultBanner;
                    }
                }

                $CmsPageId = CmsPage::getPageWithModuleId();
                if (!empty($CmsPageId)) {
                    $innerBanner = Banner::getInnerBannerList($pageObj->id, $CmsPageId->intFKModuleCode);
                    if (count($innerBanner) < 1) {
                        $innerBanner = $defaultBanner;
                    }
                }
            }

            if (null !== Request::segment(2) && Request::segment(3) !== 'preview') {
                $id = slug::resolve_alias_for_routes(Request::segment(2));
//                $MODEL = '\\App\\' . Config::get('Constant.MODULE.MODEL_NAME');
                if (Config::get('Constant.MODULE.NAME_SPACE') != '') {
                    $MODEL = Config::get('Constant.MODULE.NAME_SPACE') . 'Models\\' . Config::get('Constant.MODULE.MODEL_NAME');
                } else {
                    $MODEL = '\\App\\' . Config::get('Constant.MODULE.MODEL_NAME');
                }
                if (is_numeric($id)) {
                    $recordID = $MODEL::getRecordIdByAliasID($id);
                    if (isset($recordID->id)) {
                        $recordID = (string) $recordID->id;
                        $innerBanner = Banner::getInnerBannerList($recordID, Config::get('Constant.MODULE.ID'));
                        if (count($innerBanner) < 1) {
                            $innerBanner = $defaultBanner;
                        }
                    }
                }
            }
        } else {
            $innerBanner = $defaultBanner;
        }

        $innerBannerArr['inner_banner_data'] = $innerBanner;
        return $innerBannerArr;
    }

    public function shareData()
    {
        $shareData = [];
        $pageCms = null;
        $viewingPreview = false;
        $segmentsArr = Request::segments();

        if (!empty($segmentsArr) && in_array('preview', $segmentsArr)) {
            $viewingPreview = true;
        }

        $cmsPageId = slug::resolve_alias_for_routes(!empty(Request::segment(1)) ? Request::segment(1) : 'home');

        if (null !== Request::segment(2)) {
            if (is_numeric(Request::segment(2))) {

                if (null !== Request::segment(3) && Request::segment(3) == 'preview') {
                    $cmsPageId = slug::resolve_alias_for_routes(Request::segment(1));
                    $pageCms = CmsPage::getPageByPageId($cmsPageId, true);
                } else {
                    $cmsPageId = Request::segment(2);
                    $pageCms = CmsPage::getPageByPageId($cmsPageId, false);
                }
            } else {
                $cmsPageId = slug::resolve_alias_for_routes(Request::segment(1));
                $pageCms = CmsPage::getPageByPageId($cmsPageId, true);
            }
        } else if (is_numeric($cmsPageId)) {
//             echo Request::segment(3);exit;
            $pageCms = CmsPage::getPageByPageId($cmsPageId);
        }
        if (!Request::ajax()) {
            if (isset($pageCms->varTitle) && strtolower($pageCms->varTitle) != 'home') {
                $shareData = $this->setInnerBanner($pageCms);
            } else {
                $shareData = $this->setInnerBanner();
            }

            if (File::exists(app_path() . '/ContactInfo.php') != null) {
                $contacts = \Powerpanel\ContactInfo\Models\ContactInfo::getContactDetails();
                foreach ($contacts as $contact) {
                    if (isset($contact->chrIsPrimary) && $contact->chrIsPrimary == 'Y') {
                        $objContactInfo = $contact;
                    }
                    if (isset($contact->chrIsPrimary) && $contact->chrIsPrimary == 'N') {
                        $secondaryaddress = $contact;
                    }
                }
                $shareData['objContactInfo'] = (!empty($objContactInfo)) ? $objContactInfo : '';
                $shareData['secondaryaddress'] = (!empty($secondaryaddress)) ? $secondaryaddress : '';
            }

           if (!in_array(Request::segment(1), ['login', 'logout']) && !$viewingPreview) {
                if (isset($segmentsArr[0]) && $segmentsArr[0] != end($segmentsArr)) {
                    Page_hits::insertDetailPageHits(end($segmentsArr));
                } else {
                    Page_hits::insertHits($pageCms);
                }
            }

            $sever_info = Request::server('HTTP_USER_AGENT');
            $ip_address = MyLibrary::get_client_ip();
            $ipCount = LiveUsers::getRecordCountByIp_insert($ip_address);
            if ($ipCount != 0) {
                LiveUsers::updateRecordByIp($ip_address, [
                    'updated_at' => date('Y-m-d H:i:s'),
                    'txtBrowserInf' => $sever_info,
                ]);
            } else {
                $location = MyLibrary::get_geolocation($ip_address);
                $decodedLocation = json_decode($location, true);
                if (isset($ip_address)) {

                    LiveUsers::addRecord([
                        'varIpAddress' => !empty($ip_address) ? $ip_address : null,
                        'varContinent_code' => !empty($decodedLocation['continent_code']) ? $decodedLocation['continent_code'] : null,
                        'varContinent_name' => !empty($decodedLocation['continent_name']) ? $decodedLocation['continent_name'] : null,
                        'varCountry_code2' => !empty($decodedLocation['country_code2']) ? $decodedLocation['country_code2'] : null,
                        'varCountry_code3' => !empty($decodedLocation['country_code3']) ? $decodedLocation['country_code3'] : null,
                        'varCountry_name' => !empty($decodedLocation['country_name']) ? $decodedLocation['country_name'] : null,
                        'varCountry_capital' => !empty($decodedLocation['country_capital']) ? $decodedLocation['country_capital'] : null,
                        'varState_prov' => !empty($decodedLocation['state_prov']) ? $decodedLocation['state_prov'] : null,
                        'varDistrict' => !empty($decodedLocation['district']) ? $decodedLocation['district'] : null,
                        'varCity' => !empty($decodedLocation['city']) ? $decodedLocation['city'] : null,
                        'varZipcode' => !empty($decodedLocation['zipcode']) ? $decodedLocation['zipcode'] : null,
                        'varLatitude' => !empty($decodedLocation['latitude']) ? $decodedLocation['latitude'] : null,
                        'varLongitude' => !empty($decodedLocation['longitude']) ? $decodedLocation['longitude'] : null,
                        'varIs_eu' => !empty($decodedLocation['is_eu']) ? $decodedLocation['is_eu'] : null,
                        'varCalling_code' => !empty($decodedLocation['calling_code']) ? $decodedLocation['calling_code'] : null,
                        'varCountry_tld' => !empty($decodedLocation['country_tld']) ? $decodedLocation['country_tld'] : null,
                        'varLanguages' => !empty($decodedLocation['languages']) ? $decodedLocation['languages'] : null,
                        'varCountry_flag' => !empty($decodedLocation['country_flag']) ? $decodedLocation['country_flag'] : null,
                        'varGeoname_id' => !empty($decodedLocation['geoname_id']) ? $decodedLocation['geoname_id'] : null,
                        'varIsp' => !empty($decodedLocation['isp']) ? $decodedLocation['isp'] : null,
                        'varConnection_type' => !empty($decodedLocation['connection_type']) ? $decodedLocation['connection_type'] : null,
                        'varOrganization' => !empty($decodedLocation['organization']) ? $decodedLocation['organization'] : null,
                        'varCurrencyCode' => !empty($decodedLocation['currency']['code']) ? $decodedLocation['currency']['code'] : null,
                        'varCurrencyName' => !empty($decodedLocation['currency']['name']) ? $decodedLocation['currency']['name'] : null,
                        'varCurrencySymbol' => !empty($decodedLocation['currency']['symbol']) ? $decodedLocation['currency']['symbol'] : null,
                        'varTime_zoneName' => !empty($decodedLocation['time_zone']['name']) ? $decodedLocation['time_zone']['name'] : null,
                        'varTime_zoneOffset' => !empty($decodedLocation['time_zone']['offset']) ? $decodedLocation['time_zone']['offset'] : null,
                        'varTime_zoneCurrent_time' => !empty($decodedLocation['time_zone']['current_time']) ? $decodedLocation['time_zone']['current_time'] : null,
                        'varTime_zoneCurrent_time_unix' => !empty($decodedLocation['time_zone']['current_time_unix']) ? $decodedLocation['time_zone']['current_time_unix'] : null,
                        'varTime_zoneIs_dst' => !empty($decodedLocation['time_zone']['is_dst']) ? $decodedLocation['time_zone']['is_dst'] : null,
                        'varTime_zoneDst_savings' => !empty($decodedLocation['time_zone']['dst_savings']) ? $decodedLocation['time_zone']['dst_savings'] : null,
                        'txtBrowserInf' => $_SERVER['HTTP_USER_AGENT'],
                        'created_at' => date('Y-m-d H:i:s'),
                        'updated_at' => date('Y-m-d H:i:s'),
                    ]);
                }
            }
            /* code for display department listing in footer */
            $departmentFooterArr = array();
            if (File::exists(base_path() . '/packages/Powerpanel/Department/src/Models/Department.php')) {
                $departmentFooterArr = Department::getFrontListForFooter();
            }
            $shareData['departmentFooterArr'] = $departmentFooterArr;
        }

        /* code for alert module for front display in header */
        $data['alertSlides'] = array();
        $alertslides = array();
        if (File::exists(base_path() . '/packages/Powerpanel/Alerts/src/Models/Alerts.php')) {
            $alertslides = Alerts::getAlertsForHeader();
        }

        if (!empty($alertslides)) {
            foreach ($alertslides as $key => $value) {
                $linkUrl = self::getInternalLinkHtml($value);
                $data['alertSlides'][$key]['url'] = $linkUrl;
                if (isset($value->modules->varModuleName)) {
                    $data['alertSlides'][$key]['moduleName'] = $value->modules->varModuleName;
                } else {
                    $data['alertSlides'][$key]['moduleName'] = '';
                }
                if (isset($value->modules->id)) {
                    $data['alertSlides'][$key]['moduleId'] = $value->modules->id;
                } else {
                    $data['alertSlides'][$key]['moduleId'] = '';
                }
                $data['alertSlides'][$key]['varTitle'] = $value->varTitle;
                $data['alertSlides'][$key]['intAlertType'] = $value->intAlertType;
            }
        }
        
        /* end of code for module for front display in header */
        $quickLinks = array();
        if (File::exists(base_path() . '/packages/Powerpanel/QuickLinks/src/Models/QuickLinks.php')) {
            $quickLinks = QuickLinks::getHomePageList(8);
        }

        $data['quickLinks'] = array();
        if (!empty($quickLinks)) {
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
                    if (isset($link->varExtLink)) {
                        $data['quickLinks'][$qlinkcounter]['link'] = $link->varExtLink;
                    }
                    $data['quickLinks'][$qlinkcounter]['varTitle'] = $link->varTitle;
                    $data['quickLinks'][$qlinkcounter]['varLinkType'] = $link->varLinkType;
                    $qlinkcounter++;
                }
            }
        }

        $menuLinks = Menu::getHomeFooterFrontRecords();

        $data['menuLinks'] = array();
        if (!empty($menuLinks)) {
            $qlinkcounter1 = 0;
            foreach ($menuLinks as $link1) {
                $data['menuLinks'][$qlinkcounter1]['txtPageUrl'] = $link1->txtPageUrl;
                $data['menuLinks'][$qlinkcounter1]['varTitle'] = $link1->varTitle;
                $qlinkcounter1++;
            }
        }

//      echo Request::segment(1);exit;
        if (Request::segment(2) != '') {
            $url = Request::segment(1) . '/' . Request::segment(2);
        } else {
            $url = Request::segment(1);
        }
//        if ($url != '') {
        //            if (Request::segment(1) != 'previewpage' && Request::segment(1) != 'search' && Request::segment(1) != '' && Request::segment(1) != 'events-calender' && Request::segment(2) != 'preview' && Request::segment(3) != 'preview' && Request::segment(1) != 'sitemap' && Request::segment(1) != 'check_activity') {
        //
        //                $Breadcumbmid = Menu::GetBreadumbid($url);
        //
        //                if (isset($Breadcumbmid->intfkModuleId)) {
        //                    $Breadcumbid = Menu::GetFrontBreadumbid($url, $Breadcumbmid->intfkModuleId);
        //                } else {
        //                    $Breadcumbid = Menu::GetFrontBreadumbid($url, $pageCms->intFKModuleCode);
        //                }
        //
        //                if (isset($Breadcumbid->intParentMenuId)) {
        //                    $Breadcumbfirst = Menu::GetFrontBreadumbFirst($Breadcumbid->intParentMenuId);
        //                }
        //                if (isset($Breadcumbfirst->intParentMenuId)) {
        //                    $BreadcumbSecond = Menu::GetFrontBreadumbSecond($Breadcumbfirst->intParentMenuId);
        //                }
        //
        //                if (isset($BreadcumbSecond->intParentMenuId)) {
        //                    $BreadcumbThird = Menu::GetFrontBreadumbThird($BreadcumbSecond->intParentMenuId);
        //                }
        ////                echo $Breadcumbid->varTitle;
        //                $shareData['BreadcumbFirst'] = (isset($Breadcumbfirst->varTitle) ? $Breadcumbfirst->varTitle : '');
        //                $shareData['BreadcumbFisrtURL'] = (isset($Breadcumbfirst->txtPageUrl) ? $Breadcumbfirst->txtPageUrl : '');
        //                $shareData['BreadcumbSecond'] = (isset($BreadcumbSecond->varTitle) ? $BreadcumbSecond->varTitle : '');
        //                $shareData['BreadcumbSecondURL'] = (isset($BreadcumbSecond->txtPageUrl) ? $BreadcumbSecond->txtPageUrl : '');
        //                $shareData['BreadcumbThird'] = (isset($BreadcumbThird->varTitle) ? $BreadcumbThird->varTitle : '');
        //                $shareData['BreadcumbThirdURL'] = (isset($BreadcumbThird->txtPageUrl) ? $BreadcumbThird->txtPageUrl : '');
        //                $shareData['intParentMenuId'] = (isset($Breadcumbid->intParentMenuId) ? $Breadcumbid->intParentMenuId : '');
        //                $shareData['intParentMenuSecondId'] = (isset($Breadcumbfirst->intParentMenuId) ? $Breadcumbfirst->intParentMenuId : '');
        //                $shareData['intParentMenuThirdId'] = (isset($BreadcumbSecond->intParentMenuId) ? $BreadcumbSecond->intParentMenuId : '');
        //            }
        //            $detailtitle = '';
        //            $detailtitle = Alias::getAlias(Request::segment(1));
        //            if ($detailtitle != '' && Request::segment(1) != 'previewpage' && Request::segment(1) != 'search' && Request::segment(1) != 'news-letter' && Request::segment(1) != 'events-calender' && Request::segment(2) != 'preview' && Request::segment(3) != 'preview' && Request::segment(1) != 'sitemap' && Request::segment(2) != 'preview') {
        //                $Breadcumbmid = Menu::GetBreadumbid($url);
        //                if (isset($Breadcumbmid->intfkModuleId)) {
        //                    $Breadcumbid1 = Menu::GetFrontBreadumbid($url, $Breadcumbmid->intfkModuleId);
        //                } else {
        //                    $Breadcumbid1 = Menu::GetFrontBreadumbid($url, $pageCms->intFKModuleCode);
        //                }
        //                $shareData['BreadcumbFirst1'] = (isset($Breadcumbid1->varTitle) ? $Breadcumbid1->varTitle : '');
        //                $shareData['BreadcumbFisrtURL1'] = (isset($Breadcumbid1->txtPageUrl) ? $Breadcumbid1->txtPageUrl : '');
        //                $shareData['intParentMenuId1'] = (isset($Breadcumbid1->intParentMenuId) ? $Breadcumbid1->intParentMenuId : '');
        //
        //                $detailpagetitle1 = CmsPage::GetFrontdetaiBreadumb($detailtitle->id);
        //
        //                $shareData['breadcumbcurrentPageTitle'] = isset($detailpagetitle1->varTitle) ? $detailpagetitle1->varTitle : Request::segment(1);
        ////echo $Breadcumbid1->intParentMenuId;exit;
        //                if (isset($Breadcumbid1->intParentMenuId) && $Breadcumbid1->intParentMenuId != '0') {
        //                    $detailpagetitle = Menu::GetFrontdetaiBreadumbtitle($Breadcumbid1->intParentMenuId);
        //
        //                    $shareData['currentPageTitle'] = isset($detailpagetitle->varTitle) ? $detailpagetitle->varTitle : '';
        //                } else {
        //                    $detailpagetitle = Menu::GetFrontdetaiBreadumbtitle1($url);
        //                    $shareData['currentPageTitle'] = isset($detailpagetitle->varTitle) ? $detailpagetitle->varTitle : '';
        //                }
        //            } else if (Request::segment(2) == 'preview' && Request::segment(3) != 'preview') {
        //
        //                $Breadcumbid1 = CmsPage::getPriviewPageByPageId(Request::segment(1));
        //                $shareData['currentPageTitle'] = (isset($Breadcumbid1->varTitle) ? $Breadcumbid1->varTitle : '');
        //            } else {
        //
        //                if (Request::segment(1) != 'previewpage' && Request::segment(1) != '' && Request::segment(1) != 'search' && Request::segment(1) != 'news-letter' && Request::segment(1) != 'events-calender' && Request::segment(3) != 'preview' && Request::segment(1) != 'sitemap' && Request::segment(2) != 'preview' && Request::segment(1) != 'check_activity') {
        //                    $Breadcumbmid = Menu::GetBreadumbid($url);
        //                    if (isset($Breadcumbmid->intfkModuleId)) {
        //                        $Breadcumbid1 = Menu::GetFrontBreadumbid($url, $Breadcumbmid->intfkModuleId);
        //                    } else {
        //                        $Breadcumbid1 = Menu::GetFrontBreadumbid($url, $pageCms->intFKModuleCode);
        //                    }
        //                    $shareData['BreadcumbFirst1'] = (isset($Breadcumbid1->varTitle) ? $Breadcumbid1->varTitle : '');
        //                    $shareData['BreadcumbFisrtURL1'] = (isset($Breadcumbid1->txtPageUrl) ? $Breadcumbid1->txtPageUrl : '');
        //                    $shareData['intParentMenuId1'] = (isset($Breadcumbid1->intParentMenuId) ? $Breadcumbid1->intParentMenuId : '');
        //
        //                    $detailpagetitle1 = CmsPage::GetFrontdetaiBreadumb($detailtitle->id);
        //                    $shareData['breadcumbcurrentPageTitle'] = isset($detailpagetitle1->varTitle) ? $detailpagetitle1->varTitle : Request::segment(1);
        //
        //                    if (isset($Breadcumbid1->intParentMenuId) && $Breadcumbid1->intParentMenuId != '') {
        //                        $detailpagetitle = Menu::GetFrontdetaiBreadumbtitle($Breadcumbid1->intParentMenuId);
        //
        //                        $shareData['currentPageTitle'] = isset($detailpagetitle->varTitle) ? $detailpagetitle->varTitle : Request::segment(1);
        //                    } else {
        //                        $detailpagetitle = Menu::GetFrontdetaiBreadumbtitle1($url);
        //
        //                        $shareData['currentPageTitle'] = isset($detailpagetitle->varTitle) ? $detailpagetitle->varTitle : '';
        //                    }
        //                } else {
        //                    if (Request::segment(1) != 'sitemap') {
        //                        $detailpagetitle = Menu::GetFrontdetaiBreadumb(Request::segment(1));
        //                        $shareData['currentPageTitle'] = isset($detailpagetitle->varTitle) ? $detailpagetitle->varTitle : Request::segment(1);
        //                    } else {
        //                        $detailpagetitle = Menu::GetFrontdetaiBreadumbtitle1($url);
        //
        //                        $shareData['currentPageTitle'] = isset($detailpagetitle->varTitle) ? $detailpagetitle->varTitle : '';
        //                    }
        //                }
        //            }
        //        }
        $Breadcumbmid = Menu::GetBreadumbid($url);
        $shareData['currentPageTitle'] = isset($Breadcumbid->varTitle) ? $Breadcumbid->varTitle : ucfirst(Request::segment(1));

        $shareData['PAGE_ID'] = isset($pageCms->id) ? $pageCms->id : ucfirst(Request::segment(1));
        $shareData['META_TITLE'] = isset($pageCms->varMetaTitle) ? $pageCms->varMetaTitle : ucfirst(Request::segment(1));
        $shareData['META_KEYWORD'] = isset($pageCms->varMetaKeyword) ? $pageCms->varMetaKeyword : Config::get('Constant.META_KEYWORD');
        $shareData['META_DESCRIPTION'] = isset($pageCms->varMetaDescription) ? substr(trim($pageCms->varMetaDescription), 0, 200) : Config::get('Constant.DEFAULT_META_DESCRIPTION');
        $shareData['PAGE_CONTENT'] = isset($pageCms->txtDescription) ? FrontPageContent_Shield::renderBuilder($pageCms->txtDescription) : Config::get('Constant.PAGE_CONTENT');
        $shareData['PAGE_CONTENT_BOTTOM'] = isset($pageCms->txtDescription_bottom) ? $pageCms->txtDescription_bottom : Config::get('Constant.PAGE_CONTENT_BOTTOM');
        $shareData['APP_URL'] = Config::get('Constant.ENV_APP_URL');
        $shareData['SHARE_IMG'] = Config::get('Constant.FRONT_LOGO_ID');
        $shareData['VIEWING_PREVIEW'] = $viewingPreview;
        $shareData['CDN_PATH'] = Config::get('Constant.CDN_PATH');

        $shareData['quickLinks'] = $data['quickLinks'];
        $shareData['menuLinks'] = $data['menuLinks'];
        $shareData['alertSlides'] = $data['alertSlides'];

        $alertsArr = array();
        if (File::exists(base_path() . '/packages/Powerpanel/Alerts/src/Models/Alerts.php')) { 
            $alertsArr = Alerts::getAlertsForListing();
        }
        $shareData['alertsArr'] = $alertsArr;
        view()->share($shareData);
    }

    public function setdocumentCounter()
    {
        $docId = Input::get('docId');
        $counterType = Input::get('counterType');

        if (!empty($docId) && !empty($counterType)) {
            Document_hits::insertHits($docId, $counterType);
        }
    }

    public function download($filename)
    {
        $AWSContants = MyLibrary::getAWSconstants();
        $_APP_URL = $AWSContants['CDN_PATH'];
        $saveAsLocalPath = public_path('/documents/' . $filename);
        $file_path = $AWSContants['S3_MEDIA_BUCKET_DOCUMENT_PATH'] . '/' . $filename;
        $fileExists = Mylibrary::filePathExist($file_path);
        Aws_File_helper::getObjectWithSaveAs($file_path, $saveAsLocalPath);
        return response()->download($saveAsLocalPath, $filename);
    }

    public static function getInternalLinkHtml($value)
    {
        $linkUrl = url('/');
        if ($value->varLinkType == 'external') {

            $moduleCode = isset($value->modules->id) ? $value->modules->id : '';
            $moduleListforFindpageArray = [
                'publications-category' => 'publications',
                'publications' => 'publications',
                'events-category' => 'events',
                'events' => 'events',
                'news-category' => 'news-category',
                'news' => 'news-category',
                'faq-category' => 'faq-category',
            ];

            $categoryFieldsets = [
                'publications' => 'publications-category',
                'events' => 'event-category',
                'news' => 'news-category',
            ];
            if ($moduleCode != 4) {
                $catAlias = false;
                $catfieldName = '';
                if (isset($value->modules->varModuleName)) {
                    if (array_key_exists($value->modules->varModuleName, $moduleListforFindpageArray)) {
                        $moduleData = DB::table('module')->select('id')->where('varModuleName', $moduleListforFindpageArray[$value->modules->varModuleName])->first();
                        $moduleCode = $moduleData->id;
                    }
                }
                $pageAlias = CmsPage::select('alias.varAlias')
                    ->leftJoin('alias', 'alias.id', '=', 'cms_page.intAliasId')
                    ->where('cms_page.intFKModuleCode', $moduleCode)
                    ->where('cms_page.chrMain', 'Y')
                    ->where('cms_page.chrPublish', 'Y')
                    ->where('cms_page.chrDelete', 'N')
                    ->first();
                if (isset($pageAlias->varAlias)) {
                    $value->pageAlias = $pageAlias->varAlias;
                }

                if (isset($value->modules->varTableName)) {
                    if (\Schema::hasColumn($value->modules->varTableName, 'intAliasId')) {

                        $modulefields = ['varTitle', 'intAliasId', 'alias.varAlias as recordalias'];
                        if (\Schema::hasColumn($value->modules->varTableName, 'txtCategories')) {
                            $catAlias = true;
                            $catfieldName = 'txtCategories';
                            array_push($modulefields, $value->modules->varTableName . '.txtCategories');
                        }
                        if (\Schema::hasColumn($value->modules->varTableName, 'intFKCategory')) {
                            $catAlias = true;
                            $catfieldName = 'intFKCategory';
                            array_push($modulefields, $value->modules->varTableName . '.intFKCategory');
                        }
                        $recordData = DB::table($value->modules->varTableName)
                            ->select($modulefields)
                            ->join('alias', 'alias.id', '=', $value->modules->varTableName . '.intAliasId')
                            ->where($value->modules->varTableName . '.id', $value->fkIntPageId);
                        if (\Schema::hasColumn($value->modules->varTableName, 'chrMain')) {
                            $recordData = $recordData->where($value->modules->varTableName . '.chrMain', 'Y');
                        }
                        if (\Schema::hasColumn($value->modules->varTableName, 'chrIsPreview')) {
                            $recordData = $recordData->where($value->modules->varTableName . '.chrIsPreview', 'N');
                        }
                        $recordData = $recordData->first();

                        if ($catAlias) {
                            if ($catfieldName == 'txtCategories') {
                                if (isset($categoryFieldsets[$value->modules->varModuleName])) {
                                    $categoryRecordAlias = Mylibrary::getRecordAliasByModuleNameRecordId($categoryFieldsets[$value->modules->varModuleName], $recordData->txtCategories);
                                }
                            } else {
                                if (isset($categoryFieldsets[$value->modules->varModuleName])) {
                                    $categoryRecordAlias = Mylibrary::getRecordAliasByModuleNameRecordId($categoryFieldsets[$value->modules->varModuleName], $recordData->intFKCategory);
                                }
                            }
                            //                        if ($categoryRecordAlias != "") {
                            //                            $linkUrl = url('/') . '/' . $pageAlias->varAlias . '/' . $categoryRecordAlias . '/' . $recordData->recordalias;
                            //                        } else {
                            //                            $linkUrl = url('/') . '/' . $pageAlias->varAlias . '/' . $recordData->recordalias;
                            //                        }
                        } else {
                            //                        if (isset($recordData->recordalias)) {
                            //                            $linkUrl = url('/') . '/' . $pageAlias->varAlias . '/' . $recordData->recordalias;
                            //                        } else {
                            //                            $linkUrl = url('/') . '/' . $pageAlias->varAlias;
                            //                        }
                        }
                    }
                }
            } else {
                $pageAlias = CmsPage::select('alias.varAlias')
                    ->leftJoin('alias', 'alias.id', '=', 'cms_page.intAliasId')
                    ->where('cms_page.id', $value->fkIntPageId)
                    ->where('cms_page.chrMain', 'Y')
                    ->where('cms_page.chrPublish', 'Y')
                    ->where('cms_page.chrDelete', 'N')
                    ->first();
                if (isset($pageAlias->varAlias)) {
                    $value->pageAlias = $pageAlias->varAlias;
                    $linkUrl = url('/' . $pageAlias->varAlias);
                }
            }
        }

        return $linkUrl;
    }

    public function check_activity()
    {
        $log_id = base64_decode($_REQUEST['rfn']);
        $arrResults = LoginLog::getRecordbyId($log_id);
        $id = $arrResults['id'];
        $fkIntUserId = $arrResults['fkIntUserId'];
        $varIpAddress = $arrResults['varIpAddress'];
        $varBrowser_Name = $arrResults['varBrowser_Name'];
        $varDevice = $arrResults['varDevice'];
        $dat_time = date('' . Config::get('Constant.DEFAULT_DATE_FORMAT') . ' ' . Config::get('Constant.DEFAULT_TIME_FORMAT') . '', strtotime($arrResults['created_at']->setTimezone(Config::get('Constant.DEFAULT_TIME_ZONE'))));

        $User_Results = User::getRecordByIdWithoutRole($arrResults['fkIntUserId']);
        if ($User_Results['fkIntImgId'] != '') {
            $user_img = $User_Results['fkIntImgId'];
            $logo_url = resize_image::resize($user_img);
        } else {
            $logo_url = url('/assets/images/man.png');
        }
        $email = MyLibrary::getDecryptedString($User_Results['email']);
        echo view('errors.check_activity', compact('varBrowser_Name', 'logo_url', 'email', 'varDevice', 'dat_time', 'varIpAddress', 'id'))->render();
        exit();
    }

    public function check_activity_no_secure()
    {
        $record = Request::all();
        DB::table('login_history')
            ->where('id', $record['id'])
            ->update(['chrActive' => 'N']);
    }

    public function UpdateNotificationToken()
    {
        $agent = new Agent();
        $mybrowser = $agent->browser();

        $notificationdata = DB::table('notificationtoken')
            ->select('*')
            ->where('browser', '=', $mybrowser)
            ->get();

        $record = Request::all();
        if (count($notificationdata) > 0) {
            foreach ($notificationdata as $ddata) {
                if ($ddata->browser == $mybrowser) {
                    DB::table('notificationtoken')
                        ->where('browser', '=', $mybrowser)
                        ->update(['browser' => $mybrowser, 'notificationtoken' => $record['token'], 'notificationmsg' => $record['message'], 'notificationerr' => $record['error']]);
                } else {
                    $insertqueryArray = array();
                    $insertqueryArray['browser'] = $mybrowser;
                    $insertqueryArray['notificationtoken'] = $record['token'];
                    $insertqueryArray['notificationmsg'] = $record['message'];
                    $insertqueryArray['notificationerr'] = $record['error'];
                    DB::table('notificationtoken')->insertGetId($insertqueryArray);
                }
            }
        } else {
            $insertqueryArray = array();
            $insertqueryArray['browser'] = $mybrowser;
            $insertqueryArray['notificationtoken'] = $record['token'];
            $insertqueryArray['notificationmsg'] = $record['message'];
            $insertqueryArray['notificationerr'] = $record['error'];
            DB::table('notificationtoken')->insertGetId($insertqueryArray);
        }
    }

    public function PagePassURLListing()
    {
        $record = Request::input();
        $pagedata = DB::table($record['tablename'])
            ->select('*')
            ->where('id', '=', $record['id'])
            ->first();
        if ($pagedata->varPassword == $record['passwordprotect']) {
            $html = FrontPageContent_Shield::renderBuilder($pagedata->txtDescription);
            echo json_encode($html['response']);
        } else {
            $response = array("error" => 1, 'validatorErrors' => 'Password Does Not Match');
            echo json_encode($response);
        }
    }

}