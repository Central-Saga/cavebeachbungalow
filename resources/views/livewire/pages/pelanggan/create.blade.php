<?php

use function Livewire\Volt\{
    layout, title, state, mount, with,
    updated
};
use App\Models\Pelanggan;
use App\Models\User;

layout('components.layouts.admin');
title('Create Pelanggan');

state([
    'alamat' => '',
    'kota' => '',
    'jenis_kelamin' => 'L',
    'tanggal_lahir' => '',
    'telepon' => '',
    'user_id' => null,
]);

mount(function () {
    // Init state kalau perlu
});

$save = function () {
    $this->validate([
        'alamat' => 'required|string|max:500',
        'kota' => 'required|string|max:100',
        'jenis_kelamin' => 'required|in:L,P',
        'tanggal_lahir' => 'required|date',
        'telepon' => 'required|string|max:20',
        'user_id' => 'nullable|exists:users,id',
    ]);

    try {
        Pelanggan::create([
            'alamat' => $this->alamat,
            'kota' => $this->kota,
            'jenis_kelamin' => $this->jenis_kelamin,
            'tanggal_lahir' => $this->tanggal_lahir,
            'telepon' => $this->telepon,
            'user_id' => $this->user_id,
        ]);

        session()->flash('message', 'Pelanggan berhasil dibuat!');
        return $this->redirect(route('admin.pelanggan.index'), navigate: true);
    } catch (\Exception $e) {
        session()->flash('error', 'Gagal membuat pelanggan: ' . $e->getMessage());
    }
};

with(function () {
    $users = User::orderBy('name')->get(['id', 'name', 'email']);
    return compact('users');
});

?>

<div
    class="min-h-screen bg-gradient-to-br from-slate-50 via-blue-50 to-indigo-100 dark:from-gray-900 dark:via-gray-800 dark:to-slate-900">
    <div class="py-8">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Header -->
            <div class="mb-10">
                <div class="relative">
                    <div
                        class="absolute inset-0 bg-gradient-to-r from-blue-600/10 via-purple-600/10 to-pink-600/10 rounded-3xl blur-3xl">
                    </div>
                    <div
                        class="relative bg-white/70 dark:bg-gray-800/70 backdrop-blur-xl rounded-3xl p-8 border border-white/30 dark:border-gray-700/50 shadow-2xl">
                        <div class="flex items-center gap-4 mb-3">
                            <div
                                class="w-16 h-16 bg-gradient-to-br from-blue-600 to-purple-700 rounded-2xl flex items-center justify-center shadow-lg">
                                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 4v16m8-8H4" />
                                </svg>
                            </div>
                            <div>
                                <h1
                                    class="text-2xl font-bold bg-gradient-to-r from-gray-900 via-blue-800 to-purple-800 dark:from-white dark:via-blue-200 dark:to-purple-200 bg-clip-text text-transparent">
                                    Buat Pelanggan Baru
                                </h1>
                                <p class="mt-2 text-sm text-gray-600 dark:text-gray-300 font-medium">
                                    Tambahkan data pelanggan baru ke sistem
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Form -->
            <div class="relative">
                <div
                    class="absolute inset-0 bg-gradient-to-r from-blue-600/5 via-purple-600/5 to-pink-600/5 rounded-3xl blur-3xl">
                </div>
                <div
                    class="relative bg-white/90 dark:bg-gray-800/90 shadow-2xl rounded-3xl overflow-hidden border border-white/30 dark:border-gray-700/50 backdrop-blur-xl">
                    <form wire:submit="save" class="p-8">
                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                            <!-- Left Column -->
                            <div class="space-y-6">
                                <!-- Telepon -->
                                <div>
                                    <label for="telepon"
                                        class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                        Telepon <span class="text-red-500">*</span>
                                    </label>
                                    <input wire:model="telepon" type="text" id="telepon" required
                                        class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-2xl bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 placeholder-gray-500 focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-300">
                                    @error('telepon') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                </div>

                                <!-- Jenis Kelamin -->
                                <div>
                                    <label for="jenis_kelamin"
                                        class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                        Jenis Kelamin <span class="text-red-500">*</span>
                                    </label>
                                    <select wire:model="jenis_kelamin" id="jenis_kelamin" required
                                        class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-2xl bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-300">
                                        <option value="L">Laki-laki</option>
                                        <option value="P">Perempuan</option>
                                    </select>
                                    @error('jenis_kelamin') <span class="text-red-500 text-sm">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            <!-- Right Column -->
                            <div class="space-y-6">
                                <!-- Kota -->
                                <div>
                                    <label for="kota"
                                        class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                        Kota <span class="text-red-500">*</span>
                                    </label>
                                    <input wire:model="kota" type="text" id="kota" required
                                        class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-2xl bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 placeholder-gray-500 focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-300">
                                    @error('kota') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                </div>

                                <!-- Tanggal Lahir -->
                                <div>
                                    <label for="tanggal_lahir"
                                        class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                        Tanggal Lahir <span class="text-red-500">*</span>
                                    </label>
                                    <input wire:model="tanggal_lahir" type="date" id="tanggal_lahir" required
                                        class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-2xl bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-300">
                                    @error('tanggal_lahir') <span class="text-red-500 text-sm">{{ $message }}</span>
                                    @enderror
                                </div>

                                <!-- User ID (Optional) -->
                                <div>
                                    <label for="user_id"
                                        class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                        User Terkait (Opsional)
                                    </label>
                                    <select wire:model="user_id" id="user_id"
                                        class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-2xl bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-300">
                                        <option value="">Pilih User (Opsional)</option>
                                        @foreach($users as $user)
                                        <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->email }})</option>
                                        @endforeach
                                    </select>
                                    @error('user_id') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                </div>

                                <!-- Alamat -->
                                <div>
                                    <label for="alamat"
                                        class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                        Alamat <span class="text-red-500">*</span>
                                    </label>
                                    <textarea wire:model="alamat" id="alamat" rows="3" required
                                        class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-2xl bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 placeholder-gray-500 focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-300 resize-none"></textarea>
                                    @error('alamat') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="flex justify-end gap-4 mt-8 pt-6 border-t border-gray-200 dark:border-gray-700">
                            <a href="{{ route('admin.pelanggan.index') }}"
                                class="px-6 py-3 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-2xl font-medium hover:bg-gray-200 dark:hover:bg-gray-600 transition-all duration-300 transform hover:-translate-y-1"
                                wire:navigate>
                                Batal
                            </a>
                            <button type="submit"
                                class="px-8 py-3 bg-gradient-to-r from-blue-600 via-purple-600 to-pink-600 hover:from-blue-700 hover:via-purple-700 hover:to-pink-700 text-white font-bold rounded-2xl shadow-lg hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1">
                                Buat Pelanggan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>