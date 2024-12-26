<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class RecursiveTaskAttachment extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = 'recursive_task_attachments';

    // Define the fillable fields
    protected $fillable = [
        'task_id',
        'file',
    ];

    public function task()
    {
        return $this->belongsTo(RecurringTask::class, 'task_id');  // Ensure correct foreign key 'task_id'
    }
}
