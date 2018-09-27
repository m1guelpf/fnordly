<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePageviewsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pageviews', function (Blueprint $table) {
            $table->string('id')->unique();
            $table->string('site_id');
            $table->string('host');
            $table->string('path');
            $table->boolean('newVisitor');
            $table->boolean('newSession');
            $table->boolean('unique');
            $table->boolean('bounce');
            $table->string('referer');
            $table->string('user_agent');
            $table->integer('duration');
            $table->timestamp('visited_at');
            $table->timestamps();

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
        Schema::dropIfExists('pageviews');
    }
}
