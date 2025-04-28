<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEmployeesDbTable extends Migration
{
    public function up()
    {
        Schema::create('employees_db', function (Blueprint $table) {
            $table->id(); // Primary Key
            $table->string('name'); // Employee name
            $table->string('emp_id')->unique(); // Unique employee ID
            $table->string('address'); // Address
            $table->string('designation'); // Designation
            $table->double('salary'); // Salary
            $table->string('token')->unique()->nullable(); // token as UNIQUE and NULLABLE

            $table->timestamps();

            // Foreign key reference to devices_db.token
            $table->foreign('token')->references('token')->on('devices_db')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('employees_db');
    }
}
