<?php
namespace Powerpanel\BlogCategory\Models;

use Illuminate\Database\Eloquent\Model;
use App\CommonModel;
use Cache;
use DB;

class BlogCategory extends Model {
    protected $table = 'blog_category';
    protected $fillable = [
        'id',
        'intAliasId',
        'dtDateTime',
        'dtEndDateTime',
        'varTitle',
        'intDisplayOrder',
        'txtDescription',
        'chrPublish',
        'chrDelete',
        'intSearchRank',
        'varMetaTitle',
        'varMetaDescription',
        'intSearchRank',
        'chrPageActive',
        'varPassword',
        'chrDraft',
        'chrTrash',
        'FavoriteID',
        'LockUserID','chrLock',
        'created_at',
        'updated_at'
    ];
    /**
     * This method handels retrival of front blog detail
     * @return  Object
     * @since   2017-10-13
     * @author  NetQuick
     */
    public static function getRecordIdByAliasID($aliasID) {
        $response = false;
        $response = Cache::tags(['BlogCategory'])->get('getBlogCategoryRecordIdByAliasID_' . $aliasID);
        if (empty($response)) {
            $response = Self::Select('id')->deleted()->publish()->checkAliasId($aliasID)->first();
            Cache::tags(['BlogCategory'])->forever('getBlogCategoryRecordIdByAliasID_' . $aliasID, $response);
        }
        return $response;
    }
    /**
     * This method handels retrival of front blog detail
     * @return  Object
     * @since   2017-10-13
     * @author  NetQuick
     */
    public static function getRecordDataByAliasID($aliasID) {
        $response = false;
        $response = Cache::tags(['BlogCategory'])->get('getPublicationsCatRecordDataByAliasID' . $aliasID);
        if (empty($response)) {
            $response = Self::Select(
                            'id', 'varTitle', 'intAliasId', 'txtDescription', 'varMetaTitle', 'varMetaKeyword', 'varMetaDescription')
                    ->deleted()
                    ->publish()
                    ->checkAliasId($aliasID)
                    ->where('chrMain', 'Y')
                    ->where('chrIsPreview', 'N')
                    ->first();
            Cache::tags(['BlogCategory'])->forever('getPublicationsCatRecordDataByAliasID' . $aliasID, $response);
        }
        return $response;
    }
    public static function getCatWithParent($moduleCode = false) {
        $response = false;
        $categoryFields = ['id', 'intAliasId', 'varTitle', 'intDisplayOrder'];
        $aliasFields = ['id', 'varAlias'];
        $response = Self::getPowerPanelRecords($categoryFields, $aliasFields, $moduleCode)
                        ->deleted()
                        ->publish()
                        ->where('chrMain', 'Y')
                        ->where('chrIsPreview', 'N')
                        ->where('chrDraft', '!=', 'D')
                        ->where('chrTrash', '!=', 'Y')
                        ->orderBy('intDisplayOrder', 'asc')->get();
        return $response;
    }
    /**
     * This method handels retrival of front service list
     * @return  Object
     * @since   2017-10-14
     * @author  NetQuick
     */
    public static function getLatestForHome($limit = 1) {
        $response = false;
        $moduleFields = ['id', 'intAliasId', 'varTitle', 'dtDateTime'];
        $aliasFields = ['id', 'varAlias'];
        $response = Cache::tags(['BlogCategory'])->get('getLatestForHome_' . $limit);
        if (empty($response)) {
            $response = Self::getFrontRecords($moduleFields, $aliasFields)
                    ->deleted()
                    ->publish()
                    ->dateRange()
                    ->where('chrMain', 'Y')
                    ->take($limit)
                    ->orderBy('dtDateTime', 'DESC')
                    ->get();
            Cache::tags(['BlogCategory'])->forever('getLatestForHome_' . $limit, $response);
        }
        return $response;
    }
    /**
     * This method handels retrival of front latest BlogCategory list
     * @return  Object
     * @since   2017-10-13
     * @author  NetQuick
     */
    public static function getFrontPopularList($id = false) {
        $response = false;
        $moduleFields = ['id', 'varTitle', 'intAliasId', 'dtDateTime', 'dtEndDateTime'];
        $aliasFields = ['id', 'varAlias'];
        $response = Cache::tags(['BlogCategory'])->get('getFrontPopularBlogCategoryList_' . $id);
        if (empty($response)) {
            $response = Self::getFrontRecords($moduleFields, $aliasFields)
                    ->deleted()
                    ->publish()
                    ->latest($id)
                    ->take(3)
                    ->get();
            Cache::tags(['BlogCategory'])->forever('getFrontPopularBlogCategoryList_' . $id, $response);
        }
        return $response;
    }
    public static function getNewRecordsCount() {
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
                ->checkStarRecord('Y')
                ->where('chrTrash', '!=', 'Y')
                ->count();
        return $response;
    }
    public static function getChildrollbackGrid() {
        $id = $_REQUEST['id'];
        $response = false;
        $cmsPageFields = ['id', 'intAliasId', 'dtDateTime', 'varTitle', 'txtDescription', 'intDisplayOrder', 'varMetaTitle', 'varMetaDescription', 'chrPublish', 'chrDelete', 'created_at', 'UserID', 'chrApproved', 'fkMainRecord', 'intApprovedBy', 'intSearchRank', 'chrPageActive', 'varPassword', 'chrDraft', 'chrTrash', 'FavoriteID', 'created_at', 'updated_at'];
        $aliasFields = ['id', 'varAlias'];
        $response = Self::getPowerPanelRecords($cmsPageFields, $aliasFields)
                ->deleted()
                ->where('chrMain', 'N')
                ->where('chrRollBack', 'Y')
                ->where('fkMainRecord', $id)
                ->where('chrIsPreview', 'N')
                ->orderBy('created_at', 'desc')
                ->get();
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
                ->where('chrMain', 'Y')
                ->where('chrIsPreview', 'N')
                ->count();
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
        $moduleFields = [
            'id',
            'intAliasId',
            'varTitle',
            'dtDateTime',
            'txtDescription',
            'intDisplayOrder',
            'chrPublish',
            'chrMain',
            'dtDateTime',
            'dtEndDateTime',
            'intSearchRank',
            'chrPageActive',
            'varPassword',
            'chrAddStar',
            'chrDraft',
            'chrTrash',
            'FavoriteID',
            'LockUserID','chrLock',
            'created_at',
            'updated_at'
        ];
        $aliasFields = ['id', 'varAlias'];
        $response = Self::getPowerPanelRecords($moduleFields, $aliasFields)
                ->deleted();
        $response = $response->filter($filterArr)
                ->deleted()
                ->where(function ($query) use ($userid) {
                    $query->where("UserID", '=', $userid)->where('chrPageActive', '=', 'PR')
                    ->orWhere('chrPageActive', '!=', 'PR');
                })
                ->checkMainRecord('Y')
                ->where('chrIsPreview', 'N')
                ->where('chrTrash', '!=', 'Y')
                ->get();
        return $response;
    }
    /**
     * This method handels retrival of record count
     * @return  Object
     * @since   2017-10-16
     * @author  NetQuick
     */
    public static function getRecordListFavorite($filterArr = false, $isAdmin = false) {
        $response = false;
        $userid = auth()->user()->id;
        $moduleFields = [
            'id',
            'intAliasId',
            'varTitle',
            'dtDateTime',
            'txtDescription',
            'intDisplayOrder',
            'chrPublish',
            'chrMain',
            'dtDateTime',
            'dtEndDateTime',
            'intSearchRank',
            'chrPageActive',
            'varPassword',
            'chrAddStar',
            'chrDraft',
            'chrTrash',
            'FavoriteID',
            'LockUserID','chrLock',
            'created_at',
            'updated_at'
        ];
        $aliasFields = ['id', 'varAlias'];
        $response = Self::getPowerPanelRecords($moduleFields, $aliasFields)
                ->deleted()
                ->filter($filterArr)
                ->checkMainRecord('Y')
                ->where('chrIsPreview', 'N')
                ->where('chrTrash', '!=', 'Y')
                ->whereRaw("find_in_set($userid,FavoriteID)")
                ->where(function ($query) use ($userid) {
            $query->where("UserID", '=', $userid)->where('chrPageActive', '=', 'PR')
            ->orWhere('chrPageActive', '!=', 'PR');
        });
        $response = $response->get();
        return $response;
    }
    /**
     * This method handels retrival of record count
     * @return  Object
     * @since   2017-10-16
     * @author  NetQuick
     */
    public static function getRecordListDraft($filterArr = false, $isAdmin = false) {
        $response = false;
        $userid = auth()->user()->id;
        $moduleFields = [
            'id',
            'intAliasId',
            'varTitle',
            'dtDateTime',
            'txtDescription',
            'intDisplayOrder',
            'chrPublish',
            'chrMain',
            'dtDateTime',
            'dtEndDateTime',
            'intSearchRank',
            'chrPageActive',
            'varPassword',
            'chrAddStar',
            'chrDraft',
            'chrTrash',
            'FavoriteID',
            'LockUserID','chrLock',
            'created_at',
            'updated_at'
        ];
        $aliasFields = ['id', 'varAlias'];
        $response = Self::getPowerPanelRecords($moduleFields, $aliasFields)
                ->filter($filterArr)
                ->deleted()
                ->filter($filterArr)
                ->checkMainRecord('Y')
                ->where('chrIsPreview', 'N')
                ->where('chrDraft', 'D')
                ->where('chrTrash', '!=', 'Y')
                ->where(function ($query) use ($userid) {
            $query->where("UserID", '=', $userid)->where('chrPageActive', '=', 'PR')
            ->orWhere('chrPageActive', '!=', 'PR');
        });
        $response = $response->get();
        return $response;
    }
    /**
     * This method handels retrival of record count
     * @return  Object
     * @since   2017-10-16
     * @author  NetQuick
     */
    public static function getRecordListTrash($filterArr = false, $isAdmin = false) {
        $response = false;
        $userid = auth()->user()->id;
        $moduleFields = [
            'id',
            'intAliasId',
            'varTitle',
            'dtDateTime',
            'txtDescription',
            'intDisplayOrder',
            'chrPublish',
            'chrMain',
            'dtDateTime',
            'dtEndDateTime',
            'intSearchRank',
            'chrPageActive',
            'varPassword',
            'chrAddStar',
            'chrDraft',
            'chrTrash',
            'FavoriteID',
            'LockUserID','chrLock',
            'created_at',
            'updated_at'
        ];
        $aliasFields = ['id', 'varAlias'];
        $response = Self::getPowerPanelRecords($moduleFields, $aliasFields)
                ->deleted()
                ->filter($filterArr)
                ->checkMainRecord('Y')
                ->where('chrIsPreview', 'N')
                ->where('chrTrash', 'Y')
                ->where(function ($query) use ($userid) {
            $query->where("UserID", '=', $userid)->where('chrPageActive', '=', 'PR')
            ->orWhere('chrPageActive', '!=', 'PR');
        });
        $response = $response->get();
        return $response;
    }
    public static function getRecordList_tab1($filterArr = false) {
        $response = false;
        $cmsPageFields = ['id', 'intAliasId', 'varTitle', 'dtDateTime', 'txtDescription', 'intDisplayOrder', 'varMetaTitle', 'varMetaDescription', 'chrPublish', 'chrDelete','LockUserID','chrLock', 'chrAddStar', 'chrPageActive', 'varPassword', 'chrDraft', 'chrTrash', 'FavoriteID', 'created_at', 'updated_at'];
        $aliasFields = ['id', 'varAlias'];
        $MainIDs = Self::distinct()
                ->select("fkMainRecord")
                ->where('chrMain', 'N')
                ->where('chrIsPreview', 'N')
                ->groupBy('fkMainRecord')
                ->deleted()
                ->get()
                ->toArray();
        $MainIDs = array_column($MainIDs, 'fkMainRecord');
        $response = Self::getPowerPanelRecords($cmsPageFields, $aliasFields)
                ->deleted()
                ->where('chrAddStar', 'Y')
                ->filter($filterArr)
                ->whereIn('id', $MainIDs)
                ->where('chrTrash', '!=', 'Y')
                ->get();
        return $response;
    }
    public static function getRecordCount($filterArr = false, $returnCounter = false) {
        $response = 0;
        $cmsPageFields = ['id'];
        $pageQuery = Self::getPowerPanelRecords($cmsPageFields);
        if ($filterArr != false) {
            $pageQuery = $pageQuery->filter($filterArr, $returnCounter);
        }
        $response = $pageQuery->deleted()
                ->where('chrMain', 'Y')
                ->where('chrIsPreview', 'N')
                ->count();
        return $response;
    }
    public static function getRecordCountforList($filterArr = false, $returnCounter = false, $isAdmin = false) {
        $response = 0;
        $cmsPageFields = ['id'];
        $response = Self::getPowerPanelRecords($cmsPageFields);
        if ($filterArr != false) {
            $response = $response->filter($filterArr, $returnCounter);
        }
        $userid = auth()->user()->id;
        $response = $response->deleted()
                ->where(function ($query) use ($userid) {
                    $query->where("UserID", '=', $userid)->where('chrPageActive', '=', 'PR')
                    ->orWhere('chrPageActive', '!=', 'PR');
                })
                ->checkMainRecord('Y')
                ->where('chrIsPreview', 'N')
                ->where('chrTrash', '!=', 'Y')
                ->count();
        return $response;
    }
    public static function getRecordCountListApprovalTab($filterArr = false) {
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
                ->checkStarRecord('Y')
                 ->where('chrTrash', '!=', 'Y')
                ->count();
        return $response;
    }
    public static function getRecordCount_letest($Main_id, $id) {
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
    /**
     * This method handels retrival of record count
     * @return  Object
     * @since   2017-10-16
     * @author  NetQuick
     */
    public static function getRecordById($id = false, $ignoreDeleteScope = false) {
        $response = false;
        $moduleFields = [
            'id',
            'intAliasId',
            'varTitle',
            'dtDateTime',
            'dtEndDateTime',
            'intDisplayOrder',
            'txtDescription',
            'intSearchRank',
            'varMetaTitle',
            'varMetaDescription',
            'fkMainRecord',
            'chrMain',
            'chrPublish',
            'UserID',
            'chrPageActive',
            'varPassword',
            'chrDraft',
            'chrTrash',
            'FavoriteID',
            'LockUserID','chrLock',
            'created_at',
            'updated_at'
        ];
        $aliasFields = ['id', 'varAlias'];
        $response = Self::getPowerPanelRecords($moduleFields, $aliasFields);
        if (!$ignoreDeleteScope) {
            $response = $response->deleted();
        }
        $response = $response->checkRecordId($id)->first();
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
            'intDisplayOrder'
        ];
        if (!in_array($order, Self::$fetchedOrder)) {
            array_push(Self::$fetchedOrder, $order);
            Self::$fetchedOrderObj = Self::getPowerPanelRecords($moduleFields)
                    ->deleted()
                    ->orderCheck($order)
                    ->where('chrMain', 'Y')
                    ->where('chrIsPreview', 'N')
                    ->first();
        }
        $response = Self::$fetchedOrderObj;
        return $response;
    }
    public static function getOrderOfApproval($id) {
        $result = Self::select('intDisplayOrder')
                ->checkRecordId($id)
                ->first();
        return $result;
    }
    #Database Configurations========================================
    /**
     * This method handels retrival of BlogCategory records old version *=Delete it afterwards=*
     * @return  Object
     * @since   2016-07-14
     * @author  NetQuick
     */
    public static function getRecords() {
        $response = false;
        $data = ['alias'];
        if (count($data) > 0) {
            $response = Self::with($data);
        }
        return $response;
    }
    /**
     * This method handels retrival of BlogCategory records
     * @return  Object
     * @since   2016-07-14
     * @author  NetQuick
     */
    public static function getFrontRecords($BlogCategoryFields = false, $aliasFields = false) {
        $response = false;
        $data = [];
        if ($aliasFields != false) {
            $data = [
                'alias' => function ($query) use ($aliasFields) {
                    $query->select($aliasFields);
                },
            ];
        }
        return self::select($BlogCategoryFields)->with($data);
    }
    /**
     * This method handels retrival of BlogCategory records
     * @return  Object
     * @since   2016-07-14
     * @author  NetQuick
     */
    public static function getPowerPanelRecords($moduleFields = false, $aliasFields = false, $moduleCode = false) {
        $data = [];
        $response = false;
        $response = self::select($moduleFields);
        if ($aliasFields != false) {
            $data['alias'] = function ($query) use ($aliasFields, $moduleCode) {
                $query->select($aliasFields)->checkModuleCode($moduleCode);
            };
        }
        if (count($data) > 0) {
            $response = $response->with($data);
        }
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
        $moduleFields = ['id',
            'varTitle',
            'dtDateTime',
            'dtEndDateTime',
            'intDisplayOrder',
            'txtDescription',
            'intSearchRank',
            'varMetaTitle',
            'varMetaDescription',
            'varMetaDescription',
            'fkMainRecord',
            'chrPublish',
            'UserID',
            'chrPageActive',
            'varPassword',
            'chrDraft',
            'chrTrash',
            'FavoriteID',
            'LockUserID','chrLock',
            'created_at',
            'updated_at'
        ];
        $response = Self::getPowerPanelRecords($moduleFields)->deleted()->checkRecordId($id)->first();
        return $response;
    }
    /**
     * This method handels retrival of front latest Show list
     * @return  Object
     * @since   2017-10-13
     * @author  NetQuick
     */
    public static function getFeaturedList($limit = 10) {
        $response = false;
        $response = Cache::tags(['BlogCategory'])->get('getBlogCategoryFeaturedList');
        if (empty($response)) {
            $moduleFields = ['varTitle', 'txtDescription', 'intAliasId', 'dtDateTime', 'dtEndDateTime', 'created_at'];
            $aliasFields = ['id', 'varAlias'];
            $response = Self::getFrontRecords($moduleFields, $aliasFields)
                    ->deleted()
                    ->publish()
                    ->take($limit)
                    ->get();
            Cache::tags(['BlogCategory'])->forever('getBlogCategoryFeaturedList', $response);
        }
        return $response;
    }
    /**
     * This method handels alias relation
     * @return  Object
     * @since   2016-07-14
     * @author  NetQuick
     */
    public function alias() {
        return $this->belongsTo('App\Alias', 'intAliasId', 'id');
    }
    /**
     * This method handels alias id scope
     * @return  Object
     * @since   2016-07-24
     * @author  NetQuick
     */
    public function scopeCheckAliasId($query, $id) {
        return $query->where('intAliasId', $id);
    }
    /**
     * This method handels order scope
     * @return  Object
     * @since   2016-07-20
     * @author  NetQuick
     */
    public function scopeOrderCheck($query, $order) {
        return $query->where('intDisplayOrder', $order);
    }
    /**
     * This method handels record id scope
     * @return  Object
     * @since   2016-07-24
     * @author  NetQuick
     */
    public function scopeCheckRecordId($query, $id) {
        return $query->where('id', $id);
    }
    /**
     * This method handels order scope
     * @return  Object
     * @since   2016-07-14
     * @author  NetQuick
     */
    /**
     * This method handels publish scope
     * @return  Object
     * @since   2016-07-14
     * @author  NetQuick
     */
    public function scopePublish($query) {
        return $query->where(['chrPublish' => 'Y']);
    }
    /**
     * This method handels delete scope
     * @return  Object
     * @since   2016-07-14
     * @author  NetQuick
     */
    public function scopeDeleted($query) {
        return $query->where(['chrDelete' => 'N']);
    }
    /**
     * This method handels Popular Blog scope
     * @return  Object
     * @since   2016-08-30
     * @author  NetQuick
     */
    public function scopeLatest($query, $id = false) {
        $response = false;
        $response = $query
                /* ->groupBy('id') */
                ->orderBy('dtDateTime', 'desc');
        if ($id > 0) {
            $response = $response->where('id', '!=', $id);
            //->whereRaw('dtDateTime > DATE_SUB(NOW(), INTERVAL 7 DAY)')
            //->whereRaw('dtDateTime <= NOW()');
        }
        return $response;
    }
    /**
     * This method handels front filter scope
     * @return  Object
     * @since   2016-08-30
     * @author  NetQuick
     */
    public function scopeFrontFilter($query, $id = false, $range = false) {
        if ($range != false) {
            if ($range[0] != false && $range[1] != false) {
                $query->whereBetween('dtDateTime', $range);
            }
        }
        return $query;
    }
    /**
     * This method handels Main Record scope
     * @return  Object
     * @since   2016-07-24
     * @author  NetQuick
     */
    public function scopeCheckStarRecord($query, $flag = 'Y') {
        $response = false;
        $response = $query->where('chrAddStar', "=", $flag);
        return $response;
    }
    /**
     * This method handels filter scope
     * @return  Object
     * @since   2016-07-14
     * @author  NetQuick
     */
    public function scopeFilter($query, $filterArr = false, $retunTotalRecords = false) {
        $response = null;
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
        if (!empty($filterArr['customFilterIdentity']) && $filterArr['customFilterIdentity'] != ' ') {
            $data = $query->where('chrPageActive', $filterArr['customFilterIdentity']);
        }
        if (!empty($filterArr['searchFilter']) && $filterArr['searchFilter'] != ' ') {
            $data = $query->where('varTitle', 'like', "%" . $filterArr['searchFilter'] . "%");
        }
        if (!empty($filterArr['rangeFilter']['from']) && $filterArr['rangeFilter']['to']) {
            $data = $query->whereRaw('DATE(dtDateTime) BETWEEN "' . date('Y-m-d', strtotime(str_replace('/', '-', $filterArr['rangeFilter']['from']))) . '" AND "' . date('Y-m-d', strtotime(str_replace('/', '-', $filterArr['rangeFilter']['to']))) . '"');
        }
        if (!empty($query)) {
            $response = $query;
        }
        return $response;
    }
    public static function getChildGrid() {
        $id = $_REQUEST['id'];
        $response = false;
        $cmsPageFields = ['id', 'intAliasId', 'dtDateTime', 'varTitle', 'txtDescription', 'intDisplayOrder', 'varMetaTitle', 'varMetaDescription', 'chrPublish', 'chrDelete', 'created_at', 'UserID', 'chrApproved', 'fkMainRecord', 'intApprovedBy', 'chrPageActive', 'varPassword', 'chrDraft', 'chrTrash', 'FavoriteID', 'dtApprovedDateTime', 'created_at', 'updated_at'];
        $aliasFields = ['id', 'varAlias'];
        $response = Self::getPowerPanelRecords($cmsPageFields, $aliasFields)
                ->deleted()
                ->where('chrMain', 'N')
                ->where('fkMainRecord', $id)
                ->where('chrIsPreview', 'N')
                ->orderBy('created_at', 'desc')
                ->get();
//        print_r($response);exit;
        return $response;
    }
    public static function approved_data_Listing($request) {
        $id = $request->id;
        $main_id = $request->main_id;
        // $PUserid = $request->PUserid;
        //Select Child Record Data Start
        $response = false;
        $cmsPageFields = [
            'id',
            'intSearchRank',
            'intAliasId',
            'dtDateTime',
            'intDisplayOrder',
            'dtEndDateTime',
            'varTitle',
            'txtDescription',
            'varMetaTitle',
            'varMetaDescription',
            'chrPublish',
            'chrDelete',
            'created_at',
            'UserID',
            'chrApproved',
            'fkMainRecord',
            'chrPageActive',
            'varPassword',
            'chrDraft',
            'chrTrash',
            'FavoriteID',
            'created_at',
            'updated_at'
        ];
        $aliasFields = ['id', 'varAlias'];
        $response = Self::getPowerPanelRecords($cmsPageFields, $aliasFields)
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
            'dtDateTime' => $response['dtDateTime'],
            'dtEndDateTime' => $response['dtEndDateTime'],
            'txtDescription' => $response['txtDescription'],
            'varMetaTitle' => $response['varMetaTitle'],
            'chrAddStar' => 'N',
            'varMetaDescription' => $response['varMetaDescription'],
            'chrPublish' => $response['chrPublish'],
            'chrDraft' => $response['chrDraft'],
            'intSearchRank' => $response['intSearchRank'],
            'FavoriteID' => $response['FavoriteID'],
            'chrPageActive' => $response['chrPageActive'],
            'chrPublish' => $response['chrPublish']
        ];
        CommonModel::updateRecords($whereConditions, $updateMainRecord, false, 'Powerpanel\BlogCategory\Models\BlogCategory');
        //Update Copy Child Record To Main Record end
        $whereConditions_ApproveN = ['fkMainRecord' => $main_id];
        $updateToApproveN = [
            'chrApproved' => 'N',
            'chrLetest' => 'N',
            'intApprovedBy' => '0',
        ];
        CommonModel::updateRecords($whereConditions_ApproveN, $updateToApproveN, false, 'Powerpanel\BlogCategory\Models\BlogCategory');
        $whereConditionsApprove = ['id' => $id, 'chrMain' => 'N'];
        $updateToApprove = [
            'chrApproved' => 'Y',
            'chrRollBack' => 'Y',
            'intApprovedBy' => auth()->user()->id,
            'dtApprovedDateTime' => date('Y-m-d H:i:s')

        ];
        CommonModel::updateRecords($whereConditionsApprove, $updateToApprove, false, 'Powerpanel\BlogCategory\Models\BlogCategory');
        $msg_show = "Record successfully approved.";
        return $msg_show;
    }
    public static function getFrontList($filterArr = false, $page = 1, $monthid = false, $yearid = false, $print = false) {
        $response = false;
        $moduleFields = ['id',
            'intAliasId',
            'varTitle',
            'txtDescription',
            'dtDateTime',
            'dtEndDateTime',
            'varMetaTitle',
            'chrPublish',
            'chrPageActive',
            'varPassword',
            'chrDraft',
            'chrTrash',
            'FavoriteID',
            'created_at',
            'updated_at'];
        $aliasFields = ['id', 'varAlias'];
        $response = Cache::tags(['BlogCategory'])->get('getFrontBlogCategoryList_' . $page);
        if (empty($response)) {
            $response = Self::getFrontRecords($moduleFields, $aliasFields)
                    ->deleted()
                    ->publish()
                    ->dateRange()
                    ->orderBy('dtDateTime', 'DESC')
                    ->where('chrMain', 'Y');
            if ($monthid != '') {
                $response = $response->whereRaw(DB::raw('month(dtDateTime)=' . $monthid));
            }
            if ($yearid != '') {
                $response = $response->whereRaw(DB::raw('year(dtDateTime)=' . $yearid));
            }
            if ($print == 'print') {
                $response = $response->get();
            } else {
                $response = $response->paginate($page);
            }
            Cache::tags(['BlogCategory'])->forever('getFrontBlogCategoryList_' . $page, $response);
        }
        return $response;
    }
    public static function getMonth() {
        $response = false;
        $response = self::select(DB::raw('month(dtDateTime) as month'))
                ->where('chrPublish', '=', 'Y')
                ->where('chrDelete', '=', 'N')
                ->where('chrMain', '=', 'Y')
                ->where('chrMain', '=', 'Y')
                ->groupBy('month')
                ->orderBy('month', 'asc')
                ->get();
        return $response;
    }
    public static function getYear() {
        $response = false;
        $response = self::select(DB::raw('year(dtDateTime) as year'))
                ->where('chrPublish', '=', 'Y')
                ->where('chrDelete', '=', 'N')
                ->where('chrMain', '=', 'Y')
                ->groupBy('year')
                ->orderBy('year', 'desc')
                ->get();
        return $response;
    }
    public function scopeDateRange($query) {
        $response = false;
        $response = $query->whereRaw('((dtEndDateTime >= NOW() AND NOW() >= dtDateTime) OR (NOW() >= dtDateTime and dtEndDateTime is null))');
        return $response;
    }
    /**
     * This method handels retrival of front executives detail
     * @return  Object
     * @since   2017-10-13
     * @author  NetQuick
     */
    public static function getFrontDetail($id) {
        $response = false;
        $moduleFields = ['id',
            'intAliasId',
            'varTitle',
            'txtDescription',
            'dtDateTime',
            'dtEndDateTime',
            'varMetaTitle',
            'varMetaDescription',
            'chrPublish', 'chrPageActive', 'varPassword', 'chrDraft', 'chrTrash', 'FavoriteID', 'created_at', 'updated_at'];
        $aliasFields = ['id', 'varAlias'];
        $response = Cache::tags(['BlogCategory'])->get('getFrontBlogCategoryDetail_' . $id);
        if (empty($response)) {
            $response = Self::getFrontRecords($moduleFields, $aliasFields)
                    ->deleted()
                    ->publish()
                    ->where('fkMainRecord', 0)
                    ->checkAliasId($id)
                    ->first();
            Cache::tags(['BlogCategory'])->forever('getFrontBlogCategoryDetail_' . $id, $response);
        }
        return $response;
    }
    /**
     * This method handels retrival of front latest executives list
     * @return  Object
     * @since   2017-10-13
     * @author  NetQuick
     */
    public static function getLatestList($id = false) {
        $response = false;
        $moduleFields = ['id',
            'intAliasId',
            'varTitle',
            'txtDescription',
            'dtDateTime',
            'dtEndDateTime',
            'varMetaTitle',
            'varMetaDescription',
            'chrPublish', 'chrPageActive', 'varPassword', 'chrDraft', 'chrTrash', 'FavoriteID', 'created_at', 'updated_at'];
        $aliasFields = ['id', 'varAlias'];
        $response = Cache::tags(['BlogCategory'])->get('getFrontLatestBlogCategoryList_' . $id);
        if (empty($response)) {
            $response = Self::getFrontRecords($moduleFields, $aliasFields)
                    ->deleted()
                    ->publish()
                    ->latest($id)
                    ->take(5)
                    ->get();
            Cache::tags(['BlogCategory'])->forever('getFrontLatestBlogCategoryList_' . $id, $response);
        }
        return $response;
    }
    /**
     * This method handels retrival of front latest service list
     * @return  Object
     * @since   2017-10-13
     * @author  NetQuick
     */
    public static function getAllCategoriesFrontSidebarList() {
        $response = false;
        $moduleFields = ['id', 'intAliasId', 'varTitle', 'intDisplayOrder'];
        $aliasFields = ['id', 'varAlias'];
        $response = Cache::tags(['BlogCategory'])->get('getAllCategoriesFrontSidebarList');
        if (empty($response)) {
            $response = Self::getFrontRecords($moduleFields, $aliasFields)
                    ->deleted()
                    ->publish()
                    ->orderBy('intDisplayOrder', 'ASC')
                    ->where('chrMain', '=', 'Y')
                     ->where('chrTrash', '!=', 'Y')
                    ->where('chrDraft', '!=', 'D')
                    ->where('chrIsPreview', '=', 'N')
                    ->get();
            Cache::tags(['BlogCategory'])->forever('getAllCategoriesFrontSidebarList', $response);
        }
        return $response;
    }
    /**
     * This method handels retrival of record count
     * @return  Object
     * @since   2017-10-16
     * @author  NetQuick
     */
    public static function getRecordsForMenu($moduleCode = false) {
        $response = false;
        $moduleFields = [
            'id',
            'intAliasId',
            'dtDateTime',
            'dtEndDateTime',
            'varTitle',
            'intDisplayOrder',
            'txtDescription',
            'chrPublish',
            'chrDelete',
            'intSearchRank',
            'varMetaTitle',
            'varMetaDescription',
            'chrPageActive', 'varPassword', 'chrDraft', 'chrTrash', 'FavoriteID', 'created_at', 'updated_at'
        ];
        $aliasFields = ['id', 'varAlias'];
        $response = Self::getPowerPanelRecords($moduleFields, $aliasFields, $moduleCode)
                ->dateRange()
                ->where('chrMain', 'Y')
                ->where('chrIsPreview', 'N')
                ->deleted()
                ->publish()
                ->get();
        return $response;
    }
    public static function getCategoryData($id) {
        $response = null;
        $menuFields = ['id', 'varTitle'];
        $response = Self::getRecords($menuFields)
                ->checkPageId($id)
                ->deleted()
                ->first();
        return $response['varTitle'];
    }
    function scopeCheckPageId($query, $id) {
        return $query->where('id', $id);
    }
    public static function getRecordByIds($ids) {
        $response = false;
        $categoryFields = ['id', 'varTitle'];
        $response = Self::getFrontRecords($categoryFields)
                ->publish()
                ->deleted()
                ->where('chrTrash', '!=', 'Y')
                ->where('chrDraft', '!=', 'D')
                ->where('chrIsPreview', 'N')
                ->checkRecordIds($ids)
                ->get();
        return $response;
    }
    public function scopeCheckRecordIds($query, $ids) {
        $response = false;
        $response = $query->whereIn('id', $ids);
        return $response;
    }
    /**
     * This method handels Main Record scope
     * @return  Object
     * @since   2016-07-24
     * @author  NetQuick
     */
    public function scopeCheckMainRecord($query, $checkMain = 'Y') {
        $response = false;
        $response = $query->where('chrMain', "=", $checkMain);
        return $response;
    }
    //Start Draft Count of Records 
    public static function getRecordCountforListDarft($filterArr = false, $returnCounter = false, $isAdmin = false, $ignoreId = array()) {
        $response = 0;
        $cmsPageFields = ['id'];
        $userid = auth()->user()->id;
        $response = Self::getPowerPanelRecords($cmsPageFields);
        if ($filterArr != false) {
            $response = $response->filter($filterArr, $returnCounter);
        }
        $response = $response->deleted()
                ->checkMainRecord('Y')
                ->where('chrIsPreview', 'N')
                ->whereNotIn('id', $ignoreId)
                ->where('chrDraft', 'D')
                ->where('chrTrash', '!=', 'Y')
                ->where(function ($query) use ($userid) {
                    $query->where("UserID", '=', $userid)->where('chrPageActive', '=', 'PR')
                    ->orWhere('chrPageActive', '!=', 'PR');
                })
                ->count();
        return $response;
    }
    //End Draft Count of Records 
    //Start Trash Count of Records 
    public static function getRecordCountforListTrash($filterArr = false, $returnCounter = false, $isAdmin = false, $ignoreId = array()) {
        $response = 0;
        $cmsPageFields = ['id'];
        $userid = auth()->user()->id;
        $response = Self::getPowerPanelRecords($cmsPageFields);
        if ($filterArr != false) {
            $response = $response->filter($filterArr, $returnCounter);
        }
        $response = $response->deleted()
                ->checkMainRecord('Y')
                ->where('chrIsPreview', 'N')
                ->whereNotIn('id', $ignoreId)
                ->where('chrTrash', 'Y')
                ->where(function ($query) use ($userid) {
                    $query->where("UserID", '=', $userid)->where('chrPageActive', '=', 'PR')
                    ->orWhere('chrPageActive', '!=', 'PR');
                })
                ->count();
        return $response;
    }
    //End Trash Count of Records 
    //Start Favorite Count of Records 
    public static function getRecordCountforListFavorite($filterArr = false, $returnCounter = false, $isAdmin = false, $ignoreId = array()) {
        $response = 0;
        $cmsPageFields = ['id'];
        $userid = auth()->user()->id;
        $response = Self::getPowerPanelRecords($cmsPageFields);
        if ($filterArr != false) {
            $response = $response->filter($filterArr, $returnCounter);
        }
        $response = $response->deleted()
                ->checkMainRecord('Y')
                ->where('chrIsPreview', 'N')
                ->whereNotIn('id', $ignoreId)
                ->where('chrTrash', '!=', 'Y')
                ->whereRaw("find_in_set($userid,FavoriteID)")
                ->where(function ($query) use ($userid) {
                    $query->where("UserID", '=', $userid)->where('chrPageActive', '=', 'PR')
                    ->orWhere('chrPageActive', '!=', 'PR');
                })
                ->count();
        return $response;
    }
    //End Favorite Count of Records 
    public static function getCatData($id) {
        $response = false;
        $categoryFields = ['id', 'varTitle', 'intDisplayOrder'];
        $response = Self::getPowerPanelRecords($categoryFields)
                        ->deleted()
                        ->publish()
                        ->where('id', $id)
                        ->orderBy('intDisplayOrder', 'asc')->first();
        return $response;
    }
    
     public static function getAllCategory() {
        $response = false;
        $moduleFields = [
              'id',
            'intAliasId',
            'dtDateTime',
            'dtEndDateTime',
            'varTitle',
            'intDisplayOrder',
            'txtDescription',
            'chrPublish',
            'chrDelete',
            'intSearchRank',
            'varMetaTitle',
            'varMetaDescription',
            'chrPageActive', 'varPassword', 'chrDraft', 'chrTrash', 'FavoriteID', 'created_at', 'updated_at'
        ];
        $response = Self::getPowerPanelRecords($moduleFields, false)
                ->deleted()
                 ->publish()
                ->checkMainRecord('Y')
                ->where('chrPublish', 'Y')
                 ->where('chrIsPreview', 'N')
                ->where('chrTrash', '!=', 'Y')
                ->where('chrDraft', '!=', 'D')
                 ->groupBy('id')
                ->get();
        return $response;
    }

    //End Favorite Count of Records
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
}
