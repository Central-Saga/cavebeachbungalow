<?php

use function Livewire\Volt\{
    layout, title, state, mount, uses
};
use App\Models\TipeKamar;
use App\Models\FasilitasKamar;
use App\Models\GaleriKamar;
use Illuminate\Support\Facades\Storage;
use Livewire\WithFileUploads;

layout('components.layouts.admin');
title('Create Tipe Kamar');

uses([WithFileUploads::class]);

state([
    'nama_tipe' => '',
    'deskripsi' => '',
    'selectedFasilitas' => [],
    'galeriPhotos' => [], // koleksi utama TemporaryUploadedFile
    'incomingPhotos' => [], // batch baru dari uploadMultiple
    'fasilitas' => [],
]);

mount(function() {
    $this->fasilitas = FasilitasKamar::orderBy('nama_fasilitas')->get();
});

// Handler untuk merge batch baru ke galeriPhotos
$updated = function ($name, $value) {
    if ($name === 'incomingPhotos') {
        \Log::info('Incoming photos updated:', [
            'incoming_count' => count($this->incomingPhotos ?? []),
            'current_galeri_count' => count($this->galeriPhotos ?? [])
        ]);

        // Pastikan array dan merge batch baru
        $this->galeriPhotos = array_merge($this->galeriPhotos ?? [], $this->incomingPhotos ?? []);

        // Kosongkan buffer
        $this->incomingPhotos = [];

        \Log::info('After merge:', [
            'final_galeri_count' => count($this->galeriPhotos)
        ]);
    }
};

$save = function() {
    // Debug: log data yang akan disimpan
    \Log::info('Creating tipe kamar with data:', [
        'nama_tipe' => $this->nama_tipe,
        'deskripsi' => $this->deskripsi,
        'selectedFasilitas' => $this->selectedFasilitas,
        'galeriPhotos_count' => count($this->galeriPhotos),
        'galeriPhotos' => $this->galeriPhotos
    ]);

    $this->validate([
        'nama_tipe' => 'required|string|max:255',
        'deskripsi' => 'nullable|string',
        'galeriPhotos' => 'array',
        'galeriPhotos.*' => 'image|mimes:jpeg,png,jpg|max:5120', // 5MB max
    ]);

    try {
        $tipeKamar = TipeKamar::create([
            'nama_tipe' => $this->nama_tipe,
            'deskripsi' => $this->deskripsi,
        ]);

        \Log::info('TipeKamar created successfully:', ['id' => $tipeKamar->id, 'nama_tipe' => $tipeKamar->nama_tipe, 'kode_tipe' => $tipeKamar->kode_tipe]);

        // Sync fasilitas yang dipilih
        if (!empty($this->selectedFasilitas)) {
            // Gunakan sync untuk relasi many-to-many melalui tabel pivot
            $tipeKamar->fasilitasKamars()->sync($this->selectedFasilitas);
            \Log::info('Fasilitas synced successfully:', ['fasilitas_ids' => $this->selectedFasilitas]);
        }

        // Upload galeri photos
        if (!empty($this->galeriPhotos)) {
            \Log::info('Processing galeri photos:', ['count' => count($this->galeriPhotos)]);

            foreach ($this->galeriPhotos as $index => $photo) {
                \Log::info('Processing photo:', ['index' => $index, 'photo' => $photo]);

                $path = $photo->store('galeri-kamar', 'public');
                \Log::info('Photo stored at:', ['path' => $path]);

                GaleriKamar::create([
                    'tipe_kamar_id' => $tipeKamar->id,
                    'url_foto' => Storage::url($path),
                ]);
            }
        }

        session()->flash('message', 'Tipe kamar berhasil dibuat!');
        return $this->redirect(route('admin.tipe-kamar.index'));
    } catch (\Exception $e) {
        \Log::error('Error creating tipe kamar:', [
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString(),
            'data' => [
                'nama_tipe' => $this->nama_tipe,
                'deskripsi' => $this->deskripsi,
                'selectedFasilitas' => $this->selectedFasilitas,
                'galeriPhotos_count' => count($this->galeriPhotos)
            ]
        ]);
        session()->flash('error', 'Gagal membuat tipe kamar: ' . $e->getMessage());
    }
};

$removePhoto = function($index) {
    \Log::info('Removing photo at index:', ['index' => $index, 'before_count' => count($this->galeriPhotos)]);
    unset($this->galeriPhotos[$index]);
    $this->galeriPhotos = array_values($this->galeriPhotos);
    \Log::info('After removal count:', ['count' => count($this->galeriPhotos)]);
};

$clearAllPhotos = function() {
    \Log::info('Clearing all photos');
    $this->galeriPhotos = [];
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
                                            Tambah Tipe Kamar Baru
                                        </h1>
                                        <p class="mt-2 text-sm text-gray-600 dark:text-gray-300 font-medium">
                                            Buat tipe kamar baru untuk sistem Pondok Putri
                                        </p>
                                    </div>
                                </div>
                            </div>
                            <a href="{{ route('admin.tipe-kamar.index') }}"
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
                                Informasi Dasar
                            </h3>
                            <div class="grid grid-cols-1 gap-6">
                                <div>
                                    <label for="nama_tipe"
                                        class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-3">
                                        Nama Tipe <span class="text-red-500">*</span>
                                    </label>
                                    <input wire:model="nama_tipe" type="text" id="nama_tipe"
                                        class="block w-full px-4 py-3 border-0 rounded-2xl bg-gray-50/80 dark:bg-gray-700/80 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:ring-2 focus:ring-blue-500 focus:outline-none backdrop-blur-sm text-sm font-medium shadow-lg"
                                        placeholder="Contoh: Standard, Deluxe, Suite">
                                    @error('nama_tipe')
                                    <p class="mt-2 text-sm text-red-600 dark:text-red-400 flex items-center gap-2">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        {{ $message }}
                                    </p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="deskripsi"
                                        class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-3">
                                        Deskripsi
                                    </label>
                                    <textarea wire:model="deskripsi" id="deskripsi" rows="4"
                                        class="block w-full px-4 py-3 border-0 rounded-2xl bg-gray-50/80 dark:bg-gray-700/80 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:ring-2 focus:ring-blue-500 focus:outline-none backdrop-blur-sm text-sm font-medium shadow-lg resize-none"
                                        placeholder="Deskripsi detail tentang tipe kamar ini (opsional)"></textarea>
                                    @error('deskripsi')
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

                        <!-- Fasilitas Selection -->
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
                                Fasilitas Kamar
                            </h3>
                            <p class="text-sm text-gray-600 dark:text-gray-400 mb-6">
                                Pilih fasilitas yang akan tersedia untuk tipe kamar ini
                            </p>

                            <div
                                class="bg-gradient-to-r from-gray-50/80 via-blue-50/50 to-purple-50/50 dark:from-gray-700/80 dark:via-blue-900/20 dark:to-purple-900/20 rounded-2xl p-6 border border-gray-200/50 dark:border-gray-600/50">
                                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                                    @foreach($this->fasilitas as $fasilitasItem)
                                    <label
                                        class="group flex items-center p-4 bg-white/80 dark:bg-gray-800/80 rounded-2xl border border-gray-200/50 dark:border-gray-600/50 hover:bg-gradient-to-r hover:from-blue-50/80 hover:to-purple-50/80 dark:hover:from-blue-900/30 dark:hover:to-purple-900/30 cursor-pointer transition-all duration-300 shadow-lg hover:shadow-xl transform hover:-translate-y-1">
                                        <input wire:model="selectedFasilitas" type="checkbox"
                                            value="{{ $fasilitasItem->id }}"
                                            class="h-5 w-5 text-blue-600 focus:ring-blue-500 border-gray-300 rounded-lg">
                                        <span
                                            class="ml-3 text-sm font-medium text-gray-900 dark:text-white group-hover:text-blue-700 dark:group-hover:text-blue-300 transition-colors">
                                            {{ $fasilitasItem->nama_fasilitas }}
                                        </span>
                                    </label>
                                    @endforeach
                                </div>

                                @if($this->fasilitas->count() === 0)
                                <div class="text-center py-12">
                                    <div
                                        class="mx-auto h-24 w-24 bg-gradient-to-br from-gray-200 to-gray-300 dark:from-gray-600 dark:to-gray-700 rounded-full flex items-center justify-center mb-6 shadow-lg">
                                        <svg class="h-12 w-12 text-gray-400" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z">
                                            </path>
                                        </svg>
                                    </div>
                                    <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-2">Tidak ada
                                        fasilitas</h3>
                                    <p class="text-sm text-gray-600 dark:text-gray-400">Tidak ada fasilitas yang
                                        tersedia saat ini</p>
                                </div>
                                @endif
                            </div>
                        </div>

                        <!-- Galeri Kamar -->
                        <div>
                            <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-6 flex items-center gap-3">
                                <div
                                    class="w-8 h-8 bg-gradient-to-br from-pink-500 to-rose-600 rounded-xl flex items-center justify-center">
                                    <flux:icon name="photo" class="w-4 h-4 text-white" />
                                </div>
                                Galeri Foto
                            </h3>
                            <p class="text-sm text-gray-600 dark:text-gray-400 mb-6">
                                Upload foto-foto untuk menampilkan contoh tipe kamar ini
                            </p>

                            <div
                                class="bg-gradient-to-r from-pink-50/80 via-rose-50/50 to-pink-50/50 dark:from-pink-900/20 dark:via-rose-900/20 dark:to-pink-900/20 rounded-2xl p-6 border border-pink-200/50 dark:border-pink-700/50">

                                <!-- Upload Area -->
                                <div class="border-2 border-dashed border-pink-300 dark:border-pink-600 rounded-2xl p-8 text-center hover:border-pink-400 dark:hover:border-pink-500 transition-colors duration-300"
                                    x-data="{
                                        isDropping: false,
                                        isUploading: false,
                                        handleDrop(e) {
                                            this.isDropping = false;
                                            this.isUploading = true;
                                            const files = e.dataTransfer.files;
                                            if (files && files.length > 0) {
                                                $wire.uploadMultiple('incomingPhotos', files,
                                                    () => {
                                                        console.log('Upload success');
                                                        this.isUploading = false;
                                                    },
                                                    (err) => {
                                                        console.error('Upload error:', err);
                                                        this.isUploading = false;
                                                    }
                                                );
                                            }
                                        }
                                    }" @dragover.prevent="isDropping = true" @dragleave.prevent="isDropping = false"
                                    @drop.prevent="handleDrop($event)"
                                    :class="{ 'border-pink-400 bg-pink-50/50': isDropping }">
                                    <flux:icon name="photo"
                                        class="mx-auto h-12 w-12 text-pink-400 dark:text-pink-500 mb-4" />
                                    <div class="text-sm text-pink-600 dark:text-pink-400">
                                        <label for="galeri-upload"
                                            class="relative cursor-pointer bg-white dark:bg-pink-900/30 rounded-xl px-4 py-2 font-medium text-pink-700 dark:text-pink-300 hover:bg-pink-50 dark:hover:bg-pink-800/40 transition-colors duration-300">
                                            <span>Upload Foto</span>
                                            <input id="galeri-upload" type="file" multiple accept="image/*"
                                                class="sr-only" x-ref="fileInput" x-on:change="
                                                    isUploading = true;
                                                    $wire.uploadMultiple('incomingPhotos', $event.target.files,
                                                        () => {
                                                            console.log('Upload success');
                                                            isUploading = false;
                                                        },
                                                        (err) => {
                                                            console.error('Upload error:', err);
                                                            isUploading = false;
                                                        }
                                                    )
                                                ">
                                        </label>
                                        <p class="mt-2">atau drag & drop foto ke sini</p>
                                        <p class="text-xs text-pink-500 dark:text-pink-400 mt-1">PNG, JPG, JPEG hingga
                                            5MB</p>
                                        <p class="text-xs text-pink-500 dark:text-pink-400 mt-1">Bisa upload multiple
                                            gambar sekaligus dan tambah batch berkali-kali</p>

                                        <!-- Upload Progress -->
                                        <div x-show="isUploading" class="mt-4">
                                            <div class="flex items-center justify-center gap-2">
                                                <div
                                                    class="animate-spin rounded-full h-4 w-4 border-b-2 border-pink-500">
                                                </div>
                                                <span class="text-xs text-pink-600">Uploading...</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Debug Info -->
                                <div class="mt-4 p-3 bg-gray-100 dark:bg-gray-700 rounded-lg">
                                    <p class="text-xs text-gray-600 dark:text-gray-400">
                                        Debug: Total foto di galeriPhotos: {{ count($galeriPhotos) }}
                                    </p>
                                    <p class="text-xs text-gray-600 dark:text-gray-400">
                                        Debug: Total foto di incomingPhotos: {{ count($incomingPhotos) }}
                                    </p>
                                    @if(count($galeriPhotos) > 0)
                                    <p class="text-xs text-gray-600 dark:text-gray-400">
                                        Nama file: {{ collect($galeriPhotos)->map(fn($p) =>
                                        $p->getClientOriginalName())->implode(', ') }}
                                    </p>
                                    <p class="text-xs text-gray-600 dark:text-gray-400">
                                        Ukuran file: {{ collect($galeriPhotos)->map(fn($p) =>
                                        number_format($p->getSize() / 1024, 2) . ' KB')->implode(', ') }}
                                    </p>
                                    <p class="text-xs text-gray-600 dark:text-gray-400">
                                        Tipe file: {{ collect($galeriPhotos)->map(fn($p) =>
                                        $p->getMimeType())->implode(', ') }}
                                    </p>
                                    @endif
                                </div>

                                <!-- Preview Photos -->
                                @if(count($galeriPhotos) > 0)
                                <div class="mt-6">
                                    <h4 class="text-sm font-bold text-pink-700 dark:text-pink-300 mb-4">
                                        Foto yang akan diupload ({{ count($galeriPhotos) }} foto):
                                    </h4>
                                    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                                        @foreach($galeriPhotos as $index => $photo)
                                        <div class="relative group">
                                            <img src="{{ $photo->temporaryUrl() }}" alt="Preview {{ $index + 1 }}"
                                                class="w-full h-24 object-cover rounded-xl border-2 border-pink-200 dark:border-pink-700">
                                            <div
                                                class="absolute inset-0 bg-black/50 opacity-0 group-hover:opacity-100 transition-opacity duration-300 rounded-xl flex items-center justify-center">
                                                <button type="button" wire:click="removePhoto({{ $index }})"
                                                    class="w-8 h-8 bg-red-500 text-white rounded-full flex items-center justify-center text-sm font-bold hover:bg-red-600 transition-colors duration-300">
                                                    ×
                                                </button>
                                            </div>
                                            <div
                                                class="absolute bottom-0 left-0 right-0 bg-black/70 text-white text-xs p-1 rounded-b-xl">
                                                {{ $photo->getClientOriginalName() }}
                                            </div>
                                        </div>
                                        @endforeach
                                    </div>

                                    <!-- Batch Actions -->
                                    <div class="mt-4 flex gap-2">
                                        <button type="button" wire:click="clearAllPhotos"
                                            class="px-3 py-1 bg-red-500 text-white text-xs rounded-lg hover:bg-red-600 transition-colors duration-300">
                                            Hapus Semua Foto
                                        </button>
                                        <button type="button" wire:click="$refresh"
                                            class="px-3 py-1 bg-blue-500 text-white text-xs rounded-lg hover:bg-blue-600 transition-colors duration-300">
                                            Refresh State
                                        </button>
                                        <span class="text-xs text-gray-500 self-center">
                                            Total ukuran: {{ number_format(collect($galeriPhotos)->sum(fn($p) =>
                                            $p->getSize()) / 1024 / 1024, 2) }} MB
                                        </span>
                                    </div>
                                </div>
                                @endif
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div
                            class="flex items-center justify-end gap-4 pt-8 border-t border-gray-200/50 dark:border-gray-700/50">
                            <a href="{{ route('admin.tipe-kamar.index') }}"
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
                                <span class="relative z-10">Simpan Tipe Kamar</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>