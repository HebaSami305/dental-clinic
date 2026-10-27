<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Invoice;
use App\Models\Patient;
use App\Models\Payment;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function __invoke()
    {
        $today = today();
        $monthStart = now()->startOfMonth();

        $stats = [
            'appointments_today' => Appointment::whereDate('starts_at', $today)
                ->where('status', '!=', 'cancelled')->count(),
            'patients_total' => Patient::count(),
            'patients_month' => Patient::where('created_at', '>=', $monthStart)->count(),
            'revenue_today' => (float) Payment::whereDate('paid_at', $today)->sum('amount'),
            'revenue_month' => (float) Payment::whereDate('paid_at', '>=', $monthStart)->sum('amount'),
            'outstanding' => (float) Invoice::sum('total') - (float) Payment::sum('amount'),
        ];

        $appointments = Appointment::with('patient')
            ->onDate($today)
            ->orderBy('starts_at')
            ->get();

        // إيراد آخر 7 أيام
        $days = collect(range(6, 0))->map(fn ($i) => today()->subDays($i));

        $sums = DB::table('payments')
            ->whereBetween('paid_at', [$days->first()->toDateString(), $today->toDateString()])
            ->selectRaw('paid_at, sum(amount) as total')
            ->groupBy('paid_at')
            ->pluck('total', 'paid_at');

        $chart = $days->map(fn ($d) => [
            'label' => $d->translatedFormat('D'),
            'total' => (float) ($sums[$d->toDateString()] ?? 0),
        ]);

        return view('dashboard', [
            'stats' => $stats,
            'appointments' => $appointments,
            'chart' => $chart,
            'chartMax' => max($chart->max('total'), 1),
        ]);
    }
}
