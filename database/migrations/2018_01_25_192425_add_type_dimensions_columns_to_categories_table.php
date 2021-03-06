<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddTypeDimensionsColumnsToCategoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('categories', function (Blueprint $table) {
            $table->string('display_type')->nullable();
            $table->string('type')->nullable();
            $table->integer('min')->unsigned()->default(0);
            $table->integer('max')->unsigned()->default(0);
        });

        Schema::table('categories', function (Blueprint $table) {
            $table->string('display_type')->nullable(false)->change();
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
        Schema::table('categories', function (Blueprint $table) {
            $table->dropColumn('display_type');
            $table->dropColumn('type');
            $table->dropColumn('min');
            $table->dropColumn('max');
        });
    }
}
