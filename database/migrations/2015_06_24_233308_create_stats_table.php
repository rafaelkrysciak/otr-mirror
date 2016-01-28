<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateStatsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('stats', function(Blueprint $table)
		{
			$table->increments('id');
			$table->date('event_date')->index();
			$table->string('language', 2)->index();
			$table->string('station', 50)->index();
			$table->string('quality', 10)->index();
			$table->integer('downloads')->unsigned();
			$table->bigInteger('size')->unsigned();
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('stats');
	}

}
