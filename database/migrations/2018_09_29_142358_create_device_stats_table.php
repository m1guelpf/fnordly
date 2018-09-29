<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDeviceStatsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('device_stats', function (Blueprint $table) {
            $table->date('date')->index();
            $table->string('site_id');
            $table->unsignedInteger('pageviews');
            $table->unsignedInteger('desktop');
            $table->unsignedInteger('mobile');
            $table->unsignedInteger('tablet');
            $table->unsignedInteger('other');

            $table->foreign('site_id')->references('id')->on('sites')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('device_stats');
    }
}
