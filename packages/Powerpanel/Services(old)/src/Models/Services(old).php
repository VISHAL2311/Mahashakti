<?php

namespace Powerpanel\Services\Models;

use Cache;
use Request;
use Illuminate\Database\Eloquent\Model;
use DB;

class Services extends Model {

    protected $table = 'services';
    protected $fillable = [
        'id',
        'intAliasId',
        'fkIntImgId',
        'fkIntVideoId',
        'varTitle',
        'varExternalLink',
        'varFontAwesomeIcon',
        'txtShortDescription',
        'txtDescription',
        'txtCategories',
        'varPreferences',
        'intDisplayOrder',
        'chrFeaturedService',
        'chrPublish',
        'chrDelete',
        'varMetaTitle',
        'varMetaKeyword',
        'varMetaDescription',
        'fkMainRecord',
        'chrApproved',
        'intApprovedBy',
        'chrRollBack',
        'intFkCategory',
        'chrIsPreview',
        'dtApprovedDateTime	',
        'chrLetest',
        'chrMain',
        'chrPageActive',
        'dtDateTime',
        'created_at',
        'updated_at'
    ];

    /**
     * This method handels retrival of front service list from power composer
     * @return  Object
     * @since   2020-02-04
     * @author  NetQuick
     */
    public static function getServiceList($fields, $recIds, $limit) {
        $response = false;
        $moduleFields = [
            'id',
            'intAliasId',
            'fkIntImgId',
            'fkIntVideoId',
            'varTitle',
            'varExternalLink',
            'varFontAwesomeIcon',
            'txtShortDescription',
            'txtDescription',
            'txtCategories',
            'varPreferences',
            'intDisplayOrder',
            'chrFeaturedService',
            'chrPublish',
            'chrDelete',
            'varMetaTitle',
            'varMetaKeyword',
            'varMetaDescription',
            'created_at',
            'updated_at'
        ];
        $aliasFields = ['id', 'varAlias'];
        //$response = Cache::tags(['Services'])->get('getServiceList_' . implode('-', $recIds));
        //if (empty($response)) {
        $response = Self::getFrontRecords($moduleFields, $aliasFields)
                ->whereIn('id', $recIds)
                ->deleted()
                ->publish()
                ->limit($limit)
                ->orderByRaw(DB::raw("FIELD(id, " . implode(',', $recIds) . " )"));
        $response = $response->get();

        //Cache::tags(['Services'])->forever('getServiceList_' . implode('-', $recIds), $response);
        //}
        return $response;
    }

    /**
     * This method handels retrival of last month service
     * @return  Object
     * @since   2020-02-04
     * @author  NetQuick
     */
    public static function getTemplateServiceList($fields, $filterArr = false) {
        $response = false;
        $moduleFields = [
            'id',
            'intAliasId',
            'fkIntImgId',
            'fkIntVideoId',
            'varTitle',
            'varExternalLink',
            'varFontAwesomeIcon',
            'txtShortDescription',
            'txtDescription',
            'txtCategories',
            'varPreferences',
            'intDisplayOrder',
            'chrFeaturedService',
            'chrPublish',
            'chrDelete',
            'varMetaTitle',
            'varMetaKeyword',
            'varMetaDescription',
            'created_at',
            'updated_at'
        ];
        $aliasFields = ['id', 'varAlias'];

        $query = Self::getFrontRecords($moduleFields, $aliasFields)
                ->deleted()
                ->publish()
                ->filter($filterArr);

        if (Request::segment(1) != '') {
            $response = $query->paginate(6);
        } else {
            $response = $query->get();
        }

        return $response;
    }

    public static function getBuilderRecordList($filterArr = []) {
        $response = false;
        $moduleFields = [
            'id',
            'intAliasId',
            'fkIntImgId',
            'varTitle',
            'txtCategories',
            'chrPublish',
            'updated_at'
        ];

        $response = Self::getPowerPanelRecords($moduleFields, false, false, false, false)
                ->deleted()
                ->publish()
                ->filter($filterArr);
                // $response = $response->leftJoin('page_hits', 'services.intAliasId', '=', 'page_hits.fkIntAliasId')
                // ->where('services.chrPublish', 'Y')
                // ->where('services.chrDelete', 'N')
                // ->where('services.chrMain', 'Y')
                // ->where('services.chrIsPreview', 'N')
                // ->groupBy('services.id')
                // ->get();
        $response = $response->groupBy('id')->get();

        return $response;
    }

    public static function getBuilderService($fields, $recIds)
    {
        $response = false;
        $moduleFields = ['id',
            'intAliasId',
            'varTitle',
            'intFkCategory',
            'txtDescription',
            'varShortDescription',
            'varMetaTitle',
            'varMetaDescription',
            'chrPublish',
            'chrPageActive',
            'created_at',
            'updated_at'];
        array_push($moduleFields, 'fkIntImgId');
        $aliasFields = ['id', 'varAlias'];
        $response = Cache::tags(['Services'])->get('getBuilderService_' . implode('-', $recIds));
        if (empty($response)) {
            $response = Self::getFrontRecords($moduleFields, $aliasFields)
                ->whereIn('id', $recIds)
                ->where('chrMain', 'Y')
                ->where('chrIsPreview', 'N')
                ->deleted()
                ->publish()
                ->orderByRaw(DB::raw("FIELD(id, " . implode(',', $recIds) . " )"));
            $response = $response->get();
            Cache::tags(['Services'])->forever('getBuilderService_' . implode('-', $recIds), $response);
        }
        return $response;
    }

    /**
     * This method handels retrival of front service detail
     * @return  Object
     * @since   2017-10-13
     * @author  NetQuick
     */
    public static function getRecordIdByAliasID($aliasID) {
        $response = false;
        $response = Cache::tags(['Services'])->get('getServiceRecordIdByAliasID_' . $aliasID);
        if (empty($response)) {
            $response = Self::Select('id')->deleted()->publish()->checkAliasId($aliasID)->first();
            Cache::tags(['Services'])->forever('getServiceRecordIdByAliasID_' . $aliasID, $response);
        }
        return $response;
    }

    /**
     * This method handels retrival of front service list
     * @return  Object
     * @since   2017-10-14
     * @author  NetQuick
     */
    public static function getListByCategory($categoryId, $paginate = 6, $page = false) {
        $response = false;
        $moduleFields = ['varTitle', 'fkIntImgId', 'intAliasId', 'txtShortDescription', 'txtDescription'];
        $aliasFields = ['id', 'varAlias'];
        $response = Cache::tags(['Services'])->get('getFrontServicesList_' . $page);
        if (empty($response)) {
            $response = Self::getFrontRecords($moduleFields, $aliasFields)
                    ->deleted()
                    ->checkCategoryId($categoryId)
                    ->orderBy('intDisplayOrder', 'ASC')
                    ->publish()
                    ->paginate($paginate);

            Cache::tags(['Services'])->forever('getFrontServicesList_' . $page, $response);
        }
        return $response;
    }

    /**
     * This method handels retrival of front service list
     * @return  Object
     * @since   2017-10-14
     * @author  NetQuick
     */
    public static function getFrontList($filterArr = false, $page = false,$catid = false, $print = false, $categoryid, $name = "") {
        $response = false;
        $moduleFields = ['varTitle', 'fkIntImgId','chrPageActive','intFkCategory', 'intAliasId', 'txtShortDescription', 'varFontAwesomeIcon'];
        $aliasFields = ['id', 'varAlias'];
        $response = Cache::tags(['Services'])->get('getFrontServicesList_' . $page);
        if (empty($response)) {
            $response = Self::getFrontRecords($moduleFields, $aliasFields)
                ->deleted()
                ->publish();
            if ($categoryid != '') {
                $response = $response->whereRaw(DB::raw('intFkCategory="' . $categoryid . '"'));
            }
            if ($name != '') {
                $response = $response->where('varTitle', 'like', '%' . '' . $name . '' . '%');
            }
            // $response = $response->where('chrTrash', '!=', 'Y')
            //     ->where('chrDraft', '!=', 'D')
            //     ->orderBy('dtDateTime', 'DESC')
            //     ->where('chrIsPreview', 'N')
            //     ->where('chrMain', 'Y');
            if ($catid != false) {
                $response = $response->where('intFkCategory', '=', $catid);
            }
            Cache::tags(['Services'])->forever('getFrontServicesList_' . $page, $response);
        }
        return $response;
    }

    /**
     * This method handels retrival of front latest service list
     * @return  Object
     * @since   2017-10-13
     * @author  NetQuick
     */
    public static function getLatestList($id = false) {
        $response = false;
        $moduleFields = ['varTitle', 'fkIntImgId', 'intAliasId', 'created_at','chrPageActive',];
        $aliasFields = ['id', 'varAlias'];
        $response = Cache::tags(['Services'])->get('getFrontLatestServicesList_' . $id);
        if (empty($response)) {
            $response = Self::getFrontRecords($moduleFields, $aliasFields)
                    ->deleted()
                    ->publish()
                    ->latestRecord($id)
                    ->take(5)
                    ->get();

            Cache::tags(['Services'])->forever('getFrontLatestServicesList_' . $id, $response);
        }
        return $response;
    }

    /**
     * This method handels retrival of front latest service list
     * @return  Object
     * @since   2017-10-13
     * @author  NetQuick
     */
    public static function getSimilarRecordList($id = false) {
        $response = false;
        $moduleFields = ['varTitle', 'fkIntImgId', 'intAliasId', 'created_at'];
        $aliasFields = ['id', 'varAlias'];
        $response = Cache::tags(['Services'])->get('getSimilarRecordList_' . $id);
        if (empty($response)) {
            $response = Self::getFrontRecords($moduleFields, $aliasFields)
                    ->deleted()
                    ->publish()
                    ->latestRecord($id)
                    ->take(5)
                    ->get();

            Cache::tags(['Services'])->forever('getSimilarRecordList_' . $id, $response);
        }
        return $response;
    }

    /**
     * This method handels retrival of front latest service list
     * @return  Object
     * @since   2017-10-13
     * @author  NetQuick
     */
    public static function getFeaturedList($limit = 5) {
        $response = false;
        $moduleFields = ['varTitle', 'fkIntImgId', 'varFontAwesomeIcon', 'txtShortDescription', 'intAliasId', 'created_at'];
        $aliasFields = ['id', 'varAlias'];
        $response = Cache::tags(['Services'])->get('getServiceFeaturedList');
        if (empty($response)) {
            $response = Self::getFrontRecords($moduleFields, $aliasFields)
                    ->publish()
                    ->deleted()
                    ->featured('Y')
                    ->displayOrderBy('ASC')
                    ->take($limit)
                    ->get();
            Cache::tags(['Services'])->forever('getServiceFeaturedList', $response);
        }
        return $response;
    }

    public static function getFrontServiceDropdown() {
        $response = false;
        $moduleFields = ['id', 'varTitle'];
        $response = Cache::tags(['Services'])->get('getFrontServiceDropdown');
        if (empty($response)) {
            $response = Self::getFrontRecords($moduleFields)
                    ->publish()
                    ->deleted()
                    ->displayOrderBy('ASC')
                    ->get();

            Cache::tags(['Services'])->forever('getFrontServiceDropdown', $response);
        }
        return $response;
    }

    /**
     * This method handels retrival of front service detail
     * @return  Object
     * @since   2017-10-13
     * @author  NetQuick
     */
    public static function getFrontDetail($id) {
        $response = false;
        $moduleFields = ['id', 'intAliasId', 'fkIntImgId', 'fkIntVideoId','intFKCategory', 'varTitle', 'varExternalLink', 'varFontAwesomeIcon', 'txtShortDescription', 'txtDescription', 'txtCategories', 'varPreferences', 'intDisplayOrder', 'chrFeaturedService', 'chrPageActive','chrPublish', 'chrDelete', 'varMetaTitle', 'varMetaKeyword', 'varMetaDescription'];
        $aliasFields = ['id', 'varAlias'];
        $response = Cache::tags(['Services'])->get('getFrontServiceDetail_' . $id);
        if (empty($response)) {
            $response = Self::getFrontRecords($moduleFields, $aliasFields)
                    ->deleted()
                    ->publish()
                    ->checkAliasId($id)
                    ->first();
            Cache::tags(['Services'])->forever('getFrontServiceDetail_' . $id, $response);
        }
        return $response;
    }

    /**
     * This method handels retrival of record count based on category
     * @return  Object
     * @since   2017-10-16
     * @author  NetQuick
     */
    public static function getCountById($categoryId = null) {
        $response = false;
        $moduleFields = ['id'];
        $response = Self::getPowerPanelRecords($moduleFields)
                ->checkCategoryId($categoryId)
                ->where('chrMain', 'Y')
                ->where('chrIsPreview', 'N')
                ->deleted()
                ->count();
        return $response;
    }

    /**
     * This method handels retrival of record count
     * @return  Object
     * @since   2017-10-16
     * @author  NetQuick
     */
    protected static $fetchedID = [];
    protected static $fetchedObj = null;

    public static function getRecordById($id = false) {
        $response = false;
        $moduleFields = [
            'id',
            'intAliasId',
            'varTitle',
            'fkIntImgId',
            'fkIntVideoId',
            'txtCategories',
            'varExternalLink',
            'varFontAwesomeIcon',
            'chrFeaturedService',
            'intDisplayOrder',
            'txtShortDescription',
            'txtDescription',
            'UserID',
            'chrPageActive',
            'fkMainRecord',
            'intFKCategory',
            'varMetaTitle',
            'varMetaKeyword',
            'varMetaDescription',
            'chrPublish'];
        $aliasFields = ['id', 'varAlias'];
        $videoFields = ['id', 'varVideoName', 'varVideoExtension', 'youtubeId'];
        if (!in_array($id, Self::$fetchedID)) {
            array_push(Self::$fetchedID, $id);
            Self::$fetchedObj = Self::getPowerPanelRecords($moduleFields, $aliasFields, $videoFields)
                    ->deleted()
                    ->checkRecordId($id)
                    ->first();
        }
        $response = Self::$fetchedObj;
        return $response;
    }

    /**
     * This method handels retrival of record count
     * @return  Object
     * @since   2017-10-16
     * @author  NetQuick
     */
    protected static $fetchedOrder = [];
    protected static $fetchedOrderObj = null;

    public static function getRecordByOrder($order = false) {
        $response = false;
        $moduleFields = [
            'id',
            'intDisplayOrder',
        ];
        if (!in_array($order, Self::$fetchedOrder)) {
            array_push(Self::$fetchedOrder, $order);
            Self::$fetchedOrderObj = Self::getPowerPanelRecords($moduleFields)
                    ->deleted()
                    ->orderCheck($order)
                    ->first();
        }
        $response = Self::$fetchedOrderObj;
        return $response;
    }

    /**
     * This method handels retrival of record count
     * @return  Object
     * @since   2017-10-16
     * @author  NetQuick
     */
    public static function getRecordList($filterArr = false, $isAdmin = false) {
        $response = false;
        $userid = auth()->user()->id;
        $moduleFields = ['id', 'intAliasId', 'varTitle','intFKCategory','chrPageActive', 'varExternalLink', 'varFontAwesomeIcon', 'txtShortDescription', 'fkIntImgId', 'txtCategories', 'intDisplayOrder', 'txtDescription', 'chrFeaturedService', 'chrPublish'];
        $aliasFields = ['id', 'varAlias'];
        $response = Self::getPowerPanelRecords($moduleFields, $aliasFields)
                ->deleted()
                ->filter($filterArr)
                ->where(function ($query) use ($userid) {
                    $query->where("UserID", '=', $userid)->where('chrPageActive', '=', 'PR')
                        ->orWhere('chrPageActive', '!=', 'PR');
                })
                ->get();
        return $response;
    }

    #Config and filters====================================================
    /**
     * This method handels retrival of event records
     * @return  Object
     * @since   2016-07-14
     * @author  NetQuick
     */

    public static function getRecords() {
        $response = false;
        $response = Cache::tags(['Services'])->get('getServicesRecords');
        if (empty($response)) {
            $response = Self::Select(['id', 'intFKCategory', 'varTitle', 'fkIntImgId', 'txtDescription', 'chrPublish'])
                ->deleted()
                ->publish()
                ->paginate(10);
            Cache::tags(['Services'])->forever('getServicesRecords', $response);
        }
        // $response = self::with(['alias' => function ($query) {
        //                 $query->checkModuleCode();
        //             }, 'image']);
        return $response;
    }

    public static function getFrontRecords($moduleFields = false, $aliasFields = false) {
        $response = false;
        $data = [];
        if ($aliasFields != false) {
            $data = [
                'alias' => function ($query) use ($aliasFields) {
                    $query->select($aliasFields);
                },
            ];
        }
        $response = self::select($moduleFields)->with($data);
        return $response;
    }

    /**
     * This method handels retrival of service records
     * @return  Object
     * @since   2016-07-14
     * @author  NetQuick
     */
    public static function getPowerPanelRecords($moduleFields = false, $aliasFields = false, $videoFields = false, $imageFields = false, $categoryFields = false) {
        $data = [];
        $response = false;
        $response = self::select($moduleFields);
        if ($imageFields != false) {
            $data['image'] = function ($query) use ($imageFields) {
                $query->select($imageFields);
            };
        }
        if ($aliasFields != false) {
            $data['alias'] = function ($query) use ($aliasFields) {
                $query->select($aliasFields)->checkModuleCode();
            };
        }
        if ($videoFields != false) {
            $data['video'] = function ($query) use ($videoFields) {
                $query->select($videoFields)->publish();
            };
        }
        if ($categoryFields != false) {
            $data['serviceCategory'] = function ($query) use ($categoryFields) {
                $query->select($categoryFields);
            };
        }
        if (count($data) > 0) {
            $response = $response->with($data);
        }
        return $response;
    }

    public static function getServicesNameByServicesId($ids) {
        $response = false;
        $serviceFields = ['varTitle'];
        $response = Self::getPowerPanelRecords($serviceFields)->deleted()->whereIn('id', $ids)->get();
        return $response;
    }

    /**
     * This method handels retrival of record by id for Log Manage
     * @return  Object
     * @since   2017-10-16
     * @author  NetQuick
     */
    public static function getRecordForLogById($id) {
        $response = false;
        $moduleFields = ['id', 'varTitle', 'fkIntImgId', 'txtCategories', 'varExternalLink','UserID','intFKCategory','fkMainRecord',
        'chrPageActive', 'varFontAwesomeIcon', 'chrFeaturedService', 'intDisplayOrder', 'txtShortDescription', 'txtDescription', 'varMetaTitle', 'varMetaKeyword', 'varMetaDescription', 'chrPublish'];
        $response = Self::getPowerPanelRecords($moduleFields)->deleted()->checkRecordId($id)->first();
        return $response;
    }

    /**
     * This method handels alias relation
     * @return  Object
     * @since   2016-07-14
     * @author  NetQuick
     */
    public function alias() {
        $response = false;
        $response = $this->belongsTo('App\Alias', 'intAliasId', 'id');
        return $response;
    }

    /**
     * This method handels image relation
     * @return  Object
     * @since   2016-07-14
     * @author  NetQuick
     */
    public function image() {
        $response = false;
        $response = $this->belongsTo('App\Image', 'fkIntImgId', 'id');
        return $response;
    }

    /**
     * This method handels video relation
     * @return  Object
     * @since   2016-07-14
     * @author  NetQuick
     */
    public function video() {
        return $this->belongsTo('App\Video', 'fkIntVideoId', 'id');
    }

    /**
     * This method handels news category relation
     * @return  Object
     * @since   2016-07-14
     * @author  NetQuick
     */
    public function serviceCategory() {
        $response = false;
        $response = $this->belongsTo('Powerpanel\ServicesCategory\Models\ServiceCategory', 'intCategoryId', 'id');
        return $response;
    }

    /**
     * This method handels alias id scope
     * @return  Object
     * @since   2016-07-24
     * @author  NetQuick
     */
    public function scopeCheckAliasId($query, $id) {
        $response = false;
        $response = $query->where('intAliasId', $id);
        return $response;
    }

    public function scopeCheckVideoId($query, $id) {
        $response = false;
        $response = $query->whereIn('id', $id);
        return $response;
    }

    /**
     * This method handels record id scope
     * @return  Object
     * @since   2016-07-24
     * @author  NetQuick
     */
    public function scopeCheckRecordId($query, $id) {
        $response = false;
        $response = $query->where('id', $id);
        return $response;
    }

    /**
     * This method handels category id scope
     * @return  Object
     * @since   2016-07-14
     * @author  NetQuick
     */
    public function scopeCheckCategoryId($query, $id) {
        $response = false;
        $response = $query->where('txtCategories', 'like', '%' . serialize((string) $id) . '%')->orWhere('txtCategories', 'like', '%' . serialize($id) . '%');
        //$response = $query->where('txtCategories', 'like', '%'. $id .'%');
        return $response;
    }

    /**
     * This method handels order scope
     * @return  Object
     * @since   2016-07-14
     * @author  NetQuick
     */
    public function scopeOrderCheck($query, $order) {
        $response = false;
        $response = $query->where('intDisplayOrder', $order);
        return $response;
    }

    /**
     * This method handels publish scope
     * @return  Object
     * @since   2016-07-14
     * @author  NetQuick
     */
    public function scopePublish($query) {
        $response = false;
        $response = $query->where(['chrPublish' => 'Y']);
        return $response;
    }

    /**
     * This method handels delete scope
     * @return  Object
     * @since   2016-07-14
     * @author  NetQuick
     */
    public function scopeDeleted($query) {
        $response = false;
        $response = $query->where(['chrDelete' => 'N']);
        return $response;
    }

    /**
     * This method handels featured scope
     * @return  Object
     * @since   2016-08-08
     * @author  NetQuick
     */
    public function scopeFeatured($query, $flag = null) {
        $response = false;
        $response = $query->where(['chrFeaturedService' => $flag]);
        return $response;
    }

    /**
     * This method handels orderBy scope
     * @return  Object
     * @since   2016-08-08
     * @author  NetQuick
     */
    public function scopeDisplayOrderBy($query, $orderBy) {
        $response = false;
        $response = $query->orderBy('intDisplayOrder', $orderBy);
        return $response;
    }

    /**
     * This method handels Popular Service scope
     * @return  Object
     * @since   2016-08-30
     * @author  NetQuick
     */
    public function scopeLatestRecord($query, $id = false) {
        $response = false;
        $response = $query->groupBy('id')->orderBy('intDisplayOrder', 'ASC');
        if ($id > 0) {
            $response = $response->where('id', '!=', $id);
        }
        //->whereRaw('created_at > DATE_SUB(NOW(), INTERVAL 7 DAY)')
        //->whereRaw('created_at <= NOW()')
        return $response;
    }

    /**
     * This method handels filter scope
     * @return  Object
     * @since   2016-07-14
     * @author  NetQuick
     */
    public function scopeFilter($query, $filterArr = false, $retunTotalRecords = false) {

        $response = false;
        if (!empty($filterArr['orderByFieldName']) && !empty($filterArr['orderTypeAscOrDesc'])) {
            $query = $query->orderBy($filterArr['orderByFieldName'], $filterArr['orderTypeAscOrDesc']);
        } else {
            $query = $query->orderBy('varTitle', 'ASC');
        }
        if (!$retunTotalRecords) {
            if (!empty($filterArr['iDisplayLength']) && $filterArr['iDisplayLength'] > 0) {
                $data = $query->skip($filterArr['iDisplayStart'])->take($filterArr['iDisplayLength']);
            }
        }
        if (!empty($filterArr['statusFilter']) && $filterArr['statusFilter'] != ' ') {
            $data = $query->where('chrPublish', $filterArr['statusFilter']);
        }
        if (!empty($filterArr['catFilter']) && $filterArr['catFilter'] != ' ') {
            $data = $query->where('txtCategories', 'like', '%' . '"' . $filterArr['catFilter'] . '"' . '%');
        }
        if (!empty($filterArr['searchFilter']) && $filterArr['searchFilter'] != ' ') {
            $data = $query->where('varTitle', 'like', "%" . $filterArr['searchFilter'] . "%");
        }

        if (isset($filterArr['ignore']) && !empty($filterArr['ignore'])) {
            $data = $query->whereNotIn('id', $filterArr['ignore']);
        }

        if (isset($filterArr['template']) && $filterArr['template'] == 'featured-services') {
            $query->where('chrFeaturedService', '=', 'Y')->orderBy('intDisplayOrder', 'DESC');
        }


        if (!empty($query)) {
            $response = $query;
        }
        return $response;
    }

    /**
     * This method handels front search scope
     * @return  Object
     * @since   2016-08-09
     * @author  NetQuick
     */
    public function scopeFrontSearch($query, $term = '') {
        $response = false;
        $response = $query->where(['varTitle', 'like', '%' . $term . '%']);
        return $response;
    }

    public static function getRecordCountforList($filterArr = false, $returnCounter = false, $isAdmin = false)
    {
        $response = 0;
        $cmsPageFields = ['id'];
        $userid = auth()->user()->id;
        $response = Self::getPowerPanelRecords($cmsPageFields);
        if ($filterArr != false) {
            $response = $response->filter($filterArr, $returnCounter);
        }
        $response = $response->deleted()
            ->where(function ($query) use ($userid) {
                $query->where("UserID", '=', $userid)->where('chrPageActive', '=', 'PR')
                    ->orWhere('chrPageActive', '!=', 'PR');
            })
            ->checkMainRecord('Y')
            ->where('chrIsPreview', 'N')
            ->count();
        return $response;
    }

    public static function getRecordCountListApprovalTab($filterArr = false)
    {
        $response = false;
        $MainIDs = Self::distinct()
            ->select("fkMainRecord")
            ->where('fkMainRecord', '!=', '0')
            ->where('chrIsPreview', 'N')
            ->groupBy('fkMainRecord')
            ->get()
            ->toArray();
        $MainIDs = array_column($MainIDs, 'fkMainRecord');
        $moduleFields = ['id'];
        $response = Self::getPowerPanelRecords($moduleFields)
            ->deleted()
            ->filter($filterArr)
            ->whereIn('id', $MainIDs)
            ->count();
        return $response;
    }

    public static function getOrderOfApproval($id)
    {
        $result = Self::select('intDisplayOrder')
            ->checkRecordId($id)
            ->first();
        return $result;
    }

    public static function getPreviousRecordByMainId($id) {
        $response = Self::select('id','fkMainRecord')
                        ->deleted()  
                        ->publish()
                        ->where('fkMainRecord', $id)
                        ->where('chrMain', 'N')
                        ->where('chrApproved', 'N')
                        ->orderBy('dtApprovedDateTime','DESC')
                        ->first();
        return $response;

    }

    public static function approved_data_Listing($request)
    {
        $id = $request->id;
        $main_id = $request->main_id;

        //$PUserid = $request->PUserid;
        //Select Child Record Data Start
        $servicesCatfileds = ['id', 'varTitle'];
        $response = false;
        $moduleFields = [
            'id',
            'intAliasId',
            'intFKCategory',
            'varTitle',
            'fkIntImgId',
            'txtDescription',
            'txtShortDescription',
            'dtDateTime',
            'chrPublish',
            'chrPageActive',
            'created_at',
            'updated_at',
        ];
        $aliasFields = ['id', 'varAlias'];
        $response = Self::getPowerPanelRecords($moduleFields, false, $servicesCatfileds)
            ->deleted()
            ->where('chrMain', 'N')
            ->where('chrIsPreview', 'N')    
            ->where('id', $id)
            ->orderBy('created_at', 'desc')
            ->first();
        //Select Child Record Data END
        //Update Copy Child Record To Main Record start
        $whereConditions = ['id' => $main_id];
        $updateMainRecord = [
            'varTitle' => $response['varTitle'],
            'intFKCategory' => $response['intFKCategory'],
            'varTitle' => $response['varTitle'],
            'fkIntImgId' => $response['fkIntImgId'],
            'txtDescription' => $response['txtDescription'],
            'chrAddStar' => 'N',
            'dtDateTime' => $response['dtDateTime'],
            'chrPageActive' => $response['chrPageActive'],
            'chrPublish' => $response['chrPublish'],
        ];
        CommonModel::updateRecords($whereConditions, $updateMainRecord, false, 'Powerpanel\Services\Models\Services');
        //Update Copy Child Record To Main Record end
        $whereConditions_ApproveN = ['fkMainRecord' => $main_id];
        $updateToApproveN = [
            'chrApproved' => 'N',
            'chrLetest' => 'N',
            'intApprovedBy' => '0',
        ];
        CommonModel::updateRecords($whereConditions_ApproveN, $updateToApproveN, false, 'Powerpanel\Services\Models\Services');
        $whereConditionsApprove = ['id' => $id, 'chrMain' => 'N'];
        $updateToApprove = [
            'chrApproved' => 'Y',
            'chrRollBack' => 'Y',
            'intApprovedBy' => auth()->user()->id,
            'dtApprovedDateTime' => date('Y-m-d H:i:s')
        ];
        CommonModel::updateRecords($whereConditionsApprove, $updateToApprove, false, 'Powerpanel\Services\Models\Services');
        $msg_show = "Record successfully approved.";
        return $msg_show;
    }

    public static function getChildrollbackGrid()
    {
        $servicesCatfileds = ['id', 'varTitle'];
        $id = $_REQUEST['id'];
        $response = false;
        $moduleFields = [
            'id',
            'intFKCategory',
            'varTitle',
            'txtDescription',
            'txtShortDescription',
            'chrPublish',
            'dtDateTime',
            'fkMainRecord',
            'created_at',
            'chrApproved',
            'updated_at',
            'intApprovedBy',
            'UserID',
            'chrPageActive',
            'created_at',
            'updated_at',
        ];
        $response = Self::getPowerPanelRecords($moduleFields, false, $servicesCatfileds)
            ->deleted()
            ->where('chrMain', 'N')
            ->where('chrRollBack', 'Y')
            ->where('fkMainRecord', $id)
            ->where('chrIsPreview', 'N')
            ->orderBy('created_at', 'desc')
            ->get();
        return $response;
    }

    public static function getChildGrid()
    {
        $servicesCatfileds = ['id', 'varTitle'];
        $id = $_REQUEST['id'];
        $response = false;
        $moduleFields = [
            'id',
            'intFKCategory',
            'varTitle',
            'txtDescription',
            'txtShortDescription',
            'dtDateTime',
            'chrPublish',
            'fkMainRecord',
            'created_at',
            'chrApproved',
            'updated_at',
            'intApprovedBy',
            'UserID',
            'chrPageActive',
            'dtApprovedDateTime',
            'created_at',
            'updated_at',
        ];
        $response = Self::getPowerPanelRecords($moduleFields, false, $servicesCatfileds)
            ->deleted()
            ->where('chrMain', 'N')
            ->where('fkMainRecord', $id)
            ->where('chrIsPreview', 'N')
            ->orderBy('created_at', 'desc')
            ->get();
        return $response;
    }

    public static function getRecordCount_letest($Main_id, $id)
    {
        $moduleFields = ['chrLetest'];
        $response = Self::getPowerPanelRecords($moduleFields)
            ->deleted()
            ->where('chrMain', 'N')
            ->where('fkMainRecord', $Main_id)
            ->where('chrLetest', 'Y')
            ->where('id', '!=', $id)
            ->where('chrApproved', 'N')
            ->where('chrIsPreview', 'N')
            ->count();
        return $response;
    }

    public static function getRecordCount($filterArr = false, $returnCounter = false)
    {
        $response = 0;
        $cmsPageFields = ['id'];
        $pageQuery = Self::getPowerPanelRecords($cmsPageFields);
        if ($filterArr != false) {
            $pageQuery = $pageQuery->filter($filterArr, $returnCounter);
        }
        $response = $pageQuery
            ->deleted()
            ->where('chrMain', 'Y')
            ->where('chrIsPreview', 'N')
            ->count();
        return $response;
    }

    public static function getNewRecordsCount()
    {
        $response = false;
        $MainIDs = Self::distinct()
            ->select("fkMainRecord")
            ->where('fkMainRecord', '!=', '0')
            ->where('chrIsPreview', 'N')
            ->groupBy('fkMainRecord')
            ->get()
            ->toArray();
        $MainIDs = array_column($MainIDs, 'fkMainRecord');
        $moduleFields = ['id'];
        $response = Self::getPowerPanelRecords($moduleFields)
            ->deleted()
            ->whereIn('id', $MainIDs)
            ->count();
        return $response;
    }

    public function servicescat()
    {
        $response = false;
        $response = $this->belongsTo('Powerpanel\ServiceCategory\Models\ServiceCategory', 'intFKCategory', 'id');
        return $response;
    }

    public static function getRecordList_tab1($filterArr = false)
    {
        $response = false;
        $moduleFields = [
            'id',
            'intAliasId',
            'intFKCategory',
            'varTitle',
            'fkIntImgId',
            'txtDescription',
            'txtShortDescription',
            'chrPublish',
            'fkMainRecord',
            'dtDateTime',
            'chrPageActive',
            'created_at',
            'updated_at',
        ];
        $servicesCatfileds = ['id', 'varTitle'];
        $MainIDs = Self::distinct()
            ->select("fkMainRecord")
            ->where('chrMain', 'N')
            ->where('chrIsPreview', 'N')
            ->groupBy('fkMainRecord')
            ->deleted()
            ->get()
            ->toArray();
        $MainIDs = array_column($MainIDs, 'fkMainRecord');
        $response = Self::getPowerPanelRecords($moduleFields, $servicesCatfileds)
            ->deleted()
            ->filter($filterArr)
            ->whereIn('id', $MainIDs)
            ->get();

        return $response;
    }

    public static function getAllServices($fields, $limit, $sdate, $edate, $servicescat)
    {
        $response = false;
        $moduleFields = ['id',
            'intAliasId',
            'varTitle',
            'intFkCategory',
            'txtDescription',
            'varShortDescription',
            'varMetaTitle',
            'varMetaDescription',
            'chrPublish',
            'chrPageActive',
            'created_at',
            'updated_at'];
        array_push($moduleFields, 'fkIntImgId');
        $aliasFields = ['id', 'varAlias'];
        if (empty($response)) {
            $response = Self::getFrontRecords($moduleFields, $aliasFields)
                ->where('chrMain', 'Y');
            if ($blogscat != '') {
                $response = $response->where('intFkCategory', $blogscat);
            }

            if ($sdate != '' && $edate != '') {
                // $response = $response->whereRaw('(DATE(dtDateTime)>="' . date('Y-m-d', strtotime(str_replace('/', '-', $sdate))) . '" AND (DATE(dtDateTime)<="' . date('Y-m-d', strtotime(str_replace('/', '-', $edate))) . '") OR ("' . date('Y-m-d', strtotime(str_replace('/', '-', $sdate))) . '" >= dtDateTime and dtEndDateTime is null))');
            } else if ($sdate != '') {
                $response = $response->whereRaw('DATE(dtDateTime)>="' . date('Y-m-d', strtotime(str_replace('/', '-', $sdate))) . '"');
            } else if ($edate != '') {
                $response = $response->whereRaw('DATE(dtDateTime)<="' . date('Y-m-d', strtotime(str_replace('/', '-', $edate))) . '"');
            }
            $response = $response->where('chrIsPreview', 'N')
                ->deleted()
                ->publish()
                ->orderBy('dtDateTime', 'desc');
            if ($limit != '') {
                $response = $response->limit($limit);
            }
            if (Request::segment(1) != '') {
                $response = $response->paginate(6);
            } else {
                $response = $response->get();
            }

        }
        return $response;
    }
    

}
