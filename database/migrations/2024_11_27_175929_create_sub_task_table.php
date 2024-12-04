<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sub_tasks', function (Blueprint $table) {
            $table->id(); // Auto-increment primary key
            $table->foreignId('task_id')->nullable()->constrained()->onDelete('cascade'); // Foreign key to tasks table
            $table->foreignId('assign_to_id')->nullable()->constrained('users')->onDelete('set null'); // Foreign key to users table (nullable)
            $table->string('name'); // Name of the sub-task
            $table->timestamp('deleted_at')->nullable(); // Soft delete timestamp
            $table->foreignId('deleted_by')->nullable()->constrained('users')->onDelete('set null'); // Deleted by (user)
            $table->timestamps(); // created_at, updated_at timestamps
            $table->foreignId('created_by')->constrained('users'); // Created by (user)
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null'); // Updated by (user)
            $table->integer('priority_id')->nullable();
            $table->integer('project_id')->nullable();
            $table->integer('department_id')->nullable();
            $table->integer('sub_department_id')->nullable();
            $table->string('task_status')->nullable();
            $table->string('title')->nullable();
            $table->string('subject')->nullable();
            $table->text('description')->nullable();
            $table->date('start_date')->nullable();
            $table->date('due_date')->nullable();
            $table->date('accepted_date')->nullable();
            $table->date('completed_date')->nullable();
            $table->boolean('closed')->nullable();
            $table->string('ticket')->nullable();
            $table->date('close_date')->nullable();
            $table->string('department_name')->nullable();
            $table->string('project_name')->nullable();
            $table->string('priority_name')->nullable();
            $table->string('status_name')->nullable();
            $table->string('TaskNumber')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sub_tasks');
    }
};
