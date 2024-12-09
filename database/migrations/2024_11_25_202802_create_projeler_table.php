<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProjelerTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('projeler', function (Blueprint $table) {
            $table->id(); // Otomatik ID
            $table->string('ad'); // Proje Adı
            $table->text('aciklama')->nullable(); // Açıklama (opsiyonel)
            $table->unsignedBigInteger('team_id'); // Team ID
            $table->timestamps(); // created_at ve updated_at
            $table->softDeletes(); // Soft delete özelliği
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('projeler');
    }
}
