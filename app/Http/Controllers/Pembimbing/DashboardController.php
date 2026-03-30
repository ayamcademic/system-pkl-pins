<?php

namespace App\Http\Controllers\Pembimbing;

use App\Http\Controllers\Controller;
use App\Models\PesertaPkl;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\DailyReport;
use App\Models\Attendance;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $q = PesertaPkl::query();

        if ($request->filled('tahun')) {
            $q->whereYear('tgl_masuk_pkl', (int) $request->tahun);
        }
        if ($request->filled('asal_sekolah')) {
            $q->where('asal_sekolah', $request->asal_sekolah);
        }
        if ($request->filled('kompetensi')) {
            $q->where('kompetensi_keahlian', $request->kompetensi);
        }

        $totalPeserta     = (clone $q)->count();
        $jumlahSekolah    = (clone $q)->distinct('asal_sekolah')->count('asal_sekolah');
        $jumlahKompetensi = (clone $q)->distinct('kompetensi_keahlian')->count('kompetensi_keahlian');

        $kompetensiStats = (clone $q)
            ->select('kompetensi_keahlian', DB::raw('COUNT(*) as total'))
            ->groupBy('kompetensi_keahlian')
            ->orderByDesc('total')
            ->get();

        $sekolahTop = (clone $q)
            ->select('asal_sekolah', DB::raw('COUNT(*) as total'))
            ->groupBy('asal_sekolah')
            ->orderByDesc('total')
            ->limit(10)
            ->get();

        $masukPerTahun = (clone $q)
            ->selectRaw("YEAR(tgl_masuk_pkl) as y, COUNT(*) as total")
            ->whereNotNull('tgl_masuk_pkl')
            ->groupBy('y')
            ->orderBy('y')
            ->get();

        $filterSekolah = PesertaPkl::query()
            ->whereNotNull('asal_sekolah')
            ->distinct()
            ->orderBy('asal_sekolah')
            ->pluck('asal_sekolah');

        $filterKompetensi = PesertaPkl::query()
            ->whereNotNull('kompetensi_keahlian')
            ->distinct()
            ->orderBy('kompetensi_keahlian')
            ->pluck('kompetensi_keahlian');

        $today = Carbon::today()->toDateString();

        $activeCount = (clone $q)
            ->whereRaw("
                (
                    CASE
                        WHEN tgl_masuk_pkl_2 IS NOT NULL THEN tgl_keluar_pkl_2
                        ELSE tgl_keluar_pkl
                    END
                ) IS NULL
                OR
                (
                    CASE
                        WHEN tgl_masuk_pkl_2 IS NOT NULL THEN tgl_keluar_pkl_2
                        ELSE tgl_keluar_pkl
                    END
                ) >= ?
            ", [$today])
            ->count();

        $archiveCount = (clone $q)
            ->whereRaw("
                (
                    CASE
                        WHEN tgl_masuk_pkl_2 IS NOT NULL THEN tgl_keluar_pkl_2
                        ELSE tgl_keluar_pkl
                    END
                ) < ?
            ", [$today])
            ->count();

        $date = $request->filled('date')
            ? Carbon::parse($request->date)->toDateString()
            : now()->toDateString();

        $pesertaAktif = PesertaPkl::query()
            ->with('user')
            ->whereNotNull('user_id')
            ->get()
            ->filter(fn ($p) => $p->status_pkl === 'ACTIVE')
            ->values();

        $userIds = $pesertaAktif->pluck('user_id')->values();

        $absensiHariIni = Attendance::query()
            ->whereIn('user_id', $userIds)
            ->whereDate('date', $date)
            ->get()
            ->keyBy('user_id');

        $laporanHariIni = DailyReport::query()
            ->whereIn('user_id', $userIds)
            ->whereDate('date', $date)
            ->get()
            ->keyBy('user_id');

                $rows = $pesertaAktif->map(function ($p) use ($absensiHariIni, $laporanHariIni) {
            $a = $absensiHariIni->get($p->user_id);
            $r = $laporanHariIni->get($p->user_id);

            return [
                'peserta'    => $p,
                'email'      => $p->user?->email,
                'jam_masuk'  => $a?->check_in_at ? Carbon::parse($a->check_in_at)->format('H:i') : null,
                'jam_pulang' => $a?->check_out_at ? Carbon::parse($a->check_out_at)->format('H:i') : null,
                'laporan'    => $r?->content,
            ];
        });

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

        $userName = auth()->user()?->name ?? 'Pembimbing';
        $greetingMessage = 'Pantau aktivitas peserta PKL dan cek siapa yang sudah absen maupun mengisi task report hari ini.';

        return view('pembimbing.dashboard', [
            'totalPeserta' => $totalPeserta,
            'jumlahSekolah' => $jumlahSekolah,
            'jumlahKompetensi' => $jumlahKompetensi,
            'kompetensiStats' => $kompetensiStats,
            'sekolahTop' => $sekolahTop,
            'masukPerTahun' => $masukPerTahun,
            'filterSekolah' => $filterSekolah,
            'filterKompetensi' => $filterKompetensi,
            'tahun' => $request->tahun,
            'asalSekolah' => $request->asal_sekolah,
            'kompetensi' => $request->kompetensi,
            'activeCount' => $activeCount,
            'archiveCount' => $archiveCount,
            'date' => $date,
            'rows' => $rows,
            'greeting' => $greeting,
            'userName' => $userName,
            'greetingMessage' => $greetingMessage,
        ]);
    }
}