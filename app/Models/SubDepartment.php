<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Log;
use App\Models\Loggs;
use Illuminate\Support\Facades\Auth;

class SubDepartment extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'sub_departments';
    protected $fillable = ['sub_department_name', 'deleted_by', 'department_id', 'description', 'created_by', 'updated_by', 'status'];
    protected $guarded = [];
    public function department()
    {
        return $this->belongsTo(Department::class);
    }

}
