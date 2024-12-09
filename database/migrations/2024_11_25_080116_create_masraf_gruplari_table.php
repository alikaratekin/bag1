<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMasrafGruplariTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('masraf_gruplari', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('ad', 255); // Masraf Grubu Adı
            $table->bigInteger('team_id')->unsigned()->nullable(); // team_id eklenmiş
            $table->timestamps(); // created_at ve updated_at sütunlarını ekler
            $table->softDeletes(); // deleted_at sütununu ekler
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('masraf_gruplari');
    }
}
