<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Banner extends Model
{

    protected $table = 'banner';

    protected $fillable = [
        'image_url',
    ];

    const CREATED_AT = 'created_time';
    const UPDATED_AT = 'updated_time';
}
