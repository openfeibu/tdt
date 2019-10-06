<?php

namespace App\Imports;

use App\Models\Shop;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Concerns\ToModel;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;

class ShopImport implements ToCollection,WithColumnFormatting
{
    use Importable;

    public function collection(Collection $rows)
    {
        return $rows;
    }
    public function columnFormats(): array
    {
        return [
            'J' => NumberFormat::FORMAT_DATE_DDMMYYYY, //日期
            'M' => NumberFormat::FORMAT_DATE_DDMMYYYY,
        ];
    }
}