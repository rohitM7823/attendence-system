<?php


use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddShiftIdToEmployeesDbTable extends Migration
{
    public function up()
    {
        Schema::table('employees_db', function (Blueprint $table) {
            $table->unsignedBigInteger('shift_id')->nullable()->after('token');

            $table->foreign('shift_id')
                  ->references('id')
                  ->on('shifts_db')
                  ->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::table('employees_db', function (Blueprint $table) {
            $table->dropForeign(['shift_id']);
            $table->dropColumn('shift_id');
        });
    }
}

