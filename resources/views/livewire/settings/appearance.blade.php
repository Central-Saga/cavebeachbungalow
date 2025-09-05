<?php

use function Livewire\Volt\{layout, title, state, mount};

layout('components.layouts.admin');
title('Appearance Settings');

state(['currentTheme' => 'system']);

mount(function () {
    // Get current theme from localStorage or default to system
    $this->currentTheme = 'system'; // Will be updated by JavaScript
});
?>

<div class="min-h-screen bg-gradient-to-br from-blue-50 via-white to-blue-50 dark:from-gray-900 dark:via-gray-800 dark:to-blue-900/20 p-6"
    x-data="{
    theme: localStorage.getItem('theme') || 'system',

    updateTheme(newTheme) {
        console.log('Appearance: Updating theme to:', newTheme);

        // Update local state
        this.theme = newTheme;

        // Store in localStorage
        localStorage.setItem('theme', newTheme);

        // Apply theme immediately
        this.applyTheme(newTheme);

        // Update Livewire state
        $wire.set('currentTheme', newTheme);

        // Dispatch event for other components
        window.dispatchEvent(new CustomEvent('theme-changed', {
            detail: { theme: newTheme, preference: newTheme }
        }));

        // Update theme manager if available
        if (window.themeManager) {
            window.themeManager.theme = newTheme;
            window.themeManager.applyTheme(newTheme);
        }
    },

    applyTheme(theme) {
        console.log('Appearance: Applying theme:', theme);

        // Remove existing classes
        document.documentElement.classList.remove('light', 'dark');

        if (theme === 'system') {
            // For system theme, check browser preference
            const browserPrefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
            const actualTheme = browserPrefersDark ? 'dark' : 'light';
            document.documentElement.classList.add(actualTheme);
            console.log('Appearance: System theme applied:', actualTheme);
        } else {
            // Force the explicit theme regardless of browser preference
            document.documentElement.classList.add(theme);
            console.log('Appearance: Explicit theme applied:', theme);
        }

        // Set data attribute for additional styling
        document.documentElement.setAttribute('data-theme', theme);
    }
}" x-init="
    console.log('Appearance: Initializing component');

    // Get saved theme
    const savedTheme = localStorage.getItem('theme');
    console.log('Appearance: Saved theme from localStorage:', savedTheme);

    if (savedTheme) {
        theme = savedTheme;
        applyTheme(savedTheme);
    } else {
        // Default to system
        theme = 'system';
        applyTheme('system');
    }

    // Listen for storage changes (from other tabs)
    window.addEventListener('storage', (e) => {
        if (e.key === 'theme') {
            console.log('Appearance: Storage event - theme changed to:', e.newValue);
            const newTheme = e.newValue || 'system';
            theme = newTheme;
            applyTheme(newTheme);
        }
    });

    // Listen for theme changes from other components
    window.addEventListener('theme-changed', (e) => {
        console.log('Appearance: Theme changed event received:', e.detail);
        const newTheme = e.detail.theme;
        if (newTheme && newTheme !== theme) {
            theme = newTheme;
            applyTheme(newTheme);
        }
    });

    // Override system theme listener to respect user choice
    const mediaQuery = window.matchMedia('(prefers-color-scheme: dark)');
    mediaQuery.addEventListener('change', (e) => {
        const savedTheme = localStorage.getItem('theme');
        console.log('Appearance: System preference changed, saved theme:', savedTheme);

        // Only change if user hasn't explicitly chosen a theme
        if (!savedTheme || savedTheme === 'system') {
            const newTheme = e.matches ? 'dark' : 'light';
            console.log('Appearance: Applying system preference change:', newTheme);
            theme = 'system';
            applyTheme('system');
        } else {
            console.log('Appearance: User has explicit theme choice, ignoring system change');
        }
    });
">
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
                            <span class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Tampilan')
                                }}</span>
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
                        d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01">
                    </path>
                </svg>
            </div>
            <h1 class="text-4xl font-bold text-gray-900 dark:text-white mb-3">{{ __('Pengaturan Tampilan') }}</h1>
            <p class="text-lg text-gray-600 dark:text-gray-400 max-w-2xl mx-auto">{{ __('Kustomisasi tema tampilan untuk
                akun Anda') }}</p>
        </div>

        <!-- Theme Selection -->
        <div
            class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-8 mb-8">
            <div class="mb-8">
                <h3 class="text-2xl font-semibold text-gray-900 dark:text-white mb-3">{{ __('Pilihan Tema') }}</h3>
                <p class="text-gray-600 dark:text-gray-400">{{ __('Pilih tema tampilan yang Anda sukai') }}</p>
            </div>

            <!-- Theme Selection Cards -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                <!-- Light Theme -->
                <div class="group cursor-pointer" @click="updateTheme('light')">
                    <div class="relative">
                        <div
                            class="absolute inset-0 bg-gradient-to-r from-blue-500 to-blue-600 rounded-2xl blur opacity-0 group-hover:opacity-20 transition-opacity duration-300">
                        </div>
                        <div class="relative bg-white dark:bg-gray-800 rounded-2xl border-2 transition-all duration-300 group-hover:scale-105"
                            :class="theme === 'light' ? 'border-blue-500 shadow-lg shadow-blue-500/25' : 'border-gray-200 dark:border-gray-600 hover:border-blue-300 dark:hover:border-blue-600'">
                            <div class="p-6 text-center">
                                <div
                                    class="w-16 h-16 mx-auto mb-4 rounded-xl bg-yellow-100 dark:bg-yellow-900/30 flex items-center justify-center">
                                    <svg class="w-8 h-8 text-yellow-600 dark:text-yellow-400" fill="none"
                                        stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z">
                                        </path>
                                    </svg>
                                </div>
                                <h4 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">{{ __('Terang') }}
                                </h4>
                                <p class="text-sm text-gray-600 dark:text-gray-400">{{ __('Tema terang untuk pengalaman
                                    yang cerah') }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Dark Theme -->
                <div class="group cursor-pointer" @click="updateTheme('dark')">
                    <div class="relative">
                        <div
                            class="absolute inset-0 bg-gradient-to-r from-blue-500 to-blue-600 rounded-2xl blur opacity-0 group-hover:opacity-20 transition-opacity duration-300">
                        </div>
                        <div class="relative bg-white dark:bg-gray-800 rounded-2xl border-2 transition-all duration-300 group-hover:scale-105"
                            :class="theme === 'dark' ? 'border-blue-500 shadow-lg shadow-blue-500/25' : 'border-gray-200 dark:border-gray-600 hover:border-blue-300 dark:hover:border-blue-600'">
                            <div class="p-6 text-center">
                                <div
                                    class="w-16 h-16 mx-auto mb-4 rounded-xl bg-gray-100 dark:bg-gray-700 flex items-center justify-center">
                                    <svg class="w-8 h-8 text-gray-600 dark:text-gray-300" fill="none"
                                        stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z">
                                        </path>
                                    </svg>
                                </div>
                                <h4 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">{{ __('Gelap') }}
                                </h4>
                                <p class="text-sm text-gray-600 dark:text-gray-400">{{ __('Tema gelap untuk pengalaman
                                    yang nyaman') }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- System Theme -->
                <div class="group cursor-pointer" @click="updateTheme('system')">
                    <div class="relative">
                        <div
                            class="absolute inset-0 bg-gradient-to-r from-blue-500 to-blue-600 rounded-2xl blur opacity-0 group-hover:opacity-20 transition-opacity duration-300">
                        </div>
                        <div class="relative bg-white dark:bg-gray-800 rounded-2xl border-2 transition-all duration-300 group-hover:scale-105"
                            :class="theme === 'system' ? 'border-blue-500 shadow-lg shadow-blue-500/25' : 'border-gray-200 dark:border-gray-600 hover:border-blue-300 dark:hover:border-blue-600'">
                            <div class="p-6 text-center">
                                <div
                                    class="w-16 h-16 mx-auto mb-4 rounded-xl bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center">
                                    <svg class="w-8 h-8 text-blue-600 dark:text-blue-400" fill="none"
                                        stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z">
                                        </path>
                                    </svg>
                                </div>
                                <h4 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">{{ __('Sistem') }}
                                </h4>
                                <p class="text-sm text-gray-600 dark:text-gray-400">{{ __('Mengikuti preferensi sistem
                                    Anda') }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Current Theme Info -->
            <div
                class="p-6 bg-gradient-to-r from-blue-50 to-blue-100 dark:from-blue-900/20 dark:to-blue-800/20 rounded-xl border border-blue-200 dark:border-blue-800">
                <div
                    class="flex flex-col sm:flex-row items-start sm:items-center justify-between space-y-4 sm:space-y-0">
                    <div class="flex items-center space-x-3">
                        <div
                            class="w-10 h-10 rounded-lg bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center">
                            <svg class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-blue-800 dark:text-blue-200">{{ __('Tema Saat Ini') }}
                            </p>
                            <p class="text-sm text-blue-700 dark:text-blue-300"
                                x-text="theme === 'system' ? 'Mengikuti preferensi sistem' : theme === 'dark' ? 'Mode gelap aktif' : 'Mode terang aktif'">
                            </p>
                        </div>
                    </div>
                    <div class="flex items-center space-x-2">
                        <span class="px-3 py-1 rounded-full text-xs font-medium"
                            :class="theme === 'dark' ? 'bg-gray-800 text-gray-200' : theme === 'light' ? 'bg-gray-200 text-gray-800' : 'bg-blue-100 text-blue-800'"
                            x-text="theme === 'system' ? 'Sistem' : theme === 'dark' ? 'Gelap' : 'Terang'"></span>
                    </div>
                </div>
            </div>

            <!-- Info Section -->
            <div class="mt-6 pt-6 border-t border-gray-100 dark:border-gray-700">
                <div class="flex items-start space-x-3">
                    <svg class="w-5 h-5 text-blue-600 dark:text-blue-400 mt-0.5 flex-shrink-0" fill="none"
                        stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <p class="text-sm text-gray-600 dark:text-gray-400">
                        {{ __('Preferensi tema Anda akan disimpan secara otomatis dan diterapkan di semua halaman.') }}
                    </p>
                </div>
            </div>
        </div>

        <!-- Back Button -->
        <div class="text-center">
            <a href="{{ route('admin.settings.index') }}"
                class="inline-flex items-center px-8 py-3 bg-gray-100 hover:bg-gray-200 text-gray-700 dark:bg-gray-700 dark:hover:bg-gray-600 dark:text-gray-300 font-medium rounded-xl transition-all duration-200 hover:shadow-lg"
                wire:navigate>
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                {{ __('Kembali ke Pengaturan') }}
            </a>
        </div>
    </div>
</div>