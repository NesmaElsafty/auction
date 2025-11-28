<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ItemData extends Model
{
    //
    protected $guarded = [];
    protected $table = 'item_data';

    public function auction()
    {
        return $this->belongsTo(Auction::class);
    }

    public function input()
    {
        return $this->belongsTo(Input::class);
    }
}
