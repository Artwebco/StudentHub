<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\LessonTemplate;

class StudentPrices extends Component
{
    public $name_en, $name_mk, $description, $duration, $default_price;
    public $editingId = null;
    public $showModal = false;

    protected $rules = [
        'name_en' => 'required|min:3',
        'name_mk' => 'required|min:3',
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
                'name' => $this->name_mk,
                'name_en' => $this->name_en,
                'name_mk' => $this->name_mk,
                'description' => $this->description,
                'duration' => $this->duration,
                'default_price' => $this->default_price,
            ]);
        } else {
            LessonTemplate::create([
                'name' => $this->name_mk,
                'name_en' => $this->name_en,
                'name_mk' => $this->name_mk,
                'description' => $this->description,
                'duration' => $this->duration,
                'default_price' => $this->default_price,
            ]);
        }

        $this->resetInputFields();
        $this->showModal = false;
        session()->flash('message', __('admin.pricing.saved_message'));
    }

    public function edit($id)
    {
        $this->resetInputFields(); // Чистиме претходни остатоци за секој случај
        $template = LessonTemplate::findOrFail($id);
        $this->editingId = $id;
        $this->name_en = $template->name_en ?: $template->name;
        $this->name_mk = $template->name_mk ?: $template->name;
        $this->description = $template->description;
        $this->duration = $template->duration;
        $this->default_price = $template->default_price;
        $this->showModal = true;
    }

    public function delete($id)
    {
        LessonTemplate::find($id)->delete();
        session()->flash('message', __('admin.pricing.deleted_message'));
    }

    private function resetInputFields()
    {
        $this->name_en = '';
        $this->name_mk = '';
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
