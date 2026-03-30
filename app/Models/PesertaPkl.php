<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;
use App\Models\User;
use Illuminate\Support\Facades\DB;


class PesertaPkl extends Model
{
    protected $table = 'peserta_pkl';

    protected $fillable = [
        'user_id',
        
        'tgl_masuk_pkl',

          'tgl_keluar_pkl',
  'tgl_masuk_pkl_2',
  'tgl_keluar_pkl_2',

        'asal_sekolah',
        'nama',
        'kompetensi_keahlian',
        'durasi_pkl',
        'no_hp',
        'alamat_rumah',
        'nama_guru_pembimbing',
        'no_hp_guru_pembimbing',
        'foto_path',

        'account_password_enc',
    ];

    protected $casts = [
        'tgl_masuk_pkl' => 'date',
            'tgl_keluar_pkl' => 'date',
    'tgl_masuk_pkl_2' => 'date',
    'tgl_keluar_pkl_2' => 'date',
    ];

public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

   public function scopeAktif($query)
{
    $expr = "COALESCE(tgl_keluar_pkl_2, tgl_keluar_pkl)";

    return $query->whereRaw("{$expr} IS NULL OR DATE({$expr}) >= ?", [now()->toDateString()]);
}

       public function scopeBisaDibuatAkun($query)
{
    return $query->whereNull('user_id')->aktif();
}

    public function getDurasiPklAttribute(): ?string
    {
        // kalau kamu mau FULL otomatis, abaikan $value (yang dari DB)
        $p1 = $this->formatPeriode($this->tgl_masuk_pkl, $this->tgl_keluar_pkl);
        $p2 = $this->formatPeriode($this->tgl_masuk_pkl_2, $this->tgl_keluar_pkl_2);

        if ($p1 && $p2) return $p1.' & '.$p2;
        return $p1 ?: $p2; // kalau cuma ada periode 2, tetap tampil
    }

    private function formatPeriode($masuk, $keluar): ?string
    {
        if (!$masuk) return null;

        // pastikan Carbon
        $masuk = $masuk instanceof Carbon ? $masuk : Carbon::parse($masuk);
        $keluar = $keluar ? ($keluar instanceof Carbon ? $keluar : Carbon::parse($keluar)) : null;

        // pakai nama bulan Indonesia
        $mMasuk = $masuk->locale('id')->translatedFormat('F');
        $yMasuk = $masuk->year;

        // kalau belum ada keluar: "Januari 2026 - sekarang"
       $today = Carbon::today();

// kalau belum ada keluar ATAU keluarnya hari ini/masa depan => masih jalan
if (!$keluar || $keluar->greaterThanOrEqualTo($today)) {
    return "{$mMasuk} {$yMasuk} - sekarang";
}

        $mKeluar = $keluar->locale('id')->translatedFormat('F');
        $yKeluar = $keluar->year;

        // kalau tahun sama: "Januari - Maret 2026"
        if ($yMasuk === $yKeluar) {
            // kalau bulan sama: "Januari 2026"
            if ($masuk->month === $keluar->month) {
                return "{$mMasuk} {$yMasuk}";
            }
            return "{$mMasuk} - {$mKeluar} {$yMasuk}";
        }

        // kalau beda tahun: "Desember 2025 - Januari 2026"
        return "{$mMasuk} {$yMasuk} - {$mKeluar} {$yKeluar}";
    }

    public function getInitialsAttribute(): string
    {
        $name = trim((string) $this->nama);
        if ($name === '') return '?';

        $parts = preg_split('/\s+/', $name);
        $parts = array_values(array_filter($parts));

        // ambil 2 huruf: kata pertama + kata kedua (kalau ada)
        $first = mb_substr($parts[0], 0, 1);
        $second = isset($parts[1]) ? mb_substr($parts[1], 0, 1) : mb_substr($parts[0], 1, 1);

        $initials = mb_strtoupper($first . ($second ?: ''));
        return $initials ?: '?';
    }

    public function getStatusPklAttribute(): string
{
    [$masuk, $keluar] = $this->getLastPeriode();

    if (!$masuk) return 'UNKNOWN'; // kalau belum ada tanggal masuk sama sekali

    $today = Carbon::today();

    // belum ada tanggal keluar => masih aktif
    if (!$keluar) return 'ACTIVE';

    $keluar = $keluar instanceof Carbon ? $keluar : Carbon::parse($keluar);

    // kalau tanggal keluar hari ini atau masih di masa depan => masih aktif
    return $keluar->greaterThanOrEqualTo($today) ? 'ACTIVE' : 'ARCHIVE';
}

private function getLastPeriode(): array
{
    // kalau periode 2 ada tanggal masuknya, anggap itu periode terakhir
    if ($this->tgl_masuk_pkl_2) {
        return [$this->tgl_masuk_pkl_2, $this->tgl_keluar_pkl_2];
    }

    return [$this->tgl_masuk_pkl, $this->tgl_keluar_pkl];
}
}
