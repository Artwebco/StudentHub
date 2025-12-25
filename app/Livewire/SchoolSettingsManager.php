<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Invoice;
use App\Models\Student;
use App\Models\Lesson;
use App\Models\SchoolSetting;
use Barryvdh\DomPDF\Facade\Pdf;

class InvoiceManagement extends Component
{
    use WithPagination;

    public $showCreateModal = false;
    public $student_id, $date_from, $date_to;
    public $search = '';

    protected $rules = [
        'student_id' => 'required',
        'date_from' => 'required|date',
        'date_to' => 'required|date|after_or_equal:date_from',
    ];

    public function createInvoice()
    {
        $this->validate();

        $lessons = Lesson::where('student_id', $this->student_id)
            ->whereBetween('lesson_date', [$this->date_from, $this->date_to])
            ->get();

        if ($lessons->isEmpty()) {
            session()->flash('error', 'Нема пронајдено часови за овој ученик во избраниот период.');
            return;
        }

        // ПРЕСМЕТКА: Збирот на сите цени на часовите
        $totalAmount = $lessons->sum('price_at_time');

        Invoice::create([
            'invoice_number' => Invoice::generateNextInvoiceNumber(),
            'student_id' => $this->student_id,
            'date_from' => $this->date_from,
            'date_to' => $this->date_to,
            'total_amount' => $totalAmount,
            'is_paid' => false,
        ]);

        $this->reset(['student_id', 'date_from', 'date_to', 'showCreateModal']);
        session()->flash('message', 'Фактурата е успешно генерирана.');
    }

    public function togglePaid($id)
    {
        $invoice = Invoice::find($id);
        $invoice->is_paid = !$invoice->is_paid;
        $invoice->save();
    }

    public function deleteInvoice($id)
    {
        Invoice::destroy($id);
        session()->flash('message', 'Фактурата е избришана.');
    }

    // ОВА Е МЕТОДОТ ШТО ПРАВЕШЕ ПРОБЛЕМ - СЕГА Е ВНАТРЕ ВО КЛАСАТА
    public function downloadInvoice($invoiceId)
    {
        $invoice = Invoice::with('student')->findOrFail($invoiceId);
        $settings = SchoolSetting::first();

        if (!$settings) {
            session()->flash('error', 'Прво внесете ги податоците за училиштето во Подесувања!');
            return;
        }

        $lessons = Lesson::where('student_id', $invoice->student_id)
            ->whereBetween('lesson_date', [$invoice->date_from, $invoice->date_to])
            ->with('lessonType')
            ->get();

        $data = [
            'invoice' => $invoice,
            'settings' => $settings,
            'lessons' => $lessons,
        ];

        $pdf = Pdf::loadView('pdf.invoice_final', $data);

        return response()->streamDownload(function () use ($pdf) {
            echo $pdf->stream();
        }, "Faktura_{$invoice->invoice_number}.pdf");
    }

    public function render()
    {
        return view('livewire.invoice-management', [
            'invoices' => Invoice::with('student')
                ->whereHas('student', function ($q) {
                    $q->where('first_name', 'like', '%' . $this->search . '%')
                        ->orWhere('last_name', 'like', '%' . $this->search . '%');
                })
                ->orderBy('created_at', 'desc')
                ->paginate(15),
            'students' => Student::where('active', true)->orderBy('first_name')->get()
        ]);
    }
}
