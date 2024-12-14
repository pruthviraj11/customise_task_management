<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('task_assignees', function (Blueprint $table) {
            $table->string('department')->nullable();  // Add department column
            $table->string('sub_department')->nullable();  // Add sub_department column
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('task_assignees', function (Blueprint $table) {
            $table->dropColumn(['department', 'sub_department']);
        });
    }
};
