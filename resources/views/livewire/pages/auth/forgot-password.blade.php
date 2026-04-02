<?php

use Illuminate\Support\Facades\Password;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.guest')] class extends Component {
    public string $email = '';

    /**
     * Send a password reset link to the provided email address.
     */
    public function sendPasswordResetLink(): void
    {
        $this->validate([
            'email' => ['required', 'string', 'email'],
        ]);

        $status = Password::sendResetLink(
            $this->only('email')
        );

        if ($status != Password::RESET_LINK_SENT) {
            $this->addError('email', __($status));

            return;
        }

        $this->reset('email');

        session()->flash('status', __($status));
    }
}; ?>

<div class="flex flex-col gap-6">
    <div class="text-center">
        <h2 class="text-xl font-semibold text-slate-800">Forgot Password?</h2>
        <div class="mt-4 text-sm text-slate-500 leading-relaxed italic">
            {{ __('No problem. Enter your email address and we will send you a link to reset your password.') }}
        </div>
        <div class="mt-6 h-px w-full bg-gradient-to-r from-transparent via-blue-500/30 to-transparent"></div>
    </div>

    <x-auth-session-status class="text-center" :status="session('status')" />

    <form wire:submit="sendPasswordResetLink" class="space-y-5" novalidate>
        <div class="space-y-2">
            <label class="text-sm font-medium text-slate-500 ml-1" for="email">Email</label>
            <input wire:model="email" type="email" id="email" name="email" required autofocus autocomplete="username"
                class="flex h-11 w-full rounded-lg border border-slate-200 bg-slate-50/50 px-4 py-2 text-sm transition-all focus:border-blue-500 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500/10 placeholder:text-slate-400"
                placeholder="your@email.com">
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <button type="submit"
            class="flex h-12 w-full items-center justify-center rounded-lg bg-gradient-to-r from-blue-600 to-emerald-500 text-sm font-semibold uppercase tracking-widest text-white shadow-lg shadow-blue-500/20 transition-all hover:opacity-90 hover:scale-[1.01] active:scale-[0.99]">
            {{ __('Send Reset Link') }}
        </button>

        <div class="text-center mt-4">
            <a href="{{ route('login') }}" wire:navigate
                class="text-xs font-medium text-slate-400 hover:text-blue-600 transition-colors">
                {{ __('Back to Sign In') }}
            </a>
        </div>
    </form>
</div>