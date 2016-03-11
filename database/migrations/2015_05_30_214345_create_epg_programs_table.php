<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEpgProgramsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('epg_programs', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('tv_program_id')->unsigned()->index();
			$table->string('channel')->index();
			$table->timestamp('start')->index();
			$table->timestamp('stop');
			$table->string('title_de');
			$table->string('title_xx');
			$table->string('sub_title');
			$table->text('desc');
			$table->string('category');
			$table->string('directors');
			$table->text('actors');
			$table->smallInteger('date')->index();
			$table->smallInteger('episode');
			$table->smallInteger('season');
			$table->smallInteger('episode_total');
			$table->timestamps();

			$table->unique(['channel', 'start']);
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('epg_programs');
	}

}
