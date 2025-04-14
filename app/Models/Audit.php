<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Audit extends Model
{
    use HasFactory;

    public function tracking(){
        return $this->belongsTo(Tracking::class);
    }

    public function product(){
        return $this->belongsTo(Product::class);
    }
}
