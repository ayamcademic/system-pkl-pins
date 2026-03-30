<?php

namespace App\Http\Controllers\Peserta;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\DailyReport;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $today = Carbon::today();

        $attendanceToday = Attendance::where('user_id', $user->id)
            ->whereDate('date', $today)
            ->first();

        $reportToday = DailyReport::where('user_id', $user->id)
            ->whereDate('date', $today)
            ->first();

        $recentAttendances = Attendance::where('user_id', $user->id)
            ->orderByDesc('date')
            ->limit(7)
            ->get();

        $recentReports = DailyReport::where('user_id', $user->id)
            ->orderByDesc('date')
            ->limit(7)
            ->get();

        $hour = now()->hour;

        if ($hour < 11) {
            $greeting = 'Selamat pagi';
        } elseif ($hour < 15) {
            $greeting = 'Selamat siang';
        } elseif ($hour < 18) {
            $greeting = 'Selamat sore';
        } else {
            $greeting = 'Selamat malam';
        }

        $userName = $user->name;

        return view('peserta.dashboard', compact(
            'attendanceToday',
            'reportToday',
            'recentAttendances',
            'recentReports',
            'today',
            'greeting',
            'userName'
        ));
    }
}