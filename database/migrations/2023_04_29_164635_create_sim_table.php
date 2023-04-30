<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSimTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sims', function (Blueprint $table) {
            $table->id();
            $table->string('uniqueId');
            $table->string('phone');
            $table->string('networkId');
            $table->string('countryCode');
            $table->integer('status');
            $table->integer('success');
            $table->integer('failed');
            $table->text('metadata');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('networks', function (Blueprint $table) {
            $table->id();
            $table->string('uniqueId');
            $table->string('networkName');
            $table->integer('status');
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
        Schema::dropIfExists('sims');
        Schema::dropIfExists('networks');
    }
}
