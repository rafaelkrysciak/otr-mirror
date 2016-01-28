<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFilmMappersTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('film_mappers', function(Blueprint $table)
		{
			$table->increments('id');

			$table->integer('film_id')->unsigned();
			$table->string('org_title')->unique();
			$table->string('new_title');
			$table->integer('max_length')->unsigned();
			$table->integer('min_length')->unsigned();

			$table->timestamps();

			$table->foreign('film_id')
				->references('id')
				->on('films')
				->onDelete('cascade');

		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('film_mappers');
	}

}
