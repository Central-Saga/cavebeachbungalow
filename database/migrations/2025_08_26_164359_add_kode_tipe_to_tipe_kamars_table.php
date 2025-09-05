<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (!Schema::hasColumn('tipe_kamars', 'kode_tipe')) {
            Schema::table('tipe_kamars', function (Blueprint $table) {
                $table->string('kode_tipe', 10)->nullable()->after('nama_tipe');
            });

            // Update data existing dengan kode default
            $tipeKamars = DB::table('tipe_kamars')->get();
            foreach ($tipeKamars as $tipeKamar) {
                $kode = strtoupper(substr($tipeKamar->nama_tipe, 0, 3));
                DB::table('tipe_kamars')
                    ->where('id', $tipeKamar->id)
                    ->update(['kode_tipe' => $kode]);
            }

            // Set kode_tipe sebagai not null dan unique
            Schema::table('tipe_kamars', function (Blueprint $table) {
                $table->string('kode_tipe', 10)->nullable(false)->change();
                $table->unique('kode_tipe');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('tipe_kamars', 'kode_tipe')) {
            Schema::table('tipe_kamars', function (Blueprint $table) {
                $table->dropUnique(['kode_tipe']);
                $table->dropColumn('kode_tipe');
            });
        }
    }
};
