<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class TaskAssignee extends Model
{
    use HasFactory, LogsActivity, SoftDeletes;

    protected static $recordEvent = ['create', 'update', 'delete'];

    protected $table = 'task_assignees';  // Ensure this matches the table name
    protected $fillable = [
        'id',
        'task_id',
        'user_id',
        'status',
        'due_date',
        'remark',
        'deleted_by',
        'department',
        'sub_department',
        'created_by',
        'updated_by',
        'task_number',
        'reopen_date',
        'reopen_by',
        'close_date',
        'close_by',
        'completed_by',
        'completed_date',
        'task_status',
        'deleted_at',
        'created_at',
        'updated_at'
    ];
    protected $guarded = [];

    // Define logging options (optional but useful for tracking changes)
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()->useLogName('Task Assignee')
            ->logOnly(['id', 'task_id', 'user_id', 'status', 'remark'])
            ->logOnlyDirty();
    }

    // Define relationship with the Task model (A TaskAssignee belongs to a Task)
    public function task()
    {
        return $this->belongsTo(Task::class, 'task_id');  // Ensure correct foreign key 'task_id'
    }

    // Define relationship with the User model (A TaskAssignee belongs to a User)
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');  // Ensure correct foreign key 'user_id'
    }

    // Define relationship with the Status model (A TaskAssignee has a status)
    public function taskStatus()
    {
        return $this->belongsTo(Status::class, 'task_status');  // Ensure correct foreign key 'task_status'
    }

    public function department_data()
    {
        return $this->belongsTo(Department::class, 'department');
    }
    public function sub_department_data()
    {
        return $this->belongsTo(SubDepartment::class, 'sub_department');
    }

    // Define relationship with the creator (created_by references User)
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
    // public function status()
    // {
    //     return $this->belongsTo(Status::class, 'task_status');
    // }
    public function removeUserFromTask($subtaskId, $userId)
    {
        // Find the task assignment record
        $taskAssignee = $this->where('id', $subtaskId)
            ->where('user_id', $userId)
            ->first();

        if ($taskAssignee) {
            // Soft delete the user from the task (sets the deleted_at timestamp)
            $taskAssignee->delete();
            return true;
        }


        return false;
    }

  
}
