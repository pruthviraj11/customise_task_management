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
        Schema::create('reopen_reasons', function (Blueprint $table) {
            $table->id();
            $table->string('reason')->nullable();  // Reason for reopening
            $table->date('reopen_date')->nullable();  // The date of reopening
            $table->string('reopen_by')->nullable();  // Who reopened (e.g., user name)
            $table->integer('user_id')->nullable();  // User ID related to the reopening
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
        Schema::dropIfExists('reopen_reasons');
    }
};
