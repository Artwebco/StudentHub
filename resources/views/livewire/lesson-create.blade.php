<div>
    <div class="p-6 bg-white rounded shadow text-black">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 border-b pb-4 gap-4">
            <div>
                <h2 class="text-xl font-bold text-gray-700">Дневник на часови</h2>
                <p class="text-xs text-gray-400 uppercase tracking-widest">Евиденција на одржани часови</p>
            </div>
        </div>
        {{-- SESSION MESSAGES --}}
        @if (session()->has('message'))
            <div x-data="{ show: true }" x-init="setTimeout(() => show = false, 5000)" x-show="show" class="flex items-center justify-between bg-green-100 border-l-4 border-green-500 p-3 mb-6 text-green-800 rounded shadow-sm">
                <div class="flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span class="text-sm font-medium">{{ session('message') }}</span>
                </div>
                <button @click="show = false" class="text-green-500 hover:text-green-800">✕</button>
            </div>
        @endif

        {{-- ENTRY FORM --}}
        <div class="space-y-4 text-black bg-gray-50/50 p-4 rounded-xl border border-gray-100">
            <div class="flex flex-wrap md:flex-nowrap gap-4">
                {{-- Student --}}
                <div class="w-full md:w-1/3">
                    <label class="block font-bold mb-1 text-xs uppercase text-gray-500">Ученик</label>
                    <select wire:model.live="student_id" class="w-full border p-2 rounded shadow-sm @error('student_id') border-red-500 @else border-gray-300 @enderror">
                        <option value="">-- Избери --</option>
                        @foreach($students as $s)
                            <option value="{{ $s->id }}">{{ $s->first_name }} {{ $s->last_name }}</option>
                        @endforeach
                    </select>
                    @error('student_id') <span class="text-[10px] text-red-600 font-bold uppercase mt-1 block">{{ $message }}</span> @enderror
                </div>

                {{-- Class type --}}
                <div class="w-full md:w-1/3">
                    <label class="block font-bold mb-1 text-xs uppercase text-gray-500">Тип на час</label>
                    <select wire:model.live="lesson_type_id" class="w-full border p-2 rounded shadow-sm @error('lesson_type_id') border-red-500 @else border-gray-300 @enderror">
                        <option value="">-- Избери --</option>
                        @foreach($lessonTypes as $type)
                            <option value="{{ $type->id }}">{{ str_replace('min', 'мин', $type->name) }}</option>
                        @endforeach
                    </select>
                    @error('lesson_type_id') <span class="text-[10px] text-red-600 font-bold uppercase mt-1 block">{{ $message }}</span> @enderror
                </div>

                {{-- Date and time --}}
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block font-bold mb-1 text-[10px] uppercase text-gray-500">Датум</label>
                        <input type="date" wire:model="lesson_date" class="w-full border border-gray-300 p-2 rounded shadow-sm text-sm @error('lesson_date') border-red-500 @else border-gray-300 @enderror"">
                         @error('lesson_date') <span class="text-[10px] text-red-600 font-bold uppercase mt-1 block">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block font-bold mb-1 text-[10px] uppercase text-blue-600">Почеток</label>
                        <input type="time" wire:model.live="start_time" class="w-full border p-2 rounded shadow-sm text-sm @error('start_time') border-red-500 @else border-gray-300 @enderror">
                        @error('start_time') <span class="text-[10px] text-red-600 font-bold uppercase mt-1 block">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block font-bold mb-1 text-[10px] uppercase text-blue-600">Крај</label>
                        <input type="time" wire:model="end_time" class="w-full border p-2 rounded shadow-sm text-sm @error('end_time') border-red-500 @else border-gray-300 @enderror">
                        @error('end_time') <span class="text-[10px] text-red-600 font-bold uppercase mt-1 block">{{ $message }}</span> @enderror
                    </div>
                </div>
            </div>

            <div class="flex flex-wrap md:flex-nowrap gap-4 items-end">
                <div class="bg-blue-50 border border-blue-200 px-6 py-2 rounded-xl shadow-sm flex flex-col items-end min-w-[140px]">
                    <span class="text-[10px] text-blue-500 font-bold uppercase">Цена</span>
                    <div class="flex items-baseline gap-1">
                        <span class="text-2xl font-black text-blue-800">{{ $suggestedPrice }}</span>
                        <span class="text-sm font-bold text-blue-600">ден.</span>
                    </div>
                </div>
                <div class="w-full md:flex-1">
                    <label class="block font-bold mb-1 text-xs uppercase text-gray-500">Забелешка</label>
                    <input type="text" wire:model="notes" class="w-full border border-gray-300 p-2 rounded shadow-sm" placeholder="Што работевте?">
                </div>
                <div class="w-full md:w-auto flex items-end gap-2">
                    {{-- BUTTON (Save/Edit) --}}
                    <button wire:click="saveLesson" class="flex-1 md:px-8 text-white py-2 rounded font-bold shadow transition-all {{ $editingLessonId ? 'bg-orange-500 hover:bg-orange-600' : 'bg-blue-600 hover:bg-blue-700' }}">
                        {{ $editingLessonId ? 'Ажурирај' : 'Зачувај' }}
                    </button>

                    {{-- RESET BUTTON --}}
                    <button wire:click="resetFields" class="px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-700 rounded font-bold shadow transition-all flex items-center justify-center" title="Исчисти полиња">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                        </svg>
                        <span class="ml-1 md:hidden">Ресетирај</span>
                    </button>
                </div>
            </div>
        </div>

        {{-- FILTERS --}}
        <div class="mt-10 bg-gray-50 p-4 rounded-xl border border-gray-200">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <label class="block text-[10px] font-bold uppercase text-gray-400 mb-1">Барај ученик</label>
                    <div class="relative">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 absolute left-3 top-3 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35M17 11A6 6 0 1111 5a6 6 0 016 6z" />
                        </svg>
                        <input type="text" wire:model.live="search" placeholder="Име..." class="w-full border-gray-300 rounded-lg p-2 pl-9 text-sm">
                    </div>
                </div>
                <div>
                    <label class="block text-[10px] font-bold uppercase text-gray-400 mb-1">Тип на час</label>
                    <div class="relative">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 absolute left-3 top-3 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2a1 1 0 01-.293.707L13 12.414V19a1 1 0 01-.553.894l-3 1.5A1 1 0 018 21v-8.586L3.293 6.707A1 1 0 013 6V4z" />
                        </svg>
                        <select wire:model.live="filter_type" class="w-full border-gray-300 rounded-lg p-2 pl-9 text-sm">
                            <option value="">Сите типови</option>
                            @foreach($lessonTypes as $type)
                                <option value="{{ $type->id }}">{{ str_replace('min', 'мин', $type->name) }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div>
                    <label class="sr-only">Од датум</label>
                    <div>
                        <input type="date" wire:model.live="filter_from_date" class="w-full border-gray-300 rounded-lg p-2 mt-5 text-sm" placeholder="Од датум">
                    </div>
                </div>
                <div>
                    <label class="sr-only">До датум</label>
                    <div>
                        <input type="date" wire:model.live="filter_to_date" class="w-full border-gray-300 rounded-lg p-2 mt-5 text-sm" placeholder="До датум">
                    </div>
                </div>
            </div>
        </div>

        {{-- TABLE --}}
        <div class="mt-4 overflow-x-auto border border-gray-100 rounded-xl shadow-sm bg-white">
            <table class="w-full border-collapse bg-white text-sm">
                <thead class="bg-gray-50 text-left font-semibold text-gray-900">
                    <tr>
                        <th class="px-6 py-4">Ученик</th>
                        <th class="px-6 py-3">Тип</th>
                        <th class="px-6 py-3">Датум / Време</th>
                        <th class="px-6 py-3">Забелешка</th>
                        <th class="px-6 py-3 text-right">Цена</th>
                        <th class="px-6 py-3 text-right">Акции</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50 text-gray-600">
                    @foreach($lessonsLog as $log)
                        <tr class="hover:bg-blue-50/50 transition-colors">
                            <td class="p-4 font-bold text-gray-800">{{ $log->student->first_name }} {{ $log->student->last_name }}</td>
                            <td class="p-4 text-[11px] uppercase">{{ str_replace('min', 'мин', $log->lessonType->name) }}</td>
                            <td class="p-3 text-black">
                                <div class="font-bold">{{ \Carbon\Carbon::parse($log->lesson_date)->format('d.m.Y') }}</div>
                                <div class="text-[11px] text-blue-500 font-medium mt-0.5 flex items-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                    {{ \Carbon\Carbon::parse($log->start_time)->format('H:i') }} - {{ \Carbon\Carbon::parse($log->end_time)->format('H:i') }}
                                </div>
                            </td>
                            <td class="p-4 italic text-gray-400">{{ $log->notes ?: '/' }}</td>
                            <td class="p-4 text-right font-black text-blue-600">{{ number_format($log->price_at_time, 0, ',', '.') }} ден.</td>
                            <td class="p-4 text-right space-x-2">
                                <button wire:click="editLesson({{ $log->id }})" class="text-blue-500 hover:text-blue-700">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 inline" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
                                </button>
                                <button wire:confirm="Дали сте сигурни?" wire:click="deleteLesson({{ $log->id }})" class="text-red-500 hover:text-red-700">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 inline" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                </button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot class="bg-blue-50">
                    <tr>
                        <td colspan="4" class="p-4 text-right font-bold text-gray-700 uppercase text-xs">Вкупно:</td>
                        <td class="p-4 text-right font-black text-blue-800 text-lg">{{ number_format($totalAmount, 0, ',', '.') }} ден.</td>
                        <td></td>
                    </tr>
                </tfoot>
            </table>
            <div class="p-4 border-t bg-gray-50 rounded-b-xl">{{ $lessonsLog->links() }}</div>
        </div>
    </div>
</div>
