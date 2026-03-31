<div>
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 border-b pb-4 gap-4">
        <div>
            <h2 class="text-xl font-bold text-gray-700">Дневник на часови</h2>
            <p class="text-md text-gray-600">Евиденција на одржани часови</p>
        </div>
    </div>
    {{-- SESSION MESSAGES --}}
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

    {{-- ENTRY FORM --}}
    <div x-data="{ showDetailModal: false }"
        class="text-black bg-white px-4 pt-4 pb-2 rounded-xl border border-gray-100 shadow-sm w-full">
        <div
            class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-[0.9fr_2.35fr_1.7fr_1.1fr_0.95fr_0.78fr_0.78fr_1fr_0.95fr_0.55fr] gap-2.5 items-end">
            <div class="w-full sm:col-span-1 md:col-span-1 lg:col-span-1 2xl:col-auto">
                <label class="block text-xs font-medium text-gray-800 mb-1">Цена</label>
                <div
                    class="h-10 border border-blue-200 bg-blue-50 rounded-lg px-3 inline-flex w-full items-center justify-between whitespace-nowrap">
                    <span class="text-[11px] uppercase tracking-wide font-semibold text-blue-700">ден.</span>
                    <span class="text-sm font-bold text-blue-800">{{ $suggestedPrice }}</span>
                </div>
                <div class="h-3 mt-0.5"></div>
            </div>

            <div class="w-full relative sm:col-span-2 md:col-span-2 lg:col-span-2 xl:col-span-1 2xl:col-auto"
                x-data="{ open: @entangle('showDropdown') }" x-on:click.outside="open = false">
                <label class="block text-xs font-medium text-gray-800 mb-1">Ученик</label>

                <div @click="open = !open"
                    class="w-full h-10 border px-3 rounded-lg shadow-sm text-[13px] cursor-pointer bg-white flex justify-between items-center {{ $errors->has('student_id') ? 'border-red-500' : 'border-gray-300' }}">
                    <span>
                        @if($student_id)
                            @php $selected = \App\Models\Student::find($student_id); @endphp
                            <span class="text-black font-medium">{{ $selected->first_name }}
                                {{ $selected->last_name }}</span>
                        @else
                            <span class="text-gray-400">-- Избери ученик --</span>
                        @endif
                    </span>
                    <svg class="h-4 w-4 text-gray-400 transition-transform" :class="open ? 'rotate-180' : ''"
                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                </div>

                <div x-show="open" x-transition:enter="transition ease-out duration-100"
                    x-transition:enter-start="opacity-0 transform scale-95"
                    x-transition:enter-end="opacity-100 transform scale-100"
                    class="absolute z-50 w-full mt-1 bg-white border border-gray-200 rounded-lg shadow-2xl"
                    style="display: none;">

                    <div class="p-2 border-b bg-gray-50 relative">
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-4 w-4 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                            </div>
                            <input type="text" wire:model.live.debounce.250ms="student_search"
                                class="w-full h-9 pl-9 p-2 border border-gray-300 rounded text-[12px] focus:ring-blue-500 focus:border-blue-500"
                                placeholder="Барај ученик..." @click.stop>
                        </div>
                    </div>

                    <ul class="max-h-60 overflow-y-auto">
                        @forelse($studentsForSelect as $s)
                            <li wire:click="selectStudent({{ $s->id }}); open = false"
                                class="px-3 py-2 hover:bg-blue-600 hover:text-white cursor-pointer text-sm border-b border-gray-50 last:border-0">
                                {{ $s->first_name }} {{ $s->last_name }}
                            </li>
                        @empty
                            <li class="p-4 text-center text-gray-400 text-xs italic">Нема резултати</li>
                        @endforelse
                    </ul>
                </div>

                <div class="h-3 mt-0.5">
                    @error('student_id')
                        <span class="text-[10px] text-red-600 font-bold uppercase block leading-4">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            <div class="w-full sm:col-span-2 md:col-span-2 lg:col-span-2 xl:col-span-1 2xl:col-auto">
                <label class="block text-xs font-medium text-gray-800 mb-1">Тип на час</label>
                <select wire:model.live="lesson_type_id"
                    class="w-full h-10 border px-3 rounded-lg shadow-sm text-[13px] {{ $errors->has('lesson_type_id') ? 'border-red-500' : 'border-gray-300' }}">
                    <option value="">-- Избери --</option>
                    @foreach($lessonTypes as $type)
                        <option value="{{ $type->id }}">{{ str_replace('min', 'мин', $type->name) }}</option>
                    @endforeach
                </select>
                <div class="h-3 mt-0.5">
                    @error('lesson_type_id')
                        <span class="text-[10px] text-red-600 font-bold uppercase block leading-4">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            <div class="w-full sm:col-span-1 md:col-span-1 lg:col-span-1 2xl:col-auto">
                <label class="block text-xs font-medium text-gray-800 mb-1">Датум</label>
                <input type="date" wire:model="lesson_date"
                    class="w-full h-10 border px-3 rounded-lg shadow-sm text-[13px] {{ $errors->has('lesson_date') ? 'border-red-500' : 'border-gray-300' }}">
                <div class="h-3 mt-0.5">
                    @error('lesson_date')
                        <span class="text-[10px] text-red-600 font-bold uppercase block leading-4">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            <div class="w-full sm:col-span-1 md:col-span-1 lg:col-span-1 2xl:col-auto">
                <label class="block text-xs font-medium text-gray-800 mb-1">Статус</label>
                <select wire:model.live="lesson_status"
                    class="w-full h-10 border px-3 rounded-lg shadow-sm text-[13px] {{ $errors->has('lesson_status') ? 'border-red-500' : 'border-gray-300' }}">
                    <option value="held">Одржан</option>
                    <option value="not_held">Неодржан</option>
                </select>
                <div class="h-3 mt-0.5">
                    @error('lesson_status')
                        <span class="text-[10px] text-red-600 font-bold uppercase block leading-4">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            <div class="w-full sm:col-span-1 md:col-span-1 lg:col-span-1 2xl:col-auto">
                <label class="block text-xs font-medium text-gray-800 mb-1">Почеток</label>
                <input type="time" wire:model.live="start_time"
                    class="w-full h-10 border px-2 rounded-lg shadow-sm text-[13px] text-right {{ $errors->has('start_time') ? 'border-red-500' : 'border-gray-300' }}">
                <div class="h-3 mt-0.5">
                    @error('start_time')
                        <span class="text-[10px] text-red-600 font-bold uppercase block leading-4">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            <div class="w-full sm:col-span-1 md:col-span-1 lg:col-span-1 2xl:col-auto">
                <label class="block text-xs font-medium text-gray-800 mb-1">Крај</label>
                <input type="time" wire:model="end_time"
                    class="w-full h-10 border pl-2 pr-1 rounded-lg shadow-sm text-[13px] {{ $errors->has('end_time') ? 'border-red-500' : 'border-gray-300' }}">
                <div class="h-0 sm:h-3 mt-0 sm:mt-0.5">
                    @error('end_time')
                        <span class="text-[10px] text-red-600 font-bold uppercase block leading-4">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            <div class="w-full sm:col-span-2 md:col-span-2 lg:col-span-2 xl:col-span-1 2xl:col-auto">
                <label class="hidden sm:block text-xs font-medium text-transparent mb-1 select-none">.</label>
                <button type="button" @click="showDetailModal = true"
                    class="h-10 w-full px-3 text-sm rounded-lg border border-slate-300 text-slate-700 bg-white hover:bg-slate-50 transition-all whitespace-nowrap inline-flex items-center justify-center gap-2 font-medium">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6M9 16h6M9 8h6" />
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M5 4h10l4 4v12a1 1 0 0 1-1 1H6a1 1 0 0 1-1-1V4z" />
                    </svg>
                    <span>Забелешка +</span>
                </button>
                <div class="h-0 sm:h-3 mt-0 sm:mt-0.5"></div>
            </div>

            <div class="w-full sm:col-span-1 md:col-span-1 lg:col-span-1 2xl:col-auto">
                <label class="hidden sm:block text-xs font-medium text-transparent mb-1 select-none">.</label>
                <button wire:click="saveLesson"
                    class="h-10 w-full px-3 text-sm text-white rounded-lg font-bold shadow-sm transition-all whitespace-nowrap {{ $editingLessonId ? 'bg-orange-500 hover:bg-orange-600' : 'bg-blue-600 hover:bg-blue-700' }}">
                    {{ $editingLessonId ? 'Ажурирај' : 'Зачувај' }}
                </button>
                <div class="h-0 sm:h-3 mt-0 sm:mt-0.5"></div>
            </div>

            <div class="w-full sm:col-span-1 md:col-span-1 lg:col-span-1 xl:max-w-[78px] 2xl:col-auto">
                <label class="hidden sm:block text-xs font-medium text-transparent mb-1 select-none">.</label>
                <button wire:click="resetFields"
                    class="h-10 w-full bg-gray-200 hover:bg-gray-300 text-gray-700 rounded-lg font-bold shadow-sm transition-all flex items-center justify-center"
                    title="Исчисти полиња">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                    </svg>
                </button>
                <div class="h-0 sm:h-3 mt-0 sm:mt-0.5"></div>
            </div>
        </div>

        <div x-show="showDetailModal" x-transition.opacity style="display:none;"
            class="fixed inset-0 z-50 flex items-center justify-center p-4">
            <div class="absolute inset-0 bg-gray-900/50 backdrop-blur-sm" @click="showDetailModal = false"></div>

            <div
                class="relative w-full max-w-2xl bg-white rounded-3xl shadow-2xl border border-gray-100 overflow-hidden z-50">
                <div class="px-6 py-5 border-b border-gray-100 bg-gray-50/50 flex justify-between items-center">
                    <div>
                        <h3 class="text-lg font-bold text-gray-900 tracking-tight">Детален внес</h3>
                        <p class="text-sm text-gray-500 mt-1 font-medium">Додади дополнителна забелешка за часот.</p>
                    </div>
                    <button type="button" @click="showDetailModal = false"
                        class="text-gray-400 hover:text-gray-600 hover:bg-gray-100 p-2 rounded-full transition">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M18 6L6 18M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                <div class="px-6 py-6">
                    <label class="block text-xs font-medium text-gray-800 mb-1">Забелешка</label>
                    <textarea wire:model="notes" rows="6"
                        class="w-full border border-gray-300 px-3 py-2 rounded-lg shadow-sm text-sm resize-none"
                        placeholder="Што работевте?"></textarea>
                </div>

                <div class="px-6 py-4 bg-gray-50 border-t border-gray-100 flex justify-end gap-2">
                    <button type="button" @click="showDetailModal = false"
                        class="px-6 py-3 text-gray-500 font-semibold hover:text-gray-700">
                        Откажи
                    </button>
                    <button type="button" @click="showDetailModal = false"
                        class="h-9 px-5 rounded-lg bg-blue-600 hover:bg-blue-700 text-white font-semibold shadow-sm transition">
                        Готово
                    </button>
                </div>
            </div>
        </div>
    </div>
    <div class="bg-white p-4 mt-10 rounded-xl shadow">
        {{-- FILTERS --}}
        <div class="bg-gray-50 p-4 rounded-xl border border-gray-200">
            <div class="flex flex-wrap items-end gap-3 lg:gap-4">
                <div class="w-full sm:w-[430px]">
                    <label class="block text-[14px] font-normal text-gray-900 mb-1">Барај ученик</label>
                    <div class="relative">
                        <input type="text" wire:model.live="search" placeholder="Име..."
                            class="w-full border-gray-300 rounded-lg p-2 pr-9 text-sm">
                        <svg xmlns="http://www.w3.org/2000/svg"
                            class="h-4 w-4 absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-400 pointer-events-none"
                            fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M21 21l-4.35-4.35M17 11A6 6 0 1111 5a6 6 0 016 6z" />
                        </svg>
                    </div>
                </div>
                <div class="w-full sm:w-[240px]">
                    <label class="block text-[14px] font-normal text-gray-900 mb-1">Тип на час</label>
                    <div class="relative">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 absolute left-3 top-3 text-gray-400"
                            fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2a1 1 0 01-.293.707L13 12.414V19a1 1 0 01-.553.894l-3 1.5A1 1 0 018 21v-8.586L3.293 6.707A1 1 0 013 6V4z" />
                        </svg>
                        <select wire:model.live="filter_type"
                            class="w-full border-gray-300 rounded-lg p-2 pl-9 text-sm">
                            <option value="">Сите типови</option>
                            @foreach($lessonTypes as $type)
                                <option value="{{ $type->id }}">{{ str_replace('min', 'мин', $type->name) }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="w-full sm:w-[220px]">
                    <label class="block text-[14px] font-normal text-gray-900 mb-1">Статус</label>
                    <div class="relative">
                        <select wire:model.live="filter_status"
                            class="w-full border-gray-300 rounded-lg p-2 text-sm">
                            <option value="">Сите статуси</option>
                            <option value="held">Одржан</option>
                            <option value="not_held">Неодржан</option>
                        </select>
                    </div>
                </div>
                <div class="w-full sm:w-[145px]"><label class="block text-[14px] font-normal text-gray-900 mb-1">Период
                        од</label><input type="date" wire:model.live="filter_from_date"
                        class="w-full border-gray-300 rounded-lg px-2 py-2 text-sm"></div>
                <div class="w-full sm:w-[145px]"><label class="block text-[14px] font-normal text-gray-900 mb-1">Период
                        до</label><input type="date" wire:model.live="filter_to_date"
                        class="w-full border-gray-300 rounded-lg px-2 py-2 text-sm"></div>

                {{-- EXPORT BUTTON --}}
                <div class="w-full sm:w-auto sm:ml-auto">
                    <button wire:click="exportExcel"
                        class="w-full sm:w-auto bg-green-600 hover:bg-green-700 text-white font-bold h-9 px-3 rounded-lg flex items-center justify-center gap-1.5 shadow-sm transition-all">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        Excel
                    </button>
                </div>
            </div>
        </div>

        {{-- TABLE --}}
        <div class="mt-4 overflow-x-auto border border-gray-100 rounded-xl shadow-sm bg-white">
            <table class="w-full border-collapse bg-white">
                <thead class="bg-gray-50 text-left font-normal  text-sm text-gray-900">
                    <tr>
                        <th class="px-2 py-3">Ученик</th>
                        <th class="px-2 py-3">Тип</th>
                        <th class="px-2 py-3">Датум / Време</th>
                        <th class="px-2 py-3">Статус</th>
                        <th class="px-2 py-3">Цена</th>
                        <th class="px-2 py-3">Забелешка</th>
                        <th class="px-2 py-3 text-right">Акции</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50 text-gray-600">
                    @foreach($lessonsLog as $log)
                        <tr
                            class="transition-colors {{ $editingLessonId == $log->id ? 'bg-orange-50' : 'hover:bg-blue-50/50' }}">
                            <td class="px-2 py-2 font-medium text-gray-800">{{ $log->student->first_name }}
                                {{ $log->student->last_name }}
                            </td>
                            <td class="px-2 py-2 text-[14px]">{{ str_replace('min', 'мин', $log->lessonType->name) }}</td>
                            <td class="px-2 py-2 text-black">
                                <div class="font-normal">{{ \Carbon\Carbon::parse($log->lesson_date)->format('d.m.Y') }}
                                </div>
                                <div class="text-[11px] text-blue-800 font-medium mt-0.5 flex items-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 mr-1" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    @if($log->start_time && $log->end_time)
                                        {{ \Carbon\Carbon::parse($log->start_time)->format('H:i') }} -
                                        {{ \Carbon\Carbon::parse($log->end_time)->format('H:i') }}
                                    @else
                                        Нема време
                                    @endif
                                </div>
                            </td>
                            <td class="px-2 py-2 text-sm">
                                @if($log->lesson_status === 'held')
                                    <span class="inline-flex items-center gap-1.5 py-1 px-3 rounded-full text-xs bg-green-100 text-green-700 border border-green-200">Одржан</span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 py-1 px-3 rounded-full text-xs bg-amber-100 text-amber-700 border border-amber-200">Неодржан</span>
                                @endif
                            </td>
                            <td class="px-2 py-2 text-md text-gray-800">
                                {{ number_format($log->price_at_time, 0, ',', '.') }} ден.
                            </td>
                            <td class="px-2 py-2 italic text-gray-400">{{ $log->notes ?: '/' }}</td>
                            <td class="p-4 text-right space-x-2">
                                <button wire:click="editLesson({{ $log->id }})" title="Измени" aria-label="Измени"
                                    class="inline-flex items-center gap-2 px-3 py-1 text-sm font-medium rounded-md text-blue-600 md:bg-blue-50 md:hover:bg-blue-100 md:hover:text-blue-800 transition focus:outline-none">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 inline" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                    </svg>
                                    <span class="hidden md:inline">Измени</span>
                                </button>

                                <button type="button" onclick="confirmDelete({{ $log->id }})" title="Избриши"
                                    aria-label="Избриши"
                                    class="inline-flex items-center gap-2 px-3 py-1 text-sm font-medium rounded-md text-red-600 md:bg-red-50 md:hover:bg-red-100 md:hover:text-red-800 transition focus:outline-none">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 inline" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                    <span class="hidden md:inline">Избриши</span>
                                </button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot class="bg-blue-50">
                    <tr>
                        <td colspan="4" class="p-1 text-right font-semibold text-gray-700 uppercase text-xs">Вкупно:
                        </td>
                        <td class="p-1 font-semibold text-blue-800 text-lg">
                            {{ number_format($totalAmount, 0, ',', '.') }} ден.
                        </td>
                        <td colspan="2"></td>
                    </tr>
                </tfoot>
            </table>
            @if ($lessonsLog && $lessonsLog->hasPages())
                <div class="p-2 border-t bg-gray-50 rounded-b-xl">
                    {{ $lessonsLog->links() }}
                </div>
            @endif
        </div>
    </div>
</div>

<script>
    function confirmDelete(id) {
        Swal.fire({
            title: 'Дали сте сигурни?',
            text: "Ова дејство не може да се врати!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc2626',
            cancelButtonColor: '#2563eb',
            confirmButtonText: 'Да, избриши го!',
            cancelButtonText: 'Откажи',
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                // Повикување на Livewire функцијата
                @this.call('deleteLesson', id);
            }
        })
    }
</script>