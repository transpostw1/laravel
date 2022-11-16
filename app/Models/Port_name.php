<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Port_name extends Model
{
    protected $table = 'port_name';
    use HasFactory;
    
    public function booking(){
        return $this->belongsTo('App\Bookings','POL','id');
    }
}
