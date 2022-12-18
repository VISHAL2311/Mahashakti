<?php
namespace Powerpanel\ServicesCategory\Models;

use Cache;
use Illuminate\Database\Eloquent\Model;

class ServiceCategory extends Model
{
    protected $table    = 'service_category';
    protected $fillable = [
        'id',
        'intAliasId',
        'varTitle',
        'txtShortDescription',
        'txtDescription',
        'intParentCategoryId',
        'intDisplayOrder',
        'varMetaTitle',
        'varMetaKeyword',
        'varMetaDescription',
        'chrPublish',
        'chrDelete',
        'created_at',
        'updated_at',
    ];

    /**
     * This method handels retrival of record by id
     * @return  Object
     * @since   2017-10-16
     * @author  NetQuick
     */
    public static function getRecordByIds($ids)
    {
        $response       = false;
        $categoryFields = ['id', 'varTitle', 'intAliasId'];
        $aliasFields    = ['id', 'varAlias'];
        $response       = Self::getFrontRecords($categoryFields, $aliasFields)
            ->publish()
            ->deleted()
            ->checkRecordIds($ids)
            ->get();
        return $response;
    }

    /**
     * This method handels retrival of front blog detail
     * @return  Object
     * @since   2017-10-13
     * @author  NetQuick
     */
    public static function getRecordIdByAliasID($aliasID)
    {
        $response = false;
        $response = Cache::tags(['ServiceCategory'])->get('getServiceCatRecordIdByAliasID' . $aliasID);
        if (empty($response)) {
            $response = Self::Select('id')->deleted()->publish()->checkAliasId($aliasID)->first();
            Cache::tags(['ServiceCategory'])->forever('getServiceCatRecordIdByAliasID' . $aliasID, $response);
        }
        return $response;
    }

    /**
     * This method handels retrival of front blog detail
     * @return  Object
     * @since   2017-10-13
     * @author  NetQuick
     */
    public static function getTitleByAliasID($aliasID)
    {
        $response = false;
        if (!empty($aliasID)) {
            $moduleFields = ['id', 'intAliasId', 'varTitle'];
            $aliasFields  = ['id', 'varAlias'];
            $response     = Self::getFrontRecords($moduleFields, $aliasFields)
                ->deleted()
                ->publish()
                ->checkAliasIds($aliasID)
                ->get();
        }

        return $response;
    }

    /**
     * This method handels retrival of front latest service list
     * @return  Object
     * @since   2017-10-13
     * @author  NetQuick
     */
    public static function getFrontList()
    {
        $response      = false;
        $serviceFields = ['id', 'varTitle'];
        $response      = Cache::tags(['ServiceCategory'])->get('getFrontServiceCatList');
        if (empty($response)) {
            $response = Self::getFrontRecords($serviceFields)
                ->deleted()
                ->publish()
                ->get()
                ->pluck('varTitle', 'id');
            Cache::tags(['ServiceCategory'])->forever('getFrontServiceCatList', $response);
        }
        return $response;
    }

    /**
     * This method handels retrival of front latest service list
     * @return  Object
     * @since   2017-10-13
     * @author  NetQuick
     */
    public static function getCategoryList($paginate = 6, $page)
    {

        $response     = false;
        $moduleFields = ['id', 'intAliasId', 'varTitle', 'intParentCategoryId', 'intDisplayOrder', 'txtShortDescription', 'txtDescription'];
        $aliasFields  = ['id', 'varAlias'];
        $response     = Cache::tags(['ServiceCategory'])->get('getCategoryList_' . $page);
        if (empty($response)) {
            $response = Self::getFrontRecords($moduleFields, $aliasFields)
                ->deleted()
                ->publish()
                ->where('intParentCategoryId', 0)
                ->orderBy('intDisplayOrder', 'ASC')
                ->paginate($paginate);
            Cache::tags(['ServiceCategory'])->forever('getCategoryList_' . $page, $response);
        }
        return $response;
    }

    /**
     * This method handels retrival of front latest service list
     * @return  Object
     * @since   2017-10-13
     * @author  NetQuick
     */
    public static function getSubCategoryList($categoryId, $paginate = 6, $page)
    {
        $response     = false;
        $moduleFields = ['id', 'intAliasId', 'varTitle', 'intParentCategoryId', 'intDisplayOrder', 'txtShortDescription', 'txtDescription'];
        $aliasFields  = ['id', 'varAlias'];
        $response     = Cache::tags(['ServiceCategory'])->get('getCategoryList_' . $page);
        if (empty($response)) {
            $response = Self::getFrontRecords($moduleFields, $aliasFields)
                ->deleted()
                ->publish()
                ->where('intParentCategoryId', $categoryId)
                ->orderBy('intDisplayOrder', 'ASC')
                ->paginate($paginate, ['*'], 'categoryPage');
            Cache::tags(['ServiceCategory'])->forever('getCategoryList_' . $page, $response);
        }
        return $response;
    }

    /**
     * This method handels retrival of front service detail
     * @return  Object
     * @since   2017-10-13
     * @author  NetQuick
     */
    public static function getFrontDetail($id)
    {
        $response     = false;
        $moduleFields = ['id', 'intAliasId', 'varTitle', 'intParentCategoryId', 'intDisplayOrder', 'txtShortDescription', 'txtDescription', 'varMetaTitle', 'varMetaKeyword', 'varMetaDescription'];
        $aliasFields  = ['id', 'varAlias'];
        $response     = Cache::tags(['ServiceCategory'])->get('getServiceCategoryFrontDetail_' . $id);
        if (empty($response)) {
            $response = Self::getFrontRecords($moduleFields, $aliasFields)
                ->deleted()
                ->publish()
                ->checkAliasId($id)
                ->first();
            Cache::tags(['ServiceCategory'])->forever('getServiceCategoryFrontDetail_' . $id, $response);
        }
        return $response;
    }

    /**
     * This method handels retrival of service records
     * @return  Object
     * @since   2016-07-14
     * @author  NetQuick
     */
    public static function getFrontRecords($moduleFields = false, $aliasFields = false)
    {
        $response = false;
        $data     = [];
        if ($aliasFields != false) {
            $data = ['alias' => function ($query) use ($aliasFields) {$query->select($aliasFields);}];
        }
        $response = self::select($moduleFields)->with($data);

        return $response;
    }
    /**
     * This method handels alias relation
     * @return  Object
     * @since   2016-07-14
     * @author  NetQuick
     */
    public function alias()
    {
        $response = false;
        $response = $this->belongsTo('App\Alias', 'intAliasId', 'id');
        return $response;
    }
    /**
     * This method handels service-category relation
     * @return  Object
     * @since   2016-07-14
     * @author  NetQuick
     */
    public function services()
    {
        $response = false;
        $response = $this->hasOne('App\Services', 'id', 'intCategoryId');
        return $response;
    }
    /**
     * This method handels service sub-category relation
     * @return  Object
     * @since   2016-07-14
     * @author  NetQuick
     */
    public function serviceCategory()
    {
        $response = false;
        $response = $this->hasOne('Powerpanel\ServicesCategory\Models\ServiceCategory', 'id', 'intParentCategoryId');
        return $response;
    }
    /**
     * This method handels main category relation
     * @return  Object
     * @since   2016-07-14
     * @author  NetQuick
     */
    public function parentCategory()
    {
        $response = false;
        $response = $this->belongsTo('Powerpanel\ServicesCategory\Models\ServiceCategory', 'intParentCategoryId', 'id');
        return $response;
    }
    /**
     * This method handels retrival of records
     * @return  Object
     * @since   2016-07-14
     * @author  NetQuick
     */
    public static function getRecords($moduleId = false)
    {
        $response = false;
        $response = self::with([
            'alias' => function ($query) use ($moduleId) {
                $query->checkModuleCode($moduleId);
            }, 'parentCategory']);
        return $response;
    }
    /**
     * This method handels backend records
     * @return  Object
     * @since   2016-07-14
     * @author  NetQuick
     */
    public static function getPowerPanelRecords($moduleFields = false, $aliasFields = false,  $parentCategoryFields = false, $moduleCode = false)
    {
        $data     = [];
        $response = false;
        $response = self::select($moduleFields);
        if ($aliasFields != false) {
            $data['alias'] = function ($query) use ($aliasFields) {
                $query->select($aliasFields)->checkModuleCode();
            };
        }
        if ($parentCategoryFields != false) {
            $data['parentCategory'] = function ($query) use ($parentCategoryFields) {
                $query->select($parentCategoryFields);
            };
        }
        if (count($data) > 0) {
            $response = $response->with($data);
        }
        return $response;
    }
    /**
     * This method handels retrival of backend record list
     * @return  Object
     * @since   2017-10-24
     * @author  NetQuick
     */
    public static function getRecordList($filterArr = false)
    {
        $response     = false;
        $moduleFields = ['id', 'varTitle', 'intAliasId', 'intParentCategoryId', 'intDisplayOrder', 'txtShortDescription', 'txtDescription', 'varMetaTitle', 'varMetaKeyword', 'varMetaDescription', 'chrPublish'];
        $response     = Self::getPowerPanelRecords($moduleFields)
            ->deleted()
            ->filter($filterArr)
            ->get();
        return $response;
    }
    /**
     * This method handels retrival of Parent Category record by id
     * @return  Object
     * @since   2017-10-16
     * @author  NetQuick
     */
    public static function getParentCategoryNameBycatId($ids)
    {
        $response       = false;
        $categoryFields = ['varTitle'];
        $response       = Self::getPowerPanelRecords($categoryFields)->deleted()->whereIn('id', $ids)->get();
        return $response;
    }
    /**
     * This method handels retrival of record by id
     * @return  Object
     * @since   2017-10-16
     * @author  NetQuick
     */
    public static function getRecordById($id)
    {
        $response     = false;
        $moduleFields = ['id', 'varTitle', 'intAliasId', 'intParentCategoryId', 'intDisplayOrder', 'txtShortDescription', 'txtDescription', 'varMetaTitle', 'varMetaKeyword', 'varMetaDescription', 'chrPublish'];
        $aliasFields  = ['id', 'varAlias'];
        $response     = Self::getPowerPanelRecords($moduleFields, $aliasFields)
                        ->deleted()
                        ->checkRecordId($id)
                        ->first();
        return $response;
    }

     /**
     * This method handels retrival of record by id
     * @return  Object
     * @since   2017-10-16
     * @author  NetQuick
     */
    public static function getParentCategory($id)
    {
        $response     = false;
        $moduleFields = ['id', 'varTitle', 'intAliasId', 'intParentCategoryId'];
        $aliasFields  = ['id', 'varAlias'];
        $response     = Self::getPowerPanelRecords($moduleFields, $aliasFields)
                        ->deleted()
                        ->where('intParentCategoryId',$id)
                        ->first();
        return $response;
    }

    /**
     * This method handels retrival of category Record
     * @return  Object
     * @since   2017-10-24
     * @author  NetQuick
     */
    public static function getCategories()
    {
        $response     = false;
        $moduleFields = ['id', 'varTitle', 'intParentCategoryId','intAliasId'];
        $aliasFields  = ['id', 'varAlias'];
        $response     = Self::getFrontRecords($moduleFields,$aliasFields)
                        ->deleted()
                        ->publish()
                        ->orderBy('varTitle', 'ASC');
        return $response;
    }
    /**
     * This method handels retrival of record by id for Log Manage
     * @return  Object
     * @since   2017-10-16
     * @author  NetQuick
     */
    public static function getRecordForLogById($id)
    {
        $response     = false;
        $moduleFields = ['id', 'varTitle', 'intParentCategoryId', 'intDisplayOrder', 'txtShortDescription', 'txtDescription', 'varMetaTitle', 'varMetaKeyword', 'varMetaDescription', 'chrPublish'];
        $response     = Self::getPowerPanelRecords($moduleFields)->deleted()->checkRecordId($id)->first();
        return $response;
    }
    /**
     * This method handels retrival of record count
     * @return  Object
     * @since   2017-10-16
     * @author  NetQuick
     */
    protected static $fetchedOrder    = [];
    protected static $fetchedOrderObj = null;
    public static function getRecordByOrder($order = false)
    {
        $response     = false;
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
    public static function getCatWithParent()
    {
        $response             = false;
        $categoryFields       = ['id', 'intParentCategoryId', 'varTitle'];
        $parentCategoryFields = ['id', 'varTitle'];
        $response             = Self::getPowerPanelRecords($categoryFields, false, $parentCategoryFields)
            					->deleted()
            					->publish()
            					->get();
        return $response;
    }
    /**
     * This method handels retrival of record count based on category
     * @return  Object
     * @since   2018-01-09
     * @author  NetQuick
     */
    public static function getCountById($categoryId = null)
    {
        $response     = false;
        $moduleFields = ['id'];
        $response     = Self::getPowerPanelRecords($moduleFields)
            ->checkCategoryId($categoryId)
            ->deleted()
            ->count();
        return $response;
    }
    /**
     * This method handels category id scope
     * @return  Object
     * @since   2018-01-09
     * @author  NetQuick
     */
    public function scopeCheckCategoryId($query, $id)
    {
        $response = false;
        $response = $query->where('intParentCategoryId', $id);
        return $response;
    }
    /**
     * This method handels alias id scope
     * @return  Object
     * @since   2016-07-24
     * @author  NetQuick
     */
    public function scopeCheckAliasId($query, $id)
    {
        $response = false;
        $response = $query->where('intAliasId', $id);
        return $response;
    }

    /**
     * This method handels alias id scope
     * @return  Object
     * @since   2016-07-24
     * @author  NetQuicks
     */
    public function scopeCheckAliasIds($query, $ids)
    {
        $response = false;
        $response = $query->whereIn('intAliasId', $ids);
        return $response;
    }

    /**
     * This method handels record id scope
     * @return  Object
     * @since   2016-07-24
     * @author  NetQuick
     */
    public function scopeCheckRecordId($query, $id)
    {
        $response = false;
        $response = $query->where('id', $id);
        return $response;
    }
    /**
     * This method handels current id scope
     * @return  Object
     * @since   2016-07-14
     * @author  NetQuick
     */
    public function scopeNotIdCheck($query, $id)
    {
        $response = false;
        $response = $query->where('id', '!=', $id);
        return $response;
    }
    /**
     * This method handels order scope
     * @return  Object
     * @since   2016-07-14
     * @author  NetQuick
     */
    public function scopeOrderCheck($query, $order)
    {
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
    public function scopePublish($query)
    {
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
    public function scopeDeleted($query)
    {
        $response = false;
        $response = $query->where(['chrDelete' => 'N']);
        return $response;
    }
    /**
     * This method handels filter scope
     * @return  Object
     * @since   2016-07-14
     * @author  NetQuick
     */
    public function scopeFilter($query, $filterArr = false, $retunTotalRecords = false)
    {
        $response = null;
        if (!empty($filterArr['orderByFieldName']) && !empty($filterArr['orderTypeAscOrDesc'])) {
            $data = $query->orderBy($filterArr['orderByFieldName'], $filterArr['orderTypeAscOrDesc']);
        } else {
            $data = $query->orderBy('id', 'ASC');
        }

        if (!$retunTotalRecords) {
            if (!empty($filterArr['iDisplayLength']) && $filterArr['iDisplayLength'] > 0) {
                $data = $query->skip($filterArr['iDisplayStart'])->take($filterArr['iDisplayLength']);
            }
        }
        
        if (!empty($filterArr['statusFilter']) && $filterArr['statusFilter'] != ' ') {
            $data = $query->where('chrPublish', $filterArr['statusFilter']);
        }

        if (!empty($filterArr['searchFilter']) && $filterArr['searchFilter'] != '') {
            $data = $query->where('varTitle', 'like', "%" . $filterArr['searchFilter'] . "%");
        }
        
        if (!empty($data)) {
            $response = $data;
        }
        return $response;
    }

    /**
     * This method handels record id scope
     * @return  Object
     * @since   2017-07-25
     * @author  NetQuick
     */

    public function scopeCheckRecordIds($query, $ids)
    {
        $response = false;
        $response = $query->whereIn('id', $ids);
        return $response;
    }

    /**
     * This method handels parent record id scope
     * @return  Object
     * @since   04-08-2017
     * @author  NetQuick
     */
    public function scopeCheckParentRecordId($query, $id)
    {
        $response = false;
        $response = $query->where('intParentCategoryId', $id);
        return $response;
    }
    public function scopeDisplayOrderBy($query, $orderBy)
    {
        $response = false;
        $response = $query->orderBy('intDisplayOrder', $orderBy);
        return $response;
    }
    public static function getServicesNameByServicesId($ids)
    {
        $response             = false;
        $parentCategoryFields = ['varTitle'];
        $ids                  = explode(',', $ids[0]);
        $response             = Self::getPowerPanelRecords($parentCategoryFields)->deleted()->whereIn('id', $ids)->get();
        return $response;
    }

    public static function getCatData($id) {
        // $id=[];
        $response = false;
        $categoryFields = ['id', 'varTitle', 'intDisplayOrder'];
        $response = Self::getPowerPanelRecords($categoryFields)
                        ->deleted()
                        ->publish()
                        ->whereIn('id', $id)
                        ->orderBy('intDisplayOrder', 'asc')->first();
                        // dd($response);
        return $response;
    }
}
