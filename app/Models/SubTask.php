<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SubTask extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = 'sub_tasks';

    // Define which fields can be mass-assigned (fillable)
    protected $fillable = [
        'task_id',
        'assign_to_id',
        'name',
        'department_id',
        'task_status',
        'sub_department_id',
        'project_id',
        'ticket',
        'closed',
        'department_name', // Add new fields here
        'project_name',
        'priority_name',
        'status_name',
        'title',
        'subject',
        'description',
        'start_date',
        'due_date',
        'accepted_date',
        'completed_date',
        'deleted_at',
        'deleted_by',
        'created_by',
        'updated_by'
    ];


    protected $dates = [
        'deleted_at',
        'created_at',
        'updated_at'
    ];
    public function task()
    {
        return $this->belongsTo(Task::class);
    }

    public function assignTo()
    {
        return $this->belongsTo(User::class, 'assign_to_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function deleter()
    {
        return $this->belongsTo(User::class, 'deleted_by');
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
        return $this->belongsToMany(User::class, 'task_assignees');
    }

}
