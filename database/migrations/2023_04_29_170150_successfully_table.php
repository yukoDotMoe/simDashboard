<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class SuccessfullyTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('activitiesLog', function (Blueprint $table) {
            $table->id();
            $table->string('uniqueId');
            $table->string('userid');
            $table->string('phone');
            $table->string('networkId');
            $table->string('countryCode');
            $table->string('serviceId');
            $table->string('smsContent');
            $table->string('code');
            $table->string('status');
            $table->string('reason');
            $table->text('metadata');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('activitiesLog');
    }
}
