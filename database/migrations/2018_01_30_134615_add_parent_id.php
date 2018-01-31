<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddParentId extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('changes', function (Blueprint $blueprint) {
            $blueprint->string('parent_ref_model', 100)
                ->after('user_id')->nullable();

            $blueprint->integer('parent_ref_id', false, true)
                ->after('parent_ref_model')->nullable();

            $blueprint->string('title', 100)
                ->after('type')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('changes', function (Blueprint $blueprint) {
            $blueprint->dropColumn('parent_ref_model');
            $blueprint->dropColumn('parent_ref_id');
            $blueprint->dropColumn('title');
        });
    }
}
