<?php

    use Illuminate\Database\Migrations\Migration;
    use Illuminate\Database\Schema\Blueprint;
    use Illuminate\Support\Facades\Schema;

    class CreateEkVelilerTable extends Migration
    {
        /**
         * Run the migrations.
         *
         * @return void
         */
        public function up()
        {
            Schema::create('ek_veliler', function (Blueprint $table) {
                $table->id();
                $table->foreignId('veli_id')->constrained('veliler')->onDelete('cascade');
                $table->string('isim');
                $table->string('tc', 11)->unique();
                $table->string('meslek')->nullable();
                $table->string('tel', 15)->nullable();
                $table->string('eposta')->unique()->nullable();
                $table->string('is_tel', 15)->nullable();
                $table->string('ev_tel', 15)->nullable();
                $table->enum('anne_baba', ['anne', 'baba']);
                $table->timestamps();
                $table->softDeletes();
            });
        }

        /**
         * Reverse the migrations.
         *
         * @return void
         */
        public function down()
        {
            Schema::dropIfExists('ek_veliler');
        }
    }
