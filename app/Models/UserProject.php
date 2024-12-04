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

class UserProject extends Model
{
    use HasFactory, LogsActivity;

    protected $table = 'user_project';
    //    protected $fillable = ['user_id', 'project_id', 'created_at', 'updated_at'];
    protected $guarded = [];
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()->useLogName('User Project')

            ->logOnly(['id', 'user_id', 'project_id'])
            ->logOnlyDirty();
    }

}
