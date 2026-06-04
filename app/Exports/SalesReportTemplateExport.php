<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;

class SalesReportTemplateExport implements FromArray, WithHeadings, ShouldAutoSize
{
    public function headings(): array
    {
        return [
            'tanggal_waktu',
            'nama_produk',
            'qty',
            'catatan',
        ];
    }

    public function array(): array
    {
        return [
            ['2026-05-21 10:30:00', 'Kabel Data Type C', 2, 'Contoh laporan penjualan'],
            ['2026-05-21 11:00:00', 'Kemeja Polos Pria', 1, ''],
        ];
    }
}