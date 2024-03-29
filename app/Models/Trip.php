<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Trip extends Model
{
    use HasFactory;

    protected $guarded = [
        'id'
    ];

    public function stops()
    {
        return $this->belongsToMany(Stop::class)->withPivot('sequence');
    }
}
