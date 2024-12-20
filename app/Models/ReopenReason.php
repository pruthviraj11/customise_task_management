<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ReopenReason extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = 'reopen_reasons';
    protected $fillable = [
        'reason',     // Reason for reopening
        'reopen_date', // Date of reopening
        'reopen_by',   // Who reopened the record
        'user_id',     // User ID associated with reopening
        'created_by',
        'created_at',
        'updated_by',
        'updated_at',
    ];
    public function user()
    {
        return $this->belongsTo(User::class);
    }

}
