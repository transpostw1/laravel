<?php

namespace App\Models;
use App\Models\Surcharge;
use App\Models\Rate_surcharges; 
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rates extends Model
{
    protected $primaryKey = 'ID';

    use HasFactory;

    public function surcharge()
    {
        //return $this->hasManyThrough('App\Models\Rate_surcharge', 'App\Models\Surcharge');
        return $this->hasManyThrough(
            Surcharge::Class,
            Rate_surcharge::Class,
            'rates_id',
            'surcharge_id'
         );

    }

    public function rate_surcharge()
    {
        //return $this->hasManyThrough('App\Models\Rate_surcharge', 'App\Models\Surcharge');
        return $this->hasManyThrough(
            Surcharge::Class,
            Rate_surcharge::Class,
            'rates_id',
            'surcharge_id'
         );

    }




    // public function surcharge()
    // {
    //     return $this->hasManyThrough(Rate_surcharge::class);
    // }
}
