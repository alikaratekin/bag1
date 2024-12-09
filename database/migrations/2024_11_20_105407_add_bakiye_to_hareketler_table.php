<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddBakiyeToHareketlerTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('hareketler', function (Blueprint $table) {
            $table->decimal('bakiye', 15, 2)->default(0)->after('gelen');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('hareketler', function (Blueprint $table) {
            $table->dropColumn('bakiye');
        });
    }
}
