<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Student;
use App\Models\LessonType;
use App\Models\StudentLessonPrice;
use App\Models\Lesson;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\LessonsExport;

class LessonCreate extends Component
{
    use WithPagination;

    // Form properties
    public $student_id, $lesson_type_id, $lesson_date, $notes;
    public $suggestedPrice = 0;
    public $editingLessonId = null;
    public $start_time, $end_time;

    // Filter properties
    public $search = '';
    public $filter_type = '';
    public $filter_from_date = '';
    public $filter_to_date = '';

    protected $paginationTheme = 'tailwind';

    public function exportExcel()
    {
        // Тука ја повикуваш истата логика за филтрирање
        // и го генерираш фајлот преку Laravel Excel
        $date = now()->format('d-m-Y');
        $filename = "dnevnik_chasovi_{$date}.xlsx";
        return Excel::download(new LessonsExport($this->search, $this->filter_type, $this->filter_from_date, $this->filter_to_date), $filename);
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function mount()
    {
        $this->lesson_date = now()->format('Y-m-d');
    }

    public function resetFields()
    {
        $this->reset(['student_id', 'lesson_type_id', 'notes', 'suggestedPrice', 'start_time', 'end_time', 'lesson_date', 'editingLessonId']);
        $this->lesson_date = now()->format('Y-m-d');
        $this->resetValidation(); // Ги трга црвените пораки за грешка
    }

    public function updated($propertyName)
    {
        if ($propertyName == 'student_id' || $propertyName == 'lesson_type_id') {
            if ($this->student_id && $this->lesson_type_id) {
                $priceRecord = StudentLessonPrice::where('student_id', $this->student_id)
                    ->where('lesson_type_id', $this->lesson_type_id)
                    ->first();

                $this->suggestedPrice = $priceRecord ? $priceRecord->price : 0;
            }
        }
    }

    public function editLesson($id)
    {
        $lesson = Lesson::findOrFail($id);
        $this->editingLessonId = $id;
        $this->student_id = $lesson->student_id;
        $this->lesson_type_id = $lesson->lesson_type_id;

        $this->lesson_date = \Carbon\Carbon::parse($lesson->lesson_date)->format('Y-m-d');

        $this->start_time = \Carbon\Carbon::parse($lesson->start_time)->format('H:i');
        $this->end_time = \Carbon\Carbon::parse($lesson->end_time)->format('H:i');
        $this->notes = $lesson->notes;
        $this->suggestedPrice = $lesson->price_at_time;
    }

    public function deleteLesson($id)
    {
        Lesson::destroy($id);
        session()->flash('message', 'Успешно избришан час.');
    }

    public function saveLesson()
    {
        // Validation with Macedonian messages
        $this->validate([
            'student_id' => 'required',
            'lesson_type_id' => 'required',
            'lesson_date' => 'required|date',
            'start_time' => 'required',
            'end_time' => 'required',
        ], [
            'student_id.required' => 'Изберете ученик',
            'lesson_type_id.required' => 'Изберете тип на час',
            'start_time.required' => 'Внесете почеток',
            'end_time.required' => 'Внесете крај',
            'lesson_date.required' => 'Внесете датум',
        ]);

        $data = [
            'student_id' => $this->student_id,
            'lesson_type_id' => $this->lesson_type_id,
            'price_at_time' => $this->suggestedPrice,
            'lesson_date' => $this->lesson_date,
            'start_time' => $this->start_time,
            'end_time' => $this->end_time,
            'notes' => $this->notes,
        ];

        if ($this->editingLessonId) {
            Lesson::find($this->editingLessonId)->update($data);
            $this->editingLessonId = null;
            session()->flash('message', 'Часот е успешно ажуриран!');
        } else {
            Lesson::create($data);
            session()->flash('message', 'Часот е успешно зачуван!');
            $this->resetPage();
        }

        $this->reset(['student_id', 'lesson_type_id', 'notes', 'suggestedPrice', 'start_time', 'end_time']);
        $this->lesson_date = now()->format('Y-m-d');
    }

    public function render()
    {
        $query = Lesson::with(['student', 'lessonType']);

        if ($this->search) {
            $query->whereHas('student', function ($q) {
                $q->where('first_name', 'like', '%' . $this->search . '%')
                    ->orWhere('last_name', 'like', '%' . $this->search . '%');
            });
        }

        if ($this->filter_type) {
            $query->where('lesson_type_id', $this->filter_type);
        }

        if ($this->filter_from_date) {
            $query->where('lesson_date', '>=', $this->filter_from_date);
        }

        if ($this->filter_to_date) {
            $query->where('lesson_date', '<=', $this->filter_to_date);
        }

        $totalAmount = $query->sum('price_at_time');

        return view('livewire.lesson-create', [
            'students' => Student::where('active', true)->get(),
            'lessonTypes' => LessonType::all(),
            'lessonsLog' => $query->orderBy('lesson_date', 'desc')
                ->orderBy('start_time', 'desc')
                ->paginate(20),
            'totalAmount' => $totalAmount
        ]);
    }
}
