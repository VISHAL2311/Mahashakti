<?php

/**
 * The Testimonial class handels bannner queries
 * ORM implemetation.
 * @package   Netquick powerpanel
 * @license   http://www.opensource.org/licenses/BSD-3-Clause
 * @version   1.1
 * @since       2017-07-20
 * @author    NetQuick
 */

namespace Powerpanel\Testimonial\Models;

use Cache;
use DB;
use Request;
use Illuminate\Database\Eloquent\Model;

class Testimonial extends Model {

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $table = 'testimonials';
    protected $fillable = [
        'id',
        'varTitle', 'varStarRating', 
        'varCity',
        'fkIntImgId',
        'txtDescription',
        'dtStartDateTime',
        'chrPublish',
        'chrDelete',
    ];

    /**
     * This method handels retrival of front product list from power composer
     * @return  Object
     * @since   2020-02-06
     * @author  NetQuick
     */
    public static function getTestimonialList($recIds) {
        $response = false;
        $moduleFields = [
            'id',
            'varTitle', 'varStarRating',
            'varCity',
            'fkIntImgId',
            'txtDescription',
            'dtStartDateTime',
            'chrPublish',
            'chrDelete',
        ];
        $aliasFields = ['id', 'varAlias'];

        $response = Self::getFrontRecords($moduleFields, $aliasFields)
                ->whereIn('id', $recIds)
                ->deleted()
                ->publish()
                ->orderByRaw(DB::raw("FIELD(id, " . implode(',', $recIds) . " )"));
        if(Request::segment(1) != ''){
            $response = $response->paginate(6);
            }else{
             $response = $response->get();   
            }
        return $response;
    }

    /**
     * This method handels retrival of last month product
     * @return  Object
     * @since   2020-02-06
     * @author  NetQuick
     */
    public static function getTemplateTestimonialList() {
        $response = false;
        $moduleFields = [
            'id',
            'varTitle', 'varStarRating',
            'varCity',
            'fkIntImgId',
            'txtDescription',
            'dtStartDateTime',
            'chrPublish',
            'chrDelete',
        ];
        $aliasFields = ['id', 'varAlias'];

        $query = Self::getFrontRecords($moduleFields, $aliasFields)
                ->deleted()
                ->publish();
        if(Request::segment(1) != ''){
            $response = $query->orderBy('dtStartDateTime', 'DESC')->paginate(8);
            }else{
             $response = $query->get();   
            }

        return $response;
    }

    /**
     * This method handels retrival of product list in power composer
     * @return  Object
     * @since   2020-02-06
     * @author  NetQuick
     */
    public static function getBuilderRecordList($filterArr = []) {
        $response = false;
        $moduleFields = [
            'id',
            'varTitle', 'varStarRating',
            'varCity',
            'dtStartDateTime',
            'txtDescription',
            'chrPublish',
            'updated_at',
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
        $response = Cache::tags(['Testimonial'])->get('getTestimonialRecordIdByAliasID_' . $aliasID);
        if (empty($response)) {
            $response = Self::Select('id')->deleted()->publish()->checkAliasId($aliasID)->first();
            Cache::tags(['Testimonial'])->forever('getTestimonialRecordIdByAliasID_' . $aliasID, $response);
        }
        return $response;
    }

    /**
     * This method handels retrival of front testimonial list
     * @return  Object
     * @since   2017-10-13
     * @author  NetQuick
     */
    public static function getFrontList($paginate = false, $currentPage = 1) {
        $response = false;
        $testimonialFields = ['varTitle', 'varStarRating','varCity', 'fkIntImgId', 'txtDescription', 'created_at', 'dtStartDateTime'];
        // $response = Cache::tags(['Testimonial'])->get('getFrontTestimonialList_'.$currentPage);
        if (empty($response)) {
            $response = Self::getFrontRecords($testimonialFields)
                    ->deleted()
                    ->publish()
                    ->orderBy('dtStartDateTime', 'DESC')
                    ->paginate($paginate);
            // Cache::tags(['Testimonial'])->forever('getFrontTestimonialList_'.$currentPage, $response);
        }
        return $response;
    }

    /**
     * This method handels retrival of front testimonial list
     * @return  Object
     * @since   2017-10-13
     * @author  NetQuick
     */
    public static function getLatestList() {
        $response = false;
        $testimonialFields = ['varTitle', 'varStarRating','varCity', 'fkIntImgId', 'txtDescription', 'created_at', 'dtStartDateTime'];
        $response = Cache::tags(['Testimonial'])->get('getTestimonialLatestList');
        if (empty($response)) {
            $response = Self::getFrontRecords($testimonialFields)
                    ->deleted()
                    ->publish()
                    ->orderBy('created_at', 'desc')
                    // ->take(5)
                    ->get();
            Cache::tags(['Testimonial'])->forever('getTestimonialLatestList', $response);
        }
        return $response;
    }

    /**
     * This method handels retrival of testimonial records
     * @return  Object
     * @since   2016-07-14
     * @author  NetQuick
     */
    public static function getFrontRecords($testimonialFields = false, $aliasFields = false) {
        $response = false;
        $response = self::select($testimonialFields);
        return $response;
    }

    /**
     * This method handels retrival of testimonials records
     * @return  Object
     * @since   2016-07-20
     * @author  NetQuick
     */
    public static function getRecords() {
        $response = false;
        $response = self::with([]);
        return $response;
    }

    /**
     * This method handels backend records
     * @return  Object
     * @since   2016-07-14
     * @author  NetQuick
     */
    public static function getPowerPanelRecords($moduleFields = false) {
        $data = [];
        $response = false;
        $response = self::select($moduleFields);
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
    public static function getRecordList($filterArr = false) {
        $response = false;
        $moduleFields = ['id', 'varTitle', 'varStarRating','varCity', 'fkIntImgId', 'txtDescription', 'dtStartDateTime', 'chrPublish'];
        $response = Self::getPowerPanelRecords($moduleFields)
                ->deleted()
                ->filter($filterArr)
                ->orderBy('id', 'DESC')
                ->get();
        return $response;
    }

    /**
     * This method handels retrival of record by id
     * @return  Object
     * @since   2017-10-16
     * @author  NetQuick
     */
    public static function getRecordById($id) {
        $response = false;
        $moduleFields = ['id', 'varTitle', 'varStarRating','varCity', 'fkIntImgId', 'txtDescription', 'dtStartDateTime', 'chrPublish'];
        $response = Self::getPowerPanelRecords($moduleFields)->deleted()->checkRecordId($id)->first();
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
        $moduleFields = ['id', 'varTitle', 'varStarRating','varCity', 'fkIntImgId', 'txtDescription', 'dtStartDateTime', 'chrPublish'];
        $response = Self::getPowerPanelRecords($moduleFields)->deleted()->checkRecordId($id)->first();
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
     * This method handels publish scope
     * @return  Object
     * @since   2016-07-20
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
     * @since   2016-07-20
     * @author  NetQuick
     */
    public function scopeDeleted($query) {
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
    public function scopeFilter($query, $filterArr = false, $retunTotalRecords = false) {
        $response = null;
        if (!empty($filterArr['orderByFieldName']) && !empty($filterArr['orderTypeAscOrDesc'])) {
            $query = $query->orderBy($filterArr['orderByFieldName'], $filterArr['orderTypeAscOrDesc']);
        }

        if (!$retunTotalRecords) {
            if (!empty($filterArr['iDisplayLength']) && $filterArr['iDisplayLength'] > 0) {
                $data = $query->skip($filterArr['iDisplayStart'])->take($filterArr['iDisplayLength']);
            }
        }
        if (!empty($filterArr['dateFilter']) && $filterArr['dateFilter'] != '') {
            $data = $query->whereRaw('DATE(dtStartDateTime) = DATE("' . date('Y-m-d', strtotime(str_replace('/', '-', $filterArr['dateFilter']))) . '")');
        }
        if (!empty($filterArr['searchFilter']) && $filterArr['searchFilter'] != ' ') {
            $data = $query->where('varTitle', 'like', "%" . $filterArr['searchFilter'] . "%");
        }
        if (!empty($filterArr['statusFilter']) && $filterArr['statusFilter'] != ' ') {
            $data = $query->where('chrPublish', $filterArr['statusFilter']);
        }

        if (isset($filterArr['ignore']) && !empty($filterArr['ignore'])) {
            $data = $query->whereNotIn('id', $filterArr['ignore']);
        }

        if (!empty($query)) {
            $response = $query;
        }
        return $response;
    }

}
