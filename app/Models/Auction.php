<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Auction extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia;

    protected $guarded = [];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function user()
    {
        return $this->morphTo();
    }

    public function itemData()
    {
        return $this->hasMany(ItemData::class);
    }
}
