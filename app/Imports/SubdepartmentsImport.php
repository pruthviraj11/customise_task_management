<?php

namespace App\Imports;

use App\Models\Subdepartment;
use Maatwebsite\Excel\Concerns\ToModel;

class SubdepartmentsImport implements ToModel
{
    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        // dd($row);
        return new Subdepartment([
            'id' => (int) $row[0],
            'department_id' => (int) $row[3],
            'sub_department_name' => $row[1],
            'description' => (isset($row[2])) ? $row[2] : null,
            // 'hod' => (isset($row['hod'])) ? (int) $row['hod'] : null,
            'status' => (isset($row['status'])) ? $row[6] : 'on',

        ]);
    }
}
