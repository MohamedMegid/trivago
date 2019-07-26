<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Location extends Model
{
    public function location()
    {
      	return $this->belongsto(Hotel::class, 'hotel_id');
    }
}
