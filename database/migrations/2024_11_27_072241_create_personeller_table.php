<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('personeller', function (Blueprint $table) {
            $table->id();
            $table->string('isim'); // Zorunlu alan
            $table->string('cep_telefonu'); // Zorunlu alan
            $table->string('e_posta')->nullable(); // İsteğe bağlı
            $table->date('ise_giris_tarihi')->nullable(); // İsteğe bağlı
            $table->date('isten_ayrilis_tarihi')->nullable(); // İsteğe bağlı
            $table->date('dogum_tarihi')->nullable(); // İsteğe bağlı
            $table->string('tc_kimlik_no')->nullable(); // İsteğe bağlı
            $table->decimal('aylik_net_maas', 8, 2)->nullable(); // İsteğe bağlı
            $table->string('banka_hesap_no')->nullable(); // İsteğe bağlı
            $table->string('departman')->nullable(); // İsteğe bağlı
            $table->text('adres')->nullable(); // İsteğe bağlı
            $table->text('banka_bilgileri')->nullable(); // İsteğe bağlı
            $table->text('not_alani')->nullable(); // İsteğe bağlı
            $table->bigInteger('team_id'); // Kullanıcı takım ID'si
            $table->softDeletes(); // Soft delete özelliği
            $table->timestamps(); // created_at ve updated_at
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('personeller');
    }
};
