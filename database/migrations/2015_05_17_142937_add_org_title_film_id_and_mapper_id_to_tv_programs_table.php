<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddOrgTitleFilmIdAndMapperIdToTvProgramsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('tv_programs', function(Blueprint $table)
		{
			$table->integer('film_id')->nullable()->index();
			$table->integer('film_mapper_id')->nullable()->index();
			$table->string('org_title')->nullable()->index();;
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('tv_programs', function(Blueprint $table)
		{
			$table->dropColumn('film_id');
			$table->dropColumn('film_mapper_id');
			$table->dropColumn('org_title');
		});
	}

}
