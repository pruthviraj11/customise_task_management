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
        Schema::create('recurring_task', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('priority_id')->nullable();
            $table->unsignedBigInteger('project_id')->nullable();
            $table->unsignedBigInteger('department_id')->nullable();
            $table->string('task_assignes')->nullable(); // Assuming it's a string type
            $table->unsignedBigInteger('sub_department_id')->nullable();
            $table->string('task_status')->nullable();
            $table->string('title')->nullable();
            $table->string('subject')->nullable();
            $table->text('description')->nullable();
            $table->date('start_date')->nullable();
            $table->date('due_date')->nullable();
            $table->date('accepted_date')->nullable();
            $table->date('completed_date')->nullable();
            $table->unsignedBigInteger('completed_by')->nullable();
            $table->boolean('closed')->nullable();
            $table->string('ticket')->nullable();
            $table->date('close_date')->nullable();
            $table->unsignedBigInteger('close_by')->nullable();
            $table->string('department_name')->nullable();
            $table->string('project_name')->nullable();
            $table->string('priority_name')->nullable();
            $table->string('status_name')->nullable();
            $table->string('TaskNumber')->nullable();
            $table->integer('deleted_by')->nullable();
            $table->integer('created_by')->nullable();
            $table->integer('updated_by')->nullable();
            $table->softDeletes();
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
        Schema::dropIfExists('recurring_task');
    }
};
