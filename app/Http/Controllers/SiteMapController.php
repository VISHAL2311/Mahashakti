<?php

namespace App\Http\Controllers;

use Powerpanel\Menu\Models\MenuType;
use Powerpanel\Menu\Models\Menu;

use Spatie\Sitemap\SitemapGenerator;
use Powerpanel\Services\Models\Services;
use Powerpanel\Team\Models\Team;
use Powerpanel\Blogs\Models\Blogs;
use Powerpanel\Boat\Models\Boat;
use Powerpanel\Work\Models\Work;

class SiteMapController extends FrontController {

    public function __construct() {
        parent::__construct();
    }

    public function index() {
        $menu_array = $this->buildMenu();
        $siteMap = $this->make_menu(0, "", $menu_array);
        view()->share('META_TITLE', "Site Map of Avalon Marine Group");
        view()->share('META_KEYWORD', "Site Map");
        view()->share('META_DESCRIPTION', "With the help of a sitemap of Avalon Marine Group, you can seamlessly navigate across all website pages and save your time.");
        return view('sitemap', compact('siteMap'));
    }

    /**
     * This method handels loading process of generating array from menu data
     * @return  Menu array
     * @since   04-08-2017
     * @author  NetQuick
     */
    public function buildMenu($position = null) {
        if ($position == null) {
            $position = 5;
        }
        $response = false;
        $menu_array = array();
        $result = $this->sitemap_content;        
        if (!empty($result[$position])) {
            
            foreach ($result[$position] as $menuItem) {
            
            
                $menu_array['items'][$menuItem->id] = array(
                    'id' => $menuItem->id,
                    'pid' => $menuItem->intParentMenuId,
                    'title' => $menuItem->varTitle,
                    'url' => $menuItem->txtPageUrl,
                    'active' => $menuItem->chrActive,
                    'position' => $menuItem->intPosition,
                    'mega_menu' => $menuItem->chrMegaMenu,
                    'chrInMobile' => $menuItem->chrInMobile,
                    'chrInWeb' => $menuItem->chrInWeb,
                    'chr_publish' => $menuItem->chrPublish
                );
                $menu_array['parents'][$menuItem->intParentMenuId][] = $menuItem->id;
            }
        }
        $response = $menu_array;
        return $response;
    }

    public function make_menu($parentId = false, $parentUrl = false, $menu_array = false) {
        $parent_order = 1;
        $response = false;
        $active = false;
        $html = '';

        if (isset($menu_array['parents'][$parentId])) {
            $child_order = 1;
            $html = '';
            foreach ($menu_array['parents'][$parentId] as $itemId) {
                if (strtolower($menu_array['items'][$itemId]['url']) != 'sitemap') {
                    $child = array_column($menu_array['items'], 'pid');
                    $hasChild = (in_array($itemId, $child)) ? true : false;
                    $active = $menu_array['items'][$itemId]['active'];
                    $chr_publish = $menu_array['items'][$itemId]['chr_publish'];
                    $cur_url = $menu_array['items'][$itemId]['url'];
                    $html .= '<li>';
                    $html .= '<a href="' . $cur_url . '" title="' . $menu_array['items'][$itemId]['title'] . '" >';

                    if ($menu_array['items'][$itemId]['pid'] < 1) {
                        $html .= $menu_array['items'][$itemId]['title'];
                        if ($menu_array['items'][$itemId]['id'] == 12) {
                            $services = Services::getServicesSiteMapData();
                            $html .= '<ul>';
                            foreach ($services as $serviceitem) {
                                $html .= '<li><a href="' . $cur_url . '/' . $serviceitem->alias->varAlias . '" title="' . ucwords($serviceitem->varTitle) . '">' . ucwords($serviceitem->varTitle) . '</a></li>';
                            }
                            $html .= '</ul>';
                        }
                        if ($menu_array['items'][$itemId]['id'] == 22) {
                            $works = Work::getWorkSiteMapData();
                            $html .= '<ul>';
                            foreach ($works as $workitem) {
                                $html .= '<li><a href="' . $cur_url . '/' . $workitem->alias->varAlias . '" title="' . ucwords($workitem->varTitle) . '">' . ucwords($workitem->varTitle) . '</a></li>';
                            }
                            $html .= '</ul>';
                        }
                        if ($menu_array['items'][$itemId]['id'] == 13) {
                            $team = Team::getTeamSiteMapData();
                            $html .= '<ul>';
                            foreach ($team as $teamitem) {
                                $html .= '<li><a href="' . $cur_url . '/' . $teamitem->alias->varAlias . '" title="' . ucwords($teamitem->varTitle) . '">' . ucwords($teamitem->varTitle) . '</a></li>';
                            }
                            $html .= '</ul>';
                        } 
                        if ($menu_array['items'][$itemId]['id'] == 15) {
                            $blogs = Blogs::getBlogSiteMapData();
                            $html .= '<ul>';
                            foreach ($blogs as $blogitem) {
                                $html .= '<li><a href="' . $cur_url . '/' . $blogitem->alias->varAlias . '" title="' . ucwords($blogitem->varTitle) . '">' . ucwords($blogitem->varTitle) . '</a></li>';
                            }
                            $html .= '</ul>';
                        }
                        if ($menu_array['items'][$itemId]['id'] == 51) {
                            $boat = Boat::getBoatSiteMapData();
                            $html .= '<ul>';
                            foreach ($boat as $boatitem) {
                                $html .= '<li><a href="' . $cur_url . '/' . $boatitem->alias->varAlias . '" title="' . ucwords($boatitem->varTitle) . '">' . ucwords($boatitem->varTitle) . '</a></li>';
                            }
                            $html .= '</ul>';
                        }
                    } else {
                        $html .= $menu_array['items'][$itemId]['title'];
                    }
                    $html .= '</a>';
                    if ($hasChild) {
                        $html .= '<ul>';
                    }
                    $html .= Self::make_menu($itemId, $cur_url, $menu_array);
                    $html .= '</li>';
                    if ($hasChild) {
                        $html .= '</ul>';
                    }
                    $parent_order++;
                    $child_order++;
                }
            }
            $html .= '';
        }
        $response = $html;
        return $response;
    }

    public function generateSitemap() {
        $generatedSitemap = SitemapGenerator::create(url('/'))->writeToFile(public_path() . '/sitemap.xml');
        if ($generatedSitemap) {
            return redirect(url('/sitemap.xml'));
        }
    }

}
