<div>
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 border-b pb-4 gap-4">
        <div>
            <h2 class="text-xl font-bold text-gray-700">Подесувања на училиштето</h2>
            <p class="text-md text-gray-600">Овие податоци ќе се појавуваат на секоја генерирана фактура.</p>
        </div>
        <div class="h-16 w-16 bg-blue-50 rounded-xl flex items-center justify-center text-blue-600">
            <svg class="h-8 w-8" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"
                stroke-linecap="round" stroke-linejoin="round">
                <rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect>
                <line x1="3" y1="9" x2="21" y2="9"></line>
                <line x1="9" y1="21" x2="9" y2="9"></line>
            </svg>
        </div>
    </div>

    @if (session()->has('message'))
        <div wire:key="{{ now() }}" x-data="{ show: true }" x-init="setTimeout(() => show = false, 5000)" x-show="show"
            class="flex items-center justify-between bg-green-100 border-l-4 border-green-500 p-3 mb-6 text-green-800 rounded shadow-sm">
            <div class="flex items-center">
                <svg class="h-5 w-5 mr-2 text-green-500" fill="none" stroke="currentColor" stroke-width="2"
                    viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                    <polyline points="22 4 12 14.01 9 11.01"></polyline>
                </svg>
                <span class="text-sm font-medium">{{ session('message') }}</span>
            </div>
            <button @click="show = false" class="text-green-500 hover:text-green-800">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"
                    stroke-linecap="round" stroke-linejoin="round">
                    <path d="M18 6L6 18M6 6l12 12"></path>
                </svg>
            </button>
        </div>
    @endif

    <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm">
        <form wire:submit.prevent="save" class="space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                {{-- Име --}}
                <div>
                    <label class="block text-xs font-bold uppercase text-gray-400 mb-2 tracking-wider">Име на училиште /
                        фирма</label>
                    <input type="text" wire:model="school_name"
                        class="w-full border-gray-200 rounded-xl p-3 bg-gray-50 focus:bg-white transition-all outline-none focus:ring-2 focus:ring-blue-500 @error('school_name') border-red-500 @enderror">
                    @error('school_name') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                </div>

                {{-- Дејност --}}
                <div>
                    <label class="block text-xs font-bold uppercase text-gray-400 mb-2 tracking-wider">Дејност</label>
                    <input type="text" wire:model="activity"
                        class="w-full border-gray-200 rounded-xl p-3 bg-gray-50 focus:bg-white transition-all outline-none focus:ring-2 focus:ring-blue-500"
                        placeholder="Пример: Образование">
                </div>

                {{-- Матичен број --}}
                <div>
                    <label class="block text-xs font-bold uppercase text-gray-400 mb-2 tracking-wider">Матичен
                        број</label>
                    <input type="text" wire:model="registration_number"
                        class="w-full border-gray-200 rounded-xl p-3 bg-gray-50 focus:bg-white transition-all outline-none focus:ring-2 focus:ring-blue-500">
                </div>

                {{-- ЕДБ --}}
                <div>
                    <label class="block text-xs font-bold uppercase text-gray-400 mb-2 tracking-wider">ЕДБ (Даночен
                        број)</label>
                    <input type="text" wire:model="tax_number"
                        class="w-full border-gray-200 rounded-xl p-3 bg-gray-50 focus:bg-white transition-all outline-none focus:ring-2 focus:ring-blue-500">
                </div>


                {{-- SWIFT, IBAN, Жиро сметка (bank_account) --}}
                <div class="md:col-span-2">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label
                                class="block text-xs font-bold uppercase text-gray-400 mb-2 tracking-wider">SWIFT</label>
                            <input type="text" wire:model="swift_number"
                                class="w-full border-gray-200 rounded-xl p-3 bg-gray-50 focus:bg-white transition-all outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                        <div>
                            <label
                                class="block text-xs font-bold uppercase text-gray-400 mb-2 tracking-wider">IBAN</label>
                            <input type="text" wire:model="iban_number"
                                class="w-full border-gray-200 rounded-xl p-3 bg-gray-50 focus:bg-white transition-all outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                        <div>
                            <label class="block text-xs font-bold uppercase text-gray-400 mb-2 tracking-wider">Жиро
                                сметка</label>
                            <input type="text" wire:model="bank_account"
                                class="w-full border-gray-200 rounded-xl p-3 bg-gray-50 focus:bg-white transition-all outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                    </div>
                </div>

                {{-- Адреса --}}
                <div>
                    <label class="block text-xs font-bold uppercase text-gray-400 mb-2 tracking-wider">Целосна
                        адреса</label>
                    <input type="text" wire:model="address"
                        class="w-full border-gray-200 rounded-xl p-3 bg-gray-50 focus:bg-white transition-all outline-none focus:ring-2 focus:ring-blue-500">
                </div>

                {{-- Телефон --}}
                <div>
                    <label class="block text-xs font-bold uppercase text-gray-400 mb-2 tracking-wider">Телефон</label>
                    <input type="text" wire:model="phone"
                        class="w-full border-gray-200 rounded-xl p-3 bg-gray-50 focus:bg-white transition-all outline-none focus:ring-2 focus:ring-blue-500">
                </div>

                {{-- Е-маил --}}
                <div>
                    <label class="block text-xs font-bold uppercase text-gray-400 mb-2 tracking-wider">Е-пошта</label>
                    <input type="email" wire:model="email"
                        class="w-full border-gray-200 rounded-xl p-3 bg-gray-50 focus:bg-white transition-all outline-none focus:ring-2 focus:ring-blue-500 @error('email') border-red-500 @enderror">
                    @error('email') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                </div>

                {{-- Веб --}}
                <div>
                    <label
                        class="block text-xs font-bold uppercase text-gray-400 mb-2 tracking-wider">Веб-страница</label>
                    <input type="text" wire:model="website"
                        class="w-full border-gray-200 rounded-xl p-3 bg-gray-50 focus:bg-white transition-all outline-none focus:ring-2 focus:ring-blue-500 @error('website') border-red-500 @enderror"
                        placeholder="www.besedi.mk">
                    @error('website') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                </div>

                {{-- Лого --}}
                <div class="md:col-span-2">
                    <label class="block text-xs font-bold uppercase text-gray-400 mb-2 tracking-wider">Лого на
                        училиштето</label>
                    <div
                        class="flex flex-col sm:flex-row sm:items-center gap-4 sm:gap-6 p-4 sm:p-6 bg-gray-50 rounded-2xl border-2 border-dashed border-gray-200 hover:border-blue-300 transition-colors">
                        <div class="flex-shrink-0">
                            @if ($logo)
                                <img src="{{ $logo->temporaryUrl() }}"
                                    class="h-20 w-20 object-contain rounded-xl shadow-sm bg-white p-1">
                            @elseif ($existingLogo)
                                <img src="{{ asset('storage/' . $existingLogo) }}"
                                    class="h-20 w-20 object-contain rounded-xl shadow-sm bg-white p-1">
                            @else
                                <div
                                    class="h-20 w-20 bg-gray-200 rounded-xl flex items-center justify-center text-gray-400">
                                    <svg class="w-8 h-8" fill="none" stroke="currentColor" stroke-width="2"
                                        viewBox="0 0 24 24">
                                        <rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect>
                                        <circle cx="8.5" cy="8.5" r="1.5"></circle>
                                        <polyline points="21 15 16 10 5 21"></polyline>
                                    </svg>
                                </div>
                            @endif
                        </div>
                        <div class="flex-1 min-w-0">
                            <input id="logo-upload" type="file" wire:model="logo" class="hidden">

                            <div class="flex flex-col items-start gap-2">
                                <label for="logo-upload"
                                    class="inline-flex items-center justify-center px-4 py-2 rounded-full border-0 text-sm font-semibold bg-blue-50 text-blue-700 hover:bg-blue-100 cursor-pointer transition-colors">
                                    Избери фајл
                                </label>

                                <p class="w-full text-sm text-gray-500 break-words">
                                    {{ $logo ? $logo->getClientOriginalName() : 'Нема избрано датотека' }}
                                </p>

                                <p class="text-[11px] text-gray-400 italic">Максимална големина 2MB (JPG, PNG)</p>
                            </div>
                        </div>
                    </div>
                    @error('logo') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                </div>
            </div>

            <div class="flex justify-end pt-4">
                <button type="submit" wire:loading.attr="disabled"
                    class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-12 rounded-xl shadow-lg shadow-blue-200 transition-all transform hover:scale-105 active:scale-95 flex items-center gap-2">
                    <span wire:loading.remove>Зачувај</span>
                    <span wire:loading>Се зачувува...</span>
                    <svg wire:loading class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg"
                        fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4">
                        </circle>
                        <path class="opacity-75" fill="currentColor"
                            d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                        </path>
                    </svg>
                </button>
            </div>
        </form>
    </div>
</div>
