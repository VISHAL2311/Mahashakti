<?php
namespace Database\Seeders;

use App\Helpers\MyLibrary;
use DB;
use Illuminate\Database\Seeder;

class RoleUserTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $userObj = DB::table('users')->get();
        if ($userObj->count() > 0) {
            $model_has_roles = array();
            foreach ($userObj as $ukey => $uvalue) {

                if (MyLibrary::getDecryptedString($uvalue->email) == 'netquick@netclues.net') {
                    $model_has_roles[$ukey]['role_id'] = 1;
                } else if (MyLibrary::getDecryptedString($uvalue->email) == 'ppadmin@netclues.com') {
                    $model_has_roles[$ukey]['role_id'] = 2;
                } else {
                    $model_has_roles[$ukey]['role_id'] = 3;
                }

                $model_has_roles[$ukey]['model_type'] = 'App\User';
                $model_has_roles[$ukey]['model_id'] = $uvalue->id;
            }

            DB::table('model_has_roles')->insert($model_has_roles);
        }
    }
}