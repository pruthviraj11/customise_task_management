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
        Schema::create('internal_notifications', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('notification_from');
            $table->unsignedBigInteger('notification_to');
            $table->unsignedBigInteger('inquiry_id');
            $table->text('message');
            $table->string('notification_type');
            $table->string('notification_status'); 
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
        Schema::dropIfExists('internal_notifications');
    }
};
