<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Search_history extends Model
{
    protected $table = 'search_history';
    protected $fillable = [
        'name',
        'phone',
        'email',
        'companyName',
        'contact_person',
        'from_port',
        'to_port'
    ];
    use HasFactory;
}
