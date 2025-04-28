<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('devices_db', function (Blueprint $table) {
            $table->enum('status', ['Approved', 'Rejected'])->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('devices_db', function (Blueprint $table) {
            $table->enum('status', ['Approved', 'Rejected'])->default('Rejected')->change();
        });
    }
};
