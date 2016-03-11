<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateStatDownloadsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('stat_downloads', function(Blueprint $table)
		{
			$table->increments('id')->unsigned();
			$table->date('event_date')->index();
			$table->integer('otrkey_file_id')->unsigned();
			$table->integer('downloads')->unsigned()->default(0);
			$table->integer('total_downloads')->unsigned()->default(0);
			$table->integer('aws_downloads')->unsigned()->default(0);
			$table->integer('aws_total_downloads')->unsigned()->default(0);

			$table->foreign('otrkey_file_id')
				->references('id')
				->on('otrkey_files')
				->onDelete('restrict');

			$table->unique(['event_date','otrkey_file_id']);
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('stat_downloads');
	}

}
