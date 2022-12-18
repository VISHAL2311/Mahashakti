<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Carbon\Carbon;

class CreateTestimonialTable extends Migration {

    public function up() {
        Schema::create('testimonials', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id')->collation('utf8_general_ci');
            $table->string('varTitle')->collation('utf8_general_ci')->nullable();
            $table->unsignedInteger('fkIntImgId')->collation('utf8_general_ci')->nullable();
            $table->text('txtDescription')->collation('utf8_general_ci')->nullable();
            $table->datetime('dtStartDateTime')->collation('utf8_general_ci')->nullable();
            $table->integer('intDisplayOrder')->collation('utf8_general_ci')->default(0);
            $table->char('chrPublish', 1)->default('Y')->collation('utf8_general_ci');
            $table->char('chrDelete', 1)->default('N')->collation('utf8_general_ci');
            $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('updated_at')->default(NULL)->nullable();
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::drop('testimonials');
    }

}
