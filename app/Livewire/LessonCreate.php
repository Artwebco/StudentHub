<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Student;
use App\Models\LessonTemplate;
use App\Models\Lesson;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\LessonsExport;
use Carbon\Carbon;

class LessonCreate extends Component
{
    use WithPagination;

    // Form fields
    public $student_id, $lesson_type_id, $lesson_date, $notes, $lesson_status = 'held';
    public $suggestedPrice = 0;
    public $editingLessonId = null;
    public $start_time, $end_time;

    // Dropdown state
    public $student_search = '';
    public $showDropdown = false;

    // Table filters
    public $search = '';
    public $filter_type = '';
    public $filter_status = '';
    public $filter_from_date = '';
    public $filter_to_date = '';

    protected $paginationTheme = 'tailwind';

    public function mount()
    {
        $this->lesson_date = now()->format('Y-m-d');
    }

    // Функција за избор на студент од листата
    public function selectStudent($id)
    {
        $this->student_id = $id;
        $this->student_search = ''; // Чистиме пребарување
        $this->showDropdown = false; // Затвораме паѓачко мени
        $this->calculatePrice();
    }

    public function updatedLessonTypeId()
    {
        $this->calculatePrice();
    }

    public function calculatePrice()
    {
        if ($this->lesson_type_id) {
            $template = \App\Models\LessonTemplate::find($this->lesson_type_id);

            // Сменето од 'price' во 'default_price'
            $this->suggestedPrice = $template ? $template->default_price : 0;
        } else {
            $this->suggestedPrice = 0;
        }
    }

    public function saveLesson()
    {
        $this->start_time = $this->normalizeTime($this->start_time);
        $this->end_time = $this->normalizeTime($this->end_time);

        $this->validate(
            [
                'student_id' => 'required',
                'lesson_type_id' => 'required',
                'lesson_date' => 'required|date',
                'lesson_status' => 'required|in:held,not_held',
                'start_time' => 'nullable|required_if:lesson_status,held|date_format:H:i',
                'end_time' => 'nullable|required_if:lesson_status,held|date_format:H:i|after:start_time',
            ],
            [
                'student_id.required' => __('admin.lessons.validation.student_required'),
                'lesson_type_id.required' => __('admin.lessons.validation.lesson_type_required'),
                'lesson_date.required' => __('admin.lessons.validation.date_required'),
                'lesson_date.date' => __('admin.lessons.validation.date_invalid'),
                'lesson_status.required' => __('admin.lessons.validation.status_required'),
                'lesson_status.in' => __('admin.lessons.validation.status_invalid'),
                'start_time.required' => __('admin.lessons.validation.start_required'),
                'start_time.required_if' => __('admin.lessons.validation.start_required_if'),
                'start_time.date_format' => __('admin.lessons.validation.start_invalid'),
                'end_time.required' => __('admin.lessons.validation.end_required'),
                'end_time.required_if' => __('admin.lessons.validation.end_required_if'),
                'end_time.date_format' => __('admin.lessons.validation.end_invalid'),
                'end_time.after' => __('admin.lessons.validation.end_after'),
            ]
        );

        $data = [
            'student_id' => $this->student_id,
            'lesson_type_id' => $this->lesson_type_id,
            'lesson_date' => $this->lesson_date,
            'price_at_time' => $this->suggestedPrice,
            'start_time' => $this->start_time,
            'end_time' => $this->end_time,
            'lesson_status' => $this->lesson_status,
            'notes' => $this->notes,
        ];

        if ($this->editingLessonId) {
            Lesson::find($this->editingLessonId)->update($data);
            $this->editingLessonId = null;
            session()->flash('message', __('admin.lessons.updated'));
        } else {
            Lesson::create($data);
            session()->flash('message', __('admin.lessons.saved'));
        }

        $this->resetFields();
    }

    private function normalizeTime($value)
    {
        if (empty($value)) {
            return null;
        }

        $value = trim((string) $value);

        foreach (['H:i', 'H:i:s', 'g:i A', 'h:i A', 'g:i a', 'h:i a'] as $format) {
            try {
                return Carbon::createFromFormat($format, $value)->format('H:i');
            } catch (\Throwable $e) {
                // Try next known format.
            }
        }

        try {
            return Carbon::parse($value)->format('H:i');
        } catch (\Throwable $e) {
            return $value;
        }
    }

    public function editLesson($id)
    {
        $lesson = Lesson::findOrFail($id);
        $this->editingLessonId = $id;
        $this->student_id = $lesson->student_id;
        $this->lesson_type_id = $lesson->lesson_type_id;
        $this->lesson_date = \Carbon\Carbon::parse($lesson->lesson_date)->format('Y-m-d');
        $this->start_time = $this->normalizeTime($lesson->start_time);
        $this->end_time = $this->normalizeTime($lesson->end_time);
        $this->lesson_status = ($lesson->lesson_status === 'held') ? 'held' : 'not_held';
        $this->notes = $lesson->notes;
        $this->suggestedPrice = $lesson->price_at_time;
    }

    public function resetFields()
    {
        $this->reset(['student_id', 'lesson_type_id', 'notes', 'suggestedPrice', 'editingLessonId', 'student_search', 'showDropdown', 'start_time', 'end_time', 'lesson_status']);
        $this->lesson_date = now()->format('Y-m-d');
        $this->lesson_status = 'held';
    }

    public function deleteLesson($id)
    {
        Lesson::destroy($id);
        session()->flash('message', __('admin.lessons.deleted'));
    }

    public function exportExcel()
    {
        return Excel::download(new LessonsExport($this->search, $this->filter_type, $this->filter_status, $this->filter_from_date, $this->filter_to_date), "dnevnik.xlsx");
    }

    public function render()
    {
        // Листа на студенти за Custom Dropdown
        $studentsForSelect = Student::where('active', true)
            ->when($this->student_search, function ($q) {
                $q->where('first_name', 'like', '%' . $this->student_search . '%')
                    ->orWhere('last_name', 'like', '%' . $this->student_search . '%');
            })->orderBy('first_name')->get();

        // Главна табела
        $query = Lesson::with(['student', 'lessonType']);
        if ($this->search) {
            $query->whereHas('student', function ($q) {
                $q->where('first_name', 'like', '%' . $this->search . '%')
                    ->orWhere('last_name', 'like', '%' . $this->search . '%');
            });
        }
        if ($this->filter_type)
            $query->where('lesson_type_id', $this->filter_type);
        if ($this->filter_status) {
            if ($this->filter_status === 'not_held') {
                $query->whereIn('lesson_status', ['not_held', 'scheduled', 'cancelled']);
            } else {
                $query->where('lesson_status', 'held');
            }
        }
        if ($this->filter_from_date)
            $query->where('lesson_date', '>=', $this->filter_from_date);
        if ($this->filter_to_date)
            $query->where('lesson_date', '<=', $this->filter_to_date);

        return view('livewire.lesson-create', [
            'lessonsLog' => $query->orderBy('lesson_date', 'desc')->paginate(10),
            'lessonTypes' => LessonTemplate::all(),
            'studentsForSelect' => $studentsForSelect,
            'totalAmount' => $query->sum('price_at_time')
        ]);
    }
}
