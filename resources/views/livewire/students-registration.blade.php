<div>
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 border-b pb-4 gap-4">
        <div>
            <h2 class="text-xl font-bold text-gray-700">{{ __('admin.students.title') }}</h2>
            <p class="text-md text-gray-600">{{ __('admin.students.subtitle') }}</p>
        </div>

        <button wire:click="create()"
            class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded-xl shadow-lg shadow-blue-200 transition-all transform hover:scale-105 active:scale-95 flex items-center gap-2 ">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>{{ __('admin.students.add_student') }}</button>
    </div>


    @if (session()->has('message'))
        <x-flash-message :message="session('message')" />
    @endif

    <div class="bg-white p-4 mt-4 rounded-xl shadow">
        <div class="bg-gray-50 p-4 rounded-xl border border-gray-200">
            <div class="flex flex-col gap-2 sm:flex-row sm:items-center">
                {{-- Search Input --}}
                <div class="relative w-full sm:w-80 order-1">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>
                    <input wire:model.live.debounce.300ms="search" type="text"
                        placeholder="{{ __('admin.students.search_placeholder') }}"
                        class="block w-full pl-9 pr-3 py-1.5 border border-gray-200 rounded-lg text-md focus:ring-blue-500 focus:border-blue-500 transition-all">
                </div>

                <div class="order-2 flex items-center gap-2 w-full sm:w-auto">
                    {{-- Tabs --}}
                    <div
                        class="flex-1 sm:flex-none flex items-center gap-2 bg-white border border-gray-200 p-1 rounded-lg">
                        <button wire:click="$set('showArchived', false)"
                            class="flex-1 sm:flex-none px-3 py-1.5 text-xs font-semibold rounded-md transition {{ !$showArchived ? 'bg-blue-50 text-blue-600 shadow-sm' : 'text-gray-500 hover:bg-gray-50' }}">
                            {{ __('admin.students.active_tab') }}
                        </button>
                        <button wire:click="$set('showArchived', true)"
                            class="flex-1 sm:flex-none px-3 py-1.5 text-xs font-semibold rounded-md transition {{ $showArchived ? 'bg-red-50 text-red-600 shadow-sm' : 'text-gray-500 hover:bg-gray-50' }}">
                            {{ __('admin.students.archived_tab') }}
                        </button>
                    </div>

                    {{-- Reset Button --}}
                    <button wire:click="$set('search', '')"
                        class="shrink-0 px-2 py-2 hover:bg-gray-300 text-gray-700 rounded-lg font-bold shadow transition-all flex items-center justify-center">
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
        <div class="mt-4 overflow-x-auto border border-gray-100 rounded-xl shadow-sm bg-white">
            <table class="w-full border-collapse bg-white">
                <thead class=" bg-gray-50 text-left font-semibold text-sm text-gray-900">
                    <tr>
                        <th class="px-2 py-3">{{ __('admin.students.col_name') }}</th>
                        <th class="px-2 py-3">{{ __('admin.students.col_contact') }}</th>
                        <th class="px-2 py-3">{{ __('admin.students.col_country') }}</th>
                        <th wire:click="sortBy('lessons_count')"
                            class="px-2 py-3 text-center cursor-pointer hover:bg-gray-50 transition group">
                            <div class="flex items-center justify-center gap-1.5">
                                {{ __('admin.students.col_lessons') }}
                                <div class="flex flex-col items-center justify-center -space-y-1">
                                    <svg class="h-3 w-3 {{ $sortField === 'lessons_count' && $sortDirection === 'asc' ? 'text-blue-600' : 'text-gray-300 group-hover:text-gray-400' }}"
                                        fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M12 4l-9 12h18z" />
                                    </svg>

                                    <svg class="h-3 w-3 {{ $sortField === 'lessons_count' && $sortDirection === 'desc' ? 'text-blue-600' : 'text-gray-300 group-hover:text-gray-400' }}"
                                        fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M12 20l9-12h-18z" />
                                    </svg>
                                </div>
                            </div>
                        </th>
                        <th wire:click="sortBy('invoices_sum_total_amount')"
                            class="px-2 py-3 text-right cursor-pointer hover:bg-gray-50 transition group">
                            <div class="flex items-center justify-end gap-1.5">
                                {{ __('admin.students.col_earnings') }}

                                <div class="flex flex-col items-center justify-center -space-y-1">
                                    <svg class="h-3 w-3 {{ $sortField === 'invoices_sum_total_amount' && $sortDirection === 'asc' ? 'text-blue-600' : 'text-gray-300 group-hover:text-gray-400' }}"
                                        fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M12 4l-9 12h18z" />
                                    </svg>

                                    <svg class="h-3 w-3 {{ $sortField === 'invoices_sum_total_amount' && $sortDirection === 'desc' ? 'text-blue-600' : 'text-gray-300 group-hover:text-gray-400' }}"
                                        fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M12 20l9-12h-18z" />
                                    </svg>
                                </div>
                            </div>
                        </th>
                        <th class="px-2 py-3 text-center">{{ __('admin.students.col_active') }}</th>
                        <th class="px-2 py-3">{{ __('admin.students.col_registered') }}</th>
                        <th class="px-2 py-3">{{ __('admin.students.col_last_activity') }}</th>
                        <th class="px-2 py-3 text-right">{{ __('admin.students.col_actions') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 bg-white">
                    @if($showArchived)
                        @forelse($students as $student)
                            @if($student)
                                <tr class="border-t">
                                    <td class="px-2 py-2 font-medium text-gray-900">{{ $student->first_name }}
                                        {{ $student->last_name }}
                                    </td>
                                    <td class="px-2 py-2">
                                        <div class="flex flex-col gap-1.5">
                                            {{-- Прикажи Е-пошта само ако постои --}}
                                            @if($student->email)
                                                <div class="flex items-center gap-2 text-gray-600">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-400 shrink-0"
                                                        fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                            d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                                    </svg>
                                                    <span class="text-sm truncate max-w-[180px]"
                                                        title="{{ $student->email }}">{{ $student->email }}</span>
                                                </div>
                                            @endif

                                            {{-- Прикажи Телефон само ако постои --}}
                                            @if($student->phone)
                                                <div class="flex items-center gap-2 text-gray-600">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-400 shrink-0"
                                                        fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                            d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                                                    </svg>
                                                    <span class="text-sm">{{ $student->phone }}</span>
                                                </div>
                                            @endif
                                        </div>
                                    </td>

                                    <td class="px-2 py-2">{{ $student->country }}</td>
                                    <td class="px-2 py-2 text-center">
                                        <span
                                            class="inline-flex items-center justify-center px-2.5 py-0.5 rounded-full text-sm font-semibold bg-blue-50 text-blue-700 border border-blue-100">
                                            {{ $student->lessons_count }}
                                        </span>
                                    </td>
                                    <td class="px-2 py-2 text-right font-semibold text-gray-700">
                                        {{ number_format($student->invoices_sum_total_amount ?? 0, 0, ',', '.') }}
                                        {{ __('admin.students.currency') }}
                                    </td>
                                    <td class="px-2 py-2 text-center">
                                        <button wire:click="toggleActive({{ $student->id }})"
                                            class="relative inline-flex h-5 w-9 items-center rounded-full transition-colors focus:outline-none {{ $student->active ? 'bg-green-500' : 'bg-gray-300' }}">
                                            <span
                                                class="inline-block h-3 w-3 transform rounded-full bg-white transition {{ $student->active ? 'translate-x-5' : 'translate-x-1' }}"></span>
                                        </button>
                                    </td>
                                    <td class="px-2 py-2 text-sm text-gray-600">
                                        {{ $student->created_at ? $student->created_at->format('d.m.Y') : '/' }}
                                    </td>

                                    {{-- Last Activity --}}
                                    <td class="px-2 py-2 text-sm text-gray-600">
                                        @if($student->user && $student->user->last_login_at)
                                            <span class="px-2 py-2 text-sm text-gray-600">
                                                {{ $student->user->last_login_at->format('d.m.Y H:i') }}
                                            </span>
                                        @else
                                            <span class="text-gray-400 italic text-xs">{{ __('admin.students.never') }}</span>
                                        @endif
                                    </td>
                                    <td class="px-2 py-2 text-right space-x-3">
                                        @if($student->trashed())
                                            <button type="button"
                                                onclick="confirmRestoreStudent({{ $student->id }}, @js(trim(($student->first_name ?? '') . ' ' . ($student->last_name ?? ''))))"
                                                class="inline-flex items-center gap-2 px-3 py-1 text-sm font-medium rounded-md text-green-700 md:bg-green-50 md:hover:bg-green-100 md:hover:text-green-800 transition focus:outline-none">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                                                    stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                                </svg>
                                                <span class="hidden md:inline">{{ __('admin.students.restore') }}</span>
                                            </button>

                                            <button type="button"
                                                onclick="confirmPermanentDelete({{ $student->id }}, '{{ addslashes(trim(($student->first_name ?? '') . ' ' . ($student->last_name ?? ''))) }}')"
                                                class="inline-flex items-center gap-2 px-3 py-1 text-sm font-medium rounded-md text-red-600 md:bg-red-50 md:hover:bg-red-100 md:hover:text-red-800 transition focus:outline-none"
                                                title="{{ __('admin.students.delete_permanent') }}"
                                                aria-label="{{ __('admin.students.delete_permanent') }}">
                                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                </svg>
                                                <span class="hidden md:inline">{{ __('admin.students.delete_permanent') }}</span>
                                            </button>
                                        @endif
                                    </td>
                                </tr>
                            @endif
                        @empty
                            <tr>
                                <td colspan="9" class="text-center py-8 text-gray-400 italic">
                                    {{ __('admin.students.no_archived') }}</td>
                            </tr>
                        @endforelse
                    @else
                        @foreach($students as $student)
                            @if($student)
                                <tr class="border-t">
                                    <td class="px-2 py-2 font-medium text-gray-900">{{ $student->first_name }}
                                        {{ $student->last_name }}
                                    </td>
                                    <td class="px-2 py-2">
                                        <div class="flex flex-col gap-1.5">
                                            {{-- Прикажи Е-пошта само ако постои --}}
                                            @if($student->email)
                                                <div class="flex items-center gap-2 text-gray-600">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-400 shrink-0"
                                                        fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                            d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                                    </svg>
                                                    <span class="text-sm truncate max-w-[180px]"
                                                        title="{{ $student->email }}">{{ $student->email }}</span>
                                                </div>
                                            @endif

                                            {{-- Прикажи Телефон само ако постои --}}
                                            @if($student->phone)
                                                <div class="flex items-center gap-2 text-gray-600">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-400 shrink-0"
                                                        fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                            d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                                                    </svg>
                                                    <span class="text-sm">{{ $student->phone }}</span>
                                                </div>
                                            @endif
                                        </div>
                                    </td>

                                    <td class="px-2 py-2">{{ $student->country }}</td>
                                    <td class="px-2 py-2 text-center">
                                        <span
                                            class="inline-flex items-center justify-center px-2.5 py-0.5 rounded-full text-sm font-semibold bg-blue-50 text-blue-700 border border-blue-100">
                                            {{ $student->lessons_count }}
                                        </span>
                                    </td>
                                    <td class="px-2 py-2 text-right font-semibold text-gray-700">
                                        {{ number_format($student->invoices_sum_total_amount ?? 0, 0, ',', '.') }}
                                        {{ __('admin.students.currency') }}
                                    </td>
                                    <td class="px-2 py-2 text-center">
                                        <button wire:click="toggleActive({{ $student->id }})"
                                            class="relative inline-flex h-5 w-9 items-center rounded-full transition-colors focus:outline-none {{ $student->active ? 'bg-green-500' : 'bg-gray-300' }}">
                                            <span
                                                class="inline-block h-3 w-3 transform rounded-full bg-white transition {{ $student->active ? 'translate-x-5' : 'translate-x-1' }}"></span>
                                        </button>
                                    </td>
                                    <td class="px-2 py-2 text-sm text-gray-600">
                                        {{ $student->created_at ? $student->created_at->format('d.m.Y') : '/' }}
                                    </td>

                                    {{-- Last Activity --}}
                                    <td class="px-2 py-2 text-sm text-gray-600">
                                        @if($student->user && $student->user->last_login_at)
                                            <span class="px-2 py-2 text-sm text-gray-600">
                                                {{ $student->user->last_login_at->format('d.m.Y H:i') }}
                                            </span>
                                        @else
                                            <span class="text-gray-400 italic text-xs">{{ __('admin.students.never') }}</span>
                                        @endif
                                    </td>
                                    <td class="px-2 py-2 text-right space-x-3">
                                        @if($student->trashed())
                                            <button type="button"
                                                onclick="confirmRestoreStudent({{ $student->id }}, @js(trim(($student->first_name ?? '') . ' ' . ($student->last_name ?? ''))))"
                                                class="inline-flex items-center gap-2 px-3 py-1 text-sm font-medium rounded-md text-green-700 md:bg-green-50 md:hover:bg-green-100 md:hover:text-green-800 transition focus:outline-none">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                                                    stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                                </svg>
                                                <span class="hidden md:inline">{{ __('admin.students.restore') }}</span>
                                            </button>

                                            <button type="button"
                                                onclick="confirmPermanentDelete({{ $student->id }}, '{{ addslashes(trim(($student->first_name ?? '') . ' ' . ($student->last_name ?? ''))) }}')"
                                                class="inline-flex items-center gap-2 px-3 py-1 text-sm font-medium rounded-md text-red-600 md:bg-red-50 md:hover:bg-red-100 md:hover:text-red-800 transition focus:outline-none"
                                                title="{{ __('admin.students.delete_permanent') }}"
                                                aria-label="{{ __('admin.students.delete_permanent') }}">
                                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                </svg>
                                                <span class="hidden md:inline">{{ __('admin.students.delete_permanent') }}</span>
                                            </button>
                                        @else
                                            <button wire:click="edit({{ $student->id }})" title="{{ __('admin.students.edit') }}"
                                                aria-label="{{ __('admin.students.edit') }}"
                                                class="inline-flex items-center gap-2 px-3 py-1 text-sm font-medium rounded-md text-blue-600 md:bg-blue-50 md:hover:bg-blue-100 md:hover:text-blue-800 transition focus:outline-none">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                                                    stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                </svg>
                                                <span class="hidden md:inline">{{ __('admin.students.edit') }}</span>
                                            </button>

                                            <button type="button"
                                                onclick="confirmDelete({{ $student->id }}, '{{ addslashes($student->user->name ?? 'Unknown') }}')"
                                                class="inline-flex items-center gap-2 px-3 py-1 text-sm font-medium rounded-md text-red-600 md:bg-red-50 md:hover:bg-red-100 md:hover:text-red-800 transition focus:outline-none"
                                                title="{{ __('admin.students.delete') }}"
                                                aria-label="{{ __('admin.students.delete') }}">
                                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                </svg>
                                                <span class="hidden md:inline">{{ __('admin.students.delete') }}</span>
                                            </button>
                                        @endif
                                    </td>
                                </tr>
                            @endif
                        @endforeach
                    @endif
                </tbody>
            </table>
            @if ($students && $students->hasPages())
                <div class="p-2 border-t bg-gray-50">
                    {{ $students->links() }}
                </div>
            @endif
        </div>
    </div>
    <!-- Modal -->
    @if($isOpen)
        <div x-data="{ open: false }" x-init="setTimeout(() => open = true, 10)"
            x-on:close-modal.window="open = false; setTimeout(() => @this.closeModal(), 200)"
            class="fixed inset-0 z-50 flex items-center justify-center p-4">

            {{-- BACKDROP --}}
            <div x-show="open" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-300"
                x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                class="fixed inset-0 bg-gray-900/50 backdrop-blur-sm" @click="$dispatch('close-modal')">
            </div>

            {{-- MODAL CONTENT (Содржина) --}}
            <div x-show="open" x-transition:enter="animate-swal-show" x-transition:leave="animate-swal-hide"
                class="relative bg-white rounded-3xl shadow-2xl max-w-lg w-full overflow-hidden z-50 border border-gray-100">

                <div class="px-8 py-6 border-b border-gray-100 bg-gray-50/50 flex justify-between items-center">
                    <div>
                        <h3 class="text-xl font-bold text-gray-900">{{ __('admin.students.modal_title') }}</h3>
                        <p class="text-sm text-gray-500 mt-1">{{ __('admin.students.modal_subtitle') }}</p>
                    </div>
                    <button @click="$dispatch('close-modal')"
                        class="text-gray-400 hover:text-gray-600 hover:bg-gray-100 p-2 rounded-full transition text-2xl leading-none">
                        <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M18 6L6 18M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                <div class="px-8 py-8 space-y-4">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label
                                class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">{{ __('admin.students.label_first_name') }}</label>
                            <input type="text" wire:model="first_name"
                                placeholder="{{ __('admin.students.placeholder_first_name') }}"
                                class="w-full h-11 border-gray-200 rounded-xl shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                            <x-input-error :messages="$errors->get('first_name')" class="mt-1" />
                        </div>
                        <div>
                            <label
                                class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">{{ __('admin.students.label_last_name') }}</label>
                            <input type="text" wire:model="last_name"
                                placeholder="{{ __('admin.students.placeholder_last_name') }}"
                                class="w-full h-11 border-gray-200 rounded-xl shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                            <x-input-error :messages="$errors->get('last_name')" class="mt-1" />
                        </div>
                    </div>

                    <div>
                        <label
                            class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">{{ __('admin.students.label_email') }}</label>
                        <input type="email" wire:model="email" placeholder="email@example.com"
                            class="w-full h-11 border-gray-200 rounded-xl shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                        <x-input-error :messages="$errors->get('email')" class="mt-1" />
                    </div>
                    {{-- Password field removed; password is auto-generated and sent via email --}}
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label
                                class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">{{ __('admin.students.label_phone') }}</label>
                            <input type="text" wire:model="phone" placeholder="{{ __('admin.students.placeholder_phone') }}"
                                class="w-full h-11 border-gray-200 rounded-xl shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                        </div>
                        <div>
                            <label
                                class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">{{ __('admin.students.label_country') }}</label>
                            <input type="text" wire:model="country"
                                placeholder="{{ __('admin.students.placeholder_country') }}"
                                class="w-full h-11 border-gray-200 rounded-xl shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                        </div>
                    </div>
                </div>

                <div class="px-8 py-6 bg-gray-50 border-t border-gray-100 flex justify-end gap-3">
                    <button @click="$dispatch('close-modal')"
                        class="px-6 py-3 text-gray-500 font-semibold hover:text-gray-700">
                        {{ __('admin.students.cancel') }}
                    </button>
                    <button wire:click="store()"
                        class="bg-blue-600 hover:bg-blue-700 text-white px-8 py-2.5 rounded-xl font-bold shadow-lg shadow-blue-200 transition transform active:scale-95">
                        {{ __('admin.students.save') }}
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>
<script>
    const _st = {
        archiveTitle: @json(__('admin.students.confirm_archive_title')),
        archiveConfirm: @json(__('admin.students.confirm_archive_button')),
        restoreTitle: @json(__('admin.students.confirm_restore_title')),
        restoreConfirm: @json(__('admin.students.confirm_restore_button')),
        permTitle: @json(__('admin.students.confirm_permanent_title')),
        permInputPlaceholder: @json(__('admin.students.confirm_permanent_input_placeholder')),
        permInputError: @json(__('admin.students.confirm_permanent_input_error')),
        permConfirm: @json(__('admin.students.confirm_permanent_button')),
        cancel: @json(__('admin.students.cancel_button')),
    };

    function confirmDelete(id, name) {
        const html = @json(__('admin.students.confirm_archive_html', ['name' => ':name'])).replace(':name', '<b>' + name + '</b>');
        Swal.fire({
            title: _st.archiveTitle,
            html: html,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc2626',
            cancelButtonColor: '#2563eb',
            confirmButtonText: _st.archiveConfirm,
            cancelButtonText: _st.cancel,
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                @this.delete(id);
            }
        })
    }

    function confirmRestoreStudent(id, name) {
        const html = @json(__('admin.students.confirm_restore_html', ['name' => ':name'])).replace(':name', '<b>' + name + '</b>');
        Swal.fire({
            title: _st.restoreTitle,
            html: html,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#059669',
            cancelButtonColor: '#2563eb',
            confirmButtonText: _st.restoreConfirm,
            cancelButtonText: _st.cancel,
            reverseButtons: true,
            backdrop: `rgba(15, 23, 42, 0.5)`
        }).then((result) => {
            if (result.isConfirmed) {
                @this.restore(id);
            }
        });
    }

    function confirmPermanentDelete(id, name) {
        const normalizeFullName = (text) => (text || '').trim().replace(/\s+/g, ' ');
        const html = @json(__('admin.students.confirm_permanent_html', ['name' => ':name'])).replace(':name', '<b>' + name + '</b>');
        Swal.fire({
            title: _st.permTitle,
            html: html,
            icon: 'warning',
            input: 'text',
            inputPlaceholder: _st.permInputPlaceholder,
            inputValidator: (value) => {
                if (!value || normalizeFullName(value) !== normalizeFullName(name)) {
                    return _st.permInputError;
                }
            },
            showCancelButton: true,
            confirmButtonColor: '#dc2626',
            cancelButtonColor: '#2563eb',
            confirmButtonText: _st.permConfirm,
            cancelButtonText: _st.cancel,
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                @this.forceDeleteStudent(id);
            }
        })
    }
</script>