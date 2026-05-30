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
        Schema::create('heart_rates', function (Blueprint $table) {
            $table->id();
            $table->integer('bpm'); // Kolom untuk menyimpan angka detak jantung
            $table->timestamps();   // Otomatis mencatat waktu data masuk
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('heart_rates');
    }
};
