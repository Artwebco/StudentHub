<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\LessonTemplate;

class StudentPrices extends Component
{
    public $name, $description, $duration, $default_price;
    public $editingId = null;
    public $showModal = false;

    protected $rules = [
        'name' => 'required|min:3',
        'duration' => 'required|numeric',
        'default_price' => 'required|numeric',
    ];

    // КЛУЧНА НОВА ФУНКЦИЈА: Се повикува кога кликате на "Нов тип на час"
    public function create()
    {
        $this->resetInputFields(); // Прво чистиме сè
        $this->showModal = true;    // Потоа отвораме
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->resetInputFields();
    }

    public function save()
    {
        $this->validate();

        if ($this->editingId) {
            $template = LessonTemplate::find($this->editingId);
            $template->update([
                'name' => $this->name,
                'description' => $this->description,
                'duration' => $this->duration,
                'default_price' => $this->default_price,
            ]);
        } else {
            LessonTemplate::create([
                'name' => $this->name,
                'description' => $this->description,
                'duration' => $this->duration,
                'default_price' => $this->default_price,
            ]);
        }

        $this->resetInputFields();
        $this->showModal = false;
        session()->flash('message', 'Успешно зачувано!');
    }

    public function edit($id)
    {
        $this->resetInputFields(); // Чистиме претходни остатоци за секој случај
        $template = LessonTemplate::findOrFail($id);
        $this->editingId = $id;
        $this->name = $template->name;
        $this->description = $template->description;
        $this->duration = $template->duration;
        $this->default_price = $template->default_price;
        $this->showModal = true;
    }

    public function delete($id)
    {
        LessonTemplate::find($id)->delete();
        session()->flash('message', 'Типот на час е избришан.');
    }

    private function resetInputFields()
    {
        $this->name = '';
        $this->description = '';
        $this->duration = '';
        $this->default_price = '';
        $this->editingId = null; // Ова гарантира дека следното снимање ќе биде нов запис
    }

    public function render()
    {
        return view('livewire.student-prices', [
            'templates' => LessonTemplate::orderBy('created_at', 'desc')->get()
        ])->layout('layouts.app');
    }
}
