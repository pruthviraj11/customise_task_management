<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class ProjectStatus extends Model
{
    use HasFactory, LogsActivity, SoftDeletes;

    protected $table = 'project_status';
    protected $fillable = ['project_status_name', 'displayname', 'created_by', 'updated_by', 'status'];
    protected $guarded = [];
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()->useLogName('Project Status')
            ->logOnly(['project_status_name', 'displayname', 'created_by', 'status'])
            ->logOnlyDirty();
    }
}
