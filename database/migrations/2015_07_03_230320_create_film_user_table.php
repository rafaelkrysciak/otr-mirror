<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFilmUserTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('film_user', function(Blueprint $table)
		{
			$table->increments('id')->unsigned();
			$table->integer('user_id')->unsigned();
			$table->integer('film_id')->unsigned();
			$table->timestamps();

			$table->foreign('film_id')
				->references('id')
				->on('films')
				->onDelete('cascade');

			$table->foreign('user_id')
				->references('id')
				->on('users')
				->onDelete('cascade');

			$table->unique(['film_id','user_id']);
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('film_user');
	}

}
