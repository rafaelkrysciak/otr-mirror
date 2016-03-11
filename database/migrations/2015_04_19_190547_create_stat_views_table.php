<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateStatViewsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('stat_views', function(Blueprint $table)
		{
			$table->increments('id')->unsigned();
			$table->date('event_date')->index();
			$table->integer('tv_program_id')->unsigned();
			$table->integer('views')->unsigned()->default(0);
			$table->integer('total_views')->unsigned()->default(0);

			$table->foreign('tv_program_id')
				->references('id')
				->on('tv_programs')
				->onDelete('restrict');

			$table->unique(['event_date','tv_program_id']);
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('stat_views');
	}

}
