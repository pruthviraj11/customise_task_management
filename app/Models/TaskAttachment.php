<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TaskAttachment extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'task_attachments';
//    protected $fillable = ['status_name', 'displayname', 'created_by', 'updated_by', 'status'];
    protected $guarded = [];

    public function task()
    {
        return $this->belongsTo(Task::class);
    }
}
