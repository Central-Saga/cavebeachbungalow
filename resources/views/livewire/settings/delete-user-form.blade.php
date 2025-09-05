<?php

use App\Livewire\Actions\Logout;
use Illuminate\Support\Facades\Auth;
use function Livewire\Volt\{state, rules};

state(['password' => '']);

rules([
    'password' => ['required', 'string', 'current_password'],
]);

$deleteUser = function (Logout $logout) {
    $this->validate();

    tap(Auth::user(), $logout(...))->delete();
    $this->redirect('/', navigate: true);
};
?>

<div class="bg-gray-50 dark:bg-gray-700/50 rounded-xl border border-red-200 dark:border-red-800 p-6">
    <div class="flex items-start space-x-4 mb-6">
        <div class="w-12 h-12 rounded-xl bg-red-100 dark:bg-red-900/30 flex items-center justify-center flex-shrink-0">
            <svg class="w-6 h-6 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.34 16.5c-.77.833.192 2.5 1.732 2.5z">
                </path>
            </svg>
        </div>
        <div class="flex-1">
            <h2 class="text-2xl font-semibold text-red-800 dark:text-red-200 mb-2">{{ __('Hapus Akun') }}</h2>
            <p class="text-red-600 dark:text-red-300">{{ __('Hapus akun Anda dan semua sumber dayanya secara permanen')
                }}</p>
        </div>
    </div>

    <div class="flex flex-col sm:flex-row items-start sm:items-center gap-4">
        <flux:modal.trigger name="confirm-user-deletion">
            <flux:button variant="danger" x-data=""
                x-on:click.prevent="$dispatch('open-modal', 'confirm-user-deletion')"
                class="px-8 py-3 bg-red-600 hover:bg-red-700 text-white font-medium rounded-xl transition-all duration-200 hover:shadow-lg flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                    </path>
                </svg>
                {{ __('Hapus Akun') }}
            </flux:button>
        </flux:modal.trigger>

        <a href="{{ route('admin.settings.index') }}"
            class="px-8 py-3 bg-gray-100 hover:bg-gray-200 text-gray-700 dark:bg-gray-700 dark:hover:bg-gray-600 dark:text-gray-300 font-medium rounded-xl transition-all duration-200 flex items-center"
            wire:navigate>
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18">
                </path>
            </svg>
            {{ __('Kembali ke Pengaturan') }}
        </a>
    </div>

    <flux:modal name="confirm-user-deletion" :show="$errors->isNotEmpty()" focusable class="max-w-lg">
        <form wire:submit="deleteUser" class="space-y-6">
            <div class="text-center">
                <div
                    class="w-16 h-16 mx-auto mb-4 rounded-full bg-red-100 dark:bg-red-900/30 flex items-center justify-center">
                    <svg class="w-8 h-8 text-red-600 dark:text-red-400" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.34 16.5c-.77.833.192 2.5 1.732 2.5z">
                        </path>
                    </svg>
                </div>
                <flux:heading size="lg" class="text-red-800 dark:text-red-200">{{ __('Apakah Anda yakin ingin menghapus
                    akun?') }}</flux:heading>
            </div>

            <flux:subheading class="text-center text-gray-600 dark:text-gray-400">
                {{ __('Setelah akun Anda dihapus, semua sumber daya dan datanya akan dihapus secara permanen. Harap
                masukkan password Anda untuk mengkonfirmasi bahwa Anda ingin menghapus akun secara permanen.') }}
            </flux:subheading>

            <div class="space-y-3">
                <label for="delete_password" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{
                    __('Password') }}</label>
                <flux:input wire:model="password" id="delete_password" :label="__('Password')" type="password" />
            </div>

            <div class="flex flex-col sm:flex-row justify-end space-y-3 sm:space-y-0 sm:space-x-3">
                <flux:modal.close>
                    <flux:button variant="filled"
                        class="w-full sm:w-auto px-6 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 dark:bg-gray-700 dark:hover:bg-gray-600 dark:text-gray-300">
                        {{ __('Batal') }}
                    </flux:button>
                </flux:modal.close>

                <flux:button variant="danger" type="submit" class="w-full sm:w-auto px-6 py-2">
                    {{ __('Hapus Akun') }}
                </flux:button>
            </div>
        </form>
    </flux:modal>
</div>