<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMessagesTable extends \App\Helpers\Migration
{
    /**
     * Run the migrations.
     * @return void
     */
    public function up()
    {
        Schema::create('messages', function (Blueprint $table) {
            $table->increments('id');

            $table->string('subject');
            $table->text('body');
            $table->text('payload');
            $table->boolean('is_read')->default(false);
            $table->smallInteger('importance_level')
                ->unsigned()
                ->default(10);
            $table->string('type', 50);

            $table->unsignedInteger('from_id');
            $table->foreign('from_id')
                ->references('id')
                ->on('users')
                ->onDelete('no action');

            $table->string('from_name');

            $table->unsignedInteger('to_id');
            $table->foreign('to_id')
                ->references('id')
                ->on('users')
                ->onDelete('no action');

            $table->string('to_name');

            $table->unsignedInteger('reply')->nullable();
            $table->unsignedSmallInteger('reply_count')->default(0);

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
        Schema::dropIfExists('messages');
    }
}
