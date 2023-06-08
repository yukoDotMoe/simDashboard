<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddLimitToServices extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sim_activities', function (Blueprint $table) {
            $table->id();
            $table->string('phoneNumber');
            $table->string('serviceId');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::table('services', function (Blueprint $table) {
            $table->integer('limit')->default(-1);
            $table->integer('cooldown')->default(1);
            $table->text('structure')->nullable();
            $table->text('valid')->nullable();
        });

        Schema::table('activitieslog', function (Blueprint $table) {
            $table->string('handleByVendor')->nullable();
        });

        Schema::table('balanceslog', function (Blueprint $table) {
            $table->string('handleByVendor')->nullable();
        });

        Schema::table('sims', function (Blueprint $table) {
            $table->text('locked_services')->nullable();
            $table->text('working_services')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sim_activities');
        Schema::table('services', function (Blueprint $table) {
            $table->dropColumn('limit');
            $table->dropColumn('cooldown');
            $table->dropColumn('structure');
            $table->dropColumn('valid');
        });
        Schema::table('activitieslog', function (Blueprint $table) {
            $table->dropColumn('handleByVendor');
        });
        Schema::table('balanceslog', function (Blueprint $table) {
            $table->dropColumn('handleByVendor');
        });
        Schema::table('sims', function (Blueprint $table) {
            $table->dropColumn('locked_services');
            $table->dropColumn('working_services');
        });
    }
}
