<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVelilerTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('veliler', function (Blueprint $table) {
            $table->id();
            $table->string('isim');
            $table->string('tc', 11)->unique();
            $table->string('meslek')->nullable();
            $table->string('tel', 15)->nullable();
            $table->string('eposta')->nullable()->unique();
            $table->string('is_tel', 15)->nullable();
            $table->string('ev_tel', 15)->nullable();
            $table->enum('anne_baba', ['anne', 'baba'])->nullable();
            $table->timestamps();
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
        Schema::dropIfExists('veliler');
    }
}
