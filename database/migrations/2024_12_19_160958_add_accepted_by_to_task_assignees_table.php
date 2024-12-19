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
            //
            $table->string('accepted_by')->nullable()->after('close_by');
            $table->date('accepted_date')->nullable()->after('completed_by');
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
            //
        });
    }
};
