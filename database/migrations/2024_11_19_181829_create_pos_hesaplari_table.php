<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('pos_hesaplari', function (Blueprint $table) {
            $table->id();
            $table->string('tanım');
            $table->string('etiket_rengi')->nullable();
            $table->string('para_birimi')->default('TL');
            $table->string('pos_numarası')->nullable();
            $table->decimal('güncel_bakiye', 15, 2)->default(0.00);
            $table->boolean('aktiflik_durumu')->default(true); // Aktiflik durumu sütunu
            $table->softDeletes(); // Soft delete
            $table->unsignedBigInteger('team_id'); // Team Multi-Tenancy için
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('pos_hesaplari');
    }
};
