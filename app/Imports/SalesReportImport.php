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

class SalesReportImport implements ToCollection, WithHeadingRow, SkipsEmptyRows
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
                $tanggalWaktu = $this->parseTanggalWaktu($this->firstValue($rowData, ['tanggal_waktu', 'tanggal waktu']));
                $sku = trim((string) $this->firstValue($rowData, ['sku']));
                $quantityValue = $this->firstValue($rowData, ['qty', 'quantity', 'jumlah']);
                $quantity = is_numeric($quantityValue) ? (int) $quantityValue : 0;
                $note = trim((string) $this->firstValue($rowData, ['catatan', 'note', 'keterangan']));

                if ($tanggalWaktu === null || $sku === '' || $quantity < 1) {
                    throw new \RuntimeException("Baris {$rowNumber} wajib berisi tanggal_waktu, sku, dan qty yang valid.");
                }

                $product = Product::where('sku', $sku)->lockForUpdate()->first();

                if (! $product) {
                    throw new \RuntimeException("Baris {$rowNumber}: SKU {$sku} tidak ditemukan.");
                }

                if ($product->current_stock < $quantity) {
                    throw new \RuntimeException("Baris {$rowNumber}: stok {$product->name} tidak mencukupi.");
                }

                StockMovement::create([
                    'product_id' => $product->id,
                    'type' => 'out',
                    'status' => 'success',
                    'quantity' => $quantity,
                    'price_at_transaction' => $product->unit_price,
                    'user_id' => auth()->id(),
                    'movement_date' => $tanggalWaktu,
                    'note' => $note !== '' ? $note : 'Impor laporan penjualan',
                ]);

                $product->decrement('current_stock', $quantity);
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

    private function parseTanggalWaktu(mixed $value): ?Carbon
    {
        if ($value instanceof \DateTimeInterface) {
            return Carbon::instance($value);
        }

        if (is_numeric($value)) {
            try {
                return Carbon::instance(ExcelDate::excelToDateTimeObject((float) $value));
            } catch (\Throwable) {
                // Fall through to string parsing below.
            }
        }

        $value = trim((string) $value);

        if ($value === '') {
            return null;
        }

        foreach (['d/m/Y', 'd-m-Y', 'Y-m-d'] as $format) {
            $parsedDate = Carbon::createFromFormat($format, $value);

            if ($parsedDate !== false) {
                return $parsedDate->setTimeFrom(now());
            }
        }

        try {
            return Carbon::parse($value);
        } catch (\Throwable) {
            return null;
        }
    }
}