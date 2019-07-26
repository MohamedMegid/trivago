<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Hotel extends Model
{
	protected $table = "hotels";

    public function user()
    {
      	return $this->belongsto(User::class, 'created_by');
    }

    /**
     * Get the location associated with the hotel.
     */
    public function location()
    {
        return $this->hasOne('App\Location');
    }
}
