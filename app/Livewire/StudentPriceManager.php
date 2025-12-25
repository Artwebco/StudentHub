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
        $this->prices = [];
        session()->forget('message');

        if (!$studentId)
            return;

        // 1. Земи ги сите типови на часови
        $allTypes = LessonType::all();

        // 2. Земи ги веќе постоечките цени од базата
        $existingPrices = StudentLessonPrice::where('student_id', $studentId)
            ->pluck('price', 'lesson_type_id')
            ->toArray();

        // 3. Наполни ја низата за СЕКОЈ тип за да се појават полињата веднаш
        foreach ($allTypes as $type) {
            $this->prices[$type->id] = $existingPrices[$type->id] ?? '';
        }
    }

    public function save()
    {
        if (!$this->selectedStudent)
            return;

        foreach ($this->prices as $typeId => $amount) {
            if ($amount !== '' && $amount !== null) {
                StudentLessonPrice::updateOrCreate(
                    ['student_id' => $this->selectedStudent, 'lesson_type_id' => $typeId],
                    ['price' => $amount]
                );
            }
        }
        session()->flash('message', 'Цените се успешно зачувани!');
    }

    public function render()
    {
        return view('livewire.student-price-manager', [
            'students' => Student::where('active', true)->orderBy('first_name')->get(),
            'lessonTypes' => LessonType::all()
        ]);
    }
}
