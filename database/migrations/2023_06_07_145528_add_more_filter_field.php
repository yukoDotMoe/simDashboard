<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMoreFilterField extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('services', function (Blueprint $table) {
            $table->integer('fail')->default(-1)->after('limit');
            $table->integer('success')->default(-1)->after('limit');
        });

        Schema::create('success_records', function (Blueprint $table) {
            $table->id();
            $table->string('uniqueId');
            $table->string('vendorId')->nullable();
            $table->string('phone');
            $table->string('serviceId');
            $table->string('requestId');
            $table->string('reason')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('failed_records', function (Blueprint $table) {
            $table->id();
            $table->string('uniqueId');
            $table->string('vendorId')->nullable();
            $table->string('phone');
            $table->string('serviceId');
            $table->string('requestId');
            $table->string('reason')->nullable();
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
        Schema::table('services', function (Blueprint $table) {
            $table->dropColumn('fail');
            $table->dropColumn('success');
        });

        Schema::dropIfExists('success_records');
        Schema::dropIfExists('failed_records');
    }
}
