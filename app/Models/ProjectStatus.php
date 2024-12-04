<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProjectStatus extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'project_status';
    protected $fillable = ['project_status_name', 'displayname', 'created_by', 'updated_by', 'status'];
    protected $guarded = [];
}
