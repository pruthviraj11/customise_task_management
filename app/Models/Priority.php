<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Priority extends Model
{
    protected static $recordEvent = ['create', 'update', 'delete'];
    use HasFactory, LogsActivity, SoftDeletes;
    protected $table = 'priority';
    protected $fillable = ['priority_name', 'displayname', 'created_by', 'updated_by', 'status'];
    protected $guarded = [];
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()->useLogName('Priority')
            ->logOnly(['priority_name', 'displayname', 'created_by', 'status'])
            ->logOnlyDirty();
    }

}
