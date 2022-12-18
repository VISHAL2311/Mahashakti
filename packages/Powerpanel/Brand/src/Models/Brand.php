<?php

/**
 * The Brand class handels bannner queries
 * ORM implemetation.
 * @package   Netquick powerpanel
 * @license   http://www.opensource.org/licenses/BSD-3-Clause
 * @version   1.1
 * @since       2017-07-20
 * @author    NetQuick
 */

namespace Powerpanel\Brand\Models;

use App\Modules;
use App\CommonModel;
use Illuminate\Database\Eloquent\Model;
use Cache;
use DB;

class Brand extends Model {

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $table = 'brand';
    protected $fillable = [
        'id',
        'fkMainRecord',
        'intFKCategory',
        'varTitle',
        'txtDescription',
        'intSearchRank',
        'chrMain',
        'chrAddStar',
        'intDisplayOrder',
        'dtDateTime',
        'dtEndDateTime',
        'chrPublish',
        'chrDelete',
        'chrApproved',
        'intApprovedBy',
        'chrRollBack',
        'UserID',
        'chrAddStar',
        'chrDraft',
        'chrTrash',
        'intSearchRank',
        'FavoriteID',
        'LockUserID', 'chrLock',
        'created_at',
        'updated_at'
    ];

    /**
     * This method handels retrival of managementteams records
     * @return  Object
     * @since   2016-07-20
     * @author  NetQuick
     */
    public static function getRecords() {
        $response = false;
        $response = Cache::tags(['Brand'])->get('getBrandRecords');
        if (empty($response)) {
            $response = Self::Select(['id', 'intFKCategory', 'varTitle', 'txtDescription', 'dtDateTime', 'dtEndDateTime', 'intDisplayOrder', 'chrPublish', 'LockUserID', 'chrLock', 'chrDraft', 'chrTrash', 'intSearchRank', 'FavoriteID', 'created_at', 'updated_at'])
                    ->deleted()
                    ->publish()
                    ->paginate(10);
            Cache::tags(['Brand'])->forever('getBrandRecords', $response);
        }
        return $response;
    }

    public static function getFrontList($paginate = false, $page = false) {
        $response = false;
        $moduleFields = [
            'id',
            'varTitle',
            'intFKCategory',
            'txtDescription',
            'dtDateTime',
            'dtEndDateTime',
            'chrPublish',
            'chrDraft',
            'chrTrash',
            'intSearchRank',
            'FavoriteID',
            'created_at',
            'updated_at'
        ];
        $aliasFields = ['id', 'varAlias'];
        $response = Cache::tags(['Brand'])->get('getFrontBrandList_' . $page);
        if (empty($response)) {
            $response = Self::getFrontRecords($moduleFields, $aliasFields)
                    ->deleted()
                    ->publish()
                    // ->dateRange()
                    ->where('chrTrash', '!=', 'Y')
                    ->where('chrDraft', '!=', 'D')
                    ->where('chrMain', 'Y')
                    // ->orderBy('id', 'DESC')
                    // ->orderBy('intFKCategory', 'ASC')
                    ->orderBy('intDisplayOrder', 'ASC')
                    ->paginate($paginate);
            // if ($catid != false) {
            //     $response = $response->where('intFKCategory', '=', $catid);
            // }
            // $response = $response->get();
            Cache::tags(['Brand'])->forever('getFrontBrandList_' . $page, $response);
        }
        return $response;
    }

    public static function getFront_List($filterArr = false, $page = 1, $catid = false, $print = false, $categoryid, $name = "", $start_date_time = "", $end_date_time = "") {
        $response = false;
        $moduleFields = [
            'id',
            'varTitle',
            'intFKCategory',
            'txtDescription',
            'dtDateTime',
            'dtEndDateTime',
            'chrPublish',
            'chrDraft',
            'chrTrash',
            'intSearchRank',
            'FavoriteID',
            'created_at',
            'updated_at'
        ];
        $aliasFields = ['id', 'varAlias'];
        // $response = Cache::tags(['Brand'])->get('getFrontBrandList_' . $page . '_' . $catid);
        if (empty($response)) {
            $response = Self::getFrontRecords($moduleFields, $aliasFields)
                    ->deleted()
                    ->publish();
            if ($categoryid != '') {
                $response = $response->whereRaw(DB::raw('intFKCategory="' . $categoryid . '"'));
            }
            if ($name != '') {
                $response = $response->where('varTitle', 'like', '%' . '' . $name . '' . '%');
            }
            if ($start_date_time != '' && $end_date_time != '') {
                $response = $response->whereRaw('(dtDateTime>="' . $start_date_time . '" AND (dtDateTime<="' . $end_date_time . '"))');
            } else if ($start_date_time != '') {
                $response = $response->whereRaw('dtDateTime>="' . $start_date_time . '"')->dateRange();
            } else if ($end_date_time != '') {
                $response = $response->whereRaw('dtDateTime<="' . $end_date_time . '"')->dateRange();
            } else {
                $response = $response->dateRange();
            }
            $response = $response->where('chrTrash', '!=', 'Y')
                    ->where('chrDraft', '!=', 'D')
                    ->orderBy('dtDateTime', 'DESC')
                    ->where('chrMain', 'Y');
            if ($catid != false) {
                $response = $response->where('intFKCategory', '=', $catid);
            }
            if ($print == 'print') {
                $response = $response->get();
            } else {
                $response = $response->paginate($page);
            }
            // Cache::tags(['Brand'])->forever('getFrontBrandList_' . $page . '_' . $catid, $response);
        }
        return $response;
    }  

    /**
     * This method handels retrival of news records
     * @return  Object
     * @since   2016-07-14
     * @author  NetQuick
     */
    public static function getFrontRecords($moduleFields = false, $aliasFields = false) {
        $data = [];
        if ($aliasFields != false) {
            $data = [
                'alias' => function ($query) use ($aliasFields) {
                    $query->select($aliasFields);
                },
            ];
        }
        return self::select($moduleFields)->with($data);
    }

    /**
     * This method handels retrival of front blog detail
     * @return  Object
     * @since   2017-10-13
     * @author  NetQuick
     */
    public static function getFrontDetail($aliasID) {
        $response = false;
        $response = Cache::tags(['Brand'])->get('getBrandRecordIdByAliasID_' . $aliasID);
        if (empty($response)) {
            $moduleFields = [
                'id',
                'intFKCategory',
                'varTitle',
                'txtDescription',
                'chrDraft',
                'chrTrash',
                'intSearchRank',
                'FavoriteID',
                'created_at',
                'updated_at'
            ];
            $response = Self::Select($moduleFields)
                    ->deleted()
                    ->publish()
                    ->where('fkMainRecord', 0)
                    ->checkAliasId($aliasID)
                    ->first();
            Cache::tags(['Brand'])->forever('getBrandRecordIdByAliasID_' . $aliasID, $response);
        }
        return $response;
    }

    /**
     * This method handels backend records
     * @return  Object
     * @since   2016-07-14
     * @author  NetQuick
     */
    public static function getPowerPanelRecords($moduleFields = false, $brandCatfileds = false, $moduleCode = false) {
        $data = [];
        $response = false;
        $response = self::select($moduleFields);
        if ($brandCatfileds != false) {
            $data['brandcat'] = function ($query) use ($brandCatfileds) {
                $query->select($brandCatfileds)->publish();
            };
        }
        if (count($data) > 0) {
            $response = $response->with($data);
        }
        return $response;
    }

    public function brandcat() {
        $response = false;
      
        return $response;
    }

    public function alias() {
        $response = false;
        $response = $this->belongsTo('App\Alias', 'intAliasId', 'id');
        return $response;
    }

    /**
     * This method handels retrival of backend record list
     * @return  Object
     * @since   2017-10-24
     * @author  NetQuick
     */
    public static function getRecordList($filterArr = false, $isAdmin = false) {
        $response = false;
        $brandCatfileds = ['id', 'varTitle'];
        $moduleFields = [
            'id',
            'intFKCategory',
            'varTitle',
            'txtDescription',
            'intDisplayOrder',
            'chrPublish',
            'chrMain',
            'dtDateTime',
            'dtEndDateTime',
            'chrAddStar',
            'chrDraft',
            'chrTrash',
            'intSearchRank',
            'FavoriteID',
            'LockUserID', 'chrLock',
            'created_at',
            'updated_at'
        ];
        $response = Self::getPowerPanelRecords($moduleFields)
                ->deleted();
        $response = $response->filter($filterArr)
                ->where('chrTrash', '!=', 'Y')
                ->checkMainRecord('Y')
                ->get();
        return $response;
    }


    public static function getCatWithParent()
    {
        $response             = false;
        $categoryFields       = [ 'id', 'varTitle'];
        $parentCategoryFields = ['id', 'varTitle'];
        $response             = Self::getPowerPanelRecords($categoryFields, false, $parentCategoryFields)
            					->deleted()
            					->publish()
                                ->orderBy('intDisplayOrder','asc')
            					->get();
        return $response;
    }


    /**
     * This method handels retrival of backend record list
     * @return  Object
     * @since   2017-10-24
     * @author  NetQuick
     */
    public static function getRecordListFavorite($filterArr = false, $isAdmin = false) {
        $response = false;
        $brandCatfileds = ['id', 'varTitle'];
        $userid = auth()->user()->id;
        $moduleFields = [
            'id',
            'intFKCategory',
            'varTitle',
            'txtDescription',
            'intDisplayOrder',
            'chrPublish',
            'chrMain',
            'dtDateTime',
            'dtEndDateTime',
            'chrAddStar',
            'chrDraft',
            'chrTrash',
            'intSearchRank',
            'FavoriteID',
            'LockUserID', 'chrLock',
            'created_at',
            'updated_at'
        ];
        $response = Self::getPowerPanelRecords($moduleFields, $brandCatfileds)
                ->deleted()
                ->filter($filterArr)
                ->checkMainRecord('Y')
                ->where('chrTrash', '!=', 'Y')
                ->whereRaw("find_in_set($userid,FavoriteID)");
        $response = $response->get();
        return $response;
    }

    /**
     * This method handels retrival of backend record list
     * @return  Object
     * @since   2017-10-24
     * @author  NetQuick
     */
    public static function getRecordListDraft($filterArr = false, $isAdmin = false) {
        $response = false;
        $brandCatfileds = ['id', 'varTitle'];
        $moduleFields = [
            'id',
            'intFKCategory',
            'varTitle',
            'txtDescription',
            'intDisplayOrder',
            'chrPublish',
            'chrMain',
            'dtDateTime',
            'dtEndDateTime',
            'chrAddStar',
            'chrDraft',
            'chrTrash',
            'intSearchRank',
            'FavoriteID',
            'LockUserID', 'chrLock',
            'created_at',
            'updated_at'
        ];
        $response = Self::getPowerPanelRecords($moduleFields, $brandCatfileds)
                ->deleted()
                ->filter($filterArr)
                ->checkMainRecord('Y')
                ->where('chrDraft', 'D')
                ->where('chrTrash', '!=', 'Y');
        $response = $response->get();
        return $response;
    }

    /**
     * This method handels retrival of backend record list
     * @return  Object
     * @since   2017-10-24
     * @author  NetQuick
     */
    public static function getRecordListTrash($filterArr = false, $isAdmin = false) {
        $response = false;
        $brandCatfileds = ['id', 'varTitle'];
        $moduleFields = [
            'id',
            'intFKCategory',
            'varTitle',
            'txtDescription',
            'intDisplayOrder',
            'chrPublish',
            'chrMain',
            'dtDateTime',
            'dtEndDateTime',
            'chrAddStar',
            'chrDraft',
            'chrTrash',
            'intSearchRank',
            'FavoriteID',
            'LockUserID', 'chrLock',
            'created_at',
            'updated_at'
        ];
        $response = Self::getPowerPanelRecords($moduleFields, $brandCatfileds)
                ->deleted()
                ->filter($filterArr)
                ->checkMainRecord('Y')
                ->where('chrTrash', 'Y');
        $response = $response->get();
        return $response;
    }

    public static function getRecordList_tab1($filterArr = false) {
        $response = false;
        $moduleFields = [
            'id',
            'intFKCategory',
            'varTitle',
            'txtDescription',
            'intDisplayOrder',
            'chrPublish',
            'fkMainRecord',
            'chrAddStar',
            'dtDateTime',
            'dtEndDateTime',
            'chrDraft',
            'chrTrash',
            'intSearchRank',
            'FavoriteID',
            'LockUserID', 'chrLock',
            'created_at',
            'updated_at'
        ];
        $brandCatfileds = ['id', 'varTitle'];
        $MainIDs = Self::distinct()
                ->select("fkMainRecord")
                ->checkMainRecord('N')
                ->groupBy('fkMainRecord')
                ->deleted()
                ->get()
                ->toArray();
        $MainIDs = array_column($MainIDs, 'fkMainRecord');
        $response = Self::getPowerPanelRecords($moduleFields, $brandCatfileds)
                ->deleted()
                ->where('chrAddStar', 'Y')
                ->filter($filterArr)
                ->whereIn('id', $MainIDs)
                ->where('chrTrash', '!=', 'Y')
                ->checkStarRecord('Y')
                ->get();
        return $response;
    }

    /**
     * This method handels retrival of record by id
     * @return  Object
     * @since   2017-10-16
     * @author  NetQuick
     */
    public static function getRecordById($id, $ignoreDeleteScope = false) {
        $response = false;
        $moduleFields = [
            'id',
            'intFKCategory',
            'varTitle',
            'txtDescription',
            'intSearchRank',
            'intDisplayOrder',
            'dtDateTime',
            'dtEndDateTime',
            'dtDateTime',
            'dtEndDateTime',
            'chrPublish',
            'fkMainRecord',
            'UserID',
            'chrDraft',
            'chrTrash',
            'intSearchRank',
            'FavoriteID',
            'LockUserID', 'chrLock',
            'created_at',
            'updated_at'
        ];
        $response = Self::getPowerPanelRecords($moduleFields);
        if (!$ignoreDeleteScope) {
            $response = $response->deleted();
        }
        $response = $response->checkRecordId($id)
                ->first();
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
        $moduleFields = [
            'id',
            'intFKCategory',
            'varTitle',
            'txtDescription',
            'intSearchRank',
            'intDisplayOrder',
            'dtDateTime',
            'dtEndDateTime',
            'chrPublish',
            'fkMainRecord',
            'UserID',
            'chrDraft',
            'chrTrash',
            'intSearchRank',
            'FavoriteID',
            'LockUserID', 'chrLock',
            'created_at',
            'updated_at'
        ];
        $response = Self::getPowerPanelRecords($moduleFields)
                ->deleted()
                ->checkRecordId($id)
                ->first();
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
                ->count();
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
                    ->checkMainRecord('Y')
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
     * @since   2016-07-20
     * @author  NetQuick
     */
    public function scopeOrderCheck($query, $order) {
        return $query->where('intDisplayOrder', $order);
    }

    /**
     * This method handels publish scope
     * @return  Object
     * @since   2016-07-20
     * @author  NetQuick
     */
    public function scopePublish($query) {
        return $query->where(['chrPublish' => 'Y']);
    }

    /**
     * This method handels delete scope
     * @return  Object
     * @since   2016-07-20
     * @author  NetQuick
     */
    public function scopeDeleted($query) {
        return $query->where(['chrDelete' => 'N']);
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
       
        if (!empty($filterArr['searchFilter']) && $filterArr['searchFilter'] != ' ') {
            $data = $query->where('varTitle', 'like', "%" . $filterArr['searchFilter'] . "%");
        }
         if (isset($filterArr['ignore']) && !empty($filterArr['ignore'])) {
            $data = $query->whereNotIn('id', $filterArr['ignore']);
        }
        if (!empty($filterArr['rangeFilter']['from']) && $filterArr['rangeFilter']['to']) {
            $data = $query->whereRaw('DATE(dtDateTime) BETWEEN "' . date('Y-m-d', strtotime(str_replace('/', '-', $filterArr['rangeFilter']['from']))) . '" AND "' . date('Y-m-d', strtotime(str_replace('/', '-', $filterArr['rangeFilter']['to']))) . '"');
        }
        if (!empty($query)) {
            $response = $query;
        }
        return $response;
    }

    public static function getRecordCount($filterArr = false, $returnCounter = false) {
        $response = 0;
        $cmsPageFields = ['id'];
        $pageQuery = Self::getPowerPanelRecords($cmsPageFields);
        if ($filterArr != false) {
            $pageQuery = $pageQuery->filter($filterArr, $returnCounter);
        }
        $response = $pageQuery
                ->deleted()
                ->where('chrMain', 'Y')
                ->count();
        return $response;
    }

    /**
     * This method handels retrival of record count
     * @return  Object
     * @since   2018-10-08
     * @author  NetQuick
     */
    public static function getRecordCountforList($filterArr = false, $returnCounter = false, $isAdmin = false) {
        $response = 0;
        $cmsPageFields = ['id'];
        $response = Self::getPowerPanelRecords($cmsPageFields);
        if ($filterArr != false) {
            $response = $response->filter($filterArr, $returnCounter);
        }
        $response = $response->deleted()
                ->checkMainRecord('Y')
                ->where('chrTrash', '!=', 'Y')
                ->count();
        return $response;
    }

    public static function getRecordCountListApprovalTab($filterArr = false) {
        $response = false;
        $MainIDs = Self::distinct()
                ->select("fkMainRecord")
                ->where('fkMainRecord', '!=', '0')
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

    public static function getNewRecordsCount() {
        $response = false;
        $MainIDs = Self::distinct()
                ->select("fkMainRecord")
                ->where('fkMainRecord', '!=', '0')
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

    public static function getChildGrid() {
        $brandCatfileds = ['id', 'varTitle'];
        $id = $_REQUEST['id'];
        $response = false;
        $moduleFields = ['id', 'intFKCategory', 'varTitle', 'txtDescription', 'dtDateTime', 'dtEndDateTime', 'intDisplayOrder', 'chrPublish', 'fkMainRecord','dtApprovedDateTime','created_at', 'chrApproved', 'updated_at', 'intApprovedBy', 'UserID', 'chrDraft', 'chrTrash', 'intSearchRank', 'FavoriteID', 'created_at', 'updated_at'];
        $response = Self::getPowerPanelRecords($moduleFields, $brandCatfileds)
                ->deleted()
                ->where('chrMain', 'N')
                ->where('fkMainRecord', $id)
                ->orderBy('created_at', 'desc')
                ->get();
        return $response;
    }

    public static function getChildrollbackGrid() {
        $brandCatfileds = ['id', 'varTitle'];
        $id = $_REQUEST['id'];
        $response = false;
        $moduleFields = ['id', 'intFKCategory', 'varTitle', 'txtDescription', 'intDisplayOrder', 'chrPublish', 'dtDateTime', 'dtEndDateTime', 'fkMainRecord', 'created_at', 'chrApproved', 'updated_at', 'intApprovedBy', 'UserID', 'chrDraft', 'chrTrash', 'intSearchRank', 'FavoriteID', 'created_at', 'updated_at'];
        $response = Self::getPowerPanelRecords($moduleFields, $brandCatfileds)
                ->deleted()
                ->where('chrMain', 'N')
                ->where('chrRollBack', 'Y')
                ->where('fkMainRecord', $id)
                ->orderBy('created_at', 'desc')
                ->get();
        return $response;
    }

    public static function approved_data_Listing($request) {
        $id = $request->id;
        $main_id = $request->main_id;
        // $PUserid = $request->PUserid;
        //Select Child Record Data Start
        $brandCatfileds = ['id', 'varTitle'];
        $response = false;
        $moduleFields = [
            'id',
            'intFKCategory',
            'varTitle',
            'txtDescription',
            'intSearchRank',
            'intDisplayOrder',
            'dtDateTime',
            'dtEndDateTime',
            'chrPublish',
            'chrDraft',
            'chrTrash',
            'intSearchRank',
            'FavoriteID',
            'created_at',
            'updated_at'
        ];
        $aliasFields = ['id', 'varAlias'];
        $response = Self::getPowerPanelRecords($moduleFields, $brandCatfileds)
                ->deleted()
                ->where('chrMain', 'N')
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
            'txtDescription' => $response['txtDescription'],
            'chrAddStar' => 'N',
            'intSearchRank' => $response['intSearchRank'],
            'dtDateTime' => $response['dtDateTime'],
            'dtEndDateTime' => $response['dtEndDateTime'],
            'chrDraft' => $response['chrDraft'],
            'intSearchRank' => $response['intSearchRank'],
            'FavoriteID' => $response['FavoriteID'],
            'chrPublish' => $response['chrPublish'],
        ];
        CommonModel::updateRecords($whereConditions, $updateMainRecord, false, 'Powerpanel\Brand\Models\Brand');
        //Update Copy Child Record To Main Record end
        $whereConditions_ApproveN = ['fkMainRecord' => $main_id];
        $updateToApproveN = [
            'chrApproved' => 'N',
            'chrLetest' => 'N',
            'intApprovedBy' => '0',
        ];
        CommonModel::updateRecords($whereConditions_ApproveN, $updateToApproveN, false, 'Powerpanel\Brand\Models\Brand');
        $whereConditionsApprove = ['id' => $id, 'chrMain' => 'N'];
        $updateToApprove = [
            'chrApproved' => 'Y',
            'chrRollBack' => 'Y',
            'intApprovedBy' => auth()->user()->id
        ];
        CommonModel::updateRecords($whereConditionsApprove, $updateToApprove, false, 'Powerpanel\Brand\Models\Brand');
        $msg_show = "Record successfully approved.";
        return $msg_show;
    }

    public static function getCountById($categoryId = null) {
        $response = false;
        $moduleFields = ['id'];
        $response = Self::getPowerPanelRecords($moduleFields)
                ->checkCategoryId($categoryId)
                ->where('chrMain', 'Y')
                ->deleted()
                ->count();
        return $response;
    }

    /**
     * This method handels retrival of backend record list
     * @return  Object
     * @since   2017-10-24
     * @author  NetQuick
     */
    public static function getFrontSearchRecordById($id, $moduleCode) {
        $categoryModuleCode = Modules::getModule('brand-category')->id;
        $filter = [];
        $moduleFields = [
            'id',
            'intFKCategory'
        ];
        $catFileds = ['id', 'intAliasId'];
        $aliasFields = ['id', 'varAlias'];
        $response = Self::getSearchRecords($moduleFields, $catFileds, $aliasFields, $moduleCode, $categoryModuleCode)
                ->deleted()
                ->where('id', $id)
                ->first();
        return $response;
    }

    public static function getSearchRecords($moduleFields, $catFileds, $aliasFields, $moduleCode, $categoryModuleCode) {
        $data = [];
        $response = false;
        $response = self::select($moduleFields);
        if ($aliasFields != false) {
            $data['alias'] = function ($query) use ($aliasFields, $moduleCode) {
                $query->select($aliasFields)->checkModuleCode($moduleCode);
            };
        }
        if ($catFileds != false) {
            $data['cat'] = function ($query) use ($catFileds, $aliasFields, $categoryModuleCode) {
                $joinArr = [];
                $joinArr['alias'] = function ($query) use ($aliasFields, $categoryModuleCode) {
                    $query->select($aliasFields);
                };
                $query->select($catFileds)->with($joinArr);
            };
        }
        if (count($data) > 0) {
            $response = $response->with($data);
        }
        return $response;
    }

    public function cat() {
        $response = false;
        $response = $this->belongsTo('App\BrandCategory', 'intFKCategory', 'id');
        return $response;
    }

    public function scopeCheckCategoryId($query, $id) {
        $response = false;
        $response = $query->where('intFKCategory', 'like', '%' . $id . '%')->orWhere('intFKCategory', 'like', '%' . $id . '%');
        return $response;
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

    public function scopeDateRange($query) {
        $response = false;
        $response = $query->whereRaw('((dtEndDateTime >= NOW() AND NOW() >= dtDateTime) OR (NOW() >= dtDateTime and dtEndDateTime is null))');
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
                ->whereNotIn('id', $ignoreId)
                ->where('chrDraft', 'D')
                ->where('chrTrash', '!=', 'Y')
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
                ->whereNotIn('id', $ignoreId)
                ->where('chrTrash', 'Y')
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
                ->where('chrTrash', '!=', 'Y')
                ->whereNotIn('id', $ignoreId)
                ->whereRaw("find_in_set($userid,FavoriteID)")
                ->count();
        return $response;
    }

    //End Favorite Count of Records 
    
     public static function getBuilderRecordList($filterArr = []) {
        $response = false;
          $moduleFields = [
            'id',
            'intFKCategory',
            'varTitle',
            'txtDescription',
            'intSearchRank',
            'intDisplayOrder',
            'dtDateTime',
            'dtEndDateTime',
            'chrPublish',
            'chrDraft',
            'chrTrash',
            'intSearchRank',
            'FavoriteID',
            'created_at',
            'updated_at'
        ];
        $response = Self::getPowerPanelRecords($moduleFields, false, false, false, false)
                ->filter($filterArr);
        $response = $response->where('chrPublish', 'Y')
                ->where('chrDelete', 'N')
                ->where('chrMain', 'Y')
                ->where('chrTrash', '!=', 'Y')
                ->where('chrDraft', '!=', 'D')
                ->groupBy('id')
                ->get();
        return $response;
    }
    
     public static function getBuilderBrand($recIds) {
          $response = false;
       
        $moduleFields = [
            'id',
            'intFKCategory',
            'varTitle',
            'txtDescription',
            'intSearchRank',
            'intDisplayOrder',
            'dtDateTime',
            'dtEndDateTime',
            'chrPublish',
            'chrDraft',
            'chrTrash',
            'intSearchRank',
            'FavoriteID',
            'created_at',
            'updated_at'
        ];
          $aliasFields = ['id', 'varAlias'];
        if (empty($response)) {
            $response = Self::getFrontRecords($moduleFields, $aliasFields)
                    ->whereIn('id', $recIds)
                    ->deleted()
                    ->publish()
                    ->where('chrMain', 'Y')
                    ->where('chrTrash', '!=', 'Y')
                    ->where('chrDraft', '!=', 'D')
                    ->orderByRaw(DB::raw("FIELD(id, " . implode(',', $recIds) . " )"))
                    ->paginate(6);
        }
        return $response;
    }
    
    
      public static function getAllBrands($brandcat, $limit, $sdate, $edate) {

        $response = false;
        $moduleFields = [
            'id',
            'intFKCategory',
            'varTitle',
            'txtDescription',
            'intSearchRank',
            'intDisplayOrder',
            'dtDateTime',
            'dtEndDateTime',
            'chrPublish',
            'chrDraft',
            'chrTrash',
            'intSearchRank',
            'FavoriteID',
            'created_at',
            'updated_at'
        ];
        $aliasFields = ['id', 'varAlias'];
        if (empty($response)) {
            $response = Self::getFrontRecords($moduleFields, $aliasFields)
                    ->deleted()
                    ->publish();
            if ($brandcat != '') {
                $response = $response->where('intFKCategory', $brandcat);
            }

            if ($sdate != '' && $edate != '') {
                $response = $response->whereRaw('(DATE(dtDateTime)>="' . date('Y-m-d', strtotime(str_replace('/', '-', $sdate))) . '" AND (DATE(dtDateTime)<="' . date('Y-m-d', strtotime(str_replace('/', '-', $edate))) . '") OR ("' . date('Y-m-d', strtotime(str_replace('/', '-', $sdate))) . '" >= dtDateTime and dtEndDateTime is null))');
            } else if ($sdate != '') {
                $response = $response->whereRaw('DATE(dtDateTime)>="' . date('Y-m-d', strtotime(str_replace('/', '-', $sdate))) . '"');
            } else if ($edate != '') {
                $response = $response->whereRaw('DATE(dtDateTime)<="' . date('Y-m-d', strtotime(str_replace('/', '-', $edate))) . '"');
            }
            $response = $response->where('chrMain', 'Y')
                    ->where('chrTrash', '!=', 'Y')
                    ->where('chrDraft', '!=', 'D')
                    ->orderBy('intDisplayOrder', 'ASC');
            if ($limit != '') {
                $response = $response->limit($limit);
            }
            $response = $response->paginate(30);
        }
        return $response;

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

}
