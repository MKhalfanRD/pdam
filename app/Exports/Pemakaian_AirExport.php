<?php

namespace App\Exports;

use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;

class Pemakaian_AirExport implements FromCollection, WithHeadings, WithMapping, WithCustomStartCell, WithEvents
{
    protected $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function collection()
    {
        return $this->data;
    }

    public function startCell(): string
    {
        return 'A1'; // Sel awal untuk heading
    }

    public function headings(): array
    {
        return [
            'Blok', 'Nama', 'Bulan', 'Tahun', 'Awal', 'Akhir', 'Total Tagihan', 'Status',
        ];
    }

    public function map($row): array
    {
        $bulanTahun = Carbon::createFromFormat('Y-m-d', $row->bulan);

        return [
            $row->warga->alamat,                     // Blok
            $row->warga->nama,                       // Nama
            $bulanTahun->format('F'),                // Bulan (Nama bulan)
            $bulanTahun->format('Y'),                // Tahun
            $row->pemakaianLama,                     // Awal
            $row->pemakaianBaru,                     // Akhir
            $row->tagihanAir,                        // Total Tagihan
            $row->pembayaran->first()?->status ?? 'Belum Bayar', // Status Pembayaran
        ];
    }

    public function registerEvents(): array
{
    return [
        AfterSheet::class => function (AfterSheet $event) {
            $sheet = $event->sheet;

            // Hitung total tagihan
            $totalRow = count($this->data) + 2; // Baris data + 1 untuk heading + 1 untuk total
            $sheet->setCellValue("G{$totalRow}", "Total Tagihan:");
            $sheet->setCellValue("H{$totalRow}", "=SUM(G2:G" . ($totalRow - 1) . ")");

            // Format kolom tagihan dalam format mata uang Rp
            $sheet->getStyle("G2:G{$totalRow}")
                ->getNumberFormat()
                ->setFormatCode('"Rp. " #,##0');

            // Hitung jumlah tagihan berdasarkan status
            $statusStartRow = 2; // Baris data pertama
            $statusEndRow = $totalRow - 1; // Baris terakhir data
            $statusSummaryStart = $totalRow + 2; // Mulai dari 2 baris di bawah total tagihan

            // Belum Bayar
            $sheet->setCellValue("G{$statusSummaryStart}", "Belum Bayar:");
            $sheet->setCellValue("H{$statusSummaryStart}", "=SUMIF(H{$statusStartRow}:H{$statusEndRow},\"Belum Bayar\",G{$statusStartRow}:G{$statusEndRow})");

            // Pending
            $sheet->setCellValue("G" . ($statusSummaryStart + 1), "Pending:");
            $sheet->setCellValue("H" . ($statusSummaryStart + 1), "=SUMIF(H{$statusStartRow}:H{$statusEndRow},\"Pending\",G{$statusStartRow}:G{$statusEndRow})");

            // Terverifikasi
            $sheet->setCellValue("G" . ($statusSummaryStart + 2), "Terverifikasi:");
            $sheet->setCellValue("H" . ($statusSummaryStart + 2), "=SUMIF(H{$statusStartRow}:H{$statusEndRow},\"Terverifikasi\",G{$statusStartRow}:G{$statusEndRow})");

            // Format jumlah tagihan berdasarkan status
            $sheet->getStyle("H{$totalRow}:H" . ($statusSummaryStart + 2))
                ->getNumberFormat()
                ->setFormatCode('"Rp. " #,##0');

            // Format sel menjadi bold
            $sheet->getStyle("G{$totalRow}:H" . ($statusSummaryStart + 2))->applyFromArray([
                'font' => ['bold' => true],
            ]);
        },
    ];
}

}
