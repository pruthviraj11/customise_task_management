<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Log;
use App\Models\Loggs;
use Illuminate\Support\Facades\Auth;

use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Project extends Model
{
    use HasFactory, LogsActivity, SoftDeletes;

    protected $table = 'projects';
    protected $fillable = ['project_name', 'prifix', 'color', 'description', 'deleted_by', 'deleted_by', 'created_by', 'updated_by', 'status'];
    protected $guarded = [];
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()->useLogName('project')
            ->logOnly(['project_name', 'prifix', 'color', 'description', 'created_by', 'status'])
            ->logOnlyDirty();
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'user_project');
    }
}
