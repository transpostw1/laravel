<?php

namespace App\Models;

use App\Models\Rates;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Surcharge extends Model
{
    
    use HasFactory;

    public function rates(){
        return $this->belongsToMany(Rates::class)->withTimestamps()->withPivot(['amount', 'currency']);
    }
}
