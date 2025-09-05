<?php

use Illuminate\Auth\Events\Lockout;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Validate;
use Livewire\Volt\Component;

new #[Layout('components.layouts.auth')] class extends Component {
    #[Validate('required|string|email')]
    public string $email = '';

    #[Validate('required|string')]
    public string $password = '';

    public bool $remember = false;

    /**
     * Handle an incoming authentication request.
     */
    public function login(): void
    {
        $this->validate();

        $this->ensureIsNotRateLimited();

        if (! Auth::attempt(['email' => $this->email, 'password' => $this->password], $this->remember)) {
            RateLimiter::hit($this->throttleKey());

            throw ValidationException::withMessages([
                'email' => __('auth.failed'),
            ]);
        }

        RateLimiter::clear($this->throttleKey());
        Session::regenerate();

        $this->redirectIntended(default: route('redirect.role', absolute: false), navigate: true);
    }

    /**
     * Ensure the authentication request is not rate limited.
     */
    protected function ensureIsNotRateLimited(): void
    {
        if (! RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
            return;
        }

        event(new Lockout(request()));

        $seconds = RateLimiter::availableIn($this->throttleKey());

        throw ValidationException::withMessages([
            'email' => __('auth.throttle', [
                'seconds' => $seconds,
                'minutes' => ceil($seconds / 60),
            ]),
        ]);
    }

    /**
     * Get the authentication rate limiting throttle key.
     */
    protected function throttleKey(): string
    {
        return Str::transliterate(Str::lower($this->email).'|'.request()->ip());
    }
}; ?>

<div class="flex flex-col gap-6">
    <x-auth-header :title="__('Log in to your account')"
        :description="__('Enter your email and password below to log in')" />

    <!-- Session Status -->
    <x-auth-session-status class="text-center" :status="session('status')" />

    <!-- Alert Messages -->
    <x-alert />

    <form wire:submit="login" class="flex flex-col gap-6">
        <!-- Email Address -->
        <flux:input wire:model="email" :label="__('Email address')" type="email" required autofocus autocomplete="email"
            placeholder="email@example.com" />

        <!-- Password -->
        <div class="relative">
            <flux:input wire:model="password" :label="__('Password')" type="password" required
                autocomplete="current-password" :placeholder="__('Password')" viewable />

            @if (Route::has('password.request'))
            <a href="{{ route('password.request') }}" wire:navigate
                class="absolute end-0 top-0 text-sm text-[#133E87] hover:text-[#0f326e] font-medium transition-colors duration-200 underline decoration-[#133E87]/30 hover:decoration-[#0f326e]">
                {{ __('Forgot your password?') }}
            </a>
            @endif
        </div>

        <!-- Remember Me -->
        <div class="flex items-center">
            <input type="checkbox" wire:model="remember" id="remember"
                class="w-4 h-4 text-[#133E87] bg-gray-100 border-gray-300 rounded focus:ring-[#133E87] focus:ring-2">
            <label for="remember" class="ml-2 text-sm text-zinc-700 dark:text-zinc-300 cursor-pointer">{{ __('Remember
                me') }}</label>
        </div>

        <div class="flex items-center justify-end">
            <button type="submit"
                class="w-full inline-flex items-center justify-center gap-3 rounded-xl bg-[#133E87] hover:bg-[#0f326e] px-8 py-4 text-white font-medium text-lg transition-all duration-300 transform hover:scale-105 focus:outline-none focus:ring-4 focus:ring-blue-300/50">
                <i class="fas fa-sign-in-alt text-xl"></i>
                <span>{{ __('Log in') }}</span>
            </button>
        </div>
    </form>

    @if (Route::has('register'))
    <div class="space-x-1 rtl:space-x-reverse text-center text-sm text-zinc-600 dark:text-zinc-400">
        <span>{{ __('Don\'t have an account?') }}</span>
        <a href="{{ route('register') }}" wire:navigate
            class="text-[#133E87] hover:text-[#0f326e] font-medium transition-colors duration-200 underline decoration-[#133E87]/30 hover:decoration-[#0f326e]">{{
            __('Sign up') }}</a>
    </div>
    @endif
</div>
