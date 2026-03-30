<?php

namespace App\Http\Controllers\Peserta;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class AttendanceController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $today = Carbon::today();

        $attendanceToday = Attendance::where('user_id', $user->id)
            ->whereDate('date', $today)
            ->first();

        $history = Attendance::where('user_id', $user->id)
            ->orderByDesc('date')
            ->paginate(15);

        return view('peserta.attendance', compact('attendanceToday', 'history', 'today'));
    }

    public function checkIn(Request $request): RedirectResponse
    {
        $user = $request->user();
        $today = Carbon::today();

        $attendance = Attendance::firstOrCreate(
            ['user_id' => $user->id, 'date' => $today],
            ['device_hash' => $user->device_hash]
        );

        if ($attendance->check_in_at) {
            return back()->with('info', 'Absen masuk hari ini sudah pernah direkam. Santai, sistemnya tidak lupa.');
        }

        $attendance->check_in_at = now();
        $attendance->check_in_ip = $request->ip();
        $attendance->device_hash = $user->device_hash;
        $attendance->save();

        return back()->with('success', 'Absen masuk berhasil direkam.');
    }

    public function checkOut(Request $request): RedirectResponse
    {
        $user = $request->user();
        $today = Carbon::today();

        $attendance = Attendance::where('user_id', $user->id)
            ->whereDate('date', $today)
            ->first();

        if (!$attendance || !$attendance->check_in_at) {
            return back()->with('error', 'Kamu belum absen masuk hari ini, jadi belum bisa absen pulang.');
        }

        if ($attendance->check_out_at) {
            return back()->with('info', 'Absen pulang hari ini sudah ada. Mesin birokrasi sudah puas.');
        }

        $attendance->check_out_at = now();
        $attendance->check_out_ip = $request->ip();
        $attendance->device_hash = $user->device_hash;
        $attendance->save();

        return back()->with('success', 'Absen pulang berhasil direkam.');
    }

    public function masuk(Request $request): RedirectResponse
    {
        return $this->checkIn($request);
    }

    public function pulang(Request $request): RedirectResponse
    {
        return $this->checkOut($request);
    }
}
