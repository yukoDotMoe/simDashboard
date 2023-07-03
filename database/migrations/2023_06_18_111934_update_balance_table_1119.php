<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateBalanceTable1119 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Schema::table('balanceslog', function (Blueprint $table) {
        //     $table->string('simId')->after('id');
        // });
        
        Schema::table('activitieslog', function (Blueprint $table) {
            $table->string('simId')->after('id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Schema::table('balanceslog', function (Blueprint $table) {
        //     $table->dropColumn('simId');
        // });
        
        Schema::table('activitieslog', function (Blueprint $table) {
            $table->dropColumn('simId');
        });
    }
}
