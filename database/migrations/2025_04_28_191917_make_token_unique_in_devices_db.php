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
    Schema::table('devices_db', function (Blueprint $table) {
        $table->unique('token')->nullable();
    });
}

    public function down()
    {
        Schema::table('devices_db', function (Blueprint $table) {
            $table->dropUnique(['token']);
        });
    }
};
