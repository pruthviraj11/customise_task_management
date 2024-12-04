<?php

namespace App\Exports;

use App\Models\Task;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class TasksExport implements FromCollection, WithHeadings
{
    public function collection()
    {
        return Task::all();
    }

    public function headings(): array
    {
        return [
            'ID',
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
}
