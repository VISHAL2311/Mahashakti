<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use DB;
use Schema;

class SiteMonitorTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        DB::table('site_monitor')->insert([
            'varTitle' => 'NetcluesMonitoring',
            'chrDelete' => 'N',
        ]);
        #=
        #==
    }

}
