<?php

namespace App\Exports;

use App\Models\Lesson;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class LessonsExport implements FromQuery, WithMapping, WithHeadings, ShouldAutoSize, WithStyles, WithColumnWidths, WithEvents
{
    protected $search, $type, $from, $to;
    protected $totalSum = 0;

    public function __construct($search, $type, $from, $to)
    {
        $this->search = $search;
        $this->type = $type;
        $this->from = $from;
        $this->to = $to;
    }

    /**
     * Build the query for the export based on active filters
     */
    public function query()
    {
        $query = Lesson::with(['student', 'lessonType']);

        if ($this->search) {
            $query->whereHas('student', function ($q) {
                $q->where('first_name', 'like', '%' . $this->search . '%')
                    ->orWhere('last_name', 'like', '%' . $this->search . '%');
            });
        }

        if ($this->type) {
            $query->where('lesson_type_id', $this->type);
        }

        if ($this->from) {
            $query->where('lesson_date', '>=', $this->from);
        }

        if ($this->to) {
            $query->where('lesson_date', '<=', $this->to);
        }

        // We calculate the sum here to use it in the footer later
        $this->totalSum = $query->sum('price_at_time');

        return $query->orderBy('lesson_date', 'desc');
    }

    /**
     * Define the headers for the Excel file
     */
    public function headings(): array
    {
        return [
            'Ученик',
            'Тип на час',
            'Датум',
            'Време (Почеток - Крај)',
            'Цена (ден.)',
            'Забелешка'
        ];
    }

    /**
     * Map each row of data
     */
    public function map($lesson): array
    {
        return [
            $lesson->student->first_name . ' ' . $lesson->student->last_name,
            str_replace('min', 'мин', $lesson->lessonType->name),
            \Carbon\Carbon::parse($lesson->lesson_date)->format('d.m.Y'),
            \Carbon\Carbon::parse($lesson->start_time)->format('H:i') . ' - ' . \Carbon\Carbon::parse($lesson->end_time)->format('H:i'),
            $lesson->price_at_time,
            $lesson->notes ?: '/',
        ];
    }

    /**
     * Set specific column widths (especially for notes)
     */
    public function columnWidths(): array
    {
        return [
            'F' => 45, // Larger width for the "Notes" (Забелешка) column
        ];
    }

    /**
     * Apply styles to the worksheet
     */
    public function styles(Worksheet $sheet)
    {
        return [
            // Header styling
            1 => [
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '1d6e80']
                ],
            ],
        ];
    }

    /**
     * Use events to append the footer row with the total sum
     */
    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $lastRow = $event->sheet->getHighestRow() + 1;

                // Add the footer labels and the sum
                $event->sheet->setCellValue("D{$lastRow}", 'ВКУПНО:');
                $event->sheet->setCellValue("E{$lastRow}", $this->totalSum);

                // Style the footer row
                $event->sheet->getStyle("D{$lastRow}:E{$lastRow}")->applyFromArray([
                    'font' => ['bold' => true],
                    'borders' => [
                        'top' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN],
                    ],
                ]);

                // Align sum to the right
                $event->sheet->getStyle("E{$lastRow}")->getAlignment()->setHorizontal('right');
            },
        ];
    }
}
