<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
{
    Schema::table('t_hareketleri', function (Blueprint $table) {
        $table->bigInteger('tedarikci_id')->unsigned()->after('id');
        $table->foreign('tedarikci_id')->references('id')->on('tedarikciler')->onDelete('cascade');
    });
}

public function down()
{
    Schema::table('t_hareketleri', function (Blueprint $table) {
        $table->dropForeign(['tedarikci_id']);
        $table->dropColumn('tedarikci_id');
    });
}

};
