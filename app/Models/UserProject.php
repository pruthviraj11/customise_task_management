<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Log;
use App\Models\Loggs;
use Illuminate\Support\Facades\Auth;

class UserProject extends Model
{
    use HasFactory;

    protected $table = 'user_project';
//    protected $fillable = ['user_id', 'project_id', 'created_at', 'updated_at'];
    protected $guarded = [];


}
