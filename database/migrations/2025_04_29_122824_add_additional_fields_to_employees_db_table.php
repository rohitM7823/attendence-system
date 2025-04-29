<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
{
    Schema::table('employees_db', function (Blueprint $table) {
        $table->string('site_name')->nullable();
        $table->json('location')->nullable(); 
        $table->text('face_metadata')->nullable();
        $table->timestamp('clock_in')->nullable();
        $table->timestamp('clock_out')->nullable();
    });
}

public function down()
{
    Schema::table('employees_db', function (Blueprint $table) {
        $table->dropColumn(['site_name', 'location', 'face_metadata', 'clock_in', 'clock_out']);
    });
}

};
