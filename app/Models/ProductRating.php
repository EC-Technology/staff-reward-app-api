<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class ProductRating extends Model
{

    const PROCESS_STATUS_COMPLETED = 1;
    const PROCESS_STATUS_PENDING = 0;

    protected $table = 'product_rating';

    protected $fillable = [
        'id',
        'product_id',
        'rating',
        'user_comment',
    ];

    protected $hidden = [
        'process_status',
        'processed_time',
    ];

    const CREATED_AT = 'created_time';
    const UPDATED_AT = 'updated_time';
}
