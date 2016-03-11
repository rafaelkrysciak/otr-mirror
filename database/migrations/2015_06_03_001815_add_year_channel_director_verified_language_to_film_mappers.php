<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddYearChannelDirectorVerifiedLanguageToFilmMappers extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('film_mappers', function(Blueprint $table)
		{
			$table->smallInteger('year')->unsigned()->index()->default(0);
			$table->string('channel', 50)->index()->default('');
			$table->string('language', 50)->index()->default('');
			$table->string('director', 100)->index()->default('');
			$table->boolean('verified')->default(false);
			$table->dropUnique('film_mappers_org_title_unique');
			$table->index('org_title');

			DB::statement("ALTER TABLE `film_mappers`
				CHANGE COLUMN `max_length` `max_length` INT(10) UNSIGNED NOT NULL DEFAULT '0',
				CHANGE COLUMN `min_length` `min_length` INT(10) UNSIGNED NOT NULL DEFAULT '0'");

		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('film_mappers', function(Blueprint $table)
		{
			$table->dropColumn('year');
			$table->dropColumn('channel');
			$table->dropColumn('language');
			$table->dropColumn('director');
			$table->dropColumn('verified');
			$table->dropIndex('film_mappers_org_title_index');
			$table->unique('org_title');

			DB::statement("ALTER TABLE `film_mappers`
				ALTER `max_length` DROP DEFAULT,
				ALTER `min_length` DROP DEFAULT");
		});
	}

}
