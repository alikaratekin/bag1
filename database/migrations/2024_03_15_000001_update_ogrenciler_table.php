<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('ogrenciler', function (Blueprint $table) {
            // Yeni sütunları ekliyoruz
            $table->string('cinsiyet')->after('tc');
            $table->string('egitim_donemi')->after('sinifi');
            $table->string('kontenjan')->after('egitim_donemi');
            $table->decimal('kirtasiyeucreti', 10, 2)->after('etutucreti');
        });

        // Müdüriyet değerlerini güncelliyoruz
        DB::statement("ALTER TABLE ogrenciler MODIFY COLUMN mudureyet ENUM('anakolu', 'ilkokul', 'ortaokul', 'anadolu_lisesi', 'fen_lisesi')");
    }

    public function down()
    {
        // Müdüriyet değerlerini eski haline getiriyoruz
        DB::statement("ALTER TABLE ogrenciler MODIFY COLUMN mudureyet ENUM('anakolu', 'ilkokul', 'ortaokul', 'lise')");

        Schema::table('ogrenciler', function (Blueprint $table) {
            $table->dropColumn(['cinsiyet', 'egitim_donemi', 'kontenjan', 'kirtasiyeucreti']);
        });
    }
};
