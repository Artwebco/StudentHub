<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Student;
use App\Models\LessonType;
use App\Models\StudentLessonPrice;
use App\Models\Lesson;

class LessonCreate extends Component
{
    use WithPagination;

    public $student_id, $lesson_type_id, $lesson_date, $notes;
    public $suggestedPrice = 0;
    public $editingLessonId = null; // За следење на измените

    protected $paginationTheme = 'tailwind';

    public function mount() {
        $this->lesson_date = now()->format('Y-m-d');
    }

    public function updated($propertyName) {
        if ($propertyName == 'student_id' || $propertyName == 'lesson_type_id') {
            if ($this->student_id && $this->lesson_type_id) {
                $priceRecord = StudentLessonPrice::where('student_id', $this->student_id)
                    ->where('lesson_type_id', $this->lesson_type_id)
                    ->first();
                
                $this->suggestedPrice = $priceRecord ? $priceRecord->price : 0;
            }
        }
    }

    public function editLesson($id) {
        $lesson = Lesson::findOrFail($id);
        $this->editingLessonId = $id;
        $this->student_id = $lesson->student_id;
        $this->lesson_type_id = $lesson->lesson_type_id;
        $this->lesson_date = $lesson->lesson_date;
        $this->notes = $lesson->notes;
        $this->suggestedPrice = $lesson->price_at_time;
    }

    public function deleteLesson($id) {
        Lesson::destroy($id);
        session()->flash('message', 'Часот е избришан.');
    }

    // ОВАА ФУНКЦИЈА СЕГА Е САМО ЕДНАШ
    public function saveLesson() {
        $this->validate([
            'student_id' => 'required',
            'lesson_type_id' => 'required',
            'lesson_date' => 'required|date',
        ]);

        if ($this->editingLessonId) {
            $lesson = Lesson::find($this->editingLessonId);
            $lesson->update([
                'student_id' => $this->student_id,
                'lesson_type_id' => $this->lesson_type_id,
                'price_at_time' => $this->suggestedPrice,
                'lesson_date' => $this->lesson_date,
                'notes' => $this->notes,
            ]);
            $this->editingLessonId = null;
            session()->flash('message', 'Часот е успешно изменет!');
        } else {
            Lesson::create([
                'student_id' => $this->student_id,
                'lesson_type_id' => $this->lesson_type_id,
                'price_at_time' => $this->suggestedPrice,
                'lesson_date' => $this->lesson_date,
                'notes' => $this->notes,
            ]);
            session()->flash('message', 'Часот е успешно евидентиран!');
            $this->resetPage(); // Врати на прва страна за нов запис
        }

        $this->reset(['student_id', 'lesson_type_id', 'notes', 'suggestedPrice']);
        $this->lesson_date = now()->format('Y-m-d');
    }

    public function render() {
        return view('livewire.lesson-create', [
            'students' => Student::where('active', true)->get(),
            'lessonTypes' => LessonType::all(),
            'lessonsLog' => Lesson::with(['student', 'lessonType'])
                ->latest()
                ->paginate(10)
        ]);
    }
}