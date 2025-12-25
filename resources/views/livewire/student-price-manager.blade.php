<div class="p-6 bg-white rounded shadow text-black">
    <h2 class="text-xl font-bold mb-4">Поставување цени по ученик</h2>

    @if (session()->has('message'))
        <div class="p-3 mb-4 bg-green-100 text-green-800 rounded border-l-4 border-green-500">
            {{ session('message') }}
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
                    <tr wire:key="type-{{ $type->id }}">
                        <td class="p-2 border">{{ $type->name }} ({{ $type->duration }} мин)</td>
                        <td class="p-2 border">
                            <input type="number" wire:model="prices.{{ $type->id }}" class="w-full p-1 border rounded">
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <button wire:click="save" class="mt-4 bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 font-bold">
            Зачувај цени
        </button>
    @endif
</div>
