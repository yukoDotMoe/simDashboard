<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateActivitiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('activitieslog', function (Blueprint $table) {
            $table->boolean('customRent')->default(false);
            $table->string('smsContent')->nullable()->change();
            $table->string('code')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('activitieslog', function (Blueprint $table) {
            $table->dropColumn('customRent');
            $table->string('smsContent')->change();
            $table->string('code')->change();
        });
    }
}
