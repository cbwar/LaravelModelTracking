<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateChangesTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('changes', function (Blueprint $blueprint) {
            $blueprint->increments('id');
            $blueprint->string('ref_model', 255);
            $blueprint->string('ref_title', 255);
            $blueprint->integer('ref_id', false, true);
            $blueprint->integer('user_id', false, true)->nullable();
            $blueprint->enum('type', ['add', 'edit', 'delete']);
            $blueprint->longText('description');
            $blueprint->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('changes');
    }
}
