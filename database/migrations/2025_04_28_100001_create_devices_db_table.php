<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('devices_db', function (Blueprint $table) {
            $table->id(); // id (primary key, auto increment)
            $table->string('name');
            $table->string('os_version');
            $table->string('platform');
            $table->string('model');
            $table->string('read_id');
            $table->enum('status', ['Approved', 'Rejected'])->default(null);
            $table->string('device_id');
            $table->unsignedBigInteger('emp_id'); // <-- CHANGED HERE
            $table->timestamps();
            // assuming 'users' table still exists
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('devices_db');
    }
};
