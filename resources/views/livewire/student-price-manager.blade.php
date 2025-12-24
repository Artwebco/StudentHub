<div class="p-6 bg-white rounded shadow text-black">
    <h2 class="text-xl font-bold mb-4">Поставување цени по ученик</h2>

    @if (session()->has('message'))
        <div x-data="{ show: true }" x-init="setTimeout(() => show = false, 5000)" x-show="show"
            x-transition:leave="transition ease-in duration-500" x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="flex items-center justify-between bg-green-100 border-l-4 border-green-500 p-3 mb-6 text-green-800 rounded shadow-sm">

            <div class="flex items-center">
                <svg class="h-5 w-5 mr-2 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd"
                        d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L10 8.586 7.707 6.293a1 1 0 00-1.414 1.414L8.586 10l-2.293 2.293a1 1 0 001.414 1.414L10 11.414l2.293 2.293a1 1 0 001.414-1.414L11.414 10l2.293-2.293z"
                        clip-rule="evenodd" />
                </svg>
                <span class="text-sm font-medium">{{ session('message') }}</span>
            </div>

            <button @click="show = false" class="text-green-500 hover:text-green-800 transition-colors">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
    @endif

    <select wire:model.live="selectedStudent" class="w-full p-2 border mb-6 rounded">
        <option value="">-- Избери ученик --</option>
        @foreach($students as $s)
            <option value="{{ $s->id }}">{{ $s->first_name }} {{ $s->last_name }}</option>
        @endforeach
    </select>

    @if($selectedStudent)
        <table class="w-full border-collapse">
            <thead>
                <tr class="bg-gray-100 text-left">
                    <th class="p-2 border">Тип на час</th>
                    <th class="p-2 border">Цена за овој ученик (ден)</th>
                </tr>
            </thead>
            <tbody>
                @foreach($lessonTypes as $type)
                    <tr>
                        <td class="p-2 border">{{ $type->name }} ({{ $type->duration }} мин)</td>
                        <td class="p-2 border">
                            <input type="number" wire:model="prices.{{ $type->id }}" class="w-full p-1 border rounded">
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <button wire:click="save" class="mt-4 bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
            Зачувај цени
        </button>
    @endif
</div>
