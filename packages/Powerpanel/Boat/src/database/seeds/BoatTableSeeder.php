<?php

use Illuminate\Database\Seeder;
use Carbon\Carbon;
use App\Helpers\MyLibrary;
use App\Http\Traits\slug;

class BoatTableSeeder extends Seeder {

    public function run() {
        $pageModuleCode = DB::table('module')->select('id')->where('varTitle', 'Boat')->first();
        if (!isset($pageModuleCode->id) || empty($pageModuleCode->id)) {
            if (\Schema::hasColumn('module', 'intFkGroupCode')) {
                DB::table('module')->insert([
                    'intFkGroupCode' => '2',
                    'varTitle' => 'Boat',
                    'varModuleName' => 'boat',
                    'varTableName' => 'boat',
                    'varModelName' => 'Boat',
                    'varModuleClass' => 'BoatController',
                    'varModuleNameSpace' => 'Powerpanel\Boat\\',
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
                    'varTitle' => 'Boat',
                    'varModuleName' => 'boat',
                    'varTableName' => 'boat',
                    'varModelName' => 'Boat',
                    'varModuleClass' => 'BoatController',
                    'varModuleNameSpace' => 'Powerpanel\Boat\\',
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

            $pageModuleCode = DB::table('module')->where('varTitle', 'Boat')->first();
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

        $pageModuleCode = DB::table('module')->where('varTitle', 'Boat')->first();
        $cmsModuleCode = DB::table('module')->where('varTitle', 'pages')->first();
        $intFKModuleCode = $pageModuleCode->id;

        $exists = DB::table('cms_page')->select('id')->where('varTitle', htmlspecialchars_decode('Boat'))->first();

        if (!isset($exists->id)) {
            if (\Schema::hasColumn('cms_page', 'chrMain')) {
                DB::table('cms_page')->insert([
                    'varTitle' => htmlspecialchars_decode('Boat'),
                    'intAliasId' => MyLibrary::insertAlias(slug::create_slug(htmlspecialchars_decode('Boat')), $cmsModuleCode->id),
                    'intFKModuleCode' => $intFKModuleCode,
                    'txtDescription' => '',
                    'chrPublish' => 'Y',
                    'chrMain' => 'Y',
                    'chrDelete' => 'N',
                    'varMetaTitle' => 'Boat',
                    'varMetaKeyword' => 'Boat',
                    'varMetaDescription' => 'Boat',
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ]);
            } else {
                DB::table('cms_page')->insert([
                    'varTitle' => htmlspecialchars_decode('Boat'),
                    'intAliasId' => MyLibrary::insertAlias(slug::create_slug(htmlspecialchars_decode('Boat')), $cmsModuleCode->id),
                    'intFKModuleCode' => $intFKModuleCode,
                    'txtDescription' => '',
                    'chrPublish' => 'Y',
                    'chrDelete' => 'N',
                    'varMetaTitle' => 'Boat',
                    'varMetaKeyword' => 'Boat',
                    'varMetaDescription' => 'Boat',
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ]);
            }
        }

        $pageObj = DB::table('cms_page')->select('id')->where('varTitle', 'Boat')->first();

        $moduleCode = DB::table('module')->select('id')->where('varTableName', 'boat')->first();

         $pageModuleCodealias = DB::table('module')->select('id')->where('varTitle', 'Boat')->first();
        $intFKModuleCodealias = $pageModuleCodealias->id;
        
        DB::table('boat')->insert([
            'varTitle' => 'Boat 1',
            'intAliasId' => MyLibrary::insertAlias(slug::create_slug(htmlspecialchars_decode('Boat 1')), $intFKModuleCodealias),
            'txtDescription' => 'The standard Lorem Ipsum passage, used since the 1500s',
            'varMetaTitle' => 'Boat 1',
            'varMetaKeyword' => 'Boat 1',
            'varMetaDescription' => 'Boat 1',
            'intDisplayOrder' => '1', 
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now()
        ]);
        DB::table('boat')->insert([
            'varTitle' => 'Boat 2',
            'intAliasId' => MyLibrary::insertAlias(slug::create_slug(htmlspecialchars_decode('Boat 2')), $intFKModuleCodealias),
            'txtDescription' => 'The standard Lorem Ipsum passage, used since the 1500s',
            'varMetaTitle' => 'Boat 2',
            'varMetaKeyword' => 'Boat 2',
            'varMetaDescription' => 'Boat 2',
            'intDisplayOrder' => '2',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now()
        ]);
        DB::table('boat')->insert([
            'varTitle' => 'Boat 3',
            'intAliasId' => MyLibrary::insertAlias(slug::create_slug(htmlspecialchars_decode('Boat 3')), $intFKModuleCodealias),
            'txtDescription' => 'The standard Lorem Ipsum passage, used since the 1500s',
            'varMetaTitle' => 'Boat 3',
            'varMetaKeyword' => 'Boat 3',
            'varMetaDescription' => 'Boat 3',
            'intDisplayOrder' => '3',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now()
        ]);
        
        
        //Adding Boat Module In visual composer
        if (Schema::hasTable('visualcomposer'))
        {
            $BoatModule = DB::table('visualcomposer')->select('id')->where('varTitle','Boat')->where('fkParentID','0')->first();
            
            if(!isset($BoatModule->id) || empty($BoatModule->id)) {
                DB::table('visualcomposer')->insert([
                    'fkParentID' => '0',
                    'varTitle' => 'Boat',
                    'varIcon' =>  '',
                    'varClass' => '',
                    'varTemplateName' => '',
                    'varModuleID' => $pageModuleCode->id,
                    'created_at'=> Carbon::now(),
                    'updated_at'=> Carbon::now()
                ]);
            }

            $BoatModule = DB::table('visualcomposer')->select('id')->where('varTitle','Boat')->where('fkParentID','0')->first();

            $BoatChild = DB::table('visualcomposer')->select('id')->where('varTitle','Boat')->where('fkParentID','<>','0')->first();
            
            if(!isset($BoatChild->id) || empty($BoatChild->id)) {
                DB::table('visualcomposer')->insert([
                    'fkParentID' => $BoatModule->id,
                    'varTitle' => 'Boat',
                    'varIcon' =>  'fa fa-cogs',
                    'varClass' => 'boat',
                    'varTemplateName' => 'boat::partial.boat',
                    'varModuleID' => $pageModuleCode->id,
                    'created_at'=> Carbon::now(),
                    'updated_at'=> Carbon::now()
                ]);
            }

            $latestBoat = DB::table('visualcomposer')->select('id')->where('varTitle','All Boat')->where('fkParentID','0')->first();
            
            if(!isset($latestBoat->id) || empty($latestBoat->id)) {
                DB::table('visualcomposer')->insert([
                    'fkParentID' => $BoatModule->id,
                    'varTitle' => 'All Boat',
                    'varIcon' =>  'fa fa-cogs',
                    'varClass' => 'boat-template',
                    'varTemplateName' => 'boat::partial.all-boat',
                    'varModuleID' => $pageModuleCode->id,
                    'created_at'=> Carbon::now(),
                    'updated_at'=> Carbon::now()
                ]);
            }
        }
    }

}
