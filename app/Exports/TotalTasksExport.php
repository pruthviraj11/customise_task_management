<?php

namespace App\Exports;

use App\Models\Task;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class TotalTasksExport implements FromCollection, WithHeadings, WithMapping
{
    protected $tasks;

    public function __construct($tasks)
    {
        $this->tasks = $tasks;
    }

    public function collection()
    {
        return $this->tasks;
    }

    public function headings(): array
    {
        return [
            'Task Number',
            'Task/Ticket',
            'Title',
            'Subject',
            'AssignBy',
            'Task Assign To',
            'Status',
            'Created Date',
            'Start Date',
            'Due Date',
            'Completed Date',
            'Accepted Task Date',
            'Project',
            'Department',
            'Sub Department',
            'Owner Department',
            'Owner Sub Department',
            'Owner Contact Info',
            'Close Date'

        ];
    }

    public function map($task): array
    {
        return [
            $task->id,
            $task->ticket == 0 ? 'Task' : ($task->ticket == 1 ? 'Ticket' : '-') ?? '-',
            $task->title ?? '-',
            $task->subject ?? '-',
            $task->creator ? ($task->creator->first_name . ' ' . $task->creator->last_name) : '-',
            $task->users ? implode(', ', $task->users->map(function ($user) {
                return ($user->first_name ?? '-') . ' ' . ($user->last_name ?? '-');
            })->toArray()) : '-',
            $task->taskStatus->status_name ?? '-',
            $task->created_at ?? '-',
            $task->start_date ?? '-',
            $task->due_date ?? '-',
            $task->completed_date ?? '-',
            $task->accepted_date ?? '-',
            $task->project->project_name ?? '-',
            $task->department->department_name ?? '-',
            $task->sub_department->sub_department_name ?? '-',
            $task->creator && $task->creator->department ? $task->creator->department->department_name : '-',
            $task->creator && $task->creator->sub_department ? $task->creator->sub_department->sub_department_name : '-',
            $task->creator && $task->creator->phone_no ? $task->creator->phone_no : '-',
            $task->close_date ,

            // $task->description,
            // $task->creator ? $task->creator->first_name . ' ' . $task->creator->last_name : '-',
            // $task->users ? implode(', ', $task->users->pluck('first_name', 'last_name')->toArray()) : '-',


            // $task->created_at->format('Y-m-d H:i:s'),
            // $task->updated_at->format('Y-m-d H:i:s'),
        ];
    }
}
