<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('akun_rekening', function (Blueprint $table) {
            $table->id();
            $table->string('nama');
            $table->string('kode_akun')->unique();
            $table->enum('tipe', ['Kas', 'Pemasukan', 'Pengeluaran', 'Penampung']);
            $table->decimal('saldo_awal', 15, 2)->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('akun_rekening');
    }
};
