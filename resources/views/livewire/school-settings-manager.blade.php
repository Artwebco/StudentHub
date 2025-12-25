<div class="p-6 bg-white rounded shadow text-black">
    <div class="space-y-4 text-black bg-gray-50/50 p-4 rounded-xl border border-gray-100">
        <div class="flex items-center justify-between mb-8">
            <div>
                <h2 class="text-2xl font-bold text-gray-800">Подесувања на Училиштето</h2>
                <p class="text-sm text-gray-500">Овие податоци ќе се појавуваат на секоја генерирана фактура.</p>
            </div>
            <div class="h-16 w-16 bg-blue-50 rounded-xl flex items-center justify-center text-blue-600">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                </svg>
            </div>
        </div>

        @if (session()->has('message'))
            <div class="mb-6 p-4 bg-green-50 border-l-4 border-green-500 text-green-700 text-sm font-medium">
                {{ session('message') }}
            </div>
        @endif

        <form wire:submit.prevent="save" class="space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                <div>
                    <label class="block text-xs font-bold uppercase text-gray-400 mb-2">Име на училиште / фирма</label>
                    <input type="text" wire:model="school_name"
                        class="w-full border-gray-200 rounded-xl p-3 bg-gray-50 focus:bg-white transition-all outline-none focus:ring-2 focus:ring-blue-500">
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase text-gray-400 mb-2">Дејност</label>
                    <input type="text" wire:model="activity"
                        class="w-full border-gray-200 rounded-xl p-3 bg-gray-50 focus:bg-white transition-all outline-none focus:ring-2 focus:ring-blue-500"
                        placeholder="Пример: Образование">
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase text-gray-400 mb-2">Матичен број на фирма</label>
                    <input type="text" wire:model="registration_number"
                        class="w-full border-gray-200 rounded-xl p-3 bg-gray-50 focus:bg-white transition-all outline-none focus:ring-2 focus:ring-blue-500">
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase text-gray-400 mb-2">ЕДБ (Даночен број)</label>
                    <input type="text" wire:model="tax_number"
                        class="w-full border-gray-200 rounded-xl p-3 bg-gray-50 focus:bg-white transition-all outline-none focus:ring-2 focus:ring-blue-500">
                </div>

                <div class="md:col-span-2">
                    <label class="block text-xs font-bold uppercase text-gray-400 mb-2">Целосна адреса (Улица, број,
                        град)</label>
                    <input type="text" wire:model="address"
                        class="w-full border-gray-200 rounded-xl p-3 bg-gray-50 focus:bg-white transition-all outline-none focus:ring-2 focus:ring-blue-500">
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase text-gray-400 mb-2">Жиро сметка</label>
                    <input type="text" wire:model="bank_account"
                        class="w-full border-gray-200 rounded-xl p-3 bg-gray-50 focus:bg-white transition-all outline-none focus:ring-2 focus:ring-blue-500">
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase text-gray-400 mb-2">Депонентна Банка</label>
                    <input type="text" wire:model="bank_name"
                        class="w-full border-gray-200 rounded-xl p-3 bg-gray-50 focus:bg-white transition-all outline-none focus:ring-2 focus:ring-blue-500">
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase text-gray-400 mb-2">Е-маил адреса</label>
                    <input type="email" wire:model="email"
                        class="w-full border-gray-200 rounded-xl p-3 bg-gray-50 focus:bg-white transition-all outline-none focus:ring-2 focus:ring-blue-500">
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase text-gray-400 mb-2">Веб-страница</label>
                    <input type="text" wire:model="website"
                        class="w-full border-gray-200 rounded-xl p-3 bg-gray-50 focus:bg-white transition-all outline-none focus:ring-2 focus:ring-blue-500">
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase text-gray-400 mb-2">Телефон</label>
                    <input type="text" wire:model="phone"
                        class="w-full border-gray-200 rounded-xl p-3 bg-gray-50 focus:bg-white transition-all outline-none focus:ring-2 focus:ring-blue-500">
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase text-gray-400 mb-2">Лого на училиштето</label>
                    <div class="flex items-center gap-4 p-4 bg-gray-50 rounded-xl border border-dashed border-gray-300">
                        @if ($logo)
                            <img src="{{ $logo->temporaryUrl() }}" class="h-16 w-16 object-contain rounded-lg">
                        @elseif ($existingLogo)
                            <img src="{{ asset('storage/' . $existingLogo) }}" class="h-16 w-16 object-contain rounded-lg">
                        @endif
                        <input type="file" wire:model="logo" class="text-sm">
                    </div>
                </div>
            </div>

            <div class="flex justify-end mt-8">
                <button type="submit"
                    class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-10 rounded-xl shadow-lg shadow-blue-200 transition-all transform hover:scale-105 active:scale-95">
                    Зачувај Подесувања
                </button>
            </div>
        </form>
    </div>
</div>
