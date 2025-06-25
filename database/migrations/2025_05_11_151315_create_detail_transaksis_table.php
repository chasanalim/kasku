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
        Schema::create('detail_transaksi', function (Blueprint $table) {
            $table->id();
            $table->foreignId('transaksi_id')->constrained('transaksi')->onDelete('cascade');
            $table->foreignId('jamaah_id')->constrained('jamaah')->onDelete('cascade')->nullable();
            $table->decimal('persenan', 15, 2)->default(0)->nullable();
            $table->decimal('dapur_pusat', 15, 2)->default(0)->nullable();
            $table->decimal('shodaqah_daerah', 15, 2)->default(0)->nullable();
            $table->decimal('shodaqah_kelompok', 15, 2)->default(0)->nullable();
            $table->decimal('jimpitan', 15, 2)->default(0)->nullable();
            $table->decimal('jumlah', 15, 2)->default(0)->nullable();
            $table->boolean('status')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('detail_transaksi');
    }
};
