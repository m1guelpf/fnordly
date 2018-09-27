<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePageStatsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('page_stats', function (Blueprint $table) {
            $table->date('date')->index();
            $table->string('site_id');
            $table->string('hostname');
            $table->string('pathname');
            $table->unsignedInteger('pageviews');
            $table->unsignedInteger('visitors');
            $table->unsignedInteger('entries');
            $table->float('bounce_rate');
            $table->float('avg_duration');
            $table->unsignedInteger('known_durations');


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
        Schema::dropIfExists('page_stats');
    }
}
