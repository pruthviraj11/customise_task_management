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
            // ->leftJoin('departments', 'task_assignees.department', '=', 'departments.id')
            // ->leftJoin('sub_departments', 'task_assignees.sub_department', '=', 'sub_departments.id')
            ->leftJoin('departments as assignee_department', 'assignee.department_id', '=', 'assignee_department.id') // Assignee Department
            ->leftJoin('sub_departments as assignee_sub_department', 'assignee.subdepartment', '=', 'assignee_sub_department.id') // Assignee Sub Department
            ->leftJoin('projects', 'tasks.project_id', '=', 'projects.id')
            ->leftJoin('departments as owner_department', 'assigner.department_id', '=', 'owner_department.id')
            ->leftJoin('sub_departments as owner_sub_department', 'assigner.subdepartment', '=', 'owner_sub_department.id')
            ->leftJoin('status', 'task_assignees.task_status', '=', 'status.id')
            ->leftJoin('users as report_to_user', 'assignee.report_to', '=', 'report_to_user.id')
            ->whereIn('task_id', function ($subquery) {
                $subquery->select('id')->from('tasks')->whereNull('deleted_at');
            })
            ->whereNull('task_assignees.deleted_at')
            ->select(
                'task_assignees.task_number',
                'task_assignees.status as status',
                'task_assignees.task_id as task_id',

                'tasks.ticket',
                'tasks.title',
                'tasks.description',
                'tasks.subject',
                'assigner.first_name as assign_by', // Task assigned by
                'assignee.first_name as assign_to', // Task assigned to
                'assigner.last_name as assign_by_last', // Task assigned by
                'assignee.last_name as assign_to_last', // Task assigned to
                'status.status_name',
                'tasks.created_at',
                'tasks.start_date',
                'task_assignees.due_date',
                'tasks.due_date as tasks_due_date',
                // 'tasks.completed_date',
                'task_assignees.completed_date as task_assignee_completed_date', // Fetch from task_assignees
                'tasks.completed_date as task_completed_date',
                'task_assignees.accepted_date',
                'projects.project_name',
                // 'departments.department_name',
                // 'sub_departments.sub_department_name',
                'assignee_department.department_name as assignee_department_name',
                'assignee_sub_department.sub_department_name as assignee_sub_department_name',
                'owner_department.department_name as owner_department_name',
                'owner_sub_department.sub_department_name as owner_sub_department_name',
                'assignee.phone_no as owner_contact_info',
                'task_assignees.close_date as task_assignees_close_date',
                'tasks.close_date as tasks_close_date',
                'assignee.status as assign_to_status',
                'report_to_user.first_name as report_to_first_name',
                'report_to_user.last_name as report_to_last_name'
            )
            ->get();
    }

    public function headings(): array
    {
        return [
            'Status',
            'Task',
            'Task Number',
            'Task/Ticket',
            'Title',
            'Description',
            'Subject',
            'Assigned By',
            'Assigned To',
            'Task Status',
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
            'Close Date',
            'Assign To Status',
            'Assign To Reporting'
        ];
    }

    public function map($row): array
    {
        return [
            $this->mapStatus($row->status),
            $row->task_id,
            $row->task_number,
            $row->ticket == 0 ? 'Task' : 'Ticket',
            $row->title,
            $row->description,
            $row->subject,
            $row->assign_by . ' ' . $row->assign_by_last, // Full name of assigner
            $row->assign_to . ' ' . $row->assign_to_last, // Full name of assignee
            $row->status_name,
            $this->formatDate($row->created_at),
            $this->formatDate($row->start_date),
            // $this->formatDate($row->due_date),
            $this->formatDate($this->getDueDate($row)),
            // $this->formatDate($row->completed_date),
            $this->formatDate($this->getCompletedDate($row)),
            $this->formatDate($row->accepted_date),
            $row->project_name,
            // $row->department_name,
            // $row->sub_department_name,
            $row->assignee_department_name, // Assignee Department
            $row->assignee_sub_department_name,
            $row->owner_department_name,
            $row->owner_sub_department_name,
            !empty($row->owner_contact_info) ? $row->owner_contact_info : '0',
            // $this->formatDate($row->task_assignees_close_date)
            $this->formatDate($this->getCloseDate($row)),
            $this->mapUserStatus($row->assign_to_status),
            $this->mapReportTo($row->report_to_first_name, $row->report_to_last_name)

        ];
    }

    private function mapStatus($status)
    {
        switch ($status) {
            case 0:
                return 'Requested';
            case 1:
                return 'Accepted';
            case 2:
                return 'Rejected';
            default:
                return '-'; // For unexpected values
        }
    }
    private function getCompletedDate($row)
    {
        // if (!empty($row->task_assignee_completed_date)) {
        //     return $row->task_assignee_completed_date;
        // } elseif (!empty($row->task_completed_date)) {
        //     return $row->task_completed_date;
        // }
        // return null;
        if (!empty($row->task_completed_date)) {
            return $row->task_completed_date;
        } elseif (!empty($row->task_assignee_completed_date)) {
            return $row->task_assignee_completed_date;
        }
        return null;
    }

    private function getDueDate($row)
    {
        if (!empty($row->due_date)) {
            return $row->due_date;
        } elseif (!empty($row->tasks_due_date)) {
            return $row->tasks_due_date;
        }
        return null;
    }

    private function getCloseDate($row)
    {
        if (!empty($row->tasks_close_date)) {
            return $row->tasks_close_date;
        } elseif (!empty($row->task_assignees_close_date)) {
            return $row->task_assignees_close_date;
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
            'K' => NumberFormat::FORMAT_DATE_DDMMYYYY, // Created Date
            'L' => NumberFormat::FORMAT_DATE_DDMMYYYY, // Start Date
            'M' => NumberFormat::FORMAT_DATE_DDMMYYYY, // Due Date
            'N' => NumberFormat::FORMAT_DATE_DDMMYYYY, // Completed Date
            'O' => NumberFormat::FORMAT_DATE_DDMMYYYY, // Accepted Task Date
            'V' => NumberFormat::FORMAT_DATE_DDMMYYYY, // Close Date
        ];
    }

    private function mapUserStatus($status)
    {
        if (!isset($status)) {
            return '-';
        }
        return $status == 1 ? 'Active' : 'Inactive';
    }

    private function mapReportTo($firstName, $lastName)
    {
        if (!empty($firstName) || !empty($lastName)) {
            return trim("$firstName $lastName");
        }
        return '-';
    }
}
