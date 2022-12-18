<?php

namespace Database\Seeders;

use App\Helpers\MyLibrary;
use Illuminate\Database\Seeder;
use Carbon\Carbon;
use DB;
use App\Http\Traits\slug;
use Schema;

class WorkTableSeeder extends Seeder {

    public function run() {
        $pageModuleCode = DB::table('module')->select('id')->where('varTitle', 'Work')->first();
        if (!isset($pageModuleCode->id) || empty($pageModuleCode->id)) {
            if (\Schema::hasColumn('module', 'intFkGroupCode')) {
                DB::table('module')->insert([
                    'intFkGroupCode' => '2',
                    'varTitle' => 'Work',
                    'varModuleName' => 'work',
                    'varTableName' => 'work',
                    'varModelName' => 'Work',
                    'varModuleClass' => 'WorkController',
                    'varModuleNameSpace' => 'Powerpanel\Work\\',
                    'decVersion' => 1.0,
                    'intDisplayOrder' => 0,
                    'chrIsFront' => 'Y',
                    'chrIsPowerpanel' => 'Y',
                    'varPermissions' => 'list, create, edit, delete, publish, reviewchanges',
                    'chrPublish' => 'Y',
                    'chrDelete' => 'N',
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now()
                ]);
            } else {
                DB::table('module')->insert([
                    'varTitle' => 'Work',
                    'varModuleName' => 'work',
                    'varTableName' => 'work',
                    'varModelName' => 'Work',
                    'varModuleClass' => 'WorkController',
                    'varModuleNameSpace' => 'Powerpanel\Work\\',
                    'decVersion' => 1.0,
                    'intDisplayOrder' => 0,
                    'chrIsFront' => 'Y',
                    'chrIsPowerpanel' => 'Y',
                    'varPermissions' => 'list, create, edit, delete, publish, reviewchanges',
                    'chrPublish' => 'Y',
                    'chrDelete' => 'N',
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now()
                ]);
            }

            $pageModuleCode = DB::table('module')->where('varTitle', 'Work')->first();
            $permissions = [];
            foreach (explode(',', $pageModuleCode->varPermissions) as $permissionName) {
                $permissionName = trim($permissionName);
                $Icon = $permissionName;

                if ($permissionName == 'list') {
                    $Icon = 'per_list';
                } elseif ($permissionName == 'create') {
                    $Icon = 'per_add';
                } elseif ($permissionName == 'edit') {
                    $Icon = 'per_edit';
                } elseif ($permissionName == 'delete') {
                    $Icon = 'per_delete';
                } elseif ($permissionName == 'publish') {
                    $Icon = 'per_publish';
                } elseif ($permissionName == 'reviewchanges') {
                    $Icon = 'per_reviewchanges';
                }
                array_push($permissions, [
                    'name' => $pageModuleCode->varModuleName . '-' . $permissionName,
                    'display_name' => $Icon,
                    'description' => ucwords($permissionName) . ' Permission',
                    'intFKModuleCode' => $pageModuleCode->id
                ]);
            }

            foreach ($permissions as $key => $value) {
                $id = DB::table('permissions')->insertGetId($value);
                for ($roleId = 1; $roleId <= 3; $roleId++) {
                    $value = [
                        'permission_id' => $id,
                        'role_id' => $roleId,
                    ];
                    DB::table('permission_role')->insert($value);
                }
            }
        }

        $pageModuleCode = DB::table('module')->where('varTitle', 'Work')->first();
        $cmsModuleCode = DB::table('module')->where('varTitle', 'pages')->first();
        $intFKModuleCode = $pageModuleCode->id;

        $exists = DB::table('cms_page')->select('id')->where('varTitle', htmlspecialchars_decode('Work'))->first();

        if (!isset($exists->id)) {
            if (\Schema::hasColumn('cms_page', 'chrMain')) {
                DB::table('cms_page')->insert([
                    'varTitle' => htmlspecialchars_decode('Work'),
                    'intAliasId' => MyLibrary::insertAlias(slug::create_slug(htmlspecialchars_decode('Work')), $cmsModuleCode->id),
                    'intFKModuleCode' => $intFKModuleCode,
                    'txtDescription' => '',
                    'chrPublish' => 'Y',
                    'chrMain' => 'Y',
                    'chrDelete' => 'N',
                    'varMetaTitle' => 'Work',
                    'varMetaKeyword' => 'Work',
                    'varMetaDescription' => 'Work',
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ]);
            } else {
                DB::table('cms_page')->insert([
                    'varTitle' => htmlspecialchars_decode('Work'),
                    'intAliasId' => MyLibrary::insertAlias(slug::create_slug(htmlspecialchars_decode('Work')), $cmsModuleCode->id),
                    'intFKModuleCode' => $intFKModuleCode,
                    'txtDescription' => '',
                    'chrPublish' => 'Y',
                    'chrDelete' => 'N',
                    'varMetaTitle' => 'Work',
                    'varMetaKeyword' => 'Work',
                    'varMetaDescription' => 'Work',
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ]);
            }
        }

        $pageObj = DB::table('cms_page')->select('id')->where('varTitle', 'Work')->first();

        $moduleCode = DB::table('module')->select('id')->where('varTableName', 'work')->first();

         $pageModuleCodealias = DB::table('module')->select('id')->where('varTitle', 'Work')->first();
        $intFKModuleCodealias = $pageModuleCodealias->id;
        
        DB::table('work')->insert([
            'varTitle' => 'Work 1',
            'intAliasId' => MyLibrary::insertAlias(slug::create_slug(htmlspecialchars_decode('Work 1')), $intFKModuleCodealias),
            'txtDescription' => 'The standard Lorem Ipsum passage, used since the 1500s',
            'varMetaTitle' => 'Work 1',
            'varMetaKeyword' => 'Work 1',
            'varMetaDescription' => 'Work 1',
            'intDisplayOrder' => '1', 
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now()
        ]);
        DB::table('work')->insert([
            'varTitle' => 'Work 2',
            'intAliasId' => MyLibrary::insertAlias(slug::create_slug(htmlspecialchars_decode('Work 2')), $intFKModuleCodealias),
            'txtDescription' => 'The standard Lorem Ipsum passage, used since the 1500s',
            'varMetaTitle' => 'Work 2',
            'varMetaKeyword' => 'Work 2',
            'varMetaDescription' => 'Work 2',
            'intDisplayOrder' => '2',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now()
        ]);
        DB::table('work')->insert([
            'varTitle' => 'Work 3',
            'intAliasId' => MyLibrary::insertAlias(slug::create_slug(htmlspecialchars_decode('Work 3')), $intFKModuleCodealias),
            'txtDescription' => 'The standard Lorem Ipsum passage, used since the 1500s',
            'varMetaTitle' => 'Work 3',
            'varMetaKeyword' => 'Work 3',
            'varMetaDescription' => 'Work 3',
            'intDisplayOrder' => '3',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now()
        ]);
        
        
        //Adding Work Module In visual composer
        if (Schema::hasTable('visualcomposer'))
        {
            $WorkModule = DB::table('visualcomposer')->select('id')->where('varTitle','Work')->where('fkParentID','0')->first();
            
            if(!isset($WorkModule->id) || empty($WorkModule->id)) {
                DB::table('visualcomposer')->insert([
                    'fkParentID' => '0',
                    'varTitle' => 'Work',
                    'varIcon' =>  '',
                    'varClass' => '',
                    'varTemplateName' => '',
                    'varModuleID' => $pageModuleCode->id,
                    'created_at'=> Carbon::now(),
                    'updated_at'=> Carbon::now()
                ]);
            }

            $WorkModule = DB::table('visualcomposer')->select('id')->where('varTitle','Work')->where('fkParentID','0')->first();

            $WorkChild = DB::table('visualcomposer')->select('id')->where('varTitle','Work')->where('fkParentID','<>','0')->first();
            
            if(!isset($WorkChild->id) || empty($WorkChild->id)) {
                DB::table('visualcomposer')->insert([
                    'fkParentID' => $WorkModule->id,
                    'varTitle' => 'Work',
                    'varIcon' =>  'fa fa-cogs',
                    'varClass' => 'work',
                    'varTemplateName' => 'work::partial.work',
                    'varModuleID' => $pageModuleCode->id,
                    'created_at'=> Carbon::now(),
                    'updated_at'=> Carbon::now()
                ]);
            }

            $latestWork = DB::table('visualcomposer')->select('id')->where('varTitle','All Work')->where('fkParentID','0')->first();
            
            if(!isset($latestWork->id) || empty($latestWork->id)) {
                DB::table('visualcomposer')->insert([
                    'fkParentID' => $WorkModule->id,
                    'varTitle' => 'All Work',
                    'varIcon' =>  'fa fa-cogs',
                    'varClass' => 'work-template',
                    'varTemplateName' => 'work::partial.all-work',
                    'varModuleID' => $pageModuleCode->id,
                    'created_at'=> Carbon::now(),
                    'updated_at'=> Carbon::now()
                ]);
            }
        }
    }

}
