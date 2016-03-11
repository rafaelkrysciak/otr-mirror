<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePromotionsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('promotions', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('title');
			$table->integer('tv_program_id')->unsigned()->nullable()->index();
			$table->string('search')->nullable();
			$table->integer('position')->unsigned();
			$table->boolean('active')->default(true);
			$table->timestamps();

			$table->foreign('tv_program_id')
				->references('id')
				->on('tv_programs')
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
		Schema::drop('promotions');
	}

}
