<?php

use function Livewire\Volt\{
    layout, title, state, mount
};
use App\Models\User;
use Spatie\Permission\Models\Role;

layout('components.layouts.admin');
title('Edit User');

state([
    'user' => null,
    'name' => '',
    'email' => '',
    'password' => '',
    'password_confirmation' => '',
    'selectedRoles' => [],
]);

mount(function(User $user) {
    $this->user = $user;
    $this->name = $user->name;
    $this->email = $user->email;
    $this->selectedRoles = $user->roles->pluck('name')->toArray();
    $this->roles = Role::orderBy('name')->get();
});

$save = function() {
    $this->validate([
        'name' => 'required|string|max:255',
        'email' => 'required|string|email|max:255|unique:users,email,' . $this->user->id,
        'password' => 'nullable|string|min:8|confirmed',
    ]);

    $updateData = [
        'name' => $this->name,
        'email' => $this->email,
    ];

    if (!empty($this->password)) {
        $updateData['password'] = bcrypt($this->password);
    }

    $this->user->update($updateData);
    $this->user->syncRoles($this->selectedRoles);

    session()->flash('message', 'User berhasil diperbarui!');
    return $this->redirect(route('admin.users.index'));
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
                                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z">
                                            </path>
                                        </svg>
                                    </div>
                                    <div>
                                        <h1
                                            class="text-2xl font-bold bg-gradient-to-r from-gray-900 via-emerald-800 to-teal-800 dark:from-white dark:via-emerald-200 dark:to-teal-200 bg-clip-text text-transparent">
                                            Edit User
                                        </h1>
                                        <p class="mt-2 text-sm text-gray-600 dark:text-gray-300 font-medium">
                                            Edit user "{{ $user->name }}" untuk sistem Cave Beach Bungalow
                                        </p>
                                    </div>
                                </div>
                            </div>
                            <a href="{{ route('admin.users.index') }}"
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
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label for="name"
                                        class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-3">
                                        Nama Lengkap <span class="text-red-500">*</span>
                                    </label>
                                    <input wire:model="name" type="text" id="name"
                                        class="block w-full px-4 py-3 border-0 rounded-2xl bg-gray-50/80 dark:bg-gray-700/80 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:ring-2 focus:ring-blue-500 focus:outline-none backdrop-blur-sm text-sm font-medium shadow-lg"
                                        placeholder="Masukkan nama lengkap user">
                                    @error('name')
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
                                    <label for="email"
                                        class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-3">
                                        Email <span class="text-red-500">*</span>
                                    </label>
                                    <input wire:model="email" type="email" id="email"
                                        class="block w-full px-4 py-3 border-0 rounded-2xl bg-gray-50/80 dark:bg-gray-700/80 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:ring-2 focus:ring-blue-500 focus:outline-none backdrop-blur-sm text-sm font-medium shadow-lg"
                                        placeholder="user@example.com">
                                    @error('email')
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

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
                                <div>
                                    <label for="password"
                                        class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-3">
                                        Password Baru <span class="text-gray-500 text-xs">(Opsional)</span>
                                    </label>
                                    <input wire:model="password" type="password" id="password"
                                        class="block w-full px-4 py-3 border-0 rounded-2xl bg-gray-50/80 dark:bg-gray-700/80 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:ring-2 focus:ring-blue-500 focus:outline-none backdrop-blur-sm text-sm font-medium shadow-lg"
                                        placeholder="Kosongkan jika tidak ingin mengubah password">
                                    @error('password')
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
                                    <label for="password_confirmation"
                                        class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-3">
                                        Konfirmasi Password Baru
                                    </label>
                                    <input wire:model="password_confirmation" type="password" id="password_confirmation"
                                        class="block w-full px-4 py-3 border-0 rounded-2xl bg-gray-50/80 dark:bg-gray-700/80 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:ring-2 focus:ring-blue-500 focus:outline-none backdrop-blur-sm text-sm font-medium shadow-lg"
                                        placeholder="Ulangi password baru">
                                </div>
                            </div>
                        </div>

                        <!-- Current User Info -->
                        <div
                            class="bg-gradient-to-r from-blue-50/80 via-indigo-50/50 to-purple-50/50 dark:from-blue-900/20 dark:via-indigo-900/20 dark:to-purple-900/20 border border-blue-200/50 dark:border-blue-800/50 rounded-2xl p-6">
                            <div class="flex items-center mb-4">
                                <div
                                    class="w-8 h-8 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-xl flex items-center justify-center mr-3">
                                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </div>
                                <h4 class="text-sm font-bold text-blue-800 dark:text-blue-300">Informasi User</h4>
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm text-blue-700 dark:text-blue-400">
                                <div
                                    class="bg-white/50 dark:bg-blue-900/30 rounded-xl p-3 border border-blue-200/30 dark:border-blue-700/30">
                                    <p class="font-semibold text-blue-800 dark:text-blue-300 mb-1">Dibuat</p>
                                    <p>{{ $user->created_at->format('d M Y H:i') }}</p>
                                </div>
                                <div
                                    class="bg-white/50 dark:bg-blue-900/30 rounded-xl p-3 border border-blue-200/30 dark:border-blue-700/30">
                                    <p class="font-semibold text-blue-800 dark:text-blue-300 mb-1">Diperbarui</p>
                                    <p>{{ $user->updated_at->format('d M Y H:i') }}</p>
                                </div>
                                <div
                                    class="bg-white/50 dark:bg-blue-900/30 rounded-xl p-3 border border-blue-200/30 dark:border-blue-700/30">
                                    <p class="font-semibold text-blue-800 dark:text-blue-300 mb-1">Roles</p>
                                    <p>{{ $user->roles->count() }} role</p>
                                </div>
                            </div>
                        </div>

                        <!-- Roles -->
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
                                Roles
                            </h3>
                            <p class="text-sm text-gray-600 dark:text-gray-400 mb-6">
                                Pilih role yang akan diberikan kepada user ini
                            </p>

                            <div
                                class="bg-gradient-to-r from-gray-50/80 via-blue-50/50 to-purple-50/50 dark:from-gray-700/80 dark:via-blue-900/20 dark:to-purple-900/20 rounded-2xl p-6 border border-gray-200/50 dark:border-gray-600/50">
                                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                                    @foreach($this->roles as $role)
                                    <label
                                        class="group flex items-center p-4 bg-white/80 dark:bg-gray-800/80 rounded-2xl border border-gray-200/50 dark:border-gray-600/50 hover:bg-gradient-to-r hover:from-blue-50/80 hover:to-purple-50/80 dark:hover:from-blue-900/30 dark:hover:to-purple-900/30 cursor-pointer transition-all duration-300 shadow-lg hover:shadow-xl transform hover:-translate-y-1">
                                        <input wire:model="selectedRoles" type="checkbox" value="{{ $role->name }}"
                                            class="h-5 w-5 text-blue-600 focus:ring-blue-500 border-gray-300 rounded-lg">
                                        <span
                                            class="ml-3 text-sm font-medium text-gray-900 dark:text-white group-hover:text-blue-700 dark:group-hover:text-blue-300 transition-colors">
                                            {{ $role->name }}
                                        </span>
                                    </label>
                                    @endforeach
                                </div>

                                @if($this->roles->count() === 0)
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
                                        role</h3>
                                    <p class="text-sm text-gray-600 dark:text-gray-400">Tidak ada role yang
                                        tersedia saat ini</p>
                                </div>
                                @endif
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div
                            class="flex items-center justify-end gap-4 pt-8 border-t border-gray-200/50 dark:border-gray-700/50">
                            <a href="{{ route('admin.users.index') }}"
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
                                class="group relative inline-flex items-center gap-3 px-8 py-3 bg-gradient-to-r from-emerald-600 via-teal-600 to-blue-600 hover:from-emerald-700 hover:via-teal-700 hover:to-blue-700 text-white font-bold rounded-2xl shadow-2xl hover:shadow-emerald-500/25 transform hover:-translate-y-1 transition-all duration-300 overflow-hidden">
                                <div
                                    class="absolute inset-0 bg-gradient-to-r from-white/20 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                                </div>
                                <svg class="relative z-10 h-5 w-5" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M5 13l4 4L19 7"></path>
                                </svg>
                                <span class="relative z-10">Update User</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>