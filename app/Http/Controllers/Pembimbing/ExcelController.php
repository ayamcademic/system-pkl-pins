<?php

namespace App\Http\Controllers\Pembimbing;

use App\Http\Controllers\Controller;
use App\Exports\PesertaPklExport;
use App\Imports\PesertaPklImport;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class ExcelController extends Controller
{
    public function index()
{
    return view('pembimbing.excel');
}


    public function export()
    {
        $filename = 'data-peserta-pkl-pins ' . now()->format('d-m-Y') . '.xlsx'; //sebenarnya bisa ditambahin _H-i-s buat jam sama menitnya, cuma ngawur dan aku males benerin
        return Excel::download(new PesertaPklExport(), $filename);
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => ['required', 'file', 'mimes:xlsx,xls'],
        ]);

        Excel::import(new PesertaPklImport(), $request->file('file'));

        return back()->with('success', 'Import berhasil! YIPPIEEEE!!!!☆(*^o^)乂(^-^*)☆');
    }
}
