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
            $table->string('rejected_by')->nullable(); // Adding the 'rejected_by' field
            $table->timestamp('rejected_date')->nullable();
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
            $table->dropColumn('rejected_by'); // Dropping the 'rejected_by' field
            $table->dropColumn('rejected_date'); // Dropping the 'rejected_date' field
        });
    }
};
