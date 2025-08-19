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
        Schema::create('tabungan_masjid', function (Blueprint $table) {
            $table->id();
            $table->foreignId('akun_id')->constrained('akun_rekening')->onDelete('cascade');
            $table->date('tanggal');
            $table->foreignId('jamaah_id')->constrained('master_tabungan')->onDelete('cascade')->nullable();
            $table->decimal('jumlah', 15, 2)->default(0)->nullable();
            $table->string('keterangan');
            $table->boolean('status')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tabungan_masjid');
    }
};
