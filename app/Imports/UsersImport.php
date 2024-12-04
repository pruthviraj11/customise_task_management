<?php
namespace App\Imports;

use App\Models\User;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use Carbon\Carbon;

class UsersImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        // Split the EMP NAME into first_name and last_name
        // dd($row);
        $names = explode(' ', $row['name'], 2);

        $first_name = $names[0];
        $last_name = isset($names[1]) ? $names[1] : '';

        return new User([
            'id' => $row['id'],
            'first_name' => $first_name,
            'last_name' => $last_name,
            'email' => $row['email'],
            'department_id' => $row['department_id'],
            'subdepartment' => $row['subdepartment'],
            'phone_no' => $row['phone_no'],
            'password' => $row['password'],
            // 'email_verified_at' => $this->transformDate($row['Email Verification']),
            'username' => $row['username'],
            // 'branch' => $row['branch'],
            'report_to' => $row['report_to'],
            'designation' => $row['designation'],
            'status' => 1,
            'created_at' => now(),
            'updated_at' => now(),
            // Add other necessary mappings
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
