<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class FileHandler extends Model
{
    protected $fillable = [
        'type','url','uploaded_by'
    ];
}
