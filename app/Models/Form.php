<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Form extends Model
{
    //
    
    protected $table = "forms";

    protected $fillable = ['name', 'schema'];


    protected $casts = [
        'schema' => 'array',
    ];
}
