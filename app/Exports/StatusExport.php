<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\WithTitle;

class StatusExport implements FromView, ShouldAutoSize, WithTitle {
    private $data;

    public function __construct($orders) {
        $this->orders   = $orders;
    }

    public function view(): View {
        $orders = $this->orders;

        return view('exports.status', compact('orders'));
    }

    public function title(): string {
        return 'STATUS';
    }
}

?>
