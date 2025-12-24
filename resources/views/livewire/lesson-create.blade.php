<div> {{-- ГЛАВЕН ОБВИТКУВАЧ - МОРА ДА ПОСТОИ --}}
    <div class="p-6 bg-white rounded shadow text-black">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 border-b pb-4 gap-4">
            <div>
                <h2 class="text-xl font-bold text-gray-700">Дневник на часови</h2>
                <p class="text-xs text-gray-400 uppercase tracking-widest">Евиденција на одржани часови</p>
            </div>
        </div>

        @if (session()->has('message'))
            <div class="bg-green-100 border-l-4 border-green-500 p-3 mb-6 text-green-800 rounded shadow-sm">
                {{ session('message') }}
            </div>
        @endif

        <div class="space-y-4 text-black">
            <div class="flex flex-wrap md:flex-nowrap gap-4">
                <div class="w-full md:w-1/3">
                    <label class="block font-bold mb-1 text-xs uppercase text-gray-500">Ученик</label>
                    <select wire:model.live="student_id" class="w-full border border-gray-300 p-2 rounded shadow-sm">
                        <option value="">-- Избери --</option>
                        @foreach($students as $s)
                            <option value="{{ $s->id }}">{{ $s->first_name }} {{ $s->last_name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="w-full md:w-1/3">
                    <label class="block font-bold mb-1 text-xs uppercase text-gray-500">Тип на час</label>
                    <select wire:model.live="lesson_type_id" class="w-full border border-gray-300 p-2 rounded shadow-sm">
                        <option value="">-- Избери --</option>
                        @foreach($lessonTypes as $type)
                            <option value="{{ $type->id }}">{{ $type->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="w-full md:w-1/3">
                    <label class="block font-bold mb-1 text-xs uppercase text-gray-500">Датум</label>
                    <input type="date" wire:model="lesson_date" class="w-full border border-gray-300 p-2 rounded shadow-sm">
                </div>
            </div>

            <div class="flex flex-wrap md:flex-nowrap gap-4 items-end">
                <div class="bg-blue-50 border border-blue-200 px-6 py-2 rounded-xl shadow-sm flex flex-col items-end">
                    <span class="text-[10px] text-blue-500 font-bold uppercase">Цена за изборот</span>
                    <div class="flex items-baseline gap-1">
                        <span class="text-2xl font-black text-blue-800">{{ $suggestedPrice }}</span>
                        <span class="text-sm font-bold text-blue-600">МКД</span>
                    </div>
                </div>
                <div class="w-full md:flex-1">
                    <label class="block font-bold mb-1 text-xs uppercase text-gray-500">Забелешка</label>
                    <input type="text" wire:model="notes" 
                        class="w-full border-gray-300 p-2 rounded shadow-sm" 
                        placeholder="Што работевте денес?">
                </div>
                <div class="w-full md:w-auto">
                    <button wire:click="saveLesson" 
                        class="w-full md:px-10 bg-blue-600 text-white py-2 rounded hover:bg-blue-700 font-bold shadow transition-all">
                        Зачувај
                    </button>
                </div>
            </div>
        </div>

        <div class="mt-12">
            <div class="flex items-center justify-between mb-4 border-b pb-2">
                <h3 class="text-lg font-bold text-gray-700">Последно одржани часови</h3>
                {{-- Ја користиме променливата од пагинацијата за да го покажеме вкупниот број --}}
                <span class="text-[10px] bg-gray-100 text-gray-500 px-2 py-1 rounded uppercase font-bold tracking-tighter">
                    Вкупно запишани: {{ $lessonsLog->total() }}
                </span>
            </div>
            
            <div class="overflow-x-auto border border-gray-100 rounded-xl shadow-sm bg-white">
                <table class="w-full border-collapse bg-white">
                    <thead class="tbg-gray-50">
                    <tr class="text-left text-sm font-semibold text-gray-900">
                        <th class="px-6 py-3">Ученик</th>
                        <th class="px-6 py-3">Тип</th>
                        <th class="px-6 py-3">Датум</th>
                        <th class="px-6 py-3">Забелешка</th>
                        <th class="px-6 py-3">Цена</th>
                        <th class="px-6 py-3">Акции</th> 
                    </tr>
</thead>
<tbody class="text-gray-600 divide-y divide-gray-50 text-sm">
    @foreach($lessonsLog as $log)
        <tr class="hover:bg-blue-50/50 transition-colors {{ $editingLessonId == $log->id ? 'bg-yellow-50' : '' }}">
            <td class="p-4 font-bold text-gray-800">{{ $log->student->first_name }} {{ $log->student->last_name }}</td>
            <td class="p-4 text-[11px]">{{ $log->lessonType->name }}</td>
            <td class="p-4 text-gray-500">{{ \Carbon\Carbon::parse($log->lesson_date)->format('d.m.Y') }}</td>
            <td class="p-4 italic text-gray-400 truncate max-w-[150px]">{{ $log->notes ?: '/' }}</td>
            <td class="p-4 text-right font-black text-blue-600">{{ number_format($log->price_at_time, 0, ',', '.') }} ден.</td>
            
            <td class="p-4 text-center space-x-2">
                <button wire:click="editLesson({{ $log->id }})" class="text-blue-500 hover:text-blue-700">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 inline" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                    </svg>
                </button>
                
                <button wire:confirm="Дали сте сигурни дека сакате да го избришете овој час?" 
                        wire:click="deleteLesson({{ $log->id }})" class="text-red-500 hover:text-red-700">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 inline" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>
                </button>
            </td>
        </tr>
    @endforeach
</tbody>
                </table>
                
                {{-- Копчиња за пагинација --}}
                <div class="p-4 border-t bg-gray-50 rounded-b-xl">
                    {{ $lessonsLog->links() }}
                </div>
            </div>
        </div>
    </div>
</div> {{-- КРАЈ НА ГЛАВНИОТ ОБВИТКУВАЧ --}}