<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;

class PurchasesTemplateExport implements FromArray, WithHeadings, ShouldAutoSize
{
    public function headings(): array
    {
        return [
            'tanggal',
            'sku',
            'nama_produk',
            'qty',
            'catatan',
        ];
    }

    public function array(): array
    {
        return [
            [
                '2026-06-08',
                'PRD-00001',
                'Kabel Data Type C',
                50,
                'Restock bulanan dari supplier'
            ],
            [
                '2026-06-08',
                '', // Biarkan kosong jika mencari berdasarkan nama_produk
                'Kemeja Polos Pria',
                100,
                'Restock tambahan'
            ]
        ];
    }
}
