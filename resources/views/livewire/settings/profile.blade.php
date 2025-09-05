<?php

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\Rule;
use function Livewire\Volt\{layout, title, state, mount, rules};

layout('components.layouts.admin');
title('Profile Settings');

state(['name' => '', 'email' => '']);

mount(function () {
    $this->name = Auth::user()->name;
    $this->email = Auth::user()->email;
});

rules([
    'name' => ['required', 'string', 'max:255'],
    'email' => [
        'required',
        'string',
        'lowercase',
        'email',
        'max:255',
        Rule::unique(User::class)->ignore(Auth::user()->id)
    ],
]);

$updateProfileInformation = function () {
    $user = Auth::user();
    $validated = $this->validate();

    $user->fill($validated);

    if ($user->isDirty('email')) {
        $user->email_verified_at = null;
    }

    $user->save();

    $this->dispatch('profile-updated', name: $user->name);
};

$resendVerificationNotification = function () {
    $user = Auth::user();

    if ($user->hasVerifiedEmail()) {
        $this->redirectIntended(default: route('dashboard', absolute: false));
        return;
    }

    $user->sendEmailVerificationNotification();
    Session::flash('status', 'verification-link-sent');
};
?>

<div
    class="min-h-screen bg-gradient-to-br from-blue-50 via-white to-blue-50 dark:from-gray-900 dark:via-gray-800 dark:to-blue-900/20 p-6">
    <div class="max-w-4xl mx-auto">
        <!-- Breadcrumb Navigation -->
        <div class="mb-8">
            <nav class="flex" aria-label="Breadcrumb">
                <ol class="inline-flex items-center space-x-2">
                    <li class="inline-flex items-center">
                        <a href="{{ route('admin.settings.index') }}"
                            class="inline-flex items-center text-sm font-medium text-gray-600 hover:text-blue-600 dark:text-gray-400 dark:hover:text-blue-400 transition-colors"
                            wire:navigate>
                            <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path
                                    d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z">
                                </path>
                            </svg>
                            {{ __('Pengaturan') }}
                        </a>
                    </li>
                    <li>
                        <div class="flex items-center">
                            <svg class="w-5 h-5 text-gray-400 mx-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z"
                                    clip-rule="evenodd"></path>
                            </svg>
                            <span class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Profil') }}</span>
                        </div>
                    </li>
                </ol>
            </nav>
        </div>

        <!-- Header -->
        <div class="text-center mb-12">
            <div
                class="inline-flex items-center justify-center w-20 h-20 bg-blue-100 dark:bg-blue-900/30 rounded-2xl mb-6">
                <svg class="w-10 h-10 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor"
                    viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                </svg>
            </div>
            <h1 class="text-4xl font-bold text-gray-900 dark:text-white mb-3">{{ __('Pengaturan Profil') }}</h1>
            <p class="text-lg text-gray-600 dark:text-gray-400 max-w-2xl mx-auto">{{ __('Update nama dan alamat email
                Anda') }}</p>
        </div>

        <!-- Profile Form -->
        <div
            class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-8 mb-8">
            <form wire:submit="updateProfileInformation" class="space-y-8">
                <!-- Name Field -->
                <div class="space-y-3">
                    <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Nama')
                        }}</label>
                    <input wire:model="name" id="name" type="text" required autofocus autocomplete="name"
                        class="w-full px-4 py-3 border border-gray-200 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-white transition-all duration-200"
                        placeholder="{{ __('Masukkan nama lengkap Anda') }}">
                    @error('name') <span class="text-sm text-red-600 dark:text-red-400">{{ $message }}</span> @enderror
                </div>

                <!-- Email Field -->
                <div class="space-y-3">
                    <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Email')
                        }}</label>
                    <input wire:model="email" id="email" type="email" required autocomplete="email"
                        class="w-full px-4 py-3 border border-gray-200 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-white transition-all duration-200"
                        placeholder="{{ __('Masukkan alamat email Anda') }}">
                    @error('email') <span class="text-sm text-red-600 dark:text-red-400">{{ $message }}</span> @enderror

                    @if (auth()->user() instanceof \Illuminate\Contracts\Auth\MustVerifyEmail &&
                    !auth()->user()->hasVerifiedEmail())
                    <div
                        class="mt-4 p-4 bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-xl">
                        <div class="flex items-start space-x-3">
                            <svg class="w-5 h-5 text-yellow-600 dark:text-yellow-400 mt-0.5 flex-shrink-0" fill="none"
                                stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.34 16.5c-.77.833.192 2.5 1.732 2.5z">
                                </path>
                            </svg>
                            <div class="flex-1">
                                <p class="text-sm text-yellow-800 dark:text-yellow-200 font-medium">{{ __('Email Anda
                                    belum diverifikasi.') }}</p>
                                <button type="button" wire:click.prevent="resendVerificationNotification"
                                    class="text-sm text-blue-600 dark:text-blue-400 hover:text-blue-700 dark:hover:text-blue-300 underline mt-1">
                                    {{ __('Klik di sini untuk mengirim ulang email verifikasi.') }}
                                </button>
                            </div>
                        </div>

                        @if (session('status') === 'verification-link-sent')
                        <div
                            class="mt-3 p-3 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg">
                            <p class="text-sm text-green-800 dark:text-green-200 font-medium">
                                {{ __('Link verifikasi baru telah dikirim ke alamat email Anda.') }}
                            </p>
                        </div>
                        @endif
                    </div>
                    @endif
                </div>

                <!-- Action Buttons -->
                <div
                    class="flex flex-col sm:flex-row items-center gap-4 pt-6 border-t border-gray-100 dark:border-gray-700">
                    <button type="submit"
                        class="w-full sm:w-auto px-8 py-3 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-xl transition-all duration-200 hover:shadow-lg flex items-center justify-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7">
                            </path>
                        </svg>
                        {{ __('Simpan Perubahan') }}
                    </button>

                    <a href="{{ route('admin.settings.index') }}"
                        class="w-full sm:w-auto px-8 py-3 bg-gray-100 hover:bg-gray-200 text-gray-700 dark:bg-gray-700 dark:hover:bg-gray-600 dark:text-gray-300 font-medium rounded-xl transition-all duration-200 flex items-center justify-center"
                        wire:navigate>
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                        </svg>
                        {{ __('Kembali ke Pengaturan') }}
                    </a>

                    <div class="w-full sm:w-auto">
                        <x-action-message class="text-center sm:text-left" on="profile-updated">
                            <div
                                class="inline-flex items-center px-4 py-2 bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-200 rounded-lg">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M5 13l4 4L19 7"></path>
                                </svg>
                                {{ __('Tersimpan.') }}
                            </div>
                        </x-action-message>
                    </div>
                </div>
            </form>
        </div>

        <!-- Delete Account Section -->
        <div
            class="bg-gradient-to-r from-red-50 to-red-100 dark:from-red-900/20 dark:to-red-800/20 border border-red-200 dark:border-red-800 rounded-2xl p-8">
            <livewire:settings.delete-user-form />
        </div>
    </div>
</div>