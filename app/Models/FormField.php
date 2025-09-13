<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FormField extends Model
{
    //

    protected $fillable = ['form_id','type','label','name','options','order','validation','meta'];


    protected $casts = [
        'options' => 'array',
        'validation' => 'array',
        'meta' => 'array',
    ];

    public function form(){
        return $this->belongsTo(Form::class);
    }
}
