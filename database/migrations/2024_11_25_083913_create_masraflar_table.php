<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMasraflarTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('masraflar', function (Blueprint $table) {
            $table->bigIncrements('id'); // ID sütunu
            $table->dateTime('tarih')->nullable(false); // Masrafın tarihi
            $table->string('kullanici', 255)->nullable(); // Kullanıcı
            $table->bigInteger('masraf_kalemi_id')->unsigned()->nullable(); // Masraf kalemi (Foreign Key)
            $table->text('aciklama')->nullable(); // Açıklama
            $table->bigInteger('kaynak_hesap_no')->unsigned()->nullable(); // Kaynak hesap numarası
            $table->decimal('tutar', 15, 2)->default(0); // Tutar
            $table->string('proje')->nullable(); // Proje (boş olabilir)
            $table->bigInteger('team_id')->unsigned()->nullable(); // Team ID (Herhangi bir takım ID'si olabilir)
            $table->timestamps(); // created_at ve updated_at sütunları
            $table->softDeletes(); // deleted_at sütunu
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('masraflar');
    }
}
