<?php

use function Livewire\Volt\{
    layout, title, state, mount
};
use App\Models\TipeKamar;
use App\Models\Kamar;

layout('components.layouts.admin');
title('Edit Kamar');

state([
    'kamar' => null,
    'tipe_kamar_id' => '',
    'nomor_kamar' => '',
    'status' => 'tersedia',
    'tipeKamars' => [],
    'hargas' => [
        ['tipe_paket' => 'harian', 'harga' => 0],
        ['tipe_paket' => 'mingguan', 'harga' => 0],
        ['tipe_paket' => 'bulanan', 'harga' => 0],
    ],
]);

mount(function(Kamar $kamar) {
    $this->kamar = $kamar;
    $this->tipe_kamar_id = $kamar->tipe_kamar_id;
    $this->nomor_kamar = $kamar->nomor_kamar;
    $this->status = $kamar->status;
    $this->tipeKamars = TipeKamar::orderBy('nama_tipe')->get();

    // Load existing harga data
    $existingHargas = $kamar->hargas;
    foreach ($this->hargas as $index => $harga) {
        $existingHarga = $existingHargas->where('tipe_paket', $harga['tipe_paket'])->first();
        if ($existingHarga) {
            $this->hargas[$index]['harga'] = $existingHarga->harga;
        }
    }
});

$save = function() {
    // Debug: log data yang akan diupdate
    \Log::info('Updating kamar with data:', [
        'tipe_kamar_id' => $this->tipe_kamar_id,
        'nomor_kamar' => $this->nomor_kamar,
        'status' => $this->status,
        'hargas' => $this->hargas,
    ]);

    $this->validate([
        'tipe_kamar_id' => 'required|exists:tipe_kamars,id',
        'nomor_kamar' => 'required|string|unique:kamars,nomor_kamar,' . $this->kamar->id,
        'status' => 'required|string|in:tersedia,terisi,perbaikan',
        'hargas.*.tipe_paket' => 'required|string|in:harian,mingguan,bulanan',
        'hargas.*.harga' => 'required|numeric|min:0',
    ]);

    $this->kamar->update([
        'tipe_kamar_id' => $this->tipe_kamar_id,
        'nomor_kamar' => $this->nomor_kamar,
        'status' => $this->status,
    ]);

    // Update harga data
    // Hapus harga lama
    $this->kamar->hargas()->delete();

    // Buat harga baru
    foreach ($this->hargas as $hargaData) {
        if ($hargaData['harga'] > 0) {
            $this->kamar->hargas()->create([
                'tipe_paket' => $hargaData['tipe_paket'],
                'harga' => $hargaData['harga'],
            ]);
        }
    }

    // Debug: log kamar yang berhasil diupdate
    \Log::info('Kamar updated successfully:', $this->kamar->fresh()->toArray());

    session()->flash('message', 'Kamar berhasil diperbarui!');
    return $this->redirect(route('admin.kamar.index'));
};

?>

<div
    class="min-h-screen bg-gradient-to-br from-slate-50 via-blue-50 to-indigo-100 dark:from-gray-900 dark:via-gray-800 dark:to-slate-900">
    <div class="py-8">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Header dengan design yang senada -->
            <div class="mb-10">
                <div class="relative">
                    <!-- Background decoration -->
                    <div
                        class="absolute inset-0 bg-gradient-to-r from-blue-600/10 via-purple-600/10 to-pink-600/10 rounded-3xl blur-3xl">
                    </div>
                    <div
                        class="relative bg-white/70 dark:bg-gray-800/70 backdrop-blur-xl rounded-3xl p-8 border border-white/30 dark:border-gray-700/50 shadow-2xl">
                        <div class="flex flex-col lg:flex-row items-start lg:items-center justify-between gap-6">
                            <div class="flex-1">
                                <div class="flex items-center gap-4 mb-3">
                                    <div
                                        class="w-16 h-16 bg-gradient-to-br from-emerald-600 to-teal-700 rounded-2xl flex items-center justify-center shadow-lg">
                                        <flux:icon name="pencil-square" class="w-8 h-8 text-white" />
                                    </div>
                                    <div>
                                        <h1
                                            class="text-2xl font-bold bg-gradient-to-r from-gray-900 via-emerald-800 to-teal-800 dark:from-white dark:via-emerald-200 dark:to-teal-200 bg-clip-text text-transparent">
                                            Edit Kamar
                                        </h1>
                                        <p class="mt-2 text-sm text-gray-600 dark:text-gray-300 font-medium">
                                            Edit kamar "{{ $kamar?->nomor_kamar ?? 'Loading...' }}" untuk sistem Pondok
                                            Putri
                                        </p>
                                    </div>
                                </div>
                            </div>
                            <a href="{{ route('admin.kamar.index') }}"
                                class="group relative inline-flex items-center gap-3 px-6 py-3 bg-gradient-to-r from-gray-600 via-gray-700 to-gray-800 hover:from-gray-700 hover:via-gray-800 hover:to-gray-900 text-white font-bold rounded-2xl shadow-2xl hover:shadow-gray-500/25 transform hover:-translate-y-1 transition-all duration-300 overflow-hidden"
                                wire:navigate>
                                <div
                                    class="absolute inset-0 bg-gradient-to-r from-white/20 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                                </div>
                                <flux:icon name="arrow-left" class="relative z-10 h-5 w-5" />
                                <span class="relative z-10">Kembali</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Form dengan design yang modern -->
            <div class="relative">
                <div
                    class="absolute inset-0 bg-gradient-to-r from-blue-600/5 via-purple-600/5 to-pink-600/5 rounded-3xl blur-3xl">
                </div>
                <div
                    class="relative bg-white/90 dark:bg-gray-800/90 shadow-2xl rounded-3xl overflow-hidden border border-white/30 dark:border-gray-700/50 backdrop-blur-xl">
                    <form wire:submit="save" class="p-8 space-y-8">
                        @if(!$kamar)
                        <!-- Loading State -->
                        <div class="text-center py-12">
                            <div
                                class="mx-auto h-16 w-16 bg-gradient-to-br from-blue-200 to-purple-300 dark:from-blue-700 dark:to-purple-600 rounded-full flex items-center justify-center mb-6 shadow-lg animate-spin">
                                <flux:icon name="arrow-path" class="h-8 w-8 text-white" />
                            </div>
                            <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-2">Memuat Data Kamar</h3>
                            <p class="text-sm text-gray-600 dark:text-gray-400">Mohon tunggu sebentar...</p>
                        </div>
                        @else
                        <!-- Basic Information -->
                        <div>
                            <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-6 flex items-center gap-3">
                                <div
                                    class="w-8 h-8 bg-gradient-to-br from-blue-500 to-purple-600 rounded-xl flex items-center justify-center">
                                    <flux:icon name="bell-snooze" class="w-4 h-4 text-white" />
                                </div>
                                Informasi Kamar
                            </h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label for="tipe_kamar_id"
                                        class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-3">
                                        Tipe Kamar <span class="text-red-500">*</span>
                                    </label>
                                    <select wire:model="tipe_kamar_id" id="tipe_kamar_id"
                                        class="block w-full px-4 py-3 border-0 rounded-2xl bg-gray-50/80 dark:bg-gray-700/80 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:outline-none backdrop-blur-sm text-sm font-medium shadow-lg">
                                        <option value="">Pilih tipe kamar</option>
                                        @foreach($this->tipeKamars as $tipe)
                                        <option value="{{ $tipe->id }}">{{ $tipe->nama_tipe }}</option>
                                        @endforeach
                                    </select>
                                    @error('tipe_kamar_id')
                                    <p class="mt-2 text-sm text-red-600 dark:text-red-400 flex items-center gap-2">
                                        <flux:icon name="exclamation-triangle" class="w-4 h-4" />
                                        {{ $message }}
                                    </p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="nomor_kamar"
                                        class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-3">
                                        Nomor Kamar <span class="text-red-500">*</span>
                                    </label>
                                    <input wire:model="nomor_kamar" type="text" id="nomor_kamar"
                                        class="block w-full px-4 py-3 border-0 rounded-2xl bg-gray-50/80 dark:bg-gray-700/80 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:ring-2 focus:ring-blue-500 focus:outline-none backdrop-blur-sm text-sm font-medium shadow-lg"
                                        placeholder="Contoh: KMR-101">
                                    @error('nomor_kamar')
                                    <p class="mt-2 text-sm text-red-600 dark:text-red-400 flex items-center gap-2">
                                        <flux:icon name="exclamation-triangle" class="w-4 h-4" />
                                        {{ $message }}
                                    </p>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Current Kamar Info -->
                        <div
                            class="bg-gradient-to-r from-blue-50/80 via-indigo-50/50 to-purple-50/50 dark:from-blue-900/20 dark:via-indigo-900/20 dark:to-purple-900/20 border border-blue-200/50 dark:border-blue-800/50 rounded-2xl p-6">
                            <div class="flex items-center mb-4">
                                <div
                                    class="w-8 h-8 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-xl flex items-center justify-center mr-3">
                                    <flux:icon name="information-circle" class="w-4 h-4 text-white" />
                                </div>
                                <h4 class="text-sm font-bold text-blue-800 dark:text-blue-300">Informasi Kamar</h4>
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm text-blue-700 dark:text-blue-400">
                                <div
                                    class="bg-white/50 dark:bg-blue-900/30 rounded-xl p-3 border border-blue-200/30 dark:border-blue-700/30">
                                    <p class="font-semibold text-blue-800 dark:text-blue-300 mb-1">Dibuat</p>
                                    <p>{{ $kamar?->created_at?->format('d M Y H:i') ?? 'Loading...' }}</p>
                                </div>
                                <div
                                    class="bg-white/50 dark:bg-blue-900/30 rounded-xl p-3 border border-blue-200/30 dark:border-blue-700/30">
                                    <p class="font-semibold text-blue-800 dark:text-blue-300 mb-1">Diperbarui</p>
                                    <p>{{ $kamar?->updated_at?->format('d M Y H:i') ?? 'Loading...' }}</p>
                                </div>
                                <div
                                    class="bg-white/50 dark:bg-blue-900/30 rounded-xl p-3 border border-blue-200/30 dark:border-blue-700/30">
                                    <p class="font-semibold text-blue-800 dark:text-blue-300 mb-1">Tipe Kamar</p>
                                    <p>{{ $kamar?->tipeKamar?->nama_tipe ?? 'Loading...' }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- Additional Information -->
                        <div>
                            <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-6 flex items-center gap-3">
                                <div
                                    class="w-8 h-8 bg-gradient-to-br from-emerald-500 to-teal-600 rounded-xl flex items-center justify-center">
                                    <flux:icon name="currency-dollar" class="w-4 h-4 text-white" />
                                </div>
                                Detail Kamar
                            </h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label for="status"
                                        class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-3">
                                        Status <span class="text-red-500">*</span>
                                    </label>
                                    <select wire:model="status" id="status"
                                        class="block w-full px-4 py-3 border-0 rounded-2xl bg-gray-50/80 dark:bg-gray-700/80 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:outline-none backdrop-blur-sm text-sm font-medium shadow-lg">
                                        <option value="tersedia">Tersedia</option>
                                        <option value="terisi">Terisi</option>
                                        <option value="perbaikan">Perbaikan</option>
                                    </select>
                                    @error('status')
                                    <p class="mt-2 text-sm text-red-600 dark:text-red-400 flex items-center gap-2">
                                        <flux:icon name="exclamation-triangle" class="w-4 h-4" />
                                        {{ $message }}
                                    </p>
                                    @enderror
                                </div>

                                <div class="col-span-2">
                                    <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-3">
                                        Harga Kamar (Rp) <span class="text-red-500">*</span>
                                    </label>
                                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                        @foreach($hargas as $index => $harga)
                                        <div
                                            class="bg-gray-50/50 dark:bg-gray-700/50 rounded-xl p-4 border border-gray-200/50 dark:border-gray-600/50">
                                            <label
                                                class="block text-xs font-bold text-gray-600 dark:text-gray-400 mb-2 uppercase tracking-wide">
                                                {{ ucfirst($harga['tipe_paket']) }}
                                            </label>
                                            <input wire:model="hargas.{{ $index }}.harga" type="number"
                                                id="hargas.{{ $index }}.harga" min="0"
                                                class="block w-full px-3 py-2 border-0 rounded-lg bg-white/80 dark:bg-gray-600/80 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:ring-2 focus:ring-blue-500 focus:outline-none text-sm font-medium shadow-sm"
                                                placeholder="Contoh: 500000">
                                        </div>
                                        @endforeach
                                    </div>
                                    @error('hargas.*.harga')
                                    <p class="mt-2 text-sm text-red-600 dark:text-red-400 flex items-center gap-2">
                                        <flux:icon name="exclamation-triangle" class="w-4 h-4" />
                                        {{ $message }}
                                    </p>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Status Preview -->
                        <div
                            class="bg-gradient-to-r from-gray-50/80 via-blue-50/50 to-purple-50/50 dark:from-gray-700/80 dark:via-blue-900/20 dark:to-purple-900/20 rounded-2xl p-6 border border-gray-200/50 dark:border-gray-600/50">
                            <div class="flex items-center mb-4">
                                <div
                                    class="w-8 h-8 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-xl flex items-center justify-center mr-3">
                                    <flux:icon name="eye" class="w-4 h-4 text-white" />
                                </div>
                                <h4 class="text-sm font-bold text-blue-800 dark:text-blue-300">Preview Status</h4>
                            </div>
                            <div class="flex items-center gap-4">
                                @if($status === 'tersedia')
                                <span
                                    class="inline-flex items-center px-3 py-1.5 rounded-xl text-sm font-medium bg-gradient-to-r from-green-100 to-emerald-100 text-green-700 dark:from-green-900/30 dark:to-emerald-900/30 dark:text-green-300 border border-green-200/50 dark:border-green-700/50">
                                    <flux:icon name="check-circle" class="w-4 h-4 mr-2" />
                                    Tersedia
                                </span>
                                @elseif($status === 'terisi')
                                <span
                                    class="inline-flex items-center px-3 py-1.5 rounded-xl text-sm font-medium bg-gradient-to-r from-blue-100 to-indigo-100 text-blue-700 dark:from-blue-900/30 dark:to-indigo-900/30 dark:text-blue-300 border border-blue-200/50 dark:border-blue-700/50">
                                    <flux:icon name="user" class="w-4 h-4 mr-2" />
                                    Terisi
                                </span>
                                @elseif($status === 'perbaikan')
                                <span
                                    class="inline-flex items-center px-3 py-1.5 rounded-xl text-sm font-medium bg-gradient-to-r from-amber-100 to-orange-100 text-amber-700 dark:from-amber-900/30 dark:to-orange-900/30 dark:text-amber-300 border border-amber-200/50 dark:border-amber-700/50">
                                    <flux:icon name="wrench-screwdriver" class="w-4 h-4 mr-2" />
                                    Perbaikan
                                </span>
                                @endif
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div
                            class="flex items-center justify-end gap-4 pt-8 border-t border-gray-200/50 dark:border-gray-700/50">
                            <a href="{{ route('admin.kamar.index') }}"
                                class="group px-6 py-3 bg-gradient-to-r from-gray-100 to-gray-200 dark:from-gray-700 dark:to-gray-600 text-gray-700 dark:text-gray-200 font-bold rounded-2xl shadow-lg hover:shadow-gray-500/25 transition-all duration-300 transform hover:-translate-y-1"
                                wire:navigate>
                                <span class="flex items-center gap-2">
                                    <flux:icon name="x-mark" class="w-5 h-5" />
                                    Batal
                                </span>
                            </a>
                            <button type="submit" @if(!$kamar) disabled @endif
                                class="group relative inline-flex items-center gap-3 px-8 py-3 bg-gradient-to-r from-emerald-600 via-teal-600 to-blue-600 hover:from-emerald-700 hover:via-teal-700 hover:to-blue-700 text-white font-bold rounded-2xl shadow-2xl hover:shadow-emerald-500/25 transition-all duration-300 overflow-hidden disabled:opacity-50 disabled:cursor-not-allowed">
                                <div
                                    class="absolute inset-0 bg-gradient-to-r from-white/20 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                                </div>
                                <flux:icon name="check" class="relative z-10 h-5 w-5" />
                                <span class="relative z-10">Update Kamar</span>
                            </button>
                        </div>
                        @endif
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>