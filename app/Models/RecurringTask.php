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

    public function attachmentsrec()
    {
        return $this->hasMany(RecursiveTaskAttachment::class);
    }

    public function assignees()
    {
        return $this->hasMany(TaskAssignee::class);
    }
    public function assignees_new()
    {
        return $this->hasMany(TaskAssignee::class, 'task_id', 'id');
    }
    public function users()
    {
        return $this->belongsToMany(User::class, 'task_assignees')
            ->whereNull('task_assignees.deleted_at');
    }


    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
    public function taskStatus()
    {
        return $this->belongsTo(Status::class, 'task_status');
    }

    public function project()
    {
        return $this->belongsTo(Project::class, 'project_id');
    }
    public function task_status_data()
    {
        return $this->belongsTo(Status::class, 'task_status');
    }

    public function department()
    {
        return $this->belongsTo(Department::class, 'department_id');
    }
    public function sub_department()
    {
        return $this->belongsTo(SubDepartment::class, 'sub_department_id');
    }
    // In Task model
     public function assignedUsers()
    {
        // Assuming task_assignes holds user IDs as a comma-separated string
        return $this->belongsToMany(User::class, 'task_user', 'task_id', 'user_id');
    }

    public function isAcceptedByUser($userId)
    {
        return $this->taskAssignees()
            ->where('user_id', $userId)
            ->where('status', 1) // Status 1 indicates accepted
            ->exists(); // Check if any records match
    }

    public function taskAssignees()
    {
        return $this->hasMany(TaskAssignee::class); // Adjust the relationship based on your setup
    }
}
