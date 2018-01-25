<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddTypeDimensionsColumnsToCategoryGroupsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('category_groups', function (Blueprint $table) {
            $table->integer('rounds')->unsigned();
            $table->integer('time')->unsigned();
            $table->string('type');
            $table->integer('min')->unsigned();
            $table->integer('max')->unsigned();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('category_groups', function (Blueprint $table) {
            $table->dropColumn('rounds');
            $table->dropColumn('time');
            $table->dropColumn('type');
            $table->dropColumn('min');
            $table->dropColumn('max');
        });
    }
}
