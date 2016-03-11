<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddYearDirectorAndProposedFilmToTvProgramsTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tv_programs', function (Blueprint $table) {
            $table->smallInteger('year')->unsigned()->index()->default(0);
            $table->string('director', 100)->index()->nullable();
            $table->integer('proposed_film_id')->index()->nullable()->unsigned();

            $table->foreign('proposed_film_id')
                ->references('id')
                ->on('films')
                ->onDelete('no action')
                ->onUpdate('no action');

            DB::statement("ALTER TABLE `tv_programs`
	          CHANGE COLUMN `language` `language` VARCHAR(50) NULL DEFAULT NULL COLLATE 'utf8_unicode_ci'");
        });
    }


    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tv_programs', function (Blueprint $table) {
            $table->dropColumn('year');
            $table->dropColumn('director');
            $table->dropForeign('tv_programs_proposed_film_id_foreign');
            $table->dropColumn('proposed_film_id');

            DB::statement("ALTER TABLE `tv_programs`
	          CHANGE COLUMN `language` `language` VARCHAR(5) NULL DEFAULT NULL COLLATE 'utf8_unicode_ci'");

        });
    }

}
