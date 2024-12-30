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

class Location extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'locations';
    protected $fillable = ['id', 'location_name', 'address', 'created_by', 'updated_by', 'status'];
    protected $guarded = [];

}
