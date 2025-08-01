<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class User extends Authenticatable
{
    use HasApiTokens, LogsActivity, HasFactory, Notifiable, HasRoles, SoftDeletes;
    protected static $recordEvent = ['create', 'update', 'delete'];

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $table = 'users';
    protected $fillable = [
        'id',
        'first_name',
        'password',
        'last_name',
        'email',
        'department_id',
        'subdepartment',
        'phone_no',
        'email_verified_at',
        'username',
        'branch',
        'report_to',
        'status',
        'designation',
        'dob',
        'created_at',
        'profile_img',
        'Grad',
        'updated_at',
        'selected_fields',
        'location_id',
        'outlook_client_id',
        'outlook_client_secret',
        'outlook_redirect_url',
        'outlook_tenant_id',
        'outlook_access_token',
        'outlook_refresh_token',
        'outlook_token_expires',
        'company'
        // Add other fields as necessary
    ];
    public $incrementing = false;
    protected $guarded = [];

    protected $casts = [
        'selected_fields' => 'array',
        'email_verified_at' => 'datetime',
    ];

    protected $attributes = [
        'selected_fields' => '[]',
    ];



    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()->useLogName('user')
            ->logOnly(['id'])
            ->logOnly(['first_name', 'last_name', 'email', 'department_id', 'subdepartment', 'phone_no', 'email_verified_at', 'country', 'remember_token', 'created_at', 'username', 'dob', 'address', 'branch', 'report_to', 'status', 'deleted_at', 'created_by', 'deleted_by', 'profile_img'])
            // ->logOnlyDirty();
        ;
    }
    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    // protected $casts = [
    //     'email_verified_at' => 'datetime',
    // ];

    public function tasks()
    {
        return $this->belongsToMany(Task::class, 'task_assignees', 'user_id', 'task_id');
    }
    public function department()
    {
        return $this->belongsTo(Department::class, 'department_id');
    }
    public function sub_department()
    {
        return $this->belongsTo(SubDepartment::class, 'subdepartment');
    }

    public function reportsTo()
    {
        return $this->belongsTo(User::class, 'report_to');
    }

    // Define a relationship to get the users whom this user reports to
    public function reports()
    {
        return $this->hasMany(User::class, 'report_to');
    }
    public function subordinates()
    {
        return $this->hasMany(User::class, 'report_to');
    }

    public function reportToUser()
    {
        return $this->belongsTo(User::class, 'report_to');
    }
}
