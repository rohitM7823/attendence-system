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
        $table->dropColumn('salary'); // drop old salary column
        $table->string('account_number')->nullable(); // add new account number column
    });
}

public function down()
{
    Schema::table('employees_db', function (Blueprint $table) {
        $table->dropColumn('account_number');
        $table->decimal('salary', 10, 2)->nullable(); // revert back to original if needed
    });
}
};
