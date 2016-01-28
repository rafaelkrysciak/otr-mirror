<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTvProgramsTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tv_programs', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('otr_epg_id')->unsigned();
            $table->timestamp('start')->index();
            $table->timestamp('end');
            $table->smallInteger('length');
            $table->string('station', 50)->index();
            $table->string('title');
            $table->string('type', 50)->nullable();
            $table->string('description', 8000)->nullable();
            $table->integer('genre_id')->unsigned()->nullable();
            $table->string('fsk', 50)->nullable();
            $table->string('language', 5)->nullable();
            $table->string('weekday', 5)->nullable();
            $table->string('addition')->nullable();
            $table->string('rerun', 50)->nullable();
            $table->smallInteger('season')->unsigned()->nullable();
            $table->smallInteger('episode')->unsigned()->nullable();
            $table->string('downloadlink')->nullable();
            $table->string('infolink')->nullable();
            $table->string('programlink')->nullable();
            $table->timestamps();

            $table->unique(['start', 'station']);
        });

        Schema::create('tv_program_user', function(Blueprint $table)
        {
            $table->integer('user_id')->unsigned()->index();
            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');

            $table->integer('tv_program_id')->unsigned()->index();
            $table->foreign('tv_program_id')
                ->references('id')
                ->on('tv_programs')
                ->onDelete('cascade');

            $table->enum('type', ['favorite', 'watched', 'downloaded']);
            $table->timestamps();

            $table->primary(['user_id', 'tv_program_id', 'type']);
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('tv_program_user');
        Schema::drop('tv_programs');
    }

}
