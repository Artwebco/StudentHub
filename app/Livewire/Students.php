<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Student;
use App\Models\User;
use App\Notifications\StudentWelcomeNotification;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Hash;

class Students extends Component
{
    use WithPagination;

    public $first_name, $last_name, $email, $phone, $country;
    public $password = null;
    public $active = true, $invoice_type = 'individual', $hourly_rate = 0;
    public $studentId;
    public $isOpen = false;
    public $showArchived = false;

    // Својства за пребарување и сортирање
    public $search = '';
    public $sortField = 'created_at';
    public $sortDirection = 'desc';

    // Ресетирај пагинација кога корисникот пребарува
    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function sortBy($field)
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'desc';
        }
    }

    public function render()
    {
        $query = Student::with('user')
            ->withCount('lessons')
            ->withSum('lessons', 'price_at_time')
            ->withSum('invoices', 'total_amount');

        // Филтер за архива (Soft Delete)
        if ($this->showArchived) {
            $query->onlyTrashed();
        }

        // Логика за пребарување (Име, Презиме, Емаил, Телефон, Држава)
        if (!empty($this->search)) {
            $query->where(function ($q) {
                $q->where('first_name', 'like', '%' . $this->search . '%')
                    ->orWhere('last_name', 'like', '%' . $this->search . '%')
                    ->orWhere('email', 'like', '%' . $this->search . '%')
                    ->orWhere('phone', 'like', '%' . $this->search . '%')
                    ->orWhere('country', 'like', '%' . $this->search . '%');
            });
        }

        // Динамично сортирање
        $query->orderBy($this->sortField, $this->sortDirection);

        return view('livewire.students-registration', [
            'students' => $query->paginate(10),
            'lessonTemplates' => \App\Models\LessonTemplate::all()
        ])->layout('layouts.app');
    }

    public function restore($id)
    {
        $student = Student::withTrashed()->findOrFail($id);
        if ($student->user()->withTrashed()->exists()) {
            $student->user()->withTrashed()->restore();
        }
        $student->restore();
        session()->flash('message', 'Ученикот е успешно вратен во листата.');
    }

    public function toggleActive($id)
    {
        $student = Student::withTrashed()->findOrFail($id);
        $newStatus = $student->active ? 0 : 1;

        $student->active = $newStatus;
        $student->save();

        if ($student->user) {
            $user = $student->user;
            $user->is_active = $newStatus;
            $user->save();
        }

        session()->flash('message', 'Статусот е ажуриран.');
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
        $this->hourly_rate = 0;
        $this->search = ''; // Опционално чистење на пребарувањето
    }

    public function store()
    {
        $rules = [
            'first_name' => 'required',
            'last_name' => 'required',
            'email' => 'required|email',
        ];
        if ($this->studentId) {
            $student = Student::find($this->studentId);
            $userId = $student ? $student->user_id : null;
            $rules['email'] .= '|unique:users,email,' . $userId;
        } else {
            $rules['email'] .= '|unique:users,email';
        }
        $this->validate($rules);

        if ($this->studentId) {
            $student = Student::find($this->studentId);
            $student->update([
                'first_name' => $this->first_name,
                'last_name' => $this->last_name,
                'email' => $this->email,
                'phone' => $this->phone,
                'country' => $this->country,
                'active' => $this->active,
                'invoice_type' => $this->invoice_type,
            ]);

            $student->user->update([
                'name' => $this->first_name . ' ' . $this->last_name,
                'email' => $this->email,
            ]);
        } else {
            $user = User::create([
                'name' => $this->first_name . ' ' . $this->last_name,
                'email' => $this->email,
                // Постави привремена лозинка, но корисникот ќе добие линк за ресет
                'password' => Hash::make(bin2hex(random_bytes(8))),
                'role' => 'student',
            ]);

            $student = Student::create([
                'user_id' => $user->id,
                'first_name' => $this->first_name,
                'last_name' => $this->last_name,
                'email' => $this->email,
                'phone' => $this->phone,
                'country' => $this->country,
                'active' => $this->active,
                'invoice_type' => $this->invoice_type,
            ]);

            // Испрати welcome email со валиден линк за поставување лозинка
            $token = app('auth.password.broker')->createToken($user);
            $resetUrl = url(route('password.reset', [
                'token' => $token,
                'email' => $user->email,
            ], false));

            $user->notify(new StudentWelcomeNotification($resetUrl));
        }

        session()->flash('message', $this->studentId ? 'Успешно ажурирано.' : 'Успешно креиран ученик.');
        $this->isOpen = false;
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
        $student = Student::findOrFail($id);
        if ($student->user) {
            $student->user->delete();
        }
        $student->delete();
        session()->flash('message', 'Ученикот е архивиран.');
    }

    public function forceDeleteStudent($id)
    {
        $student = Student::withTrashed()->findOrFail($id);

        if (!$student->trashed()) {
            session()->flash('error', 'Трајно бришење е дозволено само за архивирани ученици.');
            return;
        }


        // Избриши ги сите часови поврзани со ученикот
        $student->lessons()->delete();

        // Избриши ги сите фактури поврзани со ученикот
        $student->invoices()->delete();

        $user = $student->user()->withTrashed()->first();
        if ($user) {
            $user->forceDelete();
        }

        $student->forceDelete();
        session()->flash('message', 'Ученикот и сите негови часови се трајно избришани.');
    }
}
