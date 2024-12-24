<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class RecurringTask extends Model
{
    use HasFactory,SoftDeletes;

    protected $table = 'recurring_task';  // Specify the table name

    protected $fillable = [
        'priority_id',
        'project_id',
        'department_id',
        'task_assignes',
        'sub_department_id',
        'task_status',
        'title',
        'subject',
        'description',
        'start_date',
        'due_date',
        'accepted_date',
        'completed_date',
        'completed_by',
        'deleted_by',
        'created_by',
        'updated_by',
        'closed',
        'ticket',
        'is_sub_task',
        'close_date',
        'close_by',
        'department_name',
        'project_name',
        'priority_name',
        'recurring_type',
        'number_of_days',
        'status_name',
        'TaskNumber'
    ];

    // Define the dates that should be mutated to Carbon instances (to handle date fields)
    protected $dates = [
        'deleted_at',
        'start_date',
        'due_date',
        'accepted_date',
        'completed_date',
        'close_date',
    ];
}
