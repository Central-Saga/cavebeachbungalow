<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pembayarans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('reservasi_id')->constrained()->cascadeOnDelete();
            $table->decimal('nominal', 16, 2);          // jumlah yang dibayar
            $table->string('bukti_path')->nullable();   // bukti transfer (gambar/pdf)
            $table->enum('status', ['menunggu', 'terverifikasi', 'ditolak'])->default('menunggu');
            $table->text('keterangan')->nullable();     // catatan tambahan
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pembayarans');
    }
};
