<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRefererStatsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('referer_stats', function (Blueprint $table) {
            $table->date('date')->index();
            $table->string('site_id');
            $table->unsignedInteger('pageviews');
            $table->unsignedInteger('visitors');
            $table->float('bounce_rate');
            $table->float('avg_duration');
            $table->unsignedInteger('known_durations')->default(0);
            $table->string('groupname');
            $table->string('host');
            $table->string('path');


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
        Schema::dropIfExists('referer_stats');
    }
}
