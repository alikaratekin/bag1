<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTedarikcilerTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tedarikciler', function (Blueprint $table) {
            $table->id(); // Otomatik artan benzersiz ID kolonu
            $table->string('ad'); // Tedarikçi adı (zorunlu)
            $table->string('numara')->nullable(); // Telefon numarası veya başka bir benzersiz kimlik (opsiyonel)
            $table->string('vergino')->nullable(); // Vergi numarası (opsiyonel)
            $table->text('adres')->nullable(); // Adres bilgisi (opsiyonel)
            $table->text('not')->nullable(); // Ek not bilgisi (opsiyonel)
            $table->softDeletes(); // Silinen kayıtlar için "soft delete" desteği
            $table->unsignedBigInteger('team_id'); // Multi-tenancy için takım ID'si
            $table->timestamps(); // Oluşturulma ve güncellenme tarihlerini tutar

            // Foreign key ekleyebiliriz (team_id için), eğer gerekirse:
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
        Schema::dropIfExists('tedarikciler'); // "tedarikciler" tablosunu kaldırır
    }
}
