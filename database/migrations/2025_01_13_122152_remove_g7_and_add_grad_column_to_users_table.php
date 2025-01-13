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
        Schema::table('users', function (Blueprint $table) {
            // Remove the G7 column
            $table->dropColumn('G7');

            // Add the Grad column with a string datatype
            $table->string('Grad')->nullable();  // You can choose to make it nullable if needed
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            // Revert the changes by adding the G7 column back and removing the Grad column
            $table->integer('G7')->nullable(); // Assuming G7 was an integer before, adjust if needed
            $table->dropColumn('Grad');
        });
    }
};
