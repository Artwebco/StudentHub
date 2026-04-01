<?php

use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Locked;
use Livewire\Volt\Component;

new #[Layout('layouts.guest')] class extends Component {
    #[Locked]
    public string $token = '';
    public string $email = '';
    public string $password = '';
    public string $password_confirmation = '';

    /**
     * Mount the component.
     */
    public function mount(string $token): void
    {
        $this->token = $token;

        $this->email = request()->string('email');
    }

    /**
     * Reset the password for the given user.
     */
    public function resetPassword(): void
    {
        $this->validate([
            'token' => ['required'],
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string', 'confirmed', Rules\Password::defaults()],
        ]);

        // Here we will attempt to reset the user's password. If it is successful we
        // will update the password on an actual user model and persist it to the
        // database. Otherwise we will parse the error and return the response.
        $status = Password::reset(
            $this->only('email', 'password', 'password_confirmation', 'token'),
            function ($user) {
                $user->forceFill([
                    'password' => Hash::make($this->password),
                    'remember_token' => Str::random(60),
                ])->save();

                event(new PasswordReset($user));
            }
        );

        // If the password was successfully reset, we will redirect the user back to
        // the application's home authenticated view. If there is an error we can
        // redirect them back to where they came from with their error message.
        if ($status != Password::PASSWORD_RESET) {
            $this->addError('email', __($status));

            return;
        }

        Session::flash('status', __($status));

        $this->redirectRoute('login', navigate: true);
    }
}; ?>

<div class="flex flex-col gap-6">
    <div class="text-center">
        <h2 class="text-xl font-semibold text-slate-800">Reset Password</h2>
        <div class="mt-6 h-px w-full bg-gradient-to-r from-transparent via-blue-500/30 to-transparent"></div>
    </div>

    <form wire:submit="resetPassword" class="space-y-5">
        <div class="space-y-2">
            <x-input-label for="email" :value="__('Email')" class="text-sm font-medium text-slate-500 ml-1" />
            <x-text-input wire:model="email" id="email"
                class="block w-full h-11 rounded-lg border-slate-200 bg-slate-50/50 px-4 py-2 text-sm transition-all focus:border-blue-500 focus:bg-white focus:ring-2 focus:ring-blue-500/10 placeholder:text-slate-400"
                type="email" name="email" required autofocus autocomplete="username" placeholder="your@email.com" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div class="space-y-2">
            <x-input-label for="password" :value="__('Password')" class="text-sm font-medium text-slate-500 ml-1" />
            <x-text-input wire:model="password" id="password"
                class="block w-full h-11 rounded-lg border-slate-200 bg-slate-50/50 px-4 py-2 text-sm transition-all focus:border-blue-500 focus:bg-white focus:ring-2 focus:ring-blue-500/10 placeholder:text-slate-400"
                type="password" name="password" required autocomplete="new-password" placeholder="••••••••" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <div class="space-y-2">
            <x-input-label for="password_confirmation" :value="__('Confirm Password')"
                class="text-sm font-medium text-slate-500 ml-1" />
            <x-text-input wire:model="password_confirmation" id="password_confirmation"
                class="block w-full h-11 rounded-lg border-slate-200 bg-slate-50/50 px-4 py-2 text-sm transition-all focus:border-blue-500 focus:bg-white focus:ring-2 focus:ring-blue-500/10 placeholder:text-slate-400"
                type="password" name="password_confirmation" required autocomplete="new-password"
                placeholder="••••••••" />
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <div class="pt-2">
            <button type="submit"
                class="flex h-12 w-full items-center justify-center rounded-lg bg-gradient-to-r from-blue-600 to-emerald-500 text-sm font-bold uppercase tracking-widest text-white shadow-lg shadow-blue-500/20 transition-all hover:opacity-90 hover:scale-[1.01] active:scale-[0.99]">
                {{ __('Reset Password') }}
            </button>
        </div>
    </form>
</div>
