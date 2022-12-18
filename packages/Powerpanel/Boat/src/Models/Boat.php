<?php

namespace Powerpanel\Boat\Models;

use Cache;
use Request;
use Illuminate\Database\Eloquent\Model;
use DB;

class Boat extends Model
{

    protected $table = 'boat';
    protected $fillable = [
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
    ];

    /**
     * This method handels retrival of front boat list from power composer
     * @return  Object
     * @since   2020-02-04
     * @author  NetQuick
     */
    public static function getBoatList($fields, $recIds)
    {
        $response = false;
        $moduleFields = [
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
        ];
        $aliasFields = ['id', 'varAlias'];
        //$response = Cache::tags(['Boat'])->get('getBoatList_' . implode('-', $recIds));
        //if (empty($response)) {
        $response = Self::getFrontRecords($moduleFields, $aliasFields)
            ->whereIn('id', $recIds)
            ->deleted()
            ->publish()
            ->orderByRaw(DB::raw("FIELD(id, " . implode(',', $recIds) . " )"));
        $response = $response->get();

        //Cache::tags(['Boat'])->forever('getBoatList_' . implode('-', $recIds), $response);
        //}
        return $response;
    }

    /**
     * This method handels retrival of last month boat
     * @return  Object
     * @since   2020-02-04
     * @author  NetQuick
     */
    public static function getTemplateBoatList($fields, $filterArr = false)
    {
        $response = false;
        $moduleFields = [
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
        ];
        $aliasFields = ['id', 'varAlias'];

        $query = Self::getFrontRecords($moduleFields, $aliasFields)
            ->deleted()
            ->publish()
            ->filter($filterArr);

        if (Request::segment(1) != '') {
            $response = $query->orderBy('intDisplayOrder', 'DESC')->paginate(8);
        } else {
            $response = $query->get();
        }

        return $response;
    }






    public static function get_boat_count_category($cat_val, $fkIntBoatCatId)
    {
        $response = false;
        $response  = DB::table('boat')->select('id')->where($fkIntBoatCatId, $cat_val)->where('boat.chrPublish', 'Y')->where('boat.chrDelete', 'N')->count();

        return $response;
    }


    public static function getBoatFilterList($fields, $filterArr = false)
    {

        $response = false;
        $moduleFields = [
            'boat.id',
            'intAliasId',
            'fkIntImgId',
            'fkIntVideoId',
            'boat.varTitle', 'intBoatCategoryId', 'intBoatBrandId', 'intBoatStockId', 'yearYear', 'intPrice', 'varModel', 'varLength', 'intBoatFuelId', 'varHullMaterial', 'varBoatLocation', 'varHullShape', 'varHullWarranty', 'txtOtherdetail', 'txtDescriptionnew', 'varCruisingSpeed', 'varMaxSpeed', 'varLengthOverall', 'varBridgeclearance', 'varMaxDraft', 'varBeam', 'varCabinHeadroom', 'varLengthAtWaterline', 'varDryWeight', 'varWindlass', 'varDeadriseAtTransom', 'varElectricalCircuit', 'varSeatingCapacity', 'varFreshWaterTank', 'varFuelTank', 'varHoldingTank', 'varSingleBerths', 'varHeads', 'intBoatconditionId', 'txtOtherdetails',
            'varExternalLink',
            'varFontAwesomeIcon',
            'txtShortDescription',
            'boat.txtDescription',
            'txtCategories',
            'varPreferences',
            'boat.intDisplayOrder',
            'chrFeaturedBoat',
            'boat.chrPublish',
            'boat.chrDelete',
            'boat.varMetaTitle',
            'boat.varMetaKeyword',
            'boat.varMetaDescription',
            'boat.created_at',
            'boat.updated_at'
        ];
        $aliasFields = ['id', 'varAlias'];

        $query = Self::getFrontRecords($moduleFields, $aliasFields)
        ->where('boat.chrPublish', 'Y')->where('boat.chrDelete', 'N');


        if (isset($filterArr['filterSortArr']) && !empty($filterArr['filterSortArr'])) {
            $query = $query->frontFilter($filterArr['filterSortArr']);
        }
        if (isset($filterArr['searchArr']) && !empty($filterArr['searchArr'])) {
            $query = $query->frontSearchFilter($filterArr['searchArr']);
        }

        if (Request::segment(1) != '') {
            $response['data'] = $query->orderBy('intDisplayOrder', 'ASC')->paginate(9);
        } else {
            $response['data'] = $query->get();
        }
        return $response;
    }
    public static function getBoatFilterCount($fields, $filterArr = false)
    {

        $response = false;
        $moduleFields = [
            'boat.id',
            'intAliasId',
            'fkIntImgId',
            'fkIntVideoId',
            'boat.varTitle', 'intBoatCategoryId', 'intBoatBrandId', 'intBoatStockId', 'yearYear', 'intPrice', 'varModel', 'varLength', 'intBoatFuelId', 'varHullMaterial', 'varBoatLocation', 'varHullShape', 'varHullWarranty', 'txtOtherdetail', 'txtDescriptionnew', 'varCruisingSpeed', 'varMaxSpeed', 'varLengthOverall', 'varBridgeclearance', 'varMaxDraft', 'varBeam', 'varCabinHeadroom', 'varLengthAtWaterline', 'varDryWeight', 'varWindlass', 'varDeadriseAtTransom', 'varElectricalCircuit', 'varSeatingCapacity', 'varFreshWaterTank', 'varFuelTank', 'varHoldingTank', 'varSingleBerths', 'varHeads', 'intBoatconditionId', 'txtOtherdetails',
            'varExternalLink',
            'varFontAwesomeIcon',
            'txtShortDescription',
            'boat.txtDescription',
            'txtCategories',
            'varPreferences',
            'boat.intDisplayOrder',
            'chrFeaturedBoat',
            'boat.chrPublish',
            'boat.chrDelete',
            'boat.varMetaTitle',
            'boat.varMetaKeyword',
            'boat.varMetaDescription',
            'boat.created_at',
            'boat.updated_at'
        ];
        $aliasFields = ['id', 'varAlias'];

        $query = Self::getFrontRecords($moduleFields, $aliasFields)
        ->where('boat.chrPublish', 'Y')->where('boat.chrDelete', 'N');


        if (isset($filterArr['filterSortArr']) && !empty($filterArr['filterSortArr'])) {
            $query = $query->frontFilter($filterArr['filterSortArr']);
        }
        if (isset($filterArr['searchArr']) && !empty($filterArr['searchArr'])) {
            $query = $query->frontSearchFilter($filterArr['searchArr']);
        }

        $response = $query->count();

        return $response;
    }

    public static function getBuilderRecordList($filterArr = [])
    {
        $response = false;
        $moduleFields = [
            'id',
            'intAliasId',
            'fkIntImgId',
            'varTitle', 'intBoatCategoryId', 'intBoatBrandId', 'intBoatStockId', 'yearYear', 'intPrice', 'varModel', 'varLength', 'intBoatFuelId', 'varHullMaterial', 'varBoatLocation', 'varHullShape', 'varHullWarranty', 'txtOtherdetail', 'txtDescriptionnew', 'varCruisingSpeed', 'varMaxSpeed', 'varLengthOverall', 'varBridgeclearance', 'varMaxDraft', 'varBeam', 'varCabinHeadroom', 'varLengthAtWaterline', 'varDryWeight', 'varWindlass', 'varDeadriseAtTransom', 'varElectricalCircuit', 'varSeatingCapacity', 'varFreshWaterTank', 'varFuelTank', 'varHoldingTank', 'varSingleBerths', 'varHeads', 'intBoatconditionId', 'txtOtherdetails',
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
    public static function getRecordIdByAliasID($aliasID)
    {
        $response = false;
        $response = Cache::tags(['Boat'])->get('getBoatRecordIdByAliasID_' . $aliasID);
        if (empty($response)) {
            $response = Self::Select('id')->deleted()->publish()->checkAliasId($aliasID)->first();
            Cache::tags(['Boat'])->forever('getBoatRecordIdByAliasID_' . $aliasID, $response);
        }
        return $response;
    }

    /**
     * This method handels retrival of front boat list
     * @return  Object
     * @since   2017-10-14
     * @author  NetQuick
     */
    public static function getListByCategory($paginate = 6, $page = false)
    {
        $response = false;
        $moduleFields = ['varTitle', 'fkIntImgId', 'intAliasId', 'txtShortDescription', 'txtDescription'];
        $aliasFields = ['id', 'varAlias'];
        $response = Cache::tags(['Boat'])->get('getFrontBoatList_' . $page);
        if (empty($response)) {
            $response = Self::getFrontRecords($moduleFields, $aliasFields)
                ->deleted()
                ->orderBy('intDisplayOrder', 'ASC')
                ->publish()
                ->paginate($paginate);

            Cache::tags(['Boat'])->forever('getFrontBoatList_' . $page, $response);
        }
        return $response;
    }

    /**
     * This method handels retrival of front boat list
     * @return  Object
     * @since   2017-10-14
     * @author  NetQuick
     */
    public static function getFrontList($paginate = false, $page = false)
    {
        $response = false;
        $moduleFields = ['varTitle', 'intBoatCategoryId', 'intBoatBrandId', 'intBoatStockId', 'yearYear', 'intPrice', 'varModel', 'varLength', 'intBoatFuelId', 'varHullMaterial', 'varBoatLocation', 'varHullShape', 'varHullWarranty', 'txtOtherdetail', 'txtDescriptionnew', 'varCruisingSpeed', 'varMaxSpeed', 'varLengthOverall', 'varBridgeclearance', 'varMaxDraft', 'varBeam', 'varCabinHeadroom', 'varLengthAtWaterline', 'varDryWeight', 'varWindlass', 'varDeadriseAtTransom', 'varElectricalCircuit', 'varSeatingCapacity', 'varFreshWaterTank', 'varFuelTank', 'varHoldingTank', 'varSingleBerths', 'varHeads', 'intBoatconditionId', 'txtOtherdetails', 'fkIntImgId', 'intAliasId', 'txtShortDescription', 'txtDescription', 'varFontAwesomeIcon'];
        $aliasFields = ['id', 'varAlias'];
        $response = Cache::tags(['Boat'])->get('getFrontBoatList_' . $page);
        if (empty($response)) {
            $response = Self::getFrontRecords($moduleFields, $aliasFields)
                ->deleted()
                ->publish()
                ->orderBy('intDisplayOrder', 'DESC')
                ->paginate($paginate);

            Cache::tags(['Boat'])->forever('getFrontBoatList_' . $page, $response);
        }
        return $response;
    }
    public static function getFrontListHome()
    {
        $response = false;
        $moduleFields = ['varTitle', 'intBoatCategoryId', 'intBoatBrandId', 'intBoatStockId', 'yearYear', 'intPrice', 'varModel', 'varLength', 'intBoatFuelId', 'varHullMaterial', 'varBoatLocation', 'varHullShape', 'varHullWarranty', 'txtOtherdetail', 'txtDescriptionnew', 'varCruisingSpeed', 'varMaxSpeed', 'varLengthOverall', 'varBridgeclearance', 'varMaxDraft', 'varBeam', 'varCabinHeadroom', 'varLengthAtWaterline', 'varDryWeight', 'varWindlass', 'varDeadriseAtTransom', 'varElectricalCircuit', 'varSeatingCapacity', 'varFreshWaterTank', 'varFuelTank', 'varHoldingTank', 'varSingleBerths', 'varHeads', 'intBoatconditionId', 'txtOtherdetails', 'id', 'fkIntImgId', 'intAliasId', 'txtShortDescription', 'txtDescription', 'varFontAwesomeIcon'];
        $aliasFields = ['id', 'varAlias'];
        // $response = Cache::tags(['Boat'])->get('getFrontLatestBoatList_' . $id);
        if (empty($response)) {
            $response = Self::getFrontRecords($moduleFields, $aliasFields)
                ->deleted()
                ->publish()
                ->take(3)
                ->orderBy('intDisplayOrder', 'DESC')
                ->get();

            // Cache::tags(['Boat'])->forever('getFrontLatestBoatList_' . $id, $response);
        }
        return $response;
    }
    public static function getFrontListOne($paginate = false, $page = false)
    {
        $response = false;
        $moduleFields = ['varTitle', 'intBoatCategoryId', 'intBoatBrandId', 'intBoatStockId', 'yearYear', 'intPrice', 'varModel', 'varLength', 'intBoatFuelId', 'varHullMaterial', 'varBoatLocation', 'varHullShape', 'varHullWarranty', 'txtOtherdetail', 'txtDescriptionnew', 'varCruisingSpeed', 'varMaxSpeed', 'varLengthOverall', 'varBridgeclearance', 'varMaxDraft', 'varBeam', 'varCabinHeadroom', 'varLengthAtWaterline', 'varDryWeight', 'varWindlass', 'varDeadriseAtTransom', 'varElectricalCircuit', 'varSeatingCapacity', 'varFreshWaterTank', 'varFuelTank', 'varHoldingTank', 'varSingleBerths', 'varHeads', 'intBoatconditionId', 'txtOtherdetails', 'fkIntImgId', 'intAliasId', 'txtShortDescription', 'txtDescription', 'varFontAwesomeIcon'];
        $aliasFields = ['id', 'varAlias'];
        $response = Cache::tags(['Boat'])->get('getFrontBoatList_' . $page);
        if (empty($response)) {
            $response = Self::getFrontRecords($moduleFields, $aliasFields)
                ->deleted()
                ->publish()
                ->take(3)
                ->orderBy('intDisplayOrder', 'ASC')
                ->paginate($paginate);

            Cache::tags(['Boat'])->forever('getFrontBoatList_' . $page, $response);
        }
        return $response;
    }

    /**
     * This method handels retrival of front latest boat list
     * @return  Object
     * @since   2017-10-13
     * @author  NetQuick
     */
    public static function getLatestList($id = false)
    {
        $response = false;
        $moduleFields = ['varTitle', 'intBoatCategoryId', 'intBoatBrandId', 'intBoatStockId', 'yearYear', 'intPrice', 'varModel', 'varLength', 'intBoatFuelId', 'varHullMaterial', 'varBoatLocation', 'varHullShape', 'varHullWarranty', 'txtOtherdetail', 'txtDescriptionnew', 'varCruisingSpeed', 'varMaxSpeed', 'varLengthOverall', 'varBridgeclearance', 'varMaxDraft', 'varBeam', 'varCabinHeadroom', 'varLengthAtWaterline', 'varDryWeight', 'varWindlass', 'varDeadriseAtTransom', 'varElectricalCircuit', 'varSeatingCapacity', 'varFreshWaterTank', 'varFuelTank', 'varHoldingTank', 'varSingleBerths', 'varHeads', 'intBoatconditionId', 'txtOtherdetails', 'fkIntImgId', 'intAliasId', 'created_at'];
        $aliasFields = ['id', 'varAlias'];
        $response = Cache::tags(['Boat'])->get('getFrontLatestBoatList_' . $id);
        if (empty($response)) {
            $response = Self::getFrontRecords($moduleFields, $aliasFields)
                ->deleted()
                ->publish()
                ->latestRecord($id)
                ->take(5)
                ->get();

            Cache::tags(['Boat'])->forever('getFrontLatestBoatList_' . $id, $response);
        }
        return $response;
    }

    public static function getSidebarRecordList()
    {
        $response = false;
        $moduleFields = ['id', 'intAliasId', 'varTitle', 'intBoatCategoryId', 'intBoatBrandId', 'intBoatStockId', 'yearYear', 'intPrice', 'varModel', 'varLength', 'intBoatFuelId', 'varHullMaterial', 'varBoatLocation', 'varHullShape', 'varHullWarranty', 'txtOtherdetail', 'txtDescriptionnew', 'varCruisingSpeed', 'varMaxSpeed', 'varLengthOverall', 'varBridgeclearance', 'varMaxDraft', 'varBeam', 'varCabinHeadroom', 'varLengthAtWaterline', 'varDryWeight', 'varWindlass', 'varDeadriseAtTransom', 'varElectricalCircuit', 'varSeatingCapacity', 'varFreshWaterTank', 'varFuelTank', 'varHoldingTank', 'varSingleBerths', 'varHeads', 'intBoatconditionId', 'txtOtherdetails', 'varExternalLink', 'varFontAwesomeIcon', 'txtShortDescription', 'fkIntImgId', 'txtCategories', 'intDisplayOrder', 'txtDescription', 'chrFeaturedBoat', 'chrPublish'];
        $aliasFields = ['id', 'varAlias'];
        $response = Cache::tags(['Boat'])->get('getFrontLatestBoatList');
        if (empty($response)) {
            $response = Self::getFrontRecords($moduleFields, $aliasFields)
                ->deleted()
                ->publish()
                ->orderBy('intDisplayOrder', 'DESC')
                ->take(5)
                ->get();

            Cache::tags(['Boat'])->forever('getFrontLatestBoatList', $response);
        }
        return $response;
    }

    public static function getFrontBoatDropdownList($id = false)
    {
        $response = false;
        $moduleFields = ['id', 'varTitle', 'intBoatCategoryId', 'intBoatBrandId', 'intBoatStockId', 'yearYear', 'intPrice', 'varModel', 'varLength', 'intBoatFuelId', 'varHullMaterial', 'varBoatLocation', 'varHullShape', 'varHullWarranty', 'txtOtherdetail', 'txtDescriptionnew', 'varCruisingSpeed', 'varMaxSpeed', 'varLengthOverall', 'varBridgeclearance', 'varMaxDraft', 'varBeam', 'varCabinHeadroom', 'varLengthAtWaterline', 'varDryWeight', 'varWindlass', 'varDeadriseAtTransom', 'varElectricalCircuit', 'varSeatingCapacity', 'varFreshWaterTank', 'varFuelTank', 'varHoldingTank', 'varSingleBerths', 'varHeads', 'intBoatconditionId', 'txtOtherdetails', 'fkIntImgId', 'intAliasId', 'intDisplayOrder', 'created_at'];
        $aliasFields = ['id', 'varAlias'];
        $response = Cache::tags(['Boat'])->get('getFrontLatestBoatList_' . $id);
        if (empty($response)) {
            $response = Self::getFrontRecords($moduleFields, $aliasFields)
                ->deleted()
                ->publish()
                //                    ->latestRecord($id)
                ->orderBy('intDisplayOrder', 'DESC')
                ->get();

            Cache::tags(['Boat'])->forever('getFrontLatestBoatList_' . $id, $response);
        }
        return $response;
    }

    public static function getBoatSiteMapData()
    {
        $response = false;
        $aliasFields = ['id', 'varAlias'];
        $boatFields = ['varTitle', 'intAliasId'];

        $response = Cache::tags(['Boat'])->get('getBoatSiteMapData');
        if (empty($response)) {
            $response = Self::getFrontRecords($boatFields, $aliasFields)
                ->deleted()
                ->publish()
                ->get();
            Cache::tags(['Boat'])->forever('getBoatSiteMapData', $response);
        }
        return $response;
    }


    /**
     * This method handels retrival of front latest boat list
     * @return  Object
     * @since   2017-10-13
     * @author  NetQuick
     */
    public static function getSimilarRecordList($id = false)
    {
        $response = false;
        $moduleFields = ['varTitle', 'fkIntImgId', 'intPrice', 'yearYear', 'varLength', 'intBoatFuelId', 'intBoatStockId', 'intAliasId', 'created_at'];
        $aliasFields = ['id', 'varAlias'];
        $response = Cache::tags(['Boat'])->get('getSimilarRecordList_' . $id);
        if (empty($response)) {
            $response = Self::getFrontRecords($moduleFields, $aliasFields)
                ->deleted()
                ->publish()
                ->latestRecord($id)
                ->take(5)
                ->get();

            Cache::tags(['Boat'])->forever('getSimilarRecordList_' . $id, $response);
        }
        return $response;
    }
    public static function getallBrands()
    {
        $brand = DB::table('brand')->select('id', 'varTitle')->where('chrPublish', 'Y')->where('chrDelete', 'N')->orderBy('intDisplayOrder','asc')->get();
        return $brand;
    }
    public static function getallBoatCondition()
    {
        $response =  DB::table('boat_condition')->select('id', 'varTitle')->where('chrPublish', 'Y')->where('chrDelete', 'N')->get();
        return $response;
    }
    public static function getallstock()
    {
        $response =  DB::table('stock')->select('id', 'varTitle')->where('chrPublish', 'Y')->where('chrDelete', 'N')->get();
        return $response;
    }
    public static function getallBoatCategory()
    {
        $response =  DB::table('boat_category')->select('id', 'varTitle')->where('chrPublish', 'Y')->where('chrDelete', 'N')->orderBy('intDisplayOrder','asc')->get();
        return $response;
    }

    /**
     * This method handels retrival of front latest boat list
     * @return  Object
     * @since   2017-10-13
     * @author  NetQuick
     */
    public static function getFeaturedList($limit = 5)
    {
        $response = false;
        $moduleFields = ['varTitle', 'fkIntImgId', 'varFontAwesomeIcon', 'txtShortDescription', 'intAliasId', 'created_at'];
        $aliasFields = ['id', 'varAlias'];
        $response = Cache::tags(['Boat'])->get('getBoatFeaturedList');
        if (empty($response)) {
            $response = Self::getFrontRecords($moduleFields, $aliasFields)
                ->publish()
                ->deleted()
                ->featured('Y')
                ->displayOrderBy('ASC')
                ->take($limit)
                ->get();
            Cache::tags(['Boat'])->forever('getBoatFeaturedList', $response);
        }
        return $response;
    }

    public static function getFrontBoatDropdown()
    {
        $response = false;
        $moduleFields = ['id', 'varTitle'];
        $response = Cache::tags(['Boat'])->get('getFrontBoatDropdown');
        if (empty($response)) {
            $response = Self::getFrontRecords($moduleFields)
                ->publish()
                ->deleted()
                ->displayOrderBy('ASC')
                ->get();

            Cache::tags(['Boat'])->forever('getFrontBoatDropdown', $response);
        }
        return $response;
    }


    /**
     * This method handels retrival of front boat detail
     * @return  Object
     * @since   2017-10-13
     * @author  NetQuick
     */
    public static function getFrontDetail($id)
    {
        $response = false;
        $moduleFields = ['id', 'intAliasId', 'fkIntImgId', 'fkIntVideoId', 'varTitle', 'intBoatCategoryId', 'intBoatBrandId', 'intBoatStockId', 'yearYear', 'intPrice', 'varModel', 'varLength', 'intBoatFuelId', 'varHullMaterial', 'varBoatLocation', 'varHullShape', 'varHullWarranty', 'txtOtherdetail', 'txtDescriptionnew', 'varCruisingSpeed', 'varMaxSpeed', 'varLengthOverall', 'varBridgeclearance', 'varMaxDraft', 'varBeam', 'varCabinHeadroom', 'varLengthAtWaterline', 'varDryWeight', 'varWindlass', 'varDeadriseAtTransom', 'varElectricalCircuit', 'varSeatingCapacity', 'varFreshWaterTank', 'varFuelTank', 'varHoldingTank', 'varSingleBerths', 'varHeads', 'intBoatconditionId', 'txtOtherdetails', 'varExternalLink', 'varFontAwesomeIcon', 'txtShortDescription', 'txtDescriptionnew', 'txtCategories', 'varPreferences', 'intDisplayOrder', 'chrFeaturedBoat', 'chrPublish', 'chrDelete', 'varMetaTitle', 'varMetaKeyword', 'varMetaDescription'];
        $aliasFields = ['id', 'varAlias'];
        $response = Cache::tags(['Boat'])->get('getFrontBoatDetail_' . $id);
        if (empty($response)) {
            $response = Self::getFrontRecords($moduleFields, $aliasFields)
                ->deleted()
                ->publish()
                ->checkAliasId($id)
                ->first();
            Cache::tags(['Boat'])->forever('getFrontBoatDetail_' . $id, $response);
        }
        return $response;
    }

    /**
     * This method handels retrival of record count based on category
     * @return  Object
     * @since   2017-10-16
     * @author  NetQuick
     */
    public static function getCountById()
    {
        $response = false;
        $moduleFields = ['id'];
        $response = Self::getPowerPanelRecords($moduleFields)
            ->deleted()
            ->count();
        return $response;
    }
    public static function getBoatCategoryCountById($id)
    {
        $response = false;
        $moduleFields = ['id'];
        $response = Self::getPowerPanelRecords($moduleFields)
            ->where('intBoatCategoryId', $id)
            ->deleted()
            ->count();
        return $response;
    }
    public static function getBoatBrandCountById($id)
    {
        $response = false;
        $moduleFields = ['id'];
        $response = Self::getPowerPanelRecords($moduleFields)
            ->where('intBoatBrandId', $id)
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

    public static function getRecordById($id = false)
    {
        $response = false;
        $moduleFields = [
            'id',
            'intAliasId',
            'varTitle', 'intBoatCategoryId', 'intBoatBrandId', 'intBoatStockId', 'yearYear', 'intPrice', 'varModel', 'varLength', 'intBoatFuelId', 'varHullMaterial', 'varBoatLocation', 'varHullShape', 'varHullWarranty', 'txtOtherdetail', 'txtDescriptionnew', 'varCruisingSpeed', 'varMaxSpeed', 'varLengthOverall', 'varBridgeclearance', 'varMaxDraft', 'varBeam', 'varCabinHeadroom', 'varLengthAtWaterline', 'varDryWeight', 'varWindlass', 'varDeadriseAtTransom', 'varElectricalCircuit', 'varSeatingCapacity', 'varFreshWaterTank', 'varFuelTank', 'varHoldingTank', 'varSingleBerths', 'varHeads', 'intBoatconditionId', 'txtOtherdetails',
            'fkIntImgId',
            'fkIntVideoId',
            'txtCategories',
            'varExternalLink',
            'varFontAwesomeIcon',
            'chrFeaturedBoat',
            'intDisplayOrder',
            'txtShortDescription',
            'txtDescription',
            'varMetaTitle',
            'varMetaKeyword',
            'varMetaDescription',
            'chrPublish'
        ];
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

    public static function getRecordByOrder($order = false)
    {
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
    public static function getRecordList($filterArr = false)
    {
        $response = false;
        $moduleFields = ['id', 'intAliasId', 'varTitle', 'intBoatCategoryId', 'intBoatBrandId', 'intBoatStockId', 'yearYear', 'intPrice', 'varModel', 'varLength', 'intBoatFuelId', 'varHullMaterial', 'varBoatLocation', 'varHullShape', 'varHullWarranty', 'txtOtherdetail', 'txtDescriptionnew', 'varCruisingSpeed', 'varMaxSpeed', 'varLengthOverall', 'varBridgeclearance', 'varMaxDraft', 'varBeam', 'varCabinHeadroom', 'varLengthAtWaterline', 'varDryWeight', 'varWindlass', 'varDeadriseAtTransom', 'varElectricalCircuit', 'varSeatingCapacity', 'varFreshWaterTank', 'varFuelTank', 'varHoldingTank', 'varSingleBerths', 'varHeads', 'intBoatconditionId', 'txtOtherdetails', 'varExternalLink', 'varFontAwesomeIcon', 'txtShortDescription', 'fkIntImgId', 'txtCategories', 'intDisplayOrder', 'txtDescription', 'chrFeaturedBoat', 'chrPublish'];
        $aliasFields = ['id', 'varAlias'];
        $response = Self::getPowerPanelRecords($moduleFields, $aliasFields)
            ->deleted()
            ->filter($filterArr)
            ->get();
        return $response;
    }

    public static function getBoatNameById($id = false)
    {
        $response = false;
        $boatFields = ['varTitle'];
        $response = Self::getPowerPanelRecords($boatFields)->where('id', $id)->first();
        return $response;
    }

    #Config and filters====================================================
    /**
     * This method handels retrival of event records
     * @return  Object
     * @since   2016-07-14
     * @author  NetQuick
     */

    public static function getRecords()
    {
        $response = false;
        $response = self::with(['alias' => function ($query) {
            $query->checkModuleCode();
        }, 'image']);
        return $response;
    }

    public static function getFrontRecords($moduleFields = false, $aliasFields = false)
    {
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
     * This method handels retrival of boat records
     * @return  Object
     * @since   2016-07-14
     * @author  NetQuick
     */
    public static function getPowerPanelRecords($moduleFields = false, $aliasFields = false, $videoFields = false, $imageFields = false, $categoryFields = false)
    {
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
            $data['boatCategory'] = function ($query) use ($categoryFields) {
                $query->select($categoryFields);
            };
        }
        if (count($data) > 0) {
            $response = $response->with($data);
        }
        return $response;
    }

    public static function getBoatNameByBoatId($ids)
    {
        $response = false;
        $boatFields = ['varTitle'];
        $response = Self::getPowerPanelRecords($boatFields)->deleted()->whereIn('id', $ids)->get();
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
        $response = false;
        $moduleFields = ['id', 'varTitle', 'intBoatCategoryId', 'intBoatBrandId', 'intBoatStockId', 'yearYear', 'intPrice', 'varModel', 'varLength', 'intBoatFuelId', 'varHullMaterial', 'varBoatLocation', 'varHullShape', 'varHullWarranty', 'txtOtherdetail', 'txtDescriptionnew', 'varCruisingSpeed', 'varMaxSpeed', 'varLengthOverall', 'varBridgeclearance', 'varMaxDraft', 'varBeam', 'varCabinHeadroom', 'varLengthAtWaterline', 'varDryWeight', 'varWindlass', 'varDeadriseAtTransom', 'varElectricalCircuit', 'varSeatingCapacity', 'varFreshWaterTank', 'varFuelTank', 'varHoldingTank', 'varSingleBerths', 'varHeads', 'intBoatconditionId', 'txtOtherdetails', 'fkIntImgId', 'txtCategories', 'varExternalLink', 'varFontAwesomeIcon', 'chrFeaturedBoat', 'intDisplayOrder', 'txtShortDescription', 'txtDescription', 'varMetaTitle', 'varMetaKeyword', 'varMetaDescription', 'chrPublish'];
        $response = Self::getPowerPanelRecords($moduleFields)->deleted()->checkRecordId($id)->first();
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
     * This method handels image relation
     * @return  Object
     * @since   2016-07-14
     * @author  NetQuick
     */
    public function image()
    {
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
    public function video()
    {
        return $this->belongsTo('App\Video', 'fkIntVideoId', 'id');
    }

    /**
     * This method handels news category relation
     * @return  Object
     * @since   2016-07-14
     * @author  NetQuick
     */
    public function boatCategory()
    {
        $response = false;
        $response = $this->belongsTo('Powerpanel\BoatCategory\Models\BoatCategory', 'intCategoryId', 'id');
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

    public function scopeCheckVideoId($query, $id)
    {
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
    public function scopeCheckRecordId($query, $id)
    {
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
    public function scopeCheckCategoryId($query, $id)
    {
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
     * This method handels featured scope
     * @return  Object
     * @since   2016-08-08
     * @author  NetQuick
     */
    public function scopeFeatured($query, $flag = null)
    {
        $response = false;
        $response = $query->where(['chrFeaturedBoat' => $flag]);
        return $response;
    }

    /**
     * This method handels orderBy scope
     * @return  Object
     * @since   2016-08-08
     * @author  NetQuick
     */
    public function scopeDisplayOrderBy($query, $orderBy)
    {
        $response = false;
        $response = $query->orderBy('intDisplayOrder', $orderBy);
        return $response;
    }

    /**
     * This method handels Popular Boat scope
     * @return  Object
     * @since   2016-08-30
     * @author  NetQuick
     */
    public function scopeLatestRecord($query, $id = false)
    {
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
    public function scopeFilter($query, $filterArr = false, $retunTotalRecords = false)
    {

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

        if (isset($filterArr['template']) && $filterArr['template'] == 'featured-boat') {
            $query->where('chrFeaturedBoat', '=', 'Y')->orderBy('intDisplayOrder', 'DESC');
        }


        if (!empty($query)) {
            $response = $query;
        }
        return $response;
    }

    public function scopeFrontFilter($query, $filterArr = false, $retunTotalRecords = false)
    {

        $response = false;

        if (isset($filterArr[0]) && !empty($filterArr[0]) && isset($filterArr[1]) && !empty($filterArr[1])) {
            if ($filterArr[0] == "make") {

                $query = $query
                    ->join("brand", "brand.id", "=", "boat.intBoatBrandId")
                    ->orderBy('brand.varTitle', $filterArr[1]);
            } else {
                $query = $query->orderBy($filterArr[0], $filterArr[1]);
            }
        }
        if (isset($filterArr[2]) && !empty($filterArr[2])) {
            $query = $query->where('intBoatCategoryId', $filterArr[2]);
        }
        if (isset($filterArr[3]) && !empty($filterArr[3]) && isset($filterArr[1]) && !empty($filterArr[1]) && $filterArr[3] == 'default') {
            $query = $query->orderBy('intDisplayOrder', $filterArr[1]);
        }

        if (!empty($query)) {
            $response = $query;
        }
        return $response;
    }
    
    public function scopeFrontSearchFilter($query, $filterArr = false, $retunTotalRecords = false)
    {
        $response = false;
        if (isset($filterArr[0]["value"]) && !empty($filterArr[0]["value"]) && $filterArr[0]["value"] != null) {
            $query = $query->where('intBoatBrandId', $filterArr[0]["value"]);
        }

        if (isset($filterArr[1]["value"]) && !empty($filterArr[1]["value"]) && $filterArr[1]["value"] != null) {
            $valuearr = explode(";", $filterArr[1]["value"]);
            foreach ($valuearr as $index => $value) {
                if ($index == 0) {
                    $query = $query->where('varLength', '>', $value-1);
                } else {
                    $query = $query->where('varLength', '<', $value+1);
                }
            }
        }

        if (isset($filterArr[2]["value"]) && !empty($filterArr[2]["value"]) && $filterArr[2]["value"] != null) {
            $valuearr = explode(";", $filterArr[2]["value"]);
            foreach ($valuearr as $index => $value) {
                if ($index == 0) {
                    $query = $query->where('yearYear', '>', $value-1);
                } else {
                    $query = $query->where('yearYear', '<', $value+1);
                }
            }
        }
        if (isset($filterArr[3]["value"]) && !empty($filterArr[3]["value"])) {
            $valuearr = explode(";", $filterArr[3]["value"]);
            foreach ($valuearr as $index => $value) {
                if ($index == 0) {
                    $query = $query->where('intPrice', '>', $value-1);
                } else {
                    $query = $query->where('intPrice', '<', $value+1);
                }
            }
        }
        foreach ($filterArr as $filterdata) {
            if (isset($filterdata["name"]) && !empty($filterdata["name"]) && $filterdata["name"] == "condition") {
                if($filterdata["value"] != 0){
                    $query = $query->where('intBoatconditionId', $filterdata["value"]);
                }
                
            }
            if (isset($filterdata["name"]) && !empty($filterdata["name"]) && $filterdata["name"] == "inStock") {
                $query = $query->where('intBoatStockId', $filterdata["value"]);
            }
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
    public function scopeFrontSearch($query, $term = '')
    {
        $response = false;
        $response = $query->where(['varTitle', 'like', '%' . $term . '%']);
        return $response;
    }
}
