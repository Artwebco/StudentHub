<div>
    {{-- Header --}}
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 border-b pb-4 gap-4">
        <div>
            <h2 class="text-xl font-bold text-gray-700">{{ __('admin.invoices.title') }}</h2>
            <p class="text-md text-gray-600">{{ __('admin.invoices.subtitle') }}</p>
        </div>
        <button wire:click="$set('showCreateModal', true)"
            class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded-xl shadow-lg shadow-blue-200 transition-all transform hover:scale-105 active:scale-95 flex items-center gap-2">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                stroke="currentColor" stroke-width="2.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
            </svg>
            {{ __('admin.invoices.create_invoice') }}
        </button>
    </div>

    @if (session()->has('message'))
        <x-flash-message :message="session('message')" class="mb-4" />
    @endif

    @if (session()->has('error'))
        <x-flash-message type="error" :message="session('error')" class="mb-4" />
    @endif


    <div class="bg-white p-4 mt-4 rounded-xl shadow">
        {{-- НОВ ДЕЛ: ФИЛТРИ --}}
        <div class="bg-gray-50 p-4 rounded-xl border border-gray-200">
            {{-- УЛТРА КОМПАКТНИ ФИЛТРИ --}}
            <div class="flex flex-col gap-2 sm:flex-row sm:items-center">

                {{-- Пребарување --}}
                <div class="relative w-full sm:w-80 order-1">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>
                    <input type="text" wire:model.live="search" placeholder="{{ __('admin.invoices.search_placeholder') }}"
                        class="block w-full pl-9 pr-3 py-1.5 border border-gray-200 rounded-lg text-md focus:ring-blue-500 focus:border-blue-500 transition-all">
                </div>

                <div class="order-2 grid grid-cols-[1fr_1fr_auto] gap-2 w-full sm:flex sm:w-auto sm:items-center">
                    {{-- Статус --}}
                    <select wire:model.live="filter_status"
                        class="block w-full sm:w-auto py-1.5 pl-3 pr-8 border border-gray-200 rounded-lg text-md bg-white cursor-pointer focus:ring-blue-500">
                        <option value="">{{ __('admin.invoices.all_statuses') }}</option>
                        <option value="paid">{{ __('admin.invoices.paid') }}</option>
                        <option value="unpaid">{{ __('admin.invoices.unpaid') }}</option>
                        <option value="cancelled">{{ __('admin.invoices.cancelled') }}</option>
                        <option value="sent">{{ __('admin.invoices.sent_status') }}</option>
                        <option value="unsent">{{ __('admin.invoices.unsent_status') }}</option>
                    </select>

                    {{-- Тип --}}
                    <select wire:model.live="filter_type"
                        class="block w-full sm:w-auto py-1.5 pl-3 pr-8 border border-gray-200 rounded-lg text-md bg-white cursor-pointer focus:ring-blue-500">
                        <option value="">{{ __('admin.invoices.all_types') }}</option>
                        <option value="student">{{ __('admin.invoices.teaching') }}</option>
                        <option value="service">{{ __('admin.invoices.services') }}</option>
                    </select>

                    {{-- Ресетирај --}}
                    <button wire:click="resetFilters" title="{{ __('admin.lessons.clear_fields') }}"
                        class="px-2 py-2 hover:bg-gray-300 text-gray-700 rounded-lg font-bold shadow transition-all flex items-center justify-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15">
                            </path>
                        </svg>
                    </button>
                </div>
            </div>
        </div>
        <div class="mt-4 overflow-x-auto overflow-y-visible border border-gray-100 rounded-xl shadow-sm bg-white">
            <table class="w-full border-collapse bg-white">
                <thead class=" bg-gray-50 text-left font-semibold text-sm text-gray-900">
                    <tr>
                        <th class="px-2 py-3">{{ __('admin.invoices.invoice_no') }}</th>
                        <th class="px-2 py-3">{{ __('admin.invoices.student_client') }}</th>
                        <th class="px-2 py-3">{{ __('admin.invoices.amount') }}</th>
                        <th class="px-2 py-3">{{ __('admin.invoices.realization') }}</th>
                        <th class="px-2 py-3">{{ __('admin.invoices.status') }}</th>
                        <th class="px-2 py-3">{{ __('admin.invoices.service_description') }}</th>
                        <th class="px-2 py-3 text-right tracking-wider">{{ __('admin.invoices.actions') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 bg-white">
                    @forelse($invoices as $invoice)
                        <tr
                            class="hover:bg-gray-50 transition {{ $invoice->status === 'cancelled' ? 'bg-gray-50/50' : '' }}">
                            <td
                                class="px-2 py-2 whitespace-nowrap text-md {{ $invoice->status === 'cancelled' ? 'text-gray-400 line-through' : 'text-normal-400' }}">
                                {{ $invoice->invoice_number }}
                            </td>
                            <td class="px-2 py-2 whitespace-nowrap text-md font-medium text-gray-600">
                                @if($invoice->student)
                                    <div class="text-gray-900">{{ $invoice->student->first_name }}
                                        {{ $invoice->student->last_name }}
                                    </div>
                                    <div class="text-xs text-gray-400 font-medium">{{ __('admin.invoices.teaching') }}</div>
                                @else
                                    <div class=" text-blue-700">{{ $invoice->custom_client_name }}</div>
                                    <div class="text-xs text-gray-400 font-medium">{{ __('admin.invoices.services') }}</div>
                                @endif
                            </td>
                            <td class="px-2 py-2 whitespace-nowrap text-md text-gray-800">
                                {{ number_format($invoice->total_amount, 0, ',', '.') }} {{ __('admin.pricing.currency') }}
                            </td>
                            <td class="px-2 py-2 whitespace-nowrap text-sm text-gray-700">
                                @if($invoice->is_advance && $invoice->student_id)
                                    <div class="flex flex-col items-start">
                                        <span class="font-semibold text-sm text-blue-900">
                                            {{ $invoice->realized_lessons }}<span class="text-gray-400"> / </span>{{ $invoice->expected_lessons }}<span class="text-xs font-medium text-gray-500"> {{ __('admin.invoices.realization_lessons_suffix') }}</span>
                                        </span>
                                        @if($invoice->progress_state === 'under')
                                            <span
                                                class="inline-flex mt-1 items-center gap-1 py-px px-1.5 rounded text-[11px] bg-yellow-50 text-yellow-800 border border-yellow-200 font-medium">
                                                <svg class="w-3 h-3 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                                    <path
                                                        d="M8.257 3.099c.366-.756 1.42-.756 1.786 0l7.451 15.377A1 1 0 0 1 16.451 20H3.549a1 1 0 0 1-.893-1.524L8.257 3.1zM11 16a1 1 0 1 0-2 0 1 1 0 0 0 2 0zm-1-2a1 1 0 0 0 1-1V9a1 1 0 1 0-2 0v4a1 1 0 0 0 1 1z" />
                                                </svg>
                                                {{ __('admin.invoices.missing_lessons', ['count' => $invoice->remaining_lessons]) }}
                                            </span>
                                        @elseif($invoice->progress_state === 'over')
                                            <span
                                                class="inline-flex mt-1 items-center gap-1.5 py-0.5 px-2 rounded text-xs bg-orange-50 text-orange-800 border border-orange-200 font-medium">
                                                <svg class="w-3 h-3 mr-1 text-orange-400" fill="currentColor" viewBox="0 0 20 20">
                                                    <path d="M10 2a8 8 0 1 0 0 16 8 8 0 0 0 0-16zm1 11H9v-2h2v2zm0-4H9V7h2v2z" />
                                                </svg>
                                                {{ __('admin.invoices.over_by', ['count' => abs($invoice->remaining_lessons)]) }}
                                            </span>
                                        @elseif($invoice->progress_state === 'done')
                                            <span
                                                class="inline-flex mt-1 items-center gap-1.5 py-0.5 px-2 rounded text-xs bg-green-50 text-green-700 border border-green-200 font-medium">
                                                <svg class="w-3 h-3 mr-1 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                                                    <path
                                                        d="M16.707 5.293a1 1 0 0 0-1.414 0L9 11.586 6.707 9.293a1 1 0 0 0-1.414 1.414l3 3a1 1 0 0 0 1.414 0l7-7a1 1 0 0 0 0-1.414z" />
                                                </svg>
                                                {{ __('admin.invoices.realized') }}
                                            </span>
                                        @endif
                                    </div>
                                @else
                                    <span class="text-gray-300">/</span>
                                @endif
                            </td>
                            <td class="px-2 py-2 whitespace-nowrap text-md font-medium">
                                @if($invoice->status === 'paid')
                                    <span
                                        class="inline-flex items-center gap-1.5 py-1 px-3 rounded-full text-xs bg-green-100 text-green-600 border border-green-200">{{ __('admin.invoices.paid') }}</span>
                                @elseif($invoice->status === 'cancelled')
                                    <span
                                        class="inline-flex items-center gap-1.5 py-1 px-3 rounded-full text-xs bg-red-100 text-red-700 border border-red-200">{{ __('admin.invoices.cancelled') }}</span>
                                    @if($invoice->cancelled_reason)
                                        <div class="mt-1.5 text-[11px] text-red-700/80 max-w-[240px] truncate flex items-center gap-1"
                                            title="{{ $invoice->cancelled_reason }}">
                                            {{ __('admin.invoices.cancel_reason') }}: {{ \Illuminate\Support\Str::limit($invoice->cancelled_reason, 38) }}
                                            <span class="inline-flex items-center text-red-500/80 cursor-help"
                                                title="{{ $invoice->cancelled_reason }}" aria-label="Прикажи цела причина">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none"
                                                    viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                    <circle cx="12" cy="12" r="9"></circle>
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 10v6"></path>
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 7h.01"></path>
                                                </svg>
                                            </span>
                                        </div>
                                    @endif
                                @else
                                    <span class="inline-flex items-center gap-1.5 py-1 px-3 rounded-full text-xs border"
                                        style="background-color:#fef3c7;color:#92400e;border-color:#fcd34d;">{{ __('admin.invoices.unpaid') }}</span>
                                @endif

                                @if($invoice->email_sent_at)
                                    <div class="mt-2 text-[11px] leading-4 max-w-xs" style="color:#2563eb;">
                                        <span class="inline-flex items-center gap-1 py-0.5 px-2 rounded-full border"
                                            title="{{ 'Последно праќање: ' . $invoice->email_sent_at->format('d.m.Y H:i') . ($invoice->email_sent_to ? "\nЕ-пошта: " . $invoice->email_sent_to : '') }}"
                                            style="background-color:#dbeafe;color:#2563eb;border-color:#bfdbfe;">
                                            {{ __('admin.invoices.sent_badge') }}{{ (int) $invoice->email_sent_count > 1 ? ' x' . (int) $invoice->email_sent_count : '' }}
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 opacity-70" fill="none"
                                                viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                <circle cx="12" cy="12" r="9"></circle>
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 10v6"></path>
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 7h.01"></path>
                                            </svg>
                                        </span>
                                    </div>
                                @else
                                    <div class="mt-2 text-[11px] leading-4 text-gray-500/90">
                                        <span
                                            class="inline-flex items-center gap-1 py-0.5 px-2 rounded-full bg-gray-100 text-gray-600 border border-gray-200">
                                            {{ __('admin.invoices.unsent_badge') }}
                                        </span>
                                    </div>
                                @endif

                                @if($invoice->email_last_error)
                                    <div class="mt-2 text-[11px] text-amber-700/90 truncate"
                                        title="{{ $invoice->email_last_error }}">
                                        {{ __('admin.invoices.last_send_error') }}
                                    </div>
                                @endif
                            </td>
                            <td class="px-2 py-2 text-sm text-gray-500 max-w-xs truncate">
                                @if($invoice->service_description)
                                    {{-- Приказ на описот ако е рачно внесен (за услуги) --}}
                                    {{ $invoice->service_description }}
                                @elseif($invoice->is_advance)
                                    {{-- Автоматски текст за авансни фактури --}}
                                    <span class="italic">{{ __('admin.invoices.advance_teaching') }}</span>
                                @else
                                    {{-- Период за редовна настава --}}
                                    {{ \Carbon\Carbon::parse($invoice->date_from)->format('d.m') }} -
                                    {{ \Carbon\Carbon::parse($invoice->date_to)->format('d.m.Y') }}
                                @endif
                            </td>
                            <td class="px-2 py-2 whitespace-nowrap text-right text-sm font-medium">
                                <div class="flex justify-end items-center gap-2">
                                    @if($invoice->status !== 'cancelled')
                                        <button type="button"
                                            onclick="confirmSendInvoiceEmail({{ $invoice->id }}, @js($invoice->invoice_number), @js(optional($invoice->email_sent_at)?->format('d.m.Y H:i')))"
                                            wire:loading.attr="disabled" wire:target="sendInvoiceEmail"
                                            class="p-2 text-indigo-600 bg-indigo-50 hover:bg-indigo-100 border border-indigo-100 rounded-lg transition-all disabled:opacity-50 disabled:cursor-not-allowed"
                                            title="{{ $invoice->email_sent_at ? 'Препрати по е-пошта' . ((int) $invoice->email_sent_count > 0 ? ' (пратена ' . (int) $invoice->email_sent_count . ' пати)' : '') : 'Испрати по е-пошта' }}">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
                                                viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M22 2 11 13" />
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M22 2 15 22 11 13 2 9 22 2Z" />
                                            </svg>
                                        </button>

                                        {{-- Preview Копче (нов таб) --}}
                                        <a href="{{ route('student.invoice-preview', $invoice->id) }}" target="_blank"
                                            rel="noopener noreferrer"
                                            class="p-2 text-blue-600 bg-blue-50 hover:bg-blue-100 border border-blue-100 rounded-lg transition-all inline-flex items-center justify-center"
                                            title="{{ __('admin.invoices.preview_new_tab') }}">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
                                                viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7-1.274 4.057-5.065 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            </svg>
                                        </a>

                                        {{-- Dropdown мени за секундарни акции --}}
                                        <div x-data="{
                                                                                                                    open: false,
                                                                                                                    menuStyle: '',
                                                                                                                    toggleMenu(el) {
                                                                                                                        this.open = !this.open;
                                                                                                                        if (this.open) {
                                                                                                                            this.$nextTick(() => this.placeMenu(el));
                                                                                                                        }
                                                                                                                    },
                                                                                                                    placeMenu(el) {
                                                                                                                        const rect = el.getBoundingClientRect();
                                                                                                                        const menuWidth = 224;
                                                                                                                        const menuHeight = 132;
                                                                                                                        const gap = 8;
                                                                                                                        const viewportWidth = window.innerWidth;
                                                                                                                        const viewportHeight = window.innerHeight;

                                                                                                                        let left = rect.right - menuWidth;
                                                                                                                        left = Math.max(gap, Math.min(left, viewportWidth - menuWidth - gap));

                                                                                                                        let top = rect.bottom + gap;
                                                                                                                        if (top + menuHeight > viewportHeight - gap) {
                                                                                                                            top = Math.max(gap, rect.top - menuHeight - gap);
                                                                                                                        }

                                                                                                                        this.menuStyle = `position: fixed; left: ${left}px; top: ${top}px;`;
                                                                                                                    }
                                                                                                                }"
                                            class="relative">
                                            <button type="button" @click="toggleMenu($event.currentTarget)"
                                                class="p-2 text-gray-600 bg-gray-100 hover:bg-gray-200 border border-gray-200 rounded-lg transition-all"
                                                title="{{ __('admin.invoices.more_actions') }}">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
                                                    viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        d="M12 5.25a.75.75 0 110 1.5.75.75 0 010-1.5zm0 6a.75.75 0 110 1.5.75.75 0 010-1.5zm0 6a.75.75 0 110 1.5.75.75 0 010-1.5z" />
                                                </svg>
                                            </button>

                                            <template x-teleport="body">
                                                <div x-show="open" @click.outside="open = false" :style="menuStyle"
                                                    x-transition:enter="transition ease-out duration-120"
                                                    x-transition:enter-start="opacity-0 scale-95"
                                                    x-transition:enter-end="opacity-100 scale-100"
                                                    x-transition:leave="transition ease-in duration-80"
                                                    x-transition:leave-start="opacity-100 scale-100"
                                                    x-transition:leave-end="opacity-0 scale-95"
                                                    class="w-64 bg-white border border-gray-200 rounded-xl shadow-xl p-2 z-[100]"
                                                    style="display:none;">
                                                    <button wire:click="togglePaid({{ $invoice->id }})" @click="open = false"
                                                        class="w-full flex items-center gap-2 px-3 py-2 text-left text-slate-700 hover:bg-slate-50 rounded-lg whitespace-nowrap">
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
                                                            viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                        </svg>
                                                        {{ $invoice->status === 'paid' ? __('admin.invoices.mark_unpaid') : __('admin.invoices.mark_paid') }}
                                                    </button>

                                                    @if($invoice->status !== 'paid')
                                                        <button type="button"
                                                            @click="open = false; confirmAction('cancel', {{ $invoice->id }}, '{{ $invoice->invoice_number }}')"
                                                            class="w-full flex items-center gap-2 px-3 py-2 text-left text-red-700 hover:bg-red-50 rounded-lg whitespace-nowrap">
                                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
                                                                viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                    d="M6 18 18 6M6 6l12 12" />
                                                            </svg>
                                                            {{ __('admin.invoices.cancel_invoice') }}
                                                        </button>
                                                    @endif
                                                </div>
                                            </template>
                                        </div>
                                    @endif

                                    @if($invoice->status === 'cancelled')
                                        <a href="{{ route('student.invoice-preview', $invoice->id) }}" target="_blank"
                                            rel="noopener noreferrer"
                                            class="p-2 text-blue-600 bg-blue-50 hover:bg-blue-100 border border-blue-100 rounded-lg transition-all inline-flex items-center justify-center"
                                            title="{{ __('admin.invoices.preview_new_tab') }}">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
                                                viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7-1.274 4.057-5.065 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            </svg>
                                        </a>

                                        <button type="button"
                                            onclick="confirmAction('restore', {{ $invoice->id }}, @js($invoice->invoice_number))"
                                            class="p-2 text-emerald-700 bg-emerald-50 hover:bg-emerald-100 rounded-lg transition-all border border-emerald-200"
                                            title="{{ __('admin.invoices.restore_invoice') }}">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
                                                viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M9 14 4 9m0 0 5-5m-5 5h9a5 5 0 010 10h-1" />
                                            </svg>
                                        </button>

                                        <button type="button"
                                            onclick="confirmAction('delete', {{ $invoice->id }}, @js($invoice->invoice_number))"
                                            class="p-2 text-red-900 bg-red-50 hover:bg-red-800 hover:text-white rounded-lg transition-all border border-red-200"
                                            title="{{ __('admin.invoices.delete_invoice') }}">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
                                                viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
                                            </svg>
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        {{-- Овој дел се прикажува ако НЕМА ниту една фактура (филтерот е празен) --}}
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center">
                                <div class="text-gray-400">
                                    <p class="text-lg font-semibold">{{ __('admin.invoices.empty_title') }}</p>
                                    <p class="text-sm">{{ __('admin.invoices.empty_subtitle') }}</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
                <tfoot class="bg-blue-50 border-t border-blue-100">
                    <tr>
                        <td colspan="7" class="px-3 py-2">
                            <div class="flex items-center justify-end gap-6 text-sm leading-none">
                                <div class="flex items-center gap-2">
                                    <span class="font-semibold text-blue-800 uppercase text-[11px]">{{ __('admin.invoices.total') }}:</span>
                                    <span
                                        class="font-bold text-blue-800 text-xl">{{ number_format($totalFilteredAmount ?? 0, 0, ',', '.') }}
                                        {{ __('admin.pricing.currency') }}</span>
                                </div>
                                @if(($totalUnpaidAmount ?? 0) > 0)
                                    <div
                                        class="flex items-center gap-2 px-2 py-1 rounded-md bg-amber-50 border border-amber-100">
                                        <span class="font-semibold text-amber-800 uppercase text-[11px]">{{ __('admin.invoices.unpaid_total') }}:</span>
                                        <span
                                            class="font-bold text-amber-700 text-xl">{{ number_format($totalUnpaidAmount ?? 0, 0, ',', '.') }}
                                            {{ __('admin.pricing.currency') }}</span>
                                    </div>
                                @endif
                            </div>
                        </td>
                    </tr>
                </tfoot>
            </table>
            @if ($invoices && $invoices->hasPages())
                <div class="p-2 border-t bg-gray-50">
                    {{ $invoices->links() }}
                </div>
            @endif
        </div>
        @if($showCreateModal)
            <div x-data="{ open: false }" x-init="setTimeout(() => open = true, 10)"
                x-on:close-modal.window="open = false; setTimeout(() => @this.closeModal(), 200)"
                class="fixed inset-0 z-50 flex items-center justify-center p-4">

                {{-- BACKDROP (Позадина) --}}
                <div x-show="open" x-transition:enter="transition ease-out duration-300"
                    x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                    x-transition:leave="transition ease-in duration-300" x-transition:leave-start="opacity-100"
                    x-transition:leave-end="opacity-0" class="fixed inset-0 bg-gray-900/50 backdrop-blur-sm"
                    @click="$dispatch('close-modal')">
                </div>

                {{-- MODAL CONTENT --}}
                <div x-show="open" x-transition:enter="animate-swal-show" x-transition:leave="animate-swal-hide"
                    class="relative bg-white rounded-2xl shadow-2xl max-w-md w-full overflow-hidden z-50 border border-gray-100">

                    {{-- Header --}}
                    <div class="px-5 py-4 border-b border-gray-100 bg-gray-50/50 flex justify-between items-center">
                        <div>
                            <h3 class="text-xl font-bold text-gray-900 tracking-tight">{{ __('admin.invoices.new_invoice') }}</h3>
                        </div>
                        <button @click="$dispatch('close-modal')"
                            class="text-gray-400 hover:text-gray-600 hover:bg-gray-100 p-2 rounded-full transition">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                                stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M18 6L6 18M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>

                    {{-- Body --}}
                    <div class="px-5 py-5 space-y-4">

                        <div>
                            <label class="block text-[11px] text-gray-400 uppercase tracking-widest mb-1 font-bold">{{ __('admin.invoices.service_type') }}</label>
                            <div class="grid grid-cols-2 gap-2">
                                <label
                                    class="relative flex items-center justify-center p-1.5 border rounded-lg cursor-pointer transition focus-within:ring-2 focus-within:ring-blue-500 text-xs {{ $invoice_type === 'student' ? 'border-blue-600 bg-blue-50 text-blue-700 shadow-sm' : 'border-gray-200 hover:bg-gray-50 text-gray-500' }}">
                                    <input type="radio" wire:model.live="invoice_type" value="student" class="sr-only">
                                    <span class="text-xs font-semibold">📚 {{ __('admin.invoices.teaching') }}</span>
                                </label>
                                <label
                                    class="relative flex items-center justify-center p-1.5 border rounded-lg cursor-pointer transition focus-within:ring-2 focus-within:ring-blue-500 text-xs {{ $invoice_type === 'service' ? 'border-blue-600 bg-blue-50 text-blue-700 shadow-sm' : 'border-gray-200 hover:bg-gray-50 text-gray-500' }}">
                                    <input type="radio" wire:model.live="invoice_type" value="service" class="sr-only">
                                    <span class="text-xs font-semibold">💻 {{ __('admin.invoices.services') }}</span>
                                </label>
                            </div>
                        </div>

                        <div class="space-y-4">
                            @if($invoice_type === 'student')
                                <div class="flex items-center justify-between py-3 px-2 bg-white rounded-xl border transition-all duration-300 shadow-sm"
                                    style="border-color: {{ $is_advance ? '#f97316' : '#e5e7eb' }}; background-color: {{ $is_advance ? '#fffaf5' : '#ffffff' }}; min-height: 48px;">
                                    <div class="flex items-center gap-2">
                                        <div class="flex h-7 w-7 items-center justify-center rounded-lg transition-colors duration-300 shadow-sm"
                                            style="background-color: {{ $is_advance ? '#f97316' : '#f3f4f6' }}; color: {{ $is_advance ? '#ffffff' : '#9ca3af' }}; text-white">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none"
                                                viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M3.75 13.5l10.5-11.25L12 10.5h8.25L9.75 21.75 12 13.5H3.75z" />
                                            </svg>
                                        </div>
                                        <div>
                                            <h4 class="text-xs font-bold"
                                                style="color: {{ $is_advance ? '#7c2d12' : '#111827' }}; line-height: 1.1;">
                                                {{ __('admin.invoices.advance_issuing') }}</h4>
                                            <p class="text-[9px] uppercase tracking-tight font-bold"
                                                style="color: {{ $is_advance ? '#ea580c' : '#9ca3af' }}; line-height: 1;">
                                                {{ __('admin.invoices.pay_in_advance') }}</p>
                                        </div>
                                    </div>
                                    <div wire:click="$toggle('is_advance')"
                                        class="relative inline-flex h-6 w-10 items-center rounded-full cursor-pointer transition-colors shadow-inner"
                                        style="background-color: {{ $is_advance ? '#f97316' : '#d1d5db' }};">
                                        <span
                                            class="inline-block h-4 w-4 transform rounded-full bg-white shadow-lg transition-transform duration-300"
                                            style="transform: translateX({{ $is_advance ? '1.1rem' : '0.2rem' }});"></span>
                                    </div>
                                </div>

                                @if($is_advance)
                                    <div class="mb-2 w-full relative" x-data="{ open: false }" x-on:click.outside="open = false">
                                        <label class="block text-xs text-gray-500 uppercase tracking-wider mb-1 font-bold">{{ __('admin.invoices.choose_student') }}</label>
                                        <div @click="open = !open"
                                            class="w-full h-9 border border-gray-200 rounded-xl shadow-sm text-xs font-medium bg-white flex items-center justify-between px-3 cursor-pointer">
                                            <span>
                                                @if($student_id)
                                                    @php $selected = $students->where('id', $student_id)->first(); @endphp
                                                    <span
                                                        class="text-black font-medium">{{ $selected ? ($selected->first_name . ' ' . $selected->last_name) : '' }}</span>
                                                @else
                                                    <span class="text-gray-400">-- {{ __('admin.invoices.choose_student') }} --</span>
                                                @endif
                                            </span>
                                            <svg class="h-4 w-4 text-gray-400 transition-transform"
                                                :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M19 9l-7 7-7-7" />
                                            </svg>
                                        </div>
                                        <div x-show="open" x-transition:enter="transition ease-out duration-100"
                                            x-transition:enter-start="opacity-0 transform scale-95"
                                            x-transition:enter-end="opacity-100 transform scale-100"
                                            class="absolute z-50 w-full mt-1 bg-white border border-gray-200 rounded-lg shadow-2xl"
                                            style="display: none;">
                                            <div class="p-2 border-b bg-gray-50 relative">
                                                <div class="relative">
                                                    <div
                                                        class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                                        <svg class="h-4 w-4 text-gray-400" xmlns="http://www.w3.org/2000/svg"
                                                            fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                                        </svg>
                                                    </div>
                                                    <input type="text" wire:model.live.debounce.250ms="student_search"
                                                        class="w-full h-9 pl-9 p-2 border border-gray-300 rounded text-[12px] focus:ring-blue-500 focus:border-blue-500"
                                                        placeholder="{{ __('admin.invoices.search_student') }}" @click.stop>
                                                </div>
                                            </div>
                                            <ul class="max-h-60 overflow-y-auto">
                                                @forelse($students as $s)
                                                    <li wire:click="$set('student_id', {{ $s->id }}); $set('student_search', '');"
                                                        @click="open = false"
                                                        class="px-3 py-2 hover:bg-blue-600 hover:text-white cursor-pointer text-sm border-b border-gray-50 last:border-0">
                                                        {{ $s->first_name }} {{ $s->last_name }}
                                                    </li>
                                                @empty
                                                    <li class="p-4 text-center text-gray-400 text-xs italic">{{ __('admin.lessons.no_results') }}</li>
                                                @endforelse
                                            </ul>
                                        </div>
                                    </div>
                                    <div class="grid grid-cols-12 gap-2 animate-fadeIn">
                                        <div class="col-span-6">
                                            <label class="block text-xs text-gray-500 uppercase tracking-wider mb-1 font-bold">{{ __('admin.invoices.lesson_type') }}</label>
                                            <select wire:model="lesson_type_id"
                                                class="w-full h-9 border-gray-200 rounded-xl shadow-sm text-xs font-medium">
                                                <option value="">{{ __('admin.invoices.choose') }}</option>
                                                @foreach($lessonTemplates as $template)
                                                    <option value="{{ $template->id }}">{{ $template->admin_name }}
                                                        ({{ $template->default_price }} {{ __('admin.pricing.currency') }})</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-span-3">
                                            <label class="block text-xs text-gray-500 uppercase tracking-wider mb-1 font-bold">{{ __('admin.invoices.hours_count') }}</label>
                                            <input type="number" wire:model="advance_hours"
                                                class="w-full h-9 border-gray-200 rounded-xl shadow-sm text-xs text-blue-700 font-bold">
                                        </div>
                                        <div class="col-span-3">
                                            <label
                                                class="block text-xs text-gray-500 uppercase tracking-wider mb-1 font-bold">{{ __('admin.invoices.discount') }}</label>
                                            <input type="number" min="0" max="100" wire:model="discount_percent"
                                                class="w-full h-9 border-gray-200 rounded-xl shadow-sm text-xs font-medium"
                                                placeholder="0">
                                        </div>
                                    </div>
                                @else
                                    <div class="grid grid-cols-5 gap-2 items-end">
                                        <div class="col-span-4 w-full relative" x-data="{ open: false }"
                                            x-on:click.outside="open = false">
                                            <label class="block text-xs text-gray-500 uppercase tracking-wider mb-1 font-bold">{{ __('admin.invoices.choose_student') }}</label>
                                            <div @click="open = !open"
                                                class="w-full h-9 border border-gray-200 rounded-xl shadow-sm text-xs font-medium bg-white flex items-center justify-between px-3 cursor-pointer">
                                                <span>
                                                    @if($student_id)
                                                        @php $selected = $students->where('id', $student_id)->first(); @endphp
                                                        <span
                                                            class="text-black font-medium">{{ $selected ? ($selected->first_name . ' ' . $selected->last_name) : '' }}</span>
                                                    @else
                                                        <span class="text-gray-400">-- {{ __('admin.invoices.choose_student') }} --</span>
                                                    @endif
                                                </span>
                                                <svg class="h-4 w-4 text-gray-400 transition-transform"
                                                    :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M19 9l-7 7-7-7" />
                                                </svg>
                                            </div>
                                            <div x-show="open" x-transition:enter="transition ease-out duration-100"
                                                x-transition:enter-start="opacity-0 transform scale-95"
                                                x-transition:enter-end="opacity-100 transform scale-100"
                                                class="absolute z-50 w-full mt-1 bg-white border border-gray-200 rounded-lg shadow-2xl"
                                                style="display: none;">
                                                <div class="p-2 border-b bg-gray-50 relative">
                                                    <div class="relative">
                                                        <div
                                                            class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                                            <svg class="h-4 w-4 text-gray-400" xmlns="http://www.w3.org/2000/svg"
                                                                fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                    stroke-width="2"
                                                                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                                            </svg>
                                                        </div>
                                                        <input type="text" wire:model.live.debounce.250ms="student_search"
                                                            class="w-full h-9 pl-9 p-2 border border-gray-300 rounded text-[12px] focus:ring-blue-500 focus:border-blue-500"
                                                            placeholder="{{ __('admin.invoices.search_student') }}" @click.stop>
                                                    </div>
                                                </div>
                                                <ul class="max-h-60 overflow-y-auto">
                                                    @forelse($students as $s)
                                                        <li wire:click="$set('student_id', {{ $s->id }}); $set('student_search', '');"
                                                            @click="open = false"
                                                            class="px-3 py-2 hover:bg-blue-600 hover:text-white cursor-pointer text-sm border-b border-gray-50 last:border-0">
                                                            {{ $s->first_name }} {{ $s->last_name }}
                                                        </li>
                                                    @empty
                                                        <li class="p-4 text-center text-gray-400 text-xs italic">{{ __('admin.lessons.no_results') }}</li>
                                                    @endforelse
                                                </ul>
                                            </div>
                                        </div>
                                        <div>
                                            <label
                                                class="block text-xs text-gray-500 uppercase tracking-wider mb-1 font-bold whitespace-nowrap">{{ __('admin.invoices.discount') }}</label>
                                            <input type="number" min="0" max="100" wire:model="discount_percent"
                                                class="w-full h-9 border-gray-200 rounded-xl shadow-sm text-xs font-medium px-2"
                                                placeholder="0">
                                        </div>
                                    </div>
                                @endif



                                <div class="grid grid-cols-2 gap-3">
                                    <div>
                                        <label class="block text-xs text-gray-500 uppercase tracking-wider mb-1 font-bold">{{ __('admin.invoices.from_date') }}</label>
                                        <input type="date" wire:model="date_from"
                                            class="w-full h-10 border-gray-200 rounded-xl shadow-sm text-sm font-medium">
                                    </div>
                                    <div>
                                        <label class="block text-xs text-gray-500 uppercase tracking-wider mb-1 font-bold">{{ __('admin.invoices.to_date') }}</label>
                                        <input type="date" wire:model="date_to"
                                            class="w-full h-10 border-gray-200 rounded-xl shadow-sm text-sm font-medium">
                                    </div>
                                </div>
                            @else
                                <div class="space-y-3 animate-fadeIn">
                                    <div>
                                        <label class="block text-xs text-gray-500 uppercase tracking-wider mb-1 font-bold">{{ __('admin.invoices.client_company') }}</label>
                                        <input type="text" wire:model="service_client_name" placeholder="{{ __('admin.invoices.enter_name') }}"
                                            class="w-full h-10 border-gray-200 rounded-xl shadow-sm text-sm text-blue-800 font-bold">
                                    </div>
                                    <div>
                                        <label class="block text-xs text-gray-500 uppercase tracking-wider mb-1 font-bold">{{ __('admin.invoices.service_desc') }}</label>
                                        <textarea wire:model="service_description" rows="2"
                                            class="w-full border-gray-200 rounded-xl shadow-sm text-sm p-2 font-medium"
                                            placeholder="{{ __('admin.invoices.detailed_desc') }}"></textarea>
                                    </div>
                                    <div class="grid grid-cols-12 gap-2 items-end">
                                        <div class="col-span-9">
                                            <label class="block text-xs text-gray-500 uppercase tracking-wider mb-1 font-bold">{{ __('admin.invoices.total_amount') }}</label>
                                            <div class="relative">
                                                <input type="number" wire:model="service_amount"
                                                    class="w-full h-10 pl-4 pr-12 border-gray-200 rounded-xl shadow-sm font-black text-blue-700 text-base">
                                                <span class="absolute right-4 top-1 text-gray-400 font-bold">{{ __('admin.pricing.currency') }}</span>
                                            </div>
                                        </div>
                                        <div class="col-span-3">
                                            <label
                                                class="block text-xs text-gray-500 uppercase tracking-wider mb-1 font-bold whitespace-nowrap">{{ __('admin.invoices.discount') }}</label>
                                            <input type="number" min="0" max="100" wire:model="discount_percent"
                                                class="w-full h-10 border-gray-200 rounded-xl shadow-sm text-xs font-medium px-2"
                                                placeholder="0">
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>

                    {{-- Footer --}}
                    <div class="px-5 py-4 font-semibold bg-gray-50 border-t border-gray-100 flex justify-end gap-3">
                        <button @click="$dispatch('close-modal')"
                            class="px-6 py-3 text-gray-500 font-semibold hover:text-gray-700">{{ __('admin.pricing.cancel') }}</button>

                        <button wire:click="createInvoice"
                            class="bg-blue-600 hover:bg-blue-700 text-white px-8 py-2.5 rounded-xl font-semibold shadow-lg shadow-blue-200 transition transform active:scale-95 flex items-center gap-2 tracking-tight">
                            <span>{{ __('admin.invoices.generate_invoice') }}</span>
                        </button>
                    </div>
                </div>
            </div>
        @endif
    </div>

</div>
<script>
    function confirmSendInvoiceEmail(id, number, sentAt) {
        if (!sentAt) {
            @this.sendInvoiceEmail(id);
            return;
        }

        Swal.fire({
            title: @json(__('admin.invoices.send_again_title')),
            html: @json(__('admin.invoices.send_again_html'))
                .replace(':number', number)
                .replace(':sentAt', sentAt),
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#2563eb',
            cancelButtonColor: '#6b7280',
            confirmButtonText: @json(__('admin.invoices.send_again_confirm')),
            cancelButtonText: @json(__('admin.pricing.cancel')),
            reverseButtons: true,
            backdrop: `rgba(15, 23, 42, 0.5)`
        }).then((result) => {
            if (result.isConfirmed) {
                @this.sendInvoiceEmail(id, true);
            }
        });
    }

    function confirmAction(type, id, number) {
        if (type === 'cancel') {
            Swal.fire({
                title: @json(__('admin.invoices.cancel_title')),
                html: @json(__('admin.invoices.cancel_html')).replace(':number', number),
                icon: 'warning',
                input: 'textarea',
                inputLabel: @json(__('admin.invoices.cancel_reason_label')),
                inputPlaceholder: @json(__('admin.invoices.cancel_reason_placeholder')),
                inputAttributes: {
                    'aria-label': @json(__('admin.invoices.cancel_reason_label')),
                    maxlength: 500
                },
                inputValidator: (value) => {
                    if (!value || value.trim().length < 10) {
                        return @json(__('admin.invoices.cancel_reason_min_text'));
                    }
                },
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#2563eb',
                confirmButtonText: @json(__('admin.invoices.cancel_confirm_button')),
                cancelButtonText: @json(__('admin.invoices.cancel_button')),
                reverseButtons: true,
                backdrop: `rgba(15, 23, 42, 0.5)`
            }).then((result) => {
                if (result.isConfirmed) {
                    @this.cancelInvoice(id, result.value.trim());
                }
            });

            return;
        }

        if (type === 'restore') {
            Swal.fire({
                title: @json(__('admin.invoices.restore_title')),
                html: @json(__('admin.invoices.restore_html')).replace(':number', `<b>${number}</b>`),
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#059669',
                cancelButtonColor: '#2563eb',
                confirmButtonText: @json(__('admin.invoices.restore_confirm_button')),
                cancelButtonText: @json(__('admin.invoices.cancel_button')),
                reverseButtons: true,
                backdrop: `rgba(15, 23, 42, 0.5)`
            }).then((result) => {
                if (result.isConfirmed) {
                    @this.restoreInvoice(id);
                }
            });

            return;
        }

        Swal.fire({
            title: @json(__('admin.invoices.delete_title')),
            html: @json(__('admin.invoices.delete_html')).replace(':number', `<b>${number}</b>`),
            icon: 'warning',
            input: 'text',
            inputPlaceholder: @json(__('admin.invoices.delete_input_placeholder')),
            inputAttributes: {
                autocapitalize: 'off',
                autocorrect: 'off'
            },
            inputValidator: (value) => {
                if (!value || value.trim() !== number) {
                    return @json(__('admin.invoices.delete_input_error'));
                }
            },
            showCancelButton: true,
            confirmButtonColor: '#dc2626',
            cancelButtonColor: '#2563eb',
            confirmButtonText: @json(__('admin.invoices.delete_confirm_button')),
            cancelButtonText: @json(__('admin.invoices.cancel_button')),
            reverseButtons: true,
            backdrop: `rgba(15, 23, 42, 0.5)`
        }).then((result) => {
            if (result.isConfirmed) {
                @this.deleteInvoice(id);
            }
        })
    }
</script>
