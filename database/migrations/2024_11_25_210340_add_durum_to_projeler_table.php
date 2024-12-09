<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDurumToProjelerTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('projeler', function (Blueprint $table) {
            $table->boolean('durum')->default(true)->after('aciklama'); // Aktif/Pasif durumu
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('projeler', function (Blueprint $table) {
            $table->dropColumn('durum');
        });
    }
}
