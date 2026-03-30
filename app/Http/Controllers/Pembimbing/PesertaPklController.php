<?php

namespace App\Http\Controllers\Pembimbing;

use App\Http\Controllers\Controller;
use App\Models\PesertaPkl;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Storage;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class PesertaPklController extends Controller
{
    public function index(Request $request)
{
    $q = PesertaPkl::query();

    if ($request->filled('search')) {
        $s = $request->search;
        $q->where(function ($qq) use ($s) {
            $qq->where('nama', 'like', "%$s%")
               ->orWhere('asal_sekolah', 'like', "%$s%")
               ->orWhere('kompetensi_keahlian', 'like', "%$s%");
        });
    }

    $peserta = $q
        ->with('user') // ✅ biar bisa {{ $p->user?->email }}
        ->orderBy('tgl_masuk_pkl', 'asc')
        ->orderBy('nama')
        ->paginate(10)
        ->withQueryString();

        
    // ✅ list khusus: aktif + belum punya akun
    $pesertaTanpaAkunAktif = $peserta->getCollection()
    ->filter(fn($p) => is_null($p->user_id) && $p->status_pkl === 'ACTIVE')
    ->keyBy('id');

    return view('pembimbing.peserta.index', [
        'peserta' => $peserta,
        'search' => $request->search,
        'pesertaTanpaAkunAktif' => $pesertaTanpaAkunAktif,
    ]);
}



    public function create()
    {
        return view('pembimbing.peserta.form', [
            'mode' => 'create',
            'peserta' => new PesertaPkl(),
        ]);
    }

public function store(Request $request)
{
    $data = $this->validated($request);

    if ($request->hasFile('foto')) {
        $data['foto_path'] = $request->file('foto')->store('peserta', 'public');
    }

    PesertaPkl::create($data);

    return redirect()->route('pembimbing.peserta.index')
        ->with('success', 'Peserta berhasil ditambahkan.');
}


    public function show(PesertaPkl $peserta)
    {
        return view('pembimbing.peserta.show', compact('peserta'));
    }

    public function edit(PesertaPkl $peserta)
    {
        return view('pembimbing.peserta.form', [
            'mode' => 'edit',
            'peserta' => $peserta,
        ]);
    }

 public function update(Request $request, PesertaPkl $peserta)
{
    $data = $this->validated($request);

    // ✅ kalau klik tombol Hapus Foto
    if ($request->boolean('remove_foto')) {
        if ($peserta->foto_path) {
            Storage::disk('public')->delete($peserta->foto_path);
        }

        $peserta->update(['foto_path' => null]);

        return redirect()->route('pembimbing.peserta.edit', $peserta)
            ->with('success', 'Foto berhasil dihapus.');
    }

    // ✅ kalau upload/ganti foto
    if ($request->hasFile('foto')) {
        if ($peserta->foto_path) {
            Storage::disk('public')->delete($peserta->foto_path);
        }

        $data['foto_path'] = $request->file('foto')->store('peserta', 'public');
    }

    $peserta->update($data);

    return redirect()->route('pembimbing.peserta.index')
        ->with('success', 'Peserta berhasil diperbarui.');
}


    public function destroy(PesertaPkl $peserta)
{
    if ($peserta->foto_path) {
        Storage::disk('public')->delete($peserta->foto_path);
    }

    $peserta->delete();

    return redirect()->route('pembimbing.peserta.index')
        ->with('success', 'Peserta berhasil dihapus.');
}


    private function validated(Request $request): array
    {
        return $request->validate([
            'nama' => ['required', 'string', 'max:255'],
            'asal_sekolah' => ['nullable', 'string', 'max:255'],
            'kompetensi_keahlian' => ['nullable', 'string', 'max:255'],
            'tgl_masuk_pkl' => ['nullable', 'date'],
            // 'durasi_pkl' => ['nullable', 'string', 'max:255'],
            'no_hp' => ['nullable', 'string', 'max:30'],
            'alamat_rumah' => ['nullable', 'string'],
            'nama_guru_pembimbing' => ['nullable', 'string', 'max:255'],
            'no_hp_guru_pembimbing' => ['nullable', 'string', 'max:30'],
            'foto' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'tgl_keluar_pkl' => ['nullable', 'date', 'after_or_equal:tgl_masuk_pkl'],
            'tgl_masuk_pkl_2' => ['nullable', 'date'],
            'tgl_keluar_pkl_2' => ['nullable', 'date', 'after_or_equal:tgl_masuk_pkl_2'],

        ]);
    }


    public function exportPdf()
    {
        $peserta = PesertaPkl::orderBy('asal_sekolah')->orderBy('nama')->get();

        // palette pastel biar ga norak
        $palette = [
            '#FFF7ED', // orange-50
            '#F0FDFA', // teal-50
            '#EFF6FF', // blue-50
            '#F5F3FF', // violet-50
            '#ECFDF5', // emerald-50
            '#FEF2F2', // red-50
            '#FDF4FF', // fuchsia-50
            '#F8FAFC', // slate-50
        ];

        $schoolColors = [];
        foreach ($peserta->pluck('asal_sekolah')->map(fn($s) => $s ?: '(Kosong)')->unique() as $school) {
            // deterministic: sekolah yang sama -> warna yang sama
            $idx = abs(crc32($school)) % count($palette);
            $schoolColors[$school] = $palette[$idx];
        }

        $todayLabel = Carbon::now()->locale('id')->translatedFormat('d F Y');
        $filename = 'data-peserta-pkl-' . Carbon::now()->format('Y-m-d') . '.pdf';

        $pdf = Pdf::loadView('pembimbing.peserta.pdf', compact('peserta', 'schoolColors', 'todayLabel'))
            ->setPaper('a4', 'portrait');

        return $pdf->download($filename);
    }
}