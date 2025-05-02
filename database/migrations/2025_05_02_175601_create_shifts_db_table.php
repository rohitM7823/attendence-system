<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('shifts_db', function (Blueprint $table) {
            $table->id();
            $table->timestamp('clock_in')->nullable();
            $table->timestamp('clock_out')->nullable();
            $table->json('clock_in_window');
            $table->json('clock_out_window');
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('shifts_db');
    }
};
