<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\ImportFile;

class ImportController extends Controller
{
    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv',
        ]);

        Excel::import(new ImportFile, request()->file('file'));

        return back()->with('success', 'Data Imported Successfully');
    }
    public function update(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv',
        ]);

        Excel::import(new ImportFile, request()->file('file'));

        return back()->with('success', 'Data Imported Successfully');
    }
}
