<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Student;
use App\Models\LessonType;
use App\Models\StudentLessonPrice;

class StudentPriceManager extends Component
{
    public $selectedStudent = null;
    public $prices = []; 

    public function updatedSelectedStudent($studentId)
    {

        session()->forget('message');
        
        if (!$studentId) return;

        $existingPrices = StudentLessonPrice::where('student_id', $studentId)->get();
        
        $this->prices = [];
        foreach ($existingPrices as $p) {
            $this->prices[$p->lesson_type_id] = $p->price;
        }
    }

    public function save()
    {
        foreach ($this->prices as $typeId => $amount) {
            if ($amount != '') {
                StudentLessonPrice::updateOrCreate(
                    ['student_id' => $this->selectedStudent, 'lesson_type_id' => $typeId],
                    ['price' => $amount]
                );
            }
        }
        session()->flash('message', 'Успешно зачувани индивидуални цени!');
    }

    public function render()
    {
        return view('livewire.student-price-manager', [
            'students' => Student::where('active', true)->get(),
            'lessonTypes' => LessonType::all()
        ]);
    }
}