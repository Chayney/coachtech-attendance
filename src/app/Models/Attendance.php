<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'status_id',
        'date',
        'commute',
        'leave',
        'rest',
        'break_time',
        'work_time',
        'reason'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function approves() {
        return $this->hasMany(Approve::class);
    }
}
