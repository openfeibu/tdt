<?php

namespace App\Services;

use Log;
use File;
use Session;
use Storage;
use Excel;
use App\Import\DemoImport;
use Illuminate\Http\Request;

class ExcelService
{
    protected $request;

    function __construct(Request $request)
    {
        $this->request = $request;
        $this->upload_folder = '/uploads/excel/';
    }
    /*
    public function uploadExcel()
    {
        $file = $this->request->file;

        isVaildExcel($file);

        $directory = storage_path('uploads') . DIRECTORY_SEPARATOR . 'excel';

        $extension = $file->getClientOriginalExtension();

        $filename = time().rand(100000, 999999) . '.' . $extension;

        $save_path = '/uploads/excel/'.$filename;

        Storage::put($save_path, file_get_contents($file->getRealPath()));

        $excel_file_path = storage_path('uploads') .DIRECTORY_SEPARATOR. 'excel'.DIRECTORY_SEPARATOR. $filename;

        $res = [];

        $res = (new DemoImport)->toArray($excel_file_path)[0];

        Excel::load($excel_file_path, function($reader) use (&$res) {

            $res = $reader->all()->toArray();

        });

        return $res;
    }
    */
    public function uploadExcel()
    {
        $file = $this->request->file;

        isVaildExcel($file);

        $res = (new DemoImport)->toArray($file)[0];

        return $res;
    }

}
