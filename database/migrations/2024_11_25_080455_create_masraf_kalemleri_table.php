<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMasrafKalemleriTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('masraf_kalemleri', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('ad', 255); // Masraf Kalemi Adı
            $table->bigInteger('masraf_grubu_id')->unsigned(); // Masraf Grubu ID
            $table->bigInteger('team_id')->unsigned()->nullable(); // team_id eklenmiş
            $table->timestamps(); // created_at ve updated_at sütunlarını ekler
            $table->softDeletes(); // deleted_at sütununu ekler

            // Masraf Kalemi ve Masraf Grubu arasında ilişki
            $table->foreign('masraf_grubu_id')->references('id')->on('masraf_gruplari')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('masraf_kalemleri');
    }
}
