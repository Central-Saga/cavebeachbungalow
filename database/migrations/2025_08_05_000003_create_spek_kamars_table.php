<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('spek_kamars', function (Blueprint $table) {
            $table->id();
            $table->foreignId('fasilitas_kamar_id')->constrained('fasilitas_kamars')->onDelete('cascade');
            $table->foreignId('tipe_kamar_id')->constrained('tipe_kamars')->onDelete('cascade');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('spek_kamars');
    }
};
