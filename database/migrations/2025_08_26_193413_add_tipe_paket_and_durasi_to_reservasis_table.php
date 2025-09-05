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
        Schema::table('reservasis', function (Blueprint $table) {
            $table->enum('tipe_paket', ['harian', 'mingguan', 'bulanan'])->default('harian')->after('pelanggan_id');
            $table->integer('durasi')->default(1)->after('tipe_paket');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('reservasis', function (Blueprint $table) {
            $table->dropColumn(['tipe_paket', 'durasi']);
        });
    }
};
