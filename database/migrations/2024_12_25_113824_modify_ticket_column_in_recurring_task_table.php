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
        Schema::table('recurring_task', function (Blueprint $table) {
            $table->integer('ticket')->default(0)->change(); // Change type to integer and set default value to 0
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('recurring_task', function (Blueprint $table) {
            $table->string('ticket')->change(); // Assuming the original type was string, change it back to string.
        });
    }
};
