<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVelihareketleriTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('velihareketleri', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('veli_id'); // Veliyi referanslar
            $table->unsignedBigInteger('ogrenci_id')->nullable(); // Öğrenciyi referanslar
            $table->string('islem_tipi'); // İşlem tipi (Örn: Yeni Kayıt, Ödeme)
            $table->date('tarih'); // İşlem tarihi
            $table->decimal('borcu', 10, 2); // Borç miktarı
            $table->decimal('odedi', 10, 2)->nullable(); // Ödenen miktar
            $table->string('hesap_no')->nullable(); // Hesap numarası (isteğe bağlı)
            $table->timestamps();

            // Yabancı anahtarlar
            $table->foreign('veli_id')->references('id')->on('veliler')->onDelete('cascade');
            $table->foreign('ogrenci_id')->references('id')->on('ogrenciler')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('velihareketleri');
    }
}
