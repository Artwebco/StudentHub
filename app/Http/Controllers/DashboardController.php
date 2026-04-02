<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Invoice;
use App\Models\Student;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        // 1. Години за филтер
        $availableYears = Invoice::selectRaw('YEAR(date_from) as year')
            ->distinct()
            ->orderBy('year', 'desc')
            ->pluck('year')
            ->filter(fn($y) => !empty($y));

        if ($availableYears->isEmpty())
            $availableYears = collect([date('Y')]);

        $selectedYear = $request->get('year', 'all');
        $selectedQuarter = $request->get('quarter');

        // 2. Филтрирање податоци
        $query = Invoice::query();
        if ($selectedYear !== 'all') {
            $query->whereYear('date_from', $selectedYear);
        }
        if ($selectedQuarter) {
            $months = match ($selectedQuarter) {
                '1' => [1, 3], '2' => [4, 6], '3' => [7, 9], '4' => [10, 12],
                default => null
            };
            if ($months) {
                $query->whereMonth('date_from', '>=', $months[0])
                    ->whereMonth('date_from', '<=', $months[1]);
            }
        }

        $currentInvoices = $query->with('lessonTemplate')->get();
        $totalEarnings = $currentInvoices->sum('total_amount');
        $activeStudents = Student::count();

        // 3. Пресметка на раст
        $growthTotal = 0;
        if ($selectedYear !== 'all') {
            $prevYearTotal = Invoice::whereYear('date_from', $selectedYear - 1)->sum('total_amount');
            $growthTotal = $prevYearTotal > 0 ? (($totalEarnings - $prevYearTotal) / $prevYearTotal) * 100 : 0;
        }

        // 4. Податоци за Пита (Услуги) - use EN admin name from lesson template
        $serviceStats = $currentInvoices->groupBy(
            fn($invoice) =>
            $invoice->lessonTemplate?->admin_name ?? $invoice->service_description
        )->map(fn($group) => [
                'sum' => $group->sum('total_amount')
            ])->sortByDesc('sum');

        $pieLabels = $serviceStats->keys()->toArray();
        $pieData = $serviceStats->pluck('sum')->toArray();

        // 5. Податоци за Линиски графикон
        $allYearsData = Invoice::selectRaw("YEAR(date_from) as year, MONTH(date_from) as month, SUM(total_amount) as total")
            ->groupBy('year', 'month')
            ->get()
            ->groupBy('year');

        $palette = ['#2563eb', '#f59e0b', '#10b981', '#ef4444', '#8b5cf6', '#06b6d4'];
        $chartDatasets = [];
        $colorIdx = 0;

        foreach ($allYearsData as $year => $monthsData) {
            $monthlyTotals = array_fill(0, 12, 0);
            foreach ($monthsData as $data) {
                $idx = (int) $data->month - 1;
                $monthlyTotals[$idx] = (float) $data->total;
            }
            $color = $palette[$colorIdx % count($palette)];
            $isHighlighted = ($selectedYear === 'all' || $year == $selectedYear);

            $chartDatasets[] = [
                'label' => $year,
                'data' => $monthlyTotals,
                'borderColor' => $color,
                'backgroundColor' => $isHighlighted ? $color . '1A' : 'transparent',
                'fill' => $isHighlighted,
                'tension' => 0.4
            ];
            $colorIdx++;
        }

        return view('dashboard', compact(
            'availableYears',
            'totalEarnings',
            'growthTotal',
            'selectedYear',
            'selectedQuarter',
            'serviceStats',
            'chartDatasets',
            'pieLabels',
            'pieData',
            'activeStudents'
        ));
    }
}
