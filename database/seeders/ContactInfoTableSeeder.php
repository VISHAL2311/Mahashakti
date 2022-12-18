<?php
namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use DB;
use Schema;

class ContactInfoTableSeeder extends Seeder
{

    public function run()
    {
        $pageModuleCode = DB::table('module')->select('id')->where('varTitle', 'Contact Info')->first();

        if (!isset($pageModuleCode->id) || empty($pageModuleCode->id)) {
            if (\Schema::hasColumn('module', 'intFkGroupCode')) {
                DB::table('module')->insert([
                    'intFkGroupCode' => '0',
                    'varTitle' => 'Contact Info',
                    'varModuleName' => 'contact-info',
                    'varTableName' => 'contact_info',
                    'varModelName' => 'ContactInfo',
                    'varModuleClass' => 'ContactInfoController',
                    'varModuleNameSpace' => 'Powerpanel\ContactInfo\\',
                    'decVersion' => 1.0,
                    'intDisplayOrder' => 0,
                    'chrIsFront' => 'Y',
                    'chrIsPowerpanel' => 'Y',
                    'varPermissions' => 'list, create, edit, delete, publish',
                    'chrPublish' => 'Y',
                    'chrDelete' => 'N',
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ]);
            } else {
                DB::table('module')->insert([
                    'varTitle' => 'Contact Info',
                    'varModuleName' => 'contact-info',
                    'varTableName' => 'contact_info',
                    'varModelName' => 'ContactInfo',
                    'varModuleClass' => 'ContactInfoController',
                    'varModuleNameSpace' => 'Powerpanel\ContactInfo\\',
                    'decVersion' => 1.0,
                    'intDisplayOrder' => 0,
                    'chrIsFront' => 'Y',
                    'chrIsPowerpanel' => 'Y',
                    'varPermissions' => 'list, create, edit, delete, publish',
                    'chrPublish' => 'Y',
                    'chrDelete' => 'N',
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ]);
            }

            $pageModuleCode = DB::table('module')->where('varTitle', 'Contact Info')->first();
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

        DB::table('contact_info')->insert([
            'varTitle' => 'Goverment Portal',
            'varEmail' => 'a:1:{i:0;s:20:"example@examsple.com";}',
            'varPhoneNo' => 'a:1:{i:0;s:15:"565465444653464";}',
            'intDisplayOrder' => '1',
            'txtAddress' => '11111',
            'mailingaddress' => '11111',
            'chrIsPrimary' => 'Y',
            'chrPublish' => 'Y',
            'chrDelete' => 'N',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);
    }

}
