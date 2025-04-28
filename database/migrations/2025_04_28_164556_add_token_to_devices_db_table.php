<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('devices_db', function (Blueprint $table) {
            $table->string('token')->nullable()->after('device_id');
            // adds a nullable 'token' column after 'device_id'
        });
    }

    public function down(): void
    {
        Schema::table('devices_db', function (Blueprint $table) {
            $table->dropColumn('token');
        });
    }
};
