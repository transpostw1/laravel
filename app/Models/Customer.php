<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{   
    protected $primaryKey = "ID";
    protected $fillable = [
        'name',
        'email', 
        'phone',
        'companyName',
        'gst_certificate',
        'pan_card',
        'contact_person'
    ];

    protected $table = 'customer';
    use HasFactory;
    
}
