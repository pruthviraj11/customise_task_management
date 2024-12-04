<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Log;
use App\Models\Loggs;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Department extends Model
{
    use HasFactory, LogsActivity, SoftDeletes;

    protected $table = 'departments';
    protected $fillable = ['id', 'department_name', 'description', 'hod', 'created_by', 'updated_by', 'status'];
    protected $guarded = [];
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()->useLogName('Department')
            ->logOnly(['department_name', 'description', 'hod', 'created_by', 'status'])
            ->logOnlyDirty();
    }

    public function subDepartments()
    {
        return $this->hasMany(SubDepartment::class);
    }


    public function user()
    {
        return $this->belongsTo(User::class, 'hod', 'id');
    }
    public function hod_data()
    {
        return $this->belongsTo(User::class, 'hod', 'id');
    }
    public function tasks()
    {
        return $this->hasMany(Task::class, 'department_id');
    }

}
