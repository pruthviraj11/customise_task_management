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
        'remark',
        'deleted_by',
        'created_by',
        'updated_by',
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

    // Define relationship with the creator (created_by references User)
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
    // public function status()
    // {
    //     return $this->belongsTo(Status::class, 'task_status');
    // }
}
