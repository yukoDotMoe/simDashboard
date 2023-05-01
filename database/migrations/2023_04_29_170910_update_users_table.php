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
            $table->integer('tier')->default(0);
            $table->boolean('admin')->default(false);
            $table->integer('balance')->default(0);
            $table->string('phoneNumber')->nullable();
            $table->string('api_token', 80)->unique()->nullable()->default(null);
        });

        Schema::create('balancesLog', function (Blueprint $table) {
            $table->id();
            $table->string('uniqueId');
            $table->string('accountId');
            $table->string('activityId');
            $table->string('oldBalance');
            $table->string('newBalance');
            $table->string('totalChange');
            $table->integer('status');
            $table->string('reason');
            $table->text('metadata')->nullable();
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
