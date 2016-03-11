<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDistrosTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('distros', function(Blueprint $table)
		{
			$table->increments('id')->unsigned();
			$table->string('type');
			$table->string('host');
			$table->smallInteger('port')->unsigned();
			$table->string('username');
			$table->string('password');
			$table->string('index_url');
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
		Schema::drop('distros');
	}

}
