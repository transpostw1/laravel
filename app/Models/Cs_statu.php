<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cs_statu extends Model
{
    use HasFactory;

    public function callbox()
    {
        return $this->hasMany('App\Models\Callbox', 'ID', 'callboxID');
    }
}
