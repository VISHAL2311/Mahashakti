<?php
 $connect = mysqli_connect("localhost", "avalonma_user", 'S7739uL2UB6s', "avalonma_beta");
 //host , usernm,pass,dbname
define('DB_PREFIX', 'nq_');
define('SITE_PATH', 'https://beta.avalonmarine.ky/');
$xml_string="";
header("Content-Type: application/xml; charset=utf-8");
$xml_string .= '<?xml version="1.0" encoding="UTF-8"?>';
$xml_string .= '
<?xml-stylesheet type="text/xsl" href="sitemap.xsl" ?>';
$xml_string .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9
            http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd">';
    //=========== HomePage ================
    $xml_string .= '<url>
        <loc>' . SITE_PATH . '</loc>
    </url>';
    //=========== Get Pages ================
    $query = "select CMS.*,A.varAlias as alias from " . DB_PREFIX . "cms_page as CMS left join " . DB_PREFIX . "alias as A on A.id=CMS.intAliasId and CMS.chrPublish='Y'and CMS.chrApproved='N' and CMS.id!='29' and CMS.id!='1' and CMS.id!='15' and CMS.id!='11' and CMS.id!='15'";
    $result = mysqli_query($connect, $query);
    while ($row = mysqli_fetch_array($result)) {
    if($row["alias"] !=''){
    $xml_string .= '<url>';
        $xml_string .= '<loc>' . SITE_PATH . $row["alias"] . '</loc>';
        $xml_string .= '</url>';
    }
    }
    //=========== Get Services ================
    $query = "select S.*,A.varAlias as alias from " . DB_PREFIX . "services as S left join " . DB_PREFIX . "alias as A on A.id=S.intAliasId and S.chrPublish='Y'and S.chrDelete='N'";
    $result = mysqli_query($connect, $query);
    while ($row = mysqli_fetch_array($result)) {
    if($row["alias"] !=''){
    $xml_string .= '<url>';
        $xml_string .= '<loc>' . SITE_PATH .'services/'.$row["alias"] . '</loc>';
        $xml_string .= '</url>';
    }
    }
    //=========== Get Boat ================
    $query = "select W.*,A.varAlias as alias from " . DB_PREFIX . "boat as W left join " . DB_PREFIX . "alias as A on A.id=W.intAliasId and W.chrPublish='Y'and W.chrDelete='N'";
    $result = mysqli_query($connect, $query);
    while ($row = mysqli_fetch_array($result)) {
    if($row["alias"] !=''){
    $xml_string .= '<url>';
        $xml_string .= '<loc>' . SITE_PATH .'boat/'.$row["alias"] . '</loc>';
        $xml_string .= '</url>';
    }
    }
    //=========== Get Blogs ================
    // $query = "select B.*,A.varAlias as alias from " . DB_PREFIX . "blogs as B left join " . DB_PREFIX . "alias as A on A.id=B.intAliasId and B.chrPublish='Y'and B.chrDelete='N'";
    // $result = mysqli_query($connect, $query);
    // while ($row = mysqli_fetch_array($result)) {
    // if($row["alias"] !=''){
    // $xml_string .= '<url>';
    //     $xml_string .= '<loc>' . SITE_PATH .'blog/'.$row["alias"] . '</loc>';
    //     $xml_string .= '</url>';
    // }
    // }
    // $xml_string .= '<url>';
        // $xml_string .= '<loc>' . SITE_PATH .'site-map/'.$row["alias"] . '</loc>';
        // $xml_string .= '</url>';
    $xml_string .= '</urlset>';
echo $xml_string;
?>