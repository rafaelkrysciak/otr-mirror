<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFilmsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('films', function(Blueprint $table)
		{
			$table->increments('id')->unsigned();

			$table->string('title');
			$table->string('original_title')->nullable();
			$table->text('description')->nullable();
			$table->smallInteger('year')->unsigned()->nullable();
			$table->string('country')->nullable();
			$table->string('genre')->nullable();
			$table->string('director')->nullable();
			$table->string('fsk', 20)->nullable();

			$table->string('amazon_asin', 20)->nullable();
			$table->string('amazon_link')->nullable();
			$table->string('amazon_image')->nullable();

			$table->timestamp('imdb_last_update')->default('0000-00-00 00:00:00');
			$table->string('imdb_id', 10)->unique();
			$table->float('imdb_rating')->unsigned()->nullable();
			$table->integer('imdb_votes')->unsigned()->nullable();
			$table->smallInteger('imdb_runtime')->unsigned()->nullable();
			$table->string('imdb_image')->nullable();

			$table->string('trailer')->nullable();
			$table->string('dvdkritik')->nullable();

			$table->boolean('tvseries')->default(false);
			$table->timestamps();
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('films');
	}

}
