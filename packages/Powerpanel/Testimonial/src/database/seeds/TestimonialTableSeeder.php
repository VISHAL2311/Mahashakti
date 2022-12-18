<?php
namespace Database\Seeders;

use App\Helpers\MyLibrary;
use App\Http\Traits\slug;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use DB;
use Schema;

class TestimonialTableSeeder extends Seeder
{

    public function run()
    {
        $pageModuleCode = DB::table('module')->select('id')->where('varTitle', 'Testimonials')->first();

        if (!isset($pageModuleCode->id) || empty($pageModuleCode->id)) {
            if (\Schema::hasColumn('module', 'intFkGroupCode')) {
                DB::table('module')->insert([
                    'intFkGroupCode' => '2',
                    'varTitle' => 'Testimonials',
                    'varModuleName' => 'testimonial',
                    'varTableName' => 'testimonials',
                    'varModelName' => 'Testimonial',
                    'varModuleClass' => 'TestimonialController',
                    'varModuleNameSpace' => 'Powerpanel\Testimonial\\',
                    'decVersion' => 1.0,
                    'intDisplayOrder' => 0,
                    'chrIsFront' => 'Y',
                    'chrIsPowerpanel' => 'Y',
                    'varPermissions' => 'list, create, edit, delete, publish,reviewchanges',
                    'chrPublish' => 'Y',
                    'chrDelete' => 'N',
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ]);
            } else {
                DB::table('module')->insert([
                    'varTitle' => 'Testimonials',
                    'varModuleName' => 'testimonial',
                    'varTableName' => 'testimonials',
                    'varModelName' => 'Testimonial',
                    'varModuleClass' => 'TestimonialController',
                    'varModuleNameSpace' => 'Powerpanel\Testimonial\\',
                    'decVersion' => 1.0,
                    'intDisplayOrder' => 0,
                    'chrIsFront' => 'Y',
                    'chrIsPowerpanel' => 'Y',
                    'varPermissions' => 'list, create, edit, delete, publish,reviewchanges',
                    'chrPublish' => 'Y',
                    'chrDelete' => 'N',
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ]);
            }

            $pageModuleCode = DB::table('module')->where('varTitle', 'Testimonials')->first();
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

        $pageModuleCode = DB::table('module')->where('varTitle', 'Testimonials')->first();
        $cmsModuleCode = DB::table('module')->where('varTitle', 'pages')->first();
        $intFKModuleCode = $pageModuleCode->id;

        $exists = DB::table('cms_page')->select('id')->where('varTitle', htmlspecialchars_decode('Testimonials'))->first();

        if (!isset($exists->id)) {
            if (\Schema::hasColumn('cms_page', 'chrMain')) {
                DB::table('cms_page')->insert([
                    'varTitle' => htmlspecialchars_decode('Testimonials'),
                    'intAliasId' => MyLibrary::insertAlias(slug::create_slug(htmlspecialchars_decode('Testimonials')), $cmsModuleCode->id),
                    'intFKModuleCode' => $intFKModuleCode,
                    'txtDescription' => '',
                    'chrPublish' => 'Y',
                    'chrMain' => 'Y',
                    'chrDelete' => 'N',
                    'varMetaTitle' => 'Testimonials',
                    'varMetaKeyword' => 'Testimonials',
                    'varMetaDescription' => 'Testimonials',
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ]);
            } else {
                DB::table('cms_page')->insert([
                    'varTitle' => htmlspecialchars_decode('Testimonials'),
                    'intAliasId' => MyLibrary::insertAlias(slug::create_slug(htmlspecialchars_decode('Testimonials')), $cmsModuleCode->id),
                    'intFKModuleCode' => $intFKModuleCode,
                    'txtDescription' => '',
                    'chrPublish' => 'Y',
                    'chrDelete' => 'N',
                    'varMetaTitle' => 'Testimonials',
                    'varMetaKeyword' => 'Testimonials',
                    'varMetaDescription' => 'Testimonials',
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ]);
            }
        }

        $pageObj = DB::table('cms_page')->select('id')->where('varTitle', 'Testimonials')->first();

        $moduleCode = DB::table('module')->select('id')->where('varModuleName', 'testimonial')->first();

        DB::table('testimonials')->insert([
            'varTitle' => 'Testimonials 1',
            'txtDescription' => 'The standard Lorem Ipsum passage, used since the 1500s',
            'intDisplayOrder' => '1',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);
        DB::table('testimonials')->insert([
            'varTitle' => 'Testimonials 2',
            'txtDescription' => 'The standard Lorem Ipsum passage, used since the 1500s',
            'intDisplayOrder' => '2',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);
        DB::table('testimonials')->insert([
            'varTitle' => 'Testimonials 3',
            'txtDescription' => 'The standard Lorem Ipsum passage, used since the 1500s',
            'intDisplayOrder' => '3',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        //Adding Testimonials Module In visual composer
        if (Schema::hasTable('visualcomposer')) {
            $TestimonialsModule = DB::table('visualcomposer')->select('id')->where('varTitle', 'Testimonials')->where('fkParentID', '0')->first();

            if (!isset($TestimonialsModule->id) || empty($TestimonialsModule->id)) {
                DB::table('visualcomposer')->insert([
                    'fkParentID' => '0',
                    'varTitle' => 'Testimonials',
                    'varIcon' => '',
                    'varClass' => '',
                    'varTemplateName' => '',
                    'varModuleID' => $pageModuleCode->id,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ]);
            }

            $TestimonialsModule = DB::table('visualcomposer')->select('id')->where('varTitle', 'Testimonials')->where('fkParentID', '0')->first();

            $TestimonialsChild = DB::table('visualcomposer')->select('id')->where('varTitle', 'Testimonials')->where('fkParentID', '<>', '0')->first();

            if (!isset($TestimonialsChild->id) || empty($TestimonialsChild->id)) {
                DB::table('visualcomposer')->insert([
                    'fkParentID' => $TestimonialsModule->id,
                    'varTitle' => 'Testimonials',
                    'varIcon' => 'fa fa-comments-o',
                    'varClass' => 'testimonials',
                    'varTemplateName' => 'testimonial::partial.testimonial',
                    'varModuleID' => $pageModuleCode->id,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ]);
            }

            $latestTestimonials = DB::table('visualcomposer')->select('id')->where('varTitle', 'All Testimonials')->where('fkParentID', '0')->first();

            if (!isset($latestTestimonials->id) || empty($latestTestimonials->id)) {
                DB::table('visualcomposer')->insert([
                    'fkParentID' => $TestimonialsModule->id,
                    'varTitle' => 'All Testimonials',
                    'varIcon' => 'fa fa-comments-o',
                    'varClass' => 'testimonial-template',
                    'varTemplateName' => 'testimonial::partial.all-testimonial',
                    'varModuleID' => $pageModuleCode->id,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ]);
            }
        }
    }

}
