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
            'sku',
            'qty',
            'catatan',
        ];
    }

    public function array(): array
    {
        return [
            ['2026-05-21 10:30:00', 'ELK-001', 2, 'Contoh laporan penjualan'],
            ['2026-05-21 11:00:00', 'SKU-ABC-123', 1, ''],
        ];
    }
}