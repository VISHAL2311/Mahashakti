<?php

namespace Database\Seeders;

use App\Helpers\MyLibrary;
use App\Http\Traits\slug;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use DB;
use Schema;

class GalleryTableSeeder extends Seeder {

    public function run() {
        $pageModuleCode = DB::table('module')->select('id')->where('varTitle', 'Gallery')->first();
        
        if (!isset($pageModuleCode->id) || empty($pageModuleCode->id)) {
            if (\Schema::hasColumn('module', 'intFkGroupCode')) {
                DB::table('module')->insert([
                    'intFkGroupCode' => '2',
                    'varTitle' => 'Gallery',
                    'varModuleName' => 'gallery',
                    'varTableName' => 'gallery',
                    'varModelName' => 'Gallery',
                    'varModuleClass' => 'GalleryController',
                    'varModuleNameSpace' => 'Powerpanel\Gallery\\',
                    'decVersion' => 1.0,
                    'intDisplayOrder' => 0,
                    'chrIsFront' => 'N',
                    'chrIsPowerpanel' => 'Y',
                    'varPermissions' => 'list, create, edit, delete, publish,reviewchanges',
                    'chrPublish' => 'Y',
                    'chrDelete' => 'N',
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now()
                ]);
            } else {
                DB::table('module')->insert([
                    'varTitle' => 'Gallery',
                    'varModuleName' => 'gallery',
                    'varTableName' => 'gallery',
                    'varModelName' => 'Gallery',
                    'varModuleClass' => 'GalleryController',
                    'varModuleNameSpace' => 'Powerpanel\Gallery\\',
                    'decVersion' => 1.0,
                    'intDisplayOrder' => 0,
                    'chrIsFront' => 'N',
                    'chrIsPowerpanel' => 'Y',
                    'varPermissions' => 'list, create, edit, delete, publish,reviewchanges',
                    'chrPublish' => 'Y',
                    'chrDelete' => 'N',
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now()
                ]);
            }

            $pageModuleCode = DB::table('module')->where('varTitle', 'Gallery')->first();
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
            
            
            DB::table('gallery')->insert([
                'varTitle' => 'Gallery 1',
                'fkIntImgId' => '1',
                'txtDescription' => '',
                'chrMain' => 'Y',
                'intDisplayOrder' => '1',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ]);
            DB::table('gallery')->insert([
                'varTitle' => 'Gallery 2',
                'fkIntImgId' => '2',
                'txtDescription' => '',
                'chrMain' => 'Y',
                'intDisplayOrder' => '2',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ]);
            DB::table('gallery')->insert([
                'varTitle' => 'Gallery 3',
                'fkIntImgId' => '3',
                'txtDescription' => '',
                'chrMain' => 'Y',
                'intDisplayOrder' => '3',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ]);
        }
    }

}
