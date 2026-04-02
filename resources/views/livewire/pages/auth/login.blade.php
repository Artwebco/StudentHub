<?php

use App\Livewire\Forms\LoginForm;
use Illuminate\Support\Facades\Session;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.guest')] class extends Component {
    public LoginForm $form;

    /**
     * Handle an incoming authentication request.
     */
    public function login(): void
    {
        $this->validate();

        $this->form->authenticate();

        Session::regenerate();

        $this->redirectIntended(default: route('dashboard', absolute: false), navigate: true);
    }
}; ?>

<div class="flex flex-col gap-6">
    <div class="text-center">
        <h2 class="text-xl font-semibold text-slate-800">Sign In</h2>
        <div class="mt-6 h-px w-full bg-gradient-to-r from-transparent via-blue-500/30 to-transparent"></div>
    </div>

    <x-auth-session-status class="mb-4 text-center" :status="session('status')" />

    <form wire:submit="login" class="space-y-5">
        <div class="space-y-2">
            <label class="text-sm font-medium text-slate-500 ml-1" for="email">Email</label>
            <input wire:model="form.email" type="email" id="email" name="email" required autofocus
                autocomplete="username"
                class="flex h-11 w-full rounded-lg border border-slate-200 bg-slate-50/50 px-4 py-2 text-sm transition-all focus:border-blue-500 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500/10 placeholder:text-slate-400"
                placeholder="your@email.com">
            <x-input-error :messages="$errors->get('form.email')" class="mt-2" />
        </div>

        <div class="space-y-2">
            <label class="text-sm font-medium text-slate-500 ml-1" for="password">Password</label>
            <input wire:model="form.password" type="password" id="password" name="password" required
                autocomplete="current-password"
                class="flex h-11 w-full rounded-lg border border-slate-200 bg-slate-50/50 px-4 py-2 text-sm transition-all focus:border-blue-500 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500/10 placeholder:text-slate-400"
                placeholder="••••••••">

            <x-input-error :messages="$errors->get('form.password')" class="mt-2" />
        </div>

        <div class="flex items-center justify-between">
            <label for="remember" class="flex items-center gap-2 cursor-pointer group">
                <input wire:model.live="form.remember" id="remember" type="checkbox" value="1"
                    class="h-4 w-4 rounded border-slate-300 text-blue-600 focus:ring-blue-500/20 transition-all"
                    name="remember">
                <span
                    class="text-sm font-medium text-slate-500 group-hover:text-slate-700">{{ __('Remember me') }}</span>
            </label>

            @if (Route::has('password.request'))
                <a class="text-sm font-medium text-blue-600 hover:text-blue-500 transition-colors"
                    href="{{ route('password.request') }}" wire:navigate>
                    {{ __('Forgot your password?') }}
                </a>
            @endif
        </div>

        <button type="submit"
            class="flex h-12 w-full items-center justify-center rounded-lg bg-gradient-to-r from-blue-600 to-emerald-500 text-sm font-semibold uppercase tracking-widest text-white shadow-lg shadow-blue-500/20 transition-all hover:opacity-90 hover:scale-[1.01] active:scale-[0.99]">
            {{ __('Sign In') }}
        </button>
    </form>
</div>