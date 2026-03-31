<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;
use App\Models\Lesson;
use App\Models\Invoice;
use App\Models\SchoolSetting;
use App\Support\AmountInWords;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

#[Layout('layouts.app')]
class LessonStats extends Component
{
    use WithPagination;

    protected $paginationTheme = 'tailwind';

    public $tab = 'lessons';
    public $search = '';
    public $status = 'all';
    public $selectedYear;
    public $selectedMonth = 'all';

    public function mount()
    {
        $this->selectedYear = date('Y');
    }

    private function attachAdvanceProgress($invoices): void
    {
        $invoices->getCollection()->transform(function ($invoice) {
            $invoice->expected_lessons = null;
            $invoice->realized_lessons = null;
            $invoice->remaining_lessons = null;
            $invoice->progress_state = null;

            if (!$invoice->is_advance || !$invoice->student_id || !$invoice->date_from || !$invoice->date_to) {
                return $invoice;
            }

            $expectedLessons = (int) ($invoice->quantity ?? 0);
            $realizedLessons = Lesson::where('student_id', $invoice->student_id)
                ->whereBetween('lesson_date', [$invoice->date_from, $invoice->date_to])
                ->where('lesson_status', 'held')
                ->count();

            $remainingLessons = $expectedLessons - $realizedLessons;

            $invoice->expected_lessons = $expectedLessons;
            $invoice->realized_lessons = $realizedLessons;
            $invoice->remaining_lessons = $remainingLessons;

            if ($remainingLessons > 0) {
                $invoice->progress_state = 'under';
            } elseif ($remainingLessons < 0) {
                $invoice->progress_state = 'over';
            } else {
                $invoice->progress_state = 'done';
            }

            return $invoice;
        });
    }

    public function setTab($tabName)
    {
        $this->tab = $tabName;
        $this->search = '';
        $this->status = 'all';
        $this->resetPage('lessonsPage');
        $this->resetPage('invoicesPage');
    }

    public function updatedSearch()
    {
        $this->resetPage('lessonsPage');
        $this->resetPage('invoicesPage');
    }

    public function updatedStatus()
    {
        $this->resetPage('lessonsPage');
        $this->resetPage('invoicesPage');
    }

    // --- НОВО: Функција за преземање на PDF ---
    public function downloadInvoice($invoiceId)
    {
        $invoice = Invoice::with('student')->findOrFail($invoiceId);
        $settings = SchoolSetting::first();

        if (!$settings) {
            session()->flash('error', 'Прво внесете ги податоците за училиштето во Подесувања!');
            return;
        }

        $lessons = collect();
        if ($invoice->student_id) {
            $lessons = Lesson::where('student_id', $invoice->student_id)
                ->whereBetween('lesson_date', [$invoice->date_from, $invoice->date_to])
                ->where('lesson_status', 'held')
                ->with('lessonType')
                ->get();
        }

        $sigPath = storage_path('app/signatures/signature.png');
        $sigData = null;

        if (file_exists($sigPath)) {
            $type = pathinfo($sigPath, PATHINFO_EXTENSION);
            $fileData = file_get_contents($sigPath);
            $sigData = 'data:image/' . $type . ';base64,' . base64_encode($fileData);
        }

        $amountInWords = $this->convertAmountToWords($invoice->total_amount);

        $data = [
            'invoice' => $invoice,
            'settings' => $settings,
            'lessons' => $lessons,
            'amountInWords' => $amountInWords,
            'sigData' => $sigData
        ];

        $pdf = Pdf::loadView('pdf.invoice_final', $data);

        return response()->streamDownload(function () use ($pdf) {
            echo $pdf->stream();
        }, "Faktura_" . str_replace(['/', '\\'], '-', $invoice->invoice_number) . ".pdf");
    }

    // --- НОВО: Помошна функција за зборови ---
    private function convertAmountToWords($amount)
    {
        return AmountInWords::mkdDenars($amount);
    }

    public function render()
    {
        $user = Auth::user();

        $lessonsQuery = Lesson::where('student_id', $user->id)
            ->when($this->tab === 'lessons' && $this->search, function ($q) {
                $q->where('lesson_date', 'like', '%' . $this->search . '%');
            })
            ->when($this->tab === 'lessons' && $this->status !== 'all', function ($q) {
                if ($this->status === 'not_held') {
                    $q->whereIn('lesson_status', ['not_held', 'scheduled', 'cancelled']);
                } else {
                    $q->where('lesson_status', 'held');
                }
            })
            ->orderBy('lesson_date', 'desc');

        $lessons = $lessonsQuery->paginate(10, ['*'], 'lessonsPage');

        $invoicesQuery = Invoice::where('student_id', $user->id)
            ->when($this->tab === 'invoices' && $this->search, function ($q) {
                $q->where('invoice_number', 'like', '%' . $this->search . '%');
            })
            ->when($this->status !== 'all', function ($q) {
                $q->where('status', $this->status);
            })
            ->orderBy('created_at', 'desc');

        $invoices = $invoicesQuery->paginate(10, ['*'], 'invoicesPage');
        $this->attachAdvanceProgress($invoices);

        $unpaid_amount = (clone $invoicesQuery)->where('status', 'unpaid')->sum('total_amount');

        $stats = [
            'total_count' => (clone $lessonsQuery)->count(),
            'total_minutes' => (clone $lessonsQuery)->join('lesson_types', 'lessons.lesson_type_id', '=', 'lesson_types.id')->sum('lesson_types.duration'),
            'last_lesson' => ($lastLesson = (clone $lessonsQuery)->first())
                ? Carbon::parse($lastLesson->lesson_date)->translatedFormat('d M, Y')
                : 'Нема записи'
        ];

        return view('livewire.lesson-stats', [
            'lessons' => $lessons,
            'invoices' => $invoices,
            'unpaid_amount' => $unpaid_amount,
            'stats' => $stats,
            'totalFilteredAmount' => (clone $invoicesQuery)->sum('total_amount')
        ]);
    }
}
