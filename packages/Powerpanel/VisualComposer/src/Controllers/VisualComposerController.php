<?php

namespace Powerpanel\VisualComposer\Controllers;

use Request;
use Illuminate\Support\Facades\Redirect;
use Powerpanel\Workflow\Models\Comments;
use Powerpanel\Workflow\Models\WorkflowLog;
use Powerpanel\Workflow\Models\Workflow;
use App\Helpers\FrontPageContent_Shield;
use App\Log;
use App\RecentUpdates;
use App\Alias;
use Validator;
use DB;
use Config;
use App\Http\Controllers\PowerpanelController;
use Crypt;
use Auth;
use App\Helpers\MyLibrary;
use App\CommonModel;
use Carbon\Carbon;
use Cache;
use App\Helpers\Category_builder;
use App\Helpers\CategoryArrayBuilder;
use App\Pagehit;
use App\Modules;
use Powerpanel\RoleManager\Models\Role_user;
use App\Helpers\resize_image;
use App\Helpers\AddImageModelRel;
use App\UserNotification;
use App\Helpers\PageHitsReport;
use App\User;
use Powerpanel\VisualComposer\Models\VisualComposer;
use Illuminate\Support\Facades\View;

class VisualComposerController extends PowerpanelController {

    /**
     * Create a new controller instance.
     * @return void
     */
    public function __construct() {
        parent::__construct();
        if (isset($_COOKIE['locale'])) {
            app()->setLocale($_COOKIE['locale']);
        }
    }

    public static function get_dialog_maker() {
        // $visualData = VisualComposer::where('fkParentID',0)->whereNotIn('id',[28,31,34,40,43])->get()->toArray();
        $visualData = VisualComposer::where('fkParentID',0)->get()->toArray();

        foreach ($visualData as $key => $data) {
            if($data['fkParentID'] == '0' && $data['varTitle'] == 'All'){
                // $visualData[$key]['child'] = VisualComposer::where('fkParentID','<>',0)->whereNotIn('fkParentID',[28,31,34,40,43])->get()->toArray();
                $visualData[$key]['child'] = VisualComposer::where('fkParentID','<>',0)->get()->toArray();
            }
            else if ($data['fkParentID'] == '0' && $data['varTitle'] == 'Templates') {
                $children = [];
                $myLibrary = new MyLibrary;
                if(method_exists($myLibrary, 'GetTemplateData')) {
                    $tempaletData = MyLibrary::GetTemplateData();
                    if(!empty($tempaletData)){
                        foreach($tempaletData as $tdata){
                            // $date = date('' . Config::get('Constant.DEFAULT_DATE_FORMAT') . ' ' . Config::get('Constant.DEFAULT_TIME_FORMAT') . '', strtotime($tdata->created_at));
                            // $userIsAdmin = false;
                            // $currentUserRoleData = auth()->user()->roles->first();
                            // if (!empty($currentUserRoleData)) {
                            //     $udata = $currentUserRoleData;
                            // }
                            // if ($udata->chrIsAdmin == 'Y') {
                            //     $userdata = User::getUserId($tdata->UserID);
                            //     $username = 'Created by @' . $userdata->name.' ('.$date.')';
                            // }else{
                            //     $username= '';
                            // }

                            array_push($children, [
                                'id' => $tdata->id,
                                'varTitle' => $tdata->varTemplateName,
                                'varClass' => '',
                                'varIcon' => 'fa fa-align-justify',
                                'fkParentID' => $data['id'],
                                'varTemplateName' => '',
                                'varModuleName' => '',
                            ]);
                        }
                    }
                }
                $visualData[$key]['child'] = $children;
            }
            else if ($data['fkParentID'] == '0' && $data['varTitle'] == 'Forms') {
                $myLibrary = new MyLibrary;
                $formChildren = [];
                if(method_exists($myLibrary, 'GetFormBuilderData')) {
                    $FormBuilderData = MyLibrary::GetFormBuilderData();
                    if(!empty($FormBuilderData)){
                        foreach($FormBuilderData as $fdata){
                            // $date = date('' . Config::get('Constant.DEFAULT_DATE_FORMAT') . ' ' . Config::get('Constant.DEFAULT_TIME_FORMAT') . '', strtotime($tdata->created_at));
                            // $userIsAdmin = false;
                            // $currentUserRoleData = auth()->user()->roles->first();
                            // if (!empty($currentUserRoleData)) {
                            //     $udata = $currentUserRoleData;
                            // }
                            // if ($udata->chrIsAdmin == 'Y') {
                            //     $userdata = User::getUserId($tdata->UserID);
                            //     $username = 'Created by @' . $userdata->name.' ('.$date.')';
                            // }else{
                            //     $username= '';
                            // }
    
                            array_push($formChildren, [
                                'id' => $fdata->id,
                                'varTitle' => $fdata->varName,
                                'varClass' => '',
                                'varIcon' => 'fa fa-file-text-o',
                                'fkParentID' => $data['id'],
                                'varTemplateName' => '',
                                'varModuleName' => ''
                            ]);
                        }
                    }
                }
                
                $visualData[$key]['child'] = $formChildren;
            }
            else {
                $visualData[$key]['child'] = VisualComposer::where('fkParentID',$data['id'])->get()->toArray();
            }
        }

        foreach ($visualData as $key => $data) {
            if(!empty($data['varModuleID']) && $data['varModuleID'] != 0 ) {
                $moduleData = DB::table('module')->select('varModuleName')->where('id',$data['varModuleID'])->first();
                if(isset($moduleData->varModuleName) && !empty($moduleData->varModuleName))
                {
                    $visualData[$key]['varModuleName'] = $moduleData->varModuleName;
                }
                    
            }
            foreach ($data['child'] as $index => $child) {
                if(!empty($child['varModuleID']) && $child['varModuleID'] != 0 ) {
                    $childModuleData = DB::table('module')->select('varModuleName')->where('id',$child['varModuleID'])->first();
                    if(isset($childModuleData->varModuleName) && !empty($childModuleData->varModuleName)){
                        $visualData[$key]['child'][$index]['varModuleName'] = $childModuleData->varModuleName;
                    }
                }
            }
        }
        
        $visualComposerTemplate = array();
        foreach ($visualData[0]['child'] as $key => $data) {
            if(!empty($data['varTemplateName']) ) {
                array_push($visualComposerTemplate, $data['varTemplateName']);
            }
        }

        $view = View::make('visualcomposer::dialog-maker')->with('visualData',$visualData)->with('visualComposerTemplate',$visualComposerTemplate);
        echo $view;
    }

    public static function page_section($section) {
        $MyLibrary = new MyLibrary();
        $view = View::make('visualcomposer::page-sections')->with($section)->with('MyLibrary', $MyLibrary);
        echo $view;
    }

    public static function get_builder_css_js() {
        $view = View::make('visualcomposer::builder-js-css');
        echo $view;
    }

    public static function get_visual_checkEditor() {
        $view = View::make('visualcomposer::visualckeditor');
        echo $view;
    }
}