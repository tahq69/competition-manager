<?php namespace App\Helpers;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration as LaravelMigration;

/**
 * Class Migration
 * @package App\Helpers
 */
class Migration extends LaravelMigration
{
    protected function audit(Blueprint $table)
    {
        $table->unsignedInteger('created_by')->index();
        $table->foreign('created_by')
            ->references('id')
            ->on('users')
            ->onDelete('no action');

        $table->string('created_by_name');

        $table->unsignedInteger('updated_by')->nullable();
        $table->foreign('updated_by')
            ->references('id')
            ->on('users')
            ->onDelete('no action');

        $table->string('updated_by_name')->default('');
    }
}