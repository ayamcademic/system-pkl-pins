<?php

namespace App\Imports;

use App\Models\PesertaPkl;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class PesertaPklImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        if (empty($row['nama'])) return null;

        // // tanggal masuk (aman untuk format excel/string)
        // $tgl = null;
        // if (!empty($row['tgl_masuk_pkl'])) {
        //     try { $tgl = Carbon::parse($row['tgl_masuk_pkl'])->format('Y-m-d'); } catch (\Throwable $e) {}
        // } BIANG KEROK TAHUN 1970

        $tgl = $this->excelDateToYmd($row['tgl_masuk_pkl'] ?? null);

        $foto = trim((string)($row['foto_peserta_pkl'] ?? '')) ?: null;

        return PesertaPkl::updateOrCreate(
            [
                'nama' => trim((string)$row['nama']),
                'asal_sekolah' => trim((string)($row['asal_sekolah'] ?? '')),
            ],
            [
                'tgl_masuk_pkl' => $tgl,
                'kompetensi_keahlian' => trim((string)($row['kompetensi_keahlian'] ?? '')),
                'durasi_pkl' => trim((string)($row['durasi_pkl'] ?? '')),
                'no_hp' => $this->phone($row['no_hp'] ?? null),
                'alamat_rumah' => trim((string)($row['alamat_rumah'] ?? '')),
                'nama_guru_pembimbing' => trim((string)($row['nama_guru_pembimbing'] ?? '')),
                'no_hp_guru_pembimbing' => $this->phone($row['no_hp_guru_pembimbing'] ?? null),
                // kita pakai foto_path sebagai field utama
                'foto_path' => $foto,
            ]
        );
    }

    private function phone($v): ?string
    {
        if ($v === null || $v === '') return null;
        $s = preg_replace('/\.0$/', '', trim((string)$v));
        $s = preg_replace('/[^0-9+]/', '', $s);
        if (preg_match('/^8\d{8,13}$/', $s)) $s = '0'.$s;
        return $s ?: null;
    }

     private function excelDateToYmd($value): ?string
    {
        if ($value === null || $value === '') return null;

        if (is_numeric($value)) {
            $serial = (float) $value;
            if ($serial < 20000) return null;

            $unix = ($serial - 25569) * 86400;
            return gmdate('Y-m-d', (int) round($unix));
        }

        try {
            return Carbon::parse($value)->format('Y-m-d');
        } catch (\Throwable $e) {
            return null;
        }
    }

    
}
