<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use DB;
use Schema;

class PermissionRoleTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $permissions = DB::table('permissions')->select('*')->whereNotIn('name', ['recent-updates-list', 'log-advanced'])->get();
        $roles = [];
        foreach ($permissions as $permission) {
            array_push($roles, [
                [
                    'permission_id' => $permission->id,
                    'role_id' => 1,
                ], [
                    'permission_id' => $permission->id,
                    'role_id' => 2,
                ], [
                    'permission_id' => $permission->id,
                    'role_id' => 3,
                ],
            ]);
        }

        foreach ($roles as $key => $value) {
            DB::table('role_has_permissions')->insert($value);
        }
    }
}
