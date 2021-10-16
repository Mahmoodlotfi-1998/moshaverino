<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDoctorsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('doctors', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('user_id');
            $table->integer('cat_id');
            $table->string('whatsapp_phone');
            $table->string('shaba');
            $table->string('rezome_photo');
            $table->string('education_cv');
            $table->string('natural_photo');
            $table->integer('star');
            $table->integer('number_star');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('doctors');
    }
}
