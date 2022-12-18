<?php

namespace Powerpanel\Work\Models;

use Cache;
use Request;
use Illuminate\Database\Eloquent\Model;
use DB;

class Work extends Model {

    protected $table = 'work';
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
        'chrFeaturedWork',
        'chrPublish',
        'chrDelete',
        'varMetaTitle',
        'varMetaKeyword',
        'varMetaDescription',
        'created_at',
        'updated_at'
    ];

    /**
     * This method handels retrival of front Work list from power composer
     * @return  Object
     * @since   2020-02-04
     * @author  NetQuick
     */
    public static function getWorkList($fields, $recIds) {
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
            'chrFeaturedWork',
            'chrPublish',
            'chrDelete',
            'varMetaTitle',
            'varMetaKeyword',
            'varMetaDescription',
            'created_at',
            'updated_at'
        ];
        $aliasFields = ['id', 'varAlias'];
        //$response = Cache::tags(['Work'])->get('getWorkList_' . implode('-', $recIds));
        //if (empty($response)) {
        $response = Self::getFrontRecords($moduleFields, $aliasFields)
                ->whereIn('id', $recIds)
                ->deleted()
                ->publish()
                ->orderByRaw(DB::raw("FIELD(id, " . implode(',', $recIds) . " )"));
        $response = $response->get();

        //Cache::tags(['Work'])->forever('getWorkList_' . implode('-', $recIds), $response);
        //}
        return $response;
    }

    /**
     * This method handels retrival of last month Work
     * @return  Object
     * @since   2020-02-04
     * @author  NetQuick
     */
    public static function getTemplateWorkList($fields, $filterArr = false) {
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
            'chrFeaturedWork',
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
            $response = $query->orderBy('intDisplayOrder', 'ASC')->paginate(8);
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
        $response = $response->groupBy('id')->get();

        return $response;
    }

    /**
     * This method handels retrival of front blog detail
     * @return  Object
     * @since   2017-10-13
     * @author  NetQuick
     */
    public static function getRecordIdByAliasID($aliasID) {
        $response = false;
        $response = Cache::tags(['Work'])->get('getWorkRecordIdByAliasID_' . $aliasID);
        if (empty($response)) {
            $response = Self::Select('id')->deleted()->publish()->checkAliasId($aliasID)->first();
            Cache::tags(['Work'])->forever('getWorkRecordIdByAliasID_' . $aliasID, $response);
        }
        return $response;
    }

    /**
     * This method handels retrival of front Work list
     * @return  Object
     * @since   2017-10-14
     * @author  NetQuick
     */
    public static function getListByCategory( $paginate = 6, $page = false) {
        $response = false;
        $moduleFields = ['varTitle', 'fkIntImgId', 'intAliasId', 'txtShortDescription', 'txtDescription'];
        $aliasFields = ['id', 'varAlias'];
        $response = Cache::tags(['Work'])->get('getFrontWorkList_' . $page);
        if (empty($response)) {
            $response = Self::getFrontRecords($moduleFields, $aliasFields)
                    ->deleted()
                    ->orderBy('intDisplayOrder', 'ASC')
                    ->publish()
                    ->paginate($paginate);

            Cache::tags(['Work'])->forever('getFrontWorkList_' . $page, $response);
        }
        return $response;
    }

    /**
     * This method handels retrival of front Work list
     * @return  Object
     * @since   2017-10-14
     * @author  NetQuick
     */
    public static function getFrontList($paginate = false, $page = false) {
        $response = false;
        $moduleFields = ['varTitle', 'fkIntImgId', 'intAliasId', 'txtShortDescription','txtDescription', 'varFontAwesomeIcon'];
        $aliasFields = ['id', 'varAlias'];
        $response = Cache::tags(['Work'])->get('getFrontWorkList_' . $page);
        if (empty($response)) {
            $response = Self::getFrontRecords($moduleFields, $aliasFields)
                    ->deleted()
                    ->publish()
                    ->orderBy('intDisplayOrder', 'ASC')
                    ->paginate($paginate);

            Cache::tags(['Work'])->forever('getFrontWorkList_' . $page, $response);
        }
        return $response;
    }
    public static function getFrontListService($paginate = false, $page = false) {
        $response = false;
        $moduleFields = ['varTitle', 'fkIntImgId', 'intAliasId', 'txtShortDescription','txtDescription', 'varFontAwesomeIcon'];
        $aliasFields = ['id', 'varAlias'];
        $response = Cache::tags(['Work'])->get('getFrontWorkList_' . $page);
        if (empty($response)) {
            $response = Self::getFrontRecords($moduleFields, $aliasFields)
                    ->deleted()
                    ->publish()
                    ->orderBy('intDisplayOrder', 'ASC')
                    ->paginate($paginate);

            Cache::tags(['Work'])->forever('getFrontWorkList_' . $page, $response);
        }
        return $response;
    }

    public static function getFrontListHome($id = false) {
        $response = false;
        $moduleFields = ['varTitle', 'fkIntImgId', 'intAliasId', 'txtShortDescription','txtDescription', 'varFontAwesomeIcon'];
        $aliasFields = ['id', 'varAlias'];
        $response = Cache::tags(['Work'])->get('getFrontLatestWorkList_' . $id);
        if (empty($response)) {
            $response = Self::getFrontRecords($moduleFields, $aliasFields)
                    ->deleted()
                    ->publish()
                    ->take(3)
                    ->orderBy('intDisplayOrder', 'DESC')
                    ->get();

            Cache::tags(['Work'])->forever('getFrontLatestWorkList_' . $id, $response);
        }
        return $response;
    }

    public static function getFrontListOne($paginate = false, $page = false) {
        $response = false;
        $moduleFields = ['varTitle', 'fkIntImgId', 'intAliasId', 'txtShortDescription','txtDescription', 'varFontAwesomeIcon'];
        $aliasFields = ['id', 'varAlias'];
        $response = Cache::tags(['Work'])->get('getFrontWorkList_' . $page);
        if (empty($response)) {
            $response = Self::getFrontRecords($moduleFields, $aliasFields)
                    ->deleted()
                    ->publish()
                    ->take(3)
                    ->orderBy('intDisplayOrder', 'ASC')
                    ->paginate($paginate);

            Cache::tags(['Work'])->forever('getFrontWorkList_' . $page, $response);
        }
        return $response;
    }

    /**
     * This method handels retrival of front latest Work list
     * @return  Object
     * @since   2017-10-13
     * @author  NetQuick
     */
    public static function getLatestList($id = false) {
        $response = false;
        $moduleFields = ['varTitle', 'fkIntImgId', 'intAliasId', 'created_at'];
        $aliasFields = ['id', 'varAlias'];
        $response = Cache::tags(['Work'])->get('getFrontLatestWorkList_' . $id);
        if (empty($response)) {
            $response = Self::getFrontRecords($moduleFields, $aliasFields)
                    ->deleted()
                    ->publish()
                    ->latestRecord($id)
                    ->take(5)
                    ->get();

            Cache::tags(['Work'])->forever('getFrontLatestWorkList_' . $id, $response);
        }
        return $response;
    }

    public static function getFrontWorkDropdownList($id = false) {
        $response = false;
        $moduleFields = ['id','varTitle', 'fkIntImgId', 'intAliasId', 'created_at'];
        $aliasFields = ['id', 'varAlias'];
        $response = Cache::tags(['Work'])->get('getFrontLatestWorkList_' . $id);
        if (empty($response)) {
            $response = Self::getFrontRecords($moduleFields, $aliasFields)
                    ->deleted()
                    ->publish()
                    ->latestRecord($id)
                    ->get();

            Cache::tags(['Work'])->forever('getFrontLatestWorkList_' . $id, $response);
        }
        return $response;
    }
    
    public static function getWorkSiteMapData() {
        $response = false;
        $aliasFields = ['id', 'varAlias'];
        $workFields = ['varTitle','intAliasId'];

        $response = Cache::tags(['Work'])->get('getWorkSiteMapData');
        if (empty($response)) {
            $response = Self::getFrontRecords($workFields, $aliasFields)
                    ->deleted()
                    ->publish()
                    ->get();
            Cache::tags(['Work'])->forever('getWorkSiteMapData', $response);
        }
        return $response;
    }

    /**
     * This method handels retrival of front latest Work list
     * @return  Object
     * @since   2017-10-13
     * @author  NetQuick
     */
    public static function getSimilarRecordList($id = false) {
        $response = false;
        $moduleFields = ['varTitle', 'fkIntImgId', 'intAliasId', 'created_at'];
        $aliasFields = ['id', 'varAlias'];
        $response = Cache::tags(['Work'])->get('getSimilarRecordList_' . $id);
        if (empty($response)) {
            $response = Self::getFrontRecords($moduleFields, $aliasFields)
                    ->deleted()
                    ->publish()
                    ->latestRecord($id)
                    ->take(5)
                    ->get();

            Cache::tags(['Work'])->forever('getSimilarRecordList_' . $id, $response);
        }
        return $response;
    }

    public static function getSlidebarRecordList() {
        $response = false;
        $moduleFields = ['varTitle', 'fkIntImgId', 'intAliasId', 'created_at'];
        $aliasFields = ['id', 'varAlias'];
        $response = Cache::tags(['Work'])->get('getSimilarRecordList_');
        if (empty($response)) {
            $response = Self::getFrontRecords($moduleFields, $aliasFields)
                    ->deleted()
                    ->publish()
                    ->take(5)
                    ->orderBy('id', 'DESC')
                    ->get();

            Cache::tags(['Work'])->forever('getSimilarRecordList_', $response);
        }
        return $response;
    }

    /**
     * This method handels retrival of front latest Work list
     * @return  Object
     * @since   2017-10-13
     * @author  NetQuick
     */
    public static function getFeaturedList($limit = 5) {
        $response = false;
        $moduleFields = ['varTitle', 'fkIntImgId', 'varFontAwesomeIcon', 'txtShortDescription', 'intAliasId', 'created_at'];
        $aliasFields = ['id', 'varAlias'];
        $response = Cache::tags(['Work'])->get('getWorkFeaturedList');
        if (empty($response)) {
            $response = Self::getFrontRecords($moduleFields, $aliasFields)
                    ->publish()
                    ->deleted()
                    ->featured('Y')
                    ->displayOrderBy('ASC')
                    ->take($limit)
                    ->get();
            Cache::tags(['Work'])->forever('getWorkFeaturedList', $response);
        }
        return $response;
    }

    public static function getFrontWorkDropdown() {
        $response = false;
        $moduleFields = ['id', 'varTitle'];
        $response = Cache::tags(['Work'])->get('getFrontWorkDropdown');
        if (empty($response)) {
            $response = Self::getFrontRecords($moduleFields)
                    ->publish()
                    ->deleted()
                    ->displayOrderBy('ASC')
                    ->get();

            Cache::tags(['Work'])->forever('getFrontWorkDropdown', $response);
        }
        return $response;
    }

    /**
     * This method handels retrival of front Work detail
     * @return  Object
     * @since   2017-10-13
     * @author  NetQuick
     */
    public static function getFrontDetail($id) {
        $response = false;
        $moduleFields = ['id', 'intAliasId', 'fkIntImgId', 'fkIntVideoId', 'varTitle', 'varExternalLink', 'varFontAwesomeIcon', 'txtShortDescription', 'txtDescription', 'txtCategories', 'varPreferences', 'intDisplayOrder', 'chrFeaturedWork', 'chrPublish', 'chrDelete', 'varMetaTitle', 'varMetaKeyword', 'varMetaDescription'];
        $aliasFields = ['id', 'varAlias'];
        $response = Cache::tags(['Work'])->get('getFrontWorkDetail_' . $id);
        if (empty($response)) {
            $response = Self::getFrontRecords($moduleFields, $aliasFields)
                    ->deleted()
                    ->publish()
                    ->checkAliasId($id)
                    ->first();
            Cache::tags(['Work'])->forever('getFrontWorkDetail_' . $id, $response);
        }
        return $response;
    }

    /**
     * This method handels retrival of record count based on category
     * @return  Object
     * @since   2017-10-16
     * @author  NetQuick
     */
    public static function getCountById() {
        $response = false;
        $moduleFields = ['id'];
        $response = Self::getPowerPanelRecords($moduleFields)
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
            'chrFeaturedWork',
            'intDisplayOrder',
            'txtShortDescription',
            'txtDescription',
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
    public static function getRecordList($filterArr = false) {
        $response = false;
        $moduleFields = ['id', 'intAliasId', 'varTitle', 'varExternalLink', 'varFontAwesomeIcon', 'txtShortDescription', 'fkIntImgId', 'txtCategories', 'intDisplayOrder', 'txtDescription', 'chrFeaturedWork', 'chrPublish'];
        $aliasFields = ['id', 'varAlias'];
        $response = Self::getPowerPanelRecords($moduleFields, $aliasFields)
                ->deleted()
                ->filter($filterArr)
                ->get();
        return $response;
    }

    public static function getWorkNameById($id) {
        $response = false;
        $workFields = ['varTitle'];
        $response = Self::getPowerPanelRecords($workFields)->deleted()->where('id', $id)->first();
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
        $response = self::with(['alias' => function ($query) {
                        $query->checkModuleCode();
                    }, 'image']);
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
     * This method handels retrival of Work records
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
            $data['workCategory'] = function ($query) use ($categoryFields) {
                $query->select($categoryFields);
            };
        }
        if (count($data) > 0) {
            $response = $response->with($data);
        }
        return $response;
    }

    public static function getWorkNameByWorkId($ids) {
        $response = false;
        $workFields = ['varTitle'];
        $response = Self::getPowerPanelRecords($Fields)->deleted()->whereIn('id', $ids)->get();
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
        $moduleFields = ['id', 'varTitle', 'fkIntImgId', 'txtCategories', 'varExternalLink', 'varFontAwesomeIcon', 'chrFeaturedWork', 'intDisplayOrder', 'txtShortDescription', 'txtDescription', 'varMetaTitle', 'varMetaKeyword', 'varMetaDescription', 'chrPublish'];
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
    public function workCategory() {
        $response = false;
        $response = $this->belongsTo('Powerpanel\WorkCategory\Models\WorkCategory', 'intCategoryId', 'id');
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
        $response = $query->where(['chrFeaturedWork' => $flag]);
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
     * This method handels Popular Work scope
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

        if (isset($filterArr['template']) && $filterArr['template'] == 'featured-Work') {
            $query->where('chrFeaturedWork', '=', 'Y')->orderBy('intDisplayOrder', 'DESC');
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

    public static function getSidebarRecordList()
    {
        $response = false;
        $moduleFields = ['id',
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
        'chrFeaturedWork',
        'chrPublish',
        'chrDelete',
        'varMetaTitle',
        'varMetaKeyword',
        'varMetaDescription',
        'created_at',
        'updated_at'];
        $aliasFields = ['id', 'varAlias'];
        $response = Cache::tags(['Work'])->get('getFrontLatestWorkList_');
        if (empty($response)) {
            $response = Self::getFrontRecords($moduleFields, $aliasFields)
                ->deleted()
                ->publish()
                ->orderBy('id', 'DESC')
                ->take(5)
                //->orderBy('intDisplayOrder', 'DESC')
                ->get();
            Cache::tags(['Work'])->forever('getFrontLatestWorkList_' ,$response);
        }
        return $response;
    }

}
