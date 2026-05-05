<div>
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 border-b pb-4 gap-4">
        <div>
            <h2 class="text-xl font-bold text-gray-700">{{ __('admin.lessons.title') }}</h2>
            <p class="text-md text-gray-600">{{ __('admin.lessons.subtitle') }}</p>
        </div>
        <button wire:click="openCreatePanel"
            class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded-xl shadow-lg shadow-blue-200 transition-all transform hover:scale-105 active:scale-95 inline-flex items-center gap-2">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            {{ __('admin.lessons.add_lesson') }}
        </button>
    </div>
    {{-- SESSION MESSAGES --}}
    @if (session()->has('message'))
        <x-flash-message :message="session('message')" />
    @endif

    @if($isFormOpen)
        <div x-data="{ open: false }" x-init="setTimeout(() => open = true, 10)"
            x-on:close-lesson-form.window="open = false; setTimeout(() => @this.closeForm(), 200)" class="fixed inset-0 z-50">

            <div x-show="open" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-300"
                x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                class="fixed inset-0 bg-gray-900/50 backdrop-blur-sm" @click="$dispatch('close-lesson-form')">
            </div>

            <div x-show="open" x-transition:enter="transform transition ease-out duration-300"
                x-transition:enter-start="translate-x-full" x-transition:enter-end="translate-x-0"
                x-transition:leave="transform transition ease-in duration-200" x-transition:leave-start="translate-x-0"
                x-transition:leave-end="translate-x-full"
                class="fixed inset-y-0 right-0 w-full sm:max-w-2xl bg-white rounded-none sm:rounded-l-3xl shadow-2xl z-50 border-l border-gray-100 flex flex-col">

                <div class="px-8 py-6 border-b border-gray-100 flex justify-between items-center">
                    <div>
                        <h3 class="text-xl font-bold text-gray-900">{{ $editingLessonId ? __('admin.lessons.update') : __('admin.lessons.add_lesson') }}</h3>
                        <p class="text-sm text-gray-500 mt-1">{{ __('admin.lessons.form_subtitle') }}</p>
                    </div>
                    <button @click="$dispatch('close-lesson-form')"
                        class="text-gray-400 hover:text-gray-600 hover:bg-gray-100 p-2 rounded-full transition text-2xl leading-none">
                        <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M18 6L6 18M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                <div class="px-8 py-8 overflow-y-auto flex-1 space-y-6">
                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">{{ __('admin.pricing.price') }}</label>
                        <div class="h-11 border border-blue-200 bg-blue-50 rounded-xl px-4 inline-flex w-full items-center justify-between whitespace-nowrap shadow-sm">
                            <span class="text-[11px] uppercase tracking-wide font-semibold text-blue-700">{{ __('admin.pricing.currency') }}</span>
                            <span class="text-sm font-bold text-blue-800">{{ $suggestedPrice }}</span>
                        </div>
                    </div>

                    <div class="relative" x-data="{ open: @entangle('showDropdown') }" x-on:click.outside="open = false">
                        <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">{{ __('admin.nav.students') }}</label>

                        <div @click="open = !open"
                            class="w-full h-11 border px-3 rounded-xl shadow-sm text-sm cursor-pointer bg-white flex justify-between items-center {{ $errors->has('student_id') ? 'border-red-500' : 'border-gray-200' }}">
                            <span>
                                @if($student_id)
                                    @php $selected = \App\Models\Student::find($student_id); @endphp
                                    <span class="text-gray-900 font-medium">{{ $selected->first_name }} {{ $selected->last_name }}</span>
                                @else
                                    <span class="text-gray-400">-- {{ __('admin.lessons.choose_student') }} --</span>
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
                            class="absolute z-50 w-full mt-1 bg-white border border-gray-200 rounded-xl shadow-2xl"
                            style="display: none;">
                            <div class="p-2 border-b bg-gray-50 relative rounded-t-xl">
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <svg class="h-4 w-4 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none"
                                            viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                        </svg>
                                    </div>
                                    <input type="text" wire:model.live.debounce.250ms="student_search"
                                        class="w-full h-9 pl-9 p-2 border border-gray-200 rounded-lg text-sm focus:ring-blue-500 focus:border-blue-500"
                                        placeholder="{{ __('admin.lessons.search_student') }}..." @click.stop>
                                </div>
                            </div>

                            <ul class="max-h-60 overflow-y-auto">
                                @forelse($studentsForSelect as $s)
                                    <li wire:click="selectStudent({{ $s->id }}); open = false"
                                        class="px-3 py-2 hover:bg-blue-50 cursor-pointer text-sm border-b border-gray-50 last:border-0 text-gray-700">
                                        {{ $s->first_name }} {{ $s->last_name }}
                                    </li>
                                @empty
                                    <li class="p-4 text-center text-gray-400 text-xs italic">{{ __('admin.lessons.no_results') }}</li>
                                @endforelse
                            </ul>
                        </div>

                        <x-input-error :messages="$errors->get('student_id')" class="mt-1" />
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">{{ __('admin.lessons.lesson_type') }}</label>
                            <select wire:model.live="lesson_type_id"
                                class="w-full h-11 border px-3 rounded-xl shadow-sm text-sm {{ $errors->has('lesson_type_id') ? 'border-red-500' : 'border-gray-200' }}">
                                <option value="">-- {{ __('admin.lessons.choose') }} --</option>
                                @foreach($lessonTypes as $type)
                                    <option value="{{ $type->id }}">{{ $type->admin_name }}</option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('lesson_type_id')" class="mt-1" />
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">{{ __('admin.lessons.status') }}</label>
                            <select wire:model.live="lesson_status"
                                class="w-full h-11 border px-3 rounded-xl shadow-sm text-sm {{ $errors->has('lesson_status') ? 'border-red-500' : 'border-gray-200' }}">
                                <option value="held">{{ __('admin.lessons.held') }}</option>
                                <option value="not_held">{{ __('admin.lessons.not_held') }}</option>
                            </select>
                            <x-input-error :messages="$errors->get('lesson_status')" class="mt-1" />
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <div class="sm:col-span-1">
                            <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">{{ __('admin.lessons.date') }}</label>
                            <input type="date" wire:model="lesson_date"
                                class="w-full h-11 border px-3 rounded-xl shadow-sm text-sm {{ $errors->has('lesson_date') ? 'border-red-500' : 'border-gray-200' }}">
                            <x-input-error :messages="$errors->get('lesson_date')" class="mt-1" />
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">{{ __('admin.lessons.start') }}</label>
                            <input type="time" wire:model.live="start_time"
                                class="w-full h-11 border px-3 rounded-xl shadow-sm text-sm {{ $errors->has('start_time') ? 'border-red-500' : 'border-gray-200' }}">
                            <x-input-error :messages="$errors->get('start_time')" class="mt-1" />
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">{{ __('admin.lessons.duration') }}</label>
                            <div class="relative">
                                <input type="number" wire:model.live="duration" min="1" placeholder="e.g. 60"
                                    class="w-full h-11 border px-3 pr-12 rounded-xl shadow-sm text-sm {{ $errors->has('duration') ? 'border-red-500' : 'border-gray-200' }}">
                                <span class="absolute inset-y-0 right-3 flex items-center text-xs text-gray-400 font-medium">{{ __('admin.lessons.min') }}</span>
                            </div>
                            @if($end_time)
                                <p class="text-xs text-blue-600 mt-1 font-medium">{{ __('admin.lessons.ends_at') }}: {{ $end_time }}</p>
                            @endif
                            <x-input-error :messages="$errors->get('duration')" class="mt-1" />
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">{{ __('admin.lessons.note') }}</label>
                        <textarea wire:model="notes" rows="6"
                            class="w-full border border-gray-200 px-3 py-3 rounded-xl shadow-sm text-sm resize-none focus:border-blue-500 focus:ring-blue-500"
                            placeholder="{{ __('admin.lessons.note_placeholder') }}"></textarea>
                    </div>
                </div>

                <div class="px-8 py-6 border-t border-gray-100 flex justify-end gap-3">
                    <button type="button" @click="$dispatch('close-lesson-form')"
                        class="px-6 py-3 text-gray-500 font-semibold hover:text-gray-700">
                        {{ __('admin.pricing.cancel') }}
                    </button>
                    <button wire:click="resetFields"
                        class="px-6 py-3 text-gray-700 font-semibold bg-gray-100 hover:bg-gray-200 rounded-xl transition">
                        {{ __('admin.lessons.clear_fields') }}
                    </button>
                    <button wire:click="saveLesson"
                        class="px-8 py-3 text-white rounded-xl font-bold shadow-lg transition {{ $editingLessonId ? 'bg-orange-500 hover:bg-orange-600 shadow-orange-100' : 'bg-blue-600 hover:bg-blue-700 shadow-blue-200' }}">
                        {{ $editingLessonId ? __('admin.lessons.update') : __('admin.pricing.save') }}
                    </button>
                </div>
            </div>
        </div>
    @endif

    <div class="bg-white p-4 mt-10 rounded-xl shadow">
        {{-- FILTERS --}}
        <div class="bg-gray-50 p-4 rounded-xl border border-gray-200">
            <div class="flex flex-wrap items-end gap-3 lg:gap-4">
                <div class="w-full sm:w-[430px]">
                    <label
                        class="block text-[14px] font-normal text-gray-900 mb-1">{{ __('admin.lessons.search_student') }}</label>
                    <div class="relative">
                        <input type="text" wire:model.live="search"
                            placeholder="{{ __('admin.lessons.name_placeholder') }}..."
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
                    <label
                        class="block text-[14px] font-normal text-gray-900 mb-1">{{ __('admin.lessons.lesson_type') }}</label>
                    <div class="relative">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 absolute left-3 top-3 text-gray-400"
                            fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2a1 1 0 01-.293.707L13 12.414V19a1 1 0 01-.553.894l-3 1.5A1 1 0 018 21v-8.586L3.293 6.707A1 1 0 013 6V4z" />
                        </svg>
                        <select wire:model.live="filter_type"
                            class="w-full border-gray-300 rounded-lg p-2 pl-9 text-sm">
                            <option value="">{{ __('admin.lessons.all_types') }}</option>
                            @foreach($lessonTypes as $type)
                                <option value="{{ $type->id }}">{{ $type->admin_name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="w-full sm:w-[220px]">
                    <label
                        class="block text-[14px] font-normal text-gray-900 mb-1">{{ __('admin.lessons.status') }}</label>
                    <div class="relative">
                        <select wire:model.live="filter_status" class="w-full border-gray-300 rounded-lg p-2 text-sm">
                            <option value="">{{ __('admin.lessons.all_statuses') }}</option>
                            <option value="held">{{ __('admin.lessons.held') }}</option>
                            <option value="not_held">{{ __('admin.lessons.not_held') }}</option>
                        </select>
                    </div>
                </div>
                <div class="w-full sm:w-[145px]"><label
                        class="block text-[14px] font-normal text-gray-900 mb-1">{{ __('admin.lessons.period_from') }}</label><input
                        type="date" wire:model.live="filter_from_date"
                        class="w-full border-gray-300 rounded-lg px-2 py-2 text-sm"></div>
                <div class="w-full sm:w-[145px]"><label
                        class="block text-[14px] font-normal text-gray-900 mb-1">{{ __('admin.lessons.period_to') }}</label><input
                        type="date" wire:model.live="filter_to_date"
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
                        {{ __('admin.lessons.export_excel') }}
                    </button>
                </div>
            </div>
        </div>

        {{-- TABLE --}}
        <div class="mt-4 overflow-x-auto border border-gray-100 rounded-xl shadow-sm bg-white">
            <table class="w-full border-collapse bg-white">
                <thead class="bg-gray-50 text-left font-normal  text-sm text-gray-900">
                    <tr>
                        <th class="px-2 py-3">{{ __('admin.nav.students') }}</th>
                        <th class="px-2 py-3">{{ __('admin.lessons.type') }}</th>
                        <th class="px-2 py-3">{{ __('admin.lessons.datetime') }}</th>
                        <th class="px-2 py-3">{{ __('admin.lessons.status') }}</th>
                        <th class="px-2 py-3">{{ __('admin.pricing.price') }}</th>
                        <th class="px-2 py-3">{{ __('admin.lessons.note') }}</th>
                        <th class="px-2 py-3 text-right">{{ __('admin.lessons.actions') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50 text-gray-600">
                    @if($lessonsLog->isEmpty())
                        <tr>
                            <td colspan="7" class="text-center py-8 text-gray-400 italic">
                                @if(!$hasAnyLessons)
                                    {{ __('admin.lessons.empty_title') ?? 'No lessons found.' }}<br>
                                    <span class="text-xs text-gray-400">{{ __('admin.lessons.empty_subtitle') ?? '' }}</span>
                                @elseif($search || $filter_type || $filter_status || $filter_from_date || $filter_to_date)
                                    <span class="text-lg font-semibold">{{ __('admin.lessons.no_results_title') }}</span><br>
                                    <span class="text-xs text-gray-400">{{ __('admin.lessons.no_results_subtitle') }}</span>
                                @else
                                    {{ __('admin.lessons.empty_title') ?? 'No lessons found.' }}
                                @endif
                            </td>
                        </tr>
                    @else
                        @foreach($lessonsLog as $log)
                            <tr class="transition-colors {{ $editingLessonId == $log->id ? 'bg-orange-50' : 'hover:bg-blue-50/50' }}">
                                <td class="px-2 py-2 font-medium text-gray-800">{{ $log->student->first_name }} {{ $log->student->last_name }}</td>
                                <td class="px-2 py-2 text-[14px]">{{ $log->lessonType->admin_name }}</td>
                                <td class="px-2 py-2 text-black">
                                    <div class="font-normal">{{ \Carbon\Carbon::parse($log->lesson_date)->format('d.m.Y') }}</div>
                                    <div class="text-[11px] text-blue-800 font-medium mt-0.5 flex items-center">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        @if($log->start_time && $log->end_time)
                                            {{ \Carbon\Carbon::parse($log->start_time)->format('H:i') }} - {{ \Carbon\Carbon::parse($log->end_time)->format('H:i') }}
                                        @else
                                            {{ __('admin.lessons.no_time') }}
                                        @endif
                                    </div>
                                </td>
                                <td class="px-2 py-2 text-sm">
                                    @if($log->lesson_status === 'held')
                                        <span class="inline-flex items-center gap-1.5 py-1 px-3 rounded-full text-xs bg-green-100 text-green-700 border border-green-200">{{ __('admin.lessons.held') }}</span>
                                    @else
                                        <span class="inline-flex items-center gap-1.5 py-1 px-3 rounded-full text-xs bg-amber-100 text-amber-700 border border-amber-200">{{ __('admin.lessons.not_held') }}</span>
                                    @endif
                                </td>
                                <td class="px-2 py-2 text-md text-gray-800">{{ number_format($log->price_at_time, 0, ',', '.') }} {{ __('admin.pricing.currency') }}</td>
                                <td class="px-2 py-2 italic text-gray-400">{{ $log->notes ?: '/' }}</td>
                                <td class="p-4 text-right space-x-2">
                                    <button wire:click="editLesson({{ $log->id }})" title="{{ __('admin.pricing.edit') }}" aria-label="{{ __('admin.pricing.edit') }}" class="inline-flex items-center gap-2 px-3 py-1 text-sm font-medium rounded-md text-blue-600 md:bg-blue-50 md:hover:bg-blue-100 md:hover:text-blue-800 transition focus:outline-none">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 inline" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                        </svg>
                                        <span class="hidden md:inline">{{ __('admin.pricing.edit') }}</span>
                                    </button>
                                    <button type="button" onclick="confirmDelete({{ $log->id }})" title="{{ __('admin.pricing.delete') }}" aria-label="{{ __('admin.pricing.delete') }}" class="inline-flex items-center gap-2 px-3 py-1 text-sm font-medium rounded-md text-red-600 md:bg-red-50 md:hover:bg-red-100 md:hover:text-red-800 transition focus:outline-none">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 inline" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                        <span class="hidden md:inline">{{ __('admin.pricing.delete') }}</span>
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    @endif
                </tbody>
                <tfoot class="bg-blue-50">
                    <tr>
                        <td colspan="4" class="p-1 text-right font-semibold text-gray-700 uppercase text-xs">
                            {{ __('admin.lessons.total') }}:
                        </td>
                        <td class="p-1 font-semibold text-blue-800 text-lg">
                            {{ number_format($totalAmount, 0, ',', '.') }} {{ __('admin.pricing.currency') }}
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
            title: @json(__('admin.lessons.confirm_title')),
            text: @json(__('admin.lessons.confirm_text')),
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc2626',
            cancelButtonColor: '#2563eb',
            confirmButtonText: @json(__('admin.lessons.confirm_delete')),
            cancelButtonText: @json(__('admin.pricing.cancel')),
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                // Повикување на Livewire функцијата
                @this.call('deleteLesson', id);
            }
        })
    }
</script>
