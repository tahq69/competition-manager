<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTeamMemberRoleTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('team_member_role', function (Blueprint $table) {
            $table->increments('id');

            $table->unsignedInteger('team_member_id');
            $table->foreign('team_member_id')->references('id')->on('team_members');

            $table->unsignedInteger('role_id');
            $table->foreign('role_id')->references('id')->on('roles');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('team_member_role');
    }
}
