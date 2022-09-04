<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    use HasFactory;

    public function pol()
    {
        return $this->hasOne('App\Models\Port_name', 'ID', 'POL');
    }

    public function pod()
    {
        return $this->hasOne('App\Models\Port_name', 'ID', 'POD');
    }

    public function cs_status(){
        return $this->hasOne('App\Models\Cs_statu', 'ID', 'status');
    }
}
