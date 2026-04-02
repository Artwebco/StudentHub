<x-app-layout>
    <div class="space-y-6">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center border-b pb-4">
            <div>
                <h2 class="text-xl font-bold text-gray-700">Lesson Schedule</h2>
                <p class="text-md text-gray-600">
                    @if (auth()->user()->role === 'admin')
                        Create and manage appointments. Students can only view their own scheduled lessons.
                    @else
                        Read-only calendar view of your scheduled lessons.
                    @endif
                </p>
            </div>
        </div>

        @if (session('success'))
            <x-flash-message :message="session('success')" />
        @endif

        <div class="bg-white border border-gray-200 rounded-2xl shadow-sm p-4 sm:p-5">
            @if (auth()->user()->role === 'admin')
                <div class="grid grid-cols-1 xl:grid-cols-2 gap-3 items-stretch">
                    <div class="rounded-2xl border border-gray-200 bg-gray-50 p-3 h-full">
                        <form method="POST" action="{{ route('lesson-schedule.store') }}" x-data="{
                                            open: false,
                                            search: '',
                                            selectedId: @js((string) old('student_id', '')),
                                            selectedName: @js(optional($students->firstWhere('id', (int) old('student_id')))->name ?? ''),
                                            students: @js($students->map(fn($student) => ['id' => (string) $student->id, 'name' => $student->name])->values()),
                                            filteredStudents() {
                                                const term = this.search.trim().toLowerCase();
                                                if (!term) return this.students;
                                                return this.students.filter(student => student.name.toLowerCase().includes(term));
                                            },
                                            selectStudent(student) {
                                                this.selectedId = student.id;
                                                this.selectedName = student.name;
                                                this.search = '';
                                                this.open = false;
                                            }
                                        }" x-on:click.outside="open = false"
                            class="grid grid-cols-1 md:grid-cols-[minmax(0,1.45fr)_minmax(0,1.05fr)_minmax(150px,0.6fr)] gap-2.5">
                            @csrf

                            <div class="relative">
                                <label for="student_id"
                                    class="block text-[14px] font-normal text-gray-900 mb-1">Student</label>
                                <input type="hidden" id="student_id" name="student_id" :value="selectedId">

                                <button type="button" @click="open = !open"
                                    class="flex h-10 w-full items-center justify-between rounded-xl border border-gray-300 bg-white px-3 text-left text-sm text-gray-900 focus:border-blue-500 focus:ring-blue-500">
                                    <span :class="selectedName ? 'text-gray-900' : 'text-gray-400'"
                                        x-text="selectedName || 'Select student'"></span>
                                    <svg class="h-4 w-4 text-gray-400 transition-transform"
                                        :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19 9l-7 7-7-7" />
                                    </svg>
                                </button>

                                <div x-show="open" x-transition
                                    class="absolute z-20 mt-1 w-full overflow-hidden rounded-xl border border-gray-200 bg-white shadow-2xl"
                                    style="display: none;">
                                    <div class="border-b bg-gray-50 p-2">
                                        <div class="relative">
                                            <div
                                                class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                                <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                                </svg>
                                            </div>
                                            <input type="text" x-model="search" @click.stop placeholder="Search student..."
                                                class="h-9 w-full rounded-lg border border-gray-300 pl-9 pr-3 text-sm focus:border-blue-500 focus:ring-blue-500">
                                        </div>
                                    </div>

                                    <ul class="max-h-60 overflow-y-auto py-1">
                                        <template x-for="student in filteredStudents()" :key="student.id">
                                            <li>
                                                <button type="button" @click="selectStudent(student)"
                                                    class="w-full px-3 py-2 text-left text-sm text-gray-700 transition hover:bg-blue-600 hover:text-white"
                                                    x-text="student.name"></button>
                                            </li>
                                        </template>

                                        <li x-show="filteredStudents().length === 0"
                                            class="px-3 py-3 text-center text-xs italic text-gray-400">
                                            No matching students
                                        </li>
                                    </ul>
                                </div>

                                @error('student_id')
                                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="starts_at" class="block text-[14px] font-normal text-gray-900 mb-1">Date &
                                    Time</label>
                                <input id="starts_at" name="starts_at" type="datetime-local" value="{{ old('starts_at') }}"
                                    class="w-full rounded-xl border-gray-300 pr-2 text-sm focus:border-blue-500 focus:ring-blue-500"
                                    required>
                                @error('starts_at')
                                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="duration_minutes"
                                    class="block text-[14px] font-normal text-gray-900 mb-1">Duration (min)</label>
                                <input id="duration_minutes" name="duration_minutes" type="number" min="15" max="240"
                                    value="{{ old('duration_minutes', 60) }}"
                                    class="w-full rounded-xl border-gray-300 text-sm focus:border-blue-500 focus:ring-blue-500"
                                    required>
                                @error('duration_minutes')
                                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div
                                class="md:col-span-3 grid grid-cols-1 lg:grid-cols-[minmax(0,1fr)_180px] gap-2.5 items-end">
                                <div>
                                    <label for="note" class="block text-[14px] font-normal text-gray-900 mb-1">Note
                                        (optional)</label>
                                    <input id="note" name="note" type="text" value="{{ old('note') }}"
                                        class="h-10 w-full rounded-xl border-gray-300 focus:border-blue-500 focus:ring-blue-500"
                                        placeholder="Optional note for this lesson">
                                    @error('note')
                                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div class="flex h-full items-end">
                                    <button type="submit"
                                        class="inline-flex h-10 w-full items-center justify-center rounded-xl bg-blue-600 px-4 text-sm font-semibold text-white hover:bg-blue-700">
                                        Schedule Lesson
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>

                    <div class="rounded-2xl border border-gray-200 bg-gray-50 p-3 h-full">
                        <form method="POST" action="{{ route('lesson-schedule.reminder-settings.update') }}"
                            id="reminder-settings-form" class="flex flex-col h-full gap-2.5">
                            @csrf
                            @method('PUT')

                            <div
                                class="grid grid-cols-1 lg:grid-cols-[minmax(0,1fr)_minmax(0,1fr)_180px] gap-2.5 items-end">
                                <div>
                                    <label for="first_reminder_minutes_before"
                                        class="block text-[14px] font-normal text-gray-900 mb-1">First reminder
                                        (minutes before)</label>
                                    <input id="first_reminder_minutes_before" name="first_reminder_minutes_before"
                                        type="number" min="10" max="10080"
                                        value="{{ old('first_reminder_minutes_before', $reminderSettings?->first_reminder_minutes_before ?? 1440) }}"
                                        class="h-10 w-full rounded-xl border-gray-300 focus:border-blue-500 focus:ring-blue-500"
                                        required>
                                    @error('first_reminder_minutes_before')
                                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="second_reminder_minutes_before"
                                        class="block text-[14px] font-normal text-gray-900 mb-1">Second reminder
                                        (minutes before)</label>
                                    <input id="second_reminder_minutes_before" name="second_reminder_minutes_before"
                                        type="number" min="5" max="10080"
                                        value="{{ old('second_reminder_minutes_before', $reminderSettings?->second_reminder_minutes_before ?? 30) }}"
                                        class="h-10 w-full rounded-xl border-gray-300 focus:border-blue-500 focus:ring-blue-500"
                                        required>
                                    @error('second_reminder_minutes_before')
                                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <button id="save-reminder-settings-btn" type="submit"
                                    class="inline-flex h-10 w-full items-center justify-center rounded-xl bg-slate-700 px-4 text-sm font-semibold text-white hover:bg-slate-800">
                                    Save Reminder
                                </button>
                            </div>

                            <div
                                class="mt-auto rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-700">
                                <p>
                                    First reminder: <span id="first-reminder-preview" class="font-medium"></span>
                                </p>
                                <p class="mt-1">
                                    Second reminder: <span id="second-reminder-preview" class="font-medium"></span>
                                </p>
                            </div>

                            <p id="reminder-settings-error" class="hidden text-xs font-semibold text-red-600"></p>
                        </form>
                    </div>
                </div>
            @endif
        </div>

        <div class="bg-white border border-gray-200 rounded-2xl shadow-sm p-4 sm:p-6">
            <div id="lesson-calendar"></div>
        </div>
    </div>

    <link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.15/index.global.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.15/index.global.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const calendarEl = document.getElementById('lesson-calendar');
            if (!calendarEl || typeof FullCalendar === 'undefined') {
                return;
            }

            const isAdmin = @json(auth()->user()->role === 'admin');
            const csrfToken = '{{ csrf_token() }}';
            const hasSwal = typeof Swal !== 'undefined';

            function asIsoLocal(dateObj) {
                const pad = (n) => String(n).padStart(2, '0');
                return `${dateObj.getFullYear()}-${pad(dateObj.getMonth() + 1)}-${pad(dateObj.getDate())}T${pad(dateObj.getHours())}:${pad(dateObj.getMinutes())}:${pad(dateObj.getSeconds())}`;
            }

            function formatMinutesLabel(totalMinutes) {
                const minutes = Number(totalMinutes);

                if (!Number.isFinite(minutes) || minutes <= 0) {
                    return 'n/a';
                }

                if (minutes % 1440 === 0) {
                    const days = minutes / 1440;
                    return days === 1 ? '1 day before' : `${days} days before`;
                }

                if (minutes % 60 === 0) {
                    const hours = minutes / 60;
                    return hours === 1 ? '1 hour before' : `${hours} hours before`;
                }

                return minutes === 1 ? '1 minute before' : `${minutes} minutes before`;
            }

            async function apiCall(url, method, payload) {
                const response = await fetch(url, {
                    method,
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                    },
                    body: JSON.stringify(payload),
                });

                if (!response.ok) {
                    let message = 'Request failed';

                    try {
                        const data = await response.json();
                        message = data.message || message;
                    } catch (e) {
                        // Keep fallback message.
                    }

                    throw new Error(message);
                }

                return response;
            }

            function toastSuccess(message) {
                if (hasSwal) {
                    Swal.fire({
                        toast: true,
                        position: 'top-end',
                        icon: 'success',
                        title: message,
                        showConfirmButton: false,
                        timer: 1800,
                    });
                    return;
                }

                console.log(message);
            }

            function toastError(message) {
                if (hasSwal) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Action failed',
                        text: message,
                    });
                    return;
                }

                alert(message);
            }

            const calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'dayGridMonth',
                nowIndicator: true,
                height: 'auto',
                editable: isAdmin,
                eventDurationEditable: isAdmin,
                headerToolbar: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'dayGridMonth,timeGridWeek,timeGridDay'
                },
                events: '{{ route('lesson-schedule.events') }}',
                eventTimeFormat: {
                    hour: '2-digit',
                    minute: '2-digit',
                    meridiem: false
                },
                eventDrop: async function (info) {
                    try {
                        await apiCall(`{{ url('/lesson-schedule/events') }}/${info.event.id}`, 'PATCH', {
                            starts_at: asIsoLocal(info.event.start),
                            ends_at: asIsoLocal(info.event.end),
                        });
                        toastSuccess('Lesson rescheduled.');
                    } catch (e) {
                        info.revert();
                        toastError(e.message || 'Unable to reschedule this lesson.');
                    }
                },
                eventResize: async function (info) {
                    try {
                        await apiCall(`{{ url('/lesson-schedule/events') }}/${info.event.id}`, 'PATCH', {
                            starts_at: asIsoLocal(info.event.start),
                            ends_at: asIsoLocal(info.event.end),
                        });
                        toastSuccess('Lesson duration updated.');
                    } catch (e) {
                        info.revert();
                        toastError(e.message || 'Unable to update lesson duration.');
                    }
                },
                eventClick: async function (info) {
                    if (!isAdmin) {
                        return;
                    }

                    const note = info.event.extendedProps.note ? `<br><br><strong>Note:</strong> ${info.event.extendedProps.note}` : '';
                    let confirmed = false;

                    if (hasSwal) {
                        const result = await Swal.fire({
                            title: 'Cancel lesson?',
                            html: `Cancel lesson for <strong>${info.event.title}</strong>?${note}`,
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonText: 'Yes, cancel',
                            cancelButtonText: 'Keep it',
                            reverseButtons: true,
                        });

                        confirmed = !!result.isConfirmed;
                    } else {
                        confirmed = window.confirm(`Cancel lesson for ${info.event.title}?`);
                    }

                    if (!confirmed) {
                        return;
                    }

                    try {
                        await apiCall(`{{ url('/lesson-schedule/events') }}/${info.event.id}`, 'DELETE', {});
                        info.event.remove();
                        toastSuccess('Lesson cancelled.');
                    } catch (e) {
                        toastError(e.message || 'Unable to cancel this lesson.');
                    }
                }
            });

            calendar.render();

            if (isAdmin) {
                const startsAtInput = document.getElementById('starts_at');
                const firstReminderInput = document.getElementById('first_reminder_minutes_before');
                const secondReminderInput = document.getElementById('second_reminder_minutes_before');
                const firstReminderPreview = document.getElementById('first-reminder-preview');
                const secondReminderPreview = document.getElementById('second-reminder-preview');
                const reminderSettingsForm = document.getElementById('reminder-settings-form');
                const saveReminderSettingsBtn = document.getElementById('save-reminder-settings-btn');
                const reminderSettingsError = document.getElementById('reminder-settings-error');

                const validateReminderSettings = function () {
                    let isValid = true;

                    if (firstReminderInput && firstReminderPreview) {
                        firstReminderPreview.textContent = formatMinutesLabel(firstReminderInput.value);
                    }

                    if (secondReminderInput && secondReminderPreview) {
                        secondReminderPreview.textContent = formatMinutesLabel(secondReminderInput.value);
                    }

                    const firstValue = Number(firstReminderInput ? firstReminderInput.value : 0);
                    const secondValue = Number(secondReminderInput ? secondReminderInput.value : 0);

                    if (Number.isFinite(firstValue) && Number.isFinite(secondValue) && secondValue >= firstValue) {
                        isValid = false;
                    }

                    if (saveReminderSettingsBtn) {
                        saveReminderSettingsBtn.disabled = !isValid;
                        saveReminderSettingsBtn.classList.toggle('opacity-50', !isValid);
                        saveReminderSettingsBtn.classList.toggle('cursor-not-allowed', !isValid);
                    }

                    if (reminderSettingsError) {
                        if (!isValid) {
                            reminderSettingsError.textContent = 'Second reminder must be less than the first reminder.';
                            reminderSettingsError.classList.remove('hidden');
                        } else {
                            reminderSettingsError.textContent = '';
                            reminderSettingsError.classList.add('hidden');
                        }
                    }

                    return isValid;
                };

                if (firstReminderInput) {
                    firstReminderInput.addEventListener('input', validateReminderSettings);
                }

                if (secondReminderInput) {
                    secondReminderInput.addEventListener('input', validateReminderSettings);
                }

                if (reminderSettingsForm) {
                    reminderSettingsForm.addEventListener('submit', function (event) {
                        if (!validateReminderSettings()) {
                            event.preventDefault();
                        }
                    });
                }

                validateReminderSettings();

                calendar.on('dateClick', function (info) {
                    if (!startsAtInput) {
                        return;
                    }

                    const clicked = new Date(info.dateStr);
                    if (Number.isNaN(clicked.getTime())) {
                        return;
                    }

                    const yyyy = clicked.getFullYear();
                    const mm = String(clicked.getMonth() + 1).padStart(2, '0');
                    const dd = String(clicked.getDate()).padStart(2, '0');
                    startsAtInput.value = `${yyyy}-${mm}-${dd}T09:00`;
                    startsAtInput.focus();
                });
            }
        });
    </script>
</x-app-layout>