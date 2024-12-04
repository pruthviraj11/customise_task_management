<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Log;
use App\Models\Loggs;
use Illuminate\Support\Facades\Auth;

class Project extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'projects';
    protected $fillable = ['project_name', 'prifix', 'color', 'description', 'deleted_by', 'deleted_by', 'created_by', 'updated_by', 'status'];
    protected $guarded = [];

    public function users()
    {
        return $this->belongsToMany(User::class, 'user_project');
    }
}
