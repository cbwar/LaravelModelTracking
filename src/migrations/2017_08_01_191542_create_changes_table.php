<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateChangesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('changes', function (Blueprint $blueprint) {
            $blueprint->increments('id')->unsigned();
            $blueprint->string('ref_model', 255);
            $blueprint->string('ref_title', 255);
            $blueprint->integer('ref_id')->unsigned();
            $blueprint->integer('user_id')->unsigned()->nullable();
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
        Schema::dropIfExists('changes');
    }
}
