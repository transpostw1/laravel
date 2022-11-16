<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Booking_stage extends Model
{   
    

    protected $table = 'booking_stage';
    use HasFactory;
    public function bookings()
    {
        return $this->hasMany('App\Models\Booking', 'ID', 'bookingID');
    }
    public function status()
    {
        return $this->hasMany('App\Models\Cs_statu', 'ID', 'cs_statusID');
    }
}
