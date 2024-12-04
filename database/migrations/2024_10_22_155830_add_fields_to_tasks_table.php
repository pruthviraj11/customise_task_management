<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFieldsToTasksTable extends Migration
{
    public function up()
    {
        Schema::table('tasks', function (Blueprint $table) {
            $table->string('department_name')->nullable(); // Add department_name
            $table->string('project_name')->nullable();    // Add project_name
            $table->string('priority_name')->nullable();    // Add priority_name
            $table->string('status_name')->nullable();      // Add status_name
        });
    }

    public function down()
    {
        Schema::table('tasks', function (Blueprint $table) {
            $table->dropColumn('department_name');
            $table->dropColumn('project_name');
            $table->dropColumn('priority_name');
            $table->dropColumn('status_name');
        });
    }
}
