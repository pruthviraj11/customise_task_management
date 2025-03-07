<?php

namespace App\Exports;

use App\Models\TaskAssignee;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class DashboardTaskExport implements FromCollection, WithHeadings, WithMapping, WithColumnFormatting
{
    public function collection()
    {
        return TaskAssignee::leftJoin('tasks', 'tasks.id', '=', 'task_assignees.task_id')
            ->leftJoin('users as assigner', 'assigner.id', '=', 'task_assignees.created_by') // Task assigned by
            ->leftJoin('users as assignee', 'assignee.id', '=', 'task_assignees.user_id') // Task assigned to
            ->leftJoin('departments', 'task_assignees.department', '=', 'departments.id')
            ->leftJoin('sub_departments', 'task_assignees.sub_department', '=', 'sub_departments.id')
            ->leftJoin('projects', 'tasks.project_id', '=', 'projects.id')
            ->leftJoin('departments as owner_department', 'assigner.department_id', '=', 'owner_department.id')
            ->leftJoin('sub_departments as owner_sub_department', 'assigner.subdepartment', '=', 'owner_sub_department.id')
            ->leftJoin('status', 'task_assignees.task_status', '=', 'status.id')
            ->whereIn('task_id', function ($subquery) {
                $subquery->select('id')->from('tasks')->whereNull('deleted_at');
            })
            ->whereNull('task_assignees.deleted_at')
            ->select(
                'task_assignees.task_number',
                'tasks.ticket',
                'tasks.title',
                'tasks.subject',
                'assigner.first_name as assign_by', // Task assigned by
                'assignee.first_name as assign_to', // Task assigned to
                'status.status_name',
                'tasks.created_at',
                'tasks.start_date',
                'task_assignees.due_date',
                // 'tasks.completed_date',
                'task_assignees.completed_date as task_assignee_completed_date', // Fetch from task_assignees
                'tasks.completed_date as task_completed_date',
                'task_assignees.accepted_date',
                'projects.project_name',
                'departments.department_name',
                'sub_departments.sub_department_name',
                'owner_department.department_name as owner_department_name',
                'owner_sub_department.sub_department_name as owner_sub_department_name',
                'assignee.phone_no as owner_contact_info',
                'tasks.close_date'
            )
            ->get();
    }

    public function headings(): array
    {
        return [
            'Task Number',
            'Task/Ticket',
            'Title',
            'Subject',
            'Assigned By',
            'Assigned To',
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

    public function map($row): array
    {
        return [
            $row->task_number,
            $row->ticket == 0 ? 'Task' : 'Ticket',
            $row->title,
            $row->subject,
            $row->assign_by,
            $row->assign_to,
            $row->status_name,
            $this->formatDate($row->created_at),
            $this->formatDate($row->start_date),
            $this->formatDate($row->due_date),
            // $this->formatDate($row->completed_date),
            $this->formatDate($this->getCompletedDate($row)),
            $this->formatDate($row->accepted_date),
            $row->project_name,
            $row->department_name,
            $row->sub_department_name,
            $row->owner_department_name,
            $row->owner_sub_department_name,
            $row->owner_contact_info,
            $this->formatDate($row->close_date)
        ];
    }

    private function getCompletedDate($row)
    {
        if (!empty($row->task_assignee_completed_date)) {
            return $row->task_assignee_completed_date;
        } elseif (!empty($row->task_completed_date)) {
            return $row->task_completed_date;
        }
        return null;
    }

    private function formatDate($date)
    {
        return $date ? \PhpOffice\PhpSpreadsheet\Shared\Date::dateTimeToExcel(new \DateTime($date)) : null;
    }

    public function columnFormats(): array
    {
        return [
            'H' => NumberFormat::FORMAT_DATE_DDMMYYYY . ' HH:MM:SS', // Created Date
            'I' => NumberFormat::FORMAT_DATE_DDMMYYYY, // Start Date
            'J' => NumberFormat::FORMAT_DATE_DDMMYYYY, // Due Date
            'K' => NumberFormat::FORMAT_DATE_DDMMYYYY, // Completed Date
            'L' => NumberFormat::FORMAT_DATE_DDMMYYYY, // Accepted Task Date
            'S' => NumberFormat::FORMAT_DATE_DDMMYYYY . ' HH:MM:SS', // Close Date
        ];
    }
}
