<?php

namespace App\Http\Controllers\Peserta;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\DailyReport;
use Carbon\Carbon;
use Illuminate\Http\Request;

class WeeklyReportController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        // week=YYYY-WW (ISO week), contoh: 2026-W07
        // $weekParam = $request->query('week');
        // $start = $weekParam
        //     ? Carbon::createFromFormat('o-\WW', $weekParam)->startOfWeek(Carbon::MONDAY)
        //     : now()->startOfWeek(Carbon::MONDAY);

        // $end = (clone $start)->endOfWeek(Carbon::SUNDAY);
         $weekParam = trim($request->query('week'));

            if ($weekParam && preg_match('/^\d{4}-W\d{2}$/', $weekParam)) {

                [$year, $week] = explode('-W', $weekParam);

        $start = Carbon::now()
                    ->setISODate((int)$year, (int)$week)
                    ->startOfWeek(Carbon::MONDAY);

            } else {

                $start = now()->startOfWeek(Carbon::MONDAY);
            }

        $end = (clone $start)->endOfWeek(Carbon::SUNDAY);

        $reports = DailyReport::where('user_id', $user->id)
            ->whereBetween('date', [$start->toDateString(), $end->toDateString()])
            ->orderBy('date')
            ->get()
            ->keyBy(fn ($r) => $r->date->toDateString());

        $attendances = Attendance::where('user_id', $user->id)
            ->whereBetween('date', [$start->toDateString(), $end->toDateString()])
            ->orderBy('date')
            ->get()
            ->keyBy(fn ($a) => $a->date->toDateString());

        // Build 7 hari
        $days = collect();
        for ($i = 0; $i < 5; $i++) { //kalo mau sampai jumat tinggal ubah 5 ke 7 doang anjay
            $d = (clone $start)->addDays($i);
            $key = $d->toDateString();
            $days->push([
                'date' => $d,
                'attendance' => $attendances->get($key),
                'report' => $reports->get($key),
            ]);
        }

        $prevWeek = (clone $start)->subWeek()->format('o-\WW');
        $nextWeek = (clone $start)->addWeek()->format('o-\WW');
        $currentWeek = $start->format('o-\WW');

        return view('peserta/weekly-report', compact(
            'days',
            'start',
            'end',
            'prevWeek',
            'nextWeek',
            'currentWeek'
        ));
    }
}
