<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Carbon\Carbon;

class CreateWorkTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {

        Schema::create('work', function (Blueprint $table) {

            // $table->engine = 'InnoDB';
            $table->increments('id')->collation('utf8_general_ci');
            $table->unsignedInteger('intAliasId');
            $table->string('fkIntImgId', 400)->collation('utf8_general_ci')->nullable()->default(null);
            $table->string('fkIntVideoId', 400)->collation('utf8_general_ci')->nullable()->default(null);
            $table->string('varTitle', 255)->collation('utf8_general_ci')->nullable()->default(null);
            $table->string('varExternalLink', 255)->collation('utf8_general_ci')->nullable()->default(null);
            $table->string('varFontAwesomeIcon', 20)->collation('utf8_general_ci')->nullable()->default(null);
            $table->text('txtShortDescription')->collation('utf8mb4_unicode_ci')->nullable()->default(null);
            $table->text('txtDescription')->collation('utf8mb4_unicode_ci');
            $table->text('txtCategories')->collation('utf8mb4_unicode_ci');
            $table->string('varPreferences', 255)->collation('utf8_general_ci')->nullable()->default(null);
            $table->unsignedInteger('intDisplayOrder')->collation('utf8_general_ci')->default(0);
            $table->char('chrFeaturedWork', 1)->collation('utf8_general_ci')->default('N');
            $table->char('chrPublish', 1)->collation('utf8_general_ci')->default('Y');
            $table->char('chrDelete', 1)->collation('utf8_general_ci')->default('N');
            $table->text('varMetaTitle')->collation('utf8mb4_unicode_ci');
            $table->text('varMetaKeyword')->collation('utf8mb4_unicode_ci');
            $table->text('varMetaDescription')->collation('utf8mb4_unicode_ci');
            $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('updated_at')->default(DB::raw('CURRENT_TIMESTAMP'));
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::drop('work');
    }

}
