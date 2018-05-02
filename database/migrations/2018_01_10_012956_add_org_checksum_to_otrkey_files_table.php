<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddOrgChecksumToOtrkeyFilesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('otrkey_files', function (Blueprint $table) {
	        $table->string('org_checksum')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('otrkey_files', function (Blueprint $table) {
	        $table->dropColumn('org_checksum');
        });
    }
}
