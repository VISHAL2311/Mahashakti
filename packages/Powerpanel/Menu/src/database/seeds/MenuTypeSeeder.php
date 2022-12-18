<?php
namespace Database\Seeders;

use App\Helpers\MyLibrary;
use App\Http\Traits\slug;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use DB;
use Schema;

class MenuTypeSeeder extends Seeder
{

    use slug;

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $pageModuleCode = DB::table('module')->select('id')->where('varTitle', 'menu-type')->first();

        if (!isset($pageModuleCode->id) || empty($pageModuleCode->id)) {
            if (\Schema::hasColumn('module', 'intFkGroupCode')) {
                DB::table('module')->insert([
                    'varTitle' => 'menu-type',
                    'intFkGroupCode' => '0',
                    'varModuleName' => 'menu-type',
                    'varTableName' => 'menu_type',
                    'varModelName' => 'MenuType',
                    'varModuleClass' => '',
                    'varModuleNameSpace' => 'Powerpanel\NetquickTheme\\',
                    'intDisplayOrder' => 0,
                    'chrIsFront' => 'N',
                    'chrIsPowerpanel' => 'Y',
                    'chrIsGenerated' => 'N',
                    'decVersion' => 1.0,
                    'chrPublish' => 'Y',
                    'chrDelete' => 'N',
                    'varPermissions' => 'list, create, edit, delete, publish',
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ]);
            } else {
                DB::table('module')->insert([
                    'varTitle' => 'menu-type',
                    'varModuleName' => 'menu-type',
                    'varTableName' => 'menu_type',
                    'varModelName' => 'MenuType',
                    'varModuleClass' => '',
                    'varModuleNameSpace' => 'Powerpanel\NetquickTheme\\',
                    'intDisplayOrder' => 0,
                    'chrIsFront' => 'N',
                    'chrIsPowerpanel' => 'Y',
                    'chrIsGenerated' => 'N',
                    'decVersion' => 1.0,
                    'chrPublish' => 'Y',
                    'chrDelete' => 'N',
                    'varPermissions' => 'list, create, edit, delete, publish',
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ]);
            }

            $pageModuleCode = DB::table('module')->where('varTitle', 'menu-type')->first();
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

        $moduleCode = DB::table('module')->select('id')->where('varModuleName', 'menu-type')->first();

        $headerMenu = DB::table('menu_type')->where('varTitle', 'Header Menu')->first();
        if (!isset($headerMenu->id) || empty($headerMenu->id)) {
            DB::table('menu_type')->insert([
                'varTitle' => 'Header Menu',
                'intAliasId' => MyLibrary::insertAlias(slug::create_slug('Header Menu'), $moduleCode->id),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);
        }

        $footerMenu = DB::table('menu_type')->where('varTitle', 'Footer Menu')->first();
        if (!isset($footerMenu->id) || empty($footerMenu->id)) {
            DB::table('menu_type')->insert([
                'varTitle' => 'Footer Menu',
                'intAliasId' => MyLibrary::insertAlias(slug::create_slug('Footer Menu'), $moduleCode->id),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);
        }
    }

}
