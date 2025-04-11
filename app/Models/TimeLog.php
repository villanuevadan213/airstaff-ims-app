<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TimeLog extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $dates = ['clock_in', 'clock_out', 'break_in', 'break_out'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
