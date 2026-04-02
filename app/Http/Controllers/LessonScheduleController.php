<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\AppointmentSetting;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class LessonScheduleController extends Controller
{
    public function index(Request $request): View
    {
        $students = collect();
        $reminderSettings = null;

        if ($request->user()->role === 'admin') {
            $students = User::query()
                ->where('role', 'student')
                ->where('is_active', true)
                ->orderBy('name')
                ->get(['id', 'name', 'email']);

            $reminderSettings = AppointmentSetting::query()->firstOrCreate(
                [],
                [
                    'first_reminder_minutes_before' => 1440,
                    'second_reminder_minutes_before' => 30,
                ]
            );
        }

        return view('lesson-schedule', [
            'students' => $students,
            'reminderSettings' => $reminderSettings,
        ]);
    }

    public function events(Request $request): JsonResponse
    {
        $user = $request->user();

        $appointments = Appointment::query()
            ->with(['student:id,name', 'admin:id,name'])
            ->where('status', 'scheduled')
            ->when($user->role !== 'admin', function ($query) use ($user) {
                $query->where('student_id', $user->id);
            })
            ->orderBy('starts_at')
            ->get();

        $events = $appointments->map(function (Appointment $appointment) use ($user) {
            $title = $user->role === 'admin'
                ? ($appointment->student?->name ?? 'Student')
                : 'Lesson with ' . ($appointment->admin?->name ?? 'Admin');

            return [
                'id' => $appointment->id,
                'title' => $title,
                'start' => $appointment->starts_at?->format('Y-m-d\\TH:i:s'),
                'end' => $appointment->ends_at?->format('Y-m-d\\TH:i:s'),
                'extendedProps' => [
                    'note' => $appointment->note,
                    'student_name' => $appointment->student?->name,
                    'admin_name' => $appointment->admin?->name,
                ],
            ];
        });

        return response()->json($events);
    }

    public function store(Request $request): RedirectResponse
    {
        if ($request->user()->role !== 'admin') {
            abort(403);
        }

        $validated = $request->validate([
            'student_id' => [
                'required',
                Rule::exists('users', 'id')->where(fn($query) => $query->where('role', 'student')),
            ],
            'starts_at' => ['required', 'date'],
            'duration_minutes' => ['required', 'integer', 'min:15', 'max:240'],
            'note' => ['nullable', 'string', 'max:1000'],
        ]);

        $startsAt = now()->parse($validated['starts_at']);
        $endsAt = (clone $startsAt)->addMinutes((int) $validated['duration_minutes']);

        Appointment::create([
            'admin_id' => $request->user()->id,
            'student_id' => $validated['student_id'],
            'starts_at' => $startsAt,
            'ends_at' => $endsAt,
            'status' => 'scheduled',
            'note' => $validated['note'] ?? null,
        ]);

        return redirect()
            ->route('lesson-schedule')
            ->with('success', 'Lesson has been scheduled successfully.');
    }

    public function update(Request $request, Appointment $appointment): JsonResponse
    {
        if ($request->user()->role !== 'admin') {
            abort(403);
        }

        if ($appointment->status !== 'scheduled') {
            return response()->json([
                'message' => 'Only scheduled lessons can be updated.',
            ], 422);
        }

        $validated = $request->validate([
            'starts_at' => ['required', 'date'],
            'ends_at' => ['required', 'date', 'after:starts_at'],
        ]);

        $appointment->update([
            'starts_at' => now()->parse($validated['starts_at']),
            'ends_at' => now()->parse($validated['ends_at']),
            'reminder_24h_sent_at' => null,
            'reminder_30m_sent_at' => null,
        ]);

        return response()->json([
            'message' => 'Lesson was rescheduled successfully.',
        ]);
    }

    public function destroy(Request $request, Appointment $appointment): JsonResponse
    {
        if ($request->user()->role !== 'admin') {
            abort(403);
        }

        if ($appointment->status !== 'scheduled') {
            return response()->json([
                'message' => 'Only scheduled lessons can be cancelled.',
            ], 422);
        }

        $appointment->update([
            'status' => 'cancelled',
        ]);

        return response()->json([
            'message' => 'Lesson was cancelled successfully.',
        ]);
    }

    public function updateReminderSettings(Request $request): RedirectResponse
    {
        if ($request->user()->role !== 'admin') {
            abort(403);
        }

        $validated = $request->validate([
            'first_reminder_minutes_before' => ['required', 'integer', 'min:10', 'max:10080'],
            'second_reminder_minutes_before' => ['required', 'integer', 'min:5', 'lt:first_reminder_minutes_before'],
        ]);

        $settings = AppointmentSetting::query()->firstOrCreate(
            [],
            [
                'first_reminder_minutes_before' => 1440,
                'second_reminder_minutes_before' => 30,
            ]
        );

        $settings->update($validated);

        return redirect()
            ->route('lesson-schedule')
            ->with('success', 'Reminder settings updated successfully.');
    }
}
