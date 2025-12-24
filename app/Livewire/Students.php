<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Student;
use Livewire\WithPagination;

class Students extends Component
{
    use WithPagination;

    public $first_name, $last_name, $email, $phone, $country, $active = true, $invoice_type = 'individual', $company_id, $hourly_rate;
    public $studentId;
    public $isOpen = 0;

    public function render()
    {
        return view('livewire.students', [
            'students' => Student::latest()->paginate(20)
        ])->layout('layouts.app');
    }

    public function create()
    {
        $this->resetInputFields();
        $this->openModal();
    }

    public function openModal()
    {
        $this->isOpen = true;
    }

    public function closeModal()
    {
        $this->isOpen = false;
    }

    private function resetInputFields()
    {
        $this->studentId = null;
        $this->first_name = '';
        $this->last_name = '';
        $this->email = '';
        $this->phone = '';
        $this->country = '';
        $this->active = true;
        $this->invoice_type = 'individual';
        $this->company_id = null;
        $this->hourly_rate = 0;
    }

    public function store()
    {
        $this->validate([
            'first_name' => 'required',
            'last_name' => 'required',
            'email' => 'nullable|email',
        ]);

        Student::updateOrCreate(['id' => $this->studentId], [
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'email' => $this->email,
            'phone' => $this->phone,
            'country' => $this->country,
            'active' => $this->active,
            'invoice_type' => $this->invoice_type,
            'company_id' => $this->company_id,
            'hourly_rate' => $this->hourly_rate,
        ]);

        session()->flash(
            'message',
            $this->studentId ? 'Корисникот е успешно ажуриран.' : 'Корисникот е успешно креиран.'
        );

        $this->closeModal();
        $this->resetInputFields();
    }

    public function edit($id)
    {
        $student = Student::findOrFail($id);
        $this->studentId = $id;
        $this->first_name = $student->first_name;
        $this->last_name = $student->last_name;
        $this->email = $student->email;
        $this->phone = $student->phone;
        $this->country = $student->country;
        $this->active = $student->active;
        $this->invoice_type = $student->invoice_type;

        $this->openModal();
    }

    public function delete($id)
    {
        Student::find($id)->delete();
        session()->flash('message', 'Корисникот е избришан.');
    }
}
