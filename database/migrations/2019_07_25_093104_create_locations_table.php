<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLocationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('locations', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('city', 100);
            $table->string('state', 100);
            $table->string('country', 100);
            $table->integer('zip_code');
            $table->string('address', 100);
            $table->bigInteger('hotel_id')->unsigned()->index();
            $table->timestamps();
        });

        Schema::table('locations', function($table) {
            $table->foreign('hotel_id')->references('id')->on('hotels')->onDelete('cascade');
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('locations');
    }
}
