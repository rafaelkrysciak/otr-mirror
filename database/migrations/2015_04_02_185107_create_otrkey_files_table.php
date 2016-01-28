<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOtrkeyFilesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('otrkey_files', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('tv_program_id')->unsigned()->nullable()->index();
			$table->string('name');
			$table->bigInteger('size')->unsigned()->nullable();
			$table->string('checksum')->nullable();
			$table->bigInteger('distro_size')->unsigned()->nullable();
			$table->string('distro_checksum')->nullable();
			$table->string('title');
			$table->timestamp('start')->index();
			$table->timestamp('mtime')->nullable();
			$table->string('station')->index();
			$table->smallInteger('duration')->unsigned();
			$table->string('language', 10);
			$table->string('quality', 10);
			$table->smallInteger('season')->unsigned()->nullable();
			$table->smallInteger('episode')->unsigned()->nullable();
			$table->timestamps();

			$table->unique('name');
			$table->foreign('tv_program_id')
				->references('id')
				->on('tv_programs')
				->onDelete('restrict');
		});

		Schema::create('distro_otrkeyfile', function(Blueprint $table)
		{
			$table->integer('distro_id')->unsigned()->index();
			$table->foreign('distro_id')
				->references('id')
				->on('distros')
				->onDelete('cascade');

			$table->integer('otrkeyfile_id')->unsigned()->index();
			$table->foreign('otrkeyfile_id')
				->references('id')
				->on('otrkey_files')
				->onDelete('cascade');
			$table->timestamps();

			$table->primary(['distro_id', 'otrkeyfile_id']);
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('distro_otrkeyfile');
		Schema::drop('otrkey_files');
	}

}
