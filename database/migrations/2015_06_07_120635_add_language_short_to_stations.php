<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddLanguageShortToStations extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('stations', function(Blueprint $table)
		{
			$table->string('language_short', 2)->default('de');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('stations', function(Blueprint $table)
		{
			$table->dropColumn('language_short');
		});
	}

}
