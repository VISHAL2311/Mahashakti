<?php

namespace App\Helpers;

use File;
use Request;

class FrontPageContent_Shield {

    public static $assets = array();

    static function renderBuilder($data) {
        Self::$assets['js']['lib'] = array();

        Self::$assets['js']['modulejs'] = array();
        $response = $data;
        $data = json_decode($data, true);
        if (is_array($data)) {
            $response = '';
            $i = 0;
            $two = 1;
            $three = 1;
            $four = 1;
            $two_part_row_one_count = 1;
            foreach ($data as $section) {

                if (!is_array($section)) {
                    $section2 = json_decode($section, true);
                    $j = 0;
                    $two_1 = 1;
                    $three_1 = 1;
                    $four_1 = 1;
                    if (!empty($section2) && is_array($section2)) {
                        foreach ($section2 as $section1) {
                            if (isset($section1['val']['module']) && $section1['val']['module'] != '') {
                                $module = $section1['val']['module'];
                            } else {
                                $module = $section1['type'];
                            }
                            if ($module == 'only_title') {
                                $content = $section1['val']['content'];
                                $extclass = '';
                                $response .= Self::OnlyTitleHTML($content, $extclass);
                            } else if ($module == 'iframe') {
                                $content = html_entity_decode($section1['val']['content']);
                                $extclass = isset($section1['val']['extclass']) ? $section1['val']['extclass'] : '';
                                $response .= Self::OnlyIframeHTML($content, $extclass);
                            } else if ($module == 'partitondata') {
                                if ($section1['partitionclass'] == 'TwoColumns') {
                                    $content = $section1['val'];
                                    $type = $section1['gentype'];
                                    $subtype = $section1['subtype'];
                                    $partitionclass = $section1['partitionclass'];
                                    $response .= Self::TwoColumnsHTML($content, $type, $subtype, $partitionclass, $two_1);
                                    $two_1++;
                                    if ($two_1 > 2) {
                                        $two_1 = 1;
                                    }
                                }
                                if ($section1['partitionclass'] == 'ThreeColumns') {
                                    $content = $section1['val'];
                                    $type = $section1['gentype'];
                                    $subtype = $section1['subtype'];
                                    $partitionclass = $section1['partitionclass'];
                                    $response .= Self::ThreeColumnsHTML($content, $type, $subtype, $partitionclass, $three_1);
                                    $three_1++;
                                    if ($three_1 > 3) {
                                        $three_1 = 1;
                                    }
                                }
                                if ($section1['partitionclass'] == 'FourColumns') {
                                    $content = $section1['val'];
                                    $type = $section1['gentype'];
                                    $subtype = $section1['subtype'];
                                    $partitionclass = $section1['partitionclass'];
                                    $response .= Self::FourColumnsHTML($content, $type, $subtype, $partitionclass, $four_1);
                                    $four_1++;
                                    if ($four_1 > 4) {
                                        $four_1 = 1;
                                    }
                                }
                                if ($section1['partitionclass'] == 'OneThreeColumns') {
                                    $content = $section1['val'];
                                    $type = $section1['gentype'];
                                    $subtype = $section1['subtype'];
                                    $partitionclass = $section1['partitionclass'];
                                    $response .= Self::OneThreeColumnsHTML($content, $type, $subtype, $partitionclass, $two);
                                    $two++;
                                    if ($two > 2) {
                                        $two = 1;
                                    }
                                }
                                if ($section1['partitionclass'] == 'ThreeOneColumns') {
                                    $content = $section1['val'];
                                    $type = $section1['gentype'];
                                    $subtype = $section1['subtype'];
                                    $partitionclass = $section1['partitionclass'];
                                    $response .= Self::ThreeOneColumnsHTML($content, $type, $subtype, $partitionclass, $two);
                                    $two++;
                                    if ($two > 2) {
                                        $two = 1;
                                    }
                                }
                            } else if ($module == 'formarea') {
                                $formid = $section1['val']['id'];
                                $content = $section1['val']['content'];
                                $extclass = '';
                                $response .= Self::OnlyFormBuilderHTML($formid, $content, $extclass);
                            } else if ($module == 'image') {
                                $title = $section1['val']['title'];
                                $image = $section1['val']['image'];
                                $alignment = $section1['val']['alignment'];
                                $img = $section1['val']['src'];
                                $response .= Self::ImageHTML($title, $img, $image, $alignment);
                            } else if ($module == 'document') {
                                $document = $section1['val']['document'];
                                $img = $section1['val']['src'];
                                $response .= Self::DocumentHTML($document, $img);
                            } else if ($module == 'textarea') {
                                $content = $section1['val']['content'];
                                $response .= Self::OnlyContentHTML($content);
                            } else if ($module == 'twocontent') {
                                $leftcontent = $section1['val']['leftcontent'];
                                $rightcontent = $section1['val']['rightcontent'];
                                $response .= Self::TwoContentHTML($leftcontent, $rightcontent);
                            } else if ($module == 'only_video') {
                                $title = $section1['val']['title'];
                                $videoType = $section1['val']['videoType'];
                                $vidId = $section1['val']['vidId'];
                                $response .= Self::VideoHTML($title, $videoType, $vidId);
                            } else if ($module == 'video_content') {
                                $title = $section1['val']['title'];
                                $videoType = $section1['val']['videoType'];
                                $vidId = $section1['val']['vidId'];
                                $content = $section1['val']['content'];
                                $alignment = $section1['val']['alignment'];
                                $response .= Self::VideoContentHTML($title, $videoType, $vidId, $content, $alignment);
                            } else if ($module == 'spacer_template') {
                                $config = $section1['val']['config'];
                                $response .= Self::SpacerHTML($config);
                            } else if ($module == 'img_content') {
                                $title = $section1['val']['title'];
                                $content = $section1['val']['content'];
                                $alignment = $section1['val']['alignment'];
                                $image = $section1['val']['image'];
                                $src = $section1['val']['src'];
                                $response .= Self::ContentHTML($title, $content, $alignment, $src, $image);
                            } else if ($module == 'about_block') {
                                $title = $section1['val']['title'];
                                $content = $section1['val']['content'];
                                $alignment = $section1['val']['alignment'];
                                $image = $section1['val']['image'];
                                $src = isset($section1['val']['src']) ? $section1['val']['src'] : '';
                                
                                $btntitle = $section1['val']['buttontitle'];
                                $btnurl = $section1['val']['buttonlink'];
                                $tagline = $section1['val']['tagline'];
                            
                                $response .= Self::AboutBlockHTML($title, $content, $alignment, $src, $image, $btntitle, $btnurl, $tagline);
                            } else if ($module == 'organizations_template') {
                                $title = $section1['val']['title'];
                                $parentorg = $section1['val']['parentorg'];
                                $orgclass = $section1['val']['orgclass'];
                                $filter = $section1['val']['template'];
                                $response .= Self::organizationsHTML($title, $parentorg, $orgclass, $filter);
                            } else if ($module == 'alerts') {
                                $title = $section1['val']['title'];
                                $records = $section1['val']['records'];
                                $filter = $section1['val']['template'];
                                $extraclass = $section1['val']['extraclass'];
                                $response .= Self::alertsHTML($title, $records, $filter, $extraclass);
                            } else if ($module == 'alerts_template') {
                                $title = $section1['val']['title'];
                                $limit = $section1['val']['limit'];
                                $alerttype = $section1['val']['alerttype'];
                                $sdate = $section1['val']['sdate'];
                                $edate = $section1['val']['edate'];
                                $class = $section1['val']['class'];
                                $filter = $section1['val']['template'];
                                $response .= Self::AllalertsHTML($title, $limit, $alerttype, $sdate, $edate, $class, $filter);
                            } else if ($module == 'show') {
                                $title = $section1['val']['title'];
                                $records = $section1['val']['records'];
                                $filter = $section1['val']['template'];
                                if (isset($section1['val']['config']) && $section1['val']['config'] != '') {
                                    $config = $section1['val']['config'];
                                } else {
                                    $config = '';
                                }
                                if (isset($section1['val']['extraclass']) && $section1['val']['extraclass'] != '') {
                                    $class = $section1['val']['extraclass'];
                                } else {
                                    $class = '';
                                }
                                $layout = $section1['val']['layout'];
                                $recIds = array_column($records, 'id');
                                $fill = \Powerpanel\Show\Models\Show::getBuilderShows($recIds);
                                if ($fill->count() > 0) {
                                    $owlCaurosolLib = 'assets/libraries/owl.carousel/js/owl.carousel.min.js';

                                    if (!in_array($owlCaurosolLib, Self::$assets['js']['lib'])) {

                                        Self::$assets['js']['lib'][] = $owlCaurosolLib;
                                    }

                                    $owlCaurosolLib1 = 'assets/libraries/libraries-update/owl.carousel/js/owl.carousel-update.js';

                                    if (!in_array($owlCaurosolLib1, Self::$assets['js']['lib'])) {

                                        Self::$assets['js']['lib'][] = $owlCaurosolLib1;
                                    }

                                    $owlCaurosolLib2 = 'assets/js/index.js';

                                    if (!in_array($owlCaurosolLib2, Self::$assets['js']['lib'])) {

                                        Self::$assets['js']['lib'][] = $owlCaurosolLib2;
                                    }

                                    if ($module != 'show') {

                                        $moduleJS = 'assets/js/show.js';

                                        if (!in_array($moduleJS, Self::$assets['js']['modulejs'])) {

                                            Self::$assets['js']['modulejs'][] = $moduleJS;
                                        }
                                    }
                                }
                                $response .= Self::showHTML($title, $records, $filter, $class, $config, $layout);
                            } else if ($module == 'show_template') {
                                $title = $section1['val']['title'];
                                $class = $section1['val']['extclass'];
                                if (isset($section1['val']['config']) && $section1['val']['config'] != '') {
                                    $config = $section1['val']['config'];
                                } else {
                                    $config = '';
                                }
                                $layout = $section1['val']['layout'];
                                $filter = $section1['val']['template'];
                                $response .= Self::AllshowHTML($title, $class, $config, $layout, $filter);
                            } elseif ($module == 'gallery') {
                                $fancyBox = 'assets/libraries/fancybox/js/jquery.fancybox.min.js';
                                if (!in_array($fancyBox, Self::$assets['js']['lib'])) {
                                    Self::$assets['js']['lib'][] = $fancyBox;
                                }
                                $moduleJS = 'assets/js/packages/gallery/gallery.js';
                                if (!in_array($moduleJS, Self::$assets['js']['modulejs'])) {
                                    Self::$assets['js']['modulejs'][] = $moduleJS;
                                }
                                $title = $section1['val']['title'];
                                $config = $section1['val']['config'];
                                $layout = $section1['val']['layout'];
                                $records = $section1['val']['records'];
                                $filter = $section1['val']['template'];
                                $response .= Self::galleryHTML($title, $config, $layout, $records, $filter);
                            } elseif ($module == 'gallery_template') {
                                $fancyBox = 'assets/libraries/fancybox/js/jquery.fancybox.min.js';
                                if (!in_array($fancyBox, Self::$assets['js']['lib'])) {
                                    Self::$assets['js']['lib'][] = $fancyBox;
                                }
                                $moduleJS = 'assets/js/packages/gallery/gallery.js';
                                if (!in_array($moduleJS, Self::$assets['js']['modulejs'])) {
                                    Self::$assets['js']['modulejs'][] = $moduleJS;
                                }
                                $title = $section1['val']['title'];
                                $class = $section1['val']['extclass'];
                                if (isset($section1['val']['config']) && $section1['val']['config'] != '') {
                                    $config = $section1['val']['config'];
                                } else {
                                    $config = '';
                                }
                                $layout = $section1['val']['layout'];
                                $filter = $section1['val']['template'];
                                $response .= Self::AllgalleryHTML($title, $class, $config, $layout, $filter);
                            } else if ($module == 'product') {
                                $title = $section1['val']['title'];
                                $records = $section1['val']['records'];

                                if (isset($section1['val']['extclass']) && $section1['val']['extclass'] != '') {
                                    $class = $section1['val']['extclass'];
                                } else {
                                    $class = '';
                                }
                                $filter = $section1['val']['template'];
                                if (isset($section1['val']['config']) && $section1['val']['config'] != '') {
                                    $config = $section1['val']['config'];
                                } else {
                                    $config = '';
                                }
                                $layout = $section1['val']['layout'];
                                $recIds = array_column($records, 'id');
                                $fill = \Powerpanel\Products\Models\Products::getProductList($recIds);
                                if ($fill->count() > 0) {
                                    $owlCaurosolLib = 'assets/libraries/owl.carousel/js/owl.carousel.min.js';

                                    if (!in_array($owlCaurosolLib, Self::$assets['js']['lib'])) {

                                        Self::$assets['js']['lib'][] = $owlCaurosolLib;
                                    }

                                    $owlCaurosolLib1 = 'assets/libraries/libraries-update/owl.carousel/js/owl.carousel-update.js';

                                    if (!in_array($owlCaurosolLib1, Self::$assets['js']['lib'])) {

                                        Self::$assets['js']['lib'][] = $owlCaurosolLib1;
                                    }

                                    $owlCaurosolLib2 = 'assets/js/index.js';

                                    if (!in_array($owlCaurosolLib2, Self::$assets['js']['lib'])) {

                                        Self::$assets['js']['lib'][] = $owlCaurosolLib2;
                                    }

                                    if ($module != 'product') {

                                        $moduleJS = 'assets/js/product.js';

                                        if (!in_array($moduleJS, Self::$assets['js']['modulejs'])) {

                                            Self::$assets['js']['modulejs'][] = $moduleJS;
                                        }
                                    }
                                }
                                $response .= Self::productHTML($title, $records, $class, $filter, $config, $layout);
                            } else if ($module == 'product_template') {
                                $title = $section1['val']['title'];
                                $class = $section1['val']['extclass'];
                                if (isset($section1['val']['config']) && $section1['val']['config'] != '') {
                                    $config = $section1['val']['config'];
                                } else {
                                    $config = '';
                                }
                                $layout = $section1['val']['layout'];
                                $filter = $section1['val']['template'];
                                $response .= Self::AllproductHTML($title, $class, $config, $layout, $filter);
                            } else if ($module == 'testimonial') {
                                $title = $section1['val']['title'];
                                $records = $section1['val']['records'];
                                $class = isset($section1['val']['extraclass']) ? $section1['val']['extraclass'] : '';
                                $filter = $section1['val']['template'];
                                $layout = $section1['val']['layout'];
                                $response .= Self::testimonialHTML($title, $records, $filter,  $class, $layout);
                            } else if ($module == 'testimonial_template') {
                                $title = $section1['val']['title'];
                                $config = $section1['val']['config'];
                                $class = $section1['val']['extclass'];
                                $layout = $section1['val']['layout'];
                                $filter = $section1['val']['template'];
                                $response .= Self::AlltestimonialHTML($title, $config, $layout, $class, $filter);
                            } else if ($module == 'team') {
                                $title = $section1['val']['title'];
                                if (isset($section1['val']['desc']) && $section1['val']['desc'] != '') {
                                    $desc = $section1['val']['desc'];
                                } else {
                                    $desc = '';
                                }
                                if (isset($section1['val']['extraclass']) && $section1['val']['extraclass'] != '') {
                                    $class = $section1['val']['extraclass'];
                                } else {
                                    $class = '';
                                }
                                if (isset($section1['val']['config']) && $section1['val']['config'] != '') {
                                    $config = $section1['val']['config'];
                                } else {
                                    $config = '';
                                }
                                $records = $section1['val']['records'];
                                $filter = $section1['val']['template'];
                                $layout = $section1['val']['layout'];
                                $recIds = array_column($records, 'id');
                                $fill = \Powerpanel\Team\Models\Team::getTeamList($recIds);
                                if ($fill->count() > 0) {
                                    $owlCaurosolLib = 'assets/libraries/owl.carousel/js/owl.carousel.min.js';

                                    if (!in_array($owlCaurosolLib, Self::$assets['js']['lib'])) {

                                        Self::$assets['js']['lib'][] = $owlCaurosolLib;
                                    }

                                    $owlCaurosolLib1 = 'assets/libraries/libraries-update/owl.carousel/js/owl.carousel-update.js';

                                    if (!in_array($owlCaurosolLib1, Self::$assets['js']['lib'])) {

                                        Self::$assets['js']['lib'][] = $owlCaurosolLib1;
                                    }

                                    $owlCaurosolLib2 = 'assets/js/index.js';

                                    if (!in_array($owlCaurosolLib2, Self::$assets['js']['lib'])) {

                                        Self::$assets['js']['lib'][] = $owlCaurosolLib2;
                                    }

                                    if ($module != 'team') {

                                        $moduleJS = 'assets/js/team.js';

                                        if (!in_array($moduleJS, Self::$assets['js']['modulejs'])) {

                                            Self::$assets['js']['modulejs'][] = $moduleJS;
                                        }
                                    }
                                }
                                $response .= Self::teamHTML($title, $desc, $class, $config, $records, $filter, $layout);
                            } else if ($module == 'team_template') {
                                $title = $section1['val']['title'];
                                $config = $section1['val']['config'];
                                $class = $section1['val']['extclass'];
                                $layout = $section1['val']['layout'];
                                $filter = $section1['val']['template'];
                                $response .= Self::AllteamHTML($title, $config, $layout, $class, $filter);
                            } else if ($module == 'client') {
                                $title = $section1['val']['title'];
                                if (isset($section1['val']['desc']) && $section1['val']['desc'] != '') {
                                    $desc = $section1['val']['desc'];
                                } else {
                                    $desc = '';
                                }
                                if (isset($section1['val']['extraclass']) && $section1['val']['extraclass'] != '') {
                                    $class = $section1['val']['extraclass'];
                                } else {
                                    $class = '';
                                }
                                if (isset($section1['val']['config']) && $section1['val']['config'] != '') {
                                    $config = $section1['val']['config'];
                                } else {
                                    $config = '';
                                }
                                $records = $section1['val']['records'];
                                $filter = $section1['val']['template'];
                                $layout = $section1['val']['layout'];
                                $recIds = array_column($records, 'id');
                                $fill = \Powerpanel\Client\Models\Client::getClientList($recIds);
                                if ($fill->count() > 0) {
                                    $owlCaurosolLib = 'assets/libraries/owl.carousel/js/owl.carousel.min.js';

                                    if (!in_array($owlCaurosolLib, Self::$assets['js']['lib'])) {

                                        Self::$assets['js']['lib'][] = $owlCaurosolLib;
                                    }

                                    $owlCaurosolLib1 = 'assets/libraries/libraries-update/owl.carousel/js/owl.carousel-update.js';

                                    if (!in_array($owlCaurosolLib1, Self::$assets['js']['lib'])) {

                                        Self::$assets['js']['lib'][] = $owlCaurosolLib1;
                                    }

                                    $owlCaurosolLib2 = 'assets/js/index.js';

                                    if (!in_array($owlCaurosolLib2, Self::$assets['js']['lib'])) {

                                        Self::$assets['js']['lib'][] = $owlCaurosolLib2;
                                    }

                                    if ($module != 'client') {

                                        $moduleJS = 'assets/js/client.js';

                                        if (!in_array($moduleJS, Self::$assets['js']['modulejs'])) {

                                            Self::$assets['js']['modulejs'][] = $moduleJS;
                                        }
                                    }
                                }
                                $response .= Self::clientHTML($title, $desc, $class, $config, $records, $filter, $layout);
                            } else if ($module == 'client_template') {
                                $title = $section1['val']['title'];
                                $config = $section1['val']['config'];
                                $class = $section1['val']['extclass'];
                                $layout = $section1['val']['layout'];
                                $filter = $section1['val']['template'];
                                $response .= Self::AllclientHTML($title, $config, $layout, $class, $filter);
                            } else if ($module == 'project') {
                                $title = $section1['val']['title'];
                                if (isset($section1['val']['desc']) && $section1['val']['desc'] != '') {
                                    $desc = $section1['val']['desc'];
                                } else {
                                    $desc = '';
                                }
                                if (isset($section1['val']['extraclass']) && $section1['val']['extraclass'] != '') {
                                    $class = $section1['val']['extraclass'];
                                } else {
                                    $class = '';
                                }
                                if (isset($section1['val']['config']) && $section1['val']['config'] != '') {
                                    $config = $section1['val']['config'];
                                } else {
                                    $config = '';
                                }
                                $records = $section1['val']['records'];
                                $filter = $section1['val']['template'];
                                $layout = $section1['val']['layout'];
                                $recIds = array_column($records, 'id');
                                $fill = \Powerpanel\Projects\Models\Projects::getProjectsList($recIds);
                                if ($fill->count() > 0) {
                                    $owlCaurosolLib = 'assets/libraries/owl.carousel/js/owl.carousel.min.js';

                                    if (!in_array($owlCaurosolLib, Self::$assets['js']['lib'])) {

                                        Self::$assets['js']['lib'][] = $owlCaurosolLib;
                                    }

                                    $owlCaurosolLib1 = 'assets/libraries/libraries-update/owl.carousel/js/owl.carousel-update.js';

                                    if (!in_array($owlCaurosolLib1, Self::$assets['js']['lib'])) {

                                        Self::$assets['js']['lib'][] = $owlCaurosolLib1;
                                    }

                                    $owlCaurosolLib2 = 'assets/js/index.js';

                                    if (!in_array($owlCaurosolLib2, Self::$assets['js']['lib'])) {

                                        Self::$assets['js']['lib'][] = $owlCaurosolLib2;
                                    }

                                    if ($module != 'project') {

                                        $moduleJS = 'assets/js/project.js';

                                        if (!in_array($moduleJS, Self::$assets['js']['modulejs'])) {

                                            Self::$assets['js']['modulejs'][] = $moduleJS;
                                        }
                                    }
                                }
                                $response .= Self::projectHTML($title, $desc, $class, $config, $records, $filter, $layout);
                            } else if ($module == 'project_template') {
                                $title = $section1['val']['title'];
                                $config = $section1['val']['config'];
                                $class = $section1['val']['extclass'];
                                $layout = $section1['val']['layout'];
                                $filter = $section1['val']['template'];
                                $response .= Self::AllprojectHTML($title, $config, $layout, $class, $filter);
                            } else if ($module == 'department') {
                                $title = $section1['val']['title'];
                                $records = $section1['val']['records'];
                                $filter = $section1['val']['template'];
                                $extraclass = $section1['val']['extraclass'];
                                $response .= Self::departmentHTML($title, $records, $filter, $extraclass);
                            } else if ($module == 'department_template') {
                                $title = $section1['val']['title'];
                                $limit = $section1['val']['limit'];
                                $sdate = $section1['val']['sdate'];
                                $edate = $section1['val']['edate'];
                                $class = $section1['val']['class'];
                                $filter = $section1['val']['template'];
                                $response .= Self::AlldepartmentHTML($title, $limit, $sdate, $edate, $class, $filter);
                            } else if ($module == 'photoalbum') {
                                $title = $section1['val']['title'];
                                $config = $section1['val']['config'];
                                $desc = $section1['val']['desc'];
                                $layout = $section1['val']['layout'];
                                $records = $section1['val']['records'];
                                $filter = $section1['val']['template'];
                                $extraclass = $section1['val']['extraclass'];
                                $recIds = array_column($records, 'id');
                                $fields = Self::selectFields($config);
                                $fill = \Powerpanel\Gallery\Models\Gallery::getBuilderPhotoAlbum($fields, $recIds);
                                if ($fill->count() > 0) {
                                    $owlCaurosolLib = 'assets/libraries/owl.carousel/js/owl.carousel.min.js';

                                    if (!in_array($owlCaurosolLib, Self::$assets['js']['lib'])) {

                                        Self::$assets['js']['lib'][] = $owlCaurosolLib;
                                    }

                                    $owlCaurosolLib1 = 'assets/libraries/libraries-update/owl.carousel/js/owl.carousel-update.js';

                                    if (!in_array($owlCaurosolLib1, Self::$assets['js']['lib'])) {

                                        Self::$assets['js']['lib'][] = $owlCaurosolLib1;
                                    }

                                    $owlCaurosolLib2 = 'assets/js/index.js';

                                    if (!in_array($owlCaurosolLib2, Self::$assets['js']['lib'])) {

                                        Self::$assets['js']['lib'][] = $owlCaurosolLib2;
                                    }

                                    if ($module != 'photoalbum') {

                                        $moduleJS = 'assets/js/photoalbum.js';

                                        if (!in_array($moduleJS, Self::$assets['js']['modulejs'])) {

                                            Self::$assets['js']['modulejs'][] = $moduleJS;
                                        }
                                    }
                                }
                                $response .= Self::photoalbumHTML($title, $desc, $config, $layout, $records, $filter, $extraclass);
                            } else if ($module == 'photoalbum_template') {
                                $title = $section1['val']['title'];
                                $limit = $section1['val']['limit'];
                                $desc = $section1['val']['desc'];
                                $config = $section1['val']['config'];
                                $layout = $section1['val']['layout'];
                                $filter = $section1['val']['template'];
                                $class = $section1['val']['class'];
                                $sdate = $section1['val']['sdate'];
                                $edate = $section1['val']['edate'];
                                $response .= Self::AllphotoalbumHTML($title, $limit, $desc, $config, $layout, $filter, $class, $sdate, $edate);
                            } else if ($module == 'videoalbum') {
                                $title = $section1['val']['title'];
                                $config = $section1['val']['config'];
                                $desc = $section1['val']['desc'];
                                $layout = $section1['val']['layout'];
                                $records = $section1['val']['records'];
                                $filter = $section1['val']['template'];
                                $recIds = array_column($records, 'id');
                                $fields = Self::selectFields($config);
                                $fill = \Powerpanel\VideoGallery\Models\VideoGallery::getBuilderVideoGallery($fields, $recIds);
                                if ($fill->count() > 0) {
                                    $owlCaurosolLib = 'assets/libraries/owl.carousel/js/owl.carousel.min.js';

                                    if (!in_array($owlCaurosolLib, Self::$assets['js']['lib'])) {

                                        Self::$assets['js']['lib'][] = $owlCaurosolLib;
                                    }

                                    $owlCaurosolLib1 = 'assets/libraries/libraries-update/owl.carousel/js/owl.carousel-update.js';

                                    if (!in_array($owlCaurosolLib1, Self::$assets['js']['lib'])) {

                                        Self::$assets['js']['lib'][] = $owlCaurosolLib1;
                                    }

                                    $owlCaurosolLib2 = 'assets/js/index.js';

                                    if (!in_array($owlCaurosolLib2, Self::$assets['js']['lib'])) {

                                        Self::$assets['js']['lib'][] = $owlCaurosolLib2;
                                    }

                                    if ($module != 'videoalbum') {

                                        $moduleJS = 'assets/js/videoalbum.js';

                                        if (!in_array($moduleJS, Self::$assets['js']['modulejs'])) {

                                            Self::$assets['js']['modulejs'][] = $moduleJS;
                                        }
                                    }
                                }
                                $response .= Self::videoalbumHTML($title, $desc, $config, $layout, $records, $filter);
                            } else if ($module == 'videoalbum_template') {
                                $title = $section1['val']['title'];
                                $limit = $section1['val']['limit'];
                                $desc = $section1['val']['desc'];
                                $config = $section1['val']['config'];
                                $layout = $section1['val']['layout'];
                                $filter = $section1['val']['template'];
                                $class = $section1['val']['class'];
                                $sdate = $section1['val']['sdate'];
                                $edate = $section1['val']['edate'];
                                $response .= Self::AllvideoalbumHTML($title, $limit, $desc, $config, $layout, $filter, $class, $sdate, $edate);
                            } else if ($module == 'events') {
                                $title = $section1['val']['title'];
                                $config = $section1['val']['config'];
                                $desc = $section1['val']['desc'];
                                $layout = $section1['val']['layout'];
                                $records = $section1['val']['records'];
                                $filter = $section1['val']['template'];
                                $extraclass = $section1['val']['extraclass'];
                                $recIds = array_column($records, 'id');
                                $fields = Self::selectFields($config);
                                $fill = \Powerpanel\Events\Models\Events::getBuilderEvents($fields, $recIds);
                                if ($fill->count() > 0) {
                                    $owlCaurosolLib = 'assets/libraries/owl.carousel/js/owl.carousel.min.js';

                                    if (!in_array($owlCaurosolLib, Self::$assets['js']['lib'])) {

                                        Self::$assets['js']['lib'][] = $owlCaurosolLib;
                                    }

                                    $owlCaurosolLib1 = 'assets/libraries/libraries-update/owl.carousel/js/owl.carousel-update.js';

                                    if (!in_array($owlCaurosolLib1, Self::$assets['js']['lib'])) {

                                        Self::$assets['js']['lib'][] = $owlCaurosolLib1;
                                    }

                                    $owlCaurosolLib2 = 'assets/js/index.js';

                                    if (!in_array($owlCaurosolLib2, Self::$assets['js']['lib'])) {

                                        Self::$assets['js']['lib'][] = $owlCaurosolLib2;
                                    }

                                    if ($module != 'events') {

                                        $moduleJS = 'assets/js/events.js';

                                        if (!in_array($moduleJS, Self::$assets['js']['modulejs'])) {

                                            Self::$assets['js']['modulejs'][] = $moduleJS;
                                        }
                                    }
                                }
                                $response .= Self::eventHTML($title, $desc, $config, $layout, $records, $filter, $extraclass);
                            } else if ($module == 'events_template') {
                                $title = $section1['val']['title'];
                                $limit = $section1['val']['limit'];
                                $desc = $section1['val']['desc'];
                                $config = $section1['val']['config'];
                                $layout = $section1['val']['layout'];
                                $filter = $section1['val']['template'];
                                if (isset($section1['val']['class']) && $section1['val']['class'] != '') {
                                    $class = $section1['val']['class'];
                                } else {
                                    $class = '';
                                }
                                if (isset($section1['val']['sdate']) && $section1['val']['sdate'] != '') {
                                    $sdate = $section1['val']['sdate'];
                                } else {
                                    $sdate = '';
                                }
                                if (isset($section1['val']['edate']) && $section1['val']['edate'] != '') {
                                    $edate = $section1['val']['edate'];
                                } else {
                                    $edate = '';
                                }
                                if (isset($section1['val']['eventscat']) && $section1['val']['eventscat'] != '') {
                                    $eventscat = $section1['val']['eventscat'];
                                } else {
                                    $eventscat = '';
                                }
                                $response .= Self::AlleventsHTML($title, $limit, $desc, $config, $layout, $filter, $class, $sdate, $edate, $eventscat);
                            } else if ($module == 'blogs') {
                                $title = $section1['val']['title'];
                                $config = $section1['val']['config'];
                                $desc = $section1['val']['desc'];
                                $layout = $section1['val']['layout'];
                                $records = $section1['val']['records'];
                                $filter = $section1['val']['template'];
                                $extraclass = $section1['val']['extraclass'];
                                $recIds = array_column($records, 'id');
                                $fields = Self::selectFields($config);
                                $fill = \Powerpanel\Blogs\Models\Blogs::getBuilderBlog($fields, $recIds);
                                if ($fill->count() > 0) {
                                    $owlCaurosolLib = 'assets/libraries/owl.carousel/js/owl.carousel.min.js';

                                    if (!in_array($owlCaurosolLib, Self::$assets['js']['lib'])) {

                                        Self::$assets['js']['lib'][] = $owlCaurosolLib;
                                    }

                                    $owlCaurosolLib1 = 'assets/libraries/libraries-update/owl.carousel/js/owl.carousel-update.js';

                                    if (!in_array($owlCaurosolLib1, Self::$assets['js']['lib'])) {

                                        Self::$assets['js']['lib'][] = $owlCaurosolLib1;
                                    }

                                    $owlCaurosolLib2 = 'assets/js/index.js';

                                    if (!in_array($owlCaurosolLib2, Self::$assets['js']['lib'])) {

                                        Self::$assets['js']['lib'][] = $owlCaurosolLib2;
                                    }

                                    if ($module != 'blogs') {

                                        $moduleJS = 'assets/js/blogs.js';

                                        if (!in_array($moduleJS, Self::$assets['js']['modulejs'])) {

                                            Self::$assets['js']['modulejs'][] = $moduleJS;
                                        }
                                    }
                                }
                                $response .= Self::blogsHTML($title, $desc, $config, $layout, $records, $filter, $extraclass);
                            } else if ($module == 'blogs_template') {
                                $title = $section1['val']['title'];
                                $limit = $section1['val']['limit'];
                                $desc = $section1['val']['desc'];
                                $config = $section1['val']['config'];
                                $layout = $section1['val']['layout'];
                                $filter = $section1['val']['template'];
                                if (isset($section1['val']['class']) && $section1['val']['class'] != '') {
                                    $class = $section1['val']['class'];
                                } else {
                                    $class = '';
                                }
                                if (isset($section1['val']['sdate']) && $section1['val']['sdate'] != '') {
                                    $sdate = $section1['val']['sdate'];
                                } else {
                                    $sdate = '';
                                }
                                if (isset($section1['val']['edate']) && $section1['val']['edate'] != '') {
                                    $edate = $section1['val']['edate'];
                                } else {
                                    $edate = '';
                                }
                                if (isset($section1['val']['blogscat']) && $section1['val']['blogscat'] != '') {
                                    $blogscat = $section1['val']['blogscat'];
                                } else {
                                    $blogscat = '';
                                }
                                $response .= Self::AllblogsHTML($title, $limit, $desc, $config, $layout, $filter, $class, $sdate, $edate, $blogscat);
                            } else if ($module == 'service') {
                                $title = $section1['val']['title'];
                                $config = $section1['val']['config'];
                                if (isset($section1['val']['desc']) && $section1['val']['desc'] != '') {
                                    $desc = $section1['val']['desc'];
                                } else {
                                    $desc = '';
                                }
                                $layout = $section1['val']['layout'];
                                $records = $section1['val']['records'];
                                $filter = $section1['val']['template'];
                                if (isset($section1['val']['extraclass']) && $section1['val']['extraclass'] != '') {
                                    $extraclass = $section1['val']['extraclass'];
                                } else {
                                    $extraclass = '';
                                }
                                $recIds = array_column($records, 'id');
                                $fields = Self::selectFields($config);
                                $fill = \Powerpanel\Services\Models\Services::getServiceList($fields, $recIds, 5);
                                if ($fill->count() > 0) {
                                    $owlCaurosolLib = 'assets/libraries/owl.carousel/js/owl.carousel.min.js';

                                    if (!in_array($owlCaurosolLib, Self::$assets['js']['lib'])) {

                                        Self::$assets['js']['lib'][] = $owlCaurosolLib;
                                    }

                                    $owlCaurosolLib1 = 'assets/libraries/libraries-update/owl.carousel/js/owl.carousel-update.js';

                                    if (!in_array($owlCaurosolLib1, Self::$assets['js']['lib'])) {

                                        Self::$assets['js']['lib'][] = $owlCaurosolLib1;
                                    }

                                    $owlCaurosolLib2 = 'assets/js/index.js';

                                    if (!in_array($owlCaurosolLib2, Self::$assets['js']['lib'])) {

                                        Self::$assets['js']['lib'][] = $owlCaurosolLib2;
                                    }

                                    if ($module != 'service') {

                                        $moduleJS = 'assets/js/service.js';

                                        if (!in_array($moduleJS, Self::$assets['js']['modulejs'])) {

                                            Self::$assets['js']['modulejs'][] = $moduleJS;
                                        }
                                    }
                                }
                                $response .= Self::serviceHTML($title, $desc, $config, $layout, $records, $filter, $extraclass);
                            } else if ($module == 'service_template') {
                                $title = $section1['val']['title'];
                                $config = $section1['val']['config'];
                                $layout = $section1['val']['layout'];
                                $filter = $section1['val']['template'];
                                if (isset($section1['val']['extclass']) && $section1['val']['extclass'] != '') {
                                    $class = $section1['val']['extclass'];
                                } else {
                                    $class = '';
                                }
                                $response .= Self::AllservicesHTML($title, $config, $layout, $filter, $class);
                            } else if ($module == 'boat') {
                                $title = $section1['val']['title'];
                                $config = $section1['val']['config'];
                                if (isset($section1['val']['desc']) && $section1['val']['desc'] != '') {
                                    $desc = $section1['val']['desc'];
                                } else {
                                    $desc = '';
                                }
                                $layout = $section1['val']['layout'];
                                $records = $section1['val']['records'];
                                $filter = $section1['val']['template'];
                                if (isset($section1['val']['extraclass']) && $section1['val']['extraclass'] != '') {
                                    $extraclass = $section1['val']['extraclass'];
                                } else {
                                    $extraclass = '';
                                }
                                $recIds = array_column($records, 'id');
                                $fields = Self::selectFields($config);
                                $fill = \Powerpanel\Boat\Models\Boat::getBoatList($fields, $recIds, 5);
                                if ($fill->count() > 0) {
                                    $owlCaurosolLib = 'assets/libraries/owl.carousel/js/owl.carousel.min.js';
                            
                                    if (!in_array($owlCaurosolLib, Self::$assets['js']['lib'])) {
                            
                                        Self::$assets['js']['lib'][] = $owlCaurosolLib;
                                    }
                            
                                    $owlCaurosolLib1 = 'assets/libraries/libraries-update/owl.carousel/js/owl.carousel-update.js';
                            
                                    if (!in_array($owlCaurosolLib1, Self::$assets['js']['lib'])) {
                            
                                        Self::$assets['js']['lib'][] = $owlCaurosolLib1;
                                    }
                            
                                    $owlCaurosolLib2 = 'assets/js/index.js';
                            
                                    if (!in_array($owlCaurosolLib2, Self::$assets['js']['lib'])) {
                            
                                        Self::$assets['js']['lib'][] = $owlCaurosolLib2;
                                    }
                            
                                    if ($module != 'boat') {
                            
                                        $moduleJS = 'assets/js/boat.js';
                            
                                        if (!in_array($moduleJS, Self::$assets['js']['modulejs'])) {
                            
                                            Self::$assets['js']['modulejs'][] = $moduleJS;
                                        }
                                    }
                                }
                                $response .= Self::boatHTML($title, $desc, $config, $layout, $records, $filter, $extraclass);
                            } else if ($module == 'boat_template') {
                                $title = $section1['val']['title'];
                                $config = $section1['val']['config'];
                                $layout = $section1['val']['layout'];
                                $filter = $section1['val']['template'];
                                if (isset($section1['val']['extclass']) && $section1['val']['extclass'] != '') {
                                    $class = $section1['val']['extclass'];
                                } else {
                                    $class = '';
                                }
                                $response .= Self::AllboatHTML($title, $config, $layout, $filter, $class);
                            } else if ($module == 'work') {
                                $title = $section1['val']['title'];
                                $config = $section1['val']['config'];
                                if (isset($section1['val']['desc']) && $section1['val']['desc'] != '') {
                                    $desc = $section1['val']['desc'];
                                } else {
                                    $desc = '';
                                }
                                $layout = $section1['val']['layout'];
                                $records = $section1['val']['records'];
                                $filter = $section1['val']['template'];
                                if (isset($section1['val']['extraclass']) && $section1['val']['extraclass'] != '') {
                                    $extraclass = $section1['val']['extraclass'];
                                } else {
                                    $extraclass = '';
                                }
                                $recIds = array_column($records, 'id');
                                $fields = Self::selectFields($config);
                                $fill = \Powerpanel\Work\Models\Work::getWorkList($fields, $recIds, 5);
                                if ($fill->count() > 0) {
                                    $owlCaurosolLib = 'assets/libraries/owl.carousel/js/owl.carousel.min.js';

                                    if (!in_array($owlCaurosolLib, Self::$assets['js']['lib'])) {

                                        Self::$assets['js']['lib'][] = $owlCaurosolLib;
                                    }

                                    $owlCaurosolLib1 = 'assets/libraries/libraries-update/owl.carousel/js/owl.carousel-update.js';

                                    if (!in_array($owlCaurosolLib1, Self::$assets['js']['lib'])) {

                                        Self::$assets['js']['lib'][] = $owlCaurosolLib1;
                                    }

                                    $owlCaurosolLib2 = 'assets/js/index.js';

                                    if (!in_array($owlCaurosolLib2, Self::$assets['js']['lib'])) {

                                        Self::$assets['js']['lib'][] = $owlCaurosolLib2;
                                    }

                                    if ($module != 'work') {

                                        $moduleJS = 'assets/js/work.js';

                                        if (!in_array($moduleJS, Self::$assets['js']['modulejs'])) {

                                            Self::$assets['js']['modulejs'][] = $moduleJS;
                                        }
                                    }
                                }
                                $response .= Self::workHTML($title, $desc, $config, $layout, $records, $filter, $extraclass);
                            } else if ($module == 'work_template') {
                                $title = $section1['val']['title'];
                                $config = $section1['val']['config'];
                                $layout = $section1['val']['layout'];
                                $filter = $section1['val']['template'];
                                if (isset($section1['val']['extclass']) && $section1['val']['extclass'] != '') {
                                    $class = $section1['val']['extclass'];
                                } else {
                                    $class = '';
                                }
                                $response .= Self::AllworkHTML($title, $config, $layout, $filter, $class);
                            } else if ($module == 'news') {
                                $title = $section1['val']['title'];
                                $config = $section1['val']['config'];
                                $desc = $section1['val']['desc'];
                                $layout = $section1['val']['layout'];
                                $records = $section1['val']['records'];
                                $filter = $section1['val']['template'];
                                $extraclass = $section1['val']['extraclass'];
                                $recIds = array_column($records, 'id');
                                $fields = Self::selectFields($config);
                                $fill = \Powerpanel\News\Models\News::getBuilderNews($fields, $recIds);
                                if ($fill->count() > 0) {
                                    $owlCaurosolLib = 'assets/libraries/owl.carousel/js/owl.carousel.min.js';

                                    if (!in_array($owlCaurosolLib, Self::$assets['js']['lib'])) {

                                        Self::$assets['js']['lib'][] = $owlCaurosolLib;
                                    }

                                    $owlCaurosolLib1 = 'assets/libraries/libraries-update/owl.carousel/js/owl.carousel-update.js';

                                    if (!in_array($owlCaurosolLib1, Self::$assets['js']['lib'])) {

                                        Self::$assets['js']['lib'][] = $owlCaurosolLib1;
                                    }

                                    $owlCaurosolLib2 = 'assets/js/index.js';

                                    if (!in_array($owlCaurosolLib2, Self::$assets['js']['lib'])) {

                                        Self::$assets['js']['lib'][] = $owlCaurosolLib2;
                                    }

                                    if ($module != 'news') {

                                        $moduleJS = 'assets/js/news.js';

                                        if (!in_array($moduleJS, Self::$assets['js']['modulejs'])) {

                                            Self::$assets['js']['modulejs'][] = $moduleJS;
                                        }
                                    }
                                }
                                $response .= Self::newsHTML($title, $desc, $config, $layout, $records, $filter, $extraclass);
                            } else if ($module == 'news_template') {
                                $title = $section1['val']['title'];
                                $limit = $section1['val']['limit'];
                                $desc = $section1['val']['desc'];
                                $config = $section1['val']['config'];
                                $layout = $section1['val']['layout'];
                                $filter = $section1['val']['template'];
                                if (isset($section1['val']['class']) && $section1['val']['class'] != '') {
                                    $class = $section1['val']['class'];
                                } else {
                                    $class = '';
                                }
                                if (isset($section1['val']['sdate']) && $section1['val']['sdate'] != '') {
                                    $sdate = $section1['val']['sdate'];
                                } else {
                                    $sdate = '';
                                }
                                if (isset($section1['val']['edate']) && $section1['val']['edate'] != '') {
                                    $edate = $section1['val']['edate'];
                                } else {
                                    $edate = '';
                                }
                                if (isset($section1['val']['newscat']) && $section1['val']['newscat'] != '') {
                                    $newscat = $section1['val']['newscat'];
                                } else {
                                    $newscat = '';
                                }
                                $response .= Self::AllnewsHTML($title, $limit, $desc, $config, $layout, $filter, $class, $sdate, $edate, $newscat);
                            } else if ($module == 'links') {
                                $title = $section1['val']['title'];
                                $records = $section1['val']['records'];
                                $filter = $section1['val']['template'];
                                $extraclass = $section1['val']['extraclass'];
                                $response .= Self::linksHTML($title, $records, $filter, $extraclass);
                            } else if ($module == 'link_template') {
                                $title = $section1['val']['title'];
                                $limit = $section1['val']['limit'];
                                $sdate = $section1['val']['sdate'];
                                $edate = $section1['val']['edate'];
                                $class = $section1['val']['class'];
                                $linkcat = $section1['val']['linkcat'];
                                $filter = $section1['val']['template'];
                                $response .= Self::AllLinksHTML($title, $limit, $sdate, $edate, $class, $linkcat, $filter);
                            } else if ($module == 'faqs') {
                                $title = $section1['val']['title'];
                                $records = $section1['val']['records'];
                                $filter = $section1['val']['template'];
                                $extraclass = $section1['val']['extraclass'];
                                $response .= Self::faqsHTML($title, $records, $filter, $extraclass);
                            } else if ($module == 'faq_template') {
                                $title = $section1['val']['title'];
                                $limit = $section1['val']['limit'];
                                $sdate = $section1['val']['sdate'];
                                $edate = $section1['val']['edate'];
                                $class = $section1['val']['class'];
                                $faqcat = $section1['val']['faqcat'];
                                $filter = $section1['val']['template'];
                                $response .= Self::AllFaqsHTML($title, $limit, $sdate, $edate, $class, $faqcat, $filter);
                            } else if ($module == 'publication') {
                                $title = $section1['val']['title'];
                                $config = $section1['val']['config'];
                                $desc = $section1['val']['desc'];
                                $layout = $section1['val']['layout'];
                                $records = $section1['val']['records'];
                                $filter = $section1['val']['template'];
                                $extraclass = $section1['val']['extraclass'];
                                $recIds = array_column($records, 'id');
                                $fields = Self::selectFields($config);
                                $fill = \Powerpanel\Publications\Models\Publications::getBuilderPublication($fields, $recIds);
                                if ($fill->count() > 0) {
                                    $owlCaurosolLib = 'assets/libraries/owl.carousel/js/owl.carousel.min.js';

                                    if (!in_array($owlCaurosolLib, Self::$assets['js']['lib'])) {

                                        Self::$assets['js']['lib'][] = $owlCaurosolLib;
                                    }

                                    $owlCaurosolLib1 = 'assets/libraries/libraries-update/owl.carousel/js/owl.carousel-update.js';

                                    if (!in_array($owlCaurosolLib1, Self::$assets['js']['lib'])) {

                                        Self::$assets['js']['lib'][] = $owlCaurosolLib1;
                                    }

                                    $owlCaurosolLib2 = 'assets/js/index.js';

                                    if (!in_array($owlCaurosolLib2, Self::$assets['js']['lib'])) {

                                        Self::$assets['js']['lib'][] = $owlCaurosolLib2;
                                    }

                                    if ($module != 'publication') {

                                        $moduleJS = 'assets/js/publication.js';

                                        if (!in_array($moduleJS, Self::$assets['js']['modulejs'])) {

                                            Self::$assets['js']['modulejs'][] = $moduleJS;
                                        }
                                    }
                                }
                                $response .= Self::publicationHTML($title, $desc, $config, $layout, $records, $filter, $extraclass);
                            } else if ($module == 'publication_template') {
                                $title = $section1['val']['title'];
                                $limit = $section1['val']['limit'];
                                $desc = $section1['val']['desc'];
                                $config = $section1['val']['config'];
                                $sdate = $section1['val']['sdate'];
                                $edate = $section1['val']['edate'];
                                $class = $section1['val']['class'];
                                $publicationscat = $section1['val']['publicationscat'];
                                $layout = $section1['val']['layout'];
                                $filter = $section1['val']['template'];
                                $response .= Self::AllpublicationHTML($title, $limit, $desc, $config, $layout, $filter, $sdate, $edate, $class, $publicationscat);
                            } else if ($module == 'home-img_content') {
                                $title = $section1['val']['title'];
                                $btnurl = $section1['val']['btnurl'];
                                $image = $section1['val']['image'];
                                $content = $section1['val']['content'];
                                $alignment = $section1['val']['alignment'];
                                $src = $section1['val']['src'];
                                $response .= Self::WelComeHTML($title, $image, $content, $alignment, $src,$btnurl);
                            } else if ($module == 'map') {
                                $latitude = $section1['val']['latitude'];
                                $longitude = $section1['val']['longitude'];
                                $response .= Self::MapHTML($latitude, $longitude);
                            } else if ($module == 'conatct_info') {
                                $content = $section1['val']['content'];
                                $section_address = $section1['val']['section_address'];
                                $section_email = $section1['val']['section_email'];
                                $section_phone = $section1['val']['section_phone'];
                                $response .= Self::ConatctInfoHTML($content, $section_address, $section_email, $section_phone);
                            } else if ($module == 'button_info') {
                                $title = $section1['val']['title'];
                                $content = $section1['val']['content'];
                                $alignment = $section1['val']['alignment'];
                                $target = $section1['val']['target'];
                                $response .= Self::ButtonHTML($title, $content, $alignment, $target);
                            }
                            $j++;
                        }
                    }
                } else {
                    if (isset($section['val']['module']) && $section['val']['module'] != '') {
                        $module = $section['val']['module'];
                    } else {
                        $module = $section['type'];
                    }

                    if ($module == 'only_title') {
                        $content = $section['val']['content'];
                        $extclass = '';
                        $response .= Self::OnlyTitleHTML($content, $extclass);
                    } else if ($module == 'iframe') {
                        $content = html_entity_decode($section['val']['content']);
                        $extclass = isset($section['val']['extclass']) ? $section['val']['extclass'] : '';
                        $response .= Self::OnlyIframeHTML($content, $extclass);
                    } else if ($module == 'partitondata') {
                        if ($section['partitionclass'] == 'TwoColumns') {
                            $content = $section['val'];
                            $type = $section['gentype'];
                            $subtype = $section['subtype'];
                            $partitionclass = $section['partitionclass'];
                            $count_two_part = $two_part_row_one_count;
                            $two_part_row_one_count++;
                            $response .= Self::TwoColumnsHTML($content, $type, $subtype, $partitionclass, $two, $count_two_part);
                            $two++;
                            if ($two > 2) {
                                $two = 1;
                            }
                        }
                        if ($section['partitionclass'] == 'ThreeColumns') {
                            $content = $section['val'];
                            $type = $section['gentype'];
                            $subtype = $section['subtype'];
                            $partitionclass = $section['partitionclass'];
                            $response .= Self::ThreeColumnsHTML($content, $type, $subtype, $partitionclass, $three);
                            $three++;
                            if ($three > 3) {
                                $three = 1;
                            }
                        }
                        if ($section['partitionclass'] == 'OneThreeColumns') {
                            $content = $section['val'];
                            $type = $section['gentype'];
                            $subtype = $section['subtype'];
                            $partitionclass = $section['partitionclass'];
                            $response .= Self::OneThreeColumnsHTML($content, $type, $subtype, $partitionclass, $two);
                            $two++;
                            if ($two > 2) {
                                $two = 1;
                            }
                        }
                        if ($section['partitionclass'] == 'ThreeOneColumns') {
                            $content = $section['val'];
                            $type = $section['gentype'];
                            $subtype = $section['subtype'];
                            $partitionclass = $section['partitionclass'];
                            $response .= Self::ThreeOneColumnsHTML($content, $type, $subtype, $partitionclass, $two);
                            $two++;
                            if ($two > 2) {
                                $two = 1;
                            }
                        }
                        if ($section['partitionclass'] == 'FourColumns') {
                            $content = $section['val'];
                            $type = $section['gentype'];
                            $subtype = $section['subtype'];
                            $partitionclass = $section['partitionclass'];
                            $response .= Self::FourColumnsHTML($content, $type, $subtype, $partitionclass, $four);
                            $four++;
                            if ($four > 4) {
                                $four = 1;
                            }
                        }
                    } else if ($module == 'formarea') {
                        $formid = $section['val']['id'];
                        $content = $section['val']['content'];
                        $extclass = '';
                        $response .= Self::OnlyFormBuilderHTML($formid, $content, $extclass);
                    } else if ($module == 'image') {
                        $title = $section['val']['title'];
                        $image = $section['val']['image'];
                        $alignment = $section['val']['alignment'];
                        $img = $section['val']['src'];
                        $response .= Self::ImageHTML($title, $img, $image, $alignment);
                    } else if ($module == 'document') {
                        $document = $section['val']['document'];
//                        $img = $section['val']['src'];
                        $response .= Self::DocumentHTML($document);
                    } else if ($module == 'textarea') {
                        $content = $section['val']['content'];
                        $response .= Self::OnlyContentHTML($content);
                    } else if ($module == 'twocontent') {
                        $leftcontent = $section['val']['leftcontent'];
                        $rightcontent = $section['val']['rightcontent'];
                        $response .= Self::TwoContentHTML($leftcontent, $rightcontent);
                    } else if ($module == 'only_video') {
                        $title = $section['val']['title'];
                        $videoType = $section['val']['videoType'];
                        $vidId = $section['val']['vidId'];
                        $response .= Self::VideoHTML($title, $videoType, $vidId);
                    } else if ($module == 'video_content') {
                        $title = $section['val']['title'];
                        $videoType = $section['val']['videoType'];
                        $vidId = $section['val']['vidId'];
                        $content = $section['val']['content'];
                        $alignment = $section['val']['alignment'];
                        $response .= Self::VideoContentHTML($title, $videoType, $vidId, $content, $alignment);
                    } else if ($module == 'spacer_template') {
                        $config = $section['val']['config'];
                        $response .= Self::SpacerHTML($config);
                    } else if ($module == 'img_content') {
                        $title = $section['val']['title'];
                        $content = $section['val']['content'];
                        $alignment = $section['val']['alignment'];
                        $image = $section['val']['image'];
                        $src = $section['val']['src'];
                        $response .= Self::ContentHTML($title, $content, $alignment, $src, $image);
                    } else if ($module == 'about_block') {
                        $title = $section['val']['title'];
                        $content = $section['val']['content'];
                        $alignment = $section['val']['alignment'];
                        $image = $section['val']['image'];
                        $src = isset($section['val']['src']) ? $section['val']['src'] : '';
                        
                        $btntitle = $section['val']['buttontitle'];
                        $btnurl = $section['val']['buttonlink'];
                        $tagline = $section['val']['tagline'];

                        $response .= Self::AboutBlockHTML($title, $content, $alignment, $src, $image, $btntitle, $btnurl, $tagline);
                    } else if ($module == 'organizations_template') {
                        $title = $section['val']['title'];
                        $parentorg = $section['val']['parentorg'];
                        $orgclass = $section['val']['orgclass'];
                        $filter = $section['val']['template'];
                        $response .= Self::organizationsHTML($title, $parentorg, $orgclass, $filter);
                    } else if ($module == 'alerts') {
                        $title = $section['val']['title'];
                        $records = $section['val']['records'];
                        $filter = $section['val']['template'];
                        $extraclass = $section['val']['extraclass'];
                        $response .= Self::alertsHTML($title, $records, $filter, $extraclass);
                    } else if ($module == 'alerts_template') {
                        $title = $section['val']['title'];
                        $limit = $section['val']['limit'];
                        $alerttype = $section['val']['alerttype'];
                        $sdate = $section['val']['sdate'];
                        $edate = $section['val']['edate'];
                        $class = $section['val']['class'];
                        $filter = $section['val']['template'];
                        $response .= Self::AllalertsHTML($title, $limit, $alerttype, $sdate, $edate, $class, $filter);
                    } else if ($module == 'show') {
                        $title = $section['val']['title'];
                        $records = $section['val']['records'];
                        $filter = $section['val']['template'];
                        if (isset($section['val']['config']) && $section['val']['config'] != '') {
                            $config = $section['val']['config'];
                        } else {
                            $config = '';
                        }
                        if (isset($section1['val']['extraclass']) && $section1['val']['extraclass'] != '') {
                            $class = $section1['val']['extraclass'];
                        } else {
                            $class = '';
                        }
                        $layout = $section['val']['layout'];
                        $recIds = array_column($records, 'id');
                        $fill = \Powerpanel\Show\Models\Show::getBuilderShows($recIds);
                        if ($fill->count() > 0) {
                            $owlCaurosolLib = 'assets/libraries/owl.carousel/js/owl.carousel.min.js';

                            if (!in_array($owlCaurosolLib, Self::$assets['js']['lib'])) {

                                Self::$assets['js']['lib'][] = $owlCaurosolLib;
                            }

                            $owlCaurosolLib1 = 'assets/libraries/libraries-update/owl.carousel/js/owl.carousel-update.js';

                            if (!in_array($owlCaurosolLib1, Self::$assets['js']['lib'])) {

                                Self::$assets['js']['lib'][] = $owlCaurosolLib1;
                            }

                            $owlCaurosolLib2 = 'assets/js/index.js';

                            if (!in_array($owlCaurosolLib2, Self::$assets['js']['lib'])) {

                                Self::$assets['js']['lib'][] = $owlCaurosolLib2;
                            }

                            if ($module != 'show') {

                                $moduleJS = 'assets/js/show.js';

                                if (!in_array($moduleJS, Self::$assets['js']['modulejs'])) {

                                    Self::$assets['js']['modulejs'][] = $moduleJS;
                                }
                            }
                        }
                        $response .= Self::showHTML($title, $records, $filter,$class, $config, $layout);
                    } else if ($module == 'show_template') {
                        $title = $section['val']['title'];
                        $class = $section['val']['extclass'];
                        if (isset($section['val']['config']) && $section['val']['config'] != '') {
                            $config = $section['val']['config'];
                        } else {
                            $config = '';
                        }
                        $layout = $section['val']['layout'];
                        $filter = $section['val']['template'];
                        $response .= Self::AllshowHTML($title, $class, $config, $layout, $filter);
                    } else if ($module == 'product') {

                        $title = $section['val']['title'];
                        $records = $section['val']['records'];

                        if (isset($section['val']['extclass']) && $section['val']['extclass'] != '') {
                            $class = $section['val']['extclass'];
                        } else {
                            $class = '';
                        }
                        $filter = $section['val']['template'];
                        if (isset($section['val']['config']) && $section['val']['config'] != '') {
                            $config = $section['val']['config'];
                        } else {
                            $config = '';
                        }
                        $layout = $section['val']['layout'];
                        $recIds = array_column($records, 'id');
                        $fill = \Powerpanel\Products\Models\Products::getProductList($recIds);
                        if ($fill->count() > 0) {
                            $owlCaurosolLib = 'assets/libraries/owl.carousel/js/owl.carousel.min.js';

                            if (!in_array($owlCaurosolLib, Self::$assets['js']['lib'])) {

                                Self::$assets['js']['lib'][] = $owlCaurosolLib;
                            }

                            $owlCaurosolLib1 = 'assets/libraries/libraries-update/owl.carousel/js/owl.carousel-update.js';

                            if (!in_array($owlCaurosolLib1, Self::$assets['js']['lib'])) {

                                Self::$assets['js']['lib'][] = $owlCaurosolLib1;
                            }

                            $owlCaurosolLib2 = 'assets/js/index.js';

                            if (!in_array($owlCaurosolLib2, Self::$assets['js']['lib'])) {

                                Self::$assets['js']['lib'][] = $owlCaurosolLib2;
                            }

                            if ($module != 'product') {

                                $moduleJS = 'assets/js/product.js';

                                if (!in_array($moduleJS, Self::$assets['js']['modulejs'])) {

                                    Self::$assets['js']['modulejs'][] = $moduleJS;
                                }
                            }
                        }
                        $response .= Self::productHTML($title, $records, $class, $filter, $config, $layout);
                    } else if ($module == 'product_template') {
                        $title = $section['val']['title'];
                        $class = $section['val']['extclass'];
                        if (isset($section['val']['config']) && $section['val']['config'] != '') {
                            $config = $section['val']['config'];
                        } else {
                            $config = '';
                        }
                        $layout = $section['val']['layout'];
                        $filter = $section['val']['template'];
                        $response .= Self::AllproductHTML($title, $class, $config, $layout, $filter);
                    } else if ($module == 'testimonial') {
                        $title = $section['val']['title'];
                        $records = $section['val']['records'];
                        $class = isset($section['val']['extraclass']) ? $section['val']['extraclass'] : '';
                        $filter = $section['val']['template'];
                        $layout = $section['val']['layout'];
                        $response .= Self::testimonialHTML($title, $records, $filter, $class, $layout);
                    } else if ($module == 'testimonial_template') {
                        $title = $section['val']['title'];
                        $config = $section['val']['config'];
                        $class = $section['val']['extclass'];
                        $layout = $section['val']['layout'];
                        $filter = $section['val']['template'];
                        $response .= Self::AlltestimonialHTML($title, $config, $layout, $class, $filter);
                    } else if ($module == 'team') {
                        $title = $section['val']['title'];
                        if (isset($section['val']['desc']) && $section['val']['desc'] != '') {
                            $desc = $section['val']['desc'];
                        } else {
                            $desc = '';
                        }
                        if (isset($section['val']['extraclass']) && $section['val']['extraclass'] != '') {
                            $class = $section['val']['extraclass'];
                        } else {
                            $class = '';
                        }
                        if (isset($section['val']['config']) && $section['val']['config'] != '') {
                            $config = $section['val']['config'];
                        } else {
                            $config = '';
                        }
                        $records = $section['val']['records'];
                        $filter = $section['val']['template'];
                        $layout = $section['val']['layout'];
                        $recIds = array_column($records, 'id');
                        $fill = \Powerpanel\Team\Models\Team::getTeamList($recIds);
                        if ($fill->count() > 0) {
                            $owlCaurosolLib = 'assets/libraries/owl.carousel/js/owl.carousel.min.js';

                            if (!in_array($owlCaurosolLib, Self::$assets['js']['lib'])) {

                                Self::$assets['js']['lib'][] = $owlCaurosolLib;
                            }

                            $owlCaurosolLib1 = 'assets/libraries/libraries-update/owl.carousel/js/owl.carousel-update.js';

                            if (!in_array($owlCaurosolLib1, Self::$assets['js']['lib'])) {

                                Self::$assets['js']['lib'][] = $owlCaurosolLib1;
                            }

                            $owlCaurosolLib2 = 'assets/js/index.js';

                            if (!in_array($owlCaurosolLib2, Self::$assets['js']['lib'])) {

                                Self::$assets['js']['lib'][] = $owlCaurosolLib2;
                            }

                            if ($module != 'team') {

                                $moduleJS = 'assets/js/team.js';

                                if (!in_array($moduleJS, Self::$assets['js']['modulejs'])) {

                                    Self::$assets['js']['modulejs'][] = $moduleJS;
                                }
                            }
                        }
                        $response .= Self::teamHTML($title, $desc, $class, $config, $records, $filter, $layout);
                    } else if ($module == 'team_template') {
                        $title = $section['val']['title'];
                        $config = $section['val']['config'];
                        $class = $section['val']['extclass'];
                        $layout = $section['val']['layout'];
                        $filter = $section['val']['template'];
                        $response .= Self::AllteamHTML($title, $config, $layout, $class, $filter);
                    } else if ($module == 'client') {
                        $title = $section['val']['title'];
                        if (isset($section['val']['desc']) && $section['val']['desc'] != '') {
                            $desc = $section['val']['desc'];
                        } else {
                            $desc = '';
                        }
                        if (isset($section['val']['extraclass']) && $section['val']['extraclass'] != '') {
                            $class = $section['val']['extraclass'];
                        } else {
                            $class = '';
                        }
                        if (isset($section['val']['config']) && $section['val']['config'] != '') {
                            $config = $section['val']['config'];
                        } else {
                            $config = '';
                        }
                        $records = $section['val']['records'];
                        $filter = $section['val']['template'];
                        $layout = $section['val']['layout'];
                        $recIds = array_column($records, 'id');
                        $fill = \Powerpanel\Client\Models\Client::getClientList($recIds);
                        if ($fill->count() > 0) {
                            $owlCaurosolLib = 'assets/libraries/owl.carousel/js/owl.carousel.min.js';

                            if (!in_array($owlCaurosolLib, Self::$assets['js']['lib'])) {

                                Self::$assets['js']['lib'][] = $owlCaurosolLib;
                            }

                            $owlCaurosolLib1 = 'assets/libraries/libraries-update/owl.carousel/js/owl.carousel-update.js';

                            if (!in_array($owlCaurosolLib1, Self::$assets['js']['lib'])) {

                                Self::$assets['js']['lib'][] = $owlCaurosolLib1;
                            }

                            $owlCaurosolLib2 = 'assets/js/index.js';

                            if (!in_array($owlCaurosolLib2, Self::$assets['js']['lib'])) {

                                Self::$assets['js']['lib'][] = $owlCaurosolLib2;
                            }

                            if ($module != 'client') {

                                $moduleJS = 'assets/js/client.js';

                                if (!in_array($moduleJS, Self::$assets['js']['modulejs'])) {

                                    Self::$assets['js']['modulejs'][] = $moduleJS;
                                }
                            }
                        }
                        $response .= Self::clientHTML($title, $desc, $class, $config, $records, $filter, $layout);
                    } else if ($module == 'client_template') {
                        $title = $section['val']['title'];
                        $config = $section['val']['config'];
                        $class = $section['val']['extclass'];
                        $layout = $section['val']['layout'];
                        $filter = $section['val']['template'];
                        $response .= Self::AllclientHTML($title, $config, $layout, $class, $filter);
                    } else if ($module == 'project') {
                        $title = $section['val']['title'];
                        if (isset($section['val']['desc']) && $section['val']['desc'] != '') {
                            $desc = $section['val']['desc'];
                        } else {
                            $desc = '';
                        }
                        if (isset($section['val']['extraclass']) && $section['val']['extraclass'] != '') {
                            $class = $section['val']['extraclass'];
                        } else {
                            $class = '';
                        }
                        if (isset($section['val']['config']) && $section['val']['config'] != '') {
                            $config = $section['val']['config'];
                        } else {
                            $config = '';
                        }
                        $records = $section['val']['records'];
                        $filter = $section['val']['template'];
                        $layout = $section['val']['layout'];
                        $recIds = array_column($records, 'id');
                        $fill = \Powerpanel\Projects\Models\Projects::getProjectsList($recIds);
                        if ($fill->count() > 0) {
                            $owlCaurosolLib = 'assets/libraries/owl.carousel/js/owl.carousel.min.js';

                            if (!in_array($owlCaurosolLib, Self::$assets['js']['lib'])) {

                                Self::$assets['js']['lib'][] = $owlCaurosolLib;
                            }

                            $owlCaurosolLib1 = 'assets/libraries/libraries-update/owl.carousel/js/owl.carousel-update.js';

                            if (!in_array($owlCaurosolLib1, Self::$assets['js']['lib'])) {

                                Self::$assets['js']['lib'][] = $owlCaurosolLib1;
                            }

                            $owlCaurosolLib2 = 'assets/js/index.js';

                            if (!in_array($owlCaurosolLib2, Self::$assets['js']['lib'])) {

                                Self::$assets['js']['lib'][] = $owlCaurosolLib2;
                            }

                            if ($module != 'project') {

                                $moduleJS = 'assets/js/project.js';

                                if (!in_array($moduleJS, Self::$assets['js']['modulejs'])) {

                                    Self::$assets['js']['modulejs'][] = $moduleJS;
                                }
                            }
                        }
                        $response .= Self::projectHTML($title, $desc, $class, $config, $records, $filter, $layout);
                    } else if ($module == 'project_template') {
                        $title = $section['val']['title'];
                        $config = $section['val']['config'];
                        $class = $section['val']['extclass'];
                        $layout = $section['val']['layout'];
                        $filter = $section['val']['template'];
                        $response .= Self::AllprojectHTML($title, $config, $layout, $class, $filter);
                    } else if ($module == 'department') {
                        $title = $section['val']['title'];
                        $records = $section['val']['records'];
                        $filter = $section['val']['template'];
                        $extraclass = $section['val']['extraclass'];
                        $response .= Self::departmentHTML($title, $records, $filter, $extraclass);
                    } else if ($module == 'department_template') {
                        $title = $section['val']['title'];
                        $limit = $section['val']['limit'];
                        $sdate = $section['val']['sdate'];
                        $edate = $section['val']['edate'];
                        $class = $section['val']['class'];
                        $filter = $section['val']['template'];
                        $response .= Self::AlldepartmentHTML($title, $limit, $sdate, $edate, $class, $filter);
                    } else if ($module == 'photoalbum') {
                        $title = $section['val']['title'];
                        $config = $section['val']['config'];
                        $desc = $section['val']['desc'];
                        $layout = $section['val']['layout'];
                        $records = $section['val']['records'];
                        $filter = $section['val']['template'];
                        $extraclass = $section['val']['extraclass'];
                        $recIds = array_column($records, 'id');
                        $fields = Self::selectFields($config);
                        $fill = \Powerpanel\Gallery\Models\Gallery::getBuilderPhotoAlbum($fields, $recIds);
                        if ($fill->count() > 0) {
                            $owlCaurosolLib = 'assets/libraries/owl.carousel/js/owl.carousel.min.js';

                            if (!in_array($owlCaurosolLib, Self::$assets['js']['lib'])) {

                                Self::$assets['js']['lib'][] = $owlCaurosolLib;
                            }

                            $owlCaurosolLib1 = 'assets/libraries/libraries-update/owl.carousel/js/owl.carousel-update.js';

                            if (!in_array($owlCaurosolLib1, Self::$assets['js']['lib'])) {

                                Self::$assets['js']['lib'][] = $owlCaurosolLib1;
                            }

                            $owlCaurosolLib2 = 'assets/js/index.js';

                            if (!in_array($owlCaurosolLib2, Self::$assets['js']['lib'])) {

                                Self::$assets['js']['lib'][] = $owlCaurosolLib2;
                            }

                            if ($module != 'photoalbum') {

                                $moduleJS = 'assets/js/photoalbum.js';

                                if (!in_array($moduleJS, Self::$assets['js']['modulejs'])) {

                                    Self::$assets['js']['modulejs'][] = $moduleJS;
                                }
                            }
                        }
                        $response .= Self::photoalbumHTML($title, $desc, $config, $layout, $records, $filter, $extraclass);
                    } else if ($module == 'photoalbum_template') {
                        $title = $section['val']['title'];
                        $limit = $section['val']['limit'];
                        $desc = $section['val']['desc'];
                        $config = $section['val']['config'];
                        $layout = $section['val']['layout'];
                        $filter = $section['val']['template'];
                        $class = $section['val']['class'];
                        $sdate = $section['val']['sdate'];
                        $edate = $section['val']['edate'];
                        $response .= Self::AllphotoalbumHTML($title, $limit, $desc, $config, $layout, $filter, $class, $sdate, $edate);
                    } else if ($module == 'videoalbum') {
                        $title = $section['val']['title'];
                        $config = $section['val']['config'];
                        $desc = $section['val']['desc'];
                        $layout = $section['val']['layout'];
                        $records = $section['val']['records'];
                        $filter = $section['val']['template'];
                        $recIds = array_column($records, 'id');
                        $fields = Self::selectFields($config);
                        $fill = \Powerpanel\VideoGallery\Models\VideoGallery::getBuilderVideoGallery($fields, $recIds);
                        if ($fill->count() > 0) {
                            $owlCaurosolLib = 'assets/libraries/owl.carousel/js/owl.carousel.min.js';

                            if (!in_array($owlCaurosolLib, Self::$assets['js']['lib'])) {

                                Self::$assets['js']['lib'][] = $owlCaurosolLib;
                            }

                            $owlCaurosolLib1 = 'assets/libraries/libraries-update/owl.carousel/js/owl.carousel-update.js';

                            if (!in_array($owlCaurosolLib1, Self::$assets['js']['lib'])) {

                                Self::$assets['js']['lib'][] = $owlCaurosolLib1;
                            }

                            $owlCaurosolLib2 = 'assets/js/index.js';

                            if (!in_array($owlCaurosolLib2, Self::$assets['js']['lib'])) {

                                Self::$assets['js']['lib'][] = $owlCaurosolLib2;
                            }

                            if ($module != 'videoalbum') {

                                $moduleJS = 'assets/js/videoalbum.js';

                                if (!in_array($moduleJS, Self::$assets['js']['modulejs'])) {

                                    Self::$assets['js']['modulejs'][] = $moduleJS;
                                }
                            }
                        }
                        $response .= Self::videoalbumHTML($title, $desc, $config, $layout, $records, $filter);
                    } else if ($module == 'videoalbum_template') {
                        $title = $section['val']['title'];
                        $limit = $section['val']['limit'];
                        $desc = $section['val']['desc'];
                        $config = $section['val']['config'];
                        $layout = $section['val']['layout'];
                        $filter = $section['val']['template'];
                        $class = $section['val']['class'];
                        $sdate = $section['val']['sdate'];
                        $edate = $section['val']['edate'];
                        $response .= Self::AllvideoalbumHTML($title, $limit, $desc, $config, $layout, $filter, $class, $sdate, $edate);
                    } else if ($module == 'events') {
                        $title = $section['val']['title'];
                        $config = $section['val']['config'];
                        $desc = $section['val']['desc'];
                        $layout = $section['val']['layout'];
                        $records = $section['val']['records'];
                        $filter = $section['val']['template'];
                        $extraclass = $section['val']['extraclass'];
                        $recIds = array_column($records, 'id');
                        $fields = Self::selectFields($config);
                        $fill = \Powerpanel\Events\Models\Events::getBuilderEvents($fields, $recIds);
                        if ($fill->count() > 0) {
                            $owlCaurosolLib = 'assets/libraries/owl.carousel/js/owl.carousel.min.js';

                            if (!in_array($owlCaurosolLib, Self::$assets['js']['lib'])) {

                                Self::$assets['js']['lib'][] = $owlCaurosolLib;
                            }

                            $owlCaurosolLib1 = 'assets/libraries/libraries-update/owl.carousel/js/owl.carousel-update.js';

                            if (!in_array($owlCaurosolLib1, Self::$assets['js']['lib'])) {

                                Self::$assets['js']['lib'][] = $owlCaurosolLib1;
                            }

                            $owlCaurosolLib2 = 'assets/js/index.js';

                            if (!in_array($owlCaurosolLib2, Self::$assets['js']['lib'])) {

                                Self::$assets['js']['lib'][] = $owlCaurosolLib2;
                            }

                            if ($module != 'events') {

                                $moduleJS = 'assets/js/events.js';

                                if (!in_array($moduleJS, Self::$assets['js']['modulejs'])) {

                                    Self::$assets['js']['modulejs'][] = $moduleJS;
                                }
                            }
                        }
                        $response .= Self::eventHTML($title, $desc, $config, $layout, $records, $filter, $extraclass);
                    } else if ($module == 'events_template') {
                        $title = $section['val']['title'];
                        $limit = $section['val']['limit'];
                        $desc = $section['val']['desc'];
                        $config = $section['val']['config'];
                        $layout = $section['val']['layout'];
                        $filter = $section['val']['template'];
                        if (isset($section['val']['class']) && $section['val']['class'] != '') {
                            $class = $section['val']['class'];
                        } else {
                            $class = '';
                        }
                        if (isset($section['val']['sdate']) && $section['val']['sdate'] != '') {
                            $sdate = $section['val']['sdate'];
                        } else {
                            $sdate = '';
                        }
                        if (isset($section['val']['edate']) && $section['val']['edate'] != '') {
                            $edate = $section['val']['edate'];
                        } else {
                            $edate = '';
                        }
                        if (isset($section['val']['eventscat']) && $section['val']['eventscat'] != '') {
                            $eventscat = $section['val']['eventscat'];
                        } else {
                            $eventscat = '';
                        }
                        $response .= Self::AlleventsHTML($title, $limit, $desc, $config, $layout, $filter, $class, $sdate, $edate, $eventscat);
                    } else if ($module == 'blogs') {
                        $title = $section['val']['title'];
                        $config = $section['val']['config'];
                        $desc = $section['val']['desc'];
                        $layout = $section['val']['layout'];
                        $records = $section['val']['records'];
                        $filter = $section['val']['template'];
                        $extraclass = $section['val']['extraclass'];
                        $recIds = array_column($records, 'id');
                        $fields = Self::selectFields($config);
                        $fill = \Powerpanel\Blogs\Models\Blogs::getBuilderBlog($fields, $recIds);
                        if ($fill->count() > 0) {
                            $owlCaurosolLib = 'assets/libraries/owl.carousel/js/owl.carousel.min.js';

                            if (!in_array($owlCaurosolLib, Self::$assets['js']['lib'])) {

                                Self::$assets['js']['lib'][] = $owlCaurosolLib;
                            }

                            $owlCaurosolLib1 = 'assets/libraries/libraries-update/owl.carousel/js/owl.carousel-update.js';

                            if (!in_array($owlCaurosolLib1, Self::$assets['js']['lib'])) {

                                Self::$assets['js']['lib'][] = $owlCaurosolLib1;
                            }

                            $owlCaurosolLib2 = 'assets/js/index.js';

                            if (!in_array($owlCaurosolLib2, Self::$assets['js']['lib'])) {

                                Self::$assets['js']['lib'][] = $owlCaurosolLib2;
                            }

                            if ($module != 'blogs') {

                                $moduleJS = 'assets/js/blogs.js';

                                if (!in_array($moduleJS, Self::$assets['js']['modulejs'])) {

                                    Self::$assets['js']['modulejs'][] = $moduleJS;
                                }
                            }
                        }
                        $response .= Self::blogsHTML($title, $desc, $config, $layout, $records, $filter, $extraclass);
                    } else if ($module == 'blogs_template') {
                        $title = $section['val']['title'];
                        $limit = $section['val']['limit'];
                        $desc = $section['val']['desc'];
                        $config = $section['val']['config'];
                        $layout = $section['val']['layout'];
                        $filter = $section['val']['template'];
                        if (isset($section['val']['class']) && $section['val']['class'] != '') {
                            $class = $section['val']['class'];
                        } else {
                            $class = '';
                        }
                        if (isset($section['val']['sdate']) && $section['val']['sdate'] != '') {
                            $sdate = $section['val']['sdate'];
                        } else {
                            $sdate = '';
                        }
                        if (isset($section['val']['edate']) && $section['val']['edate'] != '') {
                            $edate = $section['val']['edate'];
                        } else {
                            $edate = '';
                        }
                        if (isset($section['val']['blogscat']) && $section['val']['blogscat'] != '') {
                            $blogscat = $section['val']['blogscat'];
                        } else {
                            $blogscat = '';
                        }
                        $response .= Self::AllblogsHTML($title, $limit, $desc, $config, $layout, $filter, $class, $sdate, $edate, $blogscat);
                    } else if ($module == 'service') {
                        $title = $section['val']['title'];
                        $config = $section['val']['config'];
                        if (isset($section['val']['desc']) && $section['val']['desc'] != '') {
                            $desc = $section['val']['desc'];
                        } else {
                            $desc = '';
                        }
                        $layout = $section['val']['layout'];
                        $records = $section['val']['records'];
                        $filter = $section['val']['template'];
                        if (isset($section['val']['extraclass']) && $section['val']['extraclass'] != '') {
                            $extraclass = $section['val']['extraclass'];
                        } else {
                            $extraclass = '';
                        }
                        $recIds = array_column($records, 'id');
                        $fields = Self::selectFields($config);
                        $fill = \Powerpanel\Services\Models\Services::getServiceList($fields, $recIds, 5);
                        if ($fill->count() > 0) {
                            $owlCaurosolLib = 'assets/libraries/owl.carousel/js/owl.carousel.min.js';

                            if (!in_array($owlCaurosolLib, Self::$assets['js']['lib'])) {

                                Self::$assets['js']['lib'][] = $owlCaurosolLib;
                            }

                            $owlCaurosolLib1 = 'assets/libraries/libraries-update/owl.carousel/js/owl.carousel-update.js';

                            if (!in_array($owlCaurosolLib1, Self::$assets['js']['lib'])) {

                                Self::$assets['js']['lib'][] = $owlCaurosolLib1;
                            }

                            $owlCaurosolLib2 = 'assets/js/index.js';

                            if (!in_array($owlCaurosolLib2, Self::$assets['js']['lib'])) {

                                Self::$assets['js']['lib'][] = $owlCaurosolLib2;
                            }

                            if ($module != 'service') {

                                $moduleJS = 'assets/js/service.js';

                                if (!in_array($moduleJS, Self::$assets['js']['modulejs'])) {

                                    Self::$assets['js']['modulejs'][] = $moduleJS;
                                }
                            }
                        }
                        $response .= Self::serviceHTML($title, $desc, $config, $layout, $records, $filter, $extraclass);
                    } else if ($module == 'service_template') {
                        $title = $section['val']['title'];
                        $config = $section['val']['config'];
                        $layout = $section['val']['layout'];
                        $filter = $section['val']['template'];
                        if (isset($section['val']['extclass']) && $section['val']['extclass'] != '') {
                            $class = $section['val']['extclass'];
                        } else {
                            $class = '';
                        }
                        $response .= Self::AllservicesHTML($title, $config, $layout, $filter, $class);
                    } else if ($module == 'boat') {
                        $title = $section['val']['title'];
                        $config = $section['val']['config'];
                        if (isset($section['val']['desc']) && $section['val']['desc'] != '') {
                            $desc = $section['val']['desc'];
                        } else {
                            $desc = '';
                        }
                        $layout = $section['val']['layout'];
                        $records = $section['val']['records'];
                        $filter = $section['val']['template'];
                        if (isset($section['val']['extraclass']) && $section['val']['extraclass'] != '') {
                            $extraclass = $section['val']['extraclass'];
                        } else {
                            $extraclass = '';
                        }
                        $recIds = array_column($records, 'id');
                        $fields = Self::selectFields($config);
                        $fill = \Powerpanel\Boat\Models\Boat::getBoatList($fields, $recIds, 5);
                        if ($fill->count() > 0) {
                            $owlCaurosolLib = 'assets/libraries/owl.carousel/js/owl.carousel.min.js';

                            if (!in_array($owlCaurosolLib, Self::$assets['js']['lib'])) {

                                Self::$assets['js']['lib'][] = $owlCaurosolLib;
                            }

                            $owlCaurosolLib1 = 'assets/libraries/libraries-update/owl.carousel/js/owl.carousel-update.js';

                            if (!in_array($owlCaurosolLib1, Self::$assets['js']['lib'])) {

                                Self::$assets['js']['lib'][] = $owlCaurosolLib1;
                            }

                            $owlCaurosolLib2 = 'assets/js/index.js';

                            if (!in_array($owlCaurosolLib2, Self::$assets['js']['lib'])) {

                                Self::$assets['js']['lib'][] = $owlCaurosolLib2;
                            }

                            if ($module != 'boat') {

                                $moduleJS = 'assets/js/boat.js';

                                if (!in_array($moduleJS, Self::$assets['js']['modulejs'])) {

                                    Self::$assets['js']['modulejs'][] = $moduleJS;
                                }
                            }
                        }
                        $response .= Self::boatHTML($title, $desc, $config, $layout, $records, $filter, $extraclass);
                    } else if ($module == 'boat_template') {
                        $title = $section['val']['title'];
                        $config = $section['val']['config'];
                        $layout = $section['val']['layout'];
                        $filter = $section['val']['template'];
                        if (isset($section['val']['extclass']) && $section['val']['extclass'] != '') {
                            $class = $section['val']['extclass'];
                        } else {
                            $class = '';
                        }
                        $response .= Self::AllboatHTML($title, $config, $layout, $filter, $class);
                    } else if ($module == 'work') {
                        $title = $section['val']['title'];
                        $config = $section['val']['config'];
                        if (isset($section['val']['desc']) && $section['val']['desc'] != '') {
                            $desc = $section['val']['desc'];
                        } else {
                            $desc = '';
                        }
                        $layout = $section['val']['layout'];
                        $records = $section['val']['records'];
                        $filter = $section['val']['template'];
                        if (isset($section['val']['extraclass']) && $section['val']['extraclass'] != '') {
                            $extraclass = $section['val']['extraclass'];
                        } else {
                            $extraclass = '';
                        }
                        $recIds = array_column($records, 'id');
                        $fields = Self::selectFields($config);
                        $fill = \Powerpanel\Work\Models\Work::getWorkList($fields, $recIds, 5);
                        if ($fill->count() > 0) {
                            $owlCaurosolLib = 'assets/libraries/owl.carousel/js/owl.carousel.min.js';

                            if (!in_array($owlCaurosolLib, Self::$assets['js']['lib'])) {

                                Self::$assets['js']['lib'][] = $owlCaurosolLib;
                            }

                            $owlCaurosolLib1 = 'assets/libraries/libraries-update/owl.carousel/js/owl.carousel-update.js';

                            if (!in_array($owlCaurosolLib1, Self::$assets['js']['lib'])) {

                                Self::$assets['js']['lib'][] = $owlCaurosolLib1;
                            }

                            $owlCaurosolLib2 = 'assets/js/index.js';

                            if (!in_array($owlCaurosolLib2, Self::$assets['js']['lib'])) {

                                Self::$assets['js']['lib'][] = $owlCaurosolLib2;
                            }

                            if ($module != 'work') {

                                $moduleJS = 'assets/js/work.js';

                                if (!in_array($moduleJS, Self::$assets['js']['modulejs'])) {

                                    Self::$assets['js']['modulejs'][] = $moduleJS;
                                }
                            }
                        }
                        $response .= Self::workHTML($title, $desc, $config, $layout, $records, $filter, $extraclass);
                    } else if ($module == 'work_template') {
                        $title = $section['val']['title'];
                        $config = $section['val']['config'];
                        $layout = $section['val']['layout'];
                        $filter = $section['val']['template'];
                        if (isset($section['val']['extclass']) && $section['val']['extclass'] != '') {
                            $class = $section['val']['extclass'];
                        } else {
                            $class = '';
                        }
                        $response .= Self::AllworkHTML($title, $config, $layout, $filter, $class);
                    } else if ($module == 'news') {
                        $title = $section['val']['title'];
                        $config = $section['val']['config'];
                        $desc = $section['val']['desc'];
                        $layout = $section['val']['layout'];
                        $records = $section['val']['records'];
                        $filter = $section['val']['template'];
                        $extraclass = $section['val']['extraclass'];
                        $recIds = array_column($records, 'id');
                        $fields = Self::selectFields($config);
                        $fill = \Powerpanel\News\Models\News::getBuilderNews($fields, $recIds);
                        if ($fill->count() > 0) {
                            $owlCaurosolLib = 'assets/libraries/owl.carousel/js/owl.carousel.min.js';

                            if (!in_array($owlCaurosolLib, Self::$assets['js']['lib'])) {

                                Self::$assets['js']['lib'][] = $owlCaurosolLib;
                            }

                            $owlCaurosolLib1 = 'assets/libraries/libraries-update/owl.carousel/js/owl.carousel-update.js';

                            if (!in_array($owlCaurosolLib1, Self::$assets['js']['lib'])) {

                                Self::$assets['js']['lib'][] = $owlCaurosolLib1;
                            }

                            $owlCaurosolLib2 = 'assets/js/index.js';

                            if (!in_array($owlCaurosolLib2, Self::$assets['js']['lib'])) {

                                Self::$assets['js']['lib'][] = $owlCaurosolLib2;
                            }

                            if ($module != 'news') {

                                $moduleJS = 'assets/js/news.js';

                                if (!in_array($moduleJS, Self::$assets['js']['modulejs'])) {

                                    Self::$assets['js']['modulejs'][] = $moduleJS;
                                }
                            }
                        }
                        $response .= Self::newsHTML($title, $desc, $config, $layout, $records, $filter, $extraclass);
                    } else if ($module == 'news_template') {
                        $title = $section['val']['title'];
                        $limit = $section['val']['limit'];
                        $desc = $section['val']['desc'];
                        $config = $section['val']['config'];
                        $layout = $section['val']['layout'];
                        $filter = $section['val']['template'];
                        if (isset($section['val']['class']) && $section['val']['class'] != '') {
                            $class = $section['val']['class'];
                        } else {
                            $class = '';
                        }
                        if (isset($section['val']['sdate']) && $section['val']['sdate'] != '') {
                            $sdate = $section['val']['sdate'];
                        } else {
                            $sdate = '';
                        }
                        if (isset($section['val']['edate']) && $section['val']['edate'] != '') {
                            $edate = $section['val']['edate'];
                        } else {
                            $edate = '';
                        }
                        if (isset($section['val']['newscat']) && $section['val']['newscat'] != '') {
                            $newscat = $section['val']['newscat'];
                        } else {
                            $newscat = '';
                        }
                        $response .= Self::AllnewsHTML($title, $limit, $desc, $config, $layout, $filter, $class, $sdate, $edate, $newscat);
                    } else if ($module == 'links') {
                        $title = $section['val']['title'];
                        $records = $section['val']['records'];
                        $filter = $section['val']['template'];
                        $extraclass = $section['val']['extraclass'];
                        $response .= Self::linksHTML($title, $records, $filter, $extraclass);
                    } else if ($module == 'link_template') {
                        $title = $section['val']['title'];
                        $limit = $section['val']['limit'];
                        $sdate = $section['val']['sdate'];
                        $edate = $section['val']['edate'];
                        $class = $section['val']['class'];
                        $linkcat = $section['val']['linkcat'];
                        $filter = $section['val']['template'];
                        $response .= Self::AllLinksHTML($title, $limit, $sdate, $edate, $class, $linkcat, $filter);
                    } else if ($module == 'faqs') {
                        $title = $section['val']['title'];
                        $records = $section['val']['records'];
                        $filter = $section['val']['template'];
                        $extraclass = $section['val']['extraclass'];
                        $response .= Self::faqsHTML($title, $records, $filter, $extraclass);
                    } else if ($module == 'faq_template') {
                        $title = $section['val']['title'];
                        $limit = $section['val']['limit'];
                        $sdate = $section['val']['sdate'];
                        $edate = $section['val']['edate'];
                        $class = $section['val']['class'];
                        $faqcat = $section['val']['faqcat'];
                        $filter = $section['val']['template'];
                        $response .= Self::AllFaqsHTML($title, $limit, $sdate, $edate, $class, $faqcat, $filter);
                    } else if ($module == 'publication') {
                        $title = $section['val']['title'];
                        $config = $section['val']['config'];
                        $desc = $section['val']['desc'];
                        $layout = $section['val']['layout'];
                        $records = $section['val']['records'];
                        $filter = $section['val']['template'];
                        $extraclass = $section['val']['extraclass'];
                        $recIds = array_column($records, 'id');
                        $fields = Self::selectFields($config);
                        $fill = \Powerpanel\Publications\Models\Publications::getBuilderPublication($fields, $recIds);
                        if ($fill->count() > 0) {
                            $owlCaurosolLib = 'assets/libraries/owl.carousel/js/owl.carousel.min.js';

                            if (!in_array($owlCaurosolLib, Self::$assets['js']['lib'])) {

                                Self::$assets['js']['lib'][] = $owlCaurosolLib;
                            }

                            $owlCaurosolLib1 = 'assets/libraries/libraries-update/owl.carousel/js/owl.carousel-update.js';

                            if (!in_array($owlCaurosolLib1, Self::$assets['js']['lib'])) {

                                Self::$assets['js']['lib'][] = $owlCaurosolLib1;
                            }

                            $owlCaurosolLib2 = 'assets/js/index.js';

                            if (!in_array($owlCaurosolLib2, Self::$assets['js']['lib'])) {

                                Self::$assets['js']['lib'][] = $owlCaurosolLib2;
                            }

                            if ($module != 'publication') {

                                $moduleJS = 'assets/js/publication.js';

                                if (!in_array($moduleJS, Self::$assets['js']['modulejs'])) {

                                    Self::$assets['js']['modulejs'][] = $moduleJS;
                                }
                            }
                        }
                        $response .= Self::publicationHTML($title, $desc, $config, $layout, $records, $filter, $extraclass);
                    } else if ($module == 'publication_template') {
                        $title = $section['val']['title'];
                        $limit = $section['val']['limit'];
                        $desc = $section['val']['desc'];
                        $config = $section['val']['config'];
                        $sdate = $section['val']['sdate'];
                        $edate = $section['val']['edate'];
                        $class = $section['val']['class'];
                        $publicationscat = $section['val']['publicationscat'];
                        $layout = $section['val']['layout'];
                        $filter = $section['val']['template'];
                        $response .= Self::AllpublicationHTML($title, $limit, $desc, $config, $layout, $filter, $sdate, $edate, $class, $publicationscat);
                    } else if ($module == 'home-img_content') {
                        $title = $section['val']['title'];
                        $btnurl = isset($section['val']['btnurl'])?$section['val']['btnurl']:'';
                        $image = $section['val']['image'];
                        $content = $section['val']['content'];
                        $alignment = $section['val']['alignment'];
                        $src = $section['val']['src'];
                        $response .= Self::WelComeHTML($title, $image, $content, $alignment, $src,$btnurl);
                    } else if ($module == 'map') {
                        $latitude = $section['val']['latitude'];
                        $longitude = $section['val']['longitude'];
                        $response .= Self::MapHTML($latitude, $longitude);
                    } else if ($module == 'conatct_info') {
                        $content = $section['val']['content'];
                        $section_address = $section['val']['section_address'];
                        $section_email = $section['val']['section_email'];
                        $section_phone = $section['val']['section_phone'];
                        $response .= Self::ConatctInfoHTML($content, $section_address, $section_email, $section_phone);
                    } else if ($module == 'button_info') {
                        $title = $section['val']['title'];
                        $content = $section['val']['content'];
                        $alignment = $section['val']['alignment'];
                        $target = $section['val']['target'];
                        $response .= Self::ButtonHTML($title, $content, $alignment, $target);
                    }
                }

                $i++;
            }
        }
        if (empty($response)) {

            $response = $data;
        }

        return ['response' => $response, 'assets' => Self::$assets];
    }

    static function eventHTML($title, $desc, $config, $layout, $records, $filter, $extraclass) {
        $response = '';
        $recIds = array_column($records, 'id');
        $fields = Self::selectFields($config);
        if (File::exists(base_path() . '/packages/Powerpanel/Events/src/Models/Events.php') != null) {
            $fill = \Powerpanel\Events\Models\Events::getBuilderEvents($fields, $recIds);
            foreach ($records as $key => $row) {
                if (count(array_unique($row['custom_fields'])) > 1) {
                    $fill[($key - 1)]->custom = $row['custom_fields'];
                }
            }
            if (!empty($fill)) {
                $data = [
                    'title' => $title,
                    'desc' => $desc,
                    'events' => $fill,
                    'paginatehrml' => false,
                    'cols' => trim($layout),
                    'filter' => $filter,
                    'class' => $extraclass
                ];
                $response = view('events::frontview.builder-sections.events', compact('data'))->render();
            }
        } else {
            $response = '';
        }
        return $response;
    }

    static function AlleventsHTML($title, $limit, $desc, $config, $layout, $filter, $class, $sdate, $edate, $eventscat) {
        $response = '';
//        $recIds = array_column($records, 'id');
        $fields = Self::selectFields($config);
        if (File::exists(base_path() . '/packages/Powerpanel/Events/src/Models/Events.php') != null) {
            if ($filter == 'current-months-events') {
                $fill = \Powerpanel\Events\Models\Events::getCurrentMonthEvents($fields, $limit, $sdate, $edate, $eventscat);
            } else {
                $fill = \Powerpanel\Events\Models\Events::getAllEvents($fields, $limit, $sdate, $edate, $eventscat);
            }

            if (!empty($fill)) {
                $data = [
                    'title' => $title,
                    'limit' => $limit,
                    'desc' => $desc,
                    'events' => $fill,
                    'paginatehrml' => true,
                    'cols' => trim($layout),
                    'filter' => $filter,
                    'class' => $class
                ];
                $response = view('events::frontview.builder-sections.events', compact('data'))->render();
            }
        } else {
            $response = '';
        }
        return $response;
    }

    static function newsHTML($title, $desc, $config, $layout, $records, $filter, $extraclass) {
        $response = '';
        $recIds = array_column($records, 'id');
        $fields = Self::selectFields($config);
        if (File::exists(base_path() . '/packages/Powerpanel/News/src/Models/News.php') != null) {
            $fill = \Powerpanel\News\Models\News::getBuilderNews($fields, $recIds);
            foreach ($records as $key => $row) {
                if (count(array_unique($row['custom_fields'])) > 1) {
                    $fill[($key - 1)]->custom = $row['custom_fields'];
                }
            }
            if (!empty($fill)) {
                $data = [
                    'title' => $title,
                    'desc' => $desc,
                    'news' => $fill,
                    'paginatehrml' => false,
                    'cols' => trim($layout),
                    'filter' => $filter,
                    'class' => $extraclass
                ];
                $response = view('news::frontview.builder-sections.news', compact('data'))->render();
            }
        } else {
            $response = '';
        }
        return $response;
    }

    static function AllnewsHTML($title, $limit, $desc, $config, $layout, $filter, $class, $sdate, $edate, $newscat) {
        $response = '';

        $fields = Self::selectFields($config);
        if (File::exists(base_path() . '/packages/Powerpanel/News/src/Models/News.php') != null) {
            $fill = \Powerpanel\News\Models\News::getAllNews($fields, $limit, $sdate, $edate, $newscat);

            if (!empty($fill)) {
                $data = [
                    'title' => $title,
                    'limit' => $limit,
                    'desc' => $desc,
                    'news' => $fill,
                    'paginatehrml' => true,
                    'cols' => trim($layout),
                    'filter' => $filter,
                    'class' => $class,
                ];
                $response = view('news::frontview.builder-sections.news', compact('data'))->render();
            }
        } else {
            $response = '';
        }
        return $response;
    }

    static function alertsHTML($title, $records, $filter, $extraclass) {
        $response = '';
        $recIds = array_column($records, 'id');
        if (File::exists(base_path() . '/packages/Powerpanel/Alerts/src/Models/Alerts.php') != null) {
            $fill = \Powerpanel\Alerts\Models\Alerts::getBuilderAlerts($recIds);
            foreach ($records as $key => $row) {
                if (count(array_unique($row['custom_fields'])) > 1) {
                    $fill[($key - 1)]->custom = $row['custom_fields'];
                }
            }
            if (!empty($fill)) {
                $data = [
                    'title' => $title,
                    'alerts' => $fill,
                    'paginatehrml' => false,
                    'filter' => $filter,
                    'class' => $extraclass
                ];
                $alertsArr = \Powerpanel\Alerts\Models\Alerts::getBuilderAlerts($recIds);
                if (!empty($alertsArr)) {
                    foreach ($alertsArr as $key => $value) {
                        $linkUrl = \Powerpanel\Alerts\Models\Alerts::getInternalLinkHtml($value);
                        $data[$key]['url'] = $linkUrl;
                        $data[$key]['moduleName'] = $value->modules->varModuleName;
                        $data[$key]['moduleId'] = $value->modules->id;
                        $data[$key]['varTitle'] = $value->varTitle;
                        $data[$key]['intAlertType'] = $value->intAlertType;
                    }
                }
                $resultarry = array();
                foreach ($data as $row) {
                    if (isset($row['intAlertType'])) {
                        $resultarry[$row['intAlertType']][] = $row;
                    }
                }
                $data['alertsArr'] = $resultarry;
                $response = view('alerts::frontview.builder-sections.alerts', compact('data'))->render();
            }
        } else {
            $response = '';
        }
        return $response;
    }

    static function AllalertsHTML($title, $limit, $alerttype, $sdate, $edate, $class, $filter) {
        $response = '';
        if (File::exists(base_path() . '/packages/Powerpanel/Alerts/src/Models/Alerts.php') != null) {
            $fill = \Powerpanel\Alerts\Models\Alerts::getAllAlerts($limit, $alerttype, $sdate, $edate);
            if (!empty($fill)) {
                $data = [
                    'title' => $title,
                    'limit' => $limit,
                    'alerttype' => $alerttype,
                    'alerts' => $fill,
                    'paginatehrml' => true,
                    'class' => $class,
                    'filter' => $filter
                ];
                $alertsArr = \Powerpanel\Alerts\Models\Alerts::getAllAlerts($limit, $alerttype, $sdate, $edate);

                if (!empty($alertsArr)) {
                    foreach ($alertsArr as $key => $value) {
                        $linkUrl = \Powerpanel\Alerts\Models\Alerts::getInternalLinkHtml($value);
                        $data[$key]['url'] = $linkUrl;
                        $data[$key]['moduleName'] = $value->modules->varModuleName;
                        $data[$key]['moduleId'] = $value->modules->id;
                        $data[$key]['varTitle'] = $value->varTitle;
                        $data[$key]['intAlertType'] = $value->intAlertType;
                    }
                }
                $resultarry = array();
                foreach ($data as $row) {
                    if (isset($row['intAlertType'])) {
                        $resultarry[$row['intAlertType']][] = $row;
                    }
                }

                $data['alertsArr'] = $resultarry;
                $response = view('alerts::frontview.builder-sections.alerts', compact('data'))->render();
            }
        } else {
            $response = '';
        }
        return $response;
    }

    public static function galleryHTML($title, $config, $layout, $records, $filter, $innerpage = null, $extclass = null) {
        $response = '';
        $data = array();
        $recIds = array_column($records, 'id');
        $fields = Self::selectFields($config);
        $limit = 12;
        $galleryObj = \Powerpanel\Gallery\Models\Gallery::getGalleryList($recIds, $limit);
        foreach ($records as $key => $row) {
            if (count(array_unique($row['custom_fields'])) > 1) {
                $galleryObj[($key - 1)]->custom = $row['custom_fields'];
            }
        }
        if ($galleryObj->count() > 0) {
            $listingPage = false;
            if (Request::segment(1) != null && Request::segment(2) == null) {
                $listingPage = true;
            }
            $detailPage = false;
            if (Request::segment(1) != null && Request::segment(2) != null) {
                $detailPage = true;
            }
            $data['listingPage'] = $listingPage;
            $data['detailPage'] = $detailPage;
            $data['title'] = $title;
            $data['cols'] = trim($layout);
            $data['innerpage'] = $innerpage;
            $data['extclass'] = trim($extclass);
            $data['filter'] = $filter;
            $data['imageGalleyObj'] = $galleryObj;
            $response = view('gallery::frontview.builder-sections.gallery-list', $data)->render();
        }
        return $response;
    }

    public static function AllgalleryHTML($title, $class, $config, $layout, $filter) {
        $response = '';
        $data = array();
        $fields = Self::selectFields($config);
        $limit = 12;
        $galleryObj = \Powerpanel\Gallery\Models\Gallery::getTemplateGalleryList($filter);

        if ($galleryObj->count() > 0) {
            $listingPage = false;
            if (Request::segment(1) != null && Request::segment(2) == null) {
                $listingPage = true;
            }
            $detailPage = false;
            if (Request::segment(1) != null && Request::segment(2) != null) {
                $detailPage = true;
            }
            $data['title'] = $title;
            $data['cols'] = trim($layout);
            $data['extclass'] = $class;
            $data['imageGalleyObj'] = $galleryObj;
            $data['listingPage'] = $listingPage;
            $data['detailPage'] = $detailPage;
            $response = view('gallery::frontview.builder-sections.gallery-list', $data)->render();
        }
        return $response;
    }

    static function showHTML($title, $records, $filter,$class, $config, $layout) {
        $response = '';
        $recIds = array_column($records, 'id');
        if (File::exists(base_path() . '/packages/Powerpanel/Show/src/Models/Show.php') != null) {
            $fill = \Powerpanel\Show\Models\Show::getBuilderShows($recIds);
            foreach ($records as $key => $row) {
                if (count(array_unique($row['custom_fields'])) > 1) {
                    $fill[($key - 1)]->custom = $row['custom_fields'];
                }
            }
            if (!empty($fill)) {
                $data = [
                    'title' => $title,
                    'show' => $fill,
                    'class' => $class,
                    'filter' => $filter,
                    'config' => $config,
                    'paginatehrml' => false,
                    'cols' => $layout
                ];
                $response = view('show::frontview.builder-sections.shows', compact('data'))->render();
            }
        } else {
            $response = '';
        }
        return $response;
    }

    static function AllshowHTML($title, $class, $config, $layout, $filter) {
        $response = '';
        if (File::exists(base_path() . '/packages/Powerpanel/Show/src/Models/Show.php') != null) {
            $fill = \Powerpanel\Show\Models\Show::getTemplateShowList();
            if (!empty($fill)) {
                $data = [
                    'title' => $title,
                    'show' => $fill,
                    'class' => $class,
                    'filter' => $filter,
                    'config' => $config,
                    'paginatehrml' => true,
                    'cols' => $layout
                ];
                $response = view('show::frontview.builder-sections.shows', compact('data'))->render();
            }
        } else {
            $response = '';
        }
        return $response;
    }

    static function productHTML($title, $records, $filter, $class, $config, $layout) {
        $response = '';
        $recIds = array_column($records, 'id');
        if (File::exists(base_path() . '/packages/Powerpanel/Products/src/Models/Products.php') != null) {
            $fill = \Powerpanel\Products\Models\Products::getProductList($recIds);
            foreach ($records as $key => $row) {
                if (count(array_unique($row['custom_fields'])) > 1) {
                    $fill[($key - 1)]->custom = $row['custom_fields'];
                }
            }
            if (!empty($fill)) {
                $data = [
                    'title' => $title,
                    'products' => $fill,
                    'class' => $class,
                    'filter' => $filter,
                    'config' => $config,
                    'paginatehrml' => false,
                    'cols' => $layout
                ];
                $response = view('products::frontview.builder-sections.products', compact('data'))->render();
            }
        } else {
            $response = '';
        }
        return $response;
    }

    static function AllproductHTML($title, $class, $config, $layout, $filter) {
        $response = '';
        if (File::exists(base_path() . '/packages/Powerpanel/Products/src/Models/Products.php') != null) {
            $fill = \Powerpanel\Products\Models\Products::getTemplateProductList();
            if (!empty($fill)) {
                $data = [
                    'title' => $title,
                    'products' => $fill,
                    'class' => $class,
                    'filter' => $filter,
                    'config' => $config,
                    'paginatehrml' => true,
                    'cols' => $layout
                ];
                $response = view('products::frontview.builder-sections.products', compact('data'))->render();
            }
        } else {
            $response = '';
        }
        return $response;
    }

    static function testimonialHTML($title, $records, $filter, $class, $layout) {
        $response = '';
        $response = '';
        $recIds = array_column($records, 'id');
        if (File::exists(base_path() . '/packages/Powerpanel/Testimonial/src/Models/Testimonial.php') != null) {
            $fill = \Powerpanel\Testimonial\Models\Testimonial::getTestimonialList($recIds);
            foreach ($records as $key => $row) {
                if (count(array_unique($row['custom_fields'])) > 1) {
                    $fill[($key - 1)]->custom = $row['custom_fields'];
                }
            }
            if (!empty($fill)) {
                $data = [
                    'title' => $title,
                    'testimonial' => $fill,
                    'class' => $class,
                    'paginatehrml' => false,
                    'filter' => $filter,
                    'cols' => $layout
                ];

                $response = view('testimonial::frontview.builder-sections.testimonial', compact('data'))->render();
            }
        } else {
            $response = '';
        }
        return $response;
    }

    static function AlltestimonialHTML($title, $config, $layout, $class, $filter) {
        $response = '';
        if (File::exists(base_path() . '/packages/Powerpanel/Testimonial/src/Models/Testimonial.php') != null) {
            $fill = \Powerpanel\Testimonial\Models\Testimonial::getTemplateTestimonialList();
            if (!empty($fill)) {
                $data = [
                    'title' => $title,
                    'config' => $config,
                    'testimonial' => $fill,
                    'class' => $class,
                    'paginatehrml' => true,
                    'cols' => $layout,
                    'filter' => $filter
                ];
                $response = view('testimonial::frontview.builder-sections.testimonial-list', compact('data'))->render();
            }
        } else {
            $response = '';
        }
        return $response;
    }

    static function teamHTML($title, $desc, $class, $config, $records, $filter, $layout) {
        $response = '';
        $response = '';
        $recIds = array_column($records, 'id');
        if (File::exists(base_path() . '/packages/Powerpanel/Team/src/Models/Team.php') != null) {
            $fill = \Powerpanel\Team\Models\Team::getTeamList($recIds);
            foreach ($records as $key => $row) {
                if (count(array_unique($row['custom_fields'])) > 1) {
                    $fill[($key - 1)]->custom = $row['custom_fields'];
                }
            }
            if (!empty($fill)) {
                $data = [
                    'title' => $title,
                    'desc' => $desc,
                    'class' => $class,
                    'config' => $config,
                    'team' => $fill,
                    'paginatehrml' => false,
                    'filter' => $filter,
                    'cols' => $layout
                ];

                $response = view('team::frontview.builder-sections.team', compact('data'))->render();
            }
        } else {
            $response = '';
        }
        return $response;
    }

    static function AllteamHTML($title, $config, $layout, $class, $filter) {
        $response = '';
        if (File::exists(base_path() . '/packages/Powerpanel/Team/src/Models/Team.php') != null) {
            $fill = \Powerpanel\Team\Models\Team::getTemplateTeamList();
            if (!empty($fill)) {
                $data = [
                    'title' => $title,
                    'config' => $config,
                    'team' => $fill,
                    'class' => $class,
                    'paginatehrml' => true,
                    'cols' => $layout,
                    'filter' => $filter
                ];
                $response = view('team::frontview.builder-sections.team-list', compact('data'))->render();
            }
        } else {
            $response = '';
        }
        return $response;
    }

    static function clientHTML($title, $desc, $class, $config, $records, $filter, $layout) {
        $response = '';
        $response = '';
        $recIds = array_column($records, 'id');
        if (File::exists(base_path() . '/packages/Powerpanel/Client/src/Models/Client.php') != null) {
            $fill = \Powerpanel\Client\Models\Client::getClientList($recIds);
            foreach ($records as $key => $row) {
                if (count(array_unique($row['custom_fields'])) > 1) {
                    $fill[($key - 1)]->custom = $row['custom_fields'];
                }
            }
            if (!empty($fill)) {
                $data = [
                    'title' => $title,
                    'desc' => $desc,
                    'class' => $class,
                    'config' => $config,
                    'client' => $fill,
                    'paginatehrml' => false,
                    'filter' => $filter,
                    'cols' => $layout
                ];

                $response = view('client::frontview.builder-sections.clients', compact('data'))->render();
            }
        } else {
            $response = '';
        }
        return $response;
    }

    static function AllclientHTML($title, $config, $layout, $class, $filter) {
        $response = '';
        if (File::exists(base_path() . '/packages/Powerpanel/Client/src/Models/Client.php') != null) {
            $fill = \Powerpanel\Client\Models\Client::getTemplateClientList();
            if (!empty($fill)) {
                $data = [
                    'title' => $title,
                    'config' => $config,
                    'client' => $fill,
                    'class' => $class,
                    'paginatehrml' => true,
                    'cols' => $layout,
                    'filter' => $filter
                ];
                $response = view('client::frontview.builder-sections.clients', compact('data'))->render();
            }
        } else {
            $response = '';
        }
        return $response;
    }

    static function projectHTML($title, $desc, $class, $config, $records, $filter, $layout) {
        $response = '';
        $response = '';
        $recIds = array_column($records, 'id');
        if (File::exists(base_path() . '/packages/Powerpanel/Projects/src/Models/Projects.php') != null) {
            $fill = \Powerpanel\Projects\Models\Projects::getProjectsList($recIds);
            foreach ($records as $key => $row) {
                if (count(array_unique($row['custom_fields'])) > 1) {
                    $fill[($key - 1)]->custom = $row['custom_fields'];
                }
            }
            if (!empty($fill)) {
                $data = [
                    'title' => $title,
                    'desc' => $desc,
                    'class' => $class,
                    'config' => $config,
                    'projects' => $fill,
                    'paginatehrml' => false,
                    'filter' => $filter,
                    'cols' => $layout
                ];

                $response = view('projects::frontview.builder-sections.projects', compact('data'))->render();
            }
        } else {
            $response = '';
        }
        return $response;
    }

    static function AllprojectHTML($title, $config, $layout, $class, $filter) {
        $response = '';
        if (File::exists(base_path() . '/packages/Powerpanel/Projects/src/Models/Projects.php') != null) {
            $fill = \Powerpanel\Projects\Models\Projects::getTemplateProjectsList();
            if (!empty($fill)) {
                $data = [
                    'title' => $title,
                    'config' => $config,
                    'projects' => $fill,
                    'class' => $class,
                    'paginatehrml' => true,
                    'cols' => $layout,
                    'filter' => $filter
                ];
                $response = view('projects::frontview.builder-sections.projects', compact('data'))->render();
            }
        } else {
            $response = '';
        }
        return $response;
    }

    static function organizationsHTML($title, $parentorg, $orgclass, $filter) {
        $response = '';

        $data = [
            'title' => $title,
            'orgclass' => $orgclass,
            'filter' => $filter
        ];
        if (File::exists(base_path() . '/packages/Powerpanel/Organizations/src/Models/Organizations.php') != null) {
            $organizationData = \Powerpanel\Organizations\Models\Organizations::getBuilderOrganizations($parentorg);
            $orgdata = array();
            if (!empty($organizationData) && count($organizationData) > 0) {
                foreach ($organizationData as $orgnization) {

                    if ($orgnization['varDesignation'] != '') {
                        $designation = '<span class=\"desig-div\"><i class=\"fa fa-user-o\"></i>' . $orgnization['varDesignation'] . '</span>';
                    } else {
                        $designation = '';
                    }
                    $ogData = array();
                    $tempData = array();
                    $tempData['v'] = (String) $orgnization['id'];
                    $tempData['f'] = $orgnization['varTitle'] . $designation;
                    $ogData[] = $tempData;
                    if ($orgnization['intParentCategoryId'] > 0) {
                        array_push($ogData, (String) $orgnization['intParentCategoryId']);
                    } else {
                        array_push($ogData, null);
                    }
                    array_push($ogData, addslashes($orgnization['varTitle']));
                    $orgdata[] = $ogData;
                }
            }
            $orgdata = json_encode($orgdata);
            $data['orgdata'] = $orgdata;
            $data['orgclass'] = $orgclass;
            $response = view('organizations::frontview.builder-sections.organizations', compact('data', 'orgdata', 'orgclass'))->render();
        } else {
            $response = '';
        }
        return $response;
    }

    static function departmentHTML($title, $records, $filter, $extraclass) {
        $response = '';
        $recIds = array_column($records, 'id');
        if (File::exists(base_path() . '/packages/Powerpanel/Department/src/Models/Department.php') != null) {
            $fill = \Powerpanel\Department\Models\Department::getBuilderDepartment($recIds);
            foreach ($records as $key => $row) {
                if (count(array_unique($row['custom_fields'])) > 1) {
                    $fill[($key - 1)]->custom = $row['custom_fields'];
                }
            }
            if (!empty($fill)) {
                $data = [
                    'title' => $title,
                    'department' => $fill,
                    'filter' => $filter,
                    'class' => $extraclass
                ];
                $departmentArr = \Powerpanel\Department\Models\Department::getFrontList();
                $data['departmentArr'] = $departmentArr;
                $data['extraclass'] = $extraclass;

                $response = view('department::frontview.builder-sections.departments', compact('data'))->render();
            }
        } else {
            $response = '';
        }
        return $response;
    }

    static function AlldepartmentHTML($title, $limit, $sdate, $edate, $class, $filter) {
        $response = '';
        if (File::exists(base_path() . '/packages/Powerpanel/Department/src/Models/Department.php') != null) {
            $fill = \Powerpanel\Department\Models\Department::getAllDepartment($limit, $sdate, $edate);
            if (!empty($fill)) {
                $data = [
                    'title' => $title,
                    'limit' => $limit,
                    'department' => $fill,
                    'class' => $class,
                    'filter' => $filter
                ];
                $departmentArr = \Powerpanel\Department\Models\Department::getAllDepartment($limit, $sdate, $edate);
                $data['departmentArr'] = $departmentArr;
                $response = view('department::frontview.builder-sections.departments', compact('data'))->render();
            }
        } else {
            $response = '';
        }
        return $response;
    }

    static function linksHTML($title, $records, $filter, $extraclass) {
        $response = '';
        $recIds = array_column($records, 'id');
        if (File::exists(base_path() . '/packages/Powerpanel/Links/src/Models/Links.php') != null) {
            $fill = \Powerpanel\Links\Models\Links::getBuilderLinks($recIds);
            foreach ($records as $key => $row) {
                if (count(array_unique($row['custom_fields'])) > 1) {
                    $fill[($key - 1)]->custom = $row['custom_fields'];
                }
            }
            if (!empty($fill)) {
                $data = [
                    'title' => $title,
                    'links' => $fill,
                    'filter' => $filter,
                    'class' => $extraclass,
                    'selectionlink' => 'Y'
                ];
                $response = view('links::frontview.builder-sections.links', compact('data'))->render();
            }
        } else {
            $response = '';
        }
        return $response;
    }

    static function AllLinksHTML($title, $limit, $sdate, $edate, $class, $linkcat, $filter) {
        $response = '';
        if (File::exists(base_path() . '/packages/Powerpanel/LinksCategory/src/Models/LinksCategory.php') != null) {
            $fill = \Powerpanel\LinksCategory\Models\LinksCategory::getAllLinks($linkcat, $limit, $sdate, $edate);
            if (!empty($fill)) {
                $data = [
                    'title' => $title,
                    'limit' => $limit,
                    'links' => $fill,
                    'class' => $class,
                    'filter' => $filter
                ];
                $response = view('links::frontview.builder-sections.links', compact('data'))->render();
            }
        } else {
            $response = '';
        }
        return $response;
    }

    static function faqsHTML($title, $records, $filter, $extraclass) {
        $response = '';
        $recIds = array_column($records, 'id');
        if (File::exists(base_path() . '/packages/Powerpanel/Faq/src/Models/Faq.php') != null) {
            $fill = \Powerpanel\Faq\Models\Faq::getBuilderFaq($recIds);
            foreach ($records as $key => $row) {
                if (count(array_unique($row['custom_fields'])) > 1) {
                    $fill[($key - 1)]->custom = $row['custom_fields'];
                }
            }
            if (!empty($fill)) {
                $data = [
                    'title' => $title,
                    'faqs' => $fill,
                    'filter' => $filter,
                    'class' => $extraclass
                ];
                $response = view('faq::frontview.builder-sections.faq', compact('data'))->render();
            }
        } else {
            $response = '';
        }
        return $response;
    }

    static function AllFaqsHTML($title, $limit, $sdate, $edate, $class, $faqcat, $filter) {
        $response = '';
        if (File::exists(base_path() . '/packages/Powerpanel/Faq/src/Models/Faq.php') != null) {
            $fill = \Powerpanel\Faq\Models\Faq::getAllFaqs($faqcat, $limit, $sdate, $edate);
            if (!empty($fill)) {
                $data = [
                    'title' => $title,
                    'limit' => $limit,
                    'faqs' => $fill,
                    'class' => $class,
                    'filter' => $filter
                ];
                $response = view('faq::frontview.builder-sections.faqs-list', compact('data'))->render();
            }
        } else {
            $response = '';
        }
        return $response;
    }

    static function publicationHTML($title, $desc, $config, $layout, $records, $filter, $extraclass) {
        $response = '';
        $recIds = array_column($records, 'id');
        $fields = Self::selectFields($config);
        if (File::exists(base_path() . '/packages/Powerpanel/Publications/src/Models/Publications.php') != null) {
            $fill = \Powerpanel\Publications\Models\Publications::getBuilderPublication($fields, $recIds);
            foreach ($records as $key => $row) {
                if (count(array_unique($row['custom_fields'])) > 1) {
                    $fill[($key - 1)]->custom = $row['custom_fields'];
                }
            }
            if (!empty($fill)) {
                $data = [
                    'title' => $title,
                    'desc' => $desc,
                    'publication' => $fill,
                    'cols' => trim($layout),
                    'paginatehrml' => false,
                    'filter' => $filter,
                    'class' => $extraclass
                ];
                $response = view('publications::frontview.builder-sections.publication', compact('data'))->render();
            }
        } else {
            $response = '';
        }
        return $response;
    }

    static function AllpublicationHTML($title, $limit, $desc, $config, $layout, $filter, $sdate, $edate, $class, $publicationscat) {
        $response = '';
//        $recIds = array_column($records, 'id');

        $fields = Self::selectFields($config);
        if (File::exists(base_path() . '/packages/Powerpanel/Publications/src/Models/Publications.php') != null) {
            $fill = \Powerpanel\Publications\Models\Publications::getAllPublication($fields, $limit, $sdate, $edate, $publicationscat);

            if (!empty($fill)) {
                $data = [
                    'title' => $title,
                    'limit' => $limit,
                    'desc' => $desc,
                    'publication' => $fill,
                    'cols' => trim($layout),
                    'paginatehrml' => true,
                    'filter' => $filter,
                    'class' => $class
                ];
                $response = view('publications::frontview.builder-sections.publication', compact('data'))->render();
            }
        } else {
            $response = '';
        }
        return $response;
    }

    static function blogsHTML($title, $desc, $config, $layout, $records, $filter, $extraclass) {
        $response = '';
        $recIds = array_column($records, 'id');
        $fields = Self::selectFields($config);
        if (File::exists(base_path() . '/packages/Powerpanel/Blogs/src/Models/Blogs.php') != null) {
            $fill = \Powerpanel\Blogs\Models\Blogs::getBuilderBlog($fields, $recIds);
            foreach ($records as $key => $row) {
                if (count(array_unique($row['custom_fields'])) > 1) {
                    $fill[($key - 1)]->custom = $row['custom_fields'];
                }
            }
            if (!empty($fill)) {
                $data = [
                    'title' => $title,
                    'desc' => $desc,
                    'blogs' => $fill,
                    'cols' => trim($layout),
                    'paginatehrml' => false,
                    'filter' => $filter,
                    'class' => $extraclass
                ];
                $response = view('blogs::frontview.builder-sections.blog', compact('data'))->render();
            }
        } else {
            $response = '';
        }
        return $response;
    }

    static function AllblogsHTML($title, $limit, $desc, $config, $layout, $filter, $class, $sdate, $edate, $blogscat) {
        $response = '';
//        $recIds = array_column($records, 'id');

        $fields = Self::selectFields($config);
        if (File::exists(base_path() . '/packages/Powerpanel/Blogs/src/Models/Blogs.php') != null) {
            $fill = \Powerpanel\Blogs\Models\Blogs::getAllBlogs($fields, $limit, $sdate, $edate, $blogscat);
            
            if (!empty($fill)) {
                $data = [
                    'title' => $title,
                    'limit' => $limit,
                    'desc' => $desc,
                    'blogs' => $fill,
                    'cols' => trim($layout),
                    'paginatehrml' => true,
                    'filter' => $filter,
                    'class' => $class
                ];
                $response = view('blogs::frontview.builder-sections.blogs-list', compact('data'))->render();
            }
        } else {
            $response = '';
        }
        return $response;
    }

    static function AllservicesHTML($title, $config, $layout, $filter, $class) {
        $response = '';
//        $recIds = array_column($records, 'id');

        $fields = Self::selectFields($config);
        if (File::exists(base_path() . '/packages/Powerpanel/Services/src/Models/Services.php') != null) {
            $fill = \Powerpanel\Services\Models\Services::getTemplateServiceList($fields);

            if (!empty($fill)) {
                $data = [
                    'title' => $title,
                    'services' => $fill,
                    'cols' => trim($layout),
                    'filter' => $filter,
                    'paginatehrml' => true,
                    'class' => $class
                ];
                $response = view('services::frontview.builder-sections.services-list', compact('data'))->render();
            }
        } else {
            $response = '';
        }
        return $response;
    }

    static function serviceHTML($title, $desc, $config, $layout, $records, $filter, $extraclass) {
        $response = '';
        $recIds = array_column($records, 'id');
        $fields = Self::selectFields($config);
        if (File::exists(base_path() . '/packages/Powerpanel/Services/src/Models/Services.php') != null) {
            $fill = \Powerpanel\Services\Models\Services::getServiceList($fields, $recIds);
            foreach ($records as $key => $row) {
                if (count(array_unique($row['custom_fields'])) > 1) {
                    $fill[($key - 1)]->custom = $row['custom_fields'];
                }
            }

            if (!empty($fill)) {
                $data = [
                    'title' => $title,
                    'desc' => $desc,
                    'services' => $fill,
                    'cols' => trim($layout),
                    'paginatehrml' => false,
                    'filter' => $filter,
                    'class' => $extraclass
                ];
                $response = view('services::frontview.builder-sections.services', compact('data'))->render();
            }
        } else {
            $response = '';
        }
        return $response;
    }

    static function AllboatHTML($title, $config, $layout, $filter, $class) {
        $response = '';
//        $recIds = array_column($records, 'id');
        
        $fields = Self::selectFields($config);
       
        if (File::exists(base_path() . '/packages/Powerpanel/Boat/src/Models/Boat.php') != null) {
            $fill = \Powerpanel\Boat\Models\Boat::getTemplateBoatList($fields);
            $brand = \Powerpanel\Boat\Models\Boat::getallBrands();
            $BoatCondition = \Powerpanel\Boat\Models\Boat::getallBoatCondition();
            $stock = \Powerpanel\Boat\Models\Boat::getallstock();
            $boatcategory = \Powerpanel\Boat\Models\Boat::getallBoatCategory();

            if (!empty($fill)) {
                $data = [
                    'title' => $title,
                    'brand'=>$brand,
                    'BoatCondition'=>$BoatCondition,
                    'stock'=>$stock,
                    'boatcategory'=>$boatcategory,
                    'boat' => $fill,
                    'cols' => trim($layout),
                    'filter' => $filter,
                    'paginatehrml' => true,
                    'class' => $class
                ];
                $response = view('boat::frontview.builder-sections.boat-list', compact('data'))->render();
            }
        } else {
            $response = '';
        }
        return $response;
    }

    static function boatHTML($title, $desc, $config, $layout, $records, $filter, $extraclass) {
        $response = '';
        $recIds = array_column($records, 'id');
        $fields = Self::selectFields($config);
        if (File::exists(base_path() . '/packages/Powerpanel/Boat/src/Models/Boat.php') != null) {
            $fill = \Powerpanel\Boat\Models\Boat::getBoatList($fields, $recIds);
            foreach ($records as $key => $row) {
                if (count(array_unique($row['custom_fields'])) > 1) {
                    $fill[($key - 1)]->custom = $row['custom_fields'];
                }
            }

            if (!empty($fill)) {
                $data = [
                    'title' => $title,
                    'desc' => $desc,
                    'boat' => $fill,
                    'cols' => trim($layout),
                    'paginatehrml' => false,
                    'filter' => $filter,
                    'class' => $extraclass
                ];
                $response = view('boat::frontview.builder-sections.boat', compact('data'))->render();
            }
        } else {
            $response = '';
        }
        return $response;
    }

    static function AllworkHTML($title, $config, $layout, $filter, $class) {
        $response = '';
//        $recIds = array_column($records, 'id');

        $fields = Self::selectFields($config);
        if (File::exists(base_path() . '/packages/Powerpanel/Work/src/Models/Work.php') != null) {
            $fill = \Powerpanel\Work\Models\Work::getTemplateWorkList($fields);

            if (!empty($fill)) {
                $data = [
                    'title' => $title,
                    'work' => $fill,
                    'cols' => trim($layout),
                    'filter' => $filter,
                    'paginatehrml' => true,
                    'class' => $class
                ];
                $response = view('work::frontview.builder-sections.work-list', compact('data'))->render();
            }
        } else {
            $response = '';
        }
        return $response;
    }

    static function workHTML($title, $desc, $config, $layout, $records, $filter, $extraclass) {
        $response = '';
        $recIds = array_column($records, 'id');
        $fields = Self::selectFields($config);
        if (File::exists(base_path() . '/packages/Powerpanel/Work/src/Models/Work.php') != null) {
            $fill = \Powerpanel\Work\Models\Work::getWorkList($fields, $recIds);
            foreach ($records as $key => $row) {
                if (count(array_unique($row['custom_fields'])) > 1) {
                    $fill[($key - 1)]->custom = $row['custom_fields'];
                }
            }

            if (!empty($fill)) {
                $data = [
                    'title' => $title,
                    'desc' => $desc,
                    'work' => $fill,
                    'cols' => trim($layout),
                    'paginatehrml' => false,
                    'filter' => $filter,
                    'class' => $extraclass
                ];
                $response = view('work::frontview.builder-sections.work', compact('data'))->render();
            }
        } else {
            $response = '';
        }
        return $response;
    }

    static function photoalbumHTML($title, $desc, $config, $layout, $records, $filter, $extraclass) {
        $response = '';
        $recIds = array_column($records, 'id');
        $fields = Self::selectFields($config);
        if (File::exists(base_path() . '/packages/Powerpanel/Gallery/src/Models/Gallery.php') != null) {
            $fill = \Powerpanel\Gallery\Models\Gallery::getBuilderPhotoAlbum($fields, $recIds);
            foreach ($records as $key => $row) {
                if (count(array_unique($row['custom_fields'])) > 1) {
                    $fill[($key - 1)]->custom = $row['custom_fields'];
                }
            }
            if (!empty($fill)) {
                $data = [
                    'title' => $title,
                    'desc' => $desc,
                    // 'photoalbum' => $fill,
                    'GalleryPage' => $fill,
                    'cols' => trim($layout),
                    'paginatehrml' => false,
                    'filter' => $filter,
                    'class' => $extraclass
                ];
                $response = view('gallery::frontview.builder-sections.gallery', compact('data'))->render();
            }
        } else {
            $response = '';
        }
        return $response;
    }

    static function AllphotoalbumHTML($title, $limit, $desc, $config, $layout, $filter, $class, $sdate, $edate) {
        $response = '';

        $fields = Self::selectFields($config);
        if (File::exists(base_path() . '/packages/Powerpanel/Gallery/src/Models/Gallery.php') != null) {
            $fill = \Powerpanel\Gallery\Models\Gallery::getAllGallery($fields, $limit, $sdate, $edate);
            if (!empty($fill)) {
                $data = [
                    'title' => $title,
                    'limit' => $limit,
                    'desc' => $desc,
                    'GalleryPage' => $fill,
                    'cols' => trim($layout),
                    'paginatehrml' => true,
                    'filter' => $filter,
                    'class' => $class,
                ];
                $response = view('gallery::frontview.builder-sections.list-gallery', compact('data'))->render();
            }
        } else {
            $response = '';
        }
        return $response;
    }

    static function videoalbumHTML($title, $desc, $config, $layout, $records, $filter) {
        $response = '';
        $recIds = array_column($records, 'id');
        $fields = Self::selectFields($config);
        if (File::exists(base_path() . '/packages/Powerpanel/VideoGallery/src/Models/VideoGallery.php') != null) {
            $fill = \Powerpanel\VideoGallery\Models\VideoGallery::getBuilderVideoGallery($fields, $recIds);
            foreach ($records as $key => $row) {
                if (count(array_unique($row['custom_fields'])) > 1) {
                    $fill[($key - 1)]->custom = $row['custom_fields'];
                }
            }
            if (!empty($fill)) {
                $data = [
                    'title' => $title,
                    'desc' => $desc,
                    'videogallery' => $fill,
                    'cols' => trim($layout),
                    'paginatehrml' => false,
                    'filter' => $filter,
                    // 'class' => $extraclass
                ];
                $response = view('video-gallery::frontview.builder-sections.videogallery', compact('data'))->render();
            }
        } else {
            $response = '';
        }
        return $response;
    }

    static function AllvideoalbumHTML($title, $limit, $desc, $config, $layout, $filter, $class, $sdate, $edate) {
        $response = '';

        $fields = Self::selectFields($config);
        if (File::exists(base_path() . '/packages/Powerpanel/VideoGallery/src/Models/VideoGallery.php') != null) {
            $fill = \Powerpanel\VideoGallery\Models\VideoGallery::getAllVideoGallery($fields, $limit, $sdate, $edate);

            if (!empty($fill)) {
                $data = [
                    'title' => $title,
                    'limit' => $limit,
                    'desc' => $desc,
                    'videogallery' => $fill,
                    'cols' => trim($layout),
                    'paginatehrml' => true,
                    'filter' => $filter,
                    'class' => $class,
                ];
                $response = view('video-gallery::frontview.builder-sections.video-gallery', compact('data'))->render();
            }
        } else {
            $response = '';
        }
        return $response;
    }

    static function WelComeHTML($title, $image, $content, $alignment, $src,$btnurl) {
        $response = '';
        $data = [
            'title' => $title,
            'btnurl' => $btnurl,
            'image' => $image,
            'content' => $content,
            'alignment' => $alignment,
            'src' => $src
        ];
        $response = view('visualcomposer::frontview.builder-sections.home.welcome', compact('data'))->render();
        return $response;
    }

    static function OnlyTitleHTML($content, $extclass) {
        $response = '';
        $data = [
            'title' => $content,
            'extclass' => $extclass
        ];
        $response = view('visualcomposer::frontview.builder-sections.cms', compact('data'))->render();

        return $response;
    }

    static function OnlyIframeHTML($content, $extclass) {
        $response = '';
        $data = [
            'iframe' => $content,
            'extclass' => $extclass
        ];
        $response = view('visualcomposer::frontview.builder-sections.iframe', compact('data'))->render();

        return $response;
    }

    static function TwoColumnsHTML($content, $type, $subtype, $partitionclass, $two, $count_two_part) {
        $response = '';

        if ($type == 'formdata') {
            $formdata = \App\CommonModel::getFormBuilderData($content['id']);
            if (isset($formdata->varFormDescription)) {
                $form_data_json = json_decode($formdata->varFormDescription, true);
                $data = [
                    'formid' => $content['id'],
                    'title' => $content,
                    'Description' => $formdata->Description,
                    'formtitle' => $formdata->FormTitle,
                    'formTotalDetails' => $formdata,
                    'formdata' => $form_data_json,
                    'type' => $type,
                    'subtype' => $subtype,
                    'Columns' => $two,
                    'Two_Part_Count_Row_One' => $count_two_part
                ];
            } else {
                $data = [
                    'formid' => $content['id'],
                    'title' => $content,
                    'Description' => '',
                    'formtitle' => '',
                    'formTotalDetails' => $formdata,
                    'formdata' => '',
                    'type' => $type,
                    'subtype' => $subtype,
                    'Columns' => $two,
                    'Two_Part_Count_Row_One' => $count_two_part
                ];
            }
        } else {
            $data = [
                'content' => $content,
                'type' => $type,
                'subtype' => $subtype,
                'partitionclass' => $partitionclass,
                'Columns' => $two,
                'Two_Part_Count_Row_One' => $count_two_part
            ];
        }
        $response = view('visualcomposer::frontview.builder-sections.twopart', compact('data'))->render();

        return $response;
    }

    static function OneThreeColumnsHTML($content, $type, $subtype, $partitionclass, $two) {
        $response = '';
        if ($type == 'formdata') {
            $formdata = \App\CommonModel::getFormBuilderData($content['id']);
            if (isset($formdata->varFormDescription)) {
                $form_data_json = json_decode($formdata->varFormDescription, true);
                $data = [
                    'formid' => $content['id'],
                    'title' => $content,
                    'Description' => $formdata->Description,
                    'formtitle' => $formdata->FormTitle,
                    'formTotalDetails' => $formdata,
                    'formdata' => $form_data_json,
                    'type' => $type,
                    'subtype' => $subtype,
                    'Columns' => $two
                ];
            } else {
                $data = [
                    'formid' => $content['id'],
                    'title' => $content,
                    'Description' => '',
                    'formtitle' => '',
                    'formTotalDetails' => $formdata,
                    'formdata' => '',
                    'type' => $type,
                    'subtype' => $subtype,
                    'Columns' => $two
                ];
            }
        } else {
            $data = [
                'content' => $content,
                'type' => $type,
                'subtype' => $subtype,
                'partitionclass' => $partitionclass,
                'Columns' => $two
            ];
        }
        $response = view('visualcomposer::frontview.builder-sections.oneThreepart', compact('data'))->render();

        return $response;
    }

    static function ThreeOneColumnsHTML($content, $type, $subtype, $partitionclass, $two) {
        $response = '';
        if ($type == 'formdata') {
            $formdata = \App\CommonModel::getFormBuilderData($content['id']);
            if (isset($formdata->varFormDescription)) {
                $form_data_json = json_decode($formdata->varFormDescription, true);
                $data = [
                    'formid' => $content['id'],
                    'title' => $content,
                    'Description' => $formdata->Description,
                    'formtitle' => $formdata->FormTitle,
                    'formTotalDetails' => $formdata,
                    'formdata' => $form_data_json,
                    'type' => $type,
                    'subtype' => $subtype,
                    'Columns' => $two
                ];
            } else {
                $data = [
                    'formid' => $content['id'],
                    'title' => $content,
                    'Description' => '',
                    'formtitle' => '',
                    'formTotalDetails' => $formdata,
                    'formdata' => '',
                    'type' => $type,
                    'subtype' => $subtype,
                    'Columns' => $two
                ];
            }
        } else {
            $data = [
                'content' => $content,
                'type' => $type,
                'subtype' => $subtype,
                'partitionclass' => $partitionclass,
                'Columns' => $two
            ];
        }
        $response = view('visualcomposer::frontview.builder-sections.threeOnepart', compact('data'))->render();

        return $response;
    }

    static function ThreeColumnsHTML($content, $type, $subtype, $partitionclass, $three) {
        $response = '';
        if ($type == 'formdata') {
            $formdata = \App\CommonModel::getFormBuilderData($content['id']);
            if (isset($formdata->varFormDescription)) {
                $form_data_json = json_decode($formdata->varFormDescription, true);
                $data = [
                    'formid' => $content['id'],
                    'title' => $content,
                    'Description' => $formdata->Description,
                    'formtitle' => $formdata->FormTitle,
                    'formTotalDetails' => $formdata,
                    'formdata' => $form_data_json,
                    'type' => $type,
                    'subtype' => $subtype,
                    'Columns' => $three
                ];
            } else {
                $data = [
                    'formid' => $content['id'],
                    'title' => $content,
                    'Description' => '',
                    'formtitle' => '',
                    'formTotalDetails' => $formdata,
                    'formdata' => '',
                    'type' => $type,
                    'subtype' => $subtype,
                    'Columns' => $three
                ];
            }
        } else {
            $data = [
                'content' => $content,
                'type' => $type,
                'subtype' => $subtype,
                'partitionclass' => $partitionclass,
                'Columns' => $three
            ];
        }
        $response = view('visualcomposer::frontview.builder-sections.threepart', compact('data'))->render();

        return $response;
    }

    static function FourColumnsHTML($content, $type, $subtype, $partitionclass, $four) {
        $response = '';
        if ($type == 'formdata') {
            $formdata = \App\CommonModel::getFormBuilderData($content['id']);
            if (isset($formdata->varFormDescription)) {
                $form_data_json = json_decode($formdata->varFormDescription, true);
                $data = [
                    'formid' => $content['id'],
                    'title' => $content,
                    'Description' => $formdata->Description,
                    'formtitle' => $formdata->FormTitle,
                    'formTotalDetails' => $formdata,
                    'formdata' => $form_data_json,
                    'type' => $type,
                    'subtype' => $subtype,
                    'Columns' => $four
                ];
            } else {
                $data = [
                    'formid' => $content['id'],
                    'title' => $content,
                    'Description' => '',
                    'formtitle' => '',
                    'formTotalDetails' => $formdata,
                    'formdata' => '',
                    'type' => $type,
                    'subtype' => $subtype,
                    'Columns' => $four
                ];
            }
        } else {
            $data = [
                'content' => $content,
                'type' => $type,
                'subtype' => $subtype,
                'partitionclass' => $partitionclass,
                'Columns' => $four
            ];
        }
        $response = view('visualcomposer::frontview.builder-sections.fourpart', compact('data'))->render();

        return $response;
    }

    static function OnlyFormBuilderHTML($formid, $content, $extclass) {
        $response = '';
        $formdata = \App\CommonModel::getFormBuilderData($formid);
        if (isset($formdata->varFormDescription)) {
            $form_data_json = json_decode($formdata->varFormDescription, true);
            $data = [
                'formid' => $formid,
                'title' => $content,
                'Description' => $formdata->Description,
                'formtitle' => $formdata->FormTitle,
                'formTotalDetails' => $formdata,
                'formdata' => $form_data_json,
                'extclass' => $extclass
            ];
            $response = view('visualcomposer::frontview.builder-sections.formbuilder', compact('data'))->render();
        } else {
            $data = [
                'formid' => $formid,
                'title' => $content,
                'Description' => '',
                'formtitle' => '',
                'formTotalDetails' => $formdata,
                'formdata' => '',
                'extclass' => $extclass
            ];
            $response = view('visualcomposer::frontview.builder-sections.formbuilder')->render();
        }
        return $response;
    }

    static function OnlyContentHTML($content) {
        $response = '';
        $data = [
            'content' => $content
        ];
        $response = view('visualcomposer::frontview.builder-sections.cms', compact('data'))->render();

        return $response;
    }

    static function TwoContentHTML($leftcontent, $rightcontent) {
        $response = '';
        $data = [
            'leftcontent' => $leftcontent,
            'rightcontent' => $rightcontent
        ];
        $response = view('visualcomposer::frontview.builder-sections.cms', compact('data'))->render();

        return $response;
    }

    static function ImageHTML($title, $img, $image, $alignment) {
        $response = '';
        $data = [
            'title' => $title,
            'img' => $img,
            'image' => $image,
            'alignment' => $alignment
        ];
        $response = view('visualcomposer::frontview.builder-sections.cms', compact('data'))->render();

        return $response;
    }

    static function DocumentHTML($document) {
        $response = '';
        $data = [
            'document' => $document
        ];
        $response = view('visualcomposer::frontview.builder-sections.cms', compact('data'))->render();

        return $response;
    }

    static function ContentHTML($title, $content, $alignment, $src, $image) {
        $response = '';
        $data = [
            'title' => $title,
            'content' => $content,
            'alignment' => $alignment,
            'src' => $src,
            'image' => $image
        ];
        $response = view('visualcomposer::frontview.builder-sections.cms', compact('data'))->render();

        return $response;
    }

    static function AboutBlockHTML($title, $content, $alignment, $src, $image, $btntitle, $btnurl, $tagline) {
        $response = '';
        $data = [
            'title' => $title,
            'content' => $content,
            'alignment' => $alignment,
            'src' => $src,
            'image' => $image,
            'btntitle' => $btntitle,
            'btnurl' => $btnurl,
            'tagline' => $tagline,
        ];
        $response = view('visualcomposer::frontview.builder-sections.home.about-block', compact('data'))->render();

        return $response;
    }

    static function VideoHTML($title, $videoType, $vidId) {
        $response = '';
        if ($videoType == 'YouTube') {
            $data = [
                'title' => $title,
                'videoType' => 'YouTube',
                'vidId' => $vidId
            ];
        } else {
            $data = [
                'title' => $title,
                'videoType' => 'Vimeo',
                'vidId' => $vidId
            ];
        }
        $response = view('visualcomposer::frontview.builder-sections.cms', compact('data'))->render();

        return $response;
    }

    static function VideoContentHTML($title, $videoType, $vidId, $content, $alignment) {
        $response = '';
        if ($videoType == 'YouTube') {
            $data = [
                'videotitle' => $title,
                'videoType' => 'YouTube',
                'vidId' => $vidId,
                'content' => $content,
                'videoalignment' => $alignment
            ];
        } else {
            $data = [
                'videotitle' => $title,
                'videoType' => 'Vimeo',
                'vidId' => $vidId,
                'content' => $content,
                'videoalignment' => $alignment
            ];
        }
        $response = view('visualcomposer::frontview.builder-sections.cms', compact('data'))->render();

        return $response;
    }

    static function MapHTML($latitude, $longitude) {
        $response = '';

        $data = [
            'latitude' => $latitude,
            'longitude' => $longitude
        ];
        $response = view('visualcomposer::frontview.builder-sections.cms', compact('data'))->render();

        return $response;
    }

    static function ConatctInfoHTML($content, $section_address, $section_email, $section_phone) {
        $response = '';

        $data = [
            'othercontent' => $content,
            'section_address' => $section_address,
            'section_email' => $section_email,
            'section_phone' => $section_phone
        ];
        $response = view('visualcomposer::frontview.builder-sections.cms', compact('data'))->render();

        return $response;
    }

    static function ButtonHTML($title, $content, $alignment, $target) {
        $response = '';

        $data = [
            'btntitle' => $title,
            'btncotent' => $content,
            'btnalignment' => $alignment,
            'target' => $target
        ];
        $response = view('visualcomposer::frontview.builder-sections.cms', compact('data'))->render();

        return $response;
    }

    static function SpacerHTML($config) {
        $response = '';
        if ($config == '9') {
            $cdata = 'ac-pt-xs-0';
        } elseif ($config == '10') {
            $cdata = 'ac-pt-xs-5';
        } elseif ($config == '11') {
            $cdata = 'ac-pt-xs-10';
        } elseif ($config == '12') {
            $cdata = 'ac-pt-xs-15';
        } elseif ($config == '13') {
            $cdata = 'ac-pt-xs-20';
        } elseif ($config == '14') {
            $cdata = 'ac-pt-xs-25';
        } elseif ($config == '15') {
            $cdata = 'ac-pt-xs-30';
        } elseif ($config == '16') {
            $cdata = 'ac-pt-xs-40';
        } elseif ($config == '17') {
            $cdata = 'ac-pt-xs-50';
        } else {
            $cdata = '';
        }
        $data = [
            'config' => $cdata
        ];
        $response = view('visualcomposer::frontview.builder-sections.spacer', compact('data'))->render();

        return $response;
    }

    static function selectFields($config) {
        switch ($config) {
            case 1:
                return ['moduleFields' => ['id', 'intAliasId', 'fkIntImgId', 'varTitle']];
                break;
            case 2:
                return ['moduleFields' => ['id', 'intAliasId', 'fkIntImgId', 'varTitle', 'varShortDescription',]];
                break;
            case 3:
                return ['moduleFields' => ['id', 'varTitle', 'dtDateTime', 'dtEndDateTime']];
                break;
            case 4:
                return ['moduleFields' => ['id', 'intAliasId', 'fkIntImgId', 'varTitle', 'dtDateTime', 'dtEndDateTime']];
                break;
            case 5:
                return ['moduleFields' => ['id', 'intAliasId', 'fkIntImgId', 'varTitle', 'varShortDescription', 'dtDateTime', 'dtEndDateTime']];
                break;
            case 6:
                return ['moduleFields' => ['id', 'intAliasId', 'varTitle', 'varShortDescription']];
                break;
            case 7:
                return ['moduleFields' => ['id', 'intAliasId', 'varTitle', 'dtDateTime', 'dtEndDateTime']];
                break;
            case 8:
                return ['moduleFields' => ['id', 'intAliasId', 'varTitle', 'varShortDescription', 'dtDateTime', 'dtEndDateTime']];
                break;
            default:
                return ['*'];
                break;
        }
    }

}
