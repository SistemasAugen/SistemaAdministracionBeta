<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithDrawings;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\WithTitle;

class BalanceExport implements FromView, ShouldAutoSize, WithDrawings, WithEvents, WithTitle {
    private $data;

    public function __construct($data, $client) {
        $this->data     = $data;
        $this->client   = $client;
    }

    public function view(): View {
        $data   = $this->data;
        $client = $this->client;
        return view('exports.account', compact('data', 'client'));
    }

    public function registerEvents(): array {
        return [
          // Handle by a closure.
          AfterSheet::class => function(AfterSheet $event) {
              // Sheet header styles
              $event->sheet->getStyle('G1')->getFont()->setSize(20);
              $event->sheet->getStyle('G1')->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
              $event->sheet->getStyle('G1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
              $event->sheet->getStyle('A6:L6')->getBorders()->getTop()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_DOUBLE);
              $event->sheet->getStyle('A6:L6')->getBorders()->getTop()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_DOUBLE);

              $resultRange = sprintf('I12:L%s', 12 + count($this->data) - 1);
              $event->sheet->getStyle($resultRange)->getNumberFormat()->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_CURRENCY_USD_SIMPLE);

              // $subtotalCol = 'L' . (12 + count($this->data) + 1);
              // $subtotalFormula = sprintf('=SUM(I12:I%s)', 12 + count($this->data) - 1);
              // $event->sheet->setCellValue($subtotalCol, $subtotalFormula);
              // $event->sheet->getStyle($subtotalCol)->getNumberFormat()->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_CURRENCY_USD_SIMPLE);
              //
              // $discountCol = 'L' . (12 + count($this->data) + 2);
              // $discountFormula = sprintf('=SUM(J12:K%s)', 12 + count($this->data) - 1);
              // $event->sheet->setCellValue($discountCol, $discountFormula);
              // $event->sheet->getStyle($discountCol)->getNumberFormat()->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_CURRENCY_USD_SIMPLE);
              //
              // $taxCol = 'L' . (12 + count($this->data) + 3);
              // if(in_array($this->client->branch_id, [6, 14, 18]))
              //   $taxFormula = sprintf('=(%s-%s) * 0.08', $subtotalCol, $discountCol);
              // else
              //   $taxFormula = sprintf('=(%s-%s) * 0.16', $subtotalCol, $discountCol);
              // $event->sheet->setCellValue($taxCol, $taxFormula);
              // $event->sheet->getStyle($taxCol)->getNumberFormat()->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_CURRENCY_USD_SIMPLE);

              $totalCol = 'L' . (12 + count($this->data) + 1);
              // $totalFormula = sprintf('=SUM(%s-%s+%s)', $subtotalCol, $discountCol, $taxCol);
              $totalFormula = sprintf('=SUM(L12:L%s)', 12 + count($this->data) - 1);
              $event->sheet->setCellValue($totalCol, $totalFormula);
              $event->sheet->getStyle($totalCol)->getNumberFormat()->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_CURRENCY_USD_SIMPLE);


          },
        ];
    }

    /**
     * @return BaseDrawing|BaseDrawing[]
     */
    public function drawings()
    {
        $drawing = new \PhpOffice\PhpSpreadsheet\Worksheet\Drawing();
        $drawing->setName('Logo');
        $drawing->setDescription('Logo');
        $drawing->setPath(public_path('/images/augen-labs_excel.png'));
        $drawing->setHeight(90);

        return $drawing;
    }

    public function title(): string {
        return 'ESTADO DE CUENTA';
    }
}

?>
