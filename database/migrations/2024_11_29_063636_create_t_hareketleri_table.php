<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTHareketleriTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('t_hareketleri', function (Blueprint $table) {
            $table->id(); // Otomatik artan benzersiz ID kolonu
            $table->string('islem_tipi'); // İşlem tipi (zorunlu)
            $table->date('tarih'); // İşlem tarihi
            $table->unsignedBigInteger('kullanici_id'); // Kullanıcı ID'si (zorunlu)
            $table->text('aciklama')->nullable(); // Açıklama (opsiyonel)
            $table->string('hesap_no'); // Hesap numarası (zorunlu)
            $table->decimal('tutar', 15, 2); // Tutar, toplam miktar
            $table->unsignedBigInteger('team_id'); // Multi-tenancy için takım ID'si
            $table->timestamps(); // Oluşturulma ve güncellenme tarihlerini tutar

            // Foreign key tanımları, eğer gerekirse:
            // $table->foreign('kullanici_id')->references('id')->on('users')->onDelete('cascade');
            // $table->foreign('team_id')->references('id')->on('teams')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('t_hareketleri'); // "t_hareketleri" tablosunu kaldırır
    }
}
