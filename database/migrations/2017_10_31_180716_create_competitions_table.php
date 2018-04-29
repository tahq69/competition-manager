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
            $table->longText('cooperation')->nullable();
            $table->longText('invitation')->nullable();
            $table->longText('program')->nullable();
            $table->longText('rules')->nullable();
            $table->longText('ambulance')->nullable();
            $table->longText('prizes')->nullable();
            $table->longText('equipment')->nullable();
            $table->longText('price')->nullable();
            $table->timestamp('organization_date');
            $table->timestamp('registration_till');

            $table->unsignedInteger('judge_id')->nullable();
            $table->foreign('judge_id')
                ->references('id')
                ->on('users')
                ->onDelete('no action');

            $table->string('judge_name')->nullable();

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
