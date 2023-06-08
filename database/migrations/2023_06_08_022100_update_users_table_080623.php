<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateUsersTable080623 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->integer('profit')->nullable();
        });

        Schema::create('vendors_balance', function (Blueprint $table) {
            $table->id();
            $table->string('uniqueId');
            $table->integer('vendorId');
            $table->integer('userID');
            $table->string('requestID');
            $table->integer('amount');
            $table->string('type');
            $table->string('reason');
            $table->timestamps();
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
            $table->dropColumn('profit');
        });
        Schema::dropIfExists('vendors_balance');
    }
}
