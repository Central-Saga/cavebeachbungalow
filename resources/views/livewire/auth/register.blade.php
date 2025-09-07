<?php

use App\Models\User;
use App\Models\Pelanggan;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('components.layouts.auth')] class extends Component {
    public string $name = '';
    public string $email = '';
    public string $password = '';
    public string $password_confirmation = '';

    // Fields untuk Pelanggan
    public string $alamat = '';
    public string $jenis_kelamin = '';
    public string $telepon = '';

    /**
     * Handle an incoming registration request.
     */
    public function register(): void
    {
        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:' . User::class],
            'password' => ['required', 'string', 'confirmed', Rules\Password::defaults()],
            'alamat' => ['required', 'string', 'max:255'],
            'jenis_kelamin' => ['required', 'in:L,P'],
            'telepon' => ['required', 'string', 'max:15', 'unique:pelanggans,telepon'],
        ]);

        $userData = [
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
        ];

        $pelangganData = [
            'alamat' => $validated['alamat'],
            'jenis_kelamin' => $validated['jenis_kelamin'],
            'telepon' => $validated['telepon'],
        ];

        // Create user first
        $user = User::create($userData);

        // Create pelanggan with user_id
        $pelangganData['user_id'] = $user->id;
        Pelanggan::create($pelangganData);

        // Assign role "Pengunjung" to new user
        $user->assignRole('Pengunjung');

        event(new Registered($user));

        Auth::login($user);

        $this->redirectIntended(route('redirect.role', absolute: false), navigate: true);
    }
}; ?>

<div class="flex flex-col gap-6">
    <x-auth-header :title="__('Create an account')"
        :description="__('Enter your details below to create your account')" />

    <!-- Alert Messages -->
    <x-alert />

    <!-- Session Status -->
    <x-auth-session-status class="text-center" :status="session('status')" />

    <form wire:submit="register" class="flex flex-col gap-6">
        <div class="space-y-4">
            <!-- Name -->
            <flux:input wire:model="name" :label="__('Name')" type="text" required autofocus autocomplete="name"
                :placeholder="__('Full name')" />

            <!-- Email Address -->
            <flux:input wire:model="email" :label="__('Email address')" type="email" required autocomplete="email"
                placeholder="email@example.com" />

            <!-- Password -->
            <flux:input wire:model="password" :label="__('Password')" type="password" required
                autocomplete="new-password" :placeholder="__('Password')" viewable />

            <!-- Confirm Password -->
            <flux:input wire:model="password_confirmation" :label="__('Confirm password')" type="password" required
                autocomplete="new-password" :placeholder="__('Confirm password')" viewable />

            <!-- Phone Number -->
            <flux:input wire:model="telepon" :label="__('Phone Number')" type="tel" required autocomplete="tel"
                :placeholder="__('08123456789')" />

            <!-- Address -->
            <flux:input wire:model="alamat" :label="__('Address')" type="text" required
                autocomplete="street-address" :placeholder="__('Jl. Contoh No. 123')" />

            <!-- Gender -->
            <div class="space-y-2">
                <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300">
                    {{ __('Gender') }} <span class="text-red-500">*</span>
                </label>
                <div class="flex gap-4">
                    <label class="flex items-center cursor-pointer group">
                        <input type="radio" wire:model="jenis_kelamin" value="L"
                            class="mr-3 w-4 h-4 text-[#133E87] bg-gray-100 border-gray-300 focus:ring-[#133E87] focus:ring-2">
                        <span
                            class="text-sm text-zinc-700 dark:text-zinc-300 group-hover:text-[#133E87] transition-colors duration-200">{{
                            __('Male') }}</span>
                    </label>
                    <label class="flex items-center cursor-pointer group">
                        <input type="radio" wire:model="jenis_kelamin" value="P"
                            class="mr-3 w-4 h-4 text-[#133E87] bg-gray-100 border-gray-300 focus:ring-[#133E87] focus:ring-2">
                        <span
                            class="text-sm text-zinc-700 dark:text-zinc-300 group-hover:text-[#133E87] transition-colors duration-200">{{
                            __('Female') }}</span>
                    </label>
                </div>
                @error('jenis_kelamin')
                <p class="text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <div class="flex items-center justify-end">
            <button type="submit"
                class="w-full inline-flex items-center justify-center gap-3 rounded-xl bg-[#133E87] hover:bg-[#0f326e] px-8 py-4 text-white font-medium text-lg transition-all duration-300 transform hover:scale-105 focus:outline-none focus:ring-4 focus:ring-blue-300/50">
                <i class="fas fa-user-plus text-xl"></i>
                <span>{{ __('Create account') }}</span>
            </button>
        </div>
    </form>

    <div class="space-x-1 rtl:space-x-reverse text-center text-sm text-zinc-600 dark:text-zinc-400">
        <span>{{ __('Already have an account?') }}</span>
        <a href="{{ route('login') }}" wire:navigate
            class="text-[#133E87] hover:text-[#0f326e] font-medium transition-colors duration-200 underline decoration-[#133E87]/30 hover:decoration-[#0f326e]">{{
            __('Log in') }}</a>
    </div>
</div>
