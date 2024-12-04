<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\Permission\Models\Role as SpatieRole;
use Illuminate\Database\Eloquent\SoftDeletes;

class Role extends SpatieRole
{
    use HasFactory, SoftDeletes;

    protected $table = 'roles';
    protected $fillable = ['name','display_name','guard_name'];
    protected $guarded = [];
    public function getRouteKeyName()
    {
        return 'encryption_id';
    }
}
