<?php

use Illuminate\Support\Facades\Route;
use App\Livewire\Students;
use App\Livewire\StudentPrices;
use App\Livewire\StudentPriceManager;
use App\Livewire\InvoiceManagement;
use App\Livewire\SchoolSettingsManager;
use App\Livewire\LessonStats;
use App\Http\Controllers\DashboardController;
use App\Models\Invoice;
use App\Models\Lesson;
use App\Models\SchoolSetting;
use App\Support\AmountInWords;
use Barryvdh\DomPDF\Facade\Pdf;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// 1. ПОЧЕТНА СТРАНА
Route::get('/', function () {
    if (auth()->check()) {
        return redirect()->intended('/home');
    }
    return redirect()->route('login');
});

// 2. „ПАМЕТНА“ РУТА
Route::get('/home', function () {
    if (auth()->user()->role === 'admin') {
        return redirect()->route('dashboard');
    }
    return redirect()->route('student.my-statistic');
})->middleware(['auth'])->name('home');

// 3. ЗАШТИТЕНИ РУТИ
Route::middleware(['auth', 'verified'])->group(function () {

    Route::view('profile', 'profile')->name('profile.edit');
    Route::view('profile-view', 'profile')->name('profile');

    // АДМИН РУТИ
    Route::middleware(['admin'])->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

        Route::get('/students', Students::class)->name('students');

        // НОВИОТ ЦЕНОВНИК (Картичките од сликата - сега како Livewire)
        // Оваа една рута е доволна за Livewire компонентата
        Route::get('/student-prices', StudentPrices::class)->name('student-prices');

        // СТАРИОТ МЕНАЏЕР (Индивидуални цени по ученик)
        Route::get('/student-prices-assignment', StudentPriceManager::class)->name('student-prices.assign');

        Route::get('/invoices', InvoiceManagement::class)->name('invoices');
        Route::get('/settings', SchoolSettingsManager::class)->name('settings');
        Route::view('/lessons-log', 'lessons-log')->name('lessons-log');
        Route::get('/company-info', SchoolSettingsManager::class)->name('company-info');
    });

    // УЧЕНИК РУТИ
    Route::get('/my-statistic', LessonStats::class)->name('student.my-statistic');

    Route::get('/student/invoices/{invoice}/preview', function (Invoice $invoice) {
        $user = auth()->user();

        // Students can preview only their own invoices.
        if ($user->role !== 'admin' && (int) $invoice->student_id !== (int) $user->id) {
            abort(403);
        }

        $settings = SchoolSetting::first();
        if (!$settings) {
            abort(404, 'School settings not found.');
        }

        $lessons = collect();
        if ($invoice->student_id) {
            $lessons = Lesson::where('student_id', $invoice->student_id)
                ->whereBetween('lesson_date', [$invoice->date_from, $invoice->date_to])
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

        $pdf = Pdf::loadView('pdf.invoice_final', [
            'invoice' => $invoice,
            'settings' => $settings,
            'lessons' => $lessons,
            'amountInWords' => AmountInWords::mkdDenars($invoice->total_amount),
            'sigData' => $sigData,
        ]);

        return response($pdf->output(), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="Faktura_' . str_replace(['/', '\\'], '-', $invoice->invoice_number) . '.pdf"',
        ]);
    })->name('student.invoice-preview');

});

require __DIR__ . '/auth.php';
