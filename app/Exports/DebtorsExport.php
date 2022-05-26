<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use App\Exports\DebtorsPerSheet;

class DebtorsExport implements WithMultipleSheets {
    use Exportable;

    protected $data;

    public function __construct($data) {
        $this->data = $data;
    }

    /**
     * @return array
     */
    public function sheets(): array
    {
        $sheets = [];

        foreach ($this->data['sheets'] as $idx => $data) {
            $sheets[] = new DebtorsPerSheet($data, $idx, $this->data['branch'], $this->data['range']);
        }

        return $sheets;
    }
}
