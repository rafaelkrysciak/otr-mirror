<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CleanUpTvProgramsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		DB::statement("ALTER TABLE `tv_programs`
			CHANGE COLUMN `description` `description` TEXT NULL DEFAULT NULL COLLATE 'utf8_unicode_ci' AFTER `type`");

		Schema::table('tv_programs', function(Blueprint $table)
		{
			$table->dropColumn('weekday');
			$table->dropColumn('addition');
			$table->dropColumn('rerun');

			$table->dropColumn('downloadlink');
			$table->dropColumn('infolink');
			$table->dropColumn('programlink');

		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		DB::statement("ALTER TABLE `tv_programs`
			CHANGE COLUMN `description` `description` VARCHAR(8000) NULL COLLATE 'utf8_unicode_ci' AFTER `type`");

		Schema::table('tv_programs', function(Blueprint $table)
		{
			$table->string('weekday', 5)->nullable();
			$table->string('addition')->nullable();
			$table->string('rerun', 50)->nullable();

			$table->string('downloadlink')->nullable();
			$table->string('infolink')->nullable();
			$table->string('programlink')->nullable();

		});
	}

}
