<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithDrawings;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\WithTitle;

class DebtorsPerSheet implements FromView, ShouldAutoSize, WithDrawings, WithEvents, WithTitle {
    private $data;
    private $idx;
    private $branch;
    private $range;

    public function __construct($data, $idx, $branch, $range) {
        $this->data         = $data;
        $this->idx          = $idx;
        $this->branch       = $branch;
        $this->range        = $range;
    }

    public function view(): View {
        $data       = $this->data;
        $branch     = $this->branch;
        $range      = $this->range;
        switch ($this->idx) {
            case 0: $title = 'ESTADO DE CUENTA'; break;
            case 1: $title = 'DEUDORES'; break;
            case 2: $title = 'CARTERA VENCIDA'; break;
            case 3: $title = 'CONTADO'; break;
        }
        return view('exports.debtors', compact('data', 'branch', 'range', 'title'));
    }

    public function registerEvents(): array {
        return [
          // Handle by a closure.
          AfterSheet::class => function(AfterSheet $event) {
              // Sheet header styles
              $event->sheet->getStyle('C1')->getFont()->setSize(20);
              $event->sheet->getStyle('C1')->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
              $event->sheet->getStyle('C1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
              $event->sheet->getStyle('A6:L6')->getBorders()->getTop()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_DOUBLE);
              $event->sheet->getStyle('A6:L6')->getBorders()->getTop()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_DOUBLE);

              if(count($this->data) > 1) {
                  $resultRange = sprintf('B9:B%s', 9 + count($this->data[0]));
                  $event->sheet->getStyle($resultRange)->getNumberFormat()->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_CURRENCY_USD_SIMPLE);

                  $resultRange = sprintf('E9:E%s', 9 + count($this->data[1]));
                  $event->sheet->getStyle($resultRange)->getNumberFormat()->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_CURRENCY_USD_SIMPLE);

                  $resultRange = sprintf('H9:H%s', 9 + count($this->data[2]));
                  $event->sheet->getStyle($resultRange)->getNumberFormat()->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_CURRENCY_USD_SIMPLE);

                  $maxRow = 9 + max(count($this->data[0]), count($this->data[1]), count($this->data[2]));
                  $total1Col        = 'B' . $maxRow;
                  $total1Formula    = count($this->data[0]) > 0 ? sprintf('=SUM(B9:B%s)', 9 + count($this->data[0]) - 1) : '=0';
                  $event->sheet->setCellValue($total1Col, $total1Formula);
                  $event->sheet->getStyle($total1Col)->getNumberFormat()->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_CURRENCY_USD_SIMPLE);


                  $total2Col        = 'E' . $maxRow;
                  $total2Formula    = count($this->data[1]) > 0 ? sprintf('=SUM(E9:E%s)', 9 + count($this->data[0]) - 1) : '=0';
                  $event->sheet->setCellValue($total2Col, $total2Formula);
                  $event->sheet->getStyle($total2Col)->getNumberFormat()->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_CURRENCY_USD_SIMPLE);


                  $total3Col        = 'H' . $maxRow;
                  $total3Formula    = count($this->data[2]) > 0 ? sprintf('=SUM(H9:H%s)', 9 + count($this->data[0]) - 1) : '=0';
                  $event->sheet->setCellValue($total3Col, $total3Formula);
                  $event->sheet->getStyle($total3Col)->getNumberFormat()->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_CURRENCY_USD_SIMPLE);

              } else {
                  $resultRange = sprintf('B8:B%s', 8 + count($this->data[0]));
                  $event->sheet->getStyle($resultRange)->getNumberFormat()->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_CURRENCY_USD_SIMPLE);

                  $totalCol        = 'B' . (8 + count($this->data[0]));
                  $totalFormula    = count($this->data[0]) > 0 ? sprintf('=SUM(B8:B%s)', 8 + count($this->data[0]) - 1) : '=0';
                  $event->sheet->setCellValue($totalCol, $totalFormula);
              }


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
              //
              // $totalCol = 'L' . (12 + count($this->data) + 4);
              // $totalFormula = sprintf('=SUM(%s-%s+%s)', $subtotalCol, $discountCol, $taxCol);
              // $event->sheet->setCellValue($totalCol, $totalFormula);
              // $event->sheet->getStyle($totalCol)->getNumberFormat()->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_CURRENCY_USD_SIMPLE);


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
        switch ($this->idx) {
            case 0: return 'ESTADO DE CUENTA';
            case 1: return 'DEUDORES';
            case 2: return 'CARTERA VENCIDA';
            case 3: return 'CONTADO';
        }

    }
}
