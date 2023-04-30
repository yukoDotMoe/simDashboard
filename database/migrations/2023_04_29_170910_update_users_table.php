<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->integer('tier');
            $table->integer('balance');
            $table->string('phoneNumber');
        });

        Schema::create('balancesLog', function (Blueprint $table) {
            $table->id();
            $table->string('uniqueId');
            $table->string('accountId');
            $table->string('oldBalance');
            $table->string('newBalance');
            $table->string('totalChange');
            $table->integer('status');
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
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('tier');
            $table->dropColumn('balance');
            $table->dropColumn('phoneNumber');
        });
        Schema::dropIfExists('balancesLog');
    }
}
