<?php

use function Livewire\Volt\{ layout, title, state, mount, with, updated, usesPagination };
use App\Models\TipeKamar;
use App\Models\FasilitasKamar;
use App\Models\Kamar;

layout('components.layouts.admin');
title('Tipe Kamar');

// Aktifkan pagination
usesPagination();

state([
    'search' => '',
    'sortField' => 'created_at',
    'sortDirection' => 'desc',
    'showDeleteModal' => false,
    'typeToDelete' => null,
    'showDetailModal' => false,
    'selectedType' => null,
]);

// Sinkronkan ke URL
state(['search'])->url();
state(['page' => 1])->url(as: 'p', history: true, keep: true);

mount(function () {
    // Init state kalau perlu
});

// Lifecycle helpers Volt
updated([
    'search' => fn () => $this->resetPage(),
]);

$sortBy = function ($field) {
    if ($this->sortField === $field) {
        $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
    } else {
        $this->sortField = $field;
        $this->sortDirection = 'asc';
    }
    $this->resetPage();
};

$viewDetails = function ($typeId) {
    try {
        $type = TipeKamar::with([
            'fasilitasKamars:id,nama_fasilitas',
            'kamars:id,tipe_kamar_id',
            'galeriKamars:id,tipe_kamar_id,url_foto'
        ])->findOrFail($typeId);

        $this->selectedType = [
            'id' => $type->id,
            'nama_tipe' => $type->nama_tipe,
            'kode_tipe' => $type->kode_tipe,
            'deskripsi' => $type->deskripsi,
            'created_at' => optional($type->created_at)->format('d M Y'),
            'fasilitas' => $type->fasilitasKamars->pluck('nama_fasilitas')->sort()->values()->toArray(),
            'fasilitas_count' => $type->fasilitasKamars->count(),
            'kamar_count' => $type->kamars->count(),
            'galeri' => $type->galeriKamars->map(function($foto) {
                return [
                    'id' => $foto->id,
                    'url' => $foto->url_foto
                ];
            })->toArray(),
            'galeri_count' => $type->galeriKamars->count(),
        ];
        $this->showDetailModal = true;
    } catch (\Exception $e) {
        \Log::error('Gagal load detail tipe', ['error' => $e->getMessage()]);
        session()->flash('error', 'Tipe kamar tidak ditemukan.');
    }
};

$closeDetails = fn () => ($this->showDetailModal = false) && ($this->selectedType = null);

$confirmDeleteType = function ($typeId) {
    try {
        $type = TipeKamar::findOrFail($typeId);
        $this->typeToDelete = $type;
        $this->showDeleteModal = true;
    } catch (\Exception $e) {
        session()->flash('error', 'Tipe kamar tidak ditemukan.');
    }
};

$deleteType = function ($typeId) {
    try {
        $type = TipeKamar::withCount('kamars')->with('galeriKamars')->findOrFail($typeId);

        // Optional proteksi: cegah hapus bila masih ada kamar
        if ($type->kamars_count > 0) {
            session()->flash('error', 'Tidak dapat menghapus: masih ada kamar dengan tipe ini.');
        } else {
            // Delete galeri photos from storage
            foreach ($type->galeriKamars as $foto) {
                $path = str_replace('/storage/', '', $foto->url_foto);
                if (\Illuminate\Support\Facades\Storage::disk('public')->exists($path)) {
                    \Illuminate\Support\Facades\Storage::disk('public')->delete();
                    \Log::info('Deleted photo from storage:', ['path' => $path]);
                }
            }

            // Lepas relasi pivot (jaga-jaga bila tidak cascade)
            $type->fasilitasKamars()->detach();

            // Delete tipe kamar (galeri akan cascade delete)
            $type->delete();

            session()->flash('message', 'Tipe kamar berhasil dihapus.');
        }
    } catch (\Exception $e) {
        \Log::error('Gagal hapus tipe kamar:', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
        session()->flash('error', 'Gagal menghapus tipe kamar: ' . $e->getMessage());
    }

    $this->resetPage();
    $this->showDeleteModal = false;
    $this->typeToDelete = null;
};

$cancelDelete = fn () => ($this->showDeleteModal = false) && ($this->typeToDelete = null);

$deleteGaleriPhoto = function ($photoId) {
    try {
        $foto = \App\Models\GaleriKamar::findOrFail($photoId);

        // Delete from storage
        $path = str_replace('/storage/', '', $foto->url_foto);
        if (\Illuminate\Support\Facades\Storage::disk('public')->exists($path)) {
            \Illuminate\Support\Facades\Storage::disk('public')->delete($path);
            \Log::info('Deleted individual photo from storage:', ['path' => $path, 'photo_id' => $photoId]);
        }

        $foto->delete();

        // Refresh detail modal jika sedang terbuka
        if ($this->showDetailModal && $this->selectedType) {
            $this->viewDetails($this->selectedType['id']);
        }

        session()->flash('message', 'Foto galeri berhasil dihapus.');
    } catch (\Exception $e) {
        \Log::error('Gagal hapus foto galeri:', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
        session()->flash('error', 'Gagal menghapus foto galeri: ' . $e->getMessage());
    }
};

with(function () {
    try {
        $query = TipeKamar::query()->withCount(['fasilitasKamars', 'kamars']);

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('nama_tipe', 'like', '%' . $this->search . '%')
                  ->orWhere('deskripsi', 'like', '%' . $this->search . '%');
            });
        }

        // Validasi sortField yang diizinkan
        $sortable = ['nama_tipe', 'fasilitas_kamars_count', 'kamars_count', 'created_at'];
        $sortField = in_array($this->sortField, $sortable) ? $this->sortField : 'created_at';
        $sortDirection = $this->sortDirection === 'asc' ? 'asc' : 'desc';

        $types = $query->orderBy($sortField, $sortDirection)->paginate(10);

        // Statistik global
        $globalStats = [
            'totalTypes' => TipeKamar::count(),
            'withFacilities' => TipeKamar::has('fasilitasKamars')->count(),
            'withoutFacilities' => TipeKamar::doesntHave('fasilitasKamars')->count(),
            'totalFacilities' => FasilitasKamar::count(),
            'totalRooms' => Kamar::count(),
            'totalGaleri' => \App\Models\GaleriKamar::count(),
            'withGaleri' => TipeKamar::has('galeriKamars')->count(),
        ];

        \Log::info('Types data loaded', [
            'types_count' => $types->count(),
            'total_types' => $globalStats['totalTypes'],
            'search' => $this->search,
            'sort_field' => $sortField,
            'sort_direction' => $sortDirection,
        ]);

        return [
            'types' => $types,
            'stats' => $globalStats,
        ];
    } catch (\Exception $e) {
        \Log::error('Error loading types data', [
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString(),
        ]);

        return [
            'types' => collect(),
            'stats' => [
                'totalTypes' => 0,
                'withFacilities' => 0,
                'withoutFacilities' => 0,
                'totalFacilities' => 0,
                'totalRooms' => 0,
            ],
        ];
    }
});

?>

<div
    class="min-h-screen bg-gradient-to-br from-slate-50 via-blue-50 to-indigo-100 dark:from-gray-900 dark:via-gray-800 dark:to-slate-900">
    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Header -->
            <div class="mb-10">
                <div class="relative">
                    <div
                        class="absolute inset-0 bg-gradient-to-r from-blue-600/10 via-purple-600/10 to-pink-600/10 rounded-3xl blur-3xl">
                    </div>
                    <div
                        class="relative bg-white/70 dark:bg-gray-800/70 backdrop-blur-xl rounded-3xl p-8 border border-white/30 dark:border-gray-700/50 shadow-2xl">
                        <div class="flex flex-col lg:flex-row items-start lg:items-center justify-between gap-6">
                            <div class="flex-1">
                                <div class="flex items-center gap-4 mb-3">
                                    <div
                                        class="w-16 h-16 bg-gradient-to-br from-indigo-600 to-purple-700 rounded-2xl flex items-center justify-center shadow-lg">
                                        <flux:icon name="queue-list" class="w-8 h-8 text-white" />
                                    </div>
                                    <div>
                                        <h1
                                            class="text-2xl font-bold bg-gradient-to-r from-gray-900 via-blue-800 to-purple-800 dark:from-white dark:via-blue-200 dark:to-purple-200 bg-clip-text text-transparent">
                                            Kelola Tipe Kamar</h1>
                                        <p class="mt-2 text-sm text-gray-600 dark:text-gray-300 font-medium">Master tipe
                                            kamar, fasilitas & keterkaitan kamar</p>
                                    </div>
                                </div>
                            </div>
                            <a href="{{ route('admin.tipe-kamar.create') }}"
                                class="group relative inline-flex items-center gap-3 px-8 py-4 bg-gradient-to-r from-blue-600 via-purple-600 to-pink-600 hover:from-blue-700 hover:via-purple-700 hover:to-pink-700 text-white font-bold rounded-2xl shadow-2xl hover:shadow-blue-500/25 transform hover:-translate-y-1 transition-all duration-300 overflow-hidden"
                                wire:navigate>
                                <div
                                    class="absolute inset-0 bg-gradient-to-r from-white/20 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                                </div>
                                <flux:icon name="plus" class="relative z-10 h-6 w-6" />
                                <span class="relative z-10">Tambah Tipe</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Stats Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-6 gap-6 mb-10">
                <!-- Total Tipe -->
                <div
                    class="group relative overflow-hidden rounded-3xl bg-gradient-to-br from-indigo-500 via-indigo-600 to-indigo-700 p-6 text-white shadow-2xl transition-all duration-500 hover:scale-105 hover:shadow-indigo-500/25 transform hover:-translate-y-2">
                    <div class="absolute inset-0 bg-gradient-to-br from-indigo-600/20 to-transparent"></div>
                    <div
                        class="absolute top-0 right-0 w-32 h-32 bg-white/10 rounded-full -translate-y-16 translate-x-16">
                    </div>
                    <div class="relative z-10">
                        <div class="flex items-center justify-between mb-4">
                            <div
                                class="w-14 h-14 bg-white/20 rounded-2xl flex items-center justify-center backdrop-blur-sm border border-white/30">
                                <flux:icon name="queue-list" class="size-8" />
                            </div>
                        </div>
                        <h3 class="text-xl font-bold mb-2">{{ $stats['totalTypes'] }}</h3>
                        <p class="text-indigo-100 text-xs font-medium">Total Tipe</p>
                        @if($stats['totalTypes'] > 0)
                        <div class="text-xs text-indigo-200 mt-1">
                            @php
                            $sampleCodes = \App\Models\TipeKamar::take(3)->pluck('kode_tipe')->implode(', ');
                            @endphp
                            Sample: {{ $sampleCodes }}
                        </div>
                        @endif
                    </div>
                </div>

                <!-- Tipe dgn Fasilitas -->
                <div
                    class="group relative overflow-hidden rounded-3xl bg-gradient-to-br from-emerald-500 via-teal-600 to-emerald-700 p-6 text-white shadow-2xl transition-all duration-500 hover:scale-105 hover:shadow-emerald-500/25 transform hover:-translate-y-2">
                    <div class="absolute inset-0 bg-gradient-to-br from-emerald-600/20 to-transparent"></div>
                    <div
                        class="absolute top-0 right-0 w-32 h-32 bg-white/10 rounded-full -translate-y-16 translate-x-16">
                    </div>
                    <div class="relative z-10">
                        <div class="flex items-center justify-between mb-4">
                            <div
                                class="w-14 h-14 bg-white/20 rounded-2xl flex items-center justify-center backdrop-blur-sm border border-white/30">
                                <flux:icon name="check-circle" class="size-8" />
                            </div>
                        </div>
                        <h3 class="text-xl font-bold mb-2">{{ $stats['withFacilities'] }}</h3>
                        <p class="text-emerald-100 text-xs font-medium">Punya ≥1 Fasilitas</p>
                    </div>
                </div>

                <!-- Tipe tanpa Fasilitas -->
                <div
                    class="group relative overflow-hidden rounded-3xl bg-gradient-to-br from-amber-500 via-orange-600 to-amber-700 p-6 text-white shadow-2xl transition-all duration-500 hover:scale-105 hover:shadow-amber-500/25 transform hover:-translate-y-2">
                    <div class="absolute inset-0 bg-gradient-to-br from-amber-600/20 to-transparent"></div>
                    <div
                        class="absolute top-0 right-0 w-32 h-32 bg-white/10 rounded-full -translate-y-16 translate-x-16">
                    </div>
                    <div class="relative z-10">
                        <div class="flex items-center justify-between mb-4">
                            <div
                                class="w-14 h-14 bg-white/20 rounded-2xl flex items-center justify-center backdrop-blur-sm border border-white/30">
                                <flux:icon name="exclamation-triangle" class="size-8" />
                            </div>
                        </div>
                        <h3 class="text-xl font-bold mb-2">{{ $stats['withoutFacilities'] }}</h3>
                        <p class="text-amber-100 text-xs font-medium">Belum Punya Fasilitas</p>
                    </div>
                </div>

                <!-- Total Fasilitas -->
                <div
                    class="group relative overflow-hidden rounded-3xl bg-gradient-to-br from-blue-500 via-blue-600 to-blue-700 p-6 text-white shadow-2xl transition-all duration-500 hover:scale-105 hover:shadow-blue-500/25 transform hover:-translate-y-2">
                    <div class="absolute inset-0 bg-gradient-to-br from-blue-600/20 to-transparent"></div>
                    <div
                        class="absolute top-0 right-0 w-32 h-32 bg-white/10 rounded-full -translate-y-16 translate-x-16">
                    </div>
                    <div class="relative z-10">
                        <div class="flex items-center justify-between mb-4">
                            <div
                                class="w-14 h-14 bg-white/20 rounded-2xl flex items-center justify-center backdrop-blur-sm border border-white/30">
                                <flux:icon name="adjustments-horizontal" class="size-8" />
                            </div>
                        </div>
                        <h3 class="text-xl font-bold mb-2">{{ $stats['totalFacilities'] }}</h3>
                        <p class="text-blue-100 text-xs font-medium">Total Fasilitas</p>
                    </div>
                </div>

                <!-- Total Kamar -->
                <div
                    class="group relative overflow-hidden rounded-3xl bg-gradient-to-br from-purple-600 via-fuchsia-600 to-purple-700 p-6 text-white shadow-2xl transition-all duration-500 hover:scale-105 hover:shadow-purple-500/25 transform hover:-translate-y-2">
                    <div class="absolute inset-0 bg-gradient-to-br from-purple-600/20 to-transparent"></div>
                    <div
                        class="absolute top-0 right-0 w-32 h-32 bg-white/10 rounded-full -translate-y-16 translate-x-16">
                    </div>
                    <div class="relative z-10">
                        <div class="flex items-center justify-between mb-4">
                            <div
                                class="w-14 h-14 bg-white/20 rounded-2xl flex items-center justify-center backdrop-blur-sm border border-white/30">
                                <flux:icon name="bell-snooze" class="size-8" />
                            </div>
                        </div>
                        <h3 class="text-xl font-bold mb-2">{{ $stats['totalRooms'] }}</h3>
                        <p class="text-purple-100 text-xs font-medium">Total Kamar</p>
                    </div>
                </div>

                <!-- Total Galeri -->
                <div
                    class="group relative overflow-hidden rounded-3xl bg-gradient-to-br from-pink-600 via-rose-600 to-pink-700 p-6 text-white shadow-2xl transition-all duration-500 hover:scale-105 hover:shadow-pink-500/25 transform hover:-translate-y-2">
                    <div class="absolute inset-0 bg-gradient-to-br from-pink-600/20 to-transparent"></div>
                    <div
                        class="absolute top-0 right-0 w-32 h-32 bg-white/10 rounded-full -translate-y-16 translate-x-16">
                    </div>
                    <div class="relative z-10">
                        <div class="flex items-center justify-between mb-4">
                            <div
                                class="w-14 h-14 bg-white/20 rounded-2xl flex items-center justify-center backdrop-blur-sm border border-white/30">
                                <flux:icon name="photo" class="size-8" />
                            </div>
                        </div>
                        <h3 class="text-xl font-bold mb-2">{{ $stats['totalGaleri'] }}</h3>
                        <p class="text-pink-100 text-xs font-medium">Total Foto</p>
                    </div>
                </div>
            </div>

            <!-- Search & Sort -->
            <div class="mb-8">
                <div class="relative">
                    <div
                        class="absolute inset-0 bg-gradient-to-r from-blue-600/5 via-purple-600/5 to-pink-600/5 rounded-3xl blur-3xl">
                    </div>
                    <div
                        class="relative bg-white/80 dark:bg-gray-800/80 backdrop-blur-xl rounded-3xl p-6 border border-white/30 dark:border-gray-700/50 shadow-2xl">
                        <div class="flex flex-col lg:flex-row items-start lg:items-center gap-6">
                            <div class="flex-grow">
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                        <flux:icon name="magnifying-glass" class="h-6 w-6 text-gray-400" />
                                    </div>
                                    <input wire:model.live="search" type="text"
                                        placeholder="Cari tipe kamar (nama / deskripsi)..."
                                        class="block w-full pl-14 pr-4 py-3 border-0 rounded-2xl bg-gray-50/80 dark:bg-gray-700/80 text-gray-900 dark:text-gray-100 placeholder-gray-500 focus:ring-2 focus:ring-blue-500 focus:outline-none backdrop-blur-sm text-sm font-medium">
                                </div>
                            </div>
                            <div class="flex gap-3">
                                <button wire:click="sortBy('nama_tipe')"
                                    class="group px-6 py-4 border border-gray-300/50 dark:border-gray-600/50 rounded-2xl focus:outline-none focus:ring-2 focus:ring-blue-500 bg-gradient-to-r from-blue-50 to-purple-50 dark:from-blue-900/20 dark:to-purple-900/20 text-blue-600 dark:text-blue-400 hover:from-blue-100 hover:to-purple-100 dark:hover:from-blue-800/30 dark:hover:to-purple-800/30 transition-all duration-300 backdrop-blur-sm font-medium shadow-lg hover:shadow-xl transform hover:-translate-y-1">
                                    <span class="flex items-center gap-2">
                                        <flux:icon name="bars-3" class="w-5 h-5" />
                                        Nama
                                    </span>
                                    @if($sortField === 'nama_tipe')
                                    <span class="ml-2 text-sm">@if($sortDirection === 'asc') ↑ @else ↓ @endif</span>
                                    @endif
                                </button>
                                <button wire:click="sortBy('fasilitas_kamars_count')"
                                    class="group px-6 py-4 border border-gray-300/50 dark:border-gray-600/50 rounded-2xl focus:outline-none focus:ring-2 focus:ring-blue-500 bg-gradient-to-r from-emerald-50 to-teal-50 dark:from-emerald-900/20 dark:to-teal-900/20 text-emerald-600 dark:text-emerald-400 hover:from-emerald-100 hover:to-teal-100 dark:hover:from-emerald-800/30 dark:hover:to-teal-800/30 transition-all duration-300 backdrop-blur-sm font-medium shadow-lg hover:shadow-xl transform hover:-translate-y-1">
                                    <span class="flex items-center gap-2">
                                        <flux:icon name="chart-bar" class="w-5 h-5" />
                                        Fasilitas
                                    </span>
                                    @if($sortField === 'fasilitas_kamars_count')
                                    <span class="ml-2 text-sm">@if($sortDirection === 'asc') ↑ @else ↓ @endif</span>
                                    @endif
                                </button>
                                <button wire:click="sortBy('kamars_count')"
                                    class="group px-6 py-4 border border-gray-300/50 dark:border-gray-600/50 rounded-2xl focus:outline-none focus:ring-2 focus:ring-blue-500 bg-gradient-to-r from-purple-50 to-fuchsia-50 dark:from-purple-900/20 dark:to-fuchsia-900/20 text-purple-600 dark:text-purple-400 hover:from-purple-100 hover:to-fuchsia-100 dark:hover:from-purple-800/30 dark:hover:to-fuchsia-800/30 transition-all duration-300 backdrop-blur-sm font-medium shadow-lg hover:shadow-xl transform hover:-translate-y-1">
                                    <span class="flex items-center gap-2">
                                        <flux:icon name="bell-snooze" class="w-5 h-5" />
                                        Kamar
                                    </span>
                                    @if($sortField === 'kamars_count')
                                    <span class="ml-2 text-sm">@if($sortDirection === 'asc') ↑ @else ↓ @endif</span>
                                    @endif
                                </button>
                                <button wire:click="sortBy('created_at')"
                                    class="group px-6 py-4 border border-gray-300/50 dark:border-gray-600/50 rounded-2xl focus:outline-none focus:ring-2 focus:ring-blue-500 bg-gradient-to-r from-gray-50 to-gray-100 dark:from-gray-700 dark:to-gray-600 text-gray-700 dark:text-gray-200 hover:from-gray-100 hover:to-gray-200 dark:hover:from-gray-600 dark:hover:to-gray-500 transition-all duration-300 backdrop-blur-sm font-medium shadow-lg hover:shadow-xl transform hover:-translate-y-1">
                                    <span class="flex items-center gap-2">
                                        <flux:icon name="calendar" class="w-5 h-5" />
                                        Created
                                    </span>
                                    @if($sortField === 'created_at')
                                    <span class="ml-2 text-sm">@if($sortDirection === 'asc') ↑ @else ↓ @endif</span>
                                    @endif
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Flash Messages -->
            @if (session()->has('message'))
            <div
                class="mb-6 bg-gradient-to-r from-green-50 to-emerald-50 border border-green-200 text-green-800 px-6 py-4 rounded-2xl shadow-lg dark:from-green-900/20 dark:to-emerald-900/20 dark:border-green-700/30 dark:text-green-200">
                <div class="flex items-center gap-3">
                    <flux:icon name="check-circle" class="w-6 h-6" />
                    {{ session('message') }}
                </div>
            </div>
            @endif

            @if (session()->has('error'))
            <div
                class="mb-6 bg-gradient-to-r from-red-50 to-pink-50 border border-red-200 text-red-800 px-6 py-4 rounded-2xl shadow-lg dark:from-red-900/20 dark:to-pink-900/20 dark:border-red-700/30 dark:text-red-200">
                <div class="flex items-center gap-3">
                    <flux:icon name="exclamation-triangle" class="w-6 h-6" />
                    {{ session('error') }}
                </div>
            </div>
            @endif

            <!-- Types Table -->
            <div class="relative">
                <div
                    class="absolute inset-0 bg-gradient-to-r from-blue-600/5 via-purple-600/5 to-pink-600/5 rounded-3xl blur-3xl">
                </div>
                <div
                    class="relative bg-white/90 dark:bg-gray-800/90 shadow-2xl rounded-3xl overflow-hidden border border-white/30 dark:border-gray-700/50 backdrop-blur-xl">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200/50 dark:divide-gray-700/50">
                            <thead
                                class="bg-gradient-to-r from-gray-50/90 via-blue-50/50 to-purple-50/50 dark:from-gray-700/90 dark:via-blue-900/20 dark:to-purple-900/20">
                                <tr>
                                    <th
                                        class="px-8 py-6 text-left text-sm font-bold text-gray-700 dark:text-gray-200 uppercase tracking-wider">
                                        Tipe</th>
                                    <th
                                        class="px-8 py-6 text-left text-sm font-bold text-gray-700 dark:text-gray-200 uppercase tracking-wider">
                                        Kode</th>
                                    <th
                                        class="px-8 py-6 text-left text-sm font-bold text-gray-700 dark:text-gray-200 uppercase tracking-wider">
                                        Fasilitas</th>
                                    <th
                                        class="px-8 py-6 text-left text-sm font-bold text-gray-700 dark:text-gray-200 uppercase tracking-wider">
                                        Kamar</th>
                                    <th
                                        class="px-8 py-6 text-left text-sm font-bold text-gray-700 dark:text-gray-200 uppercase tracking-wider">
                                        Galeri</th>
                                    <th
                                        class="px-8 py-6 text-left text-sm font-bold text-gray-700 dark:text-gray-200 uppercase tracking-wider">
                                        Dibuat</th>
                                    <th
                                        class="px-8 py-6 text-left text-sm font-bold text-gray-700 dark:text-gray-200 uppercase tracking-wider">
                                        Aksi</th>
                                </tr>
                            </thead>
                            <tbody
                                class="bg-white/50 dark:bg-gray-800/50 divide-y divide-gray-200/30 dark:divide-gray-700/30">
                                @forelse($types as $t)
                                <tr
                                    class="hover:bg-gradient-to-r hover:from-blue-50/50 hover:to-purple-50/50 dark:hover:from-blue-900/10 dark:hover:to-purple-900/10 transition-all duration-300 group">
                                    <td class="px-8 py-6 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0 h-12 w-12">
                                                <div
                                                    class="h-12 w-12 rounded-2xl bg-gradient-to-br from-indigo-600 to-purple-700 flex items-center justify-center shadow-lg border-2 border-white/20">
                                                    <span class="text-white font-bold text-lg">{{
                                                        strtoupper(substr($t->nama_tipe, 0, 1)) }}</span>
                                                </div>
                                            </div>
                                            <div class="ml-5">
                                                <div class="text-sm font-bold text-gray-900 dark:text-white capitalize">
                                                    {{ $t->nama_tipe }}</div>
                                                <div class="text-xs text-gray-500 dark:text-gray-400">ID: {{ $t->id }}
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-8 py-6 whitespace-nowrap">
                                        <span
                                            class="inline-flex items-center px-3 py-1.5 rounded-xl text-xs font-medium bg-gradient-to-r from-blue-100 to-blue-200 text-blue-700 dark:from-blue-600 dark:to-blue-500 dark:text-blue-200 border border-blue-200/50 dark:border-blue-500/50 font-mono">
                                            {{ $t->kode_tipe }}
                                        </span>
                                    </td>
                                    <td class="px-8 py-6 whitespace-nowrap">
                                        <span
                                            class="inline-flex items-center px-3 py-1.5 rounded-xl text-xs font-medium bg-gradient-to-r from-gray-100 to-gray-200 text-gray-700 dark:from-gray-700 dark:to-gray-600 dark:text-gray-200 border border-gray-200/50 dark:border-gray-600/50">
                                            {{ $t->fasilitas_kamars_count }} fasilitas
                                        </span>
                                    </td>
                                    <td class="px-8 py-6 whitespace-nowrap">
                                        <span
                                            class="inline-flex items-center px-3 py-1.5 rounded-xl text-xs font-medium bg-gradient-to-r from-gray-100 to-gray-200 text-gray-700 dark:from-gray-700 dark:to-gray-600 dark:text-gray-200 border border-gray-200/50 dark:border-gray-600/50">
                                            {{ $t->kamars_count }} kamar
                                        </span>
                                    </td>
                                    <td class="px-8 py-6 whitespace-nowrap">
                                        <span
                                            class="inline-flex items-center px-3 py-1.5 rounded-xl text-xs font-medium bg-gradient-to-r from-gray-100 to-gray-200 text-gray-700 dark:from-gray-700 dark:to-gray-600 dark:text-gray-200 border border-gray-200/50 dark:border-gray-600/50">
                                            {{ $t->galeri_count ?? 0 }} galeri
                                        </span>
                                    </td>
                                    <td
                                        class="px-8 py-6 whitespace-nowrap text-sm text-gray-600 dark:text-gray-300 font-medium">
                                        {{ optional($t->created_at)->format('d M Y') }}</td>
                                    <td class="px-8 py-6 whitespace-nowrap text-right text-sm font-medium">
                                        <div class="flex space-x-3">
                                            <button wire:click="viewDetails({{ $t->id }})"
                                                class="group p-3 text-blue-600 dark:text-blue-400 hover:text-blue-900 dark:hover:text-blue-300 transition-all duration-300 bg-blue-50 dark:bg-blue-900/20 rounded-xl hover:bg-blue-100 dark:hover:bg-blue-800/30 border border-blue-200/50 dark:border-blue-700/50 hover:shadow-lg transform hover:-translate-y-1">
                                                <svg class="h-5 w-5" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7-1.274 4.057-5.065 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                </svg>
                                            </button>
                                            <a href="{{ route('admin.tipe-kamar.edit', $t) }}"
                                                class="group p-3 text-emerald-600 dark:text-emerald-400 hover:text-emerald-900 dark:hover:text-emerald-300 transition-all duration-300 bg-emerald-50 dark:bg-emerald-900/20 rounded-xl hover:bg-emerald-100 dark:hover:bg-emerald-800/30 border border-emerald-200/50 dark:border-emerald-700/50 hover:shadow-lg transform hover:-translate-y-1"
                                                wire:navigate>
                                                <svg class="h-5 w-5" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                </svg>
                                            </a>
                                            <button wire:click="confirmDeleteType({{ $t->id }})"
                                                class="group p-3 text-red-600 dark:text-red-400 hover:text-red-900 dark:hover:text-red-300 transition-all duration-300 bg-red-50 dark:bg-red-900/20 rounded-xl hover:bg-red-100 dark:hover:bg-red-800/30 border border-red-200/50 dark:border-red-700/50 hover:shadow-lg transform hover:-translate-y-1">
                                                <svg class="h-5 w-5" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                </svg>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="7" class="px-8 py-16 text-center">
                                        <div
                                            class="mx-auto h-32 w-32 bg-gradient-to-br from-gray-200 to-gray-300 dark:from-gray-600 dark:to-gray-700 rounded-full flex items-center justify-center mb-8 shadow-lg">
                                            <svg class="h-16 w-16 text-gray-400 dark:text-gray-500" fill="none"
                                                stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M3 7.5h18M3 12h18M3 16.5h18" />
                                            </svg>
                                        </div>
                                        <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-3">Belum ada tipe
                                            kamar</h3>
                                        <p class="text-sm text-gray-600 dark:text-gray-400 mb-8">Mulai dengan
                                            menambahkan tipe kamar baru.</p>
                                        <a href="{{ route('admin.tipe-kamar.create') }}"
                                            class="inline-flex items-center gap-3 px-8 py-4 bg-gradient-to-r from-blue-600 via-purple-600 to-pink-600 hover:from-blue-700 hover:via-purple-700 hover:to-pink-700 text-white font-bold rounded-2xl shadow-2xl hover:shadow-blue-500/25 transform hover:-translate-y-1 transition-all duration-300"
                                            wire:navigate>
                                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M12 4v16m8-8H4" />
                                            </svg>
                                            <span>Tambah Tipe Pertama</span>
                                        </a>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    @if($types->hasPages())
                    <div
                        class="px-8 py-6 bg-gradient-to-r from-gray-50/50 via-blue-50/30 to-purple-50/30 dark:from-gray-700/50 dark:via-blue-900/10 dark:to-purple-900/10">
                        {{ $types->links() }}
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    @if($showDeleteModal)
    <div class="fixed inset-0 z-50 overflow-y-auto">
        <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
            <div class="fixed inset-0 backdrop-blur-md transition-opacity" wire:click="cancelDelete"></div>
            <div
                class="relative transform overflow-hidden rounded-3xl bg-white/95 dark:bg-gray-800/95 backdrop-blur-xl px-6 pb-6 pt-6 text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-lg sm:p-8 border border-white/30 dark:border-gray-700/50">
                <div class="sm:flex sm:items-start">
                    <div
                        class="mx-auto flex h-16 w-16 flex-shrink-0 items-center justify-center rounded-2xl bg-gradient-to-br from-red-100 to-pink-100 dark:from-red-900/40 dark:to-pink-900/40 sm:mx-0 sm:h-14 sm:w-14 border border-red-200/50 dark:border-red-700/50">
                        <svg class="h-8 w-8 text-red-600 dark:text-red-400" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 9v2m0 4h.01M5.062 19h13.876c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.34 16.5c-.77.833.192 2.5 1.722 2.5z" />
                        </svg>
                    </div>
                    <div class="mt-4 text-center sm:ml-6 sm:mt-0 sm:text-left">
                        <h3 class="text-lg font-bold leading-6 text-gray-900 dark:text-white">Hapus Tipe Kamar</h3>
                        <div class="mt-3">
                            <p class="text-sm text-gray-600 dark:text-gray-300">
                                @if($typeToDelete)
                                Apakah Anda yakin ingin menghapus tipe <strong
                                    class="text-gray-900 dark:text-white font-bold">{{ $typeToDelete->nama_tipe
                                    }}</strong> (ID: {{ $typeToDelete->id }})?
                                <br><br>
                                <span class="text-red-600 dark:text-red-400 font-medium">Tindakan ini tidak dapat
                                    dibatalkan.</span>
                                @else
                                Apakah Anda yakin ingin menghapus tipe kamar ini? Tindakan ini tidak dapat dibatalkan.
                                @endif
                            </p>
                        </div>
                    </div>
                </div>
                <div class="mt-6 sm:mt-5 sm:flex sm:flex-row-reverse gap-4">
                    <button wire:click="deleteType({{ $typeToDelete ? $typeToDelete->id : 0 }})" type="button"
                        class="inline-flex w-full justify-center rounded-2xl bg-gradient-to-r from-red-600 to-pink-600 hover:from-red-700 hover:to-pink-700 px-6 py-3 text-sm font-bold text-white shadow-lg hover:shadow-red-500/25 transition-all duration-300 transform hover:-translate-y-1 sm:w-auto">
                        <flux:icon name="trash" class="w-5 h-5 mr-2" />
                        Hapus Tipe
                    </button>
                    <button wire:click="cancelDelete" type="button"
                        class="mt-4 inline-flex w-full justify-center rounded-2xl bg-gradient-to-r from-gray-100 to-gray-200 dark:from-gray-700 dark:to-gray-600 px-6 py-3 text-sm font-bold text-gray-700 dark:text-gray-200 shadow-lg hover:shadow-gray-500/25 transition-all duration-300 transform hover:-translate-y-1 sm:mt-0 sm:w-auto">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                        Batal
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Detail Type Modal -->
    @if($showDetailModal && $selectedType)
    <div class="fixed inset-0 z-50 overflow-y-auto">
        <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
            <div class="fixed inset-0 backdrop-blur-md transition-opacity" wire:click="closeDetails"></div>
            <div
                class="relative transform overflow-hidden rounded-3xl bg-white/95 dark:bg-gray-800/95 backdrop-blur-xl px-6 pb-6 pt-6 text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-2xl sm:p-8 border border-white/30 dark:border-gray-700/50">
                <div class="sm:flex sm:items-start">
                    <div
                        class="mx-auto flex h-16 w-16 flex-shrink-0 items-center justify-center rounded-2xl bg-gradient-to-br from-blue-100 to-purple-100 dark:from-blue-900/40 dark:to-purple-900/40 sm:mx-0 sm:h-14 sm:w-14 border border-blue-200/50 dark:border-blue-700/50">
                        <div
                            class="h-10 w-10 rounded-2xl bg-gradient-to-br from-indigo-600 to-purple-700 flex items-center justify-center shadow-lg border-2 border-white/20">
                            <span class="text-white font-bold text-base">{{ strtoupper(substr($selectedType['nama_tipe']
                                ?? 'T', 0, 1)) }}</span>
                        </div>
                    </div>
                    <div class="mt-4 text-center sm:ml-6 sm:mt-0 sm:text-left">
                        <h3 class="text-lg font-bold leading-6 text-gray-900 dark:text-white">Detail Tipe Kamar</h3>
                        <div class="mt-3 text-sm text-gray-600 dark:text-gray-300">
                            <div class="font-semibold text-gray-900 dark:text-white">{{ $selectedType['nama_tipe'] }}
                            </div>
                            <div class="text-xs mt-1 text-blue-600 dark:text-blue-400 font-mono">Kode: {{
                                $selectedType['kode_tipe'] }}</div>
                            <div class="text-xs mt-1">Dibuat: {{ $selectedType['created_at'] }}</div>
                            <div class="mt-3 text-gray-700 dark:text-gray-200">{{ $selectedType['deskripsi'] }}</div>
                        </div>
                    </div>
                </div>

                <div class="mt-6 grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <div
                        class="bg-gradient-to-r from-gray-50 to-gray-100 dark:from-gray-700 dark:to-gray-600 rounded-2xl p-4 border border-gray-200/50 dark:border-gray-600/50">
                        <div class="text-xs font-bold uppercase text-gray-500 dark:text-gray-400 mb-2">Fasilitas</div>
                        <div class="flex flex-wrap gap-2 max-h-48 overflow-auto pr-1">
                            @php $facilities = $selectedType['fasilitas'] ?? []; @endphp
                            @forelse($facilities as $f)
                            <span
                                class="inline-flex items-center px-3 py-1.5 rounded-xl text-xs font-medium bg-gradient-to-r from-gray-100 to-gray-200 text-gray-700 dark:from-gray-600 dark:to-gray-500 dark:text-gray-200 border border-gray-200/50 dark:border-gray-500/50">{{
                                $f }}</span>
                            @empty
                            <span class="text-xs text-gray-500 dark:text-gray-400">Belum ada fasilitas.</span>
                            @endforelse
                        </div>
                    </div>
                    <div
                        class="bg-gradient-to-r from-blue-50 to-purple-50 dark:from-blue-900/20 dark:to-purple-900/20 rounded-2xl p-4 border border-blue-200/50 dark:border-blue-700/50">
                        <div class="text-xs font-bold uppercase text-blue-700 dark:text-blue-300 mb-2">Ringkasan</div>
                        <div class="text-sm text-gray-700 dark:text-gray-200">
                            <div class="flex items-center justify-between py-1">
                                <span>Kode Tipe</span>
                                <span class="font-bold font-mono text-blue-600 dark:text-blue-400">{{
                                    $selectedType['kode_tipe'] }}</span>
                            </div>
                            <div class="flex items-center justify-between py-1">
                                <span>Jumlah Fasilitas</span>
                                <span class="font-bold">{{ $selectedType['fasilitas_count'] }}</span>
                            </div>
                            <div class="flex items-center justify-between py-1">
                                <span>Jumlah Kamar</span>
                                <span class="font-bold">{{ $selectedType['kamar_count'] }}</span>
                            </div>
                            <div class="flex items-center justify-between py-1">
                                <span>Jumlah Foto</span>
                                <span class="font-bold">{{ $selectedType['galeri_count'] }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Galeri Section -->
                <div class="mt-6">
                    <div class="text-xs font-bold uppercase text-pink-700 dark:text-pink-300 mb-4">Galeri Foto</div>
                    <div
                        class="bg-gradient-to-r from-pink-50/80 via-rose-50/50 to-pink-50/50 dark:from-pink-900/20 dark:via-rose-900/20 dark:to-pink-900/20 rounded-2xl p-4 border border-pink-200/50 dark:border-pink-700/50">
                        @php $galeri = $selectedType['galeri'] ?? []; @endphp
                        @if(count($galeri) > 0)
                        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                            @foreach($galeri as $foto)
                            <div
                                class="group relative overflow-hidden rounded-xl bg-white/50 dark:bg-pink-900/30 border border-pink-200/30 dark:border-pink-700/30">
                                <img src="{{ $foto['url'] }}" alt="Foto {{ $selectedType['nama_tipe'] }}"
                                    class="w-full h-24 object-cover group-hover:scale-110 transition-transform duration-300">
                                <div
                                    class="absolute inset-0 bg-black/0 group-hover:bg-black/20 transition-all duration-300 flex items-center justify-center">
                                    <flux:icon name="eye"
                                        class="w-6 h-6 text-white opacity-0 group-hover:opacity-100 transition-opacity duration-300" />
                                </div>
                                <!-- Delete Button -->
                                <button wire:click="deleteGaleriPhoto({{ $foto['id'] }})"
                                    wire:confirm="Apakah Anda yakin ingin menghapus foto ini? Tindakan ini tidak dapat dibatalkan."
                                    class="absolute top-1 right-1 w-6 h-6 bg-red-500 text-white rounded-full flex items-center justify-center text-xs font-bold hover:bg-red-600 transition-colors duration-300 opacity-0 group-hover:opacity-100">
                                    ×
                                </button>
                            </div>
                            @endforeach
                        </div>
                        @else
                        <div class="text-center py-8">
                            <div
                                class="mx-auto h-16 w-16 bg-gradient-to-br from-pink-200 to-rose-300 dark:from-pink-600 dark:to-rose-700 rounded-full flex items-center justify-center mb-4 shadow-lg">
                                <flux:icon name="photo" class="h-8 w-8 text-pink-600 dark:text-pink-400" />
                            </div>
                            <p class="text-sm text-pink-600 dark:text-pink-400">Belum ada foto galeri untuk tipe ini</p>
                        </div>
                        @endif
                    </div>
                </div>

                <div class="mt-6 sm:mt-5 sm:flex sm:flex-row-reverse gap-4">
                    <a href="{{ isset($selectedType['id']) ? route('admin.tipe-kamar.edit', $selectedType['id']) : '#' }}"
                        class="inline-flex w-full justify-center rounded-2xl bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 px-6 py-3 text-sm font-bold text-white shadow-lg hover:shadow-emerald-500/25 transition-all duration-300 transform hover:-translate-y-1 sm:w-auto"
                        wire:navigate>
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                        </svg>
                        Edit Tipe
                    </a>
                    <button wire:click="closeDetails" type="button"
                        class="mt-4 inline-flex w-full justify-center rounded-2xl bg-gradient-to-r from-gray-100 to-gray-200 dark:from-gray-700 dark:to-gray-600 px-6 py-3 text-sm font-bold text-gray-700 dark:text-gray-200 shadow-lg hover:shadow-gray-500/25 transition-all duration-300 transform hover:-translate-y-1 sm:mt-0 sm:w-auto">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                        Tutup
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>