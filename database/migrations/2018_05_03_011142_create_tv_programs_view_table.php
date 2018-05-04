<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTvProgramsViewTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tv_programs_view', function (Blueprint $table) {
            $table->increments('id');
			$table->integer('film_id');
			$table->integer('year');
			$table->string('country');
			$table->string('genre');
			$table->string('amazon_image');
			$table->string('fsk');
			$table->double('imdb_rating');
			$table->integer('imdb_votes');
			$table->integer('tvseries');
			$table->integer('node_id');
			$table->integer('otrkeyfile_id');
			$table->integer('tv_program_id');
			$table->integer('otr_epg_id');
			$table->string('title');
			$table->timestamp('start');
			$table->integer('length');
			$table->string('description');
			$table->string('station');
			$table->string('language');
			$table->string('name');
			$table->integer('episode');
			$table->integer('season');
			$table->integer('size');
			$table->string('quality');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('tv_programs_view');
    }
}
