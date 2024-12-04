<?php
namespace App\Imports;

use App\Models\Status;
use App\Models\Task;
use App\Models\Project;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class TaskUpdateImport implements ToModel, WithHeadingRow
{
    // public function model(array $row)
    // {

    //     $project = Project::where('project_name', $row['project_name'])->first();
    //     $status = Status::where('status_name', $row['status_name'])->first();
    //     $serialDate = $row['end_date'];
    //     dump($serialDate);
    //     // if ($project && $status) {
    //     //     $task = Task::find($row['task_id']);
    //     //     if ($task) {
    //     //         $project_id = $project->id;
    //     //         $task_status = $status->id;
    //     //         $baseDate = Carbon::create(1900, 1, 1)->addDays($serialDate - 2);
    //     //         // dd($baseDate);
    //     //         // $end_date = Carbon::createFromFormat('d/m/Y', $row['end_date'])->format('Y-m-d');
    //     //         $end_date = $baseDate->format('Y-m-d');
    //     //         // dump($task, $project_id, $end_date, $task_status);
    //     //         // dd('kk');
    //     //         $task->update(['project_id' => $project_id, 'due_date' => $end_date, 'task_status' => $task_status]);

    //     //     }
    //     // }
    //     // dd('j');
    // }
    public function model(array $row)
    {
        // Fetch the project and status based on the provided row data
        $project = Project::where('project_name', $row['project_name'])->first();
        $status = Status::where('status_name', $row['status_name'])->first();

        // Get the serial date (Excel date format)
        $serialDate = $row['end_date'];
        // dd($row);
        // Check if the serial date is numeric
        if (is_numeric($serialDate)) {
            // Convert Excel serial date to readable format using Carbon
            $baseDate = Carbon::create(1900, 1, 1)->addDays($serialDate - 2);
            // dd($baseDate); // Subtract 2 for Excel date bug
            $end_date = $baseDate->format('Y-m-d'); // Format the date to Y-m-d

            // Only update the task if project and status exist
            if ($project && $status) {
                // Find the task by task_id from the row
                $task = Task::find($row['task_id']);

                if ($task) {
                    // dd($end_date);
                    // Update the task with the new project_id, due_date, and task_status
                    $change = $task->update([
                        'project_id' => $project->id,
                        'due_date' => $end_date,
                        'task_status' => $status->id
                    ]);

                }
            }
        } else {
            // Handle non-numeric end_date
            dump("Non-numeric value encountered for end_date: " . $serialDate);
            dd('');

        }
    }

}
