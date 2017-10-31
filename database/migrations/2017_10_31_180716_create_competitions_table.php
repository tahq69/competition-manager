<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

class CreateCompetitionsTable extends \App\Helpers\Migration
{
    /**
     * Run the migrations.
     * @return void
     */
    public function up()
    {
        Schema::create('competitions', function (Blueprint $table) {
            $table->increments('id');

            $table->string('title');
            $table->string('subtitle');
            $table->longText('cooperation');
            $table->longText('invitation');
            $table->longText('program');
            $table->longText('rules');
            $table->longText('ambulance');
            $table->longText('prizes');
            $table->longText('equipment');
            $table->longText('price');
            $table->timestamp('organization_date')->nullable();
            $table->timestamp('registration_till')->nullable();

            $table->unsignedInteger('judge_id')->nullable();
            $table->foreign('judge_id')
                ->references('id')
                ->on('users')
                ->onDelete('no action');

            $table->string('judge_name');

            $this->audit($table);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('competitions');
    }
}
