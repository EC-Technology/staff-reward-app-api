<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Merchant extends Model
{
    use HasFactory;

    const STATUS_ACTIVE = 1;
    const STATUS_INACTIVE = 0;

    protected $table = 'merchant';

    protected $fillable = [
        'code',
        'name',
        'image_url',
        'theme_color',
        'status',
    ];

    const CREATED_AT = 'created_time';
    const UPDATED_AT = 'updated_time';
}
