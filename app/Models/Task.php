<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;
use App\Models\Comments;

class Task extends Model
{
    use HasFactory, LogsActivity, SoftDeletes;
    protected static $recordEvent = ['create', 'update', 'delete'];
    protected $table = 'tasks';
    protected $fillable = [

        'priority_id',
        // 'id',
        'project_id',
        'department_id',
        'sub_department_id',
        'task_status',
        'title',
        'subject',
        'description',
        'start_date',
        'due_date',
        'accepted_date',
        'completed_date',
        'last_task_number',
        'deleted_by',
        'created_by',
        'updated_by',
        'recursive_task_id',
        'is_recursive',
        'deleted_at',
        'created_at',
        'updated_at',
        'closed',
        'ticket',
        'close_date',
        'department_name', // Add new fields here
        'project_name',
        'priority_name',
        'status_name',
    ];
    // public $incrementing = false;
    protected $guarded = [];
    // protected static $logAttributes = ['id', 'priority_id', 'project_id', 'department_id', 'sub_department_id', 'task_status', 'title', 'subject', 'description', 'start_date', 'due_date', 'accepted_date', 'completed_date', 'deleted_by', 'created_by', 'updated_by', 'deleted_at', 'created_at', 'updated_at', 'closed'];
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()->useLogName('Task')
            ->logOnly([
                'id',
                'department_name', // Add new fields here
                'project_name',
                'priority_name',
                'status_name',
                'title',
                'subject',
                'close_by',
                'completed_by',
                'description',
                'start_date',
                'due_date',
                'accepted_date',
                'completed_date',
                'closed_date'
            ])
            ->logOnlyDirty();
    }


    public function attachments()
    {
        return $this->hasMany(TaskAttachment::class);
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
            ->withTrashed();
    }


    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by')->withTrashed();
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

    public function comments(): HasMany
    {
        return $this->hasMany(Comments::class);
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
