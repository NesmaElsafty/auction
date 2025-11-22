<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Input extends Model
{
    use HasFactory;
    
    protected $guarded = [];
    public function screen()
    {
        return $this->belongsTo(Screen::class);
    }

    public function options()
    {
        return $this->hasMany(Option::class);
    }
}
