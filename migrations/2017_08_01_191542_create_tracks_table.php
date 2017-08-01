<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTracksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tracks', function (Blueprint $blueprint) {
            $blueprint->increments('id')->unsigned();
            $blueprint->string('ref_table', 255);
            $blueprint->integer('ref_id')->unsigned();
            $blueprint->enum('type', ['add', 'edit', 'delete']);
            $blueprint->longText('description');
            $blueprint->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tracks');
    }
}
