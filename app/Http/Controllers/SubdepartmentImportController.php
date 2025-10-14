<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\SubdepartmentsImport;

class SubdepartmentImportController extends Controller
{
    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xls,xlsx',
        ]);
        Excel::import(new SubdepartmentsImport, $request->file('file'));

        return redirect()->back()->with('success', 'Subdepartments imported successfully.');
    }
}
