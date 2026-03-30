<?php

namespace App\Exports;

use App\Models\PesertaPkl;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
// use PhpOffice\PhpSpreadsheet\Shared\Date as ExcelDate;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;


class PesertaPklExport implements
    FromCollection,
    WithHeadings,
    WithMapping,
    WithColumnFormatting,
    WithStyles,
    ShouldAutoSize
{
    public function collection()
    {
        return PesertaPkl::query()
            ->orderBy('tgl_masuk_pkl', 'asc')
            ->orderBy('asal_sekolah')
            ->orderBy('nama')
            ->get([
              'tgl_masuk_pkl',
                'tgl_keluar_pkl',
                'tgl_masuk_pkl_2',
                'tgl_keluar_pkl_2',
                            'asal_sekolah',
                'nama',
                'kompetensi_keahlian',
                // 'durasi_pkl',
                'no_hp',
                'alamat_rumah',
                'nama_guru_pembimbing',
                'no_hp_guru_pembimbing',
            ]);
    }

    public function headings(): array
    {
        return [
            'Tgl Masuk PKL',
            'Tgl Keluar PKL',
            'Asal Sekolah',
            'Nama',
            'Kompetensi Keahlian',
            'Durasi PKL',
            'No HP',
            'Alamat Rumah',
            'Nama Guru Pembimbing',
            'No HP Guru Pembimbing',
        ];
    }

    public function map($row): array
    {
        // // Excel butuh serial number untuk tanggal
        // $tgl = $row->tgl_masuk_pkl
        //     ? ExcelDate::PHPToExcel(Carbon::parse($row->tgl_masuk_pkl))
        //     : null;

        $tglMasuk = $this->joinDates($row->tgl_masuk_pkl, $row->tgl_masuk_pkl_2);
    $tglKeluar = $this->joinDates($row->tgl_keluar_pkl, $row->tgl_keluar_pkl_2);


        return [
             $tglMasuk,                 // A (text)
             $tglKeluar,                // B (text)
            $row->asal_sekolah,
            $row->nama,
            $row->kompetensi_keahlian,
            $row->durasi_pkl,
            (string) $row->no_hp,                  // text biar ga scientific
            $row->alamat_rumah,
            $row->nama_guru_pembimbing,
            (string) $row->no_hp_guru_pembimbing,  // text juga
        ];
    }

    public function columnFormats(): array
    {
        return [
            'A' => NumberFormat::FORMAT_TEXT, // tgl masuk gabungan
            'B' => NumberFormat::FORMAT_TEXT, // tgl keluar gabungan
        'G' => NumberFormat::FORMAT_TEXT, // No HP
        'J' => NumberFormat::FORMAT_TEXT, // No HP Guru
        ];
    }

    public function styles(Worksheet $sheet)
    {
        // Tentuin area data (A1 sampai I{lastRow})
        $lastRow = $sheet->getHighestRow();
      $rangeAll = "A1:J{$lastRow}";
$rangeHeader = "A1:J1";
$rangeBody = $lastRow >= 2 ? "A2:J{$lastRow}" : null;


        // Freeze header
        $sheet->freezePane('A2');

        // Autofilter
        $sheet->setAutoFilter($sheet->calculateWorksheetDimension());

        // Tinggi header biar gagah
        $sheet->getRowDimension(1)->setRowHeight(22);

        // Header: hitam + putih + bold + center
        $sheet->getStyle($rangeHeader)->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF'],
                'name' => 'Georgia',
                'size' => 12,
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '000000'],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
        ]);

        // Wrap text buat alamat (kolom G)
        $sheet->getStyle('H:H')->getAlignment()->setWrapText(true);


        // Align beberapa kolom biar rapih
      $sheet->getStyle("A:A")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER); // tgl masuk text
$sheet->getStyle("B:B")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER); // tgl keluar text
$sheet->getStyle("G:G")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER); // hp
$sheet->getStyle("J:J")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER); // hp guru


        // Border tipis untuk semua cell
        $sheet->getStyle($rangeAll)->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => 'E5E7EB'], // abu soft
                ],
            ],
        ]);

        // Zebra rows (body doang)
        if ($rangeBody) {
            for ($r = 2; $r <= $lastRow; $r++) {
                if ($r % 2 === 0) {
                    $sheet->getStyle("A{$r}:J{$r}")->applyFromArray([
                        'fill' => [
                            'fillType' => Fill::FILL_SOLID,
                            'startColor' => ['rgb' => 'F9FAFB'], // abu muda
                        ],
                    ]);
                }
            }
        }

        // Optional: kasih padding rasa-rasa (Excel ga punya padding beneran, tapi vertical center helps)
        if ($rangeBody) {
            $sheet->getStyle($rangeBody)->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
        }

        return [];
    }
    private function joinDates($d1, $d2): string
{
    $out = [];

    if ($d1) {
        $out[] = Carbon::parse($d1)->format('d/m/Y');
    }

    if ($d2) {
        $out[] = Carbon::parse($d2)->format('d/m/Y');
    }

    return count($out) ? implode(' & ', $out) : '-';
}
}
