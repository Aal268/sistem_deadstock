<?php

namespace App\Imports;

use App\Models\Product;
use App\Models\StockMovement;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use PhpOffice\PhpSpreadsheet\Shared\Date as ExcelDate;

class PurchasesImport implements ToCollection, WithHeadingRow, SkipsEmptyRows
{
    public function collection(Collection $rows): void
    {
        if ($rows->isEmpty()) {
            throw new \RuntimeException('File import kosong.');
        }

        DB::transaction(function () use ($rows) {
            foreach ($rows as $index => $row) {
                $rowNumber = $index + 2;
                $rowData = $this->normalizeRow($row);

                $tanggalVal = $this->firstValue($rowData, ['tanggal', 'date', 'movement_date']);
                $tanggal = $this->parseTanggal($tanggalVal);
                $sku = trim((string) $this->firstValue($rowData, ['sku']));
                $namaProduk = trim((string) $this->firstValue($rowData, ['nama_produk', 'nama produk', 'produk', 'name', 'nama_barang']));
                $quantityValue = $this->firstValue($rowData, ['qty', 'quantity', 'jumlah', 'kuantitas']);
                $quantity = is_numeric($quantityValue) ? (int) $quantityValue : 0;
                $note = trim((string) $this->firstValue($rowData, ['catatan', 'note', 'keterangan']));

                if ($tanggal === null || ($sku === '' && $namaProduk === '') || $quantity < 1) {
                    throw new \RuntimeException("Baris {$rowNumber} wajib berisi tanggal, nama_produk/sku, dan qty yang valid.");
                }

                $product = null;
                if ($sku !== '') {
                    $product = Product::where('sku', $sku)->lockForUpdate()->first();
                }

                if (! $product && $namaProduk !== '') {
                    $product = Product::where('name', $namaProduk)->lockForUpdate()->first();
                }

                if (! $product) {
                    $identifier = $namaProduk !== '' ? "nama '{$namaProduk}'" : "SKU '{$sku}'";
                    throw new \RuntimeException("Baris {$rowNumber}: Produk dengan {$identifier} tidak ditemukan.");
                }

                StockMovement::create([
                    'product_id' => $product->id,
                    'type' => 'in',
                    'quantity' => $quantity,
                    'movement_date' => $tanggal,
                    'note' => $note !== '' ? $note : 'Impor barang masuk',
                ]);

                $product->increment('current_stock', $quantity);
            }
        });
    }

    private function normalizeRow(mixed $row): array
    {
        return collect($row)
            ->mapWithKeys(function ($value, $key) {
                $normalizedKey = strtolower(trim((string) $key));
                $normalizedKey = preg_replace('/^\x{FEFF}/u', '', $normalizedKey) ?? $normalizedKey;
                return [$normalizedKey => $value];
            })
            ->all();
    }

    private function firstValue(array $row, array $keys): mixed
    {
        foreach ($keys as $key) {
            if (array_key_exists($key, $row)) {
                return $row[$key];
            }
        }
        return null;
    }

    private function parseTanggal(mixed $value): ?Carbon
    {
        if ($value instanceof \DateTimeInterface) {
            return Carbon::instance($value);
        }

        if (is_numeric($value)) {
            try {
                return Carbon::instance(ExcelDate::excelToDateTimeObject((float) $value));
            } catch (\Throwable) {
                // Fall through
            }
        }

        $value = trim((string) $value);
        if ($value === '') {
            return null;
        }

        foreach (['d/m/Y', 'd-m-Y', 'Y-m-d'] as $format) {
            try {
                $parsedDate = Carbon::createFromFormat($format, $value);
                if ($parsedDate !== false) {
                    return $parsedDate->setTimeFrom(now());
                }
            } catch (\Throwable) {
                // Continue to next format
            }
        }

        try {
            return Carbon::parse($value);
        } catch (\Throwable) {
            return null;
        }
    }
}
