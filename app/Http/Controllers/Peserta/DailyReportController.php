<?php

namespace App\Http\Controllers\Peserta;

use App\Http\Controllers\Controller;
use App\Models\DailyReport;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class DailyReportController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $today = Carbon::today();

        $todayReport = DailyReport::where('user_id', $user->id)
            ->whereDate('date', $today)
            ->first();

        $history = DailyReport::where('user_id', $user->id)
            ->orderByDesc('date')
            ->paginate(15);

        return view('peserta/daily-report', compact('today', 'todayReport', 'history'));
    }

    public function storeOrUpdate(Request $request): RedirectResponse
    {
        $user = $request->user();
        $today = Carbon::today();

        $data = $request->validate([
            'content' => ['required', 'string', 'min:5', 'max:5000'],
        ]);

        DailyReport::updateOrCreate(
            ['user_id' => $user->id, 'date' => $today],
            ['content' => $data['content']]
        );
        

        return back()->with('success', 'Laporan harian tersimpan ✅');
    }

    public function store(Request $request): RedirectResponse
{
    return $this->storeOrUpdate($request);
}

}
