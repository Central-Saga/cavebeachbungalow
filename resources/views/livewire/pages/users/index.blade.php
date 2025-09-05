<?php

use function Livewire\Volt\{
    layout, title, state, mount, with,
    updated, usesPagination
};
use App\Models\User;
use Spatie\Permission\Models\Role;

layout('components.layouts.admin');
title('User Management');

// Aktifkan pagination
usesPagination();

state([
    'search' => '',
    'sortField' => 'created_at',
    'sortDirection' => 'desc',
    'showDeleteModal' => false,
    'userToDelete' => null,
    'showDetailModal' => false,
    'selectedUser' => null,
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

$viewDetails = function ($userId) {
    try {
        $user = User::with(['roles', 'roles.permissions'])->findOrFail($userId);
        // Ambil semua permission via Spatie (role + direct)
        $allPermissions = method_exists($user, 'getAllPermissions')
            ? $user->getAllPermissions()->pluck('name')->sort()->values()->toArray()
            : [];

        $this->selectedUser = [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'created_at' => optional($user->created_at)->format('d M Y'),
            'roles' => $user->roles->pluck('name')->sort()->values()->toArray(),
            'permissions' => $allPermissions,
        ];
        $this->showDetailModal = true;
    } catch (\Exception $e) {
        \Log::error('Gagal load detail user', ['error' => $e->getMessage()]);
        session()->flash('error', 'User tidak ditemukan.');
    }
};

$closeDetails = fn () => ($this->showDetailModal = false) && ($this->selectedUser = null);

$confirmDeleteUser = function ($userId) {
    try {
        $user = User::findOrFail($userId);
        // Proteksi: jangan hapus diri sendiri atau user super-admin pertama (opsional)
        if (auth()->id() === $user->id) {
            session()->flash('error', 'Anda tidak dapat menghapus akun Anda sendiri.');
            return;
        }
        $this->userToDelete = $user;
        $this->showDeleteModal = true;
    } catch (\Exception $e) {
        session()->flash('error', 'User tidak ditemukan.');
    }
};

$deleteUser = function ($userId) {
    try {
        $user = User::findOrFail($userId);
        if (auth()->id() === $user->id) {
            session()->flash('error', 'Anda tidak dapat menghapus akun Anda sendiri.');
        } else {
            $user->delete();
            session()->flash('message', 'User berhasil dihapus.');
        }
    } catch (\Exception $e) {
        session()->flash('error', 'Gagal menghapus user: ' . $e->getMessage());
    }

    $this->resetPage();
    $this->showDeleteModal = false;
    $this->userToDelete = null;
};

$cancelDelete = fn () => ($this->showDeleteModal = false) && ($this->userToDelete = null);

with(function () {
    try {
        $query = User::query()->with('roles');

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('name', 'like', '%' . $this->search . '%')
                  ->orWhere('email', 'like', '%' . $this->search . '%');
            });
        }

        // Validasi sortField yang diizinkan
        $sortable = ['name', 'email', 'created_at'];
        $sortField = in_array($this->sortField, $sortable) ? $this->sortField : 'created_at';
        $sortDirection = $this->sortDirection === 'asc' ? 'asc' : 'desc';

        $users = $query->orderBy($sortField, $sortDirection)->paginate(10);

        // Statistik global
        $globalStats = [
            'totalUsers' => User::count(),
            'withRoles' => User::has('roles')->count(),
            'withoutRoles' => User::doesntHave('roles')->count(),
            'rolesCount' => Role::count(),
        ];

        \Log::info('Users data loaded', [
            'users_count' => $users->count(),
            'total_users' => $globalStats['totalUsers'],
            'search' => $this->search,
            'sort_field' => $sortField,
            'sort_direction' => $sortDirection,
        ]);

        return [
            'users' => $users,
            'stats' => $globalStats,
        ];
    } catch (\Exception $e) {
        \Log::error('Error loading users data', [
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString(),
        ]);

        return [
            'users' => collect(),
            'stats' => [
                'totalUsers' => 0,
                'withRoles' => 0,
                'withoutRoles' => 0,
                'rolesCount' => 0,
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
                                        class="w-16 h-16 bg-gradient-to-br from-blue-600 to-purple-700 rounded-2xl flex items-center justify-center shadow-lg">
                                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                        </svg>
                                    </div>
                                    <div>
                                        <h1
                                            class="text-2xl font-bold bg-gradient-to-r from-gray-900 via-blue-800 to-purple-800 dark:from-white dark:via-blue-200 dark:to-purple-200 bg-clip-text text-transparent">
                                            Kelola Users</h1>
                                        <p class="mt-2 text-sm text-gray-600 dark:text-gray-300 font-medium">Manajemen
                                            pengguna, role, dan akses</p>
                                    </div>
                                </div>
                            </div>
                            <a href="{{ route('admin.users.create') }}"
                                class="group relative inline-flex items-center gap-3 px-8 py-4 bg-gradient-to-r from-blue-600 via-purple-600 to-pink-600 hover:from-blue-700 hover:via-purple-700 hover:to-pink-700 text-white font-bold rounded-2xl shadow-2xl hover:shadow-blue-500/25 transform hover:-translate-y-1 transition-all duration-300 overflow-hidden"
                                wire:navigate>
                                <div
                                    class="absolute inset-0 bg-gradient-to-r from-white/20 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                                </div>
                                <svg class="relative z-10 h-6 w-6" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 4v16m8-8H4" />
                                </svg>
                                <span class="relative z-10">Buat User Baru</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Stats Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-10">
                <!-- Total Users -->
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
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                    stroke-width="1.5" stroke="currentColor" class="size-8">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M18 18.72a9.094 9.094 0 0 0 3.741-.479 3 3 0 0 0-4.682-2.72m.94 3.198.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0 1 12 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 0 1 6 18.719m12 0a5.971 5.971 0 0 0-.941-3.197m0 0A5.995 5.995 0 0 0 12 12.75a5.995 5.995 0 0 0-5.058 2.772m0 0a3 3 0 0 0-4.681 2.72 8.986 8.986 0 0 0 3.74.477m.94-3.197a5.971 5.971 0 0 0-.94 3.197M15 6.75a3 3 0 1 1-6 0 3 3 0 0 1 6 0Zm6 3a2.25 2.25 0 1 1-4.5 0 2.25 2.25 0 0 1 4.5 0Zm-13.5 0a2.25 2.25 0 1 1-4.5 0 2.25 2.25 0 0 1 4.5 0Z" />
                                </svg>
                            </div>
                        </div>
                        <h3 class="text-xl font-bold mb-2">{{ $stats['totalUsers'] }}</h3>
                        <p class="text-blue-100 text-xs font-medium">Total Users</p>
                    </div>
                </div>

                <!-- Users dengan Role -->
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
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                    stroke-width="1.5" stroke="currentColor" class="size-8">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M9 12.75 11.25 15 15 9.75m-3-7.036A11.959 11.959 0 0 1 3.598 6 11.99 11.99 0 0 0 3 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285Z" />
                                </svg>
                            </div>
                        </div>
                        <h3 class="text-xl font-bold mb-2">{{ $stats['withRoles'] }}</h3>
                        <p class="text-emerald-100 text-xs font-medium">Users dengan Role</p>
                    </div>
                </div>

                <!-- Users tanpa Role -->
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
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                    stroke-width="1.5" stroke="currentColor" class="size-8">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M12 9v3.75m0-10.036A11.959 11.959 0 0 1 3.598 6 11.99 11.99 0 0 0 3 9.75c0 5.592 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.57-.598-3.75h-.152c-3.196 0-6.1-1.25-8.25-3.286Zm0 13.036h.008v.008H12v-.008Z" />
                                </svg>
                            </div>
                        </div>
                        <h3 class="text-xl font-bold mb-2">{{ $stats['withoutRoles'] }}</h3>
                        <p class="text-amber-100 text-xs font-medium">Users tanpa Role</p>
                    </div>
                </div>

                <!-- Jumlah Role -->
                <div
                    class="group relative overflow-hidden rounded-3xl bg-gradient-to-br from-indigo-600 via-purple-600 to-indigo-700 p-6 text-white shadow-2xl transition-all duration-500 hover:scale-105 hover:shadow-purple-500/25 transform hover:-translate-y-2">
                    <div class="absolute inset-0 bg-gradient-to-br from-purple-600/20 to-transparent"></div>
                    <div
                        class="absolute top-0 right-0 w-32 h-32 bg-white/10 rounded-full -translate-y-16 translate-x-16">
                    </div>
                    <div class="relative z-10">
                        <div class="flex items-center justify-between mb-4">
                            <div
                                class="w-14 h-14 bg-white/20 rounded-2xl flex items-center justify-center backdrop-blur-sm border border-white/30">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                    stroke-width="1.5" stroke="currentColor" class="size-8">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M16.5 10.5V6.75a4.5 4.5 0 1 0-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 0 0 2.25-2.25v-6.75a2.25 2.25 0 0 0-2.25-2.25H6.75a2.25 2.25 0 0 0-2.25 2.25v6.75a2.25 2.25 0 0 0 2.25 2.25Z" />
                                </svg>
                            </div>
                        </div>
                        <h3 class="text-xl font-bold mb-2">{{ $stats['rolesCount'] }}</h3>
                        <p class="text-indigo-100 text-xs font-medium">Jumlah Role</p>
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
                                        <svg class="h-6 w-6 text-gray-400" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                        </svg>
                                    </div>
                                    <input wire:model.live="search" type="text"
                                        placeholder="Cari user (nama / email)..."
                                        class="block w-full pl-14 pr-4 py-3 border-0 rounded-2xl bg-gray-50/80 dark:bg-gray-700/80 text-gray-900 dark:text-gray-100 placeholder-gray-500 focus:ring-2 focus:ring-blue-500 focus:outline-none backdrop-blur-sm text-sm font-medium">
                                </div>
                            </div>
                            <div class="flex gap-3">
                                <button wire:click="sortBy('name')"
                                    class="group px-6 py-4 border border-gray-300/50 dark:border-gray-600/50 rounded-2xl focus:outline-none focus:ring-2 focus:ring-blue-500 bg-gradient-to-r from-blue-50 to-purple-50 dark:from-blue-900/20 dark:to-purple-900/20 text-blue-600 dark:text-blue-400 hover:from-blue-100 hover:to-purple-100 dark:hover:from-blue-800/30 dark:hover:to-purple-800/30 transition-all duration-300 backdrop-blur-sm font-medium shadow-lg hover:shadow-xl transform hover:-translate-y-1">
                                    <span class="flex items-center gap-2">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4" />
                                        </svg>
                                        Name
                                    </span>
                                    @if($sortField === 'name')
                                    <span class="ml-2 text-sm">@if($sortDirection === 'asc') ↑ @else ↓ @endif</span>
                                    @endif
                                </button>
                                <button wire:click="sortBy('email')"
                                    class="group px-6 py-4 border border-gray-300/50 dark:border-gray-600/50 rounded-2xl focus:outline-none focus:ring-2 focus:ring-blue-500 bg-gradient-to-r from-emerald-50 to-teal-50 dark:from-emerald-900/20 dark:to-teal-900/20 text-emerald-600 dark:text-emerald-400 hover:from-emerald-100 hover:to-teal-100 dark:hover:from-emerald-800/30 dark:hover:to-teal-800/30 transition-all duration-300 backdrop-blur-sm font-medium shadow-lg hover:shadow-xl transform hover:-translate-y-1">
                                    <span class="flex items-center gap-2">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M16 12H8m8 4H8m8-8H8m12 8V8a2 2 0 00-2-2H6a2 2 0 00-2 2v8a2 2 0 002 2h12a2 2 0 002-2z" />
                                        </svg>
                                        Email
                                    </span>
                                    @if($sortField === 'email')
                                    <span class="ml-2 text-sm">@if($sortDirection === 'asc') ↑ @else ↓ @endif</span>
                                    @endif
                                </button>
                                <button wire:click="sortBy('created_at')"
                                    class="group px-6 py-4 border border-gray-300/50 dark:border-gray-600/50 rounded-2xl focus:outline-none focus:ring-2 focus:ring-blue-500 bg-gradient-to-r from-gray-50 to-gray-100 dark:from-gray-700 dark:to-gray-600 text-gray-700 dark:text-gray-200 hover:from-gray-100 hover:to-gray-200 dark:hover:from-gray-600 dark:hover:to-gray-500 transition-all duration-300 backdrop-blur-sm font-medium shadow-lg hover:shadow-xl transform hover:-translate-y-1">
                                    <span class="flex items-center gap-2">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        </svg>
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
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    {{ session('message') }}
                </div>
            </div>
            @endif

            @if (session()->has('error'))
            <div
                class="mb-6 bg-gradient-to-r from-red-50 to-pink-50 border border-red-200 text-red-800 px-6 py-4 rounded-2xl shadow-lg dark:from-red-900/20 dark:to-pink-900/20 dark:border-red-700/30 dark:text-red-200">
                <div class="flex items-center gap-3">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    {{ session('error') }}
                </div>
            </div>
            @endif

            <!-- Users Table -->
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
                                        User</th>
                                    <th
                                        class="px-8 py-6 text-left text-sm font-bold text-gray-700 dark:text-gray-200 uppercase tracking-wider">
                                        Email</th>
                                    <th
                                        class="px-8 py-6 text-left text-sm font-bold text-gray-700 dark:text-gray-200 uppercase tracking-wider">
                                        Roles</th>
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
                                @forelse($users as $user)
                                <tr
                                    class="hover:bg-gradient-to-r hover:from-blue-50/50 hover:to-purple-50/50 dark:hover:from-blue-900/10 dark:hover:to-purple-900/10 transition-all duration-300 group">
                                    <td class="px-8 py-6 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0 h-12 w-12">
                                                <div
                                                    class="h-12 w-12 rounded-2xl bg-gradient-to-br from-blue-600 to-indigo-700 flex items-center justify-center shadow-lg border-2 border-white/20">
                                                    <span class="text-white font-bold text-lg">{{
                                                        strtoupper(substr($user->name, 0, 1)) }}</span>
                                                </div>
                                            </div>
                                            <div class="ml-5">
                                                <div class="text-sm font-bold text-gray-900 dark:text-white capitalize">
                                                    {{ $user->name }}</div>
                                                <div class="text-xs text-gray-500 dark:text-gray-400">ID: {{ $user->id
                                                    }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td
                                        class="px-8 py-6 whitespace-nowrap text-sm font-semibold text-gray-900 dark:text-white">
                                        {{ $user->email }}</td>
                                    <td class="px-8 py-6 whitespace-nowrap">
                                        <div class="flex flex-wrap gap-2">
                                            @forelse($user->roles->take(3) as $role)
                                            <span
                                                class="inline-flex items-center px-3 py-1.5 rounded-xl text-xs font-medium bg-gradient-to-r from-gray-100 to-gray-200 text-gray-700 dark:from-gray-700 dark:to-gray-600 dark:text-gray-200 border border-gray-200/50 dark:border-gray-600/50">{{
                                                $role->name }}</span>
                                            @empty
                                            <span class="text-xs text-gray-500 dark:text-gray-400">—</span>
                                            @endforelse
                                            @if($user->roles->count() > 3)
                                            <span
                                                class="inline-flex items-center px-3 py-1.5 rounded-xl text-xs font-medium bg-gradient-to-r from-blue-100 to-purple-100 text-blue-700 dark:from-blue-900/30 dark:to-purple-900/30 border border-blue-200/50 dark:border-blue-700/50">+{{
                                                $user->roles->count() - 3 }}</span>
                                            @endif
                                        </div>
                                    </td>
                                    <td
                                        class="px-8 py-6 whitespace-nowrap text-sm text-gray-600 dark:text-gray-300 font-medium">
                                        {{ optional($user->created_at)->format('d M Y') }}</td>
                                    <td class="px-8 py-6 whitespace-nowrap text-right text-sm font-medium">
                                        <div class="flex space-x-3">
                                            <button wire:click="viewDetails({{ $user->id }})"
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
                                            <a href="{{ route('admin.users.edit', $user) }}"
                                                class="group p-3 text-emerald-600 dark:text-emerald-400 hover:text-emerald-900 dark:hover:text-emerald-300 transition-all duration-300 bg-emerald-50 dark:bg-emerald-900/20 rounded-xl hover:bg-emerald-100 dark:hover:bg-emerald-800/30 border border-emerald-200/50 dark:border-emerald-700/50 hover:shadow-lg transform hover:-translate-y-1"
                                                wire:navigate>
                                                <svg class="h-5 w-5" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                </svg>
                                            </a>
                                            <button wire:click="confirmDeleteUser({{ $user->id }})"
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
                                    <td colspan="5" class="px-8 py-16 text-center">
                                        <div
                                            class="mx-auto h-32 w-32 bg-gradient-to-br from-gray-200 to-gray-300 dark:from-gray-600 dark:to-gray-700 rounded-full flex items-center justify-center mb-8 shadow-lg">
                                            <svg class="h-16 w-16 text-gray-400 dark:text-gray-500" fill="none"
                                                stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                            </svg>
                                        </div>
                                        <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-3">Tidak ada user
                                        </h3>
                                        <p class="text-sm text-gray-600 dark:text-gray-400 mb-8">Mulai dengan
                                            menambahkan user baru.</p>
                                        <a href="{{ route('admin.users.create') }}"
                                            class="inline-flex items-center gap-3 px-8 py-4 bg-gradient-to-r from-blue-600 via-purple-600 to-pink-600 hover:from-blue-700 hover:via-purple-700 hover:to-pink-700 text-white font-bold rounded-2xl shadow-2xl hover:shadow-blue-500/25 transform hover:-translate-y-1 transition-all duration-300"
                                            wire:navigate>
                                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M12 4v16m8-8H4" />
                                            </svg>
                                            <span>Tambah User Pertama</span>
                                        </a>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    @if($users->hasPages())
                    <div
                        class="px-8 py-6 bg-gradient-to-r from-gray-50/50 via-blue-50/30 to-purple-50/30 dark:from-gray-700/50 dark:via-blue-900/10 dark:to-purple-900/10">
                        {{ $users->links() }}
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
                                d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.34 16.5c-.77.833.192 2.5 1.732 2.5z" />
                        </svg>
                    </div>
                    <div class="mt-4 text-center sm:ml-6 sm:mt-0 sm:text-left">
                        <h3 class="text-lg font-bold leading-6 text-gray-900 dark:text-white">Hapus User</h3>
                        <div class="mt-3">
                            <p class="text-sm text-gray-600 dark:text-gray-300">
                                @if($userToDelete)
                                Apakah Anda yakin ingin menghapus user <strong
                                    class="text-gray-900 dark:text-white font-bold">{{
                                    $userToDelete->name }}</strong>
                                ({{ $userToDelete->email }})?
                                <br><br>
                                <span class="text-red-600 dark:text-red-400 font-medium">Tindakan ini tidak dapat
                                    dibatalkan.</span>
                                @else
                                Apakah Anda yakin ingin menghapus user ini? Tindakan ini tidak dapat dibatalkan.
                                @endif
                            </p>
                        </div>
                    </div>
                </div>
                <div class="mt-6 sm:mt-5 sm:flex sm:flex-row-reverse gap-4">
                    <button wire:click="deleteUser({{ $userToDelete ? $userToDelete->id : 0 }})" type="button"
                        class="inline-flex w-full justify-center rounded-2xl bg-gradient-to-r from-red-600 to-pink-600 hover:from-red-700 hover:to-pink-700 px-6 py-3 text-sm font-bold text-white shadow-lg hover:shadow-red-500/25 transition-all duration-300 transform hover:-translate-y-1 sm:w-auto">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                        Hapus User
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

    <!-- Detail User Modal -->
    @if($showDetailModal && $selectedUser)
    <div class="fixed inset-0 z-50 overflow-y-auto">
        <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
            <div class="fixed inset-0 backdrop-blur-md transition-opacity" wire:click="closeDetails"></div>
            <div
                class="relative transform overflow-hidden rounded-3xl bg-white/95 dark:bg-gray-800/95 backdrop-blur-xl px-6 pb-6 pt-6 text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-2xl sm:p-8 border border-white/30 dark:border-gray-700/50">
                <div class="sm:flex sm:items-start">
                    <div
                        class="mx-auto flex h-16 w-16 flex-shrink-0 items-center justify-center rounded-2xl bg-gradient-to-br from-blue-100 to-purple-100 dark:from-blue-900/40 dark:to-purple-900/40 sm:mx-0 sm:h-14 sm:w-14 border border-blue-200/50 dark:border-blue-700/50">
                        <div
                            class="h-10 w-10 rounded-2xl bg-gradient-to-br from-blue-600 to-indigo-700 flex items-center justify-center shadow-lg border-2 border-white/20">
                            <span class="text-white font-bold text-base">{{ strtoupper(substr($selectedUser['name'] ??
                                'U', 0, 1)) }}</span>
                        </div>
                    </div>
                    <div class="mt-4 text-center sm:ml-6 sm:mt-0 sm:text-left">
                        <h3 class="text-lg font-bold leading-6 text-gray-900 dark:text-white">Detail User</h3>
                        <div class="mt-3 text-sm text-gray-600 dark:text-gray-300">
                            <div class="font-semibold text-gray-900 dark:text-white">{{ $selectedUser['name'] }}</div>
                            <div class="">{{ $selectedUser['email'] }}</div>
                            <div class="text-xs mt-1">Dibuat: {{ $selectedUser['created_at'] }}</div>
                        </div>
                    </div>
                </div>

                <div class="mt-6 grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <div
                        class="bg-gradient-to-r from-gray-50 to-gray-100 dark:from-gray-700 dark:to-gray-600 rounded-2xl p-4 border border-gray-200/50 dark:border-gray-600/50">
                        <div class="text-xs font-bold uppercase text-gray-500 dark:text-gray-400 mb-2">Roles</div>
                        <div class="flex flex-wrap gap-2">
                            @forelse(($selectedUser['roles'] ?? []) as $r)
                            <span
                                class="inline-flex items-center px-3 py-1.5 rounded-xl text-xs font-medium bg-gradient-to-r from-gray-100 to-gray-200 text-gray-700 dark:from-gray-600 dark:to-gray-500 dark:text-gray-200 border border-gray-200/50 dark:border-gray-500/50">{{
                                $r }}</span>
                            @empty
                            <span class="text-xs text-gray-500 dark:text-gray-400">—</span>
                            @endforelse
                        </div>
                    </div>
                    <div
                        class="bg-gradient-to-r from-blue-50 to-purple-50 dark:from-blue-900/20 dark:to-purple-900/20 rounded-2xl p-4 border border-blue-200/50 dark:border-blue-700/50">
                        <div class="text-xs font-bold uppercase text-blue-700 dark:text-blue-300 mb-2">Permissions</div>
                        <div class="flex flex-wrap gap-2 max-h-48 overflow-auto pr-1">
                            @forelse(($selectedUser['permissions'] ?? []) as $p)
                            <span
                                class="inline-flex items-center px-3 py-1.5 rounded-xl text-xs font-medium bg-white/70 dark:bg-gray-700/70 text-gray-700 dark:text-gray-200 border border-gray-200/50 dark:border-gray-600/50">{{
                                $p }}</span>
                            @empty
                            <span class="text-xs text-gray-500 dark:text-gray-400">—</span>
                            @endforelse
                        </div>
                    </div>
                </div>

                <div class="mt-6 sm:mt-5 sm:flex sm:flex-row-reverse gap-4">
                    <a href="{{ isset($selectedUser['id']) ? route('admin.users.edit', $selectedUser['id']) : '#' }}"
                        class="inline-flex w-full justify-center rounded-2xl bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 px-6 py-3 text-sm font-bold text-white shadow-lg hover:shadow-emerald-500/25 transition-all duration-300 transform hover:-translate-y-1 sm:w-auto"
                        wire:navigate>
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                        </svg>
                        Edit User
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