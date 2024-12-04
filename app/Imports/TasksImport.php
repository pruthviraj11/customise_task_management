<?php
namespace App\Imports;

use App\Models\Task;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Carbon\Carbon;

class TasksImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        if ($row['start_date'] == 'NULL') {
            $row['start_date'] = NULL;

        }
        // dd($row);
        if ($row['due_date'] == 'NULL') {
            $row['due_date'] = NULL;

        }
        if ($row['completed_task_date_time'] == 'NULL') {
            $row['completed_task_date_time'] = NULL;

        }
        if ($row['deleted_by'] == 'NULL') {
            $row['deleted_by'] = NULL;
        }
        // dd($row);
        return new Task([
            'id' => $this->parseInteger($row['id'] ?? null),
            'priority_id' => $this->parseInteger($row['priority_id'] ?? null),
            'project_id' => $this->parseInteger($row['project_id'] ?? null),
            'department_id' => $this->parseInteger($row['department_id'] ?? null),
            'sub_department_id' => $this->parseInteger($row['sub_department_id'] ?? null),
            'task_status' => $this->parseInteger($row['status'] ?? null),
            'title' => $row['title'] ?? null,
            'subject' => $row['subject'] ?? null,
            'description' => $row['description'] ?? null,
            'start_date' => $this->convertExcelDate($row['start_date'] ?? null),
            'due_date' => $this->convertExcelDate($row['due_date'] ?? null),
            'accepted_date' => $this->convertExcelDate($row['accepted_task_date_time'] ?? null),
            'completed_date' => $this->convertExcelDate($row['completed_task_date_time'] ?? null),
            'deleted_by' => $row['deleted_by'] ?? null,
            'created_by' => $row['created_by'] ?? null,
            'updated_by' => $row['updated_by'] ?? null,
            'deleted_at' => $this->convertExcelDate($row['deleted_at'] ?? null, true),
            'created_at' => $this->convertExcelDate($row['created_at'] ?? null, true),
            'updated_at' => $this->convertExcelDate($row['updated_at'] ?? null, true),
            'closed' => $row['closed'] ?? 0,
            'ticket' => $row['tickettask'] ?? 0,
        ]);
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
    private function parseDate($date)
    {
        if (!$date || strtolower($date) == 'null')
            return null;

        $formats = ['d-m-Y', 'd-m-Y H:i:s', 'Y-m-d', 'Y-m-d H:i:s'];
        return $this->parseDateUsingFormats($date, $formats);
    }
    private function convertDate($date)
    {
        if (!$date || strtolower($date) == 'null')
            return null;

        // First, parse the date in the expected format
        $parsedDate = Carbon::createFromFormat('d-m-Y', $date);

        // Then, format it to 'Y-m-d'
        return $parsedDate->format('Y-m-d');
    }
    private function parseDateTime($dateTime)
    {
        if (!$dateTime || strtolower($dateTime) == 'null')
            return null;

        $formats = ['d-m-Y H:i:s', 'Y-m-d H:i:s', 'd-m-Y H:i', 'Y-m-d H:i'];
        return $this->parseDateUsingFormats($dateTime, $formats);
    }

    private function parseDateUsingFormats($date, $formats)
    {
        foreach ($formats as $format) {
            try {
                return Carbon::createFromFormat($format, $date);
            } catch (\Exception $e) {
                // Continue to next format
            }
        }
        return null; // or you can throw an exception if preferred
    }

    private function parseInteger($value)
    {
        if (!$value || strtolower($value) == 'null')
            return null;

        return (int) $value;
    }

    private function parseBoolean($value)
    {
        if (!$value || strtolower($value) == 'null')
            return null;

        return filter_var($value, FILTER_VALIDATE_BOOLEAN);
    }
}
