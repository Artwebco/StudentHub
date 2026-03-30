<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Invoice;
use App\Models\Student;
use App\Models\Lesson;
use App\Models\SchoolSetting;
use App\Models\LessonTemplate;
use App\Mail\InvoiceCreatedMail;
use App\Support\AmountInWords;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Mail;

class InvoiceManagement extends Component
{
    use WithPagination;

    // Својства за пребарување и модал
    public $student_search = '';
    public $showCreateModal = false;
    public $student_id, $date_from, $date_to;

    // Својства за филтрирање на табелата
    public $search = '';
    public $filter_status = ''; // НОВО
    public $filter_type = '';   // НОВО
    public $totalFilteredAmount = 0;

    // Својства за креирање
    public $invoice_type = 'student';
    public $service_client_name, $service_description, $service_amount;
    public $is_advance = true;
    public $advance_hours;
    public $lesson_type_id;
    public $discount_percent = 0;

    protected function rules()
    {
        $baseRules = [];
        if ($this->invoice_type === 'student') {
            $baseRules = [
                'student_id' => 'required',
                'date_from' => 'required|date',
                'date_to' => 'required|date|after_or_equal:date_from',
            ];
        } else {
            $baseRules = [
                'service_client_name' => 'required|string|min:3',
                'service_description' => 'required|string',
                'service_amount' => 'required|numeric|min:1',
            ];
        }
        // Discount percent is always present
        $baseRules['discount_percent'] = 'nullable|integer|min:0|max:100';
        return $baseRules;
    }

    // НОВА функција за чистење на филтрите
    public function resetFilters()
    {
        $this->reset(['search', 'filter_status', 'filter_type']);
    }

    public function closeModal()
    {
        $this->showCreateModal = false;
        $this->resetValidation();
    }

    public function createInvoice()
    {
        $this->validate($this->rules());


        if ($this->invoice_type === 'student') {
            if ($this->is_advance) {
                $this->validate([
                    'advance_hours' => 'required|numeric|min:1',
                    'lesson_type_id' => 'required',
                ]);

                $template = LessonTemplate::find($this->lesson_type_id);
                $pricePerUnit = $template ? $template->default_price : 0;

                $baseAmount = (int) ($this->advance_hours * $pricePerUnit);
                $serviceDesc = $template->name ?? '';
                $studentId = $this->student_id;
                $customClient = null;
                $quantity = $this->advance_hours;
                $unitPrice = $pricePerUnit;
            } else {
                $lessons = Lesson::where('student_id', $this->student_id)
                    ->whereBetween('lesson_date', [$this->date_from, $this->date_to])
                    ->where('lesson_status', 'held')
                    ->get();

                if ($lessons->isEmpty()) {
                    session()->flash('error', 'Нема пронајдено одржани часови за овој ученик во избраниот период.');
                    return;
                }

                $baseAmount = $lessons->sum('price_at_time');
                $studentId = $this->student_id;
                $customClient = null;
                $serviceDesc = null;
                $quantity = 1;
                $unitPrice = $baseAmount;
            }
        } else {
            $baseAmount = $this->service_amount;
            $studentId = null;
            $customClient = $this->service_client_name;
            $serviceDesc = $this->service_description;
            $this->date_from = now()->format('Y-m-d');
            $this->date_to = now()->format('Y-m-d');
            $quantity = 1;
            $unitPrice = $this->service_amount;
        }

        // Пресметка на попуст
        $discount = ($this->discount_percent > 0) ? round($baseAmount * ($this->discount_percent / 100)) : 0;
        $totalAmount = $baseAmount - $discount;

        $referenceDate = ($this->invoice_type === 'student') ? $this->date_to : now();
        $numData = $this->generateInvoiceNumber($referenceDate);


        Invoice::create([
            'invoice_number' => $numData['full_number'],
            'sequence_number' => $numData['sequence'],
            'student_id' => $studentId,
            'custom_client_name' => $customClient,
            'service_description' => $serviceDesc,
            'lesson_type_id' => $this->is_advance ? $this->lesson_type_id : null,
            'quantity' => $quantity ?? 1,
            'unit_price' => $unitPrice ?? $baseAmount,
            'date_from' => $this->date_from,
            'date_to' => $this->date_to,
            'total_amount' => $totalAmount,
            'discount_percent' => $this->discount_percent ?? 0,
            'status' => 'unpaid',
            'is_advance' => $this->is_advance ?? false,
        ]);

        $this->reset(['student_id', 'date_from', 'date_to', 'showCreateModal', 'service_client_name', 'service_description', 'service_amount', 'advance_hours', 'lesson_type_id', 'discount_percent']);
        session()->flash('message', 'Фактурата е успешно генерирана.');
    }

    public function cancelInvoice($id, $reason = null)
    {
        $invoice = Invoice::find($id);
        if (!$invoice) {
            session()->flash('error', 'Фактурата не е пронајдена.');
            return;
        }

        if ($invoice->status === 'cancelled') {
            session()->flash('error', 'Фактурата е веќе поништена.');
            return;
        }

        if ($invoice->status === 'paid') {
            session()->flash('error', 'Платена фактура не може да се поништи.');
            return;
        }

        $reason = trim((string) $reason);
        if (mb_strlen($reason) < 10) {
            session()->flash('error', 'Поништување е дозволено само со валидна причина (минимум 10 карактери).');
            return;
        }

        $invoice->update([
            'status' => 'cancelled',
            'cancelled_reason' => $reason,
            'cancelled_at' => now(),
        ]);

        session()->flash('message', 'Фактурата е поништена со внесена причина.');
    }

    public function togglePaid($id)
    {
        $invoice = Invoice::find($id);
        if (!$invoice) {
            session()->flash('error', 'Фактурата не е пронајдена.');
            return;
        }

        if ($invoice->status === 'cancelled') {
            session()->flash('error', 'Поништена фактура не може да се менува во платена/неплатена.');
            return;
        }

        $invoice->status = ($invoice->status === 'paid') ? 'unpaid' : 'paid';
        $invoice->save();
    }

    protected function generateInvoiceNumber($date)
    {
        $year = date('Y', strtotime($date));
        $monthYear = date('my', strtotime($date));

        $lastInvoice = Invoice::whereYear('created_at', $year)
            ->orderBy('sequence_number', 'desc')
            ->first();

        $nextNumber = $lastInvoice ? ($lastInvoice->sequence_number + 1) : 1;

        return [
            'full_number' => str_pad($nextNumber, 3, '0', STR_PAD_LEFT) . '/' . $monthYear,
            'sequence' => $nextNumber
        ];
    }

    public function deleteInvoice($id)
    {
        $invoice = Invoice::find($id);

        if (!$invoice) {
            session()->flash('error', 'Фактурата не е пронајдена.');
            return;
        }

        if ($invoice->status !== 'cancelled') {
            session()->flash('error', 'Бришење е дозволено само за поништени фактури.');
            return;
        }

        $invoice->delete();
        session()->flash('message', 'Фактурата е избришана.');
    }

    public function restoreInvoice($id)
    {
        $invoice = Invoice::find($id);

        if (!$invoice) {
            session()->flash('error', 'Фактурата не е пронајдена.');
            return;
        }

        if ($invoice->status !== 'cancelled') {
            session()->flash('error', 'Враќање е дозволено само за поништени фактури.');
            return;
        }

        $invoice->update([
            'status' => 'unpaid',
            'cancelled_reason' => null,
            'cancelled_at' => null,
        ]);

        session()->flash('message', 'Фактурата е вратена во статус неплатена.');
    }

    public function downloadInvoice($invoiceId)
    {
        $invoice = Invoice::with('student')->findOrFail($invoiceId);
        $data = $this->buildInvoicePdfPayload($invoice);
        if (!$data) {
            return;
        }

        $pdf = Pdf::loadView('pdf.invoice_final', $data);
        $fileName = 'Faktura_' . str_replace(['/', '\\'], '-', $invoice->invoice_number) . '.pdf';

        return response()->streamDownload(function () use ($pdf) {
            echo $pdf->output();
        }, $fileName);
    }

    public function sendInvoiceEmail($invoiceId, $forceResend = false)
    {
        $invoice = Invoice::with('student')->find($invoiceId);

        if (!$invoice) {
            session()->flash('error', 'Фактурата не е пронајдена.');
            return;
        }

        if ($invoice->status === 'cancelled') {
            session()->flash('error', 'Поништена фактура не може да се прати по е-пошта.');
            return;
        }

        if ($invoice->email_sent_at && !$forceResend) {
            session()->flash('error', 'Оваа фактура веќе е испратена. За повторно праќање потребна е потврда.');
            return;
        }

        $recipientEmail = optional($invoice->student)->email;
        if (!$recipientEmail || !filter_var($recipientEmail, FILTER_VALIDATE_EMAIL)) {
            session()->flash('error', 'За оваа фактура нема валидна е-пошта кај клиентот.');
            return;
        }

        $data = $this->buildInvoicePdfPayload($invoice);
        if (!$data) {
            return;
        }

        $pdfContent = Pdf::loadView('pdf.invoice_final', $data)->output();
        $fileName = 'Faktura_' . str_replace(['/', '\\'], '-', $invoice->invoice_number) . '.pdf';
        $recipientName = $invoice->student
            ? trim($invoice->student->first_name . ' ' . $invoice->student->last_name)
            : ($invoice->custom_client_name ?? 'клиент');
        $alreadySent = !is_null($invoice->email_sent_at);

        try {
            Mail::to($recipientEmail)->send(new InvoiceCreatedMail(
                $invoice,
                $pdfContent,
                $fileName,
                $recipientName
            ));

            $invoice->update([
                'email_sent_at' => now(),
                'email_sent_to' => $recipientEmail,
                'email_sent_count' => ((int) $invoice->email_sent_count) + 1,
                'email_last_error' => null,
            ]);

            session()->flash('message', $alreadySent
                ? 'Фактурата е повторно испратена на е-пошта: ' . $recipientEmail
                : 'Фактурата е успешно испратена на е-пошта: ' . $recipientEmail);
        } catch (\Throwable $e) {
            $invoice->update([
                'email_last_error' => mb_substr((string) $e->getMessage(), 0, 1000),
            ]);

            report($e);
            session()->flash('error', 'Настана грешка при праќање на фактурата. Обидете се повторно.');
        }
    }

    private function buildInvoicePdfPayload(Invoice $invoice): ?array
    {
        $settings = SchoolSetting::first();

        if (!$settings) {
            session()->flash('error', 'Прво внесете ги податоците за училиштето во Подесувања!');
            return null;
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

        return [
            'invoice' => $invoice,
            'settings' => $settings,
            'lessons' => $lessons,
            'amountInWords' => $this->convertAmountToWords($invoice->total_amount),
            'sigData' => $sigData,
        ];
    }

    private function convertAmountToWords($amount)
    {
        return AmountInWords::mkdDenars($amount);
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

    public function render()
    {
        // 1. Почнуваме со основната кверија
        $query = Invoice::with('student');

        // 2. Прво примени ги сите филтри
        if ($this->search) {
            $query->where(function ($q) {
                $q->whereHas('student', function ($sq) {
                    $sq->where('first_name', 'like', '%' . $this->search . '%')
                        ->orWhere('last_name', 'like', '%' . $this->search . '%');
                })
                    ->orWhere('custom_client_name', 'like', '%' . $this->search . '%')
                    ->orWhere('invoice_number', 'like', '%' . $this->search . '%');
            });
        }

        if ($this->filter_status) {
            if ($this->filter_status === 'sent') {
                $query->whereNotNull('email_sent_at');
            } elseif ($this->filter_status === 'unsent') {
                $query->whereNull('email_sent_at');
            } else {
                $query->where('status', $this->filter_status);
            }
        }

        if ($this->filter_type) {
            if ($this->filter_type === 'student') {
                $query->whereNotNull('student_id');
            } else {
                $query->whereNull('student_id');
            }
        }

        // 3. СЕГА пресметај ги сумите врз основа на ФИЛТРИРАНАТА кверија
        $this->totalFilteredAmount = (clone $query)->sum('total_amount') ?: 0;

        $totalUnpaidAmount = (clone $query)
            ->where('status', 'unpaid')
            ->sum('total_amount') ?: 0;

        // 4. Земи ги филтрираните резултати со пагинација
        $invoices = $query->orderBy('created_at', 'desc')->paginate(10);
        $this->attachAdvanceProgress($invoices);

        // Логика за студенти во модалот
        $filteredStudents = Student::where('active', true)
            ->where(function ($q) {
                $q->where('first_name', 'like', '%' . $this->student_search . '%')
                    ->orWhere('last_name', 'like', '%' . $this->student_search . '%');
            })
            ->orderBy('first_name')
            ->get();

        return view('livewire.invoice-management', [
            'invoices' => $invoices,
            'students' => $filteredStudents,
            'lessonTemplates' => LessonTemplate::all(),
            'totalFilteredAmount' => $this->totalFilteredAmount, // Прати ја точната променлива
            'totalUnpaidAmount' => $totalUnpaidAmount
        ])->layout('layouts.app');
    }
}
