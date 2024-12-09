<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // Veliler tablosunu güncelleme
        Schema::table('veliler', function (Blueprint $table) {
            // Önce yeni sütunu ekliyoruz
            $table->string('yakinlik')->nullable()->after('ev_tel');
            $table->text('adres')->nullable()->after('yakinlik');
        });

        // Anne baba değerlerini yakinlik sütununa kopyalama
        DB::statement("UPDATE veliler SET yakinlik = anne_baba");

        // Eski sütunu kaldırma
        Schema::table('veliler', function (Blueprint $table) {
            $table->dropColumn('anne_baba');
        });

        // Ek veliler tablosunu güncelleme
        Schema::table('ek_veliler', function (Blueprint $table) {
            // Önce yeni sütunu ekliyoruz
            $table->string('yakinlik')->nullable()->after('ev_tel');
            $table->text('adres')->nullable()->after('yakinlik');
        });

        // Anne baba değerlerini yakinlik sütununa kopyalama
        DB::statement("UPDATE ek_veliler SET yakinlik = anne_baba");

        // Eski sütunu kaldırma
        Schema::table('ek_veliler', function (Blueprint $table) {
            $table->dropColumn('anne_baba');
        });
    }

    public function down()
    {
        // Veliler tablosunu eski haline getirme
        Schema::table('veliler', function (Blueprint $table) {
            $table->string('anne_baba')->nullable()->after('ev_tel');
        });

        DB::statement("UPDATE veliler SET anne_baba = yakinlik");

        Schema::table('veliler', function (Blueprint $table) {
            $table->dropColumn(['yakinlik', 'adres']);
        });

        // Ek veliler tablosunu eski haline getirme
        Schema::table('ek_veliler', function (Blueprint $table) {
            $table->string('anne_baba')->nullable()->after('ev_tel');
        });

        DB::statement("UPDATE ek_veliler SET anne_baba = yakinlik");

        Schema::table('ek_veliler', function (Blueprint $table) {
            $table->dropColumn(['yakinlik', 'adres']);
        });
    }
};
