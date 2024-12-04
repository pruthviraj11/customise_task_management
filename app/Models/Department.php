<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Log;
use App\Models\Loggs;
use Illuminate\Support\Facades\Auth;
use App\Models\User;


class Department extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'departments';
    protected $fillable = ['department_name', 'description', 'hod', 'created_by', 'updated_by', 'status'];
    protected $guarded = [];
    public function subDepartments()
    {
        return $this->hasMany(SubDepartment::class);
    }


    public function user()
    {
        return $this->belongsTo(User::class, 'hod', 'id');
    }

}
