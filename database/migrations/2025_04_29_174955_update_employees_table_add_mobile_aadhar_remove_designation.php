<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateEmployeesTableAddMobileAadharRemoveDesignation extends Migration
{
    public function up()
    {
        Schema::table('employees_db', function (Blueprint $table) {
            $table->dropColumn('designation');
            $table->string('aadhar_card');
            $table->string('mobile_number');
        });
    }

    public function down()
    {
        Schema::table('employees_db', function (Blueprint $table) {
            $table->string('designation');
            $table->dropColumn('aadhar_card');
            $table->dropColumn('mobile_number');
        });
    }
}
