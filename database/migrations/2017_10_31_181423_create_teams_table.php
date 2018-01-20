<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 * Class CreateTeamsTable
 */
class CreateTeamsTable extends \App\Helpers\Migration
{
    /**
     * Run the migrations.
     * @return void
     */
    public function up()
    {
        Schema::create('teams', function (Blueprint $table) {
            $table->increments('id');

            $table->string('name');
            $table->string('short', 15);

            $table->string('logo', 1000)->default('');

            $table->smallInteger('_credits')->default(0);

            $this->audit($table);
            $table->timestamps();
        });

        Schema::table('competitions', function (Blueprint $table) {
            $table->unsignedInteger('team_id')->default(0);
            $table->foreign('team_id')
                ->references('id')
                ->on('teams')
                ->onDelete('no action');

            $table->string('team_name')->default('');
            $table->string('team_short', 15)->default('');
        });
    }

    /**
     * Reverse the migrations.
     * @return void
     */
    public function down()
    {
        Schema::table('competitions', function (Blueprint $table) {
            $table->dropForeign(['team_id']);
            $table->dropColumn(['team_id', 'team_name', 'team_short']);
        });

        Schema::dropIfExists('teams');
    }
}
