<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ProductsTemplateExport implements FromArray, WithHeadings, ShouldAutoSize
{
    public function headings(): array
    {
        return [
            'sku',
            'nama_barang',
            'kategori',
            'supplier',
            'stok_sekarang',
            'stok_aman',
            'harga_beli',
            'harga_jual',
        ];
    }

    public function array(): array
    {
        return [
            [
                'PRD-00001',
                'Kabel Data Type C',
                'Elektronik',
                'PT. Global Tech',
                10,
                2,
                15000,
                25000
            ],
            [
                '', // Biarkan kosong agar auto-generate SKU
                'Kemeja Polos Pria',
                'Fashion',
                'PT. Busana Indah',
                25,
                5,
                75000,
                120000
            ]
        ];
    }
}
