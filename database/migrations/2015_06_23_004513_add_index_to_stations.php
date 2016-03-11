<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddIndexToStations extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('stations', function(Blueprint $table)
		{
			$table->index('tvprogram_name', 'stations_tvprogram_name_index');
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
			$table->dropIndex('stations_tvprogram_name_index');
		});
	}

}
