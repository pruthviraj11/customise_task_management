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

    protected $table = 'task_assignees';
    protected $fillable = ['id', 'task_id', 'user_id', 'status', 'remark', 'deleted_by', 'created_by', 'updated_by', 'deleted_at', 'created_at', 'updated_ats'];
    protected $guarded = [];
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()->useLogName('Task Assignee')
            ->logOnly(['id', 'task_id', 'user_id', 'status', 'remark',])
            ->logOnlyDirty();
    }
    public function task()
    {
        return $this->belongsTo(Task::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
