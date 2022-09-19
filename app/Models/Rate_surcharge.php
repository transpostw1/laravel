<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rate_surcharge extends Model
{
    protected $table = 'rates_surcharge';
    use HasFactory;

    // public function rates(){
    //     return $this->belongsTo('App\Rates');
    // }
    public function parentable()
    {
        return $this->morphTo();
    }

}


