<?php
namespace Database\Seeders;

use App\Helpers\MyLibrary;
use App\Http\Traits\slug;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use DB;
use Schema;

class ServicesCategoryTableSeeder extends Seeder
{

    public function run()
    {
        $pageModuleCode = DB::table('module')->select('id')->where('varTitle', 'Services Category')->first();
        if (!isset($pageModuleCode->id) || empty($pageModuleCode->id)) {
            if (\Schema::hasColumn('module', 'intFkGroupCode')) {
                DB::table('module')->insert([
                    'intFkGroupCode' => '2',
                    'varTitle' => 'Services Category',
                    'varModuleName' => 'service-category',
                    'varTableName' => 'service_category',
                    'varModelName' => 'ServiceCategory',
                    'varModuleClass' => 'ServiceCategoryController',
                    'varModuleNameSpace' => 'Powerpanel\ServicesCategory\\',
                    'decVersion' => 1.0,
                    'intDisplayOrder' => 0,
                    'chrIsFront' => 'Y',
                    'chrIsPowerpanel' => 'Y',
                    'varPermissions' => 'list, create, edit, delete, publish, reviewchanges',
                    'chrPublish' => 'Y',
                    'chrDelete' => 'N',
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ]);
            } else {
                DB::table('module')->insert([
                    'varTitle' => 'Services Category',
                    'varModuleName' => 'service-category',
                    'varTableName' => 'service_category',
                    'varModelName' => 'ServiceCategory',
                    'varModuleClass' => 'ServiceCategoryController',
                    'varModuleNameSpace' => 'Powerpanel\ServicesCategory\\',
                    'decVersion' => 1.0,
                    'intDisplayOrder' => 0,
                    'chrIsFront' => 'Y',
                    'chrIsPowerpanel' => 'Y',
                    'varPermissions' => 'list, create, edit, delete, publish, reviewchanges',
                    'chrPublish' => 'Y',
                    'chrDelete' => 'N',
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ]);
            }

            $pageModuleCode = DB::table('module')->where('varTitle', 'Services Category')->first();
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
                    'intFKModuleCode' => $pageModuleCode->id,
                ]);
            }

            foreach ($permissions as $key => $value) {
                $id = DB::table('permissions')->insertGetId($value);
                $roleObj = DB::table('roles')->select('id')->get();
                if ($roleObj->count() > 0) {
                    foreach ($roleObj as $rkey => $rvalue) {
                        $value = [
                            'permission_id' => $id,
                            'role_id' => $rvalue->id,
                        ];
                        DB::table('role_has_permissions')->insert($value);
                    }
                }
            }
        }

        $pageModuleCode = DB::table('module')->where('varTitle', 'Services Category')->first();
        $cmsModuleCode = DB::table('module')->where('varTitle', 'pages')->first();
        $intFKModuleCode = $pageModuleCode->id;

        $pageObj = DB::table('cms_page')->select('id')->where('varTitle', 'Services Category')->first();

        $moduleCode = DB::table('module')->select('id')->where('varTableName', 'Services Category')->first();

        DB::table('service_category')->insert([
            'varTitle' => 'Services Category 1',
            'intAliasId' => MyLibrary::insertAlias(slug::create_slug(htmlspecialchars_decode('Services Category 1')), $intFKModuleCode),
            'txtDescription' => 'The standard Lorem Ipsum passage, used since the 1500s',
            'varMetaTitle' => 'Services Category 1',
            'varMetaKeyword' => 'Services Category 1',
            'varMetaDescription' => 'Services Category 1',
            'intDisplayOrder' => '1',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);
        DB::table('service_category')->insert([
            'varTitle' => 'Services Category 2',
            'intAliasId' => MyLibrary::insertAlias(slug::create_slug(htmlspecialchars_decode('Services Category 2')), $intFKModuleCode),
            'txtDescription' => 'The standard Lorem Ipsum passage, used since the 1500s',
            'varMetaTitle' => 'Services Category 2',
            'varMetaKeyword' => 'Services Category 2',
            'varMetaDescription' => 'Services Category 2',
            'intDisplayOrder' => '2',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);
        DB::table('service_category')->insert([
            'varTitle' => 'Services Category 3',
            'intAliasId' => MyLibrary::insertAlias(slug::create_slug(htmlspecialchars_decode('Services Category 3')), $intFKModuleCode),
            'txtDescription' => 'The standard Lorem Ipsum passage, used since the 1500s',
            'varMetaTitle' => 'Services Category 3',
            'varMetaKeyword' => 'Services Category 3',
            'varMetaDescription' => 'Services Category 3',
            'intDisplayOrder' => '3',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);
    }

}
