<div class="p-6">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold text-gray-800">Управување со Фактури</h2>
        <button wire:click="$set('showCreateModal', true)"
            class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition">
            + Креирај Нова Фактура
        </button>
    </div>

    @if (session()->has('message'))
        <div class="mb-4 p-4 bg-green-100 text-green-700 rounded-lg">{{ session('message') }}</div>
    @endif
    @if (session()->has('error'))
        <div class="mb-4 p-4 bg-red-100 text-red-700 rounded-lg">{{ session('error') }}</div>
    @endif

    <div class="bg-white rounded-xl shadow overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Бр. Фактура</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Ученик</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Износ</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Статус</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Акции</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @foreach($invoices as $invoice)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                            {{ $invoice->invoice_number }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $invoice->student->first_name }}
                            {{ $invoice->student->last_name }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ number_format($invoice->total_amount, 2) }} ден.
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <button wire:click="togglePaid({{ $invoice->id }})"
                                class="px-2 py-1 text-xs rounded-full {{ $invoice->is_paid ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                {{ $invoice->is_paid ? 'Платено' : 'Неплатено' }}
                            </button>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <div class="flex justify-end gap-3">
                                <button wire:click="downloadInvoice({{ $invoice->id }})"
                                    class="text-indigo-600 hover:text-indigo-900 flex items-center gap-1">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                                        stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M4 16v1a2 2 0 002 2h12a2 2 0 002-2v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                                    </svg>
                                    Превземи
                                </button>
                                <button onclick="confirm('Сигурно?') || event.stopImmediatePropagation()"
                                    wire:click="deleteInvoice({{ $invoice->id }})"
                                    class="text-red-600 hover:text-red-900">Избриши</button>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        <div class="p-4 border-t">
            {{ $invoices->links() }}
        </div>
    </div>

    @if($showCreateModal)
        <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50">
            <div class="bg-white rounded-xl p-6 max-w-md w-full">
                <h3 class="text-lg font-bold mb-4">Нова Фактура</h3>
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Ученик</label>
                        <select wire:model="student_id" class="w-full border-gray-300 rounded-lg">
                            <option value="">Избери ученик</option>
                            @foreach($students as $student)
                                <option value="{{ $student->id }}">{{ $student->first_name }} {{ $student->last_name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Од датум</label>
                            <input type="date" wire:model="date_from" class="w-full border-gray-300 rounded-lg">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">До датум</label>
                            <input type="date" wire:model="date_to" class="w-full border-gray-300 rounded-lg">
                        </div>
                    </div>
                </div>
                <div class="flex justify-end gap-3 mt-6">
                    <button wire:click="$set('showCreateModal', false)"
                        class="text-gray-500 hover:text-gray-700">Откажи</button>
                    <button wire:click="createInvoice"
                        class="bg-blue-600 text-white px-4 py-2 rounded-lg">Генерирај</button>
                </div>
            </div>
        </div>
    @endif
</div>
