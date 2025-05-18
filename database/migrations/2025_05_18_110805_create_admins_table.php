<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('admins', function (Blueprint $table) {
            $table->id();
            $table->string('admin_id')->unique();
            $table->string('password');
            $table->timestamps();
        });

        // Insert default admin
        DB::table('admins')->insert([
            'admin_id' => 'admin',
            'password' => bcrypt('admin123'), // default password
        ]);
    }

    public function down(): void {
        Schema::dropIfExists('admins');
    }
};
