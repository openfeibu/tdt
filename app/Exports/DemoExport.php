<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;    // 导出集合
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;     // 自动注册事件监听器
use Maatwebsite\Excel\Concerns\WithStrictNullComparison;    // 导出 0 原样显示，不为 null
use Maatwebsite\Excel\Concerns\WithTitle;    // 设置工作䈬名称
use Maatwebsite\Excel\Events\AfterSheet;    // 在工作表流程结束时会引发事件

class DemoExport implements FromCollection,WithEvents
{
    public $data;

    public function __construct(array $data)
    {
        $this->data = $data;
    }
    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                // 定义列宽度
                $event->sheet->getDelegate()->getStyle('A:Z')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);

                $widths = ['A' => 15, 'B' => 15, 'C' => 15, 'D' => 15, 'E' => 15, 'F' => 15, 'G' => 15, 'H' => 50, 'I' => 15, 'J' => 15,'K' => 15,'L' => 15];
                   foreach ($widths as $k => $v) {
                       // 设置列宽度
                       $event->sheet->getDelegate()->getColumnDimension($k)->setWidth($v);
                   }
               },
        ];
    }
    public function collection()
    {
        return new Collection($this->data);
    }

}