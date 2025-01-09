<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Approve extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'attendance_id',
        'status'
    ];

    public function approveUser()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function approveAttendance()
    {
        return $this->belongsTo(Attendance::class, 'attendance_id');
    }
}
