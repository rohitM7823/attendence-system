<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSiteDbTable extends Migration
{
    public function up()
    {
        Schema::create('site_db', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->json('location');
            $table->double('radius');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('site_db');
    }
}

