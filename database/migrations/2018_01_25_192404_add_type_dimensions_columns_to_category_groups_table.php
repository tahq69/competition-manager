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
            $table->integer('rounds')->unsigned()->default(0);
            $table->integer('time')->unsigned()->default(0);
            $table->string('type')->nullable();
            $table->integer('min')->unsigned()->default(0);
            $table->integer('max')->unsigned()->default(0);
        });

        Schema::table('category_groups', function (Blueprint $table) {
            $table->string('type')->nullable(false)->change();
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
