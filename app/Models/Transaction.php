<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function model()
    {
        return $this->morphTo();
    }

    public function auction()
    {
        return $this->belongsTo(Auction::class);
    }

}
