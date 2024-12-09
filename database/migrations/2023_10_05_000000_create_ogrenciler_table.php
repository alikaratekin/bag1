<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOgrencilerTable extends Migration
{
    public function up()
    {
        Schema::create('ogrenciler', function (Blueprint $table) {
            $table->id();
            $table->foreignId('veli_id')->constrained('veliler')->onDelete('cascade');
            $table->string('isim');
            $table->string('tc')->unique();
            $table->date('dogum_tarihi');
            $table->enum('mudureyet', ['anakolu', 'ilkokul', 'ortaokul', 'lise']);
            $table->string('sinifi');
            $table->timestamps();
            $table->decimal('egitimucreti', 8, 2);
            $table->decimal('yemekucreti', 8, 2);
            $table->decimal('etutucreti', 8, 2);
            $table->softDeletes(); // Bu satırı ekleyin
        });
    }

    public function down()
    {
        Schema::dropIfExists('ogrenciler');
    }
}
