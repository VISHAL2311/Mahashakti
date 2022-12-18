<?php

use Illuminate\Database\Seeder;
use Carbon\Carbon;
use App\Helpers\MyLibrary;
use App\Http\Traits\slug;

class ServicesTableSeeder extends Seeder {

    public function run() {
        $pageModuleCode = DB::table('module')->select('id')->where('varTitle', 'Services')->first();
        if (!isset($pageModuleCode->id) || empty($pageModuleCode->id)) {
            if (\Schema::hasColumn('module', 'intFkGroupCode')) {
                DB::table('module')->insert([
                    'intFkGroupCode' => '2',
                    'varTitle' => 'Services',
                    'varModuleName' => 'services',
                    'varTableName' => 'services',
                    'varModelName' => 'Services',
                    'varModuleClass' => 'ServicesController',
                    'varModuleNameSpace' => 'Powerpanel\Services\\',
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
                    'varTitle' => 'Services',
                    'varModuleName' => 'services',
                    'varTableName' => 'services',
                    'varModelName' => 'Services',
                    'varModuleClass' => 'ServicesController',
                    'varModuleNameSpace' => 'Powerpanel\Services\\',
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

            $pageModuleCode = DB::table('module')->where('varTitle', 'Services')->first();
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

        $pageModuleCode = DB::table('module')->where('varTitle', 'Services')->first();
        $cmsModuleCode = DB::table('module')->where('varTitle', 'pages')->first();
        $intFKModuleCode = $pageModuleCode->id;

        $exists = DB::table('cms_page')->select('id')->where('varTitle', htmlspecialchars_decode('Services'))->first();

        if (!isset($exists->id)) {
            if (\Schema::hasColumn('cms_page', 'chrMain')) {
                DB::table('cms_page')->insert([
                    'varTitle' => htmlspecialchars_decode('Services'),
                    'intAliasId' => MyLibrary::insertAlias(slug::create_slug(htmlspecialchars_decode('Services')), $cmsModuleCode->id),
                    'intFKModuleCode' => $intFKModuleCode,
                    'txtDescription' => '',
                    'chrPublish' => 'Y',
                    'chrMain' => 'Y',
                    'chrDelete' => 'N',
                    'varMetaTitle' => 'Services',
                    'varMetaKeyword' => 'Services',
                    'varMetaDescription' => 'Services',
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ]);
            } else {
                DB::table('cms_page')->insert([
                    'varTitle' => htmlspecialchars_decode('Services'),
                    'intAliasId' => MyLibrary::insertAlias(slug::create_slug(htmlspecialchars_decode('Services')), $cmsModuleCode->id),
                    'intFKModuleCode' => $intFKModuleCode,
                    'txtDescription' => '',
                    'chrPublish' => 'Y',
                    'chrDelete' => 'N',
                    'varMetaTitle' => 'Services',
                    'varMetaKeyword' => 'Services',
                    'varMetaDescription' => 'Services',
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ]);
            }
        }

        $pageObj = DB::table('cms_page')->select('id')->where('varTitle', 'Services')->first();

        $moduleCode = DB::table('module')->select('id')->where('varTableName', 'services')->first();

         $pageModuleCodealias = DB::table('module')->select('id')->where('varTitle', 'Services')->first();
        $intFKModuleCodealias = $pageModuleCodealias->id;
        
        DB::table('services')->insert([
            'varTitle' => 'Services 1',
            'intAliasId' => MyLibrary::insertAlias(slug::create_slug(htmlspecialchars_decode('Services 1')), $intFKModuleCodealias),
            'txtDescription' => 'The standard Lorem Ipsum passage, used since the 1500s',
            'varMetaTitle' => 'Services 1',
            'varMetaKeyword' => 'Services 1',
            'varMetaDescription' => 'Services 1',
            'intDisplayOrder' => '1', 
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now()
        ]);
        DB::table('services')->insert([
            'varTitle' => 'Services 2',
            'intAliasId' => MyLibrary::insertAlias(slug::create_slug(htmlspecialchars_decode('Services 2')), $intFKModuleCodealias),
            'txtDescription' => 'The standard Lorem Ipsum passage, used since the 1500s',
            'varMetaTitle' => 'Services 2',
            'varMetaKeyword' => 'Services 2',
            'varMetaDescription' => 'Services 2',
            'intDisplayOrder' => '2',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now()
        ]);
        DB::table('services')->insert([
            'varTitle' => 'Services 3',
            'intAliasId' => MyLibrary::insertAlias(slug::create_slug(htmlspecialchars_decode('Services 3')), $intFKModuleCodealias),
            'txtDescription' => 'The standard Lorem Ipsum passage, used since the 1500s',
            'varMetaTitle' => 'Services 3',
            'varMetaKeyword' => 'Services 3',
            'varMetaDescription' => 'Services 3',
            'intDisplayOrder' => '3',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now()
        ]);
        
        
        //Adding Services Module In visual composer
        if (Schema::hasTable('visualcomposer'))
        {
            $ServicesModule = DB::table('visualcomposer')->select('id')->where('varTitle','Services')->where('fkParentID','0')->first();
            
            if(!isset($ServicesModule->id) || empty($ServicesModule->id)) {
                DB::table('visualcomposer')->insert([
                    'fkParentID' => '0',
                    'varTitle' => 'Services',
                    'varIcon' =>  '',
                    'varClass' => '',
                    'varTemplateName' => '',
                    'varModuleID' => $pageModuleCode->id,
                    'created_at'=> Carbon::now(),
                    'updated_at'=> Carbon::now()
                ]);
            }

            $ServicesModule = DB::table('visualcomposer')->select('id')->where('varTitle','Services')->where('fkParentID','0')->first();

            $ServicesChild = DB::table('visualcomposer')->select('id')->where('varTitle','Services')->where('fkParentID','<>','0')->first();
            
            if(!isset($ServicesChild->id) || empty($ServicesChild->id)) {
                DB::table('visualcomposer')->insert([
                    'fkParentID' => $ServicesModule->id,
                    'varTitle' => 'Services',
                    'varIcon' =>  'fa fa-cogs',
                    'varClass' => 'services',
                    'varTemplateName' => 'services::partial.services',
                    'varModuleID' => $pageModuleCode->id,
                    'created_at'=> Carbon::now(),
                    'updated_at'=> Carbon::now()
                ]);
            }

            $latestServices = DB::table('visualcomposer')->select('id')->where('varTitle','All Services')->where('fkParentID','0')->first();
            
            if(!isset($latestServices->id) || empty($latestServices->id)) {
                DB::table('visualcomposer')->insert([
                    'fkParentID' => $ServicesModule->id,
                    'varTitle' => 'All Services',
                    'varIcon' =>  'fa fa-cogs',
                    'varClass' => 'service-template',
                    'varTemplateName' => 'services::partial.all-services',
                    'varModuleID' => $pageModuleCode->id,
                    'created_at'=> Carbon::now(),
                    'updated_at'=> Carbon::now()
                ]);
            }
        }
    }

}
