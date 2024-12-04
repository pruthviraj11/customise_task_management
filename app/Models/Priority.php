<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Priority extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = 'priority';
    protected $fillable = ['priority_name', 'displayname', 'created_by', 'updated_by', 'status'];
    protected $guarded = [];
}
