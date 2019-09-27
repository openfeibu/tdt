<?php

namespace App\Imports;

use App\Models\Shop;
use Illuminate\Support\Facades\Hash;
//use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\Importable;

class ShopImport implements ToModel
{
    public function ToCollection(Collection $rows)
    {
        //如果需要去除表头
        unset($rows[0]);
        var_dump($rows);exit;
        //$rows 是数组格式
        $this->createData($rows);
    }

    public function createData($rows)
    {
        //todo
    }
}