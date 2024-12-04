<?php
namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ImportFile implements ToCollection
{
    /**
     * @param Collection $collection
     */
    public function collection(Collection $collection)
    {
        foreach ($collection as $key => $row) {
            // dd($row);
            if ($key == 0) {
                continue;
            }

            if ($row[3] == 1) {
                $row[3] = 0;
            }
            if ($row[3] == 0) {
                $row[3] = 1;
            }
            if ($row[3] == 2) {
                $row[3] = 2;
            }
            if ($row[6] = 'NULL') {
                $row[6] = null;
            }

            DB::table('task_assignees')->insert([
                'id' => $row[0],
                'task_id' => $row[1],
                'user_id' => $row[2],
                'status' => $row[5],
                'remark' => $row[4],
                'deleted_by' => $row[6],
                'created_by' => $row[3],
                'updated_by' => null,

            ]);
        }
    }
    private function convertExcelDate($excelDate, $isDateTime = false)
    {
        if (!$excelDate || strtolower($excelDate) == 'null')
            return null;

        try {
            $carbonDate = Carbon::createFromFormat('Y-m-d', '1899-12-30')->addDays((int) $excelDate);
            return $isDateTime ? $carbonDate : $carbonDate->format('Y-m-d');
        } catch (\Exception $e) {
            return null;
        }
    }
}
