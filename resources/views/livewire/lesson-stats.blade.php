<div>
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 border-b pb-4 gap-4">
        <div>
            <h2 class="text-xl font-bold text-gray-700">My Statistics</h2>
            <p class="text-md text-gray-600">Overview of lessons and invoices</p>
        </div>
    </div>

    <div class="bg-white p-4 mt-4 rounded-xl shadow">
        <div class="bg-gray-50 p-4 rounded-xl border border-gray-200">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3">
                <div class="flex flex-wrap items-center gap-2 w-full md:w-auto">
                    <button wire:click="setTab('lessons')"
                        class="flex items-center gap-2 px-4 py-1.5 rounded-lg text-sm font-semibold transition-all {{ $tab === 'lessons' ? 'bg-green-600 text-white border border-green-600 shadow-sm' : 'bg-white text-gray-500 border border-gray-200 hover:bg-green-50 hover:text-green-700 hover:border-green-200' }}">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253">
                            </path>
                        </svg>
                        Lessons
                    </button>

                    <button wire:click="setTab('invoices')"
                        class="flex items-center gap-2 px-4 py-1.5 rounded-lg text-sm font-semibold transition-all {{ $tab === 'invoices' ? 'bg-green-600 text-white border border-green-600 shadow-sm' : 'bg-white text-gray-500 border border-gray-200 hover:bg-green-50 hover:text-green-700 hover:border-green-200' }}">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                            </path>
                        </svg>
                        Invoices
                    </button>

                    <div class="relative w-full sm:w-72 md:w-80 max-w-full">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                        </div>
                        <input wire:model.live="search" type="text"
                            placeholder="{{ $tab === 'lessons' ? 'Search by date...' : 'Search by invoice number...' }}"
                            class="block w-full pl-9 pr-3 py-1.5 border border-gray-200 rounded-lg text-md focus:ring-blue-500 focus:border-blue-500 transition-all">
                    </div>

                    <select wire:model.live="status"
                        class="block w-auto py-1.5 pl-3 pr-8 border border-gray-200 rounded-lg text-md bg-white cursor-pointer focus:ring-blue-500">
                        <option value="all">All statuses</option>
                        @if($tab === 'lessons')
                            <option value="held">Held</option>
                            <option value="not_held">Not held</option>
                        @elseif($tab === 'invoices')
                            <option value="paid">Paid</option>
                            <option value="unpaid">Unpaid</option>
                        @endif
                    </select>

                    <button wire:click="$set('search', '')"
                        class="px-2 py-2 hover:bg-gray-300 text-gray-700 rounded-lg font-bold shadow transition-all flex items-center justify-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15">
                            </path>
                        </svg>
                    </button>
                </div>

                @if($tab === 'invoices' && $unpaid_amount > 0)
                    <div class="px-4 py-1.5 bg-red-50 border border-red-100 rounded-lg w-full md:w-auto md:ml-4 flex-shrink-0">
                        <span class="block text-xs font-bold text-red-700 text-center md:text-left">
                            Total unpaid: {{ number_format($unpaid_amount, 0, ',', '.') }} den.
                        </span>
                    </div>
                @endif
            </div>
        </div>

        <div class="mt-4 overflow-x-auto border border-gray-100 rounded-xl shadow-sm bg-white">
            @if($tab === 'lessons')
                <table class="w-full border-collapse bg-white">
                    <thead class="bg-gray-50 text-left font-semibold text-sm text-gray-900">
                        <tr>
                            <th class="px-2 py-3">Date</th>
                            <th class="px-2 py-3">Time</th>
                            <th class="px-2 py-3">Lesson Type</th>
                            <th class="px-2 py-3">Note</th>
                            <th class="px-2 py-3 text-right">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 bg-white">
                        @forelse($lessons as $lesson)
                            <tr class="hover:bg-gray-50 transition-all">
                                <td class="px-2 py-2 whitespace-nowrap text-md font-medium text-gray-900">
                                    {{ \Carbon\Carbon::parse($lesson->lesson_date)->format('d.m.Y') }}
                                </td>
                                <td class="px-2 py-2 whitespace-nowrap text-md text-gray-600">
                                    {{ $lesson->start_time }} - {{ $lesson->end_time }}
                                </td>
                                <td class="px-2 py-2 whitespace-nowrap text-md text-gray-600">
                                    {{ $lesson->lessonType->name }}
                                </td>
                                <td class="px-2 py-2 text-md text-gray-600">
                                    {{ $lesson->notes ?: '-' }}
                                </td>
                                <td class="px-2 py-2 whitespace-nowrap text-right">
                                    @if($lesson->lesson_status === 'held')
                                        <span
                                            class="inline-flex items-center gap-1.5 py-1 px-3 rounded-full text-xs bg-green-100 text-green-700 border border-green-200">
                                            Held
                                        </span>
                                    @else
                                        <span
                                            class="inline-flex items-center gap-1.5 py-1 px-3 rounded-full text-xs bg-amber-100 text-amber-700 border border-amber-200">
                                            Not held
                                        </span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-12 text-center text-gray-400">No records found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>

                @if ($lessons && $lessons->hasPages())
                    <div class="p-2 border-t bg-gray-50">
                        {{ $lessons->links() }}
                    </div>
                @endif
            @else
                <table class="w-full border-collapse bg-white">
                    <thead class="bg-gray-50 text-left font-semibold text-sm text-gray-900">
                        <tr>
                            <th class="px-2 py-3">Invoice No.</th>
                            <th class="px-2 py-3">Period</th>
                            <th class="px-2 py-3 text-center">Lessons</th>
                            <th class="px-2 py-3">Amount</th>
                            <th class="px-2 py-3">Status</th>
                            <th class="px-2 py-3 text-right tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 bg-white">
                        @forelse($invoices as $invoice)
                            <tr class="hover:bg-gray-50 transition {{ $invoice->status === 'cancelled' ? 'bg-gray-50/50' : '' }}">
                                <td class="px-2 py-2 whitespace-nowrap text-md font-medium text-gray-900">
                                    {{ $invoice->invoice_number }}
                                </td>
                                <td class="px-2 py-2 text-sm text-gray-600">
                                    @if($invoice->invoice_type === 'service')
                                        <span class="text-blue-600 font-medium italic">Service: {{ $invoice->service_description }}</span>
                                    @else
                                        {{ \Carbon\Carbon::parse($invoice->date_from)->format('d.m') }} -
                                        {{ \Carbon\Carbon::parse($invoice->date_to)->format('d.m.Y') }}
                                    @endif
                                </td>
                                <td class="px-2 py-2 text-center">
                                    @if($invoice->student_id)
                                        <span
                                            class="inline-flex items-center justify-center px-2.5 py-0.5 rounded-full text-sm font-semibold bg-blue-50 text-blue-700 border border-blue-100">
                                            {{ number_format($invoice->quantity, 0) }}
                                        </span>
                                    @else
                                        <span class="text-gray-300">/</span>
                                    @endif
                                </td>
                                <td class="px-2 py-2 whitespace-nowrap text-md text-gray-800">
                                    {{ number_format($invoice->total_amount, 0, ',', '.') }} den.
                                </td>
                                <td class="px-2 py-2 whitespace-nowrap text-md font-medium">
                                    @if($invoice->status === 'paid')
                                        <span class="inline-flex items-center gap-1.5 py-1 px-3 rounded-full text-xs bg-green-100 text-green-600 border border-green-200">Paid</span>
                                    @elseif($invoice->status === 'cancelled')
                                        <span class="inline-flex items-center gap-1.5 py-1 px-3 rounded-full text-xs bg-red-100 text-red-700 border border-red-200">Cancelled</span>
                                    @else
                                        <span class="inline-flex items-center gap-1.5 py-1 px-3 rounded-full text-xs bg-slate-100 text-slate-700 border border-slate-200">Unpaid</span>
                                    @endif
                                </td>
                                <td class="px-2 py-2 whitespace-nowrap text-right text-sm font-medium">
                                    <div class="flex justify-end gap-2">
                                        @if($invoice->status !== 'cancelled')
                                            <a href="{{ route('student.invoice-preview', $invoice->id) }}" target="_blank"
                                                rel="noopener noreferrer"
                                                class="p-2 text-blue-600 bg-blue-50 hover:bg-blue-100 rounded-lg transition-all inline-flex items-center justify-center"
                                                title="Preview">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
                                                    viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7-1.274 4.057-5.065 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                </svg>
                                            </a>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center text-gray-400">No invoices found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>

                @if ($invoices && $invoices->hasPages())
                    <div class="p-2 border-t bg-gray-50">
                        {{ $invoices->links() }}
                    </div>
                @endif
            @endif
        </div>
    </div>
</div>
