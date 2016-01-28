<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateNodesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('nodes', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('short_name', 10);
			$table->string('url');
			$table->string('key', 100);
			$table->bigInteger('free_disk_space')->unsigned();
			$table->integer('busy_workers')->unsigned();
			$table->timestamps();
		});

		Schema::create('node_otrkeyfile', function(Blueprint $table)
		{
			$table->integer('otrkeyfile_id')->unsigned();
			$table->foreign('otrkeyfile_id')
				->references('id')
				->on('otrkey_files')
				->onDelete('cascade');

			$table->integer('node_id')->unsigned();
			$table->foreign('node_id')
				->references('id')
				->on('nodes')
				->onDelete('cascade');

			$table->enum('status', ['requested', 'downloaded', 'deleted'])->default('requested');
			$table->timestamps();
			$table->primary(['otrkeyfile_id', 'node_id']);
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('node_otrkeyfile');
		Schema::drop('nodes');
	}

}
