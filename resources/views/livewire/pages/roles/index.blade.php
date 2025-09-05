<?php

use function Livewire\Volt\{
    layout, title, state, mount, with,
    updated, usesPagination, url
};
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

layout('components.layouts.admin');
title('Role Management');

// aktifkan pagination
usesPagination();

state([
    'search' => '',
    'sortField' => 'created_at',
    'sortDirection' => 'desc',
    'showDeleteModal' => false,
    'roleToDelete' => null,
]);

// sinkronkan ke URL
state(['search'])->url();
state(['page' => 1])->url(as: 'p', history: true, keep: true);

mount(function() {
    // Initialize component
});

// gunakan helper lifecycle Volt
updated([
    'search' => fn () => $this->resetPage(),
]);

$sortBy = function($field) {
    if ($this->sortField === $field) {
        $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
    } else {
        $this->sortField = $field;
        $this->sortDirection = 'asc';
    }
    $this->resetPage();
};

$confirmDelete = function($roleId) {
    try {
        $role = Role::findOrFail($roleId);
        if ($role && $role->name !== 'Admin' && $role->name !== 'Pengunjung') {
            $this->roleToDelete = $role;
            $this->showDeleteModal = true;
        } else {
            session()->flash('error', 'Tidak dapat menghapus role default.');
        }
    } catch (\Exception $e) {
        session()->flash('error', 'Role tidak ditemukan.');
    }
};

$deleteRole = function($roleId) {
    try {
        $role = Role::findOrFail($roleId);
        if ($role && $role->name !== 'Admin' && $role->name !== 'Owner' && $role->name !== 'Pengunjung') {
            $role->delete();
            session()->flash('message', 'Role berhasil dihapus.');
        } else {
            session()->flash('error', 'Tidak dapat menghapus role default.');
        }
    } catch (\Exception $e) {
        session()->flash('error', 'Gagal menghapus role: ' . $e->getMessage());
    }

    $this->resetPage();
    $this->showDeleteModal = false;
    $this->roleToDelete = null;
};

$cancelDelete = fn () => ($this->showDeleteModal = false) && ($this->roleToDelete = null);

with(function() {
    try {
        $query = Role::query();

        if ($this->search) {
            $query->where('name', 'like', '%' . $this->search . '%');
        }

        $roles = $query->orderBy($this->sortField ?: 'created_at', $this->sortDirection ?: 'desc')
            ->paginate(10);

        // Count users for each role
        foreach ($roles as $role) {
            $role->usersCount = $role->users()->count();
        }

        // Global statistics
        $globalStats = [
            'totalRoles' => Role::count(),
            'adminRole' => Role::where('name', 'Admin')->count(),
            'ownerRole' => Role::where('name', 'Owner')->count(),
            'customerRole' => Role::where('name', 'Pengunjung')->count(),
            'customRoles' => Role::whereNotIn('name', ['Admin', 'Owner', 'Pengunjung'])->count(),
        ];

        // Debug logging
        \Log::info('Roles data loaded successfully', [
            'roles_count' => $roles->count(),
            'total_roles' => $globalStats['totalRoles'],
            'search' => $this->search,
            'sort_field' => $this->sortField,
            'sort_direction' => $this->sortDirection
        ]);

        return [
            'roles' => $roles,
            'permissions' => Permission::all(),
            'stats' => $globalStats
        ];
    } catch (\Exception $e) {
        \Log::error('Error loading roles data', [
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);

        return [
            'roles' => collect(),
            'permissions' => collect(),
            'stats' => [
                'totalRoles' => 0,
                'adminRole' => 0,
                'ownerRole' => 0,
                'customerRole' => 0,
                'customRoles' => 0,
            ]
        ];
    }
});

?>

<div
    class="min-h-screen bg-gradient-to-br from-slate-50 via-blue-50 to-indigo-100 dark:from-gray-900 dark:via-gray-800 dark:to-slate-900">
    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Header dengan design yang lebih unik -->
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
                                                d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z">
                                            </path>
                                        </svg>
                                    </div>
                                    <div>
                                        <h1
                                            class="text-2xl font-bold bg-gradient-to-r from-gray-900 via-blue-800 to-purple-800 dark:from-white dark:via-blue-200 dark:to-purple-200 bg-clip-text text-transparent">
                                            Kelola Role
                                        </h1>
                                        <p class="mt-2 text-sm text-gray-600 dark:text-gray-300 font-medium">
                                            Sistem manajemen role & permission yang canggih
                                        </p>
                                    </div>
                                </div>
                            </div>
                            <a href="{{ route('admin.roles.create') }}"
                                class="group relative inline-flex items-center gap-3 px-8 py-4 bg-gradient-to-r from-blue-600 via-purple-600 to-pink-600 hover:from-blue-700 hover:via-purple-700 hover:to-pink-700 text-white font-bold rounded-2xl shadow-2xl hover:shadow-blue-500/25 transform hover:-translate-y-1 transition-all duration-300 overflow-hidden"
                                wire:navigate>
                                <div
                                    class="absolute inset-0 bg-gradient-to-r from-white/20 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                                </div>
                                <svg class="relative z-10 h-6 w-6" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 4v16m8-8H4"></path>
                                </svg>
                                <span class="relative z-10">Buat Role Baru</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Statistics Cards dengan design yang lebih menarik -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-10">
                <!-- Total Roles -->
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
                                <svg class="h-7 w-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z">
                                    </path>
                                </svg>
                            </div>
                        </div>
                        <h3 class="text-xl font-bold mb-2">{{ $stats['totalRoles'] }}</h3>
                        <p class="text-blue-100 text-xs font-medium">Total Roles</p>
                    </div>
                </div>

                <!-- Admin Role - dengan warna yang lebih kontras -->
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
                                <svg class="h-7 w-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z">
                                    </path>
                                </svg>
                            </div>
                        </div>
                        <h3 class="text-xl font-bold mb-2">{{ $stats['adminRole'] }}</h3>
                        <p class="text-purple-100 text-xs font-medium">Admin</p>
                    </div>
                </div>

                <!-- Customer Role -->
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
                                <svg class="h-7 w-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                </svg>
                            </div>
                        </div>
                        <h3 class="text-xl font-bold mb-2">{{ $stats['customerRole'] }}</h3>
                        <p class="text-emerald-100 text-xs font-medium">Customer</p>
                    </div>
                </div>

                <!-- Owner Role -->
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
                                <svg class="h-7 w-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4">
                                    </path>
                                </svg>
                            </div>
                        </div>
                        <h3 class="text-xl font-bold mb-2">{{ $stats['ownerRole'] }}</h3>
                        <p class="text-amber-100 text-xs font-medium">Owner</p>
                    </div>
                </div>
            </div>

            <!-- Search & Sort Section dengan design yang lebih modern -->
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
                                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                        </svg>
                                    </div>
                                    <input wire:model.live="search" type="text"
                                        placeholder="Cari role berdasarkan nama..."
                                        class="block w-full pl-14 pr-4 py-3 border-0 rounded-2xl bg-gray-50/80 dark:bg-gray-700/80 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:ring-2 focus:ring-blue-500 focus:outline-none backdrop-blur-sm text-sm font-medium">
                                </div>
                            </div>
                            <div class="flex gap-3">
                                <button wire:click="sortBy('name')"
                                    class="group px-6 py-4 border border-gray-300/50 dark:border-gray-600/50 rounded-2xl focus:outline-none focus:ring-2 focus:ring-blue-500 bg-gradient-to-r from-blue-50 to-purple-50 dark:from-blue-900/20 dark:to-purple-900/20 text-blue-600 dark:text-blue-400 hover:from-blue-100 hover:to-purple-100 dark:hover:from-blue-800/30 dark:hover:to-purple-800/30 transition-all duration-300 backdrop-blur-sm font-medium shadow-lg hover:shadow-xl transform hover:-translate-y-1">
                                    <span class="flex items-center gap-2">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4"></path>
                                        </svg>
                                        Name
                                    </span>
                                    @if($sortField === 'name')
                                    <span class="ml-2 text-sm">
                                        @if($sortDirection === 'asc') ↑ @else ↓ @endif
                                    </span>
                                    @endif
                                </button>
                                <button wire:click="sortBy('created_at')"
                                    class="group px-6 py-4 border border-gray-300/50 dark:border-gray-600/50 rounded-2xl focus:outline-none focus:ring-2 focus:ring-blue-500 bg-gradient-to-r from-emerald-50 to-teal-50 dark:from-emerald-900/20 dark:to-teal-900/20 text-emerald-600 dark:text-emerald-400 hover:from-emerald-100 hover:to-teal-100 dark:hover:from-emerald-800/30 dark:hover:to-teal-800/30 transition-all duration-300 backdrop-blur-sm font-medium shadow-lg hover:shadow-xl transform hover:-translate-y-1">
                                    <span class="flex items-center gap-2">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z">
                                            </path>
                                        </svg>
                                        Created
                                    </span>
                                    @if($sortField === 'created_at')
                                    <span class="ml-2 text-sm">
                                        @if($sortDirection === 'asc') ↑ @else ↓ @endif
                                    </span>
                                    @endif
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Success Message -->
            @if (session()->has('message'))
            <div
                class="mb-6 bg-gradient-to-r from-green-50 to-emerald-50 border border-green-200 text-green-800 px-6 py-4 rounded-2xl dark:bg-green-900/20 dark:border-green-800 dark:text-green-400 shadow-lg">
                <div class="flex items-center gap-3">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    {{ session('message') }}
                </div>
            </div>
            @endif

            <!-- Error Message -->
            @if (session()->has('error'))
            <div
                class="mb-6 bg-gradient-to-r from-red-50 to-pink-50 border border-red-200 text-red-800 px-6 py-4 rounded-2xl dark:bg-red-900/20 dark:border-red-800 dark:text-red-400 shadow-lg">
                <div class="flex items-center gap-3">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    {{ session('error') }}
                </div>
            </div>
            @endif

            <!-- Roles Table dengan design yang lebih modern -->
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
                                        Role</th>
                                    <th
                                        class="px-8 py-6 text-left text-sm font-bold text-gray-700 dark:text-gray-200 uppercase tracking-wider">
                                        Users</th>
                                    <th
                                        class="px-8 py-6 text-left text-sm font-bold text-gray-700 dark:text-gray-200 uppercase tracking-wider">
                                        Permissions</th>
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
                                @forelse($roles as $role)
                                <tr
                                    class="hover:bg-gradient-to-r hover:from-blue-50/50 hover:to-purple-50/50 dark:hover:from-blue-900/10 dark:hover:to-purple-900/10 transition-all duration-300 group">
                                    <td class="px-8 py-6 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0 h-12 w-12">
                                                @if($role->name === 'Admin')
                                                <div
                                                    class="h-12 w-12 rounded-2xl bg-gradient-to-br from-indigo-600 to-purple-700 flex items-center justify-center shadow-lg border-2 border-white/20">
                                                    <span class="text-white font-bold text-lg">{{
                                                        strtoupper(substr($role->name, 0, 1)) }}</span>
                                                </div>
                                                @elseif($role->name === 'Owner')
                                                <div
                                                    class="h-12 w-12 rounded-2xl bg-gradient-to-br from-amber-600 to-orange-700 flex items-center justify-center shadow-lg border-2 border-white/20">
                                                    <span class="text-white font-bold text-lg">{{
                                                        strtoupper(substr($role->name, 0, 1)) }}</span>
                                                </div>
                                                @elseif($role->name === 'Pengunjung')
                                                <div
                                                    class="h-12 w-12 rounded-2xl bg-gradient-to-br from-emerald-600 to-teal-700 flex items-center justify-center shadow-lg border-2 border-white/20">
                                                    <span class="text-white font-bold text-lg">{{
                                                        strtoupper(substr($role->name, 0, 1)) }}</span>
                                                </div>
                                                @else
                                                <div
                                                    class="h-12 w-12 rounded-2xl bg-gradient-to-br from-blue-600 to-indigo-700 flex items-center justify-center shadow-lg border-2 border-white/20">
                                                    <span class="text-white font-bold text-lg">{{
                                                        strtoupper(substr($role->name, 0, 1)) }}</span>
                                                </div>
                                                @endif
                                            </div>
                                            <div class="ml-5">
                                                <div class="text-sm font-bold text-gray-900 dark:text-white capitalize">
                                                    {{ $role->name }}
                                                </div>
                                                @if(in_array($role->name, ['Admin', 'Owner', 'Pengunjung']))
                                                <span
                                                    class="mt-2 inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-gradient-to-r from-amber-100 to-orange-100 text-amber-800 dark:from-amber-900/40 dark:to-orange-900/40 dark:text-amber-200 border border-amber-200/50">
                                                    <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd"
                                                            d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                                            clip-rule="evenodd"></path>
                                                    </svg>
                                                    System Role
                                                </span>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-8 py-6 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div
                                                class="w-8 h-8 bg-gradient-to-br from-blue-100 to-purple-100 dark:from-blue-900/30 dark:to-purple-900/30 rounded-xl flex items-center justify-center border border-blue-200/50 dark:border-blue-700/50">
                                                <span class="text-blue-700 dark:text-blue-300 font-bold text-sm">{{
                                                    $role->usersCount }}</span>
                                            </div>
                                            <span class="ml-3 text-sm font-semibold text-gray-900 dark:text-white">{{
                                                $role->usersCount }}</span>
                                        </div>
                                    </td>
                                    <td class="px-8 py-6 whitespace-nowrap">
                                        <div class="flex flex-wrap gap-2">
                                            @foreach($role->permissions->take(3) as $permission)
                                            <span
                                                class="inline-flex items-center px-3 py-1.5 rounded-xl text-xs font-medium bg-gradient-to-r from-gray-100 to-gray-200 text-gray-700 dark:from-gray-700 dark:to-gray-600 dark:text-gray-200 border border-gray-200/50 dark:border-gray-600/50">
                                                {{ $permission->name }}
                                            </span>
                                            @endforeach
                                            @if($role->permissions->count() > 3)
                                            <span
                                                class="inline-flex items-center px-3 py-1.5 rounded-xl text-xs font-medium bg-gradient-to-r from-blue-100 to-purple-100 text-blue-700 dark:from-blue-900/30 dark:to-purple-900/30 border border-blue-200/50 dark:border-blue-700/50">
                                                +{{ $role->permissions->count() - 3 }}
                                            </span>
                                            @endif
                                        </div>
                                    </td>
                                    <td
                                        class="px-8 py-6 whitespace-nowrap text-sm text-gray-600 dark:text-gray-300 font-medium">
                                        {{ $role->created_at->format('d M Y') }}
                                    </td>
                                    <td class="px-8 py-6 whitespace-nowrap text-right text-sm font-medium">
                                        <div class="flex space-x-3">
                                            <a href="{{ route('admin.roles.edit', $role) }}"
                                                class="group p-3 text-emerald-600 dark:text-emerald-400 hover:text-emerald-900 dark:hover:text-emerald-300 transition-all duration-300 bg-emerald-50 dark:bg-emerald-900/20 rounded-xl hover:bg-emerald-100 dark:hover:bg-emerald-800/30 border border-emerald-200/50 dark:border-emerald-700/50 hover:shadow-lg transform hover:-translate-y-1">
                                                <svg class="h-5 w-5" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z">
                                                    </path>
                                                </svg>
                                            </a>
                                            @if(!in_array($role->name, ['Admin', 'Owner', 'Pengunjung']))
                                            <button wire:click="confirmDelete({{ $role->id }})"
                                                class="group p-3 text-red-600 dark:text-red-400 hover:text-red-900 dark:hover:text-red-300 transition-all duration-300 bg-red-50 dark:bg-red-900/20 rounded-xl hover:bg-red-100 dark:hover:bg-red-800/30 border border-red-200/50 dark:border-red-700/50 hover:shadow-lg transform hover:-translate-y-1">
                                                <svg class="h-5 w-5" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                                                    </path>
                                                </svg>
                                            </button>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="px-8 py-16 text-center">
                                        <div
                                            class="mx-auto h-32 w-32 bg-gradient-to-br from-gray-200 to-gray-300 dark:from-gray-600 dark:to-gray-700 rounded-full flex items-center justify-center mb-8 shadow-lg">
                                            <svg class="h-16 w-16 text-gray-400" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z">
                                                </path>
                                            </svg>
                                        </div>
                                        <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-3">Tidak ada role
                                        </h3>
                                        <p class="text-sm text-gray-600 dark:text-gray-400 mb-8">Mulai dengan
                                            menambahkan role baru untuk sistem Anda.</p>
                                        <a href="{{ route('admin.roles.create') }}"
                                            class="inline-flex items-center gap-3 px-8 py-4 bg-gradient-to-r from-blue-600 via-purple-600 to-pink-600 hover:from-blue-700 hover:via-purple-700 hover:to-pink-700 text-white font-bold rounded-2xl shadow-2xl hover:shadow-blue-500/25 transform hover:-translate-y-1 transition-all duration-300"
                                            wire:navigate>
                                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M12 4v16m8-8H4"></path>
                                            </svg>
                                            <span>Tambah Role Pertama</span>
                                        </a>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    @if($roles->hasPages())
                    <div
                        class="px-8 py-6 bg-gradient-to-r from-gray-50/50 via-blue-50/30 to-purple-50/30 dark:from-gray-700/50 dark:via-blue-900/10 dark:to-purple-900/10">
                        {{ $roles->links() }}
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Delete Confirmation Modal dengan design yang lebih modern -->
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
                                d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.34 16.5c-.77.833.192 2.5 1.732 2.5z">
                            </path>
                        </svg>
                    </div>
                    <div class="mt-4 text-center sm:ml-6 sm:mt-0 sm:text-left">
                        <h3 class="text-lg font-bold leading-6 text-gray-900 dark:text-white">Hapus Role</h3>
                        <div class="mt-3">
                            <p class="text-sm text-gray-600 dark:text-gray-300">
                                @if($roleToDelete)
                                Apakah Anda yakin ingin menghapus role <strong
                                    class="text-gray-900 dark:text-white font-bold">{{ $roleToDelete->name }}</strong>?
                                <br><br>
                                <span class="text-red-600 dark:text-red-400 font-medium">Tindakan ini tidak dapat
                                    dibatalkan.</span>
                                @else
                                Apakah Anda yakin ingin menghapus role ini? Tindakan ini tidak dapat dibatalkan.
                                @endif
                            </p>
                        </div>
                    </div>
                </div>
                <div class="mt-6 sm:mt-5 sm:flex sm:flex-row-reverse gap-4">
                    <button wire:click="deleteRole({{ $roleToDelete ? $roleToDelete->id : 0 }})" type="button"
                        class="inline-flex w-full justify-center rounded-2xl bg-gradient-to-r from-red-600 to-pink-600 hover:from-red-700 hover:to-pink-700 px-6 py-3 text-sm font-bold text-white shadow-lg hover:shadow-red-500/25 transition-all duration-300 transform hover:-translate-y-1 sm:w-auto">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                            </path>
                        </svg>
                        Hapus Role
                    </button>
                    <button wire:click="cancelDelete" type="button"
                        class="mt-4 inline-flex w-full justify-center rounded-2xl bg-gradient-to-r from-gray-100 to-gray-200 dark:from-gray-700 dark:to-gray-600 px-6 py-3 text-sm font-bold text-gray-700 dark:text-gray-200 shadow-lg hover:shadow-gray-500/25 transition-all duration-300 transform hover:-translate-y-1 sm:mt-0 sm:w-auto">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                        Batal
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>