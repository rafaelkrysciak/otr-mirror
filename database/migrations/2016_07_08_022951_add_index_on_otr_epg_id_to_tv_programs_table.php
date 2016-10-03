<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddIndexOnOtrEpgIdToTvProgramsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tv_programs', function (Blueprint $table) {
            $table->index('otr_epg_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tv_programs', function (Blueprint $table) {
            $table->dropIndex('otr_epg_id');
        });
    }
}
