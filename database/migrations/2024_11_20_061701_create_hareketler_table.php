<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHareketlerTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('hareketler', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->dateTime('tarih')->nullable(false);
            $table->string('islem_tipi', 255);
            $table->decimal('giden', 15, 2)->default(0);
            $table->decimal('gelen', 15, 2)->default(0);
            $table->text('aciklama')->nullable();
            $table->bigInteger('kaynak_hesap_no')->unsigned()->nullable();
            $table->bigInteger('hedef_hesap_no')->unsigned()->nullable();
            $table->bigInteger('team_id')->unsigned()->nullable();
            $table->string('kullanici', 255)->nullable(); // Kullanıcı sütunu eklendi
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
        Schema::dropIfExists('hareketler');
    }
}
