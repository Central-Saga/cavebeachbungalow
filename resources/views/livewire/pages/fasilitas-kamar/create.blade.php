<?php

use function Livewire\Volt\{
    layout, title, state, mount
};
use App\Models\FasilitasKamar;

layout('components.layouts.admin');
title('Create Fasilitas Kamar');

state([
    'nama_fasilitas' => '',
]);

mount(function() {
    // Initialize component
});

$save = function() {
    $this->validate([
        'nama_fasilitas' => 'required|string|max:255|unique:fasilitas_kamars,nama_fasilitas',
    ]);

    FasilitasKamar::create([
        'nama_fasilitas' => $this->nama_fasilitas,
    ]);

    session()->flash('message', 'Fasilitas berhasil ditambahkan!');
            return $this->redirect(route('admin.fasilitas-kamar.index'));
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
                                        class="w-16 h-16 bg-gradient-to-br from-blue-600 to-purple-700 rounded-2xl flex items-center justify-center shadow-lg">
                                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 4v16m8-8H4"></path>
                                        </svg>
                                    </div>
                                    <div>
                                        <h1
                                            class="text-2xl font-bold bg-gradient-to-r from-gray-900 via-blue-800 to-purple-800 dark:from-white dark:via-blue-200 dark:to-purple-200 bg-clip-text text-transparent">
                                            Tambah Fasilitas Kamar
                                        </h1>
                                        <p class="mt-2 text-sm text-gray-600 dark:text-gray-300 font-medium">
                                            Buat fasilitas kamar baru untuk sistem Cave Beach Bungalow
                                        </p>
                                    </div>
                                </div>
                            </div>
                            <a href="{{ route('admin.fasilitas-kamar.index') }}"
                                class="group relative inline-flex items-center gap-3 px-6 py-3 bg-gradient-to-r from-gray-600 via-gray-700 to-gray-800 hover:from-gray-700 hover:via-gray-800 hover:to-gray-900 text-white font-bold rounded-2xl shadow-2xl hover:shadow-gray-500/25 transform hover:-translate-y-1 transition-all duration-300 overflow-hidden"
                                wire:navigate>
                                <div
                                    class="absolute inset-0 bg-gradient-to-r from-white/20 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                                </div>
                                <svg class="relative z-10 h-5 w-5" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                                </svg>
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
                        <!-- Basic Information -->
                        <div>
                            <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-6 flex items-center gap-3">
                                <div
                                    class="w-8 h-8 bg-gradient-to-br from-blue-500 to-purple-600 rounded-xl flex items-center justify-center">
                                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </div>
                                Informasi Fasilitas
                            </h3>
                            <div class="grid grid-cols-1 gap-6">
                                <div>
                                    <label for="nama_fasilitas"
                                        class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-3">
                                        Nama Fasilitas <span class="text-red-500">*</span>
                                    </label>
                                    <input wire:model="nama_fasilitas" type="text" id="nama_fasilitas"
                                        class="block w-full px-4 py-3 border-0 rounded-2xl bg-gray-50/80 dark:bg-gray-700/80 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:ring-2 focus:ring-blue-500 focus:outline-none backdrop-blur-sm text-sm font-medium shadow-lg"
                                        placeholder="Contoh: AC, WiFi, TV, Kamar Mandi Dalam">
                                    @error('nama_fasilitas')
                                    <p class="mt-2 text-sm text-red-600 dark:text-red-400 flex items-center gap-2">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        {{ $message }}
                                    </p>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Additional Information -->
                        <div>
                            <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-6 flex items-center gap-3">
                                <div
                                    class="w-8 h-8 bg-gradient-to-br from-emerald-500 to-teal-600 rounded-xl flex items-center justify-center">
                                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z">
                                        </path>
                                    </svg>
                                </div>
                                Informasi Tambahan
                            </h3>
                            <p class="text-sm text-gray-600 dark:text-gray-400 mb-6">
                                Fasilitas ini akan tersedia untuk dipilih saat mengatur tipe kamar
                            </p>

                            <div
                                class="bg-gradient-to-r from-gray-50/80 via-blue-50/50 to-purple-50/50 dark:from-gray-700/80 dark:via-blue-900/20 dark:to-purple-900/20 rounded-2xl p-6 border border-gray-200/50 dark:border-gray-600/50">
                                <div class="flex items-start gap-4">
                                    <div
                                        class="w-12 h-12 bg-gradient-to-br from-blue-100 to-purple-100 dark:from-blue-900/30 dark:to-purple-900/30 rounded-2xl flex items-center justify-center border border-blue-200/50 dark:border-blue-700/50">
                                        <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none"
                                            stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z">
                                            </path>
                                        </svg>
                                    </div>
                                    <div class="flex-1">
                                        <h4 class="text-sm font-bold text-gray-900 dark:text-white mb-2">Fasilitas Kamar
                                        </h4>
                                        <p class="text-xs text-gray-600 dark:text-gray-400 leading-relaxed">
                                            Fasilitas yang Anda buat akan otomatis tersedia untuk dipilih saat mengatur
                                            tipe kamar.
                                            Setiap tipe kamar dapat memiliki kombinasi fasilitas yang berbeda sesuai
                                            kebutuhan.
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div
                            class="flex items-center justify-end gap-4 pt-8 border-t border-gray-200/50 dark:border-gray-700/50">
                            <a href="{{ route('admin.fasilitas-kamar.index') }}"
                                class="group px-6 py-3 bg-gradient-to-r from-gray-100 to-gray-200 dark:from-gray-700 dark:to-gray-600 text-gray-700 dark:text-gray-200 font-bold rounded-2xl shadow-lg hover:shadow-gray-500/25 transition-all duration-300 transform hover:-translate-y-1">
                                <span class="flex items-center gap-2">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                    Batal
                                </span>
                            </a>
                            <button type="submit"
                                class="group relative inline-flex items-center gap-3 px-8 py-3 bg-gradient-to-r from-blue-600 via-purple-600 to-pink-600 hover:from-blue-700 hover:via-purple-700 hover:to-pink-700 text-white font-bold rounded-2xl shadow-2xl hover:shadow-blue-500/25 transform hover:-translate-y-1 transition-all duration-300 overflow-hidden">
                                <div
                                    class="absolute inset-0 bg-gradient-to-r from-white/20 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                                </div>
                                <svg class="relative z-10 h-5 w-5" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M5 13l4 4L19 7"></path>
                                </svg>
                                <span class="relative z-10">Simpan Fasilitas</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>