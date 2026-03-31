<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Профил') }}
        </h2>
    </x-slot>

    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6 py-12">

        {{-- СЕКЦИЈА 1: ПРОФИЛНИ ИНФОРМАЦИИ --}}
        <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
            <div class="max-w-xl">
                @if(auth()->user()->role === 'admin')
                    {{-- Админот може да ја гледа и користи формата за промена --}}
                    <livewire:profile.update-profile-information-form />
                @else
                    {{-- Ученикот гледа само текст (Read-only) --}}
                    <div class="space-y-6">
                        <header>
                            <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100 uppercase tracking-wider">
                                Профилни информации
                            </h2>
                            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                                Вашите основни податоци регистрирани во системот.
                            </p>
                        </header>

                        <div class="grid grid-cols-1 gap-4">
                            <div>
                                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-widest">Име и
                                    презиме</label>
                                <p class="mt-1 text-md text-gray-900 dark:text-white font-medium">{{ auth()->user()->name }}
                                </p>
                            </div>

                            <div>
                                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-widest">Е-маил
                                    адреса</label>
                                <p class="mt-1 text-md text-gray-900 dark:text-white font-medium">
                                    {{ auth()->user()->email }}</p>
                            </div>
                        </div>

                        <div class="mt-4 p-3 bg-blue-50 border-l-4 border-blue-400 text-blue-700 text-xs">
                            Доколку сакате да ги промените овие податоци, ве молиме контактирајте го вашиот
                            ментор/администратор.
                        </div>
                    </div>
                @endif
            </div>
        </div>

        {{-- СЕКЦИЈА 2: ПРОМЕНА НА ЛОЗИНКА (Достапно за сите) --}}
        <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
            <div class="max-w-xl">
                <livewire:profile.update-password-form />
            </div>
        </div>

        {{-- СЕКЦИЈА 3: БРИШЕЊЕ НА АКАУНТ (Само за Админ) --}}
@if (auth()->user()->role === 'admin')
            <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow sm:rounded-lg border-t border-red-100">
                <div class="max-w-xl text-red-600">
                    <h3 class="text-lg font-bold uppercase mb-4 text-red-500">Опасна зона</h3>

                    {{-- Ја тргнавме линијата со Jetstream и ја оставивме само компонентата --}}
                    @livewire('profile.delete-user-form')

                </div>
            </div>
        @endif
    </div>
</x-app-layout>
