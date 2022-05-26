<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\WithTitle;

class ClientsExport implements FromView, ShouldAutoSize, WithTitle {
    private $data;

    public function __construct($clients) {
        $this->clients   = $clients;
    }

    public function view(): View {
        $clients = $this->clients;

        return view('exports.clients', compact('clients'));
    }

    public function title(): string {
        return 'CLIENTES';
    }
}

?>
