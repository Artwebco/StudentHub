<?php
namespace App\Livewire;

use Livewire\Component;
use App\Models\Lesson;
use App\Models\Invoice; // Провери дали моделот е Invoice или Bill
use Illuminate\Support\Facades\Auth;

class MyStatistic extends Component
{
    public $tab = 'lessons'; // Почетен таб
    public $search = '';
    public $status = 'all';

    public function setTab($tabName)
    {
        $this->tab = $tabName;
        $this->search = ''; // Ресетирај пребарување при промена на таб
    }

    public function render()
    {
        $user = Auth::user();

        // Податоци за Дневник (Лекции)
        $lessons = Lesson::where('user_id', $user->id)
            ->when($this->search, function ($q) {
                $q->where('lesson_date', 'like', '%' . $this->search . '%');
            })
            ->orderBy('lesson_date', 'desc')
            ->get();

        // Податоци за Фактури
        $invoices = Invoice::where('user_id', $user->id)
            ->when($this->search, function ($q) {
                $q->where('invoice_number', 'like', '%' . $this->search . '%')
                    ->orWhere('period_name', 'like', '%' . $this->search . '%');
            })
            ->orderBy('created_at', 'desc')
            ->get();

        $unpaid_amount = $invoices->where('status', 'unpaid')->sum('total_amount');

        return view('livewire.my-statistic', [
            'lessons' => $lessons,
            'invoices' => $invoices,
            'unpaid_amount' => $unpaid_amount,
            'stats' => [
                'total_count' => $lessons->count(),
                'total_minutes' => $lessons->sum('duration'),
                'last_lesson' => $lessons->first()?->lesson_date ?
                    \Carbon\Carbon::parse($lessons->first()->lesson_date)->format('d.m.Y') : 'Нема записи'
            ]
        ]);
    }
}
