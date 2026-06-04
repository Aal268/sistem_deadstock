<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Illuminate\Contracts\View\View;

class SalesReportExport implements FromView, ShouldAutoSize
{
    protected $allSales;

    public function __construct($allSales)
    {
        $this->allSales = $allSales;
    }

    public function view(): View
    {
        return view('kasir.histori-sales.export', [
            'allSales' => $this->allSales
        ]);
    }
}
