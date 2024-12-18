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
            $table->date('completed_date')->nullable()->after(column: 'reopen_by');
            $table->string('completed_by')->nullable()->after('completed_date');
            $table->date('close_date')->nullable()->after(column: 'completed_by');
            $table->string('close_by')->nullable()->after('close_date');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('taskAssignment', function (Blueprint $table) {
            //
        });
    }
};
