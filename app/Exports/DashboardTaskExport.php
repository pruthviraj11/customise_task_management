<?php

namespace App\Exports;

use App\Models\TaskAssignee;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class DashboardTaskExport implements FromCollection, WithHeadings
{
    public function collection()
    {
        return TaskAssignee::leftJoin('tasks', 'tasks.id', 'task_assignees.task_id')
            ->leftJoin('users', 'users.id', 'task_assignees.user_id')
            ->leftJoin('departments', 'task_assignees.department', 'departments.id')
            ->leftJoin('sub_departments', 'task_assignees.sub_department', 'sub_departments.id')
            ->leftJoin('users as task_creator', 'task_assignees.created_by', 'task_creator.id')
            ->leftJoin('projects', 'tasks.project_id', 'projects.id')
            ->leftJoin('departments as user_department', 'users.department_id', 'user_department.id')
            ->leftJoin('sub_departments as user_sub_department', 'users.subdepartment', 'user_sub_department.id')

            ->whereIn('task_id', function ($subquery) {
                $subquery->select('id')->from('tasks')->whereNull('deleted_at');
            })
            ->whereNull('task_assignees.deleted_at')
            ->where('task_assignees.status', 1)
            ->select(
                'task_assignees.task_number',
                'tasks.ticket',
                'tasks.title',
                'tasks.subject',
                'task_creator.first_name',
                'users.first_name',
                'task_assignees.task_status',
                'tasks.created_at',
                'tasks.start_date',
                'task_assignees.due_date',
                'tasks.completed_date',
                'task_assignees.accepted_date',
                'projects.project_name',
                'departments.department_name',
                'sub_departments.sub_department_name',
                'user_department.department_name as creator_department_name',
                'user_sub_department.sub_department_name as creator_sub_department_name',
                'users.phone_no',
                'tasks.close_date'
            )
            ->get();
    }

    // Add this method to define column headers
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
}
