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
            $table->integer('number_of_days')->nullable();
            $table->string('recurring_type')->nullable()->after('number_of_days');
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
            $table->dropColumn(['number_of_days', 'recurring_type']);
        });
    }
};
