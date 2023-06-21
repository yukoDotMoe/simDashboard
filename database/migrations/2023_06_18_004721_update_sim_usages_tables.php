<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateSimUsagesTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sim_activities', function (Blueprint $table) {
            $table->string('simId')->after('id');
            $table->string('vendorId')->after('id')->nullable();
        });
        
        Schema::table('success_records', function (Blueprint $table) {
            $table->string('simId')->after('id');
            
        });
        
        Schema::table('failed_records', function (Blueprint $table) {
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
        Schema::table('sim_activities', function (Blueprint $table) {
            $table->dropColumn('simId');
            $table->dropColumn('vendorId');
        });
        
        Schema::table('success_records', function (Blueprint $table) {
            $table->dropColumn('simId');
        });
        
        Schema::table('failed_records', function (Blueprint $table) {
            $table->dropColumn('simId');
        });
    }
}
