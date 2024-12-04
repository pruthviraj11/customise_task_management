<?php
namespace App\Imports;

use App\Models\Department;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use Carbon\Carbon;

class DepartmentsImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        return new Department([
            'id' => $row['id'],
            'department_name' => $row['name'],
            'description' => $row['description'],
            'hod' => $row['hod'],
            // 'created_at' => $this->transformDate($row['created_at']),
            // 'updated_at' => $this->transformDate($row['updated_at']),
            'deleted_at' => $this->transformDate($row['deleted_at']),
        ]);
    }

    /**
     * Transform Excel date to Carbon instance
     *
     * @param string $value
     * @return \Carbon\Carbon|null
     */
    public function transformDate($value)
    {
        // Check if the value is a valid date
        if (empty($value)) {
            return null;
        }

        // If value is an Excel date (integer or float), convert it
        if (is_numeric($value)) {
            return Date::excelToDateTimeObject($value);
        }

        // Attempt to parse the date as a string
        try {
            return Carbon::parse($value);
        } catch (\Exception $e) {
            // If parsing fails, return null
            return null;
        }
    }
}