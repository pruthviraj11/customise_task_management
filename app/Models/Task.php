<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;

use App\Models\Comments;

class Task extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'tasks';
    //    protected $fillable = ['status_name', 'displayname', 'created_by', 'updated_by', 'status'];
    protected $guarded = [];

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

    public function department()
    {
        return $this->belongsTo(Department::class, 'department_id');
    }

    public function comments(): HasMany
    {
        return $this->hasMany(Comments::class);
    }
}
