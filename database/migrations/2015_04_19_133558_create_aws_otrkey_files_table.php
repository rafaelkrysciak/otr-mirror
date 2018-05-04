<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAwsOtrkeyFilesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('aws_otrkey_files', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('otrkeyfile_id')->unsigned()->unique();
			$table->timestamp('last_modified')->default('0000-00-00 00:00:00');
			$table->bigInteger('size')->unsigned();
			$table->string('checksum');
			$table->timestamps();

			$table->foreign('otrkeyfile_id')
				->references('id')
				->on('otrkey_files')
				->onDelete('restrict');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('aws_otrkey_files');
	}

}
